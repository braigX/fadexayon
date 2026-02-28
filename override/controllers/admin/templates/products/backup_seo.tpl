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
<div id="product-seo" class="panel product-tab">
	<input type="hidden" name="submitted_tabs[]" value="Seo" />
	<h3>{l s='SEO' mod='ets_awesomeurl'}</h3>
	{include file="controllers/products/multishop/check_fields.tpl" product_tab="Seo"}
	<div class="form-group">
		<div class="col-lg-1"><span class="pull-right">{include file="controllers/products/multishop/checkbox.tpl" field="meta_title" type="default" multilang="true"}</span></div>
		<label class="control-label col-lg-2" for="meta_title_{$id_lang|intval}">
			<span class="label-tooltip" data-toggle="tooltip"
				title="{l s='Public title for the product\'s page, and for search engines. Leave blank to use the product name.' mod='ets_awesomeurl'} {l s='The number of remaining characters is displayed to the left of the field.' mod='ets_awesomeurl'}">
				{l s='Meta title' mod='ets_awesomeurl'}
			</span>
		</label>
		<div class="col-lg-8">
			{include file="controllers/products/input_text_lang.tpl"
				languages=$languages
				input_name='meta_title'
				input_value=$product->meta_title
				maxchar=70
			}
		</div>
	</div>
	<div class="form-group">
		<div class="col-lg-1"><span class="pull-right">{include file="controllers/products/multishop/checkbox.tpl" field="meta_description" type="default" multilang="true"}</span></div>
		<label class="control-label col-lg-2" for="meta_description_{$id_lang|intval}">
			<span class="label-tooltip" data-toggle="tooltip"
				title="{l s='This description will appear in search engines. You need a single sentence, shorter than 160 characters (including spaces).' mod='ets_awesomeurl'}">
				{l s='Meta description' mod='ets_awesomeurl'}
			</span>
		</label>
		<div class="col-lg-8">
			{include file="controllers/products/textarea_lang.tpl"
				languages=$languages
				input_name='meta_description'
				input_value=$product->meta_description
				maxchar=160
			}
		</div>
	</div>
	{* Removed for simplicity *}
	<div class="form-group hide">
		<div class="col-lg-1"><span class="pull-right">{include file="controllers/products/multishop/checkbox.tpl" field="meta_keywords" type="default" multilang="true"}</span></div>
		<label class="control-label col-lg-2" for="meta_keywords_{$id_lang|intval}">
			<span class="label-tooltip" data-toggle="tooltip"
				title="{l s='Keywords for search engines, separated by commas.' mod='ets_awesomeurl'}">
				{l s='Meta keywords' mod='ets_awesomeurl'}
			</span>
		</label>
		<div class="col-lg-8">
			{include file="controllers/products/input_text_lang.tpl" languages=$languages
				input_value=$product->meta_keywords
				input_name='meta_keywords'}
		</div>
	</div>
	<div class="form-group">
		<div class="col-lg-1"><span class="pull-right">{include file="controllers/products/multishop/checkbox.tpl" field="link_rewrite" type="seo_friendly_url" multilang="true"}</span></div>
		<label class="control-label col-lg-2" for="link_rewrite_{$id_lang|intval}">
			<span class="label-tooltip" data-toggle="tooltip"
				title="{l s='This is the human-readable URL, as generated from the product\'s name. You can change it if you want.' mod='ets_awesomeurl'}">
				{l s='Friendly URL:' mod='ets_awesomeurl'}
			</span>

		</label>
		<div class="col-lg-6">
				{include file="controllers/products/input_text_lang.tpl"
					languages=$languages
					input_value=$product->link_rewrite
					input_name='link_rewrite'}
		</div>
		<div class="col-lg-2">
			<button type="button" class="btn btn-default" id="generate-friendly-url" onmousedown="updateFriendlyURLByName();"><i class="icon-random"></i> {l s='Generate' mod='ets_awesomeurl'}</button>
		</div>
	</div>
    {hook h='displayAdminProductsSeoStepBottom' id_product = $product->id}
	<div class="row">
		<div class="col-lg-9 col-lg-offset-3">
			{foreach from=$languages item=language}
			<div class="alert alert-warning translatable-field lang-{$language.id_lang|intval}">
				<i class="icon-link"></i> {l s='The product link will look like this:' mod='ets_awesomeurl'}<br/>
				<strong>{if isset($rewritten_links[$language.id_lang][0])}{$rewritten_links[$language.id_lang][0]|escape:'html':'UTF-8'}{/if}<span id="friendly-url_{$language.id_lang|intval}">{$product->link_rewrite[$language.id_lang]|escape:'html':'UTF-8'}</span>{if isset($rewritten_links[$language.id_lang][1])}{$rewritten_links[$language.id_lang][1]|escape:'html':'UTF-8'}{/if}</strong>
			</div>
			{/foreach}
		</div>
	</div>
	<div class="panel-footer">
		<a href="{$link->getAdminLink('AdminProducts')|escape:'html':'UTF-8'}{if isset($smarty.request.page) && $smarty.request.page > 1}&amp;submitFilterproduct={$smarty.request.page|intval}{/if}" class="btn btn-default"><i class="process-icon-cancel"></i> {l s='Cancel' mod='ets_awesomeurl'}</a>
		<button type="submit" name="submitAddproduct" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='Save' mod='ets_awesomeurl'}</button>
		<button type="submit" name="submitAddproductAndStay" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='Save and stay' mod='ets_awesomeurl'}</button>
	</div>
</div>
<script type="text/javascript">
	if (tabs_manager.allow_hide_other_languages)
		hideOtherLanguage({$default_form_language|escape:'html':'UTF-8'});
    $(window).on('load', function(){
        setTimeout(function(){
            if (jQuery().select2 && $('.js-ets-seo-select2').length){
    
                $('.js-ets-seo-select2').select2();
            }
        }, 500);
    });
</script>
