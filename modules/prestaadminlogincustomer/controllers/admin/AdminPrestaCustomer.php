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

class AdminPrestaCustomerController extends ModuleAdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function postProcess()
    {
        if (Configuration::get('PRESTA_CUSTOMER_AS_LOGIN') && $this->checkPermission()) {
            $customerLogin = Tools::getValue('logincustomer');
            $idCustomer = Tools::getValue('id_customer');
            if ($customerLogin && $idCustomer && $this->context->employee->id) {
                $params = array(
                    'id_customer' => $idCustomer,
                    'logincustomer' => 1,
                    'superadmin' => $this->context->employee->email
                );
                Tools::redirect(
                    Context::getContext()->link->getModuleLink(
                        $this->module->name,
                        'process',
                        $params
                    )
                );
            }
        }
        parent::postProcess();
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        if ($isNewTheme || !$isNewTheme) {
            if (Configuration::get('PRESTA_CUSTOMER_AS_LOGIN')
                && Configuration::get('PRESTA_CUSTOMER_LOGIN_CUSTOMER_DETAIL')
                && $this->checkPermission()
            ) {
                $this->addCss(
                    _MODULE_DIR_.'prestaadminlogincustomer/views/css/prestalogincustomer.css'
                );
            }
        }
    }

    private function checkPermission()
    {
        $allow = false;
        if (Configuration::get('PRESTA_LOGIN_CUSTOMER_PERMISSION') && Configuration::get('PRESTA_CUSTOMER_LOGIN_CUSTOMER_DETAIL')) {
            $permission = json_decode(Configuration::get('PRESTA_LOGIN_CUSTOMER_PERMISSION'));
            if ($permission) {
                $employee = new Employee($this->context->employee->id);
                foreach ($permission as $profile) {
                    if ($profile == $employee->id_profile) {
                        $allow = true;
                        break;
                    }
                }
            }
        }
        return $allow;
    }

    public function ajaxProcessSearchCustomer()
    {
        $string = Tools::getValue('string');
        if ($string) {
            $result = Customer::searchByName($string);
            if ($result) {
                $customer = array();
                foreach ($result as $key => $data) {
                    $customer[$key]['id_customer'] = $data['id_customer'];
                    $customer[$key]['fname'] = $data['firstname'];
                    $customer[$key]['lname'] = $data['lastname'];
                    $customer[$key]['email'] = $data['email'];
                }
                die(json_encode($customer));
            } else {
                die(false);
            }
        }
        die(false);
    }
}
