{**
* TNT OFFICIAL MODULE FOR PRESTASHOP.
*
* @author    Inetum <inetum.com>
* @copyright 2016-2024 Inetum, 2016-2024 TNT
* @license   https://opensource.org/licenses/MIT MIT License
*}

{assign var=arrObjTNTParcelModelList value=$objTNTOrderModel->getTNTParcelModelList()}
{assign var=isExpeditionCreated value=$objTNTOrderModel->isExpeditionCreated()}
{assign var=isUpdateParcelsStateAllowed value=$objTNTOrderModel->isUpdateParcelsStateAllowed()}
{assign var=isAccountInsuranceEnabled value=$objTNTOrderModel->isAccountInsuranceEnabled()}

<div id="formAdminParcelsPanel" class="panel card">
    <div class="panel-heading card-header">
        <i class="icon-tnt"></i>
        {l s='parcels' mod='tntofficiel'} <span class="badge">{$arrObjTNTParcelModelList|@count}</span> {if $strPickUpNumber}<span class="badge">{l s='Pickup number: ' mod='tntofficiel'} {$strPickUpNumber|escape:'htmlall':'UTF-8'}</span>{/if}
        <span class="badge">{l s='Total weight: ' mod='tntofficiel'} <span id="total-weight">{$objTNTOrderModel->getWeight()|escape:'html':'UTF-8'}</span> {l s='Kg' mod='tntofficiel'}</span>
        {if $isAccountInsuranceEnabled}
        <span class="badge">{l s='Total Insurance: ' mod='tntofficiel'} <span id="total-insurance_amount">{$objTNTOrderModel->getInsuranceAmount()|escape:'html':'UTF-8'}</span> {l s='€' mod='tntofficiel'}</span>
        {/if}
    </div>
    <div class="table-responsive">
        <table class="table" id="parcelsTable">
            <thead>
            <tr>
                <th style="width: 5ex;"><span class="title_box ">{l s='N°' mod='tntofficiel'}</span></th>
                <th style="width: 18ex;"><span class="title_box">{l s='Weight' mod='tntofficiel'}</span></th>
                {if $isAccountInsuranceEnabled}
                <th style="width: 18ex;"><span class="title_box">{l s='Insurance Amount' mod='tntofficiel'}</span></th>
                {/if}
                <th style="width: 26ex;"><span class="title_box">{l s='Tracking number' mod='tntofficiel'}</span></th>
                <th><span class="title_box">{l s='Status' mod='tntofficiel'}</span></th>
                <th style="width: 18ex;"><span class="title_box">{l s='PDL' mod='tntofficiel'}</span></th>
                <th style="width: 14ex;"><span class="title_box"></span></th>
            </tr>
            </thead>
            <tbody id="parcelsTbody">
            {assign var=counter value=1}
            {foreach from=$arrObjTNTParcelModelList item=objTNTParcelModel key=intTNTParcelIndex}
                {assign var=isParcelCreated value=$objTNTParcelModel->parcel_number != ''}
                <tr class="current-edit hidden-print" id="row-parcel-{$objTNTParcelModel->id|intval}">
                    <td>
                        <div class="input-group">
                            {$counter++|intval}
                        </div>
                    </td>
                    <td>
                        {assign var=feedback value=$objTNTParcelModel->setWeight()}
                        <div class="{if is_string($feedback)}has-error{/if}" style="float:left;margin-right:3px;">
                            <input id="parcelWeight-{$objTNTParcelModel->id|intval}"
                                   value="{$objTNTParcelModel->weight|escape:'htmlall':'UTF-8'}"
                                   class="form-control"
                                   {if $isParcelCreated}disabled="disabled"{/if}
                            />
                            {if is_string($feedback)}
                            <div class="invalid-feedback">
                                {$feedback}
                            </div>
                            {/if}
                        </div>
                    </td>
                    {if $isAccountInsuranceEnabled}
                    <td>
                        {assign var=feedback value=$objTNTParcelModel->setInsuranceAmount()}
                        <div class="{if is_string($feedback)}has-error{/if}" style="float:left;margin-right:3px;">
                            <input name="parcelInsuranceAmount" id="parcelInsuranceAmount-{$objTNTParcelModel->id|intval}"
                                   value="{$objTNTParcelModel->insurance_amount|escape:'htmlall':'UTF-8'}"
                                   class="form-control"
                                   {if $isParcelCreated}disabled="disabled"{/if}
                            />
                            {if is_string($feedback)}
                                <div class="invalid-feedback">
                                    {$feedback}
                                </div>
                            {/if}
                        </div>
                    </td>
                    {/if}
                    <td>
                        {if $objTNTParcelModel->parcel_number != ''}
                            {if $objTNTParcelModel->tracking_url != ''}
                                <a href="{$objTNTParcelModel->tracking_url|escape:'html':'UTF-8'}" target="_blank">
                                    {$objTNTParcelModel->parcel_number|escape:'htmlall':'UTF-8'}
                                    <i class="icon-external-link"></i>
                                </a>
                            {else}
                                {$objTNTParcelModel->parcel_number|escape:'htmlall':'UTF-8'}
                            {/if}
                        {else}
                            -
                        {/if}
                    </td>
                    <td>
                        {if $objTNTParcelModel->stage_id > 0}
                            <span class="label color_field"
                                  style="color:white;background-color:{$objTNTParcelModel->getStageColor()|escape:'html':'UTF-8'};">
                        {/if}
                            {$objTNTParcelModel->getStageLabel()|escape:'htmlall':'UTF-8'}
                        {if $objTNTParcelModel->stage_id > 0}
                            </span>
                        {/if}
                    </td>
                    <td>
                        {if $objTNTParcelModel->pod_url != ''}
                            <a href="{$objTNTParcelModel->pod_url|escape:'html':'UTF-8'}" target="_blank">
                                <button class="btn btn-default" >
                                    <i class="icon-search" title="{l s='see' mod='tntofficiel'}"></i>
                                    <span>{l s='see' mod='tntofficiel'}</span>
                                </button>
                            </a>
                        {else}
                            -
                        {/if}
                    </td>
                    <td class="actions">
                        {if !$isParcelCreated}
                            <div class="pull-right">
                                <button name="updateParcel"
                                        class="btn btn-default btn-text tooltip-link"
                                        value="{$objTNTParcelModel->id|intval}">
                                    <i class="material-icons" title="{l s='Update' mod='tntofficiel'}">save</i>
                                </button>&nbsp;
                                <button name="removeParcel"
                                        class="btn btn-default btn-text tooltip-link"
                                        value="{$objTNTParcelModel->id|intval}">
                                    <i class="material-icons" title="{l s='Delete' mod='tntofficiel'}">remove</i>
                                </button>
                            </div>
                        {/if}
                    </td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
    <div class="row row-margin-bottom row-margin-top">
        <div class="col-lg-7">
        </div>
        <div class="col-lg-5">
            {if !$isExpeditionCreated}
                <button name="addParcel"
                        class="btn button btn-text tooltip-link button-tntofficiel-small pull-right">
                    <span>
                        <i class="material-icons" title="{l s='add' mod='tntofficiel'}">add</i>
                        {l s='add' mod='tntofficiel'}
                    </span>
                </button>
            {/if}
            {if $isExpeditionCreated && $isUpdateParcelsStateAllowed}
                <button name="updateOrderStateDeliveredParcels"
                        class="btn btn-default btn-text tooltip-link pull-right"
                        title="{l s='Update parcels status' mod='tntofficiel'}"
                >
                    <i class="material-icons" title="{l s='refresh' mod='tntofficiel'}">refresh</i>
                    {l s='refresh' mod='tntofficiel'}
                </button>
            {/if}
        </div>
    </div>
</div>