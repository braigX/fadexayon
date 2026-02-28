<?php
/**
 * URL SEO Manager
 *
 * @author    Custom
 * @copyright Custom
 * @license   Commercial
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once dirname(__FILE__) . '/classes/UrlSeoRule.php';

class UrlSeoManager extends Module
{
    protected $protected_controllers = [
        'cart',
        'order',
        'orderopc',
        'authentication',
        'password',
        'identity',
        'address',
        'addresses',
        'discount',
        'history',
        'guest-tracking',
        'order-slip',
        'order-detail',
        'order-follow',
        'order-return',
        'credit-slip',
    ];

    public function __construct()
    {
        $this->name = 'urlseomanager';
        $this->tab = 'seo';
        $this->version = '2.0.0';
        $this->author = 'Custom';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('URL SEO Manager');
        $this->description = $this->l('Manage meta robots, canonicals, and regex rules per URL.');
        $this->ps_versions_compliancy = ['min' => '1.7', 'max' => _PS_VERSION_];
    }

    public function install()
    {
        return parent::install()
            && $this->installDb()
            && $this->registerHook('displayHeader');
    }

    public function uninstall()
    {
        return parent::uninstall()
            && $this->uninstallDb();
    }

    private function installDb()
    {
        return include(dirname(__FILE__) . '/sql/install.php');
    }

    private function uninstallDb()
    {
        return include(dirname(__FILE__) . '/sql/uninstall.php');
    }

    public function getContent()
    {
        Tools::redirectAdmin(
            $this->context->link->getAdminLink('AdminUrlSeoManager')
        );
    }

    public function hookDisplayHeader()
    {
        // 1. Check Protected Controllers -> Force noindex logic if needed
        $controller_name = Dispatcher::getInstance()->getController();
        if (in_array($controller_name, $this->protected_controllers)) {
            // Requirement 10: Exclude protected URLs automatically
            $this->applySeoTags('noindex, nofollow', null);
            return;
        }

        // Current URI
        $request_uri = $_SERVER['REQUEST_URI'];
        $id_shop = (int)$this->context->shop->id;

        // 2. Check Exact Match
        $rule = $this->findExactRule($request_uri, $id_shop);

        // 3. Check Regex Match if no exact match
        if (!$rule) {
            $rule = $this->findRegexRule($request_uri, $id_shop);
        }

        // 4. Apply Rule
        if ($rule) {
            $robots = $rule['robots'];
            $canonical = $rule['canonical'];
            $hreflang = json_decode($rule['hreflang'], true);

            // Requirement 3: Automatic canonical fallback
            if (empty($canonical)) {
                $canonical = $this->getCurrentUrl();
            }

            $this->applySeoTags($robots, $canonical, $hreflang);
        }
    }

    private function findExactRule($uri, $id_shop)
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('url_seo_manager');
        $sql->where('active = 1');
        $sql->where('id_shop = ' . (int)$id_shop);
        $sql->where('is_regex = 0');
        $sql->where('url_pattern = \'' . pSQL($uri) . '\'');

        return Db::getInstance()->getRow($sql);
    }

    private function findRegexRule($uri, $id_shop)
    {
        // Fetch all active regex rules for this shop
        // Optimized: only select active regex patterns
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('url_seo_manager');
        $sql->where('active = 1');
        $sql->where('id_shop = ' . (int)$id_shop);
        $sql->where('is_regex = 1');

        $rules = Db::getInstance()->executeS($sql);

        if ($rules) {
            foreach ($rules as $rule) {
                // Check if pattern is valid regex
                // If not delimited, assume simple match or wrap it?
                // Requirement 1: "patterns". Usually regex in PHP needs delimiters.
                // We'll treat the input as the regex content if it lacks delimiters, or require user to enter full regex.
                // Best UX: auto-wrap if missing delimiters? Or plain string match?
                // User said "Regex support", implying PCRE.
                
                $pattern = $rule['url_pattern'];
                // Check if delimiter exists (assuming / or # or ~)
                if ($pattern[0] !== $pattern[strlen($pattern) - 1] || preg_match('/^[a-zA-Z0-9]/', $pattern)) {
                     // Auto-wrap with #
                     $pattern = '#' . str_replace('#', '\#', $pattern) . '#';
                }

                try {
                    if (preg_match($pattern, $uri)) {
                        return $rule;
                    }
                } catch (Exception $e) {
                    // Invalid regex, continue
                    continue;
                }
            }
        }
        return null;
    }

    private function applySeoTags($robots, $canonical, $hreflang = [])
    {
        if ($robots) {
            // Remove existing header to avoid conflicts if possible (PrestaShop < 1.7.7 might not have easy removals)
            // But we can try to override.
            // Requirement 8: HTML brut + HTML rendu
            $this->context->smarty->assign('usm_robots', $robots);
        }

        if ($canonical) {
            $this->context->smarty->assign('usm_canonical', $canonical);
        }

        if ($hreflang && is_array($hreflang)) {
            $this->context->smarty->assign('usm_hreflang', $hreflang);
        }

        // Render the template
        $output = $this->display(__FILE__, 'views/templates/hook/header_seo.tpl');
        
        // We cannot just return output in hookDisplayHeader in some PS versions without it being echoed immediately 
        // OR concatenated. In 1.7 'displayHeader' expects string return.
        // Also note: PS might add its own. We can't easily remove PS default Canonical from a module without core overrides or tricky JS.
        // However, we can try to set the global smarty page variable to suppress PS ones if the theme uses them carefully.
        
        // Attempt to update $page.meta options (PrestaShop 1.7+)
        $page = $this->context->smarty->getTemplateVars('page');
        if (is_array($page) && isset($page['meta'])) {
            if ($robots) {
                $page['meta']['robots'] = 'noindex'; // Temporary placeholder to suppress default? 
                // Actually, usually themes use {$page.meta.robots}.
                // We should update it.
                $page['meta']['robots'] = $robots;
            }
            if ($canonical) {
                 $page['canonical'] = $canonical;
            }
            $this->context->smarty->assign('page', $page);
        }

        return $output;
    }

    private function getCurrentUrl()
    {
        return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    }
}
