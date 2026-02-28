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
 
<table class="product" width="100%" cellpadding="4" cellspacing="0">

  {assign var='widthColProduct' value=$layout.product.width}
  {if !$isTaxEnabled}
    {assign var='widthColProduct' value=$widthColProduct+$layout.tax_code.width}
  {/if}
  <thead>
  <tr>
    <th class="product header small" width="{$layout.reference.width}%">{l s='Référence' d='Shop.Pdf' pdf='true'}</th>
    <th class="product header small" width="{$widthColProduct}%">{l s='Produit' d='Shop.Pdf' pdf='true'}</th>
    {if $isTaxEnabled}
      <th class="product header small" width="{$layout.tax_code.width}%">{l s='Taux de taxe' d='Shop.Pdf' pdf='true'}</th>
    {/if}
    {if isset($layout.before_discount)}
      <th class="product header small" width="{$layout.unit_price_tax_excl.width}%">
        {l s='Base price' d='Shop.Pdf' pdf='true'}{if $isTaxEnabled}<br /> {l s='(Tax excl.)' d='Shop.Pdf' pdf='true'}{/if}
      </th>
    {/if}

    <th class="product header-right small" width="{$layout.unit_price_tax_excl.width}%">
      {l s='Prix unitaire' d='Shop.Pdf' pdf='true'}{if $isTaxEnabled}<br /> {l s='(HT)' d='Shop.Pdf' pdf='true'}{/if}
    </th>
    <th class="product header small" width="{$layout.quantity.width}%">{l s='Qté' d='Shop.Pdf' pdf='true'}</th>
    <th class="product header-right small" width="{$layout.total_tax_excl.width}%">
      {l s='Total' d='Shop.Pdf' pdf='true'}{if $isTaxEnabled}<br /> {l s='(HT)' d='Shop.Pdf' pdf='true'}{/if}
    </th>
  </tr>
  </thead>

  <tbody>

  <!-- PRODUCTS -->
{foreach $order_details as $order_detail}
    {cycle values=["color_line_even", "color_line_odd"] assign=bgcolor_class}
    <tr class="product {$bgcolor_class}">

      <!-- Product details as before -->
      <td class="product center">
        {$order_detail.product_reference}
      </td>
      <td class="product left">
        {if $display_product_images}
          <table width="100%">
            <tr>
              <td width="15%">
                {if isset($order_detail.image) && $order_detail.image->id}
                  {$order_detail.image_tag}
                {/if}
              </td>
              <td width="5%">&nbsp;</td>
              <td width="80%">
                {assign var='clean_product_name' value=$order_detail.product_name|replace:$order_detail.product_id:''}
                {$clean_product_name}
              </td>

            </tr>
          </table>
        {else}
          {assign var='clean_product_name' value=$order_detail.product_name|replace:$order_detail.product_id:''}
          {$clean_product_name}
        {/if}

        <!-- Display custom product notes -->
        {if isset($idxrCustomProductNotes)}
          {foreach from=$idxrCustomProductNotes item=note}
            {if $note.id_cart_product == $order_detail.product_id}
              <br />
              {foreach $note.notes_a as $note_row}
                {$note_row|escape:'htmlall':'UTF-8'|htmlspecialchars_decode}<br />
              {/foreach}
            {/if}
          {/foreach}
        {/if}

      </td>

      <!-- Display tax label and other product details -->
      {if $isTaxEnabled}
        <td class="product center">
          {$order_detail.order_detail_tax_label}
        </td>
      {/if}

      <td class="product right">
        {displayPrice currency=$order->id_currency price=$order_detail.unit_price_tax_excl_including_ecotax}
      </td>
      <td class="product center">
        {$order_detail.product_quantity}
      </td>
      <td class="product right">
        {displayPrice currency=$order->id_currency price=$order_detail.total_price_tax_excl_including_ecotax}
      </td>
    </tr>

    <!-- Add a new row for the preview image -->
    {foreach from=$idxrCustomProductNotes item=note}
        {if $note.id_cart_product == $order_detail.product_id && $note.preview_img}
            <!-- Create a new row for the preview image, no top border -->
            <tr class="product {$bgcolor_class}" border="0">
                <!-- Collapse the row into one <td> spanning across multiple columns -->
                <td colspan="6" class="product" align="center" border="0">
                    <!-- Render the full <img> element directly, ensuring no border -->
                    {$note.preview_img|escape:'htmlall':'UTF-8'|htmlspecialchars_decode}
                </td>
            </tr>
        {/if}
    {/foreach}
{/foreach}
  <!-- END PRODUCTS -->

  <!-- CART RULES -->

  {assign var="shipping_discount_tax_incl" value="0"}
  {foreach from=$cart_rules item=cart_rule name="cart_rules_loop"}
    {if $smarty.foreach.cart_rules_loop.first}
      <tr class="discount">
        <th class="header" colspan="{$layout._colCount}">
          {l s='Discounts' d='Shop.Pdf' pdf='true'}
        </th>
      </tr>
    {/if}
    <tr class="discount">
      <td class="white right" colspan="{$layout._colCount - 1}">
        {$cart_rule.name}
      </td>
      <td class="right white">
        - {displayPrice currency=$order->id_currency price=$cart_rule.value_tax_excl}
      </td>
    </tr>
  {/foreach}

  </tbody>
</table>
{if isset($order_detail.first_order_message) && $order_detail.first_order_message}
<table id="order_msg" class="product" width="100%">
  <tr>
    <td class="center grey bold note" width="20%">{l s='Message client' pdf='true'}</td>
    <td class="left white" width="1%"></td>
    <td class="left white note-msg" width="79%"><span>{$order_detail.first_order_message}</span></td>
  </tr>
</table>
{/if}
