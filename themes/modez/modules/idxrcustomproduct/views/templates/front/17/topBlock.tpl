{*
* 2007-2020 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innova Deluxe SL
* @copyright 2020 Innova Deluxe SL

* @license   INNOVADELUXE
*}

{*<div id="priceblock" class="row">
    <div class="col-lg-6">
        <h2 class="topblock-price"><span id="js_topblock_total_price_until">{l s='Until: ' mod='idxrcustomproduct'}{$product_price}</span></h2>  
    </div>
     <div class="col-lg-6 text-center">
        <button type="button" id="idxrcustomproduct_topblock_send" class="btn btn-lg btn-topblock disabled"><img src="{$module_dir}/views/img/white-cart.png" width="20"/> {l s='Add to cart' mod='idxrcustomproduct'}</button>
        <p id="idxrcustomproduct_topblock_send_message"><i class="material-icons">info_outline</i> {l s='Please select options' mod='idxrcustomproduct'}</p>
     </div>
</div>*}
{* Add with team wassim novatis *}
<div class="price-information topblock">
   {block name='product_price'}
      <div
        class="product-price h3 {if $product.has_discount}has-discount{/if}"   >
        <div class="current-price">
          <span  content="{$product.price_amount}">{$product.price}</span> / m² <span>TTC</span>
        </div>
      </div>
    {/block}
   {block name='product_without_taxes'}
      {if $priceDisplay == 0}
        <div class="product-without-tax">{l s='%price% / m² HT' d='Shop.Theme.Catalog' sprintf=['%price%' => $product.price_tax_exc|number_format:2]}</div>
      {/if}
    {/block}
</div>
{* End *}