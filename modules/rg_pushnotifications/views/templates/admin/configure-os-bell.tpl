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
      <svg class="onesignal-bell-svg" height="99.7" style="filter: drop-shadow(0 2px 4px rgba(34,36,38,0.35));; -webkit-filter: drop-shadow(0 2px 4px rgba(34,36,38,0.35));;" viewbox="0 0 99.7 99.7" width="99.7" xmlns="http://www.w3.org/2000/svg">
        <circle class="background" cx="49.9" cy="49.9" r="49.9" style="{if $rg_pushnotifications_bell.RGPUNO_BELL_BACK}fill: {$rg_pushnotifications_bell.RGPUNO_BELL_BACK|escape:'htmlall':'UTF-8'};{/if}">
        </circle>
        <path class="foreground" d="M50.1 66.2H27.7s-2-.2-2-2.1c0-1.9 1.7-2 1.7-2s6.7-3.2 6.7-5.5S33 52.7 33 43.3s6-16.6 13.2-16.6c0 0 1-2.4 3.9-2.4 2.8 0 3.8 2.4 3.8 2.4 7.2 0 13.2 7.2 13.2 16.6s-1 11-1 13.3c0 2.3 6.7 5.5 6.7 5.5s1.7.1 1.7 2c0 1.8-2.1 2.1-2.1 2.1H50.1zm-7.2 2.3h14.5s-1 6.3-7.2 6.3-7.3-6.3-7.3-6.3z" style="{if $rg_pushnotifications_bell.RGPUNO_BELL_FORE}fill: {$rg_pushnotifications_bell.RGPUNO_BELL_FORE|escape:'htmlall':'UTF-8'};{/if}">
        </path>
        <ellipse class="stroke" cx="49.9" cy="49.9" rx="37.4" ry="36.9" style="{if $rg_pushnotifications_bell.RGPUNO_BELL_FORE}stroke: {$rg_pushnotifications_bell.RGPUNO_BELL_FORE|escape:'htmlall':'UTF-8'};{/if}">
        </ellipse>
      </svg>
      <div class="pulse-ring" style=""></div>
      <div class="onesignal-bell-launcher-badge" style=""></div>
      <div class="onesignal-bell-launcher-message onesignal-bell-launcher-message-opened">
        <div class="onesignal-bell-launcher-message-body">{$rg_pushnotifications_bell.RGPUNO_BELL_TIP_STATE_SUB|escape:'htmlall':'UTF-8'}</div>
      </div>
    </div>
  </div>
</div>
<br><br><br>
