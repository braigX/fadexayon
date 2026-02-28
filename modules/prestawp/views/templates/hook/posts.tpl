{**
* NOTICE OF LICENSE
*
* This file is licenced under the Software License Agreement.
* With the purchase or the installation of the software in your application
* you accept the licence agreement.
*
* @author    Presta.Site
* @copyright 2017 Presta.Site
* @license   LICENSE.txt
*}
<div class="prestawpblock block psv{$psvwd|intval} {if isset($pswp_wrp_class)}{$pswp_wrp_class|escape:'html':'UTF-8'}{/if}">
	{if isset($pswp_title) && $pswp_title}
		<h3 class="pswp-custom-title">{$pswp_title|escape:'html':'UTF-8'}</h3>
	{elseif !$pswp_hide_title}
		<h2 class="{if $psv == 1.5}title_block{elseif $psv >= 1.7}h1 products-section-title text-uppercase text-center{/if}">{l s='Posts' mod='prestawp'}</h2>
	{/if}
	{strip}
	<div class="pswp_grid posts_container {if !empty($pswp_ajax_load)}pswp-ajax-load{/if} {if $show_featured_images}posts_container-fi{else}posts_container-text{/if} {if $pswp_carousel}pswp-carousel{/if} {if $pswp_masonry && $grid_columns > 1 && !$pswp_carousel}pswp_masonry{/if} pswp-cols-{$grid_columns|intval}" data-cols="{$grid_columns|intval}" {if $pswp_carousel}data-autoplay="{$pswp_carousel_autoplay|intval}" data-dots="{$pswp_carousel_dots|intval}" data-arrows="{$pswp_carousel_arrows|intval}"{/if} {if !empty($pswp_block_type)}data-type="{$pswp_block_type|escape:'html':'UTF-8'}"{/if}>
		{include file=$pswp_list_tpl_file}
	</div>
    {/strip}
	{if !$pswp_hide_readall}
	<div class="readall-wrp">
		<a {if $pswp_blank}target="_blank"{/if} class="readall" href="{if $pswp_enable_posts_page}{$pswp_posts_page_url|escape:'html':'UTF-8'}{else}{$wp_path|escape:'html':'UTF-8'}{/if}">{l s='Read All' mod='prestawp'}</a>
	</div>
	{/if}
</div>