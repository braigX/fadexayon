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
    exit;
}
class AdminPrestaManageBtwoBCustomerController extends ModuleAdminController
{
    private $tabClassName = 'AdminPrestaManageBtwoBCustomer';

    public function __construct()
    {
        $this->identifier = 'id_presta_manage_btwob_customer';
        parent::__construct();
        $this->className = 'PrestaBtwoBRegistrationManageBtwoBCustomer';
        $this->table = 'presta_manage_btwob_customer';
        $this->bootstrap = true;
        $this->list_no_link = true;
        $this->lang = false;
        $this->_join .= 'INNER JOIN ' . _DB_PREFIX_ . 'customer c ON (c.id_customer = a.id_customer)';
        $this->_group = ' GROUP BY a.id_customer';
        $this->_select = '
            c.`active` as presta_validated,
            c.email as customer_email,
            c.company as company_name,
            CONCAT(c.`firstname`, \' \', c.`lastname`)  as customer_name';

        $this->fields_list = array(
            'id_presta_manage_btwob_customer' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ),
            'customer_name' => array(
                'title' => $this->l('Customer Name'),
                'align' => 'left',
                'havingFilter' => true
            ),
            'customer_email' => array(
                'title' => $this->l('Email'),
                'align' => 'left',
                'havingFilter' => true
            ),
            'company_name' => array(
                'title' => $this->l('Company'),
                'align' => 'left',
                'havingFilter' => true
            ),
            'presta_validated' => array(
                'title' => $this->l('Validate'),
                'align' => 'center',
                'type' => 'bool',
                'active' => 'status',
                'havingFilter' => true
            ),
            'date_add' => array(
                'title' => $this->l('Create Date'),
                'type' => 'date',
                'filter_key' => 'a!date_add',
                'align' => 'right',
            ),
            'date_upd' => array(
                'title' => $this->l('Update Date'),
                'type' => 'date',
                'filter_key' => 'a!date_upd',
                'align' => 'right',
            ),
        );
        $this->addRowAction('view');
    }

    public function initToolbar()
    {
        parent::initToolBar();
        $this->page_header_toolbar_btn['desc-module-back'] = array(
            'href' => 'index.php?controller=AdminModules&token=' . Tools::getAdminTokenLite('AdminModules'),
            'icon' => 'process-icon-back',
            'desc' => $this->l('Go to module manager')
        );
        $this->page_header_toolbar_btn['desc-module-reload'] = array(
            'href' => 'index.php?controller=' .
                $this->tabClassName .
                '&token=' .
                Tools::getAdminTokenLite($this->tabClassName) .
                '&reload=1',
            'icon' => 'process-icon-refresh',
            'desc' => $this->l('Refresh')
        );
        $this->page_header_toolbar_btn['desc-module-translate'] = array(
            'href' => '#',
            'desc' => $this->l('Translate'),
            'icon' => 'process-icon-flag',
            'modal_target' => '#moduleTradLangSelect'
        );
        $this->page_header_toolbar_btn['desc-module-hook'] = array(
            'href' => 'index.php?tab=AdminModulesPositions&token=' .
                Tools::getAdminTokenLite('AdminModulesPositions') . '&show_modules=' .
                Module::getModuleIdByName($this->module->name),
            'icon' => 'process-icon-anchor',
            'desc' => $this->l('Manage hooks'),
        );
    }

    public function initToolbarTitle()
    {
        parent::initToolbarTitle();
        switch ($this->display) {
            case '':
            case 'list':
                array_pop($this->toolbar_title);
                $this->toolbar_title[] = $this->l('Manage B2B Customer');
                break;
            case 'view':
            case 'add':
            case 'edit':
        }
    }

    public function initModal()
    {
        parent::initModal();
        $languages = Language::getLanguages(false);
        $translateLinks = array();
        $module = Module::getInstanceByName($this->module->name);
        if (false === $module) {
            return;
        }
        $isNewTranslateSystem = $module->isUsingNewTranslationSystem();
        $link = Context::getContext()->link;
        foreach ($languages as $lang) {
            if ($isNewTranslateSystem) {
                $translateLinks[$lang['iso_code']] = $link->getAdminLink(
                    'AdminTranslationSf',
                    true,
                    array(
                        'lang' => $lang['iso_code'],
                        'type' => 'modules',
                        'selected' => $module->name,
                        'locale' => $lang['locale']
                    )
                );
            } else {
                $translateLinks[$lang['iso_code']] = $link->getAdminLink(
                    'AdminTranslations',
                    true,
                    array(),
                    array(
                        'type' => 'modules',
                        'module' => $module->name,
                        'lang' => $lang['iso_code']
                    )
                );
            }
        }
        $tabLink = 'index.php?tab=AdminTranslations&token=';
        $adminTranslation = Tools::getAdminTokenLite('AdminTranslations') . '&type=modules&module=';
        $configure = $this->module->name . '&lang=';
        $this->context->smarty->assign(
            array(
                'trad_link' => $tabLink . $adminTranslation . $configure,
                'module_languages' => $languages,
                'module_name' => $this->module->name,
                'translateLinks' => $translateLinks
            )
        );
        $modal_content = $this->context->smarty->fetch('controllers/modules/modal_translation.tpl');
        $this->modals[] = array(
            'modal_id' => 'moduleTradLangSelect',
            'modal_class' => 'modal-sm',
            'modal_title' => $this->l('Translate this module'),
            'modal_content' => $modal_content
        );
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();
        unset($this->toolbar_btn['new']);
    }

    public function postProcess()
    {
        parent::postProcess();
        $data = array();
        if (Tools::getIsset('status' . $this->table)) {
            $idValue = Tools::getValue($this->identifier);
            $classObject = new $this->className($idValue);
            $objCustomer = new Customer($classObject->id_customer);
            $data['{customer_name}'] = $objCustomer->firstname . ' ' . $objCustomer->lastname;
            $data['{message}'] = $objCustomer->firstname . ' ' . $objCustomer->lastname;
            $data['{email}'] = $objCustomer->email;
            $data['{company}'] = $objCustomer->company;
            $data['{login_link}'] = $this->context->link->getPageLink('authentication', true, null, ['create_account' => '1']);
            if ($idValue && Validate::isLoadedObject($classObject)) {
                $customer = new Customer($classObject->id_customer);
                $customer->active = $customer->active ? 0 : 1;
                if ($customer->update()) {
                    if ($customer->active) {
                        $registrationConfigure = $this->module->getModuleConfiguration();
                        if ($registrationConfigure->send_email_notification_to_customer) {
                            $this->module->sendMailToCustomerForAdminApproved($data, $objCustomer->email);
                        }
                    }
                }
                Tools::redirect($this->context->link->getAdminLink('AdminPrestaManageBtwoBCustomer') . '&conf=4');
            }

        } elseif ($this->display === 'view') {
            $idValue = Tools::getValue($this->identifier);
            $classObject = new $this->className($idValue);
            if ($idValue && Validate::isLoadedObject($classObject)) {
                $href = $this->context->link->getAdminLink(
                    'AdminCustomers',
                    true,
                    array(),
                    array(
                        'id_customer' => $classObject->id_customer,
                        'viewcustomer' => 1
                    )
                );
                Tools::redirectAdmin($href);
            }
        }
    }
}
