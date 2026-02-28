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
<script id="subtotal-products-template" type="x-tmpl-mustache">
{literal}
    {{#hasProducts}}
    <div class="toasterTotal pt-1">
        <div class="priceproductshipping pb-1 pt-1" style="border-color: {{borderColor}};">
            <div class="row">
                <div class="col-6 col-xs-6">{{subtotals.products.label}}</div>
                <div class="col-6 col-xs-6 text-xs-right">{{subtotals.products.value}}</div>
            </div>

            {{#hasDiscount}}
            {{{discountHtml}}}
            {{/hasDiscount}}

            {{#hasShipping}}
                <div class="row">
                    <div class="col-6 col-xs-6">{{subtotals.shipping.label}}</div>
                                    
                    {{#isFreeShipping}}
                    <div class="col-6 col-xs-6 text-xs-right">Mode de livraison à sélectionner</div>
                    {{/isFreeShipping}}

                    {{^isFreeShipping}}
                    <div class="col-6 col-xs-6 text-xs-right">{{subtotals.shipping.value}}</div>
                    {{/isFreeShipping}}
                </div>
            {{/hasShipping}}
        </div>
    </div>
    {{/hasProducts}}
    {{^hasProducts}}
    <div class="toasterTotal"></div>
    {{/hasProducts}}
{/literal}
</script>
