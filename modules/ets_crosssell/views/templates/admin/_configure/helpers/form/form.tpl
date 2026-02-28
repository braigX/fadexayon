{*
* 2007-2022 ETS-Soft
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
* needs please, contact us for extra customization service at an affordable price
*
*  @author ETS-Soft <etssoft.jsc@gmail.com>
*  @copyright  2007-2022 ETS-Soft
*  @license    Valid for 1 website (or project) for each purchase of license
*  International Registered Trademark & Property of ETS-Soft
*}
{extends file="helpers/form/form.tpl"}
{block name="input_row"}
    {if isset($input.global_field) && $input.global_field}
        </div>
        </div>
        <div id="fieldset_1" class="panel">
        <div class="panel-heading">
            <i class="icon-AdminAdmin"></i>
            {l s='Block Settings' mod='ets_crosssell'}: {$page_title|escape:'html':'UTF-8'}
        </div>
        <div class="form-wrapper">
    {/if}
     {if isset($input.first_field) && $input.first_field && isset($fields_position) && $fields_position}
        <ul id="field-positions" class="field-positions">
            {foreach from=$fields_position key='key' item='field_position'}
                {if isset($fields_value[$fields_postion_value[$key]])}
                    {assign var='value_text' value=$fields_value[$fields_postion_value[$key]]}
                {else}
                    {assign var='value_text' value=0}
                {/if}
                <li id="field_positions_{$field_position|escape:'html':'UTF-8'}">
                    <div>
                        <div class="title-field"> 
                            <span class="position_number" >
                                <span>
                                {$key+1|intval}
                                </span>
                            </span>
                            <span class="position_info">
                                {$_config_types[$field_position].title|escape:'html':'UTF-8'}
                                {if isset($_config_types[$field_position].desc)}
                                    <div class="desc">{$_config_types[$field_position].desc|escape:'html':'UTF-8'}</div>
                                {/if}
                            </span>
                        </div>
                        <div class="cross_field_config">
                            
                            <label class="ets_sc_switch{if $value_text} active{/if}">
                                <input class="ets_sc_field" type="checkbox" {if $value_text}checked ="checked"{/if} value="1" data-field="{$fields_postion_value[$key]|escape:'html':'UTF-8'}"/>
                                <span class="ets_sc_field_label on">{l s='On' mod='ets_crosssell'}</span>
                                <span class="ets_sc_field_label off">{l s='Off' mod='ets_crosssell'}</span>
                                <span class="ets_sc_field_circle"></span>
                            </label>
                            {if isset($_config_types[$field_position].setting)}
                                <div class="setting" data-setting="{$field_position|escape:'html':'UTF-8'}"><i class="icon-AdminAdmin"></i>{l s='Setting' mod='ets_crosssell'}</div>
                            {/if}
                        </div>
                    </div>
                </li>
            {/foreach}
        </ul>
     {/if}   
     {if isset($input.begin_group) && $input.begin_group}
        <div class="ets-cs-form-group-field{if isset($input.form_group_class)} {$input.form_group_class|escape:'html':'UTF-8'}{/if}">
            <div class="popup_table"><div class="popup_tablecell">
            <div class="ets-cs-form-group-field-wapper">
            <span class="close-setting-field">{l s='Close' mod='ets_crosssell'}</span>
            <div class="setting-title" >{$input.title_group|escape:'html':'UTF-8'}</div>
     {/if}
     {$smarty.block.parent}
     {if isset($input.end_group) && $input.end_group}
            {if isset($input.warning) && $input.warning}    
                <div class="alert alert-warning">
                <strong>{$input.module_name|escape:'html':'UTF-8'}</strong> {$input.warning|escape:'html':'UTF-8'}
                </div>
            {/if}
            {if isset($input.info) && $input.info}    
                <div class="alert alert-info">
                 {$input.info nofilter}
                </div>
            {/if}
            <div class="popup_footer">
                <button class="module_form_cancel_btn_filed btn btn-default pull-left" type="button">
                    <i class="process-icon-cancel"></i>
                    {l s='Cancel' mod='ets_crosssell'}
                </button>
                <button class="module_form_submit_btn_filed btn btn-default pull-right" type="button">
                    <i class="process-icon-save"></i>
                    {l s='Save' mod='ets_crosssell'}
                </button>
            </div>
            </div>
         </div>
         </div>
        </div>
     {/if}
     {if $input.name=='ETS_CS_CACHE_LIFETIME'}
        <div class="form-group">
            <label class="control-label col-lg-3">
            </label>
            <div class="col-lg-2">
                <a href="#" class="ets_cs_clear_cache btn btn-default">
                    <i class="ets_cs_clear"></i>
                    <span class="a_text">{l s='Clear cache' mod='ets_crosssell'}</span>
                </a>
            </div>
        </div>
     {/if}
{/block}
{block name="input"}
    {if $input.type == 'search'}
        <div class="ets_cs_search_product_form">
            <input class="ets_cs_search_product" name="ets_cs_search_product" {if isset($input.placeholder)}placeholder="{$input.placeholder|escape:'html':'UTF-8'}"{/if} autocomplete="off" type="text" />
            <input class="ets_cs_product_ids" name="{$input.name|escape:'html':'UTF-8'}" value="{$fields_value[$input.name]|escape:'html':'UTF-8'}" type="hidden" />
            <ul class="ets_cs_products" id="block_search_{$input.name|escape:'html':'UTF-8'}">
                {Module::getInstanceByName('ets_crosssell')->displaySearchProductList($fields_value[$input.name]) nofilter}
                <li class="ets_cs_product_loading"></li>
            </ul>
        </div>
    {else}
        {$smarty.block.parent}
    {/if}
{/block}