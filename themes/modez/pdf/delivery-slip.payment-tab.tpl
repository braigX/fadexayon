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
{*<table id="payment-tab" width="100%" cellpadding="4" cellspacing="0">
	<tr>
		<td class="payment center small grey bold" width="44%">{l s='Mode de paiement' d='Shop.Pdf' pdf='true'}</td>
		<td class="payment left white" width="56%">
			<table width="100%" border="0">
				{foreach from=$order_invoice->getOrderPaymentCollection() item=payment}
					<tr>
						<td class="right small">{$payment->payment_method}</td>
						<td class="right small">{displayPrice currency=$payment->id_currency price=$payment->amount}</td>
					</tr>
				{foreachelse}
					<tr>
						<td>{l s='Pas de paiement' d='Shop.Pdf' pdf='true'}</td>
					</tr>
				{/foreach}
			</table>
		</td>
	</tr>
</table>*}

<table id="payment-tab" width="100%" cellpadding="4" cellspacing="0">
  <tr>
    <td class="payment center small grey bold" width="44%">
      {if $order->id_lang == 1}
        Mode de paiement
      {elseif $order->id_lang == 2}
        Betaalmethode
      {elseif $order->id_lang == 3}
        Zahlungsmethode
      {elseif $order->id_lang == 4}
        Método de pago
      {elseif $order->id_lang == 5}
        Metodo di pagamento
      {else}
        Payment Method
      {/if}
    </td>
    <td class="payment left white" width="56%">
      <table width="100%" border="0">
        {foreach from=$order_invoice->getOrderPaymentCollection() item=payment}
          {assign var="method" value=$payment->payment_method|escape:'html':'UTF-8'}
          <tr>
            <td class="right small">
              {if $order->id_lang == 1} {* 🇫🇷 French *}
                {$method}
              {elseif $order->id_lang == 2} {* 🇳🇱 Dutch *}
                {if $method == 'Chèque'}Cheque
                {elseif $method == 'Paiement CB'}Kaartbetaling
                {elseif $method == 'Paiement comptant à la livraison (Cash on delivery...)'}Betaling bij levering
                {elseif $method == 'Paiement E-CARTEBLEUE'}E-Carte Bleue
                {elseif $method == 'Paiement MASTERCARD'}Mastercard-betaling
                {elseif $method == 'Paiement par carte bancaire'}Bankkaartbetaling
                {elseif $method == 'Paiement VISA'}Visa-betaling
                {elseif $method == 'PayPal'}PayPal
                {elseif $method == 'Sogecommerce'}Sogecommerce
                {elseif $method == 'Transfert bancaire'}Overschrijving
                {else}{$method}{/if}
              {elseif $order->id_lang == 3} {* 🇩🇪 German *}
                {if $method == 'Chèque'}Scheck
                {elseif $method == 'Paiement CB'}Kartenzahlung
                {elseif $method == 'Paiement comptant à la livraison (Cash on delivery...)'}Nachnahmezahlung
                {elseif $method == 'Paiement E-CARTEBLEUE'}E-Carte Bleue
                {elseif $method == 'Paiement MASTERCARD'}Mastercard-Zahlung
                {elseif $method == 'Paiement par carte bancaire'}Kreditkartenzahlung
                {elseif $method == 'Paiement VISA'}Visa-Zahlung
                {elseif $method == 'PayPal'}PayPal
                {elseif $method == 'Sogecommerce'}Sogecommerce
                {elseif $method == 'Transfert bancaire'}Banküberweisung
                {else}{$method}{/if}
              {elseif $order->id_lang == 4} {* 🇪🇸 Spanish *}
                {if $method == 'Chèque'}Cheque
                {elseif $method == 'Paiement CB'}Pago con tarjeta
                {elseif $method == 'Paiement comptant à la livraison (Cash on delivery...)'}Pago contra reembolso
                {elseif $method == 'Paiement E-CARTEBLEUE'}E-Carte Bleue
                {elseif $method == 'Paiement MASTERCARD'}Pago con Mastercard
                {elseif $method == 'Paiement par carte bancaire'}Pago con tarjeta bancaria
                {elseif $method == 'Paiement VISA'}Pago con Visa
                {elseif $method == 'PayPal'}PayPal
                {elseif $method == 'Sogecommerce'}Sogecommerce
                {elseif $method == 'Transfert bancaire'}Transferencia bancaria
                {else}{$method}{/if}
              {elseif $order->id_lang == 5} {* 🇮🇹 Italian *}
                {if $method == 'Chèque'}Assegno
                {elseif $method == 'Paiement CB'}Pagamento con carta
                {elseif $method == 'Paiement comptant à la livraison (Cash on delivery...)'}Pagamento alla consegna
                {elseif $method == 'Paiement E-CARTEBLEUE'}E-Carte Bleue
                {elseif $method == 'Paiement MASTERCARD'}Pagamento con Mastercard
                {elseif $method == 'Paiement par carte bancaire'}Pagamento con carta bancaria
                {elseif $method == 'Paiement VISA'}Pagamento con Visa
                {elseif $method == 'PayPal'}PayPal
                {elseif $method == 'Sogecommerce'}Sogecommerce
                {elseif $method == 'Transfert bancaire'}Bonifico bancario
                {else}{$method}{/if}
              {else} {* 🇬🇧 English (default) *}
                {if $method == 'Chèque'}Check
                {elseif $method == 'Paiement CB'}Card payment
                {elseif $method == 'Paiement comptant à la livraison (Cash on delivery...)'}Cash on delivery
                {elseif $method == 'Paiement E-CARTEBLEUE'}E-Carte Bleue
                {elseif $method == 'Paiement MASTERCARD'}Mastercard payment
                {elseif $method == 'Paiement par carte bancaire'}Credit card payment
                {elseif $method == 'Paiement VISA'}Visa payment
                {elseif $method == 'PayPal'}PayPal
                {elseif $method == 'Sogecommerce'}Sogecommerce
                {elseif $method == 'Transfert bancaire'}Bank transfer
                {else}{$method}{/if}
              {/if}
            </td>
            <td class="right small">
              {displayPrice currency=$payment->id_currency price=$payment->amount}
            </td>
          </tr>
        {foreachelse}
          <tr>
            <td colspan="2">
              {if $order->id_lang == 1}
                Pas de paiement
              {elseif $order->id_lang == 2}
                Geen betaling
              {elseif $order->id_lang == 3}
                Keine Zahlung
              {elseif $order->id_lang == 4}
                Sin pago
              {elseif $order->id_lang == 5}
                Nessun pagamento
              {else}
                No payment
              {/if}
            </td>
          </tr>
        {/foreach}
      </table>
    </td>
  </tr>
</table>


