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

require_once __DIR__ . '/traits/EtsSeoGetInstanceTrait.php';

class EtsSeoSetting
{
    use EtsSeoGetInstanceTrait;

    public $isNewTheme = false;
    public $context;

    public function __construct($context = null)
    {
        if (!$this->context) {
            $this->context = $context ? $context : Ets_Seo::getContextStatic();
        }
        $this->isNewTheme = $this->getRequestContainer() ? true : false;
    }

    public function getRequestContainer()
    {
        return Ets_Seo::getRequestContainer();
    }

    public function getSocialImage($path)
    {
        return basename($path);
    }

    public function isJson($string)
    {
        json_decode((string)$string);

        return JSON_ERROR_NONE == json_last_error();
    }

    public function getMinorKeyphrase($string)
    {
        if ($string && $this->isJson($string)) {
            $data = json_decode($string, true);
            $minor = [];
            foreach ($data as $item) {
                $minor[] = $item['value'];
            }

            return implode(',', $minor);
        }

        return '';
    }

    /**
     * @param string $type
     * @param array $link_rewrites
     * @param int|null $id
     * @param \Context|\ContextCore|null $context
     *
     * @return false|string
     */
    public static function validateLinkRewrite($type, $link_rewrites = [], $id = null, $context = null)
    {
        if (!(int) Configuration::get('ETS_SEO_ENABLE_REMOVE_ID_IN_URL')) {
            return false;
        }
        if (!$context) {
            $context = Ets_Seo::getContextStatic();
        }
        $table = '';
        $linkRewriteCol = 'link_rewrite';
        $idCol = '';
        $error = false;
        $join = false;
        $joinTable = '';
        switch ($type) {
            case 'product':
                $table = 'product_lang';
                $join = true;
                $joinTable = 'product';
                $idCol = 'id_product';
                break;
            case 'category':
                $table = 'category_lang';
                $join = true;
                $joinTable = 'category';
                $idCol = 'id_category';

                return false;
            case 'cms':
                $table = 'cms_lang';
                $join = true;
                $joinTable = 'cms';
                $idCol = 'id_cms';
                break;
            case 'cms_category':
                $table = 'cms_category_lang';
                $join = true;
                $joinTable = 'cms_category';
                $idCol = 'id_cms_category';
                break;
            case 'meta':
                $table = 'meta_lang';
                $linkRewriteCol = 'url_rewrite';
                $idCol = 'id_meta';
                break;
        }
        if (!$table) {
            return $error;
        }
        $filterId = '';
        if ($id) {
            $filterId = ' AND l.`' . (string) $idCol . '` !=' . (int) $id;
        }
        foreach ($link_rewrites as $id_lang => $link_rewrite) {
            $langCheck = Language::getLanguage($id_lang);
            if (!$langCheck || !(int) $langCheck['active'] || !$link_rewrite) {
                continue;
            }
            $sql = sprintf('SELECT l.* from `%s%s` l', _DB_PREFIX_, bqSQL($table));
            if ($join) {
                $sql .= sprintf(' RIGHT JOIN `%s%s` jt ON (l.`%s` = jt.`%s`)', _DB_PREFIX_, bqSQL($joinTable), bqSQL($idCol), bqSQL($idCol));
            }
            $sql .= sprintf(' WHERE l.`%s` = "%s" AND l.`id_lang` = %d AND l.`id_shop` = %d', bqSQL($linkRewriteCol), pSQL((string) $link_rewrite), (int) $id_lang, (int) $context->shop->id);
            $sql .= $filterId;
            $duplicate = Db::getInstance()->getValue(
                $sql
            );
            if ($duplicate) {
                $error = $link_rewrite . ' (' . Language::getIsoById($id_lang) . ')';
            }
            break;
        }

        return $error;
    }

    public static function checkLinkRewriteAjax($controller, $linkRewrites, $id = null, $isCmsCate = false)
    {
        $type = null;
        switch ($controller) {
            case 'AdminProducts':
                $type = 'product';
                break;
        }
        if ($type) {
            $dataLinks = [];
            foreach ($linkRewrites as $linkRewrite) {
                $dataLinks[$linkRewrite['id_lang']] = $linkRewrite['value'];
            }
            $error = self::validateLinkRewrite($type, $dataLinks, (int) $id);
            if ($error) {
                exit(json_encode([
                    'success' => false,
                    'error' => $error,
                ]));
            }
        }
        if ($isCmsCate) {
        }
        exit(json_encode([
            'success' => true,
            'error' => '',
        ]));
    }

    public static function isMetaTemplateConfigured($controller, $is_cms_cate = false)
    {
        $title = 'ETS_SEO_%s_META_TITLE';
        $desc = 'ETS_SEO_%s_META_DESC';
        $force = 'ETS_SEO_%s_FORCE_USE_META_TEMPLATE';
        $img_alt = 'ETS_SEO_%s_META_IMG_ALT';
        $key = false;
        switch ($controller) {
            case 'AdminProducts':
                $key = 'PROD';
                break;
            case 'AdminCategories':
                $key = 'CATEGORY';
                break;
            case 'AdminCmsContent':
                if ($is_cms_cate) {
                    $key = 'CMS_CATE';
                } else {
                    $key = 'CMS';
                }
                break;
            case 'AdminManufacturers':
                $key = 'MANUFACTURER';
                break;
            case 'AdminSuppliers':
                $key = 'SUPPLIER';
                break;
        }
        $languages = Language::getLanguages(false);
        $result = [];
        if ($key) {
            foreach ($languages as $lang) {
                $forceConf = Configuration::get(sprintf($force, $key));
                $result[$lang['id_lang']] = [
                    'title' => Configuration::get(sprintf($title, $key), $lang['id_lang']),
                    'desc' => Configuration::get(sprintf($desc, $key), $lang['id_lang']),
                    'force' => $forceConf,
                    'isForce' => (bool) $forceConf,
                    'imgAlt' => Configuration::get(sprintf($img_alt, $key), $lang['id_lang']) ?: false,
                ];
            }
        }

        return $result;
    }

    public static function getAnalysisScore($type, $id)
    {
        switch ($type) {
            case 'product':
                $data = EtsSeoProduct::getSeoProduct($id);
                break;
            case 'category':
                $data = EtsSeoCategory::getSeoCategory($id);
                break;
            case 'cms':
                $data = EtsSeoCms::getSeoCms($id);
                break;
            case 'cms_category':
                $data = EtsSeoCmsCategory::getSeoCmsCategory($id);
                break;
            case 'meta':
                $data = EtsSeoMeta::getSeoMeta($id);
                break;
            case 'manufacturer':
                $data = EtsSeoManufacturer::getSeoManufacturer($id);
                break;
            case 'supplier':
                $data = EtsSeoSupplier::getSeoSupplier($id);
                break;
            default:
                $data = [];
                break;
        }
        if (isset($data) && $data) {
            $result = [];
            foreach ($data as $item) {
                $item['score_analysis'] = json_decode($item['score_analysis'], true);
                $item['content_analysis'] = json_decode($item['content_analysis'], true);
                $result[$item['id_lang']] = $item;
            }

            return $result;
        }

        return [];
    }
}
