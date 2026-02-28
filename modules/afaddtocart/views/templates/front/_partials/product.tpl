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
<script id="product-template" type="x-tmpl-mustache">
{literal}
{{#products}}
    <div class="toasterPanelProduct row pt-1 pb-1" style="border-color: {{borderColor}};">
        <div class="col-3 col-xs-3">
            <div class="product-thumbnail">
                {{{productThumb}}}
            </div>
        </div>
        <div class="col-9 col-xs-9 pl-0 pr-1">
            <a style="color: {{linkColor}}" class="product-name" href="{{url}}" alt="{{name}}" title="{{name}}">
                {{name}}
            </a>
            <span class="product-reference">{{reference}}</span>

            {{#attributes}}
            <span class="product-attribute">{{name}} : {{value}}</span>
            {{/attributes}}

            {{#customizationDetails}}
            {{{customizationDetails}}}
            {{/customizationDetails}}

            <div class="product-actions">
                {{{productActions}}}
            </div>
        </div>
    </div>
    {{/products}}
{/literal}
</script>