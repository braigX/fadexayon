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
{*<a{if isset($class)} class="{$class|escape:'html':'UTF-8'}"{/if} href="{$href|escape:'html':'UTF-8'}" title="{$action|escape:'html':'UTF-8'}" class="delete">
	<i class="icon-trash"></i> {$action|escape:'html':'UTF-8'}
</a>*}
<a href="#" title="{$action|escape:'html':'UTF-8'}" {if isset($class)} class="{$class|escape:'html':'UTF-8'}"{/if} onclick="confirm_link('', '{l s='Delete selected item?' mod='ets_abandonedcart'}', '{l s='Yes' mod='ets_abandonedcart'}', '{l s='No' mod='ets_abandonedcart'}', '{$href|escape:'html':'UTF-8'}', '#')">
	<i class="icon-trash"></i> {$action|escape:'html':'UTF-8'}
</a>
