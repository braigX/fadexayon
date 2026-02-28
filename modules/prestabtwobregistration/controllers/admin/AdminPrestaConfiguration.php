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
class AdminPrestaConfigurationController extends ModuleAdminController
{
    private $tabClassName = 'AdminPrestaConfiguration';

    public function __construct()
    {
        $this->identifier = 'id_presta_btwob_registration_configuration';
        parent::__construct();
        $this->table = 'presta_btwob_registration_configuration';
        $this->className = 'PrestaBtwoBRegistrationConfiguration';
        $this->bootstrap = true;
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
                $this->toolbar_title[] = $this->l('Configuration');
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

    public function renderList()
    {
       return $this->renderForm();
    }

    public function renderForm()
    {
        $idLang = $this->context->language->id;
        $groups = Group::getGroups($idLang);
        foreach ($groups as $key => $group) {
            if ($group['id_group'] == 1 || $group['id_group'] == 2) {
                unset($groups[$key]);
            }
        }
        $idConfig = (int) PrestaBtwoBRegistrationConfiguration::getConfigId();
        if ($idConfig) {
            $configRule = new PrestaBtwoBRegistrationConfiguration($idConfig);
            $selectedGroups = json_decode($configRule->selected_groups);
            if ($selectedGroups == false) {
                $selectedGroups = array();
            }
            if (Validate::isLoadedObject($configRule)) {
                $this->context->smarty->assign(
                    array(
                        'selectedGroups' => $selectedGroups,
                        'presta_config' => $configRule,
                        'languages' => Language::getLanguages(false),
                    )
                );
            }
        }

        $this->context->smarty->assign(
            array(
                'languages' => Language::getLanguages(false),
                'current_lang' => $this->context->language,
                'groups' => $groups,
                'presta_module_dir' => _MODULE_DIR_ . $this->module->name,
                'presta_customer_group'=> $groups,
                'presta_shop_email' => Configuration::get('PS_SHOP_EMAIL'),
            )

        );
        $this->fields_form = array(
            'submit' => array(
                'name' => 'submit'
            )
        );
        return parent::renderForm();
    }

    public function postProcess()
    {
        $psDefaultLang = Configuration::get('PS_LANG_DEFAULT');
        $objDefaultLang = Language::getLanguage((int) $psDefaultLang);
        $languages = Language::getLanguages(true);
        if (Tools::isSubmit('prestabtwobregistrationconfigurationbtn')) {
            $enableGroupSelectionOne = Tools::getValue('presta_enable_group_selection_one');
            $enableGroupSelection = Tools::getValue('presta_enable_group_selection');
            $customerGroup =Tools::getValue('presta_selected_groups_conf');
            $assignGroups = Tools::getValue('presta_assign_groups');
            $enableRecaptcha = Tools::getValue('presta_enable_google_recaptcha');
            $recaptchaType = Tools::getValue('presta_recaptcha_type');
            $siteKey = trim(Tools::getValue('presta_site_key'));
            $secretKey = trim(Tools::getValue('presta_secret_key'));
            if ($enableGroupSelection && $enableGroupSelectionOne) {
                if (!$customerGroup) {
                    $this->errors[] =$this->l('Select Group should not be empty.');
                }
            }
            if ($enableRecaptcha && $recaptchaType == 1) {
                if (!$siteKey || $siteKey == '') {
                    $this->errors[] = $this->l('Google reCaptcha site key is required');
                }
                if (!$secretKey || $secretKey == '') {
                    $this->errors[] = $this->l('Google reCaptcha secret key is required');
                }
            }
            if (empty($this->errors)) {
                $id = (int) Tools::getValue($this->identifier);
                $objconfiguration = new $this->className();
                if ($id) {
                    $objconfiguration = new $this->className($id);
                }
                // LANG FIELDS
                $topTextLinkDefaulLang = trim(Tools::getValue('presta_top_link_text_' . $psDefaultLang));
                $personalDataHeadingDefaulLang = trim(Tools::getValue(
                    'presta_personal_data_heading_' . $psDefaultLang)
                );
                $addressDataHeadingDefaulLang = trim(Tools::getValue('presta_address_data_heading_' . $psDefaultLang));
                $customFieldHeadingDefaulLang = trim(Tools::getValue('presta_custom_field_heading_' . $psDefaultLang));
                $pendingAccountMessageTextDefaulLang = trim(Tools::getValue(
                    'presta_pending_account_message_text_' . $psDefaultLang)
                );
                foreach ($languages as $lang) {
                    $topTextLink = trim(Tools::getValue('presta_top_link_text_'.$lang['id_lang']));
                    $personalDataHeading = trim(Tools::getValue('presta_personal_data_heading_' . $lang['id_lang']));
                    $addressDataHeading = trim(Tools::getValue('presta_address_data_heading_' . $lang['id_lang']));
                    $customFieldHeading = trim(Tools::getValue('presta_custom_field_heading_' . $lang['id_lang']));
                    $pendingAccountMessageText = trim(Tools::getValue(
                        'presta_pending_account_message_text_'.$lang['id_lang'])
                    );
                    if (!$topTextLink) {
                        $topTextLink = $topTextLinkDefaulLang;
                    }
                    if (!$personalDataHeading) {
                        $personalDataHeading = $personalDataHeadingDefaulLang;
                    }
                    if (!$addressDataHeading) {
                        $addressDataHeading = $addressDataHeadingDefaulLang;
                    }
                    if (!$customFieldHeading) {
                        $customFieldHeading = $customFieldHeadingDefaulLang;
                    }
                    if (!$pendingAccountMessageText) {
                        $pendingAccountMessageText = $pendingAccountMessageTextDefaulLang;
                    }
                    $objconfiguration->top_link_text[$lang['id_lang']] = $topTextLink;
                    $objconfiguration->personal_data_heading[$lang['id_lang']] = $personalDataHeading;
                    $objconfiguration->address_data_heading[$lang['id_lang']] = $addressDataHeading;
                    $objconfiguration->custom_field_heading[$lang['id_lang']] = $customFieldHeading;
                    $objconfiguration->pending_account_message_text[$lang['id_lang']] = $pendingAccountMessageText;
                }
                // default setting
                $objconfiguration->enable_b2b = Tools::getValue('presta_enable_b2b');
                $objconfiguration->b2b_customer_auto_approval = Tools::getValue('presta_b2b_customer_auto_approval');
                $objconfiguration->enable_custom_fields = Tools::getValue('presta_enable_custom_fields');
                $objconfiguration->enable_group_selection_one = $enableGroupSelectionOne;
                if ($enableGroupSelectionOne == 1) {
                    $objconfiguration->enable_group_selection = $enableGroupSelection;
                    $objconfiguration->selected_groups = json_encode($customerGroup);
                    $objconfiguration->assign_groups = $assignGroups;
                } else {
                    $objconfiguration->enable_group_selection = '';
                    $objconfiguration->selected_groups = '';
                    $objconfiguration->assign_groups = '';
                }
                $objconfiguration->date_of_birth = Tools::getValue('presta_date_of_birth');
                $objconfiguration->identification_siret_number = Tools::getValue('presta_identification_siret_number');
                $objconfiguration->address = Tools::getValue('presta_address');
                $objconfiguration->vat_number = Tools::getValue('presta_vat_number');
                $objconfiguration->required_vat_number = Tools::getValue('required_vat_number');
                $objconfiguration->vat_validation = Tools::getValue('presta_vat_validation');
                $objconfiguration->address_complement = Tools::getValue('presta_address_complement');
                $objconfiguration->phone = Tools::getValue('presta_phone');
                // general setting
                $objconfiguration->disable_normal_registration = Tools::getValue('presta_disable_normal_registration');
                $objconfiguration->customer_edit = Tools::getValue('presta_allow_customer_edit');
                $objconfiguration->send_email_notification_admin = Tools::getValue(
                    'presta_send_email_notification_admin'
                );
                $objconfiguration->admin_email_id = Tools::getValue('presta_admin_email_id');
                $objconfiguration->send_email_notification_to_customer = Tools::getValue(
                    'presta_send_email_notification_to_customer'
                );
                $objconfiguration->enable_google_recaptcha = $enableRecaptcha;
                $objconfiguration->recaptcha_type = $recaptchaType;
                $objconfiguration->site_key = $siteKey;
                $objconfiguration->secret_key = $secretKey;
                if ($objconfiguration->save()) {
                    if ($objconfiguration->enable_b2b) {
                        Configuration::updateValue('PS_B2B_ENABLE', 1);
                    } else {
                        Configuration::updateValue('PS_B2B_ENABLE', 0);
                    }
                }
                Tools::redirectAdmin(
                    self::$currentIndex . '&conf=6&token=' . $this->token
                );
            }
             parent::postProcess();
         }
    }

    public function setMedia($isNewTheme = false)
    {
        if ($isNewTheme || !$isNewTheme) {
            parent::setMedia(false);
            $this->context->controller->addJs(
                _MODULE_DIR_ . $this->module->name . '/views/js/admin/presta_configuration.js'
            );
            $this->context->controller->addJs(
                array(
                    _PS_JS_DIR_ . 'tiny_mce/tiny_mce.js',
                    _PS_JS_DIR_ . 'admin/tinymce.inc.js',
                )
            );
        }
    }
}
