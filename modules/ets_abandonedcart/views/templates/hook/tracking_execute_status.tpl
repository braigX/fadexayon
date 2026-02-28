{*
 * Copyright ETS Software Technology Co., Ltd
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
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
*}

{if $executeStatus == 'read'}
    <span class="label label-primary">{l s='Read' mod='ets_abandonedcart'}</span>
{elseif $executeStatus == 'delivered'}
    <span class="label label-success">{l s='Delivered' mod='ets_abandonedcart'}</span>
{elseif $executeStatus == 'timeout'}
    <span class="label label-default">{l s='Timed out' mod='ets_abandonedcart'}</span>
{elseif $executeStatus == 'has_closed'}
    <span class="label label-default">{l s='Has closed' mod='ets_abandonedcart'}</span>
{/if}