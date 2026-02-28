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
    </table>
    </div>
    <div class="row">
    	<div class="col-lg-6">
    		{if $bulk_actions && $has_bulk_actions}
    		<div class="btn-group bulk-actions dropup">
    			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" {if $table}id="bulk_action_menu_{$table|escape:'html':'UTF-8'}"{/if}>
    				{l s='Bulk actions' mod='ets_ordermanager'} <span class="caret"></span>
    			</button>
    			<ul class="dropdown-menu">
    				<li>
    					<a href="#" onclick="javascript:checkDelBoxes($(this).closest('form').get(0), '{$list_id|escape:'html':'UTF-8'}Box[]', true);return false;">
    						<i class="icon-check-sign"></i>&nbsp;{l s='Select all' mod='ets_ordermanager'}
    					</a>
    				</li>
    				<li>
    					<a href="#" onclick="javascript:checkDelBoxes($(this).closest('form').get(0), '{$list_id|escape:'html':'UTF-8'}Box[]', false);return false;">
    						<i class="icon-check-empty"></i>&nbsp;{l s='Unselect all' mod='ets_ordermanager'}
    					</a>
    				</li>
    				<li class="divider"></li>
    				{foreach $bulk_actions as $key => $params}
    					<li{if $params.text == 'divider'} class="divider"{/if}>
    						{if $params.text != 'divider'}
    						<a href="#" onclick="{if isset($params.confirm)}if (confirm('{$params.confirm|escape:'html':'UTF-8'}')){/if}sendBulkAction($(this).closest('form').get(0), 'submitBulk{$key|escape:'html':'UTF-8'}{$table|escape:'html':'UTF-8'}');">
    							{if isset($params.icon)}<i class="{$params.icon|escape:'html':'UTF-8'}"></i>{/if}&nbsp;{$params.text|escape:'html':'UTF-8'}
    						</a>
    						{/if}
    					</li>
    				{/foreach}
                    {if isset($ets_odm_can_delete_order) && $ets_odm_can_delete_order}
                        <li>
                            <button{if Configuration::get('ETS_ODE_BEHAVIOR_DELETE_ORDER')=='permanently' || isset($smarty.get.viewtrash)} onclick="return confirm('{l s='You are going to delete this order permanently and will not be able to restore it. Do you want to delete this order?' mod='ets_ordermanager' js=1}')"{else} onclick="return confirm('{l s='Orders will be removed from list and moved to Trash, do you want to remove it?' mod='ets_ordermanager' js=1}')" {/if} type="submit" class="bulk_action" name="delete_all_order"><i class="icon-trash"></i> {l s='Delete selected orders' mod='ets_ordermanager'}</button>
                        </li>
                    {/if}
                    <li>
                        <button type="submit" class="bulk_action" name="print_invoice_all_order"><i class="icon-print"></i> {l s='Print selected invoices' mod='ets_ordermanager'}</button>
                    </li>
                     <li>
                        <button type="submit" class="bulk_action" name="print_slips_all_order"><i class="icon-print"></i> {l s='Print selected delivery slips' mod='ets_ordermanager'}</button>
                    </li>
                    <li>
                        <button type="submit" class="bulk_action" name="expot_csv_all_order"><i class="process-icon-export"></i> {l s='Export selected orders to csv' mod='ets_ordermanager'}</button>
                    </li>
                    <li>
                        <button type="submit" class="bulk_action" name="print_delivery_label_all_order"><i class="icon-print"></i> {l s='Print shipping labels of selected orders' mod='ets_ordermanager'}</button>
                    </li>
                    {if isset($smarty.get.viewtrash)}
                        <li>
                            <button type="submit" class="bulk_action" name="restore_all_order"><i class="icon icon-window-restore"></i> {l s='Restore selected' mod='ets_ordermanager'}</button>
                        </li>
                        {if isset($ets_odm_can_delete_order) && $ets_odm_can_delete_order}
                        <li>
                            <button type="submit" class="bulk_action" name='empty_trash_order' onclick="return confirm('{l s='If you agree to empty Trash, all trashed orders will be permanently deleted and cannot be restored?' mod='ets_ordermanager' js=1}')"><i class="icons icon-empty-set"></i>{l s='Empty trash' mod='ets_ordermanager'}</button>
                        </li>
                        {/if}
                    {/if}
                   
    			</ul>
    		</div>
    		{/if}
    	</div>
        <!--begin_pagination_list_order!--><div class="pagination_list_order">
    	{if !$simple_header && $list_total > $pagination[0]}
    	<div class="col-lg-6">
    		{* Choose number of results per page *}
    		<div class="pagination">
    			{l s='Display' mod='ets_ordermanager'}
    			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
    				{$selected_pagination|intval}
    				<i class="icon-caret-down"></i>
    			</button>
    			<ul class="dropdown-menu">
    			{foreach $pagination AS $value}
    				<li>
    					<a href="javascript:void(0);" class="pagination-items-page" data-items="{$value|intval}" data-list-id="{$list_id|escape:'html':'UTF-8'}">{$value|escape:'html':'UTF-8'}</a>
    				</li>
    			{/foreach}
    			</ul>
    			/ {$list_total|escape:'html':'UTF-8'} {l s='result(s)' mod='ets_ordermanager'}
    			<input type="hidden" id="{$list_id|escape:'html':'UTF-8'}-pagination-items-page" name="{$list_id|escape:'html':'UTF-8'}_pagination" value="{$selected_pagination|intval}" />
    		</div>
    		<script type="text/javascript">
    			$('.pagination-items-page').on('click',function(e){
    				e.preventDefault();
    				$('#'+$(this).data("list-id")+'-pagination-items-page').val($(this).data("items")).closest("form").submit();
    			});
    		</script>
    		<ul class="pagination pull-right">
    			<li {if $page <= 1}class="disabled"{/if}>
    				<a href="javascript:void(0);" class="pagination-link" data-page="1" data-list-id="{$list_id|escape:'html':'UTF-8'}">
    					<i class="icon-double-angle-left"></i>
    				</a>
    			</li>
    			<li {if $page <= 1}class="disabled"{/if}>
    				<a href="javascript:void(0);" class="pagination-link" data-page="{($page - 1)|intval}" data-list-id="{$list_id|escape:'html':'UTF-8'}">
    					<i class="icon-angle-left"></i>
    				</a>
    			</li>
    			{assign p 0}
    			{while $p++ < $total_pages}
    				{if $p < $page-2}
    					<li class="disabled">
    						<a href="javascript:void(0);">&hellip;</a>
    					</li>
    					{assign p $page-3}
    				{elseif $p > $page+2}
    					<li class="disabled">
    						<a href="javascript:void(0);">&hellip;</a>
    					</li>
    					{assign p $total_pages}
    				{else}
    					<li {if $p == $page}class="active"{/if}>
    						<a href="javascript:void(0);" class="pagination-link" data-page="{$p|escape:'html':'UTF-8'}" data-list-id="{$list_id|escape:'html':'UTF-8'}">{$p|intval}</a>
    					</li>
    				{/if}
    			{/while}
    			<li {if $page >= $total_pages}class="disabled"{/if}>
    				<a href="javascript:void(0);" class="pagination-link" data-page="{($page + 1)|intval}" data-list-id="{$list_id|escape:'html':'UTF-8'}">
    					<i class="icon-angle-right"></i>
    				</a>
    			</li>
    			<li {if $page >= $total_pages}class="disabled"{/if}>
    				<a href="javascript:void(0);" class="pagination-link" data-page="{$total_pages|intval}" data-list-id="{$list_id|escape:'html':'UTF-8'}">
    					<i class="icon-double-angle-right"></i>
    				</a>
    			</li>
    		</ul>
    		<script type="text/javascript">
    			$('.pagination-link').on('click',function(e){
    				e.preventDefault();
    				if (!$(this).parent().hasClass('disabled'))
    					$('#submitFilter'+$(this).data("list-id")).val($(this).data("page")).closest("form").submit();
    			});
    		</script>
    	</div>
    	{/if}
        </div><!--end_pagination_list_order!-->
    </div>
    {block name="footer"}
    {foreach from=$toolbar_btn item=btn key=k}
    	{if $k == 'back'}
    		{assign 'back_button' $btn}
    		{break}
    	{/if}
    {/foreach}
    {if isset($back_button)}
    <div class="panel-footer">
    	<a id="desc-{$table|escape:'html':'UTF-8'}-{if isset($back_button.imgclass)}{$back_button.imgclass|escape:'html':'UTF-8'}{else}{$k|escape:'html':'UTF-8'}{/if}" class="btn btn-default{if isset($back_button.target) && $back_button.target} _blank{/if}"{if isset($back_button.href)} href="{$back_button.href|escape:'html':'UTF-8'}"{/if}{if isset($back_button.js) && $back_button.js} onclick="{$back_button.js|escape:'html':'UTF-8'}"{/if}>
    		<i class="process-icon-back {if isset($back_button.class)}{$back_button.class|escape:'html':'UTF-8'}{/if}" ></i> <span {if isset($back_button.force_desc) && $back_button.force_desc == true } class="locked" {/if}>{$back_button.desc|escape:'html':'UTF-8'}</span>
    	</a>
    </div>
    {/if}
    {/block}
    {if !$simple_header}
    		<input type="hidden" name="token" value="{$token|escape:'html':'UTF-8'}" />
    	</div>
    {else}
    	</div>
    {/if}
    
    {hook h='displayAdminListAfter'}
    {if isset($name_controller)}
    	{capture name=hookName assign=hookName}display{$name_controller|ucfirst|escape:'html':'UTF-8'}ListAfter{/capture}
    	{hook h=$hookName}
    {elseif isset($smarty.get.controller)}
    	{capture name=hookName assign=hookName}display{$smarty.get.controller|ucfirst|escape:'html':'UTF-8'}ListAfter{/capture}
    	{hook h=$hookName}
    {/if}
    
    {block name="endForm"}
    </form>
    {/block}
    
    {block name="after"}
        <div class="ets_order_popup">
            <div class="popup_content table">
            <div class="popup_content_tablecell">
                <div class="popup_content_wrap" style="position: relative">
                    <span class="close_popup">+</span>
                    <div id="block-form-popup-dublicate">
                        
                    </div>
                </div>
            </div>
            </div>
        </div>
    {/block}
{else}
    {include file="helpers/list/list_footer.tpl"}
{/if}
