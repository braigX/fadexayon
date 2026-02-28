{**
* NOTICE OF LICENSE
*
* This file is licenced under the Software License Agreement.
* With the purchase or the installation of the software in your application
* you accept the licence agreement.
*
* @author    Presta.Site
* @copyright 2019 Presta.Site
* @license   LICENSE.txt
*}
<div class="prestawpblock block psv{$psvwd|intval} {if $pswp_block->hook == 'displayLeftColumn' || $pswp_block->hook == 'displayRightColumn'}block-categories{/if}">
	<h2 class="{if $psv == 1.5}title_block{elseif $psv >= 1.7}h1 products-section-title text-uppercase text-center{/if}">{if $pswp_block->title}{$pswp_block->title|escape:'html':'UTF-8'}{else}{l s='Posts' mod='prestawp'}{/if}</h2>
	{strip}
	<div class="pswp_grid posts_container {if $pswp_block->ajax_load}pswp-ajax-load{/if} {if $pswp_block->show_featured_image}posts_container-fi{else}posts_container-text{/if} {if $pswp_block->carousel}pswp-carousel{/if} {if $pswp_block->masonry && $pswp_block->grid_columns > 1 && !$pswp_block->carousel}pswp_masonry{/if} pswp-w{$pswp_block->grid_columns|intval}" data-cols="{$pswp_block->grid_columns|intval}" {if $pswp_block->carousel}data-autoplay="{$pswp_block->carousel_autoplay|intval}" data-dots="{$pswp_block->carousel_dots|intval}" data-arrows="{$pswp_block->carousel_arrows|intval}"{/if} {if $pswp_block->ajax_load}data-type="block"{/if} data-id-block="{$pswp_block->id|intval}">
		{include file=$pswp_block_posts_tpl_file posts=$pswp_block->getPostsFront()}
	</div>
    {/strip}
	<div class="readall-wrp">
		<a {if $pswp_blank}target="_blank"{/if} class="readall" href="{if $pswp_enable_posts_page}{$pswp_posts_page_url|escape:'html':'UTF-8'}{else}{$pswp_wp_path|escape:'html':'UTF-8'}{/if}">{l s='Read All' mod='prestawp'}</a>
	</div>
</div>