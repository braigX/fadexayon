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
<div class="block {$blog_config.YBC_BLOG_RTL_CLASS|escape:'html':'UTF-8'} ybc_block_gallery {if isset($blog_page) && $blog_page}page_{$blog_page|escape:'html':'UTF-8'}_gallery{else}page_blog_gallery{/if} {if isset($blog_config.YBC_BLOG_GALLERY_BLOCK_HOME_SLIDER_ENABLED) && $blog_config.YBC_BLOG_GALLERY_BLOCK_HOME_SLIDER_ENABLED && $blog_page=='home' || isset($blog_config.YBC_BLOG_GALLERY_BLOCK_SIDEBAR_SLIDER_ENABLED) && $blog_config.YBC_BLOG_GALLERY_BLOCK_SIDEBAR_SLIDER_ENABLED && $blog_page!='home'}ybc_block_slider{else}ybc_block_default{/if}">
    <h4 class="title_blog title_block">
        <a href="{$gallery_link|escape:'html':'UTF-8'}">
            {l s='Photo gallery' mod='ybc_blog'}
        </a>
    </h4> 
    {if $galleries}
        <div class="block_content">
            {assign var='product_row' value= $blog_config.YBC_BLOG_HOME_PER_ROW|intval}
            <ul class="row {if isset($blog_config.YBC_BLOG_GALLERY_BLOCK_HOME_SLIDER_ENABLED) && $blog_config.YBC_BLOG_GALLERY_BLOCK_HOME_SLIDER_ENABLED && $blog_page=='home' || isset($blog_config.YBC_BLOG_GALLERY_BLOCK_SIDEBAR_SLIDER_ENABLED) && $blog_config.YBC_BLOG_GALLERY_BLOCK_SIDEBAR_SLIDER_ENABLED && $blog_page!='home'}blog_type_slider{/if}">
                {foreach from=$galleries item='gallery'}  
                    <li {if $blog_page=='home'}class="col-xs-6 col-sm-4 col-lg-2"{/if}>
                        {if isset($blog_config.YBC_BLOG_GALLERY_SLIDESHOW_ENABLED) && $blog_config.YBC_BLOG_GALLERY_SLIDESHOW_ENABLED}
                        <a {if $gallery.description}title="{strip_tags($gallery.description)|escape:'html':'UTF-8'}"{/if}  rel="prettyPhotoBlock[galleryblock]" class="gallery_block_slider gallery_item" href="{$gallery.image|escape:'html':'UTF-8'}">
                            <img width="{Configuration::get('YBC_BLOG_IMAGE_GALLERY_THUHMB_WIDTH'|escape:'html':'UTF-8')}" height="{Configuration::get('YBC_BLOG_IMAGE_GALLERY_THUHMB_HEIGHT'|escape:'html':'UTF-8')}" src="{$gallery.thumb|escape:'html':'UTF-8'}" title="{$gallery.title|escape:'html':'UTF-8'}"  alt="{$gallery.title|escape:'html':'UTF-8'}"  />
                        </a>
                        {else}
                            <img width="{Configuration::get('YBC_BLOG_IMAGE_GALLERY_THUHMB_WIDTH'|escape:'html':'UTF-8')}" height="{Configuration::get('YBC_BLOG_IMAGE_GALLERY_THUHMB_HEIGHT'|escape:'html':'UTF-8')}" src="{$gallery.thumb|escape:'html':'UTF-8'}" title="{$gallery.title|escape:'html':'UTF-8'}"  alt="{$gallery.title|escape:'html':'UTF-8'}"  />
                        {/if}   
                        <h3 class="ybc_title_block">{if strlen($gallery.title) > 50}{substr($gallery.title,0,49)|escape:'html':'UTF-8'}...{else}{$gallery.title|escape:'html':'UTF-8'}{/if}</h3>                                           
                    </li>
                {/foreach}            
            </ul>  
            {if $blog_config.YBC_BLOG_DISPLAY_BUTTON_ALL_HOMEPAGE || $blog_page!='home'}
                <div class="blog_view_all_button">
                    <a class="view_all_link" href="{$gallery_link|escape:'html':'UTF-8'}">{l s='View all Photos' mod='ybc_blog'}</a>
                </div>
            {/if}
        </div>      
    {else}
        <div class="block_content">
            <p>{l s='No featured images' mod='ybc_blog'}</p>
            <div class="cleafix"></div>
        </div>
    {/if}
     <div class="clear"></div>
</div>