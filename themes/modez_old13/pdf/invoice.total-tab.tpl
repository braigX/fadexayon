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
<table id="total-tab" width="100%">
  <tr>
    <td class="grey" width="50%">
      {l s='Total produit HT' d='Shop.Pdf' pdf='true'}
    </td>
    <td class="white" width="50%">
      {displayPrice currency=$order->id_currency price=$footer.products_before_discounts_tax_excl}
    </td>
  </tr>

  {if $footer.product_discounts_tax_excl > 0}

    <tr>
      <td class="grey" width="50%">
        {l s='Remises totales' d='Shop.Pdf' pdf='true'}
      </td>
      <td class="white" width="50%">
        - {displayPrice currency=$order->id_currency price=$footer.product_discounts_tax_excl}
      </td>
    </tr>
  {/if}

  {if !$order->isVirtual()}
   {if $footer.shipping_tax_excl > 0}
  <tr>
    <td class="grey" width="50%">
      {l s='Frais de livraison HT' d='Shop.Pdf' pdf='true'}
    </td>
    <td class="white" width="50%">
      {if ($carrier->name == 'Livraison geodis sans rendez-vous' || $carrier->name == 'Livraison dpd sur lieu de travail' || $carrier->name == 'Livraison dpd à domicile Predict sur créneau horaire')}
        {if $order->date_add <= '2024-12-18 09:42:37'}
          {math equation="x * 0.8" x=$footer.shipping_tax_excl assign="reduced_price"}
          {displayPrice currency=$order->id_currency price=$reduced_price}
        {else}
          {displayPrice currency=$order->id_currency price=$footer.shipping_tax_excl}
        {/if}
      {else}
        {displayPrice currency=$order->id_currency price=$footer.shipping_tax_excl}
      {/if}
    </td>
  </tr>
  {else}
    {* {l s='Livraison gratuite' d='Shop.Pdf' pdf='true'}*}
  {/if}
 {/if}

  {if $footer.wrapping_tax_excl > 0}
    <tr>
      <td class="grey">
        {l s='Wrapping Costs' d='Shop.Pdf' pdf='true'}
      </td>
      <td class="white">{displayPrice currency=$order->id_currency price=$footer.wrapping_tax_excl}</td>
    </tr>
  {/if}

 {*<tr class="bold">
    <td class="grey">
      {if $isTaxEnabled}
        {l s='Total HT' d='Shop.Pdf' pdf='true'}
      {else}
        {l s='Total u' d='Shop.Pdf' pdf='true'}
      {/if}
    </td>
    <td class="white">
      {displayPrice currency=$order->id_currency price=$footer.total_paid_tax_excl}
    </td>
  </tr>*}
  

      {* INVOKE MINIMUM CART HOOK HOOK RIGHT HERE *}
        {hook h='displayMinimumCartFee' object=$order_invoice} 
		
		
  {if $isTaxEnabled}
    {if $footer.total_taxes > 0}
      <tr class="bold">
        <td class="grey">
          {l s='Total TVA' d='Shop.Pdf' pdf='true'}
        </td>
        <td class="white">

      {if $footer.shipping_tax_excl > 0 && ($carrier->name == 'Livraison geodis sans rendez-vous' || $carrier->name == 'Livraison dpd sur lieu de travail'  ||  $carrier->name == 'Livraison dpd à domicile Predict sur créneau horaire')}
       {if $order->date_add <= '2024-12-18 09:42:37'}
        {math equation="x * 0.2" x=$footer.shipping_tax_excl assign="reduction"}
        {math equation="x + y" x=$footer.total_taxes y=$reduction assign="final_price"}
        {displayPrice currency=$order->id_currency price=$final_price}
       {else}
        {displayPrice currency=$order->id_currency price=$footer.total_taxes}
       {/if}
      {else}
        {displayPrice currency=$order->id_currency price=$footer.total_taxes}
      {/if} 
        </td>
      </tr>
    {/if}
    <tr class="bold big">
      <td class="grey">
        {l s='Total TTC' d='Shop.Pdf' pdf='true'}
      </td>
      <td class="white">
        {displayPrice currency=$order->id_currency price=$footer.total_paid_tax_incl}
      </td>
    </tr>
  {/if}
</table>
