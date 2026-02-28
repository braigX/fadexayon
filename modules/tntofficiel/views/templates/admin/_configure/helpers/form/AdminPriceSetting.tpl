{**
* TNT OFFICIAL MODULE FOR PRESTASHOP.
*
* @author    Inetum <inetum.com>
* @copyright 2016-2024 Inetum, 2016-2024 TNT
* @license   https://opensource.org/licenses/MIT MIT License
*}

{* extends /<ADMIN>/themes/default/template/helpers/form/form.tpl *}
{extends file="helpers/form/form.tpl"}


{block name="defaultForm" }
    {if isset($identifier_bk) && $identifier_bk == $identifier}{capture name='identifier_count'}{counter name='identifier_count'}{/capture}{/if}
    {assign var='identifier_bk' value=$identifier scope='parent'}
    {if isset($table_bk) && $table_bk == $table}{capture name='table_count'}{counter name='table_count'}{/capture}{/if}
    {assign var='table_bk' value=$table scope='parent'}
    <form id="{if isset($fields.form.form.id_form)}{$fields.form.form.id_form|escape:'html':'UTF-8'}{else}{if $table == null}configuration_form{else}{$table|escape:'html':'UTF-8'}_form{/if}{if isset($smarty.capture.table_count) && $smarty.capture.table_count}_{$smarty.capture.table_count|intval}{/if}{/if}"
          class="defaultForm form-horizontal{if isset($name_controller) && $name_controller} {$name_controller|escape:'html':'UTF-8'}{/if}"
            {if isset($current) && $current} action="{$current|escape:'html':'UTF-8'}{if isset($token) && $token}&amp;token={$token|escape:'html':'UTF-8'}{/if}"{/if}
          method="post" enctype="multipart/form-data"{if isset($style)} style="{$style|escape:'html':'UTF-8'}"{/if} novalidate>
        {if $form_id}
            <input type="hidden" name="{$identifier|escape:'html':'UTF-8'}"
                   id="{$identifier|escape:'html':'UTF-8'}{if isset($smarty.capture.identifier_count) && $smarty.capture.identifier_count}_{$smarty.capture.identifier_count|intval}{/if}"
                   value="{$form_id|escape:'html':'UTF-8'}" />
        {/if}
        {if !empty($submit_action)}
            <input type="hidden" name="{$submit_action|escape:'html':'UTF-8'}" value="1" />
        {/if}
        {foreach $fields as $f => $fieldset}
            {block name="fieldset"}
                {capture name='fieldset_name'}{counter name='fieldset_name'}{/capture}
                <div class="panel" id="fieldset_{$f|escape:'html':'UTF-8'}{if isset($smarty.capture.identifier_count) && $smarty.capture.identifier_count}_{$smarty.capture.identifier_count|intval}{/if}{if $smarty.capture.fieldset_name > 1}_{($smarty.capture.fieldset_name - 1)|intval}{/if}">
                    {foreach $fieldset.form as $key => $field}
                        {if $key == 'legend'}
                            {block name="legend"}
                                <div class="panel-heading">
                                    {if isset($field.image) && isset($field.title)}<img src="{$field.image|escape:'html':'UTF-8'}" alt="{$field.title|escape:'html':'UTF-8'}" />{/if}
                                    {if isset($field.icon)}<i class="{$field.icon|escape:'html':'UTF-8'}"></i>{/if}
                                    {$field.title|escape:'htmlall':'UTF-8'}
                                </div>
                            {/block}
                        {elseif $key == 'description' && $field}
                            {* VALIDATOR: This variable is HTML content. Do not escape. *}
                            <div class="alert alert-info">{$field nofilter}</div>
                        {elseif $key == 'warning' && $field}
                            {* VALIDATOR: This variable is HTML content. Do not escape. *}
                            <div class="alert alert-warning">{$field nofilter}</div>
                        {elseif $key == 'success' && $field}
                            {* VALIDATOR: This variable is HTML content. Do not escape. *}
                            <div class="alert alert-success">{$field nofilter}</div>
                        {elseif $key == 'error' && $field}
                            {* VALIDATOR: This variable is HTML content. Do not escape. *}
                            <div class="alert alert-danger">{$field nofilter}</div>
                        {elseif $key == 'input'}
                            <div class="clearfix">
                                {foreach $field as $input}
                                    {block name="input_row"}
                                        {*<div class="form-group{if isset($input.form_group_class)} {$input.form_group_class|escape:'html':'UTF-8'}{/if}{if $input.type == 'hidden'} hide{/if}"{if $input.name == 'id_state'} id="contains_states"{if !$contains_states} style="display:none;"{/if}{/if}{if isset($tabs) && isset($input.tab)} data-tab-id="{$input.tab|escape:'html':'UTF-8'}"{/if}>*}
                                        {if $input.type == 'hidden'}
                                            <input type="hidden" name="{$input.name|escape:'html':'UTF-8'}" id="{$input.name|escape:'html':'UTF-8'}" value="{$fields_value[$input.name]|escape:'html':'UTF-8'}" />
                                        {else}
                                            {block name="label"}

                                            {/block}

                                        {block name="field"}
                                            {*<div class="col-lg-{if isset($input.col)}{$input.col|intval}{else}9{/if}{if !isset($input.label)} col-lg-offset-3{/if}">*}
                                        {block name="input"}
                                        {if $input.type == 'text' || $input.type == 'tags'}
                                        {if isset($input.lang) AND $input.lang}
                                        {if $languages|count > 1}
                                            <div class="form-group">
                                                {/if}
                                                {foreach $languages as $language}
                                                    {assign var='value_text' value=$fields_value[$input.name][$language.id_lang]}
                                                    {if $languages|count > 1}
                                                        <div class="translatable-field lang-{$language.id_lang|escape:'html':'UTF-8'}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
                                                        <div class="col-lg-9">
                                                    {/if}
                                                    {if $input.type == 'tags'}
                                                    {literal}
                                                        <script type="text/javascript">
                                                            window.TNTOfficiel_Ready = window.TNTOfficiel_Ready || [];
                                                            window.TNTOfficiel_Ready.push(function (jQuery) {
                                                                var input_id = '{/literal}{if isset($input.id)}{$input.id}_{$language.id_lang}{else}{$input.name}_{$language.id_lang}{/if}{literal}';
                                                                jQuery('#'+input_id).tagify({delimiters: [13,44], addTagPrompt: '{/literal}{l s='Add tag' js=1}{literal}'});
                                                                jQuery({/literal}'#{$table}{literal}_form').on('submit', function() {
                                                                    jQuery(this).find('#'+input_id).val(jQuery('#'+input_id).tagify('serialize'));
                                                                });
                                                            });
                                                        </script>
                                                    {/literal}
                                                    {/if}
                                                {if isset($input.maxchar) || isset($input.prefix) || isset($input.suffix)}
                                                    <div class="input-group{if isset($input.class)} {$input.class|escape:'html':'UTF-8'}{/if}">
                                                {/if}
                                                    {if isset($input.maxchar) && $input.maxchar}
                                                        <span id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{/if}_counter"
                                                              class="input-group-addon">
                                                        <span class="text-count-down">{$input.maxchar|intval}</span>
                                                    </span>
                                                    {/if}
                                                    {if isset($input.prefix)}
                                                        <span class="input-group-addon">
                                                          {$input.prefix|escape:'html':'UTF-8'}
                                                        </span>
                                                    {/if}
                                                    <input type="text"
                                                           id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{/if}"
                                                           name="{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}"
                                                           class="{if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if}{if $input.type == 'tags'} tagify{/if}"
                                                           value="{if isset($input.string_format) && $input.string_format}{$value_text|string_format:$input.string_format|escape:'html':'UTF-8'}{else}{$value_text|escape:'html':'UTF-8'}{/if}"
                                                           onkeyup="if (isArrowKey(event)) return ;updateFriendlyURL();"
                                                            {if isset($input.size)} size="{$input.size|escape:'html':'UTF-8'}"{/if}
                                                            {if isset($input.maxchar) && $input.maxchar} data-maxchar="{$input.maxchar|intval}"{/if}
                                                            {if isset($input.maxlength) && $input.maxlength} maxlength="{$input.maxlength|intval}"{/if}
                                                            {if isset($input.readonly) && $input.readonly} readonly="readonly"{/if}
                                                            {if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}
                                                            {if isset($input.autocomplete) && !$input.autocomplete} autocomplete="off"{/if}
                                                            {if isset($input.required) && $input.required} required="required" {/if}
                                                            {if isset($input.placeholder) && $input.placeholder} placeholder="{$input.placeholder|escape:'html':'UTF-8'}"{/if} />
                                                    {if isset($input.suffix)}
                                                        <span class="input-group-addon">
                                                          {$input.suffix|escape:'html':'UTF-8'}
                                                        </span>
                                                    {/if}
                                                {if isset($input.maxchar) || isset($input.prefix) || isset($input.suffix)}
                                                    </div>
                                                {/if}
                                                    {if $languages|count > 1}
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                                                                {$language.iso_code|escape:'htmlall':'UTF-8'}
                                                                <i class="icon-caret-down"></i>
                                                            </button>
                                                            <ul class="dropdown-menu">
                                                                {foreach from=$languages item=language}
                                                                    <li><a href="javascript:hideOtherLanguage({$language.id_lang});" tabindex="-1">{$language.name|escape:'html':'UTF-8'}</a></li>
                                                                {/foreach}
                                                            </ul>
                                                        </div>
                                                        </div>
                                                    {/if}
                                                {/foreach}
                                                {if isset($input.maxchar) && $input.maxchar}
                                                    <script type="text/javascript">
                                                        window.TNTOfficiel_Ready = window.TNTOfficiel_Ready || [];
                                                        window.TNTOfficiel_Ready.push(function (jQuery) {
                                                            {foreach from=$languages item=language}
                                                            countDown(jQuery("#{if isset($input.id)}{$input.id}_{$language.id_lang}{else}{$input.name}_{$language.id_lang}{/if}"), jQuery("#{if isset($input.id)}{$input.id}_{$language.id_lang}{else}{$input.name}_{$language.id_lang}{/if}_counter"));
                                                            {/foreach}
                                                        });
                                                    </script>
                                                {/if}
                                                {if $languages|count > 1}
                                            </div>
                                        {/if}
                                        {else}
                                        {if $input.type == 'tags'}
                                        {literal}
                                            <script type="text/javascript">
                                                window.TNTOfficiel_Ready = window.TNTOfficiel_Ready || [];
                                                window.TNTOfficiel_Ready.push(function (jQuery) {
                                                    var input_id = '{/literal}{if isset($input.id)}{$input.id}{else}{$input.name}{/if}{literal}';
                                                    jQuery('#'+input_id).tagify({delimiters: [13,44], addTagPrompt: '{/literal}{l s='Add tag'}{literal}'});
                                                    jQuery({/literal}'#{$table}{literal}_form').on('submit', function() {
                                                        jQuery(this).find('#'+input_id).val(jQuery('#'+input_id).tagify('serialize'));
                                                    });
                                                });
                                            </script>
                                        {/literal}
                                        {/if}
                                            {assign var='value_text' value=$fields_value[$input.name]}
                                        {if isset($input.maxchar) || isset($input.prefix) || isset($input.suffix)}
                                            <div class="input-group{if isset($input.class)} {$input.class|escape:'html':'UTF-8'}{/if}">
                                                {/if}
                                                {if isset($input.maxchar) && $input.maxchar}
                                                    <span id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}_counter" class="input-group-addon"><span class="text-count-down">{$input.maxchar|intval}</span></span>
                                                {/if}
                                                {if isset($input.prefix)}
                                                    <span class="input-group-addon">
                                                                      {$input.prefix|escape:'html':'UTF-8'}
                                                                    </span>
                                                {/if}
                                                {*prénom*}
                                                <div class="form-group {if isset($input.size)} col-md-{$input.size|escape:'html':'UTF-8'} {else} col-md-6{/if} {if $input.name|in_array:$tntofficiel.errorFields} has-error{/if} ">
                                                    {if isset($input.label)}
                                                        <label class="control-label{if isset($input.required) && $input.required && $input.type != 'radio'} required{/if}">
                                                            {if isset($input.hint)}
                                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="{if is_array($input.hint)}
                                                                                        {foreach $input.hint as $hint}
                                                                                            {if is_array($hint)}
                                                                                                {$hint.text|escape:'html':'UTF-8'}
                                                                                            {else}
                                                                                                {$hint|escape:'html':'UTF-8'}
                                                                                            {/if}
                                                                                        {/foreach}
                                                                                    {else}
                                                                                        {$input.hint|escape:'html':'UTF-8'}
                                                                                    {/if}">
                                                                            {/if}
                                                                {$input.label|escape:'html':'UTF-8'}
                                                                {if isset($input.hint)}
                                                                            </span>
                                                            {/if}
                                                        </label>
                                                    {/if}
                                                    <div class="input-group col-md-10 col-sm-12">
                                                        <input type="text"
                                                               name="{$input.name|escape:'html':'UTF-8'}"
                                                               id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}"
                                                               value="{if isset($input.string_format) && $input.string_format}{$value_text|string_format:$input.string_format|escape:'html':'UTF-8'}{else}{$value_text|escape:'html':'UTF-8'}{/if}"
                                                               class="form-control {if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if}{if $input.type == 'tags'} tagify{/if}"
                                                                {if isset($input.size)} size="{$input.size|escape:'html':'UTF-8'}"{/if}
                                                                {if isset($input.maxchar) && $input.maxchar} data-maxchar="{$input.maxchar|intval}"{/if}
                                                                {if isset($input.maxlength) && $input.maxlength} maxlength="{$input.maxlength|intval}"{/if}
                                                                {if isset($input.readonly) && $input.readonly} readonly="readonly"{/if}
                                                                {if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}
                                                                {if isset($input.autocomplete) && !$input.autocomplete} autocomplete="off"{/if}
                                                                {if isset($input.required) && $input.required } required="required" {/if}
                                                                {if isset($input.placeholder) && $input.placeholder } placeholder="{$input.placeholder|escape:'html':'UTF-8'}"{/if}
                                                        />
                                                        {if isset($input.desc) && !empty($input.desc)}
                                                            <p class="help-block">
                                                                {if is_array($input.desc)}
                                                                    {foreach $input.desc as $p}
                                                                        {if is_array($p)}
                                                                            <span id="{$p.id|escape:'html':'UTF-8'}">{$p.text|escape:'htmlall':'UTF-8'}</span><br />
                                                                        {else}
                                                                            {$p|escape:'htmlall':'UTF-8'}<br />
                                                                        {/if}
                                                                    {/foreach}
                                                                {else}
                                                                    {* VALIDATOR: This variable is HTML content. Do not escape. *}
                                                                    {$input.desc nofilter}
                                                                {/if}
                                                            </p>
                                                        {/if}
                                                    </div>
                                                </div>
                                                {if $input.name == 'TNTOFFICIEL_SOCIETE' || $input.name == 'TNTOFFICIEL_ACCOUNT_NUMBER' }
                                                    <div class="form-group"></div>
                                                {/if}
                                                {if $input.name == 'TNTOFFICIEL_TELEPHONE' }
                                                    <div class="col-md-12">
                                                        <hr>
                                                    </div>
                                                {/if}

                                                {if isset($input.suffix)}
                                                    <span class="input-group-addon">
                                              {$input.suffix|escape:'html':'UTF-8'}
                                            </span>
                                                {/if}

                                                {if isset($input.maxchar) || isset($input.prefix) || isset($input.suffix)}
                                            </div>
                                        {/if}
                                        {if isset($input.maxchar) && $input.maxchar}
                                            <script type="text/javascript">
                                                window.TNTOfficiel_Ready = window.TNTOfficiel_Ready || [];
                                                window.TNTOfficiel_Ready.push(function (jQuery) {
                                                    countDown(jQuery("#{if isset($input.id)}{$input.id}{else}{$input.name}{/if}"), jQuery("#{if isset($input.id)}{$input.id}{else}{$input.name}{/if}_counter"));
                                                });
                                            </script>
                                        {/if}
                                        {/if}
                                        {elseif $input.type == 'textbutton'}
                                            {assign var='value_text' value=$fields_value[$input.name]}
                                            <div class="row">
                                                <div class="col-lg-9">
                                                    {if isset($input.maxchar)}
                                                    <div class="input-group">
                                                                    <span id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}_counter" class="input-group-addon">
                                                                        <span class="text-count-down">{$input.maxchar|intval}</span>
                                                                    </span>
                                                        {/if}
                                                        <input type="text"
                                                               name="{$input.name|escape:'html':'UTF-8'}"
                                                               id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}"
                                                               value="{if isset($input.string_format) && $input.string_format}{$value_text|string_format:$input.string_format|escape:'html':'UTF-8'}{else}{$value_text|escape:'html':'UTF-8'}{/if}"
                                                               class="{if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if}{if $input.type == 'tags'} tagify{/if}"
                                                                {if isset($input.size)} size="{$input.size|escape:'html':'UTF-8'}"{/if}
                                                                {if isset($input.maxchar) && $input.maxchar} data-maxchar="{$input.maxchar|intval}"{/if}
                                                                {if isset($input.maxlength) && $input.maxlength} maxlength="{$input.maxlength|intval}"{/if}
                                                                {if isset($input.readonly) && $input.readonly} readonly="readonly"{/if}
                                                                {if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}
                                                                {if isset($input.autocomplete) && !$input.autocomplete} autocomplete="off"{/if}
                                                                {if isset($input.placeholder) && $input.placeholder } placeholder="{$input.placeholder|escape:'html':'UTF-8'}"{/if}
                                                        />
                                                        {if isset($input.suffix)}{$input.suffix|escape:'html':'UTF-8'}{/if}
                                                        {if isset($input.maxchar) && $input.maxchar}
                                                    </div>
                                                    {/if}
                                                </div>
                                                <div class="col-lg-2">
                                                    <button type="button" class="btn btn-default{if isset($input.button.attributes['class'])} {$input.button.attributes['class']}{/if}{if isset($input.button.class)} {$input.button.class|escape:'html':'UTF-8'}{/if}"
                                                    {foreach from=$input.button.attributes key=name item=value}
                                                        {if $name|lower != 'class'}
                                                            {$name|escape:'html':'UTF-8'}="{$value|escape:'html':'UTF-8'}"
                                                        {/if}
                                                    {/foreach} >
                                                    {$input.button.label|escape:'htmlall':'UTF-8'}
                                                    </button>
                                                </div>
                                            </div>
                                        {if isset($input.maxchar) && $input.maxchar}
                                            <script type="text/javascript">
                                                window.TNTOfficiel_Ready = window.TNTOfficiel_Ready || [];
                                                window.TNTOfficiel_Ready.push(function (jQuery) {
                                                    countDown(jQuery("#{if isset($input.id)}{$input.id}{else}{$input.name}{/if}"), jQuery("#{if isset($input.id)}{$input.id}{else}{$input.name}{/if}_counter"));
                                                });
                                            </script>
                                        {/if}
                                        {elseif $input.type == 'swap'}
                                            <div class="form-group">
                                                <div class="col-lg-9">
                                                    <div class="form-control-static row">
                                                        <div class="col-xs-6">
                                                            <select {if isset($input.size)}size="{$input.size|escape:'html':'UTF-8'}"{/if}{if isset($input.onchange)} onchange="{$input.onchange|escape:'html':'UTF-8'}"{/if} class="{if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if}" id="availableSwap" name="{$input.name|escape:'html':'UTF-8'}_available[]" multiple="multiple">
                                                                {foreach $input.options.query AS $option}
                                                                    {if is_object($option)}
                                                                        {if !in_array($option->$input.options.id, $fields_value[$input.name])}
                                                                            <option value="{$option->$input.options.id}">{$option->$input.options.name}</option>
                                                                        {/if}
                                                                    {elseif $option == "-"}
                                                                        <option value="">-</option>
                                                                    {else}
                                                                        {if !in_array($option[$input.options.id], $fields_value[$input.name])}
                                                                            <option value="{$option[$input.options.id]|escape:'html':'UTF-8'}">{$option[$input.options.name]|escape:'html':'UTF-8'}</option>
                                                                        {/if}
                                                                    {/if}
                                                                {/foreach}
                                                            </select>
                                                            <a href="#" id="addSwap" class="btn btn-default btn-block">{l s='Add' d='Admin.Actions'} <i class="icon-arrow-right"></i></a>
                                                        </div>
                                                        <div class="col-xs-6">
                                                            <select {if isset($input.size)}size="{$input.size|escape:'html':'UTF-8'}"{/if}{if isset($input.onchange)} onchange="{$input.onchange|escape:'html':'UTF-8'}"{/if} class="{if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if}" id="selectedSwap" name="{$input.name|escape:'html':'UTF-8'}_selected[]" multiple="multiple">
                                                                {foreach $input.options.query AS $option}
                                                                    {if is_object($option)}
                                                                        {if in_array($option->$input.options.id, $fields_value[$input.name])}
                                                                            <option value="{$option->$input.options.id}">{$option->$input.options.name}</option>
                                                                        {/if}
                                                                    {elseif $option == "-"}
                                                                        <option value="">-</option>
                                                                    {else}
                                                                        {if in_array($option[$input.options.id], $fields_value[$input.name])}
                                                                            <option value="{$option[$input.options.id]|escape:'html':'UTF-8'}">{$option[$input.options.name]|escape:'html':'UTF-8'}</option>
                                                                        {/if}
                                                                    {/if}
                                                                {/foreach}
                                                            </select>
                                                            <a href="#" id="removeSwap" class="btn btn-default btn-block"><i class="icon-arrow-left"></i> {l s='Remove' mod='tntofficiel'}</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        {elseif $input.type == 'select'}
                                            {*isSelect*}
                                        {if $input.name == 'TNTOFFICIEL_HEURE_RAMASSAGE_DRIVER' ||$input.name == 'TNTOFFICIEL_HEURE_RAMASSAGE_CLOSING'}
                                            <div class="form-group {if isset($input.class)} {$input.class|escape:'html':'UTF-8'} {else} col-md-6{/if}">
                                                {if isset($input.label)}
                                                    <label class="control-label{if isset($input.required) && $input.required && $input.type != 'radio'} required{/if}">
                                                        {if isset($input.hint)}
                                                        <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="{if is_array($input.hint)}
                                                                            {foreach $input.hint as $hint}
                                                                                {if is_array($hint)}
                                                                                    {$hint.text|escape:'html':'UTF-8'}
                                                                                {else}
                                                                                    {$hint|escape:'html':'UTF-8'}
                                                                                {/if}
                                                                            {/foreach}
                                                                            {else}
                                                                                {$input.hint|escape:'html':'UTF-8'}
                                                                            {/if}">
                                                                                {/if}
                                                            {$input.label|escape:'htmlall':'UTF-8'}
                                                            {if isset($input.hint)}
                                                                        </span>
                                                        {/if}
                                                    </label>
                                                {/if}
                                                <div class="input-group col-md-10">
                                                    {if isset($input.options.query) && !$input.options.query && isset($input.empty_message)}
                                                        {$input.empty_message|escape:'htmlall':'UTF-8'}
                                                        {$input.required = false}
                                                        {$input.desc = null}
                                                    {else}
                                                        <select name="{$input.name|escape:'html':'UTF-8'}"
                                                                id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}"
                                                                {if isset($input.multiple) && $input.multiple} multiple="multiple"{/if}
                                                                {if isset($input.size)} size="{$input.size|escape:'html':'UTF-8'}"{/if}
                                                                {if isset($input.onchange)} onchange="{$input.onchange|escape:'html':'UTF-8'}"{/if}
                                                                {if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}>
                                                            {if isset($input.options.default)}
                                                                <option value="{$input.options.default.value|escape:'html':'UTF-8'}">{$input.options.default.label|escape:'html':'UTF-8'}</option>
                                                            {/if}
                                                            {if isset($input.options.optiongroup)}
                                                                {foreach $input.options.optiongroup.query AS $optiongroup}
                                                                    <optgroup label="{$optiongroup[$input.options.optiongroup.label]|escape:'html':'UTF-8'}">
                                                                        {foreach $optiongroup[$input.options.options.query] as $option}
                                                                            <option value="{$option[$input.options.options.id]|escape:'html':'UTF-8'}"
                                                                                    {if isset($input.multiple)}
                                                                                        {foreach $fields_value[$input.name] as $field_value}
                                                                                            {if $field_value == $option[$input.options.options.id]}selected="selected"{/if}
                                                                                        {/foreach}
                                                                                    {else}
                                                                                        {if $fields_value[$input.name] == $option[$input.options.options.id]}selected="selected"{/if}
                                                                                    {/if}
                                                                            >{$option[$input.options.options.name]|escape:'html':'UTF-8'}</option>
                                                                        {/foreach}
                                                                    </optgroup>
                                                                {/foreach}
                                                            {else}
                                                                {foreach $input.options.query AS $option}
                                                                    {if is_object($option)}
                                                                        <option value="{$option->$input.options.id}"
                                                                                {if isset($input.multiple)}
                                                                                    {foreach $fields_value[$input.name] as $field_value}
                                                                                        {if $field_value == $option->$input.options.id}
                                                                                            selected="selected"
                                                                                        {/if}
                                                                                    {/foreach}
                                                                                {else}
                                                                                    {if $fields_value[$input.name] == $option->$input.options.id}
                                                                                        selected="selected"
                                                                                    {/if}
                                                                                {/if}
                                                                        >{$option->$input.options.name}</option>
                                                                    {elseif $option == "-"}
                                                                        <option value="">-</option>
                                                                    {else}
                                                                        <option value="{$option[$input.options.id]|escape:'html':'UTF-8'}"
                                                                                {if isset($input.multiple)}
                                                                                    {foreach $fields_value[$input.name] as $field_value}
                                                                                        {if $field_value == $option[$input.options.id]}
                                                                                            selected="selected"
                                                                                        {/if}
                                                                                    {/foreach}
                                                                                {else}
                                                                                    {if $fields_value[$input.name] == $option[$input.options.id]}
                                                                                        selected="selected"
                                                                                    {/if}
                                                                                {/if}
                                                                        >{$option[$input.options.name]|escape:'html':'UTF-8'}</option>

                                                                    {/if}
                                                                {/foreach}
                                                            {/if}
                                                        </select>
                                                    {/if}
                                                    {elseif $input.name == 'TNTOFFICIEL_MINUTE_RAMASSAGE_DRIVER' || $input.name == 'TNTOFFICIEL_MINUTE_RAMASSAGE_CLOSING'}
                                                    {if isset($input.options.query) && !$input.options.query && isset($input.empty_message)}
                                                        {$input.empty_message|escape:'htmlall':'UTF-8'}
                                                        {$input.required = false}
                                                        {$input.desc = null}
                                                    {else}
                                                        <select name="{$input.name|escape:'html':'UTF-8'}"
                                                                id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}"
                                                                {if isset($input.multiple) && $input.multiple} multiple="multiple"{/if}
                                                                {if isset($input.size)} size="{$input.size|escape:'html':'UTF-8'}"{/if}
                                                                {if isset($input.onchange)} onchange="{$input.onchange|escape:'html':'UTF-8'}"{/if}
                                                                {if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}>
                                                            {if isset($input.options.default)}
                                                                <option value="{$input.options.default.value|escape:'html':'UTF-8'}">{$input.options.default.label|escape:'html':'UTF-8'}</option>
                                                            {/if}
                                                            {if isset($input.options.optiongroup)}
                                                                {foreach $input.options.optiongroup.query AS $optiongroup}
                                                                    <optgroup label="{$optiongroup[$input.options.optiongroup.label]|escape:'html':'UTF-8'}">
                                                                        {foreach $optiongroup[$input.options.options.query] as $option}
                                                                            <option value="{$option[$input.options.options.id]|escape:'html':'UTF-8'}"
                                                                                    {if isset($input.multiple)}
                                                                                        {foreach $fields_value[$input.name] as $field_value}
                                                                                            {if $field_value == $option[$input.options.options.id]}selected="selected"{/if}
                                                                                        {/foreach}
                                                                                    {else}
                                                                                        {if $fields_value[$input.name] == $option[$input.options.options.id]}selected="selected"{/if}
                                                                                    {/if}
                                                                            >{$option[$input.options.options.name]|escape:'html':'UTF-8'}</option>
                                                                        {/foreach}
                                                                    </optgroup>
                                                                {/foreach}
                                                            {else}
                                                                {foreach $input.options.query AS $option}
                                                                    {if is_object($option)}
                                                                        <option value="{$option->$input.options.id}"
                                                                                {if isset($input.multiple)}
                                                                                    {foreach $fields_value[$input.name] as $field_value}
                                                                                        {if $field_value == $option->$input.options.id}
                                                                                            selected="selected"
                                                                                        {/if}
                                                                                    {/foreach}
                                                                                {else}
                                                                                    {if $fields_value[$input.name] == $option->$input.options.id}
                                                                                        selected="selected"
                                                                                    {/if}
                                                                                {/if}
                                                                        >{$option->$input.options.name}</option>
                                                                    {elseif $option == "-"}
                                                                        <option value="">-</option>
                                                                    {else}
                                                                        <option value="{$option[$input.options.id]|escape:'html':'UTF-8'}"
                                                                                {if isset($input.multiple)}
                                                                                    {foreach $fields_value[$input.name] as $field_value}
                                                                                        {if $field_value == $option[$input.options.id]}
                                                                                            selected="selected"
                                                                                        {/if}
                                                                                    {/foreach}
                                                                                {else}
                                                                                    {if $fields_value[$input.name] == $option[$input.options.id]}
                                                                                        selected="selected"
                                                                                    {/if}
                                                                                {/if}
                                                                        >{$option[$input.options.name]|escape:'html':'UTF-8'}</option>

                                                                    {/if}
                                                                {/foreach}
                                                            {/if}
                                                        </select>
                                                    {/if}
                                                </div>
                                            </div>
                                        {else}
                                            <div class="form-group {if isset($input.class)} {$input.class|escape:'html':'UTF-8'} {else} col-md-6{/if}">

                                                {if isset($input.label)}
                                                    {*2nd tab*}
                                                    <label class="control-label{if isset($input.class_label)} {{$input.class_label|escape:'html':'UTF-8'}} {/if} {if isset($input.required) && $input.required && $input.type != 'radio'} required{/if}">
                                                        {*--2nd tab*}
                                                        {if isset($input.hint)}
                                                        <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="{if is_array($input.hint)}
                                                            {foreach $input.hint as $hint}
                                                                    {if is_array($hint)}
                                                                        {$hint.text|escape:'html':'UTF-8'}
                                                                    {else}
                                                                        {$hint|escape:'html':'UTF-8'}
                                                                    {/if}
                                                            {/foreach}
                                                            {else}
                                                                {$input.hint|escape:'html':'UTF-8'}
                                                            {/if}">
                                                        {/if}
                                                            {$input.label|escape:'htmlall':'UTF-8'}
                                                        {if isset($input.hint)}
                                                        </span>
                                                        {/if}
                                                    </label>
                                                {/if}
                                                {*2nd tab*}
                                                <div class="input-group {if isset($input.class_type)} {{$input.class_type|escape:'html':'UTF-8'}} {else} col-md-10{/if}">
                                                    {*---- 2nd tab*}
                                                    {if isset($input.options.query) && !$input.options.query && isset($input.empty_message)}
                                                        {$input.empty_message|escape:'htmlall':'UTF-8'}
                                                        {$input.required = false}
                                                        {$input.desc = null}
                                                    {else}
                                                        <select name="{$input.name|escape:'html':'UTF-8'}"
                                                                class="{if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if}"
                                                                id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}"
                                                                {if isset($input.multiple) && $input.multiple} multiple="multiple"{/if}
                                                                {if isset($input.size)} size="{$input.size|escape:'html':'UTF-8'}"{/if}
                                                                {if isset($input.onchange)} onchange="{$input.onchange|escape:'html':'UTF-8'}"{/if}
                                                                {if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}>
                                                            {if isset($input.options.default)}
                                                                <option value="{$input.options.default.value|escape:'html':'UTF-8'}">{$input.options.default.label|escape:'html':'UTF-8'}</option>
                                                            {/if}
                                                            {if isset($input.options.optiongroup)}
                                                                {foreach $input.options.optiongroup.query AS $optiongroup}
                                                                    <optgroup label="{$optiongroup[$input.options.optiongroup.label]|escape:'html':'UTF-8'}">
                                                                        {foreach $optiongroup[$input.options.options.query] as $option}
                                                                            <option value="{$option[$input.options.options.id]|escape:'html':'UTF-8'}"
                                                                                    {if isset($input.multiple)}
                                                                                        {foreach $fields_value[$input.name] as $field_value}
                                                                                            {if $field_value == $option[$input.options.options.id]}selected="selected"{/if}
                                                                                        {/foreach}
                                                                                    {else}
                                                                                        {if $fields_value[$input.name] == $option[$input.options.options.id]}selected="selected"{/if}
                                                                                    {/if}
                                                                            >{$option[$input.options.options.name]|escape:'html':'UTF-8'}</option>
                                                                        {/foreach}
                                                                    </optgroup>
                                                                {/foreach}
                                                            {else}
                                                                {foreach $input.options.query AS $option}
                                                                    {if is_object($option)}
                                                                        <option value="{$option->$input.options.id}"
                                                                                {if isset($input.multiple)}
                                                                                    {foreach $fields_value[$input.name] as $field_value}
                                                                                        {if $field_value == $option->$input.options.id}
                                                                                            selected="selected"
                                                                                        {/if}
                                                                                    {/foreach}
                                                                                {else}
                                                                                    {if $fields_value[$input.name] == $option->$input.options.id}
                                                                                        selected="selected"
                                                                                    {/if}
                                                                                {/if}
                                                                        >{$option->$input.options.name}</option>
                                                                    {elseif $option == "-"}
                                                                        <option value="">-</option>
                                                                    {else}
                                                                        <option value="{$option[$input.options.id]|escape:'html':'UTF-8'}"
                                                                                {if isset($input.multiple)}
                                                                                    {foreach $fields_value[$input.name] as $field_value}
                                                                                        {if $field_value == $option[$input.options.id]}
                                                                                            selected="selected"
                                                                                        {/if}
                                                                                    {/foreach}
                                                                                {else}
                                                                                    {if $fields_value[$input.name] == $option[$input.options.id]}
                                                                                        selected="selected"
                                                                                    {/if}
                                                                                {/if}
                                                                        >{$option[$input.options.name]|escape:'html':'UTF-8'}</option>

                                                                    {/if}
                                                                {/foreach}
                                                            {/if}
                                                        </select>
                                                    {/if}
                                                </div>
                                            </div>
                                        {if isset($input.desc) && !empty($input.desc) && ($input.name == 'TNTOFFICIEL_ZONE_2[]')}
                                            <div class="form-group col-md-12" >
                                                <p class="help-block">
                                                    {if is_array($input.desc)}
                                                        {foreach $input.desc as $p}
                                                            {if is_array($p)}
                                                                <span id="{$p.id|escape:'html':'UTF-8'}">{$p.text|escape:'htmlall':'UTF-8'}</span><br />
                                                            {else}
                                                                {$p|escape:'htmlall':'UTF-8'}<br />
                                                            {/if}
                                                        {/foreach}
                                                    {else}
                                                        {* VALIDATOR: This variable is HTML content. Do not escape. *}
                                                        {$input.desc nofilter}
                                                    {/if}
                                                </p>
                                            </div>
                                        {/if}
                                        {/if}
                                        {elseif $input.type == 'radio'}
                                        {foreach $input.values as $value}
                                            <div class="radio {if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if}">
                                                {strip}
                                                    <label>
                                                        <input type="radio" name="{$input.name|escape:'html':'UTF-8'}" id="{$value.id|escape:'html':'UTF-8'}"
                                                               value="{$value.value|escape:'html':'UTF-8'}"
                                                                {if $fields_value[$input.name] == $value.value} checked="checked"{/if}
                                                                {if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}
                                                        />
                                                        {$value.label|escape:'htmlall':'UTF-8'}
                                                    </label>
                                                {/strip}
                                            </div>
                                        {if isset($value.p) && $value.p}<p class="help-block">{$value.p|escape:'htmlall':'UTF-8'}</p>{/if}
                                        {/foreach}
                                            {*display TNTOFFICIEL_ZONES_CLONING_ENABLED below the list zone and not here*}
                                        {elseif $input.type == 'switch' and $input.name !== 'TNTOFFICIEL_ZONES_CLONING_ENABLED'}
                                            <div class="form-group {if isset($input.size)} col-md-{$input.size|escape:'html':'UTF-8'} {else} col-md-6{/if}">

                                                {if isset($input.label)}
                                                    <label class="control-label{if isset($input.required) && $input.required && $input.type != 'radio'} required{/if}">
                                                        {if isset($input.hint)}
                                                        <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="{if is_array($input.hint)}
                                                                        {foreach $input.hint as $hint}
                                                                            {if is_array($hint)}
                                                                                {$hint.text|escape:'html':'UTF-8'}
                                                                            {else}
                                                                                {$hint|escape:'html':'UTF-8'}
                                                                            {/if}
                                                                        {/foreach}
                                                                {else}
                                                                    {$input.hint|escape:'html':'UTF-8'}
                                                                {/if}">
                                                                    {/if}
                                                            {$input.label|escape:'htmlall':'UTF-8'}
                                                            {if isset($input.hint)}
                                                                                            </span>
                                                        {/if}
                                                    </label>
                                                {/if}
                                                <div class="input-group col-md-10">
                                                    {*mdp*}
                                                    <span class="switch prestashop-switch fixed-width-lg">
                                                                    {foreach $input.values as $value}
                                                                        <input type="radio" name="{$input.name|escape:'html':'UTF-8'}"
                                                                                {if $value.value == 1} id="{$input.name|escape:'html':'UTF-8'}_on"{else} id="{$input.name|escape:'html':'UTF-8'}_off"{/if}
                                                                               value="{$value.value|escape:'html':'UTF-8'}"
                                                                                {if $fields_value[$input.name] == $value.value} checked="checked"{/if}
                                                                                {if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}
                                                                        />
                                                                                                {strip}
                                                                        <label {if $value.value == 1} for="{$input.name|escape:'html':'UTF-8'}_on"{else} for="{$input.name|escape:'html':'UTF-8'}_off"{/if}>
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
                                            </div>
                                        {elseif $input.type == 'textarea'}
                                        {if isset($input.maxchar) && $input.maxchar}<div class="input-group">{/if}
                                            {assign var=use_textarea_autosize value=true}
                                            {if isset($input.lang) AND $input.lang}
                                            {foreach $languages as $language}
                                            {if $languages|count > 1}
                                                <div class="form-group translatable-field lang-{$language.id_lang|escape:'html':'UTF-8'}"{if $language.id_lang != $defaultFormLanguage} style="display:none;"{/if}>
                                                <div class="col-lg-9">
                                            {/if}
                                                {if isset($input.maxchar) && $input.maxchar}
                                                    <span id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{/if}_counter"
                                                          class="input-group-addon">
                                                                <span class="text-count-down">{$input.maxchar|intval}</span>
                                                            </span>
                                                {/if}
                                                <textarea
                                                        {if isset($input.readonly) && $input.readonly} readonly="readonly"{/if}
                                                        name="{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}"
                                                        id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}_{$language.id_lang|escape:'html':'UTF-8'}"
                                                        class="{if isset($input.autoload_rte) && $input.autoload_rte}rte autoload_rte{else}textarea-autosize{/if}{if isset($input.class)} {$input.class|escape:'html':'UTF-8'}{/if}"
                                                        {if isset($input.maxlength) && $input.maxlength} maxlength="{$input.maxlength|intval}"{/if}
                                                        {if isset($input.maxchar) && $input.maxchar} data-maxchar="{$input.maxchar|intval}"{/if}
                                                >{$fields_value[$input.name][$language.id_lang]|escape:'html':'UTF-8'}</textarea>
                                            {if $languages|count > 1}
                                                </div>
                                                <div class="col-lg-2">
                                                    <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                                                        {$language.iso_code|escape:'htmlall':'UTF-8'}
                                                        <span class="caret"></span>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        {foreach from=$languages item=language}
                                                            <li>
                                                                <a href="javascript:hideOtherLanguage({$language.id_lang});" tabindex="-1">{$language.name|escape:'htmlall':'UTF-8'}</a>
                                                            </li>
                                                        {/foreach}
                                                    </ul>
                                                </div>
                                                </div>
                                            {/if}
                                            {/foreach}
                                            {if isset($input.maxchar) && $input.maxchar}
                                                <script type="text/javascript">
                                                    window.TNTOfficiel_Ready = window.TNTOfficiel_Ready || [];
                                                    window.TNTOfficiel_Ready.push(function (jQuery) {
                                                        {foreach from=$languages item=language}
                                                        countDown(jQuery("#{if isset($input.id)}{$input.id}_{$language.id_lang}{else}{$input.name}_{$language.id_lang}{/if}"), jQuery("#{if isset($input.id)}{$input.id}_{$language.id_lang}{else}{$input.name}_{$language.id_lang}{/if}_counter"));
                                                        {/foreach}
                                                    });
                                                </script>
                                            {/if}
                                            {else}
                                            {if isset($input.maxchar) && $input.maxchar}
                                                <span id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{/if}_counter" class="input-group-addon">
                                                    <span class="text-count-down">{$input.maxchar|intval}</span>
                                                </span>
                                            {/if}
                                                <textarea
                                                        {if isset($input.readonly) && $input.readonly} readonly="readonly"{/if}
                                                        name="{$input.name|escape:'html':'UTF-8'}"
                                                        id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}"
                                                        {if isset($input.cols)}cols="{$input.cols|escape:'html':'UTF-8'}"{/if}
                                                        {if isset($input.rows)}rows="{$input.rows|escape:'html':'UTF-8'}"{/if}
                                                        class="{if isset($input.autoload_rte) && $input.autoload_rte}rte autoload_rte{else}textarea-autosize{/if}{if isset($input.class)} {$input.class|escape:'html':'UTF-8'}{/if}"
                                                        {if isset($input.maxlength) && $input.maxlength} maxlength="{$input.maxlength|intval}"{/if}
                                                        {if isset($input.maxchar) && $input.maxchar} data-maxchar="{$input.maxchar|intval}"{/if}
                                                >{$fields_value[$input.name]|escape:'html':'UTF-8'}</textarea>
                                            {if isset($input.maxchar) && $input.maxchar}
                                                <script type="text/javascript">
                                                    window.TNTOfficiel_Ready = window.TNTOfficiel_Ready || [];
                                                    window.TNTOfficiel_Ready.push(function (jQuery) {
                                                        countDown(jQuery("#{if isset($input.id)}{$input.id}{else}{$input.name}{/if}"), jQuery("#{if isset($input.id)}{$input.id}{else}{$input.name}{/if}_counter"));
                                                    });
                                                </script>
                                            {/if}
                                            {/if}
                                            {if isset($input.maxchar) && $input.maxchar}</div>{/if}
                                        {elseif $input.type == 'checkbox'}
                                        {if isset($input.expand)}
                                            <a class="btn btn-default show_checkbox{if strtolower($input.expand.default) == 'hide'} hidden{/if}" href="#">
                                                <i class="icon-{$input.expand.show.icon|escape:'html':'UTF-8'}"></i>
                                                {$input.expand.show.text|escape:'htmlall':'UTF-8'}
                                                {if isset($input.expand.print_total) && $input.expand.print_total > 0}
                                                    <span class="badge">{$input.expand.print_total|escape:'htmlall':'UTF-8'}</span>
                                                {/if}
                                            </a>
                                            <a class="btn btn-default hide_checkbox{if strtolower($input.expand.default) == 'show'} hidden{/if}" href="#">
                                                <i class="icon-{$input.expand.hide.icon|escape:'html':'UTF-8'}"></i>
                                                {$input.expand.hide.text|escape:'htmlall':'UTF-8'}
                                                {if isset($input.expand.print_total) && $input.expand.print_total > 0}
                                                    <span class="badge">{$input.expand.print_total|escape:'htmlall':'UTF-8'}</span>
                                                {/if}
                                            </a>
                                        {/if}
                                        {foreach $input.values.query as $value}
                                            {assign var=id_checkbox value=$input.name|cat:'_'|cat:$value[$input.values.id]}
                                            <div class="checkbox{if isset($input.expand) && strtolower($input.expand.default) == 'show'} hidden{/if}">
                                                {strip}
                                                    <label for="{$id_checkbox|escape:'html':'UTF-8'}">
                                                        <input type="checkbox" name="{$id_checkbox|escape:'html':'UTF-8'}" id="{$id_checkbox|escape:'html':'UTF-8'}"
                                                               class="{if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if}"
                                                                {if isset($value.val)} value="{$value.val|escape:'html':'UTF-8'}"{/if}
                                                                {if isset($fields_value[$id_checkbox]) && $fields_value[$id_checkbox]} checked="checked"{/if} />
                                                        {$value[$input.values.name]|escape:'html':'UTF-8'}
                                                    </label>
                                                {/strip}
                                            </div>
                                        {/foreach}
                                        {elseif $input.type == 'change-password'}
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <button type="button" id="{$input.name|escape:'html':'UTF-8'}-btn-change" class="btn btn-default">
                                                        <i class="icon-lock"></i>
                                                        {l s='Change password...' mod='tntofficiel'}
                                                    </button>
                                                    <div id="{$input.name|escape:'html':'UTF-8'}-change-container" class="form-password-change well hide">
                                                        <div class="form-group">
                                                            <label for="old_passwd" class="control-label col-lg-2 required">
                                                                {l s='Current password' mod='tntofficiel'}
                                                            </label>
                                                            <div class="col-lg-10">
                                                                <div class="input-group fixed-width-lg">
                                                                                <span class="input-group-addon">
                                                                                    <i class="icon-unlock"></i>
                                                                                </span>
                                                                    <input type="password" id="old_passwd" name="old_passwd" class="form-control" value="" required="required" autocomplete="off">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <hr />
                                                        <div class="form-group">
                                                            <label for="{$input.name|escape:'html':'UTF-8'}" class="required control-label col-lg-2">
                                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{l s='Password should be at least 8 characters long.' mod='tntofficiel'}">
                                                                {l s='New password' mod='tntofficiel'}
                                                            </span>
                                                            </label>
                                                            <div class="col-lg-9">
                                                                <div class="input-group fixed-width-lg">
                                                                <span class="input-group-addon">
                                                                    <i class="icon-key"></i>
                                                                </span>
                                                                    <input type="password" id="{$input.name|escape:'html':'UTF-8'}" name="{$input.name|escape:'html':'UTF-8'}"
                                                                           class="{if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if}"
                                                                           value="" required="required" autocomplete="off"/>
                                                                </div>
                                                                <span id="{$input.name|escape:'html':'UTF-8'}-output"></span>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="{$input.name|escape:'html':'UTF-8'}2" class="required control-label col-lg-2">
                                                                {l s='Confirm password' mod='tntofficiel'}
                                                            </label>
                                                            <div class="col-lg-4">
                                                                <div class="input-group fixed-width-lg">
                                                                <span class="input-group-addon">
                                                                    <i class="icon-key"></i>
                                                                </span>
                                                                    <input type="password" id="{$input.name|escape:'html':'UTF-8'}2" name="{$input.name|escape:'html':'UTF-8'}2"
                                                                           class="{if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if}" value="" autocomplete="off"/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="col-lg-10 col-lg-offset-2">
                                                                <input type="text" class="form-control fixed-width-md pull-left" id="{$input.name|escape:'html':'UTF-8'}-generate-field" disabled="disabled">
                                                                <button type="button" id="{$input.name|escape:'html':'UTF-8'}-generate-btn" class="btn btn-default">
                                                                    <i class="icon-random"></i>
                                                                    {l s='Generate password' mod='tntofficiel'}
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-lg-12">
                                                                <button type="button" id="{$input.name|escape:'html':'UTF-8'}-cancel-btn" class="btn btn-default">
                                                                    <i class="icon-remove"></i>
                                                                    {l s='Cancel' d='Admin.Actions'}
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <script>
                                                // On Ready.
                                                window.TNTOfficiel_Ready = window.TNTOfficiel_Ready || [];
                                                window.TNTOfficiel_Ready.push(function (jQuery) {
                                                    var $oldPwd = jQuery('#old_passwd');
                                                    var $passwordField = jQuery('#{$input.name}');
                                                    var $output = jQuery('#{$input.name}-output');
                                                    var $generateBtn = jQuery('#{$input.name}-generate-btn');
                                                    var $generateField = jQuery('#{$input.name}-generate-field');
                                                    var $cancelBtn = jQuery('#{$input.name}-cancel-btn');

                                                    var feedback = [
                                                        { badge: 'text-danger', text: '{l s="Invalid" mod='tntofficiel' js=1}'},
                                                        { badge: 'text-warning', text: '{l s="Okay" mod='tntofficiel' js=1}'},
                                                        { badge: 'text-success', text: '{l s="Good" mod='tntofficiel' js=1}'},
                                                        { badge: 'text-success', text: '{l s="Fabulous" mod='tntofficiel' js=1}'}
                                                    ];
                                                    jQuery.passy.requirements.length.min = 8;
                                                    jQuery.passy.requirements.characters = 'DIGIT';
                                                    $passwordField.passy(function (strength, valid) {
                                                        $output.text(feedback[strength].text);
                                                        $output.removeClass('text-danger')
                                                        .removeClass('text-warning')
                                                        .removeClass('text-success');
                                                        $output.addClass(feedback[strength].badge);
                                                        if (valid) {
                                                            $output.show();
                                                        }
                                                        else {
                                                            $output.hide();
                                                        }
                                                    });
                                                    var $container = jQuery('#{$input.name}-change-container');
                                                    var $changeBtn = jQuery('#{$input.name}-btn-change');
                                                    var $confirmPwd = jQuery('#{$input.name}2');

                                                    $changeBtn
                                                    .on('click', function () {
                                                        $container.removeClass('hide');
                                                        $changeBtn.addClass('hide');
                                                    });
                                                    $generateBtn
                                                    .on('click', function () {
                                                        $generateField.passy('generate', 8);
                                                        var generatedPassword = $generateField.val();
                                                        $passwordField.val(generatedPassword);
                                                        $confirmPwd.val(generatedPassword);
                                                    });
                                                    $cancelBtn
                                                    .on('click', function () {
                                                        $container.find("input").val("");
                                                        $container.addClass('hide');
                                                        $changeBtn.removeClass('hide');
                                                    });

                                                    jQuery.validator.addMethod('password_same', function (value, element) {
                                                        return $passwordField.val() == $confirmPwd.val();
                                                    },
                                                    '{l s="Invalid password confirmation" mod='tntofficiel' js=1}');

                                                    jQuery('#employee_form').validate({
                                                        rules: {
                                                            "email": {
                                                                email: true
                                                            },
                                                            "{$input.name|escape:'html':'UTF-8'}": {
                                                                minlength: 8
                                                            },
                                                            "{$input.name|escape:'html':'UTF-8'}2": {
                                                                password_same: true
                                                            },
                                                            "old_passwd": {},
                                                        },
                                                        // override jquery validate plugin defaults for bootstrap 3
                                                        highlight: function (element) {
                                                            jQuery(element)
                                                            .closest('.form-group')
                                                            .addClass('has-error');
                                                        },
                                                        unhighlight: function (element) {
                                                            jQuery(element)
                                                            .closest('.form-group')
                                                            .removeClass('has-error');
                                                        },
                                                        errorElement: 'span',
                                                        errorClass: 'help-block',
                                                        errorPlacement: function (error, element) {
                                                            if (element.parent('.input-group').length) {
                                                                error.insertAfter(element.parent());
                                                            } else {
                                                                error.insertAfter(element);
                                                            }
                                                        }
                                                    });
                                                });
                                            </script>
                                        {elseif $input.type == 'password'}

                                            <div class="form-group {if isset($input.size)} col-md-{$input.size|escape:'html':'UTF-8'} {else} col-md-6{/if}">


                                                {if isset($input.label)}
                                                    <label class="control-label{if isset($input.required) && $input.required && $input.type != 'radio'} required{/if}">
                                                        {if isset($input.hint)}
                                                        <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="{if is_array($input.hint)}
                                                                        {foreach $input.hint as $hint}
                                                                            {if is_array($hint)}
                                                                                {$hint.text|escape:'html':'UTF-8'}
                                                                            {else}
                                                                                {$hint|escape:'html':'UTF-8'}
                                                                            {/if}
                                                                        {/foreach}
                                                                {else}
                                                                    {$input.hint|escape:'html':'UTF-8'}
                                                                {/if}">
                                                                    {/if}
                                                            {$input.label|escape:'htmlall':'UTF-8'}
                                                            {if isset($input.hint)}
                                                                                            </span>
                                                        {/if}
                                                    </label>
                                                {/if}
                                                <div class="input-group col-md-10">
                                                                <span class="input-group-addon">
                                                                    <i class="icon-key"></i>
                                                                </span>
                                                    {*mdp*}
                                                    <input type="password"
                                                           id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}"
                                                           name="{$input.name|escape:'html':'UTF-8'}"
                                                           class="{if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if}"
                                                           value="{$fields_value[$input.name]|escape:'html':'UTF-8'}"
                                                           {if isset($input.autocomplete) && !$input.autocomplete}autocomplete="off"{/if}
                                                            {if isset($input.required) && $input.required } required="required" {/if} />
                                                </div>
                                            </div>
                                        {if $input.name == 'TNTOFFICIEL_ACCOUNT_PASSWORD'}
                                            <div class="col-md-12">
                                                <hr>
                                            </div>
                                        {/if}
                                        {elseif $input.type == 'birthday'}
                                            <div class="form-group">
                                                {foreach $input.options as $key => $select}
                                                    <div class="col-lg-2">
                                                        <select name="{$key|escape:'html':'UTF-8'}" class="fixed-width-lg{if isset($input.class)} {$input.class|escape:'html':'UTF-8'}{/if}">
                                                            <option value="">-</option>
                                                            {if $key == 'months'}
                                                                {*
                                                                    This comment is useful to the translator tools /!\ do not remove them
                                                                    {l s='January' mod='tntofficiel'}
                                                                    {l s='February' mod='tntofficiel'}
                                                                    {l s='March' mod='tntofficiel'}
                                                                    {l s='April' mod='tntofficiel'}
                                                                    {l s='May' mod='tntofficiel'}
                                                                    {l s='June' mod='tntofficiel'}
                                                                    {l s='July' mod='tntofficiel'}
                                                                    {l s='August' mod='tntofficiel'}
                                                                    {l s='September' mod='tntofficiel'}
                                                                    {l s='October' mod='tntofficiel'}
                                                                    {l s='November' mod='tntofficiel'}
                                                                    {l s='December' mod='tntofficiel'}
                                                                *}
                                                                {foreach $select as $k => $v}
                                                                    <option value="{$k|escape:'html':'UTF-8'}" {if $k == $fields_value[$key]}selected="selected"{/if}>{l s=$v mod='tntofficiel'}</option>
                                                                {/foreach}
                                                            {else}
                                                                {foreach $select as $v}
                                                                    <option value="{$v|escape:'html':'UTF-8'}" {if $v == $fields_value[$key]}selected="selected"{/if}>{$v|escape:'htmlall':'UTF-8'}</option>
                                                                {/foreach}
                                                            {/if}
                                                        </select>
                                                    </div>
                                                {/foreach}
                                            </div>
                                        {elseif $input.type == 'group'}
                                            {assign var=groups value=$input.values}
                                            {include file='helpers/form/form_group.tpl'}
                                        {elseif $input.type == 'shop'}
                                            {$input.html}
                                        {elseif $input.type == 'categories'}
                                            {$categories_tree}
                                        {elseif $input.type == 'file'}
                                            {$input.file}
                                        {elseif $input.type == 'categories_select'}
                                            {$input.category_tree}
                                        {elseif $input.type == 'asso_shop' && isset($asso_shop) && $asso_shop}
                                            {$asso_shop}
                                        {elseif $input.type == 'color'}
                                            <div class="form-group">
                                                <div class="col-lg-2">
                                                    <div class="row">
                                                        <div class="input-group">
                                                            <input type="color"
                                                                   data-hex="true"
                                                                    {if isset($input.class)} class="{$input.class|escape:'html':'UTF-8'}"
                                                                    {else} class="color mColorPickerInput"{/if}
                                                                   name="{$input.name|escape:'html':'UTF-8'}"
                                                                   value="{$fields_value[$input.name]|escape:'html':'UTF-8'}" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        {elseif $input.type == 'date'}
                                            <div class="row">
                                                <div class="input-group col-lg-4">
                                                    <input
                                                            id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}"
                                                            type="text"
                                                            data-hex="true"
                                                            {if isset($input.class)} class="{$input.class|escape:'html':'UTF-8'}"
                                                            {else}class="datepicker"{/if}
                                                            name="{$input.name|escape:'html':'UTF-8'}"
                                                            value="{$fields_value[$input.name]|escape:'html':'UTF-8'}" />
                                                <span class="input-group-addon">
                                                    <i class="icon-calendar-empty"></i>
                                                </span>
                                                </div>
                                            </div>
                                        {elseif $input.type == 'datetime'}
                                            <div class="row">
                                                <div class="input-group col-lg-4">
                                                    <input
                                                            id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}"
                                                            type="text"
                                                            data-hex="true"
                                                            {if isset($input.class)} class="{$input.class|escape:'html':'UTF-8'}"
                                                            {else} class="datetimepicker"{/if}
                                                            name="{$input.name|escape:'html':'UTF-8'}"
                                                            value="{$fields_value[$input.name]|escape:'html':'UTF-8'}" />
                                                <span class="input-group-addon">
                                                    <i class="icon-calendar-empty"></i>
                                                </span>
                                                </div>
                                            </div>
                                        {elseif $input.type == 'free'}
                                            {* VALIDATOR: This variable is HTML content. Do not escape. *}
                                            {$fields_value[$input.name] nofilter}
                                        {elseif $input.type == 'html'}
                                            {if isset($input.html_content)}
                                                {* VALIDATOR: This variable is HTML content. Do not escape. *}
                                                {$input.html_content nofilter}
                                            {else}
                                                {* VALIDATOR: This variable is HTML content. Do not escape. *}
                                                {$input.name nofilter}
                                            {/if}
                                        {/if}
                                        {/block}{* end block input *}

                                            {*</div>*}
                                        {/block}{* end block field *}
                                        {/if}
                                        {*</div>*}
                                    {/block}
                                {/foreach}
                            </div><!-- /.form-wrapper -->
                        {elseif $key == 'desc'}
                            <div class="alert alert-info col-lg-offset-3">
                                {if is_array($field)}
                                    {foreach $field as $k => $p}
                                        {if is_array($p)}
                                            <span{if isset($p.id)} id="{$p.id|escape:'html':'UTF-8'}"{/if}>{$p.text|escape:'htmlall':'UTF-8'}</span><br />
                                        {else}
                                            {$p|escape:'htmlall':'UTF-8'}
                                            {if isset($field[$k+1])}<br />{/if}
                                        {/if}
                                    {/foreach}
                                {else}
                                    {$field|escape:'htmlall':'UTF-8'}
                                {/if}
                            </div>
                        {/if}
                        {block name="other_input"}{/block}
                    {/foreach}
                    {block name="footer"}
                        {capture name='form_submit_btn'}{counter name='form_submit_btn'}{/capture}
                        {if isset($fieldset['form']['submit']) || isset($fieldset['form']['buttons'])}
                            <div id="tab-zone">
                                <ul class="nav nav-tabs">
                                    {foreach $tntofficiel.arrZonesConfList as $intZoneConfID => $arrZoneConf}
                                        {if $tntofficiel.arrZonesInfoList['showTab'][$intZoneConfID]}
                                            <li class="{if $arrZoneConf@iteration == 1}active{/if}"><a href="#zone{$intZoneConfID|escape:'html':'UTF-8'}" data-toggle="tab">
                                                    {if $intZoneConfID == 0}
                                                        {TNTOfficiel::getCodeTranslate('tabRateZoneDefault')|escape:'htmlall':'UTF-8'}
                                                    {else}
                                                        {sprintf(TNTOfficiel::getCodeTranslate('tabRateZone'), $intZoneConfID)|escape:'htmlall':'UTF-8'}
                                                    {/if}
                                                </a></li>
                                        {/if}
                                    {/foreach}
                                </ul>
                                <div class="tab-content panel">
                                    {foreach $tntofficiel.arrZonesConfList as $intZoneConfID => $arrZoneConf}
                                        <div id="zone{$intZoneConfID|escape:'html':'UTF-8'}" class="tab-pane {if $arrZoneConf@iteration == 1}active{/if}">
                                            {* <pre>{$arrZoneConf|var_export:true|escape:'htmlall':'UTF-8'}</pre> *}
                                            <div class="alert alert-context">
                                                {if ($intZoneConfID == 0)}
                                                    <strong>{l s='Entering the pricing for the Default Zone (Metropolitan France, excluding regional tariff zones 1 and 2)' mod='tntofficiel'}
                                                        :</strong>
                                                {else}
                                                    <strong>{l s='Entering pricing for the Regional Tariff Area' mod='tntofficiel'} {$intZoneConfID|escape:'html':'UTF-8'}
                                                        :</strong>
                                                    <ul>
                                                        {* VALIDATOR: This variable is HTML content. Do not escape. *}
                                                        {$tntofficiel.arrZonesInfoList['html'][$intZoneConfID] nofilter}
                                                    </ul>
                                                {/if}
                                            </div>
                                            {if ($intZoneConfID > 0)
                                            && (($arrZoneConf['strRangeType'] == 'weight' && $arrZoneConf['arrRangeWeightList']|count == 0)
                                            || ($arrZoneConf['strRangeType'] == 'price' && $arrZoneConf['arrRangePriceList']|count == 0))}
                                            <div class="alert alert-warning">
                                                {l s='If no slice is defined, the default zone is used.' mod='tntofficiel'}
                                            </div>
                                            {/if}
                                            <br/>
                                            <div class="form-group" data-tab-id="zone{$intZoneConfID|escape:'html':'UTF-8'}">
                                                <label class="control-label col-lg-4">{l s='Calculate shipping costs according to the price or weight of the basket?' mod='tntofficiel'}</label>
                                                <div class="col-lg-2">
                                                    <select name="TNTOFFICIEL_ZONES_CONF[{$intZoneConfID|escape:'html':'UTF-8'}][strRangeType]"
                                                            class="TNTOFFICIEL_ZONES_TYPE"
                                                    >
                                                        <option value="weight" {if $arrZoneConf['strRangeType'] == 'weight'} selected{/if}>Poids</option>
                                                        <option value="price" {if $arrZoneConf['strRangeType'] == 'price'} selected{/if}>Prix (TTC)</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group" data-tab-id="zone{$intZoneConfID|escape:'html':'UTF-8'}">
                                                    <div class="row clearfix">
                                                        <div class="col-sm-12 col-md-10 col-lg-9">
                                                            <table class="table table-bordered table-hover">
                                                                <thead>
                                                                <tr>
                                                                    <th class="text-center">
                                                                        {if $arrZoneConf['strRangeType'] == 'price'}
                                                                            {l s='Will be applied when the TTC price is <(€)' mod='tntofficiel'}
                                                                        {else}
                                                                            {l s='Will be applied when the weight is = <(kg)' mod='tntofficiel'}
                                                                        {/if}
                                                                    </th>
                                                                    <th class="text-center">
                                                                        {l s='Price of postage taxes to be applied (€)' mod='tntofficiel'}
                                                                    </th>
                                                                    <th class="text-center">
                                                                        {l s='Remove' mod='tntofficiel'}
                                                                    </th>
                                                                </tr>
                                                                </thead>
                                                                <tbody id="tab_weight" class="{if $arrZoneConf['strRangeType'] == 'price'} hidden {/if}">
                                                                {if isset($arrZoneConf['arrRangeWeightList'])}
                                                                    {foreach $arrZoneConf['arrRangeWeightList'] as $col1 => $col2}
                                                                        <tr id='addr{$col2@iteration}'>
                                                                            <td>
                                                                                <div class="col-sm-6 col-sm-offset-3">
                                                                                    <input type="text"
                                                                                            {*name="TNTOFFICIEL_LW_{$intZoneConfID|escape:'html':'UTF-8'}_COL1[]"*}
                                                                                           name="TNTOFFICIEL_ZONES_CONF[{$intZoneConfID|escape:'html':'UTF-8'}][arrRangeWeightListCol1][]"
                                                                                           class="form-control"
                                                                                           value="{$col1}">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="col-sm-6 col-sm-offset-3">
                                                                                    <input type="text" name="TNTOFFICIEL_ZONES_CONF[{$intZoneConfID|escape:'html':'UTF-8'}][arrRangeWeightListCol2][]" class="form-control" value="{$col2}">
                                                                                    {*<input type="text" name="TNTOFFICIEL_LW_{$intZoneConfID|escape:'html':'UTF-8'}_COL2[]" class="form-control" value="{$col2}">*}
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <a class="delete_row pull-right btn btn-default"><i class="icon-minus"></i></a>
                                                                            </td>
                                                                        </tr>
                                                                    {/foreach}
                                                                    {assign var=nbTr value=({$arrZoneConf['arrRangeWeightList']|@count}+1) }
                                                                {else}
                                                                    {assign var=nbTr value=0 }
                                                                {/if}
                                                                <tr id="addr{$nbTr|escape:'html':'UTF-8'}"></tr>
                                                                </tbody>
                                                                <tbody id="tab_price" class="{if $arrZoneConf['strRangeType'] == 'weight'} hidden {/if}">
                                                                {if isset($arrZoneConf['arrRangePriceList'])}
                                                                    {foreach $arrZoneConf['arrRangePriceList'] as $col1 => $col2}
                                                                        <tr id='addr{$col2@iteration}'>
                                                                            <td>
                                                                                <div class="col-sm-6 col-sm-offset-3">
                                                                                    <input type="text" name="TNTOFFICIEL_ZONES_CONF[{$intZoneConfID|escape:'html':'UTF-8'}][arrRangePriceListCol1][]" class="form-control" value="{$col1}">
                                                                                    {*<input type="text" name="TNTOFFICIEL_LP_{$intZoneConfID|escape:'html':'UTF-8'}_COL1[]" class="form-control" value="{$col1}">*}
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="col-sm-6 col-sm-offset-3">
                                                                                    <input type="text" name="TNTOFFICIEL_ZONES_CONF[{$intZoneConfID|escape:'html':'UTF-8'}][arrRangePriceListCol2][]" class="form-control" value="{$col2}">
                                                                                    {*<input type="text" name="TNTOFFICIEL_LP_{$intZoneConfID|escape:'html':'UTF-8'}_COL2[]" class="form-control" value="{$col2}">*}
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <a id='delete_row' class="delete_row pull-right btn btn-default"><i class="icon-minus"></i></a>
                                                                            </td>
                                                                        </tr>
                                                                    {/foreach}
                                                                    {assign var=nbTr value=({$arrZoneConf['arrRangePriceList']|@count}+1) }
                                                                {else}
                                                                    {assign var=nbTr value=0 }
                                                                {/if}
                                                                <tr id="addr{$nbTr|escape:'html':'UTF-8'}"></tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-9">
                                                        <a id="add_row" class="add_row btn btn-default pull-left"><i class="icon-plus"></i></a>
                                                        {*<a id='delete_row' class="delete_row pull-right btn btn-default"><i class="icon-minus"></i></a>*}
                                                    </div>
                                            </div>
                                            <div id="field_price_sup" class="form-group {if $arrZoneConf['strRangeType'] == 'price'} hidden{/if}" data-tab-id="zone{$intZoneConfID|escape:'html':'UTF-8'}">
                                                <label class="control-label col-lg-4">
                                                    {l s='Price of extra kilogram (€)' mod='tntofficiel'} :
                                                </label>
                                                <div class="col-lg-1">
                                                    <input type="text"
                                                           name="TNTOFFICIEL_ZONES_CONF[{$intZoneConfID|escape:'html':'UTF-8'}][fltRangeWeightPricePerKg]"
                                                           value="{if isset($arrZoneConf['fltRangeWeightPricePerKg'])}{$arrZoneConf['fltRangeWeightPricePerKg']}{/if}"
                                                    />
                                                </div>
                                            </div>
                                            <div id="field_limit" class="form-group {if $arrZoneConf['strRangeType'] == 'price'} hidden{/if}" data-tab-id="zone{$intZoneConfID|escape:'html':'UTF-8'}">
                                                <label class="control-label col-lg-4">
                                                    {l s='Within the limit of (kg)' mod='tntofficiel'} :
                                                </label>
                                                <div class="col-lg-1">
                                                    <input type="text"
                                                           name="TNTOFFICIEL_ZONES_CONF[{$intZoneConfID|escape:'html':'UTF-8'}][fltRangeWeightLimitMax]"
                                                           value="{if isset($arrZoneConf['fltRangeWeightLimitMax'])}{$arrZoneConf['fltRangeWeightLimitMax']}{/if}"
                                                    />
                                                </div>
                                            </div>
                                            <div class="form-group" data-tab-id="zone{$intZoneConfID|escape:'html':'UTF-8'}">
                                                <label class="control-label col-lg-4">
                                                    {l s='Beyond the last tranche, adopt the following behavior :' mod='tntofficiel'}
                                                </label>
                                                <div class="col-lg-3">
                                                    <select name="TNTOFFICIEL_ZONES_CONF[{$intZoneConfID|escape:'html':'UTF-8'}][strOutOfRangeBehavior]" >
                                                        <option value="lastrange"
                                                                {if $arrZoneConf['strOutOfRangeBehavior'] == 'lastrange'}selected{/if}
                                                        >{l s='Apply the price of the largest slice' mod='tntofficiel'}</option>
                                                        <option value="disabled"
                                                                {if $arrZoneConf['strOutOfRangeBehavior'] == 'disabled'}selected{/if}
                                                        >{l s='Disable the carrier' mod='tntofficiel'}</option>
                                                    </select>
                                                </div>
                                            </div>
                                            {if $tntofficiel.isHRAApplicable }
                                            <div class="form-group" data-tab-id="zone{$intZoneConfID|escape:'html':'UTF-8'}">
                                                <label class="control-label col-lg-4">
                                                    {l s='Additional cost to be applied for deliveries in hard to reach areas (€)' mod='tntofficiel'} :</label>
                                                <div class="col-lg-1">
                                                    <input type="text"
                                                           name="TNTOFFICIEL_ZONES_CONF[{$intZoneConfID|escape:'html':'UTF-8'}][fltHRAAdditionalCost]"
                                                           value="{if isset($arrZoneConf['fltHRAAdditionalCost'])}{$arrZoneConf['fltHRAAdditionalCost']}{/if}"
                                                    />
                                                </div>
                                            </div>
                                            {/if}
                                            <div class="form-group" data-tab-id="zone{$intZoneConfID|escape:'html':'UTF-8'}">
                                                <label class="control-label col-lg-4">
                                                    {l s='Apply an extra margin on calculated prices (%)' mod='tntofficiel'} :</label>
                                                <div class="col-lg-1">
                                                    <input type="text"
                                                           name="TNTOFFICIEL_ZONES_CONF[{$intZoneConfID|escape:'html':'UTF-8'}][fltMarginPercent]"
                                                           value="{if isset($arrZoneConf['fltMarginPercent'])}{$arrZoneConf['fltMarginPercent']}{/if}"
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                    {/foreach}
                                </div>
                            </div>
                            {if ($tntofficiel.arrCarrierCloningTableList) and ($input.name == 'TNTOFFICIEL_ZONES_CLONING_ENABLED') }
                                {*switch*}
                                <div class="clearfix">
                                    <div class="form-group {if isset($input.size)} col-md-{$input.size|escape:'html':'UTF-8'} {else} col-md-6{/if}">
                                        {if isset($input.label)}
                                            <label class="control-label{if isset($input.required) && $input.required && $input.type != 'radio'} required{/if}">
                                                {$input.label|escape:'html':'UTF-8'}
                                            </label>
                                        {/if}
                                        <div class="input-group col-md-10">
                                            <span class="switch prestashop-switch fixed-width-lg">
                                                {foreach $input.values as $value}
                                                    <input type="radio"
                                                           name="{$input.name|escape:'html':'UTF-8'}"{if $value.value == 1}
                                                    id="{$input.name|escape:'html':'UTF-8'}_on"{else} id="{$input.name|escape:'html':'UTF-8'}_off"{/if}
                                                           value="{$value.value|escape:'html':'UTF-8'}"
                                                            {if $fields_value[$input.name] == $value.value} checked="checked"{/if}
                                                            {if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}
                                                    />
                                                    {strip}
                                                    <label {if $value.value == 1} for="{$input.name|escape:'html':'UTF-8'}_on"{else} for="{$input.name|escape:'html':'UTF-8'}_off"{/if}>
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
                                    </div>
                                </div>
                                {*listCloning*}
                                <div id="tab-cloning" class="table-responsive-row clearfix">
                                    <table id="table-configuration" class="table configuration">
                                        <thead>
                                        <tr class="nodrag nodrop">
                                            <th class="center fixed-width-xs"></th>
                                            <th class="fixed-width-xs center">{l s='TNT service' mod='tntofficiel'}</th>
                                            <th class="fixed-width-xs center">{l s='Carrier' mod='tntofficiel'}</th>
                                            <th class="fixed-width-xs center">{l s='Shop' mod='tntofficiel'}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        {foreach $tntofficiel.arrCarrierCloningTableList as $key => $carrier}
                                            <tr class="odd">
                                                <td class="row-selector text-center">
                                                    <input type="checkbox"
                                                            {if ($carrier.checkedValue)}
                                                                checked="checked"
                                                            {/if}
                                                           name="carriersSelected[]"
                                                           value="{$carrier.carrier_id|escape:'html':'UTF-8'}"
                                                           class="noborder"
                                                    />
                                                </td>
                                                <td class="pointer fixed-width-xs center">{$carrier.service_label|escape:'html':'UTF-8'}</td>
                                                <td class="pointer fixed-width-xs center">{$carrier.carrier_name|escape:'html':'UTF-8'}</td>
                                                <td class="pointer fixed-width-xs center">{$carrier.shop|escape:'html':'UTF-8'}</td>
                                            </tr>
                                        {/foreach}
                                        </tbody>
                                    </table>
                                </div>
                                <br />
                            {/if}
                            <div class="panel-footer">
                                {if isset($fieldset['form']['submit']) && !empty($fieldset['form']['submit'])}
                                    <button type="submit" value="1"
                                            id="{if isset($fieldset['form']['submit']['id'])}{$fieldset['form']['submit']['id']}{else}{$table|escape:'html':'UTF-8'}_form_submit_btn{/if}{if $smarty.capture.form_submit_btn > 1}_{($smarty.capture.form_submit_btn - 1)|intval}{/if}"
                                            name="{if isset($fieldset['form']['submit']['name'])}{$fieldset['form']['submit']['name']}{else}{$submit_action|escape:'html':'UTF-8'}{/if}{if isset($fieldset['form']['submit']['stay']) && $fieldset['form']['submit']['stay']}AndStay{/if}"
                                            class="{if isset($fieldset['form']['submit']['class'])}{$fieldset['form']['submit']['class']}{else}btn btn-default pull-right{/if}">
                                        <i class="{if isset($fieldset['form']['submit']['icon'])}{$fieldset['form']['submit']['icon']}{else}process-icon-save{/if}"></i> {$fieldset['form']['submit']['title']}
                                    </button>
                                {/if}
                                {if isset($show_cancel_button) && $show_cancel_button}
                                    <a href="{$back_url|escape:'html':'UTF-8'}" class="btn btn-default" onclick="window.history.back();">
                                        <i class="process-icon-cancel"></i> {l s='Cancel' d='Admin.Actions'}
                                    </a>
                                {/if}
                                {if isset($fieldset['form']['reset'])}
                                    <button
                                            type="reset"
                                            id="{if isset($fieldset['form']['reset']['id'])}{$fieldset['form']['reset']['id']}{else}{$table|escape:'html':'UTF-8'}_form_reset_btn{/if}"
                                            class="{if isset($fieldset['form']['reset']['class'])}{$fieldset['form']['reset']['class']}{else}btn btn-default{/if}"
                                    >
                                        {if isset($fieldset['form']['reset']['icon'])}<i class="{$fieldset['form']['reset']['icon']}"></i> {/if} {$fieldset['form']['reset']['title']}
                                    </button>
                                {/if}
                                {if isset($fieldset['form']['buttons'])}
                                    {foreach from=$fieldset['form']['buttons'] item=btn key=k}
                                        {if isset($btn.href) && trim($btn.href) != ''}
                                            <a href="{$btn.href|escape:'html':'UTF-8'}" {if isset($btn['id'])}id="{$btn['id']}"{/if}
                                               class="btn btn-default{if isset($btn['class'])} {$btn['class']}{/if}"
                                                    {if isset($btn.js) && $btn.js} onclick="{$btn.js}"{/if}
                                            >{if isset($btn['icon'])}<i class="{$btn['icon']}" ></i> {/if}{$btn.title|escape:'htmlall':'UTF-8'}</a>
                                        {else}
                                            <button type="{if isset($btn['type'])}{$btn['type']}{else}button{/if}"
                                                    {if isset($btn['id'])}id="{$btn['id']}"{/if}
                                                    class="btn btn-default{if isset($btn['class'])} {$btn['class']}{/if}"
                                                    name="{if isset($btn['name'])}{$btn['name']}{else}submitOptions{$table|escape:'html':'UTF-8'}{/if}"
                                                    {if isset($btn.js) && $btn.js} onclick="{$btn.js}"{/if}
                                            >{if isset($btn['icon'])}<i class="{$btn['icon']}" ></i> {/if}{$btn.title|escape:'htmlall':'UTF-8'}</button>
                                        {/if}
                                    {/foreach}
                                {/if}
                            </div>
                        {/if}
                    {/block}
                </div>
            {/block}
            {block name="other_fieldsets"}{/block}
        {/foreach}
    </form>
{/block}
