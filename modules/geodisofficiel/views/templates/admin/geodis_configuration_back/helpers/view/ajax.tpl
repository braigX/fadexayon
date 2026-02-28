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

<div class="js-template">
    {foreach $groupCarrierCollection as $groupKey => $group}
        {if $group.active}
            <div class="panel">
                <div class="row">
                    <div class="col-lg-112">
                        <h2>{__ s="Admin.ConfigurationBack.ajax.group.name.%s" vars=[($group.name)|escape:'htmlall':'UTF-8']}</h2>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-lg-6">
                        <div class="col-lg-8">
                            <label for="preparation_delay-{$groupKey|escape:'htmlall':'UTF-8'}">{__ s="Admin.ConfigurationBack.ajax.preparationDelay.label"}</label>
                            <input type="text" data-index="{$groupKey|escape:'htmlall':'UTF-8'}" data-id="groupCarrierCollection.preparation_delay" value="{($group.preparation_delay)|escape:'htmlall':'UTF-8'}" id="preparation_delay-{$groupKey|escape:'htmlall':'UTF-8'}"/>
                        </div>
                    </div>
                </div>
                {foreach $carrierCollection as $carrierKey => $carrier}{if ((isset($carrier.key_group_carrier) && $carrier.key_group_carrier == $groupKey || isset($carrier.id_group_carrier) && $carrier.id_group_carrier == $group.id) && !isset($carrier.removed))}
                    <hr>
                    <div class="js-carrier">
                        <div class="form-wrapper row">
                            <div class="form-group  col-lg-6">
                                <input type="hidden" data-index="{$carrierKey|escape:'htmlall':'UTF-8'}" data-id="carrierCollection.key_group_carrier" value="{$groupKey|escape:'htmlall':'UTF-8'}" />
                                <div class="col-lg-8">
                                    <label for="account-{$carrierKey|escape:'htmlall':'UTF-8'}">{__ s="Admin.ConfigurationBack.ajax.account.label"}</label>
                                    <select data-index="{$carrierKey|escape:'htmlall':'UTF-8'}" data-id="carrierCollection.id_account" data-update-on-change="true" id="account-{$carrierKey|escape:'htmlall':'UTF-8'}">
                                        {foreach $accountCollection as $account}<option value="{($account.id)|escape:'htmlall':'UTF-8'}" {if $account.id == $carrier.id_account}selected{/if}>{$account.code_sa} / {$account.code_client} - {$account.name}</option>{/foreach}
                                    </select>
                                    <br />
                                </div>
                                <div class="col-lg-8">
                                    <label for="prestation-{$carrierKey|escape:'htmlall':'UTF-8'}">{__ s="Admin.ConfigurationBack.ajax.prestation.label"}</label>
                                    <select data-index="{$carrierKey|escape:'htmlall':'UTF-8'}" data-id="carrierCollection.id_prestation" data-update-on-change="true" id="prestation-{$carrierKey|escape:'htmlall':'UTF-8'}">
                                        {foreach $prestationCollection as $prestation}{foreach $accountPrestationCollection as $accountPrestation}{if ($accountPrestation.id_prestation == $prestation.id
                                        && $accountPrestation.id_account == $carrier.id_account) && (
                                        $group.reference == 'rdv'
                                        && ($prestation.web_appointment || $prestation.tel_appointment)
                                        ||
                                        $group.reference == 'relay'
                                        && ($prestation.withdrawal_point || $prestation.withdrawal_agency)
                                        ||
                                        $group.reference == 'classic'
                                        && !$prestation.web_appointment
                                        && !$prestation.withdrawal_point)}<option value="{($prestation.id)|escape:'htmlall':'UTF-8'}" {if $carrier.id_prestation == $prestation.id}selected{/if}>{$prestation.libelle}</option>{/if}{/foreach}{/foreach}
                                    </select>
                                    <br />
                                </div>
                                <div class="col-lg-8">
                                    <label for="price-{$carrierKey|escape:'htmlall':'UTF-8'}">{__ s="Admin.ConfigurationBack.ajax.price.label"}</label>
                                    <br />
                                    <p class="geodis_description">
                                        <label for="enable_price_fixed-{$carrierKey|escape:'htmlall':'UTF-8'}">{__ s="Admin.ConfigurationBack.ajax.price.fixed.label"}</label>
                                        <input type="checkbox" data-index="{$carrierKey|escape:'htmlall':'UTF-8'}" data-id="carrierCollection.enable_price_fixed" {if $carrier.enable_price_fixed > 0}checked{/if} id="enable_price_fixed-{$carrierKey|escape:'htmlall':'UTF-8'}" />
                                    </p>
                                    <input type="text" data-type="float" data-index="{$carrierKey|escape:'htmlall':'UTF-8'}" data-id="carrierCollection.price" value="{($carrier.price)|escape:'htmlall':'UTF-8'}" id="price-{$carrierKey|escape:'htmlall':'UTF-8'}"/>
                                    <br />
                                    <p class="geodis_description">
                                        <label for="enable_price_according-{$carrierKey|escape:'htmlall':'UTF-8'}">{__ s="Admin.ConfigurationBack.ajax.price.according.amount.weight.label"}</label>
                                        <input type="checkbox" data-index="{$carrierKey|escape:'htmlall':'UTF-8'}"  data-id="carrierCollection.enable_price_according" class="js-enable_price_according" {if $carrier.enable_price_according > 0}checked{/if} id="enable_price_according-{$carrierKey|escape:'htmlall':'UTF-8'}"/>
                                        <br />
                                        {__ s="Admin.ConfigurationBack.ajax.price.parameters.label"}
                                    </p>
                                </div>
                                <div class="col-lg-8">
                                    <label for="enable_free_shipping-{$carrierKey|escape:'htmlall':'UTF-8'}">{__ s="Admin.ConfigurationBack.ajax.enabledFreeShipping.label"}</label>
                                    <input type="checkbox" data-index="{$carrierKey|escape:'htmlall':'UTF-8'}"  data-id="carrierCollection.enable_free_shipping" class="js-enable_free_shipping" {if $carrier.enable_free_shipping > 0}checked{/if} id="enable_free_shipping-{$carrierKey|escape:'htmlall':'UTF-8'}" onchange="jQuery('#additional_shipping_cost-{$carrierKey|escape:'javascript':'UTF-8'}').attr('disabled', !jQuery(this).is(':checked')); if (!jQuery(this).is(':checked')) { jQuery('.js-free_shipping_from-{$carrierKey|escape:'javascript':'UTF-8'}').val(''); } jQuery('#free_shipping_from-{$carrierKey|escape:'javascript':'UTF-8'}').attr('readonly', !jQuery(this).is(':checked'));"/>
                                    <p class="geodis_description">{__ s="Admin.ConfigurationBack.ajax.enabledFreeShipping.desc"}</p>
                                    <br />
                                </div>
                                <div class="col-lg-8 js-free_shipping_from-{$carrierKey|escape:'htmlall':'UTF-8'}" >
                                    <label for="free_shipping_from-{$carrierKey|escape:'htmlall':'UTF-8'}">{__ s="Admin.ConfigurationBack.ajax.freeShipping.label"}</label>
                                    <input type="text" {if $carrier.enable_free_shipping <= 0}readonly{/if} data-type="float" data-index="{$carrierKey|escape:'htmlall':'UTF-8'}" data-id="carrierCollection.free_shipping_from" value="{($carrier.free_shipping_from)|escape:'htmlall':'UTF-8'}" id="free_shipping_from-{$carrierKey|escape:'htmlall':'UTF-8'}"  class="js-free_shipping_from-{$carrierKey|escape:'htmlall':'UTF-8'}" {if !$carrier.free_shipping_from}readonly{/if} />
                                    <br />
                                </div>
                                <div class="col-lg-8">
                                    <label for="additional_shipping_cost-{$carrierKey|escape:'htmlall':'UTF-8'}">{__ s="Admin.ConfigurationBack.ajax.includeAdditionnalShippingCost.label"}</label>
                                    <input type="checkbox" {if $carrier.enable_free_shipping <= 0}disabled{/if} data-index="{$carrierKey|escape:'htmlall':'UTF-8'}" data-id="carrierCollection.additional_shipping_cost" {if $carrier.additional_shipping_cost}checked{/if} value="1" id="additional_shipping_cost-{$carrierKey|escape:'htmlall':'UTF-8'}" {if !$carrier.free_shipping_from}disabled{/if}/>
                                    <p class="geodis_description">{__ s="Admin.ConfigurationBack.ajax.includeAdditionnalShippingCost.desc"}</p>
                                    <br />
                                </div>
                                <div class="form-group  col-lg-8"></div>
                                <div class="col-lg-6">
                                    <label for="active-{$carrierKey|escape:'htmlall':'UTF-8'}">{__ s="Admin.ConfigurationBack.ajax.active.label"}</label>
                                    <input type="checkbox" data-index="{$carrierKey|escape:'htmlall':'UTF-8'}" data-id="carrierCollection.active" {if $carrier.active}checked{/if} value="1" id="active-{$carrierKey|escape:'htmlall':'UTF-8'}"/>
                                    <br />
                                </div>
                                <div class="col-lg-6">
                                    <button class="js-remove-carrier btn btn-default pull-left" data-index="{$carrierKey|escape:'htmlall':'UTF-8'}">{__ s="Admin.ConfigurationBack.ajax.action.remove"}</button>
                                </div>
                            </div>

                            <div class="form-group col-lg-6">
                                {if $group.reference != 'relay'}
                                    {foreach $carrierOptionCollection as $carrierOptionKey => $carrierOption}{if (isset($carrierOption.key_carrier) && $carrierOption.key_carrier == $carrierKey || !empty($carrierOption.id_carrier) && $carrierOption.id_carrier == $carrier.id)}
                                        <div class="col-lg-12 prestation">
                                            {foreach $optionCollection as $option}{if $option.id == $carrierOption.id_option}
                                                <div class="col-lg-5">
                                                    <label for="active-{$carrierKey|escape:'htmlall':'UTF-8'}-{$carrierOptionKey|escape:'htmlall':'UTF-8'}">{($option.name)|escape:'htmlall':'UTF-8'}</label>
                                                    <p class="prestationOptionDescription">{($option.description)|escape:'htmlall':'UTF-8'}</p>
                                                </div>
                                            {/if}{/foreach}
                                            <div class="col-lg-1">
                                                <label for="active-{$carrierKey|escape:'htmlall':'UTF-8'}-{$carrierOptionKey|escape:'htmlall':'UTF-8'}">{__ s="Admin.ConfigurationBack.ajax.active.label"}</label>
                                            </div>
                                            <div class="col-lg-1">
                                                <input
                                                        type="checkbox" data-index="{$carrierOptionKey|escape:'htmlall':'UTF-8'}"
                                                        data-id="carrierOptionCollection.active"
                                                        id="active-{$carrierKey|escape:'htmlall':'UTF-8'}-{$carrierOptionKey|escape:'htmlall':'UTF-8'}"
                                                        {if $carrierOption.active}checked{/if}
                                                />
                                            </div>
                                            <div class="col-lg-3">
                                                <label for="price-impact-{$carrierKey|escape:'htmlall':'UTF-8'}-{$carrierOptionKey|escape:'htmlall':'UTF-8'}">{__ s="Admin.ConfigurationBack.ajax.priceImpact.label"}</label>
                                            </div>
                                            <div class="col-lg-2">
                                                <input type="text" data-index="{$carrierOptionKey|escape:'htmlall':'UTF-8'}" data-type="float" data-id="carrierOptionCollection.price_impact" value="{($carrierOption.price_impact)|escape:'htmlall':'UTF-8'}" id="price-impact-{$carrierKey|escape:'htmlall':'UTF-8'}-{$carrierOptionKey|escape:'htmlall':'UTF-8'}"/>
                                            </div>
                                        </div>
                                    {/if}{/foreach}{/if}
                            </div>
                        </div>
                    </div>
                {/if}{/foreach}
                <div class="panel-footer">
                    <button class="js-disable-group btn btn-default pull-left" data-index="{$groupKey}"><i data-index="{$groupKey}" class="process-icon-cancel"></i>{__ s="Admin.ConfigurationBack.ajax.action.disable"}</button>
                    <button class="js-save btn btn-default pull-right"><i class="process-icon-save"></i>{__ s="Admin.ConfigurationBack.ajax.action.save"}</button>
                    <button data-index="{$groupKey}" class="js-add-carrier btn btn-default pull-right"><i data-index="{$groupKey}" class="process-icon-new"></i>{__ s="Admin.ConfigurationBack.ajax.action.addCarrier"}</button>
                </div>
            </div>
        {else}
            <div class="panel">
                <div class="row">
                    <div class="col-lg-12">
                        <h2>{__ s="Admin.ConfigurationBack.ajax.group.name.%s" vars=[$group.name]}</h2>
                    </div>
                </div>
                <div class="panel-footer">
                    <button class="js-enable-group btn btn-default pull-left" data-index="{$groupKey}"><i data-index="{$groupKey}" class="process-icon-ok"></i>{__ s="Admin.ConfigurationBack.ajax.action.enable"}</button>
                </div>
            </div>
        {/if}
    {/foreach}
</div>
