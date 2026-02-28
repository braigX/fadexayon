{**
* NOTICE OF LICENSE
*
* This file is licenced under the Software License Agreement.
* With the purchase or the installation of the software in your application
* you accept the licence agreement.
*
* @author    Presta.Site
* @copyright 2015 Presta.Site
* @license   LICENSE.txt
*}

{extends file="helpers/form/form.tpl"}

{block name="legend"}
    {$smarty.block.parent}
    {if isset($field.info)}{$field.info|escape:'html':'UTF-8'}{/if}
{/block}

{block name="label"}
	{if $psv == 1.5}
		<div class="form-group {if isset($input.form_group_class)} {$input.form_group_class|escape:'html':'UTF-8'}{/if}">
		{$smarty.block.parent}
	{elseif version_compare($pswp_ps_version, '1.7.8.0', '>=')}
		{if isset($input.label)}
			<label class="control-label col-lg-3{if isset($input.required) && $input.required && $input.type != 'radio'} required{/if}">
				{if isset($input.hint)}
					<span class="label-tooltip" data-toggle="tooltip" data-html="true" title="{if is_array($input.hint)}{foreach $input.hint as $hint}{if is_array($hint)}{$hint.text|escape:'html':'UTF-8'}{else}{$hint|escape:'html':'UTF-8'}{/if}{/foreach}{else}{$input.hint|escape:'html':'UTF-8'}{/if}">
				{/if}
					{$input.label|escape:'html':'UTF-8'}
				{if isset($input.hint)}
					</span>
				{/if}
			</label>
		{/if}
	{else}
		{$smarty.block.parent}
	{/if}
	{/block}
{block name="field"}
	{if $psv == 1.5}
		{if $input.type == 'html'}
			<div class="html_content15">
				{if isset($input.html_content)}{$input.html_content nofilter}{/if}
			</div>
		{else}
			{$smarty.block.parent}
		{/if}
		</div>
	{else}
		{$smarty.block.parent}
	{/if}
{/block}

{block name="field"}
    {if $input.type == 'theme'}
		<div class="col-lg-{if isset($input.col)}{$input.col|intval}{else}9{/if}{if !isset($input.label)} col-lg-offset-3{/if} themes-wrp-{$psvd|escape:'html':'UTF-8'}">
            {foreach $input.values as $value}
				<div class="col-lg-4 col-md-4 col-xs-6 theme-item {if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if}">
                    {strip}
						<label>
							<input type="radio"	name="{$input.name|escape:'html':'UTF-8'}" id="theme-{$value.label|escape:'html':'UTF-8'}" value="{$value.value|escape:'html':'UTF-8'}" data-theme="{rtrim($value.value, '.css')|escape:'quotes':'UTF-8'}" {if $fields_value[$input.name] == $value.value} checked="checked"{/if}{if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}/>
							<img class="theme-img" src="{$value.img|escape:'html':'UTF-8'}" alt="{$value.label|escape:'html':'UTF-8'}">
						</label>
                    {/strip}
				</div>
                {if isset($value.p) && $value.p}<p class="help-block">{$value.p|escape:'html':'UTF-8'}</p>{/if}
            {/foreach}
		</div>
    {elseif $input.type == 'colors'}
		<div class="pspc-colors-wrp">
            {foreach from=$input.colors_data item=elem key='i'}
				<div class="pstg_color_wrp pspc_color_wrp color-theme-{$elem.theme|escape:'quotes':'UTF-8'}">
                    {$elem.name|escape:'html':'UTF-8'}<br>
					<input type="text"
                           {if isset($input.class)}class="{$input.class|escape:'html':'UTF-8'}"
                           {else}class="pspc-color mColorPickerInput"{/if}
						   name="{$input.name|escape:'html':'UTF-8'}_{$i|escape:'html':'UTF-8'}"
						   value="{$fields_value[$input.name][$i]|escape:'html':'UTF-8'}" />
				</div>
            {/foreach}
			<button class="btn btn-default button pspc-reset-colors" name="reset_colors" value="1">{l s='Reset' mod='prestawp'}</button>
		</div>
    {elseif $input.type == 'product_sources'}
		<div class="pspc-sources-wrp col-lg-9 ps{$psvd|intval}">
            {foreach from=$pspc_sources item='source' key='key_level_0'}
				<div class="checkbox">
					<label for="pspc_source_{$key_level_0|escape:'html':'UTF-8'}_{$input.id|escape:'html':'UTF-8'}">
						<input type="checkbox" class="pspc-root0-checkbox pspc-root-checkbox" name="pspc_source[{$key_level_0|escape:'html':'UTF-8'}]"
							   id="pspc_source_{$key_level_0|escape:'html':'UTF-8'}_{$input.id|escape:'html':'UTF-8'}"
							   data-key="{$key_level_0|escape:'html':'UTF-8'}"
                               {if $source.checked}checked{/if}
						>
                        {$source.name|escape:'html':'UTF-8'}
					</label>
					<span class="btn btn-default btn-toggle-children-sources" data-key="{$key_level_0|escape:'html':'UTF-8'}">+</span>
                    {if isset($source.children) && count($source.children)}
						<div class="pspc-sub-checkboxes pspc-source-children-{$key_level_0|escape:'html':'UTF-8'}">
                            {foreach from=$source.children item='child' key='key_level_1'}
								<div class="checkbox checkbox-level-1">
									<label for="pspc_source_{$key_level_1|escape:'html':'UTF-8'}_{$input.id|escape:'html':'UTF-8'}">
										<input type="checkbox" class="pspc-root-checkbox pspc-child-checkbox pspc-group-{$key_level_0|escape:'html':'UTF-8'}"
											   name="pspc_source[{$key_level_1|escape:'html':'UTF-8'}]"
											   id="pspc_source_{$key_level_1|escape:'html':'UTF-8'}_{$input.id|escape:'html':'UTF-8'}"
											   data-parent="pspc_source_{$key_level_0|escape:'html':'UTF-8'}_{$input.id|escape:'html':'UTF-8'}"
											   data-group="{$key_level_0|escape:'html':'UTF-8'}"
											   data-key="{$key_level_1|escape:'html':'UTF-8'}"
                                               {if $child.checked || $source.checked}checked{/if}
										>
                                        {$child.name|escape:'html':'UTF-8'}
									</label>
                                    {if isset($child.children) && count($child.children)}
										<span class="btn btn-default btn-toggle-children-sources" data-key="{$key_level_1|escape:'html':'UTF-8'}">+</span>
										<div class="pspc-sub-checkboxes pspc-source-children-{$key_level_1|escape:'html':'UTF-8'}">
                                            {foreach from=$child.children item='child_countdown'}
                                                {assign var='key_level_2' value=$child_countdown[$child.item_key]}
                                                {assign var='id_object' value=$child_countdown[$child.item_object_id]}
												<div class="checkbox">
													<label for="pspc_source_{$key_level_1|escape:'html':'UTF-8'}_{$key_level_2|escape:'html':'UTF-8'}_{$input.id|escape:'html':'UTF-8'}">
														<input type="checkbox" class="pspc-child-checkbox pspc-group-{$key_level_1|escape:'html':'UTF-8'}"
															   name="pspc_source[chosen][{$key_level_1|escape:'html':'UTF-8'}][{$key_level_2|escape:'html':'UTF-8'}]"
															   value="{$key_level_2|escape:'html':'UTF-8'}"
															   id="pspc_source_{$key_level_1|escape:'html':'UTF-8'}_{$key_level_2|escape:'html':'UTF-8'}_{$input.id|escape:'html':'UTF-8'}"
															   data-parent="pspc_source_{$key_level_1|escape:'html':'UTF-8'}_{$input.id|escape:'html':'UTF-8'}"
															   data-group="{$key_level_1|escape:'html':'UTF-8'}"
															   data-key="{$key_level_2|escape:'html':'UTF-8'}"
                                                               {if (isset($child_countdown.checked) && $child_countdown.checked || $child.checked)}checked{/if}
														>
														#{$id_object|intval} {$child_countdown[$child.item_name]|escape:'html':'UTF-8'}
													</label>
												</div>
                                            {/foreach}
										</div>
                                    {/if}
								</div>
                            {/foreach}
						</div>
                    {/if}
				</div>
            {/foreach}
		</div>
	{elseif $input.type == 'switch'}
		<div class="col-lg-8">
			<span class="switch prestashop-switch fixed-width-lg">
				{foreach $input.values as $value}
					{assign var="input_id" value=$input.name}
					{if isset($input.id) && $input.id}
						{assign var="input_id" value=$input.id}
					{/if}
					<input type="radio" name="{$input.name|escape:'html':'UTF-8'}"{if $value.value == 1} id="{$input_id|escape:'html':'UTF-8'}_on"{else} id="{$input_id|escape:'html':'UTF-8'}_off"{/if} value="{$value.value|escape:'html':'UTF-8'}"{if $fields_value[$input.name] == $value.value} checked="checked"{/if}{if (isset($input.disabled) && $input.disabled) or (isset($value.disabled) && $value.disabled)} disabled="disabled"{/if}/>
					{strip}
					<label {if $value.value == 1} for="{$input_id|escape:'html':'UTF-8'}_on"{else} for="{$input_id|escape:'html':'UTF-8'}_off"{/if}>
						{if $value.value == 1}
							{l s='Yes' d='Admin.Global'}
						{else}
							{l s='No' d='Admin.Global'}
						{/if}
					</label>
					{/strip}
				{/foreach}
				<a class="slide-button btn"></a>
			</span>
		</div>
	{elseif $input.type == 'wp_content'}
		{include file='../../../../admin/_post_select_form.tpl'}
    {elseif $input.type == 'full_width_html'}
		<div class="row">
			{if isset($input.html_content)}
				{$input.html_content nofilter}
			{else}
				{$input.name|escape:'html':'UTF-8'}
			{/if}
		</div>
	{elseif $input.type == 'shops'}
		<div class="pswp_cb_wrp">
			{foreach from=Shop::getShops() item='shop'}
				<label for="pswp_shop_{$shop.id_shop|intval}_{if $pswp_block}{$pswp_block->id|intval}{else}0{/if}" class="pswp_cb_label">
					<input type="checkbox"
						   name="{$input.name|escape:'html':'UTF-8'}[]"
						   value="{$shop.id_shop|intval}"
						   id="pswp_shop_{$shop.id_shop|intval}_{if $pswp_block}{$pswp_block->id|intval}{else}0{/if}"
						   class="pswp_shop_cb"
						   {if (is_array($fields_value[$input.name]) && in_array($shop.id_shop, $fields_value[$input.name])) || !($pswp_block && $pswp_block->id)}checked{/if}>
					{$shop.name|escape:'html':'UTF-8'}
				</label>
			{/foreach}
		</div>
    {else}
        {$smarty.block.parent}
    {/if}
{/block}
