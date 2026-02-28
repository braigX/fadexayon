{*
 * Quantitydiscounttable
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 *  @author    FME Modules
 *  @copyright 2023 FMM Modules All right reserved
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  @category  FMM Modules
 *  @package   Quantitydiscounttable
*}

{if $QDT_SHOW_PD == 1} 
{if (isset($quantity_discounts) && count($quantity_discounts) > 0)}
<div id="quantity-disount-table">
    <section class="page-product-box">
        <h2 class="page-product-heading">{l s='Volume discounts' mod='quantitydiscounttable'}</h2>
        <div id="quantityDiscount">
            <table class="table std {if $QUANTITY_DISCOUNT_TABLE_BORDER_STRIPED == 1}table-striped{/if} {if $QUANTITY_DISCOUNT_TABLE_BORDER == 1}table-bordered{/if}
                {if $QUANTITY_DISCOUNT_TABLE_BORDER_STRIPED == 0 && $QUANTITY_DISCOUNT_TABLE_BORDER == 0} table-product-discounts {/if}">
                <thead>
                    <tr>
                        <th>{l s='Quantity' mod='quantitydiscounttable'}</th>
                        <th>{if $display_discount_price}{l s='Price' mod='quantitydiscounttable'}{else}{l s='Discount' mod='quantitydiscounttable'}{/if}</th>
                        <th>{l s='You Save' mod='quantitydiscounttable' mod='quantitydiscounttable'}</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$quantity_discounts item='quantity_discount' name='quantity_discounts'}
                    {if $quantity_discount.price >= 0 || $quantity_discount.reduction_type == 'amount'}
                    {$realDiscountPrice=$quantity_discount.base_price|floatval-$quantity_discount.real_value|floatval}
                    {else}
                    {$realDiscountPrice=$quantity_discount.base_price|floatval*(1 -
                    $quantity_discount.reduction)|floatval}
                    {/if}
                    <tr class="quantityDiscount_{$quantity_discount.id_product_attribute|escape:'htmlall':'UTF-8'}"
                        data-real-discount-value="{convertPrice price = $realDiscountPrice}"
                        data-discount-type="{$quantity_discount.reduction_type|escape:'htmlall':'UTF-8'}"
                        data-discount="{$quantity_discount.real_value|floatval}"
                        data-discount-quantity="{$quantity_discount.quantity|intval}">
                        <td>
                            {$quantity_discount.quantity|intval}
                        </td>
                        <td>
                            {if $quantity_discount.price >= 0 || $quantity_discount.reduction_type == 'amount'}
                                {if $display_discount_price}
                                    {if $quantity_discount.reduction_tax == 0 && !$quantity_discount.price}
                                        {convertPrice price = $productPriceWithoutReduction|floatval-($productPriceWithoutReduction*$quantity_discount.reduction_with_tax)|floatval}
                                    {else}
                                        {convertPrice price=$productPriceWithoutReduction|floatval-$quantity_discount.real_value|floatval}
                                    {/if}
                                {else}
                                    {convertPrice price=$quantity_discount.real_value|floatval}
                                {/if}
                            {else}
                                {if $display_discount_price}
                                    {if $quantity_discount.reduction_tax == 0}
                                        {convertPrice price = $productPriceWithoutReduction|floatval-($productPriceWithoutReduction*$quantity_discount.reduction_with_tax)|floatval}
                                    {else}
                                        {convertPrice price = $productPriceWithoutReduction|floatval-($productPriceWithoutReduction*$quantity_discount.reduction)|floatval}
                                    {/if}
                                {else}
                                    {$quantity_discount.real_value|floatval}%
                                {/if}
                            {/if}
                        </td>
                        <td>
                            <span>{l s='Up to' mod='quantitydiscounttable'}</span>
                            {if $quantity_discount.price >= 0 || $quantity_discount.reduction_type == 'amount'}
                                {$discountPrice=$productPriceWithoutReduction|floatval-$quantity_discount.real_value|floatval}
                            {else}
                                {$discountPrice=$productPriceWithoutReduction|floatval-($productPriceWithoutReduction*$quantity_discount.reduction)|floatval}
                            {/if}
                            {$discountPrice=$discountPrice * $quantity_discount.quantity}
                            {$qtyProductPrice=$productPriceWithoutReduction|floatval * $quantity_discount.quantity}
                            {convertPrice price=$qtyProductPrice - $discountPrice}
                        </td>
                    </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>
    </section>
</div>
{/if}
{/if}

<style>
table{
  text-align: {$QUANTITY_DISCOUNT_TABLE_TEXT_ALIGN|escape:'htmlall':'UTF-8'};
}
.table-bordered{
  border: {$QUANTITY_DISCOUNT_TABLE_BORDER_SIZE|escape:'htmlall':'UTF-8'}px solid {$QUANTITY_DISCOUNT_TABLE_BORDER_COLOR|escape:'htmlall':'UTF-8'} !important;
}
thead{
  font-size: {$QUANTITY_DISCOUNT_TABLE_HEADER_FONT|escape:'htmlall':'UTF-8'}px;
  color: {$QUANTITY_DISCOUNT_TABLE_HEADER_COLOR|escape:'htmlall':'UTF-8'};
  background-color: {$QUANTITY_DISCOUNT_TABLE_BG_COLOR|escape:'htmlall':'UTF-8'};
  text-align: {$QUANTITY_DISCOUNT_TABLE_TEXT_ALIGN|escape:'htmlall':'UTF-8'};
}
tbody{
  font-size: {$QUANTITY_DISCOUNT_TABLE_TEXT_FONT|escape:'htmlall':'UTF-8'}px;
  background-color: {$QUANTITY_DISCOUNT_TABLE_BODY_COLOR|escape:'htmlall':'UTF-8'};
  color: {$QUANTITY_DISCOUNT_TABLE_TEXT_COLOR|escape:'htmlall':'UTF-8'};
}

</style>