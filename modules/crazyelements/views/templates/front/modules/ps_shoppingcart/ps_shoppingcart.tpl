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
    * @author    PrestaShop SA and Contributors <contact@prestashop.com>
    * @copyright Since 2007 PrestaShop SA and Contributors
    * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
    *}

   <div id="_desktop_cart" class="crazy-shopping-cart">
				<div class="blockcart cart-preview crazy-cart-preview {$active_text}" data-refresh-url="{$refres_url}">
					<div class="header">
					{if $prd_count > 0}
						<a rel="nofollow" href="{$cart_page_url}">
					{/if}
						{if $icon_pos == 'before'}
							{if $show_count}
								{$icon_html nofilter}
								<div class="sb-cart-with-count">
									{if $count_style == 'ball_top'}
										<span class="sb-cart-count">
									{/if}
										{$prd_count}
									{if $count_style == 'ball_top'}
										</span>
									{/if}
								</div>
							{/if}
						{/if}
						<span class="cart-text-wrapper">
							{if $cart_text != ''}
								<span class="hidden-sm-down">{$cart_text}</span>
							{/if}

							{if $show_subtotal}
								<span class="cart-products-count">{$formatted_subtotal}</span>
							{/if}

							{if $show_count}
								{if $count_style == 'text'}
									<span class="cart-products-count">{$count_text}</span>
								{/if}
							{/if}
						</span>
						{if $icon_pos == 'after'}
              {$icon_html nofilter}
							<div class="sb-cart-with-count">
								{if $count_style == 'ball_top'}
									<span class="sb-cart-count">
								{/if}
									{$prd_count}
								{if $count_style == 'ball_top'}
									</span>
								{/if}
							</div>
						{/if}
					{if $prd_count > 0}
						</a>
					{/if}		
					</div>
				</div>
			</div>