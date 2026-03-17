<?php
/**
 * Injects stored beta critical CSS into matching front-office pages.
 */

class PrestaLoadCriticalCssInjector
{
    /**
     * @var Context
     */
    private $context;

    /**
     * @var PrestaLoadCriticalCssStore
     */
    private $store;

    /**
     * @var string
     */
    private $logFile;

    public function __construct(Context $context, PrestaLoadCriticalCssStore $store, $modulePath = '')
    {
        $this->context = $context;
        $this->store = $store;
        $modulePath = rtrim((string) $modulePath, '/');
        $this->logFile = ($modulePath !== '' ? $modulePath : dirname(__DIR__)) . '/cache/prestaload-critical-css.log';
    }

    public function optimize($html)
    {
        if (!is_string($html) || stripos($html, '</head>') === false) {
            return $html;
        }

        $pageType = $this->resolvePageType();
        $device = $this->resolveDevice();
        if ($pageType === '') {
            $this->log([
                'result' => 'skip',
                'reason' => 'unsupported-page-type',
                'device' => $device,
                'controller' => isset($this->context->controller->php_self) ? (string) $this->context->controller->php_self : '',
            ]);
            return $html;
        }

        $entry = $this->store->get($pageType, $device);
        if (empty($entry['css'])) {
            $this->log([
                'result' => 'miss',
                'page_type' => $pageType,
                'device' => $device,
            ]);
            return $html;
        }

        $html = preg_replace('#<style[^>]*id="prestaload-critical-css"[^>]*>.*?</style>#is', '', $html);

        $marker = '<!-- PrestaLoad Critical CSS: ' . htmlspecialchars($pageType, ENT_QUOTES, 'UTF-8') . ' (' . htmlspecialchars($device, ENT_QUOTES, 'UTF-8') . ') -->';
        $styleTag = $marker . '<style id="prestaload-critical-css" data-prestaload-critical-css="' . htmlspecialchars($pageType, ENT_QUOTES, 'UTF-8') . '" data-prestaload-critical-css-device="' . htmlspecialchars($device, ENT_QUOTES, 'UTF-8') . '">' . $entry['css'] . '</style>';

        $this->log([
            'result' => 'inject',
            'page_type' => $pageType,
            'device' => $device,
            'size_bytes' => isset($entry['size_bytes']) ? (int) $entry['size_bytes'] : strlen((string) $entry['css']),
            'generated_at' => isset($entry['generated_at']) ? (string) $entry['generated_at'] : '',
        ]);

        return preg_replace('#</head>#i', $styleTag . '</head>', $html, 1);
    }

    private function resolveDevice()
    {
        if (method_exists($this->context, 'isMobile') && $this->context->isMobile() && (!method_exists($this->context, 'isTablet') || !$this->context->isTablet())) {
            return 'mobile';
        }

        return 'desktop';
    }

    private function resolvePageType()
    {
        $controller = '';
        if (isset($this->context->controller->php_self)) {
            $controller = (string) $this->context->controller->php_self;
        }

        if ($controller === '' && isset($this->context->controller->page_name)) {
            $controller = (string) $this->context->controller->page_name;
        }

        $controller = Tools::strtolower($controller);
        if ($controller === 'index') {
            return 'home';
        }

        if (in_array($controller, ['category'], true)) {
            return 'category';
        }

        if (in_array($controller, ['product'], true)) {
            return 'product';
        }

        if (in_array($controller, ['cms'], true)) {
            return 'cms';
        }

        return '';
    }

    private function log(array $payload)
    {
        $payload['logged_at'] = date('c');
        $line = json_encode($payload, JSON_UNESCAPED_SLASHES);
        if ($line === false) {
            return;
        }

        @file_put_contents($this->logFile, $line . "\n", FILE_APPEND);
    }
}
