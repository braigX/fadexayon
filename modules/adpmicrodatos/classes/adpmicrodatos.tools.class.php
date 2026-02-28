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

class AdpmicrodatosTools
{
    private $name = 'AdpmicrodatosTools';

    const ADPMICRODATOS_TIPO_IMAGEN = 'adpmicrodatos_tipo_imagen';
    const ADPMICRODATOS_NUMERO_PAGINA = 'adpmicrodatos_numero_pagina';
    const ADPMICRODATOS_ORDEN_PAGINA = 'adpmicrodatos_orden_pagina';
    const ADPMICRODATOS_TIPO_ORDEN_PAGINA = 'adpmicrodatos_tipo_orden_pagina';
    const ADPMICRODATOS_GET_STORES = 'adpmicrodatos_get_stores';
    const ADPMICRODATOS_STORE_HOURS = 'adpmicrodatos_store_hours';

    public static function getHttp()
    {
        $ruta = (Configuration::get('PS_SSL_ENABLED') || Tools::usingSecureMode()) ? 'https://' : 'http://';

        return $ruta;
    }

    public static function getLinkRoot($num_idiomas_activados = 0)
    {
        $ruta = Tools::getShopDomainSsl(true);
        if (!Configuration::get('PS_SSL_ENABLED') || !Configuration::get('PS_SSL_ENABLED_EVERYWHERE')) {
            $ruta = Tools::getShopDomain(true);
        }

        $ruta .= __PS_BASE_URI__;

        if ($num_idiomas_activados > 1) {
            $ruta .= Context::getContext()->language->iso_code . '/';
        }

        return $ruta;
    }

    public function movePositionHookAfterInstall($nameHooks, $name)
    {
        foreach ($nameHooks as $nameHook) {
            $hookID = (int) Hook::getIdByName($nameHook);
            $moduleInstance = Module::getInstanceByName($name);
            $moduleInstance->updatePosition($hookID, 0, 1);
        }
    }

    public static function getDataByVersionPrestashop($tipo, $input_data = '', $numpagina = 0)
    {
        switch ($tipo) {
            case self::ADPMICRODATOS_TIPO_IMAGEN:
                return ImageType::getFormattedName($input_data);
            case self::ADPMICRODATOS_NUMERO_PAGINA:
                $pageValue = Tools::getValue('page');
                if (!empty($pageValue)) {
                    $numpagina = $pageValue;
                }

                return $numpagina;
            case self::ADPMICRODATOS_ORDEN_PAGINA:
                $defaultOrderByValue = 'position';

                $orderImplodeValue = Tools::getValue('order');
                if (empty($orderImplodeValue)) {
                    return $defaultOrderByValue;
                }

                $orderExplodeValue = explode('.', $orderImplodeValue);
                if (3 != count($orderExplodeValue)) {
                    return $defaultOrderByValue;
                }

                $orderByValue = $orderExplodeValue[1];
                if (empty($orderByValue)) {
                    return $defaultOrderByValue;
                }

                return $orderByValue;

            case self::ADPMICRODATOS_TIPO_ORDEN_PAGINA:
                $defaultWayValue = 'ASC';

                $orderImplodeValue = Tools::getValue('order');
                if (empty($orderImplodeValue)) {
                    return $defaultWayValue;
                }

                $orderExplodeValue = explode('.', $orderImplodeValue);
                if (3 != count($orderExplodeValue)) {
                    return $defaultWayValue;
                }

                $wayValue = $orderExplodeValue[2];
                if (empty($wayValue)) {
                    return $defaultWayValue;
                }

                return $wayValue;
            case self::ADPMICRODATOS_GET_STORES:
                return Db::getInstance()->executeS('
                        SELECT * FROM `' . _DB_PREFIX_ . 'store` s
                        ' . Shop::addSqlAssociation('store', 's') . '
                        INNER JOIN `' . _DB_PREFIX_ . 'store_lang` sl ON (s.`id_store` = sl.`id_store`)
                        WHERE s.`active` = 1 and sl.`id_lang` = ' . Context::getContext()->language->id);
            case self::ADPMICRODATOS_STORE_HOURS:
                return json_decode($input_data);
        }

        return '';
    }

    public static function getProductPrice($productId, $id_product_attribute = null)
    {
        $tax_product = (PS_TAX_INC == Product::$_taxCalculationMethod);

        switch (Configuration::get('ADP_SET_CONFIGURATION_PRODUCT_TAXES')) {
            case 1:
                $tax_product = false;
                break;
            case 2:
                $tax_product = true;
                break;
        }

        if (version_compare(_PS_VERSION_, '1.7.7', '<')) {
            return Tools::ps_round(Product::getPriceStatic((int) $productId, $tax_product, $id_product_attribute, 6, null, false, true), (int) Configuration::get('PS_PRICE_DISPLAY_PRECISION'));
        }

        $product = new Product($productId, false, Context::getContext()->language->id, Context::getContext()->shop->id);

        return $product->getPrice($tax_product, $id_product_attribute);
    }

    public static function getImageNames($type)
    {
        return array_map(function ($image_type) {
            return $image_type['name'];
        }, ImageType::getImagesTypes($type, true));
    }

    public static function getDataComboImageTypes()
    {
        return [
            'product' => [
                'selected' => Configuration::get('ADP_PRODUCT_IMAGE_TYPE'),
                'types' => self::getImageNames('products'),
            ],
            'category' => [
                'selected' => Configuration::get('ADP_CATEGORY_IMAGE_TYPE'),
                'types' => self::getImageNames('categories'),
            ],
            'manufacturer' => [
                'selected' => Configuration::get('ADP_MANUFACTURER_IMAGE_TYPE'),
                'types' => self::getImageNames('manufacturers'),
            ],
        ];
    }

    public static function getDataComboRefundPolicies()
    {
        return [
            'returnpolicycategory' => [
                'selected' => Configuration::get('ADP_RETURN_POLICY_CATEGORIES'),
                'types' => ['', 'MerchantReturnFiniteReturnWindow', 'MerchantReturnNotPermitted', 'MerchantReturnUnlimitedWindow', 'MerchantReturnUnspecified'],
            ],
            'returnMethod' => [
                'selected' => Configuration::get('ADP_RETURN_METHOD'),
                'types' => ['', 'KeepProduct', 'ReturnAtKiosk', 'ReturnByMail', 'ReturnInStore'],
            ],
            'returnFees' => [
                'selected' => Configuration::get('ADP_RETURN_FEES'),
                'types' => ['', 'FreeReturn', 'OriginalShippingFees', 'RestockingFees', 'ReturnFeesCustomerResponsibility', 'ReturnShippingFees'],
            ],
        ];
    }

    public static function getImagesCombination($id_product_combination)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
            SELECT a.`id_image`
            FROM `' . _DB_PREFIX_ . 'product_attribute_image` a
            ' . Shop::addSqlAssociation('product_attribute', 'a') . '
            WHERE a.`id_product_attribute` = ' . (int) $id_product_combination . '
        ');
    }
}
