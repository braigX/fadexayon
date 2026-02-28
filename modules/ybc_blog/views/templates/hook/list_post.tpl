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
<div class="ybc_blog_layout_{$blog_layout|escape:'html':'UTF-8'} ybc-blog-wrapper ybc-blog-wrapper-blog-list{if isset($blog_config.YBC_BLOG_AUTO_LOAD) &&$blog_config.YBC_BLOG_AUTO_LOAD} loadmore{/if} {if $blog_latest}ybc-page-latest{elseif $blog_category}ybc-page-category{elseif $blog_tag}ybc-page-tag{elseif $blog_search}ybc-page-search{elseif $author}ybc-page-author{else}ybc-page-home{/if}">
    {if $is_main_page}
        {$html_slide_block nofilter}
    {/if}
    {if $blog_category}
        {if isset($blog_category.enabled) && $blog_category.enabled}
            <div class="blog-category {if $blog_category.image}has-blog-image{/if}">
                {if $blog_category.image}
                    <div class="ybc_item_img{if isset($blog_config.YBC_BLOG_LAZY_LOAD)&& $blog_config.YBC_BLOG_LAZY_LOAD} ybc_item_img_ladyload{/if}">
                        {if $blog_config.YBC_BLOG_CATEGORY_ENABLE_POST_SLIDESHOW}
                        <a href="{$link->getMediaLink("`$smarty.const._PS_YBC_BLOG_IMG_`category/`$blog_category.image|escape:'htmlall':'UTF-8'`")}" class="prettyPhoto">
                            {/if}
                            <img src="{$link->getMediaLink("`$smarty.const._PS_YBC_BLOG_IMG_`category/`$blog_category.image|escape:'htmlall':'UTF-8'`")}" alt="{$blog_category.title|escape:'html':'UTF-8'}" title="{$blog_category.title|escape:'html':'UTF-8'}" {if isset($blog_config.YBC_BLOG_LAZY_LOAD)&& $blog_config.YBC_BLOG_LAZY_LOAD} data-original="{$link->getMediaLink("`$smarty.const._PS_YBC_BLOG_IMG_`category/`$blog_category.image|escape:'htmlall':'UTF-8'`")}" class="lazyload"{/if} />
                            {if isset($blog_config.YBC_BLOG_LAZY_LOAD)&& $blog_config.YBC_BLOG_LAZY_LOAD}
                                <svg width="{Configuration::get('YBC_BLOG_IMAGE_CATEGORY_WIDTH'|escape:'html':'UTF-8')}" height="{Configuration::get('YBC_BLOG_IMAGE_CATEGORY_HEIGHT'|escape:'html':'UTF-8')}" style="width: 100%;height: auto">
                                </svg>
                            {/if}
                            {if $blog_config.YBC_BLOG_CATEGORY_ENABLE_POST_SLIDESHOW}
                        </a>
                        {/if}
                        {if isset($blog_config.YBC_BLOG_LAZY_LOAD)&& $blog_config.YBC_BLOG_LAZY_LOAD}
                            <div class="loader_lady_custom"></div>
                        {/if}
                    </div>
                {/if}
                <h1 class="page-heading product-listing">{$blog_category.title|escape:'html':'UTF-8'}</h1>
                {if $blog_category.description}
                    <div class="blog-category-desc">
                        {$blog_category.description|escape nofilter}
                    </div>
                {/if}
            </div>
        {else}
            <p class="alert alert-warning">{l s='This category is not available' mod='ybc_blog'}</p>
        {/if}
    {elseif $blog_latest}
        <h1 class="page-heading product-listing">{l s='Latest posts' mod='ybc_blog'}</h1>
    {elseif $blog_popular}
        <h1 class="page-heading product-listing">{l s='Popular posts' mod='ybc_blog'}</h1>
    {elseif $blog_featured}
        <h1 class="page-heading product-listing">{l s='Featured posts' mod='ybc_blog'}</h1>
    {elseif $blog_tag}
        <h1 class="page-heading product-listing">{l s='Tag: ' mod='ybc_blog'}"{ucfirst($blog_tag)|escape:'html':'UTF-8'}"</h1>
    {elseif $blog_search}
        <h1 class="page-heading product-listing">{l s='Search: ' mod='ybc_blog'}"{ucfirst(str_replace('+',' ',$blog_search))|escape:'html':'UTF-8'}"</h1>
    {elseif $author}
        {if isset($author.disabled) && $author.disabled}
            <p class="alert alert-warning">{l s='This author is not available' mod='ybc_blog'}</p>
        {else}
            {if isset($blog_config.YBC_BLOG_AUTHOR_INFORMATION)&& $blog_config.YBC_BLOG_AUTHOR_INFORMATION}

                {if isset($author.description)&&$author.description}
                    <div class="ybc-block-author ybc-block-author-avata">
                        {if $author.avata}
                            <div class="avata_img">
                                <img class="avata" src="{$link->getMediaLink("`$smarty.const._PS_YBC_BLOG_IMG_`avata/`$author.avata|escape:'html':'UTF-8'`")}"/>
                            </div>
                        {/if}
                        <div class="ybc-des-and-author">
                            <div class="ybc-author-name">
                                <h1 class="page-heading product-listing">
                                    <a href="{$author.author_link|escape:'html':'UTF-8'}">
                                        {l s='Author' mod='ybc_blog'}: {$author.name|escape:'html':'UTF-8'}
                                    </a>
                                </h1>
                            </div>
                            {if isset($author.description)&&$author.description}
                                <div class="ybc-author-description">
                                    {$author.description|nl2br nofilter}
                                </div>
                            {/if}
                        </div>
                    </div>
                {else}
                    <div class="ybc-author-name">
                        <h1 class="page-heading product-listing">
                            {l s='Author' mod='ybc_blog'}: {$author.name|escape:'html':'UTF-8'}
                        </h1>
                    </div>
                {/if}
            {else}
                <h1 class="page-heading product-listing">{l s='Author: ' mod='ybc_blog'}"{$author.name|escape:'html':'UTF-8'}"</h1>
            {/if}
        {/if}
    {elseif $month}
        <h1 class="page-heading product-listing">{l s='Posted in : ' mod='ybc_blog'}"{$month|escape:'html':'UTF-8'}"</h1>
    {elseif $year}
        <h1 class="page-heading product-listing">{l s='Posted in: ' mod='ybc_blog'}"{$year|escape:'html':'UTF-8'}"</h1>
    {/if}

    {if !($blog_category && (!isset($blog_category.enabled) || isset($blog_category.enabled) && !$blog_category.enabled)) && ($blog_category || $blog_tag || $blog_search || $author || $is_main_page || $blog_latest || $blog_featured || $blog_popular || $month || $year) && (!$author || ($author && !isset($author.disabled)))}
        {if isset($blog_posts) && $blog_posts}
            {if Configuration::get('YBC_BLOG_DISPLAY_SORT_BY')}
                <div>
                    <div id="js-post-list-top" class="row post-selection">
                        <div class="col-lg-6 col-md-4 col-sm-4 hidden-sm-down total-products">&nbsp;</div>
                        <div class="col-md-8 col-sm-8 col-lg-6">
                            <span class="col-sm-4 col-md-4 hidden-sm-down sort-by">{l s='Sort by:' mod='ybc_blog'}</span>
                            <div class="col-sm-8 col-xs-8 col-md-8 products-sort-order dropdown">
                                <select class="select" name="ybc_sort_by_posts">
                                    <option value="id_post" selected="selected">{l s='Latest post' mod='ybc_blog'}</option>
                                    <option value="sort_order">{l s='Sort order' mod='ybc_blog'}</option>
                                    <option value="click_number">{l s='Popular post' mod='ybc_blog'}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="ets_blog_loading sort">
                                <span id="squaresWaveG">
                                    <span id="squaresWaveG_1" class="squaresWaveG"></span>
                                    <span id="squaresWaveG_2" class="squaresWaveG"></span>
                                    <span id="squaresWaveG_3" class="squaresWaveG"></span>
                                    <span id="squaresWaveG_4" class="squaresWaveG"></span>
                                    <span id="squaresWaveG_5" class="squaresWaveG"></span>
                                </span>
                    </div>
                    <div class="clearfix"></div>
                </div>
            {/if}
            <ul class="ybc-blog-list row {if $is_main_page}blog-main-page{/if}">
                {if isset($is17) && $is17}
                    {include file='module:ybc_blog/views/templates/hook/blog_list.tpl' blog_posts=$blog_posts first_post=true}
                {else}
                    {include file='./blog_list.tpl' blog_posts=$blog_posts first_post=true}
                {/if}
            </ul>
            {if $blog_paggination}
                <div class="blog-paggination">
                    {$blog_paggination nofilter}
                </div>
            {/if}
            {if isset($blog_config.YBC_BLOG_AUTO_LOAD) &&$blog_config.YBC_BLOG_AUTO_LOAD}
                <div class="ets_blog_loading autoload">
                            <span id="squaresWaveG">
                                <span id="squaresWaveG_1" class="squaresWaveG"></span>
                                <span id="squaresWaveG_2" class="squaresWaveG"></span>
                                <span id="squaresWaveG_3" class="squaresWaveG"></span>
                                <span id="squaresWaveG_4" class="squaresWaveG"></span>
                                <span id="squaresWaveG_5" class="squaresWaveG"></span>
                            </span>
                </div>
                <div class="clearfix"></div>
            {/if}
        {else}
            <p>{l s='No posts found' mod='ybc_blog'}</p>
        {/if}
    {/if}
</div>