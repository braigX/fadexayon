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
<div class="prestaregistrationheadings" id="presta_custom_field">
    <div style="display:none;">
        {if isset($presta_errors) && $presta_errors}
            {foreach $presta_errors as $key => $custom_error}
                <span class="presta_custom_errors" data-error="{$key|escape:'htmlall':'UTF-8'}" data-error-value="{$custom_error|escape:'htmlall':'UTF-8'}"></span>
            {/foreach}
        {/if}
    </div>
    {if isset($fieldInfo) && $fieldInfo}
        {foreach $fieldInfo as $allFields}
            {if isset($allFields.fields) && $allFields.fields}
                {foreach $allFields.fields as $field}
                    {assign var="presta_custom" value="presta_custom_{$field.id_presta_btwob_custom_fields}"}
                    {if $field['field_type'] === $TEXT}
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="">
                                {if $field['is_mandatory'] == '1'}
                                    <span class="required">*</span>
                                {/if}
                                {$field.field_title|escape:'htmlall':'UTF-8'}
                            </label>
                            <div class="col-md-6">
                                <input
                                    class="form-control"
                                    name="{$presta_custom|escape:'htmlall':'UTF-8'}"
                                    type="text"
                                    {if !Configuration::get('PRESTA_ALLOW_CUSTOMER_EDIT_FIELDS') &&
                                        isset($presta_controller_name) && $presta_controller_name == 'identity'
                                    }
                                        disabled="disabled"
                                    {/if}
                                    {if isset($presta_controller_name) && $presta_controller_name == 'identity'}
                                        value="{if isset($smarty.post.$presta_custom) && $smarty.post.$presta_custom|escape:'htmlall':'UTF-8'}{$smarty.post.$presta_custom|escape:'htmlall':'UTF-8'}{else if isset($field.selected_value)|escape:'htmlall':'UTF-8'}{$field.selected_value|escape:'htmlall':'UTF-8'}{else}{$field.default_value|escape:'htmlall':'UTF-8'}{/if}"
                                    {/if}
                                    placeholder="{if isset($field.default_value)}{$field.default_value|escape:'htmlall':'UTF-8'}{/if}"
                                    value="{if isset($smarty.post.$presta_custom) && $smarty.post.$presta_custom|escape:'htmlall':'UTF-8'}{$smarty.post.$presta_custom|escape:'htmlall':'UTF-8'}{/if}">
                                    {if isset($presta_errors[$presta_custom]) && $presta_errors[$presta_custom]}
                                        <p style="color:red;">{$presta_errors[$presta_custom]|escape:'htmlall':'UTF-8'}</p>
                                    {/if}
                            </div>
                        </div>
                    {/if}

                    {if $field['field_type'] === $TEXTAREA}
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="">
                            {if $field['is_mandatory'] == '1'}
                                <span class="required">*</span>
                            {/if}
                                {$field.field_title|escape:'htmlall':'UTF-8'}
                            </label>
                            <div class="col-md-6">
                                <textarea
                                    class="form-control"
                                    name="{$presta_custom|escape:'htmlall':'UTF-8'}"
                                    {if !Configuration::get('PRESTA_ALLOW_CUSTOMER_EDIT_FIELDS') &&
                                        isset($presta_controller_name) && $presta_controller_name == 'identity'
                                    }
                                        disabled="disabled"
                                    {/if}
                                    placeholder="{if isset($field.default_value)}{$field.default_value|escape:'htmlall':'UTF-8'}{/if}">{if isset($presta_controller_name) && $presta_controller_name == 'identity'}{if isset($smarty.post.$presta_custom) && $smarty.post.$presta_custom}{$smarty.post.$presta_custom|escape:'htmlall':'UTF-8'}{else if isset($field.selected_value)}{$field.selected_value|escape:'htmlall':'UTF-8'}{else}{$field.default_value|escape:'htmlall':'UTF-8'}{/if}{/if}{if isset($smarty.post.$presta_custom) && $smarty.post.$presta_custom|escape:'htmlall':'UTF-8'}{$smarty.post.$presta_custom|escape:'htmlall':'UTF-8'}{/if}</textarea>
                                    {if isset($presta_errors[$presta_custom]) && $presta_errors[$presta_custom]}
                                        <p style="color:red;">{$presta_errors[$presta_custom]|escape:'htmlall':'UTF-8'}</p>
                                    {/if}
                            </div>
                        </div>
                    {/if}

                    {if $field['field_type'] === $DATE}
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="">
                                {if $field['is_mandatory'] == '1'}
                                    <span class="required">*</span>
                                {/if}
                                {$field.field_title|escape:'htmlall':'UTF-8'}
                            </label>
                            <div class="col-md-6">
                                <input
                                    type="date"
                                    id=""
                                    class="form-control"
                                    name="{$presta_custom|escape:'htmlall':'UTF-8'}"
                                    {if isset($presta_controller_name) && $presta_controller_name == 'identity'}
                                    value="{if isset($smarty.post.$presta_custom) && $smarty.post.$presta_custom|escape:'htmlall':'UTF-8'}{$smarty.post.$presta_custom|escape:'htmlall':'UTF-8'}{else if isset($field.selected_value)|escape:'htmlall':'UTF-8'}{$field.selected_value|escape:'htmlall':'UTF-8'}{else}{$field.default_value|escape:'htmlall':'UTF-8'}{/if}"
                                    {/if}
                                    {if !Configuration::get('PRESTA_ALLOW_CUSTOMER_EDIT_FIELDS') &&
                                        isset($presta_controller_name) && $presta_controller_name == 'identity'
                                    }
                                        disabled="disabled"
                                    {/if}
                                    value="{if isset($smarty.post.$presta_custom) && $smarty.post.$presta_custom|escape:'htmlall':'UTF-8'}{$smarty.post.$presta_custom|escape:'htmlall':'UTF-8'}{else}{$field.default_value|escape:'htmlall':'UTF-8'}{/if}">
                                    {if isset($presta_errors[$presta_custom]) && $presta_errors[$presta_custom]}
                                        <p style="color:red;">{$presta_errors[$presta_custom]|escape:'htmlall':'UTF-8'}</p>
                                    {/if}
                            </div>
                        </div>
                    {/if}

                    {if $field['field_type'] === $YESNO}
                        <div class="form-group row presta_field-dependant">
                            <label class="col-md-3 form-control-label" for="">
                                {if $field['is_mandatory'] == '1'}
                                    <span class="required">*</span>
                                {/if}
                                {$field.field_title|escape:'htmlall':'UTF-8'}
                            </label>
                            <div class="col-md-9 presta_yes_no" data-id="{$field.id_presta_btwob_custom_fields|escape:'htmlall':'UTF-8'}">
                                <span class="switch prestashop-switch fixed-width-lg">
                                    <input
                                        type="radio"
                                        name="{$presta_custom|escape:'htmlall':'UTF-8'}"
                                        class="presta_yes_no"
                                        value="1"
                                        {if !Configuration::get('PRESTA_ALLOW_CUSTOMER_EDIT_FIELDS') &&
                                            isset($presta_controller_name) && $presta_controller_name == 'identity'
                                        }
                                            disabled="disabled"
                                        {/if}
                                        {if isset($smarty.post.$presta_custom) && $smarty.post.$presta_custom == 1}
                                            checked="checked"
                                        {else if isset($field.selected_value) && $field.selected_value == 1}
                                            checked="checked"
                                        {/if}>
                                    <label class="t">{l s='Yes' mod='prestabtwobregistration'}</label>
                                    <input
                                        type="radio"
                                        name="{$presta_custom|escape:'htmlall':'UTF-8'}"
                                        class="presta_yes_no"
                                        {if !Configuration::get('PRESTA_ALLOW_CUSTOMER_EDIT_FIELDS') &&
                                            isset($presta_controller_name) && $presta_controller_name == 'identity'
                                        }
                                            disabled="disabled"
                                        {/if}
                                        {if isset($smarty.post.$presta_custom) && $smarty.post.$presta_custom == 0}
                                            checked="checked"
                                        {else if isset($field.selected_value) && $field.selected_value == 0}
                                            checked="checked"
                                        {/if}
                                        value="0">
                                    <label class="t">{l s='No' mod='prestabtwobregistration'}</label>
                                    <a class="slide-button btn-news"></a>
                                </span>
                                {* {if isset($presta_errors[$presta_custom]) && $presta_errors[$prefieldsescape:'htmlall':'UTF-8'}</p>
                                {/if} *}
                            </div>
                        </div>
                    {/if}

                    {if $field['field_type'] === $MULTISELECT}
                        <div class="form-group row presta_multipleSelected">
                            <label class="col-md-3 form-control-label" for="">
                                {if $field['is_mandatory'] == '1'}
                                    <span class="required">*</span>
                                {/if}
                                {$field.field_title|escape:'htmlall':'UTF-8'}
                            </label>
                            <div class="col-md-9">
                                <select
                                    multiple="multiple"
                                    class="form-control presta_rf_selected"
                                    name="{$presta_custom|escape:'htmlall':'UTF-8'}[]"
                                    {if !Configuration::get('PRESTA_ALLOW_CUSTOMER_EDIT_FIELDS') &&
                                        isset($presta_controller_name) && $presta_controller_name == 'identity'
                                    }
                                        disabled="disabled"
                                    {/if}>
                                    {foreach $field['values'] as $value}
                                        <option
                                            {if isset($smarty.post.$presta_custom) && $smarty.post.$presta_custom == $value['id_multi_value']}
                                                selected="selected"
                                            {else if isset($field.selected_value) && $field.selected_value}
                                                data-id="1"
                                                {if in_array($value['id_multi_value'], $field.selected_value)}
                                                    selected="selected"
                                                {/if}
                                            {/if}
                                            value="{$value['id_multi_value']|escape:'htmlall':'UTF-8'}">{$value['value']|escape:'htmlall':'UTF-8'}
                                        </option>
                                    {/foreach}
                                </select>
                            </div>
                            {if isset($presta_errors[$presta_custom]) && $presta_errors[$presta_custom]}
                                <p style="color:red; margin-left:154px;">{$presta_errors[$presta_custom]|escape:'htmlall':'UTF-8'}</p>
                            {/if}
                        </div>
                    {/if}

                    {if $field['field_type'] === $DROPDOWN}
                        <div class="form-group row presta_field-dependant">
                            <label class="col-md-3 form-control-label" for="">
                                {if $field['is_mandatory'] == '1'}
                                    <span class="required">*</span>
                                {/if}
                                {$field.field_title|escape:'htmlall':'UTF-8'}
                            </label>
                            <div class="col-md-6 presta_dropDown_select" data-id="{$field.id_presta_btwob_custom_fields|escape:'htmlall':'UTF-8'}">
                                <select
                                    id="presta-dropdown"
                                    class="form-control form-control-select"
                                    name="{$presta_custom|escape:'htmlall':'UTF-8'}"
                                    {if !Configuration::get('PRESTA_ALLOW_CUSTOMER_EDIT_FIELDS') &&
                                        isset($presta_controller_name) && $presta_controller_name == 'identity'
                                    }
                                        disabled="disabled"
                                    {/if}>
                                {foreach $field['values'] as $key => $value}
                                    {if ($key == 0)}
                                        <option value="0" selected="selected">{l s='----Choose Option----' mod='prestabtwobregistration'}</option>
                                    {/if}
                                    <option
                                        {if isset($smarty.post.$presta_custom) && $smarty.post.$presta_custom == $value['id_multi_value']}
                                            selected="selected"
                                        {else if isset($field.selected_value) && $field.selected_value == $value['id_multi_value']}
                                            selected="selected"
                                        {/if}
                                            value="{$value['id_multi_value']|escape:'htmlall':'UTF-8'}">{$value['value']|escape:'htmlall':'UTF-8'}
                                    </option>
                                {/foreach}
                                </select>
                            </div>
                        </div>
                        {if isset($presta_errors[$presta_custom]) && $presta_errors[$presta_custom]}
                            <p style="color:red; margin-left:154px;">{$presta_errors[$presta_custom]|escape:'htmlall':'UTF-8'}</p>
                        {/if}
                    {/if}

                    {if $field['field_type'] === $CHECKBOX}
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label">
                                {if $field['is_mandatory'] == '1'}
                                    <span class="required">*</span>
                                {/if}
                                {$field.field_title|escape:'htmlall':'UTF-8'}
                            </label>
                            <div class="clearfix col-md-9 form-control-label">
                                {if isset($field['values']) && $field['values']}
                                    {foreach $field['values'] as $value}
                                        <span class="custom-checkbox">
                                            <label>
                                                <input
                                                    name="{$presta_custom|escape:'htmlall':'UTF-8'}[]"
                                                    type="checkbox"
                                                    value="{$value['id_multi_value']|escape:'htmlall':'UTF-8'}"
                                                    {if $field['is_mandatory'] == '1'}
                                                        required="required"
                                                    {/if}
                                                    {if isset($smarty.post.$presta_custom) && $smarty.post.$presta_custom == $value['id_multi_value']}
                                                        checked="checked"
                                                    {else if isset($field.selected_value) && $field.selected_value}
                                                        {if in_array($value['id_multi_value'], $field.selected_value)}
                                                        checked="checked"
                                                        {/if}
                                                    {/if}
                                                >
                                                <span><i class="material-icons rtl-no-flip checkbox-checked">&#xE5CA;</i></span>
                                                {$value['value']|escape:'htmlall':'UTF-8'}<br>
                                            </label>
                                        </span>
                                    {/foreach}
                                {/if}
                                <p class="presta_error_code" id="presta_error_{$presta_custom|escape:'htmlall':'UTF-8'}"></p>
                            </div>
                        </div>
                    {/if}

                    {if $field['field_type'] === $RADIO}
                        <div class="form-group row presta_field-dependant">
                            <label class="col-md-3 form-control-label" for="">
                                {if $field['is_mandatory'] == '1'}
                                    <span class="required">*</span>
                                {/if}
                                {$field.field_title|escape:'htmlall':'UTF-8'}
                            </label>
                            <div class="radio_button col-md-9 presta_radio_select" data-id="{$field.id_presta_btwob_custom_fields|escape:'htmlall':'UTF-8'}">
                                {foreach $field['values'] as $value}
                                    <input
                                        name="{$presta_custom|escape:'htmlall':'UTF-8'}"
                                        {if !Configuration::get('PRESTA_ALLOW_CUSTOMER_EDIT_FIELDS') &&
                                            isset($presta_controller_name) && $presta_controller_name == 'identity'
                                        }
                                            disabled="disabled"
                                        {/if}
                                        {if isset($smarty.post.$presta_custom) && $smarty.post.$presta_custom == $value['id_multi_value']}
                                            checked="checked"
                                        {else if isset($field.selected_value) && $field.selected_value == $value['id_multi_value']}
                                            checked="checked"
                                        {/if}
                                        value="{$value['id_multi_value']|escape:'htmlall':'UTF-8'}"
                                        type="radio"
                                        class="presta_radio"
                                    >{$value['value']|escape:'htmlall':'UTF-8'}
                                {/foreach}
                            </div>
                                {if isset($presta_errors[$presta_custom]) && $presta_errors[$presta_custom]}
                                    <p style="color:red;">{$presta_errors[$presta_custom]|escape:'htmlall':'UTF-8'}</p>
                                {/if}
                        </div>
                    {/if}

                    {if $field['field_type'] === $FILE}
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="">
                            {if $field['is_mandatory'] == '1'}
                                <span class="required">*</span>
                            {/if}
                                {$field.field_title|escape:'htmlall':'UTF-8'}
                            </label>
                            <div class="col-md-9 clearfix">
                                {if isset($presta_controller_name) && $presta_controller_name == 'identity'}
                                    {if isset($field['selected_value_id'])}
                                        <a
                                            href="{$downloadLink|escape:'htmlall':'UTF-8'}&id={$field['selected_value_id']|escape:'htmlall':'UTF-8'}" class="btn btn-primary btn-lg">
                                            {l s='View File' mod='prestabtwobregistration'}
                                        </a><br/>
                                    {/if}
                                {/if}
                                {if !Configuration::get('PRESTA_ALLOW_CUSTOMER_EDIT_FIELDS') &&
                                    isset($presta_controller_name) && $presta_controller_name == 'identity'
                                }
                                {else}
                                <input
                                    style="margin-top:15px;"
                                    type="file"
                                    class="presta_file_type"
                                    name="{$presta_custom|escape:'htmlall':'UTF-8'}"
                                    title="{l s='Click here to upload image' mod='prestabtwobregistration'}">
                                    <p class="presta_image_type" style="font-size: 12px;margin-top: 5px;">
                                        {l s='Allowed file types:' mod='prestabtwobregistration'}{$field.file_types|escape:'htmlall':'UTF-8'}
                                    </p>
                                    {if isset($presta_errors[$presta_custom]) && $presta_errors[$presta_custom]}
                                        <p style="color:red;">{$presta_errors[$presta_custom]|escape:'htmlall':'UTF-8'}</p>
                                    {/if}
                                {/if}
                            </div>
                        </div>
                    {/if}

                    {if $field['field_type'] === $MESSAGE}
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label">
                                {$field.field_title|escape:'htmlall':'UTF-8'}
                            </label>
                            <div class="col-md-6 presta_notice_messages">
                                <div class="alert alert-{if $field['notice_types'] == '1'}danger{else if $field['notice_types'] == '2'}warning{else if $field['notice_types'] == '3'}info{else if $field['notice_types'] == '4'}success{/if}">
                                    <h4 class="">{$field.notice_message|escape:'htmlall':'UTF-8'}</h4>
                                </div>
                            </div>
                        </div>
                    {/if}
                {/foreach}
            {/if}
        {/foreach}
    {/if}
</div>
