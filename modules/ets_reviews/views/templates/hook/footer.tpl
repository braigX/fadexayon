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

<div class="ets_rv_upload_avatar" style="display: none!important;">
  <div class="form-group row ">
    <label class="col-md-3 form-control-label avatar-label">{l s='Upload reviewer avatar' mod='ets_reviews'}</label>
    <div class="col-md-6 ets_rv_profile_image">
      <div id="ets_rv_avatar_thumbnail" data-upload-url="{$upload_url|escape:'quotes':'UTF-8'}" data-btn-delete-title="{l s='Delete' mod='ets_reviews'}">
          {if isset($avatar) && $avatar}
            <img class="ets_rv_avatar" src="{$upload_dir|cat:$avatar|escape:'quotes':'UTF-8'}" title="{$avatar|escape:'quotes':'UTF-8'}" style="max-width: 110px!important;"/>
            <span class="ets_rv_delete_avatar" title="{l s='Delete' mod='ets_reviews'}">
              <i class="ets_svg_icon svg_trash">
                <svg width="14" height="14" class="w_14 h_14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M704 1376v-704q0-14-9-23t-23-9h-64q-14 0-23 9t-9 23v704q0 14 9 23t23 9h64q14 0 23-9t9-23zm256 0v-704q0-14-9-23t-23-9h-64q-14 0-23 9t-9 23v704q0 14 9 23t23 9h64q14 0 23-9t9-23zm256 0v-704q0-14-9-23t-23-9h-64q-14 0-23 9t-9 23v704q0 14 9 23t23 9h64q14 0 23-9t9-23zm-544-992h448l-48-117q-7-9-17-11h-317q-10 2-17 11zm928 32v64q0 14-9 23t-23 9h-96v948q0 83-47 143.5t-113 60.5h-832q-66 0-113-58.5t-47-141.5v-952h-96q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h309l70-167q15-37 54-63t79-26h320q40 0 79 26t54 63l70 167h309q14 0 23 9t9 23z"/></svg>
              </i>
            </span>
          {/if}
      </div>
      <div class="input-group js-parent-focus">
        <input name="avatar" id="avatar" type="file" style="display: none!important;">
        <div class="dummyfile input-group">
          <span class="input-group-addon"><i class="ets_svg_icon svg_image_icon">
              <svg viewBox="0 0 2048 1792" xmlns="http://www.w3.org/2000/svg"><path d="M704 576q0 80-56 136t-136 56-136-56-56-136 56-136 136-56 136 56 56 136zm1024 384v448h-1408v-192l320-320 160 160 512-512zm96-704h-1600q-13 0-22.5 9.5t-9.5 22.5v1216q0 13 9.5 22.5t22.5 9.5h1600q13 0 22.5-9.5t9.5-22.5v-1216q0-13-9.5-22.5t-22.5-9.5zm160 32v1216q0 66-47 113t-113 47h-1600q-66 0-113-47t-47-113v-1216q0-66 47-113t113-47h1600q66 0 113 47t47 113z"/></svg>
            </i></span>
          <input id="avatar-name" type="text" name="avatar" readonly="" class="form-control">
          <span class="input-group-btn">
            <button id="avatar-selectbutton" type="button" name="submitAddAttachments" class="btn btn-primary">
              {l s='Add image' mod='ets_reviews'}
            </button>
          </span>
        </div>
      </div>
      <span class="form-control-comment">{l s='Recommended size: 150 x 150 px' mod='ets_reviews'}</span>
    </div>
    <div class="col-md-3 form-control-comment">{l s='Optional' mod='ets_reviews'}</div>
  </div>
</div>