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

<ul class="tags_select" {if $social}style="padding-top:10px"{/if}>
	<li>
		<a class="pattern-btn" data-ref="{literal}{{/literal}product_name{literal}}{/literal}" title="{l s='Product name' mod='seoimg'}">
			{l s='Product name' mod='seoimg'}
		</a>
	</li>
	<li>
		<a class="pattern-btn" data-ref="{literal}{{/literal}product_reference{literal}}{/literal}" title="{l s='Product reference' mod='seoimg'}">
			{l s='Product reference' mod='seoimg'}
		</a>
	</li>
	<li>
		<a class="pattern-btn" data-ref="{literal}{{/literal}manufacturer_name{literal}}{/literal}" title="{l s='Product manufacturer' mod='seoimg'}">
			{l s='Product manufacturer' mod='seoimg'}
		</a>
	</li>
	<li>
		<a class="pattern-btn" data-ref="{literal}{{/literal}default_cat_name{literal}}{/literal}" title="{l s='Product category name' mod='seoimg'}">
			{l s='Product category name' mod='seoimg'}
		</a>
	</li>
{*
	<li>
		<a class="pattern-btn" data-ref="{literal}{{/literal}parent_cat_name{literal}}{/literal}" title="{l s='Product parent category name' mod='seoimg'}">
			{l s='Product parent category name' mod='seoimg'}
		</a>
	</li>
*}
	<li>
		<a class="pattern-btn" data-ref="{literal}{{/literal}product_price{literal}}{/literal}" title="{l s='Product retail price with tax' mod='seoimg'}">
			{l s='Product retail price with tax' mod='seoimg'}
		</a>
	</li>
	<li>
		<a class="pattern-btn" data-ref="{literal}{{/literal}product_reduce_price{literal}}{/literal}" title="{l s='Product specific prices with tax' mod='seoimg'}">
			{l s='Product specific prices with tax' mod='seoimg'}
		</a>
	</li>
	<li>
		<a class="pattern-btn" data-ref="{literal}{{/literal}product_price_wt{literal}}{/literal}" title="{l s='Product pre-tax retail price' mod='seoimg'}">
			{l s='Product pre-tax retail price' mod='seoimg'}
		</a>
	</li>
	<li>
		<a class="pattern-btn" data-ref="{literal}{{/literal}product_reduce_price_wt{literal}}{/literal}" title="{l s='Product pre-tax specific prices' mod='seoimg'}">
			{l s='Product pre-tax specific prices' mod='seoimg'}
		</a>
	</li>
	<li>
		<a class="pattern-btn" data-ref="{literal}{{/literal}product_reduction_percent{literal}}{/literal}" title="{l s='Product reduction' mod='seoimg'}">
			{l s='Product reduction' mod='seoimg'}
		</a>
	</li>
</ul>
