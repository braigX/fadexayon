{*
*  @author    Amazzing <mail@mirindevo.com>
*  @copyright Amazzing
*  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}

<h1 class="page-header">{$seo_data.header|escape:'html':'UTF-8'}</h1>
<div class="page-description">{$seo_data.description}{* can not be escaped *}</div>
{if $products}
	<div class="content_sortPagiBar clearfix">
		<div class="sortPagiBar clearfix">
			{include file="{$tpl_dir}product-sort.tpl"}
			{include file="{$tpl_dir}nbr-product-page.tpl"}
		</div>
		<div class="top-pagination-content clearfix">
			{include file="{$tpl_dir}product-compare.tpl"}
			{include file="{$tpl_dir}pagination.tpl"}
		</div>
	</div>
	<div class="af_pl_wrapper clearfix">
		{include file="{$tpl_dir}product-list.tpl" products=$products}
	</div>
	<div class="content_sortPagiBar">
		<div class="bottom-pagination-content clearfix">
			{include file="{$tpl_dir}product-compare.tpl" paginationId='bottom'}
			{include file="{$tpl_dir}pagination.tpl" paginationId='bottom'}
		</div>
	</div>
{else}
	<div class="alert alert-warning">{l s='No products' mod='af_seopages'}</div>
{/if}
<div class="page-description-lower">{$seo_data.description_lower}{* can not be escaped *}</div>
{* since 1.0.0 *}
