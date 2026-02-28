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

{if ($review_enabled || $question_enabled)}
	{assign var="hidden_on_button" value=$nb_comments < 1 && $nb_questions < 1 || $nb_comments < 1 && $nb_questions > 0 && !$question_enabled || $nb_comments > 0 && !$review_enabled && $nb_questions < 1}
	<div class="ets-rv-product-comments-additional-info">
    {if isset($review_enabled) && $review_enabled}
        <a class="ets-rv-btn-read-user{if !$average_grade && $ETS_RV_DISPLAY_RATE_AND_QUESTION|trim === 'button'} ets-rv-hidden{/if}" href="#ets-rv-product-comments-list-header">
            {include file='./average-grade-stars.tpl' grade=$average_grade position='product'}
        </a>
		{if $ETS_RV_DISPLAY_RATE_AND_QUESTION|trim === 'button'}
			{if $nb_comments == 0 && $hidden_on_button}
				<button class="btn ets-rv-btn-comment ets-rv-post-product-comment ets-rv-btn-comment-big{if !empty($ETS_RV_DESIGN_COLOR2)} background2{/if}{if !empty($ETS_RV_DESIGN_COLOR3)} bg_hover3 bd_hover3{/if}">
					<i class="svg_fill_white lh_18">
						<svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M491 1536l91-91-235-235-91 91v107h128v128h107zm523-928q0-22-22-22-10 0-17 7l-542 542q-7 7-7 17 0 22 22 22 10 0 17-7l542-542q7-7 7-17zm-54-192l416 416-832 832h-416v-416zm683 96q0 53-37 90l-166 166-416-416 166-165q36-38 90-38 53 0 91 38l235 234q37 39 37 91z"/></svg>
					</i>
					{l s='Write your review' mod='ets_reviews'}
				</button>
			{else}
				{if isset($question_enabled) && $question_enabled}
				<div class="clearfix"></div>
				{/if}
				{if isset($displaySchema) && $displaySchema}
					<div   >
						<meta  content="{$nb_comments|intval}"/>
						<meta  content="{$average_grade|floatval|round:1}"/>
					</div>
				{/if}
				<a class="link-comment{if !$hidden_on_button} ets-rv-hidden{/if} ets-rv-post-product-comment ets-rv-btn-comment ets-rv-btn-comment-big{if !empty($ETS_RV_DESIGN_COLOR2)} background2{/if}{if !empty($ETS_RV_DESIGN_COLOR3)} bg_hover3 bd_hover3{/if}" href="#ets-rv-product-comments-list-header">
					<i class="svg_fill_white lh_18">
						<svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M491 1536l91-91-235-235-91 91v107h128v128h107zm523-928q0-22-22-22-10 0-17 7l-542 542q-7 7-7 17 0 22 22 22 10 0 17-7l542-542q7-7 7-17zm-54-192l416 416-832 832h-416v-416zm683 96q0 53-37 90l-166 166-416-416 166-165q36-38 90-38 53 0 91 38l235 234q37 39 37 91z"/></svg>
					</i>
					{l s='Write your review' mod='ets_reviews'}
				</a>
			{/if}
		{/if}
    {/if}
    {if isset($question_enabled) && $question_enabled && $ETS_RV_DISPLAY_RATE_AND_QUESTION|trim==='button'}
        {if $nb_questions == 0 && $hidden_on_button}
			<button class="btn ets-rv-btn-comment ets-rv-post-product-question ets-rv-btn-comment-big{if !empty($ETS_RV_DESIGN_COLOR2)} background2{/if}{if !empty($ETS_RV_DESIGN_COLOR3)} bg_hover3 bd_hover3{/if}">
				<i class="svg_fill_white lh_18">
					<svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1024 1376v-192q0-14-9-23t-23-9h-192q-14 0-23 9t-9 23v192q0 14 9 23t23 9h192q14 0 23-9t9-23zm256-672q0-88-55.5-163t-138.5-116-170-41q-243 0-371 213-15 24 8 42l132 100q7 6 19 6 16 0 25-12 53-68 86-92 34-24 86-24 48 0 85.5 26t37.5 59q0 38-20 61t-68 45q-63 28-115.5 86.5t-52.5 125.5v36q0 14 9 23t23 9h192q14 0 23-9t9-23q0-19 21.5-49.5t54.5-49.5q32-18 49-28.5t46-35 44.5-48 28-60.5 12.5-81zm384 192q0 209-103 385.5t-279.5 279.5-385.5 103-385.5-103-279.5-279.5-103-385.5 103-385.5 279.5-279.5 385.5-103 385.5 103 279.5 279.5 103 385.5z"/></svg>
				</i>
                {l s='Ask a question' mod='ets_reviews'}
			</button>
        {else}
			<a class="link-comment{if !$hidden_on_button} ets-rv-hidden{/if} ets-rv-post-product-question ets-rv-btn-comment ets-rv-btn-comment-big{if !empty($ETS_RV_DESIGN_COLOR2)} background2{/if}{if !empty($ETS_RV_DESIGN_COLOR3)} bg_hover3 bd_hover3{/if}" href="#ets-rv-product-questions-list-header">
				<i class="svg_fill_white lh_18">
					<svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1024 1376v-192q0-14-9-23t-23-9h-192q-14 0-23 9t-9 23v192q0 14 9 23t23 9h192q14 0 23-9t9-23zm256-672q0-88-55.5-163t-138.5-116-170-41q-243 0-371 213-15 24 8 42l132 100q7 6 19 6 16 0 25-12 53-68 86-92 34-24 86-24 48 0 85.5 26t37.5 59q0 38-20 61t-68 45q-63 28-115.5 86.5t-52.5 125.5v36q0 14 9 23t23 9h192q14 0 23-9t9-23q0-19 21.5-49.5t54.5-49.5q32-18 49-28.5t46-35 44.5-48 28-60.5 12.5-81zm384 192q0 209-103 385.5t-279.5 279.5-385.5 103-385.5-103-279.5-279.5-103-385.5 103-385.5 279.5-279.5 385.5-103 385.5 103 279.5 279.5 103 385.5z"/></svg>
				</i>
                {l s='Ask a question' mod='ets_reviews'}
			</a>
        {/if}
    {/if}
</div>
{/if}