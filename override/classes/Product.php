<?php
if (!defined('_PS_VERSION_')) {
    exit;
}
class Product extends ProductCore
{
    /*
    * module: amazzingfilter
    * date: 2025-04-16 11:57:55
    * version: 3.3.0
    */
    public static function getProductsProperties($id_lang, $query_result)
    {
        if (!empty(Context::getContext()->properties_not_required)) {
            return $query_result;
        } else {
            return parent::getProductsProperties($id_lang, $query_result);
        }
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    /*
    * module: wksampleproduct
    * date: 2026-01-29 11:42:05
    * version: 5.3.3
    */
    public static function priceCalculation(
        $id_shop,
        $id_product,
        $id_product_attribute,
        $id_country,
        $id_state,
        $zipcode,
        $id_currency,
        $id_group,
        $quantity,
        $use_tax,
        $decimals,
        $only_reduc,
        $use_reduc,
        $with_ecotax,
        &$specific_price,
        $use_group_reduction,
        $id_customer = 0,
        $use_customer_price = true,
        $id_cart = 0,
        $real_quantity = 0,
        $id_customization = 0
    ) {
        if (version_compare(_PS_VERSION_, '1.7.7.1', '>=')
            && ((int) $id_cart > 0)
            && Module::isEnabled('wksampleproduct')
        ) {
            Module::getInstanceByName('wksampleproduct');
            $objSampleCart = new WkSampleCart();
            $isSampleProduct = $objSampleCart->getSampleCartProduct($id_cart, $id_product, $id_product_attribute);
            if ($isSampleProduct) {
                $orderId = Order::getIdByCartId((int) $id_cart);
                if ($orderId) {
                    return Product::getPriceFromOrder(
                        $orderId,
                        $id_product,
                        (int) $id_product_attribute,
                        $use_tax,
                        true,
                        $with_ecotax
                    );
                }
            }
        }
        return parent::priceCalculation(
            $id_shop,
            $id_product,
            $id_product_attribute,
            $id_country,
            $id_state,
            $zipcode,
            $id_currency,
            $id_group,
            $quantity,
            $use_tax,
            $decimals,
            $only_reduc,
            $use_reduc,
            $with_ecotax,
            $specific_price,
            $use_group_reduction,
            $id_customer,
            $use_customer_price,
            $id_cart,
            $real_quantity,
            $id_customization
        );
    }
    /*
    * module: gmerchantfeedes
    * date: 2026-01-29 14:14:49
    * version: 1.5.2
    */
    public function __construct($id_product = null, $full = false, $id_lang = null, $id_shop = null, Context $context = null)
    {
        parent::__construct($id_product, $full, $id_lang, $id_shop, $context);
        if (Tools::getValue('id_country', false) && Configuration::get('GMERCHANTFEEDS_FORCE_COUNTY_PRODUCT')) {
            $id_country = (int)Tools::getValue('id_country');
            $country = new Country($id_country);
            if (Validate::isLoadedObject($country)) {
                Context::getContext()->country = $country;
            }
        }
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    /*
    * module: ets_seo
    * date: 2026-01-29 18:07:28
    * version: 3.1.3
    */
    public function getAnchor($id_product_attribute, $with_id = false)
    {
        if (Module::isEnabled('ets_seo') && (bool) Configuration::get('ETS_SEO_ENABLE_REMOVE_ID_IN_URL')) {
            if ((bool) Configuration::get('ETS_SEO_ENABLE_REMOVE_ATTR_ALIAS')) {
                return '';
            }
            if ((bool) Configuration::get('ETS_SEO_ENABLE_REMOVE_ID_ATTR_ALIAS')) {
                return parent::getAnchor($id_product_attribute, false);
            }
        }
        return parent::getAnchor($id_product_attribute, $with_id);
    }
    
    /*
    * module: ets_seo
    * date: 2026-01-29 18:07:28
    * version: 3.1.3
    */
    public function getImages($id_lang, Context $context = null)
    {
        $data = $this->coreGetImages($id_lang);
        if (Module::isEnabled('ets_seo') && $module = Module::getInstanceByName('ets_seo')) {
            
            $meta = $module->getCurrentMetaData();
            if (isset($meta['img_alt']) && $alts = $meta['img_alt']) {
                foreach ($data as &$item) {
                    if (is_array($alts)) {
                        $item['legend'] = isset($alts[$item['id_image']]) && $alts[$item['id_image']] ? $alts[$item['id_image']] : $item['legend'];
                    } else {
                        $item['legend'] = $alts;
                    }
                }
                unset($item);
            }
            return $data;
        }
        return $data;
    }
    
    /*
    * module: ets_seo
    * date: 2026-01-29 18:07:28
    * version: 3.1.3
    */
    public function coreGetImages($id_lang)
    {
        $context = Ets_Seo::getContextStatic();
        $cache_id = 'ProductCore::getImages_' . $this->id . '_' . (int) $id_lang . '-' . (int) $context->shop->id;
        if (!Cache::isStored($cache_id)) {
            $data = parent::getImages($id_lang);
            Cache::store($cache_id, $data);
            return $data;
        }
        return Cache::retrieve($cache_id);
    }
}
