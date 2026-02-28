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
<script id="total-products-template" type="x-tmpl-mustache">
{literal}
    {{#hasProducts}}
    <div class="toasterTotal finalTotal pt-1">
        <div class="row">
            <div class="col-6 col-xs-6">{{totals.total_including_tax.label}}</div>
            <div class="col-6 col-xs-6 text-right text-xs-right">{{totals.total_including_tax.value}}</div>
        </div>
    </div>
    {{/hasProducts}}
    {{^hasProducts}}
    <div class="toasterTotal"></div>
    {{/hasProducts}}
{/literal}
</script>
