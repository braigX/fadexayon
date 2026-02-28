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
<div class="panel">
    <div class="comment-content">
        <div class="panel-heading-action">
            <div class="panel-heading-action-status">
                {l s='Status' mod='ybc_blog'}:&nbsp;<span class="action_status{if $comment->approved} status_approved{else} status_pending{/if}">{if $comment->approved}{l s='Approved' mod='ybc_blog'}{else}{l s='Pending' mod='ybc_blog'}{/if}</span>
            </div>
            <div class="panel-heading-action-right">
                {if $comment->approved}
                    <a class="field-approved list-action-enable action-enabled" title="{l s='Click to disapprove' mod='ybc_blog'}" href="{$curenturl|escape:'html':'UTF-8'}&id_comment={$comment->id|intval}&change_comment_approved=0">
                        <i class="ets_svg check"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1671 566q0 40-28 68l-724 724-136 136q-28 28-68 28t-68-28l-136-136-362-362q-28-28-28-68t28-68l136-136q28-28 68-28t68 28l294 295 656-657q28-28 68-28t68 28l136 136q28 28 28 68z"/></svg>
                        </i>
                    </a>
                {else}
                    <a class="field-approved list-action-enable action-disabled" title="{l s='Click to mark as approved' mod='ybc_blog'}" href="{$curenturl|escape:'html':'UTF-8'}&id_comment={$comment->id|intval}&change_comment_approved=1">
                        <i class="ets_svg remove">
                            <svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1490 1322q0 40-28 68l-136 136q-28 28-68 28t-68-28l-294-294-294 294q-28 28-68 28t-68-28l-136-136q-28-28-28-68t28-68l294-294-294-294q-28-28-28-68t28-68l136-136q28-28 68-28t68 28l294 294 294-294q28-28 68-28t68 28l136 136q28 28 28 68t-28 68l-294 294 294 294q28 28 28 68z"/></svg>
                        </i>
                    </a>
                {/if}
                <a class="del_comment" title="{l s='Delete' mod='ybc_blog'}" onclick="return confirm('Do you want to delete this comment?');" href="{$link_delete|escape:'html':'UTF-8'}">
                    <i class="ets_svg trash">
                        <svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M704 736v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm128 724v-948h-896v948q0 22 7 40.5t14.5 27 10.5 8.5h832q3 0 10.5-8.5t14.5-27 7-40.5zm-672-1076h448l-48-117q-7-9-17-11h-317q-10 2-17 11zm928 32v64q0 14-9 23t-23 9h-96v948q0 83-47 143.5t-113 60.5h-832q-66 0-113-58.5t-47-141.5v-952h-96q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h309l70-167q15-37 54-63t79-26h320q40 0 79 26t54 63l70 167h309q14 0 23 9t9 23z"/></svg>
                    </i>
                </a>
            </div>
        </div>
        <h4 class="subject_comment">{$comment->subject|escape:'html':'UTF-8'}</h4>
        {if $comment->name}
            <h4 class="comment_name">
                {l s='By' mod='ybc_blog'}: <span>{$comment->name|escape:'html':'UTF-8'}</span>
                <div class="rating">

                    <div class="blog_star_admin" data-rate="{$comment->rating|intval}">
                        {assign var='everage_rating' value=$comment->rating}
                        {if $everage_rating == 1}★☆☆☆☆
                        {elseif  $everage_rating == 2}★★☆☆☆
                        {elseif  $everage_rating == 3}★★★☆☆
                        {elseif  $everage_rating == 4}★★★★☆
                        {elseif  $everage_rating == 5}★★★★★{/if}
                    </div>
                </div>
            </h4>
        {/if}
        <h4 class="post_title">
            {l s='Post title' mod='ybc_blog'}: <span><a target="_blank" href="{$post_link|escape:'html':'UTF-8'}" title="{$post_class->title|escape:'html':'UTF-8'}">{$post_class->title|escape:'html':'UTF-8'}</a></span>
        </h4> 
        <div class="comment-content">
            <p>{$comment->comment nofilter}</p>
        </div>
        <form method="post" action="">
            <div class="form_reply">
                <textarea id="reply_comment_text" placeholder="{l s='Reply ...' mod='ybc_blog'}" name="reply_comment_text">{if isset($reply_comment_text)}{$reply_comment_text|escape:'html':'UTF-8'}{/if}</textarea>
                <input class="btn btn-primary btn-default" type="submit" value="{l s='Send' mod='ybc_blog'}" name="addReplyComment"/><br />
            </div>
            {if $replies}
                <h4 class="replies_comment">{l s='Replies' mod='ybc_blog'}:</h4>
                <div class="table-responsive clearfix">
                    <table class="table configuration">
                        <thead>
                            <tr class="nodrag nodrop">
                                <script type="text/javascript">
                                    var detele_confirm ="{l s='Do you want to delete this item?' mod='ybc_blog'}";
                                </script>
                                <th class="fixed-width-xs">
                                    <span class="title_box">
                                        <input value="" class="reply_readed_all" type="checkbox" />
                                    </span>
                                </th>
                                <td>{l s='Id' mod='ybc_blog'}</td>
                                <td>{l s='Name' mod='ybc_blog'}</td>
                                <td>{l s='Reply content' mod='ybc_blog'}</td>
                                <td class="text-center">{l s='Approved' mod='ybc_blog'}</td>
                                <td class="text-center">{l s='Action' mod='ybc_blog'}</td>
                            </tr>
                        </thead>
                        <tbody id="list-ybc_reply">
                            {foreach from=$replies item='reply'}
                                <tr>
                                    <td class="reply-more-action">
                                        <input type="checkbox" name="reply_readed[{$reply.id_reply|intval}]" class="reply_readed" value="1" data-approved="{if $reply.approved}1{else}0{/if}"/>
                                    </td> 
                                    <td>{$reply.id_reply|intval}</td>
                                    <td>{$reply.name|escape:'html':'UTF-8'}</td>
                                    <td>{$reply.reply nofilter}</td>
                                    <td class="text-center">
                                        {if $reply.approved}
                                            <a class="list-action field-approved list-action-enable action-enabled list-item-{$reply.id_reply|intval}" data-id="{$reply.id_reply|intval}" title="{l s='Unapproved' mod='ybc_blog'}" href="{$curenturl|escape:'html':'UTF-8'}&id_reply={$reply.id_reply|intval}&change_approved=0">
                                                <i class="ets_svg check"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1671 566q0 40-28 68l-724 724-136 136q-28 28-68 28t-68-28l-136-136-362-362q-28-28-28-68t28-68l136-136q28-28 68-28t68 28l294 295 656-657q28-28 68-28t68 28l136 136q28 28 28 68z"/></svg>
                                                </i>
                                            </a>
                                        {else}
                                            <a class="list-action field-approved list-action-enable action-disabled list-item-{$reply.id_reply|intval}" data-id="{$reply.id_reply|intval}" title="{l s='Approved' mod='ybc_blog'}" href="{$curenturl|escape:'html':'UTF-8'}&id_reply={$reply.id_reply|intval}&change_approved=1">
                                                <i class="ets_svg remove">
                                                    <svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1490 1322q0 40-28 68l-136 136q-28 28-68 28t-68-28l-294-294-294 294q-28 28-68 28t-68-28l-136-136q-28-28-28-68t28-68l294-294-294-294q-28-28-28-68t28-68l136-136q28-28 68-28t68 28l294 294 294-294q28-28 68-28t68 28l136 136q28 28 28 68t-28 68l-294 294 294 294q28 28 28 68z"/></svg>
                                                </i>
                                            </a>
                                        {/if}
                                    </td>
                                    <td class="text-center">
                                        <a class="del_reply" href="{$curenturl|escape:'html':'UTF-8'}&delreply=1&id_reply={$reply.id_reply|intval}" onclick="return confirm('{l s='Do you want to delete this item?' mod='ybc_blog'}');" title="{l s='Delete' mod='ybc_blog'}"><i class="ets_svg trash"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M704 736v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm128 724v-948h-896v948q0 22 7 40.5t14.5 27 10.5 8.5h832q3 0 10.5-8.5t14.5-27 7-40.5zm-672-1076h448l-48-117q-7-9-17-11h-317q-10 2-17 11zm928 32v64q0 14-9 23t-23 9h-96v948q0 83-47 143.5t-113 60.5h-832q-66 0-113-58.5t-47-141.5v-952h-96q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h309l70-167q15-37 54-63t79-26h320q40 0 79 26t54 63l70 167h309q14 0 23 9t9 23z"/></svg>
                                            </i>{l s='Delete' mod='ybc_blog'}</a>
                                    </td>
                                </tr>
                            {/foreach}
                        </tbody>
                    </table>
                    <select id="bulk_action_reply" name="bulk_action_reply" style="display:none;width: 200px;">
                        <option value="">{l s='Bulk actions' mod='ybc_blog'}</option>
                        <option value="mark_as_approved">{l s='Approved' mod='ybc_blog'}</option>
                        <option value="mark_as_unapproved">{l s='Unapproved' mod='ybc_blog'}</option>
                        <option value="delete_selected">{l s='Delete selected' mod='ybc_blog'}</option>
                    </select>
                </div>
            {/if}
            <div class="panel-footer">
                <a class="btn btn-default" href="{$link_back|escape:'html':'UTF-8'}" title="{l s='Back' mod='ybc_blog'}">
                <i class="process-icon-cancel"></i>
                {l s='Back' mod='ybc_blog'}
            </a>
            </div>
        </form>
    </div>
</div>