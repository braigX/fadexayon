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

<form id="ets_rv_user_comment_form_1" class="review_user_popup defaultForm form-horizontal" action="{$currentIndex|escape:'quotes':'UTF-8'}"
      method="post" enctype="multipart/form-data" novalidate="">
	<div class="panel">
		<div class="panel-heading">
			<i class="icon-eye-close"></i> {l s=' View author' mod='ets_reviews'}
			<span class="panel-heading-action">
        <a class="list-toolbar-btn" href="javascript:void(0);">
          <span title="" data-toggle="tooltip" class="label-tooltip ets_rv_cancel" data-original-title="{l s='Close' mod='ets_reviews'}" data-html="true" data-placement="top">
            <i class="process-icon-cancel"></i>
          </span>
        </a>
      </span>
		</div>
		<div class="form-wrapper">
			<div class="form-group ets_rv_customer">
				<label class="control-label col-lg-3 col-xs-3">{l s='Authors' mod='ets_reviews'}</label>
				<div class="col-lg-9 col-xs-9">
					<div  class="form-control-static">
                        {if isset($user->customer) && $user->customer}
							<a class="ets_rv_customer_link" href="{$user->link|escape:'quotes':'UTF-8'}">{$user->customer->firstname|cat:' '|cat: $user->customer->lastname|escape:'html':'UTF-8'} ({$user->customer->email|escape:'html':'UTF-8'})</a>
                        {elseif !empty($user->customer_name)}
							<span>{$user->customer_name|escape:'html':'UTF-8'}</span>
                        {/if}
					</div>
				</div>
			</div>
			<div class="form-group ets_rv_grade">
				<label class="control-label col-xs-3 col-lg-3">{l s='Rating' mod='ets_reviews'}</label>
				<div class="col-xs-9 col-lg-9">
					<div  class="form-control-static">
						<div class="ets_rv_grade_stars{if !empty($ETS_RV_DESIGN_COLOR1)} color1{/if}" data-rate-full="★★★★★" data-rate-empty="☆☆☆☆☆" data-grade="{$user->grade|floatval|round:1}">

                        </div>
					</div>
				</div>
			</div>
			<div class="form-group ets_rv_title">
				<label class="control-label col-xs-3 col-lg-3">{l s='Is blocked' mod='ets_reviews'}</label>
				<div class="col-lg-9 col-xs-9">
		          <span class="form-control-static">
		            <span class="is_block_yes" style="display: {if $user->is_block|intval}inline-block{else}none{/if};">{l s='Block' mod='ets_reviews'}</span>
		            <span class="is_block_no" style="display: {if $user->is_block|intval}none{else}inline-block{/if};">{l s='Active' mod='ets_reviews'}</span>
		          </span>
				</div>
			</div>
			<div class="form-group ets_rv_total_reviews">
				<label class="control-label col-xs-3 col-lg-3">{l s='Total review(s)' mod='ets_reviews'}</label>
				<div class="col-lg-9 col-xs-9">
					<div class="form-control-static">
						{if $user->total_reviews|intval > 0}
							<a href="{$link->getAdminLink('AdminEtsRVActivity')|escape:'quotes':'UTF-8'}&activity_type=rev&customer_id={$user->id_customer|intval}" title="{l s='Click to view all review activities by this author' mod='ets_reviews'}">
								<span class="ets_rv_total_reviews">{$user->total_reviews|intval}</span>
							</a>
							<a class="ets_rv_delete btn btn-default" href="{$currentIndex|escape:'quotes':'UTF-8'}&token={$token|escape:'html':'UTF-8'}&question=0&table=ets_rv_product_comment&id_customer={$user->id_customer|intval}"><i class="icon-trash"></i></a>
						{else}--{/if}
					</div>
				</div>
			</div>
			<div class="form-group ets_rv_total_review_comments">
				<label class="control-label col-xs-3 col-lg-3">{l s='Total review comment(s)' mod='ets_reviews'}</label>
				<div class="col-lg-9 col-xs-9">
					<div class="form-control-static">
                        {if $user->total_review_comments|intval > 0}
							<a href="{$link->getAdminLink('AdminEtsRVActivity')|escape:'quotes':'UTF-8'}&activity_type=com&customer_id={$user->id_customer|intval}" title="{l s='Click to view all review comment activities by this author' mod='ets_reviews'}">
								<span class="ets_rv_total_review_comments">{$user->total_review_comments|intval}</span>
							</a>
							<a class="ets_rv_delete btn btn-default" href="{$currentIndex|escape:'quotes':'UTF-8'}&token={$token|escape:'html':'UTF-8'}&question=0&table=ets_rv_comment&id_customer={$user->id_customer|intval}"><i class="icon-trash"></i></a>
						{else}--{/if}
					</div>
				</div>
			</div>
			<div class="form-group ets_rv_total_review_replies">
				<label class="control-label col-xs-3 col-lg-3">{l s='Total review reply(ies)' mod='ets_reviews'}</label>
				<div class="col-lg-9 col-xs-9">
					<div class="form-control-static">
                        {if $user->total_review_replies|intval > 0}
							<a href="{$link->getAdminLink('AdminEtsRVActivity')|escape:'quotes':'UTF-8'}&activity_type=rep&customer_id={$user->id_customer|intval}" title="{l s='Click to view all review reply activities by this author' mod='ets_reviews'}">
								<span class="ets_rv_total_review_replies">{$user->total_review_replies|intval}</span>
							</a>
							<a class="ets_rv_delete btn btn-default" href="{$currentIndex|escape:'quotes':'UTF-8'}&token={$token|escape:'html':'UTF-8'}&question=0&table=ets_rv_reply_comment&id_customer={$user->id_customer|intval}"><i class="icon-trash"></i></a>
                        {else}--{/if}
					</div>
				</div>
			</div>
			<div class="form-group ets_rv_total_questions">
				<label class="control-label col-xs-3 col-lg-3">{l s='Total question(s)' mod='ets_reviews'}</label>
				<div class="col-lg-9 col-xs-9">
					<div class="form-control-static">
                        {if $user->total_questions|intval > 0}
							<a href="{$link->getAdminLink('AdminEtsRVActivity')|escape:'quotes':'UTF-8'}&activity_type=que&customer_id={$user->id_customer|intval}" title="{l s='Click to view all question activities by this author' mod='ets_reviews'}">
								<span class="ets_rv_total_questions">{$user->total_questions|intval}</span>
							</a>
							<a class="ets_rv_delete btn btn-default" href="{$currentIndex|escape:'quotes':'UTF-8'}&token={$token|escape:'html':'UTF-8'}&question=1&table=ets_rv_product_comment&id_customer={$user->id_customer|intval}"><i class="icon-trash"></i></a>
                        {else}--{/if}
					</div>
				</div>
			</div>
			<div class="form-group ets_rv_total_answers">
				<label class="control-label col-xs-3 col-lg-3">{l s='Total answer(s)' mod='ets_reviews'}</label>
				<div class="col-lg-9 col-xs-9">
					<div class="form-control-static">
                        {if $user->total_answers|intval > 0}
							<a href="{$link->getAdminLink('AdminEtsRVActivity')|escape:'quotes':'UTF-8'}&activity_type=ans&customer_id={$user->id_customer|intval}" title="{l s='Click to view all answer activities by this author' mod='ets_reviews'}">
								<span class="ets_rv_total_answers">{$user->total_answers|intval}</span>
							</a>
							<a class="ets_rv_delete btn btn-default" href="{$currentIndex|escape:'quotes':'UTF-8'}&token={$token|escape:'html':'UTF-8'}&question=1&answer=1&table=ets_rv_comment&id_customer={$user->id_customer|intval}"><i class="icon-trash"></i></a>
                        {else}--{/if}
					</div>
				</div>
			</div>
			<div class="form-group ets_rv_total_qa_comments">
				<label class="control-label col-xs-3 col-lg-3">{l s='Total Q&A comments(s)' mod='ets_reviews'}</label>
				<div class="col-lg-9 col-xs-9">
					<div class="form-control-static">
                        {if $user->total_qa_comments|intval > 0}
						<a href="{$link->getAdminLink('AdminEtsRVActivity')|escape:'quotes':'UTF-8'}&activity_type=cmq&customer_id={$user->id_customer|intval}" title="{l s='Click to view all Q&A comments by this author' mod='ets_reviews'}">
							<span class="ets_rv_total_qa_comments">{$user->total_qa_comments|intval}</span>
						</a>
						<span class="ets_rv_delete btn btn-default" href="{$currentIndex|escape:'quotes':'UTF-8'}&token={$token|escape:'html':'UTF-8'}&question=1&answer=0&table=ets_rv_comment,ets_rv_reply_comment&id_customer={$user->id_customer|intval}"><i class="icon-trash"></i></span>
                        {else}--{/if}
					</div>
				</div>
			</div>
		</div>
		<div class="panel-footer">
            {if isset($buttons) && $buttons}
                {foreach from=$buttons item='button'}
                    {if $button.id == 'delete_all' || !empty($user->id_customer)}
						<a href="{if isset($button.href) && $button.href}{$button.href|escape:'quotes':'UTF-8'}&{$identifier|escape:'html':'UTF-8'}={$user->id_customer|intval}{else}{$currentIndex|escape:'quotes':'UTF-8'}&{$button.id|escape:'html':'UTF-8'}{$table|escape:'html':'UTF-8'}=&{$identifier|escape:'html':'UTF-8'}={$user->id|intval}&token={$token|escape:'html':'UTF-8'}{/if}"
                           {if $user->is_block && $button.id == 'block' || !$user->is_block && $button.id == 'unblock'}style="display: none;" {/if}
						   class="btn btn-default pull-right{if isset($button.class) && $button.class} {$button.class|escape:'html':'UTF-8'}{/if}"
						   title="{$button.title|escape:'html':'UTF-8'}"{if isset($button.confirm) && $button.confirm} onclick="{literal}if (confirm('{/literal}{$button.confirm|escape:'html':'UTF-8'}{literal}')){return true;}else{event.stopPropagation(); event.preventDefault();};{/literal}"{/if}>
                            {if isset($button.icon) && $button.icon}<i
								class="{$button.icon}"></i>{/if} {$button.name|escape:'html':'UTF-8'}
						</a>
                    {/if}
                {/foreach}
            {/if}
			<a href="{$currentIndex|escape:'quotes':'UTF-8'}&token={$token|escape:'html':'UTF-8'}"
			   class="btn btn-default ets_rv_cancel ets_button_gray"><i
						class="process-icon-cancel"></i> {l s='Cancel' mod='ets_reviews'}</a>
		</div>
	</div>
</form>