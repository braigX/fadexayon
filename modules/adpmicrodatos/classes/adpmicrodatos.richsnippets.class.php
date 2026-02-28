<?php
/**
 * 2007-2023 PrestaShop.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    Ádalop <contact@prestashop.com>
 *  @copyright 2023 Ádalop
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class AdpmicrodatosRichsnippets
{
    // Agregar el nombre del módulo que se vaya a implementar y definir un método estático cuyo nombre sea [modulename]GetProductRichSnippet($id_product)
    public static $richSnippetsImplementedModules = [
        'productcomments' => [
            'id' => 9144,
            'link' => 'https://addons.prestashop.com/product.php?id_product=9144',
            'name' => 'Allows users to post reviews and rate products on specific criteria.',
            'support-shop-reviews' => false,
        ],
        'netreviews' => [
            'id' => 17497,
            'link' => 'https://addons.prestashop.com/product.php?id_product=17497',
            'name' => 'Verified Reviews Module',
            'support-shop-reviews' => false,
        ],
        'gsnippetsreviews' => [
            'id' => 6144,
            'link' => 'https://addons.prestashop.com/product.php?id_product=6144',
            'name' => 'Shop Product Reviews: Opiniones de Productos & Tienda',
            'support-shop-reviews' => false,
        ],
        'steavisgarantis' => [
            'id' => 29150,
            'link' => 'https://addons.prestashop.com/product.php?id_product=29150',
            'name' => 'Guaranteed Reviews Company, shop and product ratings Module',
            'support-shop-reviews' => false,
        ],
        'trustedshopseasyintegration' => [
            'id' => 89788,
            'link' => 'https://addons.prestashop.com/product.php?id_product=89788',
            'name' => 'Trusted Shops Easy Integration Module',
            'support-shop-reviews' => true,
        ],
        'lgcomments' => [
            'id' => 17896,
            'link' => 'https://addons.prestashop.com/product.php?id_product=17896',
            'name' => 'Store Reviews + Product Reviews + Google Rich Snippets Module',
            'support-shop-reviews' => true,
        ],
        'spmgsnipreview' => [
            'id' => 41875,
            'link' => 'https://addons.prestashop.com/product.php?id_product=41875',
            'name' => 'Product, Shop Reviews, Loyalty Program, Google Snippets Module',
            'support-shop-reviews' => false,
        ],
        'ifeedback' => [
            'id' => 3149,
            'link' => 'https://addons.prestashop.com/product.php?id_product=3149',
            'name' => 'iFeedback - Advanced ratings Module.',
            'support-shop-reviews' => false,
        ],
        'yotpo' => [
            'id' => 31083,
            'link' => 'https://addons.prestashop.com/product.php?id_product=31083',
            'name' => 'Yotpo',
            'support-shop-reviews' => false,
        ],
        'revi' => [
            'link' => 'https://revi.io/es',
            'name' => 'revi.',
            'support-shop-reviews' => false,
        ],
        'myprestacomments' => [
            'link' => 'https://mypresta.eu/modules/advertising-and-marketing/free-product-reviews-comments.html',
            'name' => 'Prestashop Free product reviews (comments)',
            'support-shop-reviews' => false,
        ],
        'prrs' => [
            'link' => 'https://mypresta.eu/modules/seo/product-comments-reviews-rich-snippet.html',
            'name' => 'Prestashop Product Comments - Reviews rich snippet',
            'support-shop-reviews' => false,
        ],
        'iqitreviews' => [
            'link' => 'https://iqit-commerce.com/xdocs/warehouse-theme-documentation/#iqitreviews',
            'name' => 'iqitreviews',
            'support-shop-reviews' => false,
        ],
        'feedaty' => [
            'link' => 'https://www.feedaty.com/',
            'name' => 'Feedaty',
            'support-shop-reviews' => false,
        ],
        'stproductcommentspro' => [
            'link' => 'https://www.sunnytoo.com/product/advanced-product-reviews-prestashop-module',
            'name' => 'PrestaShop Product Reviews module.',
            'support-shop-reviews' => false,
        ],
        'ets_reviews' => [
            'link' => 'https://addons.prestashop.com/product.php?id_product=53172',
            'name' => 'Product Reviews - Ratings, Google Snippets, Q&A',
            'support-shop-reviews' => false,
        ],
        // 'homecomments' => array(
        //     'id' => 4999,
        //     'link' => 'https://addons.prestashop.com/es/product.php?id_product=4999',
        //     'name' => 'Go Reviews - Reviews, Advices, Ratings, SEO and Google Rich Snippets',
        //     'support-shop-reviews' => false
        // )
    ];

    public static function getRichSnippetsEnabledModuleName()
    {
        foreach (self::$richSnippetsImplementedModules as $key => $value) {
            if (Module::isInstalled($key) && Module::isEnabled($key)) {
                return $key;
            }
        }
    }

    public static function getRichSnippetsEnabledModuleNames()
    {
        foreach (self::$richSnippetsImplementedModules as $key => $value) {
            if (Module::isInstalled($key) && Module::isEnabled($key)) {
                yield $key;
            }
        }
    }

    /**
     * Obtiene las valoraciones del producto especificado.
     *
     * @param int $id_product
     * @param string $moduleName Nombre del módulo de valoraciones desde el que obtener las valoraciones (null para usar cualquier módulo soportado que encuentre)
     *
     * @return array Valoraciones del producto
     */
    public static function getRichSnippetsFromProduct($id_product, $moduleName = null)
    {
        // Si no nos pasan el nombre del módulo, obtenemos el primer módulo soportado que encontremos activo
        $enableRichSnippetModules = isset($moduleName) && Module::isInstalled($moduleName) && Module::isEnabled($moduleName)
            ? [$moduleName]
            : self::getRichSnippetsEnabledModuleNames();

        $result = [];
        foreach ($enableRichSnippetModules as $richSnippetModule) {
            $rsModuleName = $richSnippetModule . 'GetProductRichSnippets';
            if (method_exists(self::class, $rsModuleName)) {
                $result = $result + self::$rsModuleName($id_product);
            }
        }

        return $result;
    }

    /**
     * Obtiene las valoraciones de la tienda.
     *
     * @param int $id_shop Identificador de la tienda (null para obtener la tienda del contexto)
     * @param string $moduleName Nombre del módulo de valoraciones desde el que obtener las valoraciones (null para usar el primer módulo soportado que encuentre)
     *
     * @return array Valoraciones de la tienda
     */
    public static function getShopRichSnippets($id_shop = null, $module_name = null)
    {
        $id_shop = isset($id_shop) ? $id_shop : Context::getContext()->shop->id;
        $module_name = isset($module_name) ? $module_name : self::getRichSnippetsEnabledModuleName();

        if (!isset($module_name) || !self::$richSnippetsImplementedModules[$module_name]['support-shop-reviews']) {
            return [];
        }

        $rsModuleName = $module_name . 'GetShopRichSnippets';

        return self::$rsModuleName($id_shop);
    }

    private static function myprestacommentsGetProductRichSnippets($id_product)
    {
        return self::productcommentsGetProductRichSnippets($id_product);
    }

    private static function prrsGetProductRichSnippets($id_product)
    {
        return self::productcommentsGetProductRichSnippets($id_product);
    }

    private static function productcommentsGetProductRichSnippets($id_product)
    {
        $reviews = [];
        $ratingValue = 0;

        $validate = Configuration::get('PRODUCT_COMMENTS_MODERATE');

        $sql = 'SELECT pc.*, cu.`firstname`, cu.`lastname`
                FROM `' . _DB_PREFIX_ . 'product_comment` pc
                LEFT JOIN `' . _DB_PREFIX_ . 'customer` cu
                ON pc.`id_customer` = cu.`id_customer`
                WHERE pc.`id_product` = ' . (int) $id_product . ('1' == $validate ? ' AND pc.`validate` = 1' : '');

        if ($results = Db::getInstance()->ExecuteS($sql)) {
            foreach ($results as $result) {
                $ratingValue += $result['grade'];

                $review = [];
                $review['grade'] = $result['grade'];
                $review['title'] = $result['title'];

                $review['customer_name'] = trim($result['customer_name']);
                if (empty($review['customer_name'])) {
                    $review['customer_name'] = trim($result['firstname'] . ' ' . $result['lastname']);
                }
                if (empty($review['customer_name'])) {
                    $review['customer_name'] = 'Anonymous';
                }

                $review['date_add'] = date('Y-m-d', strtotime($result['date_add']));
                $review['content'] = $result['content'];
                $review['worstRating'] = 1;
                $review['bestRating'] = 5;
                $reviews['review'][] = $review;
            }

            $reviews['ratingCount'] = count($results);
            $resultRatingValue = 0;
            if (count($results) > 0) {
                $resultRatingValue = (float) $ratingValue / count($results);
                $resultRatingValue = round($resultRatingValue, 1);
            }

            $reviews['ratingValue'] = $resultRatingValue;

            $reviews['worstRating'] = 1;
            $reviews['bestRating'] = 5;
        }

        return $reviews;
    }

    private static function lgcommentsGetProductRichSnippets($id_product)
    {
        $reviews = [];
        $ratingValue = 0;

        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'lgcomments_productcomments` 
                    WHERE id_product = ' . (int) $id_product;

        if ($results = Db::getInstance()->ExecuteS($sql)) {
            $ratingscale = Configuration::get('PS_LGCOMMENTS_SCALE');

            if (5 == $ratingscale) {
                $bestRating = 5;
            } elseif (20 == $ratingscale) {
                $bestRating = 20;
            } else {
                $bestRating = 10;
            }

            foreach ($results as $result) {
                $customer_name = $result['nick'];
                if (empty($customer_name)) {
                    $cliente = new Customer($result['id_customer']);
                    $customer_name = $cliente->firstname;
                    if (!empty($cliente->lastname)) {
                        $customer_name .= ' ' . $cliente->lastname;
                    }
                }

                $review = [];
                if (5 == $ratingscale) {
                    $result['stars'] = round($result['stars'] / 2);
                } elseif (20 == $ratingscale) {
                    $result['stars'] = round($result['stars'] * 2);
                }

                $ratingValue += $result['stars'];

                $review['grade'] = $result['stars'];
                $review['title'] = $result['title'];
                $review['customer_name'] = $customer_name;
                $review['date_add'] = date('Y-m-d', strtotime($result['date']));
                $review['content'] = $result['comment'];
                $review['worstRating'] = 0;
                $review['bestRating'] = $bestRating;
                $reviews['review'][] = $review;
            }

            $reviews['ratingCount'] = count($results);
            $resultRatingValue = 0;
            if (count($results) > 0) {
                $resultRatingValue = (float) $ratingValue / count($results);
                $resultRatingValue = round($resultRatingValue, 1);
            }
            $reviews['ratingValue'] = $resultRatingValue;
            $reviews['worstRating'] = 0;
            $reviews['bestRating'] = $bestRating;
        }

        return $reviews;
    }

    private static function lgcommentsGetShopRichSnippets($id_shop)
    {
        $reviews = [];
        $ratingValue = 0;
        $ratingscale = Configuration::get('PS_LGCOMMENTS_SCALE');
        if (5 == $ratingscale) {
            $bestRating = 5;
        } elseif (20 == $ratingscale) {
            $bestRating = 20;
        } else {
            $bestRating = 10;
        }

        $reviewsTableName = _DB_PREFIX_ . 'lgcomments_storecomments';
        $sql = "SELECT *  FROM `{$reviewsTableName}`";

        if ($results = Db::getInstance()->ExecuteS($sql)) {
            foreach ($results as $result) {
                $customer_name = $result['nick'];
                if (empty($customer_name)) {
                    $cliente = new Customer($result['id_customer']);
                    $customer_name = $cliente->firstname;
                    if (!empty($cliente->lastname)) {
                        $customer_name .= ' ' . $cliente->lastname;
                    }
                }

                $review = [];
                if (5 == $ratingscale) {
                    $result['stars'] = round($result['stars'] / 2);
                } elseif (20 == $ratingscale) {
                    $result['stars'] = round($result['stars'] * 2);
                }

                $ratingValue += $result['stars'];
                $review['grade'] = $result['stars'];
                $review['title'] = $result['title'];
                $review['customer_name'] = $customer_name;
                $review['date_add'] = date('Y-m-d', strtotime($result['date']));
                $review['content'] = $result['comment'];
                $review['worstRating'] = 0;
                $review['bestRating'] = $bestRating;
                $reviews['review'][] = $review;
            }

            $reviews['ratingCount'] = count($results);
            $resultRatingValue = 0;
            if (count($results) > 0) {
                $resultRatingValue = (float) $ratingValue / count($results);
                $resultRatingValue = round($resultRatingValue, 1);
            }
            $reviews['ratingValue'] = $resultRatingValue;
            $reviews['worstRating'] = 0;
            $reviews['bestRating'] = $bestRating;
        }

        return $reviews;
    }

    private static function iqitreviewsGetProductRichSnippets($id_product)
    {
        $reviews = [];
        $ratingValue = 0;

        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'iqitreviews_products` 
                    WHERE id_product = ' . (int) $id_product . ' and status = 1';

        if ($results = Db::getInstance()->ExecuteS($sql)) {
            foreach ($results as $result) {
                $cliente = new Customer($result['id_customer']);
                $customer_name = $cliente->firstname;
                if (!empty($cliente->lastname)) {
                    $customer_name .= ' ' . $cliente->lastname;
                }

                $ratingValue += $result['rating'];

                $review = [];
                $review['grade'] = $result['rating'];
                $review['title'] = $result['title'];
                $review['customer_name'] = $customer_name;
                $review['date_add'] = date('Y-m-d', strtotime($result['date_add']));
                $review['content'] = $result['comment'];
                $review['worstRating'] = 1;
                $review['bestRating'] = 5;
                $reviews['review'][] = $review;
            }

            $reviews['ratingCount'] = count($results);
            $resultRatingValue = 0;
            if (count($results) > 0) {
                $resultRatingValue = (float) $ratingValue / count($results);
                $resultRatingValue = round($resultRatingValue, 1);
            }
            $reviews['ratingValue'] = $resultRatingValue;
            $reviews['worstRating'] = 1;
            $reviews['bestRating'] = 5;
        }

        return $reviews;
    }

    private static function gsnippetsreviewsGetProductRichSnippets($id_product)
    {
        $module = Module::getInstanceByName('gsnippetsreviews');
        if (!empty($module)) {
            $version = $module->version;

            if (version_compare($version, '5', '>=')) {
                return self::gsnippetsreviewsGetProductRichSnippets_v5($id_product);
            } else {
                return self::gsnippetsreviewsGetProductRichSnippets_v4($id_product);
            }
        }
    }

    private static function gsnippetsreviewsGetProductRichSnippets_v4($id_product)
    {
        $result = [];
        $count = 0;
        $ratingAccumulatedValue = 0;

        $ps_gsr_review = _DB_PREFIX_ . 'gsr_review';
        $ps_gsr_rating = _DB_PREFIX_ . 'gsr_rating';

        $sql = "SELECT {$ps_gsr_rating}.*, {$ps_gsr_review}.*" .
            "FROM {$ps_gsr_rating} " .
            "LEFT JOIN {$ps_gsr_review} ON {$ps_gsr_review}.RTG_ID={$ps_gsr_rating}.RTG_ID " .
            "WHERE {$ps_gsr_rating}.RTG_PROD_ID = {$id_product} AND {$ps_gsr_rating}.RTG_STATUS = '1'";

        foreach (Db::getInstance()->ExecuteS($sql) as $dbItem) {
            $customer = new Customer($dbItem['RTG_CUST_ID']);

            $customer_name = $customer->firstname;
            if (!empty($customer->lastname)) {
                $customer_name .= ' ' . $customer->lastname;
            }

            if (empty($customer_name)) {
                $customer_name = 'Anonymous';
            }

            $title = '';
            $content = '';
            if (!empty($dbItem['RVW_DATA'])) {
                $rvwData = json_decode($dbItem['RVW_DATA'], true);
                $title = $rvwData['sTitle'];
                $content = $rvwData['sComment'];
            }

            $ratingAccumulatedValue += $dbItem['RTG_NOTE'];

            $result['review'][] = [
                'grade' => $dbItem['RTG_NOTE'],
                'title' => $title,
                'customer_name' => $customer_name,
                'date_add' => date('Y-m-d', strtotime($dbItem['RTG_DATE_ADD'])),
                'content' => $content,
                'worstRating' => 1,
                'bestRating' => 5,
            ];

            ++$count;
        }

        $result['ratingCount'] = $count;
        $result['ratingValue'] = 0;
        if (!empty($count)) {
            $resultRatingValue = (float) $ratingAccumulatedValue / $count;
            $result['ratingValue'] = round($resultRatingValue, 1);
        }
        $result['worstRating'] = 1;
        $result['bestRating'] = 5;

        return $result;
    }

    private static function gsnippetsreviewsGetProductRichSnippets_v5($id_product)
    {
        $result = [];
        $count = 0;
        $ratingAccumulatedValue = 0;

        $reviews = _DB_PREFIX_ . 'bt_spr_products_reviews';

        $sql = 'SELECT *' .
            "FROM `{$reviews}` " .
            "WHERE {$reviews}.`id_product` = {$id_product} AND {$reviews}.`review_status` = '1'";

        foreach (Db::getInstance()->ExecuteS($sql) as $dbItem) {
            $customer = new Customer($dbItem['id_customer']);

            $customer_name = $customer->firstname;
            if (!empty($customer->lastname)) {
                $customer_name .= ' ' . $customer->lastname;
            }

            $result['review'][] = [
                'grade' => $dbItem['rating_value'],
                'title' => $dbItem['title_review'],
                'customer_name' => $customer_name,
                'date_add' => date('Y-m-d', strtotime($dbItem['date_add'])),
                'content' => $dbItem['text_review'],
                'worstRating' => 1,
                'bestRating' => 5,
            ];

            $ratingAccumulatedValue += $dbItem['rating_value'];
            ++$count;
        }

        $result['ratingCount'] = $count;
        $result['ratingValue'] = 0;
        if (!empty($count)) {
            $resultRatingValue = (float) $ratingAccumulatedValue / $count;
            $result['ratingValue'] = round($resultRatingValue, 1);
        }
        $result['worstRating'] = 1;
        $result['bestRating'] = 5;

        return $result;
    }

    private static function netreviewsGetProductRichSnippets($id_product)
    {
        $result = [];
        $count = 0;
        $ratingAccumulatedValue = 0;

        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'av_products_reviews` 
        WHERE ref_product = ' . (int) $id_product;

        foreach (Db::getInstance()->ExecuteS($sql) as $dbItem) {
            // Campos SQL
            // id_product_av, ref_product, rate, review, customer_name, horodate,
            // horodate_order, discussion, helpful, helpless, media_full, iso_lang, id_shop
            $ratingAccumulatedValue += $dbItem['rate'];
            $result['review'][] = [
                'grade' => $dbItem['rate'],
                'title' => '',
                'customer_name' => $dbItem['customer_name'],
                'date_add' => date('Y-m-d', $dbItem['horodate']),
                'content' => $dbItem['review'],
                'worstRating' => 1,
                'bestRating' => 5,
            ];
            ++$count;
        }

        $result['ratingCount'] = $count;
        $result['ratingValue'] = 0;
        if (!empty($count)) {
            $resultRatingValue = (float) $ratingAccumulatedValue / $count;
            $result['ratingValue'] = round($resultRatingValue, 1);
        }
        $result['worstRating'] = 1;
        $result['bestRating'] = 5;

        return $result;
    }

    private static function homecommentsGetProductRichSnippets($id_product)
    {
        $reviews = [];
        $ratingValue = 0;

        $sql = 'SELECT lhc.*, cu.`firstname`, cu.`lastname`
                FROM `' . _DB_PREFIX_ . 'lineven_home_comments` lhc
                LEFT JOIN `' . _DB_PREFIX_ . 'customer` cu ON lhc.`id_customer` = cu.`id_customer`
                LEFT JOIN `' . _DB_PREFIX_ . 'order_detail` od ON lhc.`id_order` = od.`id_order`
                WHERE (lhc.`id_product` = ' . $id_product . ' OR od.`product_id` = ' . $id_product . ") AND lhc.`moderated` = 1 AND lhc.`status` = 'VAL'";

        if ($results = Db::getInstance()->ExecuteS($sql)) {
            foreach ($results as $result) {
                $ratingValue += $result['rate'];

                $review = [];
                $review['grade'] = $result['rate'];
                $review['title'] = $result['title'];
                $review['customer_name'] = trim($result['pseudo']);
                if (empty($review['customer_name'])) {
                    $review['customer_name'] = trim($result['firstname'] . ' ' . $result['lastname']);
                }
                if (empty($review['customer_name'])) {
                    $review['customer_name'] = 'Anonymous';
                }

                $review['date_add'] = date('Y-m-d', strtotime($result['date_add']));
                $review['content'] = $result['comment'];
                $review['worstRating'] = 0;
                $review['bestRating'] = 5;

                $reviews['review'][] = $review;
            }

            $reviews['ratingCount'] = count($results);
            $resultRatingValue = 0;
            if (count($results) > 0) {
                $resultRatingValue = (float) $ratingValue / count($results);
                $resultRatingValue = round($resultRatingValue, 1);
            }
            $reviews['ratingValue'] = $resultRatingValue;
            $reviews['worstRating'] = 0;
            $reviews['bestRating'] = 5;
        }

        return $reviews;
    }

    private static function spmgsnipreviewGetProductRichSnippets($id_product)
    {
        $reviews = [];
        $ratingValue = 0;

        $sql = 'SELECT spmgsr.*, cu.`firstname`, cu.`lastname`
                FROM `' . _DB_PREFIX_ . 'spmgsnipreview spmgsr`
                LEFT JOIN `' . _DB_PREFIX_ . 'customer` cu ON spmgsr.`id_customer` = cu.`id_customer`
                WHERE spmgsr.`id_product` = ' . $id_product . "  AND spmgsr.`is_active` = 1 AND spmgsr.`is_abuse` = '0'";

        if ($results = Db::getInstance()->ExecuteS($sql)) {
            foreach ($results as $result) {
                $ratingValue += $result['rating'];

                $review = [];
                $review['grade'] = $result['rating'];
                $review['title'] = $result['title_review'];
                $review['customer_name'] = trim($result['customer_name']);
                if (empty($review['customer_name'])) {
                    $review['customer_name'] = trim($result['firstname'] . ' ' . $result['lastname']);
                }
                if (empty($review['customer_name'])) {
                    $review['customer_name'] = 'Anonymous';
                }

                $review['date_add'] = date('Y-m-d', strtotime($result['time_add']));
                $review['content'] = $result['text_review'];
                $review['worstRating'] = 0;
                $review['bestRating'] = 5;

                $reviews['review'][] = $review;
            }

            $reviews['ratingCount'] = count($results);
            $resultRatingValue = 0;
            if (count($results) > 0) {
                $resultRatingValue = (float) $ratingValue / count($results);
                $resultRatingValue = round($resultRatingValue, 1);
            }
            $reviews['ratingValue'] = $resultRatingValue;
            $reviews['worstRating'] = 0;
            $reviews['bestRating'] = 5;
        }

        return $reviews;
    }

    private static function yotpoGetProductRichSnippets($id_product)
    {
        $reviews = [];

        $yotpo_app_key = Configuration::get('yotpo_app_key');
        $apiUrl = "https://api-cdn.yotpo.com/v1/widget/{$yotpo_app_key}/products/{$id_product}/reviews.json?per_page=150&page=1&sort=date&direction=desc";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        $output = curl_exec($ch);
        curl_close($ch);

        $apiResult = json_decode($output);

        if (200 == $apiResult->status->code) {
            $ratingValue = 0;
            $ratingCount = 0;
            foreach ($apiResult->response->reviews as $result) {
                $ratingValue += $result->score;

                $review = [];
                $review['grade'] = $result->score;
                $review['title'] = $result->title;
                $review['customer_name'] = trim($result->user->display_name);
                if (empty($review['customer_name'])) {
                    $review['customer_name'] = 'Anonymous';
                }

                $review['date_add'] = date('Y-m-d', strtotime($result->created_at));
                $review['content'] = $result->content;
                $review['worstRating'] = 0;
                $review['bestRating'] = 5;

                $reviews['review'][] = $review;
                ++$ratingCount;
            }

            $reviews['ratingCount'] = $ratingCount;
            $resultRatingValue = 0;
            if ($ratingCount > 0) {
                $resultRatingValue = (float) $ratingValue / $ratingCount;
                $resultRatingValue = round($resultRatingValue, 1);
            }
            $reviews['ratingValue'] = $resultRatingValue;
            $reviews['worstRating'] = 0;
            $reviews['bestRating'] = 5;
        }

        return $reviews;
    }

    private static function reviGetProductRichSnippets($id_product)
    {
        $module = Module::getInstanceByName('revi');
        if (!empty($module)) {
            $version = $module->version;

            if (version_compare($version, '6', '>=')) {
                return self::reviGetProductRichSnippets_v6($id_product);
            } else {
                return self::reviGetProductRichSnippets_v5($id_product);
            }
        }
    }

    private static function reviGetProductRichSnippets_v5($id_product)
    {
        $reviews = [];

        $reviewsSQL = 'SELECT *
            FROM `revi_comments`
            WHERE id_product = ' . (int) $id_product . " AND status = '1'";

        if ($reviewsResult = Db::getInstance()->ExecuteS($reviewsSQL)) {
            foreach ($reviewsResult as $reviewResult) {
                $review = [];
                $review['grade'] = $reviewResult['rating'];
                $review['customer_name'] = trim($reviewResult['customer_name']) . ' ' . trim($reviewResult['customer_lastname']);
                if (empty($review['customer_name'])) {
                    $review['customer_name'] = 'Anonymous';
                }

                $review['date_add'] = date('Y-m-d', strtotime($reviewResult['date']));
                $review['content'] = $reviewResult['comment'];

                $review['worstRating'] = 1;
                $review['bestRating'] = 5;

                $reviews['review'][] = $review;
            }
        }

        $ratingSQL = 'SELECT *
            FROM revi_products
            WHERE id_product = ' . $id_product;

        if ($rating = Db::getInstance()->ExecuteS($ratingSQL)) {
            $reviews['ratingCount'] = $rating[0]['num_ratings'];
            $reviews['ratingValue'] = $rating[0]['avg_rating'];
            $reviews['worstRating'] = 1;
            $reviews['bestRating'] = 5;
        }

        return $reviews;
    }

    private static function reviGetProductRichSnippets_v6($id_product)
    {
        $reviews = [];

        $ratingSQL = 'SELECT *
            FROM revi_products
            WHERE id_product = ' . $id_product;

        if ($rating = Db::getInstance()->ExecuteS($ratingSQL)) {
            $reviews['ratingCount'] = $rating[0]['num_reviews'];
            $reviews['ratingValue'] = $rating[0]['avg_rating'];
            $reviews['bestRating'] = 5;
        }

        return $reviews;
    }

    private static function ifeedbackGetProductRichSnippets($id_product)
    {
        $reviews = [];
        $ratingValue = 0;

        $sql = 'SELECT feedback.*, cu.`firstname`, cu.`lastname`, rating.`star`
                FROM `' . _DB_PREFIX_ . 'ifb_feedback feedback`
                LEFT JOIN `' . _DB_PREFIX_ . 'customer` cu ON feedback.`id_customer` = cu.`id_customer`
                LEFT JOIN `' . _DB_PREFIX_ . 'ifb_rating` rating ON feedback.`id_feedback` = rating.`id_feedback`
                WHERE feedback.`id_product` = ' . (int) $id_product . '  AND feedback.`approved` = 1';

        if ($results = Db::getInstance()->ExecuteS($sql)) {
            foreach ($results as $result) {
                $ratingValue += $result['star'];

                $review = [];
                $review['grade'] = $result['star'];
                $review['customer_name'] = trim($result['name_customer']);
                if (empty($review['customer_name'])) {
                    $review['customer_name'] = trim($result['firstname'] . ' ' . $result['lastname']);
                }
                if (empty($review['customer_name'])) {
                    $review['customer_name'] = 'Anonymous';
                }

                $review['date_add'] = date('Y-m-d', strtotime($result['date_add']));
                $review['content'] = $result['feedback'];

                $review['worstRating'] = 0;
                $review['bestRating'] = 5;

                $reviews['review'][] = $review;
            }

            $reviews['ratingCount'] = count($results);
            $resultRatingValue = 0;
            if (count($results) > 0) {
                $resultRatingValue = (float) $ratingValue / count($results);
                $resultRatingValue = round($resultRatingValue, 1);
            }
            $reviews['ratingValue'] = $resultRatingValue;
            $reviews['worstRating'] = 0;
            $reviews['bestRating'] = 5;
        }

        return $reviews;
    }

    private static function steavisgarantisGetProductRichSnippets($id_product)
    {
        $reviews = [];

        $reviewsTableName = _DB_PREFIX_ . 'steavisgarantis_reviews';
        $id_lang = Context::getContext()->language->id;

        $reviewsSQL = 'SELECT *' .
            "FROM `{$reviewsTableName}` " .
            "WHERE product_id = {$id_product} AND id_lang = {$id_lang}";

        if ($reviewsResult = Db::getInstance()->ExecuteS($reviewsSQL)) {
            foreach ($reviewsResult as $reviewResult) {
                $review = [];
                $review['grade'] = $reviewResult['rate'];
                $review['customer_name'] = $reviewResult['ag_reviewer_name'];
                if (empty($review['customer_name'])) {
                    $review['customer_name'] = 'Anonymous';
                }

                $review['date_add'] = date('Y-m-d', $reviewResult['date_time']);
                $review['content'] = $reviewResult['review'];

                $review['worstRating'] = 1;
                $review['bestRating'] = 5;
                $reviews['review'][] = $review;
            }
        }

        $ratingTableName = _DB_PREFIX_ . 'steavisgarantis_average_rating';
        $ratingSQL = "SELECT *
            FROM {$ratingTableName}
            WHERE product_id = " . (int) $id_product;

        if ($rating = Db::getInstance()->ExecuteS($ratingSQL)) {
            $reviews['ratingCount'] = $rating[0]['reviews_nb'];
            $reviews['ratingValue'] = $rating[0]['rate'];
            $reviews['worstRating'] = 1;
            $reviews['bestRating'] = 5;
        }

        return $reviews;
    }

    private static function trustedshopseasyintegrationGetProductRichSnippets($id_product)
    {
        return self::trustedshopsintegrationGetProductRichSnippets($id_product);
    }

    private static function trustedshopsintegrationGetProductRichSnippets($id_product)
    {
        $reviews = [];

        $id_lang = Context::getContext()->language->id;

        $aux = json_decode(Configuration::get('ADP_RICH_SNIPPETS_TS_CODE'), true);

        $tsId = '';
        if (empty($aux) || empty($aux[$id_lang])) {
            return $reviews;
        }

        $tsId = trim($aux[$id_lang]);

        $product = new Product($id_product);

        $sku = urlencode(trim($product->reference));
        if (empty($sku)) {
            $sku = $id_product;
        }

        $sku = bin2hex($sku);

        $apiUrl = 'https://cdn1.api.trustedshops.com/shops/' . $tsId . '/products/skus/' . $sku . '/productreviewstickers/v1/reviews.json';

        $obj = json_decode(Tools::file_get_contents($apiUrl));

        if (empty($obj) || empty($obj->response) || empty($obj->response->data) || empty($obj->response->data->product) || empty($obj->response->data->reviews)) {
            return $reviews;
        }

        $ratingValue = 0;
        $results = $obj->response->data->product->reviews;
        foreach ($results as $result) {
            $ratingValue += $result->mark;

            $review = [];
            $review['grade'] = $result->mark;
            $review['customer_name'] = isset($result->reviewer)
                ? $result->reviewer->profile->firstname . (isset($result->reviewer->profile->lastname) ? ' ' . $result->reviewer->profile->lastname : '')
                : 'Anonymous';

            $review['date_add'] = $result->creationDate;
            $review['content'] = $result->comment;

            $review['worstRating'] = 0;
            $review['bestRating'] = 5;

            $reviews['review'][] = $review;
        }

        $reviews['ratingCount'] = count($results);
        $resultRatingValue = 0;
        if (count($results) > 0) {
            $resultRatingValue = (float) $ratingValue / count($results);
            $resultRatingValue = round($resultRatingValue, 1);
        }
        $reviews['ratingValue'] = $resultRatingValue;
        $reviews['worstRating'] = 0;
        $reviews['bestRating'] = 5;

        return $reviews;
    }

    private static function trustedshopscachecheck($filename_cache, $timeout = 10800)
    {
        return file_exists($filename_cache) && time() - filemtime($filename_cache) < $timeout;
    }

    private static function trustedshopseasyintegrationGetShopRichSnippets($id_shop)
    {
        $reviews = [];

        $id_lang = Context::getContext()->language->id;

        $aux = json_decode(Configuration::get('ADP_RICH_SNIPPETS_TS_CODE'), true);

        $tsId = '';
        if (empty($aux) || empty($aux[$id_lang])) {
            return $reviews;
        }

        $tsId = trim($aux[$id_lang]);

        $cacheFileName = _PS_MODULE_DIR_ . 'adpmicrodatos/tmp/' . $tsId . '.json';
        $cacheTimeOut = 43200; // half a day
        $apiUrl = 'https://api.trustedshops.com/rest/public/v2/shops/' . $tsId . '/quality/reviews.json';

        // check if cached version exists
        if (!self::trustedshopscachecheck($cacheFileName, $cacheTimeOut)) {
            // load fresh from API
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, false);
            curl_setopt($ch, CURLOPT_URL, $apiUrl);
            $output = curl_exec($ch);
            curl_close($ch);
            // Write the contents back to the file
            // Make sure you can write to file's destination
            file_put_contents($cacheFileName, $output);
        }

        if ($jsonObject = json_decode(Tools::file_get_contents($cacheFileName), true)) {
            $ratingvalue = $jsonObject['response']['data']['shop']['qualityIndicators']['reviewIndicator']['overallMark'];
            $ratingcount = $jsonObject['response']['data']['shop']['qualityIndicators']['reviewIndicator']['activeReviewCount'];

            if ($ratingcount > 0) {
                $reviews['ratingValue'] = $ratingvalue;
                $reviews['bestRating'] = '5.00';
                $reviews['ratingCount'] = $ratingcount;
                $reviews['worstRating'] = 0;
            }
        }

        return $reviews;
    }

    private static function feedatyGetProductRichSnippets($id_product)
    {
        $reviews = [];

        // Verificar si el módulo y las clases existen
        if (!file_exists(_PS_MODULE_DIR_ . 'feedaty/feedaty.php')
            || !file_exists(_PS_MODULE_DIR_ . 'feedaty/lib/FeedatyClasses.php')
            || !class_exists('FeedatyWebservice')) {
            return $reviews;
        }

        require_once _PS_MODULE_DIR_ . 'feedaty/feedaty.php';
        require_once _PS_MODULE_DIR_ . 'feedaty/lib/FeedatyClasses.php';

        $feedatyWebService = new FeedatyWebservice(Context::getContext()->language->iso_code);
        $feedatyProductData = $feedatyWebService->fdGetProductData($id_product);
        if ($feedatyProductData) {
            foreach ($feedatyProductData['Feedbacks'] as $feedback) {
                $review = [];
                $review['grade'] = $feedback['ProductRating'];
                $review['customer_name'] = 'Anonymous';
                $review['date_add'] = date('Y-m-d', strtotime(str_replace('/', '-', $feedback['Recorded'])));
                $review['content'] = $feedback['ProductReview'];
                $review['worstRating'] = 1;
                $review['bestRating'] = 5;
                $reviews['review'][] = $review;
            }

            if (!empty($feedatyProductData['Product'])) {
                $reviews['ratingCount'] = $feedatyProductData['Product']['RatingsCount'];
                $reviews['ratingValue'] = $feedatyProductData['Product']['AvgRating'];
                $reviews['worstRating'] = 1;
                $reviews['bestRating'] = 5;
            }
        }

        return $reviews;
    }

    private static function stproductcommentsproGetProductRichSnippets($id_product)
    {
        $db_prefix = _DB_PREFIX_;
        $id_shop = Context::getContext()->shop->id;

        $reviews = [];
        $ratingValue = 0;

        $sql = "SELECT pc.`title`, pc.`content`, pc.`customer_name`, pc.`grade`, pc.`date_add`, cu.`firstname`, cu.`lastname` 
                FROM `{$db_prefix}st_product_comment_pro` AS pc
                LEFT JOIN `{$db_prefix}customer` AS cu ON pc.`id_customer` = cu.`id_customer`
                WHERE pc.`id_shop` = {$id_shop} AND pc.`id_product` = {$id_product} AND validate = 1";

        if ($results = Db::getInstance()->ExecuteS($sql)) {
            foreach ($results as $result) {
                $ratingValue += $result['grade'];

                $review = [];
                $review['grade'] = $result['grade'];
                $review['customer_name'] = trim($result['customer_name']);
                if (empty($review['customer_name'])) {
                    $review['customer_name'] = trim($result['firstname'] . ' ' . $result['lastname']);
                }
                if (empty($review['customer_name'])) {
                    $review['customer_name'] = 'Anonymous';
                }

                $review['date_add'] = date('Y-m-d', strtotime($result['date_add']));
                $review['content'] = $result['content'];

                $review['worstRating'] = 0;
                $review['bestRating'] = 5;

                $reviews['review'][] = $review;
            }

            $reviews['ratingCount'] = count($results);
            $resultRatingValue = 0;
            if (count($results) > 0) {
                $resultRatingValue = (float) $ratingValue / count($results);
                $resultRatingValue = round($resultRatingValue, 1);
            }
            $reviews['ratingValue'] = $resultRatingValue;
            $reviews['worstRating'] = 0;
            $reviews['bestRating'] = 5;
        }

        return $reviews;
    }

    private static function ets_reviewsGetProductRichSnippets($id_product)
    {
        $db_prefix = _DB_PREFIX_;
        $reviews = [];
        $ratingValue = 0;

        $sql = "SELECT etspcl.`title`, etspcl.`content`, etspc.`customer_name`, etspc.`grade`, etspc.`date_add`, cu.`firstname`, cu.`lastname` 
                FROM `{$db_prefix}ets_rv_product_comment` AS etspc
                LEFT JOIN `{$db_prefix}ets_rv_product_comment_lang` AS etspcl ON etspc.`id_ets_rv_product_comment` = etspcl.`id_ets_rv_product_comment`
                LEFT JOIN `{$db_prefix}customer` AS cu ON etspc.`id_customer` = cu.`id_customer`
                WHERE etspc.`id_product` = {$id_product} AND validate = 1";

        if ($results = Db::getInstance()->ExecuteS($sql)) {
            foreach ($results as $result) {
                $ratingValue += $result['grade'];

                $review = [];
                $review['grade'] = $result['grade'];
                $review['customer_name'] = trim($result['customer_name']);
                if (empty($review['customer_name'])) {
                    $review['customer_name'] = trim($result['firstname'] . ' ' . $result['lastname']);
                }
                if (empty($review['customer_name'])) {
                    $review['customer_name'] = 'Anonymous';
                }

                $review['date_add'] = date('Y-m-d', strtotime($result['date_add']));
                $review['content'] = $result['content'];

                $review['worstRating'] = 0;
                $review['bestRating'] = 5;

                $reviews['review'][] = $review;
            }

            $reviews['ratingCount'] = count($results);
            $resultRatingValue = 0;
            if (count($results) > 0) {
                $resultRatingValue = (float) $ratingValue / count($results);
                $resultRatingValue = round($resultRatingValue, 1);
            }
            $reviews['ratingValue'] = $resultRatingValue;
            $reviews['worstRating'] = 0;
            $reviews['bestRating'] = 5;
        }

        return $reviews;
    }
}
