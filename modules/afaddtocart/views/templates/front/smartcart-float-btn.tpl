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
<script id="smartcart-float-btn-template" type="x-tmpl-mustache">
{literal}
    <a href="#" class="btn btn-primary btnOpenCloseToaster js-hover" style="background-color: {{btnBackgroundColor}}; color: {{btnTextColor}};" aria-label="{/literal}{l s='Open Cart'}{literal}">
        {{#displayProductsCount}}
        <span id="quantityProductBtn" style="background-color: {{btnBackgroundColor}}; color: {{btnTextColor}};">
            {{productsCount}}
        </span>
        {{/displayProductsCount}}
        <i class="flaticon flaticon-sm-shopping-cart-empty-side-view"></i>
    </a>
{/literal}
</script>


