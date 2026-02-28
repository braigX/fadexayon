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
if (!defined('_PS_VERSION_')) {
    exit;
}
class Link extends LinkCore
{
    protected function getLangLink($idLang = null, Context $context = null, $idShop = null)
    {
        $langLink = parent::getLangLink($idLang, $context, $idShop);
        if (!Module::getInstanceByName('ets_seo')) {
            return $langLink;
        }
        if (!$context) {
            $context = Ets_Seo::getContextStatic();
        }
        if (!$idLang) {
            $idLang = $context->language->id;
        }
        if (Language::isMultiLanguageActivated($idShop) && (int) Configuration::get('ETS_SEO_ENABLE_REMOVE_LANG_CODE_IN_URL') && $idLang == (int) Configuration::get('PS_LANG_DEFAULT')) {
            return '';
        }

        return $langLink;
    }

    public function getCategoryLink(
        $category,
        $alias = null,
        $idLang = null,
        $selectedFilters = null,
        $idShop = null,
        $relativeProtocol = false
    ) {
        if (!Module::isEnabled('ets_seo')) {
            return parent::getCategoryLink($category, $alias, $idLang, $selectedFilters, $idShop, $relativeProtocol);
        }
        /** @var \Dispatcher|\DispatcherCore $dispatcher */
        $dispatcher = Dispatcher::getInstance();

        if (!$idLang) {
            $idLang = Ets_Seo::getContextStatic()->language->id;
        }

        $url = $this->getBaseLink($idShop, null, $relativeProtocol) . $this->getLangLink($idLang, null, $idShop);

        // Set available keywords
        $params = [];
        if (Validate::isLoadedObject($category)) {
            $params['id'] = $category->id;
        } elseif (isset($category['id_category'])) {
            $params['id'] = $category['id_category'];
        } elseif (is_int($category) or ctype_digit($category)) {
            $params['id'] = (int) $category;
        } else {
            throw new \InvalidArgumentException('Invalid category parameter');
        }

        // Selected filters is used by the module ps_facetedsearch
        $selectedFilters = null === $selectedFilters ? '' : $selectedFilters;

        if (empty($selectedFilters)) {
            $rule = 'category_rule';
        } else {
            $rule = 'layered_rule';
            $params['selected_filters'] = $selectedFilters;
        }

        if (!$alias) {
            $category = $this->getCategoryObject($category, $idLang);
        }
        $params['rewrite'] = (!$alias) ? $category->link_rewrite : $alias;
        if ($dispatcher->hasKeyword($rule, $idLang, 'parent_rewrite', $idShop)) {
            $params['parent_rewrite'] = '';
            try {
                $cats = [];
                /** @var \Category|\CategoryCore $currentCategory */
                $currentCategory = $this->getCategoryObject($category, $idLang);
                foreach ($currentCategory->getParentsCategories($idLang) as $cat) {
                    if (!in_array($cat['id_category'], [1, 2, $currentCategory->id])) {
                        $cats[] = $cat['link_rewrite'];
                    }
                }
                if (count($cats)) {
                    $params['parent_rewrite'] = implode('/', array_reverse($cats));
                }
            } catch (PrestaShopException $e) {
                // Do nothing
            }
        }
        if ($dispatcher->hasKeyword($rule, $idLang, 'meta_keywords', $idShop)) {
            $category = $this->getCategoryObject($category, $idLang);
            $params['meta_keywords'] = Tools::str2url($category->getFieldByLang('meta_keywords'));
        }
        if ($dispatcher->hasKeyword($rule, $idLang, 'meta_title', $idShop)) {
            $category = $this->getCategoryObject($category, $idLang);
            $params['meta_title'] = Tools::str2url($category->getFieldByLang('meta_title'));
        }

        return $url . $dispatcher->createUrl($rule, $idLang, $params, $this->allow, '', $idShop);
    }
}
