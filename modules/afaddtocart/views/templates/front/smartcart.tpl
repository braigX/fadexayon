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
{include file='module:afaddtocart/views/templates/front/_partials/shipping-rest.tpl'}
{include file='module:afaddtocart/views/templates/front/_partials/product.tpl'}
{include file='module:afaddtocart/views/templates/front/_partials/product-customization.tpl'}
{include file='module:afaddtocart/views/templates/front/_partials/product-actions.tpl'}
{include file='module:afaddtocart/views/templates/front/_partials/subtotal-products.tpl'}
{include file='module:afaddtocart/views/templates/front/_partials/total-products.tpl'}
{include file='module:afaddtocart/views/templates/front/_partials/total-discount.tpl'}
{include file='module:afaddtocart/views/templates/front/_partials/discount-form.tpl'}
{include file='module:afaddtocart/views/templates/front/_partials/cross-selling.tpl'}

{include file='module:afaddtocart/views/templates/front/smartcart-errors.tpl'}
{include file='module:afaddtocart/views/templates/front/smartcart-action.tpl'}
{include file='module:afaddtocart/views/templates/front/smartcart-float-btn.tpl'}
{include file='module:afaddtocart/views/templates/front/smartcart-extra-text.tpl'}

{include file='module:afaddtocart/views/templates/front/addtocart-toolbar.tpl'}
{include file='module:afaddtocart/views/templates/front/gotocart-toolbar.tpl'}


<script id="smartcart-template" type="x-tmpl-mustache">
{literal}
    <div id="toasterPanel">
        <div class="toasterHeader" style="border-color: {{borderColor}};">
            <div class="toasterTitle">
                <p class="h5 products-section-title text-uppercase">{{summaryString}}</p>
                <a href="#" class="btn btn-link btn-sm closeToasterButton" style="color: {{linkColor}};" aria-label="{/literal}{l s='Close'}{literal}">
                    <i class="flaticon flaticon-sm-cross"></i>
                </a>
            </div>
            {{#displayRemainingShippingCost}}
            	{{{shippingCostRest}}}
            {{/displayRemainingShippingCost}}
        </div>

        <div class="toasterBody">
            {{{toasterErrors}}}
            {{{productsHtml}}}
        </div>

        <div class="toasterFooter">
            {{{subtotalProducts}}}
            {{{totalProducts}}}
            {{#displayDiscountForm}}
            	{{{discountForm}}}
            {{/displayDiscountForm}}
            {{{btnCardOrder}}}
            {{#extraText}}
            	{{{extraText}}}
            {{/extraText}}
        </div>
    </div>
{/literal}
</script>
