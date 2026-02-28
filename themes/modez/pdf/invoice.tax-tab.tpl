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
<!--  TAX DETAILS -->
{if $isTaxEnabled}
  {if $tax_exempt}

    {l s='Exempt of VAT according to section 259B of the General Tax Code.' d='Shop.Pdf' pdf='true'}

  {elseif (isset($tax_breakdowns) && $tax_breakdowns)}
    <table id="tax-tab" width="100%">
      <thead>
        <tr>
          <th class="header small">{l s='Detail de la taxe' d='Shop.Pdf' pdf='true'}</th>
          <th class="header small">{l s='Taux de taxe' d='Shop.Pdf' pdf='true'}</th>
          {if $display_tax_bases_in_breakdowns}
            <th class="header small">{l s='Base price' d='Shop.Pdf' pdf='true'}</th>
          {/if}
          <th class="header-right small">{l s='Taxe totale' d='Shop.Pdf' pdf='true'}</th>
        </tr>
      </thead>
      <tbody>
      {assign var=has_line value=false}
*}
      {* INVOKE MINIMUM CART HOOK HOOK RIGHT HERE 
    {hook h='displayMinimumCartFee' object=$order_invoice} *}
		 {*   
      {foreach $tax_breakdowns as $label => $bd}
        {assign var=label_printed value=false}

			  {foreach $bd as $line}*}
          {* We force the display of the ecotax even if the ecotax rate is equals to 0 *}
        {*  {if $line.rate == 0 and $label != 'ecotax_tax'}
            {continue}
          {/if}

          {assign var=has_line value=true}

          <tr>
            <td class="white">
              {if !$label_printed}
                {if $label == 'product_tax'}
                  {l s='Produits' d='Shop.Pdf' pdf='true'}
                {elseif $label == 'shipping_tax'}
                  {l s='Livraison' d='Shop.Pdf' pdf='true'}
                {elseif $label == 'ecotax_tax'}
                  {l s='Ecotax' d='Shop.Pdf' pdf='true'}
                {elseif $label == 'wrapping_tax'}
                  {l s='Wrapping' d='Shop.Pdf' pdf='true'}
                {/if}
                {assign var=label_printed value=true}
              {/if}
            </td>

            <td class="center white">
              {$line.rate} %
            </td>

            {if $display_tax_bases_in_breakdowns}
              <td class="right white">
                {if isset($is_order_slip) && $is_order_slip}- {/if}
                {displayPrice currency=$order->id_currency price=$line.total_tax_excl}
              </td>
            {/if}

            <td class="right white">
              {if isset($is_order_slip) && $is_order_slip}- {/if}
              {displayPrice currency=$order->id_currency price=$line.total_amount}
            </td>
          </tr>
        {/foreach}
      {/foreach}

      {if !$has_line}
        <tr>
          <td class="white center" colspan="{if $display_tax_bases_in_breakdowns}4{else}3{/if}">
            {l s='No taxes' d='Shop.Pdf' pdf='true'}
          </td>
        </tr>
      {/if}

      </tbody>
    </table>
  {/if}
{/if}
<!--  / TAX DETAILS -->*}


<!-- TAX DETAILS -->
{if $isTaxEnabled}
  {if $tax_exempt}

    {if $order->id_lang == 1}Exempt of VAT according to section 259B of the General Tax Code.
    {elseif $order->id_lang == 2}Vrijgesteld van btw volgens sectie 259B van de Algemene Belastingwet.
    {elseif $order->id_lang == 3}Von der Mehrwertsteuer gemäß § 259B des Allgemeinen Steuergesetzes befreit.
    {elseif $order->id_lang == 4}Exento de IVA según la sección 259B del Código Tributario General.
    {elseif $order->id_lang == 5}Esente da IVA ai sensi della sezione 259B del Codice Fiscale Generale.
    {else}Exempt of VAT according to section 259B of the General Tax Code.
    {/if}

  {elseif (isset($tax_breakdowns) && $tax_breakdowns)}
    <table id="tax-tab" width="100%">
      <thead>
        <tr>
          <th class="header small">
            {if $order->id_lang == 1}Détail de la taxe
            {elseif $order->id_lang == 2}Belastingdetails
            {elseif $order->id_lang == 3}Steuerdetails
            {elseif $order->id_lang == 4}Detalle del impuesto
            {elseif $order->id_lang == 5}Dettaglio tasse
            {else}Tax details
            {/if}
          </th>
          <th class="header small">
            {if $order->id_lang == 1}Taux de taxe
            {elseif $order->id_lang == 2}Belastingtarief
            {elseif $order->id_lang == 3}Steuersatz
            {elseif $order->id_lang == 4}Tasa de impuesto
            {elseif $order->id_lang == 5}Aliquota fiscale
            {else}Tax rate
            {/if}
          </th>
          {if $display_tax_bases_in_breakdowns}
            <th class="header small">
              {if $order->id_lang == 1}Base price
              {elseif $order->id_lang == 2}Basiskosten
              {elseif $order->id_lang == 3}Basispreis
              {elseif $order->id_lang == 4}Precio base
              {elseif $order->id_lang == 5}Prezzo base
              {else}Base price
              {/if}
            </th>
          {/if}
          <th class="header-right small">
            {if $order->id_lang == 1}Taxe totale
            {elseif $order->id_lang == 2}Totale belasting
            {elseif $order->id_lang == 3}Gesamtsteuer
            {elseif $order->id_lang == 4}Impuesto total
            {elseif $order->id_lang == 5}Tasse totali
            {else}Total tax
            {/if}
          </th>
        </tr>
      </thead>
      <tbody>
      {assign var=has_line value=false}

      {foreach $tax_breakdowns as $label => $bd}
        {assign var=label_printed value=false}

        {foreach $bd as $line}
          {if $line.rate == 0 and $label != 'ecotax_tax'}
            {continue}
          {/if}

          {assign var=has_line value=true}

          <tr>
            <td class="white">
              {if !$label_printed}
                {if $label == 'product_tax'}
                  {if $order->id_lang == 1}Produits
                  {elseif $order->id_lang == 2}Producten
                  {elseif $order->id_lang == 3}Produkte
                  {elseif $order->id_lang == 4}Productos
                  {elseif $order->id_lang == 5}Prodotti
                  {else}Products{/if}
                {elseif $label == 'shipping_tax'}
                  {if $order->id_lang == 1}Livraison
                  {elseif $order->id_lang == 2}Verzending
                  {elseif $order->id_lang == 3}Versand
                  {elseif $order->id_lang == 4}Envío
                  {elseif $order->id_lang == 5}Spedizione
                  {else}Shipping{/if}
                {elseif $label == 'ecotax_tax'}
                  {if $order->id_lang == 1}Ecotax
                  {elseif $order->id_lang == 2}Ecotax
                  {elseif $order->id_lang == 3}Ökosteuer
                  {elseif $order->id_lang == 4}Ecotasa
                  {elseif $order->id_lang == 5}Ecotassa
                  {else}Ecotax{/if}
                {elseif $label == 'wrapping_tax'}
                  {if $order->id_lang == 1}Wrapping
                  {elseif $order->id_lang == 2}Inpakken
                  {elseif $order->id_lang == 3}Verpackung
                  {elseif $order->id_lang == 4}Embalaje
                  {elseif $order->id_lang == 5}Confezionamento
                  {else}Wrapping{/if}
                {/if}
                {assign var=label_printed value=true}
              {/if}
            </td>

            <td class="center white">
              {$line.rate} %
            </td>

            {if $display_tax_bases_in_breakdowns}
              <td class="right white">
                {if isset($is_order_slip) && $is_order_slip}- {/if}
                {displayPrice currency=$order->id_currency price=$line.total_tax_excl}
              </td>
            {/if}

            <td class="right white">
              {if isset($is_order_slip) && $is_order_slip}- {/if}
              {displayPrice currency=$order->id_currency price=$line.total_amount}
            </td>
          </tr>
        {/foreach}
      {/foreach}

      {if !$has_line}
        <tr>
          <td class="white center" colspan="{if $display_tax_bases_in_breakdowns}4{else}3{/if}">
            {if $order->id_lang == 1}No taxes
            {elseif $order->id_lang == 2}Geen belastingen
            {elseif $order->id_lang == 3}Keine Steuern
            {elseif $order->id_lang == 4}Sin impuestos
            {elseif $order->id_lang == 5}Nessuna tassa
            {else}No taxes
            {/if}
          </td>
        </tr>
      {/if}

      </tbody>
    </table>
  {/if}
{/if}
<!-- /TAX DETAILS -->
