{*
* 2007-2022 ETS-Soft
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
* versions in the future. If you wish to customize PrestaShop for your
* needs please, contact us for extra customization service at an affordable price
*
*  @author ETS-Soft <etssoft.jsc@gmail.com>
*  @copyright  2007-2022 ETS-Soft
*  @license    Valid for 1 website (or project) for each purchase of license
*  International Registered Trademark & Property of ETS-Soft
*}
{if $ets_per_row_desktop}
    {assign var='nbItemsPerLine' value=$ets_per_row_desktop}
	{assign var='nbItemsPerLineTablet' value=$ets_per_row_tablet}
	{assign var='nbItemsPerLineMobile' value=$ets_per_row_mobile}
{else}
    {if $page_name !='index' && $page_name !='product' && $page_name != 'order-confirmation' && $page_name!='orderconfirmation' && $page_name!='cms' && $page_name!='cart'}
    	{assign var='nbItemsPerLine' value=3}
    	{assign var='nbItemsPerLineTablet' value=2}
    	{assign var='nbItemsPerLineMobile' value=3}
    {else}
    	{assign var='nbItemsPerLine' value=4}
    	{assign var='nbItemsPerLineTablet' value=3}
    	{assign var='nbItemsPerLineMobile' value=2}
    {/if}
{/if}
<script type="text/javascript">
    var nbItemsPerLine ={$nbItemsPerLine|intval};
    var nbItemsPerLineTablet ={$nbItemsPerLineTablet|intval};
    var nbItemsPerLineMobile ={$nbItemsPerLineMobile|intval};
</script>
{if $sub_categories && ($products || $id_ets_css_sub_category)}
    <ul class="ets_cs_sub_categories {$page_name|escape:'html':'UTF-8'}{if isset($sort_options) && $sort_options && isset($products) && $products} ets_cs_has_sortby{/if}">
        {foreach from=$sub_categories item='sub_category'}
            <li>
                <a class="ets_crosssel_sub_category{if $id_ets_css_sub_category==$sub_category.id_category} active{/if}" data-id_product="{$id_product_page|intval}" data-page="{$name_page|escape:'html':'UTF-8'}" data-tab="{$tab|escape:'html':'UTF-8'}" data-id_category="{$sub_category.id_category|intval}" href="{$link->getCategoryLink($sub_category.id_category)|escape:'html':'UTF-8'}">{$sub_category.name|escape:'html':'UTF-8'}</a>
            </li>
        {/foreach}
    </ul>
{/if}
{if isset($sort_options) && $sort_options && isset($products) && $products}
    <form class="ets_sortby_form" action="" method="post">
        <label for="ets_crosssell_sort_by_{$name_page|escape:'html':'UTF-8'}_{$tab|escape:'html':'UTF-8'}">{l s='Sort by' mod='ets_crosssell'}</label>
        <select name="ets_crosssell_sort_by" data-id_product="{$id_product_page|intval}" data-page="{$name_page|escape:'html':'UTF-8'}" data-tab="{$tab|escape:'html':'UTF-8'}" id="ets_crosssell_sort_by_{$name_page|escape:'html':'UTF-8'}_{$tab|escape:'html':'UTF-8'}" class="ets_crosssel_sort_by">
            <option value="">--</option>
            {foreach from=$sort_options item='option'}
                <option{if $option.id_option==$sort_by} selected="selected"{/if} value="{$option.id_option|escape:'html':'UTF-8'}">{$option.name|escape:'html':'UTF-8'}</option>
            {/foreach}
        </select>
    </form>
{/if}
{if isset($products) && $products}
    <div class="featured-products product_list">
    	<div id="{$name_page|escape:'html':'UTF-8'}-{$tab|escape:'html':'UTF-8'}" class="{$name_page|escape:'html':'UTF-8'} product_list grid products crosssell_product_list_wrapper{if isset($tab) && $tab} cs-wrapper-{$tab|escape:'html':'UTF-8'}{/if} layout-{$layout_mode|escape:'html':'UTF-8'} ets_mp_desktop_{$nbItemsPerLine|intval} ets_mp_tablet_{$nbItemsPerLineTablet|intval} ets_mp_mobile_{$nbItemsPerLineMobile|intval}">
    	{foreach from=$products item="product"}
              {include file="catalog/_partials/miniatures/product.tpl" product=$product}
        {/foreach}
        </div>
    </div>
{else}
    <div id="{$name_page|escape:'html':'UTF-8'}-{$tab|escape:'html':'UTF-8'}" class="no-product {$name_page|escape:'html':'UTF-8'} crosssell_product_list_wrapper{if isset($tab) && $tab} cs-wrapper-{$tab|escape:'html':'UTF-8'}{/if} layout-{$layout_mode|escape:'html':'UTF-8'} ets_mp_desktop_{$nbItemsPerLine|intval} ets_mp_tablet_{$nbItemsPerLineTablet|intval} ets_mp_mobile_{$nbItemsPerLineMobile|intval}">
        <div class="col-sm-12 col-xs-12"><div class="clearfix"></div><span class="alert alert-warning">{l s='No products available' mod='ets_crosssell'}</span></div>
    </div>
{/if}
