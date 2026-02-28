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
<form id="@FORM_ID@{rand()|intval}" class="form-control-{$form_class|escape:'html':'UTF-8'}{if isset($comment_class) && $comment_class|trim !== ''} {$comment_class|escape:'html':'UTF-8'}{/if}{if $back_office && $employee} bo defaultForm form-horizontal{/if}" action="{if $qa}{$qa_comment_url nofilter}{else}{$comment_url nofilter}{/if}&__ac=post_{$form nofilter}{if $qa}&qa=1{/if}" method="post" enctype="multipart/form-data" novalidate data-lang-default="{$defaultFormLanguage|intval}">
	<div class="form-wrapper">
        {if $form_fields|count > 0}
            {foreach from=$form_fields key='id' item='input'}
                {block name='input_row'}
		            <div class="form-group{if isset($input.form_group_class)} {$input.form_group_class|escape:'html':'UTF-8'}{/if}{if $input.type == 'hidden' || $input.type == 'datetime'} hide{/if}">
	                    {if $input.type == 'textarea'}
                            {if $back_office && $employee && $multilang_enabled && $languages|count > 0}
                                {foreach $languages as $language}
                                    {if $languages|count > 1}
					                    <div class="translatable-field lang-{$language.id_lang|intval}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
					                    <div class="col-lg-{if isset($input.col)}{$input.col|intval}{else}9{/if}">
                                    {/if}
				                    <textarea class="ets_rv_{$form|escape:'html':'UTF-8'} form-control" name="comment_content_{$language.id_lang|intval}" placeholder="{$message|escape:'html':'UTF-8'}"></textarea>
                                    {if $languages|count > 1}
					                    </div>
					                    <div class="col-lg-2">
						                    <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">{$language.iso_code|escape:'quotes':'UTF-8'}<i class="icon-caret-down"></i></button>
						                    <ul class="dropdown-menu">
                                                {foreach from=$languages item=language}
								                    <li><a href="javascript:hideOtherLanguage({$language.id_lang|intval});" tabindex="-1">{$language.name|escape:'html':'UTF-8'}</a></li>
                                                {/foreach}
						                    </ul>
					                    </div>
					                    </div>
                                    {/if}
                                {/foreach}
                            {else}
			                    <textarea class="ets_rv_{$form|escape:'html':'UTF-8'} form-control" name="comment_content" placeholder="{$message|escape:'html':'UTF-8'}"></textarea>
                            {/if}
						{elseif $input.type == 'datetime'}
							<label for="{$id|escape:'html':'UTF-8'}" class="control-label col-lg-3{if isset($input.required) && $input.required && $input.type != 'radio'} required{/if}">{$input.name|escape:'html':'UTF-8'}</label>
							<div class="input-group col-lg-4">
								<span class="input-group-addon"><i class="icon-calendar-empty"></i></span>
							</div>
						{else}
							<label class="control-label col-lg-3{if isset($input.required) && $input.required && $input.type != 'radio'} required{/if}">{$input.name|escape:'html':'UTF-8'}</label>
							<div class="col-lg-{if isset($input.col)}{$input.col|intval}{else}9{/if}">
								{if $input.type == 'select' && $input.values|count > 0}
									{assign var=multiple value=isset($input.multiple) && $input.multiple}
									<select name="{$id|escape:'html':'UTF-8'}{if $multiple}[]{/if}" class="fixed-width-xl" id="{$id|escape:'html':'UTF-8'}{if $multiple}[]{/if}"{if $multiple} multiple="multiple"{/if}>
										{if $multiple}<option value="all"{if $input.default == 'all'}selected="selected"{/if}>{l s='All' mod='ets_reviews'}</option>{/if}
										{foreach from=$input.values.options key='id' item='option'}
											<option value="{$option[$input.values.id]|intval}" {if $option[$input.values.id]|intval == $input.default|intval}selected="selected"{/if}>{$option[$input.values.name]|escape:'html':'UTF-8'}</option>
										{/foreach}
									</select>
								{/if}
							</div>
	                    {/if}
					</div>
	            {/block}
            {/foreach}
        {/if}
		{(Module::getInstanceByName('ets_reviews')->hookRenderReCaptcha(['reCaptchaFor'=>$reCaptchaFor, 'class'=>'form-group'])) nofilter}
	</div>
	<div class="form-footer">
		<button class="ets_rv_post_{$form|escape:'html':'UTF-8'}{if !empty($ETS_RV_DESIGN_COLOR1)} background1{/if}{if !empty($ETS_RV_DESIGN_COLOR3)} bg_hover3 bd_hover3{/if} btn btn-default" name="ets_rv_post_{$form|escape:'html':'UTF-8'}" data-name="{$button_name|escape:'html':'UTF-8'}" data-upd="{$data_name|escape:'html':'UTF-8'}"{if $press_enter_enabled} style="display: none!important;"{/if}>{$button_name|escape:'html':'UTF-8'}</button>
		<button class="ets_rv_cancel_{$form|escape:'html':'UTF-8'} ets_button_gray{if isset($button_cancel_class)} {$button_cancel_class|escape:'html':'UTF-8'}{/if}" name="ets_rv_cancel_{$form|escape:'html':'UTF-8'}" style="display: none">{l s='Cancel' mod='ets_reviews'}</button>
	</div>
</form>