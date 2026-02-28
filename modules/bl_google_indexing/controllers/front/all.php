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
    die('Not Allowed, Bl_Google_Indexing');
}

/**
 * Class Bl_Google_IndexingApiModuleFrontController
 *
 * /index.php?fc=module&module=bl_google_indexing&controller=all
 * /module/bl_google_indexing/all
 */
class Bl_Google_IndexingAllModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        include_once(dirname(__FILE__).'/../../IndexingApiLog.php');
        include_once(dirname(__FILE__).'/../../IndexingApi.php');

        $indexingApi = new IndexingApi();

        $settings = json_decode(htmlspecialchars_decode(Configuration::get('BLMOD_INDEXING_SETTINGS')), true);

        if (empty($settings['indexing_all_products'])) {
            die('disabled');
        }

        if (empty($settings['product_lang_id'])) {
            die('empty language param');
        }

        if (!$indexingApi->isValidDayQuota($settings)) {
            die('daily quota reached');
        }

        $products = $this->getProducts();

        if (empty($products)) {
            die('done, all');
        }

        $this->send($products, $settings);

        die('done');
    }

    protected function send($products, $settings)
    {
        $indexingApi = new IndexingApi();

        foreach ($products as $p) {
            foreach ($settings['product_lang_id'] as $langId) {
                $indexingApi->sendAfterProductUpdate($p['id_product'], $langId, $settings);
            }
        }
    }

    protected function getProducts()
    {
        return Db::getInstance()->executeS('SELECT p.id_product
            FROM '._DB_PREFIX_.'product_shop p
            LEFT JOIN '._DB_PREFIX_.'blmod_indexing_api_product b ON
            b.id_product = p.id_product
            WHERE p.id_shop = 1 AND p.active = 1 AND b.updated_at IS NULL
            LIMIT 3');
    }
}
