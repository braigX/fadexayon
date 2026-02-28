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
<script id="shipping-rest-template" type="x-tmpl-mustache">
{literal}
    {{#showRemainingShipping}}
    <span class="remainingShipping" style="border-color: {{borderColor}};">
        {{shippingRemaining}} {{shippingRemainingText}}
    </span>
    {{/showRemainingShipping}}
{/literal}
</script>
