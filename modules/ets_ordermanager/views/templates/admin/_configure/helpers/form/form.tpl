{*
* 2007-2023 ETS-Soft
*
* NOTICE OF LICENSE
*
* This file is not open source! Each license that you purchased is only available for 1 website only.
* If you want to use this file on more websites (or projects), you need to purchase additional licenses. 
* You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
* 
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs, please contact us for extra customization service at an affordable price
*
*  @author ETS-Soft <etssoft.jsc@gmail.com>
*  @copyright  2007-2023 ETS-Soft
*  @license    Valid for 1 website (or project) for each purchase of license
*  International Registered Trademark & Property of ETS-Soft
*  International Registered Trademark & Property of ETS-Soft
*}
{extends file="helpers/form/form.tpl"}
{block name="field"}
    {if $input.type == 'products_search'}
        <div class="col-lg-9">
            <div id="ajax_choose_product">
                <input type="hidden" name="inputAccessories" id="inputAccessories"
                       value="{if $input.selected_products}{foreach from=$input.selected_products item=accessory}{$accessory.id_product|intval}-{/foreach}{/if}"/>
                <input type="hidden" name="nameAccessories" id="nameAccessories"
                       value="{if $input.selected_products}{foreach from=$input.selected_products item=accessory}{$accessory.name|escape:'html':'UTF-8'}¤{/foreach}{/if}"/>
                <div class="input-group">
                    <input type="text" id="product_autocomplete_input" name="product_autocomplete_input"/>
                    <span class="input-group-addon"><i class="icon-search"></i></span>
                </div>
                <div id="divAccessories">
                    {if $input.selected_products}
                        {foreach from=$input.selected_products item=accessory}
                            <div class="form-control-static">
                                <button type="button" class="btn btn-default"
                                        onclick="etsDelAccessory({$accessory.id_product|intval});"
                                        name="{$accessory.id_product|intval}">
                                    <i class="icon-remove text-danger"></i>
                                </button>
                                {$accessory.name|escape:'html':'UTF-8'}{if !empty($accessory.reference)}{$accessory.reference|escape:'html':'UTF-8'}{/if}
                            </div>
                        {/foreach}
                    {/if}
                </div>
            </div>
        </div>
    {else}
        {$smarty.block.parent}
    {/if}
{/block}
{block name="legend"}
    <div class="panel-heading{if isset($settingTabs) && $settingTabs} ode_settings_header{/if}">
        {if isset($field.image) && isset($field.title)}<img src="{$field.image|escape:'html':'UTF-8'}" alt="{$field.title|escape:'html':'UTF-8'}" />{/if}
        {if isset($field.icon)}<i class="{$field.icon|escape:'html':'UTF-8'}"></i>{/if}
        {$field.title|escape:'html':'UTF-8'}
        {if isset($addNewUrl)}
            <span class="panel-heading-action">
            <a class="list-toolbar-btn ets-custom-payment-add-new" href="{$addNewUrl|escape:'html':'UTF-8'}">
                <span data-placement="top" data-html="true"
                      data-original-title="{l s='Add new rule ' mod='ets_ordermanager'}" class="label-tooltip"
                      data-toggle="tooltip" title="">
                    <i class="process-icon-new"></i>
                </span>
            </a>
        </span>
        {/if}
        {if isset($post_key) && $post_key}<input type="hidden" name="post_key" value="{$post_key|escape:'html':'UTF-8'}" />{/if}
    </div>
    {if isset($configTabs) && $configTabs}
        <ul class="config_tableft">
            {foreach from=$configTabs item='tab' key='tabId'}
                <li class="confi_tab config_tab_{$tabId|escape:'html':'UTF-8'}" data-tab-id="{$tabId|escape:'html':'UTF-8'}">{$tab|escape:'html':'UTF-8'}</li>
            {/foreach}
        </ul>
    {/if}
    {if isset($settingTabs) && $settingTabs}
        <ul class="config_tableft">
            {foreach from=$settingTabs key='id' item='tab'}
            <li class="ode_setting_tab confi_tab setting_tab_{$id|escape:'html':'UTF-8'}" data-tab="{$id|escape:'html':'UTF-8'}">{$tab|escape:'html':'UTF-8'}</li>
            {/foreach}
            <li class="confi_tab2 setting_tab_manager_order"><a href="{$link->getAdminLink('AdminOrders')|escape:'html':'UTF-8'}" target="_blank">{l s='Manage orders' mod='ets_ordermanager'}</a></li>
            <li class="confi_tab2 setting_tab_export_order"><a href="{$link->getAdminLink('AdminOrderManagerExports')|escape:'html':'UTF-8'}" target="_blank">{l s='Export orders' mod='ets_ordermanager'}</a></li>
            {if isset($intro) && $intro}
                <li class="li_othermodules ">
                    <a class="{if isset($refsLink) && $refsLink}refs_othermodules{else}link_othermodules{/if}" href="{$other_modules_link|escape:'html':'UTF-8'}"
                       {if isset($refsLink) && $refsLink}target="_blank" {/if}>
                        <span class="tab-title">{l s='Other modules' mod='ets_ordermanager'}</span>
                        <span class="tab-sub-title">{l s='Made by ETS-Soft' mod='ets_ordermanager'}</span>
                    </a>
                </li>
            {/if}
        </ul>
    {/if}
{/block}

{block name="input_row"}
    {if $input.name=='ETS_ODE_HOST'}
        <div class="ets-form-group  ets-custom-payment-tab-manager_form">
            {$order_manager_form nofilter}
        </div>
    {/if}
    {if $input.name=='send_file_via_email' && (isset($ETS_ODE_USE_CRONJOB) && !$ETS_ODE_USE_CRONJOB)}
        <div class="ets-form-group ets-custom-payment-tab-settings">
            <div class="alert alert-warning">{l s='Cronjob is disabled. Please' mod='ets_ordermanager'} <a href="{$link->getAdminLink('AdminModules')|escape:'html':'UTF-8'}&configure=ets_ordermanager" target="_blank">{l s='enable cronjob' mod='ets_ordermanager'}</a> {l s='to make automation task work' mod='ets_ordermanager'}</div>
        </div>
    {/if}
    {if isset($isConfigForm) && $isConfigForm}
        <div class="ets-form-group form_{$input.name|lower|escape:'html':'UTF-8'}{if isset($input.tab) && $input.tab} ets-custom-payment-tab-{$input.tab|escape:'html':'UTF-8'}{/if}">
            {if $input.name == 'ETS_ODE_HOST'}
                <div class="form-group">
                    <label class="control-label col-lg-3"></label>
                    <p style="width: 100%;" class="ets_ode_group_ftp col-lg-9 alert alert-info">{l s='Configure global FTP of a remote host that can be used for all order export rules to save exported file to the remote host.' mod='ets_ordermanager'}</p>
                </div>
            {/if}
            {if !isset($define)}{assign var="define" value="0"}{/if}
            {if !$define && $input.name == 'exported_fields'}{if $define <= 0}{assign var="define" value="1"}{/if}<div class="form-group">
                <span class="ets_ode_title_available">{l s='Available fields' mod='ets_ordermanager'}</span>
            </div>{/if}
            {$smarty.block.parent}
            {if isset($input.info) && $input.info}
                <div class="ets_tc_info alert alert-warning">{$input.info|escape:'html':'UTF-8'}</div>
            {/if}
            {if $input.name == 'ETS_ODE_CRONJOB_TOKEN'}
                {hook h='displayOdeCronjobRules' secure=$fields_value[$input.name]}
            {/if}
        </div>
    {else }
        {$smarty.block.parent}
    {/if}
    {if $input.name=='ETS_ODE_SAVE_CRONJOB_TOKEN'}
        <div class="ets-form-group form_ets_ode_save_cronjob_token ets-custom-payment-tab-cronjob">
            <div class="form-group">
                <label class="control-label col-lg-3">&nbsp;</label>
                <div class="col-lg-9">
                    <p>{if $time_zone}{l s='Time zone' mod='ets_ordermanager'}: {$time_zone|escape:'html':'UTF-8'}{/if} {l s='Current time' mod='ets_ordermanager'}: {$time_now|escape:'html':'UTF-8'}</p>
                </div>
            </div>
        </div>
        <div class="ets-form-group form_ets_ode_save_cronjob_token ets-custom-payment-tab-cronjob">
            <div class="form-group">
                <label class="control-label col-lg-3">{l s='Cronjob log' mod='ets_ordermanager'}</label>
                <div class="col-lg-9">
                    <textarea readonly>{$ets_cron_log|escape:'html':'UTF-8'}</textarea>
                    <button type="button" class="clear-log-cronjob btn btn-default"><i class="icon icon-trash"></i> {l s='Clear log' mod='ets_ordermanager'}</button>
                </div>
            </div>
        </div>
    {/if}
{/block}
{block name="label"}
    
    {if $input.name|strpos : '_time'}
        <label class="control-label col-lg-3"> <p>{$input.label|escape:'html':'UTF-8'}</p><p class="ets_mp_hour_at" style="margin-top: 25px;">{l s='Hour at' mod='ets_ordermanager'}</p>  </label>
        
    {else}
        {$smarty.block.parent}
    {/if}
{/block}
{block name="input"}
    {if $input.name|strpos : '_time'}
        {assign var='key_hours' value = $input.name|cat:'_hours'}
        {assign var='key_weeks' value = $input.name|cat:'_weeks'}
        {assign var='key_months' value = $input.name|cat:'_months'}
        {include file="./schedule.tpl" name=$input.name time_hours= $fields_value[$key_hours] time_weeks=$fields_value[$key_weeks] time_months = $fields_value[$key_months] schedule=$fields_value[$input.name|replace:'time':''|cat:'schedule']}
    {/if}
    {if $input.name == 'specific_product'}
        <div class="ets_ode_specific_product">
            <div class="input-group">
                <input id="ets_ode_product_search" name="ets_ode_product_search" value="" autocomplete="off" type="text" placeholder="{l s='Search for product by name, reference or ID' mod='ets_ordermanager'}"/>
                <span class="input-group-addon">
                    <i class="icon icon-search"></i>
                </span>
            </div>
            <ul class="ets_ode_products">
                {hook h='displayOdeProductList' ids = $fields_value[$input.name]}
                <li class="ets_ode_product_loading"></li>
            </ul>
        </div>
    {/if}
    {if $input.type == 'checkbox'}
        {if isset($input.values.query) && $input.values.query}
            {assign var='input_class' value=$input.class|trim|replace:' ':'.'}
            <div class="checkbox">
                {strip}
                    <label for="{$input_class|escape:'html':'UTF-8'}">
                        <input type="checkbox" name="{$input_class|escape:'html':'UTF-8'}"
                               id="{$input_class|escape:'html':'UTF-8'}"
                               class="{if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if}" value=""/>
                        {l s='All' mod='ets_ordermanager'}
                    </label>
                {/strip}
            </div>
        {/if}
    {/if}

    {if $input.type == 'check_list'}
        {if isset($input.values.query) && $input.values.query}{foreach from=$input.values.query item='option'}
        {assign var="id_option" value = $input.name|cat:'_'|cat: $option.id_employee}
        <div class="checkbox">
            {strip}
                <label for="{$id_option|escape:'html':'UTF-8'}">
                    <input type="checkbox" id="{$id_option|escape:'html':'UTF-8'}"
                           name="{$input.name|escape:'html':'UTF-8'}[]"
                           class="{if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if}{if $input.values.supper_admin != $input.values.id_employee || $input.values.id_employee == $option.id_employee} disabled{/if}"
                           value="{$option.id_employee|intval}"
                            {if in_array($option.id_employee, $fields_value[$input.name])} checked="checked"{/if}
                    />
                    {$option.employee_name|escape:'html':'UTF-8'}&nbsp;({$option.profile_name|escape:'html':'UTF-8'})<br>
                    {l s='Email' mod='ets_ordermanager'}:&nbsp;{$option.email|escape:'html':'UTF-8'}
                </label>
            {/strip}
        </div>
    {/foreach}{/if}
    {/if}
    {if $input.name == 'ETS_ODE_CRONJOB_TOKEN'}
        <div class="input-group col-lg-3">
            {$smarty.block.parent}
            <span class="input-group-btn">
            <a href="#" class="ets_ode_gencode btn btn-default"><i class="icon-random"></i> {l s='Generate' mod='ets_ordermanager'}</a>
        </span>
        </div>
        <div class="ets_ode_help_block">
            <p style="margin-top: 15px;">
                <strong>*{l s='Setup a cronjob as below (once per minute) on your server to send exported orders to receivers automatically' mod='ets_ordermanager'}</strong><br>
                <span class="ets_ode_php_command">* * * * * {$php_path|escape:'html':'UTF-8'} {$path_local|cat:'cronjob.php'|escape:'html':'UTF-8'}
                    secure={$fields_value[$input.name]|escape:'html':'UTF-8'}</span>
            </p>
            <p style="margin-top: 5px;">
                <strong>{l s='Manually send exported orders to receivers by running the following URL on your web browser' mod='ets_ordermanager'}</strong><br>
                {assign var="url" value=$domain|trim:'/'|cat: $path_uri|cat:'cronjob.php'|cat:'?secure='|cat:$fields_value[$input.name]}
                <a class="btn btn-default btn-run-cronjob" target="_blank" href="{$url|escape:'html':'UTF-8'}">{l s='Execute cronjob manually' mod='ets_ordermanager'}</a>
            </p>
        </div>
    {else}
        {if $input.type == 'password'}
		<div class="input-group fixed-width-lg">
			<span class="input-group-addon">
				<i class="icon-key"></i>
			</span>
			<input type="password"
				id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}"
				name="{$input.name|escape:'html':'UTF-8'}"
				class="{if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if}"
				value="{$fields_value[$input.name]|escape:'html':'UTF-8'}"
				{if isset($input.autocomplete) && !$input.autocomplete}autocomplete="off"{/if}
				{if isset($input.required) && $input.required } required="required" {/if} />
		</div>
        {else}
            {$smarty.block.parent}
        {/if}
    {/if}
    {if $input.name == 'specific_customer_select'}
        <ul>
            {hook h='displayOdeCustomerList' ids = $fields_value['specific_customer']}
        </ul>
    {/if}
    {if $input.name=='ETS_ODE_DELIVERY_LABEL_HEADER'}
        <input id="copy_text_short_code" value="" type="hidden" />
    {/if}
{/block}
