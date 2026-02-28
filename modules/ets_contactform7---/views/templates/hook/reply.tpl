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
<li>
    <span class="content-reply">
        <b>{l s='Reply' mod='ets_contactform7'}&nbsp;{$countReply|intval}:&nbsp;</b>{$reply->content|strip_tags:'UTF-8'|truncate:150:'...'}
    </span>
    <span class="content-reply-full">
        <p>
            <b>{l s='Reply to:' mod='ets_contactform7'}</b>&nbsp;{$reply->reply_to|escape:'html':'UTF-8'} {$reply->date_add|escape:'html':'UTF-8'}
        </p>
        <p>
            <b>{l s='Subject:' mod='ets_contactform7'}</b>&nbsp;{$reply->subject|escape:'html':'UTF-8'}
        </p>
        <p class="content-message">
            <b>{l s='Content:' mod='ets_contactform7'}</b>&nbsp;{$reply->content nofilter}
        </p>
        {if $reply->attachment}
            <p class="attachment-message">
                <b>{l s='Attachment:' mod='ets_contactform7'}</b>&nbsp;<a href="{$link->getAdminLink('AdminContactFormMessage')|escape:'html':'UTF-8'}&downloadFileReply=1&id_reply={$reply->id|intval}">{$reply->attachment_name|escape:'html':'UTF-8'}</a>
            </p>
        {/if}
    </span>
</li>