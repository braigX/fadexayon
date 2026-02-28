<?php
/**
 * 2007-2020 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * @author    Innovadeluxe SL
 * @copyright 2020 Innovadeluxe SL

 * @license   INNOVADELUXE
 */

class IdxrcustomproductFileModuleFrontController extends ModuleFrontController
{
    
    public function initContent()
    {
        //parent::initContent();
        
        $token = Configuration::get(Tools::strtoupper($this->module->name .'_TOKEN'));
        if (!$token) {
            $token_upd = Tools::encrypt(Tools::getShopDomainSsl() . time());
            Configuration::updateGlobalValue(Tools::strtoupper($this->module->name) . '_TOKEN', $token_upd);
        }
        $post_token = Tools::getValue('token');
        if ($post_token !== $token) {
            die('wrong token');
        }
        
        $file_key = Tools::getValue('key');

        $sql = 'select * from ' . _DB_PREFIX_ . 'idxrcustomproduct_files where target_name = "' . pSQL($file_key) . '"';
        $file_info = Db::getInstance()->getRow($sql);

        $url = _PS_ROOT_DIR_ . '/upload/' . $file_info['target_name'];

        $mime = mime_content_type($url);

        $name = $file_info['original_name'];

        // Fetch and serve
        if ($url) {
            $size= filesize($url);

            // Generate the server headers
            if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false) {
                header('Content-Type: "' . $mime . '"');
                header('Content-Disposition: attachment; filename="' . $name . '"');
                header('Expires: 0');
                header('Content-Length: '.$size);
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header("Content-Transfer-Encoding: binary");
                header('Pragma: public');
            } else {
                header('Content-Type: "' . $mime . '"');
                header('Content-Disposition: attachment; filename="' . $name . '"');
                header("Content-Transfer-Encoding: binary");
                header('Expires: 0');
                header('Content-Length: '.$size);
                header('Pragma: no-cache');
            }

            readfile($url);
            exit;
        }

        // Not found
        exit('File not found');
    }
}
