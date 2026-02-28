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
<script id="cross-selling-template" type="x-tmpl-mustache">
{literal}
    <div id="toasterCrossSellingPanel" style="border-color: {{borderColor}}; background-color: {{backgroundColor}}; color: {{color}};">
        <div class="toasterHeader" style="border-color: {{headerBorderColor}};">
            <p class="h5 products-section-title text-uppercase">{{crossSellingTitle}}</p>
        </div>
        <div class="toasterBody">
            {{#products}}
            {{#addToCartUrl}}
            <div class="toasterPanelProduct row pt-1 pb-1" style="border-color: {{productBorderColor}};">
                <div class="col-3 col-xs-3">
                    <div class="product-thumbnail">
                        {{{productThumb}}}
                    </div>
                </div>
                <div class="col-9 col-xs-9 pl-0">
                    <a style="color: {{linkColor}}" class="product-name" href="{{url}}" alt="{{name}}" title="{{name}}">
                        {{name}}
                    </a>
                    <div class="product-price">
                        {{#hasRegularPrice}}
                        <span class="whitout-reduc">{{regularPrice}}</span>
                        {{/hasRegularPrice}}
                        <span>{{price}}</span>
                    </div>
                    <a style="color: {{linkColor}}" class="add-to-cart addProductLink" href="{{addToCartUrl}}" alt="{{name}}" title="{{name}}"
                        data-button-action="add-to-cart" data-id-product="{{id}}" data-id-product-attribute="{{idProductAttribute}}">
                        {{addToCartText}}
                        <i class="flaticon flaticon-sm-right-arrow-1"></i>
                    </a>
                </div>
            </div>
            {{/addToCartUrl}}
            {{/products}}
        </div>
    </div>
{/literal}
</script>
