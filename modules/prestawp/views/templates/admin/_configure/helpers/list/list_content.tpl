{**
* NOTICE OF LICENSE
*
* This file is licenced under the Software License Agreement.
* With the purchase or the installation of the software in your application
* you accept the licence agreement.
*
* @author    Presta.Site
* @copyright 2019 Presta.Site
* @license   LICENSE.txt
*}

{capture name='tr_count'}{counter name='tr_count'}{/capture}
<tbody class="pswp-blocks-wrp">
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
					{if isset($params.align)} {$params.align|escape:'html':'UTF-8'}{/if}{/strip}"
                    {if $pbc_psv >= 1.6 && (!isset($params.position) && !$no_link && !isset($params.remove_onclick))}
						onclick="document.location = '{$current_index|escape:'html':'UTF-8'}&amp;{$identifier|escape:'html':'UTF-8'}={$tr.$identifier|escape:'html':'UTF-8'}{if $view}&amp;view{else}&amp;update{/if}{$table|escape:'html':'UTF-8'}{if $page > 1}&amp;page={$page|intval}{/if}&amp;token={$token|escape:'html':'UTF-8'}'">
                    {else}
						>
                    {/if}
                {/block}
                {block name="td_content"}
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
								<span title="{$tr.$key|escape:'html':'UTF-8'}">{$tr.$key|truncate:$params.maxlength:'...'}</span>
                            {else}
                                {$tr.$key|escape:'html':'UTF-8'}
                            {/if}
                        {elseif isset($params.activeVisu)}
                            {if $tr.$key}
							<i class="icon-check-ok"></i> {l s='Enabled' mod='prestawp'}
                            {else}
                                <i class="icon-remove"></i> {l s='Disabled' mod='prestawp'}
                        {/if}
                        {elseif isset($params.position)}
                            {if !$filters_has_value && $order_by == 'position' && $order_way != 'DESC'}
							<div class="dragGroup">
                                    <div class="positions">
                                        {$tr.$key.position + 1|intval}
                                    </div>
                                </div>
                        {else}
                            {$tr.$key.position + 1|intval}
                        {/if}
                        {elseif isset($params.image)}
                            {$tr.$key|escape:'html':'UTF-8'}
                        {elseif isset($params.icon)}
                            {if is_array($tr[$key])}
                            {if isset($tr[$key]['class'])}
								<i class="{$tr[$key]['class']|escape:'html':'UTF-8'}"></i>
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
                        {elseif isset($params.type) && $params.type == 'status'}
                            {strip}
							<span class="pswp_active_toggle {if $pbc_psv == 1.5}ps15{/if}" data-id-block="{$tr.id_prestawp_block|intval}">
                                    {if $tr.$key == 1}
										<span class="label label-success">{l s='Active' mod='prestawp'}</span>
                                    {elseif $tr.$key == 0}
                                        <span class="label label-danger">{l s='Disabled' mod='prestawp'}</span>
                                    {elseif $tr.$key == -1}
                                        <span class="label label-default">{l s='Inactive' mod='prestawp'}</span>
                                    {/if}
                                </span>
                        {/strip}
                        {elseif isset($params.type) && $params.type == 'date'}
                            {dateFormat date=$tr.$key full=0}
                        {elseif isset($params.type) && $params.type == 'datetime'}
                            {dateFormat date=$tr.$key full=1}
                        {elseif isset($params.type) && $params.type == 'decimal'}
                            {$tr.$key|string_format:"%.2f"|escape:'html':'UTF-8'}
                        {elseif isset($params.type) && $params.type == 'percent'}
                            {$tr.$key|escape:'html':'UTF-8'} {l s='%' mod='prestawp'}
                        {* If type is 'editable', an input is created *}
                        {elseif isset($params.type) && $params.type == 'editable' && isset($tr.id)}
                            <input type="text" name="{$key|escape:'html':'UTF-8'}_{$tr.id|escape:'html':'UTF-8'}" value="{$tr.$key|escape:'html':'UTF-8'}" class="{$key|escape:'html':'UTF-8'}" />
                        {elseif $key == 'color'}
                            {if !is_array($tr.$key)}
							<div style="background-color: {$tr.$key|escape:'html':'UTF-8'};" class="attributes-color-container"></div>
                            {else} {*TEXTURE*}
                            <img src="{$tr.$key.texture|escape:'quotes':'UTF-8'}" alt="{$tr.name|escape:'html':'UTF-8'}" class="attributes-color-container" />
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
                {/block}
                {block name="close_td"}
					</td>
                {/block}
            {/foreach}

            {if $shop_link_type}
				<td title="{$tr.shop_name|escape:'html':'UTF-8'}">
                    {if isset($tr.shop_short_name)}
                        {$tr.shop_short_name|escape:'html':'UTF-8'}
                    {else}
                        {$tr.shop_name|escape:'html':'UTF-8'}
                    {/if}
				</td>
            {/if}
            {if $has_actions}
				<td class="text-right">
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
                        {elseif $action == 'save'}
							<div class="pswp-edit-wrp">
								<button class="btn btn-primary btn-pswp-save" data-id-block="{$tr.id_prestawp_block|intval}"><i class="icon-refresh"></i> {l s='Apply' mod='prestawp'}</button>
								<button class="btn btn-default btn-pswp-close-edit">&times;</button>
							</div>
                        {/if}
                    {/foreach}
                    {if $compiled_actions|count > 0}
                        {if $compiled_actions|count > 1}<div class="btn-group-action">{/if}
						<div class="btn-group pull-right">
                            {$compiled_actions[0] nofilter} {*HTML*}
                            {if $compiled_actions|count > 1}
								<button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
									<i class="icon-caret-down"></i>&nbsp;
								</button>
								<ul class="dropdown-menu">
                                    {foreach $compiled_actions AS $key => $action}
                                        {if $key != 0}
											<li{if $action == 'divider' && $compiled_actions|count > 3} class="divider"{/if}>
                                                {if $action != 'divider'}{$action nofilter}{*HTML*}{/if}
											</li>
                                        {/if}
                                    {/foreach}
								</ul>
                            {/if}
						</div>
                        {if $compiled_actions|count > 1}</div>{/if}
                    {/if}
				</td>
            {/if}
		</tr>
		<tr class="pswp_edit_row" data-id-block="{if isset($tr.id_prestawp_block)}{$tr.id_prestawp_block|intval}{/if}">
			<td colspan="7">
				<div class="pswp_edit_row_content"></div>
			</td>
		</tr>
    {/foreach}
{else}
	<tr>
		<td class="list-empty" colspan="{count($fields_display)+1|intval}">
			<div class="list-empty-msg">
				<i class="icon-warning-sign list-empty-icon"></i>
                {l s='No records found' mod='prestawp'}
			</div>
		</td>
	</tr>
{/if}
</tbody>
