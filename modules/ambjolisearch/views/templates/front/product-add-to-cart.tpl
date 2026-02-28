{*
*   @author    Ambris Informatique
*   @copyright Copyright (c) 2013-2023 Ambris Informatique SARL
*   @license   Licensed under the EUPL-1.2-or-later
*  International Registered Trademark & Property of PrestaShop SA
*}

{if isset($product.amb_add_to_cart_url)}
<div class="product-list-add-to-cart">
    <form action="{if $product.amb_add_to_cart_url}{$product.amb_add_to_cart_url}{else}{$product.url}{/if}">
        <div class="product-quantity">
        {if $product.amb_add_to_cart_url}
            <input type="hidden" name="qty" value="1">
            <div class="add">
              <button
                class="add-to-cart btn btn-primary"
                data-button-action="add-to-cart"
                type="submit"
              >{strip}
              {if ($settings.add_to_cart_button_style & 1) != 0}<i class="material-icons shopping-cart {if $settings.add_to_cart_button_style == 1} icon-only{/if}">shopping_cart</i>{/if}
              {if ($settings.add_to_cart_button_style & 2) != 0}{l s='Add to cart' d='Shop.Theme.Actions'}{/if}
              {/strip}</button>
            </div>
        {/if}
        </div>
    </form>
</div>
{/if}