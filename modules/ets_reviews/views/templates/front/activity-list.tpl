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
{*{hook h='renderJavascript'}*}
{*{(Module::getInstanceByName('ets_reviews')->hookRenderJavascript()) nofilter}*}
{if $tabs}
	<ul class="ets_rv_tabs">
		{foreach from=$tabs key='id' item='tab'}
			<li class="ets_rv_tab {$tab.class|escape:'html':'UTF-8'}{if $current_tab == $id}{if !empty($ETS_RV_DESIGN_COLOR2)} background2{/if} active{/if}">
				<a href="{$tab.link nofilter}">{$tab.title|escape:'html':'UTF-8'}</a>
			</li>
		{/foreach}
	</ul>
{/if}
<div class="ets_rv_wrap_table">
	<div class="ets_rv_activity_list">
		{if isset($activityList) && $activityList}
			{foreach from=$activityList item='item'}
				<div class="ets_rv_activity_item">
					<div class="ets_rv_author">
						<span class="ets_rv_author_avatar{if isset($item.profile.color) && $item.profile.color|trim !== ''} gene-color{/if}"{if isset($item.profile.photo) && $item.profile.photo|trim !== ''} style="background-image:url({$item.profile.photo nofilter});"{/if}{if isset($item.profile.color) && $item.profile.color|trim !== ''} data-profile="{$item.profile.color|escape:'html':'UTF-8'}"{/if}>
							{if !empty($item.profile.caption)}
								<span class="ets_rv_author_caption">{$item.profile.caption|escape:'html':'UTF-8'}</span>
							{/if}
                            {if !empty($item.profile.link)}
	                            <a class="ets_rv_author_link" href="{$item.profile.link nofilter}" title="{$item.customer_name|escape:'html':'UTF-8'}" target="_blank"><i class="ets_svg_icon">
										<svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M491 1536l91-91-235-235-91 91v107h128v128h107zm523-928q0-22-22-22-10 0-17 7l-542 542q-7 7-7 17 0 22 22 22 10 0 17-7l542-542q7-7 7-17zm-54-192l416 416-832 832h-416v-416zm683 96q0 53-37 90l-166 166-416-416 166-165q36-38 90-38 53 0 91 38l235 234q37 39 37 91z"/></svg>
									</i></a>
                            {/if}
						</span>
					</div>
					<div class="ets_rv_content">
                        <div class="ets_rv_content_content">{$item.content nofilter}</div>
                        <span class="ets_rv_time">
                            <svg viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg">
                                <path d="M192 1664h288v-288h-288v288zm352 0h320v-288h-320v288zm-352-352h288v-320h-288v320zm352 0h320v-320h-320v320zm-352-384h288v-288h-288v288zm736 736h320v-288h-320v288zm-384-736h320v-288h-320v288zm768 736h288v-288h-288v288zm-384-352h320v-320h-320v320zm-352-864v-288q0-13-9.5-22.5t-22.5-9.5h-64q-13 0-22.5 9.5t-9.5 22.5v288q0 13 9.5 22.5t22.5 9.5h64q13 0 22.5-9.5t9.5-22.5zm736 864h288v-320h-288v320zm-384-384h320v-288h-320v288zm384 0h288v-288h-288v288zm32-480v-288q0-13-9.5-22.5t-22.5-9.5h-64q-13 0-22.5 9.5t-9.5 22.5v288q0 13 9.5 22.5t22.5 9.5h64q13 0 22.5-9.5t9.5-22.5zm384-64v1280q0 52-38 90t-90 38h-1408q-52 0-90-38t-38-90v-1280q0-52 38-90t90-38h128v-96q0-66 47-113t113-47h64q66 0 113 47t47 113v96h384v-96q0-66 47-113t113-47h64q66 0 113 47t47 113v96h128q52 0 90 38t38 90z"/>
                            </svg>
                            {l s='Time' mod='ets_reviews'}: <span title="{dateFormat date=$item.date_add full=1}">{$item.display_date_add|escape:'html':'UTF-8'}</span>
						</span>
                    </div>
				</div>
			{/foreach}
		{else}<p class="alert alert-info">{l s='No activity.' mod='ets_reviews'}</p>{/if}
	</div>
    {if !empty($show_footer_btn)}
		<div class="ets_rv_pagination_footer">
            {if !empty($list_per_pages)}
				<div class="pagination">
                    {l s='Display' mod='ets_reviews'}
					<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">{$current_per_page|intval}<i class="icon-caret-down"></i></button>
					<ul class="dropdown-menu">
                        {foreach from=$list_per_pages key='n' item='item_link'}
							<li>
								<a href="{if !empty($item_link)}{$item_link nofilter}{else}javascript:void(0);{/if}" class="pagination-items-page" data-items="{$n|intval}">{$n|intval}</a>
							</li>
                        {/foreach}
					</ul>
					/ {$total_records|intval} {l s='result(s)' mod='ets_reviews'}
				</div>
            {/if}
            {if !empty($paginates) && $paginates|count > 3}
				<ul class="pagination pull-right">
                    {foreach from=$paginates item='item'}
						<li class="{$item.class|escape:'html':'UTF-8'}">
							<a href="{if !empty($item.link)}{$item.link nofilter}{else}javascript:void(0);{/if}" class="pagination-link">
								{if !empty($item.icon)}<i class="ets_svg_icon svg_{$item.icon|escape:'html':'UTF-8'}">
									{if $item.icon == 'angle-left'}
										<svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1203 544q0 13-10 23l-393 393 393 393q10 10 10 23t-10 23l-50 50q-10 10-23 10t-23-10l-466-466q-10-10-10-23t10-23l466-466q10-10 23-10t23 10l50 50q10 10 10 23z"/></svg>
									{elseif $item.icon == 'angle-right'}
										<svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1171 960q0 13-10 23l-466 466q-10 10-23 10t-23-10l-50-50q-10-10-10-23t10-23l393-393-393-393q-10-10-10-23t10-23l50-50q10-10 23-10t23 10l466 466q10 10 10 23z"/></svg>
									{elseif $item.icon == 'angle-double-left'}
										<svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1011 1376q0 13-10 23l-50 50q-10 10-23 10t-23-10l-466-466q-10-10-10-23t10-23l466-466q10-10 23-10t23 10l50 50q10 10 10 23t-10 23l-393 393 393 393q10 10 10 23zm384 0q0 13-10 23l-50 50q-10 10-23 10t-23-10l-466-466q-10-10-10-23t10-23l466-466q10-10 23-10t23 10l50 50q10 10 10 23t-10 23l-393 393 393 393q10 10 10 23z"/></svg>
									{elseif $item.icon == 'angle-double-right'}
										<svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M979 960q0 13-10 23l-466 466q-10 10-23 10t-23-10l-50-50q-10-10-10-23t10-23l393-393-393-393q-10-10-10-23t10-23l50-50q10-10 23-10t23 10l466 466q10 10 10 23zm384 0q0 13-10 23l-466 466q-10 10-23 10t-23-10l-50-50q-10-10-10-23t10-23l393-393-393-393q-10-10-10-23t10-23l50-50q10-10 23-10t23 10l466 466q10 10 10 23z"/></svg>
									{/if}
									</i>{/if}
                                {if !empty($item.title)}{$item.title|escape:'html':'UTF-8'}{/if}
							</a>
						</li>
                    {/foreach}
				</ul>
            {/if}
		</div>
    {/if}
</div>