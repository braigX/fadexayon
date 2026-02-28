<?php
/**
 * 2024 Novatis Agency - www.novatis-paris.fr.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@novatis-paris.fr so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    NOVATIS <info@novatis-paris.fr>
 *  @copyright 2024 NOVATIS
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

function upgrade_module_1_0_1()
{
    $date = date("Y-m-d H:i:s");
    $res = Db::getInstance()->execute(
        'ALTER TABLE `' . _DB_PREFIX_ . GEODIS_NAME_SQL . '_log'
        . '` CHANGE `id_log` `id_geodis_log` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT'
    );
    $res &= Db::getInstance()->execute(
        "INSERT INTO `" . _DB_PREFIX_ . GEODIS_NAME_SQL
        . "_translation` (`key`, `id_lang`, `value`, `date_add`, `date_upd`)
     VALUES ('geodis.*.*.menu.cron', 1, 'Job Cron','$date','$date'),
            ('geodis.*.*.cron.description', 1,
            'Pour exécuter vos tâches cron, veuillez insérer la ligne suivante dans votre fichier cron job:',
            '$date','$date')"
    );
    $res &= Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . GEODIS_NAME_SQL
        . '_prestation` ADD `code_sa` VARCHAR(50) NOT NULL AFTER `id_prestation`, '
        .' ADD `code_client` VARCHAR(50) NOT NULL AFTER `code_sa`');

    $res &= GeodisServiceSynchronize::getInstance()->syncCustomerConfiguration(true);

    return $res;
}
