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
<div class="prestawpblock margin-top-40 block psv{$psvwd|intval} {if isset($pswp_wrp_class)}{$pswp_wrp_class|escape:'html':'UTF-8'}{/if}">
	{strip}
	<div class="margin-top-20 pswp_grid posts_container {if !empty($pswp_ajax_load)}pswp-ajax-load{/if} {if $show_featured_images}posts_container-fi{else}posts_container-text{/if} {if $pswp_carousel}pswp-carousel{/if} {if $pswp_masonry && $grid_columns > 1 && !$pswp_carousel}pswp_masonry{/if} pswp-cols-{$grid_columns|intval}" data-cols="{$grid_columns|intval}" {if $pswp_carousel}data-autoplay="{$pswp_carousel_autoplay|intval}" data-dots="{$pswp_carousel_dots|intval}" data-arrows="{$pswp_carousel_arrows|intval}"{/if} {if !empty($pswp_block_type)}data-type="{$pswp_block_type|escape:'html':'UTF-8'}"{/if}>
		{include file=$pswp_list_tpl_file}
	</div>
    {/strip}
</div>