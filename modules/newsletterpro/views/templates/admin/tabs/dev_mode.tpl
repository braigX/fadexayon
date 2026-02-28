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

{if isset($fix_document_write) && $fix_document_write == 1}
<div id="{$tab_id|escape:'html':'UTF-8'}" style="display: none;">
{else}
<script type="text/javascript"> 
	if(window.location.hash == '#devMode') {ldelim}
		document.write('<div id="{$tab_id|escape:'html':'UTF-8'}" style="display: block;">');
	{rdelim} else {ldelim}
		document.write('<div id="{$tab_id|escape:'html':'UTF-8'}" style="display: none;">');
	{rdelim} 
</script>
{/if}
	<h4>{l s='Developer Section' mod='newsletterpro'}</h4>
	<div class="np-dev-mode">
		<div class="clearfix no-console-box">
			<i class="icon icon-refresh icon-spin np-console-loading"></i>
			<form action="POST">
				<pre id="np-dev-output-box" class="np-dev-output"><code id="np-dev-output"></code></pre>
				<textarea class="np-dev-command" id="np-dev-input" name="np_dev_command" placeholder="help" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"/></textarea>
			</form>
		</div>
	</div>
</div>