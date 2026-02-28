<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
*/
if (!defined('_PS_VERSION_')) {
    exit;
}

class Bonmarkup extends Module
{
    public function __construct()
    {
        $this->name = 'bonmarkup';
        $this->tab = 'front_office_features';
        $this->version = '2.8.0';
        $this->bootstrap = true;
        $this->author = 'Bonpresta';
        $this->module_key = '294dd989d6cce3ebd58f78d83373951d';
        parent::__construct();
        $this->default_language = Language::getLanguage(Configuration::get('PS_LANG_DEFAULT'));
        $this->id_shop = Context::getContext()->shop->id;
        $this->displayName = $this->l('SEO Schema Markup Structured Data Rich Snippet');
        $this->description = $this->l('Display structured data & schema markup.');
        $this->ps_versions_compliancy = ['min' => '1.6', 'max' => _PS_VERSION_];
    }

    protected function getConfigurations()
    {
        $configurations = [
            'BON_MARKUP_NAME' => 'Fashion Store',
            'BON_MARKUP_TYPE' => 'Organization',
            'BON_MARKUP_DESCRIPTION' => 'Our store sells the best clothes',
            'BON_MARKUP_URL' => 'https://site.com/',
            'BON_MARKUP_TELEPHONE' => '+1-646-350-2789',
            'BON_MARKUP_LOGO' => 'https://site.com/logo.png',
            'BON_MARKUP_EMAIL' => 'site@site.com',
            'BON_MARKUP_ADDRESS_COUNRY' => 'France',
            'BON_MARKUP_ADDRESS_REGION' => 'Paris',
            'BON_MARKUP_ADDRESS_LOCALITY' => 'Paris',
            'BON_MARKUP_ADDRESS_STREET' => 'Vaugirard',
            'BON_MARKUP_ADDRESS_CODE' => 7500,
            'BON_MARKUP_ADDRESS_LATITUDE' => 48.864716,
            'BON_MARKUP_ADDRESS_LONGITUDE' => 2.349014,
            'BON_MARKUP_FACEBOOK' => 'prestashop',
            'BON_MARKUP_INSTAGRAM' => 'prestashop',
            'BON_MARKUP_GOOGLE' => '+Prestashop',
            'BON_MARKUP_TWITTER' => 'prestashop',
            'BON_MARKUP_YOUTUBE' => 'prestashop',
            'BON_MARKUP_LIKEDIN' => 'prestashop',
        ];

        return $configurations;
    }

    public function install()
    {
        $configurations = $this->getConfigurations();

        foreach ($configurations as $name => $config) {
            Configuration::updateValue($name, $config);
        }

        return parent::install() &&
        $this->registerHook('displayHeader');
    }

    public function uninstall()
    {
        $configurations = $this->getConfigurations();

        foreach (array_keys($configurations) as $config) {
            Configuration::deleteByName($config);
        }

        return parent::uninstall();
    }

    public function getContent()
    {
        $output = '';
        $result = '';

        if ((bool) Tools::isSubmit('submitSettings')) {
            if (!$result = $this->preValidateForm()) {
                $output .= $this->postProcess();
                $output .= $this->displayConfirmation($this->l('Save all settings.'));
            } else {
                $output = $result;
                $output .= $this->renderTabForm();
            }
        }

        if (!$result) {
            $output .= $this->renderTabForm();
        }

        return $output;
    }

    protected function renderTabForm()
    {
        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'select',
                        'label' => $this->l('Type:'),
                        'name' => 'BON_MARKUP_TYPE',
                        'options' => [
                            'query' => [
                                [
                                    'id' => 'Organization',
                                    'name' => $this->l('Organization')],
                                [
                                    'id' => 'WebSite',
                                    'name' => $this->l('WebSite')],
                            ],
                            'id' => 'id',
                            'name' => 'name',
                        ],
                    ],
                    [
                        'type' => 'text',
                        'required' => true,
                        'label' => $this->l('Legal Name:'),
                        'name' => 'BON_MARKUP_NAME',
                        'col' => 2,
                    ],
                    [
                        'type' => 'text',
                        'required' => true,
                        'label' => $this->l('Store Description:'),
                        'name' => 'BON_MARKUP_DESCRIPTION',
                        'col' => 2,
                    ],
                    [
                        'type' => 'text',
                        'required' => true,
                        'label' => $this->l('Store URL:'),
                        'name' => 'BON_MARKUP_URL',
                        'col' => 2,
                    ],
                    [
                        'type' => 'text',
                        'required' => true,
                        'label' => $this->l('Store Logo:'),
                        'name' => 'BON_MARKUP_LOGO',
                        'col' => 2,
                    ],
                    [
                        'type' => 'text',
                        'required' => true,
                        'label' => $this->l('Telephone:'),
                        'name' => 'BON_MARKUP_TELEPHONE',
                        'col' => 2,
                    ],
                    [
                        'type' => 'text',
                        'required' => true,
                        'label' => $this->l('Email:'),
                        'name' => 'BON_MARKUP_EMAIL',
                        'col' => 2,
                    ],
                    [
                        'type' => 'text',
                        'required' => true,
                        'label' => $this->l('Address Country:'),
                        'name' => 'BON_MARKUP_ADDRESS_COUNRY',
                        'col' => 2,
                    ],
                    [
                        'type' => 'text',
                        'required' => true,
                        'label' => $this->l('Address Region:'),
                        'name' => 'BON_MARKUP_ADDRESS_REGION',
                        'col' => 2,
                    ],
                    [
                        'type' => 'text',
                        'required' => true,
                        'label' => $this->l('Address Locality:'),
                        'name' => 'BON_MARKUP_ADDRESS_LOCALITY',
                        'col' => 2,
                    ],
                    [
                        'type' => 'text',
                        'required' => true,
                        'label' => $this->l('Address Street:'),
                        'name' => 'BON_MARKUP_ADDRESS_STREET',
                        'col' => 2,
                    ],
                    [
                        'type' => 'text',
                        'required' => true,
                        'label' => $this->l('Postal Code:'),
                        'name' => 'BON_MARKUP_ADDRESS_CODE',
                        'col' => 2,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Latitude:'),
                        'name' => 'BON_MARKUP_ADDRESS_LATITUDE',
                        'col' => 2,
                        'required' => true,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Longitude:'),
                        'name' => 'BON_MARKUP_ADDRESS_LONGITUDE',
                        'col' => 2,
                        'required' => true,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Facebook:'),
                        'name' => 'BON_MARKUP_FACEBOOK',
                        'col' => 2,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Instagram:'),
                        'name' => 'BON_MARKUP_INSTAGRAM',
                        'col' => 2,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Google +:'),
                        'name' => 'BON_MARKUP_GOOGLE',
                        'col' => 2,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Twitter:'),
                        'name' => 'BON_MARKUP_TWITTER',
                        'col' => 2,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Youtube:'),
                        'name' => 'BON_MARKUP_YOUTUBE',
                        'col' => 2,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Linkedin:'),
                        'name' => 'BON_MARKUP_LIKEDIN',
                        'col' => 2,
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ],
        ];

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitSettings';
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = [
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm([$fields_form]);
    }

    public function getConfigFieldsValues()
    {
        $fields = [];
        $configurations = $this->getConfigurations();

        foreach (array_keys($configurations) as $config) {
            $fields[$config] = Configuration::get($config);
        }

        return $fields;
    }

    protected function postProcess()
    {
        $form_values = $this->getConfigFieldsValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }

    protected function preValidateForm()
    {
        $errors = [];

        if (Tools::isEmpty(Tools::getValue('BON_MARKUP_NAME'))) {
            $errors[] = $this->l('The name is required.');
        }

        if (Tools::isEmpty(Tools::getValue('BON_MARKUP_LOGO'))) {
            $errors[] = $this->l('The logo is required.');
        }

        if (Tools::isEmpty(Tools::getValue('BON_MARKUP_DESCRIPTION'))) {
            $errors[] = $this->l('The description is required.');
        }

        if (Tools::isEmpty(Tools::getValue('BON_MARKUP_ADDRESS_COUNRY'))) {
            $errors[] = $this->l('The country is required.');
        }

        if (Tools::isEmpty(Tools::getValue('BON_MARKUP_ADDRESS_REGION'))) {
            $errors[] = $this->l('The region is required.');
        }

        if (Tools::isEmpty(Tools::getValue('BON_MARKUP_ADDRESS_LOCALITY'))) {
            $errors[] = $this->l('The locality is required.');
        }

        if (Tools::isEmpty(Tools::getValue('BON_MARKUP_ADDRESS_STREET'))) {
            $errors[] = $this->l('The street is required.');
        }

        if (Tools::isEmpty(Tools::getValue('BON_MARKUP_ADDRESS_CODE'))) {
            $errors[] = $this->l('The postal code is required.');
        }

        if (Tools::isEmpty(Tools::getValue('BON_MARKUP_URL'))) {
            $errors[] = $this->l('The url is required.');
        } else {
            if (!Validate::isUrl(Tools::getValue('BON_MARKUP_URL'))) {
                $errors[] = $this->l('Bad url format');
            }
        }

        if (Tools::isEmpty(Tools::getValue('BON_MARKUP_TELEPHONE'))) {
            $errors[] = $this->l('The telephone is required.');
        } else {
            if (!Validate::isPhoneNumber(Tools::getValue('BON_MARKUP_TELEPHONE'))) {
                $errors[] = $this->l('Bad telephone format');
            }
        }

        if (Tools::isEmpty(Tools::getValue('BON_MARKUP_EMAIL'))) {
            $errors[] = $this->l('The email is required.');
        } else {
            if (!Validate::isEmail(Tools::getValue('BON_MARKUP_EMAIL'))) {
                $errors[] = $this->l('Bad email format');
            }
        }

        if (Tools::isEmpty(Tools::getValue('BON_MARKUP_ADDRESS_LATITUDE'))) {
            $errors[] = $this->l('The latitude is required.');
        } else {
            if (!Validate::isCoordinate(Tools::getValue('BON_MARKUP_ADDRESS_LATITUDE'))) {
                $errors[] = $this->l('Bad latitude format');
            }
        }

        if (Tools::isEmpty(Tools::getValue('BON_MARKUP_ADDRESS_LONGITUDE'))) {
            $errors[] = $this->l('The longitude is required.');
        } else {
            if (!Validate::isCoordinate(Tools::getValue('BON_MARKUP_ADDRESS_LONGITUDE'))) {
                $errors[] = $this->l('Bad longitude format');
            }
        }

        if (count($errors)) {
            return $this->displayError(implode('<br />', $errors));
        }

        return false;
    }

    public function hookDisplayHeader()
    {
        $this->context->smarty->assign([
            'bon_markup_type' => Configuration::get('BON_MARKUP_TYPE'),
            'bon_markup_name' => Configuration::get('BON_MARKUP_NAME'),
            'bon_markup_url' => Configuration::get('BON_MARKUP_URL'),
            'bon_markup_telephone' => Configuration::get('BON_MARKUP_TELEPHONE'),
            'bon_markup_logo' => Configuration::get('BON_MARKUP_LOGO'),
            'bon_markup_description' => Configuration::get('BON_MARKUP_DESCRIPTION'),
            'bon_markup_email' => Configuration::get('BON_MARKUP_EMAIL'),
            'bon_markup_address_country' => Configuration::get('BON_MARKUP_ADDRESS_COUNRY'),
            'bon_markup_address_region' => Configuration::get('BON_MARKUP_ADDRESS_REGION'),
            'bon_markup_address_locality' => Configuration::get('BON_MARKUP_ADDRESS_LOCALITY'),
            'bon_markup_address_street' => Configuration::get('BON_MARKUP_ADDRESS_STREET'),
            'bon_markup_address_code' => Configuration::get('BON_MARKUP_ADDRESS_CODE'),
            'bon_markup_address_latitude' => Configuration::get('BON_MARKUP_ADDRESS_LATITUDE'),
            'bon_markup_address_longitude' => Configuration::get('BON_MARKUP_ADDRESS_LONGITUDE'),
            'bon_markup_facebook' => Configuration::get('BON_MARKUP_FACEBOOK'),
            'bon_markup_instagram' => Configuration::get('BON_MARKUP_INSTAGRAM'),
            'bon_markup_google' => Configuration::get('BON_MARKUP_GOOGLE'),
            'bon_markup_twitter' => Configuration::get('BON_MARKUP_TWITTER'),
            'bon_markup_youtube' => Configuration::get('BON_MARKUP_YOUTUBE'),
            'bon_markup_likedin' => Configuration::get('BON_MARKUP_LIKEDIN'),
        ]);

        return $this->fetch('module:bonmarkup/views/templates/hook/bonmarkup.tpl');
    }
}
