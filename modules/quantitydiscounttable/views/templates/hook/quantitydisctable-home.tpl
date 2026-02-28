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
    *  @copyright 2020 FMM Modules All right reserved
    *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
    *  @category  FMM Modules
    *  @package   Quantitydiscounttable
  *}
  {if $QDT_SHOW_HOME == 1}
    {if $quantity_discounts}
        <button onclick="qdtFunctionTogggle({$qdt_product_id})" class="btn btn-primary qdt-btn {if $ps_version < '1.7.0.0'} qdt-btn-6 {/if}">{l s='View Discounts' mod='quantitydiscounttable'}{if $ps_version > '1.7.0.0'}<span class="material-icons">
          keyboard_arrow_down
          </span>{/if}</button>
    {/if}
   <div id="qdt-toggle-{$qdt_product_id|escape:'htmlall':'UTF-8'}" style="display:none">  
    <div id="quantity-discount-table-home">
      <section class="product-discounts">
        {if $quantity_discounts}
                {block name='product_discount_table'}
                  <table class="table std {if $QUANTITY_DISCOUNT_TABLE_BORDER_STRIPED == 1}table-striped{/if} {if $QUANTITY_DISCOUNT_TABLE_BORDER == 1}table-bordered{/if}
                    {if $QUANTITY_DISCOUNT_TABLE_BORDER_STRIPED == 0 && $QUANTITY_DISCOUNT_TABLE_BORDER == 0}table-responsive table-product-discounts{/if}">
                    <thead>
                    <tr>
                      <th>{l s='Quantity' mod='quantitydiscounttable'}</th>
                      <th>{if $display_disc_price}{l s='Price' mod='quantitydiscounttable'}{else}{l s='Discount' mod='quantitydiscounttable'}{/if}</th>
                    </tr>
                    </thead>
                    <tbody>
                    {foreach from=$quantity_discounts item='quantity_discount' name='quantity_discounts'}
                      <tr>   
                        <td>{$quantity_discount.from_quantity|escape:'htmlall':'UTF-8'}</td>
                        <td>{if $quantity_discount.reduction_type == 'percentage'}{$quantity_discount.reduction * (100)|escape:'htmlall':'UTF-8'}%{else}{Currency::getDefaultCurrency()->sign|escape:'htmlall':'UTF-8'}{$quantity_discount.reduction|escape:'htmlall':'UTF-8'}{/if} </td>
                      </tr>
                    {/foreach}
                    </tbody>
                  </table>
                {/block}
              {/if}
      </section>
    </div>
   </div>
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