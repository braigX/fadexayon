{*
* 2007-2021 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innova Deluxe SL
* @copyright 2021 Innova Deluxe SL

* @license   INNOVADELUXE
*}

<div id="submit_idxrcustomproduct_alert" class='alert alert-warning'>
    {l s='You must fill all the customization options before to make the order' mod='idxrcustomproduct'}
</div>
<div id="submit_idxrcustomproduct">
    {*<button class="btn btn-link" type="submit" id="idxrcustomproduct_save">
        <i class="material-icons">save</i> {l s='Ajouter au favoris' mod='idxrcustomproduct'}
    </button>*}
    {if $available_for_order}
    <div class="add-to-cart">
        <div class="product-quantity clearfix">
            <div class="qty idxrcp_qty">
                <div class="input-group bootstrap-touchspin">
                    <span class="input-group-addon bootstrap-touchspin-prefix" style="display: none;"></span>
                    <input min="{$minimal_qty}" type="number" name="qty" id="idxrcustomprouct_quantity_wanted" value="{$minimal_qty}" class="input-group form-control" style="display: block;">
                    <span class="input-group-addon bootstrap-touchspin-postfix" style="display: none;"></span>
                   {* <span class="input-group-btn-vertical">
                        <button class="btn btn-touchspin js-touchspin bootstrap-touchspin-up" type="button">
                            <i class="material-icons touchspin-up"></i>
                        </button>
                        <button class="btn btn-touchspin js-touchspin bootstrap-touchspin-down" type="button">
                            <i class="material-icons touchspin-down"></i>
                        </button>
                    </span>*}
                </div>
            </div>
            {*Add with team wassim novatis*}
              <input type="hidden" name="product_volume" id="product_volume" value="">
              <input type="hidden" name="product_weight" id="product_weight" value="">
              <input type="hidden" name="product_width" id="product_width" value="">
              <input type="hidden" name="product_height" id="product_height" value="">
              <input type="hidden" name="product_surface" id="product_surface" value="">
              <input type="hidden" name="product_depth" id="product_depth" value="">
            {*End*}
        </div>
        <button class="btn btn-success" type="submit" id="idxrcustomproduct_send">
            {l s='Ajouter au panier' mod='idxrcustomproduct'}
        </button>
    </div>
    {/if}
</div>