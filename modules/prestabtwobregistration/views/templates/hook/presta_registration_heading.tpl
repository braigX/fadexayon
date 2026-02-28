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
<input type="hidden" name="prestaBtoBRegistration" value="1">
{if isset($registrationConfigure) && $registrationConfigure->enable_custom_fields == 1}
    {if isset($fieldInfo) && $fieldInfo}
        <h4 class = "custom-heading-feild">{$presta_custom_field_heading|escape:'htmlall':'UTF-8'}</h4>
    {/if}
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
                                    {if $field['is_mandatory'] == '1'}<span class="required">*</span>{/if}
                                    {$field.field_title|escape:'htmlall':'UTF-8'}
                                </label>
                                <div class="col-md-6">
                                    <input
                                        class="form-control"
                                        {if isset($registrationConfigure->customer_edit) && $registrationConfigure->customer_edit == '0' && isset($presta_controller_name) && $presta_controller_name == 'identity'}disabled="disabled"{/if}
                                        name="{$presta_custom|escape:'htmlall':'UTF-8'}"
                                        type="text"
                                        {if isset($presta_controller_name) && $presta_controller_name == 'identity'}
                                            value="{if isset($smarty.post.$presta_custom) && $smarty.post.$presta_custom|escape:'htmlall':'UTF-8'}{$smarty.post.$presta_custom|escape:'htmlall':'UTF-8'}{else if isset($field.selected_value)|escape:'htmlall':'UTF-8'}{$field.selected_value|escape:'htmlall':'UTF-8'}{else}{$field.default_value|escape:'htmlall':'UTF-8'}{/if}"
                                        {else}
                                            value="{if isset($smarty.post.$presta_custom) && $smarty.post.$presta_custom|escape:'htmlall':'UTF-8'}{$smarty.post.$presta_custom|escape:'htmlall':'UTF-8'}{/if}"
                                        {/if}
                                        placeholder="{if isset($field.default_value)}{$field.default_value|escape:'htmlall':'UTF-8'}{/if}">
                                        {if isset($presta_errors[$presta_custom]) && $presta_errors[$presta_custom]}
                                            <p class="presta-custom-field-errors">{$presta_errors[$presta_custom]|escape:'htmlall':'UTF-8'}</p>
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
                                        {if isset($registrationConfigure->customer_edit) && $registrationConfigure->customer_edit == '0' && isset($presta_controller_name) && $presta_controller_name == 'identity'}disabled="disabled"{/if}
                                        name="{$presta_custom|escape:'htmlall':'UTF-8'}"
                                        placeholder="{if isset($field.default_value)}{$field.default_value|escape:'htmlall':'UTF-8'}{/if}">{if isset($presta_controller_name) && $presta_controller_name == 'identity'}{if isset($smarty.post.$presta_custom) && $smarty.post.$presta_custom}{$smarty.post.$presta_custom|escape:'htmlall':'UTF-8'}{else if isset($field.selected_value)}{$field.selected_value|escape:'htmlall':'UTF-8'}{else}{$field.default_value|escape:'htmlall':'UTF-8'}{/if}{/if}{if isset($smarty.post.$presta_custom) && $smarty.post.$presta_custom|escape:'htmlall':'UTF-8'}{$smarty.post.$presta_custom|escape:'htmlall':'UTF-8'}{/if}</textarea>
                                        {if isset($presta_errors[$presta_custom]) && $presta_errors[$presta_custom]}
                                            <p class="presta-custom-field-errors">{$presta_errors[$presta_custom]|escape:'htmlall':'UTF-8'}</p>
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
                                        {if isset($registrationConfigure->customer_edit) && $registrationConfigure->customer_edit == '0' && isset($presta_controller_name) && $presta_controller_name == 'identity'}disabled="disabled"{/if}
                                        class="form-control"
                                        name="{$presta_custom|escape:'htmlall':'UTF-8'}"
                                        {if isset($presta_controller_name) && $presta_controller_name == 'identity'}
                                            value="{if isset($smarty.post.$presta_custom) && $smarty.post.$presta_custom|escape:'htmlall':'UTF-8'}{$smarty.post.$presta_custom|escape:'htmlall':'UTF-8'}{else if isset($field.selected_value)|escape:'htmlall':'UTF-8'}{$field.selected_value|escape:'htmlall':'UTF-8'}{else}{$field.default_value|escape:'htmlall':'UTF-8'}{/if}"
                                        {else}
                                            value="{if isset($smarty.post.$presta_custom) && $smarty.post.$presta_custom|escape:'htmlall':'UTF-8'}{$smarty.post.$presta_custom|escape:'htmlall':'UTF-8'}{else}{$field.default_value|escape:'htmlall':'UTF-8'}{/if}"
                                        {/if}>
                                        {if isset($presta_errors[$presta_custom]) && $presta_errors[$presta_custom]}
                                            <p class="presta-custom-field-errors">{$presta_errors[$presta_custom]|escape:'htmlall':'UTF-8'}</p>
                                        {/if}
                                </div>
                            </div>
                        {/if}
                        {if $field['field_type'] === $YESNO}
                            <div class="form-group row presta_field-dependant">
                                <label class="col-md-3 form-control-label" for="">
                                    {if $field['is_mandatory'] == '1'}<span class="required">*</span>{/if}
                                    {$field.field_title|escape:'htmlall':'UTF-8'}
                                </label>
                                <div class="col-md-9 presta_yes_no" data-id="{$field.id_presta_btwob_custom_fields|escape:'htmlall':'UTF-8'}">
                                    <span class="switch prestashop-switch fixed-width-lg">
                                        <input
                                            {if isset($registrationConfigure->customer_edit) && $registrationConfigure->customer_edit == '0' && isset($presta_controller_name) && $presta_controller_name == 'identity'}disabled="disabled"{/if}
                                            type="radio"
                                            name="{$presta_custom|escape:'htmlall':'UTF-8'}"
                                            class="presta_yes_no"
                                            {if isset($smarty.post.$presta_custom) && $smarty.post.$presta_custom == 1}checked="checked"{else if isset($field.selected_value) && $field.selected_value == 1}checked="checked"{/if}
                                            value="1">
                                        <label class="t">{l s='Yes' mod='prestabtwobregistration'}</label>
                                        <input
                                            {if isset($registrationConfigure->customer_edit) && $registrationConfigure->customer_edit == '0' && isset($presta_controller_name) && $presta_controller_name == 'identity'}disabled="disabled"{/if}
                                            type="radio"
                                            name="{$presta_custom|escape:'htmlall':'UTF-8'}"
                                            class="presta_yes_no"
                                            {if isset($smarty.post.$presta_custom) && $smarty.post.$presta_custom == 0}checked="checked"{else if isset($field.selected_value) && $field.selected_value == 0}checked="checked"{/if}
                                            value="0">
                                        <label class="t">{l s='No' mod='prestabtwobregistration'}</label>
                                        <a class="slide-button btn-news"></a>
                                    </span>
                                    {if isset($presta_errors[$presta_custom]) && $presta_errors[$presta_custom]}
                                        <p class="presta-custom-field-errors">{$presta_errors[$presta_custom]|escape:'htmlall':'UTF-8'}</p>
                                    {/if}
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
                                        {if isset($registrationConfigure->customer_edit) && $registrationConfigure->customer_edit == '0' && isset($presta_controller_name) && $presta_controller_name == 'identity'}disabled="disabled"{/if}
                                        class="form-control presta_rf_selected"
                                        name="{$presta_custom|escape:'htmlall':'UTF-8'}[]">
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
                                    <p class="presta-custom-field-errors" style="margin-left:154px;">{$presta_errors[$presta_custom]|escape:'htmlall':'UTF-8'}</p>
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
                                        {if isset($registrationConfigure->customer_edit) && $registrationConfigure->customer_edit == '0' && isset($presta_controller_name) && $presta_controller_name == 'identity'}disabled="disabled"{/if}
                                        id="presta-dropdown"
                                        class="form-control form-control-select"
                                        name="{$presta_custom|escape:'htmlall':'UTF-8'}">
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
                                <p class="presta-custom-field-errors" style="margin-left:154px;">{$presta_errors[$presta_custom]|escape:'htmlall':'UTF-8'}</p>
                            {/if}
                        {/if}

                        {if $field['field_type'] === $CHECKBOX}
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label" for="">
                                    {if $field['is_mandatory'] == '1'}<span class="required">*</span>{/if}
                                    {$field.field_title|escape:'htmlall':'UTF-8'}
                                </label>
                                <div class="Checkbox">
                                    <span class="custom-checkbox">
                                        {if isset($field['values']) && $field['values']}
                                            {foreach $field['values'] as $value}
                                                <input
                                                    {if isset($registrationConfigure->customer_edit) && $registrationConfigure->customer_edit == '0' && isset($presta_controller_name) && $presta_controller_name == 'identity'}disabled="disabled"{/if}
                                                    {if isset($smarty.post.$presta_custom) && $smarty.post.$presta_custom == $value['id_multi_value']}
                                                        checked="checked"
                                                    {else if isset($field.selected_value) && $field.selected_value}
                                                        {if in_array($value['id_multi_value'], $field.selected_value)}
                                                        checked="checked"
                                                        {/if}
                                                    {/if}
                                                    name="{$presta_custom|escape:'htmlall':'UTF-8'}[]"
                                                    value="{$value['id_multi_value']|escape:'htmlall':'UTF-8'}"
                                                    type="checkbox"
                                                    class="presta_checkbox">
                                                <span class="ps-shown-by-js">
                                                    <i class="material-icons rtl-no-flip checkbox-checked"></i>
                                                </span>
                                                {$value['value']|escape:'htmlall':'UTF-8'}<br>
                                            {/foreach}
                                        {/if}
                                        {if isset($presta_errors[$presta_custom]) && $presta_errors[$presta_custom]}
                                            <p class="presta-custom-field-errors">{$presta_errors[$presta_custom]|escape:'htmlall':'UTF-8'}</p>
                                        {/if}
                                    </span>
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
                                            {if isset($registrationConfigure->customer_edit) && $registrationConfigure->customer_edit == '0' && isset($presta_controller_name) && $presta_controller_name == 'identity'}disabled="disabled"{/if}
                                            name="{$presta_custom|escape:'htmlall':'UTF-8'}"
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
                                        <p class="presta-custom-field-errors">{$presta_errors[$presta_custom]|escape:'htmlall':'UTF-8'}</p>
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
                                    <input
                                        {if isset($registrationConfigure->customer_edit) && $registrationConfigure->customer_edit == '0' && isset($presta_controller_name) && $presta_controller_name == 'identity'}disabled="disabled"{/if}
                                        style="margin-top:15px;"
                                        type="file"
                                        class="presta_file_type"
                                        name="{$presta_custom|escape:'htmlall':'UTF-8'}"
                                        title="{l s='Click here to upload image' mod='prestabtwobregistration'}">
                                        <p class="presta_image_type" style="font-size: 12px;margin-top: 5px;">
                                            {l s='Allowed file types:' mod='prestabtwobregistration'}{$field.file_types|escape:'htmlall':'UTF-8'}
                                        </p>
                                        {if isset($presta_errors[$presta_custom]) && $presta_errors[$presta_custom]}
                                            <p class="presta-custom-field-errors">{$presta_errors[$presta_custom]|escape:'htmlall':'UTF-8'}</p>
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
{/if}
{if isset($registrationConfigure) && $registrationConfigure->enable_group_selection == 1}
    <div class="form-group row ">
        <label class="col-md-3 form-control-label required">{l s='Customer Group' mod='prestabtwobregistration'}</label>
        <div class="col-md-6">
            <select
                class="form-control form-control-select"
                name="customer_group"
                required="required"
                {if isset($registrationConfigure->customer_edit) && $registrationConfigure->customer_edit == '0' && isset($presta_controller_name) && $presta_controller_name == 'identity'}disabled="disabled"{/if}>
                <option value="" disabled="disabled" selected="">{l s='-- please choose --' mod='prestabtwobregistration'}</option>
                {if isset($presta_groups) && $presta_groups}
                    {foreach $presta_groups as $groups}
                        <option
                            {if isset($presta_default_customer_group) && $presta_default_customer_group == $groups.id_group}selected="selected"{/if}
                            value="{$groups.id_group|escape:'htmlall':'UTF-8'}">{$groups.name|escape:'htmlall':'UTF-8'}
                        </option>
                    {/foreach}
                {/if}
            </select>
            <div style="display:none;" class="alert alert-warning" id="validateInfo">
                {l s='Your account will be validated by admin' mod='prestabtwobregistration'}
            </div>
        </div>
        <div class="col-md-3 form-control-comment"></div>
    </div>
{/if}
{if $registrationConfigure->enable_google_recaptcha == '1'}
    {if $registrationConfigure->recaptcha_type == 1}
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
        <div class="form-group row ">
            <label class="col-md-3 form-control-label"></label>
            <div class="col-md-6">
                <div
                    class="g-recaptcha"
                    data-theme="light"
                    data-size="Normal"
                    data-sitekey="{$registrationConfigure->site_key|escape:'htmlall':'UTF-8'}">
                </div>
            </div>
            <div class="col-md-3 form-control-comment"></div>
        </div>
    {else}
        <div class="clearfix form-group row">
            <label class="col-md-3 form-control-label">
                <strong>{l s='Enter Captcha' mod='prestabtwobregistration'}</strong>
            </label>
            <div class="col-md-6">
                <input autocomplete="off" type="text" name="presta_imgcaptcha" required="required" class="form-control" />
                <p><br />
                    <img src="{$presta_recaptcha_process|escape:'htmlall':'UTF-8'}" id="presta_captcha_image">
                </p>
                <p>
                    {l s='Can\'t read the image?' mod='prestabtwobregistration'}
                    <a href='javascript: refreshCaptcha();'>
                    {l s='Click here' mod='prestabtwobregistration'}</a> {l s='to refresh' mod='prestabtwobregistration'}
                </p>
            </div>
        </div>
    {/if}
{/if}
