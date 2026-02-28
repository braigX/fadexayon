{*
*  @author    Amazzing <mail@mirindevo.com>
*  @copyright Amazzing
*  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}

{extends file='catalog/listing/product-list.tpl'}

{block name='product_list_header'}
	<h1 class="page-header">{$seo_data.header}</h1>
	<div class="page-description">{$seo_data.description nofilter}</div>
{/block}

{block name='product_list' append}
	<div class="page-description-lower">{$seo_data.description_lower nofilter}</div>
{/block}

{* since 0.1.2 *}
