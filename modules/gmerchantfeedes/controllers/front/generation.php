<?php
/**
 * 2019 ExtraSolutions
 *
 * NOTICE OF LICENSE
 *
 * @author    ExtraSolutions
 * @copyright 2019 ExtraSolutions
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class GMerchantFeedESGenerationModuleFrontController extends ModuleFrontController
{
    public function __construct()
    {
        $token = Tools::getValue('token');
        if (!Tools::getValue('token') || empty($token)
            || !Tools::getValue('key')
            || md5(_COOKIE_KEY_ . Tools::getValue('key')) != Tools::getValue('token')
        ) {
            Tools::redirect('index.php?controller=index');
        }

        parent::__construct();
    }

    public function initContent()
    {
        if (Tools::getValue('inventory', false)) {
            return $this->module->generationCSVList();
        }

        if (Tools::getValue('only_rebuild', false) == 1) {
            return $this->module->generationList('only_rebuild');
        }

        if (Tools::getValue('only_download')) {
            $generate_path = _PS_MODULE_DIR_ . $this->module->name . DIRECTORY_SEPARATOR . 'export' . DIRECTORY_SEPARATOR . (int)Tools::getValue('key');
            $generate_file = md5(_COOKIE_KEY_ . Tools::getValue('key')) . '.xml';
            $generate_path_file = $generate_path . DIRECTORY_SEPARATOR . $generate_file;

            $download_file_name = Date('m-d-y') . '_google';
            header('Content-disposition: attachment; filename="' . $download_file_name . '.xml"');
            header('Content-Type: text/xml');
            readfile($generate_path_file);

            exit();
        }

        return $this->module->generationList();
    }
}
