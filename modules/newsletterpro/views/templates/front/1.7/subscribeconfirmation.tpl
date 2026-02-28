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
	{l s='Subscribe'  mod='newsletterpro'}
{/block}

{block name='page_content'}


	<div id="newsletterpro-subscribe">
	{*
	<!-- {include file="$tpl_dir./errors.tpl"} -->
	*}

	{if isset($success_message)}
		<div class="alert alert-success success">
		{foreach $success_message as $value}
			{$value|escape:'html':'UTF-8'} <br>
		{/foreach}
		</div>
	{/if}
	</div>



{/block}

