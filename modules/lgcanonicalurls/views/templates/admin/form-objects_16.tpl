{**
 * Copyright 2022 LÍNEA GRÁFICA E.C.E S.L.
 *
 * @author    Línea Gráfica E.C.E. S.L.
 * @copyright Lineagrafica.es - Línea Gráfica E.C.E. S.L. all rights reserved.
 * @license   https://www.apache.org/licenses/LICENSE-2.0
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *}

{foreach $field as $input}
    {block name="input_row"}
        <div class="form-group{if isset($input.form_group_class)} {$input.form_group_class|escape:'htmlall':'UTF-8'}{/if}{if $input.type == 'hidden'} hide{/if}"{if $input.name == 'id_state'} id="contains_states"{if !$contains_states} style="display:none;"{/if}{/if}{if isset($tabs) && isset($input.tab)} data-tab-id="{$input.tab|escape:'htmlall':'UTF-8'}"{/if}>
            {if $input.type == 'hidden'}
                <input type="hidden" name="{$input.name|escape:'htmlall':'UTF-8'}" id="{$input.name|escape:'htmlall':'UTF-8'}" value="{if isset($fields_value[$input.name])}{$fields_value[$input.name]|escape:'html':'UTF-8'}{/if}" />
            {else}
                {block name="label"}
                    {if isset($input.label)}
                        <label class="control-label col-lg-3{if isset($input.required) && $input.required && $input.type != 'radio'} required{/if}">
                            {if isset($input.hint)}
                            <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="{if is_array($input.hint)}
                                    {foreach $input.hint as $hint}
                                        {if is_array($hint)}
                                            {$hint.text|escape:'quotes':'UTF-8'}
                                        {else}
                                            {$hint|escape:'quotes':'UTF-8'}
                                        {/if}
                                    {/foreach}
                                {else}
                                    {$input.hint|escape:'quotes':'UTF-8'}
                                {/if}">
                            {/if}
                            {$input.label|escape:'htmlall':'UTF-8'}
                            {if isset($input.hint)}
                            </span>
                            {/if}
                        </label>
                    {/if}
                {/block}

                {block name="field"}
                    <div class="col-lg-{if isset($input.col)}{$input.col|intval}{else}9{/if}{if !isset($input.label)} col-lg-offset-3{/if}">
                        
                    {block name="input"}
                       
                        {if $input.type == 'text' || $input.type == 'tags'}
                            
                            {if isset($input.lang) AND $input.lang}
                                
                                {if $langs|count > 1} 
                                <div class="form-group">
                                {/if}
                                {foreach $langs as $language}
                                    {if isset($fields_value[$input.name])}
                                        {assign var='value_text' value=$fields_value[$input.name][$language.id_lang]}
                                    {else}
                                        {assign var='value_text' value=''}
                                    {/if}

                                    {if $langs|count > 1}
                                        <div class="translatable-field lang-{$language.id_lang|escape:'htmlall':'UTF-8'}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
                                        <div class="col-lg-9 aaa">
                                    {/if}
                                    {if $input.type == 'tags'}
                                    {literal}
                                        <script type="text/javascript">
                                            $().ready(function () {
                                                var input_id = '{/literal}{if isset($input.id)}{$input.id|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}{else}{$input.name|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}{/if}{literal}';
                                                $('#'+input_id).tagify({delimiters: [13,44], addTagPrompt: '{/literal}{l s='Add tag' js=1 mod='lgcanonicalurls'}{literal}'});
                                                $('#{/literal}{$table|escape:'htmlall':'UTF-8'}{literal}_form').submit( function() {
                                                    $(this).find('#'+input_id).val($('#'+input_id).tagify('serialize'));
                                                });
                                            });
                                        </script>
                                    {/literal}
                                    {/if}
                                    {if isset($input.maxchar) || isset($input.prefix) || isset($input.suffix)}
                                    <div class="input-group{if isset($input.class)} {$input.class|escape:'htmlall':'UTF-8'}{/if}">
                                    {/if}
                                    {if isset($input.maxchar) && $input.maxchar}
                                        <span id="{if isset($input.id)}{$input.id|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}{else}{$input.name|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}{/if}_counter" class="input-group-addon">
                                            <span class="text-count-down">{$input.maxchar|intval}</span>
                                        </span>
                                    {/if}
                                    {if isset($input.prefix)}
                                        <span class="input-group-addon">
                                          {$input.prefix|escape:'htmlall':'UTF-8'}
                                        </span>
                                    {/if}
                                    <input type="text"
                                           id="{if isset($input.id)}{$input.id|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}{else}{$input.name|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}{/if}"
                                           name="{$input.name|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}"
                                           class="{if isset($input.class)}{$input.class|escape:'htmlall':'UTF-8'}{/if}{if $input.type == 'tags'} tagify{/if}"
                                           value="{if isset($input.string_format) && $input.string_format}{$value_text|string_format:$input.string_format|escape:'html':'UTF-8'}{else}{$value_text|escape:'html':'UTF-8'}{/if}"
                                           onkeyup="if (isArrowKey(event)) return ;updateFriendlyURL();"
                                            {if isset($input.size)} size="{$input.size|escape:'htmlall':'UTF-8'}"{/if}
                                            {if isset($input.maxchar) && $input.maxchar} data-maxchar="{$input.maxchar|intval}"{/if}
                                            {if isset($input.maxlength) && $input.maxlength} maxlength="{$input.maxlength|intval}"{/if}
                                            {if isset($input.readonly) && $input.readonly} readonly="readonly"{/if}
                                            {if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}
                                            {if isset($input.autocomplete) && !$input.autocomplete} autocomplete="off"{/if}
                                            {if isset($input.required) && $input.required} required="required" {/if}
                                            {if isset($input.placeholder) && $input.placeholder} placeholder="{$input.placeholder|escape:'htmlall':'UTF-8'}"{/if} />
                                    {if isset($input.suffix)}
                                        <span class="input-group-addon">
                                          {$input.suffix|escape:'htmlall':'UTF-8'}
                                        </span>
                                    {/if}
                                    {if isset($input.maxchar) || isset($input.prefix) || isset($input.suffix)}
                                        </div>
                                    {/if}
                                    {if $langs|count > 1}
                                        </div>
                                        <div class="col-lg-2">
                                            <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                                                {$language.iso_code|escape:'htmlall':'UTF-8'}
                                                <i class="icon-caret-down"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                {foreach from=$langs item=language}
                                                    <li><a href="javascript:hideOtherLanguage({$language.id_lang|escape:'htmlall':'UTF-8'});" tabindex="-1">{$language.name|escape:'htmlall':'UTF-8'}</a></li>
                                                {/foreach}
                                            </ul>
                                        </div>
                                        </div>
                                    {/if}
                                {/foreach}
                                {if isset($input.maxchar) && $input.maxchar}
                                    <script type="text/javascript">
                                        $(document).ready(function(){
                                            {foreach from=$lans item=language}
                                            countDown($("#{if isset($input.id)}{$input.id|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}{else}{$input.name|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}{/if}"), $("#{if isset($input.id)}{$input.id|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}{else}{$input.name|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}{/if}_counter"));
                                            {/foreach}
                                        });
                                    </script>
                                {/if}
                                {if $langs|count > 1}
                                </div>
                                {/if}
                            {else}
                            {if $input.type == 'tags'}
                                {literal}
                                    <script type="text/javascript">
                                        $().ready(function () {
                                            var input_id = '{/literal}{if isset($input.id)}{$input.id|escape:'htmlall':'UTF-8'}{else}{$input.name|escape:'htmlall':'UTF-8'}{/if}{literal}';
                                            $('#'+input_id).tagify({delimiters: [13,44], addTagPrompt: '{/literal}{l s='Add tag' mod='lgcanonicalurls'}{literal}'});
                                            $({/literal}'#{$table|escape:'htmlall':'UTF-8'}{literal}_form').submit( function() {
                                                $(this).find('#'+input_id).val($('#'+input_id).tagify('serialize'));
                                            });
                                        });
                                    </script>
                                {/literal}
                            {/if}
                            {if isset($fields_value[$input.name])}
                                {assign var='value_text' value=$fields_value[$input.name]}
                            {else}
                                {assign var='value_text' value=''}
                            {/if}
                            {if isset($input.maxchar) || isset($input.prefix) || isset($input.suffix)}
                                <div class="input-group{if isset($input.class)} {$input.class|escape:'htmlall':'UTF-8'}{/if}">
                            {/if}
                            {if isset($input.maxchar) && $input.maxchar}
                                <span id="{if isset($input.id)}{$input.id|escape:'htmlall':'UTF-8'}{else}{$input.name|escape:'htmlall':'UTF-8'}{/if}_counter" class="input-group-addon"><span class="text-count-down">{$input.maxchar|intval}</span></span>
                            {/if}
                            {if isset($input.prefix)}
                                <span class="input-group-addon">
                                  {$input.prefix|escape:'htmlall':'UTF-8'}
                                </span>
                            {/if}
                            <input type="text"
                                   {if isset($input.name)}name="{$input.name|escape:'htmlall':'UTF-8'}"{/if}
                                   {if isset($input.id)}id="{$input.id|escape:'htmlall':'UTF-8'}"{else}{if isset($input.name)}id="{$input.name|escape:'htmlall':'UTF-8'}"{/if}{/if}
                                   value="{if isset($input.string_format) && $input.string_format}{$value_text|string_format:$input.string_format|escape:'html':'UTF-8'}{else}{$value_text|escape:'html':'UTF-8'}{/if}"
                                   class="{if isset($input.class)}{$input.class|escape:'htmlall':'UTF-8'}{/if}{if $input.type == 'tags'} tagify{/if}"
                                    {if isset($input.size)} size="{$input.size|escape:'htmlall':'UTF-8'}"{/if}
                                    {if isset($input.maxchar) && $input.maxchar} data-maxchar="{$input.maxchar|intval}"{/if}
                                    {if isset($input.maxlength) && $input.maxlength} maxlength="{$input.maxlength|intval}"{/if}
                                    {if isset($input.readonly) && $input.readonly} readonly="readonly"{/if}
                                    {if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}
                                    {if isset($input.autocomplete) && !$input.autocomplete} autocomplete="off"{/if}
                                    {if isset($input.required) && $input.required } required="required" {/if}
                                    {if isset($input.placeholder) && $input.placeholder } placeholder="{$input.placeholder|escape:'htmlall':'UTF-8'}"{/if}
                            />
                            {if isset($input.suffix)}
                                <span class="input-group-addon">
                                  {$input.suffix|escape:'htmlall':'UTF-8'}
                                </span>
                            {/if}

                            {if isset($input.maxchar) || isset($input.prefix) || isset($input.suffix)}
                                </div>
                            {/if}
                            {if isset($input.maxchar) && $input.maxchar}
                                <script type="text/javascript">
                                    $(document).ready(function(){
                                        countDown($("#{if isset($input.id)}{$input.id|escape:'htmlall':'UTF-8'}{else}{$input.name|escape:'htmlall':'UTF-8'}{/if}"), $("#{if isset($input.id)}{$input.id|escape:'htmlall':'UTF-8'}{else}{$input.name|escape:'htmlall':'UTF-8'}{/if}_counter"));
                                    });
                                </script>
                            {/if}
                        {/if}
                            {elseif $input.type == 'textbutton'}
                                {if isset($fields_value[$input.name])}
                                    {assign var='value_text' value=$fields_value[$input.name]}
                                {else}
                                    {assign var='value_text' value=''}
                                {/if}
                                <div class="row">
                                    <div class="col-lg-9">
                                        {if isset($input.maxchar)}
                                        <div class="input-group">
            <span id="{if isset($input.id)}{$input.id|escape:'htmlall':'UTF-8'}{else}{$input.name|escape:'htmlall':'UTF-8'}{/if}_counter" class="input-group-addon">
                <span class="text-count-down">{$input.maxchar|intval}</span>
            </span>
                                            {/if}
                                            <input type="text"
                                                   {if isset($input.name)}name="{$input.name|escape:'htmlall':'UTF-8'}"{/if}
                                                   id="{if isset($input.id)}{$input.id|escape:'htmlall':'UTF-8'}{else}{$input.name|escape:'htmlall':'UTF-8'}{/if}"
                                                   value="{if isset($input.string_format) && $input.string_format}{$value_text|string_format:$input.string_format|escape:'html':'UTF-8'}{else}{$value_text|escape:'html':'UTF-8'}{/if}"
                                                   class="{if isset($input.class)}{$input.class|escape:'htmlall':'UTF-8'}{/if}{if $input.type == 'tags'} tagify{/if}"
                                                    {if isset($input.size)} size="{$input.size|escape:'htmlall':'UTF-8'}"{/if}
                                                    {if isset($input.maxchar) && $input.maxchar} data-maxchar="{$input.maxchar|intval}"{/if}
                                                    {if isset($input.maxlength) && $input.maxlength} maxlength="{$input.maxlength|intval}"{/if}
                                                    {if isset($input.readonly) && $input.readonly} readonly="readonly"{/if}
                                                    {if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}
                                                    {if isset($input.autocomplete) && !$input.autocomplete} autocomplete="off"{/if}
                                                    {if isset($input.placeholder) && $input.placeholder } placeholder="{$input.placeholder|escape:'htmlall':'UTF-8'}"{/if}
                                            />
                                            {if isset($input.suffix)}{$input.suffix|escape:'htmlall':'UTF-8'}{/if}
                                            {if isset($input.maxchar) && $input.maxchar}
                                        </div>
                                        {/if}
                                    </div>
                                    <div class="col-lg-2">
                                        <button type="button" class="btn btn-default{if isset($input.button.attributes['class'])} {$input.button.attributes['class']|escape:'htmlall':'UTF-8'}{/if}{if isset($input.button.class)} {$input.button.class|escape:'htmlall':'UTF-8'}{/if}"
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
                                    $(document).ready(function() {
                                        countDown($("#{if isset($input.id)}{$input.id|escape:'htmlall':'UTF-8'}{else}{$input.name|escape:'htmlall':'UTF-8'}{/if}"), $("#{if isset($input.id)|escape:'htmlall':'UTF-8'}{$input.id|escape:'htmlall':'UTF-8'}{else}{$input.name|escape:'htmlall':'UTF-8'}{/if}_counter"));
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
                                                                <option value="{$option->$input.options.id|escape:'htmlall':'UTF-8'}">{$option->$input.options.name|escape:'htmlall':'UTF-8'}</option>
                                                            {/if}
                                                        {elseif $option == "-"}
                                                            <option value="">-</option>
                                                        {else}
                                                            {if !in_array($option[$input.options.id], $fields_value[$input.name])}
                                                                <option value="{$option[$input.options.id]|escape:'htmlall':'UTF-8'}">{$option[$input.options.name]|escape:'htmlall':'UTF-8'}</option>
                                                            {/if}
                                                        {/if}
                                                    {/foreach}
                                                </select>
                                                <a href="#" id="addSwap" class="btn btn-default btn-block">{l s='Add' mod='lgcanonicalurls'} <i class="icon-arrow-right"></i></a>
                                            </div>
                                            <div class="col-xs-6">
                                                <select {if isset($input.size)}size="{$input.size|escape:'html':'UTF-8'}"{/if}{if isset($input.onchange)} onchange="{$input.onchange|escape:'html':'UTF-8'}"{/if} class="{if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if}" id="selectedSwap" name="{$input.name|escape:'html':'UTF-8'}_selected[]" multiple="multiple">
                                                    {foreach $input.options.query AS $option}
                                                        {if is_object($option)}
                                                            {if in_array($option->$input.options.id, $fields_value[$input.name])}
                                                                <option value="{$option->$input.options.id|escape:'htmlall':'UTF-8'}">{$option->$input.options.name|escape:'htmlall':'UTF-8'}</option>
                                                            {/if}
                                                        {elseif $option == "-"}
                                                            <option value="">-</option>
                                                        {else}
                                                            {if in_array($option[$input.options.id], $fields_value[$input.name])}
                                                                <option value="{$option[$input.options.id]|escape:'htmlall':'UTF-8'}">{$option[$input.options.name]|escape:'htmlall':'UTF-8'}</option>
                                                            {/if}
                                                        {/if}
                                                    {/foreach}
                                                </select>
                                                <a href="#" id="removeSwap" class="btn btn-default btn-block"><i class="icon-arrow-left"></i> {l s='Remove' mod='lgcanonicalurls'}</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            {elseif $input.type == 'select'}
                            {if isset($input.options.query) && !$input.options.query && isset($input.empty_message)}
                                {$input.empty_message|escape:'htmlall':'UTF-8'}
                                {$input.required = false}
                                {$input.desc = null}
                            {else}
                                <select name="{$input.name|escape:'html':'UTF-8'}"
                                        class="{if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if} fixed-width-xl"
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
                                            <optgroup label="{$optiongroup[$input.options.optiongroup.label]|escape:'htmlall':'UTF-8'}">
                                                {foreach $optiongroup[$input.options.options.query] as $option}
                                                    <option value="{$option[$input.options.options.id]|escape:'htmlall':'UTF-8'}"
                                                            {if isset($input.multiple)}
                                                                {foreach $fields_value[$input.name] as $field_value}
                                                                    {if $field_value == $option[$input.options.options.id]}selected="selected"{/if}
                                                                {/foreach}
                                                            {else}
                                                                {if $fields_value[$input.name] == $option[$input.options.options.id]}selected="selected"{/if}
                                                            {/if}
                                                    >{$option[$input.options.options.name]|escape:'htmlall':'UTF-8'}</option>
                                                {/foreach}
                                            </optgroup>
                                        {/foreach}
                                    {else}
                                        {foreach $input.options.query AS $option}
                                            {if is_object($option)}
                                                <option value="{$option->$input.options.id|escape:'htmlall':'UTF-8'}"
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
                                                >{$option->$input.options.name|escape:'htmlall':'UTF-8'}</option>
                                            {elseif $option == "-"}
                                                <option value="">-</option>
                                            {else}
                                                <option value="{$option[$input.options.id]|escape:'htmlall':'UTF-8'}"
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
                                                >{$option[$input.options.name]|escape:'htmlall':'UTF-8'}</option>

                                            {/if}
                                        {/foreach}
                                    {/if}
                                </select>
                            {/if}
                            {elseif $input.type == 'radio'}
                            {foreach $input.values as $value}
                                <div class="radio {if isset($input.class)}{$input.class|escape:'htmlall':'UTF-8'}{/if}">
                                    {strip}
                                        <label>
                                            <input type="radio"	name="{$input.name|escape:'htmlall':'UTF-8'}" id="{$value.id|escape:'htmlall':'UTF-8'}" value="{$value.value|escape:'html':'UTF-8'}"{if $fields_value[$input.name] == $value.value} checked="checked"{/if}{if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}/>
                                            {$value.label|escape:'htmlall':'UTF-8'}
                                        </label>
                                    {/strip}
                                </div>
                            {if isset($value.p) && $value.p}<p class="help-block">{$value.p nofilter} {* HTML CONTENT *}</p>{/if}
                            {/foreach}
                            {elseif $input.type == 'switch'}
                                <span class="switch prestashop-switch fixed-width-lg">
                                    {foreach $input.values as $value}
                                        <input type="radio" name="{$input.name|escape:'htmlall':'UTF-8'}"{if $value.value == 1} id="{$input.name|escape:'htmlall':'UTF-8'}_on"{else} id="{$input.name|escape:'htmlall':'UTF-8'}_off"{/if} value="{$value.value|escape:'htmlall':'UTF-8'}"{if $fields_value[$input.name] == $value.value} checked="checked"{/if}{if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}/>
                                    {strip}
                                        <label {if $value.value == 1} for="{$input.name|escape:'htmlall':'UTF-8'}_on"{else} for="{$input.name|escape:'htmlall':'UTF-8'}_off"{/if}>
                                            {if $value.value == 1}
                                                {l s='Yes' mod='lgcanonicalurls'}
                                            {else}
                                                {l s='No' mod='lgcanonicalurls'}
                                            {/if}
                                        </label>
                                    {/strip}
                                    {/foreach}
                                    <a class="slide-button btn"></a>
                                </span>
                            {elseif $input.type == 'textarea'}
                            {if isset($input.maxchar) && $input.maxchar}<div class="input-group">{/if}
                                {assign var=use_textarea_autosize value=true}
                                {if isset($input.lang) AND $input.lang}
                                {foreach $langs as $language}
                                {if $langs|count > 1}
                                    <div class="form-group translatable-field lang-{$language.id_lang|escape:'htmlall':'UTF-8'}"{if $language.id_lang != $defaultFormLanguage} style="display:none;"{/if}>
                                    <div class="col-lg-9">
                                {/if}
                                    {if isset($input.maxchar) && $input.maxchar}
                                        <span id="{if isset($input.id)}{$input.id|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}{else}{$input.name|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}{/if}_counter" class="input-group-addon">
                                            <span class="text-count-down">{$input.maxchar|intval}</span>
                                        </span>
                                    {/if}
                                    <textarea{if isset($input.readonly) && $input.readonly} readonly="readonly"{/if} name="{$input.name|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}" id="{if isset($input.id)}{$input.id|escape:'htmlall':'UTF-8'}{else}{$input.name|escape:'htmlall':'UTF-8'}{/if}_{$language.id_lang|escape:'htmlall':'UTF-8'}" class="{if isset($input.autoload_rte) && $input.autoload_rte}rte autoload_rte{else}textarea-autosize{/if}{if isset($input.class)} {$input.class|escape:'htmlall':'UTF-8'}{/if}"{if isset($input.maxlength) && $input.maxlength} maxlength="{$input.maxlength|intval}"{/if}{if isset($input.maxchar) && $input.maxchar} data-maxchar="{$input.maxchar|intval}"{/if}>{$fields_value[$input.name][$language.id_lang]|escape:'html':'UTF-8'}</textarea>
                                {if $langs|count > 1}
                                    </div>
                                    <div class="col-lg-2">
                                        <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                                            {$language.iso_code|escape:'htmlall':'UTF-8'}
                                            <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu">
                                            {foreach from=$langs item=language}
                                                <li>
                                                    <a href="javascript:hideOtherLanguage({$language.id_lang|escape:'htmlall':'UTF-8'});" tabindex="-1">{$language.name|escape:'htmlall':'UTF-8'}</a>
                                                </li>
                                            {/foreach}
                                        </ul>
                                    </div>
                                    </div>
                                {/if}
                                {/foreach}
                                {if isset($input.maxchar) && $input.maxchar}
                                    <script type="text/javascript">
                                        $(document).ready(function(){
                                            {foreach from=$langs item=language}
                                            countDown($("#{if isset($input.id)}{$input.id|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}{else}{$input.name|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}{/if}"), $("#{if isset($input.id)}{$input.id|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}{else}{$input.name|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}{/if}_counter"));
                                            {/foreach}
                                        });
                                    </script>
                                {/if}
                                {else}
                                {if isset($input.maxchar) && $input.maxchar}
                                    <span id="{if isset($input.id)}{$input.id|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}{else}{$input.name|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}{/if}_counter" class="input-group-addon">
                                        <span class="text-count-down">{$input.maxchar|intval}</span>
                                    </span>
                                {/if}
                                    <textarea{if isset($input.readonly) && $input.readonly} readonly="readonly"{/if} name="{$input.name|escape:'htmlall':'UTF-8'}" id="{if isset($input.id)}{$input.id|escape:'htmlall':'UTF-8'}{else}{$input.name|escape:'htmlall':'UTF-8'}{/if}" {if isset($input.cols)}cols="{$input.cols|escape:'htmlall':'UTF-8'}"{/if} {if isset($input.rows)}rows="{$input.rows|escape:'htmlall':'UTF-8'}"{/if} class="{if isset($input.autoload_rte) && $input.autoload_rte}rte autoload_rte{else}textarea-autosize{/if}{if isset($input.class)} {$input.class|escape:'htmlall':'UTF-8'}{/if}"{if isset($input.maxlength) && $input.maxlength} maxlength="{$input.maxlength|intval}"{/if}{if isset($input.maxchar) && $input.maxchar} data-maxchar="{$input.maxchar|intval}"{/if}>{$fields_value[$input.name]|escape:'html':'UTF-8'}</textarea>
                                {if isset($input.maxchar) && $input.maxchar}
                                    <script type="text/javascript">
                                        $(document).ready(function(){
                                            countDown($("#{if isset($input.id)}{$input.id|escape:'htmlall':'UTF-8'}{else}{$input.name|escape:'htmlall':'UTF-8'}{/if}"), $("#{if isset($input.id)}{$input.id|escape:'htmlall':'UTF-8'}{else}{$input.name|escape:'htmlall':'UTF-8'}{/if}_counter"));
                                        });
                                    </script>
                                {/if}
                                {/if}
                                {if isset($input.maxchar) && $input.maxchar}</div>{/if}
                            {elseif $input.type == 'checkbox'}
                            {if isset($input.expand)}
                                <a class="btn btn-default show_checkbox{if strtolower($input.expand.default) == 'hide'} hidden{/if}" href="#">
                                    <i class="icon-{$input.expand.show.icon|escape:'htmlall':'UTF-8'}"></i>
                                    {$input.expand.show.text|escape:'htmlall':'UTF-8'}
                                    {if isset($input.expand.print_total) && $input.expand.print_total > 0}
                                        <span class="badge">{$input.expand.print_total|escape:'htmlall':'UTF-8'}</span>
                                    {/if}
                                </a>
                                <a class="btn btn-default hide_checkbox{if strtolower($input.expand.default) == 'show'} hidden{/if}" href="#">
                                    <i class="icon-{$input.expand.hide.icon|escape:'htmlall':'UTF-8'}"></i>
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
                                        <label for="{$id_checkbox|escape:'htmlall':'UTF-8'}">
                                            <input type="checkbox" name="{$id_checkbox|escape:'htmlall':'UTF-8'}" id="{$id_checkbox|escape:'htmlall':'UTF-8'}" class="{if isset($input.class)}{$input.class|escape:'htmlall':'UTF-8'}{/if}"{if isset($value.val)} value="{$value.val|escape:'html':'UTF-8'}"{/if}{if isset($fields_value[$id_checkbox]) && $fields_value[$id_checkbox]} checked="checked"{/if} />
                                            {$value[$input.values.name]|escape:'htmlall':'UTF-8'}
                                        </label>
                                    {/strip}
                                </div>
                            {/foreach}
                            {elseif $input.type == 'change-password'}
                                <div class="row">
                                    <div class="col-lg-12">
                                        <button type="button" id="{$input.name|escape:'htmlall':'UTF-8'}-btn-change" class="btn btn-default">
                                            <i class="icon-lock"></i>
                                            {l s='Change password...' mod='lgcanonicalurls'}
                                        </button>
                                        <div id="{$input.name|escape:'htmlall':'UTF-8'}-change-container" class="form-password-change well hide">
                                            <div class="form-group">
                                                <label for="old_passwd" class="control-label col-lg-2 required">
                                                    {l s='Current password' mod='lgcanonicalurls'}
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
                                                <label for="{$input.name|escape:'htmlall':'UTF-8'}" class="required control-label col-lg-2">
                                                    <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{l s='Password should be at least 8 characters long.' mod='lgcanonicalurls'}">
                                                        {l s='New password' mod='lgcanonicalurls'}
                                                    </span>
                                                </label>
                                                <div class="col-lg-9">
                                                    <div class="input-group fixed-width-lg">
                                                        <span class="input-group-addon">
                                                            <i class="icon-key"></i>
                                                        </span>
                                                        <input type="password" id="{$input.name|escape:'htmlall':'UTF-8'}" name="{$input.name|escape:'htmlall':'UTF-8'}" class="{if isset($input.class)}{$input.class|escape:'htmlall':'UTF-8'}{/if}" value="" required="required" autocomplete="off"/>
                                                    </div>
                                                    <span id="{$input.name|escape:'htmlall':'UTF-8'}-output"></span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="{$input.name|escape:'htmlall':'UTF-8'}2" class="required control-label col-lg-2">
                                                    {l s='Confirm password' mod='lgcanonicalurls'}
                                                </label>
                                                <div class="col-lg-4">
                                                    <div class="input-group fixed-width-lg">
                                                        <span class="input-group-addon">
                                                            <i class="icon-key"></i>
                                                        </span>
                                                        <input type="password" id="{$input.name|escape:'htmlall':'UTF-8'}2" name="{$input.name|escape:'htmlall':'UTF-8'}2" class="{if isset($input.class)}{$input.class|escape:'htmlall':'UTF-8'}{/if}" value="" autocomplete="off"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-lg-10 col-lg-offset-2">
                                                    <input type="text" class="form-control fixed-width-md pull-left" id="{$input.name|escape:'htmlall':'UTF-8'}-generate-field" disabled="disabled">
                                                    <button type="button" id="{$input.name|escape:'htmlall':'UTF-8'}-generate-btn" class="btn btn-default">
                                                        <i class="icon-random"></i>
                                                        {l s='Generate password' mod='lgcanonicalurls'}
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-lg-10 col-lg-offset-2">
                                                    <p class="checkbox">
                                                        <label for="{$input.name|escape:'htmlall':'UTF-8'}-checkbox-mail">
                                                            <input name="passwd_send_email" id="{$input.name|escape:'htmlall':'UTF-8'}-checkbox-mail" type="checkbox" checked="checked">
                                                            {l s='Send me this new password by Email' mod='lgcanonicalurls'}
                                                        </label>
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <button type="button" id="{$input.name|escape:'htmlall':'UTF-8'}-cancel-btn" class="btn btn-default">
                                                        <i class="icon-remove"></i>
                                                        {l s='Cancel' mod='lgcanonicalurls'}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <script>
                                    $(function(){
                                        var $oldPwd = $('#old_passwd');
                                        var $passwordField = $('#{$input.name|escape:'htmlall':'UTF-8'}');
                                        var $output = $('#{$input.name|escape:'htmlall':'UTF-8'}-output');
                                        var $generateBtn = $('#{$input.name|escape:'htmlall':'UTF-8'}-generate-btn');
                                        var $generateField = $('#{$input.name|escape:'htmlall':'UTF-8'}-generate-field');
                                        var $cancelBtn = $('#{$input.name|escape:'htmlall':'UTF-8'}-cancel-btn');

                                        var feedback = [
                                            { badge: 'text-danger', text: '{l s='Invalid' js=1 mod='lgcanonicalurls'}' },
                                            { badge: 'text-warning', text: '{l s='Okay' js=1 mod='lgcanonicalurls'}' },
                                            { badge: 'text-success', text: '{l s='Good' js=1 mod='lgcanonicalurls'}' },
                                            { badge: 'text-success', text: '{l s='Fabulous' js=1 mod='lgcanonicalurls'}' }
                                        ];
                                        $.passy.requirements.length.min = 8;
                                        $.passy.requirements.characters = 'DIGIT';
                                        $passwordField.passy(function(strength, valid) {
                                            $output.text(feedback[strength].text);
                                            $output.removeClass('text-danger').removeClass('text-warning').removeClass('text-success');
                                            $output.addClass(feedback[strength].badge);
                                            if (valid){
                                                $output.show();
                                            }
                                            else {
                                                $output.hide();
                                            }
                                        });
                                        var $container = $('#{$input.name|escape:'htmlall':'UTF-8'}-change-container');
                                        var $changeBtn = $('#{$input.name|escape:'htmlall':'UTF-8'}-btn-change');
                                        var $confirmPwd = $('#{$input.name|escape:'htmlall':'UTF-8'}2');

                                        $changeBtn.on('click',function(){
                                            $container.removeClass('hide');
                                            $changeBtn.addClass('hide');
                                        });
                                        $generateBtn.click(function() {
                                            $generateField.passy( 'generate', 8 );
                                            var generatedPassword = $generateField.val();
                                            $passwordField.val(generatedPassword);
                                            $confirmPwd.val(generatedPassword);
                                        });
                                        $cancelBtn.on('click',function() {
                                            $container.find("input").val("");
                                            $container.addClass('hide');
                                            $changeBtn.removeClass('hide');
                                        });

                                        $.validator.addMethod('password_same', function(value, element) {
                                            return $passwordField.val() == $confirmPwd.val();
                                        }, '{l s='Invalid password confirmation' js=1 mod='lgcanonicalurls'}');

                                        $('#employee_form').validate({
                                            rules: {
                                                "email": {
                                                    email: true
                                                },
                                                "{$input.name|escape:'htmlall':'UTF-8'}" : {
                                                    minlength: 8
                                                },
                                                "{$input.name|escape:'htmlall':'UTF-8'}2": {
                                                    password_same: true
                                                },
                                                "old_passwd" : {},
                                            },
                                            // override jquery validate plugin defaults for bootstrap 3
                                            highlight: function(element) {
                                                $(element).closest('.form-group').addClass('has-error');
                                            },
                                            unhighlight: function(element) {
                                                $(element).closest('.form-group').removeClass('has-error');
                                            },
                                            errorElement: 'span',
                                            errorClass: 'help-block',
                                            errorPlacement: function(error, element) {
                                                if(element.parent('.input-group').length) {
                                                    error.insertAfter(element.parent());
                                                } else {
                                                    error.insertAfter(element);
                                                }
                                            }
                                        });
                                    });
                                </script>
                            {elseif $input.type == 'password'}
                                <div class="input-group fixed-width-lg">
                                    <span class="input-group-addon">
                                        <i class="icon-key"></i>
                                    </span>
                                    <input type="password"
                                           id="{if isset($input.id)}{$input.id|escape:'htmlall':'UTF-8'}{else}{$input.name|escape:'htmlall':'UTF-8'}{/if}"
                                           name="{$input.name|escape:'htmlall':'UTF-8'}"
                                           class="{if isset($input.class)}{$input.class|escape:'htmlall':'UTF-8'}{/if}"
                                           value=""
                                           {if isset($input.autocomplete) && !$input.autocomplete}autocomplete="off"{/if}
                                            {if isset($input.required) && $input.required } required="required" {/if} />
                                </div>

                            {elseif $input.type == 'birthday'}
                                <div class="form-group">
                                    {foreach $input.options as $key => $select}
                                        <div class="col-lg-2">
                                            <select name="{$key|escape:'htmlall':'UTF-8'}" class="fixed-width-lg{if isset($input.class)} {$input.class|escape:'htmlall':'UTF-8'}{/if}">
                                                <option value="">-</option>
                                                {if $key == 'months'}
                                                    {*
                                                        This comment is useful to the translator tools /!\ do not remove them
                                                        {l s='January' mod='lgcanonicalurls'}
                                                        {l s='February' mod='lgcanonicalurls'}
                                                        {l s='March' mod='lgcanonicalurls'}
                                                        {l s='April' mod='lgcanonicalurls'}
                                                        {l s='May' mod='lgcanonicalurls'}
                                                        {l s='June' mod='lgcanonicalurls'}
                                                        {l s='July' mod='lgcanonicalurls'}
                                                        {l s='August' mod='lgcanonicalurls'}
                                                        {l s='September' mod='lgcanonicalurls'}
                                                        {l s='October' mod='lgcanonicalurls'}
                                                        {l s='November' mod='lgcanonicalurls'}
                                                        {l s='December' mod='lgcanonicalurls'}
                                                    *}
                                                    {foreach $select as $k => $v}
                                                        <option value="{$k|escape:'htmlall':'UTF-8'}" {if $k == $fields_value[$key]}selected="selected"{/if}>{l s=$v mod='lgcanonicalurls'}</option>
                                                    {/foreach}
                                                {else}
                                                    {foreach $select as $v}
                                                        <option value="{$v|escape:'htmlall':'UTF-8'}" {if $v == $fields_value[$key]}selected="selected"{/if}>{$v|escape:'htmlall':'UTF-8'}</option>
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
                                {html_entity_decode($input.html|escape:'htmlall':'UTF-8')} {* HTML CONTENT *}
                            {elseif $input.type == 'categories'}
                                {$categories_tree|escape:'htmlall':'UTF-8'}
                            {elseif $input.type == 'file'}
                                {$input.file|escape:'htmlall':'UTF-8'}
                            {elseif $input.type == 'categories_select'}
                                {$input.category_tree|escape:'htmlall':'UTF-8'}
                            {elseif $input.type == 'asso_shop' && isset($asso_shop) && $asso_shop}
                                {$asso_shop|escape:'htmlall':'UTF-8'}
                            {elseif $input.type == 'color'}
                                <div class="form-group">
                                    <div class="col-lg-2">
                                        <div class="row">
                                            <div class="input-group">
                                                <input type="color"
                                                       data-hex="true"
                                                        {if isset($input.class)} class="{$input.class}"
                                                        {else} class="color mColorPickerInput"{/if}
                                                       name="{$input.name|escape:'htmlall':'UTF-8'}"
                                                       value="{if isset($fields_value[$input.name])}{$fields_value[$input.name]|escape:'html':'UTF-8'}{/if}" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            {elseif $input.type == 'date'}
                                <div class="row">
                                    <div class="input-group col-lg-4">
                                        <input
                                                id="{if isset($input.id)}{$input.id|escape:'htmlall':'UTF-8'}{else}{$input.name|escape:'htmlall':'UTF-8'}{/if}"
                                                type="text"
                                                data-hex="true"
                                                {if isset($input.class)} class="{$input.class}"
                                                {else}class="datepicker"{/if}
                                                name="{$input.name|escape:'htmlall':'UTF-8'}"
                                                value="{if isset($fields_value[$input.name])}{$fields_value[$input.name]|escape:'html':'UTF-8'}{/if}" />
                                        <span class="input-group-addon">
                                            <i class="icon-calendar-empty"></i>
                                        </span>
                                    </div>
                                </div>
                            {elseif $input.type == 'datetime'}
                                <div class="row">
                                    <div class="input-group col-lg-4">
                                        <input
                                                id="{if isset($input.id)}{$input.id|escape:'htmlall':'UTF-8'}{else}{$input.name|escape:'htmlall':'UTF-8'}{/if}"
                                                type="text"
                                                data-hex="true"
                                                {if isset($input.class)} class="{$input.class}"
                                                {else} class="datetimepicker"{/if}
                                                name="{$input.name|escape:'htmlall':'UTF-8'}"
                                                value="{if isset($fields_value[$input.name])}{$fields_value[$input.name]|escape:'html':'UTF-8'}{/if}" />
                                        <span class="input-group-addon">
                                            <i class="icon-calendar-empty"></i>
                                        </span>
                                    </div>
                                </div>
                            {elseif $input.type == 'free'}
                            {if isset($fields_value[$input.name])}{$fields_value[$input.name]|escape:'htmlall':'UTF-8'}{/if}
                            {elseif $input.type == 'html'}
                                {if isset($input.html_content)}
                                    {$input.html_content|escape:'htmlall':'UTF-8'}
                                {else}
                                    {$input.name|escape:'htmlall':'UTF-8'}
                                {/if}
                            {/if}
                        {/block}{* end block input *}
                        {block name="description"}
                            {if isset($input.desc) && !empty($input.desc)}
                                <p class="help-block">
                                    {if is_array($input.desc)}
                                        {foreach $input.desc as $p}
                                            {if is_array($p)}
                                                <span id="{$p.id|escape:'htmlall':'UTF-8'}">{$p.text nofilter} {* HTML CONTENT *}</span><br />
                                            {else}
                                                {$p nofilter} {* HTML CONTENT *}<br />
                                            {/if}
                                        {/foreach}
                                    {else}
                                        {$input.desc nofilter} {* HTML CONTENT *}
                                    {/if}
                                </p>
                            {/if}
                        {/block}
                    </div>
                {/block}{* end block field *}
            {/if}
        </div>
    {/block}
{/foreach}
{block name="footer"}
    {capture name='form_submit_btn'}{counter name='form_submit_btn'}{/capture}
    {if isset($submit) || isset($buttons)}
        <div class="panel-footer">
            {if isset($submit) && !empty($submit)}
                <button type="submit" value="1"	id="{if isset($submit['id'])}{$submit['id']|escape:'htmlall':'UTF-8'}{else}{$table|escape:'htmlall':'UTF-8'}_form_submit_btn{/if}{if $smarty.capture.form_submit_btn > 1}_{($smarty.capture.form_submit_btn - 1)|intval}{/if}" name="{if isset($submit['name'])}{$submit['name']|escape:'htmlall':'UTF-8'}{else}{$submit_action|escape:'htmlall':'UTF-8'}{/if}{if isset($submit['stay']) && $submit['stay']}AndStay{/if}" class="{if isset($submit['class'])}{$submit['class']|escape:'htmlall':'UTF-8'}{else}btn btn-default pull-right{/if}" {if isset($submit['disabled']) && $submit['disabled']}disabled="disabled"{/if}>
                    <i class="{if isset($submit['icon'])}{$submit['icon']|escape:'htmlall':'UTF-8'}{else}process-icon-save{/if}"></i> {$submit['title']|escape:'htmlall':'UTF-8'}
                </button>
            {/if}
            {if isset($show_cancel_button) && $show_cancel_button}
                <a href="{$back_url|escape:'html':'UTF-8'}" class="btn btn-default" onclick="window.history.back();">
                    <i class="process-icon-cancel"></i> {l s='Cancel' mod='lgcanonicalurls'}
                </a>
            {/if}
            {if isset($fieldset['form']['reset'])}
                <button
                        type="reset"
                        id="{if isset($fieldset['form']['reset']['id'])}{$fieldset['form']['reset']['id']|escape:'htmlall':'UTF-8'}{else}{$table|escape:'htmlall':'UTF-8'}_form_reset_btn{/if}"
                        class="{if isset($fieldset['form']['reset']['class'])}{$fieldset['form']['reset']['class']|escape:'htmlall':'UTF-8'}{else}btn btn-default{/if}"
                        {if isset($fieldset['form']['reset']['disabled']) && $fieldset['form']['reset']['disabled']}disabled="disabled"{/if}
                        >
                    {if isset($fieldset['form']['reset']['icon'])}<i class="{$fieldset['form']['reset']['icon']|escape:'htmlall':'UTF-8'}"></i> {/if} {$fieldset['form']['reset']['title']|escape:'htmlall':'UTF-8'}
                </button>
            {/if}
            {if isset($buttons)}
                {foreach from=$buttons item=btn key=k}
                    {if isset($btn.href) && trim($btn.href) != ''}
                        <a href="{$btn.href|escape:'htmlall':'UTF-8'}" {if isset($btn['id'])}id="{$btn['id']|escape:'htmlall':'UTF-8'}"{/if} class="btn btn-default{if isset($btn['class'])} {$btn['class']|escape:'htmlall':'UTF-8'}{/if}" {if isset($btn['disabled']) && $btn['disabled']}disabled="disabled"{/if} {if isset($btn.js) && $btn.js} onclick="{$btn.js|escape:'htmlall':'UTF-8'}"{/if}>{if isset($btn['icon'])}<i class="{$btn['icon']|escape:'htmlall':'UTF-8'}" ></i> {/if}{$btn.title|escape:'htmlall':'UTF-8'}</a>
                    {else}
                        <button type="{if isset($btn['type'])}{$btn['type']|escape:'htmlall':'UTF-8'}{else}button{/if}" {if isset($btn['id'])}id="{$btn['id']|escape:'htmlall':'UTF-8'}"{/if} class="btn btn-default{if isset($btn['class'])} {$btn['class']|escape:'htmlall':'UTF-8'}{/if}" name="{if isset($btn['name'])}{$btn['name']|escape:'htmlall':'UTF-8'}{else}submitOptions{$table|escape:'htmlall':'UTF-8'}{/if}" {if isset($btn['disabled']) && $btn['disabled']}disabled="disabled"{/if} {if isset($btn.js) && $btn.js} onclick="{$btn.js|escape:'htmlall':'UTF-8'}"{/if}>{if isset($btn['icon'])}<i class="{$btn['icon']|escape:'htmlall':'UTF-8'}" ></i> {/if}{$btn.title|escape:'htmlall':'UTF-8'}</button>
                    {/if}
                {/foreach}
            {/if}
        </div>
    {/if}
{/block}
