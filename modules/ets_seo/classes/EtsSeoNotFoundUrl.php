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

/**
 * Class EtsSeoNotFoundUrl
 * Model class for "ets_seo_not_found_url" table.
 *
 * @since 2.5.3
 */
class EtsSeoNotFoundUrl extends ObjectModel
{
    const PRIMARY_COLUMN = 'id_ets_seo_not_found_url';
    const TBL_NAME_SUR_FIX = 'ets_seo_not_found_url';

    /**
     * @var int
     */
    public $id_ets_seo_not_found_url;

    /**
     * @var int
     */
    public $id_shop;

    /**
     * @var string
     */
    public $url;

    /**
     * @var string
     */
    public $referer;

    /**
     * @var int
     */
    public $visit_count = 0;

    /**
     * @var int|string
     */
    public $last_visited_at;

    /**
     * @var array Contains object definition
     */
    public static $definition = [
        'table' => 'ets_seo_not_found_url',
        'primary' => 'id_ets_seo_not_found_url',
        'multilang_shop' => false,
        'fields' => [
            'id_ets_seo_not_found_url' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
            ],
            'id_shop' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
            ],
            'url' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
            ],
            'referer' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
            ],
            'visit_count' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
            ],
            'last_visited_at' => [
                'type' => self::TYPE_DATE,
                'validate' => 'isDate',
            ],
        ],
    ];

    /**
     * @param string $name
     *
     * @return null
     */
    public function __get($name)
    {
        if ('id' === $name) {
            return $this->{self::PRIMARY_COLUMN};
        }

        return null;
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        if ('id' === $name) {
            $this->{self::PRIMARY_COLUMN} = $value;
        } else {
            $this->{$name} = $value;
        }
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function __isset($name)
    {
        if ('id' === $name) {
            return isset($this->{self::PRIMARY_COLUMN});
        }

        return isset($this->{$name});
    }
    public function __construct($id = null, $id_lang = null, $id_shop = null, $translator = null)
    {
        parent::__construct($id, $id_lang, $id_shop, $translator);
        if (!$id) {
            // trick for mysql default "CURRENT_TIMESTAMP" value
            $this->last_visited_at = date('Y-m-d h:i:s');
            if ($id_shop && Validate::isUnsignedInt($id_shop)) {
                $this->id_shop = (int) $id_shop;
            }
        }
    }

    /**
     * @param string $url
     * @param int|null $shopId
     *
     * @return self|null
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public static function findOneByUrl($url, $shopId = null)
    {
        $sql = sprintf('SELECT `%s`, `id_shop` FROM `%s%s` WHERE `url` = "%s"', bqSQL(self::PRIMARY_COLUMN), _DB_PREFIX_, bqSQL(self::TBL_NAME_SUR_FIX), pSQL($url));
        if ($shopId && Validate::isUnsignedInt($shopId)) {
            $sql .= sprintf(' AND `id_shop` = %d', (int) $shopId);
        }

        $result = Db::getInstance()->getRow($sql);
        if ($result) {
            return new self($result[self::PRIMARY_COLUMN], null, $result['id_shop']);
        }

        return null;
    }

    /**
     * @param string $url
     * @param int|null $idShop
     *
     * @return \EtsSeoNotFoundUrl
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public static function firstOrNew($url, $idShop = null)
    {
        if (($rs = self::findOneByUrl($url, $idShop)) instanceof self && Validate::isLoadedObject($rs)) {
            return $rs;
        }
        $rs = new self(null, null, $idShop);
        $rs->url = $url;

        return $rs;
    }

    // </editor-fold>

    /**
     * @param \FrontControllerCore $controller
     * @param bool $force
     *
     * @return bool
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public static function checkAndUpdatePageNotFoundUrl($controller, $force = false)
    {
        if (!(bool) Configuration::get('ETS_SEO_ENABLE_RECORD_404_REQUESTS')) {
            return false;
        }
        $excludes = [
            '/js\//',
            '/\.js$/',
            '/\.map$/',
            '/\.svg$/',
            '/themes\/new-theme\/public/',
            '/page-not-found/',
            '/pagina-no-encontrada/',
            '/seite-nicht-gefunden/',
            '/favicon\.ico$/',
            '/\?controller=404/',
            '/modules?\//',
            '/\.jpg$/',
        ];
        if ($controller instanceof \PageNotFoundControllerCore || $force) {
            $url = $_SERVER['REQUEST_URI'];
            $url = Tools::str_replace_once(Ets_Seo::getContextStatic()->shop->getBaseURI(), '', $url);
            foreach ($excludes as $exclude) {
                if (preg_match($exclude, $url)) {
                    return false;
                }
            }
            $model = self::firstOrNew($url, Ets_Seo::getContextStatic()->shop->id);
            if (!$model->id_ets_seo_not_found_url) {
                $model->referer = isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] ? $_SERVER['HTTP_REFERER'] : null;
            }
            ++$model->visit_count;
            $model->save();

            return true;
        }

        return false;
    }
}
