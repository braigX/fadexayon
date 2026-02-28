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

require_once _PS_MODULE_DIR_ . 'faqop/classes/helpers/HelperFaq.php';

class FrontHelperFaq extends HelperFaq
{
    public function getContentByShortCode($html)
    {
        preg_match_all('/\{(' . ConfigsFaq::SHORTCODE_NAME . ':)(.*?)\}/', $html, $matches);

        $replacementText = '';

        if (isset($matches[2]) && $matches[2]) {
            foreach ($matches[2] as $block_id) {
                $row = $this->module->rep->getContentBlock($block_id, 'list');

                if ($row) {
                    if ($this->determineHookDisplayByCustomerGroupShortcode($row)) {
                        $replacementText = $this->module->fetchBlockFront($row);
                    }
                }

                $originalShortCode = '{' . ConfigsFaq::SHORTCODE_NAME . ':' . $block_id . '}';

                $html = str_replace($originalShortCode, $replacementText, $html);
            }
        }

        return $html;
    }

    public function determineHookDisplayByCustomerGroupShortcode($block)
    {
        if ($block['not_all_customer_groups'] && !$this->isCurrentCustomerGroup($block['customer_groups'])) {
            return false;
        }

        return $this->determineHookDisplayByCurrencyShortcode($block);
    }

    public function determineHookDisplayByCurrencyShortcode($block)
    {
        if ($block['not_all_currencies'] && !$this->isCurrentCurrency($block['currencies'])) {
            return false;
        }

        return true;
    }

    public function determineHookDisplayByPage($block)
    {
        $html = '';
        if (isset($block['not_all_pages'])) {
            if (!$block['not_all_pages'] || $this->pageBindCheck($block)) {
                $html = $this->module->fetchBlockFront($block);
            }
        } else {
            $html = $this->module->fetchBlockFront($block);
        }

        return $html;
    }

    public function isCurrentCurrency($currencies)
    {
        if ($currencies) {
            $current_currency = Validate::isLoadedObject(Context::getContext()->currency) ?
                (int) Context::getContext()->currency->id : (int) Configuration::get('PS_CURRENCY_DEFAULT');
            $cur_array = explode(',', $currencies);
            foreach ($cur_array as $id_cur) {
                if ($id_cur == $current_currency) {
                    return true;
                }
            }
        }

        return false;
    }

    public function isCurrentLanguage($langs, $current_lang)
    {
        if ($langs) {
            $lang_array = explode(',', $langs);
            foreach ($lang_array as $id_lang) {
                if ($id_lang == $current_lang) {
                    return true;
                }
            }
        }

        return false;
    }

    public function isCurrentCustomerGroup($customer_groups)
    {
        if ($customer_groups) {
            $current_group = Group::getCurrent();
            $group_array = explode(',', $customer_groups);
            foreach ($group_array as $id_group) {
                if ($id_group == $current_group->id) {
                    return true;
                }
            }
        }

        return false;
    }

    public function determineHookDisplayByCustomerGroup($block)
    {
        $html = '';
        if (!$block['not_all_customer_groups']) {
            $html .= $this->determineHookDisplayByLanguage($block);
        } elseif ($block['not_all_customer_groups']) {
            if ($block['customer_groups'] && $this->isCurrentCustomerGroup(
                $block['customer_groups']
            )) {
                $html .= $this->determineHookDisplayByLanguage($block);
            }
        }

        return $html;
    }

    public function determineHookDisplayByLanguage($block)
    {
        $html = '';
        if (!$block['not_all_languages']) {
            $html .= $this->determineHookDisplayByCurrency($block);
        } elseif ($block['not_all_languages']) {
            if ($block['languages'] && $this->isCurrentLanguage(
                $block['languages'],
                $block['id_lang']
            )) {
                $html .= $this->determineHookDisplayByCurrency($block);
            }
        }

        return $html;
    }

    public function determineHookDisplayByCurrency($block)
    {
        $html = '';
        if (!$block['not_all_currencies']) {
            $html = $this->determineHookDisplayByPage($block);
        } elseif ($block['not_all_currencies']) {
            if ($block['currencies'] && $this->isCurrentCurrency($block['currencies'])) {
                $html = $this->determineHookDisplayByPage($block);
            }
        }

        return $html;
    }

    public function pageBindCheck($block)
    {
        $controller = Tools::getValue('controller');

        if ($controller == 'product') {
            return $this->processProduct($block);
        } elseif ($controller == 'category') {
            return $this->processCategory($block);
        } elseif ($controller == 'manufacturer') {
            return $this->processBrand($block);
        } elseif ($controller == 'cms') {
            return $this->processCmsPage($block);
        } else {
            return $this->processSpecialPage($block);
        }
    }

    public function processProduct($block)
    {
        if (!$block['all_products']) {
            return false;
        }
        if ((int) $block['all_products'] === 1) {
            return true;
        }
        if ($block['select_products_by_id'] && $block['product_ids']) {
            $product_ids = explode(',', $block['product_ids']);
            $product_ids = array_flip($product_ids);
            if (isset($product_ids[Tools::getValue('id_product')])) {
                return true;
            }
        }
        if ($block['select_products_by_category'] && $block['category_ids_p']) {
            $categories = $this->module->rep->getCategoryIdsByProductId(Tools::getValue('id_product'),
                $block['is_only_default_category']);
            $category_ids = explode(',', $block['category_ids_p']);
            $category_ids = array_flip($category_ids);
            foreach ($categories as $category) {
                if (isset($category_ids[$category['id_category']])) {
                    return true;
                }
            }
        }
        if ($block['select_products_by_brand'] && $block['brand_ids_p']) {
            $productBrand = $this->module->rep->getBrandIdByProductId(Tools::getValue('id_product'));

            $brand_ids = explode(',', $block['brand_ids_p']);
            $brand_ids = array_flip($brand_ids);

            if (isset($brand_ids[$productBrand])) {
                return true;
            }
        }
        if ($block['select_products_by_tag'] && $block['tag_ids_p']) {
            $tagIds = $this->module->rep->getTagIdsByProductId(Tools::getValue('id_product'));

            $tag_ids = explode(',', $block['tag_ids_p']);
            $tag_ids = array_flip($tag_ids);

            foreach ($tagIds as $tag) {
                if (isset($tag_ids[$tag['id_tag']])) {
                    return true;
                }
            }
        }
        if ($block['select_products_by_feature'] && $block['feature_ids_p']) {
            $featureIds = $this->module->rep->getFeatureIdsByProductId(Tools::getValue('id_product'));

            $feature_ids = explode(',', $block['feature_ids_p']);
            $feature_ids = array_flip($feature_ids);

            foreach ($featureIds as $feature) {
                if (isset($feature_ids[$feature['id_feature']])) {
                    return true;
                }
            }
        }

        return false;
    }

    public function processCategory($block)
    {
        if (!$block['all_categories']) {
            return false;
        }
        if ((int) $block['all_categories'] === 1) {
            return true;
        }
        if ($block['category_ids']) {
            $category_ids = explode(',', $block['category_ids']);
            $category_ids = array_flip($category_ids);
            if (isset($category_ids[Tools::getValue('id_category')])) {
                return true;
            }
        }

        return false;
    }

    public function processBrand($block)
    {
        if (!$block['all_brands']) {
            return false;
        }
        if ((int) $block['all_brands'] === 1) {
            return true;
        }
        if ($block['brand_ids']) {
            $brand_ids = explode(',', $block['brand_ids']);
            $brand_ids = array_flip($brand_ids);
            if (isset($brand_ids[Tools::getValue('id_manufacturer')])) {
                return true;
            }
        }

        return false;
    }

    public function processCmsPage($block)
    {
        if (!$block['all_cms_pages']) {
            return false;
        }
        if ((int) $block['all_cms_pages'] === 1) {
            return true;
        }
        if ($block['cms_page_ids']) {
            $cms_page_ids = explode(',', $block['cms_page_ids']);
            $cms_page_ids = array_flip($cms_page_ids);
            if (isset($cms_page_ids[Tools::getValue('id_cms')])) {
                return true;
            }
        }

        return false;
    }

    public function processSpecialPage($block)
    {
        if (!$block['all_without']) {
            return false;
        }
        if ((int) $block['all_without'] === 1) {
            return true;
        }
        if ($block['special_ids']) {
            $special_ids = str_replace('-', '', $block['special_ids']);

            $special_ids = explode(',', $special_ids);
            $special_ids = array_flip($special_ids);
            if (isset($special_ids[Tools::getValue('controller')])) {
                return true;
            }
        }

        return false;
    }

    public function processBlockForFront($block)
    {
        $block['block_tag'] = empty($block['block_tag']) ? 'div' : $block['block_tag'];
        $block['title_tag'] = empty($block['title_tag']) ? 'h2' : $block['title_tag'];
        $block['content_tag'] = empty($block['content_tag']) ? 'div' : $block['content_tag'];
        $block['item_tag'] = empty($block['item_tag']) ? 'div' : $block['item_tag'];
        $block['question_tag'] = empty($block['question_tag']) ? 'div' : $block['question_tag'];
        $block['answer_tag'] = empty($block['answer_tag']) ? 'div' : $block['answer_tag'];
        $block['description_tag'] = empty($block['description_tag']) ? 'div' : $block['description_tag'];

        $block['items'] = json_decode($block['content'], true);

        $markup_items = [];
        foreach ($block['items'] as $key => $item) {
            $markup_items[$key]['question'] = $this->cutBq($item['question']);
            $markup_items[$key]['answer'] = $this->cutBq($item['answer']);
        }

        $block['markup_items'] = $markup_items;

        $block['includeTpl'] = _PS_MODULE_DIR_ . 'faqop/views/templates/hook/markup.tpl';

        if (!$block['accordion']) {
            $block['accordion_toggle'] = '';
            $block['accordion_panel'] = '';
        } else {
            $block['accordion_toggle'] = 'op-accordion-toggle';
            $block['accordion_panel'] = 'op-accordion-panel';
        }

        $block['accordion_wrap'] = '';

        if ($block['accordion'] == 2) {
            $block['accordion_wrap'] = 'op-accordion-collapsable';
        }

        return $block;
    }

    public function cutBq($string)
    {
        return strip_tags(str_replace('"', '', $string));
    }

    public function getTitleText()
    {
        $id_shop = (int) Context::getContext()->shop->id;
        $id_lang = (int) Context::getContext()->language->id;
        $req = 'SELECT opl.`title`
                FROM `' . _DB_PREFIX_ . 'op_faq_pages_lang` opl
                INNER JOIN `' . _DB_PREFIX_ . 'op_faq_pages` op
                USING (id)
                WHERE op.`id_shop` = ' . $id_shop . ' 
                AND opl.id_lang = ' . $id_lang;
        $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($req);

        return $row;
    }
}
