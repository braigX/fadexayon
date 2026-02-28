<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Presta.Site
 * @copyright 2019 Presta.Site
 * @license   LICENSE.txt
 */

use PrestaShop\PrestaShop\Adapter\Search\PswpProductSearchProvider;
use PrestaShop\PrestaShop\Core\Product\Search\Pagination;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchResult;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;

if (!defined('_PS_VERSION_')) {
    exit;
}

class PswpProductListingFrontController extends ProductListingFrontController
{
    public $php_self = 'pswp';
    protected $listing_label;
    public $custom_total;
    public $custom_products;
    // for compatibility with third-party modules
    public $page_name = 'prices-drop';

    protected function getProductSearchQuery()
    {
        $query = new ProductSearchQuery();
        $query
            ->setSortOrder(new SortOrder('product', Tools::getProductsOrder('by'), Tools::getProductsOrder('way')));

        return $query;
    }

    protected function getDefaultProductSearchProvider()
    {
        return new PswpProductSearchProvider(
            $this->getTranslator(),
            $this->custom_total,
            $this->custom_products
        );
    }

    public function getListingLabel()
    {
        return $this->listing_label;
    }

    public function getProductListing($label)
    {
        $this->listing_label = $label;

        return $this->getProductSearchVariables();
    }

    public function getAjaxProductListing($label)
    {
        $this->container = $this->buildContainer();
        $this->listing_label = $label;

        return $this->getAjaxProductSearchVariables();
    }

    protected function getTemplateVarPagination(
        ProductSearchQuery $query,
        ProductSearchResult $result
    ) {
        $pagination = new Pagination();
        $totalItems = $result->getTotalProductsCount();
        if ($this->custom_total !== null) {
            $totalItems = $this->custom_total;
        }
        $pagination
            ->setPage($query->getPage())
            ->setPagesCount(
                (int) ceil((int) $totalItems / $query->getResultsPerPage())
            )
        ;

        $itemsShownFrom = ($query->getResultsPerPage() * ($query->getPage() - 1)) + 1;
        $itemsShownTo = $query->getResultsPerPage() * $query->getPage();

        $c = $this;
        $pages = array_map(function ($link) use ($c) {
            $link['url'] = $c->updateQueryString([
                'page' => $link['page'] > 1 ? $link['page'] : null,
            ]);

            return $link;
        }, $pagination->buildLinks());

        // Filter next/previous link on first/last page
        $pages = array_filter($pages, function ($page) use ($pagination) {
            if ('previous' === $page['type'] && 1 === $pagination->getPage()) {
                return false;
            }
            if ('next' === $page['type'] && $pagination->getPagesCount() === $pagination->getPage()) {
                return false;
            }

            return true;
        });

        return [
            'total_items' => $totalItems,
            'items_shown_from' => $itemsShownFrom,
            'items_shown_to' => ($itemsShownTo <= $totalItems) ? $itemsShownTo : $totalItems,
            'current_page' => $pagination->getPage(),
            'pages_count' => $pagination->getPagesCount(),
            'pages' => $pages,
            // Compare to 3 because there are the next and previous links
            'should_be_displayed' => (count($pagination->buildLinks()) > 3),
        ];
    }

    public function getContainer()
    {
        if (!$this->container) {
            $this->container = $this->buildContainer();
        }

        return $this->container;
    }
}
