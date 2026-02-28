{**
* Copyright since 2007 PrestaShop SA and Contributors
* PrestaShop is an International Registered Trademark & Property of PrestaShop SA
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License 3.0 (AFL-3.0)
* that is bundled with this package in the file LICENSE.md.
* It is also available through the world-wide-web at this URL:
* https://opensource.org/licenses/AFL-3.0
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to https://devdocs.prestashop.com/ for more information.
*
* @author PrestaShop SA and Contributors <contact@prestashop.com>
* @copyright Since 2007 PrestaShop SA and Contributors
* @license https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
*}
    {block name='cart_detailed_totals'}
    <div class="cart-detailed-totals js-cart-detailed-totals">
{* Modified with Team Wassim Novatis.*}
        <div class="card-block">
            {foreach from=$cart.subtotals item="subtotal"}
            {if $subtotal && $subtotal.value|count_characters > 0 && $subtotal.type !== 'tax'}
            <div class="cart-summary-line" id="cart-subtotal-{$subtotal.type}">
                <span class="label{if 'products' === $subtotal.type} js-subtotal{/if}">
                    {if 'products' == $subtotal.type}
                    {$cart.summary_string}
                    {else}
                    {$subtotal.label}
                    {/if}
                </span>
                <span class="value">
                    {if 'discount' == $subtotal.type}-&nbsp;{/if}
                      {if $subtotal.type === 'shipping'}
                       {if $subtotal.value == 0} Mode de livraison<br> à sélectionner
                       {else}
                       {$subtotal.value}
                     {/if}
                     {else}
                     {$subtotal.value}
                    {/if}
                </span>
                {if $subtotal.type === 'shipping'}
                <div><small class="value">{hook h='displayCheckoutSubtotalDetails' subtotal=$subtotal}</small></div>
				{*Add with team wassim novatis*}
                {*<div><small class="value"> <a href="/content/5-paiement" target="_blank">(sous conditions)</a></small></div>*}
				{*End*}
                {/if}
            </div>
            {/if}
            {/foreach}
        </div>
{* End*}
        {block name='cart_voucher'}
        {include file='checkout/_partials/cart-voucher.tpl'}
        {/block}

        <hr class="separator">

        {block name='cart_summary_totals'}
            {include file='checkout/_partials/cart-summary-totals.tpl' cart=$cart}
        {/block}
    </div>
    {/block}