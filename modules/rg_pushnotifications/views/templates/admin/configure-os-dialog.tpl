{**
 * Web Browser Push Notifications using OneSignal
 *
 * @author    Rolige <www.rolige.com>
 * @copyright Since 2011 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 *}

<div id="onesignal-bell-container" class="onesignal-bell-container onesignal-reset">
  <div id="onesignal-bell-launcher" class="onesignal-bell-launcher onesignal-bell-launcher-theme-{$rg_pushnotifications_bell.RGPUNO_BELL_THEME|escape:'htmlall':'UTF-8'} onesignal-bell-launcher-active onesignal-bell-launcher-{if $rg_pushnotifications_bell.RGPUNO_BELL_SIZE=='small'}sm{elseif $rg_pushnotifications_bell.RGPUNO_BELL_SIZE=='medium'}md{else}lg{/if}">
    <div class="onesignal-bell-launcher-button">
      <div class="onesignal-bell-launcher-dialog onesignal-bell-launcher-dialog-opened" style="filter: drop-shadow(0px 2px 2px rgba(34,36,38,.15));; -webkit-filter: drop-shadow(0px 2px 2px rgba(34,36,38,.15));;">
        <div class="onesignal-bell-launcher-dialog-body">
          <h1>{$rg_pushnotifications_bell.RGPUNO_BELL_MAIN_TITLE|escape:'htmlall':'UTF-8'}</h1>
          <div class="divider"></div>
          <div class="push-notification">
            <div class="push-notification-icon push-notification-icon-default"></div>
            <div class="push-notification-text-container">
              <div class="push-notification-text push-notification-text-short"></div>
              <div class="push-notification-text"></div>
              <div class="push-notification-text push-notification-text-medium"></div>
              <div class="push-notification-text"></div>
              <div class="push-notification-text push-notification-text-medium"></div>
            </div>
          </div>
          <div class="action-container">
            <button type="button" class="action" id="subscribe-button" style="{if $rg_pushnotifications_bell.RGPUNO_BELL_DIAG_FORE}color: {$rg_pushnotifications_bell.RGPUNO_BELL_DIAG_FORE|escape:'htmlall':'UTF-8'};{/if}{if $rg_pushnotifications_bell.RGPUNO_BELL_DIAG_BACK}background: {$rg_pushnotifications_bell.RGPUNO_BELL_DIAG_BACK|escape:'htmlall':'UTF-8'} none repeat scroll 0% 0%;{/if}">{$rg_pushnotifications_bell.RGPUNO_BELL_MAIN_SUB|escape:'htmlall':'UTF-8'}</button>
          </div>
          {if $rg_pushnotifications_bell.RGPUNO_BELL_DIAG_BACK_HOVER}
            <style id="onesignal-background-hover-style" type="text/css">#onesignal-bell-container.onesignal-reset .onesignal-bell-launcher .onesignal-bell-launcher-dialog button.action:hover { background: {$rg_pushnotifications_bell.RGPUNO_BELL_DIAG_BACK_HOVER|escape:'htmlall':'UTF-8'} !important; }</style>
          {/if}
          <div class="divider RGPUNO_BELL_SHOW_CREDIT" style="display: {if $rg_pushnotifications_bell.RGPUNO_BELL_SHOW_CREDIT}block{else}none{/if};"></div>
          <div class="kickback RGPUNO_BELL_SHOW_CREDIT" style="display: {if $rg_pushnotifications_bell.RGPUNO_BELL_SHOW_CREDIT}block{else}none{/if};">Powered by <a href="https://onesignal.com" class="kickback" target="_blank">OneSignal</a></div>
        </div>
      </div>
    </div>
  </div>
</div>
<br><br><br><br><br><br><br><br><br><br><br><br>
