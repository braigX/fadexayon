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
{if isset($dependantFields) && $dependantFields}
    {foreach $dependantFields as $field}
        {assign var="presta_custom" value="presta_custom_{$field.id_presta_btwob_custom_fields}"}

        {if $field['field_type'] === $TEXT}
            {if isset($currentFieldId) && $currentFieldId}
                <div
                    data-custom="{$presta_custom|escape:'htmlall':'UTF-8'}"
                    class="form-group row customIdField-{$currentFieldId|escape:'htmlall':'UTF-8'}">
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
                            {if isset($registrationConfigure->customer_edit) && $registrationConfigure->customer_edit == '0' && isset($front_controller) && $front_controller == 'identity'}
                                disabled="disabled"
                            {/if}
                            value="{if isset($smarty.post.$presta_custom) && $smarty.post.$presta_custom|escape:'htmlall':'UTF-8'}{$smarty.post.$presta_custom|escape:'htmlall':'UTF-8'}{else if isset($field.selected_value)|escape:'htmlall':'UTF-8'}{$field.selected_value|escape:'htmlall':'UTF-8'}{else}{$field.default_value|escape:'htmlall':'UTF-8'}{/if}"
                            {if $field['is_mandatory'] == '1'}
                                required="required"
                            {/if}
                            />
                    </div>
                </div>
            {/if}
        {/if}

        {if $field['field_type'] === $TEXTAREA}
            <div data-custom="{$presta_custom|escape:'htmlall':'UTF-8'}" class="form-group row customIdField-{$currentFieldId|escape:'htmlall':'UTF-8'}">
                <label class="col-md-3 form-control-label" for="">
                    {if $field['is_mandatory'] == '1'}
                        <span class="required">*</span>
                    {/if}
                    {$field.field_title|escape:'htmlall':'UTF-8'}
                </label>
                <div class="col-md-6">
                    <textarea
                        id=""
                        class="form-control"
                        name="{$presta_custom|escape:'htmlall':'UTF-8'}"
                        {if isset($registrationConfigure->customer_edit) && $registrationConfigure->customer_edit == '0' && isset($front_controller) && $front_controller == 'identity'}
                            disabled="disabled"
                        {/if}
                        placeholder="{if isset($smarty.post.$presta_custom) && $smarty.post.$presta_custom|escape:'htmlall':'UTF-8'}{$smarty.post.$presta_custom|escape:'htmlall':'UTF-8'}{else}{$field.default_value|escape:'htmlall':'UTF-8'}{/if}"
                        {if $field['is_mandatory'] == '1'}
                            required="required"
                        {/if}>{if isset($smarty.post.$presta_custom) && $smarty.post.$presta_custom}{$smarty.post.$presta_custom}{else if isset($field.selected_value)}{$field.selected_value}{else}{$field.default_value}{/if}</textarea>
                </div>
            </div>
        {/if}
        {if $field['field_type'] === $DATE}
            <div data-custom="{$presta_custom|escape:'htmlall':'UTF-8'}" class="form-group row ">
                <label class="col-md-3 form-control-label" for="">
                    {if $field['is_mandatory'] == '1'}
                        <span class="required">*</span>
                    {/if}
                    {$field.field_title|escape:'htmlall':'UTF-8'}
                </label>
                <div class="col-md-6">
                    <input
                        type="date"
                        class="form-control"
                        name="{$presta_custom|escape:'htmlall':'UTF-8'}"
                        {if isset($registrationConfigure->customer_edit) && $registrationConfigure->customer_edit == '0' && isset($front_controller) && $front_controller == 'identity'}
                            disabled="disabled"
                        {/if}
                        value="{if isset($smarty.post.$presta_custom) && $smarty.post.$presta_custom|escape:'htmlall':'UTF-8'}{$smarty.post.$presta_custom|escape:'htmlall':'UTF-8'}{else}{$field.default_value|escape:'htmlall':'UTF-8'}{/if}"
                        {if $field['is_mandatory'] == '1'}
                            required="required"
                        {/if}>
                </div>
            </div>
        {/if}

        {if $field['field_type'] === $YESNO}
            <div data-custom="{$presta_custom|escape:'htmlall':'UTF-8'}" class="form-group row presta_field-dependant">
                <label class="col-md-3 form-control-label" for="">
                    {if $field['is_mandatory'] == '1'}
                        <span class="required">*</span>
                    {/if}
                    {$field.field_title|escape:'htmlall':'UTF-8'}
                </label>
                <div class="col-md-6 presta_yes_no" data-id="{$field.id_presta_btwob_custom_fields|escape:'htmlall':'UTF-8'}">
                    <span class="switch prestashop-switch fixed-width-lg">
                        <input
                            type="radio"
                            name="{$presta_custom|escape:'htmlall':'UTF-8'}"
                            class="presta_yes_no"
                            value="1">
                        <label class="t">
                            {l s='Yes' mod='prestabtwobregistration'}
                        </label>
                        <input
                            type="radio"
                            name="{$presta_custom|escape:'htmlall':'UTF-8'}"
                            class="presta_yes_no"
                            value="0">
                        <label class="t">
                            {l s='No' mod='prestabtwobregistration'}
                        </label>
                        <a class="slide-button btn-news">
                        </a>
                    </span>
                </div>
            </div>
        {/if}

        {if $field['field_type'] === $MULTISELECT}
            <div data-custom="{$presta_custom|escape:'htmlall':'UTF-8'}" class="form-group row customIdField-{$currentFieldId|escape:'htmlall':'UTF-8'}">
                <label class="col-md-3 form-control-label" for="">
                    {if $field['is_mandatory'] == '1'}
                        <span class="required">*</span>
                    {/if}
                    {$field.field_title|escape:'htmlall':'UTF-8'}
                </label>
                <div class="col-md-9">
                    <select
                        multiple="multiple"
                        name="{$presta_custom|escape:'htmlall':'UTF-8'}"
                        {if isset($registrationConfigure->customer_edit) && $registrationConfigure->customer_edit == '0' && isset($front_controller) && $front_controller == 'identity'}
                            disabled="disabled"
                        {/if}
                        class="form-control dependent presta_rf_selected"
                        {if $field['is_mandatory'] == '1'}
                            required="required"
                        {/if}>
                        {foreach $field['values'] as $value}
                            <option
                            {if isset($presta_custom) && $presta_custom == $value['id_multi_value']}
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
        {/if}

        {if $field['field_type'] === $DROPDOWN}
            <div data-custom="{$presta_custom|escape:'htmlall':'UTF-8'}" class="form-group row presta_field-dependant">
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
                        {if isset($registrationConfigure->customer_edit) && $registrationConfigure->customer_edit == '0' && isset($front_controller) && $front_controller == 'identity'}
                            disabled="disabled"
                        {/if}
                        name="{$presta_custom|escape:'htmlall':'UTF-8'}">
                        {foreach $field['values'] as $value}
                            <option value="{$value['id_multi_value']|escape:'htmlall':'UTF-8'}">{$value['value']|escape:'htmlall':'UTF-8'}</option>
                        {/foreach}
                    </select>
                </div>
            </div>
        {/if}

        {if $field['field_type'] === $CHECKBOX}
            <div data-custom="{$presta_custom|escape:'htmlall':'UTF-8'}" class="form-group row customIdField-{$currentFieldId|escape:'htmlall':'UTF-8'}">
                <label class="col-md-3 form-control-label" for="">
                    {if $field['is_mandatory'] == '1'}
                        <span class="required">*</span>
                    {/if}
                    {$field.field_title|escape:'htmlall':'UTF-8'}
                </label>
                <div class="Checkbox">
                    <span class="custom-checkbox">
                        {if isset($field['values']) && $field['values']}
                            {foreach $field['values'] as $value}
                                <input
                                    name="{$presta_custom|escape:'htmlall':'UTF-8'}"
                                    value="{$value['id_multi_value']|escape:'htmlall':'UTF-8'}"
                                    type="checkbox"
                                    {if isset($registrationConfigure->customer_edit) && $registrationConfigure->customer_edit == '0' && isset($front_controller) && $front_controller == 'identity'}
                                        disabled="disabled"
                                    {/if}
                                    class="presta_checkbox">
                                <span class="ps-shown-by-js">
                                    <i class="material-icons rtl-no-flip checkbox-checked"></i>
                                </span>
                                {$value['value']|escape:'htmlall':'UTF-8'}<br>
                            {/foreach}
                        {/if}
                    </span>
                </div>
            </div>
        {/if}

        {if $field['field_type'] === $RADIO}
            <div data-custom="{$presta_custom|escape:'htmlall':'UTF-8'}" class="form-group row presta_field-dependant customIdField-{$currentFieldId|escape:'htmlall':'UTF-8'}">
                <label class="col-md-3 form-control-label" for="">
                    {if $field['is_mandatory'] == '1'}
                        <span class="required">*</span>
                    {/if}
                    {$field.field_title|escape:'htmlall':'UTF-8'}
                </label>
                <div class="radio_button presta_radio_select" data-id="{$field.id_presta_btwob_custom_fields|escape:'htmlall':'UTF-8'}">
                    {foreach $field['values'] as $value}
                        <input
                            {if isset($smarty.post.$presta_custom) && $smarty.post.$presta_custom == $value['id_multi_value']}
                                checked="checked"
                            {/if}
                            name="{$presta_custom|escape:'htmlall':'UTF-8'}"
                            type="radio"
                            class="presta_radio_select"
                            {if isset($registrationConfigure->customer_edit) && $registrationConfigure->customer_edit == '0' && isset($front_controller) && $front_controller == 'identity'}
                                disabled="disabled"
                            {/if}
                            value="{$value['id_multi_value']|escape:'htmlall':'UTF-8'}">
                        {$value['value']|escape:'htmlall':'UTF-8'}
                    {/foreach}
                </div>
            </div>
        {/if}

        {if $field['field_type'] === $FILE}
            <div data-custom="{$presta_custom|escape:'htmlall':'UTF-8'}" class="form-group row customIdField-{$currentFieldId|escape:'htmlall':'UTF-8'}">
                <label class="col-md-3 form-control-label" for="">
                {if $field['is_mandatory'] == '1'}
                    <span class="required">*</span>
                {/if}
                    {$field.field_title|escape:'htmlall':'UTF-8'}
                </label>
                <span class="link-item">
                    <i class="material-icons">{l s='add_a_photo' mod='prestabtwobregistration'}</i>
                </span>
                <input
                    style="padding-top:8px;"
                    type="file"
                    class="presta_file_type"
                    name="{$presta_custom|escape:'htmlall':'UTF-8'}"
                    title="{l s='Click here to upload image' mod='prestabtwobregistration'}"
                    {if $field['is_mandatory'] == '1'}
                        required="required"
                    {/if}>
                    <p class="presta_image_type col-md-offset-3" style="padding:6px; text-align:center;">
                        {l s='Allowed file types:' mod='prestabtwobregistration'}{$field.file_types|escape:'htmlall':'UTF-8'}
                    </p>
            </div>
        {/if}

        {if $field['field_type'] === $MESSAGE}
            <div class="form-group row customIdField-{$currentFieldId|escape:'htmlall':'UTF-8'}">
                <label class="col-md-3 form-control-label {if $field['is_mandatory']}required{/if}" for="">
                {$field.field_title|escape:'htmlall':'UTF-8'}
                </label>
                <div class="col-md-6 presta_notice_messages">
                    {if $field['notice_types'] == '1'}
                        <div class="alert alert-danger">
                        <h4 class="">{$field.notice_message|escape:'htmlall':'UTF-8'}</h4>
                        </div>
                    {/if}
                    {if $field['notice_types'] == '2'}
                        <div class="alert alert-warning">
                        <h4 class="">{$field.notice_message|escape:'htmlall':'UTF-8'}</h4>
                        </div>
                    {/if}
                    {if $field['notice_types'] == '3'}
                        <div class="alert alert-info">
                        <h4 class="">{$field.notice_message|escape:'htmlall':'UTF-8'}</h4>
                        </div>
                    {/if}
                    {if $field['notice_types'] == '4'}
                        <div class="alert alert-success">
                        <h4 class="">{$field.notice_message|escape:'htmlall':'UTF-8'}</h4>
                        </div>
                    {/if}
                </div>
            </div>
        {/if}
    {/foreach}
{/if}
