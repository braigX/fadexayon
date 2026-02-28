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

{*
* Available hooks 
*
* displayFooterBefore
* displayFooter
* displayRightColumn
* displayLeftColumn
*
* newsletter_subscribe_block.tpl
*  
* To create a new template for a different hook
*  
* newsletter_subscribe_block_display_footer_before.tpl
* newsletter_subscribe_block_display_footer.tpl
* newsletter_subscribe_block_display_right_column.tpl
* newsletter_subscribe_block_display_left_column.tpl
*}

<div class="pqnp-subscribe-block {if isset($display_hook)}pqnp-{$display_hook|escape:'html':'UTF-8'}{/if}">
	<div class="row">
		<div class="col-xs-12">
			<h6 class="pqnp-subscribe-title">{l s='Newsletter' mod='newsletterpro'}</h6>
			<a href="javascript:{}" class="btn btn-primary float-xs-right pqnp-subscribe-button">{l s='OK' mod='newsletterpro'}</a>
			<div class="input-wrapper">
				<input type="email" class="inputNew form-control grey pqnp-email-address" value="" placeholder="{l s='Your email address' mod='newsletterpro'}">
			</div>
		</div>
		<div class="col-xs-12">
			<p class='pqnp-subscribe-description'>{l s='You may unsubscribe at any moment. For that purpose, please find our contact info in the legal notice.' mod='newsletterpro'}</p>
		</div>
	</div>
</div>
