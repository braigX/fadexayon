<?php
/**
 * Google-Friendly FAQ Pages and Lists With Schema Markup module
 *
 * @author    Opossum Dev
 * @copyright Opossum Dev
 * @license   You are just allowed to modify this copy for your own use. You are not allowed
 * to redistribute it. License is permitted for one Prestashop instance only, except for test
 * instances.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'faqop/classes/models/OpFaqModelFactory.php';
require_once _PS_MODULE_DIR_ . 'faqop/controllers/admin/AdminFaqopBasicBlockController.php';

abstract class AdminFaqopHookBasicController extends AdminFaqopBasicBlockController
{
    protected function postValidationBlock($errors = [])
    {
        if ($this->module->helper->checkIfSubmitClicked()) {
            /* Checks position */
            if (!Validate::isInt(Tools::getValue('position')) || (Tools::getValue('position') < 0)) {
                $errors[] = $this->l('Invalid block position.');
            }
        }

        return parent::postValidationBlock($errors);
    }

    public function setBlockFields($block)
    {
        $block->position = (int) Tools::getValue('position');

        $block->not_all_pages = (int) Tools::getValue('not_all_pages');
        $block->not_all_languages = (int) Tools::getValue('not_all_languages');
        if (is_array(Tools::getValue('languages'))) {
            $block->languages = implode(',', Tools::getValue('languages'));
        } else {
            $block->languages = '';
        }
        $block->not_all_currencies = (int) Tools::getValue('not_all_currencies');
        if (is_array(Tools::getValue('currencies'))) {
            $block->currencies = implode(',', Tools::getValue('currencies'));
        } else {
            $block->currencies = '';
        }
        $block->not_all_customer_groups = (int) Tools::getValue('not_all_customer_groups');
        if (is_array(Tools::getValue('customer_groups'))) {
            $block->customer_groups = implode(',', Tools::getValue('customer_groups'));
        } else {
            $block->customer_groups = '';
        }
    }

    public function renderBlockNav($active_url = 'position')
    {
        return parent::renderBlockNav($active_url);
    }

    public function getFieldsFormFirst()
    {
    }

    public function getAddBlockFieldsValues($block, $fields = [])
    {
        $fields['not_all_pages'] = Tools::getValue('not_all_pages', $block->not_all_pages);
        $fields['not_all_languages'] = Tools::getValue('not_all_languages', $block->not_all_languages);
        $fields['not_all_currencies'] = Tools::getValue('not_all_currencies', $block->not_all_currencies);
        $fields['not_all_customer_groups'] = Tools::getValue('not_all_customer_groups',
            $block->not_all_customer_groups);

        return $fields;
    }

    public function addPositionToForm($fields, $item)
    {
        $fields['form']['input'][] =
            [
                'type' => 'number',
                'label' => $this->l('Position'),
                'form_group_class' => 'position-form-group',
                'name' => 'position',
                'desc' => $this->l('Use this on extra occasions. Better change position 
                in common grid with lists. 1)Choose a hook in filter 2) Drag and drop. 3) Position is the last 
                automatically when you create a new list.'),
                'class' => 'fixed-width-sm textfield-custom',
                'lang' => false,
                'value' => Tools::getValue('position', $item->position),
            ];

        return $fields;
    }

    public function addLangCurToForm($fields, $item)
    {
        $language_ids = $item->languages;
        $selected_languages = [];
        if ($language_ids) {
            $selected_languages_first = explode(',', $language_ids);
            foreach ($selected_languages_first as $id_lang) {
                if (Language::getLanguage($id_lang)) {
                    $selected_languages[] = $id_lang;
                }
            }
        }

        $currency_ids = $item->currencies;
        $selected_currencies = [];
        if ($currency_ids) {
            $selected_currencies_first = explode(',', $currency_ids);
            foreach ($selected_currencies_first as $idCurrency) {
                if (Currency::getCurrency($idCurrency)) {
                    $selected_currencies[] = $idCurrency;
                }
            }
        }

        $customer_group_ids = $item->customer_groups;
        $selected_customer_groups = [];
        if ($customer_group_ids) {
            $selected_customer_groups_first = explode(',', $customer_group_ids);
            foreach ($selected_customer_groups_first as $idGroup) {
                if ($this->module->rep->customerGroupExists($idGroup, $this->context->shop->id)) {
                    $selected_customer_groups[] = $idGroup;
                }
            }
        }

        $fields['form']['input'][] =
            [
                'type' => 'radio_op',
                'form_group_class' => 'sub-line-0',
                'label' => $this->l('Languages to display hook'),
                'name' => 'not_all_languages',
                'values' => [
                    [
                        'id' => 'collapse_select_languages',
                        'value' => 0,
                        'label' => $this->l('All languages'),
                    ],

                    [
                        'id' => 'expand_select_languages',
                        'value' => 1,
                        'label' => $this->l('Selected languages'),
                    ],
                ],
            ];

        $fields['form']['input'][] =
            [
                'type' => 'radio_op',
                'form_group_class' => 'sub-line-0',
                'label' => $this->l('Currencies to display hook and shortcode'),
                'name' => 'not_all_currencies',
                'values' => [
                    [
                        'id' => 'collapse_select_currencies',
                        'value' => 0,
                        'label' => $this->l('All currencies'),
                    ],

                    [
                        'id' => 'expand_select_currencies',
                        'value' => 1,
                        'label' => $this->l('Selected currencies'),
                    ],
                ],
            ];

        $fields['form']['input'][] =
            [
                'type' => 'radio_op',
                'form_group_class' => 'sub-line-0',
                'label' => $this->l('Customer groups to display hook and shortcode'),
                'name' => 'not_all_customer_groups',
                'values' => [
                    [
                        'id' => 'collapse_select_customer_groups',
                        'value' => 0,
                        'label' => $this->l('All customer groups'),
                    ],

                    [
                        'id' => 'expand_select_customer_groups',
                        'value' => 1,
                        'label' => $this->l('Selected customer groups'),
                    ],
                ],
            ];

        $language_list = Language::getLanguages(false, $this->context->shop->id);

        $languages_array = [];

        foreach ($language_list as $language) {
            $languages_array[$language['id_lang']] =
                [
                    'id' => $language['id_lang'],
                    'label' => $language['name'],
                    'checked' => 0,
                ];
        }

        if (sizeof($selected_languages) > 0) {
            foreach ($selected_languages as $lang_id) {
                $languages_array[$lang_id]['checked'] = 1;
            }
        }

        $fields['form']['input'][] =
            [
                'type' => 'languages_checkbox',
                'form_group_class' => 'select_languages checkbox-block-custom when-not-all-languages',
                'name' => 'languages[]',
                'values' => $languages_array,
            ];

        $currency_list = Currency::getCurrencies(false, false);

        $currencies_array = [];

        foreach ($currency_list as $currency) {
            $currencies_array[$currency['id_currency']] =
                [
                    'id' => $currency['id_currency'],
                    'label' => $currency['name'],
                    'checked' => 0,
                ];
        }

        if (sizeof($selected_currencies) > 0) {
            foreach ($selected_currencies as $currency_id) {
                $currencies_array[$currency_id]['checked'] = 1;
            }
        }

        $fields['form']['input'][] =
            [
                'type' => 'currencies_checkbox',
                'form_group_class' => 'select_currencies checkbox-block-custom when-not-all-currencies',
                'name' => 'currencies[]',
                'values' => $currencies_array,
            ];

        $customer_group_list = Group::getGroups($this->context->language->id, $this->context->shop->id);

        $customer_groups_array = [];

        foreach ($customer_group_list as $customer_group) {
            $customer_groups_array[$customer_group['id_group']] =
                [
                    'id' => $customer_group['id_group'],
                    'label' => $customer_group['name'],
                    'checked' => 0,
                ];
        }

        if (sizeof($selected_customer_groups) > 0) {
            foreach ($selected_customer_groups as $customer_group_id) {
                $customer_groups_array[$customer_group_id]['checked'] = 1;
            }
        }

        $fields['form']['input'][] =
            [
                'type' => 'customer_groups_checkbox',
                'form_group_class' => 'select_customer_groups checkbox-block-custom when-not-all-customer_groups',
                'name' => 'customer_groups[]',
                'values' => $customer_groups_array,
            ];

        return $fields;
    }
}
