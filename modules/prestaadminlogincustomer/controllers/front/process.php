<?php
/**
 * 2008-2024 Prestaworld
 *
 * NOTICE OF LICENSE
 *
 * The source code of this module is under a commercial license.
 * Each license is unique and can be installed and used on only one website.
 * Any reproduction or representation total or partial of the module, one or more of its components,
 * by any means whatsoever, without express permission from us is prohibited.
 *
 * DISCLAIMER
 *
 * Do not alter or add/update to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @author    prestaworld
 * @copyright 2008-2024 Prestaworld
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 * International Registered Trademark & Property of prestaworld
 */
if (!defined('_PS_VERSION_')) {
    die('VERSION NOT EXIST');
}

class PrestaAdminLoginCustomerProcessModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        $idCustomer = Tools::getValue('id_customer');
        $logincustomer = Tools::getValue('logincustomer');
        $superadmin = Tools::getValue('superadmin');
        if ($superadmin && $idCustomer && $logincustomer) {
            $emp = new Employee();
            $employee = $emp->getByEmail($superadmin);
            if (isset($employee->id) && $employee->id) {
                $this->context->customer->mylogout();

                $customer = new Customer($idCustomer);
                $this->context->updateCustomer($customer);
                $cookieValue = 'presta_'.$customer->id;
                $this->context->cookie->{$cookieValue} = 'presta_'.$customer->id;
                $this->context->cookie->write();
                // Hook::exec('actionAuthentication', ['customer' => $this->context->customer]);

                // Login information have changed, so we check if the cart rules still apply
                CartRule::autoRemoveFromCart($this->context);
                CartRule::autoAddToCart($this->context);

                if (Configuration::get('PRESTA_CUSTOMER_LOGIN_HISTORY')) {
                    $objLoggedIn = new PrestaCustomerLoggedIn();
                    $objLoggedIn->id_customer = $idCustomer;
                    $objLoggedIn->id_employee = $employee->id;
                    $objLoggedIn->login_date = date('Y-m-d H:i:s');
                    $objLoggedIn->save();
                }
            }
        }
        Tools::redirect($this->context->link->getPageLink('my-account'));
    }
}
