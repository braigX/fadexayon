<?php
/**
 * Google-Friendly FAQ Pages and Lists With Schema Markup module
 *
 * @author    Opossum Dev
 * @copyright Opossum Dev
 * @license   You are just allowed to modify this copy for your own use. You are not allowed
 * to redistribute it. License is permitted for one Prestashop instance only, except for test
 * instances.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'faqop/classes/models/OpFaqList.php';
require_once _PS_MODULE_DIR_ . 'faqop/classes/models/OpFaqPage.php';
require_once _PS_MODULE_DIR_ . 'faqop/classes/models/OpFaqItem.php';

class OpFaqModelFactory
{
    public function createBlock($module)
    {
        try {
            if (Tools::isSubmit('op_type')) {
                $op_type = Tools::getValue('op_type');
            } else {
                Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminFaqop'));
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        if (Tools::isSubmit('id_list')) {
            $id_list = Tools::getValue('id_list');

            return $this->makeBlock($module, $op_type, $id_list);
        } else {
            return $this->makeBlock($module, $op_type);
        }
    }

    public function makeBlock($module, $op_type, $id_list = null, $id_lang = null, $id_shop = null)
    {
        if ($op_type == 'page') {
            return new OpFaqPage($module, $id_list, $id_lang, $id_shop);
        }

        return new OpFaqList($module, $id_list, $id_lang, $id_shop);
    }

    public function makeBlockShop($module, $op_type, $id_shop, $id_list = null)
    {
        return $this->makeBlock($module, $op_type, $id_list, null, $id_shop);
    }

    public function createItem($module)
    {
        if (Tools::isSubmit('id_item')) {
            $id_item = Tools::getValue('id_item');

            return $this->makeItem($module, $id_item);
        } else {
            return $this->makeItem($module);
        }
    }

    public function makeItem($module, $id_item = null, $id_lang = null, $id_shop = null)
    {
        return new OpFaqItem($module, $id_item, $id_lang, $id_shop);
    }

    public function makeItemShop($module, $id_shop, $id_list = null)
    {
        return $this->makeItem($module, $id_list, null, $id_shop);
    }
}
