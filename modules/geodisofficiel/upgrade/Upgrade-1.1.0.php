<?php
/**
 * 2021 GEODIS.
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
 *  @copyright 2021 GEODIS
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

function upgrade_module_1_1_0()
{
    $date = date("Y-m-d H:i:s");

    $res = Db::getInstance()->execute(
        "INSERT INTO `" . _DB_PREFIX_ . GEODIS_NAME_SQL
        . "_translation` (`key`, `id_lang`, `value`, `date_add`, `date_upd`)
     VALUES ('geodis.Admin.OrdersGrid.index.action.printLabels.form.label', 1, 'Vos étiquettes sont prêtes : ','$date','$date'),
        ('geodis.Admin.OrdersGrid.index.action.printLabels.form.download.btn.label', 1, 'Télécharger','$date','$date'),
        ('geodis.Admin.OrdersGrid.index.action.printLabels', 1, 'Imprimer les étiquettes','$date','$date'),
        ('geodis.Admin.OrdersGrid.index.action.sendShipments', 1, 'Transmettre les envois','$date','$date'),
        ('geodis.Admin.OrdersGrid.index.action.error.no.order.checked', 1, 'Au moins une commande doit être cochée','$date','$date'),
        ('geodis.Admin.OrdersGrid.index.action.error.failed.to.find.all.orders', 1, 'Impossible de trouver toutes les commandes dans la base de données','$date','$date'),
        ('geodis.Admin.OrdersGrid.index.action.printLabels.failed', 1, 'L’envoi d’une ou plusieurs commandes n’a pas été créé. Veuillez créer les envois de ces commandes pour imprimer les étiquettes : ','$date','$date'),
        ('geodis.Admin.OrdersGrid.index.action.sendShipments.failed', 1, 'Un ou plusieurs envois n’ont pas pu être transmis car n’ont pas été créés ou les étiquettes n’ont pas été imprimées ou ont déjà été transmis. Veuillez vérifier les envois non transmis : ','$date','$date'),
        ('geodis.Admin.OrdersGrid.index.action.error.no.print.ws.answer', 1, 'Aucune réponse du Web Service GEODIS reçue','$date','$date'),
        ('geodis.Admin.OrdersGrid.index.action.error.no.send.shipments.ws.answer', 1, 'Aucune réponse du Web Service GEODIS reçue','$date','$date'),
        ('geodis.Admin.OrdersGrid.index.action.success.shipments', 1, 'Les envois suivant ont été transmis avec succès : ','$date','$date'),
        ('geodis.Admin.OrdersGrid.index.action.downloadFile.error.data.empty', 1, 'Le nom du fichier ou son contenu est vide','$date','$date'),
        ('geodis.Admin.ConfigurationBack.ajax.price.fixed.label', 1, 'Part fixe quelque soit le montant ou le poids', '$date','$date'),
        ('geodis.Admin.ConfigurationBack.ajax.price.according.amount.weight.label', 1, 'Définit selon le montant ou le poids par zone', '$date','$date'),
        ('geodis.Admin.ConfigurationBack.ajax.price.parameters.label', 1, 'Vous devez paramétrer les tarifs dans le menu Transporteurs de Prestashop', '$date','$date')"
    );
    $res &= Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . GEODIS_NAME_SQL
        . '_carrier` ADD `enable_price_fixed` TINYINT');
    $res &= Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . GEODIS_NAME_SQL
        . '_carrier` ADD `enable_price_according` TINYINT');
    $res &= Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . GEODIS_NAME_SQL
        . '_carrier` ADD `enable_free_shipping` TINYINT');
    $res &= Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . GEODIS_NAME_SQL
        . '_shipment` ADD `is_endlife` TINYINT(1) UNSIGNED DEFAULT 0 AFTER `date_upd`');

    return $res;
}
