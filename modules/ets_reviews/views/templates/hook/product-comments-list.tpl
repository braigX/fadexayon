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

{if $back_office && $employee || $id_product_comment}
<div id="ets_rv_group_tabs" class="ets_rv_tabs" data-profile-photo="{$profile_photo nofilter}" data-profile-name="{$customer_name|escape:'html':'UTF-8'}" data-my-account-link="{$my_account_link nofilter}">
    {/if}
	<div id="ets-rv-product-{if $qa}questions{else}comments{/if}-list"
	     class="ets_rv_tab_content col-md-12 col-sm-12 col-xs-12{if $back_office && $employee || $id_product_comment} active{/if}"
	     data-comments-url="{if $qa}{$qa_comment_url nofilter}{else}{$comment_url nofilter}{/if}"
	     data-reviews-initial="{$reviews_initial|intval}"
	     data-reviews-per-page="{$reviews_per_page|intval}"
	     data-comments-initial="{$comments_initial|intval}"
	     data-comments-per-page="{$comments_per_page|intval}"
	     data-replies-initial="{$replies_initial|intval}"
	     data-replies-per-page="{$replies_per_page|intval}"
	>
		<div id="product-comments-list-footer" class="col-md-12 col-sm-12 col-xs-12">
            {if $qa}
                {assign var="rest" value={math equation="nb - init" nb=$nb_questions init=$reviews_initial}}
                {assign var="more_multi_text" value={l s='questions' mod='ets_reviews'}}
                {assign var="more_text" value={l s='question' mod='ets_reviews'}}
            {else}
                {assign var="rest" value={math equation="nb - init" nb=$nb_reviews init=$reviews_initial}}
                {assign var="more_multi_text" value={l s='reviews' mod='ets_reviews'}}
                {assign var="more_text" value={l s='review' mod='ets_reviews'}}
            {/if}
			<span class="ets_rv_product_comment_load_more{if !empty($ETS_RV_DESIGN_COLOR3)} hover3{/if}" style="display: none;"
			   data-begin="{$reviews_initial|intval}"
			   data-reviews-per-page="{$reviews_per_page|intval}"
			   data-rest="{$rest|intval}"
			   data-text-rest="{l s='View more %1s %2s of %3s rest' sprintf=['%1s', $more_text, '%2s'] mod='ets_reviews'}"
			   data-multi-text-rest="{l s='View more %1s %2s of %3s rest' sprintf=['%1s', $more_multi_text, '%2s'] mod='ets_reviews'}"
			   data-text="{l s='View more %1s %2s' sprintf=['%1s', $more_text] mod='ets_reviews'}"
			   data-multi-text="{l s='View more %1s %2s' sprintf = ['%1s', $more_multi_text] mod='ets_reviews'}">
                {if $rest > $reviews_per_page}
                    {l s='View more %1s %2s of %3s rest' sprintf=[$reviews_per_page, $more_multi_text, $rest] mod='ets_reviews'}
                {else}
                    {assign var="sl_more_text" value=$more_multi_text}
                    {if $rest|intval <= 1}
                        {assign var="sl_more_text" value=$more_text}
                    {/if}
					{l s='View more %1s %2s' sprintf=[$rest, $sl_more_text] mod='ets_reviews'}
                {/if}
			</span>
			{if $qa < 1}
				<p class="ets-rv-comment empty alert alert-info"{if $nb_reviews > 0} style="display: none;" {/if}>{l s='There are no available reviews.' mod='ets_reviews'}&nbsp;<span class="ets-rv-write-rewrite">{l s='Write your review.' mod='ets_reviews'}</span></p>
			{/if}
			{if $qa > 0}
				<p class="ets-rv-question empty alert alert-info"{if $nb_questions > 0} style="display: none;" {/if}>{l s='There are no available questions.' mod='ets_reviews'}&nbsp;<span class="ets-rv-ask-question">{l s='Ask your question.' mod='ets_reviews'}</span></p>
			{/if}
		</div>
	</div>
    {if $back_office && $employee}
</div>
{/if}