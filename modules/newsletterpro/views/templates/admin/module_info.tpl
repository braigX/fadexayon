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

{if count($pqnp_upgrade_warning) > 0}
<div class="clearfix pqnp-update-warning-message">
	<div class="col-sm-12">
		<div class="alert alert-warning error clearfix">
			<h3>{l s='Important changes on this update' mod='newsletterpro'}</h3>
			<div class="pqnp-update-warning-message-content">
				{foreach $pqnp_upgrade_warning as $key => $warn}
					{if is_numeric($key)}
						{if $warn === ' '}
							<br>
						{else}
							<p>{$warn|replace:'&quot;':'"'|escape:'html':'UTF-8'}</p>
						{/if}
					{else}
						<p><strong>{$key|replace:'&quot;':'"'|escape:'html':'UTF-8'}</strong> {$warn|replace:'&quot;':'"'|escape:'html':'UTF-8'}</p>
					{/if}
				{/foreach}
			</div>
			<a href="javascript:{}" class="btn btn-default" onclick="NewsletterProControllers.UpgradeController.clearUpdateWarnings($(this));">
				<i class="icon icon-check-circle"></i>
				{l s='I Understand' mod='newsletterpro'}
			</a>
		</div>
	</div>
</div>
{/if}

{if $update.needs_update == true}
{include file="$tpl_location"|cat:"templates/admin/module_update.tpl"}
{elseif $CONFIGURATION.SHOW_CLEAR_CACHE == true}
<div id="clear-cache-box" class="clearfix">
	<div class="col-sm-12">
		<div class="alert alert-danger error clearfix">
			<div class="clearfix" style="margin-bottom: 5px;">
				{l s='The module has been updated. It\'s required to clear the prestashop cache from "Advanced Parameters" > "Performance", and also the web browser cache.'  mod='newsletterpro'}
			</div>
			<a href="javascript:{}" class="btn btn-default" onclick="NewsletterProControllers.ClearCacheController.clear($(this));">
				<i class="icon icon-check-circle"></i>
				{l s='I Agree' mod='newsletterpro'}
			</a>
		</div>
	</div>
</div>
{/if}
