<?php
/**
 * 2007-2026 PrestaShop
 *
 * Performance remediation workspace module.
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class PrestaLoad extends Module
{
    public const CONFIG_ENABLED = 'PRESTALOAD_ENABLED';
    public const CONFIG_AUDIT_REPORT = 'PRESTALOAD_AUDIT_REPORT';
    public const CONFIG_PLAN_PATH = 'PRESTALOAD_PLAN_PATH';
    public const CONFIG_ISSUE_STATES = 'PRESTALOAD_ISSUE_STATES';

    private const DEFAULT_AUDIT_REPORT = 'modules/prestaload/plexi.local.test-20260314T182958.json';
    private const DEFAULT_PLAN_PATH = 'modules/prestaload/plan.md';
    private const FOCUS_SCAN_SCRIPT = 'modules/prestaload/tools/focus-scan.mjs';

    public function __construct()
    {
        $this->name = 'prestaload';
        $this->tab = 'administration';
        $this->version = '0.2.0';
        $this->author = 'Acrosoft';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->ps_versions_compliancy = [
            'min' => '1.7.0.0',
            'max' => _PS_VERSION_,
        ];

        parent::__construct();

        $this->displayName = $this->trans('PrestaLoad', [], 'Modules.Prestaload.Admin');
        $this->description = $this->trans(
            'Provides a per-issue performance testing dashboard for Prestashop remediation work.',
            [],
            'Modules.Prestaload.Admin'
        );
        $this->confirmUninstall = $this->trans(
            'This removes PrestaLoad configuration only. It does not delete the audit JSON or plan files.',
            [],
            'Modules.Prestaload.Admin'
        );
    }

    public function install()
    {
        return parent::install()
            && Configuration::updateValue(self::CONFIG_ENABLED, 1)
            && Configuration::updateValue(self::CONFIG_AUDIT_REPORT, self::DEFAULT_AUDIT_REPORT)
            && Configuration::updateValue(self::CONFIG_PLAN_PATH, self::DEFAULT_PLAN_PATH)
            && Configuration::updateValue(self::CONFIG_ISSUE_STATES, '{}')
            && $this->registerHook('displayBackOfficeHeader')
            && $this->registerHook('actionFrontControllerSetMedia');
    }

    public function uninstall()
    {
        return Configuration::deleteByName(self::CONFIG_ENABLED)
            && Configuration::deleteByName(self::CONFIG_AUDIT_REPORT)
            && Configuration::deleteByName(self::CONFIG_PLAN_PATH)
            && Configuration::deleteByName(self::CONFIG_ISSUE_STATES)
            && parent::uninstall();
    }

    public function getContent()
    {
        if (Tools::getValue('ajax') && Tools::getValue('configure') === $this->name) {
            $this->handleAjaxRequest();
        }

        $issues = $this->getIssues();

        $this->context->smarty->assign([
            'prestaload_ajax_url' => $this->getAdminModuleUrl() . '&ajax=1',
            'prestaload_admin_token' => Tools::getAdminTokenLite('AdminModules'),
            'prestaload_issues' => $issues,
            'prestaload_groups' => $this->groupIssues($issues),
            'prestaload_scanner_url' => $this->getShopScanUrl(),
            'prestaload_plan_path' => Configuration::get(self::CONFIG_PLAN_PATH, self::DEFAULT_PLAN_PATH),
            'prestaload_report_path' => Configuration::get(self::CONFIG_AUDIT_REPORT, self::DEFAULT_AUDIT_REPORT),
        ]);

        return $this->display(__FILE__, 'views/templates/admin/configure.tpl');
    }

    public function hookDisplayBackOfficeHeader()
    {
        if (!self::isModuleEnabled()) {
            return;
        }
    }

    public function hookActionFrontControllerSetMedia()
    {
        if (!self::isModuleEnabled()) {
            return;
        }
    }

    public static function isModuleEnabled()
    {
        return (bool) Configuration::get(self::CONFIG_ENABLED, 1);
    }

    private function handleAjaxRequest()
    {
        if (Tools::getValue('token') !== Tools::getAdminTokenLite('AdminModules')) {
            $this->jsonResponse([
                'success' => false,
                'message' => $this->trans('Invalid admin token.', [], 'Admin.Notifications.Error'),
            ]);
        }

        $action = (string) Tools::getValue('action');

        if ($action === 'runIssueTest') {
            $this->runIssueTest();
        }

        if ($action === 'saveIssueStatus') {
            $this->saveIssueStatus();
        }

        $this->jsonResponse([
            'success' => false,
            'message' => $this->trans('Unknown action.', [], 'Admin.Notifications.Error'),
        ]);
    }

    private function runIssueTest()
    {
        $slug = (string) Tools::getValue('issue_slug');
        $issuesBySlug = $this->getIssuesBySlug();

        if (!isset($issuesBySlug[$slug])) {
            $this->jsonResponse([
                'success' => false,
                'message' => $this->trans('Unknown issue.', [], 'Admin.Notifications.Error'),
            ]);
        }

        $issue = $issuesBySlug[$slug];

        if (empty($issue['test_target_type']) || empty($issue['test_target_value'])) {
            $this->jsonResponse([
                'success' => false,
                'message' => $this->trans('This issue does not have a direct scanner target yet.', [], 'Modules.Prestaload.Admin'),
            ]);
        }

        $scriptPath = $this->getWorkspacePath(self::FOCUS_SCAN_SCRIPT);
        if (!is_file($scriptPath)) {
            $this->jsonResponse([
                'success' => false,
                'message' => $this->trans('Focus scanner script not found.', [], 'Admin.Notifications.Error'),
            ]);
        }

        $command = [
            'node',
            escapeshellarg($scriptPath),
            '--url',
            escapeshellarg($this->getShopScanUrl()),
            '--device',
            'both',
            '--scan',
        ];

        if ($issue['test_target_type'] === 'audit_id') {
            $command[] = '--audit-id';
            $command[] = escapeshellarg($issue['test_target_value']);
        } else {
            $command[] = '--audit';
            $command[] = escapeshellarg($issue['test_target_value']);
        }

        $output = shell_exec(implode(' ', $command) . ' 2>&1');

        if (!is_string($output) || trim($output) === '') {
            $this->jsonResponse([
                'success' => false,
                'message' => $this->trans('The scanner did not return any output.', [], 'Admin.Notifications.Error'),
            ]);
        }

        $decoded = json_decode($output, true);
        if (!is_array($decoded)) {
            $this->jsonResponse([
                'success' => false,
                'message' => $this->trans('Scanner output was not valid JSON.', [], 'Admin.Notifications.Error'),
                'raw_output' => $output,
            ]);
        }

        $states = $this->getIssueStates();
        $states[$slug]['last_test'] = $this->buildStoredTestSummary($decoded, $issue);
        Configuration::updateValue(self::CONFIG_ISSUE_STATES, json_encode($states));

        $this->jsonResponse([
            'success' => true,
            'issue_slug' => $slug,
            'message' => $this->trans('Issue test completed.', [], 'Admin.Notifications.Success'),
            'result' => $decoded,
        ]);
    }

    private function saveIssueStatus()
    {
        $slug = (string) Tools::getValue('issue_slug');
        $status = (string) Tools::getValue('status');
        $allowedStatuses = ['solved', 'not_solved', 'unknown'];

        if (!in_array($status, $allowedStatuses, true)) {
            $this->jsonResponse([
                'success' => false,
                'message' => $this->trans('Invalid issue status.', [], 'Admin.Notifications.Error'),
            ]);
        }

        $issuesBySlug = $this->getIssuesBySlug();
        if (!isset($issuesBySlug[$slug])) {
            $this->jsonResponse([
                'success' => false,
                'message' => $this->trans('Unknown issue.', [], 'Admin.Notifications.Error'),
            ]);
        }

        $states = $this->getIssueStates();
        if (!isset($states[$slug])) {
            $states[$slug] = [];
        }

        $states[$slug]['status'] = $status;
        $states[$slug]['updated_at'] = date('c');

        Configuration::updateValue(self::CONFIG_ISSUE_STATES, json_encode($states));

        $this->jsonResponse([
            'success' => true,
            'issue_slug' => $slug,
            'status' => $status,
            'message' => $this->trans('Issue status saved.', [], 'Admin.Notifications.Success'),
        ]);
    }

    private function getIssues()
    {
        $planPath = $this->getWorkspacePath(Configuration::get(self::CONFIG_PLAN_PATH, self::DEFAULT_PLAN_PATH));
        $states = $this->getIssueStates();

        if (!is_file($planPath)) {
            return [];
        }

        $lines = file($planPath, FILE_IGNORE_NEW_LINES);
        if (!is_array($lines)) {
            return [];
        }

        $issues = [];
        $group = '';
        $currentIssue = null;

        foreach ($lines as $line) {
            if (preg_match('/^##\s+(.+)$/', $line, $matches)) {
                if ($currentIssue !== null) {
                    $issues[] = $this->finalizeIssue($currentIssue, $states);
                    $currentIssue = null;
                }

                $group = trim($matches[1]);
                continue;
            }

            if (preg_match('/^- \[[ xX]\] (.+)$/', $line, $matches)) {
                if ($currentIssue !== null) {
                    $issues[] = $this->finalizeIssue($currentIssue, $states);
                }

                $currentIssue = [
                    'group' => $group,
                    'title' => trim($matches[1]),
                    'details' => [],
                ];
                continue;
            }

            if ($currentIssue !== null && preg_match('/^\s{2,}- (.+)$/', $line, $matches)) {
                $currentIssue['details'][] = trim($matches[1]);
            }
        }

        if ($currentIssue !== null) {
            $issues[] = $this->finalizeIssue($currentIssue, $states);
        }

        return $issues;
    }

    private function finalizeIssue(array $issue, array $states)
    {
        $auditIds = $this->extractAuditIds($issue['title']);
        foreach ($issue['details'] as $detail) {
            $auditIds = array_merge($auditIds, $this->extractAuditIds($detail));
        }
        $auditIds = array_values(array_unique($auditIds));

        $testTarget = $this->resolveTestTarget($issue['title'], $issue['group'], $auditIds);
        $slug = Tools::link_rewrite($issue['group'] . '-' . $issue['title']);
        $state = isset($states[$slug]) && is_array($states[$slug]) ? $states[$slug] : [];

        return [
            'slug' => $slug,
            'group' => $issue['group'],
            'title' => $this->stripAuditSuffix($issue['title']),
            'details' => $issue['details'],
            'audit_ids' => $auditIds,
            'test_target_type' => $testTarget['type'],
            'test_target_value' => $testTarget['value'],
            'status' => isset($state['status']) ? $state['status'] : 'unknown',
            'updated_at' => isset($state['updated_at']) ? $state['updated_at'] : null,
            'last_test' => isset($state['last_test']) ? $state['last_test'] : null,
        ];
    }

    private function stripAuditSuffix($title)
    {
        return trim(preg_replace('/\s+\((`[^`]+`(?:,\s*`[^`]+`)*)\)\s*$/', '', $title));
    }

    private function extractAuditIds($text)
    {
        preg_match_all('/`([^`]+)`/', $text, $matches);
        $auditIds = [];

        foreach ($matches[1] as $candidate) {
            if (
                preg_match('/^[a-z0-9-]+$/', $candidate)
                && strpos($candidate, '/') === false
                && strpos($candidate, '.') === false
            ) {
                $auditIds[] = $candidate;
            }
        }

        return $auditIds;
    }

    private function resolveTestTarget($title, $group, array $auditIds)
    {
        $auditIdToLabel = [
            'third-party-summary' => '3rd parties',
            'third-parties' => '3rd parties',
            'layout-shifts' => 'Avoid large layout shifts',
            'redirects' => 'Avoid multiple page redirects',
            'non-composited-animations' => 'Avoid non composited animations',
            'total-byte-weight' => 'Avoids enormous network payloads',
            'cumulative-layout-shift' => 'Cumulative Layout Shift',
            'diagnostics' => 'Diagnostics',
            'document-latency-insight' => 'Document request latency',
            'server-response-time' => 'Initial server response time was short',
            'duplicated-javascript-insight' => 'Duplicated JavaScript',
            'final-screenshot' => 'Final Screenshot',
            'first-contentful-paint' => 'First Contentful Paint',
            'font-display-insight' => 'Font display',
            'forced-reflow-insight' => 'Forced reflow',
            'unsized-images' => 'Image elements do not have explicit `width` and `height`',
            'image-delivery-insight' => 'Improve image delivery',
            'bootup-time' => 'JavaScript execution time',
            'lcp-breakdown-insight' => 'LCP breakdown',
            'largest-contentful-paint' => 'Largest Contentful Paint',
            'legacy-javascript-insight' => 'Legacy JavaScript',
            'max-potential-fid' => 'Max Potential First Input Delay',
            'metrics' => 'Metrics',
            'unminified-css' => 'Minify CSS',
            'unminified-javascript' => 'Minify JavaScript',
            'mainthread-work-breakdown' => 'Minimizes main thread work',
            'network-requests' => 'Network Requests',
            'network-rtt' => 'Network Round Trip Times',
            'network-dependency-tree-insight' => 'Network dependency tree',
            'dom-size-insight' => 'Optimize DOM size',
            'unused-css-rules' => 'Reduce unused CSS',
            'unused-javascript' => 'Reduce unused JavaScript',
            'render-blocking-insight' => 'Render blocking requests',
            'resource-summary' => 'Resources Summary',
            'screenshot-thumbnails' => 'Screenshot Thumbnails',
            'script-treemap-data' => 'Script Treemap Data',
            'speed-index' => 'Speed Index',
            'interactive' => 'Time to Interactive',
            'total-blocking-time' => 'Total Blocking Time',
            'cache-insight' => 'Use efficient cache lifetimes',
            'modern-http-insight' => 'Network dependency tree',
            'bf-cache' => null,
        ];

        foreach ($auditIds as $auditId) {
            if (array_key_exists($auditId, $auditIdToLabel)) {
                return [
                    'type' => 'audit_id',
                    'value' => $auditId,
                ];
            }
        }

        $lowerTitle = Tools::strtolower($title);

        $heuristics = [
            'rendering path' => ['type' => 'audit_id', 'value' => 'largest-contentful-paint'],
            'javascript execution cost' => ['type' => 'audit_id', 'value' => 'bootup-time'],
            'unused javascript' => ['type' => 'audit_id', 'value' => 'unused-javascript'],
            'unused css' => ['type' => 'audit_id', 'value' => 'unused-css-rules'],
            'minify plexi css' => ['type' => 'audit_id', 'value' => 'unminified-css'],
            'javascript minification' => ['type' => 'audit_id', 'value' => 'unminified-javascript'],
            'render-blocking css' => ['type' => 'audit_id', 'value' => 'render-blocking-insight'],
            'forced reflows' => ['type' => 'audit_id', 'value' => 'forced-reflow-insight'],
            'image delivery' => ['type' => 'audit_id', 'value' => 'image-delivery-insight'],
            'width' => ['type' => 'audit_id', 'value' => 'unsized-images'],
            'total page weight' => ['type' => 'audit_id', 'value' => 'total-byte-weight'],
            'cache lifetimes' => ['type' => 'audit_id', 'value' => 'cache-insight'],
            'back/forward cache' => ['type' => 'audit_id', 'value' => 'bf-cache'],
            'lcp path' => ['type' => 'audit_id', 'value' => 'lcp-breakdown-insight'],
            'charla' => ['type' => 'audit_id', 'value' => 'third-party-summary'],
            'google tag manager' => ['type' => 'audit_id', 'value' => 'third-party-summary'],
            'google sign-in' => ['type' => 'audit_id', 'value' => 'third-party-summary'],
            'google fonts' => ['type' => 'audit_id', 'value' => 'font-display-insight'],
            'same-brand assets' => ['type' => 'audit_id', 'value' => 'total-byte-weight'],
            'browser extensions' => ['type' => 'audit_id', 'value' => 'unused-javascript'],
            'indexeddb' => ['type' => 'audit_id', 'value' => 'diagnostics'],
        ];

        foreach ($heuristics as $needle => $target) {
            if (strpos($lowerTitle, $needle) !== false) {
                return $target;
            }
        }

        if ($group === 'Outside Plexi Errors') {
            return ['type' => 'audit_id', 'value' => 'third-party-summary'];
        }

        return ['type' => null, 'value' => null];
    }

    private function getIssuesBySlug()
    {
        $issues = $this->getIssues();
        $indexed = [];

        foreach ($issues as $issue) {
            $indexed[$issue['slug']] = $issue;
        }

        return $indexed;
    }

    private function groupIssues(array $issues)
    {
        $groups = [];

        foreach ($issues as $issue) {
            if (!isset($groups[$issue['group']])) {
                $groups[$issue['group']] = [];
            }

            $groups[$issue['group']][] = $issue;
        }

        return $groups;
    }

    private function getIssueStates()
    {
        $raw = Configuration::get(self::CONFIG_ISSUE_STATES, '{}');
        $decoded = json_decode($raw, true);

        return is_array($decoded) ? $decoded : [];
    }

    private function buildStoredTestSummary(array $result, array $issue)
    {
        return [
            'scanned_at' => isset($result['scanned_at']) ? $result['scanned_at'] : null,
            'source_report' => isset($result['source_report']) ? $result['source_report'] : null,
            'target_type' => $issue['test_target_type'],
            'target_value' => $issue['test_target_value'],
            'mobile' => isset($result['mobile']) ? $result['mobile'] : null,
            'desktop' => isset($result['desktop']) ? $result['desktop'] : null,
        ];
    }

    private function getAdminModuleUrl()
    {
        return $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name
            . '&tab_module=' . $this->tab
            . '&module_name=' . $this->name
            . '&token=' . Tools::getAdminTokenLite('AdminModules');
    }

    private function getShopScanUrl()
    {
        $baseUri = defined('__PS_BASE_URI__') ? __PS_BASE_URI__ : '/';

        return Tools::getShopDomainSsl(true, true) . rtrim($baseUri, '/') . '/';
    }

    private function getWorkspacePath($relativePath)
    {
        $cleanPath = ltrim((string) $relativePath, '/');

        return rtrim(_PS_ROOT_DIR_, '/') . '/' . $cleanPath;
    }

    private function jsonResponse(array $payload)
    {
        header('Content-Type: application/json');
        exit(Tools::jsonEncode($payload));
    }
}
