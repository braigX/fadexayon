{**
* TNT OFFICIAL MODULE FOR PRESTASHOP.
*
* @author    Inetum <inetum.com>
* @copyright 2016-2024 Inetum, 2016-2024 TNT
* @license   https://opensource.org/licenses/MIT MIT License
*}

{assign var='isReceiverProtected' value=($strDeliveryPointType !== null && $isExpeditionCreated)}
<div id="extra_address_data" class="panel card">
    <div class="panel-heading card-header">
        <i class="icon-tnt"></i> {l s='TNT Additional Address' mod='tntofficiel'}
    </div>
    <div class="clearfix card-body" data-validated="true">
        <div class="row">
            <div class="form-group col-sm-6">
                <label for="receiver_email">{l s='Email' mod='tntofficiel'}</label>
                {* Email *}
                <input class="form-control" type="text" id="receiver_email" name="receiver_email" value="{$arrFormReceiverInfoValidate.fields.receiver_email|escape:'htmlall':'UTF-8'}" {if $isReceiverProtected}disabled="disabled"{/if} />
                {if $arrFormReceiverInfoValidate.fields.receiver_email && array_key_exists('receiver_email', $arrFormReceiverInfoValidate.errors)}
                    <div class="form-text alert-danger error-receiver_email">{$arrFormReceiverInfoValidate.errors.receiver_email|escape:'htmlall':'UTF-8'}<span class="tiles"></span></div>
                {/if}
            </div>
            <div class="form-group col-sm-6">
                <label for="receiver_phone">{l s='Cellphone' mod='tntofficiel'}</label>
                {* Téléphone portable *}
                <input class="form-control" type="tel" id="receiver_phone" name="receiver_phone" value="{$arrFormReceiverInfoValidate.fields.receiver_phone|escape:'htmlall':'UTF-8'}" {if $isReceiverProtected}disabled="disabled"{/if} />
                {if $arrFormReceiverInfoValidate.fields.receiver_phone && array_key_exists('receiver_phone', $arrFormReceiverInfoValidate.errors)}
                    <div class="form-text alert-danger error-receiver_phone">{$arrFormReceiverInfoValidate.errors.receiver_phone|escape:'htmlall':'UTF-8'}<span class="tiles"></span></div>
                {/if}
            </div>
        </div>
        {if !$isReceiverProtected}
            <a id="submitAddressExtraData" class="btn button button-tntofficiel-small pull-right">
                <span><i class="icon-save" title="{l s='Update' mod='tntofficiel'}"></i> {l s='Validate' mod='tntofficiel'}</span>
            </a>
        {/if}
    </div>
</div>