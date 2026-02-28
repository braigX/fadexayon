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

<div class="table-responsive">
	<table class="table" id="shipping_table">
		<thead>
			<tr>
				<th>
					<span class="title_box ">{l s='Date' mod='ets_ordermanager'}</span>
				</th>
				<th>
					<span class="title_box ">&nbsp;</span>
				</th>
				<th>
					<span class="title_box ">{l s='Carrier' mod='ets_ordermanager'}</span>
				</th>
				<th>
					<span class="title_box ">{l s='Weight' mod='ets_ordermanager'}</span>
				</th>
				<th>
					<span class="title_box ">{l s='Shipping cost' mod='ets_ordermanager'}</span>
				</th>
				<th>
					<span class="title_box ">{l s='Tracking number' mod='ets_ordermanager'}</span>
				</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			{foreach from=$order->getShipping() item=line}
			<tr>
				<td>
                    <span class="span_change_order">{dateFormat date=$line.date_add full=true}</span>
                    <div class="wapper-change-order">
                        <input data-validate="isDate" data-required="0" type="text" value="{$line.date_add|escape:'html':'UTF-8'}" class="input_change_order_inline datepicker2" data-id="{$line.id_order_carrier|intval}" data-key="id_order_carrier" data-table="order_carrier" data-change="date_add" data-order="{$order->id|intval}" /> 
                    </div>
                </td>
				<td>&nbsp;</td>
				<td>
                    <span class="span_change_order">{$line.carrier_name|escape:'html':'UTF-8'}</span>
                    <div class="wapper-change-order">
                        <select name="shipping_carrier" id="shipping_carrier" class="input_change_order_inline" data-validate="isUnsignedId" data-required="1" data-order="{$order->id|intval}" data-id="{$line.id_order_carrier|intval}" data-key="id_order_carrier" data-table="order_carrier" data-change="id_carrier" >
							{foreach from=$ets_carrier_list item=carrier}
								<option value="{$carrier.id_carrier|intval}"{if $carrier.id_carrier==$line.id_carrier} selected="selected"{/if}>{$carrier.name|escape:'html':'UTF-8'} {if isset($carrier.delay)}({$carrier.delay|escape:'html':'UTF-8'}){/if}</option>
							{/foreach}
						</select>
                    </div>
                </td>
				<td class="weight">
                    <span class="span_change_order">{$line.weight|string_format:"%.3f"|escape:'html':'UTF-8'} {Configuration::get('PS_WEIGHT_UNIT')|escape:'html':'UTF-8'}</span>
                    <div class="wapper-change-order">
                        <div class="input-group">
                            <input data-validate="isFloat" data-required="0" type="text" value="{$line.weight|string_format:"%.3f"|escape:'html':'UTF-8'}" class="input_change_order_inline" data-id="{$line.id_order_carrier|intval}" data-key="id_order_carrier" data-table="order_carrier" data-change="weight" data-order="{$order->id|intval}" />
                            <div class="input-group-addon">{Configuration::get('PS_WEIGHT_UNIT')|escape:'html':'UTF-8'}</div>
                        </div>
                    </div>    
                </td>
				<td class="text-left">
                    <span class="span_change_order">
    					{if $order->getTaxCalculationMethod() == $smarty.const.PS_TAX_INC}
    						{displayPrice price=$line.shipping_cost_tax_incl currency=$currency->id}
    					{else}
    						{displayPrice price=$line.shipping_cost_tax_excl currency=$currency->id}
    					{/if}
                    </span>
                    <div class="wapper-change-order">
                        <div class="input-group">
                            <input data-validate="isFloat" data-required="0" type="text" value="{if $order->getTaxCalculationMethod() == $smarty.const.PS_TAX_INC}{$line.shipping_cost_tax_incl|floatval}{else}{$line.shipping_cost_tax_excl|floatval}{/if}" class="input_change_order_inline" data-id="{$line.id_order_carrier|intval}" data-key="id_order_carrier" data-table="order_carrier" data-change="{if $order->getTaxCalculationMethod() == $smarty.const.PS_TAX_INC}shipping_cost_tax_incl{else}shipping_cost_tax_excl{/if}" data-order="{$order->id|intval}" />
                            <div class="input-group-addon">{$currency->sign|escape:'html':'UTF-8'} {if $order->getTaxCalculationMethod() == $smarty.const.PS_TAX_INC}{l s='tax incl.' mod='ets_ordermanager'}{else}{l s='tax excl.' mod='ets_ordermanager'}{/if}</div>
                        </div>
                    </div>
				</td>
				<td>
					<span class="shipping_number_show">
                        <span class="span_change_order">
                            {if $line.url && $line.tracking_number}<a class="_blank" href="{$line.url|replace:'@':$line.tracking_number|escape:'html':'UTF-8'}">{$line.tracking_number|escape:'html':'UTF-8'}</a>{else}{$line.tracking_number|escape:'html':'UTF-8'}{/if}
                        </span>
                        <div class="wapper-change-order">
                            <input data-validate="isTrackingNumber" data-required="0" type="text" value="{$line.tracking_number|escape:'html':'UTF-8'}" class="input_change_order_inline" data-id="{$line.id_order_carrier|intval}" data-key="id_order_carrier" data-table="order_carrier" data-change="tracking_number" data-order="{$order->id|intval}" />
                        </div>
                    </span>
				</td>
				<td class="text-right">
                    {if  $line.can_edit}
                        <div class="group-action-order">
                            <a class="edit edit_order_inline btn btn-default" href="#" title="{l s='Edit' mod='ets_ordermanager'}">
                            <i class="icon-pencil"></i>
                            {l s='Edit' mod='ets_ordermanager'}
                            </a>
                        </div>
                        <div class="group-action-update-order" style="display:none">
                            <button class="btn btn-default" type="button" name="submitOrderChangeInLine">
                                <i class="icon-ok"></i>{l s='Update' mod='ets_ordermanager'}
                            </button>
                            <button class="btn btn-default cancel_order_change_link" type="button"><i class="icon-remove"></i>{l s='Cancel' mod='ets_ordermanager'}</button>
                        </div>
                    {/if}
				</td>
			</tr>
			{/foreach}
		</tbody>
	</table>
</div>

