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

<div id="{$modal_id|escape:'html':'UTF-8'}" style="display: none!important;">
	<div class="reply-comment-list-item row{if $qa} question{/if}" data-comment-id="@COMMENT_ID@" data-reply-comment-id="@REPLY_COMMENT_ID@" data-status="@CURRENT_STATE@">
        {if !$qa}<div class="ets-rv-comment-author-avatar" title="@CUSTOMER_NAME@">@COMMENT_AVATAR@{if !$employee}
        <a target="_blank" class="reply-comment-author-profile-link dev1" data-href="@MY_ACCOUNT@"><i class="ets_svg_icon svg_pencil"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M491 1536l91-91-235-235-91 91v107h128v128h107zm523-928q0-22-22-22-10 0-17 7l-542 542q-7 7-7 17 0 22 22 22 10 0 17-7l542-542q7-7 7-17zm-54-192l416 416-832 832h-416v-416zm683 96q0 53-37 90l-166 166-416-416 166-165q36-38 90-38 53 0 91 38l235 234q37 39 37 91z"></path></svg></i></a>{/if}</div>{else}
	        <div class="col-md-12 col-sm-12 col-xs-12 ets-rv-comment-content">
		        <p class="reply-comment-content-html">
                    {if $back_office && $employee && $multilang_enabled && $languages|count > 0}
                        {foreach from=$languages item='language'}
                            <span class="translatable-field lang-{$language.id_lang|intval}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
                                @COMMENT_COMMENT_{$language.id_lang|intval}@
                            </span>
                        {/foreach}
                    {else}@COMMENT_COMMENT@{/if}
                </p>
	        </div>
        {/if}
		<div class="col-md-12 col-sm-12 col-xs-12 ets-rv-comment-infos">
			<div class="comment-author">
				{if $back_office}
					<a data-href="@ACTIVITY_LINK@" target="_blank" class="ets-rv-comment-author-name@NO_LINK@{if !empty($ETS_RV_DESIGN_COLOR5)} color5{/if}">@CUSTOMER_NAME@</a>{else}
					<div class="ets-rv-comment-author-name@NO_LINK@{if !empty($ETS_RV_DESIGN_COLOR5)} color5{/if}">@CUSTOMER_NAME@</div>
				{/if}
				<span class="comment-author-profile">@AUTHOR_PROFILE@</span>
				{if !$qa}<span class="comment_flag">@COMMENT_ISO_COUNTRY@</span>{include file="./prototype-item-actions.tpl" prop='reply-comment'}{/if}
                {if !$qa}{(Module::getInstanceByName('ets_reviews')->hookDisplayVerifyPurchase(['id_product'=>$product_id, 'prop'=>'comment'])) nofilter}{/if}
			</div>
			<div class="ets-rv-comment-author-rate">
				<div class="reply-comment-date ets_rv_form_date_add">
                    {if $back_office}
						<div class="comment-form">
							<div class="input-group">
								<input type="text" class="datepicker comment input-medium" name="date_add" autocomplete="off" value="@DATE_ADD@">
								<span class="input-group-addon"><i class="fa fa-calendar-o"></i></span>
							</div>
							<a href="{if $qa}{$qa_comment_url nofilter}{else}{$comment_url nofilter}{/if}&__ac=update_date_reply_comment{if $qa}&qa=1{/if}" class="ets_rv_btn_edit_date reply-comment btn btn-primary ets_button_gray">{l s='OK' mod='ets_reviews'}</a>
						</div>
						<span class="ets_rv_edit_date" data-date-format="{$date_format|escape:'quotes':'UTF-8'}">
                        <i class="ets_svg_icon svg_pencil-square-o"></i>
                        </span>
                    {/if}
					{if $qa && $ETS_RV_QA_SHOW_DATE_ADD || !$qa && $ETS_RV_SHOW_DATE_ADD}<span class="reply-comment-date-add ets_rv_date_add">@COMMENT_DATE@</span>{/if}
				</div>
				<div class="ets-rv-no-approved reply-comment">@COMMENT_NO_APPROVE@</div>
			</div>
			{if $qa}{include file="./prototype-item-actions.tpl" prop='reply-comment'}{include file="./reply-comment-item-button.tpl"}{/if}
		</div>
        {if !$qa}<div class="col-md-12 col-sm-12 col-xs-12 ets-rv-comment-content">
			<p class="reply-comment-content-html">
                {if $back_office && $employee && $multilang_enabled && $languages|count > 0}
                    {foreach from=$languages item='language'}
                        <span class="translatable-field lang-{$language.id_lang|intval}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
                            @COMMENT_COMMENT_{$language.id_lang|intval}@
                        </span>
                    {/foreach}
                {else}@COMMENT_COMMENT@{/if}
            </p>
			{include file="./reply-comment-item-button.tpl"}
		</div>{/if}
	</div>
</div>
