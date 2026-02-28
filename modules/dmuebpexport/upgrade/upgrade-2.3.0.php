<?php
/**
 * NOTICE OF LICENSE.
 *
 * This source file is subject to a commercial license from SARL DREAM ME UP
 * Use, copy, modification or distribution of this source file without written
 * license agreement from the SARL DREAM ME UP is strictly forbidden.
 *
 *   .--.
 *   |   |.--..-. .--, .--.--.   .--.--. .-.   .  . .,-.
 *   |   ;|  (.-'(   | |  |  |   |  |  |(.-'   |  | |   )
 *   '--' '   `--'`-'`-'  '  `-  '  '  `-`--'  `--`-|`-'
 *        w w w . d r e a m - m e - u p . f r       '
 *
 * @author    Dream me up <prestashop@dream-me-up.fr>
 * @copyright 2007 - 2024 Dream me up
 * @license   All Rights Reserved
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_2_3_0($object)
{
    unset($object);
    $sql = [];
    $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'dmuebp_report` (
				`id_report` int(8) NOT NULL AUTO_INCREMENT,
				  `type` varchar(16) DEFAULT NULL,
				  `ecriture_originale` mediumtext,
				  `difference` double DEFAULT NULL,
				  `is_avoir` int(1) DEFAULT NULL,
				  `ecriture_corrigee` mediumtext,
				  `num_piece` varchar(32) DEFAULT NULL,
				  `id_order` int(8) DEFAULT NULL,
				  PRIMARY KEY (`id_report`)
				) ENGINE = ' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';
    foreach ($sql as $query) {
        if (!Db::getInstance()->execute($query)) {
            return false;
        }
    }

    return true;
}
