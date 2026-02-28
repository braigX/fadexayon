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

{if $posts}
    <div data-items="{$blog_config.YBC_BLOG_RELATED_POST_ROW_IN_PRODUCT|intval}"
         class="ybc-blog-related-posts on_product ybc_blog_related_posts_type_{if $blog_config.YBC_BLOG_PRODUCT_POST_TYPE}{$blog_config.YBC_BLOG_PRODUCT_POST_TYPE|escape:'html':'UTF-8'}{else}default{/if} ybc_blog_{$blog_config.YBC_BLOG_PRODUCT_POST_TYPE|escape:'html':'UTF-8'}">
        <h4 class="title_blog">{l s='Related articles' mod='ybc_blog'}</h4>
        <div class="ybc-blog-related-posts-wrapper">
            {assign var='post_row' value=$blog_config.YBC_BLOG_RELATED_POST_ROW_IN_PRODUCT|intval}
            <div class="ybc_blog_content_block ybc-blog-related-posts-list dt-{$post_row|intval}{if $blog_config.YBC_BLOG_PRODUCT_POST_TYPE=='carousel'} blog_type_slider{/if}">
                {foreach from=$posts item='rpost'}                                            
                    <div class="ybc_blog_content_block_item ybc-blog-related-posts-list-li col-xs-12 col-sm-4 col-lg-{12/$post_row|intval} thumbnail-container">
                        {if $rpost.thumb}
                            <a class="ybc_item_img{if isset($blog_config.YBC_BLOG_LAZY_LOAD)&& $blog_config.YBC_BLOG_LAZY_LOAD} ybc_item_img_ladyload{/if}" href="{$rpost.link|escape:'html':'UTF-8'}">
                                <img width="{Configuration::get('YBC_BLOG_IMAGE_BLOG_THUMB_WIDTH'|escape:'html':'UTF-8')}" height="{Configuration::get('YBC_BLOG_IMAGE_BLOG_THUMB_HEIGHT'|escape:'html':'UTF-8')}" src="{if isset($blog_config.YBC_BLOG_LAZY_LOAD)&& $blog_config.YBC_BLOG_LAZY_LOAD}{$link->getMediaLink("`$smarty.const._MODULE_DIR_`ybc_blog/views/img/bg-grey.png")|escape:'html':'UTF-8'}{else}{$rpost.thumb|escape:'html':'UTF-8'}{/if}" alt="{$rpost.title|escape:'html':'UTF-8'}" {if isset($blog_config.YBC_BLOG_LAZY_LOAD)&& $blog_config.YBC_BLOG_LAZY_LOAD} data-original="{$rpost.thumb|escape:'html':'UTF-8'}" class="lazyload"{/if} />
                                {if isset($blog_config.YBC_BLOG_LAZY_LOAD)&& $blog_config.YBC_BLOG_LAZY_LOAD}
                                    <div class="loader_lady_custom"></div>
                                {/if}
                            </a>   
                                                                             
                        {/if}
                        <a class="ybc_title_block" href="{$rpost.link|escape:'html':'UTF-8'}">{$rpost.title|escape:'html':'UTF-8'}</a>
                        <div class="ybc-blog-sidear-post-meta">
                            {if $rpost.categories}
                                {assign var='ik' value=0}
                                {assign var='totalCat' value=count($rpost.categories)}                        
                                <div class="ybc-blog-categories">
                                    <span class="be-label">{l s='Posted in' mod='ybc_blog'}: </span>
                                    {foreach from=$rpost.categories item='cat'}
                                        {assign var='ik' value=$ik+1}                                        
                                        <a href="{$cat.link|escape:'html':'UTF-8'}">{ucfirst($cat.title)|escape:'html':'UTF-8'}</a>{if $ik < $totalCat}, {/if}
                                    {/foreach}
                                </div>
                            {/if}
                            <span class="post-date">{dateFormat date=$rpost.datetime_added full=0}</span>
                        </div>
                        {if $allowComments || $show_views || $allow_like}
                            <div class="ybc-blog-latest-toolbar">                                         
                                {if $show_views}
                                    <span class="ybc-blog-latest-toolbar-views">
                                        <i class="ets_svg">
                                                                <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1664 960q-152-236-381-353 61 104 61 225 0 185-131.5 316.5t-316.5 131.5-316.5-131.5-131.5-316.5q0-121 61-225-229 117-381 353 133 205 333.5 326.5t434.5 121.5 434.5-121.5 333.5-326.5zm-720-384q0-20-14-34t-34-14q-125 0-214.5 89.5t-89.5 214.5q0 20 14 34t34 14 34-14 14-34q0-86 61-147t147-61q20 0 34-14t14-34zm848 384q0 34-20 69-140 230-376.5 368.5t-499.5 138.5-499.5-139-376.5-368q-20-35-20-69t20-69q140-229 376.5-368t499.5-139 499.5 139 376.5 368q20 35 20 69z"/></svg>
                                                            </i> {$rpost.click_number|intval}
                                        {if $rpost.click_number!=1}{l s='views' mod='ybc_blog'}
                                        {else}{l s='view' mod='ybc_blog'}{/if}
                                    </span> 
                                {/if}                       
                                {if $allow_like}
                                    <span class="ybc-blog-like-span ybc-blog-like-span-{$rpost.id_post|intval} {if isset($rpost.liked) && $rpost.liked}active{/if}"  data-id-post="{$rpost.id_post|intval}">                        
                                        <i class="ets_svg">
                                            <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M384 1344q0-26-19-45t-45-19-45 19-19 45 19 45 45 19 45-19 19-45zm1152-576q0-51-39-89.5t-89-38.5h-352q0-58 48-159.5t48-160.5q0-98-32-145t-128-47q-26 26-38 85t-30.5 125.5-59.5 109.5q-22 23-77 91-4 5-23 30t-31.5 41-34.5 42.5-40 44-38.5 35.5-40 27-35.5 9h-32v640h32q13 0 31.5 3t33 6.5 38 11 35 11.5 35.5 12.5 29 10.5q211 73 342 73h121q192 0 192-167 0-26-5-56 30-16 47.5-52.5t17.5-73.5-18-69q53-50 53-119 0-25-10-55.5t-25-47.5q32-1 53.5-47t21.5-81zm128-1q0 89-49 163 9 33 9 69 0 77-38 144 3 21 3 43 0 101-60 178 1 139-85 219.5t-227 80.5h-129q-96 0-189.5-22.5t-216.5-65.5q-116-40-138-40h-288q-53 0-90.5-37.5t-37.5-90.5v-640q0-53 37.5-90.5t90.5-37.5h274q36-24 137-155 58-75 107-128 24-25 35.5-85.5t30.5-126.5 62-108q39-37 90-37 84 0 151 32.5t102 101.5 35 186q0 93-48 192h176q104 0 180 76t76 179z"></path></svg>
                                        </i> <span class="ben_{$rpost.id_post|intval}">{$rpost.likes|intval}</span>
                                        <span class="blog-post-like-text blog-post-like-text-{$rpost.id_post|intval}">
                                            {l s='Liked' mod='ybc_blog'}
                                        </span>
                                    </span>  
                                {/if}
                                {if $allowComments && $rpost.comments_num>0}
                                    <span class="ybc-blog-latest-toolbar-comments"><i class="ets_svg"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M896 384q-204 0-381.5 69.5t-282 187.5-104.5 255q0 112 71.5 213.5t201.5 175.5l87 50-27 96q-24 91-70 172 152-63 275-171l43-38 57 6q69 8 130 8 204 0 381.5-69.5t282-187.5 104.5-255-104.5-255-282-187.5-381.5-69.5zm896 512q0 174-120 321.5t-326 233-450 85.5q-70 0-145-8-198 175-460 242-49 14-114 22h-5q-15 0-27-10.5t-16-27.5v-1q-3-4-.5-12t2-10 4.5-9.5l6-9 7-8.5 8-9q7-8 31-34.5t34.5-38 31-39.5 32.5-51 27-59 26-76q-157-89-247.5-220t-90.5-281q0-174 120-321.5t326-233 450-85.5 450 85.5 326 233 120 321.5z"/></svg>
                                                                </i> {$rpost.comments_num|intval}
                                        {if $rpost.comments_num!=1}
                                            {l s='comments' mod='ybc_blog'}
                                        {else}
                                            {l s='comment' mod='ybc_blog'}
                                        {/if}
                                    </span> 
                                {/if}
                            </div>
                        {/if} 
                        {if $display_desc}
                            {if $rpost.short_description}
                                <div class="blog_description">{$rpost.short_description|strip_tags:'UTF-8'|truncate:120:'...'|escape:'html':'UTF-8'}</div>
                            {elseif $rpost.description}
                                <div class="blog_description">{$rpost.description|strip_tags:'UTF-8'|truncate:120:'...'|escape:'html':'UTF-8'}</div>
                            {/if}
                        {/if}
                        <a class="read_more" href="{$rpost.link|escape:'html':'UTF-8'}">{if $blog_config.YBC_BLOG_TEXT_READMORE}{$blog_config.YBC_BLOG_TEXT_READMORE|escape:'html':'UTF-8'}{else}{l s='Read More' mod='ybc_blog'}{/if}</a>    
                    </div>
                {/foreach}                        
            </div>
        </div>
    </div>
{/if}