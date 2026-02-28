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
<div data-comment-url="{$commentUrl nofilter}" class="ets_rv_wrap"{if $ETS_RV_DISPLAY_RATE_AND_QUESTION == 'button' && (!$review_enabled && !$question_enabled || $nb_comments < 1 && $nb_questions < 1 || $nb_comments < 1 && $nb_questions > 0 && !$question_enabled || $nb_comments > 0 && !$review_enabled && $nb_questions < 1)} style="display: none;" {/if}>
	<div class="container">
	 <div class="ets_rv_reviews">
        {if !$back_office || !$employee}
            <div class="ets_rv_header_wrap">
				<div class="h4 ets_rv_reviews_header">
					<div class="ets_rv_reviews_title">
						{if $review_enabled && !$question_enabled}
							{l s='Product Review' mod='ets_reviews'}
						{elseif $question_enabled && $review_enabled }
							{l s='Product Reviews / Q&A' mod='ets_reviews'}
						{elseif !$review_enabled && $question_enabled}
							{l s='Questions & Answers' mod='ets_reviews'}
						{/if}
					</div>
				</div>
				<div class="row ets_rv_wrap_filter">
					<div class="col-md-12 col-sm-12 col-xs-12" id="ets-rv-product-comments-list-header">
						<div class="ets_rv_statistics">
							{if $review_enabled}
                                <div class="ets_rv_stats_review">
    								<div class="ets_rv_average_rating">
    									<div class="h3">{l s='Average rating' mod='ets_reviews'}</div>
    									<div class="ets_rv_average_grade">{$average_grade|floatval|string_format:'%.1f'}</div>
    									<div data-rate-full="★★★★★" data-rate-empty="☆☆☆☆☆" class="ets_rv_grade_stars{if !empty($ETS_RV_DESIGN_COLOR1)} color1{/if}" data-grade="{$average_grade|floatval|string_format:'%.1f'}">
                                        </div>
    									<div class="ets_rv_nb_comments" data-text="{l s='%s Review' mod='ets_reviews'}" data-multi-text="{l s='%s Reviews' mod='ets_reviews'}">
    										{$nb_reviews|intval} {if $nb_reviews > 1}{l s='Reviews' mod='ets_reviews'}{else}{l s='Review' mod='ets_reviews'}{/if}
    									</div>
    								</div>
    								<div class="ets_rv_statistic_rating">
                                        {if $grade_stats|count > 0}
											{foreach from=$grade_stats key='type' item='stats'}
												<div class="ets_rv_grade_stars_{$stats.id|escape:'html':'UTF-8'}">
													<span class="ets_rv_grade_stars{if !empty($ETS_RV_DESIGN_COLOR1)} color1{/if}" data-grade="{$type|intval}">
														{if $type == 1}★☆☆☆☆
														{elseif $type == 2}★★☆☆☆
														{elseif $type == 3}★★★☆☆
														{elseif $type == 4}★★★★☆
														{elseif $type == 5}★★★★★{/if}
													</span>
													<span class="ets_rv_grade_stars_type">{$stats.name|escape:'html':'UTF-8'}</span>
													<span class="ets_rv_grade_stars_process"><span class="ets_rv_grade_stars_percent{if !empty($ETS_RV_DESIGN_COLOR1)} background1{/if}" style="width: {$stats.grade_percent|floatval|string_format:'%.1f'}%;"></span></span>
													<span class="ets_rv_grade_stars_total">{$stats.grade_total|intval}</span>
												</div>
											{/foreach}
										{/if}
    								</div>
    							</div>
                            {/if}
							<div class="ets_rv_modal_review {if !$review_enabled || !$question_enabled} only_1_button{/if}">
	                            {if $review_enabled}
								<button class="btn ets-rv-btn-comment ets-rv-btn-comment-big ets-rv-post-product-comment{if !empty($ETS_RV_DESIGN_COLOR2)} background2{/if}{if !empty($ETS_RV_DESIGN_COLOR3)} bg_hover3 bd_hover3{/if}">
									<i class="svg_fill_white lh_18">
										<svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M491 1536l91-91-235-235-91 91v107h128v128h107zm523-928q0-22-22-22-10 0-17 7l-542 542q-7 7-7 17 0 22 22 22 10 0 17-7l542-542q7-7 7-17zm-54-192l416 416-832 832h-416v-416zm683 96q0 53-37 90l-166 166-416-416 166-165q36-38 90-38 53 0 91 38l235 234q37 39 37 91z"/></svg>
									</i>
									{l s='Write your review' mod='ets_reviews'}
								</button><br />
	                            {/if}
                                {if $question_enabled}
	                                <button class="btn btn-question ets-rv-btn-question-big ets-rv-post-product-question{if !empty($ETS_RV_DESIGN_COLOR2)} background2{/if}{if !empty($ETS_RV_DESIGN_COLOR3)} bg_hover3 bd_hover3{/if}">
		                                <i class="svg_fill_white lh_18">
											<svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1024 1376v-192q0-14-9-23t-23-9h-192q-14 0-23 9t-9 23v192q0 14 9 23t23 9h192q14 0 23-9t9-23zm256-672q0-88-55.5-163t-138.5-116-170-41q-243 0-371 213-15 24 8 42l132 100q7 6 19 6 16 0 25-12 53-68 86-92 34-24 86-24 48 0 85.5 26t37.5 59q0 38-20 61t-68 45q-63 28-115.5 86.5t-52.5 125.5v36q0 14 9 23t23 9h192q14 0 23-9t9-23q0-19 21.5-49.5t54.5-49.5q32-18 49-28.5t46-35 44.5-48 28-60.5 12.5-81zm384 192q0 209-103 385.5t-279.5 279.5-385.5 103-385.5-103-279.5-279.5-103-385.5 103-385.5 279.5-279.5 385.5-103 385.5 103 279.5 279.5 103 385.5z"/></svg>
										</i> {l s='Ask a question' mod='ets_reviews'}
	                                </button>
                                {/if}
							</div>
						</div>
						{include file="./all-photo.tpl"}
						<div class="ets_rv_review_filter">
							<ul class="ets_rv_filter">
                                {if $review_enabled}
									<li class="ets_rv_tab ets_rv_tab_reviews{if !empty($ETS_RV_DESIGN_COLOR1)} bg_hover1 bd_hover1 bg1{/if}" data-tab-id="ets-rv-product-comments-list">
										<div class="ets_rv_tab_item" data-tab-id="#review">
											<div class="ets_rv_bulk_actions">
												<span class="ets_rv_selection" data-default="{l s='Reviews' mod='ets_reviews'} ({$nb_reviews|intval})">{l s='Reviews' mod='ets_reviews'} ({$nb_reviews|intval})</span>
												<div class="dropdown-toggle" data-toggle="dropdown">
													<i class="icon-caret-down"></i>
												</div>
                                                {if $grade_stats|count > 0}
													<ul class="dropdown-menu ets_rv_ul_dropdown">
														<li class="ets_rv_li_dropdown ets_rv_all_review{if !empty($ETS_RV_DESIGN_COLOR3)} bg_hover3 bd_hover3 bg3{/if} active" data-grade="all">{l s='All reviews' mod='ets_reviews'} ({$nb_reviews|intval})</span></li>
                                                        {if $has_video_image}
                                                            <li class="ets_rv_li_dropdown ets_rv_image_review{if !empty($ETS_RV_DESIGN_COLOR3)} bg_hover3 bd_hover3 bg3{/if}{if !$nb_reviewHasImageVideo} empty{/if}" data-grade="has_video_image">{l s='Has image/video' mod='ets_reviews'} ({$nb_reviewHasImageVideo|intval})</span></li>
                                                        {/if}
                                                        {foreach from=$grade_stats key='type' item='stats'}
															<li class="ets_rv_li_dropdown ets_rv_{$stats.id|escape:'html':'UTF-8'}{if !empty($ETS_RV_DESIGN_COLOR3)} bg_hover3 bd_hover3 bg3{/if} {if !$stats.grade_total|intval}empty{/if}" data-grade="{$type|intval}">{$stats.name|escape:'html':'UTF-8'}
																({$stats.grade_total|intval})
															</li>
                                                        {/foreach}
													</ul>
                                                {/if}
											</div>
										</div>
									</li>
                                {/if}
								{if $question_enabled}
									<li class="ets_rv_tab ets_rv_tab_questions{if !empty($ETS_RV_DESIGN_COLOR1)} bg_hover1 bd_hover1 bg1{/if}" data-tab-id="ets-rv-product-questions-list">
										<div class="ets_rv_tab_item" data-tab-id="#question">
											<span class="ets_rv_question_selection">{l s='Questions & Answers' mod='ets_reviews'} ({$nb_questions|intval})</span>
										</div>
									</li>
								{/if}
								<li class="ets_rv_sort_by review">
									<div class="ets_rv_bulk_actions">
                                        {if isset($sort_by) && $sort_by|count > 0}
                                            <div class="dropdown-toggle" data-toggle="dropdown">
        										<label>{l s='Sort by: ' mod='ets_reviews'}</label>
        										<span class="ets_rv_selection" data-default="{$default_sort_by_info.name|escape:'html':'UTF-8'}">{$default_sort_by_info.name|escape:'html':'UTF-8'}</span>
                                            </div>
											<ul class="dropdown-menu ets_rv_ul_dropdown">
	                                            {foreach from=$sort_by item='option'}
													{if $option.value|trim == $default_sort_by|trim}{assign var="sort_by_selected" value=1}{else}{assign var="sort_by_selected" value=0}{/if}
													<li class="ets_rv_li_dropdown ets_rv_sort_by_{$option.id|escape:'html':'UTF-8'}{if !empty($ETS_RV_DESIGN_COLOR3)} bg_hover3 bd_hover3 bg3{/if}{if $sort_by_selected} active{/if}" data-sort="{$option.value|escape:'html':'UTF-8'}">{$option.name|escape:'html':'UTF-8'}</li>
	                                            {/foreach}
											</ul>
	                                    {/if}
									</div>
								</li>
								<li class="ets_rv_sort_by question">
									<div class="ets_rv_bulk_actions">
										{if isset($sort_by_question) && $sort_by_question|count > 0}
											<div class="dropdown-toggle" data-toggle="dropdown">
												<label>{l s='Sort by: ' mod='ets_reviews'}</label>
												<span class="ets_rv_selection" data-default="{$default_sort_by_question_info.name|escape:'html':'UTF-8'}">{$default_sort_by_question_info.name|escape:'html':'UTF-8'}</span>
											</div>
											<ul class="dropdown-menu ets_rv_ul_dropdown">
												{foreach from=$sort_by_question item='option'}
													{if $option.value|trim == $default_sort_by_question|trim}{assign var="sort_by_question_selected" value=1}{else}{assign var="sort_by_question_selected" value=0}{/if}
													<li class="ets_rv_li_dropdown ets_rv_sort_by_question_{$option.id|escape:'html':'UTF-8'}{if !empty($ETS_RV_DESIGN_COLOR3)} bg_hover3 bd_hover3 bg3{/if}{if $sort_by_question_selected} active{/if}" data-sort="{$option.value|escape:'html':'UTF-8'}">{$option.name|escape:'html':'UTF-8'}</li>
												{/foreach}
											</ul>
										{/if}
									</div>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
        {/if}
		<div class="ets_rv_wrap_content row">
			<div id="ets_rv_group_tabs" class="ets_rv_tabs" data-tab-ref="{if $nb_reviews || $nb_questions}ets-rv-product-{if $nb_reviews && $review_enabled}comments{elseif $question_enabled}questions{/if}-list{/if}" data-profile-photo="{$profile_photo nofilter}" data-profile-name="{$customer_name|escape:'html':'UTF-8'}" data-my-account-link="{$my_account_link nofilter}">
                {$PRODUCT_COMMENTS_LIST nofilter}
                {$PRODUCT_QUESTIONS_LIST nofilter}
			</div>
		</div>
	 </div>
	</div>
</div>
