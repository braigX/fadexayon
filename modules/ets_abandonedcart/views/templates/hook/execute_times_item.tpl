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

<a href="{if isset($link_to_tracking) && $link_to_tracking}{$link_to_tracking nofilter}{else}#{/if}" class="ets_ab_execute_times_link" title="{l s='View total execute times' mod='ets_abandonedcart'}">
    {if isset($execute_times_item)}{$execute_times_item|escape:'html':'UTF-8'}{/if}
</a>