{*
* 2007-2023 ETS-Soft
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
* versions in the future. If you wish to customize PrestaShop for your
* needs, please contact us for extra customization service at an affordable price
*
*  @author ETS-Soft <etssoft.jsc@gmail.com>
*  @copyright  2007-2023 ETS-Soft
*  @license    Valid for 1 website (or project) for each purchase of license
*  International Registered Trademark & Property of ETS-Soft
*}
<div class="panel ets-custom-payment-panel">
    <div class="panel-heading">{$title|escape:'html':'UTF-8'} <span class="badge badge-default">{$field_values|count|intval}</span>
        <span class="panel-heading-action">
            {if !isset($show_add_new) || isset($show_add_new) && $show_add_new}
                <a class="list-toolbar-btn" href="{$currentIndex|cat:'&add'|cat: $table|cat: '&token='|cat: $token|escape:'html':'UTF-8'}">
                    <span data-placement="top" data-html="true" data-original-title="{l s='Add new rule' mod='ets_ordermanager'}" class="label-tooltip hidden_not_15" data-toggle="tooltip" title="">
        				<i class="process-icon-new"></i> {l s='Add new rule' mod='ets_ordermanager'}
                    </span>
                </a>
            {/if}
            {if isset($preview_link) && $preview_link}
                <a target="_blank" class="list-toolbar-btn" href="{$preview_link|escape:'html':'UTF-8'}">
                    <span data-placement="top" data-html="true" data-original-title="{l s='Preview ' mod='ets_ordermanager'} ({$title|escape:'html':'UTF-8'})" class="label-tooltip hidden_not_15" data-toggle="tooltip" title="">
        				<i style="margin-left: 5px;" class="icon-search"></i> {l s='Preview' mod='ets_ordermanager'}
                    </span>
                </a>
            {/if}
        </span>
    </div>
    {if $fields_list}
        <div class="table-responsive clearfix">
            <form method="post" action="{$currentIndex|escape:'html':'UTF-8'}&amp;list=true">
                <div class="table configuration">
                    <div class="table-row nodrag nodrop">
                        {foreach from=$fields_list item='field' key='index'}
                            <div class="table-cell">
                                <span class="title_box">
                                    {$field.title|escape:'html':'UTF-8'}
                                    {if isset($field.sort) && $field.sort}
                                        <a href="{$currentIndex|escape:'html':'UTF-8'}&amp;sort={$index|escape:'html':'UTF-8'}&amp;sort_type=asc&amp;list=true{$filter_params nofilter}"><i class="icon-caret-down"></i></a>
                                        <a href="{$currentIndex|escape:'html':'UTF-8'}&amp;sort={$index|escape:'html':'UTF-8'}&amp;sort_type=desc&amp;list=true{$filter_params nofilter}"><i class="icon-caret-up"></i></a>
                                    {/if}
                                </span>
                            </div>
                        {/foreach}
                        {if $show_action}
                            <div class="table-cell" style="text-align: center;">{l s='Action' mod='ets_ordermanager'}</div>
                        {/if}
                    </div>
                    <div id="rules-list" class="ui-sortable">
                        {foreach from=$field_values item='row'}
                            <div id="rule-{$row.id_ets_export_order_rule|intval}" class="table-row">
                                {foreach from=$fields_list item='field' key='key'}
                                    <div class="table-cell pointer">
                                        {if isset($field.rating_field) && $field.rating_field}
                                            {if isset($row.$key) && $row.$key > 0}
                                                {for $i=1 to (int)$row.$key}
                                                    <div class="star star_on"></div>
                                                {/for}
                                                {if (int)$row.$key < 5}
                                                    {for $i=(int)$row.$key+1 to 5}
                                                        <div class="star"></div>
                                                    {/for}
                                                {/if}
                                            {else}
                                                {l s='--' mod='ets_ordermanager'}
                                            {/if}
                                        {elseif $key == 'send_file_via_email'}
                                            {if $row.send_file_via_email}<ul class="list_table send_email">
                                                <li class="li_item"><span>{l s='Email:' mod='ets_ordermanager'} {$row.receivers_mail|escape:'html':'UTF-8'}</span></li>
                                                <li class="li_item"><span>{l s='Schedule:' mod='ets_ordermanager'}
                                                    {assign var="ik" value="0"}
                                                    {foreach from=$schedule item='item'}{if $item.id_option == $row.send_file_schedule}{assign var="ik" value="1"}{$item.name|escape:'html':'UTF-8'}{/if}{/foreach}
                                                    {if $ik == 0}{l s='--' mod='ets_ordermanager'}{/if}
                                                </span></li>
                                                <li class="li_item"><span>{l s='Schedule time:' mod='ets_ordermanager'}
                                                    {if $row.send_file_schedule == 'daily'}{$hours[$row.send_file_time]|escape:'html':'UTF-8'}{/if}
                                                    {if $row.send_file_schedule == 'weekly'}{$weeks[$row.send_file_time]|escape:'html':'UTF-8'}{/if}
                                                    {if $row.send_file_schedule == 'monthly'}{$months[$row.send_file_time]|escape:'html':'UTF-8'}{/if}
                                                </span></li>
                                            </ul>{else}{l s='--' mod='ets_ordermanager'}{/if}
                                        {elseif $key == 'export_to_server2'}
                                            {if $row.export_to_server2}<ul class="list_table server2">
                                                <li class="li_item"><span>{l s='Directory path:' mod='ets_ordermanager'} {$row.directory_path2|escape:'html':'UTF-8'}</span></li>
                                                <li class="li_item"><span>{l s='Schedule:' mod='ets_ordermanager'}
                                                    {assign var="ik" value="0"}
                                                    {foreach from=$schedule item='item'}{if $item.id_option == $row.server2_schedule}{assign var="ik" value="1"}{$item.name|escape:'html':'UTF-8'}{/if}{/foreach}
                                                    {if $ik == 0}{l s='--' mod='ets_ordermanager'}{/if}
                                                </span></li>
                                                <li class="li_item"><span>{l s='Schedule time:' mod='ets_ordermanager'}
                                                    {if $row.server2_schedule == 'daily'}{$hours[$row.server2_time]|escape:'html':'UTF-8'}{/if}
                                                    {if $row.server2_schedule == 'weekly'}{$weeks[$row.server2_time]|escape:'html':'UTF-8'}{/if}
                                                    {if $row.server2_schedule == 'monthly'}{foreach from=$months item='item'}{if $item.id_option == $row.server2_time}{$item.label|escape:'html':'UTF-8'}{/if}{/foreach}{/if}
                                                </span></li>
                                            </ul>{else}{l s='--' mod='ets_ordermanager'}{/if}
                                        {elseif $key == 'export_to_server1'}
                                            {if $row.export_to_server1}<ul class="list_table server1 chung">
                                            <li><span class="li_item">{l s='Directory path:' mod='ets_ordermanager'} {$row.directory_path1|escape:'html':'UTF-8'}</span></li>
                                                <li class="li_item"><span>{l s='Schedule:' mod='ets_ordermanager'}
                                                    {assign var="ik" value="0"}
                                                    {foreach from=$schedule item='item'}{if $item.id_option == $row.server1_schedule}{assign var="ik" value="1"}{$item.name|escape:'html':'UTF-8'}{/if}{/foreach}
                                                    {if $ik == 0}{l s='--' mod='ets_ordermanager'}{/if}
                                                </span></li>
                                                <li class="li_item"><span>{l s='Schedule time:' mod='ets_ordermanager'}
                                                    {if $row.server1_schedule == 'daily'}{$hours[$row.server1_time]|escape:'html':'UTF-8'}{/if}
                                                    {if $row.server1_schedule == 'weekly'}{$weeks[$row.server1_time]|escape:'html':'UTF-8'}{/if}
                                                    {if $row.server1_schedule == 'monthly'}{foreach from=$months item='item'}{if $item.id_option == $row.server1_time}{$item.label|escape:'html':'UTF-8'}{/if}{/foreach}{/if}
                                                </span></li>
                                            </ul>{else}{l s='--' mod='ets_ordermanager'}{/if}
                                        {elseif $field.type != 'active'}
                                            {if isset($row.$key) && !is_array($row.$key)}{if isset($field.strip_tag) && !$field.strip_tag}{$row.$key|escape:'html':'UTF-8'}{else}{$row.$key|strip_tags:'UTF-8'|truncate:120:'...'|escape:'html':'UTF-8'}{/if}{/if}
                                            {if isset($row.$key) && is_array($row.$key) && isset($row.$key.image_field) && $row.$key.image_field}
                                                <a class="ets_fancy" href="{$row.$key.img_url|escape:'html':'UTF-8'}"><img style="{if isset($row.$key.height) && $row.$key.height}max-height: {$row.$key.height|intval}px;{/if}{if isset($row.$key.width) && $row.$key.width}max-width: {$row.$key.width|intval}px;{/if}" src="{$row.$key.img_url|escape:'html':'UTF-8'}" /></a>
                                            {/if}
                                        {else}
                                            {if isset($row.$key) && $row.$key}
                                                <a href="{$currentIndex|escape:'html':'UTF-8'}&amp;{$identifier|escape:'html':'UTF-8'}={$row.$identifier|escape:'html':'UTF-8'}&amp;change_enabled=0&amp;field={$key|escape:'html':'UTF-8'}" class="list-action field-{$key|escape:'html':'UTF-8'} list-action-enable action-enabled list-item-{$row.$identifier|escape:'html':'UTF-8'}" data-id="{$row.$identifier|escape:'html':'UTF-8'}"><i class="icon-check"></i></a>
                                            {else}
                                                <a href="{$currentIndex|escape:'html':'UTF-8'}&amp;{$identifier|escape:'html':'UTF-8'}={$row.$identifier|escape:'html':'UTF-8'}&amp;change_enabled=1&amp;field={$key|escape:'html':'UTF-8'}" class="list-action field-{$key|escape:'html':'UTF-8'} list-action-enable action-disabled  list-item-{$row.$identifier|escape:'html':'UTF-8'}" data-id="{$row.$identifier|escape:'html':'UTF-8'}"><i class="icon-remove"></i></a>
                                            {/if}
                                        {/if}
                                    </div>
                                {/foreach}
                                {if $show_action}
                                    <div class=" table-cell text-right">
                                        <div class="btn-group-action">
                                            <div class="btn-group pull-right">
                                                <a class="edit btn btn-default" target="_blank" href="{$currentIndex|escape:'html':'UTF-8'}&amp;{$identifier|escape:'html':'UTF-8'}={$row.$identifier|escape:'html':'UTF-8'}&action=submitExportOrder"><i class="icon-exchange"></i> {l s='Export' mod='ets_ordermanager'}</a>
                                                <button data-toggle="dropdown" class="btn btn-default dropdown-toggle">
                            						<i class="icon-caret-down"></i>&nbsp;
                            					</button>
                                                <ul class="dropdown-menu">
                                                    {if isset($row.view_url) && $row.view_url}
                                                        <li><a target="_blank" href="{$row.view_url|escape:'html':'UTF-8'}"><i class="icon-search-plus"></i> {if isset($row.view_text) && $row.view_text} {$row.view_text|escape:'html':'UTF-8'}{else} {l s='Preview' mod='ets_ordermanager'}{/if}</a></li>
                                                        <li class="divider"></li>
                                                    {/if}
                                                    {if $row.send_file_via_email}
                                                        <li><a href="{$currentIndex|cat:'&submitSendMail&list=true&'|cat : $identifier|cat :'='|cat: $row.$identifier|escape:'html':'UTF-8'}"><i class="icon-envelope"></i> {l s='Send via email' mod='ets_ordermanager'}</a></li>
                                                    {/if}
                                                    {if $row.export_to_server2}
                                                        <li><a href="{$currentIndex|cat:'&submitSendFTP&list=true&'|cat : $identifier|cat :'='|cat: $row.$identifier|escape:'html':'UTF-8'}"><i class="icon-file-o"></i> {l s='Send via FTP' mod='ets_ordermanager'}</a></li>
                                                    {/if}
                                                    {if $row.export_to_server1}
                                                        <li><a href="{$currentIndex|cat:'&submitSaveFile&list=true&'|cat : $identifier|cat :'='|cat: $row.$identifier|escape:'html':'UTF-8'}"><i class="icon-save"></i> {l s='Save to web directory' mod='ets_ordermanager'}</a></li>
                                                    {/if}
                                                    <li><a  href="{$currentIndex|escape:'html':'UTF-8'}&amp;{$identifier|escape:'html':'UTF-8'}={$row.$identifier|escape:'html':'UTF-8'}"><i class="icon-pencil"></i> {l s='Edit' mod='ets_ordermanager'}</a></li>
                                                    <li><a onclick="return confirm('{l s='Do you want to delete this item?' mod='ets_ordermanager'}');" href="{$currentIndex|escape:'html':'UTF-8'}&amp;{$identifier|escape:'html':'UTF-8'}={$row.$identifier|escape:'html':'UTF-8'}&del=yes"><i class="icon-trash"></i> {l s='Delete' mod='ets_ordermanager'}</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                     </div>
                                {/if}
                            </div>
                        {/foreach}
                    </div>
                </div>
            </form>
        </div>
    {/if}
</div>