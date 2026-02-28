<?php
/**
 * Copyright ETS Software Technology Co., Ltd
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
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */

if (!defined('_PS_VERSION_')) { exit; }
require_once dirname(__FILE__) . '/AdminEtsRVBaseController.php';

class AdminEtsRVReviewCriteriaController extends AdminEtsRVBaseController
{
    private static $types = array();

    public function __construct()
    {
        $this->table = 'ets_rv_product_comment_criterion';
        $this->className = 'EtsRVProductCommentCriterion';
        $this->identifier = 'id_ets_rv_product_comment_criterion';

        $this->allow_export = false;
        $this->_redirect = false;
        $this->list_no_link = true;
        $this->lang = true;

        parent::__construct();

        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->bulk_actions = array();

        $this->_select = 'a.`id_ets_rv_product_comment_criterion`, a.id_product_comment_criterion_type, b.`name`, a.active';

        $this->_filter = 'AND deleted = 0';

        if (!self::$types)
            self::$types = EtsRVProductCommentCriterion::getTypes();

        $this->fields_list = array(
            'id_ets_rv_product_comment_criterion' => array(
                'title' => $this->l('ID', 'AdminEtsRVReviewCriteriaController'),
                'type' => 'text',
                'align' => 'ets-rv-id_ets_rv_product_comment_criterion',
                'class' => 'ets-rv-id_ets_rv_product_comment_criterion',
            ),
            'name' => array(
                'title' => $this->l('Criterion name', 'AdminEtsRVReviewCriteriaController'),
                'type' => 'text',
                'align' => 'ets-rv-name',
                'class' => 'ets-rv-name',
            ),
            'id_product_comment_criterion_type' => array(
                'title' => $this->l('Type', 'AdminEtsRVReviewCriteriaController'),
                'type' => 'select',
                'list' => self::$types,
                'filter_key' => 'a!id_product_comment_criterion_type',
                'callback' => 'buildFieldTypeName',
                'align' => 'ets-rv-id_product_comment_criterion_type',
                'class' => 'ets-rv-id_product_comment_criterion_type',
            ),
            'active' => array(
                'title' => $this->l('Status', 'AdminEtsRVReviewCriteriaController'),
                'active' => 'status',
                'class' => 'ets-rv-active text-center',
                'type' => 'bool',
                'align' => 'ets-rv-active',
            ),
        );
    }

    public function setHelperDisplay(Helper $helper)
    {
        parent::setHelperDisplay($helper);

        $helper->bulk_actions = array();
        return $helper;
    }

    public function buildFieldTypeName($value)
    {
        if (self::$types && !empty(self::$types[$value])) {
            return self::$types[$value];
        }
    }

    public function initCategoriesAssociation($id_root = null, $id_criterion = 0)
    {
        if (is_null($id_root)) {
            $id_root = Configuration::get('PS_ROOT_CATEGORY');
        }
        $id_shop = (int)Tools::getValue('id_shop');
        $shop = new Shop($id_shop);
        if ($id_criterion == 0) {
            $selected_cat = array();
        } else {
            $pdc_object = new EtsRVProductCommentCriterion($id_criterion);
            $selected_cat = $pdc_object->getCategories();
        }

        if (Shop::getContext() == Shop::CONTEXT_SHOP && Tools::isSubmit('id_shop')) {
            $root_category = new Category($shop->id_category);
        } else {
            $root_category = new Category($id_root);
        }
        $root_category = array('id_category' => $root_category->id, 'name' => $root_category->name[$this->context->language->id]);

        $helper = new Helper();

        return $helper->renderCategoryTree($root_category, $selected_cat, 'categoryBox', false, true);
    }

    public function renderForm()
    {
        $id_criterion = (int)Tools::getValue('id_ets_rv_product_comment_criterion');

        $types = self::$types;
        $query = array();
        foreach ($types as $key => $value) {
            $query[] = array(
                'id' => $key,
                'label' => $value,
            );
        }

        $criterion = new EtsRVProductCommentCriterion((int)$id_criterion);
        $selected_categories = $criterion->getCategories();

        $product_table_values = Product::getSimpleProducts($this->context->language->id);
        $selected_products = $criterion->getProducts();
        foreach ($product_table_values as $key => $product) {
            if (false !== array_search($product['id_product'], $selected_products)) {
                $product_table_values[$key]['selected'] = 1;
            }
        }

        if (version_compare(_PS_VERSION_, '1.6', '<')) {
            $field_category_tree = array(
                'type' => 'categories_select',
                'name' => 'categoryBox',
                'label' => $this->l('Criterion will be restricted to the following categories', 'AdminEtsRVReviewCriteriaController'),
                'category_tree' => $this->initCategoriesAssociation(null, $id_criterion),
            );
        } else {
            $field_category_tree = array(
                'type' => 'categories',
                'label' => $this->l('Criterion will be restricted to the following categories', 'AdminEtsRVReviewCriteriaController'),
                'name' => 'categoryBox',
                'desc' => $this->l('Mark the boxes of categories to which this criterion applies.', 'AdminEtsRVReviewCriteriaController'),
                'tree' => array(
                    'use_search' => false,
                    'id' => 'categoryBox',
                    'use_checkbox' => true,
                    'selected_categories' => $selected_categories,
                ),
                //retro compat 1.5 for category tree
                'values' => array(
                    'trads' => array(
                        'Root' => Category::getTopCategory(),
                        'selected' => $this->l('Selected', 'AdminEtsRVReviewCriteriaController'),
                        'Collapse All' => $this->l('Collapse All', 'AdminEtsRVReviewCriteriaController'),
                        'Expand All' => $this->l('Expand All', 'AdminEtsRVReviewCriteriaController'),
                        'Check All' => $this->l('Check All', 'AdminEtsRVReviewCriteriaController'),
                        'Uncheck All' => $this->l('Uncheck All', 'AdminEtsRVReviewCriteriaController'),
                    ),
                    'selected_cat' => $selected_categories,
                    'input_name' => 'categoryBox[]',
                    'use_radio' => false,
                    'use_search' => false,
                    'disabled_categories' => array(),
                    'top_category' => Category::getTopCategory(),
                    'use_context' => true,
                ),
            );
        }

        $fields_form_1 = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->object->id ? sprintf($this->l('Edit #%s criterion', 'AdminEtsRVReviewCriteriaController'), $this->object->id) : $this->l('Add new criterion', 'AdminEtsRVReviewCriteriaController'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'hidden',
                        'name' => 'id_ets_rv_product_comment_criterion',
                    ),
                    array(
                        'type' => 'text',
                        'lang' => true,
                        'label' => $this->l('Criterion name', 'AdminEtsRVReviewCriteriaController'),
                        'name' => 'name',
                        'desc' => sprintf($this->l('Maximum length: %s characters', 'AdminEtsRVReviewCriteriaController'), EtsRVProductCommentCriterion::NAME_MAX_LENGTH),
                        'required' => true,
                    ),
                    array(
                        'type' => 'select',
                        'name' => 'id_product_comment_criterion_type',
                        'label' => $this->l('Application scope of the criterion', 'AdminEtsRVReviewCriteriaController'),
                        'options' => array(
                            'query' => $query,
                            'id' => 'id',
                            'name' => 'label',
                        ),
                    ),
                    $field_category_tree,
                    array(
                        'type' => 'products',
                        'label' => $this->l('The criterion will be restricted to the following products', 'AdminEtsRVReviewCriteriaController'),
                        'name' => 'ids_product',
                        'values' => $product_table_values,
                    ),
                    array(
                        'type' => 'switch',
                        'is_bool' => true, //retro compat 1.5
                        'label' => $this->l('Active', 'AdminEtsRVReviewCriteriaController'),
                        'name' => 'active',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled', 'AdminEtsRVReviewCriteriaController'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled', 'AdminEtsRVReviewCriteriaController'),
                            ),
                        ),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save', 'AdminEtsRVReviewCriteriaController'),
                    'class' => 'btn btn-default pull-right',
                    'name' => 'submitAdd' . $this->table,
                ),
                'buttons' => array(
                    'back' => array(
                        'href' => self::$currentIndex . '&token=' . $this->token,
                        'title' => $this->l('Cancel', 'AdminEtsRVReviewCriteriaController'),
                        'icon' => 'process-icon-cancel',
                        'class' => 'ets_rv_cancel'
                    ),
                )
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->module = $this->module;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitAdd' . $this->table;
        $helper->currentIndex = self::$currentIndex;
        $helper->token = $this->token;
        $helper->show_cancel_button = false;
        $helper->tpl_vars = array(
            'fields_value' => $this->getCriterionFieldsValues($id_criterion),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($fields_form_1));
    }

    public function getCriterionFieldsValues($id = 0)
    {
        $criterion = new EtsRVProductCommentCriterion($id);

        return array(
            'name' => $criterion->name,
            'id_product_comment_criterion_type' => $criterion->id_product_comment_criterion_type,
            'active' => $criterion->active,
            'id_ets_rv_product_comment_criterion' => $criterion->id,
        );
    }

    public function processSave()
    {
        $criterion = new EtsRVProductCommentCriterion((int)Tools::getValue('id_ets_rv_product_comment_criterion'));
        $criterion->id_product_comment_criterion_type = (int)Tools::getValue('id_product_comment_criterion_type');
        $criterion->active = (int)Tools::getValue('active') ? 1 : 0;

        $languages = Language::getLanguages();
        $name = array();
        foreach ($languages as $value) {
            $name[$value['id_lang']] = Tools::getValue('name_' . $value['id_lang']);
        }
        $criterion->name = $name;

        if (!$criterion->validateFields(false) || !$criterion->validateFieldsLang(false)) {
            $this->errors[] = $this->l('The criterion cannot be saved', 'AdminEtsRVReviewCriteriaController');
        } else {
            $criterion->save();
            // Clear before reinserting data
            $criterion->deleteCategories();
            $criterion->deleteProducts();
            if ($criterion->id_product_comment_criterion_type == 2) {
                if ($categories = Tools::getValue('categoryBox')) {
                    if (is_array($categories) && count($categories)) {
                        foreach ($categories as $id_category) {
                            $criterion->addCategory((int)$id_category);
                        }
                    }
                }
            } elseif ($criterion->id_product_comment_criterion_type == 3) {
                if ($products = Tools::getValue('ids_product')) {
                    if (!is_array($products))
                        $products = explode(',', $products);
                    if (is_array($products) && count($products)) {
                        foreach ($products as $product) {
                            $criterion->addProduct((int)$product);
                        }
                    }
                }
            }
            if (!$criterion->save()) {
                $this->errors[] = $this->l('The criterion cannot be saved', 'AdminEtsRVReviewCriteriaController');
            }
        }
        if (!count($this->errors))
            $this->confirmations = $this->_conf[4];
    }

    public function processDelete()
    {
        $productCommentCriterion = new EtsRVProductCommentCriterion((int)Tools::getValue('id_ets_rv_product_comment_criterion'));
        if ($productCommentCriterion->id) {
            if (!$productCommentCriterion->delete()) {
                $this->errors[] = $this->l('Criterion has been deleted', 'AdminEtsRVReviewCriteriaController');
            }
        }
        if (!count($this->errors))
            $this->confirmations = $this->_conf[2];
    }

    public function processStatus()
    {
        $criterion = new EtsRVProductCommentCriterion((int)Tools::getValue('id_ets_rv_product_comment_criterion'));
        if ($criterion->id) {
            $criterion->active = (int)(!$criterion->active);
            if (!$criterion->save())
                $this->errors[] = $this->l('An error occurred while updating the status', 'AdminEtsRVReviewCriteriaController');
        }
        if (!$this->errors)
            $this->confirmations = $this->_conf[5];
    }
}