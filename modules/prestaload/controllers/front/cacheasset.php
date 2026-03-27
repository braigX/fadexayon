<?php

class PrestaloadCacheassetModuleFrontController extends ModuleFrontController
{
    public $ssl = true;
    public $ajax = true;
    public $display_column_left = false;
    public $display_column_right = false;

    public function initContent()
    {
    }

    public function display()
    {
        $shopId = (int) Tools::getValue('shop_id');
        $variantKey = trim((string) Tools::getValue('variant_key', ''));
        $type = trim((string) Tools::getValue('type', ''));

        if ($shopId <= 0 || $variantKey === '' || $type !== 'used_css') {
            $this->sendNotFound();
        }

        $path = $this->module->getCacheStoreService()->getUsedCssPath($shopId, $variantKey);
        if (!is_file($path)) {
            $this->sendNotFound();
        }

        $css = @file_get_contents($path);
        if (!is_string($css) || $css === '') {
            $this->sendNotFound();
        }

        header('Content-Type: text/css; charset=utf-8');
        header('Cache-Control: public, max-age=3600');
        header('X-PrestaLoad-Asset: used-css');

        $this->ajaxDie($css);
    }

    private function sendNotFound()
    {
        http_response_code(404);
        $this->ajaxDie('/* not found */');
    }
}
