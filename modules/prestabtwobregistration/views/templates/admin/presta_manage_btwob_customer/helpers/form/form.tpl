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
<div class="col-lg-12">
    <div class="panel">
        <form method="post" id="customFieldForm" class="defaultForm form-horizontal tab-content">
            <div class="panel-heading">
                <i class="icon-cogs"></i>{l s='Manage B2B Customer' mod='prestabtwobregistration
                '}
            </div>
            <div class="form-wrapper">
            <div class="form-group clearfix ">
                <label class="control-label col-lg-3">{l s='Social Title:' mod='prestabtwobregistration
                '}</label>
                <div class="col-lg-5">
                    <input
                        type="text"
                        class="form-control"
                        name=" presta_social_title"
                        autocomplete="off"
                        value="{if isset($smarty.post.presta_social_title)}{$smarty.post.presta_social_title|escape:'html':'UTF-8'}
                        {elseif isset($presta_manage_customer) && $presta_manage_customer->social_title}{$presta_manage_customer->social_title|escape:'html':'UTF-8'}{/if}">
                </div>
            </div>

        <div class="form-group clearfix ">
            <label class="control-label col-lg-3">{l s='FirstName:' mod='prestabtwobregistration
            '}</label>
            <div class="col-lg-5">
                <input
                    type="text"
                    class="form-control"
                    name="presta_firstname"
                    autocomplete="off"
                    value="{if isset($smarty.post.presta_firstname)}{$smarty.post.presta_firstname|escape:'html':'UTF-8'}
                    {elseif isset($presta_manage_customer) && $presta_manage_customer->firstname}{$presta_manage_customer->firstname|escape:'html':'UTF-8'}{/if}">
            </div>
        </div>

        <div class="form-group clearfix ">
            <label class="control-label col-lg-3">{l s='LastName:' mod='prestabtwobregistration
            '}</label>
            <div class="col-lg-5">
                <input
                    type="text"
                    class="form-control"
                    name="presta_lastname"
                    autocomplete="off"
                    value="{if isset($smarty.post.presta_lastname)}{$smarty.post.presta_lastname|escape:'html':'UTF-8'}
                    {elseif isset($presta_manage_customer) && $presta_manage_customer->lastname}{$presta_manage_customer->lastname|escape:'html':'UTF-8'}
                    {/if}">
             </div>
        </div>

        <div class="form-group clearfix ">
            <label class="control-label col-lg-3">{l s='Email-Adress:' mod='prestabtwobregistration
            '}</label>
            <div class="col-lg-5">
                <input
                    type="text"
                    class="form-control"
                    name="presta_email_address"
                    autocomplete="off"
                    value="{if isset($smarty.post.presta_email_address)}{$smarty.post.presta_email_address|escape:'html':'UTF-8'}
                    {elseif isset($presta_manage_customer) && $presta_manage_customer->email_address}{$presta_manage_customer->email_address|escape:'html':'UTF-8'}
                    {/if}">
            </div>
         </div>

         <div class="form-group clearfix presta-select-store-div">
         <label class="control-label col-lg-3">{l s='Selected Groups:' mod='prestabtwobregistration'}</label>
         <div class="col-lg-5">
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
                                         name="presta_customer_groups[]"
                                         value="{$group.id_group|escape:'html':'UTF-8'}"
                                         {if in_array($group.id_group,$presta_customer_group)}checked{/if}>
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

        <div class="form-group clearfix">
            <label class="control-label col-lg-3">{l s='Enabled Company :' mod='prestabtwobregistration'}</label>
            <div class="col-lg-5">
                <span class="switch prestashop-switch fixed-width-lg">
                    <input
                        type="radio"
                        name="presta_enabled"
                        id="presta_enabled_on"
                        class="form-control"
                        value="1"
                        {if isset($smarty.post.presta_enabled) && $smarty.post.presta_enabled == 1}
                            checked="checked"
                        {elseif isset($presta_manage_customer) && $presta_manage_customer->active == 1}
                            checked="checked"
                        {elseif !isset($smarty.post.presta_enabled)}
                            checked="checked"
                        {/if}>
                    <label for="presta_enabled_on">{l s='Yes' mod='prestabtwobregistration'}</label>
                    <input
                        type="radio"
                        name="presta_enabled"
                        id="presta_enabled"
                        class="form-control"
                        value="0"
                        {if isset($presta_manage_customer) && $presta_manage_customer->active == 0}
                            checked="checked"
                        {elseif isset($smarty.post.presta_enabled) && $smarty.post.presta_enabled == 0}
                            checked="checked"
                        {/if}>
                    <label for="presta_enabled_off">{l s='No' mod='prestabtwobregistration'}</label>
                    <a class="slide-button btn"></a>
                </span>
            </div>
        </div>

        <div class="form-group clearfix presta-company">
            <label class="control-label col-lg-3 required">{l s='Company:' mod='prestabtwobregistration
            '}</label>
            <div class="col-lg-5">
                <input
                    type="text"
                    class="form-control"
                    name="presta_company"
                    autocomplete="off"
                    value="{if isset($smarty.post.presta_company)}{$smarty.post.presta_company|escape:'html':'UTF-8'}
                    {elseif isset($presta_manage_customer) && $presta_manage_customer->company}{$presta_manage_customer->company|escape:'html':'UTF-8'}
                    {/if}">
            </div>
        </div>

        <div class="form-group clearfix ">
            <label class="control-label col-lg-3">{l s='Identification Number' mod='prestabtwobregistration
            '}</label>
            <div class="col-lg-5">
                <input
                    type="number"
                    class="form-control"
                    name="presta_indentification_number"
                    autocomplete="off"
                    value="{if isset($smarty.post.presta_indentification_number)}
                    {$smarty.post.presta_indentification_number|escape:'html':'UTF-8'}
                    {elseif isset($presta_manage_customer) && $presta_manage_customer->identification_number}{$presta_manage_customer->identification_number|escape:'html':'UTF-8'}{/if}">
            </div>
        </div>

        <div class="form-group clearfix ">
            <label class="control-label col-lg-3">{l s='Address:' mod='prestabtwobregistration
            '}</label>
            <div class="col-lg-5">
                <input
                    type="text"
                    class="form-control"
                    name="presta_address"
                    autocomplete="off"
                    value="{if isset($smarty.post.presta_address)}{$smarty.post.presta_address|escape:'html':'UTF-8'}
                    {elseif isset($presta_manage_customer) && $presta_manage_customer->address}{$presta_manage_customer->address|escape:'html':'UTF-8'}{/if}">
            </div>
        </div>

        <div class="form-group clearfix ">
            <label class="control-label col-lg-3">{l s='City:' mod='prestabtwobregistration
            '}</label>
            <div class="col-lg-5">
                <input
                    type="text"
                    class="form-control"
                    name="presta_city"
                    autocomplete="off"
                    value="{if isset($smarty.post.presta_city)}{$smarty.post.presta_city|escape:'html':'UTF-8'}
                    {elseif isset($presta_manage_customer) && $presta_manage_customer->city}{$presta_manage_customer->city|escape:'html':'UTF-8'}{/if}">
            </div>
        </div>

            </div>
            <div class="panel-footer">
                <button
                    type="submit"
                    value="1"
                    name="prestabtwobregistrationSubmit"
                    class="btn btn-default pull-right">
                    <i class="process-icon-save"></i> {l s='Save' mod='prestabtwobregistration'}
                </button>
            </div>
        </form>
    </div>
</div>


