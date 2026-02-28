<?php
/**
 * URL SEO Manager SQL Install
 */

$sql = array();

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'url_seo_manager` (
    `id_url_seo` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_shop` INT(11) UNSIGNED NOT NULL DEFAULT 1,
    `url_pattern` VARCHAR(255) NOT NULL,
    `is_regex` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
    `robots` VARCHAR(64) DEFAULT NULL,
    `canonical` VARCHAR(255) DEFAULT NULL,
    `hreflang` TEXT DEFAULT NULL,
    `active` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
    PRIMARY KEY (`id_url_seo`),
    KEY `idx_shop_url` (`id_shop`, `url_pattern`),
    KEY `idx_active` (`active`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
