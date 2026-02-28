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
{* {if $QDT_SHOW_PD == 1}  *}
  {* {if (isset($quantity_discounts) && count($quantity_discounts) > 0)} *}
<div id="quantity-discount-table">
  <section class="product-discounts">
    {if $product.quantity_discounts}
      <br>
            <h3 class="h3 qdt_cus_heading">{l s='Volume discounts' mod='quantitydiscounttable'}</h3>
            {block name='product_discount_table'}
              <table class="table {if $QUANTITY_DISCOUNT_TABLE_BORDER_STRIPED == 1}table-striped{/if} {if $QUANTITY_DISCOUNT_TABLE_BORDER == 1}table-bordered{/if}
                 {if $QUANTITY_DISCOUNT_TABLE_BORDER_STRIPED == 0 && $QUANTITY_DISCOUNT_TABLE_BORDER == 0}table-responsive table-product-discounts{/if}">
                <thead style="">
                <tr>
                  <th>{l s='Quantity' mod='quantitydiscounttable'}</th>
                  <th>{$configuration.quantity_discount.label|escape:'htmlall':'UTF-8'}</th>
                  <th>{l s='You Save' mod='quantitydiscounttable'}</th>
                </tr>
                </thead>
                <tbody>
                {foreach from=$product.quantity_discounts item='quantity_discount' name='quantity_discounts'}
                  <tr data-discount-type="{$quantity_discount.reduction_type|escape:'htmlall':'UTF-8'}" data-discount="{$quantity_discount.real_value|escape:'htmlall':'UTF-8'}" data-discount-quantity="{$quantity_discount.quantity|escape:'htmlall':'UTF-8'}">
                    <td>{$quantity_discount.quantity|escape:'htmlall':'UTF-8'}</td>
                    <td>{$quantity_discount.discount|escape:'htmlall':'UTF-8'}</td>
                    <td>{l s='Up to %discount%' mod='quantitydiscounttable' sprintf=['%discount%' => $quantity_discount.save]}</td>
                  </tr>
                {/foreach}
                </tbody>
              </table>
            {/block}
          {/if}
  </section>
</div>
{* {/if} *}
{* {/if} *}
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