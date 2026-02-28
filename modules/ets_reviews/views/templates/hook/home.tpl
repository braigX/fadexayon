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

{if isset($nb_reviews) && $nb_reviews > 0}
    <div class="ets_rv_home_reviews">
        <div class="ets_rv_average_rating">
            <h3>{l s='Customer reviews' mod='ets_reviews'}</h3>
            <div class="ets_rv_grade_stars{if !empty($ETS_RV_DESIGN_COLOR1)} color1{/if}" data-rate-full="★★★★★" data-rate-empty="☆☆☆☆☆" data-grade="{$average_grade|floatval|string_format:'%.1f'}">
            </div>
            <div class="ets_rv_average_grade_comments">
                <div class="ets_rv_average_grade">{$average_grade|floatval|string_format:'%.1f'}&nbsp;{l s='rating of' mod='ets_reviews'} {$nb_reviews|intval}&nbsp;{if $nb_reviews > 1}{l s='reviews' mod='ets_reviews'}{else}{l s='review' mod='ets_reviews'}{/if}</div>
            </div>
        </div>
        <div class="ets_rv_latest_reviews">
            {if $latest_reviews}
                {foreach from=$latest_reviews item='rv'}
                    <div class="ets_rv_latest_item">
                        <div class="ets_rv_latest_item_content_wrap">
                            <div class="ets-rv-comment-author-avatar" title="{$rv.customer_name|escape:'html':'UTF-8'}">
                                {if isset($rv.avatar) && $rv.avatar}
                                    <span class="ets_rv_avatar_photo" style="background-image:url({$rv.avatar nofilter})"></span>
                                {elseif isset($rv.avatar_caption) && isset($rv.avatar_color)}
                                    <span class="ets_rv_avatar_caption" style="background-color: {$rv.avatar_color|escape:'html':'UTF-8'}">{$rv.avatar_caption|escape:'html':'UTF-8'}</span>
                                {/if}
                            </div>
                            <div class="ets_rv_latest_item_content">
                                <div class="ets_rv_latest_customer{if !empty($ETS_RV_DESIGN_COLOR5)} color5{/if}">{$rv.customer_name|escape:'html':'UTF-8'}</div>
                                {if isset($rv.verify_purchase) && $rv.verify_purchase}
                                    <span class="product-comment-order-status verify_label purchased{if !empty($ETS_RV_DESIGN_COLOR4)} color4{/if}">{$rv.verify_purchase nofilter}</span>
                                {/if}
                                <div class="ets_rv_latest_date_add">{$rv.display_date_add nofilter}</div>
                                <div data-rate-full="★★★★★" data-rate-empty="☆☆☆☆☆" class="ets_rv_grade_stars{if !empty($ETS_RV_DESIGN_COLOR1)} color1{/if}" data-grade="{$rv.grade|floatval|string_format:'%.1f'}">
                                </div>
                                <div class="ets_rv_latest_title">{$rv.title nofilter}</div>
                                <div class="ets_rv_latest_content">{$rv.content nofilter}</div>
                            </div>
                            <div class="ets_rv_latest_product_infos">
                                <a href="{$rv.product_link nofilter}">
                                    {if isset($rv.product_cover) && $rv.product_cover}
                                        <span class="ets_rv_rate_img">
                                                <img src="{$rv.product_cover nofilter}"/>
                                            </span>
                                    {/if}
                                    <span>{$rv.product_name|escape:'html':'UTF-8'}</span>
                                </a>
                            </div>
                        </div>
                    </div>
                {/foreach}
            {/if}
        </div>
        <div class="ets_rv_latest_reviews_footer">
            <a href="{$link_all_reviews nofilter}" class="{if !empty($ETS_RV_DESIGN_COLOR3)}hover3{/if}">{l s='View all customer reviews' mod='ets_reviews'}</a>
        </div>
    </div>
{/if}