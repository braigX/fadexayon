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

{if $review_enabled || $question_enabled}

	{* Product comment or Question*}
	{if $review_enabled}{include file='./product-comment-item-prototype.tpl' modal_id='ets-rv-product-comment-item-prototype' qa=0}{/if}
	{if $question_enabled}{include file='./product-comment-item-prototype.tpl' modal_id='ets-rv-product-question-item-prototype' qa=1}{/if}


	{* Comment && answer question *}
	{if $review_enabled}{include file='./comment-item-prototype.tpl' modal_id='comment-item-prototype' qa=0 }{/if}
	{if $question_enabled}{include file='./comment-item-prototype.tpl' modal_id='question-comment-item-prototype' qa=1}{/if}


	{* Reply comment && comment answer question *}
	{if $review_enabled}{include file='./reply-comment-item-prototype.tpl' modal_id='reply-comment-item-prototype' qa=0}{/if}
	{if $question_enabled}{include file='./reply-comment-item-prototype.tpl' modal_id='answer-comment-item-prototype' qa=1}{/if}


	{* Empty product comment modal *}
	{include file='./empty-product-comment.tpl'}

	{* Appreciation post error modal *}
	{include file='./alert-modal.tpl'
		modal_id='ets-rv-update-comment-usefulness-post-error'
		modal_title="{l s='Your review appreciation cannot be sent' mod='ets_reviews'}"
		icon='error'
	}

	{* General voucher modal *}
	{if $discount_enabled}
		{include file='./alert-modal.tpl'
			modal_id='general-voucher-modal'
			modal_title="{l s='Congratulation! You get a discount code from us' mod='ets_reviews'}"
		}
	{/if}
{/if}