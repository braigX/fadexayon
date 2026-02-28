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
<div class="form-group">
    <div class="ets_ode_rules">
        {if $rules}
            <ul>{foreach from=$rules item='rule'}
                    <li class="ets_ode_item_rule">
                        <h4 class="ets_ode_title">{$rule.name|escape:'html':'UTF-8'}</h4>
                        <p>
                            <strong>*{l s='Set cronjob on server' mod='ets_ordermanager'}:&nbsp;</strong><br>
                            <span class="ets_ode_php_command">0 * * * * {$php_path|escape:'html':'UTF-8'} {$path_local|cat:'cronjob.php'|escape:'html':'UTF-8'}
                                secure={$secure|escape:'html':'UTF-8'}&nbsp;rule={$rule.id|intval}</span>
                        </p>
                        <p>
                            <strong>*{l s='Manually running URL' mod='ets_ordermanager'}:&nbsp;</strong><br>
                            {assign var="url" value=$domain|trim:'/'|cat: $path_uri|cat:'cronjob.php'|cat:'?secure='|cat:$secure|cat:'&rule='|cat:$rule.id}
                            <a target="_blank" href="{$url|escape:'html':'UTF-8'}">{$url|escape:'html':'UTF-8'}</a>
                        </p>
                    </li>
                {/foreach}</ul>
        {else}<p class="alert alert-warning">{l s='No cronjob.' mod='ets_ordermanager'}</p>{/if}
    </div>
</div>