{**
 * Web Browser Push Notifications using OneSignal
 *
 * @author    Rolige <www.rolige.com>
 * @copyright Since 2011 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 *}

<div class="col-lg-9">
  <input id="notification_icon_input" class="hide" type="file" onchange="uploadNotificationIcon();" name="notification_icon_input" />
  <div class="dummyfile input-group">
    <span class="input-group-addon"><i class="icon-file"></i></span>
    <input id="attachement_filename" type="text" name="filename" readonly="" />
    <span class="input-group-btn">
      <button id="attachement_fileselectbutton" type="button" name="submitAddAttachments" class="btn btn-default">
        <i class="icon-folder-open"></i> {l s='Choose a file' mod='rg_pushnotifications'}
      </button>
    </span>
  </div>
  <p class="help-block">
    {l s='Recommended format:' mod='rg_pushnotifications'} PNG. {l s='Recommended image dimensions: 192x192 pixels.' mod='rg_pushnotifications'}
  </p>
</div>
