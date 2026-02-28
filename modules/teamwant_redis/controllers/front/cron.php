<?php
/**
 * Redis Cache
 * Version: 3.0.0
 * Copyright (c) 2020-2023. Mateusz Szymański Teamwant
 * https://teamwant.pl
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @author    Teamwant <kontakt@teamwant.pl>
 * @copyright Copyright 2020-2023 © Teamwant Mateusz Szymański All right reserved
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *
 * @category  Teamwant
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

use Teamwant\Prestashop17\Redis\Classes\Cache\Redis;

require_once __DIR__ . '/../../src/CategoryController.php';

/**
 * generate cache for sensitive places
 * curl -k -L --max-redirs 10000 'http://localhost/index.php?redis_healthcheck_key=1eb5a801a3f1b558a6cdb5c3aaa932c492509df07fdb3f9472433a96466a96b6&fc=module&module=teamwant_redis&controller=cron&o'
 * curl -k -L --max-redirs 10000 'http://localhost/index.php?redis_healthcheck_key=1eb5a801a3f1b558a6cdb5c3aaa932c492509df07fdb3f9472433a96466a96b6&fc=module&module=teamwant_redis&controller=cron&offse=&offs4=&offse34=&redis_cron_type=categories'
 */
class Teamwant_RediscronModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        /** @var string $redis_healthcheck_key */
        $redis_healthcheck_key = Configuration::get(Redis::REDIS_HEALTHCHECK_KEY);

        if (!$redis_healthcheck_key
            || (!Tools::getValue('redis_healthcheck_key', false))
            || (!((string) Tools::getValue('redis_healthcheck_key') === (string) $redis_healthcheck_key))
        ) {
            header('HTTP/1.0 401 Unauthorized');
            exit(json_encode(['msg' => 'HTTP/1.0 401 Unauthorized']));
        }

        $parameters = include _PS_ROOT_DIR_ . '/app/config/parameters.php';

        if (!(
            is_array($parameters) &&
            !empty($parameters['parameters']) &&
            !empty($parameters['parameters']['ps_caching']) &&
            !empty($parameters['parameters']['ps_cache_enable']) &&
            $parameters['parameters']['ps_caching'] === 'Redis' &&
            $parameters['parameters']['ps_cache_enable'] === true
        )) {
            header('HTTP/1.0 403 Unauthorized');
            exit(json_encode(['msg' => 'Redis is not enabled']));
        }

        switch (Tools::getValue('redis_cron_type', false)) {
            case 'categories':
                $this->categories();
                break;
            case 'products':
                $this->products();
                break;
            default:
                header('HTTP/1.0 404');
                exit(json_encode(['msg' => 'Type not found']));
        }
    }

    public function generateUrl($queryOverride)
    {
        $url = parse_url($_SERVER['REQUEST_URI']);
        parse_str($url['query'], $query);
        $query = array_merge($query, $queryOverride);

        return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://$_SERVER[HTTP_HOST]" . $url['path'] . '?' . http_build_query($query);
    }

    public function categories()
    {
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }
        set_time_limit(60 * 2); // 60sec*2min

        $limit = 25;
        $_GET['resultsPerPage'] = 500; // todo: it's a limit for products per page, change limit for dynamic some day
        $offset = (int) Tools::getValue('offset', 0);

        $allActiveCategories = Db::getInstance()->executeS(
            (new DbQuery())
                ->from('category', 'c')
                ->leftJoin('category_shop', 'cs', 'cs.id_category = c.id_category')
                ->leftJoin('category_lang', 'cl', 'cl.id_category = c.id_category')
                ->where('c.active = 1')
                ->limit($limit, $offset),
                true,
                false
        );


        if (!empty($allActiveCategories)) {
            foreach ($allActiveCategories as $categoryArr) {
                try {
                    $category = new Category((int) $categoryArr['id_category'], (int) $categoryArr['id_lang'], (int) $categoryArr['id_shop']);
                    $category->overrideObjectCache();
                    $categoryController = new RedisCategoryController();
                    $categoryController->setCategory($category);

                    $categoryVar = $categoryController->getTemplateVarCategoryPublic();
                    Hook::exec(
                        'filterCategoryContent',
                        ['object' => $categoryVar],
                        $id_module = null,
                        $array_return = false,
                        $check_exceptions = true,
                        $use_push = false,
                        $id_shop = null,
                        $chain = true
                    );

                    $categoryController->getTemplateVarSubCategoriesPublic();
                    $categoryController->getBreadcrumbLinks();
                    $categoryController->ajax = true;
                    $categoryController->publicDoSearch();


                    // $categoryController->doProductSearch(
                    //    'catalog/listing/category',
                    //    [
                    //        'entity' => 'category',
                    //        'id' => $category->id,
                    //    ]
                    // );

                    // $urlCategory = (new Link())->getCategoryLink((int) $categoryArr['id_category'], null, (int) $categoryArr['id_lang'], null, (int) $categoryArr['id_shop']);
                    // $ch = curl_init();
                    // curl_setopt($ch, CURLOPT_URL, $urlCategory);
                    // curl_setopt($ch, CURLOPT_TIMEOUT_MS, 500);
                    // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                    // curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
                    // curl_setopt($ch, CURLOPT_NOBODY, true);
                    // curl_exec($ch);
                    // curl_close($ch);

                } catch (Throwable $e) {
                }
            }

            $url = $this->generateUrl([
                'offset' => $offset + $limit,
            ]);

            Tools::redirect($url);
        } else {
            header('HTTP/1.0 200');
            exit(json_encode(['msg' => 'done']));
        }
    }

    /**
     * todo: need a cron cache for product attributes
     *
     * @throws PrestaShopDatabaseException
     */
    private function products()
    {
        $limit = 100;
        $offset = (int) Tools::getValue('offset', 0);

        $all = Db::getInstance()->executeS(
            (new DbQuery())
                ->from('product', 'p')
                ->leftJoin('product_shop', 'ps', 'ps.id_product = p.id_product')
                ->leftJoin('product_lang', 'pl', 'pl.id_product = p.id_product')
                ->where('p.active = 1')
                ->limit($limit, $offset)
        );

        if (!empty($all)) {
            foreach ($all as $productArray) {
                try {
                    $id_product = (int) $productArray['id_product'];
                    $product = new Product($id_product, true, (int) $productArray['id_lang'], $productArray['id_shop']);
                    $product->overrideObjectCache();

                    $productController = new ProductController();
                    $productController->setProduct($product);

                    $productController->setTemplate('catalog/product', [
                        'entity' => 'product',
                        'id' => $id_product,
                    ]);

                    // $productController->initContent();
                    $productController->initContentForRedisCache();
                } catch (Throwable $e) {
                }
            }

            $url = $this->generateUrl([
                'offset' => $offset + $limit,
            ]);

            Tools::redirect($url);
        } else {
            header('HTTP/1.0 200');
            exit(json_encode(['msg' => 'done']));
        }
    }


    protected function getAjaxProductSearchVariables()
    {
        $search = $this->getProductSearchVariables();

        $rendered_products_top = $this->render('catalog/_partials/products-top', ['listing' => $search]);
        $rendered_products = $this->render('catalog/_partials/products', ['listing' => $search]);
        $rendered_products_bottom = $this->render('catalog/_partials/products-bottom', ['listing' => $search]);

        $data = array_merge(
            [
                'rendered_products_top' => $rendered_products_top,
                'rendered_products' => $rendered_products,
                'rendered_products_bottom' => $rendered_products_bottom,
            ],
            $search
        );

        if (!empty($data['products']) && is_array($data['products'])) {
            $data['products'] = $this->prepareProductArrayForAjaxReturn($data['products']);
        }

        return $data;
    }

    protected function getProductSearchQuery()
    {
        $query = new ProductSearchQuery();
        $query
            ->setQueryType('category')
            ->setIdCategory($this->category->id)
            ->setSortOrder(new SortOrder('product', Tools::getProductsOrder('by'), Tools::getProductsOrder('way')));

        return $query;
    }

    protected function getProductSearchContext()
    {
        return (new ProductSearchContext())
            ->setIdShop($this->context->shop->id)
            ->setIdLang($this->context->language->id)
            ->setIdCurrency($this->context->currency->id)
            ->setIdCustomer($this->context->customer ? $this->context->customer->id : null);
    }
    private function getProductSearchProviderFromModules($query)
    {
        $providers = Hook::exec(
            'productSearchProvider',
            ['query' => $query],
            null,
            true
        );

        if (!is_array($providers)) {
            $providers = [];
        }

        foreach ($providers as $provider) {
            if ($provider instanceof ProductSearchProviderInterface) {
                return $provider;
            }
        }

        return null;
    }

    protected function getDefaultProductSearchProvider()
    {
        return new CategoryProductSearchProvider(
            $this->getTranslator(),
            $this->category
        );
    }
}
