{**
* TNT OFFICIAL MODULE FOR PRESTASHOP.
*
* @author    Inetum <inetum.com>
* @copyright 2016-2024 Inetum, 2016-2024 TNT
* @license   https://opensource.org/licenses/MIT MIT License
*}

{if $strDeliveryPointType !== null}
    <div class="panel card">
        <div class="panel-heading card-header">
            <i class="icon-tnt"></i> {l s='TNT Delivery Address' mod='tntofficiel'}
        </div>
        <div class="clearfix card-body">
            <div class="row clearfix">
                <div class="col-sm-8"><div class="info-block">
                    {if $strDeliveryPointCode !== null && $arrDeliveryPoint}
                        <b>{$arrDeliveryPoint['name']|escape:'htmlall':'UTF-8'}</b><br />
                        {if $strDeliveryPointType === 'xett'}
                            {$arrDeliveryPoint['address']|escape:'htmlall':'UTF-8'}
                        {else}
                            {$arrDeliveryPoint['address1']|escape:'htmlall':'UTF-8'}<br />
                            {$arrDeliveryPoint['address2']|escape:'htmlall':'UTF-8'}
                        {/if}
                        <br />
                        {$arrDeliveryPoint['postcode']|escape:'htmlall':'UTF-8'} {$arrDeliveryPoint['city']|escape:'htmlall':'UTF-8'}<br />
                        {l s='France' mod='tntofficiel'}
                    {else}
                        {* Smarty registered Prestashop methode AddressFormat::generateAddressSmarty() *}
                        {displayAddressDetail address=$objPSAddressDelivery newLine='<br />'}
                    {/if}
                    </div></div>
                <div class="col-sm-4 text-center">
                    {if !$isExpeditionCreated}
                        <button type="button"
                                class="btn button button-tntofficiel-small tntofficiel-shipping-method-info-select"
                        ><span><i class="icon-pencil"></i> &nbsp;{if $strDeliveryPointCode !== null}{l s='Change' mod='tntofficiel'}{else}{l s='Select' mod='tntofficiel'}{/if}</span></button>
                        <br />
                    {/if}
                    {if $strDeliveryPointCode !== null}
                        {l s='Code' mod='tntofficiel'}: <b>{$strDeliveryPointCode|escape:'htmlall':'UTF-8'}</b>
                    {/if}
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-sm-8"><div class="info-block">
                    {if $strDeliveryPointCode !== null && $arrDeliveryPoint}
                        <b>{l s='Schedules' mod='tntofficiel'} :</b><br />
                        {foreach from=$arrDeliveryPoint['schedule'] key=day item=schedule}
                            <span class="weekday">{l s=$day mod='tntofficiel'}:</span>
                            {if !empty($schedule)}
                                {assign var='i' value=0}
                                {foreach from=$schedule item=part}
                                    <span>{' - '|implode:$part|escape:'htmlall':'UTF-8'}</span>
                                    {if ($schedule|@count) > 1 and $i < (($schedule|@count) -1)}
                                        <span>{l s='and' mod='tntofficiel'}</span>
                                    {/if}
                                    {assign var='i' value=$i+1}
                                {/foreach}
                                <br />
                            {else}
                                <span>{l s='Closed' mod='tntofficiel'}</span>
                                <br />
                            {/if}
                        {/foreach}
                    {/if}
                    </div></div>
                <div class="col-sm-4 hidden-print">
                    <div id="map-delivery-point-canvas"></div>
                </div>
            </div>
        </div>
    </div>
{/if}