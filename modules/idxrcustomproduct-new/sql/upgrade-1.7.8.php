<?php
/**
 * 2007-2026 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * @author    Innova Deluxe SL
 * @copyright 2026 Innova Deluxe SL
 *
 * @license   INNOVADELUXE
 */

$sql = array();

$sql[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'idxrcustomproduct_runtime_customisations`
    ADD COLUMN IF NOT EXISTS `id_customized_product` int(11) NOT NULL DEFAULT 0 AFTER `id_product`';

$sql[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'idxrcustomproduct_runtime_customisations`
    ADD KEY `idxr_runtime_customized_product` (`id_customized_product`)';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}

return true;
