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
{if isset($weeks) && $weeks}<select name="{$name|cat:'_weeks'|escape:'html':'UTF-8'}"
                                    data-time="weekly"
                                    class="{if $schedule == 'weekly'}active {/if}fixed-width-xl"
                                    id="{$name|cat:'_weeks'|escape:'html':'UTF-8'}" style="margin-bottom: 15px;">
    {foreach from=$weeks key='id' item='week'}
        <option value="{$id|intval}"{if $time_weeks|trim==$id|trim} selected="selected"{/if}>{$week|escape:'html':'UTF-8'}</option>{/foreach}
</select>{/if}
{if isset($months) && $months}<select name="{$name|cat:'_months'|escape:'html':'UTF-8'}"
                                      data-time="monthly"
                                      class="{if $schedule == 'monthly'}active {/if}fixed-width-xl"
                                      id="{$name|cat:'_months'|escape:'html':'UTF-8'}" style="margin-bottom: 15px;">
    {foreach from=$months item='month'}
        <option value="{$month.id_option|escape:'html':'UTF-8'}"{if $time_months|trim==$month.id_option|trim} selected="selected"{/if}>{$month.label|escape:'html':'UTF-8'}</option>{/foreach}
    </select>{/if}
{if isset($hours) && $hours}<select name="{$name|cat:'_hours'|escape:'html':'UTF-8'}"
                                    data-time="daily"
                                    class="{if $schedule == 'daily' || $schedule == 'weekly' || $schedule == 'monthly'}active {/if}fixed-width-xl"
                                    id="{$name|cat:'_hours'|escape:'html':'UTF-8'}" style="margin-bottom: 15px;">
    {foreach from=$hours key='id' item='hour'}
        <option value="{$id|intval}"{if $time_hours|trim==$id|trim} selected="selected"{/if}>{$hour|escape:'html':'UTF-8'}</option>{/foreach}
</select>{/if}
