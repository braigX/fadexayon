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

class Tag extends TagCore
{
    public function getProducts($associated = true, Context $context = null)
    {
        $list = parent::getProducts($associated,$context);
        if (!$list) {
            return $list;
        }
        foreach ($list as $index => $product) {
            if (IdxCustomizedProduct::isCustomizedById($product['id_product'])) {
                unset($list[$index]);
            }
        }
        return $list;
    }
}