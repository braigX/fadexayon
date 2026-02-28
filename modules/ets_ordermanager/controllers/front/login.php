<?php
/**
 * 2007-2023 ETS-Soft
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses. 
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 * 
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please contact us for extra customization service at an affordable price
 *
 *  @author ETS-Soft <etssoft.jsc@gmail.com>
 *  @copyright  2007-2023 ETS-Soft
 *  @license    Valid for 1 website (or project) for each purchase of license
 *  International Registered Trademark & Property of ETS-Soft
 */

if (!defined('_PS_VERSION_'))
	exit;
class Ets_ordermanagerLoginModuleFrontController extends ModuleFrontController
{
    public function __construct()
	{
		parent::__construct();
	}
    public function postProcess()
    {
        if(($token = Tools::getValue('token')) && Validate::isMd5($token))
        {
            if($id_customer = (int)Db::getInstance()->getValue('SELECT id_customer FROM  `'._DB_PREFIX_.'ets_odm_customer_login` WHERE token="'.$token.'" AND date_add > "'.pSQL(date('Y-m-d H:i:s', strtotime('-1 minutes'))).'"'))
            {
                $customer = new Customer($id_customer);
                if(Validate::isLoadedObject($customer))
                {
                    $this->context->cookie->id_customer = (int)($customer->id);
                    $this->context->cookie->customer_lastname = $customer->lastname;
                    $this->context->cookie->customer_firstname = $customer->firstname;
                    $this->context->cookie->logged = 1;
                    $customer->logged = 1;
                    $this->context->cookie->is_guest = $customer->isGuest();
                    $this->context->cookie->passwd = $customer->passwd;
                    $this->context->cookie->email = $customer->email;
                    $this->context->customer = $customer;
    
                    if (Configuration::get('PS_CART_FOLLOWING') && (empty($this->context->cookie->id_cart) || Cart::getNbProducts($this->context->cookie->id_cart) == 0) && $id_cart = (int)Cart::lastNoneOrderedCart($this->context->customer->id)) {
                        $this->context->cart = new Cart($id_cart);
                    } else {
                        $id_carrier = (int)$this->context->cart->id_carrier;
                        $this->context->cart->id_carrier = 0;
                        $this->context->cart->setDeliveryOption(null);
                        $this->context->cart->id_address_delivery = (int)Address::getFirstCustomerAddressId((int)($customer->id));
                        $this->context->cart->id_address_invoice = (int)Address::getFirstCustomerAddressId((int)($customer->id));
                    }
                    $this->context->cart->id_customer = (int)$customer->id;
                    $this->context->cart->secure_key = $customer->secure_key;
    
                    if (Tools::isSubmit('ajax') && isset($id_carrier) && $id_carrier) {
                        $delivery_option = array($this->context->cart->id_address_delivery => $id_carrier.',');
                        $this->context->cart->setDeliveryOption($delivery_option);
                    }
    
                    $this->context->cart->save();
                    $this->context->cookie->id_cart = (int)$this->context->cart->id;
                    $this->context->cookie->write();
                    $this->context->cart->autosetProductAddress();
                    if(method_exists($this->context,'updateCustomer'))
                        $this->context->updateCustomer($customer);
                    else
                        $this->updateContext($customer);
                    Hook::exec('actionAuthentication', array('customer' => $this->context->customer));
                    CartRule::autoRemoveFromCart($this->context);
                    CartRule::autoAddToCart($this->context);
                }
                Db::getInstance()->execute('DELETE FROM  `'._DB_PREFIX_.'ets_odm_customer_login` WHERE id_customer='.(int)$id_customer);
            }
        }
        Tools::redirect($this->context->link->getPageLink('my-account'));
    }
    protected function updateContext(Customer $customer)
    {
        $customer->logged = 1;
        $this->context->customer = $customer;
        $this->context->cookie->id_customer = (int)$customer->id;
        $this->context->cookie->customer_lastname = $customer->lastname;
        $this->context->cookie->customer_firstname = $customer->firstname;
        $this->context->cookie->passwd = $customer->passwd;
        $this->context->cookie->logged = 1;
        $this->context->cookie->email = $customer->email;
        $this->context->cookie->is_guest = $customer->is_guest;
        $this->context->cart->secure_key = $customer->secure_key;
        $this->context->cookie->write();
        $this->context->cart->update();
        $customer->update();        
    }
}