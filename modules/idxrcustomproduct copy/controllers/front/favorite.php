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

class IdxrcustomproductFavoriteModuleFrontController extends ModuleFrontController
{

    public $ssl = true;
    public $display_column_left = false;

    public function __construct()
    {
        parent::__construct();
        $this->context = Context::getContext();
    }

    /**
     * See ModuleFrontController initContent
     */
    public function initContent()
    {

        parent::initContent();

        if (!$this->context->customer->isLogged() && $this->php_self != 'authentication' && $this->php_self != 'password') {
            $this->setTemplate('notlogged.tpl');
        } else {
            $token = Configuration::get(Tools::strtoupper($this->module->name .'_TOKEN'));
            $favorites = $this->getFavorites($this->context->customer->id);
            $favorites = array_map(array($this, 'pToArray'), $favorites);

            $this->context->controller->addCSS(
                $this->module->getLocalPath() . 'views/css/favorite.css',
                'all'
            );
            $this->context->controller->addJS($this->module->getLocalPath() . 'views/js/favorite.js');
            Media::addJsDef(
                array(
                    'url_ajax' => $this->context->link->getModuleLink($this->module->name, 'ajax', array('ajax' => true, 'token' => $token)),
                    'confirm_text' => $this->module->l('are you sure of delete it?')
                )
            );

            $this->context->smarty->assign(array(
                'favorites' => $favorites
            ));

            if (_PS_VERSION_ >= '1.7') {
                $this->setTemplate('module:idxrcustomproduct/views/templates/front/favorite17.tpl');
            } else {
                $this->setTemplate('favorite.tpl');
            }
        }
    }

    public function getFavorites($customer_id)
    {
        $fav_query = 'Select * from ' . _DB_PREFIX_ . 'idxrcustomproduct_customer_fav where id_customer = ' . (int) $customer_id . ';';
        $result = Db::getInstance()->executeS($fav_query);
        foreach ($result as &$fav) {
            $fav['product_name'] = Product::getProductName($fav['id_product']);
            $fav['product_url'] = $this->context->link->getProductLink($fav['id_product']);
            $fav['extra_data'] = $this->module->getExtraByFav($fav['id_fav']);
        }
        
        return $result;
    }
    
    public function pToArray($favorite)
    {
        $parts = explode('<p>', $favorite['description']);
        foreach ($parts as $key => &$value) {
            $value = str_replace('</p>', '', $value);
            if (!$value) {
                unset($parts[$key]);
            }
        }
        $favorite['description'] = $parts;
        return $favorite;
    }
}
