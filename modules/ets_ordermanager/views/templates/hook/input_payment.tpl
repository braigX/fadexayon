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
<input data-validate="isNegativePrice" data-required="1" class="input_change_order_inline" value="{$orderPayment->amount|escape:'html':'UTF-8'}" data-id="{$orderPayment->id|intval}" data-key="id_order_payment" data-table="order_payment" data-change="amount" data-order="{$id_order|intval}" type="text" />
<div class="input-group-addon">{Ets_ordermanager::getCurrencySign($orderPayment->id_currency)|escape:'html':'UTF-8'}</div>