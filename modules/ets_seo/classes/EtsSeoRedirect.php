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

class EtsSeoRedirect extends ObjectModel
{
    /**
     * @var int
     */
    public $id_ets_seo_redirect;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $url;

    /**
     * @var string
     */
    public $target;

    /**
     * @var string
     */
    public $type;

    /**
     * @var bool
     */
    public $active;

    /**
     * @var int
     */
    public $id_shop;

    public static $definition = [
        'table' => 'ets_seo_redirect',
        'primary' => 'id_ets_seo_redirect',
        'multilang_shop' => false,
        'fields' => [
            'id_ets_seo_redirect' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
            ],
            'name' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
            ],
            'url' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
            ],
            'target' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
            ],
            'type' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
            ],
            'active' => [
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
            ],

            'id_shop' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
            ],
        ],
    ];

    /**
     * @param string $url
     *
     * @return string
     */
    private static function removeShopBaseUrl($url)
    {
        $shopBase = Ets_Seo::getContextStatic()->shop->getBaseURL(true, true);
        $url = str_replace($shopBase, '', $url);

        return ltrim($url, '/');
    }

    /**
     * @param string $current_url
     * @param \Context|\ContextCore|null $context
     * @param bool $active
     *
     * @return array|false
     */
    public static function getTypeUrlRedirect($current_url, $context = null, $active = false)
    {
        if (!$context) {
            $context = Ets_Seo::getContextStatic();
        }
        $url = trim(self::removeShopBaseUrl($current_url));
        $where_active = $active ? ' AND active = 1' : '';
        if (false !== strpos($url, '/*')) {
            return Db::getInstance()->getRow('SELECT `type`, `target` FROM `' . _DB_PREFIX_ . "ets_seo_redirect` WHERE '" . pSQL($url) . "' REGEXP (REPLACE(`url`, '*', '(.*)')) " . pSQL($where_active) . ' AND `id_shop`=' . (int) $context->shop->id);
        }

        return Db::getInstance()->getRow('SELECT `type`, `target` FROM `' . _DB_PREFIX_ . "ets_seo_redirect` WHERE `url` = '" . pSQL($url) . "'" . pSQL($where_active) . ' AND `id_shop`=' . (int) $context->shop->id);
    }

    /**
     * @param string $url
     * @param int|null $idShop
     *
     * @return \EtsSeoRedirect|null
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public static function findOneByUrl($url, $idShop = null)
    {
        return ($id = self::getIdByUrl($url, $idShop)) ? new self($id) : null;
    }

    /**
     * @param string $url
     * @param int|null $idShop
     *
     * @return int|null
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public static function getIdByUrl($url, $idShop = null)
    {
        $sql = sprintf('SELECT `id_ets_seo_redirect` FROM `%sets_seo_redirect` WHERE `url` = "%s"', _DB_PREFIX_, pSQL($url));
        if ($idShop && Validate::isUnsignedInt($idShop)) {
            $sql .= sprintf(' AND `id_shop` = %d', (int) $idShop);
        }

        return ($rs = Db::getInstance()->getValue($sql)) ? (int) $rs : null;
    }

    /**
     * @param string $url
     * @param int|null $id
     *
     * @return false|string
     */
    public static function checkSeoUrl($url, $id = null)
    {
        $query = (new \DbQuery())->select('url')->from('ets_seo_redirect');
        $query->where(sprintf('url = "%s" AND id_shop = %d', pSQL($url), (int) Ets_Seo::getContextStatic()->shop->id));
        if ($id) {
            $query->where('id_ets_seo_redirect != ' . (int) $id);
        }

        return Db::getInstance()->getValue($query);
    }
}
