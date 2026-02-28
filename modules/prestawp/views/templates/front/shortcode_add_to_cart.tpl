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

<form method="post" action="{$pswp_cart_link|escape:'html':'UTF-8'}" class="pswp-add-to-cart-form" {if $pswp_align}style="text-align: {$pswp_align|escape:'html':'UTF-8'};"{/if}>
    <input type="hidden" name="token" value="{$pswp_token|escape:'html':'UTF-8'}" class="pswp-token">
    <input type="hidden" name="id_product" value="{$pswp_id_product|escape:'html':'UTF-8'}">
    <input type="hidden" name="add" value="1">
    <input type="hidden" name="qty" value="{$pswp_qty|escape:'html':'UTF-8'}">
    <input type="hidden" name="id_product_attribute" value="{$pswp_id_attribute|escape:'html':'UTF-8'}">
    <button type="submit" name="add_to_cart" class="btn btn-primary pswp-add-to-cart-btn">{$pswp_add_to_cart_text|escape:'html':'UTF-8'}</button>
</form>