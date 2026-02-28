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
{foreach from=$posts item='post'}
    <li class="list_post_item list_all_comments">
        <div class="post-wrapper">
            <div class="author_avata_show">
                <img class="author_avata" src="{$post.avata|escape:'html':'UTF-8'}" />
            </div>
            <div class="ybc-blog-wrapper-content">
                <div class="ybc-blog-wrapper-content-main">
                    <div class="ybc-blog-comment-info">
                        <div class="post-author">
                            {if $post.name}
                                <span class="post-author-name">{$post.name|escape:'html':'UTF-8'}</span>
                            {/if}
                            {l s='on' mod='ybc_blog'}
                            <a class="ybc_title_block" href="{$post.link|escape:'html':'UTF-8'}">
                                {$post.title|escape:'html':'UTF-8'}
                            </a>
                        </div>
                        <div class="ybc-blog-latest-toolbar">
                            {if $allow_rating && $post.rating}
                                {assign var='everage_rating' value=$post.rating}
                                <span title="{l s='Average rating' mod='ybc_blog'}" class="ybc_blog_review"  data-rate="{$everage_rating|escape:'html':'UTF-8'}">
                                    {if $everage_rating == 1}★☆☆☆☆
                                    {elseif  $everage_rating == 2}★★☆☆☆
                                    {elseif  $everage_rating == 3}★★★☆☆
                                    {elseif  $everage_rating == 4}★★★★☆
                                    {elseif  $everage_rating == 5}★★★★★{/if}
                                    <meta  content="0"/>
                                    <meta  content="5"/>
                                </span>
                            {/if}
                            <span class="comment-time"><span>{l s='On' mod='ybc_blog'} </span>{dateFormat date=$post.datetime_added full=0}</span>
                        </div>

                    </div>
                    <div class="ybc-blog-comment-content">
                        <span class="subject-comment">{$post.subject|escape:'html':'UTF-8'}</span>
                        <div class="blogcomment">
                            {$post.comment|strip_tags:'UTF-8'|truncate:$comment_length:'...'|escape:'html':'UTF-8'}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </li>
{/foreach}