<?php
/**
 * 2012 - 2024 HiPresta
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0).
 * It is also available through the world-wide-web at this URL: https://opensource.org/licenses/AFL-3.0
 *
 * @author    HiPresta <support@hipresta.com>
 * @copyright HiPresta 2024
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 *
 * @website   https://hipresta.com
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class HiGoogleConnectGoogleConnectModuleFrontController extends ModuleFrontController
{
    public function __construct()
    {
        $this->secure_key = Tools::getValue('secure_key');
        parent::__construct();
    }

    public function init()
    {
        parent::init();

        if ($this->ajax && $this->secure_key == $this->module->secure_key) {
            if (Tools::getValue('action') == 'connectUser') {
                $id_token = Tools::getValue('id_token');
                $url = 'https://oauth2.googleapis.com/tokeninfo?id_token=' . $id_token;
                $user_json_data = Tools::file_get_contents($url);
                $user_data = json_decode($user_json_data);

                if (isset($user_data->error) && !isset($user_data->sub)) {
                    exit(json_encode([
                        'error' => $user_data->error,
                        'error_description' => $user_data->error_description,
                    ]));
                }

                $firstName = preg_replace('/\PL/u', '', $user_data->given_name);
                $lastName = preg_replace('/\PL/u', '', $user_data->family_name);
                $email = $user_data->email;
                $userId = $user_data->sub;

                if (!$lastName) {
                    $lastName = $firstName;
                }

                // let's save customer details in module Db
                $user = HiGoogleConnectUser::getUserByGoogleId($userId);
                if (!$user) {
                    $user = new HiGoogleConnectUser();
                    $user->id_shop = $this->context->shop->id;
                    $user->id_google_account = $userId;
                    $user->first_name = $firstName;
                    $user->last_name = $lastName;
                    $user->email = $email;
                    $user->add();
                }

                // check if customer already exists
                $customer = new Customer();
                $customer = $customer->getByEmail($email);
                if ($customer) {
                    $this->customerLogin($customer);
                } else {
                    $this->customerRegisterAndLogin($firstName, $lastName, $email);
                }

                $this->context->smarty->assign([
                    'firstName' => $firstName,
                ]);
                if ($this->module->psv >= 1.7) {
                    $message = $this->context->smarty->fetch('module:' . $this->module->name . '/views/templates/hook/message.tpl');
                } else {
                    $message = $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->module->name . '/views/templates/hook/message.tpl');
                }

                exit(json_encode([
                    'error' => false,
                    'firstName' => $firstName,
                    'message' => $message,
                ]));
            }
        }

        exit;
    }

    public function customerLogin($customer)
    {
        Hook::exec('actionBeforeAuthentication');
        $this->context->cookie->id_customer = (int) $customer->id;
        $this->context->cookie->customer_lastname = $customer->lastname;
        $this->context->cookie->customer_firstname = $customer->firstname;
        $this->context->cookie->logged = 1;
        $customer->logged = 1;
        $this->context->cookie->is_guest = $customer->isGuest();
        $this->context->cookie->passwd = $customer->passwd;
        $this->context->cookie->email = $customer->email;
        /* Add customer to the context */
        $this->context->customer = $customer;
        if (Configuration::get('PS_CART_FOLLOWING')
            && (empty($this->context->cookie->id_cart)
            || Cart::getNbProducts($this->context->cookie->id_cart) == 0)
            && $id_cart = (int) Cart::lastNoneOrderedCart($this->context->customer->id)) {
            $this->context->cart = new Cart($id_cart);
        } else {
            if (!$this->context->cart) {
                $cart = new Cart();
                $cart->id_lang = (int) $this->context->cookie->id_lang;
                $cart->id_currency = (int) $this->context->cookie->id_currency;
                $cart->id_guest = (int) $this->context->cookie->id_guest;
                $cart->id_shop_group = (int) $this->context->shop->id_shop_group;
                $cart->id_shop = $this->context->shop->id;
                $cart->id_address_delivery = 0;
                $cart->id_address_invoice = 0;

                $this->context->cart = $cart;
            }
            $this->context->cart->id_carrier = 0;
            $this->context->cart->setDeliveryOption(null);
            $this->context->cart->id_address_delivery = (int) Address::getFirstCustomerAddressId((int) $customer->id);
            $this->context->cart->id_address_invoice = (int) Address::getFirstCustomerAddressId((int) $customer->id);
        }
        if ($this->context->cart) {
            $this->context->cart->id_customer = (int) $customer->id;
            $this->context->cart->secure_key = $customer->secure_key;
            $this->context->cart->save();
            $this->context->cookie->id_cart = (int) $this->context->cart->id;
            $this->context->cart->autosetProductAddress();
        }
        $this->context->cookie->write();
        if ($this->module->psv >= 1.7) {
            $this->context->updateCustomer($customer);

            Hook::exec('actionAuthentication', ['customer' => $this->context->customer]);
        } else {
            Hook::exec('actionAuthentication');
        }

        /* Login information have changed, so we check if the cart rules still apply */
        CartRule::autoRemoveFromCart($this->context);
        CartRule::autoAddToCart($this->context);
    }

    public function customerRegisterAndLogin($firstName, $lastName, $email)
    {
        Hook::exec('actionBeforeSubmitAccount');
        $customer = new Customer();
        $customer->firstname = $firstName;
        $customer->lastname = $lastName;
        $customer->email = $email;
        $password = Tools::passwdGen();
        $customer->passwd = md5(pSQL(_COOKIE_KEY_ . $password));
        $customer->is_guest = 0;
        $customer->active = 1;
        $customer->add();

        Hook::exec('actionCustomerAccountAdd', [
            '_POST' => $_POST,
            'newCustomer' => $customer,
        ]);
        $this->sendConfirmationMail($customer);

        /* Customer login */
        $context = Context::getContext();
        $context->customer = $customer;
        $context->cookie->id_customer = (int) $customer->id;
        $context->cookie->customer_lastname = $customer->lastname;
        $context->cookie->customer_firstname = $customer->firstname;
        $context->cookie->passwd = $customer->passwd;
        $context->cookie->logged = 1;
        $customer->logged = 1;
        $context->cookie->email = $customer->email;
        $context->cookie->is_guest = $customer->is_guest;
        $context->cookie->update();

        if (!$context->cart) {
            $cart = new Cart();
            $cart->id_lang = (int) $context->cookie->id_lang;
            $cart->id_currency = (int) $context->cookie->id_currency;
            $cart->id_guest = (int) $context->cookie->id_guest;
            $cart->id_shop_group = (int) $context->shop->id_shop_group;
            $cart->id_shop = $context->shop->id;
            $cart->id_address_delivery = 0;
            $cart->id_address_invoice = 0;

            $context->cart = $cart;
        }
        $context->cart->secure_key = $customer->secure_key;
        $context->cart->update();

        $context->updateCustomer($customer);
    }

    private function sendConfirmationMail(Customer $customer)
    {
        if ($customer->is_guest || !Configuration::get('PS_CUSTOMER_CREATION_EMAIL')) {
            return true;
        }

        return Mail::Send(
            $this->context->language->id,
            'account',
            $this->translator->trans(
                'Welcome!',
                [],
                'Emails.Subject'
            ),
            [
                '{firstname}' => $customer->firstname,
                '{lastname}' => $customer->lastname,
                '{email}' => $customer->email,
            ],
            $customer->email,
            $customer->firstname . ' ' . $customer->lastname
        );
    }
}
