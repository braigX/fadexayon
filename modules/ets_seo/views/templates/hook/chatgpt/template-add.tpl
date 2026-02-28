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

<div class="ets_seo_popup" id="gptTemplatePopup">
  <div class="popup_content table">
    <div class="popup_content_tablecell">
      <div class="popup_content_wrap" style="position: relative">
        <span class="close_popup" title="{$transMsg.close|escape:'html':'UTF-8'}">+</span>
        <div id="ets-seo-form-popup">
          <form></form>
          <form id="gptTemplateAddFrm" class="defaultForm form-horizontal" action="" method="post" enctype="multipart/form-data" novalidate="">
            <input type="hidden" name="saveTemplateGPT" value="1">

            <div class="panel">
              <div class="panel-heading">
                <i class="icon-AdminCatalog"></i> {(isset($template)) ? $transMsg.edit : $transMsg.addNew|escape:'html':'UTF-8'}
              </div>

              <div class="form-wrapper">

                {foreach $fields as $key => $field}
                  <div class="form-group">
                    <label class="control-label col-lg-3 required">
                      {$field.title|escape:'html':'UTF-8'}
                    </label>
                    <div class="col-lg-8">

                      <div class="form-group">
                        {if isset($field.lang) && $field.lang}
                          {foreach $languages as $lang}
                            <div class="translatable-field lang-{$lang.id_lang|escape:'html':'UTF-8'}">
                              <div class="col-lg-9">
                                  {if $field.type === 'textarea'}
                                    <textarea id="{$key|escape:'html':'UTF-8'}_{$lang.id_lang|escape:'html':'UTF-8'}" name="{$key|escape:'html':'UTF-8'}[{$lang.id_lang|escape:'html':'UTF-8'}]" class="textarea-autosize gpt-reset-when-complete" {if isset($field.defaultValue)}data-default-value="{$field.defaultValue|escape:'html':'UTF-8'}"{/if} style="overflow: hidden; overflow-wrap: break-word; resize: none; height: 100px;">{(isset($template.$key[$lang.id_lang])) ? $template.$key[$lang.id_lang]:''|escape:'html':'UTF-8'}</textarea>
                                      <p class="help-block" id="{$key|escape:'html':'UTF-8'}HelpBlock_{$lang.id_lang|escape:'html':'UTF-8'}"></p>
                                  {else}
                                    <input type="{$field.type|escape:'html':'UTF-8'}" id="{$key|escape:'html':'UTF-8'}_{$lang.id_lang|escape:'html':'UTF-8'}" name="{$key|escape:'html':'UTF-8'}[{$lang.id_lang|escape:'html':'UTF-8'}]" class="gpt-reset-when-complete" value="{(isset($template.$key[$lang.id_lang])) ? $template.$key[$lang.id_lang]:''|escape:'html':'UTF-8'}" {if isset($field.defaultValue)}data-default-value="{$field.defaultValue|escape:'html':'UTF-8'}"{/if} required="required">
                                  {/if}
                              </div>
                              <div class="col-lg-2">
                                <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                                    {$lang.iso_code|escape:'html':'UTF-8'}
                                  <i class="icon-caret-down"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    {foreach $languages as $lang}
                                      <li><a href="javascript:hideOtherLanguage({$lang.id_lang|escape:'html':'UTF-8'});" tabindex="-1">{$lang.name|escape:'html':'UTF-8'}</a></li>
                                    {/foreach}
                                </ul>
                              </div>
                            </div>
                          {/foreach}
                        {else}
                          <div class="non-translatable-field">
                            <div class="col-lg-11">
                                {if $field.type === 'textarea'}
                                  <textarea id="{$key|escape:'html':'UTF-8'}" name="{$key|escape:'html':'UTF-8'}" class="textarea-autosize gpt-reset-when-complete" style="overflow: hidden; overflow-wrap: break-word; resize: none; height: 100px;" {if isset($field.defaultValue)}data-default-value="{$field.defaultValue|escape:'html':'UTF-8'}"{/if}>{(isset($template.$key)) ? $template.$key:''|escape:'html':'UTF-8'}</textarea>
                                {elseif $field.type === 'select'}
                                  <select id="{$key|escape:'html':'UTF-8'}" name="{$key|escape:'html':'UTF-8'}" class="gpt-reset-when-complete form-control" {if isset($field.defaultValue)}data-default-value="{$field.defaultValue|escape:'html':'UTF-8'}"{/if}>
                                    {foreach $field.options as $option}
                                        <option value="{$option.value|escape:'html':'UTF-8'}" {if isset($template[$key]) && $template[$key] == $option.value}selected{elseif isset($field.defaultValue) && $option.value == $field.defaultValue}selected{/if}>{$option.name|escape:'html':'UTF-8'}</option>
                                    {/foreach}
                                  </select>
                                {else}
                                  <input type="{$field.type|escape:'html':'UTF-8'}" id="{$key|escape:'html':'UTF-8'}" name="{$key|escape:'html':'UTF-8'}" class="gpt-reset-when-complete" value="{(isset($template.$key)) ? $template.$key:''|escape:'html':'UTF-8'}" {if isset($field.defaultValue)}data-default-value="{$field.defaultValue|escape:'html':'UTF-8'}"{/if} required="required">
                                {/if}
                            </div>
                          </div>
                        {/if}
                      </div>

                    </div>
                  </div>
                {/foreach}

                <div class="form-group hide">
                  <input type="hidden" name="id_ets_seo_gpt_template" class="gpt-reset-when-complete" id="id_ets_seo_gpt_template" value="{(isset($template['id'])) ? $template['id'] : 0}" data-default-value="0">
                </div>

              </div><!-- /.form-wrapper -->

              <div class="panel-footer">
                <a class="btn btn-default cancel_popup" href="#"><i class="process-icon-cancel"></i>{$transMsg.cancel|escape:'html':'UTF-8'}</a>
                <div class="img_loading_wrapper">
                </div>
                <button type="submit" name="saveTemplateGPT" class="btn btn-default pull-right">
                  <i class="process-icon-save"></i> {$transMsg.save|escape:'html':'UTF-8'}
                </button>
              </div>

            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
  $(document).ready(() => hideOtherLanguage(etsSeoBo.currentActiveLangId));
</script>