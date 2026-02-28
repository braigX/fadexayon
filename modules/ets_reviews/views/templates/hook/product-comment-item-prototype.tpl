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
	<div class="ets-rv-product-comment-list-item row {if $qa} question{/if}" data-product-comment-id="@PRODUCT_COMMENT_ID@" data-product-id="@PRODUCT_ID@" data-status="@CURRENT_STATE@">
		{if !$qa}<div class="ets-rv-comment-author-avatar" title="@CUSTOMER_NAME@">@COMMENT_AVATAR@
        {if !$employee}<a target="_blank" class="product-comment-author-profile-link" data-href="@MY_ACCOUNT@">
			<i class="ets_svg">
				<svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M491 1536l91-91-235-235-91 91v107h128v128h107zm523-928q0-22-22-22-10 0-17 7l-542 542q-7 7-7 17 0 22 22 22 10 0 17-7l542-542q7-7 7-17zm-54-192l416 416-832 832h-416v-416zm683 96q0 53-37 90l-166 166-416-416 166-165q36-38 90-38 53 0 91 38l235 234q37 39 37 91z"/></svg>
			</i>
		</a>{/if}</div>{/if}
		<div class="col-md-12 col-sm-12 col-xs-12 ets-rv-comment-infos">
			{if $qa}
				<i class="comment_qa_title">
					<svg width="20" height="20" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1255 787q0-318-105-474.5t-330-156.5q-222 0-326 157t-104 474q0 316 104 471.5t326 155.5q74 0 131-17-22-43-39-73t-44-65-53.5-56.5-63-36-77.5-14.5q-46 0-79 16l-49-97q105-91 276-91 132 0 215.5 54t150.5 155q67-149 67-402zm390 632h117q3 27-2 67t-26.5 95-58 100.5-107 78-162.5 32.5q-71 0-130.5-19t-105.5-56-79-78-66-96q-97 27-205 27-150 0-292.5-58t-253-158.5-178-249-67.5-317.5q0-170 67.5-319.5t178.5-250.5 253.5-159 291.5-58q121 0 238.5 36t217 106 176 164.5 119.5 219 43 261.5q0 190-80.5 347.5t-218.5 264.5q47 70 93.5 106.5t104.5 36.5q61 0 94-37.5t38-85.5z"></path></svg>
				</i>
				<div class="h4 ets-rv-product-comment-title-html">
                    {if $back_office && $employee && $multilang_enabled && $languages|count > 0}
                        {foreach from=$languages item='language'}
                            <span class="translatable-field lang-{$language.id_lang|intval}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
                                @COMMENT_TITLE_{$language.id_lang|intval}@
                            </span>
                        {/foreach}
                    {else}@COMMENT_TITLE@{/if}
                </div>
			{/if}
			<div class="comment-author">
                {if !$qa}
					{if $back_office}<a data-href="@ACTIVITY_LINK@" target="_blank" class="ets-rv-comment-author-name@NO_LINK@{if !empty($ETS_RV_DESIGN_COLOR5)} color5{/if}">@CUSTOMER_NAME@</a>{else}
					<div class="ets-rv-comment-author-name@NO_LINK@{if !empty($ETS_RV_DESIGN_COLOR5)} color5{/if}">@CUSTOMER_NAME@</div>{/if}
					<span class="comment-author-profile">@AUTHOR_PROFILE@</span>
					<span class="comment_flag">@COMMENT_ISO_COUNTRY@</span>
				{/if}
                {include file="./prototype-item-actions.tpl" prop='product-comment'}
                {if !$qa}{(Module::getInstanceByName('ets_reviews')->hookDisplayVerifyPurchase(['id_product'=>$product_id, 'prop'=>'product-comment'])) nofilter}{/if}
			</div>
            {if !$qa}
	            <div class="ets-rv-comment-author-rate ">
					{assign var="average_grade" value="@COMMENT_GRADE@"}
					<div class="ets_rv_grade_stars{if !empty($ETS_RV_DESIGN_COLOR1)} color1{/if}" data-rate-empty="☆☆☆☆☆" data-rate-full="★★★★★" data-grade="@COMMENT_GRADE@" data-graded="{$average_grade|floatval|string_format:'%.1f'}">
                        <span class="ets_rv_average_grade_item">(@COMMENT_GRADE@)</span>
                    </div>
					<div class="review-date ets_rv_form_date_add">
                        {if $back_office}
							<div class="comment-form">
								<div class="input-group">
									<input type="text" class="datepicker comment input-medium" name="date_add" autocomplete="off" value="@DATE_ADD@">
									<span class="input-group-addon"><i class="fa fa-calendar-o"></i></span>
								</div>
								<a href="{if $qa}{$qa_comment_url nofilter}{else}{$comment_url nofilter}{/if}&__ac=update_date_product_comment{if $qa}&qa=1{/if}" class="ets_rv_btn_edit_date product-comment btn btn-primary ets_button_gray">{l s='OK' mod='ets_reviews'}</a>
							</div>
                        {/if}
						{if $qa && $ETS_RV_QA_SHOW_DATE_ADD || !$qa && $ETS_RV_SHOW_DATE_ADD}<span class="review-date-add ets_rv_date_add">@COMMENT_DATE@</span>{/if}
					</div>
                    <div class="ets-rv-no-approved product-comment">@COMMENT_NO_APPROVE@</div>
				</div>
            {/if}
			{if $qa}
				<div class="comment-author width_full">
					<a data-href="@ACTIVITY_LINK@" target="_blank" class="ets-rv-comment-author-name@NO_LINK@{if !empty($ETS_RV_DESIGN_COLOR5)} color5{/if}">@CUSTOMER_NAME@</a>
					<span class="comment-author-profile">@AUTHOR_PROFILE@</span>
					<div class="review-date ets_rv_form_date_add">
						{if $back_office}
							<div class="comment-form">
								<div class="input-group">
									<input type="text" class="datepicker comment input-medium" name="date_add" autocomplete="off" value="@DATE_ADD@">
									<span class="input-group-addon"><i class="fa fa-calendar-o"></i></span>
								</div>
								<a href="{if $qa}{$qa_comment_url nofilter}{else}{$comment_url nofilter}{/if}&__ac=update_date_product_comment{if $qa}&qa=1{/if}" class="ets_rv_btn_edit_date product-comment btn btn-primary ets_button_gray">
									{l s='OK' mod='ets_reviews'}
								</a>
							</div>
						{/if}
						{if $qa && !empty($ETS_RV_QA_SHOW_DATE_ADD) || !$qa && !empty($ETS_RV_SHOW_DATE_ADD)}<span class="review-date-add ets_rv_date_add">@COMMENT_DATE@</span>{/if}
					</div>
					<div class="ets-rv-no-approved product-comment">@COMMENT_NO_APPROVE@</div>
				</div>
				<p class="product-comment-content-html">
                    {if $back_office && $employee && $multilang_enabled && $languages|count > 0}
                        {foreach from=$languages item='language'}
                            <span class="translatable-field lang-{$language.id_lang|intval}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
                                @COMMENT_COMMENT_{$language.id_lang|intval}@
                            </span>
                        {/foreach}
                    {else}@COMMENT_COMMENT@{/if}
                </p>
			{/if}
		</div>
		{if $qa}
			<div class="comment_actions_right">
				<span class="ets_rv_btn_show_answer active{if !empty($ETS_RV_DESIGN_COLOR3)} hover3 bd_hover3{/if}{if $back_office} bo{/if}" data-btn="answer" title="{l s='View all answers' mod='ets_reviews'}">
					<span class="ets_rv_answers_text text" data-text="{l s='Answer' mod='ets_reviews'}" data-multi-text="{l s='Answers' mod='ets_reviews'}">{l s='Answers' mod='ets_reviews'}</span>
					<i class="ets_svg_icon">
						<svg viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1792 1120q0 166-127 451-3 7-10.5 24t-13.5 30-13 22q-12 17-28 17-15 0-23.5-10t-8.5-25q0-9 2.5-26.5t2.5-23.5q5-68 5-123 0-101-17.5-181t-48.5-138.5-80-101-105.5-69.5-133-42.5-154-21.5-175.5-6h-224v256q0 26-19 45t-45 19-45-19l-512-512q-19-19-19-45t19-45l512-512q19-19 45-19t45 19 19 45v256h224q713 0 875 403 53 134 53 333z"/></svg>
					</i>
					<span class="ets_rv_nb_answers">@ANSWERS_NB@</span>
				</span>
				{if !$qa}<span class="label_helpful">{l s='Is this helpful?' mod='ets_reviews'}</span>{/if}
				<span class="nb-comment{if $qa}{if !empty($ETS_RV_DESIGN_COLOR3)} hover3 bd_hover3{/if} question{/if}" title="{l s='View all comments' mod='ets_reviews'}" data-comments="@COMMENTS_NB@">
					<span class="nb-comment-text text" data-text="{l s='Comment' mod='ets_reviews'}" data-multi-text="{l s='Comments' mod='ets_reviews'}">{l s='Comments' mod='ets_reviews'}</span>
					<i class="ets_svg_icon">
						<svg viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg">
							<path d="M640 896q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm384 0q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm384 0q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-512-512q-204 0-381.5 69.5t-282 187.5-104.5 255q0 112 71.5 213.5t201.5 175.5l87 50-27 96q-24 91-70 172 152-63 275-171l43-38 57 6q69 8 130 8 204 0 381.5-69.5t282-187.5 104.5-255-104.5-255-282-187.5-381.5-69.5zm896 512q0 174-120 321.5t-326 233-450 85.5q-70 0-145-8-198 175-460 242-49 14-114 22h-5q-15 0-27-10.5t-16-27.5v-1q-3-4-.5-12t2-10 4.5-9.5l6-9 7-8.5 8-9q7-8 31-34.5t34.5-38 31-39.5 32.5-51 27-59 26-76q-157-89-247.5-220t-90.5-281q0-130 71-248.5t191-204.5 286-136.5 348-50.5 348 50.5 286 136.5 191 204.5 71 248.5z"/></svg>
					</i>
					<span class="nb-comment-value">@COMMENTS_NB@</span>
				</span>
			</div>
		{/if}
		<div class="col-md-12 col-sm-12 col-xs-12 ets-rv-comment-content">
            {if !$qa}
	            <div class="h4 ets-rv-product-comment-title-html">
                    {if $back_office && $employee && $multilang_enabled && $languages|count > 0}
                        {foreach from=$languages item='language'}
                            <span class="translatable-field lang-{$language.id_lang|intval}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
                                @COMMENT_TITLE_{$language.id_lang|intval}@
                            </span>
                        {/foreach}
                    {else}@COMMENT_TITLE@{/if}
				</div>
				<p class="product-comment-content-html">
                    {if $back_office && $employee && $multilang_enabled && $languages|count > 0}
                        {foreach from=$languages item='language'}
                            <span class="translatable-field lang-{$language.id_lang|intval}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
                                @COMMENT_COMMENT_{$language.id_lang|intval}@
                            </span>
                        {/foreach}
                    {else}@COMMENT_COMMENT@{/if}
                </p>
            {/if}
            {if !$qa}
				<div class="product-comment-content_images_videos{if isset($video_enabled) && $video_enabled == true || isset($photo_enabled) && $photo_enabled == true} display_type_slider{/if}">
					<div class="product-comment-image-html">@COMMENT_IMAGES@</div>
					<div class="product-comment-video-html">@COMMENT_VIDEOS@</div>
				</div>
			{/if}
			<div class="ets-rv-comment-buttons btn-group">
                {if $qa}
					<span class="ets_rv_btn_add_answer{if !empty($ETS_RV_DESIGN_COLOR3)} hover3{/if}{if $back_office} bo{/if}" data-btn="answer">{l s='Write an answer' mod='ets_reviews'}</span>
					<span class="ets_rv_btn_add_comment{if !empty($ETS_RV_DESIGN_COLOR3)} hover3{/if}{if $back_office} bo{/if}" data-btn="comment">{l s='Add a comment' mod='ets_reviews'}</span>
					{if $qa_usefulness_enabled}
						<span class="useful-product-comment{if !empty($ETS_RV_DESIGN_COLOR3)} hover3 bd_hover3{/if}{if $back_office} bo{/if}" title="{l s='Like' mod='ets_reviews'}">
							<span class="text"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M384 1344q0-26-19-45t-45-19-45 19-19 45 19 45 45 19 45-19 19-45zm1152-576q0-51-39-89.5t-89-38.5h-352q0-58 48-159.5t48-160.5q0-98-32-145t-128-47q-26 26-38 85t-30.5 125.5-59.5 109.5q-22 23-77 91-4 5-23 30t-31.5 41-34.5 42.5-40 44-38.5 35.5-40 27-35.5 9h-32v640h32q13 0 31.5 3t33 6.5 38 11 35 11.5 35.5 12.5 29 10.5q211 73 342 73h121q192 0 192-167 0-26-5-56 30-16 47.5-52.5t17.5-73.5-18-69q53-50 53-119 0-25-10-55.5t-25-47.5q32-1 53.5-47t21.5-81zm128-1q0 89-49 163 9 33 9 69 0 77-38 144 3 21 3 43 0 101-60 178 1 139-85 219.5t-227 80.5h-129q-96 0-189.5-22.5t-216.5-65.5q-116-40-138-40h-288q-53 0-90.5-37.5t-37.5-90.5v-640q0-53 37.5-90.5t90.5-37.5h274q36-24 137-155 58-75 107-128 24-25 35.5-85.5t30.5-126.5 62-108q39-37 90-37 84 0 151 32.5t102 101.5 35 186q0 93-48 192h176q104 0 180 76t76 179z"/></svg></span>
							<span class="useful-product-comment-value" data-count="@COMMENT_USEFUL_ADVICES@">@COMMENT_USEFUL_ADVICES@</span>
						</span>
						<span class="ets-rv-not-useful-product-comment{if !empty($ETS_RV_DESIGN_COLOR3)} hover3 bd_hover3{/if}{if $back_office} bo{/if}" title="{l s='Dislike' mod='ets_reviews'}">
							<span class="text"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg">
									<path d="M384 448q0-26-19-45t-45-19-45 19-19 45 19 45 45 19 45-19 19-45zm1152 576q0-35-21.5-81t-53.5-47q15-17 25-47.5t10-55.5q0-69-53-119 18-31 18-69 0-37-17.5-73.5t-47.5-52.5q5-30 5-56 0-85-49-126t-136-41h-128q-131 0-342 73-5 2-29 10.5t-35.5 12.5-35 11.5-38 11-33 6.5-31.5 3h-32v640h32q16 0 35.5 9t40 27 38.5 35.5 40 44 34.5 42.5 31.5 41 23 30q55 68 77 91 41 43 59.5 109.5t30.5 125.5 38 85q96 0 128-47t32-145q0-59-48-160.5t-48-159.5h352q50 0 89-38.5t39-89.5zm128 1q0 103-76 179t-180 76h-176q48 99 48 192 0 118-35 186-35 69-102 101.5t-151 32.5q-51 0-90-37-34-33-54-82t-25.5-90.5-17.5-84.5-31-64q-48-50-107-127-101-131-137-155h-274q-53 0-90.5-37.5t-37.5-90.5v-640q0-53 37.5-90.5t90.5-37.5h288q22 0 138-40 128-44 223-66t200-22h112q140 0 226.5 79t85.5 216v5q60 77 60 178 0 22-3 43 38 67 38 144 0 36-9 69 49 73 49 163z"/></svg>
							 </span>
							<span class="ets-rv-not-useful-product-comment-value" data-count="@COMMENT_NOT_USEFUL_ADVICES@">@COMMENT_NOT_USEFUL_ADVICES@</span>
						</span>
					{/if}
                {/if}
                {if !$qa && $usefulness_enabled}
                    {if !$qa}<span class="label_helpful">{l s='Is this helpful?' mod='ets_reviews'}</span>{/if}
					<span class="useful-product-comment{if !empty($ETS_RV_DESIGN_COLOR3)} hover3{/if}{if $back_office} bo{/if}" title="{l s='Like' mod='ets_reviews'}">
						<i class="ets_svg_icon">
                            <svg viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M384 1344q0-26-19-45t-45-19-45 19-19 45 19 45 45 19 45-19 19-45zm1152-576q0-51-39-89.5t-89-38.5h-352q0-58 48-159.5t48-160.5q0-98-32-145t-128-47q-26 26-38 85t-30.5 125.5-59.5 109.5q-22 23-77 91-4 5-23 30t-31.5 41-34.5 42.5-40 44-38.5 35.5-40 27-35.5 9h-32v640h32q13 0 31.5 3t33 6.5 38 11 35 11.5 35.5 12.5 29 10.5q211 73 342 73h121q192 0 192-167 0-26-5-56 30-16 47.5-52.5t17.5-73.5-18-69q53-50 53-119 0-25-10-55.5t-25-47.5q32-1 53.5-47t21.5-81zm128-1q0 89-49 163 9 33 9 69 0 77-38 144 3 21 3 43 0 101-60 178 1 139-85 219.5t-227 80.5h-129q-96 0-189.5-22.5t-216.5-65.5q-116-40-138-40h-288q-53 0-90.5-37.5t-37.5-90.5v-640q0-53 37.5-90.5t90.5-37.5h274q36-24 137-155 58-75 107-128 24-25 35.5-85.5t30.5-126.5 62-108q39-37 90-37 84 0 151 32.5t102 101.5 35 186q0 93-48 192h176q104 0 180 76t76 179z"/></svg>
                        </i>
						<span class="useful-product-comment-value" data-count="@COMMENT_USEFUL_ADVICES@">@COMMENT_USEFUL_ADVICES@</span>
					</span>
					<span class="ets-rv-not-useful-product-comment{if !empty($ETS_RV_DESIGN_COLOR3)} hover3{/if}{if $back_office} bo{/if}" title="{l s='Dislike' mod='ets_reviews'}">
						<i class="ets_svg_icon">
                            <svg width="13" height="13" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg">
                            <path d="M384 448q0-26-19-45t-45-19-45 19-19 45 19 45 45 19 45-19 19-45zm1152 576q0-35-21.5-81t-53.5-47q15-17 25-47.5t10-55.5q0-69-53-119 18-31 18-69 0-37-17.5-73.5t-47.5-52.5q5-30 5-56 0-85-49-126t-136-41h-128q-131 0-342 73-5 2-29 10.5t-35.5 12.5-35 11.5-38 11-33 6.5-31.5 3h-32v640h32q16 0 35.5 9t40 27 38.5 35.5 40 44 34.5 42.5 31.5 41 23 30q55 68 77 91 41 43 59.5 109.5t30.5 125.5 38 85q96 0 128-47t32-145q0-59-48-160.5t-48-159.5h352q50 0 89-38.5t39-89.5zm128 1q0 103-76 179t-180 76h-176q48 99 48 192 0 118-35 186-35 69-102 101.5t-151 32.5q-51 0-90-37-34-33-54-82t-25.5-90.5-17.5-84.5-31-64q-48-50-107-127-101-131-137-155h-274q-53 0-90.5-37.5t-37.5-90.5v-640q0-53 37.5-90.5t90.5-37.5h288q22 0 138-40 128-44 223-66t200-22h112q140 0 226.5 79t85.5 216v5q60 77 60 178 0 22-3 43 38 67 38 144 0 36-9 69 49 73 49 163z"/></svg>
                        </i>
						<span class="ets-rv-not-useful-product-comment-value" data-count="@COMMENT_NOT_USEFUL_ADVICES@">@COMMENT_NOT_USEFUL_ADVICES@</span>
					</span>
					<span class="nb-comment{if $qa}{if !empty($ETS_RV_DESIGN_COLOR3)} hover3{/if} question{/if}" title="{l s='View all comments' mod='ets_reviews'}" data-comments="@COMMENTS_NB@">
						<i class="ets_svg_icon">
                            <svg width="13" height="13" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg">
                            <path d="M640 896q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm384 0q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm384 0q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-512-512q-204 0-381.5 69.5t-282 187.5-104.5 255q0 112 71.5 213.5t201.5 175.5l87 50-27 96q-24 91-70 172 152-63 275-171l43-38 57 6q69 8 130 8 204 0 381.5-69.5t282-187.5 104.5-255-104.5-255-282-187.5-381.5-69.5zm896 512q0 174-120 321.5t-326 233-450 85.5q-70 0-145-8-198 175-460 242-49 14-114 22h-5q-15 0-27-10.5t-16-27.5v-1q-3-4-.5-12t2-10 4.5-9.5l6-9 7-8.5 8-9q7-8 31-34.5t34.5-38 31-39.5 32.5-51 27-59 26-76q-157-89-247.5-220t-90.5-281q0-130 71-248.5t191-204.5 286-136.5 348-50.5 348 50.5 286 136.5 191 204.5 71 248.5z"/></svg>
                        </i>
						<span class="nb-comment-value">@COMMENTS_NB@</span>
					</span>
                {/if}
                {if !$qa}
					<span class="write-a-comment{if isset($guest) && $guest} guest{/if} pull-right{if !empty($ETS_RV_DESIGN_COLOR3)} hover3{/if}">{l s='Write a comment' mod='ets_reviews'}</span>
                {/if}
			</div>
			<div class="ets_rv_comment_list" data-product-comment-id="@PRODUCT_COMMENT_ID@" data-product-id="@PRODUCT_ID@">
				<div class="ets_rv_comment_header">
                    {assign var="more_text" value={l s='comment' mod='ets_reviews'}}
                    {assign var="more_multi_text" value={l s='comments' mod='ets_reviews'}}
					<span class="ets_rv_comment_load_more{if $qa} question{/if}" style="display: none;"
					   data-begin="@COMMENTS_BEGIN@"
					   data-comments-per-page="@COMMENTS_PER_PAGE@"
					   data-rest="@COMMENTS_LOADMORE@"
					   data-text-rest="{l s='View more %1s %2s of %3s rest' sprintf = ['%1s', $more_text, '%2s'] mod='ets_reviews'}"
					   data-multi-text-rest="{l s='View more %1s %2s of %3s rest' sprintf = ['%1s', $more_multi_text, '%2s'] mod='ets_reviews'}"
					   data-text="{l s='View more %1s %2s' sprintf = ['%1s', $more_text] mod='ets_reviews'}"
					   data-multi-text="{l s='View more %1s %2s' sprintf = ['%1s', $more_multi_text] mod='ets_reviews'}">
					</span>
				</div>
				<div class="ets_rv_comment_footer">
					{if $back_office}
						<span class="ets_rv_comment_load_more{if $qa} question{/if} forward-comments bo" style="display: none;"
						   data-begin="@COMMENTS_BEGIN_FORWARD@"
						   data-comments-per-page="@COMMENTS_PER_PAGE_FORWARD@"
						   data-rest="@COMMENTS_LOADMORE_FORWARD@"
						   data-text-rest="{l s='View more %1s %2s of %3s rest' sprintf = ['%1s', $more_text, '%2s'] mod='ets_reviews'}"
						   data-multi-text-rest="{l s='View more %1s %2s of %3s rest' sprintf = ['%1s', $more_multi_text, '%2s'] mod='ets_reviews'}"
						   data-text="{l s='View more %1s %2s' sprintf = ['%1s', $more_text] mod='ets_reviews'}"
						   data-multi-text="{l s='View more %1s %2s' sprintf = ['%1s', $more_multi_text] mod='ets_reviews'}">
						</span>
					{/if}
					<div class="ets_rv_form_comment{if isset($guest) && $guest} guest{/if}{if !$qa && $show_comment_box || $qa && $qa_show_comment_box} active{/if}">
						<div class="ets-rv-comment-author-avatar" title="@CURRENT_NAME@">@CURRENT_AVATAR@{if !$employee}<a target="_blank" class="comment-author-profile-link" data-href="@CURRENT_MY_ACCOUNT@"><i class="ets_svg_icon svg_pencil"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M491 1536l91-91-235-235-91 91v107h128v128h107zm523-928q0-22-22-22-10 0-17 7l-542 542q-7 7-7 17 0 22 22 22 10 0 17-7l542-542q7-7 7-17zm-54-192l416 416-832 832h-416v-416zm683 96q0 53-37 90l-166 166-416-416 166-165q36-38 90-38 53 0 91 38l235 234q37 39 37 91z"></path></svg></i></a>{/if}</div>
						{include file="./product-comment-form.tpl"
							form="comment"
							form_class="comment"
							button_name="{l s='Post comment' mod='ets_reviews'}"
							data_name="{l s='Update comment' mod='ets_reviews'}"
							message="{l s='Write a comment...' mod='ets_reviews'}"
							reCaptchaFor="{if $qa}qa_{/if}comment"
							qa=$qa
							employee=$employee
							button_cancel_class="{if !$qa && $show_comment_box || $qa && $qa_show_comment_box} show_comment_box{/if}"
						}
					</div>
				</div>
			</div>
			{if $qa}
				<div class="ets_rv_answer_list answer show_content" data-product-comment-id="@PRODUCT_COMMENT_ID@" data-product-id="@PRODUCT_ID@">
					<div class="ets_rv_comment_header">
						<h3 class="ets_rv_answers_nb" style="display: none;">
							<i class="comment_answer_title">
								<svg width="20" height="20" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M789 559l-170 450q33 0 136.5 2t160.5 2q19 0 57-2-87-253-184-452zm-725 1105l2-79q23-7 56-12.5t57-10.5 49.5-14.5 44.5-29 31-50.5l237-616 280-724h128q8 14 11 21l205 480q33 78 106 257.5t114 274.5q15 34 58 144.5t72 168.5q20 45 35 57 19 15 88 29.5t84 20.5q6 38 6 57 0 5-.5 13.5t-.5 12.5q-63 0-190-8t-191-8q-76 0-215 7t-178 8q0-43 4-78l131-28q1 0 12.5-2.5t15.5-3.5 14.5-4.5 15-6.5 11-8 9-11 2.5-14q0-16-31-96.5t-72-177.5-42-100l-450-2q-26 58-76.5 195.5t-50.5 162.5q0 22 14 37.5t43.5 24.5 48.5 13.5 57 8.5 41 4q1 19 1 58 0 9-2 27-58 0-174.5-10t-174.5-10q-8 0-26.5 4t-21.5 4q-80 14-188 14z"/></svg>
							</i>
							<span class="ets_rv_answers_text text" data-text="{l s='Answer' mod='ets_reviews'}" data-multi-text="{l s='Answers' mod='ets_reviews'}"></span> <span class="ets_rv_nb_answers">@ANSWERS_NB@</span>
						</h3>
                        {assign var="more_text" value={l s='answer' mod='ets_reviews'}}
                        {assign var="more_multi_text" value={l s='answers' mod='ets_reviews'}}
						<span class="ets_rv_comment_load_more question answer" style="display: none;"
						   data-begin="@ANSWERS_BEGIN@"
						   data-comments-per-page="@COMMENTS_PER_PAGE@"
						   data-rest="@ANSWERS_LOADMORE@"
						   data-text-rest="{l s='View more %1s %2s of %3s rest' sprintf = ['%1s', $more_text, '%2s'] mod='ets_reviews'}"
						   data-multi-text-rest="{l s='View more %1s %2s of %3s rest' sprintf = ['%1s', $more_multi_text, '%2s'] mod='ets_reviews'}"
						   data-text="{l s='View more %1s %2s' sprintf = ['%1s', $more_text] mod='ets_reviews'}"
						   data-multi-text="{l s='View more %1s %2s' sprintf = ['%1s', $more_multi_text] mod='ets_reviews'}">
						</span>
					</div>
					<div class="ets_rv_comment_footer">
						{if $back_office}
							<span class="ets_rv_comment_load_more question answer forward-answers bo" style="display: none;"
							   data-begin="@ANSWERS_BEGIN_FORWARD@"
							   data-comments-per-page="@ANSWERS_PER_PAGE_FORWARD@"
							   data-rest="@ANSWERS_LOADMORE_FORWARD@"
							   data-text-rest="{l s='View more %1s %2s of %3s rest' sprintf = ['%1s', $more_text, '%2s'] mod='ets_reviews'}"
							   data-multi-text-rest="{l s='View more %1s %2s of %3s rest' sprintf = ['%1s', $more_multi_text, '%2s'] mod='ets_reviews'}"
							   data-text="{l s='View more %1s %2s' sprintf = ['%1s', $more_text] mod='ets_reviews'}"
							   data-multi-text="{l s='View more %1s %2s' sprintf = ['%1s', $more_multi_text] mod='ets_reviews'}">
							</span>
						{/if}
						<div class="ets_rv_form_comment answer{if $qa && $show_answer_box} active{/if}">
							<div class="ets-rv-comment-author-avatar" title="@CURRENT_NAME@">@CURRENT_AVATAR@{if !$employee}<a target="_blank" class="comment-author-profile-link" data-href="@CURRENT_MY_ACCOUNT@"><i class="ets_svg_icon svg_pencil"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M491 1536l91-91-235-235-91 91v107h128v128h107zm523-928q0-22-22-22-10 0-17 7l-542 542q-7 7-7 17 0 22 22 22 10 0 17-7l542-542q7-7 7-17zm-54-192l416 416-832 832h-416v-416zm683 96q0 53-37 90l-166 166-416-416 166-165q36-38 90-38 53 0 91 38l235 234q37 39 37 91z"></path></svg></i></a>{/if}</div>
                            {include file="./product-comment-form.tpl"
	                            form='comment'
	                            form_class='comment'
	                            button_name="{l s='Post answer' mod='ets_reviews'}"
	                            data_name="{l s='Update answer' mod='ets_reviews'}"
	                            message="{l s='Write an answer...' mod='ets_reviews'}"
	                            reCaptchaFor='qa_answer'
                                qa=$qa
	                            comment_class='answer'
                                employee=$employee
								button_cancel_class="{if $qa && $show_answer_box} show_answer_box{/if}"
                            }
						</div>
					</div>
				</div>
			{/if}
		</div>
	</div>
</div>