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

{*
{l s='We have logged your return request.' d='Shop.Pdf' pdf='true'}<br />
{l s='Your package must be returned to us within' d='Shop.Pdf' pdf='true'} {$return_nb_days} {l s='days of receiving your order.' d='Shop.Pdf' pdf='true'}<br /><br />

<table id="summary-tab" width="100%">
	<tr>
		<th class="header small" valign="middle">{l s='Return Number' d='Shop.Pdf' pdf='true'}</th>
		<th class="header small" valign="middle">{l s='Date' d='Shop.Pdf' pdf='true'}</th>
	</tr>
	<tr>
		<td class="center small white">{'%06d'|sprintf:$order_return->id}</td>
		<td class="center small white">{dateFormat date=$order_return->date_add full=0}</td>
	</tr>
</table>
*}



{if $order->id_lang == 1}Nous avons enregistré votre demande de retour.
{elseif $order->id_lang == 2}We hebben uw retouraanvraag geregistreerd.
{elseif $order->id_lang == 3}Wir haben Ihre Rücksendeanfrage protokolliert.
{elseif $order->id_lang == 4}Hemos registrado su solicitud de devolución.
{elseif $order->id_lang == 5}Abbiamo registrato la richiesta di reso.
{else}Nous avons enregistré votre demande de retour.
{/if}
<br />

{if $order->id_lang == 1}Votre colis doit nous être retourné dans
{elseif $order->id_lang == 2}Uw pakket moet binnen
{elseif $order->id_lang == 3}Ihr Paket muss innerhalb von
{elseif $order->id_lang == 4}Su paquete debe ser devuelto en
{elseif $order->id_lang == 5}Il pacco deve essere restituito entro
{else}Votre colis doit nous être retourné dans
{/if} 
{$return_nb_days} 
{if $order->id_lang == 1}jours suivant la réception de votre commande.
{elseif $order->id_lang == 2}dagen na ontvangst van uw bestelling.
{elseif $order->id_lang == 3}Tagen nach Erhalt Ihrer Bestellung.
{elseif $order->id_lang == 4}días de recibir su pedido.
{elseif $order->id_lang == 5}giorni dal ricevimento del vostro ordine.
{else}jours suivant la réception de votre commande.
{/if}
<br /><br />

<table id="summary-tab" width="100%">
    <tr>
        <th class="header small" valign="middle">
            {if $order->id_lang == 1}Numéro de retour
            {elseif $order->id_lang == 2}Retournummer
            {elseif $order->id_lang == 3}Rücksendenummer
            {elseif $order->id_lang == 4}Número de devolución
            {elseif $order->id_lang == 5}Numero di reso
            {else}Numéro de retour
            {/if}
        </th>
        <th class="header small" valign="middle">
            {if $order->id_lang == 1}Date
            {elseif $order->id_lang == 2}Datum
            {elseif $order->id_lang == 3}Datum
            {elseif $order->id_lang == 4}Fecha
            {elseif $order->id_lang == 5}Data
            {else}Date
            {/if}
        </th>
    </tr>
    <tr>
        <td class="center small white">{'%06d'|sprintf:$order_return->id}</td>
        <td class="center small white">{dateFormat date=$order_return->date_add full=0}</td>
    </tr>
</table>
