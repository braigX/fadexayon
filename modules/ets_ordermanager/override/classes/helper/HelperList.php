<?php
/**
 * 2007-2018 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
 
class HelperList extends HelperListCore
{
    public function displayRestoreLink($token, $id, $name = null)
    {
        $ets_ordermanager = Module::getInstanceByName('ets_ordermanager');
        unset($name);
        return $ets_ordermanager->displayRestoreLink($this->currentIndex,$this->identifier,$id,$this->table,$token != null ? $token : $this->token);
    }
    public function displayPrintDeliveryLabelLink($token, $id, $name = null)
    {
        $ets_ordermanager = Module::getInstanceByName('ets_ordermanager');
        unset($name);
        return $ets_ordermanager->displayPrintDeliveryLabelLink($this->currentIndex,$this->identifier,$id,$this->table,$token != null ? $token : $this->token);
    }
    public function displayLoginAsCustomerLink($token, $id, $name = null)
    {
        $ets_ordermanager = Module::getInstanceByName('ets_ordermanager');
        unset($name);
        return $ets_ordermanager->displayLoginAsCustomerLink($this->currentIndex,$this->identifier,$id,$this->table,$token != null ? $token : $this->token);
    }
}