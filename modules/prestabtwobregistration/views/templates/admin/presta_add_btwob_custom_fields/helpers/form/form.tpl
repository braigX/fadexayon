{**
 * 2008-2024 Prestaworld
 *
 * NOTICE OF LICENSE
 *
 * The source code of this module is under a commercial license.
 * Each license is unique and can be installed and used on only one website.
 * Any reproduction or representation total or partial of the module, one or more of its components,
 * by any means whatsoever, without express permission from us is prohibited.
 *
 * DISCLAIMER
 *
 * Do not alter or add/update to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @author    prestaworld
 * @copyright 2008-2024 Prestaworld
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 * International Registered Trademark & Property of prestaworld
 *}

<form action="" method="post" class="ps-select-form-wrapper" enctype="multipart/form-data" id="presta_custom_field">
    <div class="panel">
        <div class="panel-heading">
            {l s='REGISTRATION FIELDS' mod='prestabtwobregistration'}
        </div>
        <div class="panel-body">
            <input
                type="hidden"
                class="form-control"
                name="presta_fields_id"
                value="{if isset($registrationFields) && $registrationFields}{$registrationFields->id|escape:'htmlall':'UTF-8'}{/if}">

            <div class="form-group clearfix">
                <label for="form-label" class="col-md-3 control-label">
                    {l s='Choose Language' mod='prestabtwobregistration'}
                </label>
                <div class="col-md-6">
                    <select onChange="changeLanguage(this)" class="form-control" name="presta_registration_lang">
                        {if isset($languages) && $languages}
                            {foreach from=$languages item=language}
                                <option value="{$language.id_lang|escape:'htmlall':'UTF-8'}" {if $current_lang->id == $language.id_lang}selected{/if}>
                                    {$language.name|escape:'htmlall':'UTF-8'}</option>
                            {/foreach}
                        {/if}
                    </select>
                </div>
            </div>

            <div class="form-wrapper">
                <div class="form-group clearfix">
                    <label class="col-md-3 control-label required">
                        {l s='Field Title' mod='prestabtwobregistration'}
                    </label>
                    <div class="col-md-6">
                        {foreach from=$languages item=language}
                            {assign var="presta_current_div" value="presta_current_registration_fields_div_`$language.id_lang`"}
                            {assign var="name" value="presta_rf_field_title_`$language.id_lang`"}
                            <div class="presta_registration_main_div {$presta_current_div|escape:'htmlall':'UTF-8'}" {if
                                $current_lang->id != $language.id_lang}style="display:none;" {/if}>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="{$name|escape:'htmlall':'UTF-8'}"
                                    value="{if isset($smarty.post.{$name})}{$smarty.post.{$name|escape:'htmlall':'UTF-8'}|escape:'htmlall':'UTF-8'}{else if isset($registrationFields->field_title[$language.id_lang])}{$registrationFields->field_title[$language.id_lang]|escape:'htmlall':'UTF-8'}{/if}" />
                            </div>
                        {/foreach}
                    </div>
                </div>

                <div class="form-group clearfix">
                    <label for="form-label" class="col-md-3 control-label">
                        {l s='Field Type' mod='prestabtwobregistration'}
                    </label>
                    <div class="col-md-2">
                        <select class="form-control" name="field_type" id="postion2">
                            {if isset($typeList) && $typeList}
                                {foreach $typeList as $key => $list}
                                    <option
                                    {if isset($registrationFields) && $registrationFields->field_type == $key}
                                        selected="selected"
                                    {/if}
                                    value="{$key|escape:'htmlall':'UTF-8'}">{$list|escape:'htmlall':'UTF-8'}</option>
                                {/foreach}
                            {/if}
                        </select>
                    </div>
                </div>

                <div class="form-group clearfix presta_rf_text_field"
                    {if isset($registrationFields) && ($registrationFields->field_type === $text_type || $registrationFields->field_type === $textarea_type)} {else} style="display:none;" {/if}>
                    <label class="col-md-3 control-label">
                        {l s='Default Value' mod='prestabtwobregistration'}
                    </label>
                    <div class="col-md-6">
                        {foreach from=$languages item=language}
                            {assign var="presta_current_div" value="presta_current_registration_fields_div_`$language.id_lang`"}
                            {assign var="name" value="presta_rf_default_field_`$language.id_lang`"}
                            <div class="presta_registration_main_div {$presta_current_div|escape:'htmlall':'UTF-8'}" {if
                                $current_lang->id != $language.id_lang}style="display:none;" {/if}>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="{$name|escape:'htmlall':'UTF-8'}"
                                    value="{if isset($smarty.post.{$name})}{$smarty.post.{$name}|escape:'htmlall':'UTF-8'}{else if isset($registrationFields->default_value[$language.id_lang])}{$registrationFields->default_value[$language.id_lang]|escape:'htmlall':'UTF-8'}{/if}" />
                            </div>
                        {/foreach}
                    </div>
                </div>

                <div
                    class="form-group clearfix presta_rf_text_field"
                    {if isset($registrationFields) && $registrationFields->field_type == 'yesno'}style="display:none;"
                    {/if}
                    >
                    <label for="form-label" class="col-md-3 control-label">
                        {l s='Field Validation' mod='prestabtwobregistration'}
                    </label>
                    <div class="col-md-2">
                        <select class="form-control" name="presta_field_validation" id="postion3">
                            {if isset($validationFields) && $validationFields}
                                {foreach $validationFields as $key => $fieldValues}
                                    <option
                                    value="{$key|escape:'htmlall':'UTF-8'}"
                                    {if isset($registrationFields) && $registrationFields->field_validation == $key}
                                        selected="selected"
                                    {/if}
                                        value="{$fieldValues|escape:'htmlall':'UTF-8'}">{$fieldValues|escape:'htmlall':'UTF-8'}</option>
                                {/foreach}
                            {/if}
                        </select>
                    </div>
                </div>

                <div class="form-group clearfix presta-select-url-content presta_rf_multiple_selected"
                    {if isset($registrationFields) && array_key_exists($registrationFields->field_type, $multi_type_fields)}
                    {else}
                        style="display:none;"
                    {/if}>
                    <label for="form-label" class="col-md-3 control-label presta-label">
                        {l s='Values' mod='prestabtwobregistration'}
                    </label>
                    <div class="col-md-6 presta_multiple-select">
                        <button class="btn btn-success rf-multiple-add-button" type="button">
                            <i class="icon icon-plus-circle"></i>
                            {l s='Add' mod='prestabtwobregistration'}
                        </button>
                    </div>
                </div>

                {if isset($registrationFields) && isset($registrationFields->multiple_values) && $registrationFields->multiple_values}
                    {foreach $registrationFields->multiple_values as $multiple}
                        <div class="clearfix form-group select_urls">
                            <label for="form-label" class="col-md-3 control-label presta-label"></label>
                            {if isset($multiple.data) && $multiple.data}
                                {foreach $multiple.data as $m_values}
                                    {assign var="name" value="presta_registration_multi_value_field_`$m_values.id_lang`[]"}
                                    <div
                                        class="col-md-6 presta_multiple-select presta_registration_main_div presta_current_registration_fields_div_{$m_values.id_lang|escape:'html':'UTF-8'}"
                                        {if $current_lang->id != $m_values.id_lang}style="display:none;{/if}">
                                        <input
                                            type="text"
                                            name="{$name|escape:'htmlall':'UTF-8'}"
                                            class="form-control presta_rf_multivalue"
                                            value="{$m_values.value|escape:'htmlall':'UTF-8'}">
                                    </div>
                                {/foreach}
                            {/if}
                            <div class="col-md-3 input-group-btn">
                                <button class="btn btn-danger multiple_delete" type="button">
                                    <i class="icon icon-trash"></i>
                                </button>
                            </div>
                        </div>
                    {/foreach}
                {/if}

                <div
                    class="form-group clearfix presta_image_types"
                    {if isset($registrationFields) && $registrationFields->field_type != 'file'}style="display:none"{/if}
                    >
                    <label class="col-md-3 control-label required">
                        {l s='Maximum size' mod='prestabtwobregistration'}
                    </label>
                    <div class="col-lg-2">
                        <div class="input-group">
                            <input
                                type="text"
                                class="form-control"
                                name="maximum_size"
                                value="{if isset($registrationFields) && $registrationFields}{$registrationFields->maximum_size|escape:'htmlall':'UTF-8'}{/if}">
                            <span class="input-group-addon">{l s='KB' mod='prestabtwobregistration'}</span>
                        </div>
                        <div class="help-block">
                            {l s='1024kb = 1 MB' mod='prestabtwobregistration'}
                        </div>
                    </div>
                </div>

                <div class="form-group clearfix presta_image_types" {if isset($registrationFields) && $registrationFields->field_type != 'file'}style="display:none"{/if}>
                    <label class="col-md-3 control-label required">{l s='File Types' mod='prestabtwobregistration'}</label>
                    <div class="col-md-6">
                        <input
                            type="text"
                            class="form-control"
                            name="field_types"
                            value="{if isset($registrationFields) && $registrationFields}{$registrationFields->file_types|escape:'htmlall':'UTF-8'}{/if}">
                            <div class="help-block">
                                {l s='Use comma to add multiple file extension Eg- jpg,jpeg,png,pdf,csv' mod='prestabtwobregistration'}
                            </div>
                    </div>
                </div>

                <div class="form-group clearfix presta_message_types" {if isset($registrationFields) && $registrationFields->field_type == 'message'}{else}style="display:none"{/if}>
                    <label class="col-md-3 control-label">
                        {l s='Message' mod='prestabtwobregistration'}
                    </label>
                    <div class="col-md-6">
                        {foreach $languages as $lang}
                            {assign var="presta_current_div" value="presta_current_registration_fields_div_`$lang.id_lang`"}
                            {assign var="text_message" value="presta_message_fields_div`$lang.id_lang`"}
                            <div
                                class="presta_registration_main_div {$presta_current_div|escape:'htmlall':'UTF-8'}"
                                {if $current_lang->id != $lang.id_lang}style="display:none;"{/if} >
                                <textarea
                                    type="text"
                                    class="form-control"
                                    name="{$text_message|escape:'htmlall':'UTF-8'}">{if isset($smarty.post.{$text_message})}{$smarty.post.{$text_message|escape:'htmlall':'UTF-8'}|escape:'htmlall':'UTF-8'}{else if isset($registrationFields) && $registrationFields->notice_message[$lang.id_lang]}{$registrationFields->notice_message[$lang.id_lang]|escape:'htmlall':'UTF-8'}{/if}</textarea>
                            </div>
                        {/foreach}
                    </div>
                </div>

                <div class="alert_types clearfix presta_message_types"
                    {if isset($registrationFields) && $registrationFields->field_type == 'message'}

                    {else}
                        style="display:none"
                    {/if}
                    >
                    <label class="col-md-3 control-label">
                        {l s='Notice Types' mod='prestabtwobregistration'}
                    </label>
                    <div class="col-lg-6 t">
                        <div class="danger presta_notice">
                            <input
                                {if isset($registrationFields) && $registrationFields->notice_types == '1'}
                                    checked="checked"
                                {else}
                                    checked="checked"
                                {/if}
                                type="radio"
                                name="alert_types"
                                value="1">
                            <label class="alert alert-danger">{l s='Danger' mod='prestabtwobregistration'}</label>
                        </div>
                        <div class="warning presta_notice">
                            <input
                                {if isset($registrationFields) && $registrationFields->notice_types == '2'}
                                    checked="checked"
                                {/if}
                                    type="radio"
                                    name="alert_types"
                                    value="2">
                                <label class="alert alert-warning">{l s='Warning' mod='prestabtwobregistration'}</label>
                        </div>
                        <div class="info presta_notice">
                            <input
                                {if isset($registrationFields) && $registrationFields->notice_types == '3'}
                                    checked="checked"
                                {/if}
                                    type="radio"
                                    name="alert_types"
                                    value="3">
                                <label class="alert alert-info">{l s='Info' mod='prestabtwobregistration'}</label>
                        </div>
                        <div class="success presta_notice">
                           <input
                                {if isset($registrationFields) && $registrationFields->notice_types == '4'}
                                    checked="checked"
                                {/if}
                                type="radio"
                                name="alert_types"
                                value="4">
                                <label class="alert alert-success">{l s='Success' mod='prestabtwobregistration'}</label>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>

                <div class="form-group clearfix">
                    <label class="col-md-3 control-label">
                        {l s='Active' mod='prestabtwobregistration'}
                    </label>
                    <div class="col-md-6">
                        <span class="switch prestashop-switch fixed-width-lg">
                            <input
                            {if isset($smarty.post.status) && $smarty.post.status == 1}
                                checked="checked"
                            {else if isset($registrationFields) && $registrationFields->active == 1 && !isset($smarty.post.status)}
                            checked = checked
                            {else}
                            checked = checked
                            {/if}
                                type="radio"
                                name="status"
                                value="1">
                            <label class="t">
                                {l s='Yes' mod='prestabtwobregistration'}
                            </label>
                            <input
                            {if isset($smarty.post.status) && $smarty.post.status == 0 }
                                checked="checked"
                            {else if isset($registrationFields) && $registrationFields->active == 0 && !isset($smarty.post.status)}
                                checked="checked"
                            {/if}
                                type="radio"
                                name="status"
                                value="0">
                            <label class="t">
                                {l s='No' mod='prestabtwobregistration'}
                            </label>
                            <a class="slide-button btn-news">
                            </a>
                        </span>
                    </div>
                </div>

                <div class="form-group clearfix presta_rf_is_required">
                    <label class="col-md-3 control-label">
                        {l s='Is Mandatory' mod='prestabtwobregistration'}
                    </label>
                    <div class="col-md-6">
                        <span class="switch prestashop-switch fixed-width-lg">
                            <input
                                {if isset($smarty.post.is_required) && $smarty.post.is_required == 1}
                                    checked="checked"
                                {else if isset($registrationFields) && $registrationFields->is_mandatory == 1 && !isset($smarty.post.is_required)}
                                    checked="checked"
                                {/if}
                                type="radio"
                                name="is_required"
                                value="1"
                                checked>
                            <label class="t">
                                {l s='Yes' mod='prestabtwobregistration'}
                            </label>
                            <input
                                {if isset($smarty.post.is_required) && $smarty.post.is_required == 0}
                                    checked="checked"
                                {else if isset($registrationFields) && $registrationFields->is_mandatory == 0 && !isset($smarty.post.is_required)}
                                    checked="checked"
                                {/if}
                                type="radio"
                                name="is_required"
                                value="0">
                            <label class="t">
                                {l s='No' mod='prestabtwobregistration'}
                            </label>
                            <a class="slide-button btn-news">
                            </a>
                        </span>
                    </div>
                </div>

                <div class="form-group clearfix">
                    <label class="col-md-3 control-label">
                        {l s='Dependant Field' mod='prestabtwobregistration'}
                    </label>
                    <div class="col-md-6">
                        <span class="switch prestashop-switch fixed-width-lg">
                            <input
                                {if isset($smarty.post.PRESTA_DEPENDANT_FIELDS) && $smarty.post.PRESTA_DEPENDANT_FIELDS == 1}
                                    checked="checked"
                                {else if isset($registrationFields) && $registrationFields->is_dependant == 1}
                                    checked="checked"
                                {/if}
                                type="radio"
                                name="PRESTA_DEPENDANT_FIELDS"
                                id="PRESTA_DEPENDANT_FIELDS_on"
                                value="1">
                            <label class="t" for="PRESTA_DEPENDANT_FIELDS_on">
                                {l s='Yes' mod='prestabtwobregistration'}
                            </label>
                            <input
                                {if isset($smarty.post.PRESTA_DEPENDANT_FIELDS) && $smarty.post.PRESTA_DEPENDANT_FIELDS == 0}
                                    checked="checked"
                                {else if isset($registrationFields) && $registrationFields->is_dependant == 0}
                                    checked="checked"
                                {else if !isset($registrationFields)}
                                    checked="checked"
                                {/if}
                                type="radio"
                                name="PRESTA_DEPENDANT_FIELDS"
                                id="PRESTA_DEPENDANT_FIELDS_off"
                                value="0">
                            <label class="t" for="PRESTA_DEPENDANT_FIELDS_off">
                                {l s='No' mod='prestabtwobregistration'}
                            </label>
                            <a class="slide-button btn-custom-logo">
                            </a>
                        </span>
                    </div>
                </div>

                <div class="form-group clearfix presta_rf_dependant_fields">
                    {if isset($fieldList) && $fieldList}
                        <label for="form-label" class="col-md-3 control-label">
                            {l s='Select Dependant Field' mod='prestabtwobregistration'}
                        </label>
                        <div class="col-md-2">
                            <select class="form-control presta_dependent" name="presta_dependant_field" id="postion4">
                                <option value="0">{l s='Choose Depandant Field' mod='prestabtwobregistration'}</option>
                                {foreach $fieldList as $list}
                                    <option
                                    {if isset($registrationFields) && $registrationFields->id_dependant_field}
                                        selected="selected"
                                    {/if}
                                        value="{$list.id_presta_btwob_custom_fields|escape:'htmlall':'UTF-8'}">{$list.field_title|escape:'htmlall':'UTF-8'}
                                    </option>
                                {/foreach}
                            </select>
                        </div>
                        <input type="hidden" name="presta_check" value="1">
                    {else}
                    <p class="col-md-offset-3 alert alert-info">&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                        {l s='No field found related to Radio | Yes/No | Dropdown type.' mod='prestabtwobregistration'}
                        <input type="hidden" name="presta_check" value="0">
                    </p>
                    {/if}
                </div>

                <div class="form-group clearfix" id="presta_dependant_field"></div>
            </div>
        </div>
        <div class="panel-footer">
            <button type="submit" value="1" name="submit" class="btn btn-default pull-right">
                <i class="process-icon-save"></i>
                {l s='Save' mod='prestabtwobregistration'}
            </button>
            <button type="submit" name="staysubmit" class="btn btn-default pull-right">
                <i class="process-icon-save"></i>
                {l s='Save then add another field' mod='prestabtwobregistration'}
            </button>
            <a href="{$urlBack|escape:'htmlall':'UTF-8'}" class="btn btn-primary">{l s='Back' mod='prestabtwobregistration'}</a>
        </div>
    </div>
</form>
