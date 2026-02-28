<?php
/**
 * 2007-2021 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * @author    Innova Deluxe SL
 * @copyright 2021 Innova Deluxe SL
 * @license   INNOVADELUXE
 */

class Cart extends CartCore
{
    public function duplicate()
    {
        $new_cart = parent::duplicate();
        if ((bool) Module::isEnabled('idxrcustomproduct')) {
            $module = Module::getInstanceByName('idxrcustomproduct');
            $module->duplicateCartInfo($this->id, $new_cart->id);
        }
        return $new_cart;
    }
}