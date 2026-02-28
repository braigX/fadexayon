{*
* Since 2013 Ovidiu Cimpean
*
* Ovidiu Cimpean - Newsletter Pro © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at addons4prestashop@gmail.com.
*
* @author Ovidiu Cimpean <addons4prestashop@gmail.com>
* @copyright Since 2013 Ovidiu Cimpean
* @license   Do not edit, modify or copy this file
* @version   Release: 4
*}

{extends 'customer/page.tpl'}

{block name='page_title'}
	{l s='Unsubscribe'  mod='newsletterpro'}
{/block}

{block name='page_content'}
	<div id="newsletterpro-unsubscribe">
		{if isset($unsubscribe)}
		<p class="alert alert-success success">{l s='You have successfully unsubscribed from our newsletter.' mod='newsletterpro'}</p>
		{elseif isset($email_not_found)}
		<p class="alert alert-success success">{l s='You are not subscribed at our newsletter.' mod='newsletterpro'}</p>
		{elseif isset($email_not_valid)}
		<p class="alert alert-danger error">{l s='Your email is not valid.' mod='newsletterpro'}</p>
		{elseif isset($token_not_valid)}
		<p class="alert alert-danger error">{l s='Invalid unsubscription token.' mod='newsletterpro'}</p>
		{elseif isset($pqnp_errors)}
		<p class="alert alert-danger error">{$pqnp_errors}</p>
		{else}
		&nbsp;
		{/if}
	</div>
{/block}
