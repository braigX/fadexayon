{*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License version 3.0
* that is bundled with this package in the file LICENSE.txt
* It is also available through the world-wide-web at this URL:
* https://opensource.org/licenses/AFL-3.0
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your needs
* please refer to CustomizationPolicy.txt file inside our module for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
*}

<div class="wk-sample-block" style="padding-top: 25px;clear: both;">
    <div class="alert alert-danger" id="wk_sp_standard_product_error" {if !isset($standardAdded) || !$standardAdded} style="display:none"{/if}>
        <i class="material-icons product-last-items" style="color:#ff9a52;">&#xE002;</i>
        {l s='You have added this standard product in cart. Please proceed or delete that cart then you can buy the sample product.' mod='wksampleproduct'}
    </div>
    <div class="alert alert-danger" id="wk_sp_ajax_error_wrap" style="display:none">
        <i class="material-icons product-last-items" style="color:#ff9a52;">&#xE002;</i>
        <spam id="wk_sp_ajax_error">
        </spam>
    </div>
    <span class="control-label">
        {if isset($samplePrice) && isset($sampleOrgPrice)}
            {l s='Sample price' mod='wksampleproduct'} : <span class="product-price">{if $sampleOrgPrice == 0}{l s='Free' mod='wksampleproduct'}{else}{$samplePrice|escape:'htmlall':'UTF-8'} {if (($sample.price_type == 4) && ($sample.price_tax == 0)) || ($isTaxExclDisplay|escape:'htmlall':'UTF-8')}({l s='Tax excluded' mod='wksampleproduct'}){else}({l s='Tax included' mod='wksampleproduct'}){/if}{/if}
            </span>
        {/if}
    </span>
    <p>{$sample.description nofilter}</p>
    {* {if $wkShowQtySpin}<span class="control-label">{l s='Sample quantity' mod='wksampleproduct'}</span>{/if} *}
    <div class="product-quantity clearfix" {if isset($standardAdded) && $standardAdded} style="display:none"{/if} style="display:block">
        {if $wkShowQtySpin}
        <div class="qty">
            <div class="wktouchspin input-group bootstrap-touchspin">
                <input type="text" name="wkqty" id="wkquantity_wanted" min="1" value="1" class="input-group form-control" style="display: block;">
                <span class="input-group-btn-vertical">
                    <button class="btn btn-touchspin wkjs-touchspin wkbootstrap-touchspin-up" type="button">
                        <i class="material-icons touchspin-up"></i>
                    </button>
                    <button class="btn btn-touchspin wkjs-touchspin wkbootstrap-touchspin-down" type="button">
                        <i class="material-icons touchspin-down"></i>
                    </button>
                </span>
            </div>
        </div>
        {else}
            <input type="hidden" name="wkqty" id="wkquantity_wanted" value="1">
        {/if}
        <button class="btn btn-primary add-to-cart"
            id="wksamplebuybtn"
            data-id-product="{$wkIdProduct|escape:'htmlall':'UTF-8'}"
            data-id-customer="{$wkIdCustomer|escape:'htmlall':'UTF-8'}"
            data-id-product-attr="{$wkIdProductAttr|escape:'htmlall':'UTF-8'}"
            data-cart-url="{$cartPageURL|escape:'htmlall':'UTF-8'}"
            {if $sampleFullInCart}disabled{/if}
            style="background: {$wkSampleBg|escape:'htmlall':'UTF-8'}!important;color: {$wkSampleColor|escape:'htmlall':'UTF-8'}!important;"
        >
            <i class="material-icons shopping-cart"></i>
            {if empty($sample.button_label)}
                {l s='Buy sampless' mod='wksampleproduct'}
            {else}{$sample.button_label}{/if}
        </button>
    </div>
    <span id="wksampleproductqty_spinerror" class="wksampleproduct-lineerror w-100" {if !$sampleQtyWarning}style="display:none;"{/if}>
        <i class="material-icons wkproduct-unavailable"></i>
        {l s='You can buy maximum ' mod='wksampleproduct'}{$sample.max_cart_qty|escape:'htmlall':'UTF-8'} {l s='samples.' mod='wksampleproduct'}
    </span>
    {if !$addToCartEnabled || $sampleFullInCart}
        <span  id="wksampleproductqty_stockerror" class="wksampleproduct-lineerror">
            <i class="material-icons wkproduct-unavailable"></i>
            {l s='Out-of-stock' mod='wksampleproduct'}
        </span>
    {/if}
</div>
