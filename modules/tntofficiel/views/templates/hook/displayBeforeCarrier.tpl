{**
* TNT OFFICIAL MODULE FOR PRESTASHOP.
*
* @author    Inetum <inetum.com>
* @copyright 2016-2024 Inetum, 2016-2024 TNT
* @license   https://opensource.org/licenses/MIT MIT License
*}

{if !$boolCityPostCodeIsValid}
<div class="tntofficiel-box-panel row clearfix" id="noTNTCarrierWarning">
    <span class="col-xs-8">{l s='To view all delivery options, please verify the postal code and city of your delivery address.' mod='tntofficiel'}</span>
    <span class="col-xs-4">
        <a href="{$linkAddress|escape:'html':'UTF-8'}?id_address={$id_address_delivery|intval}&amp;back=order.php"
           class="btn button button-tntofficiel-small pull-right"><span>{l s='Validate my address' mod='tntofficiel'} <i class="icon-chevron-right right"></i> </span></a>
    </span>
</div>
{/if}

{if $strTNTPaymentReadyError}
<div class="row">
    <p class="alert alert-danger">{TNTOfficiel::MODULE_TITLE|escape:'htmlall':'UTF-8'} : {$strTNTPaymentReadyError|escape:'htmlall':'UTF-8'}</p>
</div>
{/if}
