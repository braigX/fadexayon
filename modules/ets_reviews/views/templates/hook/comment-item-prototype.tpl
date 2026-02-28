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
    <div class="comment-list-item row{if $qa} question{/if}" data-comment-id="@COMMENT_ID@"
         data-product-comment-id="@PRODUCT_COMMENT_ID@"{if $qa} data-useful-answer="@USEFUL_ANSWER@"{/if}
         data-status="@CURRENT_STATE@">
        <div class="comment-list-item-info">
            {if $qa}
                {if $qa_usefulness_enabled}
                    <div class="comment_arrow_total_ans answer answer-buttons">
                        <a class=ets-rv-star-on" title="{l s='Like' mod='ets_reviews'}"></a>
                        <span class="total-usefulness-comment" data-count="@TOTAL_USEFULNESS@">@TOTAL_USEFULNESS@</span>
                        <span class="useful-comment" title="{l s='Like' mod='ets_reviews'}">
                            <span class="useful-comment-value" data-count="@COMMENT_USEFUL_ADVICES@"></span>
                        </span>
                        <span class="ets-rv-not-useful-comment" title="{l s='Dislike' mod='ets_reviews'}">
                            <span class="ets-rv-not-useful-comment-value" data-count="@COMMENT_NOT_USEFUL_ADVICES@"></span>
                        </span>
                        <a class="useful-answer{if $back_office} bo{/if} @USEFUL_ANSWER@"
                           title="{l s='Useful answer' mod='ets_reviews'}"></a>
                    </div>
                {/if}
                <div class="col-md-12 col-sm-12 col-xs-12 ets-rv-comment-content">
                    <div class="ets-rv-comment-content-html">
                        <i class="ets-rv-comment-content-html_cm">
                            <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg">
                                <path d="M896 384q-204 0-381.5 69.5t-282 187.5-104.5 255q0 112 71.5 213.5t201.5 175.5l87 50-27 96q-24 91-70 172 152-63 275-171l43-38 57 6q69 8 130 8 204 0 381.5-69.5t282-187.5 104.5-255-104.5-255-282-187.5-381.5-69.5zm896 512q0 174-120 321.5t-326 233-450 85.5q-70 0-145-8-198 175-460 242-49 14-114 22h-5q-15 0-27-10.5t-16-27.5v-1q-3-4-.5-12t2-10 4.5-9.5l6-9 7-8.5 8-9q7-8 31-34.5t34.5-38 31-39.5 32.5-51 27-59 26-76q-157-89-247.5-220t-90.5-281q0-174 120-321.5t326-233 450-85.5 450 85.5 326 233 120 321.5z"/>
                            </svg>
                        </i>
                        <span class="review_content_comment">
                            {if $back_office && $employee && $multilang_enabled && $languages|count > 0}
                                {foreach from=$languages item='language'}
                                    <span class="translatable-field lang-{$language.id_lang|intval}"
                                          {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
                                        @COMMENT_COMMENT_{$language.id_lang|intval}@
                                    </span>
                                {/foreach}
                            {else}@COMMENT_COMMENT@{/if}
                        </span>
                    </div>
                </div>
            {else}
                <div class="ets-rv-comment-author-avatar" title="@CUSTOMER_NAME@" title="@CUSTOMER_NAME@">
                    @COMMENT_AVATAR@{if !$employee}<a target="_blank" class="comment-author-profile-link"
                                                      data-href="@MY_ACCOUNT@">
                    <i class="ets_svg_icon">
                        <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg">
                            <path d="M491 1536l91-91-235-235-91 91v107h128v128h107zm523-928q0-22-22-22-10 0-17 7l-542 542q-7 7-7 17 0 22 22 22 10 0 17-7l542-542q7-7 7-17zm-54-192l416 416-832 832h-416v-416zm683 96q0 53-37 90l-166 166-416-416 166-165q36-38 90-38 53 0 91 38l235 234q37 39 37 91z"/>
                        </svg>
                    </i></a>{/if}
                </div>
            {/if}
            <div class="col-md-12 col-sm-12 col-xs-12 ets-rv-comment-infos">
                <div class="comment-author">
                    {if $back_office}
                        <a data-href="@ACTIVITY_LINK@" target="_blank" class="ets-rv-comment-author-name@NO_LINK@{if !empty($ETS_RV_DESIGN_COLOR5)} color5{/if}">@CUSTOMER_NAME@</a>
                    {else}
                    <div class="ets-rv-comment-author-name@NO_LINK@{if !empty($ETS_RV_DESIGN_COLOR5)} color5{/if}">@CUSTOMER_NAME@</div>
                    {/if}
                    <span class="comment-author-profile">@AUTHOR_PROFILE@</span>
                    {if !$qa}
                        <span class="comment_flag">@COMMENT_ISO_COUNTRY@</span>
                        {include file="./prototype-item-actions.tpl" prop='comment'}{/if}
                    {if !$qa}{(Module::getInstanceByName('ets_reviews')->hookDisplayVerifyPurchase(['id_product'=>$product_id, 'prop'=>'comment'])) nofilter}{/if}
                </div>
                <div class="ets-rv-comment-author-rate">
                    <div class="comment-date ets_rv_form_date_add">
                        {if $back_office}
                            <div class="comment-form">
                                <div class="input-group">
                                    <input type="text" class="datepicker comment input-medium" name="date_add"
                                           autocomplete="off" value="@DATE_ADD@">
                                    <span class="input-group-addon"><i class="fa fa-calendar-o"></i></span>
                                </div>
                                <a href="{if $qa}{$qa_comment_url nofilter}{else}{$comment_url nofilter}{/if}&__ac=update_date_comment{if $qa}&qa=1{/if}"
                                   class="ets_rv_btn_edit_date comment btn btn-primary ets_button_gray">{l s='OK' mod='ets_reviews'}</a>
                            </div>
                            <span class="ets_rv_edit_date" data-date-format="{$date_format|escape:'quotes':'UTF-8'}"><i
                                        class="ets_svg_icon svg_pencil-square-o"></i></span>
                        {/if}
                        {if $qa && $ETS_RV_QA_SHOW_DATE_ADD || !$qa && $ETS_RV_SHOW_DATE_ADD}
                            <span class="comment-date-add ets_rv_date_add">@COMMENT_DATE@</span>
                        {/if}
                    </div>
                    <div class="ets-rv-no-approved comment">@COMMENT_NO_APPROVE@</div>
                </div>
                {if $qa}{include file="./prototype-item-actions.tpl" prop='comment'}{include file="./comment-item-button.tpl" qa=$qa}{/if}
            </div>
            {if !$qa}
                <div class="col-md-12 col-sm-12 col-xs-12 ets-rv-comment-content">
                    <p class="ets-rv-comment-content-html">
                        <span>
                            {if $back_office && $employee && $multilang_enabled && $languages|count > 0}
                                {foreach from=$languages item='language'}
                                    <span class="translatable-field lang-{$language.id_lang|intval}"
                                          {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
                                        @COMMENT_COMMENT_{$language.id_lang|intval}@
                                    </span>
                                {/foreach}
                            {else}@COMMENT_COMMENT@{/if}
                        </span>
                    </p>
                    {include file="./comment-item-button.tpl" qa=$qa}
                </div>
            {/if}
        </div>
        <div class="ets_rv_reply_comment_list" data-comment-id="@COMMENT_ID@" data-product-id="@PRODUCT_ID@">
            <div class="ets_rv_reply_comment_header">
                {if $qa}
                    {assign var="more_text" value={l s='comment' mod='ets_reviews'}}
                    {assign var="more_multi_text" value={l s='comments' mod='ets_reviews'}}
                {else}
                    {assign var="more_text" value={l s='reply' mod='ets_reviews'}}
                    {assign var="more_multi_text" value={l s='replies' mod='ets_reviews'}}
                {/if}
                <span class="ets_rv_reply_comment_load_more{if $qa} question{/if}" style="display: none;"
                      data-begin="@REPLIES_BEGIN@"
                      data-replies-per-page="@REPLIES_PER_PAGE@"
                      data-rest="@REPLIES_LOADMORE@"
                      data-text-rest="{l s='View more %1s %2s of %3s rest' sprintf = ['%1s', $more_text, '%2s'] mod='ets_reviews'}"
                      data-multi-text-rest="{l s='View more %1s %2s of %3s rest' sprintf = ['%1s', $more_multi_text, '%2s'] mod='ets_reviews'}"
                      data-text="{l s='View more %1s %2s' sprintf=['%1s', $more_text] mod='ets_reviews'}"
                      data-multi-text="{l s='View more %1s %2s' sprintf=['%1s', $more_multi_text] mod='ets_reviews'}">
				</span>
                {if $qa}
                    {assign var="button_name" value={l s='Post comment' mod='ets_reviews'}}
                    {assign var="data_name" value={l s='Update comment' mod='ets_reviews'}}
                    {assign var="message" value={l s='Write a comment...' mod='ets_reviews'}}
                {else}
                    {assign var="button_name" value={l s='Reply comment' mod='ets_reviews'}}
                    {assign var="data_name" value={l s='Update' mod='ets_reviews'}}
                    {assign var="message" value={l s='Write a reply...' mod='ets_reviews'}}
                {/if}
            </div>
            <div class="ets_rv_reply_comment_footer">
                {if $back_office}
                    <span class="ets_rv_reply_comment_load_more{if $qa} question{/if} forward-replies bo"
                          style="display: none;"
                          data-begin="@REPLIES_BEGIN_FORWARD@"
                          data-replies-per-page="@REPLIES_PER_PAGE_FORWARD@"
                          data-rest="@REPLIES_LOADMORE_FORWARD@"
                          data-text-rest="{l s='View more %1s %2s of %3s rest' sprintf = ['%1s', $more_text, '%2s'] mod='ets_reviews'}"
                          data-multi-text-rest="{l s='View more %1s %2s of %3s rest' sprintf = ['%1s', $more_multi_text, '%2s'] mod='ets_reviews'}"
                          data-text="{l s='View more %1s %2s' sprintf=['%1s', $more_text] mod='ets_reviews'}"
                          data-multi-text="{l s='View more %1s %2s' sprintf=['%1s', $more_multi_text] mod='ets_reviews'}">
					</span>
                {/if}
                <div class="ets_rv_form_reply_comment{if !$qa && $show_reply_box || $qa && $qa_show_comment_box} active{/if}">
                    <div class="ets-rv-comment-author-avatar" title="@CURRENT_NAME@">
                        @CURRENT_AVATAR@
                        {if !$employee}
                            <a target="_blank" class="reply-comment-author-profile-link dev" data-href="@CURRENT_MY_ACCOUNT@"><i class="ets_svg_icon svg_pencil">
                                    <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M491 1536l91-91-235-235-91 91v107h128v128h107zm523-928q0-22-22-22-10 0-17 7l-542 542q-7 7-7 17 0 22 22 22 10 0 17-7l542-542q7-7 7-17zm-54-192l416 416-832 832h-416v-416zm683 96q0 53-37 90l-166 166-416-416 166-165q36-38 90-38 53 0 91 38l235 234q37 39 37 91z"></path></svg>
                                </i></a>
                        {/if}
                    </div>
                    {include file="./product-comment-form.tpl"
                    form = 'reply_comment'
                    form_class = 'reply-comment'
                    button_name = $button_name
                    data_name = $data_name
                    message = $message
                    reCaptchaFor = 'reply'
                    employee=$employee
                    button_cancel_class="{if !$qa && $show_reply_box || $qa && $qa_show_comment_box} show_reply_box{/if}"
                    }
                </div>
            </div>
        </div>
    </div>
</div>
