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
                        <img width="{Configuration::get('YBC_BLOG_IMAGE_CATEGORY_THUMB_WIDTH'|escape:'html':'UTF-8')}" height="{Configuration::get('YBC_BLOG_IMAGE_CATEGORY_THUMB_HEIGHT'|escape:'html':'UTF-8')}" src="{if isset($blog_config.YBC_BLOG_LAZY_LOAD) && $blog_config.YBC_BLOG_LAZY_LOAD}#{else} {$link->getMediaLink("`$smarty.const._PS_YBC_BLOG_IMG_`category/`$blog_category.image|escape:'htmlall':'UTF-8'`")}{/if}" alt="{$blog_category.title|escape:'html':'UTF-8'}" title="{$blog_category.title|escape:'html':'UTF-8'}" {if isset($blog_config.YBC_BLOG_LAZY_LOAD)&& $blog_config.YBC_BLOG_LAZY_LOAD} data-original="{$link->getMediaLink("`$smarty.const._PS_YBC_BLOG_IMG_`category/`$blog_category.image|escape:'htmlall':'UTF-8'`")}" class="lazyload"{/if} />
                        {if isset($blog_config.YBC_BLOG_LAZY_LOAD)&& $blog_config.YBC_BLOG_LAZY_LOAD}
                        <div class="loader_lady_custom"></div>
                        {/if}
                    </div>
                {/if}
                <h1 class="page-heading product-listing">{$blog_category.title|escape:'html':'UTF-8'}</h1>            
                {if $blog_category.description}
                    <div class="blog-category-desc">
                        {$blog_category.description nofilter}
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
        {if isset($blog_config.YBC_BLOG_AUTHOR_INFORMATION)&& $blog_config.YBC_BLOG_AUTHOR_INFORMATION}
            {if isset($author.description)&&$author.description}
                <div class="ybc-block-author">
                    {if $author.avata}
                        <div class="avata_img">
                            <img class="avata" src="{$link->getMediaLink("`$smarty.const._PS_YBC_BLOG_IMG_`avata/`$author.avata|escape:'htmlall':'UTF-8'`")}"/>
                        </div>
                    {/if}
                    <div class="ybc-des-and-author">
                        <div class="ybc-author-name">
                            <h1 class="page-heading product-listing">
                                {l s='Author' mod='ybc_blog'}: {$author.name|escape:'html':'UTF-8'}
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
            <h1 class="page-heading product-listing">{l s='Author: ' mod='ybc_blog'}"{$author|escape:'html':'UTF-8'}"</h1>
        {/if}
    {elseif $month}
        <h1 class="page-heading product-listing">{l s='Posted in : ' mod='ybc_blog'}"{$month|escape:'html':'UTF-8'}"</h1>
    {elseif $year}
        <h1 class="page-heading product-listing">{l s='Posted in: ' mod='ybc_blog'}"{$year|escape:'html':'UTF-8'}"</h1>
    {/if}
    
    {if !($blog_category && (!isset($blog_category.enabled) || isset($blog_category.enabled) && !$blog_category.enabled)) && ($blog_category || $blog_tag || $blog_search || $author || $is_main_page || $blog_latest || $blog_featured || $blog_popular || $month || $year)}
        {if isset($blog_posts) && $blog_posts}
            {if Configuration::get('YBC_BLOG_DISPLAY_SORT_BY')}
                <div>
                    <div id="js-post-list-top" class="row post-selection">
                        <div class="col-md-6 hidden-sm-down total-products">&nbsp;</div>
                        <div class="col-md-6">
                            <span class="col-sm-3 col-md-3 hidden-sm-down sort-by">{l s='Sort by:' mod='ybc_blog'}</span>
                            <div class="col-sm-9 col-xs-8 col-md-9 products-sort-order dropdown">
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
                {assign var='first_post' value=true}
                {foreach from=$blog_posts item='post'}            
                    <li>                         
                        <div class="post-wrapper">
                            {if $is_main_page && $first_post && ($blog_layout == 'large_list' || $blog_layout == 'large_grid')}
                                {if $post.image}
                                    <a class="ybc_item_img{if isset($blog_config.YBC_BLOG_LAZY_LOAD)&& $blog_config.YBC_BLOG_LAZY_LOAD} ybc_item_img_ladyload{/if}" href="{$post.link|escape:'html':'UTF-8'}">
                                        <img width="{Configuration::get('YBC_BLOG_IMAGE_BLOG_THUMB_WIDTH'|escape:'html':'UTF-8')}" height="{Configuration::get('YBC_BLOG_IMAGE_BLOG_THUMB_HEIGHT'|escape:'html':'UTF-8')}" title="{$post.title|escape:'html':'UTF-8'}" src="{if isset($blog_config.YBC_BLOG_LAZY_LOAD)&& $blog_config.YBC_BLOG_LAZY_LOAD}{$link->getMediaLink("`$smarty.const._MODULE_DIR_`ybc_blog/views/img/bg-grey.png")|escape:'html':'UTF-8'}{else}{$post.image|escape:'html':'UTF-8'}{/if}" alt="{$post.title|escape:'html':'UTF-8'}" {if isset($blog_config.YBC_BLOG_LAZY_LOAD)&& $blog_config.YBC_BLOG_LAZY_LOAD} data-original="{$post.image|escape:'html':'UTF-8'}" class="lazyload"{/if} />
                                        {if isset($blog_config.YBC_BLOG_LAZY_LOAD)&& $blog_config.YBC_BLOG_LAZY_LOAD}
                                        <div class="loader_lady_custom"></div>
                                        {/if}
                                    </a>                              
                                {elseif $post.thumb}
                                    <a class="ybc_item_img{if isset($blog_config.YBC_BLOG_LAZY_LOAD)&& $blog_config.YBC_BLOG_LAZY_LOAD} ybc_item_img_ladyload{/if}" href="{$post.link|escape:'html':'UTF-8'}">
                                        <img width="{Configuration::get('YBC_BLOG_IMAGE_BLOG_THUMB_WIDTH'|escape:'html':'UTF-8')}" height="{Configuration::get('YBC_BLOG_IMAGE_BLOG_THUMB_HEIGHT'|escape:'html':'UTF-8')}" title="{$post.title|escape:'html':'UTF-8'}" src="{if isset($blog_config.YBC_BLOG_LAZY_LOAD)&& $blog_config.YBC_BLOG_LAZY_LOAD}{$link->getMediaLink("`$smarty.const._MODULE_DIR_`ybc_blog/views/img/bg-grey.png")|escape:'html':'UTF-8'}{else}{$post.thumb|escape:'html':'UTF-8'}{/if}" alt="{$post.title|escape:'html':'UTF-8'}" {if isset($blog_config.YBC_BLOG_LAZY_LOAD)&& $blog_config.YBC_BLOG_LAZY_LOAD} data-original="{$post.thumb|escape:'html':'UTF-8'}" class="lazyload"{/if} />
                                        {if isset($blog_config.YBC_BLOG_LAZY_LOAD)&& $blog_config.YBC_BLOG_LAZY_LOAD}
                                        <div class="loader_lady_custom"></div>
                                        {/if}
                                    </a>
                                {/if}
                                {assign var='first_post' value=false}
                            {elseif $post.thumb}
                                <a class="ybc_item_img{if isset($blog_config.YBC_BLOG_LAZY_LOAD)&& $blog_config.YBC_BLOG_LAZY_LOAD} ybc_item_img_ladyload{/if}" href="{$post.link|escape:'html':'UTF-8'}">
                                    <img width="{Configuration::get('YBC_BLOG_IMAGE_BLOG_THUMB_WIDTH'|escape:'html':'UTF-8')}" height="{Configuration::get('YBC_BLOG_IMAGE_BLOG_THUMB_HEIGHT'|escape:'html':'UTF-8')}" title="{$post.title|escape:'html':'UTF-8'}" src="{if isset($blog_config.YBC_BLOG_LAZY_LOAD)&& $blog_config.YBC_BLOG_LAZY_LOAD}{$link->getMediaLink("`$smarty.const._MODULE_DIR_`ybc_blog/views/img/bg-grey.png")|escape:'html':'UTF-8'}{else}{$post.thumb|escape:'html':'UTF-8'}{/if}" alt="{$post.title|escape:'html':'UTF-8'}" {if isset($blog_config.YBC_BLOG_LAZY_LOAD)&& $blog_config.YBC_BLOG_LAZY_LOAD} data-original="{$post.thumb|escape:'html':'UTF-8'}" class="lazyload"{/if} />
                                    {if isset($blog_config.YBC_BLOG_LAZY_LOAD)&& $blog_config.YBC_BLOG_LAZY_LOAD}
                                        <div class="loader_lady_custom"></div>
                                    {/if}
                                </a>
                            {/if}
                            <div class="ybc-blog-wrapper-content">
                            <div class="ybc-blog-wrapper-content-main">
                                <a class="ybc_title_block" href="{$post.link|escape:'html':'UTF-8'}">{$post.title|escape:'html':'UTF-8'}</a>
                                {if $show_categories && $post.categories}
                                    <div class="ybc-blog-sidear-post-meta">
                                        {if $show_categories && $post.categories}
                                            <div class="ybc-blog-categories">
                                                {assign var='ik' value=0}
                                                {assign var='totalCat' value=count($post.categories)}
                                                <span class="be-label">{l s='Posted in' mod='ybc_blog'}: </span>
                                                {foreach from=$post.categories item='cat'}
                                                    {assign var='ik' value=$ik+1}                                        
                                                    <a href="{$cat.link|escape:'html':'UTF-8'}">{ucfirst($cat.title)|escape:'html':'UTF-8'}</a>{if $ik < $totalCat}, {/if}
                                                {/foreach}
                                            </div>
                                        {/if}
                                    </div> 
                                {/if}
                                <div class="ybc-blog-latest-toolbar">	
    								{if $show_views}                    
                                            <span class="ybc-blog-latest-toolbar-views" title="{l s='Page views' mod='ybc_blog'}">
                                                {$post.click_number|intval}
                                                {if $post.click_number !=1}<span>
                                                    <i class="ets_svg">
                                                                <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1664 960q-152-236-381-353 61 104 61 225 0 185-131.5 316.5t-316.5 131.5-316.5-131.5-131.5-316.5q0-121 61-225-229 117-381 353 133 205 333.5 326.5t434.5 121.5 434.5-121.5 333.5-326.5zm-720-384q0-20-14-34t-34-14q-125 0-214.5 89.5t-89.5 214.5q0 20 14 34t34 14 34-14 14-34q0-86 61-147t147-61q20 0 34-14t14-34zm848 384q0 34-20 69-140 230-376.5 368.5t-499.5 138.5-499.5-139-376.5-368q-20-35-20-69t20-69q140-229 376.5-368t499.5-139 499.5 139 376.5 368q20 35 20 69z"/></svg>
                                                            </i> {l s='Views' mod='ybc_blog'}</span>
                                                {else}
                                                    <span><i class="ets_svg">
                                                                <svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1664 960q-152-236-381-353 61 104 61 225 0 185-131.5 316.5t-316.5 131.5-316.5-131.5-131.5-316.5q0-121 61-225-229 117-381 353 133 205 333.5 326.5t434.5 121.5 434.5-121.5 333.5-326.5zm-720-384q0-20-14-34t-34-14q-125 0-214.5 89.5t-89.5 214.5q0 20 14 34t34 14 34-14 14-34q0-86 61-147t147-61q20 0 34-14t14-34zm848 384q0 34-20 69-140 230-376.5 368.5t-499.5 138.5-499.5-139-376.5-368q-20-35-20-69t20-69q140-229 376.5-368t499.5-139 499.5 139 376.5 368q20 35 20 69z"/></svg>
                                                            </i> {l s='View' mod='ybc_blog'}</span>
                                                {/if}
                                            </span>
                                    {/if} 
                                    {if $allow_comment && $post.total_review}
                                             <div class="blog_rating_wrapper"   >
                                                 <span class="total_views" ><i class="ets_svg"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M896 384q-204 0-381.5 69.5t-282 187.5-104.5 255q0 112 71.5 213.5t201.5 175.5l87 50-27 96q-24 91-70 172 152-63 275-171l43-38 57 6q69 8 130 8 204 0 381.5-69.5t282-187.5 104.5-255-104.5-255-282-187.5-381.5-69.5zm896 512q0 174-120 321.5t-326 233-450 85.5q-70 0-145-8-198 175-460 242-49 14-114 22h-5q-15 0-27-10.5t-16-27.5v-1q-3-4-.5-12t2-10 4.5-9.5l6-9 7-8.5 8-9q7-8 31-34.5t34.5-38 31-39.5 32.5-51 27-59 26-76q-157-89-247.5-220t-90.5-281q0-174 120-321.5t326-233 450-85.5 450 85.5 326 233 120 321.5z"></path></svg>
                                                                </i> {$post.total_review|intval}</span>
                                                 <span>
                                                    {if $post.total_review != 1}
                                                        {l s='Comments' mod='ybc_blog'}
                                                    {else}
                                                        {l s='Comment' mod='ybc_blog'}
                                                    {/if}
                                                </span>
                                                {if $allow_rating && isset($post.everage_rating) && $post.everage_rating}
                                                    {assign var='everage_rating' value=$post.everage_rating}
                                                    <div class="blog-extra-item be-rating-block item">
                                                        <div class="blog_rating_wrapper">
                                                            <div title="{l s='Average rating' mod='ybc_blog'}" class="ybc_blog_review" >
                                                                {if $everage_rating == 1}★☆☆☆☆
                                                                {elseif  $everage_rating == 2}★★☆☆☆
                                                                {elseif  $everage_rating == 3}★★★☆☆
                                                                {elseif  $everage_rating == 4}★★★★☆
                                                                {elseif  $everage_rating == 5}★★★★★{/if}
                                                                <meta  content="0"/>
                                                                <span class="ybc-blog-rating-value"  >({number_format((float)$everage_rating, 1, '.', '')|escape:'html':'UTF-8'})</span>
                                                                <meta  content="5"/>
                                                            </div>
                                                        </div>
                                                    </div>
                                                {/if} 
                                             </div>
                                        {/if}
                                    {if $allow_like}
                                        <span title="{if $post.liked}{l s='Liked' mod='ybc_blog'}{else}{l s='Like this post' mod='ybc_blog'}{/if}" class="item ybc-blog-like-span ybc-blog-like-span-{$post.id_post|escape:'html':'UTF-8'} {if $post.liked}active{/if}"  data-id-post="{$post.id_post|escape:'html':'UTF-8'}">                        
                                            <i class="ets_svg">
                                            <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M384 1344q0-26-19-45t-45-19-45 19-19 45 19 45 45 19 45-19 19-45zm1152-576q0-51-39-89.5t-89-38.5h-352q0-58 48-159.5t48-160.5q0-98-32-145t-128-47q-26 26-38 85t-30.5 125.5-59.5 109.5q-22 23-77 91-4 5-23 30t-31.5 41-34.5 42.5-40 44-38.5 35.5-40 27-35.5 9h-32v640h32q13 0 31.5 3t33 6.5 38 11 35 11.5 35.5 12.5 29 10.5q211 73 342 73h121q192 0 192-167 0-26-5-56 30-16 47.5-52.5t17.5-73.5-18-69q53-50 53-119 0-25-10-55.5t-25-47.5q32-1 53.5-47t21.5-81zm128-1q0 89-49 163 9 33 9 69 0 77-38 144 3 21 3 43 0 101-60 178 1 139-85 219.5t-227 80.5h-129q-96 0-189.5-22.5t-216.5-65.5q-116-40-138-40h-288q-53 0-90.5-37.5t-37.5-90.5v-640q0-53 37.5-90.5t90.5-37.5h274q36-24 137-155 58-75 107-128 24-25 35.5-85.5t30.5-126.5 62-108q39-37 90-37 84 0 151 32.5t102 101.5 35 186q0 93-48 192h176q104 0 180 76t76 179z"></path></svg>
                                        </i> <span class="blog-post-total-like ben_{$post.id_post|escape:'html':'UTF-8'}">{$post.likes|escape:'html':'UTF-8'}</span>
                                            <span class="blog-post-like-text blog-post-like-text-{$post.id_post|escape:'html':'UTF-8'}"><span>{l s='Liked' mod='ybc_blog'}</span></span>
                                        </span> 
                                    {/if}                     
                                </div>
                                <div class="blog_description">
                                    {if $post.short_description}
                                        <p>{$post.short_description|strip_tags:'UTF-8'|truncate:500:'...'|escape:'html':'UTF-8'}</p>
                                    {elseif $post.description}
                                        <p>{$post.description|strip_tags:'UTF-8'|truncate:500:'...'|escape:'html':'UTF-8'}</p>
                                    {/if}
                                </div>
                                <a class="read_more" href="{$post.link|escape:'html':'UTF-8'}">{if $blog_config.YBC_BLOG_TEXT_READMORE}{$blog_config.YBC_BLOG_TEXT_READMORE|escape:'html':'UTF-8'}{else}{l s='Read More' mod='ybc_blog'}{/if}</a>
                              </div>
                            </div>
                        </div>
                        
                    </li>
                {/foreach}
            </ul>
            {if $blog_paggination}
                <div class="blog-paggination">
                    {$blog_paggination nofilter}
                </div>
            {/if}
            {if isset($blog_config.YBC_BLOG_AUTO_LOAD) &&$blog_config.YBC_BLOG_AUTO_LOAD}
                <div class="ets_blog_loading">
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