<?php

use Symfony\Component\Serializer\Encoder\JsonEncode;

/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * @author    Innovadeluxe SL
 * @copyright 2017 Innovadeluxe SL

 * @license   INNOVADELUXE
 */

class IdxrcustomproductAjaxModuleFrontController extends ModuleFrontController
{

    public function initContent()
    {
        parent::initContent();
        $token = Configuration::get(Tools::strtoupper($this->module->name .'_TOKEN'));
        if (!$token) {
            $token_upd = Tools::encrypt(Tools::getShopDomainSsl() . time());
            Configuration::updateGlobalValue(Tools::strtoupper($this->module->name) . '_TOKEN', $token_upd);
        }
        $post_token = Tools::getValue('token');
        if ($post_token == $token) {
            $this->ajaxProcessFront();
        } else {
            die('wrong token');
        }
    }

    public function ajaxProcessFront()
    {
        if (Tools::getValue('action') == 'savefav') {
            $this->ajaxProcessSavefav();
        }

        if (Tools::getValue('action') == 'deletefav') {
            $this->ajaxProcessDeletefav();
        }

        if (Tools::getValue('action') == 'handlesnaps') {
            $this->ajaxHandleSnaps();
        }

        if (Tools::getValue('action') == 'customfile') {
            $this->ajaxProcessCustomfile();
        }

        if (Tools::getValue('action') == 'createproduct') {
            $this->ajaxProcessCreateproduct();
        }

        if (Tools::getValue('action') == 'setCart') {
            $this->ajaxProcessSetCart();
        }

        if (Tools::getValue('action') == 'isCustomized') {
            $this->ajaxProcessIsCustomized();
        }
        
        if (Tools::getValue('action') == 'getParentLink') {
            $this->ajaxProcessGetParentLink();
        }

        if (Tools::getValue('action') == 'getCustomizedData') {
            $this->ajaxProcessGetCustomizedData();
        }

        if (Tools::getValue('action') == 'saveimgstatus') {
            $this->ajaxProcessSaveimgstatus();
        }

        if (Tools::getValue('action') == 'getimgstatus') {
            $this->ajaxProcessGetimgstatus();
        }

        if (Tools::getValue('action') == 'formatprice') {
            $this->ajaxProcessFormatPrice();
        }
        
        if (Tools::getValue('action') == 'refreshimpact') {
            $this->ajaxProcessRefreshImpact();
        }
        
        if (Tools::getValue('action') == 'getTaxChange') {
            $this->ajaxProcessGetTaxChange();
        }
    }

    public function ajaxProcessSavefav()
    {
        $product_id = Tools::getValue('product');
        $attribute_id = Tools::getValue('attribute');
        $customization = explode(',', Tools::getValue('custom'));
        $extra_values = explode('3x7r4', Tools::getValue('extra'));
        $result = $this->module->saveFavorite($product_id, $attribute_id, $customization, $extra_values);
        if ($result == 1) {
            die($this->module->l('Customization saved in you favorite zone', 'ajax'));
        } elseif ($result == 2) {
            die($this->module->l('This customization is already in you favorite zone', 'ajax'));
        } else {
            die($this->module->l('There was any error saving your customization, please try again later', 'ajax'));
        }
    }

    public function ajaxProcessDeletefav()
    {
        $fav_id = Tools::getValue('favid');
        die($this->module->delFavorite($fav_id));
    }

    public function ajaxProcessCustomfile()
    {
        $fileName = $_FILES['file']['name'];
        $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $fileSizeMB = ($_FILES['file']['size'] / 1024) / 1024; // Convert bytes to MB
        $id_component = Tools::getValue('component');
        $id_product = Tools::getValue('product');

        $file_rules_query = 'SELECT * FROM ' . _DB_PREFIX_ . 'idxrcustomproduct_components_lang WHERE id_component = ' . (int)$id_component;
        $file_rules = Db::getInstance()->getRow($file_rules_query);

        // Check if rules exist and are properly retrieved, otherwise set default rules
        if (!$file_rules || !isset($file_rules['json_values'])) {
            // Default rules if not found
            $rules = new stdClass();
            $rules->size = 11;  // default max size in MB
            $rules->allowed_extension = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
        } else {
            $rules = json_decode($file_rules['json_values']);
        }

        // Validate file size against the rules
        if ($fileSizeMB > $rules->size) {
            die($this->module->l('File too big.', 'ajax'));
        }

        // Validate file extension against the rules
        if (!in_array($fileType, $rules->allowed_extension)) {
            die($this->module->l('Invalid format.', 'ajax'));
        }

        // Create directories dynamically based on year and month
        $path0 = DIRECTORY_SEPARATOR .'img'. DIRECTORY_SEPARATOR .'idxrcustomproduct' . DIRECTORY_SEPARATOR . 'uploads'. DIRECTORY_SEPARATOR . date('Y') . DIRECTORY_SEPARATOR . date('m');
        $uploadDir = _PS_ROOT_DIR_ . $path0;
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $md5filename = md5('idxrcustomproduct_' . $id_product . '_' . $id_component . '_' . (int)Context::getContext()->cart->id . '_' . $fileName) . '.' . $fileType;
        $fileTarget = $uploadDir . DIRECTORY_SEPARATOR . $md5filename;

        file_put_contents(__DIR__ . '/logfiles.txt', "path 1 : ".$fileTarget."\n", FILE_APPEND);

        if (move_uploaded_file($_FILES["file"]["tmp_name"], $fileTarget)) {
            $exist_q = 'SELECT id_file FROM ' . _DB_PREFIX_ . 'idxrcustomproduct_files WHERE target_name = "' . pSQL($md5filename) . '"';
            $exist = Db::getInstance()->getRow($exist_q);
            if (!$exist) {
                $data = array(
                    'id_cart' => (int) Context::getContext()->cart->id,
                    'id_product' => (int) $id_product,
                    'id_component' => (int) $id_component,
                    'original_name' => pSQL($fileName),
                    'target_name' => pSQL($md5filename),
                );
                Db::getInstance()->insert('idxrcustomproduct_files', $data);
            }
            die('ok');
        } else {
            $errorMessage = 'Error uploading file from ' . $_FILES["file"]["tmp_name"] . ' to ' . $fileTarget;
            file_put_contents(__DIR__ . '/logfiles.txt', $errorMessage . "\n", FILE_APPEND);
            die($this->module->l('Error uploading file.', 'ajax'));
        }
    }

    public function ajaxHandleSnaps()
    {
        $responses = [];
        $uploadedFiles = [];
        $id_product = (int) Tools::getValue('product');

        // Upload directory setup (shared for files + SVG)
        $path0 = DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'idxrcustomproduct' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . date('Y') . DIRECTORY_SEPARATOR . date('m');
        $uploadDir = _PS_ROOT_DIR_ . $path0;
        if (!is_dir($uploadDir) && !@mkdir($uploadDir, 0777, true) && !is_dir($uploadDir)) {
            $responses['dir'] = $this->module->l('Upload directory is not writable.', 'ajax');
            die(json_encode($responses));
        }
    
        // File types expected
        $requiredFiles = ['file1' => 'png'];
        $index = 1;
        foreach ($requiredFiles as $inputName => $expectedType) {
            if (!isset($_FILES[$inputName])) {
                $responses[$inputName] = $this->module->l('No file uploaded.', 'ajax');
                continue;
            }
    
            $fileName = $_FILES[$inputName]['name'];
            $actualFileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $fileSizeMB = ($_FILES[$inputName]['size'] / 1024) / 1024; // Convert bytes to MB

            // Validate file size
            if ($fileSizeMB > 11) { // 11 MB max size
                $responses[$inputName] = $this->module->l('File too big.', 'ajax');
                continue;
            }
    
            // Validate file extension
            if ($actualFileType !== $expectedType) {
                $responses[$inputName] = $this->module->l('Invalid format. Expected ' . $expectedType, 'ajax');
                continue;
            }
    
            // File saving process
            $timestamp = microtime(true); // Current time in microseconds
            $randomNumber = rand(10000, 99999); // A random 4-digit number
            
            $md5filename = md5('idxrcustomproduct_' . $id_product . '_' . $timestamp . '_' . $randomNumber) . '.' . $actualFileType;

            // $md5filename = md5('idxrcustomproduct_' . $id_product . '_' . (int)Context::getContext()->cart->id . '_' . $fileName) . '.' . $actualFileType;
            $fileTarget = $uploadDir . DIRECTORY_SEPARATOR . $md5filename;
            $fileURL = $this->context->link->getBaseLink() . trim($path0, '/') . '/' . $md5filename;
    
            if (move_uploaded_file($_FILES[$inputName]["tmp_name"], $fileTarget)) {
                $uploadedFiles[$index] = $fileTarget;
                $index++;
                $responses[$inputName] = 'ok';
            } else {
                $responses[$inputName] = $this->module->l('Error uploading file.', 'ajax');
            }
        }
    

        // Handle SVG markup
        $svgMarkup = Tools::getValue('svgMarkup');
        if ($svgMarkup) {
            $timestamp = microtime(true); // Current time in microseconds
            $randomNumber = rand(1000, 9999); // A random 4-digit number
            $svgFileName = 'design_' . md5('idxrcustomproduct_' . $id_product . '_' . $timestamp . '_' . $randomNumber) . '.svg';

            // $svgFileName = 'design_' . md5('idxrcustomproduct_' . $id_product . '_' . (int)Context::getContext()->cart->id) . '.svg';
            $svgFilePath = $uploadDir . DIRECTORY_SEPARATOR . $svgFileName;
            $svgFileURL = $this->context->link->getBaseLink() . trim($path0, '/') . '/' . $svgFileName; // Construct the URL
            if (file_put_contents($svgFilePath, $svgMarkup) !== false) {
                $uploadedFiles['svg'] = $svgFileURL;
                $responses['svg'] = 'ok';
            } else {
                $responses['svg'] = 'Error saving SVG file';
            }
        } else {
            $responses['svg'] = 'No SVG markup provided';
        }

        // Debugging output
        if (isset($uploadedFiles[1])) {
            $data = [
                'id_cart' => (int)Context::getContext()->cart->id,
                'id_product' => (int)$id_product,
                'svg_file' => pSQL($uploadedFiles[1]),
                'svg_code' => pSQL($uploadedFiles['svg']),
                'console' => pSQL(Tools::getValue('console')),
            ];

            // Try to insert and capture the result
            $insertResult = Db::getInstance()->insert('idxrcustomproduct_snaps', $data);
            
            // Check if the insert was successful
            if ($insertResult) {
                $insertedId = Db::getInstance()->Insert_ID();  // Retrieves the last inserted ID
                $responses['id'] = $insertedId;
                $responses['db'] = 'Insert successful';
            } else {
                $responses['db'] = 'Insert failed';
                // Optionally, log last SQL error
                $responses['db_error'] = Db::getInstance()->getMsgError();
            }
        } else {
            $responses['db'] = 'Required files not uploaded';
        }

    
        header('Content-Type: application/json');
        echo json_encode($responses);
        exit;
    }
    

    public function ajaxProcessCreateproduct()
    {
        $product_id = Tools::getValue('product');
        $attribute_id = Tools::getValue('attribute');
        // === PrestaShop Context for customer info ===
        $context = Context::getContext();
        $customerId = $context->customer ? (int)$context->customer->id : null;
        $customerEmail = $context->customer ? $context->customer->email : null;

        /*Add with team wassim novatis*/
        $productWeight = (float)Tools::getValue('product_weight');
        $productVolume = (float)Tools::getValue('product_volume');
        $productWidth = (float)Tools::getValue('product_width');
        $productHeight = (float)Tools::getValue('product_height');
        $productDeptht = (float)Tools::getValue('product_depth');
        $prix_de_decouper = (float)Tools::getValue('prix_de_decoupe');
        $price_from_cube = (float)Tools::getValue('price_from_cube');
        $snaps = (float)Tools::getValue('snaps');
        /*End*/
        $customization = explode(',', Tools::getValue('custom'));
        $extra = explode('3x7r4', Tools::getValue('extra'));

        // $rawExtra = Tools::getValue('extra');
        // $extra = array_map(function ($item) {
        //     // Remove encoded quotes and other entities
        //     $item = html_entity_decode($item, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        //     $item = str_replace('"', '', $item); // just in case
        //     $item = trim($item); // remove whitespace
        //     return $item;
        // }, explode('3x7r4', $rawExtra));

        $quantity = Tools::getValue('quantity')?:1;

        // log backup:
        // === Timestamp ===
        $timestamp = date('Y-m-d H:i:s');

        // === Prepare log entry ===
        $logData = [
            'timestamp' => $timestamp,
            'customer_id' => $customerId,
            'customer_email' => $customerEmail,
            'product_id' => $product_id,
            'attribute_id' => $attribute_id,
            'weight' => $productWeight,
            'volume' => $productVolume,
            'width' => $productWidth,
            'height' => $productHeight,
            'depth' => $productDeptht,
            'prix_de_decouper' => $prix_de_decouper,
            'price_from_cube' => $price_from_cube,
            'snaps' => $snaps,
            'quantity' => $quantity,
            'customization' => $customization,
            'extra' => $extra,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
        ];

        // === Write to log file ===
        $backupDir = __DIR__ . '/backups';
        if ((is_dir($backupDir) || @mkdir($backupDir, 0777, true)) && is_writable($backupDir)) {
            $logFile = $backupDir . '/backup-products-' . date('Y-m') . '.log';
            @file_put_contents($logFile, json_encode($logData, JSON_PRETTY_PRINT) . PHP_EOL, FILE_APPEND);
        }

        foreach ($customization as &$option) {
            $option = explode('_', $option);
            if (substr_count($option[0],'x')) {
                $qty_option = explode('x',$option[0]);
                $option['qty'] = $qty_option[0];
                $option['id_component'] = $qty_option[1];
                unset($qty_option);
            }
            else {
                $option['id_component'] = $option[0];
            }
            $option['id_option'] = $option[1];
            unset($option[0]);
            unset($option[1]);
        }
        // Braigue
        $customization[] = array("id_component" => "99999", "id_option" => mt_rand());
        //

        /*Edit with team wassim novatis*/
        try {
            $this->module->createProduct($product_id, $snaps, $attribute_id, $customization, $extra, $quantity, $productWeight, $productVolume, $productWidth, $productHeight, $productDeptht, $prix_de_decouper, $price_from_cube);
        } catch (\Throwable $th) {
            $moduleLogFile = __DIR__ . '/logfiles.txt';
            if ((file_exists($moduleLogFile) && is_writable($moduleLogFile)) || (!file_exists($moduleLogFile) && is_writable(__DIR__))) {
                @file_put_contents($moduleLogFile, "error : " . $th . "\n", FILE_APPEND);
            }
        }
        /*End */
        die();
    }

    public function ajaxProcessSetCart()
    {
        $context = Context::getContext();
        if (!$context->cart->id) {
            $cart = $context->cart;
            try {
                $cart->save();
                $context->cart = $cart;
                $context->cookie->id_cart = $cart->id;
                $context->cookie->write();
            } catch (Exception $e) {
                PrestashopLogger::addLog($e->getMessage());
            }
        }
    }

    public function ajaxProcessIsCustomized()
    {
        $product_id = Tools::getValue('product_id');
        if ($this->module->getConfigurationByProduct($product_id)) {
            $link = new Link();
            die($link->getProductLink($product_id));
        }
        die(false);
    }
    
    public function ajaxProcessGetParentLink()
    {
        $product_id = Tools::getValue('product_id');
        if ($parent = IdxCustomizedProduct::getParentProduct($product_id)) {
            $link = new Link();
            die($link->getProductLink($parent));
        }
        die(false);
    }

    public function ajaxProcessGetCustomizedData()
    {
        $clean = Tools::isSubmit('clean');
        $id_cart = Tools::getValue('id_cart');
        $extra_info = $this->module->getExtraByContext($clean, $id_cart);
        die(json_encode($extra_info));
    }

    public function ajaxProcessSaveimgstatus()
    {
        $image_id = Tools::getValue('id_image');
        $status_string = Tools::getValue('status');
        $result = IdxConfiguration::saveConfigurationImageStatus($image_id, $status_string);
        die(json_encode('ok'));
    }

    public function ajaxProcessGetimgstatus()
    {
        $id_configuration = Tools::getValue('id_configuration');
        $status_string = Tools::getValue('status');
        if (!$status_string) {
            die(json_encode('ko'));
        }
        $image = IdxConfiguration::getImageidFromStatus($id_configuration, $status_string);
        if ($image) {
            $device = Context::getContext()->getDevice();
            switch ($device) {
                case Context::DEVICE_COMPUTER:
                    $type = Configuration::get(Tools::strtoupper($this->module->name . '_PCIMGTYPE'));
                    break;
                case Context::DEVICE_MOBILE:
                    $type = Configuration::get(Tools::strtoupper($this->module->name . '_MIMGTYPE'));
                    break;
                case Context::DEVICE_TABLET:
                    $type = Configuration::get(Tools::strtoupper($this->module->name . '_TIMGTYPE'));
                    break;
                default:
                    $type = Configuration::get(Tools::strtoupper($this->module->name . '_PCIMGTYPE'));
                    break;
            }
            $image_src = IdxConfiguration::getImage($image, 'src', $type);
            die(json_encode($image_src));
        }
        die(json_encode($image));
    }

    public function ajaxProcessFormatPrice()
    {
        $price = Tools::getValue('price');
        $idxcp_originaltax = Tools::getValue('idxcp_originaltax');
        $idxcp_newtax = Tools::getValue('idxcp_newtax');
        if ($idxcp_newtax && $idxcp_originaltax > 0) {
            $price = ($price/(1+($idxcp_originaltax/100)))*(1+($idxcp_newtax/100));
        }
        $price_formated = Idxrcustomproduct::formatPrice($price);
        die(json_encode($price_formated));
    }
    
    public function ajaxProcessRefreshImpact()
    {
        $configuration = Tools::getValue('configuration');
        $component = Tools::getValue('component');
        $option = Tools::getValue('option');
        $att_product = Tools::getValue('att_product');
        $baseproduct = Tools::getValue('base_product');
        $changes = IdxConfiguration::getImpactChanges($configuration, $component, $option, $att_product, $baseproduct);
        die(json_encode($changes));
    }
    
    public function ajaxProcessGetTaxChange()
    {
        $component = Tools::getValue('component');
        $option = Tools::getValue('option');
        $tax = false;
        if (IdxComponent::hasTaxchange($component)) {
            $tax_id = IdxOption::getTaxChange($component, $option);
            if ($tax_id) {
                $tax = New Tax($tax_id);
            }
        }
        die(json_encode($tax));
    }
}
