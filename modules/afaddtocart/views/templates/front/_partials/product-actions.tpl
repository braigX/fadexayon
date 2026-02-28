{**
 *  2024 ALGO-FACTORY.COM
 *
 *  NOTICE OF LICENSE
 *
 * @author        Algo Factory <contact@algo-factory.com>
 * @copyright     Copyright (c) 2024 Algo Factory
 * @license   You are just allowed to modify this copy for your own use. You must not redistribute it. License
 *               is permitted for one Prestashop instance only but you can install it on your test instances.
 *
 * @version       1.0.0
 * @website       www.algo-factory.com
 *
 *  You can not resell or redistribute this software.
 *}
<script id="product-actions-template" type="x-tmpl-mustache">
{literal}
    <div class="input-group input-group-quantity">
        <a class="btn btn-primary input-quantity-btn input-quantity-minus js-hover {{classDisabled}}"
           data-quantity-down-url="{{down_quantity_url}}"
           style="background-color: {{btnBackgroundColor}}; color: {{btnTextColor}};">
            <i class="flaticon flaticon-sm-minus-sign" aria-hidden="true"></i>
        </a>
        <input class="form-control js-cart-line-product-quantity input-quantity" type="text" min="1"
               value="{{quantity}}"
               data-product-qty="{{quantity}}"
               data-id-product="{{id_product}}"
               data-id-product-attribute="{{id_product_attribute}}"
               data-quantity-update-url="{{update_quantity_url}}">
        <a class="btn btn-primary input-quantity-btn input-quantity-plus js-hover"
           data-quantity-up-url="{{up_quantity_url}}"
           style="background-color: {{btnBackgroundColor}}; color: {{btnTextColor}};">
            <i class="flaticon flaticon-sm-plus" aria-hidden="true"></i>
        </a>
    </div>
    <div class="product-quantity">
        <div class="col-12 col-md-12">
            {{#priceWithoutReduc}}
            <span class="whitout-reduc">{{priceWithoutReduc}}</span>
            {{/priceWithoutReduc}}
            <span>x {{price}}</span>
        </div>
    </div>
    <div class="product-delete">
        <a class="js-cart-line-product-quantity btnDeleteProduct"
           href="{{remove_from_cart_url}}"
           style="color: {{linkColor}};">
            <i class="falticon flaticon-sm-garbage" aria-hidden="true"></i>
        </a>
    </div>
{/literal}
</script>