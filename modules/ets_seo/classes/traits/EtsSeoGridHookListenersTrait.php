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
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

require_once __DIR__ . '/EtsSeoTranslationTrait.php';
/**
 * Trait EtsSeoGridHookListenersTrait
 */
trait EtsSeoGridHookListenersTrait
{
    use EtsSeoTranslationTrait;

    public function actionAdminCategoriesChangeFilter(&$filters, $tblName = null)
    {
        $this->_actionListChangeFilter($filters, false, $tblName);
    }

    public function actionAdminManufacturerChangeFilter(&$filters, $tblName = null)
    {
        $this->_actionListChangeFilter($filters, false, $tblName);
    }

    public function actionAdminSupplierChangeFilter(&$filters, $tblName = null)
    {
        $this->_actionListChangeFilter($filters, false, $tblName);
    }

    public function actionAdminCmsChangeFilter(&$filters, $tblName = null)
    {
        $this->_actionListChangeFilter($filters, false, $tblName);
    }

    public function actionAdminCmsCategoryChangeFilter(&$filters, $tblName = null)
    {
        $this->_actionListChangeFilter($filters, true, $tblName);
    }

    public function actionAdminMetaChangeFilter(&$filters, $tblName = null)
    {
        $this->_actionListChangeFilter($filters, false, $tblName);
    }

    /**
     * actionAdminProductsListingFieldsModifier.
     *
     * @param array $params
     *
     * @return void
     */
    public function hookActionAdminProductsListingFieldsModifier(&$params)
    {
        $filter_seo_score = ($filter_seo_score = Tools::getValue('filter_ets_seo_score', '')) && Validate::isCleanHtml($filter_seo_score) ? $filter_seo_score : '';
        $filter_readability_score = ($filter_readability_score = Tools::getValue('filter_ets_seo_readability', '')) && Validate::isCleanHtml($filter_readability_score) ? $filter_readability_score : '';
        if ($filter_seo_score || $filter_readability_score) {
            if ($filter_seo_score) {
                $sql_select = [
                    'seo_score' => [
                        'table' => 'esp',
                        'field' => 'seo_score',
                        'filtering' => ' %s ',
                    ],
                ];
                $sqlOverAllSeoScore = ' (esp.`seo_score` / (' . ETS_TOTAL_SEO_RULE_SCORE . ' * 9) * 10)';
                if ('bad' == $filter_seo_score) {
                    $params['sql_where'][] = $sqlOverAllSeoScore . '<= 4 ';
                } elseif ('ok' == $filter_seo_score) {
                    $params['sql_where'][] = $sqlOverAllSeoScore . ' > 4 AND ' . $sqlOverAllSeoScore . ' <= 7 ';
                } elseif ('good' == $filter_seo_score) {
                    $params['sql_where'][] = $sqlOverAllSeoScore . ' > 7 ';
                } elseif ('na' == $filter_seo_score) {
                    $params['sql_where'][] = " (esp.`key_phrase` IS NULL OR esp.`key_phrase` = '') ";
                } elseif ('noindex' == $filter_seo_score) {
                    if (!(int) Configuration::get('ETS_SEO_PROD_SHOW_IN_SEARCH_RESULT')) {
                        $params['sql_where'][] = ' (esp.`allow_search` = 0 OR esp.`allow_search` = 2)';
                    } else {
                        $params['sql_where'][] = ' (esp.`allow_search` = 0)';
                    }
                }
            } else {
                $sql_select = [
                    'seo_score' => [
                        'table' => 'esp',
                        'field' => 'seo_score',
                        'filtering' => ' %s ',
                    ],
                    'readability_score' => [
                        'table' => 'esp',
                        'field' => 'readability_score',
                        'filtering' => ' %s ',
                    ],
                ];
                $sqlOverAllReadabilityScore = ' (esp.`readability_score` / (' . ETS_TOTAL_READABILITY_RULE_SCORE . ' * 9) * 10)';
                if ('bad' == $filter_readability_score) {
                    $params['sql_where'][] = $sqlOverAllReadabilityScore . ' <= 4 ';
                } elseif ('ok' == $filter_readability_score) {
                    $params['sql_where'][] = $sqlOverAllReadabilityScore . ' > 4 AND ' . $sqlOverAllReadabilityScore . ' <= 7 ';
                } elseif ('good' == $filter_readability_score) {
                    $params['sql_where'][] = $sqlOverAllReadabilityScore . ' > 7 ';
                }
            }
            $sql_table = [
                'esp' => [
                    'table' => 'ets_seo_product',
                    'join' => 'LEFT JOIN',
                    'on' => 'esp.`id_product` = p.`id_product` AND esp.`id_shop` = ' . (int) $this->context->shop->id . ' AND esp.`id_lang` = ' . (int) $this->context->language->id,
                ],
            ];
            $params['sql_select'] = array_merge($params['sql_select'], $sql_select);
            $params['sql_table'] = array_merge($params['sql_table'], $sql_table);
        }
    }

    protected function _actionListChangeFilter(&$filters, $is_cms_category = false, $tblName = '')
    {
        $controller = ($controller = Tools::getValue('controller')) && Validate::isControllerName($controller) ? $controller : '';

        if ('AdminMeta' == $controller
            || 'AdminCmsContent' == $controller
            || 'AdminCategories' == $controller
            || 'AdminSuppliers' == $controller
            || 'AdminManufacturers' == $controller) {
            $config_name = '';
            switch ($controller) {
                case 'AdminCmsContent':
                    $config_name = $is_cms_category ? 'ETS_SEO_CMS_CATE_SHOW_IN_SEARCH_RESULT' : 'ETS_SEO_CMS_SHOW_IN_SEARCH_RESULT';
                    break;
                case 'AdminMetaContent':
                    $config_name = 'ETS_SEO_META_SHOW_IN_SEARCH_RESULT';
                    break;
                case 'AdminCategoriesContent':
                    $config_name = 'ETS_SEO_CATEGORY_SHOW_IN_SEARCH_RESULT';
                    break;
                case 'AdminManufacturers':
                    $config_name = 'ETS_SEO_MANUFACTURER_SHOW_IN_SEARCH_RESULT';
                    break;
                case 'AdminSuppliers':
                    $config_name = 'ETS_SEO_SUPPLIER_SHOW_IN_SEARCH_RESULT';
                    break;
            }
            if ($tblName) {
                $tblName .= '.';
            }
            $sql = '`allow_search` = 0';
            if (!(int) Configuration::get($config_name)) {
                $sql = '(`allow_search` = 0 OR `allow_search` = 2)';
            }
            $sqlOverAllSeoScore = ' (`seo_score` / (' . ETS_TOTAL_SEO_RULE_SCORE . ' * 9) * 10)';
            $sqlOverAllReadabilityScore = ' (`readability_score` / (' . ETS_TOTAL_READABILITY_RULE_SCORE . ' * 9) * 10)';

            $filters = str_replace("`seo_score` = 'ok'", $sqlOverAllSeoScore . ' > 4 AND ' . $sqlOverAllSeoScore . ' < 7', $filters);
            $filters = str_replace("`seo_score` = 'good'", $sqlOverAllSeoScore . ' > 7', $filters);
            $filters = str_replace("`seo_score` = 'bad'", $sqlOverAllSeoScore . ' <= 4', $filters);
            $filters = str_replace($tblName . "`seo_score` = 'noindex'", $sql, $filters);

            $filters = str_replace($tblName . "`seo_score` = 'na'", "((`key_phrase` IS NULL OR `key_phrase` = '') AND (`minor_key_phrase` IS NULL OR `minor_key_phrase` = ''))", $filters);
            if ('AdminMeta' !== $controller) {
                $filters = str_replace("`readability_score` = 'ok'", $sqlOverAllReadabilityScore . ' > 4 AND ' . $sqlOverAllReadabilityScore . ' <= 7', $filters);
                $filters = str_replace("`readability_score` = 'good'", $sqlOverAllReadabilityScore . ' > 7', $filters);
                $filters = str_replace("`readability_score` = 'bad'", $sqlOverAllReadabilityScore . ' <= 4', $filters);
            }
        }
    }

    /**
     * Modify SQL Query. Add where to GRID select.
     *
     * @param string $type
     *
     * @return void
     */
    protected function _actionGridQueryBuilderModifier($type, array $params)
    {
        $table = 'ets_seo_product';
        $sortName = 'esp';
        $tableJoin = 'p';
        $sqlJoin = '';
        $config_name = '';
        switch ($type) {
            case 'product':
                $table = 'ets_seo_product';
                $sortName = 'esp';
                $config_name = 'ETS_SEO_PROD_SHOW_IN_SEARCH_RESULT';
                break;
            case 'cms':
                $table = 'ets_seo_cms';
                $sortName = 'esc';
                $tableJoin = 'c';
                $config_name = 'ETS_SEO_CMS_SHOW_IN_SEARCH_RESULT';
                $sqlJoin = '(esc.`id_cms` = c.`id_cms` AND esc.`id_shop`=' . (int) $this->context->shop->id . ' AND esc.`id_lang`=cl.`id_lang`)';
                break;
            case 'cms_category':
                $table = 'ets_seo_cms_category';
                $sortName = 'esc';
                $tableJoin = 'cc';
                $config_name = 'ETS_SEO_CMS_CATE_SHOW_IN_SEARCH_RESULT';
                $sqlJoin = '(esc.`id_cms_category` = cc.`id_cms_category` AND esc.`id_shop`=' . (int) $this->context->shop->id . ' AND esc.`id_lang`=ccl.`id_lang`)';
                break;
            case 'meta':
                $table = 'ets_seo_meta';
                $sortName = 'esm';
                $tableJoin = 'm';
                $config_name = 'ETS_SEO_META_SHOW_IN_SEARCH_RESULT';
                $sqlJoin = '(esm.`id_meta` = m.`id_meta` AND esm.`id_shop`=' . (int) $this->context->shop->id . ' AND esm.`id_lang`=l.`id_lang`)';
                break;
            case 'category':
                $table = 'ets_seo_category';
                $sortName = 'esc';
                $tableJoin = 'c';
                $config_name = 'ETS_SEO_CATEGORY_SHOW_IN_SEARCH_RESULT';
                $sqlJoin = '(esc.`id_category` = c.`id_category` AND esc.`id_shop`=' . (int) $this->context->shop->id . ' AND esc.`id_lang`=cl.`id_lang`)';
                break;

            case 'manufacturer':
                $table = 'ets_seo_manufacturer';
                $sortName = 'esc';
                $tableJoin = 'm';
                $config_name = 'ETS_SEO_MANUFACTURER_SHOW_IN_SEARCH_RESULT';
                $sqlJoin = '(esc.`id_manufacturer` = m.`id_manufacturer` AND esc.`id_shop`=' . (int) $this->context->shop->id . ' AND esc.`id_lang`=' . (int) $this->context->language->id . ')';
                break;
            case 'supplier':
                $table = 'ets_seo_supplier';
                $sortName = 'esc';
                $tableJoin = 's';
                $config_name = 'ETS_SEO_SUPPLIER_SHOW_IN_SEARCH_RESULT';
                $sqlJoin = '(esc.`id_supplier` = s.`id_supplier` AND esc.`id_shop`=' . (int) $this->context->shop->id . ' AND esc.`id_lang`=' . (int) $this->context->language->id . ')';
                break;
        }

        if (isset($params['search_query_builder']) && $params['search_query_builder']) {
            $searchQueryBuilder = &$params['search_query_builder'];
            $searchQueryBuilder
                ->addSelect($sortName . '.`seo_score`')
                ->addSelect($sortName . '.`readability_score`')
                ->leftJoin(
                    $tableJoin,
                    _DB_PREFIX_ . $table,
                    $sortName,
                    $sqlJoin
                );
        }

        if (isset($params['count_query_builder']) && $params['count_query_builder']) {
            $countQueryBuilder = &$params['count_query_builder'];
            $countQueryBuilder
                ->leftJoin(
                    $tableJoin,
                    _DB_PREFIX_ . $table,
                    $sortName,
                    $sqlJoin
                );
        }
        $filters = null;
        if ('cms' == $type) {
            if (($cmsPage = Tools::getValue('cms_page', [])) && Ets_Seo::validateArray($cmsPage) && isset($cmsPage['filters'])) {
                $filters = $cmsPage['filters'];
            }
        } elseif ('cms_category' == $type) {
            if (($cmsPage = Tools::getValue('cms_page_category', [])) && Ets_Seo::validateArray($cmsPage) && isset($cmsPage['filters'])) {
                $filters = $cmsPage['filters'];
            }
        } elseif ('manufacturer' == $type) {
            if (($cmsPage = Tools::getValue('manufacturer', [])) && Ets_Seo::validateArray($cmsPage) && isset($cmsPage['filters'])) {
                $filters = $cmsPage['filters'];
            }
        } elseif ('supplier' == $type) {
            if (($cmsPage = Tools::getValue('supplier', [])) && Ets_Seo::validateArray($cmsPage) && isset($cmsPage['filters'])) {
                $filters = $cmsPage['filters'];
            }
        } else {
            $filters = ($filters = Tools::getValue('filters', [])) && Ets_Seo::validateArray($filters) ? $filters : [];
        }
        $sqlOverAllSeoScore = ' (' . (string) $sortName . '.`seo_score` / (' . ETS_TOTAL_SEO_RULE_SCORE . ' * 9) * 10)';
        $sqlOverAllReadabilityScore = ' (' . (string) $sortName . '.`readability_score` / (' . ETS_TOTAL_READABILITY_RULE_SCORE . ' * 9) * 10)';

        if ($filters) {
            if (isset($searchQueryBuilder) && isset($countQueryBuilder) && isset($filters['seo_score']) && $filters['seo_score']) {
                switch ($filters['seo_score']) {
                    case 'bad':
                        $searchQueryBuilder->andWhere($sqlOverAllSeoScore . ' <= 4');
                        $countQueryBuilder->andWhere($sqlOverAllSeoScore . ' <= 4');
                        break;
                    case 'ok':
                        $searchQueryBuilder->andWhere($sqlOverAllSeoScore . ' > 4 AND ' . $sqlOverAllSeoScore . ' <= 7');
                        $countQueryBuilder->andWhere($sqlOverAllSeoScore . ' > 4 AND ' . $sqlOverAllSeoScore . ' <= 7');
                        break;
                    case 'good':
                        $searchQueryBuilder->andWhere($sqlOverAllSeoScore . ' > 7');
                        $countQueryBuilder->andWhere($sqlOverAllSeoScore . ' > 7');
                        break;
                    case 'na':
                        $searchQueryBuilder->andWhere('((' . (string) $sortName . '.`key_phrase` IS NULL OR ' . (string) $sortName . '.`key_phrase` = \'\') AND (' . (string) $sortName . '.`minor_key_phrase` IS NULL OR ' . (string) $sortName . '.`minor_key_phrase` = \'\' ))');
                        $countQueryBuilder->andWhere('((' . (string) $sortName . '.`key_phrase` IS NULL OR ' . (string) $sortName . '.`key_phrase` = \'\') AND (' . (string) $sortName . '.`minor_key_phrase` IS NULL OR ' . (string) $sortName . '.`minor_key_phrase` = \'\' ))');
                        break;
                    case 'noindex':
                        $sql = (string) $sortName . '.`allow_search` = 0';
                        if ((int) Configuration::get($config_name)) {
                            $sql = '(' . (string) $sortName . '.`allow_search` = 0 OR ' . (string) $sortName . '.`allow_search` = 2)';
                        }
                        $searchQueryBuilder->andWhere((string) $sql);
                        $countQueryBuilder->andWhere((string) $sql);
                        break;
                }
            }
            if (isset($searchQueryBuilder) && isset($countQueryBuilder) &&  isset($filters['readability_score']) && $filters['readability_score']) {
                if ('meta' == $type) {
                    $searchQueryBuilder->andWhere($sqlOverAllReadabilityScore . ' < 0');
                } else {
                    switch ($filters['readability_score']) {
                        case 'bad':
                            $searchQueryBuilder->andWhere($sqlOverAllReadabilityScore . ' <= 4');
                            $countQueryBuilder->andWhere($sqlOverAllReadabilityScore . ' <= 4');
                            break;
                        case 'ok':
                            $searchQueryBuilder->andWhere($sqlOverAllReadabilityScore . ' > 4');
                            $searchQueryBuilder->andWhere($sqlOverAllReadabilityScore . ' <= 7');
                            break;
                        case 'good':
                            $searchQueryBuilder->andWhere($sqlOverAllReadabilityScore . ' > 7');
                            $countQueryBuilder->andWhere($sqlOverAllReadabilityScore . ' > 7');
                            break;
                    }
                }
            }
        }
    }

    public function hookActionCategoryGridQueryBuilderModifier($params)
    {
        $this->_actionGridQueryBuilderModifier('category', $params);
    }

    public function hookActionCmsPageGridQueryBuilderModifier($params)
    {
        $this->_actionGridQueryBuilderModifier('cms', $params);
    }

    public function hookActionCmsPageCategoryGridQueryBuilderModifier($params)
    {
        $this->_actionGridQueryBuilderModifier('cms_category', $params);
    }

    public function hookActionManufacturerGridQueryBuilderModifier($params)
    {
        $this->_actionGridQueryBuilderModifier('manufacturer', $params);
    }

    public function hookActionSuppliersGridQueryBuilderModifier($params)
    {
        $this->_actionGridQueryBuilderModifier('supplier', $params);
    }

    public function hookActionMetaGridQueryBuilderModifier($params)
    {
        $this->_actionGridQueryBuilderModifier('meta', $params);
    }
    protected function _actionGridDefinitionModifier( array $params)
    {
        $trans = [
            'seo_score' => $this->l('SEO score'),
            'readability_score' => $this->l('Readability score'),
            'seo_score_ok' => $this->l('SEO: Acceptable'),
            'seo_score_good' => $this->l('SEO: Excellent'),
            'seo_score_na' => $this->l('SEO: No Focus or Related key phrases'),
            'seo_score_noindex' => $this->l('SEO: No Index'),
            'seo_score_placeholder' => $this->l('All SEO Scores'),
            'readability_score_bad' => $this->l('Readability: Not good'),
            'readability_score_ok' => $this->l('Readability: Acceptable'),
            'readability_score_good' => $this->l('Readability: Excellent'),
            'readability_score_placeholder' => $this->l('All Readability Scores'),
        ];
        require_once(dirname(__FILE__) . '/../OverrideUtil');
        $class= 'Ets_Seo_OverrideUtil';
        $method = '_actionGridDefinitionModifier';
        call_user_func_array(array($class, $method),array($params, $trans));
    }

    public function hookActionCategoryGridDefinitionModifier($params)
    {
        $this->_actionGridDefinitionModifier( $params);
    }

    public function hookActionProductGridDefinitionModifier($params)
    {
        if(version_compare(_PS_VERSION_, '9.0.0', '>=')) {
            $this->_actionGridDefinitionModifier( $params);
        }
    }

    public function hookActionCmsPageCategoryGridDefinitionModifier($params)
    {
        $this->_actionGridDefinitionModifier( $params);
    }

    public function hookActionCmsPageGridDefinitionModifier($params)
    {
        $this->_actionGridDefinitionModifier($params);
    }

    public function hookActionManufacturerGridDefinitionModifier($params)
    {
        $this->_actionGridDefinitionModifier( $params);
    }

    public function hookActionSupplierGridDefinitionModifier($params)
    {
        $this->_actionGridDefinitionModifier( $params);
    }

    public function hookActionMetaGridDefinitionModifier($params)
    {
        $this->_actionGridDefinitionModifier($params);
    }

    /**
     * modifyResultList.
     *
     * @param string $type
     * @param array $lists
     *
     * @return array
     */
    public function modifyResultList($type = 'cms', $lists = [])
    {
        return EtsSeoAnalysis::getInstance()->modifyResultList($type, $lists);
    }

    public function hookActionAdminProductsListingResultsModifier($params)
    {
        if (isset($params['products']) && is_array($params['products'])) {
            $products = $params['products'];
            if (!$products) {
                return;
            }
            $products = $this->modifyResultList('product', $products);
            $params['products'] = $products;
        }
    }

    public function hookActionAdminCategoriesListingResultsModifier($params)
    {
        if (isset($params['list']) && is_array($params['list'])) {
            $params['list'] = $this->modifyResultList('category', $params['list']);
        }
    }

    public function hookActionAdminCmsListingResultsModifier($params)
    {
        if (isset($params['list']) && is_array($params['list'])) {
            $params['list'] = $this->modifyResultList('cms', $params['list']);
        }
    }

    public function hookActionAdminCmsCategoriesListingResultsModifier($params)
    {
        if (isset($params['list']) && is_array($params['list'])) {
            $params['list'] = $this->modifyResultList('cms_category', $params['list']);
        }
    }

    public function hookActionAdminManufacturersListingResultsModifier($params)
    {
        if (isset($params['list']) && is_array($params['list'])) {
            $params['list'] = $this->modifyResultList('manufacturer', $params['list']);
        }
    }

    public function hookActionAdminSuppliersListingResultsModifier($params)
    {
        if (isset($params['list']) && is_array($params['list'])) {
            $params['list'] = $this->modifyResultList('supplier', $params['list']);
        }
    }

    public function hookActionAdminMetaListingResultsModifier($params)
    {
        if (isset($params['list']) && is_array($params['list'])) {
            $params['list'] = $this->modifyResultList('meta', $params['list']);
        }
    }

    /**
     * __actionGridDataModifier.
     *
     * @param string $type
     *
     * @return void
     */
    protected function _actionGridDataModifier($type, array $params)
    {
        if (isset($params['data']) && $params['data']) {
            $data = &$params['data'];
            $results = $data->getRecords();

            $results = $this->modifyResultList($type, $results);
            $recordCollection = new RecordCollection($results);
            $gridData = new GridData($recordCollection, $data->getRecordsTotal(), $data->getQuery());
            $data = $gridData;
        }
    }

    public function hookActionCategoryGridDataModifier($params)
    {
        $this->_actionGridDataModifier('category', $params);
    }
    public function hookActionProductGridDataModifier($params)
    {
        if(version_compare(_PS_VERSION_, '9.0.0', '>=')) {
            $this->_actionGridDataModifier('product', $params);
        }
    }

    public function hookActionCmsPageGridDataModifier($params)
    {
        $this->_actionGridDataModifier('cms', $params);
    }

    public function hookActionCmsPageCategoryGridDataModifier($params)
    {
        $this->_actionGridDataModifier('cms_category', $params);
    }

    public function hookActionManufacturerGridDataModifier($params)
    {
        $this->_actionGridDataModifier('manufacturer', $params);
    }

    public function hookActionSupplierGridDataModifier($params)
    {
        $this->_actionGridDataModifier('supplier', $params);
    }

    public function hookActionMetaGridDataModifier($params)
    {
        $this->_actionGridDataModifier('meta', $params);
    }
}
