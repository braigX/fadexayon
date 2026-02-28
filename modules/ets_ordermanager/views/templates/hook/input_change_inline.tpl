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
*}
{if isset($params.edit) && $params.edit}
    <div class="wapper-change-order">
        {if isset($params.type) && $params.type=='price'}
            {assign var='sign_price' value=Ets_ordermanager::getSignPrice($tr.id_currency)}
        {/if}
        {if isset($params.type_edit) && $params.type_edit=='text'}
            {if $tr[$params.table_id]}
                {if isset($params.type) && $params.type=='price'}
                    <div class="input-group">
                {/if}
                        <input data-validate="{if isset($params.validate)}{$params.validate|escape:'html':'UTF-8'}{/if}" data-required="{if isset($params.required) && $params.required}1{else}0{/if}" type="text" value="{if isset($params.type) && $params.type=='price'}{str_replace($sign_price,'',$tr[$params.change])|escape:'html':'UTF-8'}{else}{$tr[$params.change]|escape:'html':'UTF-8'}{/if}" class="input_change_order_inline" data-order="{$tr.id|escape:'html':'UTF-8'}" data-id="{$tr[$params.table_id]|escape:'html':'UTF-8'}" data-key="{$params.table_key|escape:'html':'UTF-8'}" data-table="{$params.table_edit|escape:'html':'UTF-8'}" data-change="{$params.change|escape:'html':'UTF-8'}" placeholder="{$params.placeholder|escape:'html':'UTF-8'}" />
                {if isset($params.type) && $params.type=='price'}
                        <div class="input-group-addon">{$sign_price|escape:'html':'UTF-8'}</div>
                    </div>
                {/if}
                {if isset($params.change2)}
                    <input data-validate="{if isset($params.validate)}{$params.validate|escape:'html':'UTF-8'}{/if}" data-required="{if isset($params.required) && $params.required}1{else}0{/if}" type="text" value="{$tr[$params.change2]|escape:'html':'UTF-8'}" class="input_change_order_inline" data-order="{$tr.id|escape:'html':'UTF-8'}" data-id="{$tr[$params.table_id]|escape:'html':'UTF-8'}" data-key="{$params.table_key|escape:'html':'UTF-8'}" data-table="{$params.table_edit|escape:'html':'UTF-8'}" data-change="{$params.change2|escape:'html':'UTF-8'}" placeholder="{$params.placeholder2|escape:'html':'UTF-8'}" />
                {/if}
            {/if}
        {/if}
        {if isset($params.type_edit) && $params.type_edit=='textarea'}
            <textarea data-validate="{if isset($params.validate)}{$params.validate|escape:'html':'UTF-8'}{/if}" data-required="{if isset($params.required) && $params.required}1{else}0{/if}" class="input_change_order_inline" data-order="{$tr.id|escape:'html':'UTF-8'}" data-id="{$tr[$params.table_id]|escape:'html':'UTF-8'}" data-key="{$params.table_key|escape:'html':'UTF-8'}" data-table="{$params.table_edit|escape:'html':'UTF-8'}" data-change="{$params.change|escape:'html':'UTF-8'}">{$tr[$params.change]|escape:'html':'UTF-8'}</textarea>
        {/if}
        {if isset($params.type_edit) && $params.type_edit=='select'}
            <select data-validate="{if isset($params.validate)}{$params.validate|escape:'html':'UTF-8'}{/if}" data-required="{if isset($params.required) && $params.required}1{else}0{/if}" class="input_change_order_inline" data-order="{$tr.id|escape:'html':'UTF-8'}" data-id="{$tr[$params.table_id]|escape:'html':'UTF-8'}" data-key="{$params.table_key|escape:'html':'UTF-8'}" data-table="{$params.table_edit|escape:'html':'UTF-8'}" data-change="{$params.change|escape:'html':'UTF-8'}">
                {if $params.change=='id_carrier'}
                    {assign var='carrier_list' value= Ode_dbbase::getListCarriersByIDOrder($tr.id)}
                    {if $carrier_list}
                        {foreach from=$carrier_list item='carrier'}
                            <option value="{$carrier.id_carrier|intval}"{if $carrier.id_carrier== $tr[$params.change]} selected="selected"{/if} >{$carrier.name|escape:'html':'UTF-8'} {if isset($carrier.delay)}({$carrier.delay|escape:'html':'UTF-8'}){/if}</option>
                        {/foreach}
                    {/if}
                {else}
                    {if $params.list_edits}
                        {foreach from=$params.list_edits key='id_status' item='status'}
                            <option value="{$id_status|intval}"{if $id_status== $tr[$params.change]} selected="selected"{/if} >{$status|escape:'html':'UTF-8'}</option>
                        {/foreach}
                    {/if}
                {/if}
            </select>
        {/if}
        {if isset($params.type_edit) && $params.type_edit=='date'}
    		<input data-validate="{if isset($params.validate)}{$params.validate|escape:'html':'UTF-8'}{/if}" data-required="{if isset($params.required) && $params.required}1{else}0{/if}" id="order_date_add_{$tr.id|intval}" value="{$tr[$params.change]|escape:'html':'UTF-8'}" class="datepicker2 input_change_order_inline" data-order="{$tr.id|escape:'html':'UTF-8'}" data-id="{$tr[$params.table_id]|escape:'html':'UTF-8'}" data-key="{$params.table_key|escape:'html':'UTF-8'}" data-table="{$params.table_edit|escape:'html':'UTF-8'}" data-change="{$params.change|escape:'html':'UTF-8'}" placeholder="{$params.placeholder|escape:'html':'UTF-8'}" type="text" />
        {/if}
    </div>
{/if}