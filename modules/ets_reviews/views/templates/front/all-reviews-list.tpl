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
<div class="ets_rv_all_reviews">
    {if isset($grade_stats) && $grade_stats}
        <div class="ets_rv_all_stats">
            <div class="ets_rv_stats_total">
                <h4>{l s='Total reviews' mod='ets_reviews'}</h4>
                <span>{$nb_reviews|intval}</span>
            </div>
            <div class="ets_rv_stats_average">
                <h4>{l s='Average rating' mod='ets_reviews'}</h4>
                <div class="ets-rv-grade">{$average_grade|floatval|round:1}</div>
                <div class="ets_rv_grade_stars{if !empty($ETS_RV_DESIGN_COLOR1)} color1{/if}" data-rate-full="★★★★★" data-rate-empty="☆☆☆☆☆" data-grade="{$average_grade|floatval|round:1}">
                </div>
            </div>
            <div class="ets_rv_stats_grade">
                {if $grade_stats|count > 0}
                    {foreach from=$grade_stats key='type' item='stats'}
                        <div class="ets_rv_grade_stars_status ets_rv_grade_stars_{$stats.id|escape:'html':'UTF-8'}">
                            <span class="ets_rv_grade_stars{if !empty($ETS_RV_DESIGN_COLOR1)} color1{/if}">
                                    {if $type == 1}★☆☆☆☆
                                    {elseif $type == 2}★★☆☆☆
                                    {elseif $type == 3}★★★☆☆
                                    {elseif $type == 4}★★★★☆
                                    {elseif $type == 5}★★★★★
                                    {/if}
                            </span>
                            <span class="ets_rv_grade_stars_type">{$stats.name|escape:'html':'UTF-8'}</span>
                            <span class="ets_rv_grade_stars_rate">
                                <span class="ets_rv_grade_stars_process">
                                    <span class="ets_rv_grade_stars_percent{if !empty($ETS_RV_DESIGN_COLOR1)} background1{/if}" style="width: {$stats.grade_percent|floatval|string_format:'%.1f'}%; background-color: {$stats.color|escape:'html':'UTF-8'}"></span>
                                </span>
                            </span>
                            <span class="ets_rv_grade_stars_total">{$stats.grade_total|intval}</span>
                        </div>
                    {/foreach}
                {/if}
            </div>
        </div>
    {/if}
    <div class="ets_rv_all_header_grade">
        <h3 class="all_reviews_title">{l s='All reviews' mod='ets_reviews'}</h3>
        <div class="ets_rv_all_filter">
            {if !empty($grade_stats)}
                <div class="ets_rv_all_filter_grade">
                    <span class="ets_rv_all_filter_grade_title">{l s='Rating' mod='ets_reviews'}</span> <button type="button" class="btn btn-default dropdown-toggle{if !empty($ETS_RV_DESIGN_COLOR1)} bg_hover1 bd_hover1 bg1{/if}" data-toggle="dropdown">{if $currentGrade|intval >0}{$currentGrade|intval}{else}{l s='All' mod='ets_reviews'}{/if}<i class="icon-caret-down"></i></button>
                    <ul class="dropdown-menu">
                        <li class="{if !empty($ETS_RV_DESIGN_COLOR3)} bg_hover3 bd_hover3 bg3{/if}"><a href="{if !empty($currentIndex)}{$currentIndex nofilter}{else}javascript:void(0);{/if}" class="grade-items-page" data-items="-1">{l s='All' mod='ets_reviews'}</a></li>
                        {foreach from=$grade_stats key='n' item='item'}
                            <li class="ets-rv-grade-item{if !empty($ETS_RV_DESIGN_COLOR3)} bg_hover3 bd_hover3 bg3{/if}">
                                <a href="{if !empty($currentIndex)}{$currentIndex nofilter}&grade={$n|intval}{if $currentSortBy}&sort_by={$currentSortBy.value|escape:'htmlall':'UTF-8'}{/if}{else}javascript:void(0);{/if}" class="grade-items-page" data-items="{$n|intval}">{$n|intval}</a>
                            </li>
                        {/foreach}
                    </ul>
                </div>
            {/if}
            {if !empty($sort_by)}
                <div class="ets_rv_all_filter_sort_by">
                    <span class="ets_rv_all_filter_sort_by_title">{l s='Sort by' mod='ets_reviews'}</span> <button type="button" class="btn btn-default dropdown-toggle{if !empty($ETS_RV_DESIGN_COLOR1)} bg_hover1 bd_hover1 bg1{/if}" data-toggle="dropdown">{if $currentSortBy}{$currentSortBy.name|escape:'htmlall':'UTF-8'}{/if}<i class="icon-caret-down"></i></button>
                    <ul class="dropdown-menu">
                        {foreach from=$sort_by key='id' item='item'}
                            {if $item.id !== 'helpful'}
                                <li class="ets-rv-sort-item{if !empty($ETS_RV_DESIGN_COLOR3)} bg_hover3 bd_hover3 bg3{/if}">
                                    <a href="{if !empty($currentIndex)}{$currentIndex nofilter}&sort_by={$id|escape:'htmlall':'UTF-8'}{if $currentGrade|intval > 0}&grade={$currentGrade|intval}{/if}{else}javascript:void(0);{/if}" class="sort-by-items-page" data-items="{$id|escape:'htmlall':'UTF-8'}">{$item.name|escape:'htmlall':'UTF-8'}</a>
                                </li>
                            {/if}
                        {/foreach}
                    </ul>
                </div>
            {/if}
        </div>
    </div>
	<div class="ets_rv_all_list">
		{if isset($list) && $list}
			{foreach from=$list item='rv'}
                <div class="ets_rv_review_item item{$rv.id_ets_rv_product_comment|intval}">
                    <div class="ets_rv_author_info">
                        <div class="ets-rv-comment-author-avatar" title="{$rv.customer_name|escape:'html':'UTF-8'}">
                            {if isset($rv.avatar) && $rv.avatar}
                                <span class="ets_rv_avatar_photo" style="background-image:url({$rv.avatar nofilter})"></span>
                            {elseif isset($rv.avatar_caption) && isset($rv.avatar_color)}
                                <span class="ets_rv_avatar_caption" style="background-color: {$rv.avatar_color|escape:'html':'UTF-8'}">{$rv.avatar_caption|escape:'html':'UTF-8'}</span>
                            {/if}
                        </div>
                        <div class="ets_rv_product_infos_right">
                            <div class="ets_rv_latest_customer{if !empty($ETS_RV_DESIGN_COLOR5)} color5{/if}">{$rv.customer_name|escape:'html':'UTF-8'}</div>
                            {if isset($rv.verify_purchase) && $rv.verify_purchase}
                                <span class="product-comment-order-status verify_label purchased{if !empty($ETS_RV_DESIGN_COLOR4)} color4{/if}">{$rv.verify_purchase nofilter}</span>
                            {/if}
                            <div class="ets_rv_product_infos">
                                <a href="{$rv.product_link nofilter}">
                                    {if isset($rv.product_cover) && $rv.product_cover}
                                        <span class="ets_rv_rate_img"> <img src="{$rv.product_cover nofilter}"/></span>
                                    {/if}
                                    <span>{$rv.product_name|escape:'html':'UTF-8'}</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="ets_rv_review_content">
                        <div class="ets-rv-review-infos">
                            <div data-rate-full="★★★★★" data-rate-empty="☆☆☆☆☆" class="ets_rv_grade_stars{if !empty($ETS_RV_DESIGN_COLOR1)} color1{/if}" data-grade="{$rv.grade|floatval|string_format:'%.1f'}">
                            </div>
                            <span class="ets-rv-review-date">{$rv.display_date_add|escape:'htmlall':'UTF-8'}</span>
                        </div>
                        <p class="ets-rv-title">{$rv.title nofilter}</p>
                        <p class="ets-rv-content">{$rv.content nofilter}</p>
                        <div class="ets-rv-images-videos">
                            {if isset($rv.images) && $rv.images}
                                <ul class="ets_rv_images">
                                    {assign var="ik" value=1}
                                    {foreach from=$rv.images item='image'}
                                        <li class="ets_rv_image_item ets_rv_upload_photo_item">
                                            <a class="ets_rv_fancy" target="_blank" data-value="{$ik|intval}" href="{$path_uri|cat:$image.image|escape:'quotes':'UTF-8'}-large.jpg" style="background-image:url({$path_uri|cat:$image.image|escape:'quotes':'UTF-8'}-thumbnail.jpg)">
                                                <img src="{$path_uri|cat:$image.image|escape:'quotes':'UTF-8'}-large.jpg" alt="{$image.image|escape:'quotes':'UTF-8'}-thumbnail.jpg">
                                            </a>
                                        </li>
                                        {assign var="ik" value=$ik+1}
                                    {/foreach}
                                </ul>
                            {/if}
                            {if isset($rv.videos) && $rv.videos}
                                <ul class="ets_rv_videos ">
                                    {assign var="ik" value=1}
                                    {foreach from=$rv.videos item='video'}
                                        {assign var="ik" value=$ik+1}
                                        <li class="ets_rv_video_item">
                                            <video controls>
                                                <source src="{$video.url|escape:'html':'UTF-8'}" type="{$video.type|escape:'html':'UTF-8'}" />
                                            </video>
                                        </li>
                                    {/foreach}
                                </ul>
                            {/if}
                        </div>
                    </div>
                </div>
			{/foreach}
		{else}<p class="alert alert-info">{l s='No reviews.' mod='ets_reviews'}</p>{/if}
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
            {if !empty($paginates) && isset($current_per_page) && isset($total_records) && $current_per_page|intval < $total_records|intval}
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
    <div class="ets_image_list_popup">
        <span class="close_img_list"></span>
        <div class="ets_table">
            <div class="ets_table-cell">
                <div class="ets_popup_content"></div>
            </div>
        </div>
    </div>
</div>