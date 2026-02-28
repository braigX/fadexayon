{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 *}

 {if $banner|count > 0}
	<div id="ps_topbanner_wrapper">
	
		{if $banner['with_mobile_text'] == 1}
			<div class="ps_topbanner_mobile">
				{* HTML in variable is expected here *}
				{$banner['mobile_text'] nofilter}
			</div>
		{/if}
		<div class="ps_topbanner_desktop">
			{* HTML in variable is expected here *}
			{$banner['text'] nofilter}
		</div>
	
		{if $banner['timer'] == 1}
			{$banner['timer_left_text']|escape:'htmlall':'UTF-8'}
			{include file="./timer.tpl"}
			{$banner['timer_right_text']|escape:'htmlall':'UTF-8'}
		{/if}
	
		{if $banner['cta'] == 1}
			<a class="ps_topbanner_cta" href="{$banner['cta_link']}" target="_blank">{$banner['cta_text']|escape:'htmlall':'UTF-8'}</a>
		{/if}
	
	</div>
	{/if}
	<style>
		.header-nav {
			height: {$banner['height']|escape:'htmlall':'UTF-8'}px;
		}
		
		header .banner {
			background-color: {$banner['background']|escape:'htmlall':'UTF-8'};
		}
	
		#ps_topbanner_wrapper {
			width: 100%;
			left: 0;
			z-index: 999;
			top: 0;
			height: {$banner['height']|escape:'htmlall':'UTF-8'}px;
			padding: .5em;
			background-color: {$banner['background']|escape:'htmlall':'UTF-8'};
			font-size: {$banner['text_size']|escape:'htmlall':'UTF-8'}px;
			text-align: center;
		}
	
		.ps_topbanner_desktop p {
			font-size: inherit;
			height: {$banner['height']|escape:'htmlall':'UTF-8'}px;
		}
	
		{if $banner['with_mobile_text'] == 1}
			
		.ps_topbanner_mobile p {
			font-size: inherit;
		}
	
		@media (min-width: 992px) {
			.ps_topbanner_mobile {
				display: none;
			}
		}
	
		@media (max-width: 991px) {
			.ps_topbanner_desktop {
				display: none;
			}
	
			.ps_topbanner_mobile, .ps_topbanner_mobile ~ .ps_topbanner_cta {
				font-size: {$banner['mobile_text_size']|escape:'htmlall':'UTF-8'}px;
				width: 100%;
				overflow: hidden;
				overflow-wrap: break-word;
			}
	
		}
		{/if}
	
		{if $banner['cta'] == 1}
			#ps_topbanner_wrapper {
				position: relative;
			}
			
			.ps_topbanner_cta {
				color: {$banner['cta_text_color']|escape:'htmlall':'UTF-8'}!important;
				padding: 7px 10px;
				background-color: {$banner['cta_background']|escape:'htmlall':'UTF-8'};
				border-radius: 4px;
				margin-top: .5em;
				display: inline-table;
			}
	
			.ps_topbanner_cta:after {
				content: '';
				position: absolute;
				top: 0;
				right: 0;
				bottom: 0;
				left: 0;
			}
		{/if}
	</style>