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
    <div class="cart_widget">
        <div class="blockcart cart-preview {if $cart.products_count > 0}active{else}inactive{/if}" data-refresh-url="{$refresh_url}">
            <div class="cart_header tip_inside-">
                <a rel="nofollow" href="{$cart_url}" class="cart_nogo">
                    <i class="shopping-cart"></i>
                    {*<span class="text hidden-sm-down">{l s='Cart' d='Shop.Theme.Checkout'}</span>*}
			    {* Modified with Team Wassim Novatis.*}
                   {* <span class="cart-products-count{if $cart.products_count < 1} hidden{/if}">{$cart.products_count}</span>*}
					 <span class="cart-products-count">{$cart.products_count}</span>
				{* End of modification *}
                    {*{if $cart.products_count > 0}
                    <span class="tip">{l s='Open Shopping cart' d='Shop.Theme.Checkout'}</span>
                    {else}
                    <span class="tip">{l s='Cart is empty' d='Shop.Theme.Checkout'}</span>
                    {/if}*}
                </a>
            </div>

            <div class="cart_inside">
                <div class="side_title h4 ">{l s='Shopping Cart' d='Shop.Theme.Actions'}</div>
                {if $cart.products_count > 0}
                <ul class="cart-prods">
                    <div class="loader">
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                    </div>
                    {foreach from=$cart.products item=product}
                    <li>{include 'module:ps_shoppingcart/ps_shoppingcart-product-line.tpl' product=$product}</li>
                    {/foreach}
                </ul>
                <div class="cart-prices">
                {foreach from=$cart.subtotals item="subtotal"}
    {if $subtotal && $subtotal.value|count_characters > 0 && $subtotal.type !== 'tax'}
      <div class="cart-summary-line cart-summary-subtotals" id="cart-subtotal-{$subtotal.type}">

        <span class="label">
            {$subtotal.label}
        </span>

        <span class="value">
          {if 'discount' == $subtotal.type}-&nbsp;{/if}{$subtotal.value}
        </span>
      </div>
    {/if}
  {/foreach}
                </div>
                <div class="cart-checkout">
                    <a href="{$cart_url}" class="btn btn-primary btn-high">{l s='Checkout' d='Shop.Theme.Actions'}</a>
                </div>
                <!-- New Cart widget totals -->
                <div class="cart-total">
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
                {if $subtotal.value == 0}
                Mode de livraison<br> à sélectionner
                 {else}
                {$subtotal.value}
                {/if}
               {else}
               {$subtotal.value}
               {/if}
              </span>
                {if $subtotal.type === 'shipping'}
                    <div>
                        <small class="value">
                            {hook h='displayCheckoutSubtotalDetails' subtotal=$subtotal}
                        </small>
                    </div>
                {/if}
            </div>
        {/if}
    {/foreach}
</div>
{* End*}
                    {if $cart.vouchers.added}
                        {block name='cart_voucher_list'}
                            <ul class="promo-name card-block">
                            {foreach from=$cart.vouchers.added item=voucher}
                                <li class="cart-summary-line">
                                <span class="label">{$voucher.name}</span>
                                {if isset($voucher.code) && $voucher.code !== ''}
                                <a href="{$voucher.delete_url}" class="remove-voucher" data-link-action="remove-voucher"><i class="material-icons">&#xE872;</i></a>
                                {/if}
                                <div class="float-xs-right">
                                    {$voucher.reduction_formatted}
                                </div>
                                </li>
                            {/foreach}
                            </ul>
                        {/block}
                    {/if}

                    <div class="cart-summary-line side-total">
                        <span class="label">{$cart.totals.total.label}&nbsp;{if $configuration.taxes_enabled}{$cart.labels.tax_short}{/if}</span>
                        <span class="value value-total">{$cart.totals.total.value}</span>
                    </div>

                    
                </div>                
                <!-- //New Cart widget totals -->

                {else}
                <p class="text-center">{l s='Cart is empty' d='Shop.Theme.Checkout'}</p>
                <i class="shopping-cart empty"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <g data-name="Layer 4">
                            <rect x="3.5" y="2.5" width="17" height="19" rx="5" ry="5" style="fill:none;stroke:#000;stroke-miterlimit:10;stroke-width:2.5px" />
                            <path d="M8.5,6v.9c0,2.35,1.57,4.27,3.5,4.27s3.5-1.92,3.5-4.27V6" style="fill:none;stroke:#000;stroke-miterlimit:10;stroke-width:2.299999952316284px" />
                        </g>
                    </svg></i>
                <div class="cart-checkout">
                    <button class="btn btn-primary btn-high return">{l s='Continue shopping' d='Shop.Theme.Actions'}</button>
                </div>
                {/if}
            </div>
        </div>
    </div>