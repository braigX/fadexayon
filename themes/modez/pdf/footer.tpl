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
<table style="width: 100%;">
	<tr>
		<td style="text-align: center; font-size: 6pt; color: #444;  width:100%;">
			{*{if $available_in_your_account}
				{l s='Une version électronique de cette facture est disponible dans votre compte. Pour y accéder, connectez-vous à notre site web en utilisant votre adresse e-mail et votre mot de passe (que vous avez créé lors de votre première commande).' d='Shop.Pdf' pdf='true'}
				<br />
			{/if}*}
			{*{$shop_address|escape:'html':'UTF-8'}*}<br />

			{*{if !empty($shop_phone) OR !empty($shop_fax)}
				{l s='Pour obtenir une assistance supplémentaire, veuillez contacter le support :' d='Shop.Pdf' pdf='true'}<br />
				{if !empty($shop_phone)}
					{l s='Tel: %s' sprintf=[$shop_phone|escape:'html':'UTF-8'] d='Shop.Pdf' pdf='true'}
				{/if}

				{if !empty($shop_fax)}
					{l s='Fax: %s' sprintf=[$shop_fax|escape:'html':'UTF-8'] d='Shop.Pdf' pdf='true'}
				{/if}
				<br />
			{/if}*}

			{*{if isset($shop_details)}
				{$shop_details|escape:'html':'UTF-8'}<br />
			{/if}*}





			{*
			{if isset($free_text)}
				{$free_text|escape:'html':'UTF-8'}<br />
			{/if}
			*}




			
			{if isset($free_text)}
			{if isset($order) && $order->id_lang == 1}
				Pénalité de retard au taux annuel de : 9% - Pas d'escompte en cas de paiement anticipé
			{elseif isset($order) && $order->id_lang == 2}
				Rente over de achterstallige betaling aan jaarlijkse rente van: 9% - Geen korting bij vroegtijdige betaling
			{elseif isset($order) && $order->id_lang == 3}
				Verzugszins zum jährlichen Satz von: 9% - Kein Skonto bei frühzeitiger Zahlung
			{elseif isset($order) && $order->id_lang == 4}
				Penalización por demora a una tasa anual de: 9% - Sin descuento por pago anticipado
			{elseif isset($order) && $order->id_lang == 5}
				Penale di mora al tasso annuo del: 9% - Nessuno sconto per pagamento anticipato
			{else}
				Pénalité de retard au taux annuel de : 9% - Pas d'escompte en cas de paiement anticipé
			{/if}
			<br />
			{/if}

		</td>
	</tr>
</table>

