<?php
/**
 * 2007-2023 ETS-Soft
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please contact us for extra customization service at an affordable price
 *
 * @author ETS-Soft <etssoft.jsc@gmail.com>
 * @copyright  2007-2023 ETS-Soft
 * @license    Valid for 1 website (or project) for each purchase of license
 *  International Registered Trademark & Property of ETS-Soft
 */

class Order extends OrderCore
{
    public static function getCustomerOrders($id_customer, $show_hidden_status = false, Context $context = null)
    {
        $res =parent::getCustomerOrders($id_customer, $show_hidden_status,$context);
        if($res)
        {
            $values = array();
            foreach ($res as $key => $val) 
            {
                if($val['deleted']) 
                    unset($res[$key]);
                else
                   $values[] = $val; 
            }
            return $values;
        }
        return array();
    }
    public function hasBeenShipped()
    {
        $action = Tools::getValue('action');
        $controller = Tools::getValue('controller');
        if(($action=='addProductOnOrder' || $action=='editProductOnOrder') && $controller=='AdminOrders')
            return false;
        return parent::hasBeenShipped();
    }
    public function refreshShippingCost()
    {
        $ets_ordermanager = Module::getInstanceByName('ets_ordermanager');
        $ets_ordermanager->refreshShippingCost($this);
        if($this->invoice_number)
        {
            $id_order_invoice = (int)Db::getInstance()->getValue('SELECT id_order_invoice FROM `'._DB_PREFIX_.'order_invoice` WHERE id_order='.(int)$this->id);
            if($id_order_invoice)
            {
                $order_invoice = new OrderInvoice($id_order_invoice);
                    $this->setInvoiceDetails($order_invoice);
            } 
            else
                $this->setInvoice();
        }
        return $this;
    }
    public function hasBeenDelivered()
    {
        $action = Tools::getValue('action');
        $controller = Tools::getValue('controller');
        if(($action=='addProductOnOrder' || $action=='editProductOnOrder') && $controller=='AdminOrders')
            return false;
        return parent::hasBeenDelivered();

    }
}