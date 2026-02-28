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
{Module::getInstanceByName('ets_contactform7')->hookContactForm7LeftBlok() nofilter}
<script type="text/javascript">
{if isset($is_ps15) && $is_ps15}
    $(document).on('click','.dropdown-toggle',function(){
       $(this).closest('.btn-group').toggleClass('open'); 
    }); 
{/if}
</script>
<div class="ctf7-right-block">
    <form id="form-message" class="form-horizontal clearfix products-catalog" action="{$link->getAdminLink('AdminContactFormMessage',true)|escape:'html':'UTF-8'}" method="post">
        <input id="submitFilterMessage" type="hidden" value="0" name="submitFilterMessage" />
        <input type="hidden" value="1" name="page" />
        <input type="hidden" value="50" name="selected_pagination" />
        <div class="panel col-lg-12">
            <div class="panel-heading">
                 {l s='Messages' mod='ets_contactform7'}
                 {if isset($totalMessage) && $totalMessage}<span class="badge">{$totalMessage|intval}</span>{/if}
            </div>
            <div class="table-responsive-row clearfix">
                <table id="table-message" class="table message">
                    <thead>
                        <tr class="nodrag nodrop">
                            <th class="fixed-width-xs">
                                <span class="title_box">
                                    {if count($messages)}
                                        <input value="" class="message_readed_all" type="checkbox" />
                                    {/if}
                                </span>
                            </th>
                            <th class="subject_col">
                                <span class="title_box">
                                    {l s='Subject' mod='ets_contactform7'}
                                    <a href="{$url_full|escape:'html':'UTF-8'}&OrderBy=m.subject&OrderWay=DESC" {if $orderBy=='m.subject' && $orderWay=='DESC'}class="active"{/if}>
    									<i class="icon-caret-down"></i>
    								</a>
                                    <a href="{$url_full|escape:'html':'UTF-8'}&OrderBy=m.subject&OrderWay=ASC" {if $orderBy=='m.subject' && $orderWay=='ASC'}class="active"{/if}>
    									<i class="icon-caret-up"></i>
    								</a>
                                </span>
                            </th>
                            <th class="message_col">
                                <span class="title_box">
                                    {l s='Message' mod='ets_contactform7'}
                                </span>
                            </th>
                            <th class="form_col">
                                <span class="title_box">
                                    {l s='Contact form' mod='ets_contactform7'}
                                    <a href="{$url_full|escape:'html':'UTF-8'}&OrderBy=m.id_contact&OrderWay=DESC" {if $orderBy=='m.id_contact' && $orderWay=='DESC'}class="active"{/if}>
    									<i class="icon-caret-down"></i>
    								</a>
                                    <a href="{$url_full|escape:'html':'UTF-8'}&&OrderBy=m.id_contact&OrderWay=ASC" {if $orderBy=='m.id_contact' && $orderWay=='ASC'}class="active"{/if}>
    									<i class="icon-caret-up"></i>
    								</a>
                                </span>
                            </th>
                            <th class="reply_col">
                                <span class="title_box">
                                    {l s='Replied' mod='ets_contactform7'}
                                    <a href="{$url_full|escape:'html':'UTF-8'}&OrderBy=replied&OrderWay=DESC" {if $orderBy=='replied' && $orderWay=='DESC'}class="active"{/if}>
    									<i class="icon-caret-down"></i>
    								</a>
                                    <a href="{$url_full|escape:'html':'UTF-8'}&&OrderBy=replied&OrderWay=ASC" {if $orderBy=='replied' && $orderWay=='ASC'}class="active"{/if}>
    									<i class="icon-caret-up"></i>
    								</a>
                                </span>
                            </th>
                            <th class="text-center">
                                <span class="title_box">
                                    {l s='Date' mod='ets_contactform7'}
                                    <a href="{$url_full|escape:'html':'UTF-8'}&OrderBy=m.id_contact_message&OrderWay=DESC" {if $orderBy=='m.id_contact_message' && $orderWay=='DESC'}class="active"{/if}>
    									<i class="icon-caret-down"></i>
    								</a>
                                    <a href="{$url_full|escape:'html':'UTF-8'}&&OrderBy=m.id_contact_message&OrderWay=ASC" {if $orderBy=='m.id_contact_message' && $orderWay=='ASC'}class="active"{/if}>
    									<i class="icon-caret-up"></i>
    								</a>
                                </span>
                                
                            </th>
                            <th class="text-center" style="width: 170px;">
                                <span class="title_box">
                                    {l s='Action' mod='ets_contactform7'}
                                </span>
                            </th>
                        </tr>
                        <tr class="nodrag nodrop filter row_hover">
                            <th>
                                
                            </th>
                            <th class="subject_col">
                                <input class="form-control" name="subject" value="{if isset($values_submit.subject)}{$values_submit.subject|escape:'html':'UTF-8'}{/if}" />
                            </th>
                            <th class="messsage_col">
                                <input class="form-control" name="messageFilter_message" value="{if isset($values_submit.messageFilter_message)}{$values_submit.messageFilter_message|escape:'html':'UTF-8'}{/if}" />
                            </th>
                            <th class="form_col">
                                <select class="form-control" name="id_contact" id="id_contact"> <option value="0">---</option>{foreach from=$contacts item='contact'}<option value="{$contact.id_contact|intval}" {if isset($values_submit.id_contact)&&$values_submit.id_contact==$contact.id_contact}selected="selected"{/if}>{$contact.title|escape:'html':'UTF-8'|truncate:100:'...'}</option>{/foreach} </select>
                            </th>
                            <th class="reply_col text-center">
                                <select id="messageFilter_replied" name="messageFilter_replied">
                                    <option value="">---</option>
                                    <option value="0"{if isset($values_submit.messageFilter_replied) && $values_submit.messageFilter_replied==0} selected="selected"{/if} >{l s='No' mod='ets_contactform7'}</option>
                                    <option value="1"{if isset($values_submit.messageFilter_replied) && $values_submit.messageFilter_replied==1} selected="selected"{/if}>{l s='Yes' mod='ets_contactform7'}</option>
                                </select>
                            </th>
                            <th class="date_col">
                                <div class="date_range row">
									<div class="input-group fixed-width-md center">
										<input type="text" value="{if isset($values_submit.messageFilter_dateadd_from)}{$values_submit.messageFilter_dateadd_from|escape:'html':'UTF-8'}{/if}" placeholder="{l s='From' mod='ets_contactform7'}" name="messageFilter_dateadd_from" id="messageFilter_dateadd_from" class="filter datepicker date-input form-control" />
										<span class="input-group-addon">
											<i class="icon-calendar"></i>
										</span>
									</div>
									<div class="input-group fixed-width-md center">
										<input type="text" value="{if isset($values_submit.messageFilter_dateadd_to)}{$values_submit.messageFilter_dateadd_to|escape:'html':'UTF-8'}{/if}" placeholder="{l s='To' mod='ets_contactform7'}" name="messageFilter_dateadd_to" id="messageFilter_dateadd_to" class="filter datepicker date-input form-control" />
										<span class="input-group-addon">
											<i class="icon-calendar"></i>
										</span>
									</div>
								</div>
                            </th>
                            <th class="action_col text-center">
                                <span class="pull-right">
                                    <button id="submitFilterButtonMessage" class="btn btn-default" name="submitFilterButtonMessage" type="submit">
                                    <i class="icon-search"></i>
                                        {l s='Search' mod='ets_contactform7'}
                                    </button>
                                    <button id="submitExportButtonMessage" name="submitExportButtonMessage" class="btn btn-default" type="submit">
                                        <i class="icon-download"></i>
                                        {l s='Export' mod='ets_contactform7'}
                                    </button>
                                    {if isset($filter)&& $filter}
                                        <a class="btn btn-warning" href="{$link->getAdminLink('AdminContactFormMessage',true)|escape:'html':'UTF-8'}">
                                            <i class="icon-eraser"></i>
                                            {l s='Reset' mod='ets_contactform7'}
                                        </a>
                                    {/if}
                                </span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        {if $messages}
                            {foreach from=$messages item='message'}
                                <tr id="tr-message-{$message.id_contact_message|intval}" class="{if !$message.readed}no-reaed{/if}">
                                    {$message.row_message nofilter}
                                </tr>
                            {/foreach}
                        {else}
                            <tr>
                                <td colspan="7">
                                    <p class="alert alert-warning">{l s='No messages available' mod='ets_contactform7'}</p>
                                </td>
                            </tr>
                            
                        {/if}
                    </tbody>
                </table>
                <div class="ets_cfu_actions_footer">
                    <select id="bulk_action_message" name="bulk_action_message" style="display:none">
                        <option value="">{l s='Bulk actions' mod='ets_contactform7'}</option>
                        <option value="mark_as_read">{l s='Mark as read' mod='ets_contactform7'}</option>
                        <option value="mark_as_unread">{l s='Mark as  unread' mod='ets_contactform7'}</option>
                        <option value="delete_selected">{l s='Delete selected' mod='ets_contactform7'}</option>
                    </select>
                    {if $messages}
                        {$pagination_text nofilter}
                    {/if}
                </div>
            </div>
        </div>
    </form>
    <script type="text/javascript">
        $(document).ready(function(){
            if ($("table .datepicker").length > 0) {
				$("table .datepicker").datepicker({
            prevText: '',
            nextText: '',
            dateFormat: 'yy-mm-dd',
            changeMonth:true,
            changeYear:true,
				});
			}
        });
    </script>
</div>
<div class="ctf-popup-wapper-admin">
     <div class="fuc"></div>
        <div class="ctf-popup-tablecell">
            <div class="ctf-popup-content">
                <div class="ctf_close_popup">{l s='close' mod='ets_contactform7'}</div>
                <div id="form-message-preview">
                
                </div>
            </div>
        </div>
</div>