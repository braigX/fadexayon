{**
 * Web Browser Push Notifications using OneSignal
 *
 * @author    Rolige <www.rolige.com>
 * @copyright Since 2011 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 *}

{if $rg_pushnotifications.is_https}
  <link rel="manifest" href="{$rg_pushnotifications._path|escape:'htmlall':'UTF-8'}manifest.json">
{/if}
<script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" async></script>
