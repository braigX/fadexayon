<?php
/**
 * 2007-2023 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * @author    Innova Deluxe SL
 * @copyright 2023 Innova Deluxe SL

 * @license   INNOVADELUXE
 */

$sql = array();

$sql[] = 'ALTER TABLE `'._DB_PREFIX_.'idxrcustomproduct_components_lang` MODIFY `description` TEXT;';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
