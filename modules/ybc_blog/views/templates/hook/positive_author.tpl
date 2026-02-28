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
{if $authors}
    <div class="page_blog block ybc_block_author {$blog_config.YBC_BLOG_RTL_CLASS|escape:'html':'UTF-8'} {if isset($blog_page) && $blog_page}page_{$blog_page|escape:'html':'UTF-8'}{else}page_blog{/if}">
        <h4 class="title_blog title_block">{l s='Top authors' mod='ybc_blog'}</h4>
        {assign var='product_row' value=$blog_config.YBC_BLOG_HOME_PER_ROW|intval}
        <div class="block_content">
        <ul class="">
            {foreach from=$authors item='author'}
                <li {if $blog_page=='home'}class="col-xs-12 col-sm-4 col-lg-{12/$product_row|intval}"{/if}> 
                    <div class="ybc-blog-comment-content">
                        {if $author.avata}
                            <div class="author_avata_show">
                                <img class="author_avata" src="{$author.avata|escape:'html':'UTF-8'}" />
                            </div>
                        {/if}
                        <div class="author_infor">
                            <a class="ybc_title_block" href="{$author.link|escape:'html':'UTF-8'}">{$author.information.name|escape:'html':'UTF-8'}</a> 
                            <span class="ybc_author_post_count">
                                {if count($author.posts)>1}
                                    {$author.posts|@count|intval} {l s='Posts' mod='ybc_blog'}
                                {else}
                                    {$author.posts|@count|intval} {l s='Post' mod='ybc_blog'}
                                {/if}
                            </span>
                            <a class="view_post" href="{$author.link|escape:'html':'UTF-8'}">
                                {if count($author.posts)>1}
                                    {l s='View posts' mod='ybc_blog'}
                                {else}
                                    {l s='View post' mod='ybc_blog'}
                                {/if}
                            </a>
                        </div>
                    </div>
                </li>
            {/foreach}
        </ul>
        <div class="blog_view_all_button">
            <a href="{$author_link|escape:'html':'UTF-8'}" class="view_all_link" title="{l s='View all authors' mod='ybc_blog'}">{l s='View all authors' mod='ybc_blog'}</a>
        </div>
        </div>
        <div class="clear"></div>
    </div>
{/if}