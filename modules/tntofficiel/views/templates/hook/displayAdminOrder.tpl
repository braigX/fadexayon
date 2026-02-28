{**
* TNT OFFICIAL MODULE FOR PRESTASHOP.
*
* @author    Inetum <inetum.com>
* @copyright 2016-2024 Inetum, 2016-2024 TNT
* @license   https://opensource.org/licenses/MIT MIT License
*}


<script type="text/javascript">
    {literal}

    // On Ready.
    window.TNTOfficiel_Ready = window.TNTOfficiel_Ready || [];
    window.TNTOfficiel_Ready.push(function (jQuery) {
        TNTOfficiel.alert = TNTOfficiel.alert || [];
        {/literal}
        {foreach $arrAdminMessageList as $strType => $arrMessageTypeList}
        {foreach $arrMessageTypeList as $strMessage }
        if (TNTOfficiel_isType(TNTOfficiel.alert.{$strType}, 'array') === true) {
            TNTOfficiel.alert.{$strType}.push('{$strMessage|escape:'javascript':'UTF-8'}');
        }
        {/foreach}
        {/foreach}
        {literal}
    });

    {/literal}
</script>

<div class="row"><div class="col-lg-12">
        <div id="TNTOfficelAdminOrdersViewOrder" class="panel card">

            <div class="panel-heading card-header">
                <i class="icon-tnt"></i>
                {l s='TNT' mod='tntofficiel'}
            </div>

            <div class="card-body">

                {if !$boolDisplayNew}
                    <div id="TNTOfficielOrderWellButton" class="well info-block mb-2 hidden-print">
                        {if $isExpeditionCreated}
                            <a class="btn btn-default {if ($strBTLabelName == '')}disabled{/if}"
                               href="{$hrefDownloadBT|escape:'html':'UTF-8'}"
                               title="{$strBTLabelName|escape:'html':'UTF-8'}"
                               target="_blank"
                            >
                                <i class="icon-tnt"></i>
                                {l s='TNT Transport Ticket' mod='tntofficiel'}
                            </a>
                        {else}
                            <span class="span label label-inactive">
                <i class="icon-remove"></i>
                {l s='TNT Transport Ticket' mod='tntofficiel'}
            </span>
                        {/if}
                        &nbsp;
                        <a class="btn btn-default"
                           href="{$hrefGetManifest|escape:'html':'UTF-8'}"
                           title="{l s='Manifest' mod='tntofficiel'}"
                        >
                            <i class="icon-tnt"></i>
                            {l s='TNT Manifest' mod='tntofficiel'}
                        </a>
                        &nbsp;
                        {if $isExpeditionCreated}
                            <a class="btn btn-default"
                               href="javascript:void(0);"
                               onclick="window.open('{$hrefTracking|escape:'html':'UTF-8'}', 'Tracking', 'menubar=no, scrollbars=yes, top=100, left=100, width=900, height=600');"
                            >
                                <i class="icon-tnt"></i>
                                {l s='TNT Tracking' mod='tntofficiel'}
                            </a>
                        {else}
                            <span class="span label label-inactive">
                <i class="icon-remove"></i>
                {l s='TNT Tracking' mod='tntofficiel'}
            </span>
                        {/if}
                        &nbsp;
                    </div>
                {/if}

                <div class="">
                    <div class="row">
                        <div id="TNTOfficielSection2" class="col-lg-7">

                            {include '../admin/displayOrderDeliveryPoint.tpl' isExpeditionCreated=$isExpeditionCreated strDeliveryPointType=$strDeliveryPointType strDeliveryPointCode=$strDeliveryPointCode arrDeliveryPoint=$arrDeliveryPoint objPSAddressDelivery=$objPSAddressDelivery}

                        </div>
                        <div id="TNTOfficielSection3" class="col-lg-5">

                            {include '../admin/displayOrderReceiverInfo.tpl' isExpeditionCreated=$isExpeditionCreated strDeliveryPointType=$strDeliveryPointType arrFormReceiverInfoValidate=$arrFormReceiverInfoValidate}

                        </div>
                    </div>
                </div>


                <div class="">
                    <div class="row">
                        <div class="col-lg-7">

                            {include '../admin/displayAjaxUpdateOrderParcels.tpl' objTNTOrderModel=$objTNTOrderModel strPickUpNumber=$strPickUpNumber}

                        </div>
                        <div class="col-lg-5">

                            <div id="formAdminShippingDatePanel" class="panel card">
                                <div class="panel-heading card-header">
                                    <i class="icon-calendar"></i>
                                    {l s='Shipping date' mod='tntofficiel'}
                                </div>
                                <div class="table-responsive">
                                    <table class="table" id="parcelsTable">
                                        <thead>
                                        <tr>
                                            <th><span class="title_box ">{l s='Shipping date' mod='tntofficiel'}</span></th>
                                            <th><span class="title_box ">{l s='Due date' mod='tntofficiel'}</span></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>
                                                <div class="input-group fixed-width-xxl" style="float:left;margin-right:3px;">
                                                    <div id="shipping_date_alt" class="form-control" ></div>
                                                    <span class="input-group-addon"><i class="icon-calendar-empty"></i></span>
                                                    <input type="text" name="shipping_date" id="shipping_date" value=""
                                                           class="form-control"
                                                           style="visibility: hidden;position: absolute;left: 0;top: 0;"
                                                    />
                                                </div>
                                                <div id="delivery-date-error" class="input-group" style="display: none">
                                                    <div class="alert alert-danger alert-danger-small"><p></p></div>
                                                </div>
                                                <div id="delivery-date-warning" class="input-group" style="display: none">
                                                    <div class="alert alert-warning alert-warning-small"><p></p></div>
                                                </div>
                                            </td>
                                            <td id="due-date">
                                                {$strDueDateFormatted|escape:'htmlall':'UTF-8'}
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                {if $strPickupDateRegistered}
                                    <div class="row-margin-top">
                                        <b>{l s='A pickup request has been made for %s' mod='tntofficiel' sprintf=[$strPickupDateRegistered|escape:'html':'UTF-8']}.</b>
                                    </div>
                                {/if}
                            </div>

                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div></div>

<script type="text/javascript">

    // Before Ready.
    (function () {

        {literal}
        window.TNTOfficiel.order.isTNT = true;
        window.TNTOfficiel.order.isDirectAddressCheck = {/literal}{if $boolDirectAddressCheck}true{else}false{/if}{literal};
        window.TNTOfficiel.order.isExpeditionCreated = {/literal}{if $isExpeditionCreated}true{else}false{/if}{literal};
        window.TNTOfficiel.order.intOrderID = {/literal}{$objPSOrder->id|intval|escape:'javascript':'UTF-8'}{literal};
        window.TNTOfficiel.order.intCarrierID = {/literal}{$objPSOrder->id_carrier|intval|escape:'javascript':'UTF-8'}{literal};
        window.TNTOfficiel.order.isCarrierDeliveryPoint = {/literal}{if $strDeliveryPointType !== null}true{else}false{/if}{literal};
        {/literal}

        window.TNTOfficiel.order.objDatePickupStart = new Date("{$intDatePickupStart|escape:'javascript':'UTF-8'}"*1000);
        // Optional pickup date.
        {if $intDatePickupShipping}
        window.TNTOfficiel.order.objDatePickupShipping = new Date("{$intDatePickupShipping|escape:'javascript':'UTF-8'}"*1000);
        {/if}

    })();

</script>