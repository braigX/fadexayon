<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.txt
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to a newer
 * versions in the future. If you wish to customize this module for your needs
 * please refer to CustomizationPolicy.txt file inside our module for more information.
 *
 * @author Webkul IN
 * @copyright Since 2010 Webkul
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_5_2_0($module)
{
    $wkQueries = [
        'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'wk_sample_product_lang` (
            `id_sample_product` int(10) unsigned NOT NULL,
            `id_shop` int(10) unsigned NOT NULL,
            `id_lang` int(10) unsigned NOT NULL,
            `button_label` varchar(32) NOT NULL,
            `description` TEXT,
        PRIMARY KEY  (`id_sample_product`, `id_lang`, `id_shop`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8',
        'ALTER TABLE `' . _DB_PREFIX_ . 'wk_sample_product` ADD COLUMN `sample_file` varchar(512) NOT NULL DEFAULT 0',
        'ALTER TABLE `' . _DB_PREFIX_ . 'wk_sample_product_shop` ADD COLUMN `sample_file` varchar(512) NOT NULL DEFAULT 0',
    ];

    $dbInstance = Db::getInstance();
    $success = true;
    foreach ($wkQueries as $query) {
        $success &= $dbInstance->execute(trim($query));
    }

    if ($success) {
        $shopIds = Shop::getContextListShopID();
        $languages = Language::getLanguages(false);

        $wkSampleProductLangs = Db::getInstance()->executeS(
            'SELECT * FROM ' . _DB_PREFIX_ . 'wk_sample_product'
        );

        if ($wkSampleProductLangs) {
            foreach ($wkSampleProductLangs as $wkSampleProductLang) {
                foreach ($shopIds as $idShop) {
                    foreach ($languages as $language) {
                        $idlang = $language['id_lang'];
                        $isExists = Db::getInstance()->getRow(
                            'SELECT * FROM ' . _DB_PREFIX_ . 'wk_sample_product_lang WHERE
                            `id_sample_product` = ' . (int) $wkSampleProductLang['id_sample_product'] . '
                            AND `id_shop` = ' . (int) $idShop . '
                            AND `id_lang` = ' . (int) $idlang
                        );
                        if (!$isExists) {
                            $wkSql = 'INSERT INTO `' . _DB_PREFIX_ . 'wk_sample_product_lang` (`id_sample_product`,`id_shop`,
                            `id_lang`, `button_label`, `description`) VALUES
                            (' . (int) $wkSampleProductLang['id_sample_product'] . ', ' . (int) $idShop . ", '" . (int) $idlang . "',
                            '" . pSQL($wkSampleProductLang['button_label']) . "', '" . pSQL($wkSampleProductLang['description']) . "')";
                            $dbInstance->execute($wkSql);
                        }
                    }
                }
            }
        }
    }
    $module->registerHook('actionCartUpdateQuantityBefore');

    return true;
}
