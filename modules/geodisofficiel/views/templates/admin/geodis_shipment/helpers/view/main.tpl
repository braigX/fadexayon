{*
* 2018 GEODIS
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    GEODIS <contact@geodis.com>
*  @copyright 2018 GEODIS
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}

{$menu}

{if (!$hasError)}

{foreach $shipmentSuccess as $success}
    <div class="col-lg-12 js-success">
        <div class="alert alert-success" role="alert">
                <p>{$success|escape:'htmlall':'UTF-8'}</p>
        </div>
    </div>
{/foreach}

{foreach $shipmentErrors as $error}
    <div class="col-lg-12 js-error">
        <div class="alert alert-danger" role="alert">
            <p>{$error|escape:'htmlall':'UTF-8'}</p>
        </div>
    </div>
{/foreach}

<div class="col-lg-12 js_alertDayOff">
    <div class="alert alert-info">
        <p>{__ s='Admin.Shipment.index.warning.daysOff'}</p>
    </div>
</div>

<div class="col-lg-12 js_alertNoCarrierAvailable">
    <div class="alert alert-info">
        <p>{__ s='Admin.shipment.index.warning.noCarrierAvailable'}</p>
    </div>
</div>

<div class="col-lg-12 js-alertNoGroupCarrierAvailable">
    <div class="alert alert-info">
        <p>{__ s='Admin.shipment.index.warning.noGroupCarrierAvailable'}</p>
    </div>
</div>

<div class="col-lg-12 js_alertWl">
    <div class="alert alert-info">
        <p>{__ s='Admin.shipment.index.warning.wsDisabled'}</p>
    </div>
</div>

<div class="col-lg-12 js_alertWs">
    <div class="alert alert-info">
        <p>{__ s='Admin.ShipmentController.index.error.unavailableWSDate'}</p>
    </div>
</div>

<div class="col-lg-12 js-alertPrinter">
    <div class="alert alert-info">
        <p>{__ s='Admin.shipment.index.thermalPrinting.error'}</p>
    </div>
</div>

<div class="col-lg-12 js-alertWsPrinter">
    <div class="alert alert-info">
        <p>{__ s='Admin.ShipmentController.index.error.unvailableWSPrint'}</p>
    </div>
</div>

<div class="col-lg-12 js-alertModule">
    <div class="alert alert-info">
        <p>{__ s='Admin.shipment.index.module.error'}</p>
    </div>
</div>

<div class="col-lg-12 js-alertWsPrestation">
    <div class="alert alert-info">
        <p>{__ s='Admin.shipment.index.error.checkPrestation'}</p>
    </div>
</div>

<div class="panel col-md-12">
    <div class="panel-heading">
        {if $shipment->id}
            <p class="title">{__ s='Admin.Shipment.index.editTitle' vars=[($shipment->reference)|escape:'htmlall':'UTF-8', ($order->reference)|escape:'htmlall':'UTF-8']}</p>
        {else}
            <p class="title">{__ s='Admin.Shipment.index.createTitle' vars=[($order->reference)|escape:'htmlall':'UTF-8']}</p>
        {/if}
    </div>

    <form class="form" method="POST" action="{$formUrl|escape:'htmlall':'UTF-8'}">
        <div class="form--carrier form-row col-md-12">
            <div class="form-group col-md-3">
                <label for="groupCarrier" class="">{__ s='Admin.index.groupCarrier'}</label>
                <select id="groupCarrier" name="groupCarrier" class="form-control js-groupCarrierSelect">
                    {foreach $groupCarrierCollection as $groupCarrier}
                        <option
                            value="{($groupCarrier->id)|escape:'htmlall':'UTF-8'}"
                            data-delay="{($groupCarrier->preparation_delay)|escape:'htmlall':'UTF-8'}"
                            data-reference="{($groupCarrier->reference)|escape:'htmlall':'UTF-8'}"
                        >
                            {($groupCarrier->getCarrier()->name)|escape:'htmlall':'UTF-8'}
                        </option>
                    {/foreach}
                </select>
            </div>
            <div class="form-group col-md-3 js-divCarrierSelect">
                <label for="carrier" class="">{__ s='Admin.index.carrier'}</label>
                <select id="carrier" name="carrier" class="form-control js-carrierSelect">
                </select>
            </div>

            <div class="form-group col-md-3 js-divRemovalDate">
                <label for="removal_date">{__ s='Admin.Shipment.index.removalDate'}</label>
                <input type="text" id="datepicker" name="removal_date" class="form-control js-removalDate" value="{($shipment->departure_date)|escape:'htmlall':'UTF-8'}"/>
            </div>

            <input type="hidden" name="firstDayAvailable" class="js-firstDayAvailable" value="" />
        </div>


        <div class="js-package">
           <input type="hidden" name="" value="" class="js-remove_package"/>
           <h2>{__ s='Admin.Shipment.index.packageReference'}<span class="js-package_reference_label"></span></h2>
           <input type="hidden" class="js-package_reference" name="" value="" />
           <input type="hidden" name="" value="" class="js-package_id" />
           <div class="form--package_infos">
               <div class="form-group col-md-12">
                   <div class=" form-row input-group  col-md-12">
                       <label style="width:80px" for="height">{__ s='Admin.Shipment.index.placeholder.height'}</label>
                       <label style="width:80px" for="width">{__ s='Admin.Shipment.index.placeholder.width'}</label>
                       <label style="width:80px" for="Weight">{__ s='Admin.Shipment.index.placeholder.depth'}</label>
                       <label style="width:80px" for="depth">{__ s='Admin.Shipment.index.placeholder.weight'}</label>
                       <label style="width:80px" for="depth">{__ s='Admin.Shipment.index.placeholder.volume'}</label>
                    </div>
                    <div class=" form-row input-group col-md-12" style="z-index:1;">
                       <input type="text" name="" class="form--package_infos-input js-height js-float form-control" placeholder="{__ s='Admin.Shipment.index.placeholder.height'}" value=""/>
                       <input type="text" name="" class="form--package_infos-input js-width js-float form-control" placeholder="{__ s='Admin.Shipment.index.placeholder.width'}" value="" />
                       <input type="text" name="" class="form--package_infos-input js-depth js-float form-control" placeholder="{__ s='Admin.Shipment.index.placeholder.depth'}" value="" />
                       <input type="text" name="" class="form--package_infos-input js-weight js-float form-control" placeholder="{__ s='Admin.Shipment.index.placeholder.weight'}" value="" />
                       <input type="text" name="" class="form--package_infos-input js-volume js-float form-control" placeholder="{__ s='Admin.Shipment.index.placeholder.volume'}" value="" />
                       <select name="" class="form--package_infos-input js-package_type form-control">
                           <option value="box" >Box</option>
                           <option value="pallet">Pallet</option>
                       </select>
                       <button type="button" name="remove_package" class="js-button_remove btn btn-primary">{__ s='Admin.Shipment.index.button.removePackage'}</button>

                   </div>
               </div>
           </div>

           <div class="form--package_infos">
           </div>

            <div class="form--package_infos-div_error">
                {foreach $packageErrors as $packageNumber => $errors}
                    {foreach $errors as $error}
                        <p data-package="{$packageNumber|escape:'htmlall':'UTF-8'}" class="form--div_error-error">{$error|escape:'htmlall':'UTF-8'}</p>
                    {/foreach}
                {/foreach}
            </div>

            <div class="">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th scope="col">{__ s='Admin.Shipment.index.quantity'}</th>
                            <th scope="col">{__ s='Admin.Shipment.index.reference'}</th>
                            <th scope="col">{__ s='Admin.Shipment.index.combinationReference'}</th>
                            <th scope="col">{__ s='Admin.Shipment.index.name'}</th>
                            <th scope="col">{__ s='Admin.Shipment.index.vs'}</th>
                        </tr>
                    </thead>
                    <tbody class="js-items">
                        <tr class="js-item">
                            <td>
                                <input type="checkbox" name="checkbox" value="1" class="hidden js-product_selected" />
                                <input type="hidden" name="" value="" class="js-id_order_package_detail" />
                                <select class="js-quantity"></select>
                            </td>
                            <td  class="js-item_reference"></td>
                            <td><p class="js-combination_reference"></p></td>
                            <td><p class="js-item_name"></p></td>
                            <td>
                                <i class="icon-pencil js-wl wl-status"></i>
                                <div class="js-popin-no-wl popin-no-wl">
                                    <div class="geodisPopin bootstrap">
                                        <div class="form-row">
                                            <div class="col-md-12">
                                                <p>{__ s='Admin.Shipment.index.warning.noWlAccepted'}</p>
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="col-md-12">
                                                <button class="js-no-wl-close btn btn-primary pull-right">{__ s='Admin.Shipment.index.button.back'}</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="js-popin-wl popin-wl">
                                    <div class="geodisPopin bootstrap">
                                        <div class="form-row">
                                            <div class="col-md-12">
                                                <button class="js-wl-cancel btn btn-primary pull-right popin-button">{__ s='Admin.Shipment.index.wl.cancel.label'}</button>
                                                <button class="js-wl-submit btn btn-primary pull-right popin-button">{__ s='Admin.Shipment.index.wl.label'}</button>
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-4">
                                                <label for="id_fiscal_code" class="js-product_id_fiscal_code">{__ s='Admin.Shipment.index.fiscalCode.label'}</label>
                                                <select name="id_fiscal_code" class="form-control js-product_id_fiscal_code">
                                                    <option value="0"></option>
                                                    {foreach $fiscalCodeCollection as $fiscalCode}
                                                        <option value="{($fiscalCode->id)|escape:'htmlall':'UTF-8'}">{$fiscalCode->label|escape:'htmlall':'UTF-8'}</option>
                                                    {/foreach}
                                                </select>
                                            </div>
                                            <div class="form-group col-md-4 js-wl-row-nb_col">
                                                <label for="nb_col" class="js-product_nb_col">{__ s='Admin.Shipment.index.nbCol.label'}</label>
                                                <input type="text" class="js-product_nb_col form-control" id="nb_col" placeholder="{__ s='Admin.Shipment.index.nbCol.placeholder'}" maxlength="5" />
                                            </div>
                                            <div class="form-group col-md-4 js-wl-row-volume_cl">
                                                <label for="volume_cl" class="js-product_volume_cl">{__ s='Admin.Shipment.index.volumeCl.label'}</label>
                                                <select class="js-product_volume_cl form-control" id="volume_cl">
                                                    {foreach $volumes as $volume}
                                                        <option value="{($volume->key)|escape:'htmlall':'UTF-8'}">{($volume->label)|escape:'htmlall':'UTF-8'}</option>
                                                    {/foreach}
                                                </select>
                                            </div>
                                            <div class="form-group col-md-4 js-wl-row-volume_l">
                                                <label for="volume_l" class="js-product_volume_l">{__ s='Admin.Shipment.index.volumeL.label'}</label>
                                                <input type="text" class="js-product_volume_l form-control" id="volume_l" placeholder="{__ s='Admin.Shipment.index.volumeL.placeholder'}" />
                                            </div>
                                            <div class="form-group col-md-4">
                                                <div class="js-wl-row-fiscal_code_ref">
                                                    <label for="fiscal_code_ref" class="js-product_fiscal_code_ref">{__ s='Admin.Shipment.index.fiscalCodeRef.label'}</label>
                                                    <input type="text" class="js-product_fiscal_code_ref form-control" id="fiscal_code_ref" placeholder="{__ s='Admin.Shipment.index.fiscalCodeRef.placeholder'}" maxlength="30" />
                                                </div>
                                                <div class="js-wl-row-n_mvt">
                                                    <label for="n_mvt" class="js-product_n_mvt">{__ s='Admin.Shipment.index.nMvt.label'}</label>
                                                    <input type="text" class="js-product_n_mvt form-control" id="n_mvt" placeholder="{__ s='Admin.Shipment.index.nMvt.placeholder'}" maxlength="30" />
                                                </div>
                                            </div>
                                            <div class="form-group col-md-4 js-wl-row-shipping_duration">
                                                <label for="shipping_duration" class="js-product_shipping_duration">{__ s='Admin.Shipment.index.shippingDuration.label'}</label>
                                                <input type="text" class="js-product_shipping_duration form-control" id="shipping_duration" placeholder="{__ s='Admin.Shipment.index.shippingDuration.placeholder'}" maxlength="2" />
                                            </div>
                                            <div class="form-group col-md-4 js-wl-row-n_ea">
                                                <label for="n_ea" class="js-product_n_ea">{__ s='Admin.Shipment.index.nEa.label'}</label>
                                                <input type="text" class="js-product_n_ea form-control" id="n_ea" placeholder="{__ s='Admin.Shipment.index.nEa.placeholder'}" maxlength="13"/>
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="col-md-12">
                                                <div class="js-error alert alert-danger"></div>
                                                <div class="js-warning alert alert-warning"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                </div>

            <div class="form--product_infos-div_error">
                    {foreach $productErrors as $error}
                    <p class="form--div_error-error">{$error|escape:'htmlall':'UTF-8'}</p>
                    {/foreach}
            </div>
        </div>

        <div class="js-form-content"></div>


        <div class="panel-footer">
            <div class="container  col-md-12">
                <div class="row">
                    <a id="add_package_button" class="btn btn-primary pull-right js-button_add" href="#" name="add_package">{__ s='Admin.Shipment.index.addPackage'}</a>
                </div>
                <br />
                <div class="row">
                    <div class="col">
                        {if (!$shipment->is_complete)}
                            <button type="submit" name="submit" class="btn btn-primary pull-right js-submit">{__ s='Admin.Shipment.index.submit'}</button>
                        {/if}
                        {if (!$shipment->is_complete)}
                            <button type="submit" name="submitandnew" class="js-save-and-new btn btn-primary pull-right js-submitAndNew">{__ s='Admin.Shipment.index.submitAndNew'}</button>
                        {/if}
                        <button type="button" class=" btn btn-default pull-left" onclick="window.location.href='{$orderGridLink|escape:'htmlall':'UTF-8'}'">{__ s='Admin.Shipment.index.cancel'}</button>
                    </div>
                </div>
                <br />
                <div class="row">
                    <div class="col">
                        {if $shipment->id && $shipment->is_complete}
                            <a href="{$printDeliveryControllerUrl|escape:'htmlall':'UTF-8'}" class="btn btn-default pull-right js-printDelivery">{__ s='Admin.Shipment.index.printDelivery'}</a>
                        {/if}
                        {if $shipment->id && $shipment->recept_number}
                            <a href="{$printlabelControllerUrl|escape:'htmlall':'UTF-8'}" class="btn btn-default pull-right js-printLabel" onclick="jQuery('.js-transmit').removeClass('disabled'); return true">{__ s='Admin.Shipment.index.printLabel'}</a>
                        {/if}
                    </div>
                </div>
                <br />
                <div class="row">
                    <div class="col">
                        {if (!$shipment->is_complete)}
                            {if !$shipment->is_label_printed}
                                <span class="pull-right help-box" data-placement="left" data-toggle="popover" data-trigger="click" data-content="{$tooltip|escape:'htmlall':'UTF-8'}"></span>
                            {else}
                                <span class="js-tooltip pull-right help-box hidden" data-placement="left" data-toggle="popover" data-trigger="click" data-content="{$tooltipFormUpdated|escape:'htmlall':'UTF-8'}"></span>
                            {/if}
                            <button
                                type="submit"
                                name="send"
                                class="btn btn-primary pull-right {if !$shipment->is_complete} incomplete{/if} {if !$shipment->is_label_printed}disabled{/if} js-transmit"
                                onclick="return confirm('{__ s='Admin.Shipment.index.send.confirm'}')"
                                title="{__ s='Admin.Shipment.index.send.confirm'}"
                            >
                                {__ s='Admin.Shipment.index.send.label'}
                            </button>
                        {/if}
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

{/if}
