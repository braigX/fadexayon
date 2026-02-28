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
<script id="product-customization-template" type="x-tmpl-mustache">
{literal}
    {{#customizations}}
    <dl class="customizations">
        {{#fields}}
        <dt>{{label}}</dt>
        {{#isText}}
        <dd>{{text}}</dd>
        {{/isText}}
        {{#isImage}}
        <dd><img src="{{image.small.url}}" alt="{{label}}"/></dd>
        {{/isImage}}
        {{/fields}}
    </dl>
    {{/customizations}}
{/literal}
</script>