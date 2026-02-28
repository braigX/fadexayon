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

{if Ets_ordermanager::checkViewModule()}
    {capture name='tr_count'}{counter name='tr_count'}{/capture}
    <!--begin_tbody_list_order!--><tbody class="tbody_list_order{if Configuration::get('ETS_ODM_ENABLE_INSTANT_FILTER')} search{/if}">
    {if count($list)}
    {foreach $list AS $index => $tr}
    	<tr{if $position_identifier} id="tr_{$position_group_identifier|escape:'html':'UTF-8'}_{$tr.$identifier|escape:'html':'UTF-8'}_{if isset($tr.position['position'])}{$tr.position['position']|escape:'html':'UTF-8'}{else}0{/if}"{/if} class="{if isset($tr.class)}{$tr.class|escape:'html':'UTF-8'}{/if} {if $tr@iteration is odd by 1}odd{/if}"{if isset($tr.color) && $color_on_bg} style="background-color: {$tr.color|escape:'html':'UTF-8'}"{/if} >
    		{if $bulk_actions && $has_bulk_actions}
    			<td class="row-selector text-center">
    				{if isset($list_skip_actions.delete)}
    					{if !in_array($tr.$identifier, $list_skip_actions.delete)}
    						<input type="checkbox" name="{$list_id|escape:'html':'UTF-8'}Box[]" value="{$tr.$identifier|escape:'html':'UTF-8'}"{if isset($checked_boxes) && is_array($checked_boxes) && in_array($tr.$identifier, $checked_boxes)} checked="checked"{/if} class="noborder" />
    					{/if}
    				{else}
    					<input type="checkbox" name="{$list_id|escape:'html':'UTF-8'}Box[]" value="{$tr.$identifier|escape:'html':'UTF-8'}"{if isset($checked_boxes) && is_array($checked_boxes) && in_array($tr.$identifier, $checked_boxes)} checked="checked"{/if} class="noborder" />
    				{/if}
    			</td>
    		{/if}
    		{foreach $fields_display AS $key => $params}
    			{block name="open_td"}
    				<td
    					{if isset($params.position)}
    						id="td_{if !empty($position_group_identifier)}{$position_group_identifier|escape:'html':'UTF-8'}{else}0{/if}_{$tr.$identifier|escape:'html':'UTF-8'}{if $smarty.capture.tr_count > 1}_{($smarty.capture.tr_count - 1)|intval}{/if}"
    					{/if}
    					class="{strip}{if !$no_link}pointer{/if}
    					{if isset($params.position) && $order_by == 'position'  && $order_way != 'DESC'} dragHandle{/if}
    					{if isset($params.class)} {$params.class|escape:'html':'UTF-8'}{/if}
    					{if isset($params.align)} {$params.align|escape:'html':'UTF-8'}{/if}{/strip}
                        {if isset($params.type) && $params.type == 'price' && $key!='fee'}list_order_price{/if} {$key|escape:'html':'UTF-8'}
                        {if $key=='id_pdf'} list_order_pdf{/if}
                        "
    					{if (!isset($params.position) && !$no_link && !isset($params.remove_onclick))}
    						onclick="document.location = '{$current_index|addslashes|escape:'html':'UTF-8'}&amp;{$identifier|escape:'html':'UTF-8'}={$tr.$identifier|escape:'html':'UTF-8'}{if $view}&amp;view{else}&amp;update{/if}{$table|escape:'html':'UTF-8'}{if $page > 1}&amp;page={$page|intval}{/if}&amp;token={$token|escape:'html':'UTF-8'}'">
    					{else}
    					>
    				{/if}
    			{/block}
    			{block name="td_content"}
                    {if (isset($params.edit) && isset($tr.id))|| (isset($params.type) && $params.type == 'price')}
                        <span class="span_change_order">
                    {/if}
    				{if isset($params.prefix)}{$params.prefix|escape:'html':'UTF-8'}{/if}
    				{if isset($params.badge_success) && $params.badge_success && isset($tr.badge_success) && $tr.badge_success == $params.badge_success}<span class="badge badge-success">{/if}
    				{if isset($params.badge_warning) && $params.badge_warning && isset($tr.badge_warning) && $tr.badge_warning == $params.badge_warning}<span class="badge badge-warning">{/if}
    				{if isset($params.badge_danger) && $params.badge_danger && isset($tr.badge_danger) && $tr.badge_danger == $params.badge_danger}<span class="badge badge-danger">{/if}
    				{if isset($params.color) && isset($tr[$params.color])}
    					<span class="label color_field" style="background-color:{$tr[$params.color]|escape:'html':'UTF-8'};color:{if Tools::getBrightness($tr[$params.color]) < 128}white{else}#383838{/if}">
    				{/if}
    				{if isset($tr.$key)}
    					{if isset($params.active)}
    						{$tr.$key|escape:'html':'UTF-8'}
    					{elseif isset($params.callback)}
    						{if isset($params.maxlength) && Tools::strlen($tr.$key) > $params.maxlength}
    							<span title="{$tr.$key|escape:'html':'UTF-8'}">{$tr.$key|truncate:$params.maxlength:'...'|escape:'html':'UTF-8'}</span>
    						{else}
    							{$tr.$key nofilter}
    						{/if}
    					{elseif isset($params.activeVisu)}
    						{if $tr.$key}
    							<i class="icon-check-ok"></i> {l s='Enabled' mod='ets_ordermanager'}
    						{else}
    							<i class="icon-remove"></i> {l s='Disabled' mod='ets_ordermanager'}
    						{/if}
    					{elseif isset($params.position)}
    						{if !$filters_has_value && $order_by == 'position' && $order_way != 'DESC'}
    							<div class="dragGroup">
    								<div class="positions">
    									{($tr.$key.position + 1)|intval}
    								</div>
    							</div>
    						{else}
    							{($tr.$key.position + 1)|intval}
    						{/if}
    					{elseif isset($params.image)}
    						{$tr.$key|escape:'html':'UTF-8'}
    					{elseif isset($params.icon)}
    						{if is_array($tr[$key])}
    							{if isset($tr[$key]['class'])}
    								<i class="{$tr[$key]['class']|escape:'html':'UTF-8' }"></i>
    							{else}
    								<img src="../img/admin/{$tr[$key]['src']|escape:'html':'UTF-8'}" alt="{$tr[$key]['alt']|escape:'html':'UTF-8'}" title="{$tr[$key]['alt']|escape:'html':'UTF-8'}" />
    							{/if}
    						{/if}
    					{elseif isset($params.type) && $params.type == 'price'}
    						{if isset($tr.id_currency)}
    							{displayPrice price=$tr.$key currency=$tr.id_currency}
    						{else}
    							{displayPrice price=$tr.$key}
    						{/if}
    					{elseif isset($params.float)}
    						{$tr.$key|escape:'html':'UTF-8'}
    					{elseif isset($params.type) && $params.type == 'date'}
    						{dateFormat date=$tr.$key full=0}
    					{elseif isset($params.type) && $params.type == 'datetime'}
    						{$tr.$key|escape:'html':'UTF-8'}
    					{elseif isset($params.type) && $params.type == 'decimal'}
    						{$tr.$key|string_format:"%.2f"|escape:'html':'UTF-8'}
    					{elseif isset($params.type) && $params.type == 'percent'}
    						{$tr.$key|escape:'html':'UTF-8'} {l s='%' mod='ets_ordermanager'}
    					{elseif isset($params.type) && $params.type == 'bool'}
                {if $tr.$key == 1}
                  {l s='Yes' mod='ets_ordermanager'}
                {elseif $tr.$key == 0 && $tr.$key != ''}
                  {l s='No' mod='ets_ordermanager'}
                {/if}
    					{* If type is 'editable', an input is created *}
    					{elseif isset($params.type) && $params.type == 'editable' && isset($tr.id)}
    						<input type="text" name="{$key|escape:'html':'UTF-8'}_{$tr.id|escape:'html':'UTF-8'}" value="{$tr.$key|escape:'html':'UTF-8'}" class="{$key|escape:'html':'UTF-8'}" />
    					{elseif $key == 'color'}
    						{if !is_array($tr.$key)}
    						<div style="background-color: {$tr.$key|escape:'html':'UTF-8'};" class="attributes-color-container"></div>
    						{else} {*TEXTURE*}
    						<img src="{$tr.$key.texture|escape:'html':'UTF-8'}" alt="{$tr.name|escape:'html':'UTF-8'}" class="attributes-color-container" />
    						{/if}
    					{elseif isset($params.maxlength) && Tools::strlen($tr.$key) > $params.maxlength}
    						<span title="{$tr.$key|escape:'html':'UTF-8'}">{$tr.$key|truncate:$params.maxlength:'...'|escape:'html':'UTF-8'}</span>
    					{else}
    						{$tr.$key|escape:'html':'UTF-8'}
    					{/if}
    				{else}
    					{block name="default_field"}--{/block}
    				{/if}
    				{if isset($params.suffix)}{$params.suffix|escape:'html':'UTF-8'}{/if}
    				{if isset($params.color) && isset($tr.color)}
    					</span>
    				{/if}
    				{if isset($params.badge_danger) && $params.badge_danger && isset($tr.badge_danger) && $tr.badge_danger == $params.badge_danger}</span>{/if}
    				{if isset($params.badge_warning) && $params.badge_warning && isset($tr.badge_warning) && $tr.badge_warning == $params.badge_warning}</span>{/if}
    				{if isset($params.badge_success) && $params.badge_success && isset($tr.badge_success) && $tr.badge_success == $params.badge_success}</span>{/if}
                    {if isset($params.edit) &&  isset($tr.id)}
                        </span>
                        <div class="wapper-change-order">
                            {if $params.type=='price'}
                                {assign var='sign_price' value=Ets_ordermanager::getSignPrice($tr.id_currency)}
                            {/if}
                            {if $params.type_edit=='text'}
                                {if $params.type=='price'}
                                    <div class="input-group">
                                {/if}
                                        <input data-validate="{if isset($params.validate)}{$params.validate|escape:'html':'UTF-8'}{/if}" data-required="{if isset($params.required) && $params.required}1{else}0{/if}" type="text" value="{if $params.type=='price'}{str_replace($sign_price,'',$tr[$params.change])|escape:'html':'UTF-8'}{else}{$tr[$params.change]|escape:'html':'UTF-8'}{/if}" class="input_change_order_inline" data-order="{$tr.id|escape:'html':'UTF-8'}" data-id="{$tr[$params.table_id]|escape:'html':'UTF-8'}" data-key="{$params.table_key|escape:'html':'UTF-8'}" data-table="{$params.table_edit|escape:'html':'UTF-8'}" data-change="{$params.change|escape:'html':'UTF-8'}" placeholder="{$params.placeholder|escape:'html':'UTF-8'}" />
                                {if $params.type=='price'}
                                        <div class="input-group-addon">{$sign_price|escape:'html':'UTF-8'}</div>
                                    </div>
                                {/if}
                                {if isset($params.change2)}
                                    <input data-validate="{if isset($params.validate)}{$params.validate|escape:'html':'UTF-8'}{/if}" data-required="{if isset($params.required) && $params.required}1{else}0{/if}" type="text" value="{$tr[$params.change2]|escape:'html':'UTF-8'}" class="input_change_order_inline" data-order="{$tr.id|escape:'html':'UTF-8'}" data-id="{$tr[$params.table_id]|escape:'html':'UTF-8'}" data-key="{$params.table_key|escape:'html':'UTF-8'}" data-table="{$params.table_edit|escape:'html':'UTF-8'}" data-change="{$params.change2|escape:'html':'UTF-8'}" placeholder="{$params.placeholder2|escape:'html':'UTF-8'}" />
                                {/if}
                            {/if}
                            {if $params.type_edit=='textarea'}
                                <textarea data-validate="{if isset($params.validate)}{$params.validate|escape:'html':'UTF-8'}{/if}" data-required="{if isset($params.required) && $params.required}1{else}0{/if}" class="input_change_order_inline" data-order="{$tr.id|escape:'html':'UTF-8'}" data-id="{$tr[$params.table_id]|escape:'html':'UTF-8'}" data-key="{$params.table_key|escape:'html':'UTF-8'}" data-table="{$params.table_edit|escape:'html':'UTF-8'}" data-change="{$params.change|escape:'html':'UTF-8'}">{$tr[$params.change]|escape:'html':'UTF-8'}</textarea>
                            {/if}
                            {if $params.type_edit=='select'}
                                <select data-validate="{if isset($params.validate)}{$params.validate|escape:'html':'UTF-8'}{/if}" data-required="{if isset($params.required) && $params.required}1{else}0{/if}" class="input_change_order_inline" data-order="{$tr.id|escape:'html':'UTF-8'}" data-id="{$tr[$params.table_id]|escape:'html':'UTF-8'}" data-key="{$params.table_key|escape:'html':'UTF-8'}" data-table="{$params.table_edit|escape:'html':'UTF-8'}" data-change="{$params.change|escape:'html':'UTF-8'}">
                                    {if $params.change=='id_carrier'}
                                        {assign var='carrier_list' value= Ets_ordermanager::getListCarriersByIDOrder($tr.id)}
                                        {if $carrier_list}
                                            {foreach from=$carrier_list item='carrier'}
                                                <option value="{$carrier.id_carrier|intval}"{if $carrier.id_carrier== $tr[$params.change]} selected="selected"{/if} >{$carrier.name|escape:'html':'UTF-8'} {if isset($carrier.delay)}({$carrier.delay|escape:'html':'UTF-8'}){/if}</option>
                                            {/foreach}
                                        {/if}
                                    {else}
                                        {if $params.list_edits}
                                            {foreach from=$params.list_edits key='id_status' item='status'}
                                                <option value="{$id_status|intval}"{if $id_status== $tr[$params.change]} selected="selected"{/if} >{$status|escape:'html':'UTF-8'}</option>
                                            {/foreach}
                                        {/if}
                                    {/if}
                                </select>
                            {/if}
                            {if $params.type_edit=='date'}
    							<input data-validate="{if isset($params.validate)}{$params.validate|escape:'html':'UTF-8'}{/if}" data-required="{if isset($params.required) && $params.required}1{else}0{/if}" id="order_date_add_{$tr.id|intval}" value="{$tr[$params.change]|escape:'html':'UTF-8'}" class="datepicker2 input_change_order_inline" data-order="{$tr.id|escape:'html':'UTF-8'}" data-id="{$tr[$params.table_id]|escape:'html':'UTF-8'}" data-key="{$params.table_key|escape:'html':'UTF-8'}" data-table="{$params.table_edit|escape:'html':'UTF-8'}" data-change="{$params.change|escape:'html':'UTF-8'}" placeholder="{$params.placeholder|escape:'html':'UTF-8'}" type="text" />
                            {/if}
                        </div>
                    {else}
                        {if (isset($params.type) && $params.type == 'price')}
                            </span>
                        {/if}
                    {/if}
    			{/block}
    			{block name="close_td"}
    				</td>
    			{/block}
    		{/foreach}
    
    	{if isset($multishop_active) && $multishop_active && $shop_link_type}
    		<td title="{$tr.shop_name|escape:'html':'UTF-8'}">
    			{if isset($tr.shop_short_name)}
    				{$tr.shop_short_name|escape:'html':'UTF-8'}
    			{else}
    				{$tr.shop_name|escape:'html':'UTF-8'}
    			{/if}
    		</td>
    	{/if}
        <td>
            <a class="quick_view_order" href="{$link->getAdminLink('AdminOrders')|escape:'html':'UTF-8'}&vieworder&id_order={$tr.id|intval}" data-id="{$tr.id|intval}" title="{l s='Quick view' mod='ets_ordermanager'}" >{l s='Quick view' mod='ets_ordermanager'} <span class="wait_popup">{l s='waiting...' mod='ets_ordermanager'}</span></a>
        </td>
    	{if $has_actions}
    		<td class="text-right">
    			<div class="group-action-order">
                    {assign var='compiled_actions' value=array()}
        			{foreach $actions AS $key => $action}
        				{if isset($tr.$action)}
        					{if $key == 0}
        						{assign var='action' value=$action}
        					{/if}
        					{if $action == 'delete' && $actions|@count > 2}
        						{$compiled_actions[] = 'divider'}
        					{/if}
        					{$compiled_actions[] = $tr.$action}
        				{/if}
        			{/foreach}
        			{if $compiled_actions|count > 0}
        				{if $compiled_actions|count > 1}<div class="btn-group-action">{/if}
        				<div class="btn-group pull-right">
        					{$compiled_actions[0] nofilter}
        					{if $compiled_actions|count > 1}
        					<button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
        						<i class="icon-caret-down"></i>&nbsp;
        					</button>
        						<ul class="dropdown-menu">
        						{foreach $compiled_actions AS $key => $action}
        							{if $key != 0}
        							<li{if $action == 'divider' && $compiled_actions|count > 3} class="divider"{/if}>
        								{if $action != 'divider'}{$action nofilter}{/if}
        							</li>
        							{/if}
        						{/foreach}
        						</ul>
        					{/if}
        				</div>
        				{if $compiled_actions|count > 1}</div>{/if}
        			{/if}
                </div>
                <div class="group-action-update-order" style="display: none;">
                    <button class="btn btn-default" type="button" name="submitOrderChangeInLine">
                        <i class="icon-ok"></i>{l s='Update' mod='ets_ordermanager'}
                    </button>
                    <button class="btn btn-default cancel_order_change_link" type="button"><i class="icon-remove"></i>{l s='Cancel' mod='ets_ordermanager'}</button>
                </div>
    		</td>
    	{/if}
    	</tr>
    {/foreach}
    {else}
    	<tr>
    		<td class="list-empty" colspan="{(count($fields_display)+1)|intval}">
    			<div class="list-empty-msg">
    				<i class="icon-warning-sign list-empty-icon"></i>
    				{l s='No records found' mod='ets_ordermanager'}
    			</div>
    		</td>
    	</tr>
    {/if}
    </tbody><!--end_tbody_list_order!-->
    <script type="text/javascript">
        $(document).ready(function(){
            $('.fancybox').fancybox({
    			'type': 'iframe',
    			'width': '90%',
    			'height': '90%',
    		});
        });
    </script>
{else}
    {include file="helpers/list/list_content.tpl"}
{/if}