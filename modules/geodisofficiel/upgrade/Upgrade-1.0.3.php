<?php
/**
 * 2020 GEODIS.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@geodis.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    GEODIS <contact@geodis.com>
 *  @copyright 2020 GEODIS
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

function upgrade_module_1_0_3()
{
    $date = date("Y-m-d H:i:s");

    $res = Db::getInstance()->execute(
        "INSERT INTO `" . _DB_PREFIX_ . GEODIS_NAME_SQL
        . "_translation` (`key`, `id_lang`, `value`, `date_add`, `date_upd`)
     VALUES ('geodis.Admin.info.locality.desti.change', 1, 'La localité du destinataire a été corrigée par :','$date','$date'),
            ('geodis.Admin.info.locality.exped.change', 1,
            'La localité de l\'expéditeur a été corrigée par :',
            '$date','$date')"
    );

    return $res;
}
