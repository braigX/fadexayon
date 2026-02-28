{**
* 2007-2020 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innova Deluxe SL
* @copyright 2020 Innova Deluxe SL
* @license   INNOVADELUXE
*}

<div id="priceblock" class="row">
    <div class="col-lg-6">
        <h2 class="topblock-price"><span id="js_topblock_total_price_until">{l s='Until: ' mod='idxrcustomproduct'}{$product_price}</span></h2>  
    </div>
     <div class="col-lg-6 text-center">
        <button type="button" id="idxrcustomproduct_topblock_send" class="btn btn-lg btn-topblock disabled"><img src="{$module_dir}/views/img/white-cart.png" width="20"/> {l s='Add to cart' mod='idxrcustomproduct'}</button>
        <p id="idxrcustomproduct_topblock_send_message"><i class="icon icon-info-circle fa fa-info-circle"></i> {l s='Please select options' mod='idxrcustomproduct'}</p>
     </div>
</div>