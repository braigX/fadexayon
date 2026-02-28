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
{if isset($ets_rv_product) && $ets_rv_product->id|intval}
	<div class="ets_rv_comments_wrap{if $qa} question{/if}">
		<a href="{$href nofilter}?current_tab={if isset($current_tab)}{$current_tab|escape:'html':'UTF-8'}{else}waiting_for_review{/if}" class="btn btn-secondary account-link ets_rv_comments_back">
			<i class="ets_svg_icon">
				<svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1203 544q0 13-10 23l-393 393 393 393q10 10 10 23t-10 23l-50 50q-10 10-23 10t-23-10l-466-466q-10-10-10-23t10-23l466-466q10-10 23-10t23 10l50 50q10 10 10 23z"/></svg>
			</i>
			<span>{l s='Back to list' mod='ets_reviews'}</span>
		</a>
        <div class="clearfix"></div>
		<h4 class="view_review_title">{if !$qa}{l s='View review' mod='ets_reviews'}{else}{l s='View question' mod='ets_reviews'}{/if} {if isset($id_product_comment) && $id_product_comment }#{$id_product_comment|intval}{/if} - {l s='Product' mod='ets_reviews'}: {if isset($product)}<a href="{$product->link nofilter}">{$product->name|escape:'html':'UTF-8'}</a>{/if}</h4>
        {$list nofilter}
	</div>
    {if $qa}{(Module::getInstanceByName('ets_reviews')->renderProductQuestionModal(['id_product'=>$ets_rv_product->id|intval])) nofilter}{else}{(Module::getInstanceByName('ets_reviews')->renderProductCommentModal(['id_product'=>$ets_rv_product->id|intval])) nofilter}{/if}
    {(Module::getInstanceByName('ets_reviews')->renderTemplateModal(['id_product'=>$ets_rv_product->id|intval, 'no_qa'=>!$qa])) nofilter}
{else}
	{if $tabs}
		<ul class="ets_rv_tabs">
			{foreach from=$tabs key='id' item='tab'}
				<li class="ets_rv_tab {$tab.class|escape:'html':'UTF-8'}{if $current_tab == $id}{if !empty($ETS_RV_DESIGN_COLOR2)} background2{/if} active{/if}">
					<a href="{$tab.link nofilter}">{$tab.title|escape:'html':'UTF-8'}</a>
				</li>
			{/foreach}
		</ul>
	{/if}
	<div class="ets_rv_wrap_table commentlist">
		<div class="table-responsive">
			<table class="table">
				{if $current_tab|trim == 'my_review' || $current_tab|trim == 'manager_review' || $current_tab|trim == 'manager_question' || $current_tab|trim == 'my_question' }
					{if isset($fields_list) && $fields_list|count > 0}
						<thead>
						<tr class="nodrag nodrop">
							{foreach from=$fields_list item='field'}
								<th{if !empty($field.class)} class="{$field.class|escape:'html':'UTF-8'}"{/if}>{$field.title|escape:'html':'UTF-8'}</th>
							{/foreach}
							<th class="text-right"></th>
						</tr>
						</thead>
					{/if}
					{if isset($productComments) && $productComments|count > 0 && isset($fields_list) && $fields_list|count > 0}
						{assign var="ik" value=0}
						{foreach from=$productComments item='item'}
							{assign var="ik" value=$ik++}
							<tr class="{if $ik % 2 !== 0}odd{/if}">
								{foreach from=$fields_list key='id' item='field'}
									{if is_array($item) && $id}
										<td{if !empty($field.class)} class="{$field.class|escape:'html':'UTF-8'}"{/if}>
											{if $id == 'product_name'}
												{if isset($item.id_product) && $item.id_product}
													<a href="{$link->getProductLink($item.id_product, $item.link_rewrite)|escape:'quotes':'UTF-8'}">
														{$item.$id|escape:'html':'UTF-8'}
													</a>
												{else}--{/if}
											{elseif $id == 'id_image'}
												{if isset($item.image_link) && $item.image_link}
													<img src="{$item.image_link nofilter}" max-width="80" height="auto" />
												{else}--{/if}
											{else}
												{if ($id == 'grade' && $item.$id|floatval <= 0) || (!$item.$id && $id !== 'validate')}
													--
												{elseif $id == 'validate'}
													<span class="{if $item.badge_warning == '1'}warning{/if}">{$field.list[$item.validate]|escape:'html':'UTF-8'}</span>
												{elseif $id == 'grade'}
													{$item.$id|floatval|cat:'/5'|escape:'quotes':'UTF-8'}
												{else}
													{$item.$id|escape:'html':'UTF-8'}
												{/if}
												{if !empty($field.suffix)}{$field.suffix|escape:'html':'UTF-8'}{/if}
											{/if}
										</td>
									{/if}
								{/foreach}
								<td class="text-right">
									<div class="btn-group-action">
										<div class="btn-group pull-right open">
											<a href="{$item.link nofilter}{if isset($current_tab)}?back={$current_tab|trim|escape:'html':'UTF-8'}{/if}" class="btn btn-default ets_review_action_view" title="View">
												<i class="ets_svg_icon">
													<svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-inline--fa fa-search-plus">
														<path d="M312 196v24c0 6.6-5.4 12-12 12h-68v68c0 6.6-5.4 12-12 12h-24c-6.6 0-12-5.4-12-12v-68h-68c-6.6 0-12-5.4-12-12v-24c0-6.6 5.4-12 12-12h68v-68c0-6.6 5.4-12 12-12h24c6.6 0 12 5.4 12 12v68h68c6.6 0 12 5.4 12 12zm196.5 289.9l-22.6 22.6c-4.7 4.7-12.3 4.7-17 0L347.5 387.1c-2.3-2.3-3.5-5.3-3.5-8.5v-13.2c-36.5 31.5-84 50.6-136 50.6C93.1 416 0 322.9 0 208S93.1 0 208 0s208 93.1 208 208c0 52-19.1 99.5-50.6 136h13.2c3.2 0 6.2 1.3 8.5 3.5l121.4 121.4c4.7 4.7 4.7 12.3 0 17zM368 208c0-88.4-71.6-160-160-160S48 119.6 48 208s71.6 160 160 160 160-71.6 160-160z" class="">
														</path>
													</svg>
												</i>
											</a>
										</div>
									</div>
								</td>
							</tr>
						{/foreach}
					{else}
						<tfoot>
						<tr class="nodrag nodrop">
							<td colspan="100%">
								<p class="ets_rv_empty list-empty-msg">
									<i class="list-empty-icon">
										<svg width="84" height="84" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1024 1375v-190q0-14-9.5-23.5t-22.5-9.5h-192q-13 0-22.5 9.5t-9.5 23.5v190q0 14 9.5 23.5t22.5 9.5h192q13 0 22.5-9.5t9.5-23.5zm-2-374l18-459q0-12-10-19-13-11-24-11h-220q-11 0-24 11-10 7-10 21l17 457q0 10 10 16.5t24 6.5h185q14 0 23.5-6.5t10.5-16.5zm-14-934l768 1408q35 63-2 126-17 29-46.5 46t-63.5 17h-1536q-34 0-63.5-17t-46.5-46q-37-63-2-126l768-1408q17-31 47-49t65-18 65 18 47 49z"/></svg>
									</i> {l s='No records found' mod='ets_reviews'}
								</p>
							</td>
						</tr>
						</tfoot>
					{/if}
				{else}
					<thead>
					<tr class="ets">
						<th>{l s='Order ID' mod='ets_reviews'}</th>
						<th>{l s='Image' mod='ets_reviews'}</th>
						<th>{l s='Product' mod='ets_reviews'}</th>
						<th>{l s='Order status' mod='ets_reviews'}</th>
						<th>{l s='Purchased date' mod='ets_reviews'}</th>
						<th>{l s='Action' mod='ets_reviews'}</th>
					</tr>
					</thead>
					{if isset($orders) && $orders|count > 0}
						<tbody>
						{foreach from=$orders item='order'}
							<tr>
								<td style="text-align: center;vertical-align: middle;">{$order.id_order|intval}</td>
								<td>
									<img src="{$order.image_link nofilter}" width="80" title="{$order.product_name|escape:'html':'UTF-8'}">
								</td>
								<td>
									<a href="{$order.product_link nofilter}">
										{$order.product_name|escape:'html':'UTF-8'}
									</a>
								</td>
								<td>
									<span class="ets_rv_order_state_name" style="background-color:{$order.color nofilter};color:#ffffff;">{$order.order_state_name nofilter}</span>
								</td>
								<td>{dateFormat date=$order.date_add}</td>
								<td>
									{if $order.purchased}
										<a data-id-product="{$order.id_product|intval}" data-id-order="{$order.id_order|intval}"
										   class="ets_rv_product_id link-comment ets-rv-post-product-comment ets-rv-btn-comment ets-rv-ets-rv-btn-comment-big{if !empty($ETS_RV_DESIGN_COLOR2)} background2{/if}{if !empty($ETS_RV_DESIGN_COLOR3)} bg_hover3 bd_hover3{/if}"
										   href="#ets-rv-product-comments-list-header"
										>
											<i class="svg_fill_white lh_18">
												<svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M491 1536l91-91-235-235-91 91v107h128v128h107zm523-928q0-22-22-22-10 0-17 7l-542 542q-7 7-7 17 0 22 22 22 10 0 17-7l542-542q7-7 7-17zm-54-192l416 416-832 832h-416v-416zm683 96q0 53-37 90l-166 166-416-416 166-165q36-38 90-38 53 0 91 38l235 234q37 39 37 91z"/></svg>
											</i>
											{l s='Write your review' mod='ets_reviews'}
										</a>
									{else}
										{assign var="order_state_list" value=""}
										{if isset($order_states) && $order_states}
											{assign var='ik' value=0}
											{foreach from=$order_states item='state'}
												{assign var='ik' value=$ik+1}
												{assign var="order_state_list" value=$order_state_list|cat:'"'|cat:$state.name|cat:'"'}{if $ik < $order_states|count}{assign var="order_state_list" value=$order_state_list|cat:', '}{/if}
											{/foreach}
										{/if}
										<span class="btn btn-default"
											  style="color: white; background-color: #e69c13;"
											  title="{l s='Your current order status is "%s", you can write your review when the order status changes to %s' sprintf=[$order.order_state_name, $order_state_list] mod='ets_reviews'}">
												{l s='Pending' mod='ets_reviews'}
											</span>
									{/if}
								</td>
							</tr>
						{/foreach}
						</tbody>
					{else}
						<tfoot>
						<tr class="nodrag nodrop">
							<td colspan="100%">
								<p class="ets_rv_empty list-empty-msg">
									<i class="list-empty-icon">
										<svg width="84" height="84" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1024 1375v-190q0-14-9.5-23.5t-22.5-9.5h-192q-13 0-22.5 9.5t-9.5 23.5v190q0 14 9.5 23.5t22.5 9.5h192q13 0 22.5-9.5t9.5-23.5zm-2-374l18-459q0-12-10-19-13-11-24-11h-220q-11 0-24 11-10 7-10 21l17 457q0 10 10 16.5t24 6.5h185q14 0 23.5-6.5t10.5-16.5zm-14-934l768 1408q35 63-2 126-17 29-46.5 46t-63.5 17h-1536q-34 0-63.5-17t-46.5-46q-37-63-2-126l768-1408q17-31 47-49t65-18 65 18 47 49z"/></svg>
									</i> {l s='No records found' mod='ets_reviews'}
								</p>
							</td>
						</tr>
						</tfoot>
					{/if}
				{/if}
			</table>
		</div>
		{if !empty($show_footer_btn)}
			<div class="ets_rv_pagination_footer">
				{if !empty($per_pages)}
					<div class="pagination">
						{l s='Display' mod='ets_reviews'}
						<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">{$current_per_page|intval}<i class="icon-caret-down"></i></button>
						<ul class="dropdown-menu">
							{foreach from=$per_pages key='n' item='item_link'}
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
	{if $current_tab|trim == 'waiting_for_review'}
		{*{hook h='renderJavascript'}*}
		{*{(Module::getInstanceByName('ets_reviews')->hookRenderJavascript()) nofilter}*}
		<div class="ets_rv_form ets_rv_modal"></div>
	{/if}
{/if}