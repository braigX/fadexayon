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
    {if $input.type == 'hidden'}
        <input type="hidden" name="{$input.name|escape:'htmlall':'UTF-8'}" id="{$input.name|escape:'htmlall':'UTF-8'}" value="{$fields_value[$input.name]|escape:'htmlall':'UTF-8'}" />
    {else}
        {if $input.name == 'id_state'}
            <div id="contains_states" {if !$contains_states}style="display:none;"{/if}>
        {/if}

        {block name="field"}
            <div class="margin-form">
                {block name="label"}
                    {if isset($input.label)}<label>{$input.label|escape:'htmlall':'UTF-8'} </label>{/if}
                {/block}

                {block name="input"}
                {if $input.type == 'text' || $input.type == 'tags'}
                {if isset($input.lang) AND $input.lang}
                    <div class="translatable">
                        {foreach $languages as $language}
                            <div class="lang_{$language.id_lang|escape:'htmlall':'UTF-8'}" style="display:{if $language.id_lang == $defaultFormLanguage}block{else}none{/if}; float: left;">
                                {if $input.type == 'tags'}
                                {literal}
                                    <script type="text/javascript">
                                        $().ready(function () {
                                            var input_id = '{/literal}{if isset($input.id)}{$input.id|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}{else}{$input.name|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}{/if}{literal}';
                                            $('#'+input_id).tagify({delimiters: [13,44], addTagPrompt: '{/literal}{l s='Add tag' js=1}{literal}'});
                                            $('#{/literal}{$table|escape:'htmlall':'UTF-8'}{literal}_form').submit( function() {
                                                $(this).find('#'+input_id).val($('#'+input_id).tagify('serialize'));
                                            });
                                        });
                                    </script>
                                {/literal}
                                {/if}
                                {assign var='value_text' value=$fields_value[$input.name][$language.id_lang]}
                                <input type="text"
                                       name="{$input.name|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}"
                                       id="{if isset($input.id)}{$input.id|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}{else}{$input.name|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}{/if}"
                                       value="{if isset($input.string_format) && $input.string_format}{$value_text|string_format:$input.string_format|escape:'htmlall':'UTF-8'}{else}{$value_text|escape:'htmlall':'UTF-8'}{/if}"
                                       class="{if $input.type == 'tags'}tagify {/if}{if isset($input.class)}{$input.class|escape:'htmlall':'UTF-8'}{/if}"
                                       {if isset($input.size)}size="{$input.size|escape:'htmlall':'UTF-8'}"{/if}
                                        {if isset($input.maxlength)}maxlength="{$input.maxlength|escape:'htmlall':'UTF-8'}"{/if}
                                        {if isset($input.readonly) && $input.readonly}readonly="readonly"{/if}
                                        {if isset($input.disabled) && $input.disabled}disabled="disabled"{/if}
                                        {if isset($input.autocomplete) && !$input.autocomplete}autocomplete="off"{/if} />
                                {if !empty($input.hint)}<span class="hint" name="help_box">{$input.hint|escape:'htmlall':'UTF-8'}<span class="hint-pointer">&nbsp;</span></span>{/if}
                            </div>
                        {/foreach}
                    </div>
                    {else}
                {if $input.type == 'tags'}
                {literal}
                    <script type="text/javascript">
                        $().ready(function () {
                            var input_id = '{/literal}{if isset($input.id)}{$input.id|escape:'htmlall':'UTF-8'}{else}{$input.name|escape:'htmlall':'UTF-8'}{/if}{literal}';
                            $('#'+input_id).tagify({delimiters: [13,44], addTagPrompt: '{/literal}{l s='Add tag'}{literal}'});
                            $({/literal}'#{$table|escape:'htmlall':'UTF-8'}{literal}_form').submit( function() {
                                $(this).find('#'+input_id).val($('#'+input_id).tagify('serialize'));
                            });
                        });
                    </script>
                {/literal}
                {/if}
                    {assign var='value_text' value=$fields_value[$input.name]}
                <input type="text"
                       name="{$input.name|escape:'htmlall':'UTF-8'}"
                       id="{if isset($input.id)}{$input.id|escape:'htmlall':'UTF-8'}{else}{$input.name|escape:'htmlall':'UTF-8'}{/if}"
                       value="{if isset($input.string_format) && $input.string_format}{$value_text|string_format:$input.string_format|escape:'htmlall':'UTF-8'}{else}{$value_text|escape:'htmlall':'UTF-8'}{/if}"
                       class="{if $input.type == 'tags'}tagify {/if}{if isset($input.class)}{$input.class|escape:'htmlall':'UTF-8'}{/if}"
                       {if isset($input.size)}size="{$input.size|escape:'htmlall':'UTF-8'}"{/if}
                        {if isset($input.maxlength)}maxlength="{$input.maxlength|escape:'htmlall':'UTF-8'}"{/if}
                        {if isset($input.class)}class="{$input.class|escape:'htmlall':'UTF-8'}"{/if}
                        {if isset($input.readonly) && $input.readonly}readonly="readonly"{/if}
                        {if isset($input.disabled) && $input.disabled}disabled="disabled"{/if}
                        {if isset($input.autocomplete) && !$input.autocomplete}autocomplete="off"{/if} />
                    {if isset($input.suffix)}{$input.suffix|escape:'htmlall':'UTF-8'}{/if}
                {if !empty($input.hint)}<span class="hint" name="help_box">{$input.hint|escape:'htmlall':'UTF-8'}<span class="hint-pointer">&nbsp;</span></span>{/if}
                {/if}
                    {elseif $input.type == 'select'}
                {if isset($input.options.query) && !$input.options.query && isset($input.empty_message)}
                    {$input.empty_message|escape:'htmlall':'UTF-8'}
                    {$input.required = false}
                    {$input.desc = null}
                    {else}
                    <select name="{$input.name|escape:'htmlall':'UTF-8'}" class="{if isset($input.class)}{$input.class|escape:'htmlall':'UTF-8'}{/if}"
                            id="{if isset($input.id)}{$input.id|escape:'htmlall':'UTF-8'}{else}{$input.name|escape:'htmlall':'UTF-8'}{/if}"
                            {if isset($input.multiple)}multiple="multiple" {/if}
                            {if isset($input.size)}size="{$input.size|escape:'htmlall':'UTF-8'}"{/if}
                            {if isset($input.onchange)}onchange="{$input.onchange|escape:'htmlall':'UTF-8'}"{/if}>
                        {if isset($input.options.default)}
                            <option value="{$input.options.default.value|escape:'htmlall':'UTF-8'}">{$input.options.default.label|escape:'htmlall':'UTF-8'}</option>
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
                {if !empty($input.hint)}<span class="hint" name="help_box">{$input.hint|escape:'htmlall':'UTF-8'}<span class="hint-pointer">&nbsp;</span></span>{/if}
                {/if}
                    {elseif $input.type == 'radio'}
                        {foreach $input.values as $value}
                            <input type="radio" name="{$input.name|escape:'htmlall':'UTF-8'}" id="{$value.id|escape:'htmlall':'UTF-8'}" value="{$value.value|escape:'htmlall':'UTF-8'}"
                                   {if $fields_value[$input.name] == $value.value}checked="checked"{/if}
                                    {if isset($input.disabled) && $input.disabled}disabled="disabled"{/if} />
                                <label {if isset($input.class)}class="{$input.class|escape:'htmlall':'UTF-8'}"{/if} for="{$value.id|escape:'htmlall':'UTF-8'}">
                                    {if isset($input.is_bool) && $input.is_bool == true}
                                        {if $value.value == 1}
                                            <img src="../img/admin/enabled.gif" alt="{$value.label|escape:'htmlall':'UTF-8'}" title="{$value.label|escape:'htmlall':'UTF-8'}" />
                                        {else}
                                            <img src="../img/admin/disabled.gif" alt="{$value.label|escape:'htmlall':'UTF-8'}" title="{$value.label|escape:'htmlall':'UTF-8'}" />
                                        {/if}
                                    {else}
                                        {$value.label|escape:'htmlall':'UTF-8'}
                                    {/if}
                                </label>
                            {if isset($input.br) && $input.br}<br />{/if}
                            {if isset($value.p) && $value.p}<p>{$value.p|escape:'htmlall':'UTF-8'}</p>{/if}
                        {/foreach}
                    {elseif $input.type == 'textarea'}
                        {if isset($input.lang) AND $input.lang}
                            <div class="translatable">
                                {foreach $languages as $language}
                                    <div class="lang_{$language.id_lang|escape:'htmlall':'UTF-8'}" id="{$input.name|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}" style="display:{if $language.id_lang == $defaultFormLanguage}block{else}none{/if}; float: left;">
                                        <textarea cols="{$input.cols|escape:'htmlall':'UTF-8'}" rows="{$input.rows|escape:'htmlall':'UTF-8'}" name="{$input.name|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}" {if isset($input.autoload_rte) && $input.autoload_rte}class="rte autoload_rte {if isset($input.class)}{$input.class|escape:'htmlall':'UTF-8'}{/if}"{/if} >{$fields_value[$input.name][$language.id_lang]|escape:'htmlall':'UTF-8'}</textarea>
                                    </div>
                                {/foreach}
                            </div>
                        {else}
                            <textarea name="{$input.name|escape:'htmlall':'UTF-8'}" id="{if isset($input.id)}{$input.id|escape:'htmlall':'UTF-8'}{else}{$input.name|escape:'htmlall':'UTF-8'}{/if}" cols="{$input.cols|escape:'htmlall':'UTF-8'}" rows="{$input.rows|escape:'htmlall':'UTF-8'}" {if isset($input.autoload_rte) && $input.autoload_rte}class="rte autoload_rte {if isset($input.class)}{$input.class|escape:'htmlall':'UTF-8'}{/if}"{/if}>{$fields_value[$input.name]|escape:'htmlall':'UTF-8'}</textarea>
                        {/if}
                    {elseif $input.type == 'checkbox'}
                {foreach $input.values.query as $value}
                    {assign var=id_checkbox value=$input.name|cat:'_'|cat:$value[$input.values.id]}
                <input type="checkbox"
                       name="{$id_checkbox|escape:'htmlall':'UTF-8'}"
                       id="{$id_checkbox|escape:'htmlall':'UTF-8'}"
                       class="{if isset($input.class)}{$input.class|escape:'htmlall':'UTF-8'}{/if}"
                       {if isset($value.val)}value="{$value.val|escape:'htmlall':'UTF-8'}"{/if}
                        {if isset($fields_value[$id_checkbox]) && $fields_value[$id_checkbox]}checked="checked"{/if} />
                    <label for="{$id_checkbox|escape:'htmlall':'UTF-8'}" class="t"><strong>{$value[$input.values.name]|escape:'htmlall':'UTF-8'}</strong></label><br />
                {/foreach}
                    {elseif $input.type == 'file'}
                {if isset($input.display_image) && $input.display_image}
                {if isset($fields_value[$input.name].image) && $fields_value[$input.name].image}
                    <div id="image">
                        {$fields_value[$input.name].image|escape:'htmlall':'UTF-8'}
                        <p align="center">{l s='File size' mod='lgcanonicalurls'} {$fields_value[$input.name].size|escape:'htmlall':'UTF-8'}{l s='kb' mod='lgcanonicalurls'}</p>
                        <a href="{$current|escape:'htmlall':'UTF-8'}&{$identifier|escape:'htmlall':'UTF-8'}={$form_id|escape:'htmlall':'UTF-8'}&token={$token|escape:'htmlall':'UTF-8'}&deleteImage=1">
                            <img src="../img/admin/delete.gif" alt="{l s='Delete' mod='lgcanonicalurls'}" /> {l s='Delete' mod='lgcanonicalurls'}
                        </a>
                    </div><br />
                {/if}
                {/if}

                {if isset($input.lang) AND $input.lang}
                    <div class="translatable">
                        {foreach $languages as $language}
                            <div class="lang_{$language.id_lang|escape:'htmlall':'UTF-8'}" id="{$input.name|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}" style="display:{if $language.id_lang == $defaultFormLanguage}block{else}none{/if}; float: left;">
                                <input type="file" name="{$input.name|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}" {if isset($input.id)}id="{$input.id|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}"{/if} />

                            </div>
                        {/foreach}
                    </div>
                    {else}
                <input type="file" name="{$input.name|escape:'htmlall':'UTF-8'}" {if isset($input.id)}id="{$input.id|escape:'htmlall':'UTF-8'}"{/if} />
                {/if}
                {if !empty($input.hint)}<span class="hint" name="help_box">{$input.hint|escape:'htmlall':'UTF-8'}<span class="hint-pointer">&nbsp;</span></span>{/if}
                    {elseif $input.type == 'password'}
                <input type="password"
                       name="{$input.name|escape:'htmlall':'UTF-8'}"
                       size="{$input.size|escape:'htmlall':'UTF-8'}"
                       class="{if isset($input.class)}{$input.class|escape:'htmlall':'UTF-8'}{/if}"
                       value=""
                       {if isset($input.autocomplete) && !$input.autocomplete}autocomplete="off"{/if} />
                    {elseif $input.type == 'birthday'}
                {foreach $input.options as $key => $select}
                    <select name="{$key|escape:'htmlall':'UTF-8'}" class="{if isset($input.class)}{$input.class|escape:'htmlall':'UTF-8'}{/if}">
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
                {/foreach}
                    {elseif $input.type == 'group'}
                    {assign var=groups value=$input.values}
                    {include file='helpers/form/form_group.tpl'}
                    {elseif $input.type == 'shop'}
                    {html_entity_decode($input.html|escape:'htmlall':'UTF-8')}{* HTML CONTENT *}
                    {elseif $input.type == 'categories'}
                    {include file='helpers/form/form_category.tpl' categories=$input.values}
                    {elseif $input.type == 'categories_select'}
                    {$input.category_tree|escape:'htmlall':'UTF-8'}
                    {elseif $input.type == 'asso_shop' && isset($asso_shop) && $asso_shop}
                    {$asso_shop|escape:'htmlall':'UTF-8'}
                    {elseif $input.type == 'color'}
                <input type="color"
                       size="{$input.size|escape:'htmlall':'UTF-8'}"
                       data-hex="true"
                       {if isset($input.class)}class="{$input.class|escape:'htmlall':'UTF-8'}"
                       {else}class="color mColorPickerInput"{/if}
                       name="{$input.name|escape:'htmlall':'UTF-8'}"
                       value="{$fields_value[$input.name]|escape:'htmlall':'UTF-8'}" />
                    {elseif $input.type == 'date'}
                <input type="text"
                       size="{$input.size|escape:'htmlall':'UTF-8'}"
                       data-hex="true"
                       {if isset($input.class)}class="{$input.class|escape:'htmlall':'UTF-8'}"
                       {else}class="datepicker"{/if}
                       name="{$input.name|escape:'htmlall':'UTF-8'}"
                       value="{$fields_value[$input.name]|escape:'htmlall':'UTF-8'}" />
                    {elseif $input.type == 'free'}
                    {$fields_value[$input.name]|escape:'htmlall':'UTF-8'}
                {/if}
                {if isset($input.required) && $input.required && $input.type != 'radio'} <sup>*</sup>{/if}
                {/block}{* end block input *}
                {block name="description"}
                    {if isset($input.desc) && !empty($input.desc)}
                        <p class="preference_description">
                            {if is_array($input.desc)}
                                {foreach $input.desc as $p}
                                    {if is_array($p)}
                                        <span id="{$p.id|escape:'htmlall':'UTF-8'}">{$p.text|escape:'htmlall':'UTF-8'}</span><br />
                                    {else}
                                        {$p|escape:'htmlall':'UTF-8'}<br />
                                    {/if}
                                {/foreach}
                            {else}
                                {$input.desc|escape:'htmlall':'UTF-8'}
                            {/if}
                        </p>
                    {/if}
                {/block}
                {if isset($input.lang) && isset($languages)}<div class="clear"></div>{/if}
            </div>
            <div class="clear"></div>
        {/block}{* end block field *}
        {if $input.name == 'id_state'}
            </div>
        {/if}
    {/if}
{/foreach}
{block name="footer"}
    {capture name='form_submit_btn'}{counter name='form_submit_btn'}{/capture}
    {if isset($submit) || isset($buttons)}
        <div class="panel-footer">
            {if isset($submit) && !empty($submit)}
                <button type="submit" value="1" id="{if isset($submit['id'])}{$submit['id']|escape:'htmlall':'UTF-8'}{else}{$table|escape:'htmlall':'UTF-8'}_form_submit_btn{/if}{if $smarty.capture.form_submit_btn > 1}_{($smarty.capture.form_submit_btn - 1)|intval}{/if}" name="{if isset($submit['name'])}{$submit['name']|escape:'htmlall':'UTF-8'}{else}{$submit_action|escape:'htmlall':'UTF-8'}{/if}{if isset($submit['stay']) && $submit['stay']}AndStay{/if}" class="{if isset($submit['class'])}{$submit['class']|escape:'htmlall':'UTF-8'}{else}btn btn-default pull-right{/if}" {if isset($submit['disabled']) && $submit['disabled']}disabled="disabled"{/if}>
                    <i class="{if isset($submit['icon'])}{$submit['icon']|escape:'htmlall':'UTF-8'}{else}process-icon-save{/if}"></i> {$submit['title']|escape:'htmlall':'UTF-8'}
                </button>
            {/if}
            {if isset($show_cancel_button) && $show_cancel_button}
                <a href="{$back_url|escape:'html':'UTF-8'}" class="btn btn-default" onclick="window.history.back();">
                    <i class="process-icon-cancel"></i> {l s='Cancel' mod='lgcanonicalurls'}
                </a>
            {/if}
            {if isset($fieldset['form']['reset'])}
                <button type="reset" id="{if isset($fieldset['form']['reset']['id'])}{$fieldset['form']['reset']['id']|escape:'htmlall':'UTF-8'}{else}{$table|escape:'htmlall':'UTF-8'}_form_reset_btn{/if}" class="{if isset($fieldset['form']['reset']['class'])}{$fieldset['form']['reset']['class']|escape:'htmlall':'UTF-8'}{else}btn btn-default{/if}" {if isset($fieldset['form']['reset']['disabled']) && $fieldset['form']['reset']['disabled']}disabled="disabled"{/if}>
                    {if isset($fieldset['form']['reset']['icon'])}<i class="{$fieldset['form']['reset']['icon']|escape:'htmlall':'UTF-8'}"></i> {/if} {$fieldset['form']['reset']['title']|escape:'htmlall':'UTF-8'}
                </button>
            {/if}
            {if isset($buttons)}
                {foreach from=$buttons item=btn key=k}
                    {if isset($btn.href) && trim($btn.href) != ''}
                        <a href="{$btn.href|escape:'htmlall':'UTF-8'}" {if isset($btn['id'])}id="{$btn['id']|escape:'htmlall':'UTF-8'}"{/if} class="btn btn-default{if isset($btn['class'])} {$btn['class']|escape:'htmlall':'UTF-8'}{/if}" {if isset($btn['disabled']) && $btn['disabled']}disabled="disabled"{/if} {if isset($btn.js) && $btn.js} onclick="{$btn.js|escape:'htmlall':'UTF-8'}"{/if}>
                            {if isset($btn['icon'])}<i class="{$btn['icon']|escape:'htmlall':'UTF-8'}" ></i> {/if}{$btn.title|escape:'htmlall':'UTF-8'}
                        </a>
                    {else}
                        <button type="{if isset($btn['type'])}{$btn['type']|escape:'htmlall':'UTF-8'}{else}button{/if}" {if isset($btn['id'])}id="{$btn['id']|escape:'htmlall':'UTF-8'}"{/if} class="btn btn-default{if isset($btn['class'])} {$btn['class']|escape:'htmlall':'UTF-8'}{/if}" name="{if isset($btn['name'])}{$btn['name']|escape:'htmlall':'UTF-8'}{else}submitOptions{$table|escape:'htmlall':'UTF-8'}{/if}" {if isset($btn['disabled']) && $btn['disabled']}disabled="disabled"{/if} {if isset($btn.js) && $btn.js} onclick="{$btn.js|escape:'htmlall':'UTF-8'}"{/if}>
                            {if isset($btn['icon'])}<i class="{$btn['icon']|escape:'htmlall':'UTF-8'}" ></i> {/if}{$btn.title|escape:'htmlall':'UTF-8'}
                        </button>
                    {/if}
                {/foreach}
            {/if}
        </div>
    {/if}
{/block}
