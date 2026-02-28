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

<form action="" id="wk-filtered-products" method="POST" enctype="multipart/form-data" class="defaultForm form-horizontal">
    <div class="row">
        <div class="col-lg-4" id="wk-product-search-block">
            {$productSearchBlock nofilter}
        </div>
        <div class="col-lg-4">
            <div class="panel">
                <div class="panel-heading">
                    <i class="icon-list-alt"></i> {l s='Select products' mod='wksampleproduct'}
                    <!-- <img src="{$ajaxLoader|escape:'htmlall':'UTF-8'}" class="wk-search-product-loader"> -->
                    <i class="icon-refresh icon-spin icon-fw wk-search-product-loader"></i>
                </div>
                <div class="form-wrapper" style="padding: 0;">
                    <div class="form-group" style="margin: 0;">
                        <div id="wk-filtered-list" class="col-12"></div>
                        <div class="alert alert-info" id="wk-product-search-info" style="margin: 0;">
                            {l s='Please search products by clicking on search button.' mod='wksampleproduct'}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="panel">
                <div class="panel-heading">
                    <i class="icon-cogs"></i> {l s='Sample settings' mod='wksampleproduct'}
                    <!-- <img src="{$ajaxLoader|escape:'htmlall':'UTF-8'}" class="wk-search-product-loader"> -->
                    <i class="icon-refresh icon-spin icon-fw wk-search-product-loader"></i>
                </div>
                <div class="form-wrapper">
                    <div class="form-group">
                        <label for="max_cart_qty" class="control-label col-sm-12" style="text-align: left;">
                            <b>{l s='Maximum quantity in cart' mod='wksampleproduct'}</b>
                        </label>
                        <div class="col-sm-12">
                            <input type="number" id="max_cart_qty" class="form-control" name="max_cart_qty" value="{$wk_sample.max_cart_qty|escape:'htmlall':'UTF-8'}"></input>
                            <small class="form-text text-muted"><em>{l s='Leave empty or 0 if no limitation' mod='wksampleproduct'}</em></small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="wk_sample_price_type" class="control-label col-sm-12" style="text-align: left;">
                            <b>{l s='Price type' mod='wksampleproduct'}</b>
                        </label>
                        <div class="col-sm-12">
                            <select class="form-control" name="wk_sample_price_type" id="wk_sample_price_type">
                                <option value="1"{if $wk_sample.wk_sample_price_type == 1} selected{/if}>{l s='Product standard price' mod='wksampleproduct'}</option>
                                <option value="2"{if $wk_sample.wk_sample_price_type == 2} selected{/if}>{l s='Deduct fix amount from product price' mod='wksampleproduct'}</option>
                                <option value="3"{if $wk_sample.wk_sample_price_type == 3} selected{/if}>{l s='Deduct percentage of price from product price' mod='wksampleproduct'}</option>
                                <option value="4"{if $wk_sample.wk_sample_price_type == 4} selected{/if}>{l s='Custom price' mod='wksampleproduct'}</option>
                                <option value="5"{if $wk_sample.wk_sample_price_type == 5} selected{/if}>{l s='Free sample' mod='wksampleproduct'}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group wk_price_type_amount">
                        <label for="wk_sample_amount" class="control-label col-sm-12 required" style="text-align: left;">
                            <b>{l s='Amount' mod='wksampleproduct'}</b>
                        </label>
                        <div class="col-sm-12">
                            <div class="input-group">
                                <span class="input-group-addon">{Context::getContext()->currency->sign|escape:'htmlall':'UTF-8'}</span>
                                <input type="text" id="wk_sample_amount" name="wk_sample_amount" class="form-control" value="{$wk_sample.wk_sample_amount|escape:'htmlall':'UTF-8'}">
                            </div>
                        </div>
                    </div>
                    <div class="form-group wk_price_type_percent">
                        <label for="wk_sample_percent" class="control-label col-sm-12 required" style="text-align: left;">
                            <b>{l s='Percent' mod='wksampleproduct'}</b>
                        </label>
                        <div class="col-sm-12">
                            <div class="input-group">
                                <span class="input-group-addon">%</span>
                                <input type="text" id="wk_sample_percent" name="wk_sample_percent" class="form-control" value="{$wk_sample.wk_sample_percent|escape:'htmlall':'UTF-8'}">
                            </div>
                        </div>
                    </div>
                    <div class="form-group wk_price_type_customprice">
                        <label for="wk_sample_customprice" class="control-label col-sm-12 required" style="text-align: left;">
                            <b>{l s='Sample price' mod='wksampleproduct'}</b>
                        </label>
                        <div class="col-sm-12">
                            <div class="input-group">
                                <span class="input-group-addon">{Context::getContext()->currency->sign|escape:'htmlall':'UTF-8'}</span>
                                <input type="text" id="wk_sample_customprice" name="wk_sample_customprice" class="form-control" value="{$wk_sample.wk_sample_customprice|escape:'htmlall':'UTF-8'}">
                            </div>
                        </div>
                    </div>
                    <div class="form-group wk_price_type_tax">
                        <label for="wk_sample_tax" class="control-label col-sm-12" style="text-align: left;">
                            <b>{l s='Tax' mod='wksampleproduct'}</b>
                        </label>
                        <div class="col-sm-12">
                            <select id="wk_sample_tax" name="wk_sample_tax" class="form-control">
                                <option value="0"{if $wk_sample.wk_sample_tax == 0} selected{/if}>{l s='Tax excluded' mod='wksampleproduct'}</option>
                                <option value="1"{if $wk_sample.wk_sample_tax == 1} selected{/if}>{l s='Tax included' mod='wksampleproduct'}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="wk_sample_weight" class="control-label col-sm-12" style="text-align: left;">
                            <b>{l s='Weight' mod='wksampleproduct'}</b>
                        </label>
                        <div class="col-sm-12">
                            <input type="number" id="wk_sample_weight" class="form-control" name="wk_sample_weight" value="{$wk_sample.wk_sample_weight|escape:'htmlall':'UTF-8'}"></input>
                            <small class="form-text text-muted"><em>{l s='Set 0 to apply standard product weight' mod='wksampleproduct'}</em></small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="wk_sample_btn_label" class="control-label col-sm-12" style="text-align: left;">
                            <b>{l s='Sample button title' mod='wksampleproduct'}</b>
                        </label>
                        <div class="col-sm-12">
                            {foreach from=$wk_languages item=lang}
                                <div class="translatable-field lang-{$lang.id_lang|escape:'htmlall':'UTF-8'} row" {if $lang.id_lang|escape:'htmlall':'UTF-8' != $wk_language|escape:'htmlall':'UTF-8'}style="display: none;"{/if}>
                                    <div class="col-sm-9">
                                        {assign var="wkBtnKey" value="wk_sample_btn_label_{$lang.id_lang}"}
                                        <input type="text" id="{$wkBtnKey|escape:'htmlall':'UTF-8'}" name="{$wkBtnKey|escape:'htmlall':'UTF-8'}" class="form-control" value="{$wk_sample[$wkBtnKey]|escape:'htmlall':'UTF-8'}" onkeyup="if (isArrowKey(event)) return ;updateFriendlyURL();">
                                        <small class="form-text text-muted"><em>{l s='Default label name \'Buy Sample\', applied if empty' mod='wksampleproduct'}</em></small>
                                    </div>
                                    <div class="col-sm-3">
                                        <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                                            {$lang.iso_code|escape:'htmlall':'UTF-8'}
                                            <i class="icon-caret-down"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            {foreach from=$wk_languages item=langOption}
                                            <li>
                                                <a href="javascript:hideOtherLanguage({$langOption.id_lang|escape:'htmlall':'UTF-8'});" tabindex="-1">{$langOption.name|escape:'htmlall':'UTF-8'}</a>
                                            </li>
                                            {/foreach}
                                        </ul>
                                    </div>
                                </div>
                            {/foreach}
                        </div>
                    </div>
                    <div class="form-group wk_sample_desc_mce">
                        <label for="wk_sample_desc" class="control-label col-sm-12" style="text-align: left;">
                            <b>{l s='Sample description' mod='wksampleproduct'}</b>
                        </label>
                        <div class="col-sm-12" id="wk_sample_desc_wrap">
                            {foreach from=$wk_languages item=lang}
                                <div class="translatable-field lang-{$lang.id_lang|escape:'htmlall':'UTF-8'} row" {if $lang.id_lang != $wk_language|escape:'htmlall':'UTF-8'}style="display: none;"{/if}>
                                    <div class="col-sm-9">
                                        {assign var="wkDescKey" value="wk_sample_desc_{$lang.id_lang}"}
                                        <textarea name="{$wkDescKey|escape:'htmlall':'UTF-8'}" class="wk_autoload_rte form-control">
                                            {$wk_sample[$wkDescKey]|escape:'htmlall':'UTF-8'}
                                        </textarea>
                                    </div>
                                    <div class="col-sm-3">
                                        <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                                            {$lang.iso_code|escape:'htmlall':'UTF-8'}
                                            <i class="icon-caret-down"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            {foreach from=$wk_languages item=langOption}
                                            <li>
                                                <a href="javascript:hideOtherLanguage({$langOption.id_lang|escape:'htmlall':'UTF-8'});" tabindex="-1">{$langOption.name|escape:'htmlall':'UTF-8'}</a>
                                            </li>
                                            {/foreach}
                                        </ul>
                                    </div>
                                </div>
                            {/foreach}
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="wk_sample_carriers" class="control-label col-sm-12" style="text-align: left;">
                            <b>{l s='Sample carriers' mod='wksampleproduct'}</b>
                        </label>
                        <div class="col-sm-12">
                            <div class="checkbox">
                                <label class="form-check-label">
                                    <input type="checkbox" id="wk_sample_carriers_0" name="wk_sample_carriers[]" class="wk_sample_bulk_carriers" value="0">
                                    {l s='Select/unselect all' mod='wksampleproduct'}
                                </label>
                            </div>
                            {foreach from=$wk_carrier_list item=wk_carrier}
                            <div class="checkbox">
                                <label class="form-check-label">
                                    <input type="checkbox" id="wk_sample_carriers_{$wk_carrier.id_reference|escape:'htmlall':'UTF-8'}" class="wk_sample_bulk_carriers" name="wk_sample_carriers[]" value="{$wk_carrier.id_reference|escape:'htmlall':'UTF-8'}">
                                    {$wk_carrier.id_carrier|escape:'htmlall':'UTF-8'} - {$wk_carrier.name|escape:'htmlall':'UTF-8'}
                                </label>
                            </div>
                            {/foreach}
                            <small class="form-text text-muted"><em>{l s='Only selected carriers will be available for a sample product. If no carrier selected, then standard product settings will be applied.' mod='wksampleproduct'}</em></small>
                        </div>
                    </div>
                </div>

                <div class="panel-footer" id="addNewProductButton">
                    <button type="submit" value="1" id="submitSampleSettingsBulk" name="submitSampleSettingsBulk"
                        class="btn btn-default pull-right">
                        <i class="process-icon-save"></i> {l s='Save' mod='wksampleproduct'}
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
