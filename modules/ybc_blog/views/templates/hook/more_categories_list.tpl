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
{if $blog_categories}
    {foreach from=$blog_categories item='category'}
        <li class="list_category_item">
            <div class="post-wrapper">
                {if $category.thumb}
                    <a class="ybc_item_img{if isset($blog_config.YBC_BLOG_LAZY_LOAD)&& $blog_config.YBC_BLOG_LAZY_LOAD} ybc_item_img_ladyload{/if}" href="{$category.link|escape:'html':'UTF-8'}">
                        <img width="{Configuration::get('YBC_BLOG_IMAGE_CATEGORY_THUMB_WIDTH'|escape:'html':'UTF-8')}" height="{Configuration::get('YBC_BLOG_IMAGE_CATEGORY_THUMB_HEIGHT'|escape:'html':'UTF-8')}" title="{$category.title|escape:'html':'UTF-8'}" src="{if isset($blog_config.YBC_BLOG_LAZY_LOAD)&& $blog_config.YBC_BLOG_LAZY_LOAD}{$link->getMediaLink("`$smarty.const._MODULE_DIR_`ybc_blog/views/img/bg-grey.png")|escape:'html':'UTF-8'}{else}{$category.thumb|escape:'html':'UTF-8'}{/if}" alt="{$category.title|escape:'html':'UTF-8'}" {if isset($blog_config.YBC_BLOG_LAZY_LOAD)&& $blog_config.YBC_BLOG_LAZY_LOAD} data-original="{$category.thumb|escape:'html':'UTF-8'}" class="lazyload"{/if} />
                        {if isset($blog_config.YBC_BLOG_LAZY_LOAD)&& $blog_config.YBC_BLOG_LAZY_LOAD}
                            <div class="loader_lady_custom"></div>
                        {/if}
                    </a>
                {elseif $category.image}
                    <a class="ybc_item_img{if isset($blog_config.YBC_BLOG_LAZY_LOAD)&& $blog_config.YBC_BLOG_LAZY_LOAD} ybc_item_img_ladyload{/if}" href="{$category.link|escape:'html':'UTF-8'}">
                        <img width="{Configuration::get('YBC_BLOG_IMAGE_CATEGORY_THUMB_WIDTH'|escape:'html':'UTF-8')}" height="{Configuration::get('YBC_BLOG_IMAGE_CATEGORY_THUMB_HEIGHT'|escape:'html':'UTF-8')}" title="{$category.title|escape:'html':'UTF-8'}" src="{if isset($blog_config.YBC_BLOG_LAZY_LOAD)&& $blog_config.YBC_BLOG_LAZY_LOAD}{$link->getMediaLink("`$smarty.const._MODULE_DIR_`ybc_blog/views/img/bg-grey.png")|escape:'html':'UTF-8'}{else}{$category.image|escape:'html':'UTF-8'}{/if}" alt="{$category.title|escape:'html':'UTF-8'}" {if isset($blog_config.YBC_BLOG_LAZY_LOAD)&& $blog_config.YBC_BLOG_LAZY_LOAD} data-original="{$category.image|escape:'html':'UTF-8'}" class="lazyload"{/if} />
                        {if isset($blog_config.YBC_BLOG_LAZY_LOAD)&& $blog_config.YBC_BLOG_LAZY_LOAD}
                            <div class="loader_lady_custom"></div>
                        {/if}
                    </a>
                {/if}
                <div class="ybc-blog-wrapper-content">
                    <div class="ybc-blog-wrapper-content-main">
                        <a class="ybc_title_block" href="{$category.link|escape:'html':'UTF-8'}">
                            {$category.title|escape:'html':'UTF-8'}&nbsp;({$category.count_posts|intval})
                        </a>

                        {if $category.sub_categogires}
                            <div class="sub_category">
                                <ul>
                                    {foreach from=$category.sub_categogires item='sub_category'}
                                        <li>
                                            <a href="{$sub_category.link|escape:'html':'UTF-8'}">{$sub_category.title|escape:'html':'UTF-8'}</a>
                                        </li>
                                    {/foreach}
                                </ul>
                            </div>
                        {/if}
                        <div class="blog_description">
                            {if $category.description}
                                {$category.description|truncate:500:'...'|escape:'html':'UTF-8' nofilter}
                            {/if}
                        </div><div class="clearfix"></div>
                        {if ( $category.count_posts > 0 )}
                            {if ( $category.count_posts == 1 )}
                                <a class="view_detail_link blog_view_all" href="{$category.link|escape:'html':'UTF-8'}">
                                    {l s='View %d post' sprintf=[$category.count_posts] mod='ybc_blog'}
                                </a>
                            {else}
                                <a class="view_detail_link blog_view_all" href="{$category.link|escape:'html':'UTF-8'}">
                                    {l s='View %d posts' sprintf=[$category.count_posts] mod='ybc_blog'}
                                </a>
                            {/if}
                        {else}
                            <a class="view_detail_link blog_view_all" href="{$category.link|escape:'html':'UTF-8'}">
                                {l s='View detail' mod='ybc_blog'}
                            </a>
                        {/if}
                        {*
                        <a class="view_detail_link read_more" href="{$category.link|escape:'html':'UTF-8'}">{l s='View detail' mod='ybc_blog'}</a>
                        *}
                    </div>
                </div>
            </div>
        </li>
    {/foreach}
{/if}