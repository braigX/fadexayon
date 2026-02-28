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
<table class="product" width="100%" cellpadding="4" cellspacing="0">
	<tr>
		<th class="header small left" valign="middle">{l s='If the following conditions are not met, we reserve the right to refuse your package and/or refund:' d='Shop.Pdf' pdf='true'}</th>
	</tr>
	<tr>
		<td class="center small white">
			<ul class="left">
				<li>{l s='Please include this return reference on your return package:' d='Shop.Pdf' pdf='true'} {$order_return->id}</li>
				<li>{l s='All products must be returned in their original package and condition, unused and without damage.' d='Shop.Pdf' pdf='true'}</li>
				<li>{l s='Please print out this document and slip it into your package.' d='Shop.Pdf' pdf='true'}</li>
				<li>{l s='The package should be sent to the following address:' d='Shop.Pdf' pdf='true'}</li>
			</ul>
			<span style="margin-left: 20px;">{$shop_address}</span>
		</td>
	</tr>
</table>
<br/>
{l s='Upon receiving your package, we will notify you by e-mail. We will then begin processing the refund, if applicable. Let us know if you have any questions' d='Shop.Pdf' pdf='true'}
*}





<table class="product" width="100%" cellpadding="4" cellspacing="0">
    <tr>
        <th class="header small left" valign="middle">
            {if $lang == 1}Si les conditions suivantes ne sont pas respectées, nous nous réservons le droit de refuser votre colis et/ou votre remboursement :
            {elseif $lang == 2}Indien de volgende voorwaarden niet worden nageleefd, behouden wij ons het recht voor om uw pakket en/of terugbetaling te weigeren:
            {elseif $lang == 3}Wenn die folgenden Bedingungen nicht erfüllt sind, behalten wir uns das Recht vor, Ihr Paket und/oder Ihre Rückerstattung abzulehnen:
            {elseif $lang == 4}Si no se cumplen las siguientes condiciones, nos reservamos el derecho de rechazar su paquete y/o reembolso:
            {elseif $lang == 5}Se le seguenti condizioni non vengono rispettate, ci riserviamo il diritto di rifiutare il pacco e/o il rimborso:
            {else}If the following conditions are not met, we reserve the right to refuse your package and/or refund:
            {/if}
        </th>
    </tr>
    <tr>
        <td class="center small white">
            <ul class="left">
                <li>
                    {if $lang == 1}Veuillez inclure cette référence de retour sur votre colis : 
                    {elseif $lang == 2}Gelieve dit retourreferentienummer op uw pakket te vermelden: 
                    {elseif $lang == 3}Bitte geben Sie diese Rücksendenummer auf Ihrem Paket an: 
                    {elseif $lang == 4}Por favor incluya esta referencia de devolución en su paquete: 
                    {elseif $lang == 5}Si prega di includere questo riferimento di reso sul pacco: 
                    {else}Please include this return reference on your return package: 
                    {/if} {$order_return->id}
                </li>
                <li>
                    {if $lang == 1}Tous les produits doivent être retournés dans leur emballage et état d'origine, non utilisés et sans dommages.
                    {elseif $lang == 2}Alle producten moeten in hun originele verpakking en staat worden geretourneerd, ongebruikt en zonder schade.
                    {elseif $lang == 3}Alle Produkte müssen in ihrer Originalverpackung und -zustand, unbenutzt und unbeschädigt zurückgesendet werden.
                    {elseif $lang == 4}Todos los productos deben devolverse en su embalaje y estado original, sin usar y sin daños.
                    {elseif $lang == 5}Tutti i prodotti devono essere restituiti nel loro imballaggio e condizioni originali, non utilizzati e senza danni.
                    {else}All products must be returned in their original package and condition, unused and without damage.
                    {/if}
                </li>
                <li>
                    {if $lang == 1}Veuillez imprimer ce document et le glisser dans votre colis.
                    {elseif $lang == 2}Gelieve dit document af te drukken en in uw pakket te stoppen.
                    {elseif $lang == 3}Bitte drucken Sie dieses Dokument aus und legen Sie es in Ihr Paket.
                    {elseif $lang == 4}Por favor imprima este documento y colóquelo en su paquete.
                    {elseif $lang == 5}Si prega di stampare questo documento e inserirlo nel pacco.
                    {else}Please print out this document and slip it into your package.
                    {/if}
                </li>
                <li>
                    {if $lang == 1}Le colis doit être envoyé à l'adresse suivante :
                    {elseif $lang == 2}Het pakket moet naar het volgende adres worden verzonden:
                    {elseif $lang == 3}Das Paket sollte an die folgende Adresse gesendet werden:
                    {elseif $lang == 4}El paquete debe enviarse a la siguiente dirección:
                    {elseif $lang == 5}Il pacco deve essere inviato al seguente indirizzo:
                    {else}The package should be sent to the following address:
                    {/if}
                </li>
            </ul>
            <span style="margin-left: 20px;">{$shop_address}</span>
        </td>
    </tr>
</table>
<br/>
{if $lang == 1}À la réception de votre colis, nous vous informerons par e-mail. Nous commencerons ensuite le traitement du remboursement, le cas échéant. N'hésitez pas à nous contacter si vous avez des questions.
{elseif $lang == 2}Bij ontvangst van uw pakket informeren wij u per e-mail. We zullen vervolgens beginnen met de terugbetaling indien van toepassing. Neem contact met ons op als u vragen heeft.
{elseif $lang == 3}Nach Erhalt Ihres Pakets werden wir Sie per E-Mail benachrichtigen. Wir beginnen dann mit der Bearbeitung der Rückerstattung, falls zutreffend. Kontaktieren Sie uns, wenn Sie Fragen haben.
{elseif $lang == 4}Al recibir su paquete, le notificaremos por correo electrónico. Luego comenzaremos a procesar el reembolso, si corresponde. Contáctenos si tiene alguna pregunta.
{elseif $lang == 5}Al ricevimento del pacco, ti informeremo via e-mail. In seguito inizieremo l'elaborazione del rimborso, se applicabile. Contattaci se hai domande.
{else}Upon receiving your package, we will notify you by e-mail. We will then begin processing the refund, if applicable. Let us know if you have any questions.
{/if}
