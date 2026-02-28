<?php
/**
 * 2010-2023 Bl Modules.
 *
 * If you wish to customize this module for your needs,
 * please contact the authors first for more information.
 *
 * It's not allowed selling, reselling or other ways to share
 * this file or any other module files without author permission.
 *
 * @author    Bl Modules
 * @copyright 2010-2023 Bl Modules
 * @license
 */

if (!defined('_PS_VERSION_')) {
    exit;
}
class IndexingApi
{
    const ACTION_UPDATE = 'URL_UPDATED';
    const RESPONSE_OK = 'OK';

    protected $langId = 1;

    public function send($url, $settings)
    {
        if (!$this->isValidDayQuota($settings)) {
            return [
                'phrase' => 'none',
                'message' => [
                    'error' => [
                        'message' => 'Daily quota reached',
                    ],
                ],
            ];
        }

        $context = stream_context_create(
            [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ],
                'http' => [
                    'method' => 'POST',
                    'header' => 'Content-Type: application/x-www-form-urlencoded',
                    'content' => http_build_query(
                        [
                            'product_url' => $url,
                            'json_api_key' => $settings['json_api_key'],
                        ]
                    ),
                ],
            ]
        );

        $domain = Configuration::get('PS_SHOP_DOMAIN_SSL');

        if (strpos(substr($domain, 0, 7), ':/') === false) {
            $domain = $this->getShopProtocol().$domain;
        }

        $result = Tools::file_get_contents($domain.'/modules/bl_google_indexing/api/GoogleApi.php', false, $context);

        if (empty($result)) {
            return ['phrase' => 'empty', 'message' => ''];
        }

        $resultArray = json_decode($result);

        IndexingApiLog::add($url, self::ACTION_UPDATE, $resultArray->phrase, json_encode($resultArray->message));

        return $resultArray;
    }

    public function sendAfterProductUpdate($productId, $langId, $settings)
    {
        $this->langId = (int)$langId;

        $link = new Link();
        $product = new Product($productId, false, $this->langId);
        $combinations = $this->getProductCombinations($product, $settings);

        if (empty($settings['json_api_key'])) {
            return false;
        }

        if (!empty($settings['indexing_only_active']) && empty($product->active)) {
            return false;
        }

        Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'blmod_indexing_api_product 
                WHERE `id_product` = "'.(int)$productId.'"');

        Db::getInstance()->Execute('INSERT INTO '._DB_PREFIX_.'blmod_indexing_api_product
            (`id_product`, `id_shop`, `updated_at`)
            VALUES
            ("'.(int)$productId.'", 1, "'.pSQL(date('Y-m-d H:i:s')).'")
        ');

        foreach ($combinations as $c) {
            $pageUrl = $this->getProductURL($product, $link, $c);
            $pageUrl = str_replace('?id_product_attribute=0', '', $pageUrl);

            if (!$this->isNewUrl($pageUrl)) {
                continue;
            }

            $this->send($pageUrl, $settings);
        }

        return true;
    }

    public function isNewUrl($pageUrl)
    {
        $r = Db::getInstance()->getValue('SELECT l.id
            FROM '._DB_PREFIX_.'blmod_indexing_api_log l 
            WHERE l.url = "'.pSQL($pageUrl).'" AND l.created_at > "'.pSQL(date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s').'-30seconds'))).'"');
            
        return empty($r);
    }

    public function getProductCombinations(Product $product, $settings)
    {
        $combinations = $product->getAttributesResume($this->langId, ' ', ', ');

        if (empty($combinations) || empty($settings['combination_indexing'])) {
            $combinations = [];
            $combinations[0]['id_product_attribute'] = 0;

            return $combinations;
        }

        $combinationsEmpty = [];
        $combinationsEmpty[] = ['id_product_attribute' => 0,];

        return array_merge($combinationsEmpty, $combinations);
    }

    public function getProductURL(Product $product, Link $link, $combination)
    {
        if (empty($combination['id_product_attribute'])) {
            return $link->getProductLink($product, null, null, null, $this->langId);
        }

        return $link->getProductLink($product, null, null, null, $this->langId, null, $combination['id_product_attribute'], Configuration::get('PS_REWRITING_SETTINGS'), false, true);
    }

    public function isValidDayQuota($settings)
    {
        if (empty($settings['requests_per_day'])) {
            return true;
        }

        $indexingApiLog = new IndexingApiLog();

        if ($indexingApiLog->countCurrentDayLogsTotal() >= $settings['requests_per_day']) {
            return false;
        }

        return true;
    }

    public function getShopProtocol()
    {
        if (method_exists('Tools', 'getShopProtocol')) {
            return Tools::getShopProtocol();
        }

        return (Configuration::get('PS_SSL_ENABLED') || (!empty($_SERVER['HTTPS'])
                && Tools::strtolower($_SERVER['HTTPS']) != 'off')) ? 'https://' : 'http://';
    }
}
