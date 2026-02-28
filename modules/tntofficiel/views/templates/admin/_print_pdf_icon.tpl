{**
* TNT OFFICIAL MODULE FOR PRESTASHOP.
*
* @author    Inetum <inetum.com>
* @copyright 2016-2024 Inetum, 2016-2024 TNT
* @license   https://opensource.org/licenses/MIT MIT License
*}

{* Generate HTML code for printing Invoice Icon with link *}
<span class="btn-group-action">
	<span class="btn-group">
	{if Configuration::get('PS_INVOICE') && $objPSOrder->invoice_number}
		<a class="btn btn-default _blank" href="{$hrefGenerateInvoicePDF|escape:'html':'UTF-8'}">
			<i class="icon-file-text"></i>
		</a>
	{/if}
	{* Generate HTML code for printing Delivery Icon with link *}
	{if $objPSOrder->delivery_number}
		<a class="btn btn-default _blank" href="{$hrefGenerateDeliverySlipPDF|escape:'html':'UTF-8'}">
			<i class="icon-truck"></i>
		</a>
	{/if}
	</span>
</span>
