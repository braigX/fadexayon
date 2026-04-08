<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * @author    Innova Deluxe SL
 * @copyright 2016 Innova Deluxe SL

 * @license   INNOVADELUXE
 */

class AdminIdxrcustomproductController extends ModuleAdminController
{

    public $supported_image = array(
        'gif',
        'jpg',
        'jpeg',
        'png',
        'svg'
    );

    public function ajaxProcessAddoption()
    {
        $return = array(
            'result' => 'ok',
            'message' => $this->module->l('Option updated sucessfully', 'ajax')
        );
        $id_component = Tools::getValue('componentid');

        $sql = 'Select * from ' . _DB_PREFIX_ . 'idxrcustomproduct_components_lang where id_component = ' . $id_component . ';';
        $component_options = Db::getInstance()->executeS($sql);
        foreach ($component_options as &$lang) {
            $lang['values'] = json_decode($lang['json_values']);
            if (!isset($lang['values']->options)) {
                if (!is_object($lang['values'])) {
                    $lang['values'] = new stdClass();
                }
                $lang['values']->options = array();
            }

            $id_option = 0;
            if (count($lang['values']->options) > 0) {
                foreach ($lang['values']->options as $option) {
                    if ($option->id >= $id_option) {
                        $id_option = $option->id + 1;
                    }
                }
            }

            $new_option = new stdClass();
            $new_option->id = $id_option;
            $new_option->name = Tools::getValue('optionname_' . $lang['id_lang']);
            $new_option->description = Tools::getValue('optiondesc_' . $lang['id_lang']);
            //if (isset($_FILES['myfile']['name'])) {
            $new_option->img_ext = 'png'; //pSQL($extension);
            //}
            $lang['values']->options[] = $new_option;
            $update = array(
                'json_values' => Db::getInstance()->escape(json_encode($lang['values'], JSON_UNESCAPED_UNICODE))
            );
            $where = 'id_component = ' . (int) $lang['id_component'] . ' and id_lang = ' . (int) $lang['id_lang'];
            Db::getInstance()->update('idxrcustomproduct_components_lang', $update, $where);
        }

        $option = new IdxOption();
        $option->id = (int) $id_option;
        $option->id_component = (int) $id_component;
        $option->price_impact = (float) Tools::getValue('option_priceimpact');
        $option->price_impact_wodiscount = (float) Tools::getValue('option_priceimpact_wodiscount');
        $option->weight_impact = (float) Tools::getValue('option_weightimpact');
        $option->reference = pSQL(Tools::getValue('option_reference'));
        $option->attach_product = pSQL(Tools::getValue('option_product_attached'));
        $option->attach_product_qty = (int) Tools::getValue('option_product_qty');
        $option->add();

        if (isset($_FILES['myfile']['name'])) {
            $this->saveOptionImage($_FILES['myfile'], $id_component, $id_option, $return);
        }
        die(json_encode($return));
    }

    public function ajaxProcessDeleteoption()
    {
        $return = array(
            'result' => 'ok',
            'message' => $this->module->l('Option deleted sucessfully')
        );
        $id_component = (int) Tools::getValue('component');
        $id_option = (int) Tools::getValue('option');
        $this->module->deleteOption($id_component, $id_option);
        die(json_encode($return));
    }

    public function ajaxProcessUpdateoption()
    {
        $return = array(
            'result' => 'ok',
            'message' => $this->module->l('Option updated sucessfully', 'ajax')
        );
        $id_component = (int) Tools::getValue('component');
        $id_option = (int) Tools::getValue('option');
        $data = Tools::getValue('data');
        $this->module->updateOption($id_component, $id_option, $data);
        if (isset($_FILES['file']['name'])) {
            $this->saveOptionImage($_FILES['file'], $id_component, $id_option, $return);
        }
        die(json_encode($return));
    }

    public function saveOptionImage($source, $id_component, $id_option, &$return)
    {
        $array = explode('.', $source['name']);
        $extension = end($array);
        $destination_folder = _PS_IMG_DIR_ . $this->module->name . DIRECTORY_SEPARATOR . 'options';
        if (!file_exists($destination_folder)) {
            mkdir($destination_folder, 0777, true);
        }
        $target_path = $destination_folder . DIRECTORY_SEPARATOR . $id_component . '_' . $id_option . '.png';
        if (!in_array($extension, $this->supported_image)) {
            $return['result'] = 'ko';
            $return['message'] = $extension . ' ' . $this->module->l('is not an allowed extension for image', 'ajax');
        } else {
            if ($extension == 'png') {
                if (move_uploaded_file($source['tmp_name'], $target_path)) {
                    $return['result'] = 'ok';
                } else {
                    $return['result'] = 'ko';
                    $return['message'] = $this->module->l('Fail when try to update the image, please try again', 'ajax');
                }
            } else {
                if (imagepng(imagecreatefromstring(Tools::file_get_contents($source['tmp_name'])), $target_path)) {
                    $return['result'] = 'ok';
                } else {
                    $return['result'] = 'ko';
                    $return['message'] = $this->module->l('Fail when try to conver and update the image, please try again', 'ajax');
                }
            }
        }
    }

    public function ajaxProcessOrderOptions()
    {
        $list = Tools::getValue('order');
        $item = explode('_', $list[0]);
        $order = array();
        foreach ($list as $position) {
            $order[] = explode('_', $position)[1];
        }

        $component_options = Db::getInstance()->executeS('Select id_lang, json_values from ' . _DB_PREFIX_ . 'idxrcustomproduct_components_lang where id_component = ' . (int) $item[0]);
        foreach ($component_options as $lang_options) {
            $language = $lang_options['id_lang'];
            $values = json_decode($lang_options['json_values'])->options;
            $new_options = array();
            foreach ($order as $option_id) {
                foreach ($values as $option) {
                    if ($option->id == $option_id) {
                        $new_options[] = $option;
                    }
                }
            }
            $x = new stdClass();
            $x->options = $new_options;
            $update_opt = Db::getInstance()->escape(json_encode($x));
            Db::getInstance()->update('idxrcustomproduct_components_lang', array('json_values' => $update_opt), 'id_lang = ' . (int) $language . ' and id_component = ' . (int) $item[0]);
        }
        die('ok');
    }

    public function ajaxProcessGetOptionsList()
    {
        $component_id = Tools::getValue('component');
        $component = new IdxComponent($component_id, true);
        die($this->module->generateOptionsForm($component));
    }

    public function ajaxProcessShowconstraints()
    {
        $configuration_id = Tools::getValue('configuration');
        $component_id = Tools::getValue('component');
        $data = array();
        $data['component_name'] = $this->module->getComponentName($component_id);
        $data['configuration'] = $this->module->getConfigurationFront($configuration_id);
        die(json_encode($data));
    }

    public function ajaxProcessAddconstraints()
    {
        $configuration_id = Tools::getValue('configuration');
        $component_id = Tools::getValue('component');
        $constraint = Tools::getValue('constraint');
        $this->module->addConstraint($configuration_id, $component_id, $constraint);
        die('ok');
    }

    public function ajaxProcessDelconstraints()
    {
        $configuration_id = Tools::getValue('configuration');
        $component_id = Tools::getValue('component');
        $constraint = Tools::getValue('constraint');
        $this->module->delConstraint($configuration_id, $component_id, $constraint);
        die('ok');
    }

    public function ajaxProcessAddImpact()
    {
        $configuration_id = Tools::getValue('configuration');
        $component_id = Tools::getValue('component');
        $impact = Tools::getValue('impact');
        $configuration = new IdxConfiguration($configuration_id);
        $configuration->addOptionImpact($impact);
        die('ok');
    }
    
    public function ajaxProcessDelimpact()
    {
        $configuration_id = Tools::getValue('configuration');
        $component_id = Tools::getValue('component');
        $impact = Tools::getValue('impact');
        $configuration = new IdxConfiguration($configuration_id);
        $configuration->delOptionImpact($impact);
        die('ok');
    }
    
    public function ajaxProcessComponentdefault()
    {
        $component_id = Tools::getValue('component');
        $option_id = Tools::getValue('option');
        $this->module->setDefaultComponentOption($component_id, $option_id);
        die('ok');
    }

    public function ajaxProcessConfigurationdefault()
    {
        $configuration_id = Tools::getValue('configuration');
        $component_id = Tools::getValue('component');
        $option_id = Tools::getValue('option');
        $this->module->setDefaultConfigurationComponentOption($configuration_id, $component_id, $option_id);
        die('ok');
    }

    public function ajaxProcessDeletecomponenticon()
    {
        $component_id = Tools::getValue('component');
        if (file_exists(_PS_MODULE_DIR_ . $this->module->name . '/img/icon/' . $component_id . '.png')) {
            unlink(_PS_MODULE_DIR_ . $this->module->name . '/img/icon/' . $component_id . '.png');
            die('ok');
        }
    }

    public function ajaxProcessMassimageupd()
    {
        $configuration = Tools::getValue('configuration_id');
        $return = array();
        if ($configuration && isset($_FILES['myfile']['name'])) {
            IdxConfiguration::saveConfigurationImage($_FILES['myfile'], $configuration, $return);
//            if ($return['result'] == 'ko') {
//                IdxConfiguration::delImage($configuration, $index);
//            }
        } else {
            $return['result'] = 'ko';
            $return['message'] = $this->module->l('Configuration is not valid', 'ajax') . ' ' . $configuration;
        }
        die(json_encode($return));
    }

    public function ajaxProcessGetConfigAllImages()
    {
        $configuration = Tools::getValue('configuration_id');
        $images = IdxConfiguration::getAllImages($configuration);
        die(json_encode($images));
    }

    public function ajaxProcessDeleteConfigImage()
    {
        $image = Tools::getValue('name');
        $values = explode('_', str_replace('.png', '', $image));
        IdxConfiguration::delImage($values[0], $values[1]);
    }

    public function ajaxProcessGetProductComponentForm()
    {
        $id_product = Tools::getValue('product');
        die($this->module->generateProductComponentForm($id_product));
    }

    public function ajaxProcessSearchProducts()
    {
        $q = Tools::getValue('q');
        $hidden_categorie = Configuration::get(Tools::strtoupper($this->module->name) . '_CATEGORY');
        $sql = 'SELECT DISTINCT prod.id_product,
            prod.reference,
            prod.price,
            prodl.name,
            prodl.description_short,
            prod.id_tax_rules_group
            FROM ' . _DB_PREFIX_ . 'product prod, ' . _DB_PREFIX_ . 'product_lang prodl
            WHERE prod.id_category_default != ' . (int) $hidden_categorie . ' AND prod.id_product = prodl.id_product and
            (prodl.name like "%' . pSQL($q) . '%" or prod.reference like "%' . pSQL($q) . '%" or prod.ean13 like "%' . pSQL($q) . '%" or prodl.description like "%' . pSQL($q) . '%" or prodl.description_short like "%' . pSQL($q) . '%")';
        $products = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        $productos = array();
        if ($products) {
            foreach ($products as $product) {
                $combinaciones = Product::getProductAttributesIds($product['id_product']);
                if (!empty($combinaciones)) {
                    foreach ($combinaciones as $combinacion) {
                        $reference = Db::getInstance()->getValue('select reference from ' . _DB_PREFIX_ . 'product_attribute where id_product = ' . (int) $product['id_product'] . ' and id_product_attribute = ' . (int) $combinacion['id_product_attribute']);
                        if (!$reference) {
                            $reference = $product['reference'];
                        }
                        $datos = array(
                            'id' => $product['id_product'] . '_' . $combinacion['id_product_attribute'],
                            'name' => Product::getProductName($product['id_product'], $combinacion['id_product_attribute']) . ($reference ? ' - ' . $reference : '')
                        );
                        $productos[] = $datos;
                    }
                } else {
                    $datos = array(
                        'id' => $product['id_product'] . '_0',
                        'name' => $product['name'] . ($product['reference'] ? ' - ' . $product['reference'] : '')
                    );
                    $productos[] = $datos;
                }
            }
        }
        die(json_encode($productos));
    }
    
    public function ajaxProcessSearchProductsWocomb()
    {
        $q = Tools::getValue('q');
        $hidden_categorie = Configuration::get(Tools::strtoupper($this->module->name) . '_CATEGORY');
        $context = Context::getContext();
        
        $sql = new DbQuery();
        $sql->select('prod.id_product, prod.reference, prod.price, prodl.name, prodl.description_short, prod.id_tax_rules_group');
        $sql->from('product', 'prod');
        $sql->innerJoin('product_lang', 'prodl', 'prod.id_product = prodl.id_product');
        $sql->where('prod.id_category_default != ' . (int) $hidden_categorie);
        $sql->where('prodl.id_lang = '.(int)$context->language->id);
        $sql->where('(prodl.name like "%' . pSQL($q) . '%" or prod.reference like "%' . pSQL($q) . '%" or prod.ean13 like "%' . pSQL($q) . '%" or prodl.description like "%' . pSQL($q) . '%" or prodl.description_short like "%' . pSQL($q) . '%")');
        
        $products = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        $productos = array();
        if ($products) {
            foreach ($products as $product) {
                //TODO https://gitlab.innovadeluxe.com/prestashop/idxrcustomproduct/-/issues/9
                $datos = array(
                    'id' => $product['id_product'],
                    'name' => $product['name'] . ($product['reference'] ? ' (ref: ' . $product['reference'] .')': ''),
                    'used' => $this->module->getConfigurationByProduct($product['id_product'])
                );
                $productos[] = $datos;
            }
        }
        die(json_encode($productos));
    }
    
    public function ajaxProcessSearchCategory()
    {
        $query = Tools::getValue('q', false);
        if (!$query || $query == '' || Tools::strlen($query) < 1) {
            die();
        }
        
        $sql = new DbQuery();
        $sql->select('id_category, name');
        $sql->from('category_lang');
        $sql->where("id_category != ".(int)Configuration::get(Tools::strtoupper($this->module->name) . '_CATEGORY'));
        $sql->where("(name LIKE '%".pSQL($query)."%' OR description LIKE '%".pSQL($query)."%')");
        $sql->where("id_lang = " . (int) $this->context->language->id . Shop::addSqlRestrictionOnLang());

        $categories = array();
        if($reg = Db::getInstance()->executeS($sql)){
            foreach($reg as $elemento){
                $categories[] = array(
                    'id' => $elemento['id_category'],
                    'name' => $elemento['name'],
                    'used' => $this->module->getConfigurationByCategory($elemento['id_category'])
                );
            }
        }
        die(json_encode($categories));
    }
}
