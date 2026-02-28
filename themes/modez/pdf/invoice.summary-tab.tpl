{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 *}
{*<table id="summary-tab" width="100%">
	<tr>
		<th class="header small" valign="middle">{l s='Numéro de facture' d='Shop.Pdf' pdf='true'}</th>
		<th class="header small" valign="middle">{l s='Date de facture' d='Shop.Pdf' pdf='true'}</th>
		<th class="header small" valign="middle">{l s='Référence achat' d='Shop.Pdf' pdf='true'}</th>
		<th class="header small" valign="middle">{l s='Date de commande' d='Shop.Pdf' pdf='true'}</th>
		{if $addresses.invoice->vat_number}
			<th class="header small" valign="middle">{l s='Num TVA' d='Shop.Pdf' pdf='true'}</th>
		{/if}
	</tr>
	<tr>
		<td class="center small white">{$title|escape:'html':'UTF-8'}</td>
		<td class="center small white">{dateFormat date=$order->invoice_date full=0}</td>
		<td class="center small white">{$order->getUniqReference()}</td>
		<td class="center small white">{dateFormat date=$order->date_add full=0}</td>
		{if $addresses.invoice->vat_number}
			<td class="center small white">
				{$addresses.invoice->vat_number}
			</td>
		{/if}
	</tr>
</table>*}





<table id="summary-tab" width="100%">
  <tr>
    <th class="header small" valign="middle">
      {if $order->id_lang == 1}Numéro de facture
      {elseif $order->id_lang == 2}Factuurnummer
      {elseif $order->id_lang == 3}Rechnungsnummer
      {elseif $order->id_lang == 4}Número de factura
      {elseif $order->id_lang == 5}Numero fattura
      {else}Numéro de facture
      {/if}
    </th>
    <th class="header small" valign="middle">
      {if $order->id_lang == 1}Date de facture
      {elseif $order->id_lang == 2}Factuurdatum
      {elseif $order->id_lang == 3}Rechnungsdatum
      {elseif $order->id_lang == 4}Fecha de factura
      {elseif $order->id_lang == 5}Data fattura
      {else}Date de facture
      {/if}
    </th>
    <th class="header small" valign="middle">
      {if $order->id_lang == 1}Référence achat
      {elseif $order->id_lang == 2}Bestelreferentie
      {elseif $order->id_lang == 3}Bestellreferenz
      {elseif $order->id_lang == 4}Referencia de compra
      {elseif $order->id_lang == 5}Riferimento ordine
      {else}Référence achat
      {/if}
    </th>
    <th class="header small" valign="middle">
      {if $order->id_lang == 1}Date de commande
      {elseif $order->id_lang == 2}Besteldatum
      {elseif $order->id_lang == 3}Bestelldatum
      {elseif $order->id_lang == 4}Fecha de pedido
      {elseif $order->id_lang == 5}Data ordine
      {else}Date de commande
      {/if}
    </th>
    {if $addresses.invoice->vat_number}
      <th class="header small" valign="middle">
        {if $order->id_lang == 1}Num TVA
        {elseif $order->id_lang == 2}BTW-nummer
        {elseif $order->id_lang == 3}USt-Nummer
        {elseif $order->id_lang == 4}NIF
        {elseif $order->id_lang == 5}Partita IVA
        {else}Num TVA
        {/if}
      </th>
    {/if}
  </tr>

  <tr>
    <td class="center small white">{$title|escape:'html':'UTF-8'}</td>
    <td class="center small white">{dateFormat date=$order->invoice_date full=0}</td>
    <td class="center small white">{$order->getUniqReference()}</td>
    <td class="center small white">{dateFormat date=$order->date_add full=0}</td>
    {if $addresses.invoice->vat_number}
      <td class="center small white">{$addresses.invoice->vat_number}</td>
    {/if}
  </tr>
</table>
