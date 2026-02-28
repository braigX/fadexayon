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
class Product extends ProductCore
{
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

    /**
     * {@inheritDoc}
     */
    public function getImages($id_lang, Context $context = null)
    {
        $data = $this->coreGetImages($id_lang);
        if (Module::isEnabled('ets_seo') && $module = Module::getInstanceByName('ets_seo')) {
            /** @var \Ets_Seo $module */
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

    /**
     * Improve parent::getImages with cache enabled
     *
     * @param int $id_lang
     *
     * @return array
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
