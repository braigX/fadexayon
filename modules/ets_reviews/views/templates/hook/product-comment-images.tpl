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

{if $ETS_RV_UPLOAD_PHOTO_ENABLED && $ETS_RV_MAX_UPLOAD_PHOTO|intval > 0}
	<div class="row form-group ets_rv_upload_images" data-photos="{$ETS_RV_MAX_UPLOAD_PHOTO|intval}">
		<label class="form-label col-lg-3" data-for="upload_photo">{l s='Upload photos' mod='ets_reviews'}</label>
		<div class="col-lg-9">
			<ul class="ets_rv_upload_photos" data-photos="{$ETS_RV_MAX_UPLOAD_PHOTO|intval}">
				<li class="ets_rv_upload_photo_item">
					<input id="image_1" type="file" name="image[1]" style="display: none!important;" accept="image/*"/>
					<div class="ets_rv_upload_photo_wrap">
						<span class="ets_rv_btn_upload"></span>
						<span class="ets_rv_btn_delete_photo"><svg width="12" height="12" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1490 1322q0 40-28 68l-136 136q-28 28-68 28t-68-28l-294-294-294 294q-28 28-68 28t-68-28l-136-136q-28-28-28-68t28-68l294-294-294-294q-28-28-28-68t28-68l136-136q28-28 68-28t68 28l294 294 294-294q28-28 68-28t68 28l136 136q28 28 28 68t-28 68l-294 294 294 294q28 28 28 68z"/></svg></span>
					</div>
				</li>
			</ul>
			<p class="help-block">{l s='Accepted formats: jpg, jpeg, png, gif. Limit: %s' sprintf=[$PS_ATTACHMENT_MAXIMUM_SIZE] mod='ets_reviews'}</p>
		</div>
	</div>
{/if}
{if $ETS_RV_UPLOAD_VIDEO_ENABLED && $ETS_RV_MAX_UPLOAD_VIDEO|intval > 0}
	<div class="row form-group ets_rv_upload_images">
			<label class="form-label col-lg-3" data-for="upload_video">{l s='Upload videos' mod='ets_reviews'}</label>
            <div class="col-lg-9">
    			<ul class="ets_rv_upload_videos">
                    {for $ik=1 to $ETS_RV_MAX_UPLOAD_VIDEO|intval}
    					<li class="ets_rv_upload_video_item">
    						<input class="video" id="video_{$ik|intval}" type="file" name="video[{$ik|intval}]" accept="video/mp4,video/webm"/>
                            <div class="ets_rv_upload_video_wrap">
                                <span class="ets_rv_video"></span>
    							<span class="ets_rv_btn_delete_video">
									<svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1490 1322q0 40-28 68l-136 136q-28 28-68 28t-68-28l-294-294-294 294q-28 28-68 28t-68-28l-136-136q-28-28-28-68t28-68l294-294-294-294q-28-28-28-68t28-68l136-136q28-28 68-28t68 28l294 294 294-294q28-28 68-28t68 28l136 136q28 28 28 68t-28 68l-294 294 294 294q28 28 28 68z"/></svg>
								</span>
                            </div>
    					</li>
                    {/for}
    			</ul>
	            <p class="help-block">{l s='Accepted formats: mp4, webm. Limit: %s' sprintf=[$PS_ATTACHMENT_MAXIMUM_SIZE] mod='ets_reviews'}</p>
            </div>
	</div>
{/if}