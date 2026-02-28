<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Presta.Site
 * @copyright 2020 Presta.Site
 * @license   LICENSE.txt
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class PrestaWPAjaxModuleFrontController extends ModuleFrontController
{
    public function init()
    {
        // Allow cross-domain ajax requests from the blog
        $prestawp = Module::getInstanceByName('prestawp');
        $url_components = parse_url($prestawp->getWPPath());
        $allowed_domain = $url_components['host'];
        // if this connection is secured by secure key, use domain from the request to whitelist it
        if ((Tools::getValue('securekey') == $prestawp->securekey || Tools::getValue('getCart'))
            && Tools::getValue('wp_domain')
        ) {
            $allowed_domain = Tools::getValue('wp_domain');
        }
        $origin = $url_components['scheme'] . '://' . $allowed_domain;

        header('Access-Control-Allow-Origin: ' . $origin, false);
        header('Access-Control-Allow-Credentials: true', false);

        if (Tools::getValue('check_url')) {
            exit('1');
        }
        if (Tools::getValue('check_key')) {
            if (Tools::getValue('securekey') == $prestawp->securekey) {
                exit('1');
            } else {
                exit('0');
            }
        }

        // check the secure key unless it's the getCart action made from the customer's browser
        // getCart returns only the current customer's cart, so it's safe
        if (!Tools::getValue('getCart')) {
            if (Tools::getValue('securekey') != $prestawp->securekey) {
                exit;
            }
        }

        parent::init();
        $this->processAjax();
    }

    protected function processAjax()
    {
        $prestawp = Module::getInstanceByName('prestawp');
        $context = Context::getContext();

        if (Tools::isSubmit('getCart')) {
            $data = [];
            $total = $context->cart->getOrderTotal();
            $nbProducts = $context->cart->nbProducts();

            $data['total'] = $total;
            $data['order_url'] = $context->link->getPageLink('order');
            $data['cart_url'] = $context->link->getPageLink('cart', null, null, ['action' => 'show']);
            $data['search_url'] = $context->link->getPageLink('search');
            $data['nb_products'] = $nbProducts;
            $data['token'] = Tools::getToken(false);
            exit(json_encode($data));
        } elseif (Tools::isSubmit('get_products_url')) {
            exit($context->link->getModuleLink('prestawp', 'products'));
        } elseif (Tools::isSubmit('get_cart_url')) {
            exit($context->link->getPageLink('cart'));
        } elseif (Tools::isSubmit('get_search_url')) {
            exit($context->link->getPageLink('search'));
        } elseif (Tools::getValue('action') == 'searchProducts') {
            $prestawp->ajaxProcessGetProducts();
            exit;
        } elseif (Tools::getValue('action') == 'searchCategories') {
            $prestawp->ajaxProcessGetCategories();
            exit;
        } elseif (Tools::getValue('action') == 'searchManufacturers') {
            $prestawp->ajaxProcessGetManufacturers();
            exit;
        }

        exit('0');
    }
}
