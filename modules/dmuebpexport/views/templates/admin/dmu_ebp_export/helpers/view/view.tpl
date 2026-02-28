{**
* NOTICE OF LICENSE
*
* This source file is subject to a commercial license from SARL DREAM ME UP
* Use, copy, modification or distribution of this source file without written
* license agreement from the SARL DREAM ME UP is strictly forbidden.
*
*   .--.
*   |   |.--..-. .--, .--.--.   .--.--. .-.   .  . .,-.
*   |   ;|  (.-'(   | |  |  |   |  |  |(.-'   |  | |   )
*   '--' '   `--'`-'`-'  '  `-  '  '  `-`--'  `--`-|`-'
*        w w w . d r e a m - m e - u p . f r       '
*
*  @author    Dream me up <prestashop@dream-me-up.fr>
*  @copyright 2007 - 2024 Dream me up
*  @license   All Rights Reserved
*}

<fieldset>
	<div class="panel" id="fieldset_0">
		<div class="panel-heading">
			<i class="icon-info"></i>&nbsp;{$txt_infos|escape:'htmlall':'UTF-8'}
		</div>						
		<div class="form-wrapper">								
			<div class="form-group">																	
				{$txt_select|escape:'htmlall':'UTF-8'}
				
				{if $selected_format == "0"}
					<strong>{$txt_export_comptable|escape:'htmlall':'UTF-8'} - {$txt_ventilation|escape:'htmlall':'UTF-8'}</strong>
				{else}
					<strong>{$txt_export_ebp|escape:'htmlall':'UTF-8'} - {$txt_ventilation|escape:'htmlall':'UTF-8'}</strong>
				{/if}	

				({$txt_switch|escape:'htmlall':'UTF-8'}<a href="{$cnf_link|escape:'quotes':'UTF-8'}">{$txt_cnfpage|escape:'htmlall':'UTF-8'}</a>)			
				<div class="clearfix"></div>																	
			</div>	
		</div>
	</div>
</fieldset>