{*
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
* needs, please contact us for extra customization service at an affordable price
*
*  @author ETS-Soft <etssoft.jsc@gmail.com>
*  @copyright  2007-2023 ETS-Soft
*  @license    Valid for 1 website (or project) for each purchase of license
*  International Registered Trademark & Property of ETS-Soft
*}
{if $odm_order->invoice_number}
    <a class="btn tooltip-link js-link-row-action dropdown-item inline-dropdown-item" href="{$ets_ordermanager->getLinkAdminController('admin_orders_generate_invoice_pdf',['orderId'=>$odm_order->id])|escape:'html':'UTF-8'}" data-confirm-message="" data-toggle="pstooltip" data-placement="top" data-original-title="View invoice" data-clickable-row="">
        <i class="material-icons">receipt</i>
    </a>
{/if}
{if $odm_order->delivery_number}
    <a class="btn tooltip-link js-link-row-action dropdown-item inline-dropdown-item" href="{$ets_ordermanager->getLinkAdminController('admin_orders_generate_delivery_slip_pdf',['orderId'=>$odm_order->id])|escape:'html':'UTF-8'}" data-confirm-message="" data-toggle="pstooltip" data-placement="top" data-original-title="View delivery slip" data-clickable-row="">
        <i class="material-icons">local_shipping</i>
    </a>
{/if}