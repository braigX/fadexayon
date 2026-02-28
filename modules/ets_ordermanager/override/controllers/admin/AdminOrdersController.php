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

class AdminOrdersController extends AdminOrdersControllerCore
{
    public function __construct()
    {
        parent::__construct();
        $ets_ordermanager  = Module::getInstanceByName('ets_ordermanager');
        if(Ets_ordermanager::checkViewModule())
        {
            $ets_ordermanager->_postOrder();
            $this->_select .= ',a.id_customer,
                a.current_state,
                a.id_address_invoice,
                a.id_address_delivery,
                country.id_country,
                a.id_order AS id,
                a.id_order,
                a.id_order as images,  
                a.id_carrier,
                c.`firstname`,
                c.`lastname`,
                a.order_note,c.email';
            $fields_list = $ets_ordermanager->getFieldsListOrder();
            if(isset($fields_list['address1']) || isset($fields_list['city']) || isset($fields_list['company'])|| isset($fields_list['vat_number']) || isset($fields_list['postcode']) || isset($fields_list['number_phone']) || isset($fields_list['phone']))
            {
                $this->_select .=',address.id_address,address.address1,address.vat_number,address.city,address.company,address.postcode,IF(address.phone, address.`phone`, address.`phone_mobile`) as number_phone,IF(address.phone, address.`phone`, address.`phone_mobile`) as phone';
            }
            if(isset($fields_list['address_invoice']))
            {
                $this->_select .=',address_invoice.address1 as address_invoice';
                $this->_join .= ' LEFT JOIN `' . _DB_PREFIX_ . 'address` address_invoice ON address_invoice.id_address = a.id_address_invoice';
            }
            if(isset($fields_list['caname']))
            {
                $this->_select .=',carrier.name as caname';
                $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'carrier` carrier ON carrier.id_carrier = a.id_carrier';
            }
            if(isset($fields_list['shipping_cost_tax_excl']) || isset($fields_list['shipping_cost_tax_incl']) || isset($fields_list['id_order_carrier']) || isset($fields_list['tracking_number']))
            {
                $this->_select .=',oca.shipping_cost_tax_excl,oca.shipping_cost_tax_incl,oca.id_order_carrier,oca.tracking_number';
                $this->_join .=' LEFT JOIN `'._DB_PREFIX_.'order_carrier` oca ON (oca.id_order=a.id_order)';
            }
            if(isset($fields_list['last_message']) || isset($fields_list['id_customer_thread']) || isset($fields_list['id_customer_message']))
            {
                $this->_select .=',cm.message as last_message,cm.id_customer_thread,cm.id_customer_message';
                $this->_join .=' LEFT JOIN `'._DB_PREFIX_.'customer_thread` ct ON (ct.id_order= a.id_order) 
                                LEFT JOIN (SELECT max(id_customer_message) as id_customer_message, id_customer_thread,message FROM `'._DB_PREFIX_.'customer_message` WHERE id_employee=0 GROUP BY id_customer_thread) cm ON (cm.id_customer_thread=ct.id_customer_thread)';
            }
            if(isset($fields_list['customer_group']))
            {
                $this->_select .=',grl.name as customer_group';
                $this->_join .=' LEFT JOIN `'._DB_PREFIX_.'customer_group` cg ON(c.id_customer= cg.id_customer AND c.id_default_group=cg.id_group)
                LEFT JOIN `'._DB_PREFIX_.'group_lang` grl ON (grl.id_group= cg.id_group AND grl.id_lang="'.(int)$this->context->language->id.'")';
            }
            if(isset($fields_list['images']))
            {
                $this->_select .= ',od.product_name';
                $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'order_detail` od ON (od.id_order=a.id_order)';
            }
            if(Module::isInstalled('ets_payment_with_fee') && Module::isEnabled('ets_payment_with_fee') && isset($fields_list['fee']))
            {
                $this->_select .=',epo.fee';
                $this->_join .='LEFT JOIN `'._DB_PREFIX_.'ets_paymentmethod_order` epo ON (epo.id_order = a.id_order)';
            }
            $this->_group .=' GROUP BY a.id_order';
            if(Tools::isSubmit('viewtrash'))
            {
                $this->_where .= ' AND a.deleted=1';
            }
            else
                $this->_where .= ' AND a.deleted!=1';
            $this->fields_list = $fields_list;
            if(Tools::isSubmit('expot_csv_all_order'))
            {
                if(($orderBox =  Tools::getValue('orderBox')) && Ets_ordermanager::validateArray($orderBox,'isInt'))
                {
                    $this->_where .= ' AND a.id_order IN ('.implode(',',array_map('intval',$orderBox)).')';
                    $this->getList($this->context->language->id);
                    $ets_ordermanager->exportOrderToCSV($this->fields_list,$this->_list);
                }
                else
                    $ets_ordermanager->exportOrderToCSV($this->fields_list,false);
            }
        }
    }
    public function postProcess()
    {
        if(Configuration::get('ETS_ODM_ENABLE_INSTANT_FILTER') && !Tools::isSubmit('submitFilterorder'))
        {
            $this->filter =false;
            $this->action ='reset_filters';
        } 
        parent::postProcess();
        if($this->redirect_after && (Tools::isSubmit('filterviewtrash') || Tools::isSubmit('viewtrash')))
            $this->redirect_after .='&viewtrash=1';
    }
    public function ajaxProcessEditProductOnOrder(){
        $ets_ordermanager  = Module::getInstanceByName('ets_ordermanager');
        if (Tools::version_compare(_PS_VERSION_,'1.7','>=')) 
            $ets_ordermanager->ajaxProcessEditProductOnOrder();
        else
            $ets_ordermanager->ajaxProcessEditProductOnOrder16();
    }
    public function printLastMessage($last_message, $tr)
    {
        if($last_message)
        {
            $ets_ordermanager  = Module::getInstanceByName('ets_ordermanager');
            return $ets_ordermanager->displayLastMessage($last_message,$tr);
        }
        else
            return '';
    }
    public function renderKpis()
    {
        if(Tools::isSubmit('viewtrash'))
            return '';
        return parent::renderKpis();
    }
    public function doDeleteProductLineValidation(OrderDetail $order_detail, Order $order)
    {
        $ets_ordermanager = Module::getInstanceByName('ets_ordermanager');
        $ets_ordermanager->doDeleteProductLineValidation($order_detail,$order);  
    }
    public function printOrderProducts($id_order, $tr)
    {
        $ets_ordermanager = Module::getInstanceByName('ets_ordermanager');
        return $ets_ordermanager->printOrderProducts($id_order, $tr);
    }
}
