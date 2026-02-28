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
<div class="order_manager_form">
    <div class="alert alert-info">{l s='Assign order management permissions to specific staff profiles.' mod='ets_ordermanager'}</div>
    <div class="form-wrapper">
        <table border='1' class="table_config_permistion_order">
            {if $profiles}
                <tr>
                    <th>{l s='Profile' mod='ets_ordermanager'}</th>
                    <th>{l s='View' mod='ets_ordermanager'}</th>
                    <th>{l s='Add' mod='ets_ordermanager'}</th>
                    <th>{l s='Edit' mod='ets_ordermanager'}</th>
                    <th>{l s='Delete' mod='ets_ordermanager'}</th>
                    <th>{l s='All' mod='ets_ordermanager'}</th>
                </tr>
                {foreach from = $profiles item='profile'}
                    <tr>
                        <td>{$profile.name|escape:'html':'UTF-8'}</td>
                        {foreach from =$permistions item='permistion'}
                            {if $profile.id_profile==1}
                                <td><input type="checkbox" value="1" checked="checked" disabled="disabled"/></td>
                            {else}
                                <td><input class="change_permistion_ordermanager" name="" data-perm="{$permistion|escape:'html':'UTF-8'}" data-id_profile = "{$profile.id_profile|intval}" type="checkbox"{if $profile[$permistion]} checked="checked"{/if}/></td>
                            {/if}
                        {/foreach}
                        {if $profile.id_profile==1}
                            <td><input type="checkbox" value="1" checked="checked" disabled="disabled"/></td>
                        {else}
                            <td><input class="change_permistion_ordermanager all" data-perm="all" data-id_profile = "{$profile.id_profile|intval}" type="checkbox"{if $profile['all']} checked="checked"{/if} /></td>
                        {/if}
                    </tr>
                {/foreach}
            {/if}
        </table>
    </div>
</div>