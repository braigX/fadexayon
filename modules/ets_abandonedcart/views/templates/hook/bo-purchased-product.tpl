{*
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
*}
{if $wrapper|intval}
<ul class="ets_abancart_product_list ets_abancart_result_productlist{if isset($name) && $name} {$name|escape:'html':'UTF-8'}{/if}">
{/if}{if isset($products) && $products}
    {foreach from=$products item='product'}
		<li class="ets_abancart_product_item" data-id="{$product.id|intval}" ref="#{$name|escape:'html':'UTF-8'}">
			<a href="{$product.link|escape:'quotes':'UTF-8'}">
				<img src="{$product.image|escape:'quotes':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}{if $product.ref}({$product.ref|escape:'html':'UTF-8'}){/if}" width="64"/>{$product.name|escape:'html':'UTF-8'}{if $product.ref}({$product.ref|escape:'html':'UTF-8'}){/if}
			</a>
			<span class="remove_ctm"><i class="icon-trash"></i></span>
		</li>
    {/foreach}
{/if}{if $wrapper|intval}
</ul>
{/if}