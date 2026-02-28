{*
* 2007-2023 PrestaShop
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
*  @author    Ádalop <contact@prestashop.com>
*  @copyright 2023 Ádalop
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}

{* Customize refunds policy *}
<div role="tabpanel" class="tab-pane" id="customize_shipping_details">
    <div class="form-group">
        <div class="alert alert-warning" role="alert">
            {l s='Customise structured data for product shipping details. For more information' mod='adpmicrodatos'} <a href="https://schema.org/OfferShippingDetails" target="_blank">https://schema.org/OfferShippingDetails</a>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-lg-5">
            <span class="label-tooltip" data-toggle="tooltip" title="{l s='Do you want to activate or deactivate the structured data of product shipping details?' mod='adpmicrodatos'}" data-placement="bottom">
                {l s='Active shipping details' mod='adpmicrodatos'}
            </span>
        </label>
        <div class="col-lg-7">
            <span class="switch prestashop-switch fixed-width-lg">
                <input type="radio" name="active_microdata_shipping_details" id="active_microdata_shipping_details_on" value="1" {if $active_microdata_shipping_details==1}checked="checked"{/if}>
                <label for="active_microdata_shipping_details_on">{l s='Yes' mod='adpmicrodatos'}</label>
                <input type="radio" name="active_microdata_shipping_details" id="active_microdata_shipping_details_off" value="0" {if $active_microdata_shipping_details==0}checked="checked"{/if}>
                <label for="active_microdata_shipping_details_off">{l s='No' mod='adpmicrodatos'}</label>
                <a class="slide-button btn"></a>
            </span>
            <div class="help-block">{l s='Only works on product page' mod='adpmicrodatos'}</div>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-lg-5">
            <span class="label-tooltip" data-toggle="tooltip" title="{l s='Specifies the shipping rate for the products.' mod='adpmicrodatos'}" data-placement="bottom">
                {l s='Shipping Rate' mod='adpmicrodatos'}
            </span>
        </label>
        <div class="col-lg-7">
            <div class="col-md-12">
                <div class="input-group">
                    <input name="adp_shipping_details_shipping_rate" type="number" min="0" id="adp_shipping_details_shipping_rate" step="0.01" value="{$adp_shipping_details_shipping_rate|escape:'htmlall':'UTF-8'}" class="text form-control">
                </div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-lg-5">
            <span class="label-tooltip" data-toggle="tooltip" title="{l s='Indicates the 2-letter country code in ISO 3166-1 alpha-2 format.' mod='adpmicrodatos'}" data-placement="bottom">
                {l s='Address country' mod='adpmicrodatos'}
            </span>
        </label>
        <div class="col-lg-7">
            <div class="col-md-12">
                <div class="input-group">
                    <input name="adp_shipping_details_address_country" type="text" pattern="[A-Z][A-Z]" maxlength="2" id="adp_shipping_details_address_country" value="{$adp_shipping_details_address_country|escape:'htmlall':'UTF-8'}" class="text form-control">
                </div>
                <div class="help-block">{l s='Two letters in upper case. Examples:: FR, ES, GB, US, DE, IT' mod='adpmicrodatos'}</div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-lg-5">
            <span class="label-tooltip" data-toggle="tooltip" title="{l s='Minimum number of days for delivery time' mod='adpmicrodatos'}" data-placement="bottom">
                {l s='Min delivery handling days' mod='adpmicrodatos'}
            </span>
        </label>
        <div class="col-lg-7">
            <div class="col-md-12">
                <div class="input-group">
                    <input name="adp_shipping_details_delivery_handling_time_min" type="number" min="0" id="adp_shipping_details_delivery_handling_time_min" value="{$adp_shipping_details_delivery_handling_time_min|escape:'htmlall':'UTF-8'}" class="text form-control">
                </div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-lg-5">
            <span class="label-tooltip" data-toggle="tooltip" title="{l s='Maximum number of days for delivery time' mod='adpmicrodatos'}" data-placement="bottom">
                {l s='Max delivery handling days' mod='adpmicrodatos'}
            </span>
        </label>
        <div class="col-lg-7">
            <div class="col-md-12">
                <div class="input-group">
                    <input name="adp_shipping_details_delivery_handling_time_max" type="number" min="0" id="adp_shipping_details_delivery_handling_time_max" value="{$adp_shipping_details_delivery_handling_time_max|escape:'htmlall':'UTF-8'}" class="text form-control">
                </div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-lg-5">
            <span class="label-tooltip" data-toggle="tooltip" title="{l s='Minimum number of days for transit time' mod='adpmicrodatos'}" data-placement="bottom">
                {l s='Min transit handling days' mod='adpmicrodatos'}
            </span>
        </label>
        <div class="col-lg-7">
            <div class="col-md-12">
                <div class="input-group">
                    <input name="adp_shipping_details_transit_handling_time_min" type="number" min="0" id="adp_shipping_details_transit_handling_time_min" value="{$adp_shipping_details_transit_handling_time_min|escape:'htmlall':'UTF-8'}" class="text form-control">
                </div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-lg-5">
            <span class="label-tooltip" data-toggle="tooltip" title="{l s='Maximum number of days for transit time' mod='adpmicrodatos'}" data-placement="bottom">
                {l s='Max transit handling days' mod='adpmicrodatos'}
            </span>
        </label>
        <div class="col-lg-7">
            <div class="col-md-12">
                <div class="input-group">
                    <input name="adp_shipping_details_transit_handling_time_max" type="number" min="0" id="adp_shipping_details_transit_handling_time_max" value="{$adp_shipping_details_transit_handling_time_max|escape:'htmlall':'UTF-8'}" class="text form-control">
                </div>
            </div>
        </div>
    </div>
</div>