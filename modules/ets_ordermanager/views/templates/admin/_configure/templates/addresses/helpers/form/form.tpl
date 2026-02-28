{*
* 2007-2023 ETS-Soft
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
* versions in the future. If you wish to customize PrestaShop for your
* needs, please contact us for extra customization service at an affordable price
*
*  @author ETS-Soft <etssoft.jsc@gmail.com>
*  @copyright  2007-2023 ETS-Soft
*  @license    Valid for 1 website (or project) for each purchase of license
*  International Registered Trademark & Property of ETS-Soft
*  International Registered Trademark & Property of ETS-Soft
*}

{extends file="controllers/addresses/helpers/form/form.tpl"}
{block name="footer"}
{capture name='form_submit_btn'}{counter name='form_submit_btn'}{/capture}
	{if isset($fieldset['form']['submit']) || isset($fieldset['form']['buttons'])}
		<div class="panel-footer">
			{if isset($fieldset['form']['submit']) && !empty($fieldset['form']['submit'])}
			<button type="submit" value="1"	id="{if isset($fieldset['form']['submit']['id'])}{$fieldset['form']['submit']['id']|escape:'html':'UTF-8'}{else}{$table|escape:'html':'UTF-8'}_form_submit_btn{/if}{if $smarty.capture.form_submit_btn > 1}_{($smarty.capture.form_submit_btn - 1)|intval}{/if}" name="{if isset($fieldset['form']['submit']['name'])}{$fieldset['form']['submit']['name']|escape:'html':'UTF-8'}{else}{$submit_action|escape:'html':'UTF-8'}{/if}{if isset($fieldset['form']['submit']['stay']) && $fieldset['form']['submit']['stay']}AndStay{/if}" class="{if isset($fieldset['form']['submit']['class'])}{$fieldset['form']['submit']['class']|escape:'html':'UTF-8'}{else}btn btn-default pull-right{/if}">
				<i class="{if isset($fieldset['form']['submit']['icon'])}{$fieldset['form']['submit']['icon']|escape:'html':'UTF-8'}{else}process-icon-save{/if}"></i> {$fieldset['form']['submit']['title']|escape:'html':'UTF-8'}
			</button>
			{/if}
			{if isset($show_cancel_button) && $show_cancel_button}
			<a class="btn btn-default" {if $table}id="{$table|escape:'html':'UTF-8'}_form_cancel_btn"{/if} onclick="{if (isset($smarty.get.liteDisplaying) && $smarty.get.liteDisplaying) || isset($smarty.post.submitFormAjax)}javascript:window.parent.$.fancybox.close();{else}javascript:window.history.back();{/if}">
				<i class="process-icon-cancel"></i> {l s='Cancel' mod='ets_ordermanager'}
			</a>
			{/if}
			{if isset($fieldset['form']['reset'])}
    			<button
    				type="reset"
    				id="{if isset($fieldset['form']['reset']['id'])}{$fieldset['form']['reset']['id']|escape:'html':'UTF-8'}{else}{$table|escape:'html':'UTF-8'}_form_reset_btn{/if}"
    				class="{if isset($fieldset['form']['reset']['class'])}{$fieldset['form']['reset']['class']|escape:'html':'UTF-8'}{else}btn btn-default{/if}"
    				>
    				{if isset($fieldset['form']['reset']['icon'])}<i class="{$fieldset['form']['reset']['icon']|escape:'html':'UTF-8'}"></i> {/if} {$fieldset['form']['reset']['title']|escape:'html':'UTF-8'}
    			</button>
			{/if}
			{if isset($fieldset['form']['buttons'])}
			{foreach from=$fieldset['form']['buttons'] item=btn key=k}
				{if isset($btn.href) && trim($btn.href) != ''}
					<a href="{$btn.href|escape:'html':'UTF-8'}" {if isset($btn['id'])}id="{$btn['id']|escape:'html':'UTF-8'}"{/if} class="btn btn-default{if isset($btn['class'])} {$btn['class']|escape:'html':'UTF-8'}{/if}" {if isset($btn.js) && $btn.js} onclick="{$btn.js|escape:'html':'UTF-8'}"{/if}>{if isset($btn['icon'])}<i class="{$btn['icon']|escape:'html':'UTF-8'}" ></i> {/if}{$btn.title|escape:'html':'UTF-8'}</a>
				{else}
					<button type="{if isset($btn['type'])}{$btn['type']|escape:'html':'UTF-8'}{else}button{/if}" {if isset($btn['id'])}id="{$btn['id']|escape:'html':'UTF-8'}"{/if} class="btn btn-default{if isset($btn['class'])} {$btn['class']|escape:'html':'UTF-8'}{/if}" name="{if isset($btn['name'])}{$btn['name']|escape:'html':'UTF-8'}{else}submitOptions{$table|escape:'html':'UTF-8'}{/if}"{if isset($btn.js) && $btn.js} onclick="{$btn.js|escape:'html':'UTF-8'}"{/if}>{if isset($btn['icon'])}<i class="{$btn['icon']|escape:'html':'UTF-8'}" ></i> {/if}{$btn.title|escape:'html':'UTF-8'}</button>
				{/if}
			{/foreach}
			{/if}
		</div>
	{/if}
{/block}