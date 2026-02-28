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

class AdminCustomersController extends AdminCustomersControllerCore
{
    public function postProcess()
    {
        parent::postProcess();
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
                        'prestaadminlogincustomer',
                        'process',
                        $params
                    )
                );
            }
        }
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();
        if (Configuration::get('PRESTA_CUSTOMER_AS_LOGIN') && $this->display == 'view' && $this->checkPermission()) {
            $this->page_header_toolbar_btn['login_customer'] = array(
                'href' => self::$currentIndex . '&' .
                    $this->identifier . '=' .
                    Tools::getValue('id_customer') .
                    '&logincustomer=1&token=' .
                    $this->token,
                'desc' => $this->l('Login Customer', null, null, false),
                'icon' => 'icon-unlock',
                'target' => '_blank'
            );
        }
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
                    _MODULE_DIR_ . 'prestaadminlogincustomer/views/css/prestalogincustomer.css'
                );
            }
        }
    }

    public function __construct()
    {
        parent::__construct();
        if (Configuration::get('PRESTA_CUSTOMER_AS_LOGIN') && $this->checkPermission()) {
            $this->addRowAction('login');
        }
    }

    public function displayLoginLink($token = null, $id = null)
    {
        if (Configuration::get('PRESTA_CUSTOMER_AS_LOGIN') && $this->checkPermission()) {
            $this->context->smarty->assign(
                array(
                    'href' => self::$currentIndex .
                        '&token=' .
                        $token .
                        '&logincustomer=1&' .
                        $this->identifier . '=' .
                        $id,
                    'action' => $this->l('Login as customer')
                )
            );
            $tpl = $this->context->smarty->fetch(
                _PS_MODULE_DIR_ . 'prestaadminlogincustomer/views/templates/hook/presta_customer_list_login.tpl'
            );

            return $tpl;
        }
    }

    private function checkPermission()
    {
        $allow = false;
        if (Configuration::get('PRESTA_LOGIN_CUSTOMER_PERMISSION')
            && Configuration::get('PRESTA_CUSTOMER_LOGIN_CUSTOMER_DETAIL')
        ) {
            $permission = json_decode(Configuration::get('PRESTA_LOGIN_CUSTOMER_PERMISSION'));
            if ($permission) {
                foreach ($permission as $profile) {
                    if ($profile == $this->context->employee->id_profile) {
                        $allow = true;
                        break;
                    }
                }
            }
        }

        return $allow;
    }
}
