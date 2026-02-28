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
<h4 class="ets_abancart_title">{l s='Trackings' mod='ets_abandonedcart'}</h4>
{if isset($TRACKINGs) && $TRACKINGs}
	<div class="ets_abancart_tracking_content">
		{if isset($TYPE) && $TYPE && in_array($TYPE, array('email', 'customer'))}
			<table>
				<tr><td>{l s='Total customers: ' mod='ets_abandonedcart'}</td><td>{$TRACKINGs.total_customer|intval}</td></tr>
				<tr><td>{l s='Total read email:' mod='ets_abandonedcart'}</td><td>{$TRACKINGs.total_read|intval}</td></tr>
				<tr><td>{l s='Total discount code: ' mod='ets_abandonedcart'}</td><td>{$TRACKINGs.total_cart_rule|intval}</td></tr>
			</table>
		{else}
			<table>
				<tr><td>{l s='Total cart: ' mod='ets_abandonedcart'}</td><td>{$TRACKINGs.total_cart|intval}</td></tr>
				<tr><td>{l s='Total views: ' mod='ets_abandonedcart'}</td><td>{$TRACKINGs.total_view|intval}</td></tr>
				<tr><td>{l s='Total discount code: ' mod='ets_abandonedcart'}</td><td>{$TRACKINGs.total_cart_rule|intval}</td></tr>
			</table>
		{/if}
	</div>
{else}<p class="alert alert-info"></p>{/if}