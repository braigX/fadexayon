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
 
class AdminPdfController extends AdminPdfControllerCore
{
    public function processGenerateInvoicePdfAll()
	{
        $ets_ordermanager = Module::getInstanceByName('ets_ordermanager');
        $list_id_order = Tools::getValue('list_id_order');
        if(Ets_ordermanager::validateArray($list_id_order,'isInt'))
            $ets_ordermanager->generateInvoicePDFByIdOrderAll($list_id_order);
	}
    public function processGenerateDeliverySlipPDFAll()
    {
        $ets_ordermanager = Module::getInstanceByName('ets_ordermanager');
        $list_id_order = Tools::getValue('list_id_order');
        if(Ets_ordermanager::validateArray($list_id_order,'isInt'))
            $ets_ordermanager->generateDeliverySlipPDFByIdOrderAll($list_id_order);
    }
    public function processGenerateDeliveryLabelByIdOrderAll()
    {
        $ets_ordermanager = Module::getInstanceByName('ets_ordermanager');
        $list_id_order = Tools::getValue('list_id_order');
        if(Ets_ordermanager::validateArray($list_id_order,'isInt'))
            $ets_ordermanager->generateDeliveryLabelByIdOrderAll($list_id_order);
    }
}