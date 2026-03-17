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

    public function __construct(Context $context, PrestaLoadCriticalCssStore $store)
    {
        $this->context = $context;
        $this->store = $store;
    }

    public function optimize($html)
    {
        if (!is_string($html) || stripos($html, '</head>') === false) {
            return $html;
        }

        $pageType = $this->resolvePageType();
        if ($pageType === '') {
            return $html;
        }

        $entry = $this->store->get($pageType);
        if (empty($entry['css'])) {
            return $html;
        }

        $html = preg_replace('#<style[^>]*id="prestaload-critical-css"[^>]*>.*?</style>#is', '', $html);

        $styleTag = '<style id="prestaload-critical-css" data-prestaload-critical-css="' . htmlspecialchars($pageType, ENT_QUOTES, 'UTF-8') . '">' . $entry['css'] . '</style>';

        return preg_replace('#</head>#i', $styleTag . '</head>', $html, 1);
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
}
