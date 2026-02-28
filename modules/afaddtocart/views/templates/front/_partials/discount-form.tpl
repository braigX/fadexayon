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
<script id="discount-form-template" type="x-tmpl-mustache">
{literal}
    {{#showForm}}
    <div class="discounts-form-list">
    <form id="discountForm" method="post" class="text-md-center" style="border-color: {{borderColor}};">
        <div class="input-group" style="display: flex">
            <input class="form-control promo-input" type="text" name="discount_name" placeholder="{{inputPlaceholder}}">
            <button class="btn btn-primary js-hover btnPromo" type="submit" style="background-color: {{btnBackgroundColor}}; color: {{btnTextColor}};">
                {{submitText}}
            </button>
        </div>
    </form>
    {{/showForm}}

    {{#highlightDiscounts}}
    <dl class="vouchers">
        <dt>{{name}}</dt>
        <dd>
            <a href="#" class="add-voucher" style="color: {{linkColor}};">{{code}}</a>
        </dd>
    </dl>
    {{/highlightDiscounts}}

    {{#addedVouchers}}
    <dl class="vouchers">
        <dt>{{name}}</dt>
        <dd>
            {{reductionFormatted}}
            <a href="{{deleteUrl}}" class="delete-discount" style="color: {{linkColor}};">
                <i class="falticon flaticon-sm-garbage" aria-hidden="true"></i>
            </a>
        </dd>
    </dl>
    {{/addedVouchers}}
    </div>
{/literal}
</script>
