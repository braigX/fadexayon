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
<div class="panel-heading">{l s='Default Setting' mod='prestabtwobregistration'}</div>
<div class="form-wrapper">
    {if count($languages) > 1}
        <div class="form-group clearfix">
            <label for="form-label" class="col-md-3 control-label">
                {l s='Choose Language' mod='prestabtwobregistration'}
            </label>
            <div class="col-md-8">
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
    {/if}

    <div class="form-group clearfix">
        <label class="control-label col-lg-3">{l s='Enable B2B' mod='prestabtwobregistration'}</label>
        <div class="col-lg-5">
            <span class="switch prestashop-switch fixed-width-lg">
                <input
                    type="radio"
                    name="presta_enable_b2b"
                    id="presta_enable_b2b_on"
                    class="form-control"
                    value="1"
                    {if isset($smarty.post.presta_enable_b2b) && $smarty.post.presta_enable_b2b == 1}
                        checked="checked"
                    {elseif isset($presta_config) && $presta_config->enable_b2b == 1}
                        checked="checked"
                    {/if}>
                <label for="presta_enable_b2b_on">{l s='Yes' mod='prestabtwobregistration'}</label>
                <input
                    type="radio"
                    name="presta_enable_b2b"
                    id="presta_enable_b2b_off"
                    class="form-control"
                    value="0"
                    {if isset($smarty.post.presta_enable_b2b) && $smarty.post.presta_enable_b2b == 0}
                        checked="checked"
                    {elseif isset($presta_config) && $presta_config->enable_b2b == 0}
                        checked="checked"
                    {elseif !isset($smarty.post.presta_enable_b2b) && !isset($presta_config)}
                        checked="checked"
                    {/if}>
                <label for="presta_enable_b2b_off">{l s='No' mod='prestabtwobregistration'}</label>
                <a class="slide-button btn"></a>
            </span>
            <p class="help-block">
                {l s='If Disabled, Admin has be validate customer in order to activate them.' mod='prestabtwobregistration'}
            </p>
        </div>
    </div>

    <div class="form-group clearfix">
        <label class="control-label col-lg-3">{l s='B2B Customer Auto Approval' mod='prestabtwobregistration'}</label>
        <div class="col-lg-5">
            <span class="switch prestashop-switch fixed-width-lg">
                <input
                    type="radio"
                    name="presta_b2b_customer_auto_approval"
                    id="presta_b2b_customer_auto_approval_on"
                    class="form-control"
                    value="1"
                    {if isset($smarty.post.presta_b2b_customer_auto_approval) && $smarty.post.presta_b2b_customer_auto_approval == 1}
                        checked="checked"
                    {elseif isset($presta_config) && $presta_config->b2b_customer_auto_approval == 1}
                        checked="checked"
                    {elseif !isset($smarty.post.presta_b2b_customer_auto_approval)}
                        checked="checked"
                    {/if}>
                <label for="presta_b2b_customer_auto_approval_on">{l s='Yes' mod='prestabtwobregistration'}</label>
                <input
                    type="radio"
                    name="presta_b2b_customer_auto_approval"
                    id="presta_b2b_customer_auto_approval_off"
                    class="form-control"
                    value="0"
                    {if isset($smarty.post.presta_b2b_customer_auto_approval) && $smarty.post.presta_b2b_customer_auto_approval == 0}
                        checked="checked"
                    {elseif isset($presta_config) && $presta_config->b2b_customer_auto_approval == 0}
                        checked="checked"
                    {/if}>
                <label for="presta_b2b_customer_auto_approval_off">{l s='No' mod='prestabtwobregistration'}</label>
                <a class="slide-button btn"></a>
            </span>
            <p class="help-block">
                {l s='If Disabled, Admin has be validate customer in order to activate them.'
                mod='prestabtwobregistration'}
            </p>
        </div>
    </div>

    <div class="form-group clearfix presta-pending-account-text">
        <label class="control-label col-lg-3 ">{l s='Pending Account Message Text' mod='prestabtwobregistration'}</label>
        <div class="col-lg-8">
            {foreach $languages as $lang}
                {assign var="presta_current_div" value="presta_current_div_`$lang.id_lang`"}
                {assign var="name" value="presta_pending_account_message_text_`$lang.id_lang`"}
                <div class="presta_div {$presta_current_div|escape:'html':'UTF-8'}"
                    {if $current_lang->id != $lang.id_lang}style="display:none;" {/if}>
                    <textarea
                        type="text"
                        name="{$name|escape:'htmlall':'UTF-8'}"
                        autocomplete="off"
                        class="form-control presta-tiny-mce">{if isset($smarty.post.{$name|escape:'htmlall':'UTF-8'})}{$smarty.post.{$name|escape:'htmlall':'UTF-8'}|escape:'htmlall':'UTF-8'}{else if isset($presta_config->pending_account_message_text[$lang.id_lang])}{$presta_config->pending_account_message_text[$lang.id_lang|escape:'html':'UTF-8']}{else}{l s='We appreciate your registration. Your account is currently awaiting admin approval. Once your account has been approved and enabled, you will be notified.' mod='prestabtwobregistration'}{/if}</textarea>
                </div>
            {/foreach}
        </div>
    </div>

    <div class="form-group clearfix">
        <label class="control-label col-lg-3">{l s='Enable Custom Fields' mod='prestabtwobregistration'}</label>
        <div class="col-lg-5">
            <span class="switch prestashop-switch fixed-width-lg">
                <input
                    type="radio"
                    name="presta_enable_custom_fields"
                    id="presta_enable_custom_fields_on"
                    class="form-control"
                    value="1"
                    {if isset($smarty.post.presta_enable_custom_fields) && $smarty.post.presta_enable_custom_fields == 1}
                        checked="checked"
                    {elseif isset($presta_config) && $presta_config->enable_custom_fields == 1}
                        checked="checked"
                    {/if}>
                <label for="presta_enable_custom_fields_on">{l s='Yes' mod='prestabtwobregistration'}</label>
                <input
                    type="radio"
                    name="presta_enable_custom_fields"
                    id="presta_enable_custom_fields_off"
                    class="form-control"
                    value="0"
                    {if isset($smarty.post.presta_enable_custom_fields) && $smarty.post.presta_enable_custom_fields == 0}
                        checked="checked"
                    {elseif isset($presta_config) && !isset($smarty.post.presta_enable_custom_fields) && $presta_config->enable_custom_fields == 0}
                        checked="checked"
                    {elseif !isset($smarty.post.presta_enable_custom_fields) && !isset($presta_config)}
                        checked="checked"
                    {/if}>
                <label for="presta_enable_custom_fields_off">{l s='No' mod='prestabtwobregistration'}</label>
                <a class="slide-button btn"></a>
            </span>
        </div>
    </div>

    <div class="form-group clearfix presta-top-link">
        <label class="control-label col-lg-3 ">{l s='Top Link Text' mod='prestabtwobregistration'}</label>
        <div class="col-lg-8">
            {foreach $languages as $lang}
                {assign var="presta_current_div" value="presta_current_div_`$lang.id_lang`"}
                {assign var="name" value="presta_top_link_text_`$lang.id_lang`"}
                <div class="presta_div {$presta_current_div|escape:'html':'UTF-8'}"
                    {if $current_lang->id != $lang.id_lang}style="display:none;" {/if}>
                    <input
                        type="text"
                        name="{$name|escape:'htmlall':'UTF-8'}"
                        autocomplete="off"
                        class="form-control"
                        value="{if isset($smarty.post.{$name|escape:'htmlall':'UTF-8'})}{$smarty.post.{$name|escape:'htmlall':'UTF-8'}|escape:'htmlall':'UTF-8'}{else if isset($presta_config->top_link_text[$lang.id_lang])}{$presta_config->top_link_text[$lang.id_lang|escape:'html':'UTF-8']}{/if}">
                </div>
            {/foreach}
            <p class="help-block">
                {l s='Write a title to display link in header at front office. Leave blank if you dose not want to display.' mod='prestabtwobregistration'}
            </p>
        </div>
    </div>
    <div class="form-group clearfix">
        <label class="control-label col-lg-3 ">{l s='Personal Data Heading' mod='prestabtwobregistration'}</label>
        <div class="col-lg-8">
            {foreach $languages as $lang}
                {assign var="presta_current_div" value="presta_current_div_`$lang.id_lang`"}
                {assign var="name" value="presta_personal_data_heading_`$lang.id_lang`"}
                <div class="presta_div {$presta_current_div|escape:'html':'UTF-8'}"
                    {if $current_lang->id != $lang.id_lang}style="display:none;" {/if}>
                    <input
                        type="text"
                        name="{$name|escape:'htmlall':'UTF-8'}"
                        autocomplete="off"
                        class="form-control"
                        value="{if isset($smarty.post.{$name|escape:'htmlall':'UTF-8'})}{$smarty.post.{$name|escape:'htmlall':'UTF-8'}|escape:'htmlall':'UTF-8'}{else if isset($presta_config->personal_data_heading[$lang.id_lang])}{$presta_config->personal_data_heading[$lang.id_lang|escape:'html':'UTF-8']}{/if}">
                </div>
            {/foreach}
        </div>
    </div>

    <div class="form-group clearfix">
        <label class="control-label col-lg-3">{l s='Custom Field Heading' mod='prestabtwobregistration'}</label>
        <div class="col-lg-8">
            {foreach $languages as $lang}
                {assign var="presta_current_div" value="presta_current_div_`$lang.id_lang`"}
                {assign var="name" value="presta_custom_field_heading_`$lang.id_lang`"}
                <div class="presta_div {$presta_current_div|escape:'html':'UTF-8'}"
                    {if $current_lang->id != $lang.id_lang}style="display:none;" {/if}>
                    <input
                        type="text"
                        name="{$name|escape:'htmlall':'UTF-8'}"
                        autocomplete="off"
                        class="form-control"
                        value="{if isset($smarty.post.{$name|escape:'htmlall':'UTF-8'})}{$smarty.post.{$name|escape:'htmlall':'UTF-8'}|escape:'htmlall':'UTF-8'}{else if isset($presta_config->custom_field_heading[$lang.id_lang])}{$presta_config->custom_field_heading[$lang.id_lang|escape:'html':'UTF-8']}{/if}">
                </div>
            {/foreach}
        </div>
    </div>

    <div class="form-group clearfix">
        <label class="control-label col-lg-3">{l s='Enable Group Selection' mod='prestabtwobregistration'}</label>
        <div class="col-lg-5">
            <span class="switch prestashop-switch fixed-width-lg">
                <input
                    type="radio"
                    name="presta_enable_group_selection_one"
                    id="presta_enable_group_selection_one_on"
                    class="form-control"
                    value="1"
                    {if isset($smarty.post.presta_enable_group_selection_one) && $smarty.post.presta_enable_group_selection_one == 1}
                        checked="checked"
                    {elseif isset($presta_config) && $presta_config->enable_group_selection_one == 1}
                        checked="checked"
                    {/if}>
                <label for="presta_enable_group_selection_one_on">{l s='Yes' mod='prestabtwobregistration'}</label>
                <input
                    type="radio"
                    name="presta_enable_group_selection_one"
                    id="presta_enable_group_selection_one_off"
                    class="form-control"
                    value="0"
                    {if isset($smarty.post.presta_enable_group_selection_one) && $smarty.post.presta_enable_group_selection_one == 0}
                        checked="checked"
                    {elseif isset($presta_config) && !isset($smarty.post.presta_enable_group_selection_one) && $presta_config->enable_group_selection_one == 0}
                        checked="checked"
                    {elseif !isset($smarty.post.presta_enable_group_selection_one) && !isset($presta_config)}
                        checked="checked"
                    {/if}>
                <label for="presta_enable_group_selection_one_off">{l s='No' mod='prestabtwobregistration'}</label>
                <a class="slide-button btn"></a>
            </span>
        </div>
    </div>

    <div class="form-group clearfix presta-group-selection">
        <label class="control-label col-lg-3">{l s=' Group Selection' mod='prestabtwobregistration'}</label>
        <div class="col-lg-5">
            <span class="switch prestashop-switch fixed-width-lg">
                <input
                    type="radio"
                    name="presta_enable_group_selection"
                    id="presta_enable_group_selection_on"
                    class="form-control"
                    value="1"
                    {if isset($smarty.post.presta_enable_group_selection) && $smarty.post.presta_enable_group_selection == 1}
                        checked="checked"
                    {elseif isset($presta_config) && $presta_config->enable_group_selection == 1}
                        checked="checked"
                    {/if}>
                <label for="presta_enable_group_selection_on">{l s='Yes' mod='prestabtwobregistration'}</label>
                <input
                    type="radio"
                    name="presta_enable_group_selection"
                    id="presta_enable_group_selection_off"
                    class="form-control"
                    value="0"
                    {if isset($smarty.post.presta_enable_group_selection) && $smarty.post.presta_enable_group_selection == 0}
                        checked="checked"
                    {elseif isset($presta_config) && !isset($smarty.post.presta_enable_group_selection) && $presta_config->enable_group_selection == 0}
                        checked="checked"
                    {elseif !isset($smarty.post.presta_enable_group_selection) && !isset($presta_config)}
                        checked="checked"
                    {/if}>
                <label for="presta_enable_group_selection_off">{l s='No' mod='prestabtwobregistration'}</label>
                <a class="slide-button btn"></a>
            </span>
        </div>
    </div>

    <div class="form-group clearfix presta-select-group">
        <label class="control-label col-lg-3 required">{l s='Selected Groups' mod='prestabtwobregistration'}</label>
        <div class="col-lg-6">
            <table class="table presta-store-table">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="presta_check_all_groups" class="cursor_pointer " value="1"></th>
                        <th>{l s='Id' mod='prestabtwobregistration'}</th>
                        <th>{l s='Customer group' mod='prestabtwobregistration'}</th>
                    </tr>
                </thead>
                <tbody>
                    {if isset($groups) && $groups}
                        {foreach $groups as $group}
                            <tr>
                                <td>
                                    <input
                                    type="checkbox"
                                    class="cursor_pointer checkbox"
                                    id="presta_group_{$group.id_group|escape:'html':'UTF-8'}"
                                    name="presta_selected_groups_conf[]"
                                    value="{$group.id_group|escape:'html':'UTF-8'}"
                                    {if isset($smarty.post.presta_selected_groups_conf) && $smarty.post.presta_selected_groups_conf == $group.id_group}
                                        checked="checked"
                                    {else if isset($selectedGroups) && in_array($group.id_group,$selectedGroups)}
                                        checked="checked"
                                    {/if}>
                                </td>
                                    <td>{$group.id_group|escape:'html':'UTF-8'}</td>
                                <td>
                                    <label for="presta_group_{$group.id_group|escape:'html':'UTF-8'}">{$group.name|escape:'html':'UTF-8'}</label>
                                </td>
                            </tr>
                        {/foreach}
                    {else}
                        <tr>
                            <td colspan="3" class="text-danger">{l s='There are no any store' mod='prestabtwobregistration'}</td>
                        </tr>
                    {/if}
                </tbody>
            </table>
        </div>
    </div>

    <div class="form-group clearfix presta-assign-groups">
        <label class="control-label col-lg-3 required">{l s='Assign Groups' mod='prestabtwobregistration'}</label>
        <div class="col-lg-6">
            <select name="presta_assign_groups">
                {if isset($groups) && $groups}
                    {foreach $groups as $group}
                        <option
                            class="cursor_pointer checkbox"
                            value="{$group.id_group|escape:'html':'UTF-8'}"
                            {if isset($smarty.post.presta_assign_groups)}{$smarty.post.presta_assign_groups == $group.id_group}
                            selected="selected"
                            {elseif isset($presta_config) && $presta_config->assign_groups == $group.id_group}{$presta_config->assign_groups}
                            selected="selected"
                            {/if}>
                            {$group.name|escape:'html':'UTF-8'}
                        </option>
                    {/foreach}
                {else}
                    {l s='There are no any group' mod='prestabtwobregistration'}
                {/if}
            </select>
        </div>
    </div>

    <div class="form-group clearfix">
        <label class="control-label col-lg-3">{l s='Date of Birth' mod='prestabtwobregistration'}</label>
        <div class="col-lg-5">
            <span class="switch prestashop-switch fixed-width-lg">
                <input
                    type="radio"
                    name="presta_date_of_birth"
                    id="presta_date_of_birth_on"
                    class="form-control"
                    value="1"
                    {if isset($smarty.post.presta_date_of_birth) && $smarty.post.presta_date_of_birth == 1}
                        checked="checked"
                    {elseif isset($presta_config) && $presta_config->date_of_birth == 1}
                        checked="checked"
                    {/if}>
                <label for="presta_date_of_birth_on">{l s='Yes' mod='prestabtwobregistration'}</label>
                <input
                    type="radio"
                    name="presta_date_of_birth"
                    id="presta_date_of_birth_off"
                    class="form-control"
                    value="0"
                    {if isset($smarty.post.presta_date_of_birth) && $smarty.post.presta_date_of_birth == 0}
                        checked="checked"
                    {elseif isset($presta_config) && !isset($smarty.post.presta_date_of_birth) && $presta_config->date_of_birth == 0}
                        checked="checked"
                    {elseif !isset($smarty.post.presta_date_of_birth) && !isset($presta_config)}
                        checked="checked"
                    {/if}>
                <label for="presta_date_of_birth_off">{l s='No' mod='prestabtwobregistration'}</label>
                <a class="slide-button btn"></a>
            </span>
        </div>
    </div>

    <div class="form-group clearfix">
        <label class="control-label col-lg-3">{l s='IDENTIFICATION/Siret Number' mod='prestabtwobregistration'}</label>
        <div class="col-lg-5">
            <span class="switch prestashop-switch fixed-width-lg">
                <input
                    type="radio"
                    name="presta_identification_siret_number"
                    id="presta_identification_siret_number_on"
                    class="form-control"
                    value="1"
                    {if isset($smarty.post.presta_identification_siret_number) && $smarty.post.presta_identification_siret_number == 1}
                        checked="checked"
                    {elseif isset($presta_config) && $presta_config->identification_siret_number == 1}
                        checked="checked"
                    {/if}>
                <label for="presta_identification_siret_number_on">{l s='Yes' mod='prestabtwobregistration'}</label>
                <input
                    type="radio"
                    name="presta_identification_siret_number"
                    id="presta_identification_siret_number_off"
                    class="form-control"
                    value="0"
                    {if isset($smarty.post.presta_identification_siret_number) && $smarty.post.presta_identification_siret_number == 0}
                        checked="checked"
                    {elseif isset($presta_config) && !isset($smarty.post.presta_identification_siret_number) && $presta_config->identification_siret_number == 0}
                        checked="checked"
                    {elseif !isset($smarty.post.presta_identification_siret_number) && !isset($presta_config)}
                        checked="checked"
                    {/if}>
                <label for="presta_identification_siret_number_off">{l s='No' mod='prestabtwobregistration'}</label>
                <a class="slide-button btn"></a>
            </span>
        </div>
    </div>

    <div class="form-group clearfix">
        <label class="control-label col-lg-3">{l s='Enable Address fields on Registration form' mod='prestabtwobregistration'}</label>
        <div class="col-lg-5">
            <span class="switch prestashop-switch fixed-width-lg">
                <input
                    type="radio"
                    name="presta_address"
                    id="presta_address_on"
                    class="form-control"
                    value="1"
                    {if isset($smarty.post.presta_address) && $smarty.post.presta_address == 1}
                        checked="checked"
                    {elseif isset($presta_config) && $presta_config->address == 1}
                        checked="checked"
                    {/if}>
                <label for="presta_address_on">{l s='Yes' mod='prestabtwobregistration'}</label>
                <input
                    type="radio"
                    name="presta_address"
                    id="presta_address_off"
                    class="form-control"
                    value="0"
                    {if isset($smarty.post.presta_address) && $smarty.post.presta_address == 0}
                        checked="checked"
                    {elseif isset($presta_config) && !isset($smarty.post.presta_address) && $presta_config->address == 0}
                        checked="checked"
                    {elseif !isset($smarty.post.presta_address) && !isset($presta_config)}
                        checked="checked"
                    {/if}>
                <label for="presta_address_off">{l s='No' mod='prestabtwobregistration'}</label>
                <a class="slide-button btn"></a>
            </span>
        </div>
    </div>

    <div class="presta-vat-number">
        <div class="form-group clearfix">
            <label class="control-label col-lg-3">{l s='Show VAT number' mod='prestabtwobregistration'}</label>
            <div class="col-lg-5">
                <span class="switch prestashop-switch fixed-width-lg">
                    <input
                        type="radio"
                        name="presta_vat_number"
                        id="presta_vat_number_on"
                        class="form-control"
                        value="1"
                        {if isset($smarty.post.presta_vat_number) && $smarty.post.presta_vat_number == 1}
                            checked="checked"
                        {elseif isset($presta_config) && $presta_config->vat_number == 1}
                            checked="checked"
                        {/if}>
                    <label for="presta_vat_number_on">{l s='Yes' mod='prestabtwobregistration'}</label>
                    <input
                        type="radio"
                        name="presta_vat_number"
                        id="presta_vat_number_off"
                        class="form-control"
                        value="0"
                        {if isset($presta_config) && !isset($smarty.post.presta_vat_number) && $presta_config->vat_number == 0}
                            checked="checked"
                        {elseif isset($smarty.post.presta_vat_number) && $smarty.post.presta_vat_number == 0}
                            checked="checked"
                        {elseif !isset($smarty.post.presta_vat_number) && !isset($presta_config)}
                            checked="checked"
                        {/if}>
                    <label for="presta_vat_number_off">{l s='No' mod='prestabtwobregistration'}</label>
                    <a class="slide-button btn"></a>
                </span>
            </div>
        </div>
        <div class="form-group clearfix presta-required-vat">
            <label class="control-label col-lg-3">{l s='Required VAT number' mod='prestabtwobregistration'}</label>
            <div class="col-lg-5">
                <span class="switch prestashop-switch fixed-width-lg">
                    <input
                        type="radio"
                        name="required_vat_number"
                        id="required_vat_number_on"
                        class="form-control"
                        value="1"
                        {if isset($smarty.post.required_vat_number) && $smarty.post.required_vat_number == 1}
                            checked="checked"
                        {elseif isset($presta_config) && $presta_config->required_vat_number == 1}
                            checked="checked"
                        {/if}>
                    <label for="required_vat_number_on">{l s='Yes' mod='prestabtwobregistration'}</label>
                    <input
                        type="radio"
                        name="required_vat_number"
                        id="required_vat_number_off"
                        class="form-control"
                        value="0"
                        {if isset($presta_config) && !isset($smarty.post.required_vat_number) && $presta_config->required_vat_number == 0}
                            checked="checked"
                        {elseif isset($smarty.post.required_vat_number) && $smarty.post.required_vat_number == 0}
                            checked="checked"
                        {elseif !isset($smarty.post.required_vat_number) && !isset($presta_config)}
                            checked="checked"
                        {/if}>
                    <label for="required_vat_number_off">{l s='No' mod='prestabtwobregistration'}</label>
                    <a class="slide-button btn"></a>
                </span>
            </div>
        </div>
        <div class="form-group clearfix presta-vat-validation">
            <label class="control-label col-lg-3">{l s='Enable VAT validation' mod='prestabtwobregistration'}</label>
            <div class="col-lg-8">
                <span class="switch prestashop-switch fixed-width-lg">
                    <input
                        type="radio"
                        name="presta_vat_validation"
                        id="presta_vat_validation_on"
                        class="form-control"
                        value="1"
                        {if isset($smarty.post.presta_vat_validation) && $smarty.post.presta_vat_validation == 1}
                            checked="checked"
                        {elseif isset($presta_config) && $presta_config->vat_validation == 1}
                            checked="checked"
                        {/if}>
                    <label for="presta_vat_validation_on">{l s='Yes' mod='prestabtwobregistration'}</label>
                    <input
                        type="radio"
                        name="presta_vat_validation"
                        id="presta_vat_validation_off"
                        class="form-control"
                        value="0"
                        {if isset($presta_config) && !isset($smarty.post.presta_vat_validation) && $presta_config->vat_validation == 0}
                            checked="checked"
                        {elseif isset($smarty.post.presta_vat_validation) && $smarty.post.presta_vat_validation == 0}
                            checked="checked"
                        {elseif !isset($smarty.post.presta_vat_validation) && !isset($presta_config)}
                            checked="checked"
                        {/if}>
                    <label for="presta_vat_validation_off">{l s='No' mod='prestabtwobregistration'}</label>
                    <a class="slide-button btn"></a>
                </span>
                <p class="help-block">
                    {l s='Automatic verification of VAT number via VIES api.' mod='prestabtwobregistration'}
                </p>
                <div class="alert alert-info">
                    {l s='You can only use the VAT number verification for European professionals(excluding DROM-COM) and the United Kingdom. Consider removing the United Kingdom from the Europe zone.' mod='prestabtwobregistration'}
                </div>

            </div>
        </div>
    </div>

    <div class="form-group clearfix presta-address-comp">
        <label class="control-label col-lg-3">{l s='Show Address Complement' mod='prestabtwobregistration'}</label>
        <div class="col-lg-5">
            <span class="switch prestashop-switch fixed-width-lg">
                <input
                    type="radio"
                    name="presta_address_complement"
                    id="presta_address_complement_on"
                    class="form-control"
                    value="1"
                    {if isset($smarty.post.presta_address_complement) && $smarty.post.presta_address_complement == 1}
                        checked="checked"
                    {elseif isset($presta_config) && $presta_config->address_complement == 1}
                        checked="checked"
                    {/if}>
                <label for="presta_address_complement_on">{l s='Yes' mod='prestabtwobregistration'}</label>
                <input
                    type="radio"
                    name="presta_address_complement"
                    id="presta_address_complement_off"
                    class="form-control"
                    value="0"
                    {if isset($presta_config) && !isset($smarty.post.presta_address_complement) && $presta_config->address_complement == 0}
                        checked="checked"
                    {elseif isset($smarty.post.presta_address_complement) && $smarty.post.presta_address_complement == 0}
                        checked="checked"
                    {elseif !isset($smarty.post.presta_address_complement) && !isset($presta_config)}
                        checked="checked"
                    {/if}>
                <label for="presta_address_complement_off">{l s='No' mod='prestabtwobregistration'}</label>
                <a class="slide-button btn"></a>
            </span>
        </div>
    </div>

    <div class="form-group clearfix presta-phone">
        <label class="control-label col-lg-3">{l s='Show Phone' mod='prestabtwobregistration'}</label>
        <div class="col-lg-5">
            <span class="switch prestashop-switch fixed-width-lg">
                <input
                    type="radio"
                    name="presta_phone"
                    id="presta_phone_on"
                    class="form-control"
                    value="1"
                    {if isset($smarty.post.presta_phone) && $smarty.post.presta_phone == 1}
                        checked="checked"
                    {elseif isset($presta_config) && $presta_config->phone == 1}
                        checked="checked"
                    {/if}>
                <label for="presta_phone_on">{l s='Yes' mod='prestabtwobregistration'}</label>
                <input
                    type="radio"
                    name="presta_phone"
                    id="presta_phone_off"
                    class="form-control"
                    value="0"
                    {if isset($presta_config) && !isset($smarty.post.presta_phone) && $presta_config->phone == 0}
                        checked="checked"
                    {elseif isset($smarty.post.presta_phone) && $smarty.post.presta_phone == 0}
                        checked="checked"
                    {elseif !isset($smarty.post.presta_phone) && !isset($presta_config)}
                        checked="checked"
                    {/if}>
                <label for="presta_phone_off">{l s='No' mod='prestabtwobregistration'}</label>
                <a class="slide-button btn"></a>
            </span>
        </div>
    </div>
</div>
