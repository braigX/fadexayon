<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * @author    Innovadeluxe SL
 * @copyright 2017 Innovadeluxe SL

 * @license   INNOVADELUXE
 */

if (!defined('_PS_VERSION_')) {
    return false;
}

if (!class_exists('InnovaTools_2_0_0')) {
    require_once(_PS_ROOT_DIR_ . '/modules/idxrcustomproduct/libraries/innovatools_2_0_0.php');
}

require_once(_PS_ROOT_DIR_ . '/modules/idxrcustomproduct/classes/IdxComponent.php');
require_once(_PS_ROOT_DIR_ . '/modules/idxrcustomproduct/classes/IdxConfiguration.php');
require_once(_PS_ROOT_DIR_ . '/modules/idxrcustomproduct/classes/IdxCustomizedProduct.php');

class IdxrCustomProduct extends Module
{
    /*Add with team wassim novatis*/
    public function getProductWeight($product_id)
    {
        // Code pour récupérer le poids du produit en fonction de $product_id
        $product = new Product($product_id);
        return $product->weight;
    }
    public function getProductVolume($product_id)
    {
       // Code pour récupérer le volume du produit en fonction de $product_id
       $product = new Product($product_id);
       return $product->volume; //La propriété du volume soit "volume"

    }
    public function getProductWidth($product_id)
    {
       // Code pour récupérer le volume du produit en fonction de $product_id
       $product = new Product($product_id);
       return $product->width; //La propriété du volume soit "volume"
    }
    public function getProductHeight($product_id)
    {
       // Code pour récupérer le volume du produit en fonction de $product_id
       $product = new Product($product_id);
       return $product->height; //La propriété du volume soit "volume"
    }
    public function getProductDepth($product_id)
    {
       // Code pour récupérer le Epaisseur du produit en fonction de $product_id
       $product = new Product($product_id);
       return $product->depth; //La propriété du Epaisseur soit "Epaisseur"
    }

    public function fetchLatestExtraConfig() {
        // Fetch all rows ordered by `id` in descending order
        $sql = 'SELECT `prix_de_decoupe`, `demensions`, `prix_de_collage`, `prix_fixe`, `prix_fixe_vitrine`, 
                       `cut_price_4mm`, `cut_price_5mm`, `cut_price_6mm`, `cut_price_8mm`, `cut_price_10mm`, 
                       `glue_price_4mm`, `glue_price_5mm`, `glue_price_6mm`, `glue_price_8mm`, `glue_price_10mm`,
                       `polish_price_4mm`, `polish_price_5mm`, `polish_price_6mm`, `polish_price_8mm`, `polish_price_10mm`
                FROM `ps_idxrcustomproduct_extra_config` 
                ORDER BY `id` DESC';
    
        $config = Db::getInstance()->executeS($sql);
    
        // If no rows are found or query fails, handle it
        if ($config === false || empty($config)) {
            // Return default values in case of an error or no result
            return [
                'prix_de_decoupe' => 0.0,
                'demensions' => '800x800x800',
                'prix_de_collage' => 0.0,
                'prix_fixe' => 5,
                'prix_fixe_vitrine' => 90,
                'cut_prices' => [
                    '4mm' => 0.004,
                    '5mm' => 0.004,
                    '6mm' => 0.004,
                    '8mm' => 0.005,
                    '10mm' => 0.006,
                ],
                'glue_prices' => [
                    '4mm' => 0.07,
                    '5mm' => 0.07,
                    '6mm' => 0.07,
                    '8mm' => 0.07,
                    '10mm' => 0.07,
                ],
                'polish_prices' => [
                    '4mm' => 0.003,
                    '5mm' => 0.004,
                    '6mm' => 0.004,
                    '8mm' => 0.005,
                    '10mm' => 0.006,
                ]
            ];
        }
    
        // Extract cut, glue, and polish prices into separate arrays
        $cut_prices = [
            '4mm' => $config[0]['cut_price_4mm'],
            '5mm' => $config[0]['cut_price_5mm'],
            '6mm' => $config[0]['cut_price_6mm'],
            '8mm' => $config[0]['cut_price_8mm'],
            '10mm' => $config[0]['cut_price_10mm'],
        ];
    
        $glue_prices = [
            '4mm' => $config[0]['glue_price_4mm'],
            '5mm' => $config[0]['glue_price_5mm'],
            '6mm' => $config[0]['glue_price_6mm'],
            '8mm' => $config[0]['glue_price_8mm'],
            '10mm' => $config[0]['glue_price_10mm'],
        ];
    
        $polish_prices = [
            '4mm' => $config[0]['polish_price_4mm'],
            '5mm' => $config[0]['polish_price_5mm'],
            '6mm' => $config[0]['polish_price_6mm'],
            '8mm' => $config[0]['polish_price_8mm'],
            '10mm' => $config[0]['polish_price_10mm'],
        ];
    
        // Return the first row (latest config) with added arrays for cut, glue, and polish prices
        return [
            'prix_de_decoupe' => $config[0]['prix_de_decoupe'],
            'demensions' => $config[0]['demensions'],
            'prix_de_collage' => $config[0]['prix_de_collage'],
            'prix_fixe' => $config[0]['prix_fixe'],
            'prix_fixe_vitrine' => $config[0]['prix_fixe_vitrine'],
            'cut_prices' => $cut_prices,
            'glue_prices' => $glue_prices,
            'polish_prices' => $polish_prices
        ];
    }
    
    /**
     * Registers front-office translatable strings for detection by PrestaShop.
     */
    private function registerTplTranslations()
    {
        $this->l('Ajouter au panier');
        $this->l('La valeur doit être entre ${limits.min} mm et ${limits.max} mm');
        $this->l('Veuillez n’entrer qu’un seul chiffre après la virgule.');
        $this->l('Afficher la structure de prix');
        $this->l('Ce champ ne doit pas être vide');
        $this->l('Épaisseur');
        $this->l('Volume');
        $this->l('Surface');
        $this->l('Poids');
        $this->l('Non séléctionné');
        $this->l('Le champ ${fieldName} est requis.');
        $this->l('Veuillez entrer une valeur numérique.');
        $this->l('Veuillez entrer une valeur supérieure à ${min}.');
        $this->l('Veuillez entrer une valeur inférieure ou égale à ${max}.');
        $this->l('Largeur');
        $this->l('Hauteur');
        $this->l('Rayon');
        $this->l('Rayon extérieur');
        $this->l('Rayon intérieur');
        $this->l('La base');
        $this->l('Longueur');
        $this->l('Diamètre');
        $this->l('Côté');
        $this->l('Select Font');
        $this->l('Dimension souhaitée');
        $this->l('Paramètres de Vitrine');
        $this->l('Paramètres de Socle');
        $this->l('Les Dimensions Extérieures');
        $this->l('Vitrine sur mesure');
    }
    
    // /*End*/

    public function __construct()
    {
        $this->name = 'idxrcustomproduct';
        $this->tab = 'checkout';
        $this->version = '1.7.6';
        $this->author_address = '0x899FC2b81CbbB0326d695248838e80102D2B4c53';
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->author = 'Innovadeluxe';
        $this->innovatabs = "";
        $this->doclink = $this->name . "/doc/readme_en.pdf";
        $this->bootstrap = true;
        $this->es17 = version_compare(_PS_VERSION_, '1.7.0.0', '>');
        $this->es160 = version_compare(_PS_VERSION_, '1.6.1.0', '<');
        $this->module_key = 'ae42eb29e5226fd19f5ddfb42738599b';
        $this->new_id_configuration = false;
        $this->new_id_component = false;

        $this->registerTplTranslations();

        parent::__construct();

        if (!HookCore::getIdByName('actionAdminProductsListingResultsModifier')) {
            $hook_action = new Hook();
            $hook_action->name = 'actionAdminProductsListingResultsModifier';
            $hook_action->title = 'actionAdminProductsListingResultsModifier';
            $hook_action->description = 'actionAdminProductsListingResultsModifier';
            $hook_action->add();
        }

        $this->displayName = $this->l('Custom product');
        $this->description = $this->l('Create customization options for customizable products');
        $this->confirmUninstall = $this->l('If you uninstall this module will lose all the configuration already done, are you sure to uninstall? ');
    }

    public function install()
    {

        if (!HookCore::getIdByName('actionAdminMetaAfterWriteRobotsFile')) {
            $hook_action = new Hook();
            $hook_action->name = 'actionAdminMetaAfterWriteRobotsFile';
            $hook_action->title = 'actionAdminMetaAfterWriteRobotsFile';
            $hook_action->description = 'actionAdminMetaAfterWriteRobotsFile';
            $hook_action->add();
        }

        if (!HookCore::getIdByName('actionAdminProductsListingFieldsModifier')) {
            $hook_action = new Hook();
            $hook_action->name = 'actionAdminProductsListingFieldsModifier';
            $hook_action->title = 'actionAdminProductsListingFieldsModifier';
            $hook_action->description = 'actionAdminProductsListingFieldsModifier';
            $hook_action->add();
        }

        include(dirname(__FILE__) . '/sql/install.php');

        $home_type = Db::getInstance()->getValue('
			SELECT `id_image_type`
			FROM `' . _DB_PREFIX_ . 'image_type`
			WHERE `name` = "home_default"');
        if ($home_type) {
            Configuration::updateValue(Tools::strtoupper($this->name) . '_PCIMGTYPE', (int) $home_type);
            Configuration::updateValue(Tools::strtoupper($this->name) . '_PCIMGTYPE', (int) $home_type);
            Configuration::updateValue(Tools::strtoupper($this->name) . '_PCIMGTYPE', (int) $home_type);
            Configuration::updateValue(Tools::strtoupper($this->name) . '_COVERID', '.js-qv-product-cover');
        }

        Configuration::updateValue(Tools::strtoupper($this->name) . '_SHOWFAV', true);
        Configuration::updateValue(Tools::strtoupper($this->name) . '_PRICEIMPACTTAX', true);

        if ($this->es17) {
            $this->registerHook('displayOverrideTemplate');
        }

        $token = Tools::encrypt(Tools::getShopDomainSsl() . time());
        Configuration::updateGlobalValue(Tools::strtoupper($this->name) . '_TOKEN', $token);

        return parent::install() && $this->registerHook('displayBackOfficeTop') 
                && $this->registerHook('displayRightColumnProduct') && $this->registerHook('displayProductTab') 
                && $this->registerHook('displayProductTabContent') && $this->registerHook('displayProductButtons') 
                && $this->registerHook('displayFooterProduct') && $this->registerHook('displayShoppingCartFooter') 
                && $this->registerHook('displayOrderDetail') && $this->registerHook('actionCartSave') 
                && $this->registerHook('actionValidateOrder') && $this->registerHook('actionOrderReturn') 
                && $this->registerHook('actionAdminMetaAfterWriteRobotsFile') && $this->registerHook('displayAdminOrder') 
                && $this->registerHook('displayCustomerAccount') && $this->registerHook('displayHeader') 
                && $this->registerHook('displayPDFInvoice') && $this->registerHook('actionAdminProductsListingFieldsModifier') 
                && $this->registerHook('actionEmailSendBefore') && $this->createAjaxController()
                && $this->registerHook('displayBackOfficeHeader');
    }

    public function createAjaxController()
    {
        $exist = Db::getInstance()->getValue('Select id_tab from ' . _DB_PREFIX_ . 'tab where module = "' . pSQL($this->name) . '" and class_name = "AdminIdxrcustomproduct"');
        if ($exist) {
            return true;
        }
        $tab = new Tab();
        $tab->active = 1;
        $languages = Language::getLanguages(false);
        if (is_array($languages)) {
            foreach ($languages as $language) {
                $tab->name[$language['id_lang']] = $this->name;
            }
        }
        $tab->class_name = 'AdminIdxrcustomproduct';
        $tab->module = $this->name;
        $tab->id_parent = - 1;
        return (bool) $tab->add();
    }

    public function uninstall()
    {
        //include(dirname(__FILE__) . '/sql/uninstall.php');
        $this->removeAjaxController();
        Configuration::deleteByName(Tools::strtoupper($this->name) . '_CATEGORY');
        Configuration::deleteByName(Tools::strtoupper($this->name) . '_PCIMGTYPE');
        Configuration::deleteByName(Tools::strtoupper($this->name) . '_MIMGTYPE');
        Configuration::deleteByName(Tools::strtoupper($this->name) . '_TIMGTYPE');
        Configuration::deleteByName(Tools::strtoupper($this->name) . '_COVERID');
        Configuration::deleteByName(Tools::strtoupper($this->name) - '_SHOWFAV');
        return parent::uninstall();
    }

    public function removeAjaxController()
    {
        $tab_id = Tab::getIdFromClassName('AdminIdxrcustomproduct');
        $tab = new Tab($tab_id);
        if ($tab) {
            $tab->delete();
        }        
    }

    public function getContent()
    {
        $output = $this->innovaTitle();
        $output .= $this->postProcess() . $this->renderForm();
        return $output;
    }

    public function postProcess()
    {
        if ($error = $this->checkTables()){
            return $this->displayError($error);
        }
        $default_cat = (Configuration::get(Tools::strtoupper($this->name . '_CATEGORY')) || Tools::getValue('customizable_category'));
        $locked = true;
        if ($default_cat) {
            $locked = false;
        }
        Media::addJsDef(array('delete_option_query' => $this->l('Are you sure to delete this option?')));
        $url_ajax = '';
        if ($this->es17) {
            $url_ajax = $this->context->link->getAdminLink('AdminIdxrcustomproduct', true, array(), array('ajax' => true));
        } else {
            $url_ajax = $this->context->link->getAdminLink('AdminIdxrcustomproduct') . '&ajax=true';
            Media::addJsDef(
                array(
                    'currency' => $this->context->currency
                )
            );
        }
        Media::addJsDef(
            array(
                'url_ajax' => $url_ajax,
                'confirm_text' => addslashes(htmlspecialchars($this->l('Are you sure to delete?'))),
                'empty_icon_text' => addslashes(htmlspecialchars($this->l('There are not any image uploaded for this component'))),
                'saved_text' => $this->l('Succesfully updated'),
                'alert_text' => $locked ? $this->l('Category not already selected') : false,
                'fu_success' => addslashes(htmlspecialchars($this->l('The file was uploaded successfully!'))),
                'fu_error' => addslashes(htmlspecialchars($this->l('There was an error during file upload!'))),
                'mod_back_url' => AdminController::$currentIndex . '&configure=' . $this->name,
                'token' => Tools::getAdminTokenLite('AdminModules'),
                'savenstay' => $this->l('Save and stay'),
                'apply_text' => $this->l('apply'),
                'to_text' => $this->l('to'),
                'product_repeated_text' => $this->l('Can not select this product cause is already configured in customization id:'),
                'category_repeated_text' => $this->l('Can not select this category cause is already configured in customization id:'),
            )
        );

        $this->context->controller->addjqueryPlugin('sortable');
        $this->context->controller->addJS(
            $this->_path . 'views/js/sortable.js',
            false
        );
        $this->context->controller->addjqueryPlugin('validate');
        $this->context->controller->addJS($this->_path . 'views/js/typeahead.js', false);
        $this->context->controller->addJS($this->_path . 'views/js/idxautocomplete.js', false);
        $this->context->controller->addJS(
            $this->_path . 'views/js/back.js',
            false
        );
        if (Tools::isSubmit('submitModConfiguration')) {
            // save IDs:
            $rawInput = Tools::getValue('idxr_skipped_product_ids');
            $cleanIds = [];
            if (preg_match_all('/\b\d+\b/', $rawInput, $matches)) {
                $cleanIds = array_unique(array_map('intval', $matches[0]));
            }
            $cleanString = implode(',', $cleanIds);
            Configuration::updateValue('idxr_skipped_product_ids', json_encode($cleanIds));
            // Retrieve the values from the form and replace ',' with '.' for numeric fields
            $prixdepecoupe = str_replace(',', '.', Tools::getValue('idxr_prix_de_decoupe_cube'));
            $prixdecollage = str_replace(',', '.', Tools::getValue('prixdecollage'));
            $prix_fixe = str_replace(',', '.', Tools::getValue('prix_fixe'));
            $prix_fixe_vitrine = str_replace(',', '.', Tools::getValue('prix_fixe_vitrine'));
            $maximumdememnsions = Tools::getValue('maximumdememnsions');
        
            // Retrieve cut, glue, and polish prices for each thickness level
            $cut_price_4mm = str_replace(',', '.', Tools::getValue('cut_price_4mm'));
            $cut_price_5mm = str_replace(',', '.', Tools::getValue('cut_price_5mm'));
            $cut_price_6mm = str_replace(',', '.', Tools::getValue('cut_price_6mm'));
            $cut_price_8mm = str_replace(',', '.', Tools::getValue('cut_price_8mm'));
            $cut_price_10mm = str_replace(',', '.', Tools::getValue('cut_price_10mm'));
        
            $glue_price_4mm = str_replace(',', '.', Tools::getValue('glue_price_4mm'));
            $glue_price_5mm = str_replace(',', '.', Tools::getValue('glue_price_5mm'));
            $glue_price_6mm = str_replace(',', '.', Tools::getValue('glue_price_6mm'));
            $glue_price_8mm = str_replace(',', '.', Tools::getValue('glue_price_8mm'));
            $glue_price_10mm = str_replace(',', '.', Tools::getValue('glue_price_10mm'));
        
            $polish_price_4mm = str_replace(',', '.', Tools::getValue('polish_price_4mm'));
            $polish_price_5mm = str_replace(',', '.', Tools::getValue('polish_price_5mm'));
            $polish_price_6mm = str_replace(',', '.', Tools::getValue('polish_price_6mm'));
            $polish_price_8mm = str_replace(',', '.', Tools::getValue('polish_price_8mm'));
            $polish_price_10mm = str_replace(',', '.', Tools::getValue('polish_price_10mm'));
        
            // Check if the record already exists
            $sqlCheck = 'SELECT COUNT(*) as count FROM `ps_idxrcustomproduct_extra_config`';
            $exists = (int) Db::getInstance()->getValue($sqlCheck);
        
            if ($exists > 0) {
                // Update existing record
                $sqlUpdate = 'UPDATE `ps_idxrcustomproduct_extra_config`
                    SET `prix_de_decoupe` = ' . (float) $prixdepecoupe . ',
                        `prix_de_collage` = ' . (float) $prixdecollage . ',
                        `demensions` = "' . pSQL($maximumdememnsions) . '",
                        `prix_fixe` = ' . (float) $prix_fixe . ',
                        `prix_fixe_vitrine` = ' . (float) $prix_fixe_vitrine . ',
                        `cut_price_4mm` = ' . (float) $cut_price_4mm . ',
                        `cut_price_5mm` = ' . (float) $cut_price_5mm . ',
                        `cut_price_6mm` = ' . (float) $cut_price_6mm . ',
                        `cut_price_8mm` = ' . (float) $cut_price_8mm . ',
                        `cut_price_10mm` = ' . (float) $cut_price_10mm . ',
                        `glue_price_4mm` = ' . (float) $glue_price_4mm . ',
                        `glue_price_5mm` = ' . (float) $glue_price_5mm . ',
                        `glue_price_6mm` = ' . (float) $glue_price_6mm . ',
                        `glue_price_8mm` = ' . (float) $glue_price_8mm . ',
                        `glue_price_10mm` = ' . (float) $glue_price_10mm . ',
                        `polish_price_4mm` = ' . (float) $polish_price_4mm . ',
                        `polish_price_5mm` = ' . (float) $polish_price_5mm . ',
                        `polish_price_6mm` = ' . (float) $polish_price_6mm . ',
                        `polish_price_8mm` = ' . (float) $polish_price_8mm . ',
                        `polish_price_10mm` = ' . (float) $polish_price_10mm . '
                    WHERE `id` = 1';
        
                if (!Db::getInstance()->execute($sqlUpdate)) {
                    $this->context->controller->errors[] = $this->l('Failed to update the configuration values');
                }
            } else {
                // Insert a new record
                $sqlInsert = 'INSERT INTO `ps_idxrcustomproduct_extra_config`
                              (`id`, `prix_de_decoupe`, `prix_de_collage`, `demensions`, `prix_fixe`, `prix_fixe_vitrine`, 
                               `cut_price_4mm`, `cut_price_5mm`, `cut_price_6mm`, `cut_price_8mm`, `cut_price_10mm`,
                               `glue_price_4mm`, `glue_price_5mm`, `glue_price_6mm`, `glue_price_8mm`, `glue_price_10mm`,
                               `polish_price_4mm`, `polish_price_5mm`, `polish_price_6mm`, `polish_price_8mm`, `polish_price_10mm`)
                              VALUES (1, ' . (float) $prixdepecoupe . ', ' . (float) $prixdecollage . ', 
                                      "' . pSQL($maximumdememnsions) . '", ' . (float) $prix_fixe . ', ' . (float) $prix_fixe_vitrine . ',
                                      ' . (float) $cut_price_4mm . ', ' . (float) $cut_price_5mm . ', 
                                      ' . (float) $cut_price_6mm . ', ' . (float) $cut_price_8mm . ', ' . (float) $cut_price_10mm . ',
                                      ' . (float) $glue_price_4mm . ', ' . (float) $glue_price_5mm . ', 
                                      ' . (float) $glue_price_6mm . ', ' . (float) $glue_price_8mm . ', ' . (float) $glue_price_10mm . ',
                                      ' . (float) $polish_price_4mm . ', ' . (float) $polish_price_5mm . ', 
                                      ' . (float) $polish_price_6mm . ', ' . (float) $polish_price_8mm . ', ' . (float) $polish_price_10mm . ')';
        
                if (!Db::getInstance()->execute($sqlInsert)) {
                    $this->context->controller->errors[] = $this->l('Failed to insert the configuration values');
                }
            }
            
        
            $id_category = Tools::getValue('customizable_category');
            Configuration::updateValue(Tools::strtoupper($this->name) . '_CATEGORY', (int) $id_category);
            $this->updateRobotsFile();
            $clone_category = Tools::getValue('clone_category');
            Configuration::updateValue(Tools::strtoupper($this->name) . '_CLONECAT', $clone_category);
            $pc_image_type = Tools::getValue('pc_image_type');
            Configuration::updateValue(Tools::strtoupper($this->name) . '_PCIMGTYPE', (int) $pc_image_type);
            $mobile_image_type = Tools::getValue('mobile_image_type');
            Configuration::updateValue(Tools::strtoupper($this->name) . '_MIMGTYPE', (int) $mobile_image_type);
            $tablet_image_type = Tools::getValue('tablet_image_type');
            Configuration::updateValue(Tools::strtoupper($this->name) . '_TIMGTYPE', (int) $tablet_image_type);
            $coverimageid = Tools::getValue('coverimageid');
            Configuration::updateValue(Tools::strtoupper($this->name) . '_COVERID', $coverimageid);
            $showfav = Tools::getValue('show_fav');
            Configuration::updateValue(Tools::strtoupper($this->name) . '_SHOWFAV', $showfav);
            $price_impact_taxinclude = Tools::getValue('price_impact_taxinclude');
            Configuration::updateValue(Tools::strtoupper($this->name) . '_PRICEIMPACTTAX', $price_impact_taxinclude);
            $discount_line = Tools::getValue('discount_line');
            Configuration::updateValue(Tools::strtoupper($this->name) . '_DISCOUNTLINE', $discount_line);
            $maxheightdescription = Tools::getValue('maxheightdescription');
            Configuration::updateValue(Tools::strtoupper($this->name) . '_MAXHEIGHTDESCRIPTION', $maxheightdescription);
            $princeProductList = Tools::getValue('priceProductList');
            Configuration::updateValue(Tools::strtoupper($this->name) . '_PRICEPRODUCTLIST', $princeProductList);
            $addidname = Tools::getValue('addidname');
            Configuration::updateValue(Tools::strtoupper($this->name) . '_ADDIDNAME', $addidname);
            $breakdownblock = Tools::getValue('breakdownblock');
            Configuration::updateValue(Tools::strtoupper($this->name) . '_BREAKDOWNBLOCK', $breakdownblock);
            $adminproductinfoblock = Tools::getValue('adminproductinfoblock');
            Configuration::updateValue(Tools::strtoupper($this->name) . '_ADMINPRODUCTINFOBLOCK', $adminproductinfoblock);
            
            return $this->displayConfirmation($this->l('Configuration saved'));
        }
        if (Tools::isSubmit('submitConfiguration') || Tools::isSubmit('submitConfigurationStay')) {
            $name = Tools::getValue('addconftitle');
            $id_configuration = Tools::getValue('id_configuration');
            $new_id_configuration = $this->addConfiguration($name, $id_configuration);
            if (Tools::isSubmit('submitConfigurationStay')) {
                $this->new_id_configuration = $new_id_configuration;
            }
            return $this->displayConfirmation($this->l('Configuration saved'));
        }
        if (Tools::isSubmit('submitCloneConfiguration')) {
            $source_id = Tools::getValue('configuration_source');
            if ($source_id && $configuracion_fuente = new IdxConfiguration($source_id)) {
                $this->new_id_configuration = $configuracion_fuente->clonar();
                return $this->displayConfirmation($this->l('Configuration cloned'));
            }
        }
        if (Tools::isSubmit('submitUpdateFileConfiguration') || Tools::isSubmit('submitUpdateFileConfigurationStay')) {
            $id_component = (int) Tools::getValue('id_component');
            $file_size = (int) Tools::getValue('max_size');
            $post = Tools::getAllValues();
            $extensions = array();
            foreach ($post as $key => $value) {
                if (Tools::substr($key, 0, 4) == "ext_") {
                    $extensions[] = str_replace('ext_', '', $key);
                }
            }
            $this->updateOptionFile($id_component, $file_size, $extensions);
            return $this->displayConfirmation($this->l('Configuration saved'));
        }
        if (Tools::isSubmit('activeidxrcustomproduct_configurations')) {
            $id = Tools::getValue('id_configuration');
            $this->activeConfiguration($id);
            return $this->displayConfirmation($this->l('Configuration saved'));
        }
        if (Tools::isSubmit('submitComponent') || Tools::isSubmit('submitComponentStay')) {
            $languages = Language::getLanguages(false);
            $title = array();
            $description = array();
            foreach ($languages as $lang) {
                $title[$lang['id_lang']] = Tools::getValue('title_' . (int) $lang['id_lang']);
                $description[$lang['id_lang']] = Tools::getValue('description_' . (int) $lang['id_lang']);
            }
            $name = Tools::getValue('name');
            $type = Tools::getValue('type');
            $columns = Tools::getValue('columns');
            $id_component = $this->addComponent($name, $title, $description, $type, $columns);
            $this->new_id_component = $id_component;
            if ($id_component && isset($_FILES['icon']) && isset($_FILES['icon']['tmp_name']) && !empty($_FILES['icon']['tmp_name'])) {
                $this->saveComponentIcon($id_component);
            }
            return $this->displayConfirmation($this->l('Configuration saved'));
        }
        if (Tools::isSubmit('editComponent') || Tools::isSubmit('editComponentStay')) {
            $id_component = Tools::getValue('id_component');
            $component = new IdxComponent($id_component);
            $component->fillObject();
            $languages = Language::getLanguages(false);
            foreach ($languages as $lang) {
                $component->title_lang[$lang['id_lang']] = Tools::getValue('title_' . (int) $lang['id_lang']);
                $component->description_lang[$lang['id_lang']] = Tools::getValue('description_' . (int) $lang['id_lang']);
            }
            $component->name = Tools::getValue('name');
            $component->type = Tools::getValue('type');
            $component->optional = Tools::getValue('optional');
            $component->columns = Tools::getValue('columns');
            $component->zoom = Tools::getValue('zoom_icon');
            $component->show_price = Tools::getValue('button_impact');
            $component->multivalue = Tools::getValue('multivalue');
            $component->color = Tools::getValue('component_color');
            if ($id_component && isset($_FILES['icon']) && isset($_FILES['icon']['tmp_name']) && !empty($_FILES['icon']['tmp_name'])) {
                $this->saveComponentIcon($id_component);
            }
            $component->update();

            return $this->displayConfirmation($this->l('Configuration saved'));
        }
        if (Tools::isSubmit('deleteidxrcustomproduct_configurations')) {
            $id_configuration = Tools::getValue('id_configuration');
            $this->deleteConfiguration($id_configuration);
            return $this->displayConfirmation($this->l('Configuration deleted'));
        }
        if (Tools::isSubmit('deleteidxrcustomproduct_components') || Tools::isSubmit('deletecomponent')) {
            $id_component = Tools::getValue('id_component');
            $this->deleteComponent($id_component);
            return $this->displayConfirmation($this->l('Component deleted'));
        }
        if (Tools::isSubmit('submitCloneComponent')) {
            $id_component = Tools::getValue('component_source');
            $component = new IdxComponent($id_component);
            $this->new_id_component = $component->clonar();
        }
        if (Tools::isSubmit('submitGenerateComponent')) {
            $id_product = Tools::getValue('product_source');
            $this->new_id_component = IdxComponent::generateFromProduct($id_product);
        }
    }

    public function checkTables()
    {
        $exist_addbasecol = Db::getInstance()->getValue('SELECT column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = "' . _DB_PREFIX_ . 'idxrcustomproduct_configurations" AND column_name = "add_base";');
        if (!$exist_addbasecol) {
            Db::getInstance()->execute('ALTER TABLE ' . _DB_PREFIX_ . 'idxrcustomproduct_configurations ADD add_base tinyint(1) NOT NULL DEFAULT 1;');
        }
        $exist_discount = Db::getInstance()->getValue('SELECT column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = "' . _DB_PREFIX_ . 'idxrcustomproduct_configurations" AND column_name = "discount";');
        if (!$exist_discount) {
            Db::getInstance()->execute('ALTER TABLE ' . _DB_PREFIX_ . 'idxrcustomproduct_configurations ADD discount tinyint(1) NOT NULL DEFAULT 0;');
            Db::getInstance()->execute('ALTER TABLE ' . _DB_PREFIX_ . 'idxrcustomproduct_configurations ADD discount_type varchar(255);');
            Db::getInstance()->execute('ALTER TABLE ' . _DB_PREFIX_ . 'idxrcustomproduct_configurations ADD discount_amount decimal(17,2);');
        }
        $exist_discount_createdas = Db::getInstance()->getValue('SELECT column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = "' . _DB_PREFIX_ . 'idxrcustomproduct_configurations" AND column_name = "discount_createdas";');
        if (!$exist_discount_createdas) {
            Db::getInstance()->execute('ALTER TABLE ' . _DB_PREFIX_ . 'idxrcustomproduct_configurations ADD discount_createdas varchar(255);');
        }
        $exist_impact = Db::getInstance()->getValue('SELECT column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = "' . _DB_PREFIX_ . 'idxrcustomproduct_components_opt_impact" AND column_name = "price_impact_type";');
        if (!$exist_impact) {
            Db::getInstance()->execute('ALTER TABLE ' . _DB_PREFIX_ . 'idxrcustomproduct_components_opt_impact ADD price_impact_type varchar(255) DEFAULT "fixed";');
            Db::getInstance()->execute('ALTER TABLE ' . _DB_PREFIX_ . 'idxrcustomproduct_components_opt_impact ADD price_impact_calc varchar(255);');
        }
        return IdxOption::fixOptionProducts();
    }

    public function hookDisplayRightColumnProduct($params)
    {
        return $this->displayCustomization($params, 'hookDisplayRightColumnProduct');
    }

    public function hookDisplayProductButtons($params)
    {
        if (Tools::getValue('action') == 'quickview') {
            return;
        }

        return $this->displayCustomization($params, 'hookDisplayProductButtons');
    }

    public function hookdisplayProductTab($params)
    {
        return $this->displayCustomization($params, 'hookdisplayProductTab');
    }

    public function hookdisplayProductTabContent($params)
    {
        return $this->displayCustomization($params, 'hookdisplayProductTabContent');
    }

    public function hookDisplayFooterProduct($params)
    {
        return $this->displayCustomization($params, 'hookDisplayFooterProduct');
    }

    /**
     * Pass translated strings to JavaScript using addJsDef.
     */
    private function passTranslationsToJs()
    {
        $context = Context::getContext();
        $id_lang = $context->language->id;

        $translations = [
            'idxr_tr_add_to_cart' => $this->l('Ajouter au panier'),
            'idxr_tr_limits_range' => $this->l('La valeur doit être entre ${limits.min} mm et ${limits.max} mm'),
            'idxr_tr_one_decimal' => $this->l('Veuillez n’entrer qu’un seul chiffre après la virgule.'),
            'idxr_tr_show_price_structure' => $this->l('Afficher la structure de prix'),
            'idxr_tr_chemp_vide' => $this->l('Ce champ ne doit pas être vide'),
            'idxr_tr_epaisseur' => $this->l('Épaisseur'),
            'idxr_tr_volume' => $this->l('Volume'),
            'idxr_tr_surface' => $this->l('Surface'),
            'idxr_tr_weight' => $this->l('Poids'),
            'idxr_tr_not_selected' => $this->l('Non séléctionné'),
            'idxr_tr_required_field' => $this->l('Le champ ${fieldName} est requis.'),
            'idxr_tr_numeric_required' => $this->l('Veuillez entrer une valeur numérique.'),
            'idxr_tr_min_required' => $this->l('Veuillez entrer une valeur supérieure à ${min}.'),
            'idxr_tr_max_required' => $this->l('Veuillez entrer une valeur inférieure ou égale à ${max}.'),
            'idxr_tr_width' => $this->l('Largeur'),
            'idxr_tr_height' => $this->l('Hauteur'),
            'idxr_tr_radius' => $this->l('Rayon'),
            'idxr_tr_outer_radius' => $this->l('Rayon extérieur'),
            'idxr_tr_inner_radius' => $this->l('Rayon intérieur'),
            'idxr_tr_base' => $this->l('La base'),
            'idxr_tr_length' => $this->l('Longueur'),
            'idxr_tr_diameter' => $this->l('Diamètre'),
            'idxr_tr_side' => $this->l('Côté'),
            'idxr_tr_select_font' => $this->l('Select Font'),
            'idxr_tr_desired_dimension' => $this->l('Dimension souhaitée'),
            'idxr_tr_display_settings' => $this->l('Paramètres de Vitrine'),
            'idxr_tr_base_settings' => $this->l('Paramètres de Socle'),
            'idxr_tr_outer_dimensions' => $this->l('Les Dimensions Extérieures'),
            'idxr_tr_custom_display' => $this->l('Vitrine sur mesure'),
        ];

        Media::addJsDef($translations);
    }

    public function hookdisplayHeader($params)
    {
        if (Tools::getValue('ajax')) {
            return '';
        }
        Media::addJsDef(
            array(
                'custom_products' => IdxConfiguration::getCustomProducts(),
                'configure_text' => $this->l('Configure')
            )
        );
        if ($this->es17) {
            // $controller = $this->context->controller->php_self;

            // // Clean URI to ignore query params and anchors
            // $uri = strtok($_SERVER['REQUEST_URI'], '?#');

            // if (($controller == 'product') || ($controller == 'index' || $uri == '/')) {
            //     // product page logic
            // } elseif ($controller == 'index' || $uri == '/') {
            //     // home page logic
            // }

            if ($this->context->controller->php_self == 'product') {
                $id_product = (int) Tools::getValue('id_product');
                $id_configuration = $this->getConfigurationByProduct($id_product);
                if ($id_configuration) {
                    $minimal_qty = Db::getInstance()->getValue('select minimal_quantity from ' . _DB_PREFIX_ . 'product_attribute where id_product = ' . (int) $id_product . ' and default_on = 1');
                    if (!$minimal_qty) {
                        $minimal_qty = Db::getInstance()->getValue('select minimal_quantity from ' . _DB_PREFIX_ . 'product where id_product = ' . (int) $id_product);
                    }
                    $steps = $this->getConfigurationFront($id_configuration);
                    $block = preg_replace('/[ ]{2,}|[\t]|\r|\n/', ' ', $this->getToppriceBlock($id_product));
                    $top_price_block = json_encode($block);
                    $front_token = Configuration::get(Tools::strtoupper($this->name .'_TOKEN'));
                    $specific_priceranges = SpecificPrice::getQuantityDiscounts($id_product, 
                        $this->context->shop->id,
                        $this->context->currency->id,
                        $this->context->country->id,
                        Group::getCurrent()->id,
                        null, 
                        false, 
                        $this->context->customer->id
                    );
                    $current_tax = 0;
                    if (!Group::getPriceDisplayMethod($this->context->customer->id_default_group)) {
                        $current_tax = Tax::getProductTaxRate($id_product);  
                    }
                    $extraConfig = $this->fetchLatestExtraConfig();
                    
                    // Extract the values from the returned array
                    $jsonidxr_skipped_product_ids = Configuration::get('idxr_skipped_product_ids');
                    $skippedIds = json_decode($jsonidxr_skipped_product_ids, true);

                    if (!is_array($skippedIds)) {
                        $skippedIds = []; // fallback if decoding fails
                    }

                    $idxr_prix_de_decoupe_cube = $extraConfig['prix_de_decoupe'];
                    $idxr_prix_fixe = $extraConfig['prix_fixe'];
                    $idxr_prix_fixe_vitrine = $extraConfig['prix_fixe_vitrine'];
                    $cutPrices = $extraConfig['cut_prices'];
                    $gluePrices = $extraConfig['glue_prices'];
                    $polishPrices = $extraConfig['polish_prices'];
                    
                    $this->passTranslationsToJs();

                    Media::addJsDef(
                        array(
                            'idxcp_originaltax' => $current_tax,
                            'idxr_prix_fixe' => $idxr_prix_fixe,
                            'idxr_prix_fixe_vitrine' => $idxr_prix_fixe_vitrine,
                            'idxr_skipped_product_ids' => $skippedIds,
                            'idxr_prix_de_decoupe_cube' => $idxr_prix_de_decoupe_cube,
                            'idxcp_console_state' => 0,
                            'idxcp_cut_prices' => $cutPrices,
                            'idxcp_glue_prices' => $gluePrices,
                            'idxcp_id_configuration' => $id_configuration,
                            'idxcp_id_product' => $id_product,
                            'idxcp_polish_prices' => $polishPrices,
                            'url_ajax' => $this->context->link->getModuleLink($this->name, 'ajax', array('token' => $front_token, 'ajax' => true)),
                            'send_text' => $this->l('Send to cart'),
                            'favbutton' => $this->l('Save in my wishlist'),
                            'not_finish' => $this->l('You must fill all the customization options before to make the order'),
                            'next_text' => $this->l('Next'),
                            'finish_text' => $this->l('Finish'),
                            'show_topprice' => $steps['show_topprice'],
                            'toppriceblock' => $top_price_block,
                            'first_open' => $steps['first_open'],
                            'resume_open' => $steps['resume_open'],
                            'button_section' => $steps['button_section'],
                            'step_active' => $steps['step_active'],
                            'image_editor' => Tools::getValue('idxcpeditimages'),
                            'cover_image_id' => Configuration::get(Tools::strtoupper($this->name . '_COVERID')),
                            'show_fav' => (Configuration::get(Tools::strtoupper($this->name . '_SHOWFAV')) && $this->context->customer->islogged())?1:0,
                            'min_qty' => $minimal_qty?$minimal_qty:1,
                            'file_ext_error' => $this->l('This type of file is not allowed, please check allowed extension list'),
                            'file_syze_error' => $this->l('The file is bigger than allow size limit'),
                            'specific_price_ranges' => $specific_priceranges,
                            'idxcp_max_description_height' => Configuration::get(Tools::strtoupper($this->name . '_MAXHEIGHTDESCRIPTION')),
                            'icp_editing' => Tools::getValue('icp_edit'),
                        )
                    );

                    $favorite = Tools::getValue('icp');
                    if ($favorite) {
                        $this->applyfavorite(str_replace('_', '-', $favorite));
                    }
                    // Define a general version for your assets
                    $assetVersion = '1.0.0';
                    

                    $this->context->controller->registerStylesheet('modules-idxcpfrontcss', 'modules/' . $this->name . '/views/css/idxrcustomproduct.css', ['media' => 'all', 'priority' => 150]);
                    $this->context->controller->registerStylesheet('modules-idxcpfrontcss', 'modules/' . $this->name . '/views/css/17/front.css', ['media' => 'all', 'priority' => 150]);
                    $this->context->controller->registerJavascript('modules-idxcpfrontjs', 'modules/' . $this->name . '/views/js/front.js', ['position' => 'bottom', 'priority' => 150]);
                    $this->context->controller->registerStylesheet('modules-idxcpfront' . $steps['visualization'] . 'css', 'modules/' . $this->name . '/views/css/17/front_' . $steps['visualization'] . '.css', array('media' => 'all', 'priority' => 150));
                    $visualizationJsUri = 'modules/' . $this->name . '/views/js/front_' . $steps['visualization'] . '.js';
                    $visualizationJsPath = _PS_MODULE_DIR_ . $this->name . '/views/js/front_' . $steps['visualization'] . '.js';
                    $isJsThemeCacheEnabled = (bool) Configuration::get('PS_JS_THEME_CACHE');
                    if (_PS_MODE_DEV_ && !$isJsThemeCacheEnabled && file_exists($visualizationJsPath)) {
                        $visualizationJsUri .= '?v=' . (int) filemtime($visualizationJsPath);
                    }
                    $this->context->controller->registerJavascript(
                        'modules-idxcpfront' . $steps['visualization'] . 'js',
                        $visualizationJsUri,
                        array('position' => 'bottom', 'priority' => 100)
                    );

                    if ($steps['visualization'] == 'minified') {
                        $this->context->controller->registerStylesheet('modules-idxcpfront-bootstrap-select.min.css', 'modules/' . $this->name . '/views/css/bootstrap-select.min.css', array('media' => 'all', 'priority' => 150));
                        $this->context->controller->registerJavascript('modules-idxcpfront-bootstrap-select.min.js', 'modules/' . $this->name . '/views/js/bootstrap-select.min.js', array('position' => 'bottom', 'priority' => 150));
                    }

                    if (Tools::getValue('idxcpeditimages')) {
                        $this->context->controller->registerStylesheet('modules-idxcpslickcss', 'modules/' . $this->name . '/libraries/slick/slick.css', ['media' => 'all', 'priority' => 150]);
                        $this->context->controller->registerStylesheet('modules-idxcpslickcsstheme', 'modules/' . $this->name . '/libraries/slick/slick-theme.css', ['media' => 'all', 'priority' => 150]);
                        $this->context->controller->registerJavascript('modules-idxcpslickjs', 'modules/' . $this->name . '/libraries/slick/slick.min.js', ['position' => 'bottom', 'priority' => 150]);
                    }
                }
            } 
            $extra_info = $this->getExtraByContext();
            if (count($extra_info)) {
                $json_extra_info = json_encode($extra_info, JSON_UNESCAPED_UNICODE);
                Media::addJsDef(array(
                    'icp_extrainfo' => $json_extra_info, 
                    'icp_qtyblock_text' => $this->l('This product have text/files customized if want add more please go to product and configure from there')
                    )
                );

                $this->context->controller->registerStylesheet('modules-idxcpfront-cart.css', 'modules/' . $this->name . '/views/css/17/front_cart.css', array('media' => 'all', 'priority' => 150));
                $this->context->controller->registerJavascript('modules-idxcpcartjs', 'modules/' . $this->name . '/views/js/icp_order17.js', array('position' => 'bottom', 'priority' => 150));
            }
            $this->context->controller->addCSS($this->_path . 'views/css/16/front_header.css', 'all');
            $front_token = Configuration::get(Tools::strtoupper($this->name .'_TOKEN'));
            Media::addJsDef(
                array(
                    'url_ajax' => $this->context->link->getModuleLink($this->name, 'ajax', array('token' => $front_token, 'ajax' => true)),
                    'add_text' => $this->l('Customize'),
                    'show_conf_text' => $this->l('Show customization'),
                    'min_price_text' => $this->l('Price from'),
                    'idxcp_show_price_list' => Configuration::get(Tools::strtoupper($this->name . '_PRICEPRODUCTLIST')),
                    'idxcp_show_breakdowninfo' => Configuration::get(Tools::strtoupper($this->name . '_BREAKDOWNBLOCK')),
                )
            );
            $this->context->controller->registerStylesheet('modules-idxcpfront-idxopc.css', 'modules/' . $this->name . '/views/css/17/idxopc.css', array('media' => 'all', 'priority' => 150));
            $this->context->controller->registerJavascript('modules-idxcpajaxcartjs', 'modules/' . $this->name . '/views/js/icp_cart17.js', array('position' => 'bottom', 'priority' => 150));
        } else {
            $this->context->controller->addJS($this->_path . 'views/js/icp_cart16.js', false);
            if ($this->context->controller->php_self == 'order-opc' && isset($this->context->controller->name_module) && $this->context->controller->name_module == 'onepagecheckoutps') {
                $this->hookdisplayShoppingCartFooter($params);
            }
        }
    }

    public function displayCustomization($params, $hook = '')
    {
        $id_product = (int) Tools::getValue('id_product');
        
        // $product_ids = [16648, 15897, 17959];
        $product_ids = json_decode(Configuration::get('idxr_skipped_product_ids'), true);
        if (!is_array($product_ids)) {
            $product_ids = [];
        }

        $use_attribute_price = in_array($id_product, $product_ids);

        // Get combination/attribute ID from selected variant
        $groups = Tools::getValue('group', []);
        $id_product_attribute = 0;
        if (!empty($groups)) {
            $id_product_attribute = (int) Product::getIdProductAttributeByIdAttributes($id_product, $groups);
        }
        if (!$id_product_attribute) {
            $id_product_attribute = (int) Product::getDefaultAttribute($id_product);
        }

        $id_configuration = $this->getConfigurationByProduct($id_product, $hook);
        $favorite = Tools::getValue('icp');
        if ($id_configuration) {
            $minimal_qty = Db::getInstance()->getValue('select minimal_quantity from ' . _DB_PREFIX_ . 'product_attribute where id_product = ' . (int) $id_product . ' and default_on = 1');
            if (!$minimal_qty) {
                $minimal_qty = Db::getInstance()->getValue('select minimal_quantity from ' . _DB_PREFIX_ . 'product where id_product = ' . (int) $id_product);
            }
            $steps = $this->getConfigurationFront($id_configuration);
            $specific_price = SpecificPrice::getSpecificPrice($id_product,
                $this->context->shop->id,
                $this->context->currency->id,
                $this->context->country->id,
                Group::getCurrent()->id,
                1,
                null,
                $this->context->customer->id
            );
            if ($specific_price) {
                $steps["discount"] = 1;
                $steps["discount_type"] = $specific_price["reduction_type"];                
                $steps["discount_amount"] = $specific_price["reduction"];
                if ($specific_price["reduction_type"] == "percentage") {
                    $steps["discount_amount"] *= 100;
                }
            }
            
            if (!$this->es17) {
                $front_token = Configuration::get(Tools::strtoupper($this->name .'_TOKEN'));
                $specific_priceranges = SpecificPrice::getQuantityDiscounts($id_product, 
                    $this->context->shop->id,
                    $this->context->currency->id,
                    $this->context->country->id,
                    Group::getCurrent()->id,
                    null, 
                    false, 
                    $this->context->customer->id
                );
                $current_tax = 0;
                if (!Group::getPriceDisplayMethod($this->context->customer->id_default_group)) {
                    $current_tax = Tax::getProductTaxRate($id_product);  
                }

                $extraConfig = $this->fetchLatestExtraConfig();
                            
                // Extract the values from the returned array
                $idxr_skipped_product_ids = json_decode(Configuration::get('idxr_skipped_product_ids'), true);
                $idxr_prix_fixe = $extraConfig['prix_fixe'];
                $idxr_prix_fixe_vitrine = $extraConfig['prix_fixe_vitrine'];
                $idxr_prix_de_decoupe_cube = $extraConfig['prix_de_decoupe'];
                $cutPrices = $extraConfig['cut_prices'];
                $gluePrices = $extraConfig['glue_prices'];
                $polishPrices = $extraConfig['polish_prices'];
                
                Media::addJsDef(
                    array(
                        'idxcp_id_configuration' => $id_configuration,
                        'idxcp_id_product' => $id_product,
                        'idxcp_cut_prices' => $cutPrices,
                        'idxcp_glue_prices' => $gluePrices,
                        'idxcp_polish_prices' => $polishPrices,
                        'idxr_skipped_product_ids' => $idxr_skipped_product_ids,
                        'idxr_prix_de_decoupe_cube' => $idxr_prix_de_decoupe_cube,
                        'url_ajax' => $this->context->link->getModuleLink($this->name, 'ajax', array('token' => $front_token,'ajax' => true)),
                        'send_text' => $this->l('Send to cart'),
                        'favbutton' => $this->l('Save in my wishlist'),
                        'not_finish' => $this->l('You must fill all the customization options before to make the order'),
                        'show_topprice' => $steps['show_topprice'],
                        'first_open' => $steps['first_open'],
                        'step_active' => $steps['step_active'],
                        'image_editor' => Tools::getValue('idxcpeditimages'),
                        'cover_image_id' => Configuration::get(Tools::strtoupper($this->name . '_COVERID')),
                        'show_fav' => (Configuration::get(Tools::strtoupper($this->name . '_SHOWFAV')) && $this->context->customer->islogged())?1:0,
                        'min_qty' => $minimal_qty ? $minimal_qty : 1,
                        'cart_link' => $this->context->link->getPageLink('cart'),
                        'cart_token' => Tools::getToken(false),
                        'specific_price_ranges' => $specific_priceranges,
                        'icp_editing' => Tools::getValue('icp_edit'),
                        'idxcp_max_description_height' => Configuration::get(Tools::strtoupper($this->name . '_MAXHEIGHTDESCRIPTION')),
                        'file_ext_error' => $this->l('This type of file is not allowed, please check allowed extension list'),
                        'file_syze_error' => $this->l('The file is bigger than allow size limit'),
                        'idxcp_originaltax' => $current_tax,
                        'idxr_prix_fixe' => $idxr_prix_fixe,
                        'idxr_prix_fixe_vitrine' => $idxr_prix_fixe_vitrine,
                        'idxcp_console_state' => 0,
                        'idxcp_cut_prices' => $cutPrices,
                        'idxcp_glue_prices' => $gluePrices,
                    )
                );
                if ($steps['show_topprice']) {
                    $block = str_replace('"', '\"', preg_replace('/[ ]{2,}|[\t]|\r|\n/', ' ', $this->getToppriceBlock($id_product)));
                    $top_price_block = json_encode($block);
                    Media::addJsDef(array('toppriceblock' => $top_price_block));
                } else {
                    Media::addJsDef(array('toppriceblock' => ''));
                }
                if ($favorite) {
                    $this->applyfavorite(str_replace('_', '-', $favorite));
                }
                if (Tools::getValue('idxcpeditimages')) {
                    $this->context->controller->addCSS($this->_path . 'libraries/slick/slick.css', 'all');
                    $this->context->controller->addCSS($this->_path . 'libraries/slick/slick-theme.css', 'all');
                    $this->context->controller->addJS($this->_path . 'libraries/slick/slick.min.js', false);
                }
                $this->context->controller->addJS($this->_path . 'views/js/front.js', false);
                $visualizationJsLegacyUri = $this->_path . 'views/js/front_' . $steps['visualization'] . '.js';
                $visualizationJsLegacyPath = _PS_MODULE_DIR_ . $this->name . '/views/js/front_' . $steps['visualization'] . '.js';
                $isJsThemeCacheEnabledLegacy = (bool) Configuration::get('PS_JS_THEME_CACHE');
                if (_PS_MODE_DEV_ && !$isJsThemeCacheEnabledLegacy && file_exists($visualizationJsLegacyPath)) {
                    $visualizationJsLegacyUri .= '?v=' . (int) filemtime($visualizationJsLegacyPath);
                }
                $this->context->controller->addJS($visualizationJsLegacyUri, false);
                $this->context->controller->addCSS($this->_path . 'views/css/idxrcustomproduct.css', 'all');
                $this->context->controller->addCSS($this->_path . 'views/css/16/front.css', 'all');
                $this->context->controller->addCSS($this->_path . 'views/css/16/front_' . $steps['visualization'] . '.css', 'all');
            }

            // Pricing
            if ($steps['add_base']) {
                $idxrLogFile = __DIR__ . '/file_log.txt';
                if ((file_exists($idxrLogFile) && is_writable($idxrLogFile)) || (!file_exists($idxrLogFile) && is_writable(__DIR__))) {
                    file_put_contents($idxrLogFile, "[" . date('Y-m-d H:i:s') . "]in add_base \n", FILE_APPEND);
                    file_put_contents($idxrLogFile, "[" . date('Y-m-d H:i:s') . "]id_product: " . $id_product . "\n", FILE_APPEND);
                    file_put_contents($idxrLogFile, "[" . date('Y-m-d H:i:s') . "]id_product_attribute: " . $id_product_attribute . "\n", FILE_APPEND);
                    if ($use_attribute_price) {
                        file_put_contents($idxrLogFile, "[" . date('Y-m-d H:i:s') . "]use_attribute_price: \n", FILE_APPEND);
                    }
                }

                if ($use_attribute_price && $id_product_attribute) {
                    // ✅ Use variant price (without specific price or reduction)
                    $base_price = Product::getPriceStatic($id_product, true, $id_product_attribute, 6, null, false, false);
                    $base_price_wot = Product::getPriceStatic($id_product, false, $id_product_attribute, 6, null, false, false);
                } else {
                    // ✅ Use base product price (without attribute)
                    $base_price = Product::getPriceStatic($id_product, true, 0, 6, null, false, false);
                    $base_price_wot = Product::getPriceStatic($id_product, false, 0, 6, null, false, false);
                }
            } else {
                $base_price = 0;
                $base_price_wot = 0;
            }

            $product_name = ProductCore::getProductName($id_product);
            if ($favorite) {
                $steps['default_configuration'] = array();
                $favorite_default = explode(',', $favorite);
                $fav_array = array();
                foreach ($favorite_default as $fav) {
                    $fav_parts = explode('_', $fav);
                    if ($fav_parts[1] != 'false') {
                        $fav_array[$fav_parts[0]] = $fav_parts[1];
                    }
                }
                $steps['default_configuration'] = json_encode($fav_array);
            }
            $edit = Tools::getValue('icp_edit');
            if ($edit) {
                $idxproduct = new IdxCustomizedProduct($edit);
                $icp_code = $idxproduct->getIcpCode();
                $edit_default = explode(',', $icp_code);
                $edit_array = array();
                foreach ($edit_default as $ed) {
                    $edit_parts = explode('-', $ed);
                    $edit_array[$edit_parts[0]] = $edit_parts[1];
                }
                $steps['default_configuration'] = json_encode($edit_array);
            }
            
            if ($steps['default_configuration']) {                
                $default = json_decode($steps['default_configuration'], true);
                foreach ($steps['components'] as &$component) {
                    $key = explode('f',$component['id_component'])[0];
                    if (array_key_exists($key, $default)) {
                        switch ($default[$key]) {
                            case -2: //Inherit
                                break;
                            case -1:
                                $component['default_opt'] = -1;
                                break;
                            default:
                                $component['default_opt'] = $default[$key];
                                break;
                        }
                    }
                }
            }
            
            $show_discount_line = Configuration::get(Tools::strtoupper($this->name . '_DISCOUNTLINE'));
            if (!$show_discount_line) {
                $this->calculateImpacWhitDiscout($steps);
            }
            //TODO comprobar si es vendible
            $product = new Product($id_product);
            $this->smarty->assign(array(
                'steps' => $steps,
                'icp_price' => number_format($base_price, 2),
                'icp_price_formated' => self::formatPrice($base_price),
                'icp_price_wo' => number_format($base_price_wot, 2),
                'icp_price_wo_formated' => self::formatPrice($base_price_wot),
                'product_name' => $product_name,
                'available_for_order' => $product->available_for_order,
                'minimal_qty' => $minimal_qty?$minimal_qty:1,
                'modules_dir' => _MODULE_DIR_,
                'opcion_img_dir' => _PS_IMG_ . $this->name . DIRECTORY_SEPARATOR . 'options' . DIRECTORY_SEPARATOR,
                'show_discount_line' => $show_discount_line,
                'icp_editing' => $edit
            ));

            if ($steps['discount'] && $steps['discount_type'] == 'percentage' && !Configuration::get(Tools::strtoupper($this->name . '_DISCOUNTLINE'))) {
                $base_price_wd = $base_price - ($base_price*($steps['discount_amount']/100));
                $base_price_wot_wd = $base_price_wot - ($base_price_wot*($steps['discount_amount']/100));
                $this->smarty->assign(array(
                    'icp_price_wd_formated' => self::formatPrice($base_price_wd),
                    'icp_price_wo_wd_formated' => self::formatPrice($base_price_wot_wd),
                ));
            }
            
            if ($steps['discount'] && $steps['discount_type'] == 'amount') {
                $base_price_wd = $base_price - $steps['discount_amount'];
                $base_price_wot_wd = $base_price_wot - $steps['discount_amount'];
                $this->smarty->assign(array(
                    'icp_price_wd_formated' => self::formatPrice($base_price_wd),
                    'icp_price_wo_wd_formated' => self::formatPrice($base_price_wot_wd),
                ));
            }
            
            if ($steps['visualization'] == 'minified') {
                $this->context->controller->addCSS($this->_path . 'views/css/bootstrap-select.min.css', 'all');
                $this->context->controller->addJS($this->_path . 'views/js/bootstrap-select.min.js', false);
            }
            $imageeditor_modal = '';
            if (Tools::getValue('idxcpeditimages')) {
                $images = IdxConfiguration::getAllImages($id_configuration);
                $this->smarty->assign(array("conf_images" => $images));
                $imageeditor_modal = $this->display(__FILE__, 'views/templates/front/image_editor.tpl');
            }

            $actions = '';
            if ($steps['button_section']) {
                $actions = $this->display(__FILE__, 'views/templates/front/partials/actions.tpl');
            }
            return $imageeditor_modal . $this->display(__FILE__, 'views/templates/front/' . ($this->es17 ? '17' : '16') . '/' . $steps['visualization'] . '/front_' . $steps['visualization'] . '.tpl') . $actions;
        } else {
            return '';
        }
    }
    
    public function calculateImpacWhitDiscout(&$steps)
    {
        if (!$steps['discount'] || $steps['discount_type'] != 'percentage') {
            return;
        }
        
        $discount = ($steps['discount_amount']/100);

        foreach ($steps['components'] as &$component) {
            if ($component['options'] && isset($component['options']->options)) {
                foreach ($component['options']->options as &$option) {
                    if (isset($option->price_impact) && $option->price_impact) {
                        $option->price_impact_wdiscount = $option->price_impact -($option->price_impact*$discount);
                        $option->price_impact_wdiscount_formatted = $this->formatPrice($option->price_impact_wdiscount);
                    }
                }
            }
        }
    }

    public function applyfavorite($favorite)
    {
        $fav_q = 'Select id_fav from ' . _DB_PREFIX_ . 'idxrcustomproduct_customer_fav '
                . 'where id_product = ' . (int) Tools::getValue('id_product')
                . ' and id_customer = ' . (int) $this->context->customer->id
                . ' and icp_code = "' . $favorite . '"';

        $fav_id = Db::getInstance()->getValue($fav_q);
        if ($fav_id !== false) {
            $extra_q = 'Select * from ' . _DB_PREFIX_ . 'idxrcustomproduct_customer_extra where id_fav = ' . (int) $fav_id . ';';
            $extra_values = Db::getInstance()->executeS($extra_q);
            if ($extra_values) {
                $extra = array();
                foreach ($extra_values as $value) {
                    $extra[$value['id_component']] = $value['extra'];
                }
                Media::addJsDef(array('extra_values' => $extra));
            }
        }
    }

    public function hookdisplayShoppingCartFooter($params)
    {
        if (!$this->es17) {
            $extra_info = $this->getExtraByContext();
            if (count($extra_info)) {
                $json_extra_info = json_encode($extra_info, JSON_UNESCAPED_UNICODE);
                Media::addJsDef(array('icp_extrainfo' => $json_extra_info));
                $this->context->controller->addCSS($this->_path . 'views/css/16/front_cart.css', 'all');
                $this->context->controller->addJS($this->_path . 'views/js/icp_order.js', false);
            }
        }
    }

    public function hookDisplayOverrideTemplate($param)
    {
        if (isset($this->context->controller->php_self) && !$this->context->controller->ajax && ($this->context->controller->php_self == 'cart')) {
            $this->context->controller->registerStylesheet('modules-idxcpfrontcss', 'modules/' . $this->name . '/views/css/17/front_cart.css', array('media' => 'all', 'priority' => 150));

            $extra_info = $this->getExtraByContext();
            if ($extra_info) {
                $extra_info_wid = array();
                foreach ($extra_info as $extra) {
                    $extra_info_wid[$extra['id_product']] = $extra['customization'];
                }
                $this->context->smarty->assign(array('extra_info' => $extra_info_wid));
                return $this->getTemplatePath('hookDisplayOverrideTemplateCart.tpl');
            }
        }
    }

    public function hookDisplayCustomerAccount($params)
    {
        if (!Configuration::get(Tools::strtoupper($this->name . '_SHOWFAV'))) {
            return;
        }
        $id_customer = $this->context->customer->id;
        if ($id_customer) {
            if ($this->es17) {
                return $this->display(__FILE__, 'views/templates/front/account_blockcustomproduct_17.tpl', $this->getCacheId());
            } else {
                return $this->display(__FILE__, '/views/templates/front/account_blockcustomproduct.tpl', $this->getCacheId());
            }
        }
    }

    public function hookActionCartSave($params)
    {
        if (isset($params['cart'])) {
            $query = "select ps.id_product, ps.id_category_default, pl.description_short from "._DB_PREFIX_."product_shop ps
            inner join "._DB_PREFIX_."product_lang pl on ps.id_product = pl.id_product and ps.id_shop = pl.id_shop
            where pl.id_lang = ".(int)$params['cart']->id_lang." and ps.id_shop = ".(int)$params['cart']->id_shop." and ps.id_product in (select id_product from "._DB_PREFIX_."cart_product where id_cart=".(int)$params['cart']->id.")";
            
            $products = Db::getInstance()->executeS($query);
        } else {
            return;
        }
        $extra_info = array();
        $front_token = Configuration::get(Tools::strtoupper($this->name .'_TOKEN'));
        $file_controller = $this->context->link->getModuleLink($this->name, 'file', array('token' => $front_token,'ajax' => true)).'&key=';
        foreach ($products as $product) {
            if ($product['id_category_default'] == Configuration::get(Tools::strtoupper($this->name . '_CATEGORY'))) {
                $data = array();
                $data['id_cart'] = $params['cart']->id;
                $data['id_product'] = $product['id_product'];
                $data['public'] = $product['description_short'];
                $sql = 'Select description from ' . _DB_PREFIX_ . 'product_lang where id_product = ' . (int) $product['id_product'];
                $data['private'] = Db::getInstance()->getValue($sql);
                $text_customization = $this->getExtraByCart($params['cart']->id, $product['id_product']);
                $this->addDiscount($product['id_product'], $params['cart']);

                $inputIds = [
                    '3', '23', '4', '9', '10', '11',
                    '57', '58', '53', '54', '55', '56',
                    '18', '25', '26', '27',
                    '38', '64', '63', '39', '65', '40', '41', '42',
                    '75', '45', '66', '67', '68', '69'
                ];

                foreach ($text_customization as $text) {
                    if ($text['target_name']) {
                        $data['private'] .= '<p>' . $text['title'] . ': <a  target="_blank" href="' . $file_controller . $text['target_name'] . '" target="_blank">' . $text['original_name'] . '</a></p>';
                        $data['public'] .= '<p>' . $text['title'] . ': <a  target="_blank" href="' . $file_controller . $text['target_name'] . '" target="_blank">' . $text['original_name'] . '</a></p>';
                    } else {
                        if (in_array($text['id_component'], $inputIds)) {
                            $data['private'] .= '<p>' . $text['title'] . ': ' . $text['extra'] . ' mm</p>';
                            $data['public'] .= '<p>' . $text['title'] . ': ' . $text['extra'] . ' mm</p>';
                        } else {
                            $data['private'] .= '<p>' . $text['title'] . ': ' . $text['extra'] . '</p>';
                            $data['public'] .= '<p>' . $text['title'] . ': ' . $text['extra'] . '</p>';
                        }
                    }
                }

                $svgUrlQuery = "SELECT svg_file, svg_code, console FROM " . _DB_PREFIX_ . "idxrcustomproduct_snaps WHERE id_product = " . (int)$product['id_product'];

                $svgUrls = Db::getInstance()->executeS($svgUrlQuery);
                if (!$svgUrls) {
                    $error = Db::getInstance()->getMsgError();
                } else {
                    //console
                    $console = $svgUrls[0]['console'];
                    if($console == '0') $console = 'Non';
                    if($console == '1') $console = 'Oui';
                    $svgImgTag = '<p>Console ouverte ?: ' . $console . '</p>';
                    $data['private'] .= $svgImgTag;
                    $data['public'] .= $svgImgTag;
                    // svg code
                    
                    $svgUrl = $svgUrls[0]['svg_code'];
                    if($svgUrl){
                        $svgImgTag = '<p>Aperçu en SVG: <a target="_blank" href="' .$svgUrl. '">Cliquez ici pour voir le SVG</a></p>';
                        $data['private'] .= $svgImgTag;
                        $data['public'] .= $svgImgTag;
                    }

                    //svg file
                    $svgUrl = $svgUrls[0]['svg_file'];
                    if($svgUrl){
                        $svgImgTag = '<p>Aperçu: <img class="perviewImageSketch" src="' . $svgUrl . '" width="400px" height="400px"></p>';
                        $data['private'] .= $svgImgTag;
                        $data['public'] .= $svgImgTag;
                    }
                    
                }

                $extra_info[] = $data;
            }
        }
        

        $this->updateNotes($extra_info);
        $this->updateCartExtra($params['cart']);
    }

    public function adjustStock($id_cart)
    {
        $cart = new Cart($id_cart);
        $products = $cart->getProducts();
        foreach ($products as $prod) {
            $idxproduct = new IdxCustomizedProduct($prod['id_product']);
            $icp_code = $idxproduct->getIcpCode();
            if ($icp_code) {
                $configuration = $idxproduct->getConfiguration();
                $attached_products = $idxproduct->getAttachedProducts();
                if ($configuration->add_base && !$configuration->productbase_component) {
                    $producto_base = $this->getProductoOriginal($prod['id_product'],$icp_code);
                    $attached_products[] = array(
                        'id_product' => $producto_base,
                        'id_product_attribute' => 0,
                        'quantity' => 1
                    );
                }
                if ($attached_products) {
                    if ($configuration->breakdown_attachment == '1') {
                        $price_before = $cart->getOrderTotal();
                        //Todo pasar descuento al final y que ajuste precio al desglosar productos
                        if ($configuration->discount && $configuration->discount_createdas == "cartrule" && $configuration->discount_type == "percentage") {
                            // The discount is linked to the customized product, we must to add as amount discount
                            $discount_name = 'custompro_' . $prod['id_product'] . '_' . $cart->id . '_breakdown';
                            $discount_amount = $prod['total_wt'] * ($configuration->discount_amount / 100);
                            $id_cart_rule = $this->createCartRule($discount_name, false, $cart, $discount_amount, 'amount');
                            $cart->addCartRule($id_cart_rule);
                        }
                        foreach ($attached_products as $attach_product) {
                            $cart->updateQty($attach_product['quantity'] * $prod['cart_quantity'], $attach_product['id_product'], $attach_product['id_product_attribute']);
                        }
                        $cart->deleteProduct($prod['id_product'], $prod['id_product_attribute']);
                        $price_after = $cart->getOrderTotal();
                        if ($price_before != $price_after) {
                            $delta = $price_after - $price_before;
                            $this->adjustTotal($cart, $delta);
                        }
                        $cart->update();
                        $cart->getPackageList(true);
                    } else {
                        foreach ($attached_products as $attach_product) {
                            $this->setQty((-1 * $attach_product['quantity'] * $prod['cart_quantity']), $attach_product['id_product'], $attach_product['id_product_attribute']);
                        }
                    }
                }
            }
        }
    }
    
    public function adjustTotal(&$cart, $delta)
    {
        if ($delta > 0) {
            $discount_name = 'custompro_adjust_'.$cart->id;
            $id_cart_rule = $this->createCartRule($discount_name, null, $cart, $delta, 'amount');
            $cart->addCartRule($id_cart_rule);
        }

        if ($delta < 0) {
            $extra_product = IdxCustomizedProduct::incrementPriceProduct(abs($delta));
            $cart->updateQty(1, $extra_product);
            $cart->update();
        }
    }

    public function addDiscount($id_product, $cart)
    {
        $idxproduct = new IdxCustomizedProduct($id_product);
        $icp_code = $idxproduct->getIcpCode();
        $id_original = $this->getProductoOriginal($id_product, $icp_code);
        $id_configuration = $this->getConfigurationByProduct($id_original);
        if ($id_configuration) {
            $conf = new IdxConfiguration($id_configuration, true);
            if ($conf->discount) {
                $discount_name = 'custompro_' . $id_product . '_' . $cart->id;
                $discount_amount = $conf->discount_amount;
                if ($conf->discount_createdas == 'cartrule') {
                    $id_cart_rule = $this->createCartRule($discount_name, $id_product, $cart, $discount_amount, $conf->discount_type);
                    $cart->addCartRule($id_cart_rule);
                } else {
                    $this->createSpecificPrice($id_product, $cart, $discount_amount, $conf);
                }
            }
        }
        return false;
    }

    public function hookActionValidateOrder($params)
    {
        if (empty($params['order']) || empty($params['cart'])) {
            return '';
        }

        /** @var Order $order */
        $order = $params['order'];
        /** @var Cart $cart */
        $cart = $params['cart'];

        // Rebuild notes at order validation time to avoid stale/missing dimensions in PDFs.
        $orderNotes = $this->buildOrderNotesFromCart($cart, $order);
        if (!empty($orderNotes)) {
            $this->updateNotes($orderNotes);
        }

        $sql_update = 'Update ' . _DB_PREFIX_ . 'idxrcustomproduct_notes set id_order = ' . (int) $order->id . ' where id_cart = ' . (int) $cart->id . ';';
        Db::getInstance()->execute($sql_update);
        return '';
    }

    private function buildOrderNotesFromCart(Cart $cart, Order $order)
    {
        $notes = array();
        $front_token = Configuration::get(Tools::strtoupper($this->name . '_TOKEN'));
        $file_controller = $this->context->link->getModuleLink(
            $this->name,
            'file',
            array('token' => $front_token, 'ajax' => true)
        ) . '&key=';

        $inputIds = array(
            '3', '23', '4', '9', '10', '11',
            '57', '58', '53', '54', '55', '56',
            '18', '25', '26', '27',
            '38', '64', '63', '39', '65', '40', '41', '42',
            '75', '45', '66', '67', '68', '69', '118'
        );

        foreach ($order->getProducts() as $orderedProduct) {
            $id_product = (int) $orderedProduct['product_id'];
            $id_original = (int) $this->getProductoOriginal($id_product);
            if (!$id_original) {
                continue;
            }

            $product = new Product($id_product, false, (int) $cart->id_lang);
            $publicDesc = is_array($product->description_short)
                ? (isset($product->description_short[(int) $cart->id_lang]) ? $product->description_short[(int) $cart->id_lang] : reset($product->description_short))
                : (string) $product->description_short;
            $privateDesc = is_array($product->description)
                ? (isset($product->description[(int) $cart->id_lang]) ? $product->description[(int) $cart->id_lang] : reset($product->description))
                : (string) $product->description;

            $data = array(
                'id_cart' => (int) $cart->id,
                'id_product' => $id_product,
                'public' => (string) $publicDesc,
                'private' => (string) $privateDesc,
            );

            $text_customization = $this->getExtraByCart((int) $cart->id, $id_product);
            foreach ((array) $text_customization as $text) {
                if (!empty($text['target_name'])) {
                    $line = '<p>' . $text['title'] . ': <a target="_blank" href="' . $file_controller . $text['target_name'] . '">' . $text['original_name'] . '</a></p>';
                } else {
                    $suffix = in_array((string) $text['id_component'], $inputIds) ? ' mm' : '';
                    $line = '<p>' . $text['title'] . ': ' . $text['extra'] . $suffix . '</p>';
                }
                $data['private'] .= $line;
                $data['public'] .= $line;
            }

            $svgUrlQuery = 'SELECT svg_file, svg_code, console FROM ' . _DB_PREFIX_ . 'idxrcustomproduct_snaps WHERE id_product = ' . $id_product;
            $svgUrls = Db::getInstance()->executeS($svgUrlQuery);
            if (!empty($svgUrls)) {
                $console = $svgUrls[0]['console'];
                if ($console === '0') {
                    $console = 'Non';
                } elseif ($console === '1') {
                    $console = 'Oui';
                }
                $consoleLine = '<p>Console ouverte ?: ' . $console . '</p>';
                $data['private'] .= $consoleLine;
                $data['public'] .= $consoleLine;

                if (!empty($svgUrls[0]['svg_code'])) {
                    $svgLine = '<p>Aperçu en SVG: <a target="_blank" href="' . $svgUrls[0]['svg_code'] . '">Cliquez ici pour voir le SVG</a></p>';
                    $data['private'] .= $svgLine;
                    $data['public'] .= $svgLine;
                }

                if (!empty($svgUrls[0]['svg_file'])) {
                    $imgLine = '<p>Aperçu: <img class="perviewImageSketch" src="' . $svgUrls[0]['svg_file'] . '" width="400px" height="400px"></p>';
                    $data['private'] .= $imgLine;
                    $data['public'] .= $imgLine;
                }
            }

            $notes[] = $data;
        }

        return $notes;
    }

    public function hookActionOrderReturn($params)
    {
        return;
    }

    public function hookActionEmailSendBefore($params)
    {
        if ($params['template'] == 'order_conf') {
            $extra_options = $this->getExtraArray($params['cart']);
            if (count($extra_options)) {
                $front_token = Configuration::get(Tools::strtoupper($this->name .'_TOKEN'));
                $file_controller = $this->context->link->getModuleLink($this->name, 'file', array('token' => $front_token,'ajax' => true)).'&key=';
                $content = '<tr><td style="border:1px solid #D6D4D4;" colspan="5"><table class="table" style="width: 100%;"><tr>';
                foreach ($extra_options as $info) {
                    $content .= '<td style="width: 30%; text-align: center;">'.$info['product']['name'].'</td>';
                    $content .= '<td width: 70%;>'.$info['product']['description_short'];
                    if (isset($info['extra_options']) && count($info['extra_options'])) {
                        if (!is_array(reset($info['extra_options']))) {
                            $cart_info = $info['extra_options'];
                            $info['extra_options'] = array($cart_info);
                        }
                        foreach ($info['extra_options'] as $cart_info) {
                            if ($cart_info['original_name']) {
                                $content .= '<p>' . $cart_info['title'] . ': <a href="' . $file_controller . $cart_info['target_name'] . '" target="_blank">' . $cart_info['extra'] . '</a></p>';
                            } else {
                                $content .= '<p>' . $cart_info['title'] . ': ' . $cart_info['extra'] . '</p>';
                            }
                        }
                    }
                    $content .='</td>';
                }
                $content .= "</tr></table></td></tr>";
                $params['templateVars']['{products}'] .= $content;
            }
        }
    }

    public function hookActionAdminMetaAfterWriteRobotsFile($params)
    {
        $id_category = (int) Configuration::get(Tools::strtoupper($this->name . '_CATEGORY'));
        $id_shop = $this->context->shop->id;
        $base_link = $this->context->link->getBaseLink($id_shop);
        $languages = Language::getLanguages();
        
        /*Extra CUSTOM Categories*/
        $extra_cats = Db::getInstance()->executeS('select distinct id_category from '._DB_PREFIX_.'category_lang where name like "CUSTOM %";');
        
        $urls = array();
        
        foreach ($languages as $lang) {
            $urls[] = str_replace($base_link, '', $this->context->link->getCategoryLink($id_category,null,$lang['id_lang']));
            foreach ($extra_cats as $extra_cat) {
                $urls[] = str_replace($base_link, '', $this->context->link->getCategoryLink($extra_cat['id_category'],null,$lang['id_lang']));
            }
        }

        fwrite($params['write_fd'], "# START Idxrcustomproduct disallow customized products\n");
        foreach ($urls as $lang_url) {
            fwrite($params['write_fd'], 'Disallow: /*' . $lang_url . "/\n");
        }
        fwrite($params['write_fd'], "# END Idxrcustomproduct\n");
    }

    public function hookActionAdminProductsListingFieldsModifier($params)
    {
        if (!$params) {
            return;
        }
        
        $id_shop = $this->context->shop->id;
        $hidden_categories = Configuration::get(Tools::strtoupper($this->name) . '_CATEGORY', null, null, $id_shop);
        if (isset($params['sql_where'])) {//Ps 1.7
        $params['sql_where'][] = 'p.`id_category_default` !=' . (int)$hidden_categories;
        }
        if (isset($params['select'])) {//Ps 1.6
            $params['where'] .= ' AND a.`id_category_default` !=' . (int)$hidden_categories;
        }
    }

    public function hookActionAdminProductsListingResultsModifier($params)
    {
        $id_shop = $this->context->shop->id;
        $hidden_categories = Configuration::get(Tools::strtoupper($this->name) . '_CATEGORY', null, null, $id_shop);
        $products = Db::getInstance()->ExecuteS('SELECT id_product FROM ' . _DB_PREFIX_ . 'product_shop where active = 1 AND id_shop = ' . (int) $id_shop . ' AND id_category_default = ' . (int) $hidden_categories);
        $cust_prod = array();
        foreach ($products as $prod) {
            $cust_prod[] = $prod['id_product'];
        }

        $products_by_desc = Db::getInstance()->ExecuteS('Select id_product FROM ' . _DB_PREFIX_ . 'product_lang where description like "%icp_code%" and id_shop = ' . (int) $id_shop);
        $cust_prod_desc = array();
        foreach ($products_by_desc as $prod) {
            $cust_prod_desc[] = $prod['id_product'];
        }
        $cust_prod = array_merge($cust_prod, $cust_prod_desc);

        $count = 0;
        if (isset($params['list'])) {
            foreach ($params['list'] as $key => $list_prod) {
                if (in_array($list_prod['id_product'], $cust_prod)) {
                    unset($params['list'][$key]);
                    $count++;
                }
            }
            $params['list'] = array_values($params['list']);
            $params['list_total'] -= $count;
        }
        if (isset($params['products'])) {
            foreach ($params['products'] as $key => $list_prod) {
                if (in_array($list_prod['id_product'], $cust_prod)) {
                    unset($params['products'][$key]);
                    $count++;
                }
            }
            $params['products'] = array_values($params['products']);
            $params['total'] -= $count;
        }
    }

    public function hookDisplayAdminOrder($params)
    {
        if (isset($params['id_order'])) {
            return $this->getPanelForOrder($params['id_order']);
        }
    }
    
    public function hookdisplayBackOfficeHeader($params)
    {
        if (Configuration::get(Tools::strtoupper($this->name . '_ADMINPRODUCTINFOBLOCK')) &&  
                ($this->context->controller->php_self == "AdminOrders" || $this->context->controller->controller_name == "AdminOrders")) {
            
            $id_order = Tools::getValue('id_order');
            $order = new Order($id_order);
            $extra_info = $this->getExtraByContext(false,$order->id_cart);
            if (count($extra_info)) {
                $json_extra_info = json_encode($extra_info, JSON_UNESCAPED_UNICODE);
                Media::addJsDef(array(
                    'icp_extrainfo' => $json_extra_info, 
                    'icp_qtyblock_text' => $this->l('This product have text/files customized if want add more please go to product and configure from there')
                    )
                );

                $this->context->controller->addJS($this->_path . 'views/js/icp_orderback.js', false);
            }
        }
    }

    public function hookDisplayOrderDetail($params)
    {
        return $this->getPanelForOrder($params['order']->id, true);
    }

    public function hookDisplayPDFInvoice($params)
    {
        return $this->getPanelForOrder2($params['object']->id_order);
    }

    public function updateRobotsFile()
    {
        $rb_file = _PS_ROOT_DIR_ . '/robots.txt';
        $text = Tools::file_get_contents($rb_file);
        $start = strpos($text, "# START Idxrcustomproduct disallow customized products\n");
        $end = strpos($text, "# END Idxrcustomproduct\n") + Tools::strlen("# END Idxrcustomproduct\n");
        $text_start = Tools::substr($text, 0, $start);
        $text_end = Tools::substr($text, $end);
        $new_text = $text_start . $text_end;

        $id_category = (int) Configuration::get(Tools::strtoupper($this->name . '_CATEGORY'));

        if ($this->es17) {
            $cat = new Category($id_category);
            $langs = Language::getLanguages();
            $url = array();
            foreach ($langs as $lang) {
                $url[$lang['id_lang']]['id_lang'] = $lang['id_lang'];
                $url[$lang['id_lang']]['link_rewrite'] = $cat->getLink(null, $lang['id_lang']);
            }
        } else {
            $url = Category::getUrlRewriteInformations($id_category);
        }

        $new_text .= "# START Idxrcustomproduct disallow customized products\n";
        if (count($url) > 1) {
            foreach ($url as $lang_url) {
                $new_text .= 'Disallow: /*' . Language::getIsoById($lang_url['id_lang']) . '/' . $lang_url['link_rewrite'] . "/\n";
                $new_text .= 'Disallow: /*' . Language::getIsoById($lang_url['id_lang']) . '/' . $id_category . '-' . $lang_url['link_rewrite'] . "/\n";
            }
        } else {
            foreach ($url as $lang_url) {
                $new_text .= 'Disallow: /' . $lang_url['link_rewrite'] . "/\n";
                $new_text .= 'Disallow: /' . $id_category . '-' . $lang_url['link_rewrite'] . "/\n";
            }
        }
        $new_text .= "# END Idxrcustomproduct\n";
    }

    public function renderForm()
    {
        $js_vars = '';
        if ($this->es160) {
            $this->smarty->assign(array(
                'js_vars' => Media::getJsDef(),
            ));
            $js_vars = $this->display(__FILE__, 'views/templates/admin/jsvars.tpl');
        }
        return InnovaTools_2_0_0::adminTabWrap($this) . $js_vars;
    }

    public function actualizarClones($clave = 'link_rewrite')
    {
        $id_shop = $this->context->shop->id;
        $sql = 'SELECT p.id_product, p.reference, p.ean13, p.upc, pl.name, pl.description, pl.link_rewrite FROM `' . _DB_PREFIX_ . 'product` p
        INNER JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (p.id_product = pl.id_product' . Shop::addSqlRestrictionOnLang('pl', $id_shop) . ') WHERE pl.description LIKE "%icp_code%"';
        if ($reg = Db::getInstance()->executeS($sql)) {
            foreach ($reg as $elemento) {
                preg_match_all('/(.*)icp_code\((.*)\)(.*)/', $elemento['description'], $pregmatch_result);
                $icp_code = isset($pregmatch_result[2]) && !empty($pregmatch_result[2]) ? $pregmatch_result[2][0] : null;
                if (!$icp_code) {
                    continue;
                }
                if ($this->getProductoOriginal($elemento['id_product'], $icp_code)) {
                    continue;
                }
                switch ($clave) {
                    case 'reference':
                        $where = ' AND p.reference LIKE "' . $elemento['reference'] . '"';
                        break;
                    case 'ean13':
                        $where = ' AND p.ean13 LIKE "' . $elemento['ean13'] . '"';
                        break;
                    case 'upc':
                        $where = ' AND p.upc LIKE "' . $elemento['upc'] . '"';
                        break;
                    case 'name':
                        $where = ' AND pl.name LIKE "' . $elemento['name'] . '"';
                        break;
                    case 'link_rewrite':
                    default:
                        $where = ' AND pl.link_rewrite LIKE "' . $elemento['link_rewrite'] . '"';
                        break;
                }
                $sql = 'SELECT p.id_product FROM `' . _DB_PREFIX_ . 'product` p
        INNER JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (p.id_product = pl.id_product' . Shop::addSqlRestrictionOnLang('pl', $id_shop) . ') WHERE 1' . $where . ' ORDER BY id_product ASC';
                $id_product = Db::getInstance()->getValue($sql);
                $datos = array(
                    'id_producto' => $id_product,
                    'id_clon' => $elemento['id_product'],
                    'icp_code' => $icp_code
                );
                Db::getInstance()->insert($this->name . '_clones', $datos);
            }
        }
    }

    public function helpGenerateForm()
    {
        $this->context->controller->addCSS($this->_path . 'views/css/back.css', 'all');
        $this->context->controller->addCSS($this->_path . 'views/css/idxautocomplete.css', 'all');
        return $this->renderModuleConfiguration();
    }

    public function renderConfigurationList()
    {
        $configurations = IdxConfiguration::getConfigurations();
        foreach ($configurations as &$configuration) {
            $configuration['idxrcustomproduct_configurations'] = $configuration['id_configuration'];
        }
        $fields_list = array(
            'id_configuration' => array('title' => $this->l('id'), 'id_configuration' => 'id_configuration', 'align' => 'left', 'width' => 30),
            'name' => array('title' => $this->l('name'), 'name' => 'name', 'align' => 'left', 'type' => 'text'),
            'active' => array('title' => $this->l('active'), 'active' => 'active', 'align' => 'center', 'type' => 'bool')
        );
        $helper = new HelperList();
        $helper->table = 'idxrcustomproduct_configurations';
        $helper->shopLinkType = '';
        $helper->simple_header = false;
        // Actions to be displayed in the "Actions" column
        $helper->actions = array('edit', 'delete');
        $helper->identifier = 'id_configuration';
        $helper->show_toolbar = false;
        $helper->title = $this->l('Configurations');
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        return $helper->generateList($configurations, $fields_list);
    }

    public function renderComponentList()
    {
        $components = $this->getComponents();
        foreach ($components as &$comp) {
            switch ($comp['type']) {
                case 'sel':
                    $comp['type_l'] = $this->l('selector');
                    break;
                case 'sel_img':
                    $comp['type_l'] = $this->l('selector with image');
                    break;
                case 'text':
                    $comp['type_l'] = $this->l('text');
                    break;
                case 'textarea':
                    $comp['type_l'] = $this->l('textarea');
                    break;
                case 'file':
                    $comp['type_l'] = $this->l('file');
                    break;
                case 'product':
                    $comp['type_l'] = $this->l('product');
                    break;
                default:
                    $comp['type_l'] = $this->l('other');
                    break;
            }
        }
        $fields_list = array(
            'id_component' => array('title' => $this->l('id'), 'id_component' => 'id_component', 'align' => 'left', 'width' => 30),
            'name' => array('title' => $this->l('name'), 'name' => 'name', 'align' => 'left'),
            'type_l' => array('title' => $this->l('type'), 'type_l' => 'type_l', 'align' => 'left'),
            'title' => array('title' => $this->l('title'), 'title' => 'title', 'align' => 'left'),
            'description' => array('title' => $this->l('description'), 'description' => 'description', 'align' => 'left'),
        );
        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->simple_header = false;
        // Actions to be displayed in the "Actions" column
        $helper->actions = array('edit', 'delete');
        $helper->identifier = 'id_component';
        $helper->show_toolbar = false;
        $helper->title = $this->l('Components');
        $helper->table = 'component';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        return $helper->generateList($components, $fields_list);
    }

    public function renderModuleConfiguration()
    {
        if (method_exists('Category', 'getAllCategoriesName')) {
            $categories = Category::getAllCategoriesName(null, $this->context->language->id, false);
        } else {
            $categories = self::getAllCategoriesName(null, $this->context->language->id, false);
        }
        $image_types = ImageType::getImagesTypes();

        foreach ($image_types as &$type) {
            $type['name'] .= ' ' . $type['width'] . 'x' . $type['height'];
        }


        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('General Configuration'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    
                    array(
                        'type' => 'text',
                        'label' => $this->l('Afficher les déclinaisons des produits :'),
                        'name' => 'idxr_skipped_product_ids',
                        'desc' => $this->l('Ajoutez les identifiants des produits séparés des virgules, ex: 19224, 78264, 98236.'),
                    ),
                    array(
                        'type' => 'text', 
                        'label' => $this->l('Prix de decoupe pour le rectangle sur mesure.'),
                        'name' => 'idxr_prix_de_decoupe_cube',
                        'desc' => $this->l('Ce montant est le même pour tout les produits sur mesure.'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Prix de découpe pour 4mm'),
                        'name' => 'cut_price_4mm',
                        'desc' => $this->l('Ajouter prix de découpe pour 4mm'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Prix de découpe pour 5mm'),
                        'name' => 'cut_price_5mm',
                        'desc' => $this->l('Ajouter prix de découpe pour 5mm'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Prix de découpe pour 6mm'),
                        'name' => 'cut_price_6mm',
                        'desc' => $this->l('Ajouter prix de découpe pour 6mm'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Prix de découpe pour 8mm'),
                        'name' => 'cut_price_8mm',
                        'desc' => $this->l('Ajouter prix de découpe pour 8mm'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Prix de découpe pour 10mm et plus'),
                        'name' => 'cut_price_10mm',
                        'desc' => $this->l('Ajouter prix de découpe pour 10mm et plus'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Prix de collage pour 4mm'),
                        'name' => 'glue_price_4mm',
                        'desc' => $this->l('Ajouter prix de collage pour 4mm'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Prix de collage pour 5mm'),
                        'name' => 'glue_price_5mm',
                        'desc' => $this->l('Ajouter prix de collage pour 5mm'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Prix de collage pour 6mm'),
                        'name' => 'glue_price_6mm',
                        'desc' => $this->l('Ajouter prix de collage pour 6mm'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Prix de collage pour 8mm'),
                        'name' => 'glue_price_8mm',
                        'desc' => $this->l('Ajouter prix de collage pour 8mm'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Prix de collage pour 10mm et plus'),
                        'name' => 'glue_price_10mm',
                        'desc' => $this->l('Ajouter prix de collage pour 10mm et plus'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Prix de polissage pour 4mm'),
                        'name' => 'polish_price_4mm',
                        'desc' => $this->l('Ajouter prix de polissage pour 4mm'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Prix de polissage pour 5mm'),
                        'name' => 'polish_price_5mm',
                        'desc' => $this->l('Ajouter prix de polissage pour 5mm'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Prix de polissage pour 6mm'),
                        'name' => 'polish_price_6mm',
                        'desc' => $this->l('Ajouter prix de polissage pour 6mm'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Prix de polissage pour 8mm'),
                        'name' => 'polish_price_8mm',
                        'desc' => $this->l('Ajouter prix de polissage pour 8mm'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Prix de polissage pour 10mm et plus'),
                        'name' => 'polish_price_10mm',
                        'desc' => $this->l('Ajouter prix de polissage pour 10mm et plus'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Prix minimum pour les produits sur mesure.'),
                        'name' => 'prix_fixe',
                        'desc' => $this->l('Ce montant est le même pour tout les plaques sur mesure.'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Prix minimum pour les vitrines sur mesure.'),
                        'name' => 'prix_fixe_vitrine',
                        'desc' => $this->l('Ce montant est le même pour tout les vitrines sur mesure.'),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Customized category '),
                        'name' => 'customizable_category',
                        'options' => array(
                            'query' => $categories,
                            'id' => 'id_category',
                            'name' => 'name',
                        ),
                        'desc' => $this->l('The new customized product will be attached to this category, keep in mind that the first time that you change this configuration the category selected will be disallow in your robots.txt configuration'),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Clone base category?'),
                        'name' => 'clone_category',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'clone_category_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'clone_category_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                        'desc' => $this->l('Clone base product category in customized product as CUSTOM base category')
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Image size in pc'),
                        'name' => 'pc_image_type',
                        'options' => array(
                            'query' => $image_types,
                            'id' => 'id_image_type',
                            'name' => 'name',
                        ),
                        'desc' => $this->l('The module will resize product images to this size in pc'),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Image size in mobile'),
                        'name' => 'mobile_image_type',
                        'options' => array(
                            'query' => $image_types,
                            'id' => 'id_image_type',
                            'name' => 'name',
                        ),
                        'desc' => $this->l('The module will resize product images to this size in mobile'),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Image size in tablet'),
                        'name' => 'tablet_image_type',
                        'options' => array(
                            'query' => $image_types,
                            'id' => 'id_image_type',
                            'name' => 'name',
                        ),
                        'desc' => $this->l('The module will resize product images to this size in tablet'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Cover image id'),
                        'lang' => false,
                        'name' => 'coverimageid',
                        'desc' => $this->l('If module don\'t change properly the image of the product check if this id is the image id in your theme.'),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Show save customization?'),
                        'name' => 'show_fav',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'show_fav_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'show_fav_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                        'desc' => $this->l('Show save customization and saved customizations product in a new section in customer account')
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Impact prices with tax'),
                        'name' => 'price_impact_taxinclude',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'price_impact_taxinclude_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'price_impact_taxinclude_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                        'desc' => $this->l('Apply the impact as value with tax, will be the same in all taxes or No for use same base impacte instead')
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Discount line'),
                        'name' => 'discount_line',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'discount_line_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'discount_line_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                        'desc' => $this->l('If set as Yes the module will show a line with discount total otherwise it will show prices with discount applied')
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Max description'),
                        'lang' => false,
                        'name' => 'maxheightdescription',
                        'desc' => $this->l('Set a maximun height in px for options description, empty for no limit'),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Price in product lists'),
                        'name' => 'priceProductList',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'priceProductList_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'priceProductList_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                        'desc' => $this->l('If set as Yes the module will show the minimum configurable price in products lists')
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Add Id to name'),
                        'name' => 'addidname',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'addidname_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'addidname_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                        'desc' => $this->l('If have a module for remove id in url set to yes for prevent duplicate url issues')
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Keep customization after breakdown'),
                        'name' => 'breakdownblock',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'breakdownblock_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'breakdownblock_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                        'desc' => $this->l('Maintain the block with the personalisation information even if the customised product has been broken down into its components.')
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Show customization in admin product list'),
                        'name' => 'adminproductinfoblock',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'adminproductinfoblock_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'adminproductinfoblock_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                        'desc' => $this->l('Show customization in admin order product list ')
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                )
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();


        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitModConfiguration';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        return $this->display(__FILE__, 'views/templates/admin/module-info.tpl') . $helper->generateForm(array($fields_form));
    }

    public function renderFormAddConfiguration()
    {
        $id_configuration = Tools::getValue('id_configuration');
        if (!$id_configuration && $this->new_id_configuration) {
            $id_configuration = $this->new_id_configuration;
        }
        $this->context->controller->addCSS($this->_path . 'views/css/bootstrap-multiselect.css', 'all');
        $this->context->controller->addCSS($this->_path . 'views/css/uploadfile.css', 'all');
        $this->context->controller->addJS($this->_path . 'views/js/bootstrap-multiselect.js', false);
        $this->context->controller->addJS($this->_path . 'views/js/jquery.uploadfile.min.js', false);

        $categoriasSeleccionadas = array();
        $productosSeleccionados = array();

        $components = $this->getComponents();
        if ($id_configuration && !Tools::isSubmit('deleteidxrcustomproduct_configurations')) {
            $configuration = new IdxConfiguration($id_configuration, true);
            $categoriasSeleccionadas = $configuration->getCategoriasSeleccionadas();
            $productosSeleccionados = $configuration->getProductosSeleccionadas();
            if ($configuration) {
                $components_selected = array();
                if ($configuration->components) {
                    foreach ($configuration->components as $sel) {
                        foreach ($components as $key => $comp) {
                            if ($comp['id_component'] == $sel) {
                                if ($sel) {
                                    $comp['constraints'] = IdxComponent::hasConstraint($comp['id_component'], $id_configuration);
                                    $components_selected[] = $comp;
                                    unset($components[$key]);
                                }
                            }
                        }
                    }
                }
            } else {
                $id_configuration = false;
                $components_selected = array();
            }
            $components_available = $components;
            if ($components_selected) {
                $this->orderComponentsByRestrictions($components_selected);
            }
        } else {
            $components_selected = array();
            $components_available = $components;
        }

        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Add a new Configuration'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Configuration title'),
                        'lang' => false,
                        'name' => 'addconftitle',
                        'desc' => $this->l('Set the title for this configuration'),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Position in product page'),
                        'name' => 'configuration_hook',
                        'options' => array(
                            'query' => array(
                                1 => array(
                                    'hook' => $this->es17 ? 'hookDisplayProductButtons' : 'hookDisplayRightColumnProduct',
                                    'name' => $this->l('Right column'),
                                ),
                                4 => array(
                                    'hook' => 'hookDisplayFooterProduct',
                                    'name' => $this->l('Product footer'),
                                )
                            ),
                            'id' => 'hook',
                            'name' => 'name',
                        ),
                    ),
                    array (
                        'type' => 'autocompletar',
                        'label' => $this->l('Apply to this categories'),
                        'name' => 'configuration_categories',
                        'module_name' => $this->name,
                        'elementos' => $categoriasSeleccionadas,
                        'es17' => $this->es17,
                        'urlAjax' => Context::getContext()->link->getAdminLink('AdminIdxrcustomproduct') . '&forceJson=1&limit=20&ajax=true&action=searchCategory&q=',
                    ),
                    array (
                        'type' => 'autocompletar',
                        'label' => $this->l('Apply to this products'),
                        'name' => 'configuration_products',
                        'module_name' => $this->name,
                        'elementos' => $productosSeleccionados,
                        'es17' => $this->es17,
                        'urlAjax' => Context::getContext()->link->getAdminLink('AdminIdxrcustomproduct') . '&forceJson=1&limit=20&ajax=true&action=searchProductsWocomb&q=',
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Type of visualization'),
                        'name' => 'configuration_vistype',
                        'options' => array(
                            'query' => array(
                                1 => array(
                                    'visualization_type' => 'accordion',
                                    'visualization_name' => $this->l('Accordion')
                                ),
                                2 => array(
                                    'visualization_type' => 'full',
                                    'visualization_name' => $this->l('By steps in cascade')
                                ),
                                3 => array(
                                    'visualization_type' => 'minified',
                                    'visualization_name' => $this->l('Drop-down selectors')
                                ),
                            ),
                            'id' => 'visualization_type',
                            'name' => 'visualization_name',
                        ),
                    ),
                    array(
                        'type' => 'vistype_preview',
                        'label' => $this->l('Visualization preview'),
                        'name' => 'no_process',
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('First step open?'),
                        'name' => 'first_open',
                        'class' => 'accordion',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'first_open_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'first_open_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                        'desc' => $this->l('If set yes, the first option in accordion will be open when load the page')
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Keep resume tab always open?'),
                        'name' => 'resume_open',
                        'class' => 'accordion',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'resume_open_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'resume_open_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                        'desc' => $this->l('If set yes, the first option in accordion will be open when load the page')
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Show action buttons out of resume?'),
                        'name' => 'button_section',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'button_section_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'button_section_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                        'desc' => $this->l('If set yes, the buttons add to cart, quantity and add to favorite will show in a section out of resume')
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Color of the component'),
                        'name' => 'color',
                        'desc' => $this->l('Change the color of the component background'),
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Background colour resume'),
                        'name' => 'final_color',
                        'desc' => $this->l('Color for the resume container background'),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Add product price as base?'),
                        'name' => 'add_base',
                        'class' => 't',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'add_base_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'add_base_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                        'desc' => $this->l('If is enable the price of product will be add as base price')
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Show additional cost in resume list?'),
                        'name' => 'show_increment',
                        'class' => 't',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'show_increment_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'show_increment_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                        'desc' => $this->l('If is enable the resume break down the additional cost by option')
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Show price and add to cart in top?'),
                        'name' => 'show_topprice',
                        'class' => 't',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'show_topprice_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'show_topprice_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                        'desc' => $this->l('If is enable will show the price and a add to cart button in the top of the page, the button will be inactive until finish the configuration')
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Use base product combinations?'),
                        'name' => 'productbase_component',
                        'class' => 't',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'productbase_component_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'productbase_component_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                        'desc' => $this->l('If is enable will add base product combinations as firsts stpeps of configuration')
                    ),
                    array(
                        'type' => 'sortable_lists',
                        'label' => $this->l('List of components'),
                        'name' => 'configuration_components[]',
                        'list_selected' => $components_selected,
                        'list_available' => $components_available,
                    ),
                    array(
                        'type' => 'hidden',
                        'name' => 'configuration_components_order'
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable configuration'),
                        'name' => 'active',
                        'class' => 't',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                        'desc' => $this->l('Enable or disable this configuration')
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Breakdown attached products'),
                        'name' => 'bdattachment',
                        'class' => 't',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'bdattachment_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'bdattachment_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                        'desc' => $this->l('After done the order the attached products will be show as a sepparated row in order')
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Create discount'),
                        'name' => 'discount',
                        'class' => 't',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'discount_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'discount_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                        'desc' => $this->l('When the customer customized the product will add to the cart the product with a discount')
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Type of discount'),
                        'name' => 'discount_type',
                        'options' => array(
                            'query' => array(
                                1 => array(
                                    'discount_type' => 'fixed',
                                    'discount_name' => $this->l('Fixed')
                                ),
                                2 => array(
                                    'discount_type' => 'percentage',
                                    'discount_name' => $this->l('Percentage')
                                )
                            ),
                            'id' => 'discount_type',
                            'name' => 'discount_name',
                        ),
                        'class' => 'discount_field',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Discount amount'),
                        'lang' => false,
                        'name' => 'discount_amount',
                        'class' => 'discount_field fixed-width-xl',
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Create as cart rule or specific price'),
                        'name' => 'discount_createdas',
                        'options' => array(
                            'query' => array(
                                1 => array(
                                    'discount_createdas' => 'cartrule',
                                    'discount_name' => $this->l('Cart rule')
                                ),
                                2 => array(
                                    'discount_createdas' => 'specificprice',
                                    'discount_name' => $this->l('Specific price')
                                )
                            ),
                            'id' => 'discount_createdas',
                            'name' => 'discount_name',
                        ),
                        'class' => 'discount_field',
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                )
            ),
        );

        if ($id_configuration && !Tools::isSubmit('deleteidxrcustomproduct_configurations')) {
            $fields_form['form']['input'][] = array(
                'type' => 'hidden',
                'lang' => false,
                'name' => 'id_configuration',
                'value' => (int) $id_configuration
            );
        }

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();
        $helper->module = $this;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitConfiguration';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        if ($id_configuration && !Tools::isSubmit('deleteidxrcustomproduct_configurations')) {
            $helper->tpl_vars = array(
                'fields_value' => $this->getConfigFieldsValues('configuration', $id_configuration),
                'languages' => $this->context->controller->getLanguages(),
                'id_language' => $this->context->language->id
            );
        } else {
            $helper->tpl_vars = array(
                'fields_value' => $this->getConfigFieldsValues(),
                'languages' => $this->context->controller->getLanguages(),
                'id_language' => $this->context->language->id
            );
        }
        $helper->back_url = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name . '&token=' . $helper->token;
        $helper->show_cancel_button = true;

        if (Tools::isSubmit('updateconfiguration') || Tools::isSubmit('submitConfigurationStay') || Tools::isSubmit('updateidxrcustomproduct_configurations')) {
            $bloque_clonar = ''; 
        } else {
            $this->smarty->assign(array(
                'configurations' => IdxConfiguration::getConfigurations(),
                'form_action_url' => $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name . '&token=' . $helper->token
            ));
            $bloque_clonar = $this->display(__FILE__, "views/templates/admin/configuration_clone.tpl");
        }
        
        
        return  $bloque_clonar . $helper->generateForm(array($fields_form)) . $this->renderImageUploader($id_configuration) . $this->renderConstraintModal();
    }

    public function orderComponentsByRestrictions(&$components_selected)
    {
        $ordered_array = array();
        $waiting_list = array();
        while (!empty($components_selected)) {
            $selected = array_shift($components_selected);
            $added = false;
            if (empty($selected['constraints'])) {
                $ordered_array[] = $selected;
                $added = true;
            } else {
                if ($this->allConstraintsBefore($ordered_array, $selected)) {
                    $ordered_array[] = $selected;
                    $added = true;
                } else {
                    $waiting_list[] = $selected;
                }
            }
            if ($waiting_list && $added) {
                $this->processWaiting($ordered_array, $waiting_list);
            }
        }
        if (!empty($waiting_list)) {
            foreach ($waiting_list as $waiting) {
                $waiting['constraints'] = array();
                $waiting['errors'] = $this->l('Restriction integrity error, please check before saving');
                $ordered_array[] = $waiting;
            }
        }
        $components_selected = $ordered_array;
    }

    public function allConstraintsBefore($ordered_array, $selected)
    {
        $parents_before = 0;
        foreach ($selected['constraints'] as $const) {
            $parent_id = explode('_', $const)[0];
            foreach ($ordered_array as $key => $actal_comps) {
                if ($actal_comps['id_component'] == $parent_id) {
                    $parents_before++;
                }
            }
        }
        return (bool) ($parents_before == count($selected['constraints']));
    }

    public function processWaiting(&$ordered_array, &$waiting_list)
    {
        $finish = false;
        while (!$finish) {
            $added = false;
            foreach ($waiting_list as $index => $waiting) {
                if ($this->allConstraintsBefore($ordered_array, $waiting)) {
                    $ordered_array[] = $waiting;
                    unset($waiting_list[$index]);
                    $added = true;
                }
            }
            $finish = !$added;
        }
    }

    public function renderImageUploader($id_configuration)
    {
        if ($id_configuration) {
            $configuration = new IdxConfiguration($id_configuration);
            $attached_products = $configuration->getProducts();
            if ($attached_products) {
                $product_id = array_shift($attached_products);
                $product = new Product($product_id, false, $this->context->language->id);
                $product_url = $this->getImageConfigurationUrl($product);
            }
            $this->smarty->assign(array(
                'id_configuration' => $id_configuration,
                'max_image_size' => (int) Configuration::get('PS_PRODUCT_PICTURE_MAX_SIZE'),
                'product_url' => isset($product_url)?$product_url:''
            ));
            return $this->display(__FILE__, "views/templates/admin/imageUploader_panel.tpl");
        }
    }

    public function getImageConfigurationUrl(Product $product)
    {
        $id_lang = Configuration::get('PS_LANG_DEFAULT', null, null, Context::getContext()->shop->id);

        if (!ShopUrl::getMainShopDomain()) {
            return false;
        }

        $is_rewrite_active = (bool) Configuration::get('PS_REWRITING_SETTINGS');
        if ($this->es17) {
            $preview_url = $this->context->link->getProductLink(
                $product,
                $product->link_rewrite,
                Category::getLinkRewrite($product->id_category_default, $this->context->language->id),
                null,
                $id_lang,
                (int) Context::getContext()->shop->id,
                0,
                $is_rewrite_active,
                false,
                false,
                array('idxcpeditimages' => true)
            );
        } else {
            $preview_url = $this->context->link->getProductLink(
                $product,
                $product->link_rewrite,
                Category::getLinkRewrite($product->id_category_default, $this->context->language->id),
                null,
                $id_lang,
                (int) Context::getContext()->shop->id
            );
            $preview_url = $preview_url . '?idxcpeditimages=true';
        }

        if (!$product->active) {
            $admin_dir = dirname($_SERVER['PHP_SELF']);
            $admin_dir = substr($admin_dir, strrpos($admin_dir, '/') + 1);
            $preview_url .= ((strpos($preview_url, '?') === false) ? '?' : '&') . 'adtoken=' . $this->token . '&ad=' . $admin_dir . '&id_employee=' . (int) $this->context->employee->id . '&idxcpeditimages=true';
        }

        return $preview_url;
    }

    public function renderConstraintModal()
    {
        return $this->display(__FILE__, "views/templates/admin/constraint_modal.tpl");
    }

    public function renderFormAddComponent()
    {
        $types = IdxComponent::getComponentTypes();
        $columns = IdxComponent::getComponentColumns();
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Add a new Component'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Component name'),
                        'lang' => false,
                        'name' => 'name',
                        'desc' => $this->l('Set the internal name for this component'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Component title'),
                        'lang' => true,
                        'name' => 'title',
                        'desc' => $this->l('Set the title for this component'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Component description'),
                        'lang' => true,
                        'name' => 'description',
                        'desc' => $this->l('Set the description for this component'),
                    ),
                    array(
                        'type' => 'select',
                        'lang' => false,
                        'label' => $this->l('Type'),
                        'name' => 'type',
                        'desc' => $this->l('Enter the type of the component.'),
                        'options' => array(
                            'query' => $types,
                            'id' => 'id',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'select',
                        'lang' => false,
                        'label' => $this->l('Columns'),
                        'name' => 'columns',
                        'desc' => $this->l('Enter the amount of options per line.'),
                        'options' => array(
                            'query' => $columns,
                            'id' => 'id',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'file',
                        'label' => $this->l('Option Icon'),
                        'name' => 'icon',
                        'desc' => $this->l('Upload an icon for show just before option name'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                )
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitComponent';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );
        $helper->back_url = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name . '&token=' . $helper->token;
        $helper->show_cancel_button = true;

        $this->smarty->assign(array(
            'components' => $this->getComponents(),
            'form_action_url' => $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name . '&token=' . $helper->token
        ));

        return $this->display(__FILE__, "views/templates/admin/component_clone.tpl") . $helper->generateForm(array($fields_form));
    }

    public function renderEditComponent()
    {
        $id_component = Tools::getValue('id_component');
        if (!$id_component) {
            $id_component = $this->new_id_component;
        }
        $component = new IdxComponent($id_component, true);
        if ($component) {
            return $this->editComponent($component);
        }
    }

    public function getConfigFieldsValues($update = null, $value = null)
    {
        $languages = Language::getLanguages(false);
        $extraConfig = $this->fetchLatestExtraConfig();

        // Set default values if any value is missing
        $jsonidxr_skipped_product_ids = Configuration::get('idxr_skipped_product_ids');
        $skippedIds = json_decode($jsonidxr_skipped_product_ids, true);

        if (!is_array($skippedIds)) {
            $skippedIds = []; // fallback if decoding fails
        }
        $idxr_skipped_product_ids = implode(',', $skippedIds);

        $idxr_prix_fixe = $extraConfig['prix_fixe'] ?? '0.0000';
        $idxr_prix_fixe_vitrine = $extraConfig['prix_fixe_vitrine'] ?? '0.0000';
        $idxr_prix_de_decoupe_cube = $extraConfig['prix_de_decoupe'] ?? '0.0000';
        
        // Set fields for each thickness level for cutting and gluing
        $cutPrices = $extraConfig['cut_prices'];
        $gluePrices = $extraConfig['glue_prices'];
        $polishPrices = $extraConfig['polish_prices'];
        
        
        //Generic
        $fields = array();
        $fields['opt_name'] = $fields['description'] = $fields['title'] = array();
        $fields['addconftitle'] = '';
        foreach ($languages as $lang) {
            $fields['title'][$lang['id_lang']] = '';
            $fields['description'][$lang['id_lang']] = '';
            $fields['opt_name'][$lang['id_lang']] = '';
        }
        $fields['name'] = '';
        $fields['type'] = Tools::getValue('type');
        $fields['type'] = Tools::getValue('optional');
        $fields['zoom_icon'] = Tools::getValue('zoom_icon');
        $fields['button_impact'] = Tools::getValue('button_impact');
        $fields['multivalue'] = Tools::getValue('multivalue');
        $fields['columns'] = Tools::getValue('columns');
        $fields['sel_options'] = Tools::getValue('sel_options');
        $fields['configuration_categories[]'] = Tools::getValue('configuration_categories[]');
        $fields['configuration_products[]'] = Tools::getValue('configuration_products[]');
        $fields['configuration_components[]'] = Tools::getValue('configuration_components[]');
        $fields['configuration_vistype'] = Tools::getValue('configuration_vistype');
        $fields['first_open'] = 1;
        $fields['resume_open'] = 0;
        $fields['button_section'] = 0;
        $fields['configuration_hook'] = Tools::getValue('configuration_hook');
        $fields['color'] = '#000000';
        $fields['component_color'] = '#000000';
        $fields['final_color'] = '#000000';
        $fields['customizable_category'] = Configuration::get(Tools::strtoupper($this->name . '_CATEGORY'));
        $fields['clone_category'] = Configuration::get(Tools::strtoupper($this->name . '_CLONECAT'));
        $fields['pc_image_type'] = Configuration::get(Tools::strtoupper($this->name . '_PCIMGTYPE'));
        $fields['mobile_image_type'] = Configuration::get(Tools::strtoupper($this->name . '_MIMGTYPE'));
        $fields['tablet_image_type'] = Configuration::get(Tools::strtoupper($this->name . '_TIMGTYPE'));
        $fields['coverimageid'] = Configuration::get(Tools::strtoupper($this->name . '_COVERID'));
        $fields['show_fav'] = Configuration::get(Tools::strtoupper($this->name . '_SHOWFAV'));
        $fields['price_impact_taxinclude'] = Configuration::get(Tools::strtoupper($this->name . '_PRICEIMPACTTAX'));
        $fields['discount_line'] = Configuration::get(Tools::strtoupper($this->name . '_DISCOUNTLINE'));
        $fields['maxheightdescription'] = Configuration::get(Tools::strtoupper($this->name . '_MAXHEIGHTDESCRIPTION'));
        // Basic fields
        $fields['prix_fixe'] = $idxr_prix_fixe;
        $fields['prix_fixe_vitrine'] = $idxr_prix_fixe_vitrine;
        $fields['idxr_skipped_product_ids'] = $idxr_skipped_product_ids;
        $fields['idxr_prix_de_decoupe_cube'] = $idxr_prix_de_decoupe_cube;
        
        // Fields for each thickness level of cutting prices
        $fields['cut_price_4mm'] = $cutPrices['4mm'];
        $fields['cut_price_5mm'] = $cutPrices['5mm'];
        $fields['cut_price_6mm'] = $cutPrices['6mm'];
        $fields['cut_price_8mm'] = $cutPrices['8mm'];
        $fields['cut_price_10mm'] = $cutPrices['10mm'];
        
        // Fields for each thickness level of gluing prices
        $fields['glue_price_4mm'] = $gluePrices['4mm'];
        $fields['glue_price_5mm'] = $gluePrices['5mm'];
        $fields['glue_price_6mm'] = $gluePrices['6mm'];
        $fields['glue_price_8mm'] = $gluePrices['8mm'];
        $fields['glue_price_10mm'] = $gluePrices['10mm'];

        // Fields for each thickness level of pulishing prices
        $fields['polish_price_4mm'] = $polishPrices['4mm'];
        $fields['polish_price_5mm'] = $polishPrices['5mm'];
        $fields['polish_price_6mm'] = $polishPrices['6mm'];
        $fields['polish_price_8mm'] = $polishPrices['8mm'];
        $fields['polish_price_10mm'] = $polishPrices['10mm'];

        $fields['priceProductList'] = Configuration::get(Tools::strtoupper($this->name . '_PRICEPRODUCTLIST'));
        $fields['addidname'] = Configuration::get(Tools::strtoupper($this->name . '_ADDIDNAME'));
        $fields['breakdownblock'] = Configuration::get(Tools::strtoupper($this->name . '_BREAKDOWNBLOCK'));
        $fields['adminproductinfoblock'] = Configuration::get(Tools::strtoupper($this->name . '_ADMINPRODUCTINFOBLOCK'));
        $fields['configuration_components_order'] = Tools::getValue('configuration_components_order');
        $fields['active'] = false;
        $fields['bdattachment'] = false;
        $fields['add_base'] = true;
        $fields['show_increment'] = true;
        $fields['show_topprice'] = false;
        $fields['productbase_component'] = false;
        $fields['discount'] = false;
        $fields['discount_type'] = false;
        $fields['discount_amount'] = false;
        $fields['discount_createdas'] = false;
        //with a id
        if ($update) {
            switch ($update) {
                case 'component':
                    $component = $value;
                    if (isset($component)) {
                        foreach ($languages as $lang) {
                            $lang_query = 'Select * from ' . _DB_PREFIX_ . 'idxrcustomproduct_components_lang where id_component = ' . (int) $component->id_component . ' and id_lang = ' . (int) $lang['id_lang'];
                            if ($result = Db::getInstance()->getRow($lang_query)) {
                                $fields['title'][$lang['id_lang']] = $result['title'];
                                $fields['description'][$lang['id_lang']] = $result['description'];
                            }
                        }
                        $fields['name'] = $component->name;
                        $fields['type'] = $component->type;
                        $fields['optional'] = $component->optional;
                        $fields['zoom_icon'] = $component->zoom;
                        $fields['button_impact'] = $component->show_price;
                        $fields['multivalue'] = $component->multivalue;
                        $fields['component_color'] = $component->color;
                        $fields['columns'] = $component->columns;
                        $fields['id_component'] = $component->id_component;
                    }
                    break;
                case 'configuration':
                    if ($value) {
                        $configuration = new IdxConfiguration($value, true);
                        if ($configuration) {
                            $fields['addconftitle'] = $configuration->name;
                            $fields['id_configuration'] = $configuration->id_configuration;
                            $fields['configuration_categories[]'] = $configuration->categories;
                            $fields['configuration_products[]'] = $configuration->products;
                            $fields['configuration_components[]'] = $configuration->components;
                            $fields['configuration_vistype'] = $configuration->visualization;
                            $fields['first_open'] = $configuration->first_open;
                            $fields['resume_open'] = $configuration->resume_open;
                            $fields['button_section'] = $configuration->button_section;
                            $fields['configuration_hook'] = $configuration->hook;
                            $fields['color'] = $configuration->color;
                            $fields['final_color'] = $configuration->final_color;
                            $fields['configuration_components_order'] = $configuration->components_order;
                            $fields['active'] = $configuration->active;
                            $fields['bdattachment'] = $configuration->breakdown_attachment;
                            $fields['add_base'] = $configuration->add_base;
                            $fields['show_increment'] = $configuration->show_increment;
                            $fields['show_topprice'] = $configuration->show_topprice;
                            $fields['productbase_component'] = $configuration->productbase_component;
                            $fields['discount'] = $configuration->discount;
                            $fields['discount_type'] = $configuration->discount_type;
                            $fields['discount_amount'] = $configuration->discount_amount;
                            $fields['discount_createdas'] = $configuration->discount_createdas;
                        }
                    }
                    break;
            }
        }

        return $fields;
    }

    public function addConfiguration($name, $id_configuration = null)
    {
        $configuration = new IdxConfiguration($id_configuration);
        $configuration->name = $name;
        $configuration->categories = $this->simplifyArray(Tools::getValue('configuration_categories'));
        $configuration->products = $this->simplifyArray(Tools::getValue('configuration_products'));
        $configuration->components = Tools::getValue('configuration_components');
        $configuration->components_order = Tools::getValue('configuration_components_order');
        $configuration->visualization = Tools::getValue('configuration_vistype');
        $configuration->first_open = Tools::getValue('first_open');
        $configuration->resume_open = Tools::getValue('resume_open');
        $configuration->button_section = Tools::getValue('button_section');
        $configuration->color = Tools::getValue('color');
        $configuration->final_color = Tools::getValue('final_color');
        $configuration->active = Tools::getValue('active');
        $configuration->hook = Tools::getValue('configuration_hook');
        $configuration->add_base = Tools::getValue('add_base');
        $configuration->show_increment = Tools::getValue('show_increment');
        $configuration->show_topprice = Tools::getValue('show_topprice');
        $configuration->productbase_component = Tools::getValue('productbase_component');
        $configuration->breakdown_attachment = Tools::getValue('bdattachment');
        $configuration->discount = Tools::getValue('discount');
        $configuration->discount_type = Tools::getValue('discount_type');
        $configuration->discount_amount = Tools::getValue('discount_amount');
        $configuration->discount_createdas = Tools::getValue('discount_createdas');
        return $configuration->update();
    }

    public function addComponent($name, $title, $description, $type, $columns = 12, $id_component = false, $zoom = false, $color = false)
    {
        $exist_sql = 'Select id_component from ' . _DB_PREFIX_ . 'idxrcustomproduct_components where name = "' . pSQL($name) . '"';
        $result = Db::getInstance()->getValue($exist_sql);
        if ($result && !$id_component) {
            return $result;
        } else {
            $data = array(
                'name' => pSQL($name),
                'type' => pSQL($type),
                'zoom' => (bool) $zoom,
                'color' => pSQL($color),
                'columns' => (int) $columns,
            );
            if (!$id_component) {
                Db::getInstance()->insert('idxrcustomproduct_components', $data);
                $component_id = Db::getInstance()->Insert_ID();
            } else {
                $where = 'id_component = ' . (int) $id_component;
                Db::getInstance()->update('idxrcustomproduct_components', $data, $where);
                $component_id = (int) $id_component;
            }

            foreach ($title as $lang => $titlel) {
                $ldata = array(
                    'id_component' => (int) $component_id,
                    'id_lang' => (int) $lang,
                    'title' => pSql($titlel),
                    'description' => pSql($description[$lang])
                );
                if (!$id_component) {
                    Db::getInstance()->insert('idxrcustomproduct_components_lang', $ldata);
                } else {
                    $where = 'id_component = ' . (int) $id_component . ' and id_lang = ' . (int) $lang;
                    Db::getInstance()->update('idxrcustomproduct_components_lang', $ldata, $where);
                }
            }
            return $component_id;
        }
    }

    public function addConstraint($configuration_id, $component_id, $constraint)
    {
        $sql = 'Select constraints_options from ' . _DB_PREFIX_ . 'idxrcustomproduct_configurations where id_configuration = ' . (int) $configuration_id;
        $actual_constraint = Db::getInstance()->getValue($sql);
        $constraint_array = explode(',', $actual_constraint);
        $new_constraint = $component_id . '@' . $constraint;
        if (!in_array($new_constraint, $constraint_array)) {
            $constraint_array[] = $new_constraint;
            $new_constraints = implode(',', $constraint_array);
            Db::getInstance()->update('idxrcustomproduct_configurations', array('constraints_options' => pSQL($new_constraints)), 'id_configuration = ' . (int) $configuration_id);
        } else {
            echo 'ya esta en el listado de restricciones';
        }
    }

    public function delConstraint($configuration_id, $component_id, $constraint)
    {
        $sql = 'Select constraints_options from ' . _DB_PREFIX_ . 'idxrcustomproduct_configurations where id_configuration = ' . (int) $configuration_id;
        $actual_constraint = Db::getInstance()->getValue($sql);
        $constraint_array = explode(',', $actual_constraint);
        $new_constraint = $component_id . '@' . $constraint;
        if (in_array($new_constraint, $constraint_array)) {
            $key = array_search($new_constraint, $constraint_array);
            unset($constraint_array[$key]);
            $new_constraints = implode(',', $constraint_array);
            Db::getInstance()->update('idxrcustomproduct_configurations', array('constraints_options' => pSQL($new_constraints)), 'id_configuration = ' . (int) $configuration_id);
        } else {
            echo 'no esta en el listado de restricciones';
        }
    }

    public function getConfigurationByProduct($id_product, $hook = '')
    {
        $configurations = IdxConfiguration::getConfigurations($hook);
        $categories = Product::getProductCategories($id_product);
        $id_configuration = false;

        foreach ($configurations as &$conf) {
            if ($conf['active'] == 0) {
                continue;
            }
            $conf['products'] = explode(',', $conf['products']);
            $conf['categories'] = explode(',', $conf['categories']);
            if (in_array($id_product, $conf['products'])) {
                $id_configuration = $conf['id_configuration'];
                break;
            } else {
                if (array_intersect($categories, $conf['categories'])) {
                    $id_configuration = $conf['id_configuration'];
                    break;
                }
            }
        }

        return $id_configuration;
    }
    
    public function getConfigurationByCategory($id_category, $hook = '')
    {
        $configurations = IdxConfiguration::getConfigurations($hook);
        $id_configuration = false;
        
        foreach ($configurations as &$conf) {
            if ($conf['active'] == 0) {
                continue;
            }
            $conf['categories'] = explode(',', $conf['categories']);
            if (in_array($id_category, $conf['categories'])) {
                $id_configuration = $conf['id_configuration'];
                break;
            }
        }

        return $id_configuration;
    }

    public function getConfigurationFront($id_configuration)
    {
        $sql_components = 'Select * from ' . _DB_PREFIX_ . 'idxrcustomproduct_configurations where id_configuration = ' . (int) $id_configuration;
        $row = Db::getInstance()->getRow($sql_components);
        $components = explode(',', $row['components']);
        $customization = array();
        $customization['visualization'] = $row['visualization'];
        $customization['steps'] = count($components);
        $customization['components'] = array();
        $customization['final_color'] = $row['final_color'];
        $customization['add_base'] = $row['add_base'];
        $customization['show_increment'] = $row['show_increment'];
        $customization['show_topprice'] = $row['show_topprice'];
        $customization['productbase_component'] = $row['productbase_component'];
        $customization['first_open'] = $row['first_open'];
        $customization['resume_open'] = $row['resume_open'];
        $customization['button_section'] = $row['button_section'];
        $customization['default_configuration'] = $row['default_configuration'] ? $row['default_configuration'] : '';
        $customization['step_active'] = false;
        $customization['discount'] = $row['discount'];
        $customization['discount_type'] = $row['discount']?$row['discount_type']:false;
        $customization['discount_amount'] = $row['discount']?$row['discount_amount']:false;
        $tax_ratio = 100;
        $id_product = Tools::getValue('id_product');

        if (!$id_product) {
            if ($row['products']) {
                $products = explode(',', $row['products']);
                $id_product = array_shift($products);
            }
        }

        if (!$id_product) {
            if ($row['categories']) {
                $categories = explode(',', $row['categories']);
                $id_category = array_shift($categories);
                $id_product = Db::getInstance()->getValue('select id_product from '._DB_PREFIX_.'category_product where id_category = '.(int)$id_category);
            }
        }

        //This is only for show without taxes, final product will be created with taxes
        if (isset($this->context->customer)) {
            $without_taxes = Group::getPriceDisplayMethod($this->context->customer->id_default_group);
        } else {
            $without_taxes = false;
        }

        $product_tax_rule_group_id = Product::getIdTaxRulesGroupByIdProduct($id_product);
        $default_rate = TaxRulesGroup::getAssociatedTaxRatesByIdCountry(Configuration::get('PS_COUNTRY_DEFAULT'));
        $tax_rate = isset($default_rate[$product_tax_rule_group_id])?$default_rate[$product_tax_rule_group_id]:0;
        $tax_ratio += $tax_rate;
        $tax_ratio /= 100;
        $customization['without_taxes'] = $without_taxes;
        $customization['tax_ratio'] = $tax_ratio;

        if ($customization['productbase_component']) {
            $product_comp_id = IdxComponent::getComponentIdByProduct($id_product);
            if ($product_comp_id) {
                array_unshift($components, $product_comp_id);
            }
        }

        $constraints = $this->proccessConstraints($row['constraints_options']);
        $option_impacts = IdxConfiguration::getOptionsImpactStatic($id_configuration);
        $image_file_base = _PS_IMG_DIR_ . 'idxrcustomproduct' . DIRECTORY_SEPARATOR . 'options' . DIRECTORY_SEPARATOR;
        foreach ($components as $id_component) {
            if (!$id_component) {
                continue;
            }
            $comps = $this->getComponents($id_component, $id_configuration, true);
            if (!$comps) {
                continue;
            }
            foreach ($comps as $comp) {
                $comp['options'] = json_decode($comp['json_values']);
                $comp['icon_exist'] = file_exists(_PS_MODULE_DIR_ . $this->name . '/img/icon/' . $id_component . '.png');
                if (strpos($comp['id_component'], 'f') === false && !$comp['parent']) {
                    if (!isset($comp['constraint'])) {
                        $comp['constraint'] = array();
                        foreach ($constraints as $constraint) {
                            if ($constraint['target'] == $id_component) {
                                $comp['constraint'][] = $constraint['source'];
                            }
                        }
                    }
                    if (isset($comp['options']->options)) {
                        foreach ($comp['options']->options as &$option) {
                            if ($comp['type'] == 'sel_img') {
                                $image = $comp['id_component'] . '_' . $option->id;
                                if (!file_exists($image_file_base . $image . '.png')) {
                                    if (file_exists($image_file_base . $image . '.jpg')) {
                                        imagepng(imagecreatefromjpeg($image_file_base . $image . '.jpg'), $image_file_base . $image . '.png');
                                    }
                                }
                            }
                            $option->img_ext = 'png';
                            $this->generateImpact($option, $without_taxes, $tax_ratio, $id_component, $id_product);
                            $option->price_impact_formatted = $this->formatPrice($option->price_impact);
                            if (isset($option->price_impact_wodiscount) && $option->price_impact_wodiscount) {
                                $option->price_impact_wodiscount_formatted = $this->formatPrice($option->price_impact_wodiscount);
                            }
                        }
                    }
                } else {
                    if (isset($comp['options']->options)) {
                        foreach ($comp['options']->options as &$option) {
                            $option->img_ext = 'png';
                            $this->generateProductImpact($option, $without_taxes, $tax_ratio, $id_component);
                            $option->price_impact_formatted = $this->formatPrice($option->price_impact);
                            if (isset($option->price_impact_wodiscount) && $option->price_impact_wodiscount) {
                                $option->price_impact_wodiscount_formatted = $this->formatPrice($option->price_impact_wodiscount);
                            }
                        }
                    }
                }
                
                unset($comp['json_values']);
                $comp['impact_options'] = [];
                foreach ($option_impacts as $option_impact) {
                    if (strpos($option_impact['option_impacted'], $comp['id_component'].'_') === 0) {
                        $comp['impact_options'][] = $option_impact;
                    }
                }
                $customization['components'][] = $comp;
            }
        }

        $customization['step_active'] = false;
        $values = array();
        $show_resume = true;
        foreach ($customization['components'] as &$comp) {
            $values[$comp['id_component']] = $comp['default_opt'];
            $visible = ($comp['default_opt'] >= 0) ? true : false;
            if ($visible && $comp['constraint']) {
                foreach ($comp['constraint'] as $constraint) {
                    $req_val = explode('_', $constraint);
                    if (!isset($values[$req_val[0]]) || $values[$req_val[0]] != $req_val[1]) {
                        $visible = false;
                    } else {
                        break;
                    }
                }
            }
            if ($comp['constraint'] && !$visible) {
                $comp['display'] = false;
            } else {
                $comp['display'] = true;
                if ($comp['default_opt'] == -1) {
                    $show_resume = false;
                }
            }
            if ($visible && $comp['default_opt'] < 0 && !$customization['step_active']) {
                $customization['step_active'] = $comp['id_component'];
            }
        }
        if ($show_resume && !$customization['step_active']) {
            $customization['step_active'] = 'resume';
        }
        
        return $customization;
    }

    public function generateImpact(&$option, $without_taxes, $tax_ratio, $id_component, $id_product, $id_attribute = null, $default_currency = false)
    {
        // The final prices in this function are with tax
        $impact_query = 'Select * from ' . _DB_PREFIX_ . 'idxrcustomproduct_components_opt_impact where id_component = ' . (int) $id_component . ' and id_option = ' . (int) $option->id;
        $result = Db::getInstance()->getRow($impact_query);
        $option->weight_impact = (float) $result['weight_impact'];
        $option->reference = $result['reference'];
        $attached_price = 0;
        if ($result['att_product'] && $result['att_product'] != 'none' && $result['att_qty']) {
            $prod_id = explode('_', $result['att_product']);
            $product_id = (int) $prod_id[0];
            $attr_id = isset($prod_id[1]) ? (int) $prod_id[1] : null;
            $qty = Product::getQuantity($product_id, $attr_id);
            $option->max_qty = (int) $qty;
            $attached_price = Product::getPriceStatic($product_id, true, $attr_id);
            $product = new Product($product_id);
            $product->id_product_attribute = $attr_id;
            $stock_management_active = Configuration::get('PS_STOCK_MANAGEMENT');
            $allow_shipping = true;
            if (isset($this->context->cart->id_address_delivery) && $this->context->cart->id_address_delivery) {
                $avalible_carriers = Carrier::getAvailableCarrierList($product, false, $this->context->cart->id_address_delivery);
                $allow_shipping = count($avalible_carriers);
            }
            
            if (!$stock_management_active || ($qty >= $result['att_qty'])) {
                $option->active = true;
                if (!$allow_shipping) {
                    $option->active = false;
                    $option->ofs_text = $this->l('No availabe carriers for this combination');
                }
            } else {
                //actual stock not enought
                $option->ofs_text = $product->available_later[$this->context->language->id];
                if ($product->checkQty($result['att_qty'])) {
                    $option->out_of_stock = 1;
                    $option->active = true;
                    if ($option->ofs_text) {
                        $option->description .= " ".$option->ofs_text;
                    }
                } else {
                    $option->active = false;
                }
            }
            $option->tax = (($product->getTaxesRate()) / 100) + 1;
        } else {
            $option->active = true;
            $option->tax = false;
        }
        if ($id_product && $result['price_impact_type'] == 'calculated' && $result['price_impact_calc']) {
            $equation = $result['price_impact_calc'];
            $base_price = Product::getPriceStatic($id_product, true, $id_attribute);
            $search = array('[BasePrice]', '[AttachedProductPrice]', '[AttachedProductQuantity]');
            $replace = array($base_price, $attached_price, $result['att_qty']);
            $toexecute = str_replace($search, $replace, $equation);
            $result['price_impact'] = $this->calculator($toexecute);
            //TOD create same calculation process for without discout price
            $attached_price_wod = Product::getPriceStatic($id_product, true, $id_attribute,6,null,false,false);
            $result['price_impact_wodiscount'] = $attached_price_wod;
        } else { //check context currency
            if ($result['price_impact'] != 0 && !Configuration::get(Tools::strtoupper($this->name . '_PRICEIMPACTTAX'))){
                $result['price_impact'] *= $tax_ratio;
                $result['price_impact_wodiscount'] = $result['price_impact_wodiscount']?$result['price_impact_wodiscount']*$tax_ratio:null;
            }
            if ($result['price_impact'] != 0 && !$default_currency && $this->context->currency->id != CurrencyCore::getDefaultCurrency()->id) {
                $result['price_impact'] *=$this->context->currency->conversion_rate;
                $result['price_impact_wodiscount'] = $result['price_impact_wodiscount']?$result['price_impact_wodiscount']*$this->context->currency->conversion_rate:null;
            }
        }
        $option->price_impact = (float) ($without_taxes ? $result['price_impact'] / $tax_ratio : $result['price_impact']);
        if ($result['price_impact_wodiscount']) {
            $option->price_impact_wodiscount = (float) ($without_taxes ? $result['price_impact_wodiscount'] / $tax_ratio : $result['price_impact_wodiscount']);
        }
    }

    public function generateProductImpact(&$option, $without_taxes, $tax_ratio, $id_component, $default_currency = false)
    {
        if (isset($option->att_product) && $option->att_product) {
            $prod_id = explode('_', $option->att_product);
            $product_id = (int) $prod_id[0];
            $attr_id = isset($prod_id[1]) ? (int) $prod_id[1] : null;
            $att_stock = Product::getQuantity((int) $product_id, (int) $attr_id);
            $option->max_qty = $att_stock;
            $qty = 1;
            $attached_price = Product::getPriceStatic($product_id, true, $attr_id,6,null,false,false);//without reductions
            if ($attached_price != 0 && $default_currency && $this->context->currency->id != CurrencyCore::getDefaultCurrency()->id) {
                //return to default currency
                $attached_price /=$this->context->currency->conversion_rate;
            }
            $option->price_impact = (float) ($without_taxes ? $attached_price / $tax_ratio : $attached_price);
            $product = new Product($product_id);
            $product->id_product_attribute = $attr_id;
            $stock_management_active = Configuration::get('PS_STOCK_MANAGEMENT');
            $allow_shipping = true;
            if (isset($this->context->cart->id_address_delivery) && $this->context->cart->id_address_delivery) {
                $avalible_carriers = Carrier::getAvailableCarrierList($product, false, $this->context->cart->id_address_delivery);
                $allow_shipping = count($avalible_carriers);
            }
            if (!$stock_management_active || ($att_stock >= $qty)) {
                $option->active = true;
                if (!$allow_shipping) {
                    $option->active = false;
                    $option->ofs_text = $this->l('No availabe carriers for this combination');
                }
            } else {
                $minimal_qty = Db::getInstance()->getValue('select minimal_quantity from ' . _DB_PREFIX_ . 'product_attribute where id_product = ' . (int) $prod_id . ' and id_product_attribute = ' . (int) $attr_id);
                if (!$minimal_qty) {
                    $minimal_qty = 1;
                }
                if ($product->checkQty($minimal_qty)) {
                    $option->out_of_stock = 1;
                    $option->active = true;
                } else {
                    $option->active = false;
                    $option->ofs_text = $product->available_later[$this->context->language->id];
                }
            }
            $option->tax = (($product->getTaxesRate()) / 100) + 1;
        } else {
            $option->active = true;
            $option->tax = false;
            $option->price_impact = false;
        }
        $option->weight_impact = 0;
        $option->reference = '';
    }

    public function calculator($expresion)
    {
        $expresion = trim($expresion);
        if (is_numeric($expresion)) {
            return $expresion;
        }

        $operators = array('*','/','+','-');
        foreach ($operators as $operator) {
            if (strpos($expresion, $operator) !== false) {
                $elements = explode($operator, $expresion, 2);
                $results = array_map(array($this, 'calculator'), $elements);
                switch ($operator) {
                    case '*':
                        return $results[0] * $results[1];
                        break;
                    case '/':
                        return $results[0] / $results[1];
                        break;
                    case '+':
                        return $results[0] + $results[1];
                        break;
                    case '-':
                        return $results[0] - $results[1];
                        break;
                    default:
                        break;
                }
            }
        }

        return false;
    }

    public function proccessConstraints($constraints_text)
    {
        $constaint_array = explode(',', $constraints_text);
        $constraints = array();
        foreach ($constaint_array as $constraint_text) {
            if ($constraint_text) {
                $parts = explode('@', $constraint_text);
                $constraint = array(
                    'target' => $parts[0],
                    'source' => $parts[1]
                );
                $constraints[] = $constraint;
            }
        }
        return $constraints;
    }

    public function getComponents($id_component = false, $id_configuration = false, $only_parents = true, $base_product = false)
    {
        $id_lang = (int) $this->context->language->id;
        $query = 'Select * from ' . _DB_PREFIX_ . 'idxrcustomproduct_components comp '
                . 'left join ' . _DB_PREFIX_ . 'idxrcustomproduct_components_lang compl on comp.id_component = compl.id_component and compl.id_lang = ' . $id_lang
                . ' where 1=1';

        if (!$id_component &&
                ((Tools::isSubmit('submitFilteridxrcustomproduct_components') and Tools::getValue('submitFilteridxrcustomproduct_components') == '1')
                ||(Tools::isSubmit('submitFiltercomponent') and Tools::getValue('submitFiltercomponent') == '1'))) {
            $id_filter = Tools::getValue('componentFilter_id_component')?Tools::getValue('componentFilter_id_component'):Tools::getValue('idxrcustomproduct_componentsFilter_id_component');

            if ($id_filter) {
                $id_component = $id_filter;
            }

            $name_filter = Tools::getValue('componentFilter_name')?Tools::getValue('componentFilter_name'):Tools::getValue('idxrcustomproduct_componentsFilter_name');
            if ($name_filter) {
                $query .= ' and comp.name like "%' . pSQL($name_filter) . '%"';
            }

            $title_filter = Tools::getValue('componentFilter_title')?Tools::getValue('componentFilter_title'):Tools::getValue('idxrcustomproduct_componentsFilter_title');
            if ($title_filter) {
                $query .= ' and compl.title like "%' . pSQL(($title_filter)) . '%"';
            }

            $desc_filter = Tools::getValue('componentFilter_description')?Tools::getValue('componentFilter_description'):Tools::getValue('idxrcustomproduct_componentsFilter_description');
            if ($desc_filter) {
                $query .= ' and compl.description like "%' . pSQL(($desc_filter)) . '%"';
            }
        }

        if ($id_component) {
            $query .= ' and comp.id_component = ' . (int) $id_component;
        }

        if ($only_parents) {
            $query .= ' and (comp.parent = 0 || comp.parent IS NULL) ';
        }

        if (Tools::isSubmit('componentOrderby')) {
            $column = Tools::getValue('componentOrderby');
            $table = 'comp.';
            if ($column == 'type_l') {
                $column = 'type';
            }
            if ($column == 'title' || $column == 'description') {
                $table = 'compl.';
            }
            $query .= ' order by '.$table.pSQL($column).' '.pSQL(Tools::getValue('componentOrderway'));
        }

        $results = Db::getInstance()->executeS($query);

        if ($results && !$results[0]['id_component']) {
            $this->fixcomponentlanguage($id_lang);
            return $this->getComponents($id_component, $id_configuration);
        }
        if ($id_configuration) {
            $color = Db::getInstance()->getValue('Select color from ' . _DB_PREFIX_ . 'idxrcustomproduct_configurations where id_configuration = ' . (int) $id_configuration);
            foreach ($results as $key => $result) {
                if ($result['type'] == 'product') {
                    $id_parent = $result['id_component'];
                    unset($results[$key]);
                    $product_components = IdxComponent::getChildrenComponent($id_parent, $id_lang);
                    $results = array_merge($results, $product_components);
                }
            }
            foreach ($results as &$result) {
                if (!$result['color']) {
                    $result['color'] = $color;
                }
            }
        }
        foreach ($results as &$result) {
            $result['taxChange'] = IdxComponent::hasTaxchange($result['id_component']);
        }

        return $results;
    }

    public function fixcomponentlanguage($id_lang)
    {
        $components = Db::getInstance()->executeS('Select id_component from ' . _DB_PREFIX_ . 'idxrcustomproduct_components');
        if ($components) {
            foreach ($components as $component) {
                $id_component = $component['id_component'];
                $exist_lang = Db::getInstance()->getValue('Select id_components_lang from ' . _DB_PREFIX_ . 'idxrcustomproduct_components_lang where id_component = ' . (int) $id_component . ' and id_lang = ' . (int) $id_lang);
                if (!$exist_lang) {
                    $data = array(
                        'id_component' => (int) $id_component,
                        'id_lang' => (int) $id_lang,
                        'title' => '',
                        'description' => ''
                    );
                    Db::getInstance()->insert('idxrcustomproduct_components_lang', $data);
                }
            }
        }
    }

    public function getComponentOptions($id_component)
    {
        $getjsonsql = 'Select icl.id_lang ,icl.json_values, ic.type, ic.default_opt from ' . _DB_PREFIX_ . 'idxrcustomproduct_components_lang icl '
                . 'left join ' . _DB_PREFIX_ . 'idxrcustomproduct_components ic on icl.id_component = ic.id_component '
                . 'where icl.id_component = ' . (int) $id_component . ';';
        $results = Db::getInstance()->executeS($getjsonsql);
        $data = array();
        if ($results && count($results) > 0) {
            $data['type'] = $results[0]['type'];
            $data['lang'] = array();
            foreach ($results as $result) {
                $json_data = json_decode($result['json_values']);
                $options = array();
                if (isset($json_data->options)) {
                    foreach ($json_data->options as $option) {
                        $sql_impact = 'Select * from ' . _DB_PREFIX_ . 'idxrcustomproduct_components_opt_impact where id_component = ' . (int) $id_component . ' and id_option = ' . (int) $option->id;
                        $impact = Db::getInstance()->getRow($sql_impact);
                        $option->price_impact = (float) $impact['price_impact'];
                        $option->weight_impact = (float) $impact['weight_impact'];
                        $option->attach_product = $impact['att_product'];
                        $option->attach_product_qty = $impact['att_qty'];
                        $option->reference = $impact['reference'];
                        $option->img_ext = 'png';
                        $option->max_qty = 1000;
                        $option->tax_change = $impact['taxchange'];
                        if ($option->id == $results[0]['default_opt']) {
                            $option->default = true;
                        } else {
                            $option->default = false;
                        }
                        $options[$option->id] = $option;
                    }
                    unset($json_data->options);
                    $json_data->options = $options;
                }

                $data['lang'][$result['id_lang']] = $json_data;
            }
        }
        return $data;
    }

    public function getComponentName($id_component, $public_name = false, $id_lang = false)
    {
        if (!$public_name) {
            $getNameSql = 'Select name from ' . _DB_PREFIX_ . 'idxrcustomproduct_components where id_component = ' . (int) $id_component;
        } else {
            $getNameSql = 'Select title from ' . _DB_PREFIX_ . 'idxrcustomproduct_components_lang where id_component = ' . (int) $id_component . ' and id_lang = ' . (int) $id_lang;
        }
        return Db::getInstance()->getValue($getNameSql);
    }

    public function editComponent($component)
    {
        if ($component->type == 'product') {
            return $this->generateProductOptionsSelector($component);
        }
        if (file_exists(_PS_MODULE_DIR_ . $this->name . '/img/icon/' . $component->id_component . '.png')) {
            $component->icon_preview = true;
        }
        $types = IdxComponent::getComponentTypes();
        $columns = IdxComponent::getComponentColumns();    
        $multivalue_types = IdxComponent::getMultivalueTypes(IdxComponent::hasTaxchange($component->id_component));
        $inputs = array(
            array(
                'type' => 'text',
                'label' => $this->l('Component name'),
                'lang' => false,
                'name' => 'name',
                'desc' => $this->l('Set the internal name for this component'),
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Component title'),
                'lang' => true,
                'name' => 'title',
                'desc' => $this->l('Set the title for this component'),
            ),
            array(
                'type' => 'textarea',
                'html' => true,
                'label' => $this->l('Component description'),
                'lang' => true,
                'name' => 'description',
                'desc' => $this->l('Set the description for this component'),
                'autoload_rte' => 'rte',
            ),
            array(
                'type' => 'select',
                'lang' => false,
                'label' => $this->l('Type'),
                'name' => 'type',
                'desc' => $this->l('Enter the type of the component.'),
                'options' => array(
                    'query' => $types,
                    'id' => 'id',
                    'name' => 'name'
                )
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Optional'),
                'name' => 'optional',
                'values' => array(
                    array(
                        'id' => 'optional_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ),
                    array(
                        'id' => 'optional_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ),
                ),
                'desc' => $this->l('if you check as optional this product can be customized without select any option of this component')
            ),
            array(
                'type' => 'file',
                'label' => $this->l('Option Icon'),
                'name' => 'icon',
                'desc' => $this->l('Upload an icon for show just before option name, image size: 30px x 30px'),
            ),
            array(
                'type' => 'component_icon',
                'label' => $this->l('Icon preview'),
                'name' => 'icon_preview',
                'component' => $component
            ),
            array(
                'type' => 'select',
                'lang' => false,
                'label' => $this->l('Columns'),
                'name' => 'columns',
                'desc' => $this->l('Enter the amount of options per line.'),
                'options' => array(
                    'query' => $columns,
                    'id' => 'id',
                    'name' => 'name'
                )
            ),
            array(
                'type' => 'color',
                'label' => $this->l('Background colour'),
                'name' => 'component_color',
                'desc' => $this->l('Color for the container background'),
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show zoom option?'),
                'name' => 'zoom_icon',
                'values' => array(
                    array(
                        'id' => 'zoom_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ),
                    array(
                        'id' => 'zoom_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ),
                ),
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show price impact in option selector?'),
                'name' => 'button_impact',
                'values' => array(
                    array(
                        'id' => 'button_impact_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ),
                    array(
                        'id' => 'button_impact_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ),
                ),
            ),
            array(
                'type' => 'select',
                'lang' => false,
                'label' => $this->l('Multiple value'),
                'name' => 'multivalue',
                'desc' => $this->l('Select type of selection for this component.'),
                'options' => array(
                    'query' => $multivalue_types,
                    'id' => 'id',
                    'name' => 'name'
                )
            ),
            array(
                'type' => 'hidden',
                'name' => 'id_component',
            ),
        );

        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Edit a component'),
                    'icon' => 'icon-cogs'
                ),
                'input' => $inputs,
                'submit' => array(
                    'title' => $this->l('Save'),
                )
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();
        $helper->module = $this;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'editComponent';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'tinymce' => true,
            'fields_value' => $this->getConfigFieldsValues('component', $component),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );
        $helper->back_url = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name . '&token=' . $helper->token;
        $helper->show_cancel_button = true;
        if ($component->type != 'text' && $component->type != 'textarea' && $component->type != 'file') {
            return $helper->generateForm(array($fields_form)) . $this->generateOptionsForm($component);
        } else {
            return $helper->generateForm(array($fields_form)) . $this->generateConfigurationForm($component);
        }
    }

    public function deleteConfiguration($id_configuration)
    {
        $configuration = new IdxConfiguration($id_configuration);
        if ($configuration) {
            $configuration->delete();
        }
    }

    public function deleteComponent($id_component)
    {
        $component = new IdxComponent($id_component);
        $component->delete();
    }

    public function saveComponentIcon($id_component)
    {
        $destination_path = _PS_ROOT_DIR_ . '/modules/' . $this->name . '/img/icon' . DIRECTORY_SEPARATOR;
        $array = explode('.', $_FILES['icon']['name']);
        $extension = end($array);
        $destination_path_png = $destination_path . (int) $id_component . '.png';
        $destination_path .= (int) $id_component . '.' . $extension;
        
        if (@move_uploaded_file($_FILES['icon']['tmp_name'], $destination_path)) {
            switch ($extension) {
                case "jpg": case "jpeg":
                    $image = imagecreatefromjpeg($destination_path);
                    imagepng($image, $destination_path_png);
                    imagedestroy($image);
                    break;
                case "gif":
                    $image = imagecreatefromgif($destination_path);
                    imagepng($image, $destination_path_png);
                    imagedestroy($image);
                    break;
                default:
                   break;
            }
        }
    }

    public function updateOption($id_component, $id_option, $data)
    {
        IdxComponent::checkComponentLangs($id_component);
        $get_options_sql = 'Select * from ' . _DB_PREFIX_ . 'idxrcustomproduct_components_lang where id_component = ' . (int) $id_component . ';';
        $results = Db::getInstance()->ExecuteS($get_options_sql);
        $data = json_decode($data);
        if ($results && count($results) > 0) {
            $json_values_ok = '';
            foreach ($results as $result) {
                if ($result['json_values'] == '{"options":[]}' || $result['json_values'] == "") {
                    $result['json_values'] = $json_values_ok;
                } else {
                    $json_values_ok = $result['json_values'];
                }
                $lang = $result['id_lang'];
                $json_data = json_decode($result['json_values']);
                $options = array();
                if (!$json_data){
                    continue;
                }
                foreach ($json_data->options as $option) {
                    if ($option->id == $id_option) {
                        foreach ($data as $value) {
                            if ($value->id == 'optionname_' . (int) $id_option . '_' . (int) $lang) {
                                $option->name = $value->val;
                            }
                            if ($value->id == 'optiondesc_' . (int) $id_option . '_' . (int) $lang) {
                                $option->description = $value->val;
                            }
                        }
                        $options[] = $option;
                    } else {
                        $options[] = $option;
                    }
                }
                unset($json_data->options);
                $json_data->options = $options;
                $update = array('json_values' => Db::getInstance()->escape(json_encode($json_data, JSON_UNESCAPED_UNICODE)));
                $where = 'id_component = ' . (int) $id_component . ' and id_lang = ' . (int) $result['id_lang'];
                Db::getInstance()->update('idxrcustomproduct_components_lang', $update, $where);
            }
            $price_impact = 0;
            $price_impact_wodiscount = 0;
            $weight_impact = 0;
            $reference = '';
            foreach ($data as $value) {
                if ($value->id == 'priceimpacttype_' . $id_option) {
                    $price_impact_type = ($value->val == 0) ? 'fixed' : 'calculated';
                }
                if ($value->id == 'option_priceimpact_' . $id_option) {
                    $price_impact = $value->val;
                }
                if ($value->id == 'option_priceimpact_wodiscount_' . $id_option) {
                    $price_impact_wodiscount = $value->val;
                }
                if ($value->id == 'option_priceimpactcalc_' . $id_option) {
                    $price_impact_calc = $value->val;
                }
                if ($value->id == 'option_weightimpact') {
                    $weight_impact = $value->val;
                }
                if ($value->id == 'option_reference') {
                    $reference = $value->val;
                }
                if ($value->id == 'productattachtype_' . $id_option) {
                    $product_attach_type = ($value->val == 0) ? 'base' : 'product';
                }
                if ($value->id == 'option_' . $id_option . '_product_attached_value') {
                    $product_attached = $value->val;
                }
                if ($value->id == 'option_product_qty') {
                    $product_attached_qty = $value->val;
                }
                if ($value->id == 'option_taxchange_' . $id_option) {
                    $product_tax_change = $value->val;
                }
            }
            
            $exist = Db::getInstance()->getValue('Select id_component from '. _DB_PREFIX_ . 'idxrcustomproduct_components_opt_impact where id_component = ' . (int) $id_component . ' and id_option = ' . (int) $id_option);
            
            if (!$exist) {
                $data = array(
                    'id_component' => (int) $id_component,
                    'id_option' => (int) $id_option
                );
                Db::getInstance()->insert('idxrcustomproduct_components_opt_impact', $data);
            }
            
            $impact_update = 'Update ' . _DB_PREFIX_ . 'idxrcustomproduct_components_opt_impact set'
                    . ' price_impact_type = "' . pSQL($price_impact_type) . '" '
                    . ', price_impact = ' . (float) $price_impact
                    . ', price_impact_wodiscount = ' . (float) $price_impact_wodiscount
                    . ', price_impact_calc = "' . pSQL($price_impact_calc) . '" '
                    . ', weight_impact = ' . (float) $weight_impact
                    . ', reference = "' . pSQL($reference) . '" '
                    . ', attach_product_type = "'. pSQL($product_attach_type) . '" '
                    . ', att_product = "' . pSQL($product_attached) . '" '
                    . ', att_qty = "' . (int) $product_attached_qty . '" '
                    . ', taxchange = '. ($product_tax_change?(int)$product_tax_change:'NULL')
                    . ' where id_component = ' . (int) $id_component . ' and id_option = ' . (int) $id_option . ';';

            if ($product_tax_change) {
                IdxComponent::disableMultiOption($id_component);
            }
            Db::getInstance()->execute($impact_update);
        }
    }

    public function updateOptionFile($id_component, $file_size, $extensions)
    {
        $data = new stdClass();
        $data->size = $file_size;
        $data->allowed_extension = $extensions;
        $languages = Language::getLanguages(false);
        foreach ($languages as $lang) {
            $update = array('json_values' => Db::getInstance()->escape(json_encode($data, JSON_UNESCAPED_UNICODE)));
            $where = 'id_component = ' . (int) $id_component . ' and id_lang = ' . (int) $lang['id_lang'];
            Db::getInstance()->update('idxrcustomproduct_components_lang', $update, $where);
        }
    }

    public function deleteOption($id_component, $id_option)
    {
        $get_options_sql = 'Select * from ' . _DB_PREFIX_ . 'idxrcustomproduct_components_lang where id_component = ' . (int) $id_component . ';';
        $results = Db::getInstance()->ExecuteS($get_options_sql);
        if ($results && count($results) > 0) {
            foreach ($results as $result) {
                $json_data = json_decode($result['json_values']);
                $options = array();
                foreach ($json_data->options as $option) {
                    if ($option->id != $id_option) {
                        $options[] = $option;
                    }
                }
                unset($json_data->options);
                $json_data->options = $options;
                $update = array(
                    'json_values' => Db::getInstance()->escape(json_encode($json_data, JSON_UNESCAPED_UNICODE))
                );
                $where = 'id_component = ' . (int) $id_component . ' and id_lang = ' . (int) $result['id_lang'];
                Db::getInstance()->update('idxrcustomproduct_components_lang', $update, $where);
                $sql_delete = 'Delete from ' . _DB_PREFIX_ . 'idxrcustomproduct_components_opt_impact where id_component = ' . (int) $id_component . ' and id_option = ' . (int) $id_option . ';';
                Db::getInstance()->execute($sql_delete);
            }
        }
    }

    public function setDefaultComponentOption($component_id, $option_id)
    {
        $data = array(
            'default_opt' => (int) $option_id
        );
        Db::getInstance()->update('idxrcustomproduct_components', $data, 'id_component = ' . (int) $component_id);
    }

    public function setDefaultConfigurationComponentOption($configuration_id, $component_id, $option_id)
    {
        $actual_default = Db::getInstance()->getValue('Select default_configuration from ' . _DB_PREFIX_ . 'idxrcustomproduct_configurations where id_configuration = ' . (int) $configuration_id);
        $default_arr = json_decode($actual_default, true);
        if (!$default_arr) {
            $default_arr = array();
        }
        $value = false;
        switch ($option_id) {
            case 'disable':
                $value = -1;
                break;
            case 'inherit':
                $value = -2;
                break;
            default:
                $option_parts = explode('_', $option_id);
                if (isset($option_parts[1])) {
                    $value = $option_parts[1];
                }
                break;
        }
        $default_arr[$component_id] = $value;
        $update = array(
            'default_configuration' => json_encode($default_arr, JSON_UNESCAPED_UNICODE)
        );
        $where = 'id_configuration = ' . (int) $configuration_id;
        Db::getInstance()->update('idxrcustomproduct_configurations', $update, $where);
    }

    public function generateOptionsForm($component)
    {
        if ($component->parent > 0) {
            return '';
        }
        $languages = Language::getLanguages(false);
        $actual_lang_id = $this->context->language->id;
        $url_ajax = '';
        if ($this->es17) {
            $url_ajax = $this->context->link->getAdminLink('AdminIdxrcustomproduct', true, array(), array('ajax' => true));
        } else {
            $url_ajax = $this->context->link->getAdminLink('AdminIdxrcustomproduct') . '&ajax=true';
        }

        $this->smarty->assign(array(
            'component' => $component,
            'options' => $component->options_lang,
            'link' => $this->context->link,
            'languages' => $languages,
            'defaultFormLanguage' => $actual_lang_id,
            'images_dir' => _PS_IMG_ . $this->name . DIRECTORY_SEPARATOR,
            'urlAjax' => $url_ajax,
            'default_currency' => Currency::getDefaultCurrency(),
            'impactTax' => Configuration::get(Tools::strtoupper($this->name . '_PRICEIMPACTTAX')),
            'taxes' => Tax::getTaxes($this->context->language->id)
        ));

        return $this->display(__FILE__, 'views/templates/admin/component-form.tpl');
    }

    public function generateProductOptionsSelector($component)
    {
        $subcomponents = Db::getInstance()->executeS('Select * from ' . _DB_PREFIX_ . 'idxrcustomproduct_components comp where parent = ' . (int) $component->id_component);
        $this->smarty->assign(array(
            'component' => $component,
            'subcomponents' => $subcomponents,
            'backurl' => $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&module_name=' . $this->name . '&updatecomponent&id_component=',
            'token' => Tools::getAdminTokenLite('AdminModules')
        ));

        return $this->display(__FILE__, 'views/templates/admin/component-product-selector.tpl');
    }

    public function generateConfigurationForm($component)
    {
        $max_server = str_replace('M', '', ini_get('post_max_size'));
        $max_file = $max_server;
        $this->smarty->assign(array(
            'component' => $component,
            'max_file' => $max_file,
            'max_file_server' => $max_server,
            'file_extensions' => $this->getFileExtensions(),
            'currentIndex' => $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name,
            'token' => Tools::getAdminTokenLite('AdminModules')
        ));
        if ($component->type == 'file') {
            return $this->display(__FILE__, 'views/templates/admin/component-file-form.tpl');
        }
    }

    public function generateProductComponentForm($id_product)
    {
        $product = new Product($id_product);
        $combinations = $product->getAttributeCombinations($this->context->language->id);
        $this->smarty->assign(array(
            'combinations' => $combinations
        ));

        return $this->display(__FILE__, 'views/templates/admin/productcomponent-form.tpl');
    }

    public function getFileExtensions()
    {
        return array(
            'ai' => 'Documento Adobe Ilustrator',
            'aif' => 'Archivo de intercambio de audio',
            'avi' => 'Audio Video Interleave',
            'bat' => 'Archivos por lotes',
            'bin' => 'Archivo binario',
            'bmp' => 'Mapa de bits de Windows',
            'bup' => 'Archivo de copia de seguridad',
            'cab' => 'Microsoft Windows archivo comprimido',
            'cda' => 'Atajo CD Audio Track',
            'cdr' => 'Corel Draw Vector o CD de audio sin procesar',
            'chm' => 'Microsoft HTML Help comprimido',
            'dat' => 'Datos',
            'divx' => 'Vídeo DivX',
            'dll' => 'Biblioteca de vínculos dinámicos',
            'dmg' => 'Imagen de disco',
            'doc' => 'Documento Microsoft Word',
            'docx' => 'Documento Microsoft Word',
            'dwg' => 'AutoCAD DWG',
            'eml' => 'Mensaje de correo electrónico',
            'eps' => 'PostScript encapsulado',
            'exe' => 'Archivo ejecutable',
            'fla' => 'Adobe Flash',
            'flv' => 'Flash Video',
            'gif' => 'Imagen GIF (Graphics Interchange Format)',
            'gz' => 'Gzip archivo empaquetado',
            'hqx' => 'BinHex',
            'htm' => 'Web – Hypertext Markup Language (HTML)',
            'html' => 'Web – Hypertext Markup Language (HTML)',
            'ifo' => 'DVD Información del archivo',
            'indd' => 'Documento de InDesign',
            'iso' => 'Imagen de disco óptico',
            'jar' => 'Archivo Java',
            'jpeg' => 'De imagen JPEG',
            'jpg' => 'Imagen JPEG',
            'lnk' => 'Acceso directo – Atajo',
            'log' => 'Archivo de registro',
            'm4a' => 'MPEG-4 Parte 14',
            'm4b' => 'MPEG-4 Parte 14',
            'm4p' => 'AAC protegido Archivo',
            'm4v' => 'MPEG-4 Parte 14',
            'mdb' => 'Microsoft Access',
            'mid' => 'Audio midi – Musical Instrument Digital Interface',
            'mov' => 'QuickTime',
            'mp2' => 'MPEG-1 Audio Layer II',
            'mp3' => 'Archivo de Audio MP3',
            'mp4' => 'MPEG-4 Parte 14',
            'mpeg' => 'MPEG 1 Sistema Stream',
            'mpg' => 'MPEG-1 Video',
            'msi' => 'Windows Installer',
            'mswmm' => 'Windows Movie Maker Archivo de Proyecto',
            'pdf' => 'Documento PDF',
            'png' => 'Graficos – Portable Network Graphics',
            'pps' => 'Mostrar PowerPoint',
            'ppt' => 'Microsoft PowerPoint Presentation',
            'pptx' => 'Microspft PowerPoint 2007+',
            'ps' => 'PostScript',
            'psd' => 'Photoshop documento',
            'pst' => 'Microsoft Outlook emails – Personal Storage',
            'pub' => 'Microsoft Publisher',
            'qbb' => 'QuickBooks archivo de copia de seguridad',
            'qbw' => 'Hoja de cálculo; QuickBooks para Windows',
            'ram' => 'RealAudio',
            'rar' => 'RAR Archive',
            'rm' => 'RealMedia',
            'rmvb' => 'RealMedia Variable Bitrate',
            'rtf' => 'Formato de texto enriquecido',
            'sql' => 'Lenguaje de consulta estructurado',
            'ss' => 'Gráficos de mapas de bits; Splash',
            'swf' => 'SWF de gráficos vectoriales',
            'tgz' => 'Archivo; WinZipNT – TAR – GNUzip',
            'tif' => 'Tagged Image File Format',
            'torrent' => 'BitTorrent',
            'ttf' => 'Tipos de Fuente – Letra',
            'txt' => 'Archivo de texto plano',
            'vcd' => 'Virtual CD-ROM CD Archivo de imagen',
            'vob' => 'DVD-Video Object',
            'wav' => 'Waveform Audio Formato',
            'wma' => 'Windows Media Audio',
            'wmv' => 'Windows Media Video',
            'wpd' => 'WordPerfect Document',
            'wps' => 'Documento de texto, MS Works',
            'xls' => 'Microsoft Excel hoja de cálculo',
            'xlsx' => 'Microsoft Excel 2007+',
            'zip' => 'Archivo empaquetado',
        );
    }

    public function activeConfiguration($id)
    {
        $toggle_query = 'UPDATE ' . _DB_PREFIX_ . 'idxrcustomproduct_configurations SET active = 1 - active where id_configuration = ' . (int) $id . ';';
        Db::getInstance()->execute($toggle_query);
    }
    /*Edit with team wassim novatis*/
    public function createProduct($product_id, $snaps, $attribute_id, $customization, $extra = false, $quantity = false, $product_weight = 0, $product_volume = 0, $product_width = 0, $product_height = 0, $product_depth = 0, $prix_de_decouper = 0, $price_from_cube = 0 )
    {
        $customproduct = new IdxCustomizedProduct();
        return $customproduct->createInPs($product_id, $snaps, $attribute_id, $customization, $extra, $quantity, $product_weight, $product_volume, $product_width, $product_height, $product_depth, $prix_de_decouper, $price_from_cube );
    }
    /*End*/
    
    public function getCustomCategory($id_product_from)
    {
        $lang_id = $this->context->language->id;
        $id_shop = $this->context->shop->id;
        $category_name = Db::getInstance()->getValue('select b.name from ' . _DB_PREFIX_ . 'product_shop a
            inner join ' . _DB_PREFIX_ . 'category_lang b on a.id_category_default = b.id_category
            where a.id_product = '.(int)$id_product_from.' and a.id_shop = '.(int)$id_shop.' and b.id_shop = '.(int)$id_shop.' and b.id_lang = '.(int)$lang_id);
        $root_category = Category::getRootCategory();
        $root_childrens = Category::getChildren($root_category->id, Context::getContext()->language->id, false, $id_shop);
        $category_id = array_search('CUSTOM '.$category_name, array_column($root_childrens, 'name'));
        
        if (!$category_id) {
            $new_category = new Category();
            $new_category->name = self::createMultiLangField('CUSTOM '.$category_name);
            foreach ($new_category->name as $id_lang => $name) {
                if (empty($new_category->link_rewrite[$id_lang])) {
                    $new_category->link_rewrite[$id_lang] = Tools::link_rewrite($name);
                } elseif (!Validate::isLinkRewrite($new_category->link_rewrite[$id_lang])) {
                    $new_category->link_rewrite[$id_lang] = Tools::link_rewrite($new_category->link_rewrite[$id_lang]);
                }
            }
            $new_category->id_shop_default = $id_shop;
            $new_category->id_parent = $root_category->id;
            $new_category->add();
            Tools::generateRobotsFile();
            $category_id = $new_category->id;
        }
        return $category_id;
    }
    
    /**
     * Return valid multilanguage field with same value for all langages
     * 
     * @param string $field
     * @return array
     */
    protected static function createMultiLangField($field)
    {
        $res = [];
        foreach (Language::getIDs(false) as $id_lang) {
            $res[$id_lang] = $field;
        }

        return $res;
    }
    
    public function checkMpcartordersplit($source_id, $new_id)
    {
        if(!Module::isEnabled('mpcartordersplit')){
            return;
        }
        
        $parent = Db::getInstance()->getValue('Select id_ps_product from '._DB_PREFIX_.'wk_mp_seller_product where id_ps_product = '.(int)$source_id);
        $already_exist = Db::getInstance()->getValue('Select id_ps_product from '._DB_PREFIX_.'wk_mp_seller_product where id_ps_product = '.(int)$new_id);
        
        if (!$parent || $already_exist) {
            return;
        }
        
        $prow = Db::getInstance()->getRow('Select * from '._DB_PREFIX_.'wk_mp_seller_product where id_ps_product = '.(int)$source_id);
        unset($prow['id_mp_product']);
        $prow['id_ps_product'] = (int)$new_id;
        
        Db::getInstance()->insert('wk_mp_seller_product', $prow);
        $new_mpid = Db::getInstance()->Insert_ID();
        
        $pirows = Db::getInstance()->executeS('Select id_product from '._DB_PREFIX_.'wk_mp_seller_product_image where id_product = '.(int)$source_id);
        foreach ($pirows as $pirow) {
            unset($pirow['id_mp_product_image']);
            $pirow['seller_product_id'] = (int)$new_mpid;
            Db::getInstance()->insert('wk_mp_seller_product_image', $pirow);
        }        
        
        $pcrows = Db::getInstance()->executeS('Select id_product from '._DB_PREFIX_.'wk_mp_seller_product_category where id_product = '.(int)$source_id);
        foreach ($pcrows as $pcrow) {
            unset($pcrow['id_mp_category_product']);
            $pcrow['seller_product_id'] = (int)$new_mpid;
            Db::getInstance()->insert('wk_mp_seller_product_category', $pcrow);
        }
        
        $plrows = Db::getInstance()->executeS('Select id_product from '._DB_PREFIX_.'wk_mp_seller_product_lang where id_product = '.(int)$source_id); 
        foreach ($plrows as $plrow) {
            $pcrow['id_mp_product'] = (int)$new_mpid;
            Db::getInstance()->insert('wk_mp_seller_product_lang', $pcrow);
        }
    }

    public function getProductoOriginal($id_producto, $icp_code = false)
    {
        $sql = 'SELECT id_producto FROM ' . _DB_PREFIX_ . $this->name . '_clones WHERE id_clon = ' . (int) $id_producto;
        if ($icp_code) {
            $sql .= ' AND icp_code LIKE "' . pSQL($icp_code) . '"';
        }
        return Db::getInstance()->getValue($sql);
    }
    
    public function duplicateCartInfo($id_from, $id_to)
    {
        $notes = Db::getInstance()->executeS('Select * from '._DB_PREFIX_.'idxrcustomproduct_notes where id_cart = '.(int)$id_from);
        if ($notes) {
            foreach ($notes as $note) {
                unset($note['id_note']);
                $note['id_cart'] = (int) $id_to;
                Db::getInstance()->insert('idxrcustomproduct_notes', $note);
            }
        }
        
        $extras = Db::getInstance()->executeS('Select * from '._DB_PREFIX_.'idxrcustomproduct_customer_extra where id_cart = '.(int)$id_from);
        if ($extras) {
            foreach ($extras as $extra) {
                unset($extra['id_extra']);
                $extra['id_cart'] = (int) $id_to;
                Db::getInstance()->insert('idxrcustomproduct_customer_extra', $extra);
            }
        }
        
        $files = Db::getInstance()->executeS('Select * from '._DB_PREFIX_.'idxrcustomproduct_files where id_cart = '.(int)$id_from);
        if ($files) {
            foreach ($files as $file) {
                unset($file['id_file']);
                $file['id_cart'] = (int) $id_to;
                Db::getInstance()->insert('idxrcustomproduct_files', $file);
            }
        }
    }

    public function productByIcpcode($id_producto, $icp_code)
    {
        $sql = 'SELECT id_clon FROM ' . _DB_PREFIX_ . $this->name . '_clones WHERE id_producto = ' . (int) $id_producto . ' AND icp_code LIKE "' . pSQL($icp_code) . '"';
        $id_product = Db::getInstance()->getValue($sql);
        $already_exist = Db::getInstance()->getValue('Select id_product from ' . _DB_PREFIX_ . 'product where id_product = ' . (int) $id_product);
        if (!$already_exist) {
            Db::getInstance()->delete($this->name . '_clones', 'id_producto = ' . (int) $id_producto . ' AND icp_code LIKE "' . pSQL($icp_code) . '"');
            return false;
        }
        return $id_product;
    }

    public function updateNotes($data)
    {
        foreach ($data as $note) {
            if (isset($note['id_cart'])) {
                $sql_exist = 'Select id_note from ' . _DB_PREFIX_ . 'idxrcustomproduct_notes where id_cart = ' . (int) $note['id_cart'] . ' and id_cart_product = ' . (int) $note['id_product'];
                $id_note = Db::getInstance()->getValue($sql_exist);
                if ($id_note) {
                    $update_data = array(
                        'private_note' => pSQL(urlencode($note['private'])),
                        'public_note' => pSQL(urlencode($note['public']))
                    );
                    $where = 'id_note = ' . (int) $id_note;
                    Db::getInstance()->update('idxrcustomproduct_notes', $update_data, $where);
                } else {
                    $insert_data = array(
                        'id_cart' => (int) $note['id_cart'],
                        'id_cart_product' => (int) $note['id_product'],
                        'private_note' => pSQL(urlencode($note['private'])),
                        'public_note' => pSQL(urlencode($note['public']))
                    );
                    Db::getInstance()->insert('idxrcustomproduct_notes', $insert_data, true);
                }
            }
        }
        if (count($data) && !Configuration::get(Tools::strtoupper($this->name . '_BREAKDOWNBLOCK'))) {
            $id_cart = $data[0]['id_cart'];
            //Delete the notes that isn't in the data
            $all_notes = 'Select id_note, id_cart, id_cart_product from ' . _DB_PREFIX_ . 'idxrcustomproduct_notes where id_cart = ' . (int) $id_cart;
            $result = Db::getInstance()->executeS($all_notes);
            foreach ($result as $old_note) {
                $exist = false;
                foreach ($data as $new_note) {
                    if ($new_note['id_product'] == $old_note['id_cart_product']) {
                        $exist = true;
                    }
                }
                if (!$exist) {
                    Db::getInstance()->delete('idxrcustomproduct_notes', 'id_cart = ' . (int) $id_cart . ' and id_cart_product = ' . (int) $old_note['id_cart_product']);
                }
            }
        }
    }

    public function getNotesByOrder($id_order)
    {
        //multiple orders
        $id_orders_array = Db::getInstance()->executeS(
                'select id_order from '._DB_PREFIX_.'orders where reference in '
                . '(select reference from '._DB_PREFIX_.'orders where id_order = '.(int) $id_order.');');
        if (!$id_orders_array) {
            return false;
        }
        
        $id_orders = array();
        foreach ($id_orders_array as $id) {
            $id_orders[] = (int)$id['id_order'];
        }
        
        $sql = 'Select * from '._DB_PREFIX_.'idxrcustomproduct_notes where id_order IN ('. implode(',', $id_orders).')';
        if ($result = Db::getInstance()->executeS($sql)) {
            $productos_pedido = array();
            if ($listadoProductos = OrderDetail::getList($id_order)) {
                foreach ($listadoProductos as $producto) {
                    $productos_pedido[] = $producto['product_id'];
                }
            }
            foreach ($result as $index => $nota) {
                if (!in_array($nota['id_cart_product'], $productos_pedido)) {
                    unset($result[$index]);
                }
            }
            return $result;
        }
    }

    // this for admin display
    public function getPanelForOrder($id_order, $front = false)
    {
        $notes = $this->getNotesByOrder($id_order);
        if ($notes) {
            if ($front) {
                foreach ($notes as &$note) {
                    $notes_in_p = urldecode($note['public_note']);
                    $notes_array = explode('</p>', str_replace('<p>', '', $notes_in_p));
                    foreach ($notes_array as $key => &$line) {                        
                        if (strpos($line, '<a')) {
                            $line = $this->formatFileLink($line);
                        }
                        if (strpos($line, '<hr')) {
                            $line = $this->formatMultiText($line);
                        }
                        if (!$line) {
                            unset($notes_array[$key]);
                        }
                        if ((stripos($line, 'Console') !== false) || (stripos($line, '') !== false)) {
                            $line = '';
                        }
                    }
                    $note['notes_a'] = $notes_array;
                    /*Edit with team wassim novatis*/
                     $product_id = $note['id_cart_product'];
                     $note['product_volume'] = $this->getProductVolume($product_id);
                     $note['product_depth'] = $this->getProductDepth($product_id);
                     $note['product_width'] = $this->getProductWidth($product_id);
                     $note['product_height'] = $this->getProductHeight($product_id);
                     /*End */
                }
                $front_token = Configuration::get(Tools::strtoupper($this->name .'_TOKEN'));
                $file_controller = $this->context->link->getModuleLink($this->name, 'file', array('token' => $front_token,'ajax' => true)).'&key=';
                $this->smarty->assign(
                    array(
                        'notes' => $notes,
                        'file_controller' => $file_controller
                    )
                );
                return $this->display(__FILE__, 'views/templates/front/order-notes.tpl');
            } else {
                foreach ($notes as $key => $note) {
                    $notes_in_p = urldecode($note['public_note']);
                    $product_id = $note['id_cart_product'];
                    $note['product_volume'] = $this->getProductVolume($product_id);
                    $note['product_depth'] = $this->getProductDepth($product_id);
                    $note['product_width'] = $this->getProductWidth($product_id);
                    $note['product_height'] = $this->getProductHeight($product_id);

                    // Retrieve the product name for the current note
                    $note['product_name'] = Product::getProductName($product_id);
                
                    // Process and format HTML content
                    $notes_array = explode('</p>', str_replace('<p>', '', $notes_in_p));
                
                    foreach ($notes_array as $line_key => &$line) {
                        // Remove any extra white space and unwanted characters
                        $line = trim($line);
                        if (empty($line)) {
                            unset($notes_array[$line_key]);
                            continue;
                        }
                
                        // Check for images and replace local path with URL
                        if (strpos($line, '<img') !== false) {
                            // Define the base URL and local path
                            $baseURL = 'https://www.plexi-cindar.com';
                            $localPath = '/var/www/vhosts/plexi-cindar.com/httpdocs';
                
                            // Regular expression to replace src attribute
                            $pattern = '/(src=")' . preg_quote($localPath, '/') . '\/(img\/idxrcustomproduct\/uploads\/\d{4}\/\d{2}\/[^"]+)"/i';
                            $replacement = '$1' . $baseURL . '/$2"';
                            $line = preg_replace($pattern, $replacement, $line);
                        }
                
                        // Enhance visibility of certain parts
                        $colonPos = strpos($line, ':');
                        if ($colonPos !== false) {
                            $line = '<strong>' . substr($line, 0, $colonPos) . '</strong>' . substr($line, $colonPos);
                        }
                    }
                
                    // Reassemble the note after processing
                    $note['notes_a'] = implode('</p><p>', $notes_array) . '</p>';
                
                    // Update the original notes array with the modified note
                    $notes[$key] = $note;
                
                    /* Additional product-related properties, if needed */
                    $note['product_volume'] = $this->getProductVolume($product_id);
                    $note['product_depth'] = $this->getProductDepth($product_id);
                    $note['product_width'] = $this->getProductWidth($product_id);
                    $note['product_height'] = $this->getProductHeight($product_id);
                }
                $this->smarty->assign(array('notes' => $notes));
                return $this->display(__FILE__, 'views/templates/admin/order-notes.tpl');
            }
        }
    }
 
    // this for pdf display
    public function getPanelForOrder2($id_order)
    {
        $notes = $this->getNotesByOrder($id_order);
        if ($notes) {
            foreach ($notes as &$note) {
                $note['product_name'] = Product::getProductName($note['id_cart_product']);
                $thickness = number_format((float) $this->getProductDepth($note['id_cart_product']), 2, '.', '');
                
                $notes_in_p = urldecode($note['public_note']);
                $notes_array = explode('</p>', str_replace('<p>', '', $notes_in_p));
                $notes_array_out = array();
                $thickness_inserted = false;
    
                // Initialize the preview image variable
                $preview_img = '';
    
                // Process each line of the notes
                foreach ($notes_array as &$line) {
                    if (!$thickness_inserted && $thickness !== null && $thickness !== '' && stripos($line, 'Aperçu en SVG') !== false) {
                        $notes_array_out[] = '<strong>Epaisseur: </strong> ' . $thickness;
                        $thickness_inserted = true;
                    }
                    if (stripos($line, 'Console') !== false) {
                        $line = '';
                    } else {
                            // $preview_img = $line1;
                            // $line = '';
                            // $line1 = str_replace('Aperçu:', '', $line);
                            // $line1 = str_replace('300px', '500px', $line1);
                            if (stripos($line, 'Aperçu en SVG') !== false) {
                                // Modify the link by pointing to a `download.php` script
                                $line = preg_replace_callback('/<a\s+([^>]+)href="([^"]+)">([^<]+)<\/a>/', function ($matches) {
                                    // Extract the original href (URL of the SVG)
                                    $href = $matches[2];
                            
                                    // Construct the new href which points to the download.php with the file parameter
                                    $new_href = 'https://www.plexi-cindar.com/modules/idxrcustomproduct/controllers/front/download.php?file=' . urlencode($href);
                            
                                    // Return the modified <a> tag with the new href and the download attribute
                                    return '<a ' . $matches[1] . 'href="' . $new_href . '" download>' . $matches[3] . '</a>';
                                }, $line);
                            } else if ((stripos($line, 'Aperçu') !== false) || (stripos($line, 'L\'épaisseur') !== false) || (stripos($line, 'La couleur') !== false)) {
                                $line='';
                            }else{
                            $colonPos = strpos($line, ':');
                            if ($colonPos !== false) {
                                $firstPart = substr($line, 0, $colonPos);
                                $firstPart = '<strong>' . $firstPart . '</strong>';
                                $line = $firstPart . substr($line, $colonPos);
                            }
                        }
                    }
                    if ($line !== '') {
                        $notes_array_out[] = $line;
                    }
                }

                if (!$thickness_inserted && $thickness !== null && $thickness !== '') {
                    $notes_array_out[] = '<strong>Epaisseur: </strong> ' . $thickness;
                }

                $note['notes_a'] = $notes_array_out;
                $note['preview_img'] = $preview_img;
            }
            $this->context->smarty->assignGlobal('idxrCustomProductNotes', $notes);
        }
    }
    
    
    public function updateCartExtra($cart)
    {
        $clean_sql = 'delete from '._DB_PREFIX_.'idxrcustomproduct_customer_extra 
            where id_cart = '.(int)$cart->id.' and id_product not in 
            (select distinct id_product from '._DB_PREFIX_.'cart_product where id_cart = '.(int)$cart->id.');';
        Db::getInstance()->execute($clean_sql);

        $files_to_remove_sql = 'select target_name from '._DB_PREFIX_.'idxrcustomproduct_files 
            where id_cart = '.(int)$cart->id.' and id_product not in 
            (select distinct id_product from '._DB_PREFIX_.'cart_product where id_cart = '.(int)$cart->id.')';
        
        $files_to_remove = Db::getInstance()->executeS($files_to_remove_sql);
        if ($files_to_remove) {
            foreach($files_to_remove as $file) {
                $path = _PS_ROOT_DIR_ . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR . $file['target_name'];
                if (unlink($path)){
                    Db::getInstance()->delete('idxrcustomproduct_files','target_name = "'.pSQL($file['target_name']).'"');
                }
            }
        }
    }

    private function formatFileLink($link)
    {
        $start = strpos($link, '<a');
        $end = strpos($link, '/a>', $start);
        $link_old = substr($link, $start, $end+3);

        $start_key = strpos($link_old, 'key=');
        $end_key = strpos($link_old, '"', $start_key+4);
        $key = substr($link_old, $start_key+4, $end_key-($start_key+4));

        $start_title = strpos($link_old, '>');
        $end_title = strpos($link_old, '<', $start+1);
        $title = substr($link_old, $start_title+1, $end_title-($start_title+1));

        $link_array = array(
            'title' => str_replace($link_old, '', $link),
            'file_key' => $key,
            'file_name' => $title
         );
        return $link_array;
    }
    
    private function formatMultiText($line)
    {
        $title_end = strpos($line, ':');
        $title = substr($line, 0, $title_end);
        $new_line = substr($line,$title_end+1);
        $texts = explode('<hr>', $new_line);
        $line_array = array(
            'title' => $title,
            'texts' => array_map('trim',$texts)
        );
        return $line_array;
    }

    public function getToppriceBlock($id_product)
    {
        //$product_price = $this->formatPrice(Product::getPriceStatic($id_product), _PS_PRICE_COMPUTE_PRECISION_);
        $product_price = $this->formatPrice(IdxCustomizedProduct::getMinPrice($id_product));
        $this->smarty->assign(array('product_price' => $product_price));
        if ($this->es17) {
            return $this->display(__FILE__, 'views/templates/front/17/topBlock.tpl');
        } else {
            return $this->display(__FILE__, 'views/templates/front/16/topBlock.tpl');
        }
    }

    public function getExtraByContext($clean = false, $id_cart = false)
    {
        if (!$id_cart) {
            $id_cart = (int) $this->context->cart->id;
        }
        $cart = new Cart($id_cart);
        $extra_options = $this->getExtraByCart($id_cart);
        $products = $cart->getProducts();
        $extra_info = array();
        $front_token = Configuration::get(Tools::strtoupper($this->name .'_TOKEN'));
        $file_controller = $this->context->link->getModuleLink($this->name, 'file', array('token' => $front_token,'ajax' => true)).'&key=';
        foreach ($products as $product) {
            if ($product['id_category_default'] == Configuration::get(Tools::strtoupper($this->name . '_CATEGORY'))) {
                $data = array();
                $data['id_product'] = $product['id_product'];
                //var_dump($product);
                /*Add with team wassim novatis*/
                $data['product_weight'] = $this->getProductWeight($product['id_product']); 
                $data['product_volume'] = $this->getProductVolume($product['id_product']);
                $data['product_width'] = $this->getProductWidth($product['id_product']);
                $data['product_height'] = $this->getProductHeight($product['id_product']);
                $data['product_depth'] = $this->getProductDepth($product['id_product']);
                $quantity = $product['quantity'];
                /*End*/ 
                $product_img = Product::getCover($product['id_product']);
                $data['product_image_id'] = $product_img?$product_img['id_image']:false;
                $data['product_image_url'] = $this->context->link->getImageLink($product['link_rewrite'], $data['product_image_id'], ImageType::getFormattedName('home'));
                $data['original_product'] = $this->getProductoOriginal($product['id_product']);
                $parent_product = new Product($data['original_product']);
                $data['original_url'] = $this->context->link->getProductLink($parent_product);
                if ($clean) {
                    $data['customization'] = '<table class=\"table-config\">';
                } else {
                    $data['customization'] = '<table class=\"tabla-resumen table table-bordered\">';
                }
                $data['customization'] .= str_replace(array('<p>', ':', '</p>'), array('<tr><td>', ':</td><td>', '</td></tr>'), $product['description_short']);
                $data['extra_info'] = false;
                $extraTitlesLog = __DIR__ . '/extra_titles.log';
                if ((file_exists($extraTitlesLog) && is_writable($extraTitlesLog)) || (!file_exists($extraTitlesLog) && is_writable(__DIR__))) {
                    @file_put_contents($extraTitlesLog, "[" . date('Y-m-d H:i:s') . "] customization: " . $data['customization'] . "\n", FILE_APPEND);
                }
                // Remove rows with "La couleur" or "L'épaisseur" titles (case-sensitive match)
                $data['customization'] = preg_replace(
                    '#<tr><td>La couleur:</td><td>.*?</td></tr>#',
                    '',
                    $data['customization']
                );

                $data['customization'] = preg_replace(
                    "#<tr><td>L'épaisseur:</td><td>.*?</td></tr>#",
                    '',
                    $data['customization']
                );

                $inputIds = [
                    '3', '23', '4', '9', '10', '11',
                    '57', '58', '53', '54', '55', '56',
                    '18', '25', '26', '27',
                    '38', '64', '63', '39', '65', '40', '41', '42',
                    '75', '45', '66', '67', '68', '69', '118'
                ];

                foreach ($extra_options as $extra) {
                    if ($extra['id_product'] == $product['id_product']) {
                        // Skip displaying color and thickness if title is exactly those values

                        if ($extra['title'] === "L'épaisseur" || $extra['title'] === 'La couleur') {
                            continue;
                        }
                
                        $data['extra_info'] = true;
                
                        if ($extra['original_name']) {
                            $data['customization'] .= '<tr><td>' . $extra['title'] . ':</td><td>' .
                                (isset($extra['qty']) ? $extra['qty'] . ' ' : '') .
                                '<a href="' . $file_controller . $extra['target_name'] . '" target="_blank">' .
                                $extra['original_name'] . '</a></td></tr>';
                        } else {
                            if (strpos($extra['extra'], ',')) {
                                $extra_multiple = array_map('trim', explode(',', $extra['extra']));
                                $extra['extra'] = '<span class="idxcp_extratext">' . implode('</span><br/><span class="idxcp_extratext">', $extra_multiple) . "</span>";
                            }
                
                            if (in_array($extra['id_component'], $inputIds)) {
                                $data['customization'] .= '<tr><td>' . $extra['title'] . ':</td><td>' . $extra['extra'] . ' mm</td></tr>';
                            } else {
                                $data['customization'] .= '<tr><td>' . $extra['title'] . ':</td><td>' . $extra['extra'] . '</td></tr>';
                            }
                        }
                    }
                }
                
                     /*Add with team wassim novatis*/
                    //$data['customization'] .= '<tr><td>Volume:</td><td id="volume"><span id="product_volume_value">' . number_format($data['product_volume'], 5) . ' m3</span></td></tr>'; 
                    //$data['customization'] .= '<tr><td>Largeur:</td><td id="width"><span id="product_width_value">' . (float)$data['product_width'] . ' mm</span></td></tr>';
                    if ((float)$data['product_depth'] != 0) {
                        $data['customization'] .= '<tr><td>Épaisseure:</td><td id="depth"><span id="product_depth_value">' . (float)$data['product_depth'] . ' mm</span></td></tr>';
                    } 
                if ($quantity > 1) {
                    $totalWeight = number_format((float)($data['product_weight'] * $quantity), 2, '.', '');
                    $totalVolume = number_format((float)($data['product_volume'] * $quantity), 4, '.', '');
                    $data['customization'] .= '<tr><td>Volume:</td><td id="volume"><span id="product_volume_value">' . number_format($totalVolume, 4) .  ' m³</span></td></tr>'; 
                    $data['customization'] .= '<tr><td>Poids:</td><td id="poids"><span id="product_weight_value">' . number_format($totalWeight, 2) . ' kg</span></td></tr>';
                } else {
                    $totalVolume = number_format((float)($data['product_volume']), 4, '.', '');
                    $data['customization'] .= '<tr><td>Volume:</td><td id="volume"><span id="product_volume_value">' . number_format($totalVolume, 4) . ' m³</span></td></tr>'; 
                    $data['customization'] .= '<tr><td>Poids:</td><td id="poids"><span id="product_weight_value">' . number_format($data['product_weight'], 2) . ' kg</span></td></tr>';
                }
               // $data['customization'] .= '<tr><td>Prix:</td><td id="height"><span id="product_prix_value">' . (float)$product['total_wt'] * ((float)$data['product_volume'] / 0.003) . '</span></td></tr>';
                /*End*/
                $data['customization'] .= '</table>';
                $edit_url = $this->context->link->getProductLink($parent_product,null,null,null,null,null,null,false,false,false,array('icp_edit' => $product['id_product']));
                $this->smarty->assign(array('edit_link' => $edit_url));
                $data['edit_button'] = $this->display(__FILE__, 'views/templates/hook/editbutton.tpl');
                $extra_info[] = $data;
            }
        }
        return $extra_info;
    }

    public function getExtraArray($id_cart)
    {
        if (is_object($id_cart)) {
            $cart = $id_cart;
        } else {
            $cart = new Cart((int) $id_cart);
        }
        $extra_options = $this->getExtraByCart((int) $cart->id);
        $products = $cart->getProducts();
        $extra_info = array();
        $file_controller = $this->context->link->getModuleLink($this->name, 'file').'&key=';
        foreach ($products as $product) {
            if ($product['id_category_default'] == Configuration::get(Tools::strtoupper($this->name . '_CATEGORY'))) {
                $data = array();
                $data['product'] = $product;
                foreach ($extra_options as $extra) {
                    if ($extra['id_product'] == $product['id_product']) {
                        $data['extra_options'] = $extra;
                    }
                }
                $extra_info[] = $data;
            }
        }
        return $extra_info;
    }

    public function getExtraByCart($id_cart, $id_product = false)
    {
        $query = 'Select ext.*, COALESCE(comp.title, CONCAT("Component ", ext.id_component)) as title, files.original_name, files.target_name from ' . _DB_PREFIX_ . 'idxrcustomproduct_customer_extra ext '
                . 'left join ' . _DB_PREFIX_ . 'idxrcustomproduct_components_lang comp on ext.id_component = comp.id_component and comp.id_lang = ' . (int) $this->context->language->id . ' '
                . 'left join ' . _DB_PREFIX_ . 'idxrcustomproduct_files files on files.id_component = ext.id_component and files.id_cart = ext.id_cart and ext.extra  like concat("%",files.original_name,"%") '
                . 'where ext.id_cart = ' . (int) $id_cart;
        if ($id_product) {
            $query .= ' and ext.id_product = ' . (int) $id_product;
        }
        
        $result = Db::getInstance()->executeS($query);
        
        if ($result) {
            foreach ($result as &$extra) {
                if (strpos($extra['extra'], '§') !== false) {
                    $extra['extra'] = str_replace('§', '<hr>', $extra['extra']);
                }
                if ($extra['original_name']) {
                    $extra['qty'] = $this->getQtyFromExtra($extra['extra'], $extra['original_name']);
                }
            }
        }

        return $result;
    }
    
    public function getQtyFromExtra($text, $original)
    {
        $qty = false;
        $partes = explode('<hr>',$text);
        foreach ($partes as $parte) {
            if (substr_count($parte, $original)) {
                $quantity = trim(str_replace($original, '', $parte));
                if ($quantity) {
                    $qty = $quantity;
                    break;
                }
            }
        }
        
        return $qty;
    }

    public function getExtraByFav($id_fav)
    {
        $query = 'Select ext.*, comp.title from ' . _DB_PREFIX_ . 'idxrcustomproduct_customer_extra ext '
                . 'inner join ' . _DB_PREFIX_ . 'idxrcustomproduct_components_lang comp on ext.id_component = comp.id_component '
                . 'where id_fav = ' . (int) $id_fav . ' and comp.id_lang = ' . (int) $this->context->language->id;
        return Db::getInstance()->executeS($query);
    }

    public function saveFavorite($product_id, $attribute_id, $customization, $extra_values)
    {
        $customer_id = $this->context->customer->id;
        $icp_code = '';
        $icp_sep = '';
        $add_shortdesc = '';
        foreach ($customization as &$option) {
            $option_arr = explode('_', $option);
            $option = array();
            $option['id_component'] = $option_arr[0];
            $option['id_option'] = explode('&amp;',$option_arr[1]);
            $icp_code .= $icp_sep . $option['id_component'] . '-' . $option_arr[1];
            $icp_sep = ',';
            $options = $this->getComponentOptions($option['id_component']);
            if ($options['type'] != 'textarea' && $options['type'] != 'text') {
                foreach ($options['lang'][$this->context->language->id]->options as $item) {
                    foreach ($option['id_option'] as $selected_id) {
                        $qty = '';
                        if (substr_count($selected_id, 'x') !== false) {
                            $parts  = explode('x', $selected_id);
                            isset($parts[1])?$qty = $parts[1].'x ':$qty='';
                            $selected_id = $parts[0];
                        }
                        if ($item->id == $selected_id) {
                            $add_shortdesc .= '<p>' . $this->getComponentName($option['id_component'], true, $this->context->language->id) . ': ' . $qty . $item->name . '</p>';
                            break;
                        }
                    }
                }
            }
        }

        $exist_sql = 'Select id_fav from ' . _DB_PREFIX_ . 'idxrcustomproduct_customer_fav where id_customer = ' . (int) $customer_id . ' and id_product = ' . (int) $product_id . ' and icp_code = "' . pSQL($icp_code) . '"';
        if (Db::getInstance()->getValue($exist_sql)) {
            $this->addExtraFavorite($exist_sql, $product_id, $extra_values);
            return 2;
        } else {
            $insert_data = array(
                'id_customer' => (int) $customer_id,
                'id_product' => (int) $product_id,
                'icp_code' => pSQL($icp_code),
                'description' => $add_shortdesc
            );
            Db::getInstance()->insert('idxrcustomproduct_customer_fav', $insert_data);
            $fav_id = Db::getInstance()->Insert_ID();
            $this->addExtraFavorite($fav_id, $product_id, $extra_values);
            return 1;
        }
    }

    public function addExtraFavorite($fav_id, $product_id, $extra_values)
    {
        foreach ($extra_values as $opt_extra) {
            $extra = explode('_', $opt_extra);
            $extra[1] = json_decode($extra[1]);
            if ($extra[1] == "false") {
                continue;
            } else {
                $data = array(
                    'id_component' => (int) $extra[0],
                    'extra' => pSQL($extra[1]),
                    'id_fav' => (int) $fav_id,
                    'id_product' => (int) $product_id,
                );
                $exist_q = 'Select id_extra from ' . _DB_PREFIX_ . 'idxrcustomproduct_customer_extra where id_component = ' . (int) $extra[0] . ' and id_fav = ' . (int) $fav_id . ' and id_product = ' . (int) $product_id;
                $id_extra = Db::getInstance()->getValue($exist_q);
                $extra_ids = array();
                if ($id_extra) {
                    $extra_ids[] = $id_extra;
                    Db::getInstance()->update('idxrcustomproduct_customer_extra', $data, 'id_extra = ' . (int) $id_extra);
                } else {
                    Db::getInstance()->insert('idxrcustomproduct_customer_extra', $data);
                    $extra_ids[] = Db::getInstance()->Insert_ID();
                }
            }
        }
    }

    public function delFavorite($favorite_id)
    {
        $customer_id = $this->context->customer->id;
        $del_sql = 'Delete from ' . _DB_PREFIX_ . 'idxrcustomproduct_customer_fav where id_customer = ' . (int) $customer_id . ' and id_fav = ' . (int) $favorite_id . ';';
        Db::getInstance()->execute($del_sql);
        $del_extra_sql = 'Delete from ' . _DB_PREFIX_ . 'idxrcustomproduct_customer_extra where id_fav = ' . (int) $favorite_id . ';';
        return Db::getInstance()->execute($del_extra_sql);
    }

    public function setQty($qty_diff, $id_product, $id_product_attribute = null, $id_shop = null)
    {
        if (is_int($qty_diff) && $qty_diff != 0) {
            $product = new Product($id_product);
            $qty = 0;
            if ($product->available_for_order && Configuration::get('PS_STOCK_MANAGEMENT')) {
                if ($product->advanced_stock_management && !$this->es17) {
                    $employeer_q = 'select id_employee from ' . _DB_PREFIX_ . 'employee where active = 1 and id_profile = ' . _PS_ADMIN_PROFILE_;
                    $employe_id = Db::getInstance()->getValue($employeer_q);
                    $employee = new Employee($employe_id);
                    if (!isset($this->context->employee) || !$this->context->employee) {
                        $this->context->employee = $employee;
                    }
                    $warehouses = Warehouse::getWarehousesByProductId($id_product, $id_product_attribute);
                    if (!empty($warehouses)) {
                        foreach ($warehouses as $warehouse) {
                            $WarehouseStock = new Warehouse(array_shift($warehouses)['id_warehouse']);
                        }
                    } else {
                        $query = new DbQuery();
                        $query->select('DISTINCT w.id_warehouse, CONCAT(w.reference, " - ", w.name) as name');
                        $query->from('warehouse', 'w');
                        $query->leftJoin('warehouse_product_location', 'wpl', 'wpl.id_warehouse = w.id_warehouse');
                        $query->limit(1);
                        $warehouses = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
                        $WarehouseStock = new Warehouse(array_shift($warehouses)['id_warehouse']);
                    }
                    $stockmanager = StockManagerFactory::getManager();
                    $price = Product::getPriceStatic($id_product, false, $id_product_attribute);

                    if ($qty_diff > 0) {
                        $stockmanager->addProduct($id_product, (int) $id_product_attribute, $WarehouseStock, $qty_diff, 'tracking', $price, true, null, $employee);
                        StockAvailable::synchronize($id_product);
                        $location = Warehouse::getProductLocation($id_product, (int) $id_product_attribute, $WarehouseStock->id);
                        if (!$location) {
                            Warehouse::setProductLocation($id_product, (int) $id_product_attribute, $WarehouseStock->id, null);
                        }
                    } else {
                        $negqtydiff = $qty_diff * -1;
                        $stockmanager->removeProduct($id_product, (int) $id_product_attribute, $WarehouseStock, $negqtydiff, 'tracking', true, null, 0, $employee);
                        StockAvailable::synchronize($id_product);
                    }
                } else {
                    StockAvailable::updateQuantity($id_product, $id_product_attribute, $qty_diff);
                    StockAvailable::synchronize($id_product);
                }
            }
            Hook::exec('actionProductUpdate', array('id_product' => (int) $id_product, 'product' => $product));
            return true;
        } else {
            //If is not int
            return false;
        }
    }

    public function createCartRule($discount_name, $id_product, $cart, $amount, $type)
    {
        $cartrule_code = $discount_name;
        $expiration = 30;
        $cart_rule_exist = CartRule::getCartsRuleByCode($cartrule_code, $this->context->language->id);
        if (!$cart_rule_exist) {
            $cartrule = new CartRule();
            $cartrule->code = $cartrule_code;
            $name = array();
            foreach (Language::getLanguages(true, false, true) as $language_id) {
                $name[$language_id] = $this->l('Custom product discount ');
            }
            $cartrule->name = $name;
            $cartrule->date_from = date('Y-m-d 00:00:00');
            $cartrule->date_to = date('Y-m-d 00:00:00', strtotime("+" . $expiration . " day"));
            $cartrule->partial_use = 0;
            $cartrule->minimum_amount_currency = 1;
            if ($type == 'percentage') {
                $cartrule->reduction_percent = $amount;
            } else {
                $cartrule->reduction_amount = $amount;
            }
            $cartrule->reduction_product = 0;
            if ($id_product) {
                $cartrule->product_restriction = 1;
            } else {                
                $cartrule->product_restriction = 0;
            }
            $cartrule->reduction_tax = 1;
            $cartrule->reduction_currency = $this->context->cart->id_currency;
            $cartrule->active = 1;
            if ($cartrule->add()) {
                if ($id_product) {
                    $this->createCartRuleProduct($id_product, $cartrule->id);
                }
                return $cartrule->id;
            }
        } else {
            $cartrule = new CartRule($cart_rule_exist[0]['id_cart_rule']);
            $cartrule->date_from = date('Y-m-d 00:00:00');
            $cartrule->date_to = date('Y-m-d 00:00:00', strtotime("+" . $expiration . " day"));
            if ($type == 'percentage') {
                $cartrule->reduction_percent = $amount;
                $cartrule->reduction_amount = 0;
            } else {
                $cartrule->reduction_amount = $amount;
                $cartrule->reduction_percent = 0;
            }
            $cartrule->reduction_product = 0;
            $cartrule->reduction_tax = 1;
            $cartrule->quantity++;
            $cartrule->update();
            return $cartrule->id;
        }
    }

    public function createCartRuleProduct($id_product, $id_cart_rule)
    {
        Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'cart_rule_product_rule_group` (`id_cart_rule`, `quantity`)
                        VALUES (' . (int) $id_cart_rule . ', 1)');
        $id_product_rule_group = Db::getInstance()->Insert_ID();
        Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'cart_rule_product_rule` (`id_product_rule_group`, `type`)
                        VALUES (' . (int) $id_product_rule_group . ', "products")');
        $id_product_rule = Db::getInstance()->Insert_ID();
        Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'cart_rule_product_rule_value` (`id_product_rule`, `id_item`) VALUES (' . (int) $id_product_rule . ', ' . (int) $id_product . ')');
    }

    public function createSpecificPrice($id_product, $cart, $amount, $conf)
    {
        if ($conf->discount_type == 'percentage') {
            $product_price = Product::getPriceStatic($id_product);
            $discount_amount = $product_price * ($conf->discount_amount / 100);
        }
        if (!SpecificPrice::exists($id_product, 0, $this->context->shop->id, 0, 0, $cart->id_currency, $cart->id_customer, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00')) {
            $specificPrice = new SpecificPrice();
            $specificPrice->id_product = (int) $id_product;
            $specificPrice->id_product_attribute = 0;
            $specificPrice->id_shop = (int) $this->context->shop->id;
            $specificPrice->id_currency = (int) $cart->id_currency;
            $specificPrice->id_customer = (int) $cart->id_customer;
            $specificPrice->id_group = 0;
            $specificPrice->id_country = 0;
            $specificPrice->price = (float) (-1);
            $specificPrice->from_quantity = 1;
            $specificPrice->reduction = (float) ($amount);
            $specificPrice->reduction_tax = false;
            $specificPrice->reduction_type = 'amount';
            $specificPrice->from = '0000-00-00 00:00:00';
            $specificPrice->to = '0000-00-00 00:00:00';
            return $specificPrice->add();
        }
    }

    public static function getImagesTypes()
    {
        $types = array();
        $types[] = Configuration::get(Tools::strtoupper('IDXRCUSTOMPRODUCT_PCIMGTYPE'));
        $types[] = Configuration::get(Tools::strtoupper('IDXRCUSTOMPRODUCT_MIMGTYPE'));
        $types[] = Configuration::get(Tools::strtoupper('IDXRCUSTOMPRODUCT_TIMGTYPE'));
        return array_unique($types);
    }

    public static function formatPrice($price)
    {
        if (version_compare(_PS_VERSION_, '1.7.6.0', '>')) {
            if (!is_numeric($price)) {
                return $price;
            }
            $context = Context::getContext();
            $currency = $context->currency;
            if (is_int($currency)) {
                $currency = Currency::getCurrencyInstance($currency);
            }
            $locale = $context->getCurrentLocale();
            $currencyCode = is_array($currency) ? $currency['iso_code'] : $currency->iso_code;
            return $locale->formatPrice($price, $currencyCode);
        } else {
            return Tools::displayPrice($price);
        }
    }

    //ps 1.6.0
    public static function getAllCategoriesName(
        $root_category = null,
        $id_lang = false,
        $active = true,
        $groups = null,
        $use_shop_restriction = true,
        $sql_filter = '',
        $sql_sort = '',
        $sql_limit = ''
    ) {
        if (isset($root_category) && !Validate::isInt($root_category)) {
            die(Tools::displayError());
        }

        if (!Validate::isBool($active)) {
            die(Tools::displayError());
        }

        if (isset($groups) && Group::isFeatureActive() && !is_array($groups)) {
            $groups = (array) $groups;
        }

        $cache_id = 'Category::getAllCategoriesName_' . md5((int) $root_category . (int) $id_lang . (int) $active . (int) $use_shop_restriction
                        . (isset($groups) && Group::isFeatureActive() ? implode('', $groups) : ''));

        if (!Cache::isStored($cache_id)) {
            $result = Db::getInstance()->executeS('SELECT c.id_category, cl.name
				FROM `' . _DB_PREFIX_ . 'category` c
				' . ($use_shop_restriction ? Shop::addSqlAssociation('category', 'c') : '') . '
				LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON c.`id_category` = cl.`id_category`' . Shop::addSqlRestrictionOnLang('cl') . '
				' . (isset($groups) && Group::isFeatureActive() ? 'LEFT JOIN `' . _DB_PREFIX_ . 'category_group` cg ON c.`id_category` = cg.`id_category`' : '') . '
				' . (isset($root_category) ? 'RIGHT JOIN `' . _DB_PREFIX_ . 'category` c2 ON c2.`id_category` = ' . (int) $root_category . ' AND c.`nleft` >= c2.`nleft` AND c.`nright` <= c2.`nright`' : '') . '
				WHERE 1 ' . $sql_filter . ' ' . ($id_lang ? 'AND `id_lang` = ' . (int) $id_lang : '') . '
				' . ($active ? ' AND c.`active` = 1' : '') . '
				' . (isset($groups) && Group::isFeatureActive() ? ' AND cg.`id_group` IN (' . implode(',', $groups) . ')' : '') . '
				' . (!$id_lang || (isset($groups) && Group::isFeatureActive()) ? ' GROUP BY c.`id_category`' : '') . '
				' . ($sql_sort != '' ? $sql_sort : ' ORDER BY c.`level_depth` ASC') . '
				' . ($sql_sort == '' && $use_shop_restriction ? ', category_shop.`position` ASC' : '') . '
				' . ($sql_limit != '' ? $sql_limit : '')
            );

            Cache::store($cache_id, $result);
        } else {
            $result = Cache::retrieve($cache_id);
        }

        return $result;
    }

    public function setInnovaTabs()
    {
        $isoLinks = InnovaTools_2_0_0::getIsoLinks($this);
        $default_cat = (Configuration::get(Tools::strtoupper($this->name . '_CATEGORY')) || Tools::getValue('customizable_category'));
        $locked = true;
        $current_cat = 'helpGenerateForm';
        if ($default_cat) {
            $current_cat = 'renderConfigurationList';
            $locked = false;
        }

        if ((Tools::isSubmit('editComponent') && !Tools::isSubmit('editComponentStay'))
            || Tools::isSubmit('deleteidxrcustomproduct_components')
            || Tools::isSubmit('deletecomponent')
            || Tools::isSubmit('submitFilteridxrcustomproduct_components')
            || Tools::isSubmit('submitFiltercomponent')
            || Tools::isSubmit('submitResetcomponent')
            || Tools::isSubmit('componentOrderby')
        ) {
            $current_cat = 'renderComponentList';
        }

        if (Tools::isSubmit('updatecomponent') || Tools::isSubmit('editComponentStay') || Tools::isSubmit('submitComponentStay') || Tools::isSubmit('submitCloneComponent') || Tools::isSubmit('submitGenerateComponent')) {
            $current_cat = 'renderEditComponent';
        }

        if (Tools::isSubmit('updateconfiguration') || Tools::isSubmit('submitConfigurationStay') || Tools::isSubmit('updateidxrcustomproduct_configurations')) {
            $current_cat = 'renderFormAddConfiguration';
        }

        $this->innovatabs = array();

        $this->innovatabs [] = array(
            "title" => $this->l('General configuration'),
            "icon" => "wrench",
            "link" => "helpGenerateForm",
            "type" => "tab",
            "show" => "both",
            "active" => $locked
        );

        if (!$locked) {
            $this->innovatabs [] = array(
                "title" => $this->l('Configurer'),
                "icon" => "list",
                "link" => "renderConfigurationList",
                "type" => "tab",
                "show" => "both",
                "active" => ($current_cat == 'renderConfigurationList') ? true : false,
            );

            $this->innovatabs [] = array(
                "title" => $this->l('Components'),
                "icon" => "list",
                "link" => "renderComponentList",
                "type" => "tab",
                "show" => "both",
                "active" => ($current_cat == 'renderComponentList') ? true : false,
            );
        }

        $this->innovatabs [] = array(
            "title" => $this->l('Documentation'),
            "icon" => "file",
            "link" => $this->doclink,
            "type" => "doc",
            "show" => "both",
        );

        $this->innovatabs [] = array(
            "title" => $this->l('Support'),
            "icon" => "life-saver",
            "link" => $isoLinks["support"],
            "type" => "url",
            "show" => "whmcs",
        );

        if (!$locked) {
            if (Tools::isSubmit('updatecomponent') || Tools::isSubmit('editComponentStay') || Tools::isSubmit('submitComponentStay') || Tools::isSubmit('submitCloneComponent') || Tools::isSubmit('submitGenerateComponent')) {
                $this->innovatabs [] = array(
                    "title" => $this->l('Edit component'),
                    "icon" => "pencil",
                    "link" => 'renderEditComponent',
                    "type" => "tab",
                    "show" => "both",
                    "active" => true
                );
            } else {
                $this->innovatabs [] = array(
                    "title" => $this->l('New component'),
                    "icon" => "plus-circle",
                    "link" => 'renderFormAddComponent',
                    "type" => "tab",
                    "show" => "both",
                );
            }

            if (Tools::isSubmit('updateconfiguration') || Tools::isSubmit('submitConfigurationStay') || Tools::isSubmit('updateidxrcustomproduct_configurations')) {
                $this->innovatabs [] = array(
                    "title" => $this->l('Edit configuration'),
                    "icon" => "pencil",
                    "link" => 'renderFormAddConfiguration',
                    "type" => "tab",
                    "show" => "both",
                    "active" => true
                );
            } else {
                $this->innovatabs [] = array(
                    "title" => $this->l('New configuration'),
                    "icon" => "plus-circle",
                    "link" => 'renderFormAddConfiguration',
                    "type" => "tab",
                    "show" => "both",
                );
            }
        }

        $this->innovatabs[] = array(
            "title" => $this->l('Our Modules'),
            "icon" => "cubes",
            "link" => $isoLinks["ourmodules"],
            "type" => "url",
            "show" => "both",
        );
    }

    public function innovaTitle()
    {
        //tabs version
        $innovaTabs = "";
        if (method_exists(get_class($this), "setInnovaTabs")) {
            $innovaTabs = $this->setInnovaTabs();
        }
        $this->smarty->assign(array(
            "module_dir" => $this->_path,
            "module_name" => $this->displayName,
            "module_description" => $this->description,
            "isoLinks" => InnovaTools_2_0_0::getIsoLinks($this),
            "isAddons" => InnovaTools_2_0_0::isAddons($this),
            "tabs" => InnovaTools_2_0_0::getVersionTabs($this),
        ));

        $this->context->controller->addCSS(($this->_path) . "views/css/backinnova.css", "all");
        return $this->display(__FILE__, "views/templates/admin/innova-title.tpl");
    }

    public function fixCategoryProductPosition()
    {
        //Backup de la tabla
        if (!Db::getInstance()->execute('create table ' . _DB_PREFIX_ . 'category_product_bk select * from ' . _DB_PREFIX_ . 'category_product;')) {
            return false;
        }

        $categories = Db::getInstance()->executeS('select distinct id_category from ' . _DB_PREFIX_ . 'category_product where position = 0;');
        foreach ($categories as $category) {
            $id_category = $category['id_category'];
            $products = Db::getInstance()->executeS('select distinct id_product from ' . _DB_PREFIX_ . 'category_product where id_category = ' . (int) $id_category . ' and position = 0;');
            foreach ($products as $product) {
                $new_position = Db::getInstance()->getValue('select MAX(position)+1 from ' . _DB_PREFIX_ . 'category_product where id_category = ' . (int) $id_category);
                $data = array('position' => (int) $new_position);
                $where = 'id_product = ' . (int) $product['id_product'] . ' and id_category = ' . (int) $id_category;
                Db::getInstance()->update('category_product', $data, $where);
            }
        }
    }
    
    public function simplifyArray($array)
    {
        if ($array && isset($array['data']) && $array['data']) {
            return $array['data'];
        }
        return false;
    }
}
