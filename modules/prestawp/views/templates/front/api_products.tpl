{**
* NOTICE OF LICENSE
*
* This file is licenced under the Software License Agreement.
* With the purchase or the installation of the software in your application
* you accept the licence agreement.
*
* @author    Presta.Site
* @copyright 2018 Presta.Site
* @license   LICENSE.txt
*}

{if isset($pswp_products) && is_array($pswp_products) && count($pswp_products)}
    <div id="pswp-products">
        <section class="product-miniatures">
            <div class="products product_list row grid ">
                {foreach from=$pswp_products item='product' name="pswp_products"}<div class="product_list_item {if $pswp_limit_mobile && $smarty.foreach.pswp_products.iteration > $pswp_limit_mobile}pswp-mobile-hidden{/if}">
                    <article class="product-miniature js-product-miniature">
                        <div class="thumbnail-container">
                            <a href="{$product.link|escape:'quotes':'UTF-8'}" class="thumbnail product-thumbnail">
                                <img src="{$product.img_url|escape:'quotes':'UTF-8'}" alt="{$product.name|escape:'quotes':'UTF-8'}">
                            </a>
                            {if $product.price_without_reduction > 0 && isset($product.specific_prices) && $product.specific_prices && isset($product.specific_prices.reduction) && $product.specific_prices.reduction > 0}
                                {if $product.specific_prices.reduction_type == 'percentage'}
                                    <span class="discount-product discount-percentage">-{($product.specific_prices.reduction * 100)|escape:'html':'UTF-8'}%</span>
                                {else}
                                    <span class="discount-product discount-amount">-{Tools::displayPrice($product.specific_prices.reduction)|escape:'html':'UTF-8'}</span>
                                {/if}
                            {/if}
                        </div>
                        <div class="info-container">
                            <div class="product-description">
                                <div class="product-name-wrp">
                                    <h2 class="h3 product-title">
                                        <a href="{$product.link|escape:'quotes':'UTF-8'}">{$product.name|escape:'html':'UTF-8':truncate:60}</a>
                                    </h2>
                                </div>
                                <div class="product-price-and-shipping">
                                    {if $product.price_without_reduction > 0 && isset($product.specific_prices) && $product.specific_prices && isset($product.specific_prices.reduction) && $product.specific_prices.reduction > 0}
                                        <span class="regular-price">{Tools::displayPrice($product.price_without_reduction)|escape:'html':'UTF-8'}</span>
                                    {/if}
                                    <span>{Tools::displayPrice($product.price)|escape:'html':'UTF-8'}</span>
                                </div>
                            </div>
                            <div class="highlighted-informations">
                                <div class="pswp-add-to-cart-wrp">
                                    <form method="post" action="{$pswp_cart_link|escape:'quotes':'UTF-8'}">
                                        <input type="hidden" name="token" value="" class="pswp-token">
                                        <input type="hidden" name="id_product" value="{$product.id_product|intval}">
                                        {if !empty($product.product_attribute_minimal_quantity) && $product.product_attribute_minimal_quantity > 1}
                                            <div class="pswp-min-qty-info">{l s='The minimum purchase quantity is %s' mod='prestawp' sprintf=[$product.product_attribute_minimal_quantity]}</div>
                                            <input type="hidden" name="add" value="{$product.product_attribute_minimal_quantity|intval}">
                                            <input type="hidden" name="qty" value="{$product.product_attribute_minimal_quantity|intval}">
                                        {elseif !empty($product.minimal_quantity) && $product.minimal_quantity > 1}
                                            <input type="hidden" name="add" value="{$product.minimal_quantity|intval}">
                                            <input type="hidden" name="qty" value="{$product.minimal_quantity|intval}">
                                        {else}
                                            <input type="hidden" name="add" value="1">
                                        {/if}
                                        <input type="hidden" name="id_product_attribute" value="{$product.id_product_attribute|intval}">
                                        <button name="add_to_cart" class="pswp-add-to-cart-btn pswp-font-icon">{l s='Add to cart' mod='prestawp'}</button>
                                    </form>
                                </div>
                                <div class="pswp-add-to-cart-wrp pswp-btn-info-wrp">
                                    <a href="{$product.link|escape:'quotes':'UTF-8'}" class="pswp-view pswp-font-icon">{l s='More info' mod='prestawp'}</a>
                                </div>
                                {*<hr>*}
                            </div>
                        </div>
                    </article>
                </div>{/foreach}
            </div>
        </section>
    </div>
{/if}