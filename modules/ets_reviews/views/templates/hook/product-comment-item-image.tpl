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
{assign var="is_upload_img" value=false}
{if $is_upload_img}
<form class="ets_rv_form_upload_image" action="{$action nofilter}" enctype="multipart/form-data" action="POST">
	<ul class="ets_rv_images ets_rv_upload_photos" data-count="{$images|count nofilter}"{if isset($ETS_RV_MAX_UPLOAD_PHOTO)} data-photos="{$ETS_RV_MAX_UPLOAD_PHOTO|intval}"{/if}>
{/if}
        {assign var="imageCount" value=isset($images) && $images|count > 0 && $path_uri}
        {if !$is_upload_img && $imageCount}
			<ul class="ets_rv_images" data-count="{$images|count nofilter}"{if isset($ETS_RV_MAX_UPLOAD_PHOTO)} data-photos="{$ETS_RV_MAX_UPLOAD_PHOTO|intval}"{/if}>
		{/if}
				{assign var="ik" value=1}
				{if $imageCount}
					{foreach from=$images item='image'}
						<li class="ets_rv_image_item ets_rv_upload_photo_item">
							<a class="ets_rv_fancy" target="_blank" data-value="{$ik|intval}" href="{$path_uri|cat:$image.image|escape:'quotes':'UTF-8'}-large.jpg" style="background-image:url({$path_uri|cat:$image.image|escape:'quotes':'UTF-8'}-thumbnail.jpg)">
								<img src="{$path_uri|cat:$image.image|escape:'quotes':'UTF-8'}-large.jpg" alt="{$image.image|escape:'quotes':'UTF-8'}-thumbnail.jpg">
							</a>
						</li>
						{assign var="ik" value=$ik+1}
					{/foreach}
				{/if}
				{if $is_upload_img && isset($ETS_RV_MAX_UPLOAD_PHOTO) && $ik <= $ETS_RV_MAX_UPLOAD_PHOTO|intval}
					<li class="ets_rv_upload_photo_item">
						<input id="image_{$ik|intval}" type="file" name="image[{$ik|intval}]" style="display: none!important;" accept="image/*" data-index="{$ik|intval}"/>
						<div class="ets_rv_upload_photo_wrap">
							<span class="ets_rv_btn_upload"></span>
							<span class="ets_rv_btn_delete_photo">
								<svg width="12" height="12" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1490 1322q0 40-28 68l-136 136q-28 28-68 28t-68-28l-294-294-294 294q-28 28-68 28t-68-28l-136-136q-28-28-28-68t28-68l294-294-294-294q-28-28-28-68t28-68l136-136q28-28 68-28t68 28l294 294 294-294q28-28 68-28t68 28l136 136q28 28 28 68t-28 68l-294 294 294 294q28 28 28 68z"></path></svg>
							</span>
						</div>
					</li>
				{/if}
		{if !$is_upload_img && $imageCount}
			</ul>
            <div class="ets_image_list_popup">
				<span class="close_img_list"></span>
				<div class="ets_table">
					<div class="ets_table-cell">
						<div class="ets_popup_content"></div>
					</div>
				</div>
			</div>
        {/if}
        
{if $is_upload_img}
	</ul>
    <div class="ets_image_list_popup">
		<span class="close_img_list"></span>
		<div class="ets_table">
			<div class="ets_table-cell">
				<div class="ets_popup_content"></div>
			</div>
		</div>
	</div>
</form>
{/if}