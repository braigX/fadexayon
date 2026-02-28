{**
* TNT OFFICIAL MODULE FOR PRESTASHOP.
*
* @author    Inetum <inetum.com>
* @copyright 2016-2024 Inetum, 2016-2024 TNT
* @license   https://opensource.org/licenses/MIT MIT License
*}

<!-- Tnt carriers -->
<div style="display: none;">
    {foreach $arrObjTNTCarrierModelList as $id_carrier => $objTNTCarrierModel}
        {assign var='objTNTCarrierModelInfos' value=$objTNTCarrierModel->getCarrierInfos()}
        {foreach $arrDeliveryOption as $idAddressSelected => $intCarrierIDList}
            {assign var='arrCarrierIDList' value=explode(',',$intCarrierIDList)}
            {assign var='intCarrierID' value=$arrCarrierIDList[0]}
            <div id="TNTOfficielCarrier_{$objTNTCarrierModel->id_carrier|escape:'htmlall':'UTF-8'}">
                <span class="tntofficiel-delay">
                    {* VALIDATOR: This variable is HTML content. Do not escape. *}
                    {$objTNTCarrierModelInfos->delay nofilter}
                </span>
            </div>
        {/foreach}
    {/foreach}

    {* if TNT delivery option is selected *}
    {*if $strCarrierTypeSelected && $arrFormReceiverInfoValidate*}
    <div id="extra_address_data" class="card card-block clearfix" data-validated="{$strExtraAddressDataValid|escape:'htmlall':'UTF-8'}" >
        <h3 class="page-subheading">{l s='TNT Additional Address' mod='tntofficiel'}</h3>
        <div class="form-group"><p>
           {l s='Please check the information below or fill in if missing.'  mod='tntofficiel'}
           {l s='If you change the information, click on the button'  mod='tntofficiel'}
           <b>{l s='Validate'  mod='tntofficiel'}</b> {l s='before continuing.'  mod='tntofficiel'}
           {l s='The information entered will be transmitted to the driver to facilitate the delivery of your package.' mod='tntofficiel'}
        </p></div>
        <div class="row">
            <div class="form-group col-xs-12 col-sm-12 col-lg-6">
                <label for="receiver_email">{l s='Email' mod='tntofficiel'} <span class="required"></span></label>
                {* Email *}
                <input class="form-control" type="text" id="receiver_email" name="receiver_email"
                       value="{$arrFormReceiverInfoValidate.fields.receiver_email|escape:'htmlall':'UTF-8'}" />
                {if $arrFormReceiverInfoValidate.fields.receiver_email && array_key_exists('receiver_email', $arrFormReceiverInfoValidate.errors)}
                    <small class="form-text alert-danger error-receiver_email">{$arrFormReceiverInfoValidate.errors.receiver_email|escape:'htmlall':'UTF-8'}</small>
                {/if}
            </div>
            <div class="form-group col-xs-12 col-sm-12 col-lg-6">
                <label for="receiver_phone">{l s='Cellphone' mod='tntofficiel'} <span class="required"></span></label>
                {* Téléphone portable *}
                <input class="form-control" type="tel" id="receiver_phone" name="receiver_phone"
                       value="{$arrFormReceiverInfoValidate.fields.receiver_phone|escape:'htmlall':'UTF-8'}" />
                {if $arrFormReceiverInfoValidate.fields.receiver_phone && array_key_exists('receiver_phone', $arrFormReceiverInfoValidate.errors)}
                    <small class="form-text alert-danger error-receiver_phone">{$arrFormReceiverInfoValidate.errors.receiver_phone|escape:'htmlall':'UTF-8'}</small>
                {/if}
            </div>
        </div>
            {* B2C INDIVIDUAL *}
            {*if $strCarrierTypeSelected === 'INDIVIDUAL'*}
        <div class="row">
            <div class="form-group col-xs-12 col-sm-12 col-lg-4">
                <label for="receiver_building">{l s='Building Number' mod='tntofficiel'}</label>
                {* Numéro du bâtiment *}
                <input class="form-control" type="text" id="receiver_building" name="receiver_building"
                       value="{$arrFormReceiverInfoValidate.fields.receiver_building|escape:'htmlall':'UTF-8'}" maxlength="3" />
                {if $arrFormReceiverInfoValidate.fields.receiver_building && array_key_exists('receiver_building', $arrFormReceiverInfoValidate.errors)}
                    <small class="form-text alert-danger error-receiver_building">{$arrFormReceiverInfoValidate.errors.receiver_building|escape:'htmlall':'UTF-8'}.</small>
                {else}
                    <small class="form-text info-receiver_building">3 caractères maximum</small>
                {/if}
            </div>
            <div class="form-group col-xs-12 col-sm-12 col-lg-4">
                <label for="receiver_accesscode">{l s='Intercom Code' mod='tntofficiel'}</label>
                {* Code interphone *}
                <input class="form-control" type="text" id="receiver_accesscode" name="receiver_accesscode"
                       value="{$arrFormReceiverInfoValidate.fields.receiver_accesscode|escape:'htmlall':'UTF-8'}" maxlength="7" />
                {if $arrFormReceiverInfoValidate.fields.receiver_accesscode && array_key_exists('receiver_accesscode', $arrFormReceiverInfoValidate.errors)}
                    <small class="form-text alert-danger error-receiver_accesscode">{$arrFormReceiverInfoValidate.errors.receiver_accesscode|escape:'htmlall':'UTF-8'}.</small>
                {else}
                    <small class="form-text info-receiver_accesscode">7 caractères maximum</small>
                {/if}
            </div>
            <div class="form-group col-xs-12 col-sm-12 col-lg-4">
                <label for="receiver_floor">{l s='Floor' mod='tntofficiel'}</label>
                {* Etage *}
                <input class="form-control" type="text" id="receiver_floor" name="receiver_floor"
                       value="{$arrFormReceiverInfoValidate.fields.receiver_floor|escape:'htmlall':'UTF-8'}" maxlength="2" />
                {if $arrFormReceiverInfoValidate.fields.receiver_floor && array_key_exists('receiver_floor', $arrFormReceiverInfoValidate.errors)}
                    <small class="form-text alert-danger error-receiver_floor">{$arrFormReceiverInfoValidate.errors.receiver_floor|escape:'htmlall':'UTF-8'}.</small>
                {else}
                    <small class="form-text info-receiver_floor">2 caractères maximum</small>
                {/if}
            </div>
        </div>
        <div class="row">
            <div class="form-group col-xs-12 col-sm-12 col-lg-12">
                <label for="receiver_instructions">{l s='Special instructions' mod='tntofficiel'}<small class="form-text info-receiver_instructions">(30 caractères maximum)</small></label>
                {* Etage *}
                <input class="form-control" type="text" id="receiver_instructions" name="receiver_instructions"
                       value="{$arrFormReceiverInfoValidate.fields.receiver_instructions|escape:'htmlall':'UTF-8'}" maxlength="30" />
                {if $arrFormReceiverInfoValidate.fields.receiver_instructions && array_key_exists('receiver_instructions', $arrFormReceiverInfoValidate.errors)}
                    <small class="form-text alert-danger error-receiver_instructions">{$arrFormReceiverInfoValidate.errors.receiver_instructions|escape:'htmlall':'UTF-8'}.</small>
                {else}
                    <small class="info-receiver_instructions">
                        {l s='Indicate here where you want your package to be deposited in case of absence (mailbox, neighbor ...).'  mod='tntofficiel'}
                        {l s='In case of impossibility of delivery, you can give instructions or benefit from a 2nd presentation of the package the next day.'  mod='tntofficiel'}
                    </small>
                {/if}
            </div>
        </div>
        {*/if*}
        <p class="clearfix" style="min-height: 40px;margin: 3ex 0 1ex;clear: both;">
            <span class="required"></span> {l s='Required fields' mod='tntofficiel'}
            <a id="submitAddressExtraData" class="btn button button-tntofficiel-medium pull-right" {if $arrFormReceiverInfoValidate.length === 0} style="display: none;" {/if}>
                <span>{l s='Validate'  mod='tntofficiel'}</span>
            </a>
        </p>
    </div>
    {*/if*}
</div>



<script type="text/javascript">
{literal}

    // On Ready.
    window.TNTOfficiel_Ready = window.TNTOfficiel_Ready || [];
    window.TNTOfficiel_Ready.push(function (jQuery) {

        var $objDeliveryOptionRadioTNTList = TNTOfficiel_getCheckoutTNTRadio();
        $objDeliveryOptionRadioTNTList.each(function (intIndex, element) {

            var $objDeliveryOptionRadioTNTItem = jQuery(this);
            var $objDeliveryOptionItem = $objDeliveryOptionRadioTNTItem.closest('.delivery-option');

            var intTNTCarrierID = TNTOfficiel_getCheckoutTNTCarrierID($objDeliveryOptionRadioTNTItem);
            var $elmtTNTClickedCarrierDescription = jQuery('#TNTOfficielCarrier_' + intTNTCarrierID);

            var $elmtDstDelay = $objDeliveryOptionItem.find('.carrier-delay').first();
            var $elmtSrcDelay = $elmtTNTClickedCarrierDescription.find('.tntofficiel-delay').first();

            // If Delay found.
            if ($elmtDstDelay.length === 1) {
                $elmtDstDelay.replaceWith($elmtSrcDelay);
            }

            $elmtTNTClickedCarrierDescription.remove();
        });

        /**
         * Update TNT extra data form fields according to any carrier ID.
         *
         * @param intArgTNTCarrierID
         */
        function updateExtraDataDisplay(intArgTNTCarrierID) {
            var intTNTCarrierID = intArgTNTCarrierID|0;

            var strTNTAccountType = null;
            var strTNTCarrierType = null;

            if (window.TNTOfficiel
                && window.TNTOfficiel.carrier
                && window.TNTOfficiel.carrier.list
                && window.TNTOfficiel.carrier.list[intTNTCarrierID]
            ) {
                strTNTAccountType = window.TNTOfficiel.carrier.list[intTNTCarrierID].account_type;
                strTNTCarrierType = window.TNTOfficiel.carrier.list[intTNTCarrierID].carrier_type;
            }

            var $objTNTExtraDataForm = jQuery('#extra_address_data');

            // Is TNT Carrier.
            if (strTNTAccountType) {
                // Show.
                $objTNTExtraDataForm.show();

                // Essentiel Flexibilité.
                if (strTNTAccountType === 'LPSE ESSENTIEL') {
                    jQuery('#receiver_building, #receiver_accesscode, #receiver_floor').closest('.form-group').hide();
                    jQuery('#receiver_instructions').closest('.form-group').show();
                    jQuery('.special-receiver_instructions').show();
                } else {
                    jQuery('#receiver_instructions').closest('.form-group').hide();
                    jQuery('.special-receiver_instructions').hide();
                    if (strTNTCarrierType === 'INDIVIDUAL') {
                        jQuery('#receiver_building, #receiver_accesscode, #receiver_floor')
                        .closest('.form-group')
                        .show();
                    } else {
                        jQuery('#receiver_building, #receiver_accesscode, #receiver_floor')
                        .closest('.form-group')
                        .hide();
                    }
                }
            } else {
                // Hide.
                $objTNTExtraDataForm.hide();
            }

            //jQuery('#TNTOfficielCarrierExtra_'+intTNTClickedCarrierID).after($elmtTNTExtraDataForm);

            var $objDeliveryOptionRadioTNTList = TNTOfficiel_getCheckoutTNTRadio();

            $objDeliveryOptionRadioTNTList.closest('.delivery-option').closest('.delivery-options')
            .after($objTNTExtraDataForm);
        }


        var strSelectorReceiverInputItems = [
            '#extra_address_data #receiver_email',
            '#extra_address_data #receiver_phone',
            '#extra_address_data #receiver_building',
            '#extra_address_data #receiver_accesscode',
            '#extra_address_data #receiver_floor',
            '#extra_address_data #receiver_instructions'
        ].join(',');

        jQuery(strSelectorReceiverInputItems)
        .off('change.' + window.TNTOfficiel.module.name)
        .on('change.' + window.TNTOfficiel.module.name, function (objEvent) {
            // Prevent bubbling plus further handlers to execute.
            objEvent.stopImmediatePropagation();
            // Prevent default action.
            objEvent.preventDefault();

            return false;
        });

        var intTNTCarrierID = TNTOfficiel_getCheckoutTNTCarrierID();

        updateExtraDataDisplay(intTNTCarrierID);

        // Click on a checkout step.
        prestashop.on('changedCheckoutStep', function (objParam) {
            var $PersonalInfoStep = jQuery('#checkout-personal-information-step'),
            $AddressesStep = jQuery('#checkout-addresses-step'),
            $DeliveryStep = jQuery('#checkout-delivery-step'),
            $PaymentStep = jQuery('#checkout-payment-step'),
            elmntPersonalInfoStep = $PersonalInfoStep.length ? $PersonalInfoStep[0] : null,
            elmntAddressesStep = $AddressesStep.length ? $AddressesStep[0] : null,
            elmntDeliveryStep = $DeliveryStep.length ? $DeliveryStep[0] : null,
            elmntPaymentStep = $PaymentStep.length ? $PaymentStep[0] : null;

            // Delivery step selected and will be displayed.
            if (elmntDeliveryStep === objParam.event.delegateTarget) {
                // Remove hidden element from payment step, to allow user selection in delivery step.
                jQuery('.js-cart-payment-step-refresh').remove();
            }
        });

        // Click on a TNT carrier.
        prestashop.on('updatedDeliveryForm', function (objParam) {
            var $objDeliveryOptionRadioTNTList = TNTOfficiel_getCheckoutTNTRadio();
            //var $objDeliveryOptionRadioTNTClick = TNTOfficiel_getCheckoutTNTRadio(true);
            var $objDeliveryOptionRadioTNTClick = $objDeliveryOptionRadioTNTList
            .filter(function (intIndex, element) {
                return (objParam.deliveryOption.length === 1 && jQuery.contains(objParam.deliveryOption[0], this));
            });

            // If selection is TNT.
            if ($objDeliveryOptionRadioTNTClick.length !== 1) {
                //var xx = TNTOfficiel_PageSpinner(2 * 1000);
                //xx.hide();
            }

            var intTNTCarrierID = TNTOfficiel_getCheckoutTNTCarrierID($objDeliveryOptionRadioTNTClick);

            updateExtraDataDisplay(intTNTCarrierID);

            // Display pop-in to select delivery point only for DROPOFFPOINT or DEPOT.
            TNTOfficiel_XHRBoxDeliveryPoints(intTNTCarrierID);

            TNTOfficiel_updatePaymentDisplay();

            // Remove error message.
            jQuery('#hook-display-before-carrier p.alert-danger').remove();
        });

        // Update on display.
        TNTOfficiel_updatePaymentDisplay();
    });

{/literal}
</script>

{if $arrDump|count > 0}
<pre style="font-size: 11px;line-height: 1.2em;">
{foreach from=$arrDump key=strKey item=mxdItem}
<b>{TNTOfficiel_Tools::encJSON($strKey)}</b> : {TNTOfficiel_Tools::encJSON($mxdItem, TNTOfficiel_Tools::JSON_PRETTY_MULTILINE)}
{/foreach}
</pre>
{/if}
