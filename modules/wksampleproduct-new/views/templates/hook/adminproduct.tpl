{*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License version 3.0
* that is bundled with this package in the file LICENSE.txt
* It is also available through the world-wide-web at this URL:
* https://opensource.org/licenses/AFL-3.0
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your needs
* please refer to CustomizationPolicy.txt file inside our module for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
*}

<div class="col-md-12">
    {if isset($mpProduct)}
    <div class="alert alert-warning">
        <p>{l s='This is a marketplace product. You can create the sample in Marketplace products page.'
            mod='wksampleproduct'}</p>
    </div>
    {else}
    <fieldset class="form-group">
        {l s='Offer sample' mod='wksampleproduct'}

        <label for="switch"><input data-toggle="switch" id="switch" data-inverse="true" type="checkbox"
                name="sample_active" {if (isset($isSample) && count($isSample)) &&
                ($isSample['active']==1)}checked{/if}></label>
    </fieldset>
    <div id="wkproductsamplesettingwrap" class="border p-3">
        <div class="row">
            <div class="col-md-12">
                <h3>{l s='Apply sample settings from' mod='wksampleproduct'}</h3>
            </div>
            <div class="col-md-12">
                <span class="form-text text-muted"><em>{l s='Sample will be disabled if global settings is checked here
                        and the global sample is disabled in module configuration page.'
                        mod='wksampleproduct'}</em></span>
                <div id="wk_follow">
                    <div class="radio form-check form-check-radio">
                        <label class=" form-check-label">
                            <input type="radio" id="form_hooks_wk_follow_global" name="wk_follow_setting" value="1" {if
                                !isset($sample) || ($sample['active']==0)} checked{/if}>
                            <i class="form-check-round"></i>
                            {l s='Global settings' mod='wksampleproduct'}
                        </label>
                        <span id="globalValidation"></span>
                    </div>
                    <div class="radio form-check form-check-radio">
                        <label class=" form-check-label">
                            <input type="radio" id="wk_follow_product" name="wk_follow_setting" value="2" {if
                                isset($sample) && ($sample['active']==1)} checked{/if}>
                            <i class="form-check-round"></i>
                            {l s='Particular settings' mod='wksampleproduct'}
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div id="wkproductsamplesetting" class="border p-3">
            <div class="row">
                <fieldset class="col-md-4 form-group">
                    <label class="form-control-label">{l s='Maximum quantity in cart' mod='wksampleproduct'}</label>
                    <input type="number" id="form_hooks_max_cart_qty" class="form-control" name="max_cart_qty"
                        value="{if isset($sample)}{$sample['max_cart_qty']|escape:'htmlall':'UTF-8'}{/if}"></input>
                    <small class="form-text text-muted"><em>{l s='Leave empty or 0 if no limitation'
                            mod='wksampleproduct'}</em></small>
                </fieldset>
            </div>
            <div id="wk_sample_file_input_block" data-virtual-product="{if $isVirtual && $shouldUpload}1{else}0{/if}"
                class="{if !$isVirtual}d-none{/if}">
                <div id="wk_sp_virtual_input_wrapper" class="row" {if !$shouldUpload}style="display:none;" {/if}>
                    <fieldset class="col-md-12 form-group">
                        {l s='Does this sample have an associated file?' mod='wksampleproduct'}
                        <label for="switch"><input data-toggle="switch" id="wk_sp_virtual_switch" data-inverse="true"
                                type="checkbox" name="sample_file_active" {if $sampleExists}checked{/if}></label>
                    </fieldset>
                    <fieldset id="wk_sp_file_input" class="col-md-4 form-group">
                        <label class="form-control-label">{l s='Upload sample' mod='wksampleproduct'}</label>
                        <input type="file" id="form_hooks_uploaded_sample_file" class="form-control"
                            data-id="{$idProduct|escape:'htmlall':'UTF-8'}" name="uploaded_sample_file" {if $sampleExists|escape:'htmlall':'UTF-8'}data-set="1" {/if}>
                        <small id="wk_sp_sample_file_status" class="form-text text-muted">
                            <em>
                                {if $sampleExists > 0}
                                {if isset($sampleFileName)}{l s='Uploaded file: '
                                mod='wksampleproduct'}{$sampleFileName}{/if}
                                {else}
                                {l s='Upload sample to send on sample order. File size should not exceed'
                                mod='wksampleproduct'} {$attachmentMaxSize|escape:'htmlall':'UTF-8'}MB.
                                {/if}
                            </em>
                        </small>
                    </fieldset>
                    <fieldset id="wk_sp_file_delete" class="col-md-4 align-self-center form-group">
                        <button type="button" id="form_hooks_delete_sample_file" class="btn btn-danger"
                            data-id="{$idProduct|escape:'htmlall':'UTF-8'}" style="display:{if $sampleExists|escape:'htmlall':'UTF-8' > 0}block{else}none{/if};">
                            {l s='Delete' mod='wksampleproduct'}
                        </button>
                    </fieldset>
                </div>
            </div>
            <div class="row">
                <fieldset class="col-md-4 form-group">
                    <label>{l s='Product price' mod='wksampleproduct'}</label>
                    : <span>{$productPrice|escape:'htmlall':'UTF-8'}</span> {l s='(Tax excl.)' mod='wksampleproduct'}
                </fieldset>
            </div>
            <div class="row">
                <fieldset class="col-md-4 form-group">
                    <label class="form-control-label">{l s='Price type' mod='wksampleproduct'}</label>
                    <select class="form-control" name="wk_sample_price_type" id="wk_sample_price_type">
                        <option value="1" {if isset($sample)}{if $sample['price_type']==1}selected{/if}{/if}>{l
                            s='Product standard price' mod='wksampleproduct'}</option>
                        <option value="2" {if isset($sample)}{if $sample['price_type']==2}selected{/if}{/if}>{l
                            s='Deduct fix amount from product price' mod='wksampleproduct'}</option>
                        <option value="3" {if isset($sample)}{if $sample['price_type']==3}selected{/if}{/if}>{l
                            s='Deduct percentage of price from product price' mod='wksampleproduct'}</option>
                        <option value="4" {if isset($sample)}{if $sample['price_type']==4}selected{/if}{/if}>{l
                            s='Custom price' mod='wksampleproduct'}</option>
                        <option value="5" {if isset($sample)}{if $sample['price_type']==5}selected{/if}{/if}>{l s='Free
                            sample' mod='wksampleproduct'}</option>
                    </select>
                </fieldset>
                <fieldset class="col-md-2 form-group wk_sample_amount">
                    <label class="form-control-label" id="wk_sample_sign_label">{l s='Amount'
                        mod='wksampleproduct'}</label>
                    <label class="form-control-label" id="wk_sample_percent_label">{l s='Percentage'
                        mod='wksampleproduct'}</label>
                    <div class="input-group money-type">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="wk_sample_percent">%</span>
                            <span class="input-group-text" id="wk_sample_sign">{$sign|escape:'htmlall':'UTF-8'}</span>
                        </div>
                        <input type="text" id="form_hooks_sample_amount" name="sample_amount" class="form-control"
                            value="{if isset($sample)}{$sample['amount']|escape:'htmlall':'UTF-8'}{else}0{/if}">
                    </div>
                </fieldset>
                <fieldset class="col-md-2 form-group wk_sample_custom_price">
                    <label class="form-control-label">{l s='Set price' mod='wksampleproduct'}</label>
                    <input type="text" name="wk_sample_price" id="form_hooks_wk_sample_price" class="form-control"
                        value="{if isset($sample)}{$sample['price']|escape:'htmlall':'UTF-8'}{else}0{/if}"></input>
                </fieldset>
                <fieldset class="col-md-2 form-group wk_sample_price_tax">
                    <label class="form-control-label">{l s='Tax' mod='wksampleproduct'}</label>
                    <select class="form-control" name="wk_sample_price_tax" id="wk_sample_price_tax">
                        <option value="0" {if isset($sample)}{if $sample['price_tax']==0}selected{/if}{/if}>{l s='Tax
                            excluded' mod='wksampleproduct'}</option>
                        <option value="1" {if isset($sample)}{if $sample['price_tax']==1}selected{/if}{/if}>{l s='Tax
                            included' mod='wksampleproduct'}</option>
                    </select>
                </fieldset>
            </div>
            <div class="row">
                <fieldset class="col-md-3 form-group">
                    <label class="form-control-label">{l s='Set weight' mod='wksampleproduct'}</label>
                    <div class="input-group money-type">
                        <input type="text" name="wk_sample_weight" id="form_hooks_wk_sample_weight" class="form-control"
                            value="{if isset($sample)}{$sample['weight']|escape:'htmlall':'UTF-8'}{else}0{/if}"></input>
                        <div class="input-group-append">
                            <span class="input-group-text">{$wkWeightUnit|escape:'htmlall':'UTF-8'}</span>
                        </div>
                        <div id="hooks_wk_sample_weight">
                        </div>
                    </div>
                </fieldset>
            </div>
            <div class="row">
                <fieldset class="col-md-8 form-group">
                    <label class="form-control-label">{l s='Sample button title' mod='wksampleproduct'}</label>
                    <div class="form-group">
                        {foreach from=$allLanguages item=lang}
                        <div class="translatable-field lang-{$lang.id_lang} row" {if $lang.id_lang
                            !=$wk_language}style="display: none;" {/if}>
                            <div class="col-lg-8">
                                <input type="text" id="form_hooks_sample_btn_label_{$lang.id_lang|escape:'htmlall':'UTF-8'}"
                                    name="sample_btn_label_{$lang.id_lang|escape:'htmlall':'UTF-8'}" class="form-control"
                                    value="{if isset($sample['button_label']) && isset($sample['button_label'][$lang.id_lang]) && (strlen($sample['button_label'][$lang.id_lang]|escape:'htmlall':'UTF-8') > 0)}{$sample['button_label'][$lang.id_lang]|escape:'htmlall':'UTF-8'}{else}{l s='Buy Sample' mod='wksampleproduct'}{/if}"
                                    onkeyup="if (isArrowKey(event)) return ;updateFriendlyURL();">
                                <small class="form-text text-muted"><em>{l s='Default label name \'Buy Sample\', applied
                                        if empty' mod='wksampleproduct'}</em></small>
                            </div>
                            <div class="col-lg-3">
                                <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1"
                                    data-toggle="dropdown" style="background-color: white">
                                    {$lang.iso_code|escape:'htmlall':'UTF-8'}
                                    <i class="icon-caret-down"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    {foreach from=$allLanguages item=langOption}
                                    <li class="dropdown-item" onclick="hideOtherLanguage({$langOption.id_lang|escape:'htmlall':'UTF-8'});">
                                        <a href="javascript:void('0')" tabindex="-1"
                                            style="text-decoration: none;">{$langOption.name|escape:'htmlall':'UTF-8'}</a>
                                    </li>
                                    {/foreach}
                                </ul>
                            </div>
                        </div>
                        {/foreach}
                    </div>
                </fieldset>
            </div>
            <div class="row">
                <fieldset class="col-md-12 form-group">
                    <label class="form-control-label">{l s='Description' mod='wksampleproduct'}</label>
                    <div class="form-group">
                        {foreach from=$allLanguages item=lang}
                        <div class="translatable-field translationsFields tab-content lang-{$lang.id_lang} row" {if
                            $lang.id_lang !=$wk_language}style="display: none;" {/if}>
                            <div class="col-lg-9">
                                <textarea name="wk_sample_desc_{$lang.id_lang|escape:'htmlall':'UTF-8'}" class="autoload_rte form-control">
                                    {if isset($sample['description']) && isset($sample['description'][$lang.id_lang])}
                                    {$sample['description'][$lang.id_lang]|escape:'htmlall':'UTF-8'}
                                    {/if}
                                </textarea>
                            </div>
                            <div class="col-lg-2">
                                <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1"
                                    data-toggle="dropdown" style="background-color: white">
                                    {$lang.iso_code|escape:'htmlall':'UTF-8'}
                                    <i class="icon-caret-down"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    {foreach from=$allLanguages item=langOption}
                                    <li class="dropdown-item" onclick="hideOtherLanguage({$langOption.id_lang|escape:'htmlall':'UTF-8'});">
                                        <a href="javascript:void('0')" tabindex="-1"
                                            style="text-decoration: none;">{$langOption.name|escape:'htmlall':'UTF-8'}</a>
                                    </li>
                                    {/foreach}
                                </ul>
                            </div>
                        </div>
                        {/foreach}
                    </div>
                </fieldset>
            </div>
            <div class="row {if $isVirtual}d-none{/if}">
                <div class="col-md-12">
                    <b class="h3">{l s='Available carriers' mod='wksampleproduct'}</b>
                </div>
                <div class="col-md-12">
                    <span class="form-text text-muted"><em>{l s='Only selected carriers will be available for a sample
                            product. If no carrier selected, then standard product settings will be applied.'
                            mod='wksampleproduct'}</em></span>
                    <div id="wk_carriers">
                        <div class="checkbox">
                            <label class=" form-check-label">
                                <input type="checkbox" id="wk_sample_carriers_0" class="wk_sample_bulk_carriers"
                                    name="wk_sample_carriers[]" value="0">
                                {l s='Select/unselect all' mod='wksampleproduct'}
                            </label>
                        </div>
                        {foreach from=$wk_carrier_list item=wk_carrier}
                        <div class="checkbox">
                            <label class=" form-check-label">
                                <input type="checkbox" id="wk_sample_carriers_{$wk_carrier.id_reference|escape:'htmlall':'UTF-8'}"
                                    class="wk_sample_bulk_carriers" name="wk_sample_carriers[]"
                                    value="{$wk_carrier.id_reference|escape:'htmlall':'UTF-8'}" {if in_array($wk_carrier.id_reference,
                                    $wk_carrier_selected)} checked="checked" {/if}>
                                {$wk_carrier.id_carrier|escape:'htmlall':'UTF-8'} - {$wk_carrier.name|escape:'htmlall':'UTF-8'}
                            </label>
                        </div>
                        {/foreach}
                    </div>
                </div>
            </div>
        </div>
    </div>
    {/if}
</div>