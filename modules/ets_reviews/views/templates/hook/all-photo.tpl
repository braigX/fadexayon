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

{if isset($ETS_RV_PHOTOS_OF_PRODUCT) && $ETS_RV_PHOTOS_OF_PRODUCT && isset($ETS_RV_DISPLAY_ALL_PHOTO) && $ETS_RV_DISPLAY_ALL_PHOTO}
    <div class="ets_rv_review_photos_wrap display_type_slider">
        <div class="ets_rv_review_photos">
            {assign var="ik" value=0}
            {foreach from=$ETS_RV_PHOTOS_OF_PRODUCT item='photo'}
                <div class="ets_rv_image_item ets_rv_upload_photo_item">
                    <div class="ets_rv_image_item_bg" style="background-image:url({$photo_path_uri|cat:$photo.image|escape:'quotes':'UTF-8'}-large.jpg);">
                        <a class="ets_rv_fancy" target="_blank" data-value="{$ik|intval}"  href="#">
                            <img src="{$photo_path_uri|cat:$photo.image|escape:'quotes':'UTF-8'}-large.jpg" alt="{$photo.image|escape:'quotes':'UTF-8'}-thumbnail.jpg">
                        </a>
                    </div>
                </div>
                {assign var="ik" value=$ik+1}
            {/foreach}
        </div>
        <div class="ets_image_list_popup">
            <span class="close_img_list"></span>
            <div class="ets_table">
                <div class="ets_table-cell">
                    <div class="ets_popup_content">
                        <ul class="ets_rv_review_photos_ul">
                            {assign var="ik" value=0}
                            {foreach from=$ETS_RV_PHOTOS_OF_PRODUCT item='photo'}
                                <li class="ets_rv_image_item ets_rv_upload_photo_item">
                                    <a class="ets_rv_fancy" target="_blank" data-value="{$ik|intval}" href="{$photo_path_uri|cat:$photo.image|escape:'quotes':'UTF-8'}-large.jpg" style="background-image:url({$photo_path_uri|cat:$photo.image|escape:'quotes':'UTF-8'}-large.jpg)">
                                        <img src="{$photo_path_uri|cat:$photo.image|escape:'quotes':'UTF-8'}-large.jpg" alt="{$photo.image|escape:'quotes':'UTF-8'}-thumbnail.jpg">
                                    </a>
                                </li>
                                {assign var="ik" value=$ik+1}
                            {/foreach}
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
{/if}