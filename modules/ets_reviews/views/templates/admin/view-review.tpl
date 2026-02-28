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


<div id="product-comment-render-form" class="etsreviews-product-comment-render-form panel">
	<div class="panel-heading">
		<h4>{$title|escape:'html':'UTF-8'} #{$review->id|intval} - {l s='Product' mod='ets_reviews'}: {if !empty($review->product)}<a href="{$review->product->link nofilter}" target="_blank">{$review->product->name|escape:'html':'UTF-8'}</a>{/if}</h4>
    {if $languages|count > 1}
      {foreach from=$languages item=language}
        <div class="panel-heading-lang translatable-field lang-{$language.id_lang|intval}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
          <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
            {$language.iso_code|escape:'html':'UTF-8'}
            <span class="caret"></span>
          </button>
          <ul class="dropdown-menu">
            {foreach from=$languages item=lang}
                <li><a href="javascript:hideOtherLanguage({$lang.id_lang|intval});" tabindex="-1">{$lang.name|escape:'html':'UTF-8'}</a></li>
            {/foreach}
          </ul>
        </div>
      {/foreach}
    {/if}
    <span class="panel-heading-action">
      <div class="btn-group-action">
        <div class="btn-group pull-right" style="margin-right: 2px; top: 0;">
          {if isset($toolbar_btn) && $toolbar_btn && isset($review->customer->id) && $review->customer->id}
            <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
              <i class="icon-caret-down"></i>&nbsp;
            </button>
            <ul class="dropdown-menu">
              {foreach from=$toolbar_btn item='btn'}
                <li>
                  <a href="{$currentIndex|escape:'quotes':'UTF-8'}&{$btn.id|escape:'html':'UTF-8'}{$table|escape:'html':'UTF-8'}=&{$identifier|escape:'html':'UTF-8'}={$review->id|intval}&token={$token|escape:'html':'UTF-8'}"
                    {if $review->user->is_block && $btn.id == 'block' || !$review->user->is_block && $btn.id == 'unblock'}style="display: none;"
                    {/if} title="{$btn.title|escape:'html':'UTF-8'}"
                    class="{if isset($btn.class) && $btn.class}{$btn.class|escape:'html':'UTF-8'}{/if}"
                    {if isset($btn.confirm) && $btn.confirm}
                      onclick="{literal}if (confirm('{/literal}{$btn.confirm|escape:'html':'UTF-8'}{literal}')){return true;}else{event.stopPropagation(); event.preventDefault();};{/literal}"
                    {/if}>
                    <i class="{$btn.icon|escape:'html':'UTF-8'}"></i>&nbsp;{$btn.title|escape:'html':'UTF-8'}
                  </a>
                </li>
              {/foreach}
            </ul>
          {/if}
        </div>
      </div>
      <a class="list-toolbar-btn ets_rv_cancel ets_button_gray" href="javascript:void(0);">
        <span class="label-tooltip" data-toggle="tooltip" data-original-title="{l s='Cancel' mod='ets_reviews'}"
          data-html="true" data-placement="top">
          <i class="icon-cancel icon-times"></i>
        </span>
      </a>
    </span>
	</div>
	<div class="form-wrapper">
		<div class="form-group-review-wrap">
            {$list nofilter}
		</div>
	</div>
	<div class="panel-footer">
        {if isset($buttons) && $buttons}
            {foreach from=$buttons item='button'}
	            <a {if $button.id == 'approve' && $review->validate|intval == 1} style="display: none;"{/if} href="{$currentIndex|escape:'quotes':'UTF-8'}&{$button.id|escape:'html':'UTF-8'}{$table|escape:'html':'UTF-8'}=&{$identifier|escape:'html':'UTF-8'}={$review->id|intval}{if $button.id === 'edit'}&update{$table|escape:'html':'UTF-8'}&back_to_view=1{/if}&token={$token|escape:'html':'UTF-8'}"
	               class="btn btn-default pull-right{if isset($button.class) && $button.class} {$button.class|escape:'html':'UTF-8'}{/if}"
	               title="{$button.title|escape:'html':'UTF-8'}"{if isset($button.confirm) && $button.confirm} onclick="{literal}if (confirm({/literal}'{$button.confirm|escape:'html':'UTF-8'}'{literal})){return true;}else{event.stopPropagation(); event.preventDefault();};{/literal}"{/if}>
                    {if isset($button.icon) && $button.icon}<i
			            class="{$button.icon}"></i>{/if} {$button.name|escape:'html':'UTF-8'}
	            </a>
            {/foreach}
        {/if}
		<a href="{$currentIndex|escape:'quotes':'UTF-8'}&token={$token|escape:'html':'UTF-8'}" class="btn btn-default ets_rv_cancel ets_button_gray"><i class="process-icon-cancel"></i> {l s='Cancel' mod='ets_reviews'}</a>
	</div>
</div>
{if preg_match('/Reviews|Comments|Replies/i', $currentTab)}
	{(Module::getInstanceByName('ets_reviews')->renderTemplateModal(['no_qa'=>1, 'tab'=>'Reviews', 'id_product'=>$review->product->id, 'refreshController'=>$refreshController])) nofilter}
{/if}
{if preg_match('/Questions|QuestionComments|Answers|AnswerComments/i', $currentTab)}
	{(Module::getInstanceByName('ets_reviews')->renderTemplateModal(['no_qa'=>0, 'tab'=>'Questions', 'id_product'=>$review->product->id, 'refreshController'=>$refreshController])) nofilter}
{/if}