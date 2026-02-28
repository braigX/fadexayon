<?php
/**
 *   AmbJoliSearch Module : Search for prestashop
 *
 *   @author    Ambris Informatique
 *   @copyright Copyright (c) 2013-2023 Ambris Informatique SARL
 *   @license   Licensed under the EUPL-1.2-or-later
 *
 *   @module     Advanced search (AmbJoliSearch)
 *
 *   @file       ambjolisearch.php
 *
 *   @subject    script principal pour gestion du module (install/config/hook)
 *   Support by mail: support@ambris.com
 */

use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchProviderInterface;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchResult;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrderFactory;

class AmbProductSearchProvider implements ProductSearchProviderInterface
{
    private $module;

    public function __construct($module)
    {
        $this->module = $module;
        $this->context = Context::getContext();
        $this->translator = $module->getTranslator();
        $this->sortOrderFactory = new SortOrderFactory($this->translator);
    }

    public function runQuery(
        ProductSearchContext $context,
        ProductSearchQuery $query
    ) {
        $products = [];
        $count = 0;

        if ($string = $query->getSearchString()) {
            if (!class_exists('AmbSearch')) {
                require_once _PS_ROOT_DIR_ . '/modules/ambjolisearch/classes/AmbSearch.php';
            }

            $searcher = new AmbSearch(true, $this->context, $this->module);

            $id_category = Tools::getValue('ajs_cat', false);
            $id_manufacturer = Tools::getValue('ajs_man', false);

            $searcher->search($context->getIdLang(), $string, $query->getPage(), $query->getResultsPerPage(), $query->getSortOrder()->toLegacyOrderBy(), $query->getSortOrder()->toLegacyOrderWay(), $id_category, $id_manufacturer);
            $products = $searcher->getResults();
            $count = $searcher->getTotal();

            Hook::exec('actionSearch', [
                'searched_query' => $string,
                'total' => $count,

                // deprecated since 1.7.x
                'expr' => $string,
            ]);
        }
        $result = new ProductSearchResult();
        $result->setProducts($products);
        $result->setTotalProductsCount($count);

        $result->setAvailableSortOrders(
            $this->sortOrderFactory->getDefaultSortOrders()
        );

        foreach ($result->getAvailableSortOrders() as $sortOrder) {
            if ($sortOrder->getField() == 'position') {
                $sortOrder->SetDirection('desc');
            }
        }

        if ($string) {
            $this->context->smarty->assign('categories', $result->categories = $searcher->getCategories());
        }

        return $result;
    }
}
