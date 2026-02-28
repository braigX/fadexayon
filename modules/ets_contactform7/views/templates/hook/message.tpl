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
<div class="panel view-message" id="message">
    <div class="panel-heading">
        <i class="icon-envelope"></i>
        [#{$message.id_contact_message|intval}]
        {$message.subject|escape:'html':'UTF-8'}
        {if $message.reply_to_check}
            <span class="panel-heading-action">
                <span class="{if $message.reply_to_check}action-reply-message{else}action-reply-message-disable{/if}" >
                    <i class="process-icon-reply"></i>{l s='Reply' mod='ets_contactform7'}
                </span>
            </span>
        {/if}
    </div>
    <div class="message-from">
        <strong>{l s='Sent by' mod='ets_contactform7'}:</strong>&nbsp;{$message.sender|escape:'html':'UTF-8'}&nbsp;<strong>{l s='Date' mod='ets_contactform7'}:</strong>&nbsp;{dateFormat date=$message.date_add full=1}
        {if $message.id_customer}
            <span class="customer_message">
                {l s='Identified customer:' mod='ets_contactform7'} <a href="{$link->getAdminLink('AdminCustomers')|escape:'html':'UTF-8'}&viewcustomer&id_customer={$message.id_customer|intval}">{$message.customer_name|escape:'html':'UTF-8'}</a>
            </span>
        {/if}
    </div>
    <div class="bootstrap success" style="display:none;">
    	<div class="alert alert-success">
    		<button data-dismiss="alert" class="close" type="button">×</button>
    		{l s='Your message has been successfully sent' mod='ets_contactform7'}
    	</div>
    </div>
    <div id="message-content">
        {$message.body nofilter}
    </div>
    {if $message.attachments}        
        {if $message.save_attachments}
            <div class="ctf7_attachments">
                <div><strong>{l s='Attachments: ' mod='ets_contactform7'}</strong></div>
                <ul id="list-attachments">
                    {assign var='index' value=1}
                    {foreach from =$message.attachments item='attachment'}
                        <li><a href="{$attachment.link_download|escape:'html':'UTF-8'}" target="_blank">{$attachment.name|escape:'html':'UTF-8'}</a></li>
                        {assign var='index' value=$index+1}
                    {/foreach}
                </ul>
            </div>
        {else}
            <p class="alert alert-warning">{l s='Attachments were sent via email' mod='ets_contactform7'}</p>
        {/if}
    {/if}
    <ul id="list-replies">
        {if $replies}
            {foreach from=$replies key='key' item='reply'}
                <li>
                    <span class="content-reply">
                        <b>{l s='Reply' mod='ets_contactform7'}&nbsp;{$key|intval+1}:&nbsp;</b>{$reply.content|strip_tags:'UTF-8'|truncate:150:'...'}
                    </span>
                    <span class="content-reply-full">
                        <p>
                            <b>{l s='Reply to:' mod='ets_contactform7'}</b>&nbsp;{$reply.reply_to|escape:'html':'UTF-8'} {$reply.date_add|escape:'html':'UTF-8'}
                        </p>
                        <p>
                            <b>{l s='Subject:' mod='ets_contactform7'}</b>&nbsp;{$reply.subject|escape:'html':'UTF-8'}
                        </p>
                        <p class="content-message">
                            <b>{l s='Content:' mod='ets_contactform7'}</b>&nbsp;{$reply.content nofilter}
                        </p>
                        {if $reply.attachment}
                            <p class="attachment-message">
                                <b>{l s='Attachment:' mod='ets_contactform7'}</b>&nbsp;<a href="{$link->getAdminLink('AdminContactFormMessage')|escape:'html':'UTF-8'}&downloadFileReply=1&id_reply={$reply.id_ets_ctf_message_reply|intval}">{$reply.attachment_name|escape:'html':'UTF-8'}</a>
                            </p>
                        {/if}
                    </span>
                </li>
            {/foreach}
        {/if}
    </ul>
</div>
<form id="module_form_reply-message" style="display:none;" class="defaultForm form-horizontal" novalidate="" enctype="multipart/form-data" method="post" action="">
    <div class="panel" id="replay-message-form">
         <div class="panel-heading">
            <i class="icon-envelope"></i>
            {l s='Reply message:' mod='ets_contactform7'}&nbsp;[#{$message.id_contact_message|intval}]&nbsp;{$message.subject|escape:'html':'UTF-8'}
        </div>
        <div class="form-wrapper">
            <input type="hidden" value="{$message.id_contact_message|intval}" name="id_message"/>
            <div class="form-group">
                <label class="control-label col-lg-2 required">{l s='From:' mod='ets_contactform7'} </label>
                <div class="col-lg-10">
                    <input name="from_reply" value="{$message.from_reply|escape:'html':'UTF-8'}" type="text"/>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-lg-2 required">{l s='To:' mod='ets_contactform7'} </label>
                <div class="col-lg-10">
                    <input name="reply_to" value="{$message.reply_to|escape:'html':'UTF-8'}" type="text"/>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-lg-2 required">{l s='Subject' mod='ets_contactform7'}</label>
                <div class="col-lg-10">
                    <input name="reply_subject" value="{l s='Reply' mod='ets_contactform7'}: {$message.subject|escape:'html':'UTF-8'}" type="text" />
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-lg-2 required">{l s='Reply to' mod='ets_contactform7'}</label>
                <div class="col-lg-10">
                    <input name="reply_to_reply" value="{$message.reply|escape:'html':'UTF-8'}" type="text" />
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-lg-2 required">{l s='Message' mod='ets_contactform7'} </label>
                <div class="col-lg-10">
                    <textarea name="message_reply" placeholder="{l s='Message' mod='ets_contactform7'}"></textarea>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-lg-2">{l s='Attachment' mod='ets_contactform7'}</label>
                <div class="col-lg-10">
                    <div class="dummyfile input-group sass">
                        <input id="attachment" type="file" name="attachment" class="hide-file-upload">
                        <span class="input-group-addon"><i class="icon-file"></i></span>
                        <input id="attachment-name" type="text" class="disabled" name="filename" readonly="">
                        <span class="input-group-btn">
                            <button id="attachment-selectbutton" type="button" name="submitAddAttachments" class="btn btn-default">
                                <i class="icon-folder-open"></i> {l s='Choose a file' mod='ets_contactform7'}
                            </button>
                        </span>
                    </div>
                    <div class="help-block">{l s='Accepted formats: zip, jpg, gif, jpeg, png, pdf, doc, docx, txt. LIMIT '}{Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE')|intval}Mb</div>
                </div>
                <script type="text/javascript">
                    $(document).ready(function(){
                        $("#attachment-selectbutton,#attachment-name").click(function(e){
                            $("#attachment").trigger('click');
                        });
                        $("#attachment").change(function(e){
                            var val = $(this).val();
                            var file = val.split(/[\\/]/);
                            $("#attachment-name").val(file[file.length-1]);
                        });
                    });
                </script>
            </div>
            <div class="panel-footer">
                <button id="module_form_submit_btn_reply" class="btn btn-default pull-right" name="submitReplyMessage" value="1" type="submit">
                    <i class="icon process-icon-reply"></i>
                    {l s='Send' mod='ets_contactform7'}
                </button>
                <button id="module_form_submit_btn_back" class="btn btn-default pull-left" name="backReplyMessage" value="1" type="button">
                    <i class="icon process-icon-back"></i>
                    {l s='Back' mod='ets_contactform7'}
                </button>
            </div>
        </div>
    </div>
</form>