{**
 * 2012 - 2024 HiPresta
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0).
 * It is also available through the world-wide-web at this URL: https://opensource.org/licenses/AFL-3.0
 *
 * @author    HiPresta <support@hipresta.com>
 * @copyright HiPresta 2024
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 *
 * @website   https://hipresta.com
 *}
<div class="hi-gc-button-container hi-gc-{$hook}">
    <div class="g_id_signin"
        data-type="{$hiGoogleButtonSettings.buttonType|escape:'htmlall':'UTF-8'}"
        data-shape="{$hiGoogleButtonSettings.buttonShape|escape:'htmlall':'UTF-8'}"
        data-theme="{$hiGoogleButtonSettings.buttonTheme|escape:'htmlall':'UTF-8'}"
        data-text="{$hiGoogleButtonSettings.buttonText|escape:'htmlall':'UTF-8'}"
        data-size="{$hiGoogleButtonSettings.buttonSize|escape:'htmlall':'UTF-8'}"
        data-locale="{$langIsoCode}">
    </div>

    <div id="g_id_onload"
        data-client_id="{$googleClientId|escape:'htmlall':'UTF-8'}"
        data-context="signin"
        data-ux_mode="popup"
        data-callback="hiGoogleConnectResponse"
        data-auto_prompt="{if $hiGoogleButtonSettings.enableOneTapPrompt}true{else}false{/if}">
    </div>
</div>