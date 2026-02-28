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
<script src="https://accounts.google.com/gsi/client" async defer></script>
<script type="text/javascript">
    {literal}
        var psv = {/literal}{$psv|floatval}{literal};
        var id_lang = {/literal}{$id_lang|intval}{literal};
        var hiGoogleConnectSecureKey = '{/literal}{$hiGoogleConnectSecureKey|escape:'htmlall':'UTF-8'}{literal}';
        var hiGoogleConnectAdminController = '{/literal}{$hiGoogleConnectAdminController nofilter}{literal}';
        var address_token = '{/literal}{getAdminToken tab='AdminAddresses'}{literal}';
        var ajaxErrorMessage = "{/literal}{l s='Something went wrong, please refresh the page and try again' mod='higoogleconnect'}{literal}";
        var googleClientId = "{/literal}{$googleClientId|escape:'htmlall':'UTF-8'}{literal}";
        var adminPreviewResponseMessage = "{/literal}{l s='This is only preview button, nothing will happen here.' mod='higoogleconnect'}{literal}";
        var googleRegistrationsTxt = "{/literal}{l s='Google Registrations' mod='higoogleconnect'}{literal}";
        var otherTxt = "{/literal}{l s='Other' mod='higoogleconnect'}{literal}";
        var registrationsCountTxt = "{/literal}{l s='Registrations Count' mod='higoogleconnect'}{literal}";
    {/literal}
</script>