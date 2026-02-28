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
require_once _PS_MODULE_DIR_ . 'faqop/controllers/admin/AdminFaqopHookBasicController.php';

class AdminFaqopHookListController extends AdminFaqopHookBasicController
{
    public function initContent()
    {
        $this->content .= $this->module->displayNav('list');
        parent::initContent();
    }

    protected function postValidationBlock($errors = [])
    {
        if ($this->module->helper->checkIfSubmitClicked()) {
            /* Checks custom hook name */
            if (Tools::getValue('hook_name')) {
                if ($this->module->helper->hasBadWordsInHook(Tools::getValue('hook_name'))) {
                    $errors[] = $this->l('Your hook name contains forbidden words');
                }
                if (!Validate::isHookName(Tools::getValue('hook_name'))) {
                    $errors[] = $this->l('Your hook name contains forbidden symbols');
                }
            }
        }

        return parent::postValidationBlock($errors);
    }

    public function setBlockFields($block)
    {
        parent::setBlockFields($block);

        $block->hook_name = Tools::getValue('hook_name');

        $block->all_products = (int) Tools::getValue('all_products');

        $block->select_products_by_id = (int) Tools::getValue('select_products_by_id');
        $block->product_ids = Tools::getValue('product_ids');
        $block->select_products_by_category = (int) Tools::getValue('select_products_by_category');
        if (is_array(Tools::getValue('select_product_categories'))) {
            $block->category_ids_p = implode(',', Tools::getValue('select_product_categories'));
        } else {
            $block->category_ids_p = '';
        }

        if (is_array(Tools::getValue('is_only_default_category'))) {
            $block->is_only_default_category = !empty(Tools::getValue('is_only_default_category')[0]);
        } else {
            $block->is_only_default_category = 0;
        }

        $block->select_products_by_brand = (int) Tools::getValue('select_products_by_brand');
        $block->brand_ids_p = Tools::getValue('brand_ids_p');
        $block->select_products_by_tag = (int) Tools::getValue('select_products_by_tag');
        $block->tag_ids_p = Tools::getValue('tag_ids_p');
        $block->select_products_by_feature = (int) Tools::getValue('select_products_by_feature');
        $block->feature_ids_p = Tools::getValue('feature_ids_p');

        $block->all_categories = (int) Tools::getValue('all_categories');
        $block->category_ids = Tools::getValue('category_ids');
        $block->all_brands = (int) Tools::getValue('all_brands');
        $block->brand_ids = Tools::getValue('brand_ids');
        $block->all_cms_pages = (int) Tools::getValue('all_cms_pages');
        $block->cms_page_ids = Tools::getValue('cms_page_ids');
        $block->all_without = (int) Tools::getValue('all_without');
        if (is_array(Tools::getValue('special_ids'))) {
            $block->special_ids = implode(',', Tools::getValue('special_ids'));
        } else {
            $block->special_ids = '';
        }
    }

    public function getFieldsFormFirst($fields = ['form' => ['legend' => [], 'input' => []]])
    {
        $fields['form']['legend'] = [
            'title' => $this->l('Hook settings'),
            'icon' => 'icon-edit',
        ];

        $factory = new OpFaqModelFactory();
        $item = $factory->createBlock($this->module);

        $product_ids = $item->product_ids;
        $selected_products = (($product_ids != 0) ? $this->module->rep->getProducts($product_ids) : []);

        $category_ids_p = $item->category_ids_p;

        $brand_ids_p = $item->brand_ids_p;
        $selected_brands_p = (($brand_ids_p != 0) ? $this->module->rep->getBrands($brand_ids_p) : []);

        $tag_ids_p = $item->tag_ids_p;
        $selected_tags_p = (($tag_ids_p != 0) ? $this->module->rep->getTags($tag_ids_p) : []);

        $feature_ids_p = $item->feature_ids_p;
        $selected_features_p = (($feature_ids_p != 0) ? $this->module->rep->getFeatures($feature_ids_p) : []);

        $category_ids = $item->category_ids;
        $selected_categories = (($category_ids != 0) ? $this->module->rep->getCategories($category_ids) : []);

        $brand_ids = $item->brand_ids;
        $selected_brands = (($brand_ids != 0) ? $this->module->rep->getBrands($brand_ids) : []);

        $cms_ids = $item->cms_page_ids;
        $selected_cms_pages = (($cms_ids != 0) ? $this->module->rep->getCmsPages($cms_ids) : []);

        $special_ids = $item->special_ids;
        if ($special_ids) {
            $selected_special_pages = explode(',', $special_ids);
        } else {
            $selected_special_pages = [];
        }

        $select_products_by_id = $item->select_products_by_id;
        $select_products_by_category = $item->select_products_by_category;
        $select_products_by_brand = $item->select_products_by_brand;
        $select_products_by_tag = $item->select_products_by_tag;
        $select_products_by_feature = $item->select_products_by_feature;

        $root = Category::getRootCategory();

        $select_categories_p = explode(',', $category_ids_p);

        $categories_p = [];
        foreach ($select_categories_p as $key => $category) {
            $categories_p[] = $category;
        }

        $fields['form']['input'][] = [
            'type' => 'text_hook_op',
            'label' => $this->l('Hook'),
            'name' => 'hook_name',
            'class' => 'fixed-width-xxl textfield-custom',
            'lang' => false,
        ];

        if ($item->hook_name) {
            $fields['form']['input'][] = [
                'type' => 'smarty_hook_op',
                'label' => $this->l('Code for smarty templates'),
                'name' => 'smarty',
            ];
        }

        $fields['form']['input'][] =
            [
                'type' => 'select_op',
                'name' => 'hook_id',
                'index' => 'hook_id',
                'multiple' => false,
                'class' => 'fixed-width-xxl',
                'options' => [
                    'query' => $this->module->helper->getHooks(),
                    'id' => 'id_hook',
                    'name' => 'name',
                    'default' => [
                        'value' => 0,
                        'label' => $this->l('Select hook'),
                    ],
                ],
            ];

        $fields = $this->addPositionToForm($fields, $item);

        $fields['form']['input'][] =
            [
                'type' => 'radio_op',
                'label' => $this->l('Where to display hook'),
                'form_group_class' => 'sub-line-0',
                'name' => 'not_all_pages',
                'values' => [
                    [
                        'id' => 'collapse_select_main',
                        'value' => 0,
                        'label' => $this->l('All pages'),
                    ],

                    [
                        'id' => 'expand_select_main',
                        'value' => 1,
                        'label' => $this->l('Selected pages'),
                    ],
                ],
            ];

        $fields['form']['input'][] =
            [
                'type' => 'radio_op',
                'form_group_class' => 'when-not-all sub-line-1',
                'label' => $this->l('Products'),
                'name' => 'all_products',
                'values' => [
                    [
                        'id' => 'all_products_0',
                        'value' => 0,
                        'label' => $this->l('No products'),
                    ],

                    [
                        'id' => 'all_products_1',
                        'value' => 1,
                        'label' => $this->l('All products'),
                    ],

                    [
                        'id' => 'expand_select_products',
                        'value' => 2,
                        'label' => $this->l('Selected products'),
                    ],
                ],
            ];

        $fields['form']['input'][] =
            [
                'type' => 'checkbox_how_selected',
                'form_group_class' => 'how-products-selected when-not-all-2 sub-line-2',
                'label' => $this->l('Select products'),
                'name' => 'select_products',
                'values' => [
                    [
                        'name' => 'select_products_by_id',
                        'id' => 'select_products_by_id',
                        'label' => $this->l('By product ids'),
                        'val' => $select_products_by_id,
                    ],
                    [
                        'name' => 'select_products_by_category',
                        'id' => 'select_products_by_category',
                        'label' => $this->l('By categories'),
                        'val' => $select_products_by_category,
                    ],
                    [
                        'name' => 'select_products_by_brand',
                        'id' => 'select_products_by_brand',
                        'label' => $this->l('By brands'),
                        'val' => $select_products_by_brand,
                    ],
                    [
                        'name' => 'select_products_by_tag',
                        'id' => 'select_products_by_tag',
                        'label' => $this->l('By tags'),
                        'val' => $select_products_by_tag,
                    ],
                    [
                        'name' => 'select_products_by_feature',
                        'id' => 'select_products_by_feature',
                        'label' => $this->l('By features that products have'),
                        'val' => $select_products_by_feature,
                    ],
                ],
            ];
        $fields['form']['input'][] =
            [
                'type' => 'radio_op',
                'form_group_class' => 'when-not-all sub-line-1',
                'label' => $this->l('Categories'),
                'name' => 'all_categories',
                'values' => [
                    [
                        'id' => 'all_categories_0',
                        'value' => 0,
                        'label' => $this->l('No product categories'),
                    ],

                    [
                        'id' => 'all_categories_1',
                        'value' => 1,
                        'label' => $this->l('All product categories'),
                    ],

                    [
                        'id' => 'expand_product_categories',
                        'value' => 2,
                        'label' => $this->l('Selected product categories'),
                    ],
                ],
            ];
        $fields['form']['input'][] =
            [
                'type' => 'radio_op',
                'form_group_class' => 'when-not-all sub-line-1',
                'label' => $this->l('Brands'),
                'name' => 'all_brands',
                'values' => [
                    [
                        'id' => 'all_brands_0',
                        'value' => 0,
                        'label' => $this->l('No brands'),
                    ],

                    [
                        'id' => 'all_brands_1',
                        'value' => 1,
                        'label' => $this->l('All brands'),
                    ],

                    [
                        'id' => 'expand_brands',
                        'value' => 2,
                        'label' => $this->l('Selected brands'),
                    ],
                ],
            ];
        $fields['form']['input'][] =
            [
                'type' => 'radio_op',
                'form_group_class' => 'when-not-all sub-line-1',
                'label' => $this->l('Cms pages'),
                'name' => 'all_cms_pages',
                'values' => [
                    [
                        'id' => 'all_cms_pages_0',
                        'value' => 0,
                        'label' => $this->l('No CMS pages'),
                    ],

                    [
                        'id' => 'all_cms_pages_1',
                        'value' => 1,
                        'label' => $this->l('All CMS pages (and cms categories)'),
                    ],

                    [
                        'id' => 'expand_cms_pages',
                        'value' => 2,
                        'label' => $this->l('Selected CMS pages'),
                    ],
                ],
            ];
        $fields['form']['input'][] =
            [
                'type' => 'radio_op',
                'form_group_class' => 'when-not-all sub-line-1',
                'label' => $this->l('Pages except products, categories, brands and cms pages'),
                'name' => 'all_without',
                'values' => [
                    [
                        'id' => 'all_special_pages_0',
                        'value' => 0,
                        'label' => $this->l('No exceptive pages'),
                    ],

                    [
                        'id' => 'all_special_pages_1',
                        'value' => 1,
                        'label' => $this->l('All exceptive pages'),
                    ],

                    [
                        'id' => 'expand_special_pages',
                        'value' => 2,
                        'label' => $this->l('Select special pages'),
                    ],
                ],
            ];

        $fields['form']['input'][] =
            [
                'type' => 'op_products',
                'form_group_class' => 'select_products when-not-all-2',
                'name' => 'select_products',
                'descr' => $this->l('Insert product id and click OK (Do NOT press Enter!) 
                Then continue to add other products'),
                'required' => false,
                'selected_products' => $selected_products,
                'selected_products_raw' => $product_ids,
            ];

        $fields['form']['input'][] =
            [
                'type' => 'categories',
                'form_group_class' => 'select_product_categories when-not-all-2',
                'name' => 'select_product_categories',
                'required' => false,
                'tree' => [
                    'id' => 'select_product_categories',
                    'selected_categories' => $categories_p,
                    'root_category' => (int) $root->id,
                    'use_search' => true,
                    'use_checkbox' => true,
                ],
            ];

        $fields['form']['input'][] =
            [
                'type' => 'op_brands_p',
                'form_group_class' => 'select_brands_p when-not-all-2',
                'name' => 'select_brands_p',
                'descr' => $this->l('Insert brand id and click OK (Do NOT press Enter!) 
                Then continue to add other brands'),
                'required' => false,
                'selected_brands_p' => $selected_brands_p,
                'selected_brands_p_raw' => $brand_ids_p,
            ];

        $fields['form']['input'][] =
            [
                'type' => 'op_tags_p',
                'form_group_class' => 'select_tags_p when-not-all-2',
                'name' => 'select_tags_p',
                'descr' => $this->l('Insert tag id and click OK (Do NOT press Enter!) 
                Then continue to add other tags'),
                'required' => false,
                'selected_tags_p' => $selected_tags_p,
                'selected_tags_p_raw' => $tag_ids_p,
            ];

        $fields['form']['input'][] =
            [
                'type' => 'op_features_p',
                'form_group_class' => 'select_features_p when-not-all-2',
                'name' => 'select_features_p',
                'descr' => $this->l('Insert feature id and click OK (Do NOT press Enter!) 
                Then continue to add other features'),
                'required' => false,
                'selected_features_p' => $selected_features_p,
                'selected_features_p_raw' => $feature_ids_p,
            ];

        $fields['form']['input'][] =
            [
                'type' => 'op_categories',
                'form_group_class' => 'select_categories when-not-all-2',
                'name' => 'select_categories',
                'descr' => $this->l('Insert category id and click "OK" (Do NOT press Enter!) Then continue 
                to add other categories'),
                'required' => false,
                'selected_categories' => $selected_categories,
                'selected_categories_raw' => $category_ids,
            ];

        $fields['form']['input'][] =
            [
                'type' => 'op_brands',
                'form_group_class' => 'select_brands when-not-all-2',
                'name' => 'select_brands',
                'descr' => $this->l('Insert brand id and click OK (Do NOT press Enter!) 
                Then continue to add other brands'),
                'required' => false,
                'selected_brands' => $selected_brands,
                'selected_brands_raw' => $brand_ids,
            ];

        $fields['form']['input'][] =
            [
                'type' => 'op_cms_pages',
                'form_group_class' => 'select_cms_pages when-not-all-2',
                'name' => 'select_cms_pages',
                'descr' => $this->l('Insert CMS page id and click "OK" (Do NOT press Enter!) Then continue 
                to add other pages'),
                'required' => false,
                'selected_cms_pages' => $selected_cms_pages,
                'selected_cms_pages_raw' => $cms_ids,
            ];

        $special_pages =
            [
                'addresses' => $this->l('My addresses'),
                'authentication' => $this->l('Authentication'),
                'best-sales' => $this->l('Best Sales'),
                'cart' => $this->l('Cart'),
                'contact' => $this->l('Contacts page'),
                'discount' => $this->l('My discount'),
                'history' => $this->l('History of orders'),
                'identity' => $this->l('My personal information'),
                'index' => $this->l('Home page'),
                'manufacturer' => $this->l('Manufacturers (brands) page'),
                'my-account' => $this->l('My account page'),
                'new-products' => $this->l('New products'),
                'order' => $this->l('Order page'),
                'order-confirmation' => $this->l('Order Confirmation'),
                'order-follow' => $this->l('Order Follow'),
                'order-slip' => $this->l('Credit Slip'),
                'pagenotfound' => $this->l('404 page'),
                'prices-drop' => $this->l('Prices Drop page'),
                'search' => $this->l('Search results'),
                'supplier' => $this->l('Suppliers page'),
            ];

        $special_pages_array = [];

        foreach ($special_pages as $key => $explain) {
            $special_pages_array[$key] = [
                'id' => $key,
                'label' => $explain . ' (' . $key . ')',
                'checked' => 0,
            ];
        }

        if (sizeof($selected_special_pages) > 0) {
            foreach ($selected_special_pages as $page) {
                $special_pages_array[$page]['checked'] = 1;
            }
        }

        $fields['form']['input'][] =
            [
                'type' => 'special_pages_checkbox',
                'form_group_class' => 'select_special_pages checkbox-block-custom when-not-all-2',
                'name' => 'special_ids[]',
                'values' => $special_pages_array,
            ];

        $is_only_default_category = [
            'id' => 1,
            'label' => $this->l('Apply to only products\' default categories'),
            'checked' => $item->is_only_default_category,
        ];
        $fields['form']['input'][] =
            [
                'type' => 'is_only_default_category_checkbox',
                'form_group_class' => 'is_only_default_category checkbox-block-custom when-not-all-customer_groups',
                'name' => 'is_only_default_category[]',
                'value' => $is_only_default_category,
            ];

        $fields = $this->addLangCurToForm($fields, $item);

        return $fields;
    }

    public function getAddBlockFieldsValues($block, $fields = [])
    {
        $fields = parent::getAddBlockFieldsValues($block);
        $fields['hook_name'] = Tools::getValue('hook_name', $block->hook_name);
        $fields['smarty'] = Tools::getValue('hook_name', $block->hook_name);

        try {
            $fields['hook_id'] = $this->module->helper->getHookIdByName($fields['hook_name']);
        } catch (PrestaShopDatabaseException $e) {
            echo $e->getMessage();
        }

        $fields['all_products'] = Tools::getValue('all_products', $block->all_products);
        $fields['select_products_by_id'] = Tools::getValue('select_products_by_id', $block->select_products_by_id);
        $fields['select_products_by_category'] = Tools::getValue(
            'select_products_by_category',
            $block->select_products_by_category
        );

        $fields['select_products_by_brand'] = Tools::getValue(
            'select_products_by_brand',
            $block->select_products_by_brand
        );
        $fields['select_products_by_tag'] = Tools::getValue(
            'select_products_by_tag',
            $block->select_products_by_tag
        );
        $fields['select_products_by_feature'] = Tools::getValue(
            'select_products_by_feature',
            $block->select_products_by_feature
        );

        $fields['all_categories'] = Tools::getValue('all_categories', $block->all_categories);
        $fields['all_brands'] = Tools::getValue('all_brands', $block->all_brands);
        $fields['all_cms_pages'] = Tools::getValue('all_cms_pages', $block->all_cms_pages);
        $fields['all_without'] = Tools::getValue('all_without', $block->all_without);

        return $fields;
    }
}
