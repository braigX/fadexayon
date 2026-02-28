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

{assign var="currencySymbolBeforeAmount" value=true}
{* Assign product price *}
{if ($order->getTaxCalculationMethod() == $smarty.const.PS_TAX_EXC)}
	{assign var=product_price value=($product['unit_price_tax_excl'] + $product['ecotax'])}
{else}
	{assign var=product_price value=$product['unit_price_tax_incl']}
{/if}

{if ($product['product_quantity'] > $product['customized_product_quantity'])}
<tr class="product-line-row">
	<td>{if isset($product.image) && $product.image->id}{$product.image_tag nofilter}{/if}</td>
	<td>
        <span class="product_price_show">
    		<a href="{$link->getAdminLink('AdminProducts', true,['id_product'=>$product.product_id])|escape:'html':'UTF-8'}&id_product={$product.product_id|intval}&updateproduct=1">
    			<span class="productName">{$product.product_name|escape:'html':'UTF-8'}</span><br />
    			{if $product.product_reference}{l s='Reference number:' mod='ets_ordermanager'} {$product.product_reference|escape:'html':'UTF-8'}<br />{/if}
    			{if $product.product_supplier_reference}{l s='Supplier reference:' mod='ets_ordermanager'} {$product.product_supplier_reference|escape:'html':'UTF-8'}{/if}
    		</a>
            {hook h='displayShopLicense' id_product=$product.product_id}
        </span>
        {if $can_edit}
            <div class="product_price_edit" style="display:none;">
                <input type="text" name="product_name" class="edit_product_name" value="{$product.product_name|escape:'html':'UTF-8'}"/>
            {hook h ='displayShopLicenseEditField' id_product=$product.product_id}
            </div>
        {/if}
        {if isset($product.pack_items) && $product.pack_items|@count > 0}<br>
            <button name="package" class="btn btn-default" type="button" onclick="TogglePackage('{$product['id_order_detail']|intval}'); return false;" value="{$product['id_order_detail']|intval}">{l s='Package content' mod='ets_ordermanager'}</button>
        {/if}
		<div class="row-editing-warning" style="display:none;">
			<div class="alert alert-warning">
				<strong>{l s='Editing this product line will remove the reduction and base price.' mod='ets_ordermanager'}</strong>
			</div>
		</div>
	</td>
	<td>
		<span class="product_price_show">{displayPrice price=$product_price currency=$currency->id}</span>
		{if $can_edit}
		<div class="product_price_edit" style="display:none;">
			<input type="hidden" name="product_id_order_detail" class="edit_product_id_order_detail" value="{$product['id_order_detail']|intval}" />
			<div class="form-group">
				<div class="fixed-width-xl">
					<div class="input-group">
						{if $currencySymbolBeforeAmount}<div class="input-group-addon">{$currency->sign|escape:'html':'UTF-8'} {l s='tax excl.' mod='ets_ordermanager'}</div>{/if}
						<input type="text" name="product_price_tax_excl" class="edit_product_price_tax_excl edit_product_price" value="{Tools::ps_round($product['unit_price_tax_excl'], 2)|floatval}"/>
						{if !$currencySymbolBeforeAmount}<div class="input-group-addon">{$currency->sign|escape:'html':'UTF-8'} {l s='tax excl.' mod='ets_ordermanager'}</div>{/if}
					</div>
				</div>
				<br/>
				<div class="fixed-width-xl">
					<div class="input-group">
						{if $currencySymbolBeforeAmount}<div class="input-group-addon">{$currency->sign|escape:'html':'UTF-8'} {l s='tax incl.' mod='ets_ordermanager'}</div>{/if}
						<input type="text" name="product_price_tax_incl" class="edit_product_price_tax_incl edit_product_price" value="{Tools::ps_round($product['unit_price_tax_incl'], 2)|floatval}"/>
						{if !$currencySymbolBeforeAmount}<div class="input-group-addon">{$currency->sign|escape:'html':'UTF-8'} {l s='tax incl.' mod='ets_ordermanager'}</div>{/if}
					</div>
				</div>
			</div>
		</div>
		{/if}
	</td>
	<td class="productQuantity text-center">
        {assign var='quantity_product' value = $product['product_quantity'] - $product['customized_product_quantity']}
		<span class="product_quantity_show{if $quantity_product  > 1} badge{/if}">{$quantity_product|intval}</span>
		{if $can_edit}
		<span class="product_quantity_edit" style="display:none;">
			<input type="text" name="product_quantity" class="edit_product_quantity" value="{$product['product_quantity']|intval}"/>
		</span>
		{/if}
	</td>
	{if $display_warehouse}
		<td>
			{$product.warehouse_name|escape:'html':'UTF-8'}
			{if $product.warehouse_location}
				<br>{l s='Location' mod='ets_ordermanager'}: <strong>{$product.warehouse_location|escape:'html':'UTF-8'}</strong>
			{/if}
		</td>
	{/if}
	{if ($order->hasBeenPaid())}
		<td class="productQuantity text-center">
			{if !empty($product['amount_refund'])}
				{l s='%s (%s)' sprintf=[$product['product_quantity_refunded'],$product['amount_refund']] mod='ets_ordermanager'}
			{/if}
			<input type="hidden" value="{$product['quantity_refundable']|escape:'html':'UTF-8'}" class="partialRefundProductQuantity" />
			<input type="hidden" value="{(Tools::ps_round($product_price, 2) * ($product['product_quantity'] - $product['customizationQuantityTotal']))|floatval}" class="partialRefundProductAmount" />
			{if count($product['refund_history'])}
				<span class="tooltip">
					<span class="tooltip_label tooltip_button">+</span>
					<span class="tooltip_content">
					<span class="title">{l s='Refund history' mod='ets_ordermanager'}</span>
					{foreach $product['refund_history'] as $refund}
						{l s='%s - %s' sprintf=[{dateFormat date=$refund.date_add}, {displayPrice price=$refund.amount_tax_incl}] mod='ets_ordermanager'}<br />
					{/foreach}
					</span>
				</span>
			{/if}
		</td>
	{/if}
	{if Ets_ordermanager::hasBeenDelivered($order) || $order->hasProductReturned()}
		<td class="productQuantity text-center">
			{$product['product_quantity_return']|intval}
			{if count($product['return_history'])}
				<span class="tooltip">
					<span class="tooltip_label tooltip_button">+</span>
					<span class="tooltip_content">
					<span class="title">{l s='Return history' mod='ets_ordermanager'}</span>
					{foreach $product['return_history'] as $return}
						{l s='%s - %s - %s' sprintf=[{dateFormat date=$return.date_add}, $return.product_quantity, $return.state] mod='ets_ordermanager'}<br />
					{/foreach}
					</span>
				</span>
			{/if}
		</td>
	{/if}
	{if isset($stock_location_is_available) && $stock_location_is_available}<td class="productQuantity location text-center">{$product['location']|escape:'html':'UTF-8'}</td>{/if}
	{if $stock_management}<td class="productQuantity product_stock text-center">{$product['current_stock']|escape:'html':'UTF-8'}</td>{/if}
	<td class="total_product">
		{displayPrice price=(Tools::ps_round($product_price, 2) * ($product['product_quantity'] - $product['customizationQuantityTotal'])) currency=$currency->id}
	</td>
	<td colspan="2" style="display: none;" class="add_product_fields">&nbsp;</td>
	<td class="cancelCheck standard_refund_fields current-edit" style="display:none">
		<input type="hidden" name="totalQtyReturn" id="totalQtyReturn" value="{$product['product_quantity_return']|intval}" />
		<input type="hidden" name="totalQty" id="totalQty" value="{$product['product_quantity']|intval}" />
		<input type="hidden" name="productName" id="productName" value="{$product['product_name']|escape:'html':'UTF-8'}" />
	{if ((!$order->hasBeenDelivered() OR Configuration::get('PS_ORDER_RETURN')) AND (int)($product['product_quantity_return']) < (int)($product['product_quantity']))}
		<input type="checkbox" name="id_order_detail[{$product['id_order_detail']|intval}]" id="id_order_detail[{$product['id_order_detail']|intval}]" value="{$product['id_order_detail']|intval}" onchange="setCancelQuantity(this, {$product['id_order_detail']|intval}, {($product['product_quantity'] - $product['customizationQuantityTotal'] - $product['product_quantity_return'] - $product['product_quantity_refunded'])|intval})" {if ($product['product_quantity_return'] + $product['product_quantity_refunded'] >= $product['product_quantity'])}disabled="disabled" {/if}/>
	{else}
		--
	{/if}
	</td>
	<td class="cancelQuantity standard_refund_fields current-edit" style="display:none">
	{if ($product['product_quantity_return'] + $product['product_quantity_refunded'] >= $product['product_quantity'])}
		<input type="hidden" name="cancelQuantity[{$product['id_order_detail']|escape:'html':'UTF-8'}]" value="0" />
	{elseif (!$order->hasBeenDelivered() OR Configuration::get('PS_ORDER_RETURN'))}
		<input type="text" id="cancelQuantity_{$product['id_order_detail']|intval}" name="cancelQuantity[{$product['id_order_detail']|intval}]" onchange="checkTotalRefundProductQuantity(this)" value="" />
	{/if}

	{if $product['customizationQuantityTotal']}
		{assign var=productQuantity value=($product['product_quantity']-$product['customizationQuantityTotal'])}
	{else}
		{assign var=productQuantity value=$product['product_quantity']}
	{/if}
	{if ($order->hasBeenDelivered())}
		{$product['product_quantity_refunded']|intval}/{($productQuantity-$product['product_quantity_refunded'])|intval}
	{elseif ($order->hasBeenPaid())}
		{$product['product_quantity_return']|intval}/{$productQuantity|intval}
	{else}
		0
	{/if}
	</td>
	<td class="partial_refund_fields current-edit" colspan="2" style="display:none; width: 250px; min-width: 250px;">
		{if $product['quantity_refundable'] > 0}
		{if ($order->getTaxCalculationMethod() == $smarty.const.PS_TAX_EXC)}
			{assign var='amount_refundable' value=$product['amount_refundable']}
		{else}
			{assign var='amount_refundable' value=$product['amount_refundable_tax_incl']}
		{/if}
		<div class="form-group">
			<div class="{if $product['amount_refundable'] > 0}col-lg-4{else}col-lg-12{/if}">
				<label class="control-label">
					{l s='Quantity:' mod='ets_ordermanager'}
				</label>
				<div class="input-group">
					<input onchange="checkPartialRefundProductQuantity(this)" type="text" name="partialRefundProductQuantity[{$product['id_order_detail']|intval}]" value="0" />
					<div class="input-group-addon">/ {$product['quantity_refundable']|intval}</div>
				</div>
			</div>
			<div class="{if $product['quantity_refundable'] > 0}col-lg-8{else}col-lg-12{/if}">
				<label class="control-label">
					<span class="title_box ">{l s='Amount' mod='ets_ordermanager'}</span>
					<small class="text-muted">({$smarty.capture.TaxMethod|escape:'html':'UTF-8'})</small>
				</label>
				<div class="input-group">
					{if $currencySymbolBeforeAmount}<div class="input-group-addon">{$currency->sign|escape:'html':'UTF-8'}</div>{/if}
					<input onchange="checkPartialRefundProductAmount(this)" type="text" name="partialRefundProduct[{$product['id_order_detail']|intval}]" />
					{if !$currencySymbolBeforeAmount}<div class="input-group-addon">{$currency->sign|escape:'html':'UTF-8'}</div>{/if}
				</div>
        <p class="help-block"><i class="icon-warning-sign"></i> {l s='(Max %s %s)' sprintf=[Tools::displayPrice(Tools::ps_round($amount_refundable, 2), $currency->id),$smarty.capture.TaxMethod] mod='ets_ordermanager'}</p>
			</div>
		</div>
		{/if}
	</td>
	{if $can_edit}
	<td class="product_invoice" style="display: none;">
		{if sizeof($invoices_collection)}
		<select name="product_invoice" class="edit_product_invoice">
			{foreach from=$invoices_collection item=invoice}
			<option value="{$invoice->id|escape:'html':'UTF-8'}" {if $invoice->id == $product['id_order_invoice']}selected="selected"{/if}>
				#{Configuration::get('PS_INVOICE_PREFIX', $current_id_lang, null, $order->id_shop)|escape:'html':'UTF-8'}{$invoice->number|escape:'html':'UTF-8'}
			</option>
			{/foreach}
		</select>
		{else}
		&nbsp;
		{/if}
	</td>
	<td class="product_action text-right">
		{* edit/delete controls *}
		<div class="btn-group" id="btn_group_action">
			<button type="button" class="btn btn-default edit_product_change_link">
				<i class="icon-pencil"></i>
				{l s='Edit' mod='ets_ordermanager'}
			</button>
			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
				<span class="caret"></span>
			</button>
			<ul class="dropdown-menu" role="menu">
				<li>
					<a href="#" class="delete_product_line">
						<i class="icon-trash"></i>
						{l s='Delete' mod='ets_ordermanager'}
					</a>
				</li>
			</ul>
		</div>
		{* Update controls *}
		<button type="button" class="btn btn-default submitProductChange" style="display: none;">
			<i class="icon-ok"></i>
			{l s='Update' mod='ets_ordermanager'}
		</button>
		<button type="button" class="btn btn-default cancel_product_change_link" style="display: none;">
			<i class="icon-remove"></i>
			{l s='Cancel' mod='ets_ordermanager'}
		</button>
	</td>
	{/if}
</tr>
   {if isset($product.pack_items) && $product.pack_items|@count > 0}
    <tr>
        <td colspan="8" style="width:100%">
            <table style="width: 100%; display:none;" class="table" id="pack_items_{$product['id_order_detail']|intval}">
            <thead>
                <th style="width:15%;">&nbsp;</th>
                <th style="width:15%;">&nbsp;</th>
                <th style="width:50%;"><span class="title_box ">{l s='Product' mod='ets_ordermanager'}</span></th>
                <th style="width:10%;"><span class="title_box ">{l s='Qty' mod='ets_ordermanager'}</th>
                {if $stock_management}<th><span class="title_box ">{l s='Available quantity' mod='ets_ordermanager'}</span></th>{/if}
                <th>&nbsp;</th>
            </thead>
            <tbody>
            {foreach from=$product.pack_items item=pack_item}
                {if !empty($pack_item.active)}
                    <tr class="product-line-row" {if isset($pack_item.image) && $pack_item.image->id && isset($pack_item.image_size)} height="{($pack_item['image_size'][1] + 7)|escape:'html':'UTF-8'}"{/if}>
                        <td>{l s='Package item' mod='ets_ordermanager'}</td>
                        <td>{if isset($pack_item.image) && $pack_item.image->id}{$pack_item.image_tag nofilter}{/if}</td>
                        <td>
                            <a href="{$link->getAdminLink('AdminProducts', true)|escape:'html':'UTF-8'}&id_product={$pack_item.id_product|intval}&updateproduct=1">
                                <span class="productName">{$pack_item.name|escape:'html':'UTF-8'}</span><br />
                                {if $pack_item.reference}{l s='Ref:' mod='ets_ordermanager'} {$pack_item.reference|escape:'html':'UTF-8'}<br />{/if}
                                {if $pack_item.supplier_reference}{l s='Ref Supplier:' mod='ets_ordermanager'} {$pack_item.supplier_reference|escape:'html':'UTF-8'}{/if}
                            </a>
                        </td>
                        <td class="productQuantity">
                            <span class="product_quantity_show{if (int)$pack_item.pack_quantity > 1} red bold{/if}">{$pack_item.pack_quantity|intval}</span>
                        </td>
                        {if $stock_management}<td class="productQuantity product_stock">{$pack_item.current_stock|escape:'html':'UTF-8'}</td>{/if}
                        <td>&nbsp;</td>
                    </tr>
                {/if}
            {/foreach}
            </tbody>
            </table>
        </td>
    </tr>
    {/if}
{/if}
