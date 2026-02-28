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
{extends file="helpers/form/form.tpl"}

{block name="input"}
    {if $input.type == 'switch'}
        <span class="switch prestashop-switch fixed-width-lg">
    		{foreach $input.values as $value}
                <input type="radio" name="{$input.name|escape:'html':'UTF-8'}"{if $value.value == 1} id="{$input.name|escape:'html':'UTF-8'}_on"{else} id="{$input.name|escape:'html':'UTF-8'}_off"{/if} value="{$value.value|escape:'html':'UTF-8'}"{if $fields_value[$input.name] == $value.value} checked="checked"{/if}{if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}/>
                    {strip}
                <label {if $value.value == 1} for="{$input.name|escape:'html':'UTF-8'}_on"{else} for="{$input.name|escape:'html':'UTF-8'}_off"{/if}>
                        {$value.label|escape:'html':'UTF-8'}
                    </label>
            {/strip}
            {/foreach}
    		<a class="slide-button btn"></a>
    	</span>
    {elseif $input.type == 'checkbox'}
        {if isset($input.values.query) && $input.values.query}
            {assign var=id_checkbox value=$input.name|cat:'_'|cat:'all'}
            {assign var=checkall value=true}
            {foreach $input.values.query as $value}
                {if !(isset($fields_value[$input.name]) && is_array($fields_value[$input.name]) && $fields_value[$input.name] && in_array($value.value,$fields_value[$input.name]))}
                    {assign var=checkall value=false}
                {/if}
            {/foreach}
            <div class="checkbox_all checkbox">
                {strip}
                    <label for="{$id_checkbox|escape:'html':'UTF-8'}">
                        <input type="checkbox" name="{$input.name|escape:'html':'UTF-8'}[]" id="{$id_checkbox|escape:'html':'UTF-8'}" {if isset($value.value)} value="0"{/if}{if $checkall} checked="checked"{/if} />
                        {l s='Select/Unselect all' mod='ybc_blog'}
                    </label>
                {/strip}
            </div>
            {foreach $input.values.query as $value}
                {assign var=id_checkbox value=$input.name|cat:'_'|cat:$value[$input.values.id]|escape:'html':'UTF-8'}
                <div class="checkbox{if isset($input.expand) && strtolower($input.expand.default) == 'show'} hidden{/if}">
                    {strip}
                        <label for="{$id_checkbox|escape:'html':'UTF-8'}">
                            <input type="checkbox" name="{$input.name|escape:'html':'UTF-8'}[]" id="{$id_checkbox|escape:'html':'UTF-8'}" {if isset($value.value)} value="{$value.value|escape:'html':'UTF-8'}"{/if}{if isset($fields_value[$input.name]) && is_array($fields_value[$input.name]) && $fields_value[$input.name] && in_array($value.value,$fields_value[$input.name])} checked="checked"{/if} />
                            {$value[$input.values.name]|escape:'html':'UTF-8'}
                        </label>
                    {/strip}
                </div>
            {/foreach}
        {/if}
    {elseif $input.type == 'file_lang'}
    {if $languages|count > 1}
        <div class="form-group">
            {/if}
            {foreach from=$languages item=language}
            {if $languages|count > 1}
                <div class="translatable-field lang-{$language.id_lang|intval}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
                    {/if}
                    <div class="col-lg-9">
                        <div class="dummyfile input-group sass">
                            <input id="{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|intval}" type="file" name="{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|intval}" class="hide-file-upload" />
                            <span class="input-group-addon"><i class="ets_svg file">
                                    <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1152 512v-472q22 14 36 28l408 408q14 14 28 36h-472zm-128 32q0 40 28 68t68 28h544v1056q0 40-28 68t-68 28h-1344q-40 0-68-28t-28-68v-1600q0-40 28-68t68-28h800v544z"/></svg>
                                </i></span>
                            <input id="{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|intval}-name" type="text" class="disabled" name="filename" readonly />
                            <span class="input-group-btn">
								<button id="{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|intval}-selectbutton" type="button" name="submitAddAttachments" class="btn btn-default">
									<i class="icon-folder-open"></i> {l s='Choose a file' mod='ybc_blog'}
								</button>
							</span>
                        </div>
                        {if isset($fields_value[$input.name]) && $fields_value[$input.name] && $fields_value[$input.name][$language.id_lang]}
                            <label class="control-label col-lg-3 uploaded_image_label" style="font-style: italic;">{l s='Uploaded image: ' mod='ybc_blog'}</label>
                            <div class="col-lg-9 uploaded_img_wrapper">
                                <a  class="ybc_fancy" href="{if $input.name=='thumb'}{$image_baseurl_thumb|escape:'html':'UTF-8'}{else}{$image_baseurl|escape:'html':'UTF-8'}{/if}{$fields_value[$input.name][$language.id_lang]|escape:'html':'UTF-8'}"><img title="{l s='Click to see full size image' mod='ybc_blog'}" style="display: inline-block; max-width: 200px;" src="{if $input.name=='thumb'}{$image_baseurl_thumb|escape:'html':'UTF-8'}{else}{$image_baseurl|escape:'html':'UTF-8'}{/if}{$fields_value[$input.name][$language.id_lang]|escape:'html':'UTF-8'}" /></a>
                                {if $input.name=='thumb' &&  isset($thumb_del_link) && $thumb_del_link && !(isset($input.required) && $input.required)}
                                    <a class="delete_url"  style="display: inline-block; text-decoration: none!important;" href="{$thumb_del_link|escape:'html':'UTF-8'}&id_lang={$language.id_lang|intval}"><span style="color: #666"><i style="font-size: 20px;" class="process-icon-delete"></i></span></a>
                                {/if}
                                {if $input.name=='image' &&  isset($img_del_link) && $img_del_link && !(isset($input.required) && $input.required)}
                                    <a class="delete_url"  style="display: inline-block; text-decoration: none!important;" href="{$img_del_link|escape:'html':'UTF-8'}&id_lang={$language.id_lang|intval}"><span style="color: #666"><i style="font-size: 20px;" class="process-icon-delete"></i></span></a>
                                {/if}
                            </div>
                        {/if}
                    </div>
                    {if $languages|count > 1}
                        <div class="col-lg-2">
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
                    {/if}
                    {if $languages|count > 1}
                </div>
            {/if}
                <script>
                    $(document).ready(function(){
                        $("#{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|intval}-selectbutton").click(function(e){
                            $("#{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|intval}").trigger('click');
                        });
                        $("#{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|intval}").change(function(e){
                            var val = $(this).val();
                            var file = val.split(/[\\/]/);
                            $("#{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|intval}-name").val(file[file.length-1]);
                        });
                    });
                </script>
            {/foreach}
            {if $languages|count > 1}
        </div>
    {/if}
    {elseif $input.type=='text'}
        {if isset($YBC_BLOG_ENABLED_GPT) && $YBC_BLOG_ENABLED_GPT && ($input.name=='meta_title' || $input.name=='title' || $input.name=='meta_description')}
            <div class="form-group">
                {foreach $languages as $language}
                    {if isset($fields_value[$input.name][$language.id_lang])}
                        {assign var='value_text' value=$fields_value[$input.name][$language.id_lang]}
                    {else}
                        {assign var='value_text' value=''}
                    {/if}
                    {if $languages|count > 1}
                        <div class="translatable-field enable_gpt_option lang-{$language.id_lang|intval}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
                    {/if}
                    <div class="col-lg-9 enable_gpt_option">
                        {if isset($input.maxchar) || isset($input.prefix) || isset($input.suffix)}
                        <div class="input-group{if isset($input.class)} {$input.class|escape:'html':'UTF-8'}{/if}">
                            {/if}
                            {if isset($input.maxchar) && $input.maxchar}
                                <span id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}_{$language.id_lang|intval}{else}{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|intval}{/if}_counter" class="input-group-addon">
                                                    <span class="text-count-down">{$input.maxchar|intval}</span>
                                                </span>
                            {/if}
                            {if isset($input.prefix)}
                                <span class="input-group-addon">
                                                      {$input.prefix|escape:'html':'UTF-8'}
                                                    </span>
                            {/if}
                            <input type="text"
                                   id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}_{$language.id_lang|intval}{else}{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|intval}{/if}"
                                   name="{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|intval}"
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
                    </div>
                    <div class="col-lg-2 enable_gpt_option">
                        {if $languages|count > 1}
                        <div class="form-group">
                            <button type="button" class="btn btn-default dropdown-toggle blog_svg" tabindex="-1" data-toggle="dropdown">
                                {$language.iso_code|escape:'html':'UTF-8'}
                                <i class="ets_svg caret-down"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1408 704q0 26-19 45l-448 448q-19 19-45 19t-45-19l-448-448q-19-19-19-45t19-45 45-19h896q26 0 45 19t19 45z"/></svg>
                                </i>
                            </button>
                            <ul class="dropdown-menu">
                                {foreach from=$languages item=language}
                                    <li><a href="javascript:hideOtherLanguage({$language.id_lang|intval});" tabindex="-1">{$language.name|escape:'html':'UTF-8'}</a></li>
                                {/foreach}
                            </ul>
                            {/if}
                            <button type="button" class="btn-open-chatgpt" title="{l s='ChatGPT' mod='ybc_blog'}" data-name="{$input.name|escape:'html':'UTF-8'}"><svg width="41" height="41" viewBox="0 0 41 41" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M37.5324 16.8707C37.9808 15.5241 38.1363 14.0974 37.9886 12.6859C37.8409 11.2744 37.3934 9.91076 36.676 8.68622C35.6126 6.83404 33.9882 5.3676 32.0373 4.4985C30.0864 3.62941 27.9098 3.40259 25.8215 3.85078C24.8796 2.7893 23.7219 1.94125 22.4257 1.36341C21.1295 0.785575 19.7249 0.491269 18.3058 0.500197C16.1708 0.495044 14.0893 1.16803 12.3614 2.42214C10.6335 3.67624 9.34853 5.44666 8.6917 7.47815C7.30085 7.76286 5.98686 8.3414 4.8377 9.17505C3.68854 10.0087 2.73073 11.0782 2.02839 12.312C0.956464 14.1591 0.498905 16.2988 0.721698 18.4228C0.944492 20.5467 1.83612 22.5449 3.268 24.1293C2.81966 25.4759 2.66413 26.9026 2.81182 28.3141C2.95951 29.7256 3.40701 31.0892 4.12437 32.3138C5.18791 34.1659 6.8123 35.6322 8.76321 36.5013C10.7141 37.3704 12.8907 37.5973 14.9789 37.1492C15.9208 38.2107 17.0786 39.0587 18.3747 39.6366C19.6709 40.2144 21.0755 40.5087 22.4946 40.4998C24.6307 40.5054 26.7133 39.8321 28.4418 38.5772C30.1704 37.3223 31.4556 35.5506 32.1119 33.5179C33.5027 33.2332 34.8167 32.6547 35.9659 31.821C37.115 30.9874 38.0728 29.9178 38.7752 28.684C39.8458 26.8371 40.3023 24.6979 40.0789 22.5748C39.8556 20.4517 38.9639 18.4544 37.5324 16.8707ZM22.4978 37.8849C20.7443 37.8874 19.0459 37.2733 17.6994 36.1501C17.7601 36.117 17.8666 36.0586 17.936 36.0161L25.9004 31.4156C26.1003 31.3019 26.2663 31.137 26.3813 30.9378C26.4964 30.7386 26.5563 30.5124 26.5549 30.2825V19.0542L29.9213 20.998C29.9389 21.0068 29.9541 21.0198 29.9656 21.0359C29.977 21.052 29.9842 21.0707 29.9867 21.0902V30.3889C29.9842 32.375 29.1946 34.2791 27.7909 35.6841C26.3872 37.0892 24.4838 37.8806 22.4978 37.8849ZM6.39227 31.0064C5.51397 29.4888 5.19742 27.7107 5.49804 25.9832C5.55718 26.0187 5.66048 26.0818 5.73461 26.1244L13.699 30.7248C13.8975 30.8408 14.1233 30.902 14.3532 30.902C14.583 30.902 14.8088 30.8408 15.0073 30.7248L24.731 25.1103V28.9979C24.7321 29.0177 24.7283 29.0376 24.7199 29.0556C24.7115 29.0736 24.6988 29.0893 24.6829 29.1012L16.6317 33.7497C14.9096 34.7416 12.8643 35.0097 10.9447 34.4954C9.02506 33.9811 7.38785 32.7263 6.39227 31.0064ZM4.29707 13.6194C5.17156 12.0998 6.55279 10.9364 8.19885 10.3327C8.19885 10.4013 8.19491 10.5228 8.19491 10.6071V19.808C8.19351 20.0378 8.25334 20.2638 8.36823 20.4629C8.48312 20.6619 8.64893 20.8267 8.84863 20.9404L18.5723 26.5542L15.206 28.4979C15.1894 28.5089 15.1703 28.5155 15.1505 28.5173C15.1307 28.5191 15.1107 28.516 15.0924 28.5082L7.04046 23.8557C5.32135 22.8601 4.06716 21.2235 3.55289 19.3046C3.03862 17.3858 3.30624 15.3413 4.29707 13.6194ZM31.955 20.0556L22.2312 14.4411L25.5976 12.4981C25.6142 12.4872 25.6333 12.4805 25.6531 12.4787C25.6729 12.4769 25.6928 12.4801 25.7111 12.4879L33.7631 17.1364C34.9967 17.849 36.0017 18.8982 36.6606 20.1613C37.3194 21.4244 37.6047 22.849 37.4832 24.2684C37.3617 25.6878 36.8382 27.0432 35.9743 28.1759C35.1103 29.3086 33.9415 30.1717 32.6047 30.6641C32.6047 30.5947 32.6047 30.4733 32.6047 30.3889V21.188C32.6066 20.9586 32.5474 20.7328 32.4332 20.5338C32.319 20.3348 32.154 20.1698 31.955 20.0556ZM35.3055 15.0128C35.2464 14.9765 35.1431 14.9142 35.069 14.8717L27.1045 10.2712C26.906 10.1554 26.6803 10.0943 26.4504 10.0943C26.2206 10.0943 25.9948 10.1554 25.7963 10.2712L16.0726 15.8858V11.9982C16.0715 11.9783 16.0753 11.9585 16.0837 11.9405C16.0921 11.9225 16.1048 11.9068 16.1207 11.8949L24.1719 7.25025C25.4053 6.53903 26.8158 6.19376 28.2383 6.25482C29.6608 6.31589 31.0364 6.78077 32.2044 7.59508C33.3723 8.40939 34.2842 9.53945 34.8334 10.8531C35.3826 12.1667 35.5464 13.6095 35.3055 15.0128ZM14.2424 21.9419L10.8752 19.9981C10.8576 19.9893 10.8423 19.9763 10.8309 19.9602C10.8195 19.9441 10.8122 19.9254 10.8098 19.9058V10.6071C10.8107 9.18295 11.2173 7.78848 11.9819 6.58696C12.7466 5.38544 13.8377 4.42659 15.1275 3.82264C16.4173 3.21869 17.8524 2.99464 19.2649 3.1767C20.6775 3.35876 22.0089 3.93941 23.1034 4.85067C23.0427 4.88379 22.937 4.94215 22.8668 4.98473L14.9024 9.58517C14.7025 9.69878 14.5366 9.86356 14.4215 10.0626C14.3065 10.2616 14.2466 10.4877 14.2479 10.7175L14.2424 21.9419ZM16.071 17.9991L20.4018 15.4978L24.7325 17.9975V22.9985L20.4018 25.4983L16.071 22.9985V17.9991Z" fill="currentColor"></path></svg></button>
                            {if $languages|count > 1}
                        </div>
                        {/if}

                    </div>
                    {if $languages|count > 1}
                        </div>
                    {/if}
                {/foreach}
                {if isset($input.maxchar) && $input.maxchar}
                    <script type="text/javascript">
                        $(document).ready(function(){
                            {foreach from=$languages item=language}
                            countDown($("#{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}_{$language.id_lang|intval}{else}{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|intval}{/if}"), $("#{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}_{$language.id_lang|intval}{else}{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|intval}{/if}_counter"));
                            {/foreach}
                        });
                    </script>
                {/if}
            </div>
        {else}
            {$smarty.block.parent}
        {/if}
    {elseif isset($YBC_BLOG_ENABLED_GPT) && $YBC_BLOG_ENABLED_GPT && $input.type=='textarea' && ($input.name=='short_description' || $input.name=='description' || $input.name=='meta_description')}
        {foreach $languages as $language}
            {if $languages|count > 1}
                <div class="form-group enable_gpt_option translatable-field lang-{$language.id_lang|intval}"{if $language.id_lang != $defaultFormLanguage} style="display:none;"{/if}>
            {/if}
            <div class="col-lg-9 enable_gpt_option">
                {if isset($input.maxchar) && $input.maxchar}
                <div class="input-group">
            <span id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}_{$language.id_lang|intval}{else}{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|intval}{/if}_counter" class="input-group-addon">
															<span class="text-count-down">{$input.maxchar|intval}</span>
														</span>
                    {/if}
                    <textarea{if isset($input.readonly) && $input.readonly} readonly="readonly"{/if} name="{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}" id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}_{$language.id_lang|intval}" class="{if isset($input.autoload_rte) && $input.autoload_rte}rte autoload_rte{else}textarea-autosize{/if}{if isset($input.class)} {$input.class|escape:'html':'UTF-8'}{/if}"{if isset($input.maxlength) && $input.maxlength} maxlength="{$input.maxlength|intval}"{/if}{if isset($input.maxchar) && $input.maxchar} data-maxchar="{$input.maxchar|intval}"{/if}>{$fields_value[$input.name][$language.id_lang]|escape:'html':'UTF-8'}</textarea>
                    {if isset($input.maxchar) && $input.maxchar}
                </div>
                {/if}

            </div>
            <div class="col-lg-2 enable_gpt_option">
                {if $languages|count > 1}
                    <button type="button" class="btn btn-default dropdown-toggle blog_svg" tabindex="-1" data-toggle="dropdown">
                        {$language.iso_code|escape:'html':'UTF-8'}
                        <i class="ets_svg caret-down"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1408 704q0 26-19 45l-448 448q-19 19-45 19t-45-19l-448-448q-19-19-19-45t19-45 45-19h896q26 0 45 19t19 45z"/></svg>
                        </i>
                    </button>
                    <ul class="dropdown-menu">
                        {foreach from=$languages item=language}
                            <li><a href="javascript:hideOtherLanguage({$language.id_lang|intval});" tabindex="-1">{$language.name|escape:'html':'UTF-8'}</a></li>
                        {/foreach}
                    </ul>
                {/if}
                <button type="button" class="btn-open-chatgpt" title="{l s='ChatGPT' mod='ybc_blog'}" data-name="{$input.name|escape:'html':'UTF-8'}"><svg width="41" height="41" viewBox="0 0 41 41" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M37.5324 16.8707C37.9808 15.5241 38.1363 14.0974 37.9886 12.6859C37.8409 11.2744 37.3934 9.91076 36.676 8.68622C35.6126 6.83404 33.9882 5.3676 32.0373 4.4985C30.0864 3.62941 27.9098 3.40259 25.8215 3.85078C24.8796 2.7893 23.7219 1.94125 22.4257 1.36341C21.1295 0.785575 19.7249 0.491269 18.3058 0.500197C16.1708 0.495044 14.0893 1.16803 12.3614 2.42214C10.6335 3.67624 9.34853 5.44666 8.6917 7.47815C7.30085 7.76286 5.98686 8.3414 4.8377 9.17505C3.68854 10.0087 2.73073 11.0782 2.02839 12.312C0.956464 14.1591 0.498905 16.2988 0.721698 18.4228C0.944492 20.5467 1.83612 22.5449 3.268 24.1293C2.81966 25.4759 2.66413 26.9026 2.81182 28.3141C2.95951 29.7256 3.40701 31.0892 4.12437 32.3138C5.18791 34.1659 6.8123 35.6322 8.76321 36.5013C10.7141 37.3704 12.8907 37.5973 14.9789 37.1492C15.9208 38.2107 17.0786 39.0587 18.3747 39.6366C19.6709 40.2144 21.0755 40.5087 22.4946 40.4998C24.6307 40.5054 26.7133 39.8321 28.4418 38.5772C30.1704 37.3223 31.4556 35.5506 32.1119 33.5179C33.5027 33.2332 34.8167 32.6547 35.9659 31.821C37.115 30.9874 38.0728 29.9178 38.7752 28.684C39.8458 26.8371 40.3023 24.6979 40.0789 22.5748C39.8556 20.4517 38.9639 18.4544 37.5324 16.8707ZM22.4978 37.8849C20.7443 37.8874 19.0459 37.2733 17.6994 36.1501C17.7601 36.117 17.8666 36.0586 17.936 36.0161L25.9004 31.4156C26.1003 31.3019 26.2663 31.137 26.3813 30.9378C26.4964 30.7386 26.5563 30.5124 26.5549 30.2825V19.0542L29.9213 20.998C29.9389 21.0068 29.9541 21.0198 29.9656 21.0359C29.977 21.052 29.9842 21.0707 29.9867 21.0902V30.3889C29.9842 32.375 29.1946 34.2791 27.7909 35.6841C26.3872 37.0892 24.4838 37.8806 22.4978 37.8849ZM6.39227 31.0064C5.51397 29.4888 5.19742 27.7107 5.49804 25.9832C5.55718 26.0187 5.66048 26.0818 5.73461 26.1244L13.699 30.7248C13.8975 30.8408 14.1233 30.902 14.3532 30.902C14.583 30.902 14.8088 30.8408 15.0073 30.7248L24.731 25.1103V28.9979C24.7321 29.0177 24.7283 29.0376 24.7199 29.0556C24.7115 29.0736 24.6988 29.0893 24.6829 29.1012L16.6317 33.7497C14.9096 34.7416 12.8643 35.0097 10.9447 34.4954C9.02506 33.9811 7.38785 32.7263 6.39227 31.0064ZM4.29707 13.6194C5.17156 12.0998 6.55279 10.9364 8.19885 10.3327C8.19885 10.4013 8.19491 10.5228 8.19491 10.6071V19.808C8.19351 20.0378 8.25334 20.2638 8.36823 20.4629C8.48312 20.6619 8.64893 20.8267 8.84863 20.9404L18.5723 26.5542L15.206 28.4979C15.1894 28.5089 15.1703 28.5155 15.1505 28.5173C15.1307 28.5191 15.1107 28.516 15.0924 28.5082L7.04046 23.8557C5.32135 22.8601 4.06716 21.2235 3.55289 19.3046C3.03862 17.3858 3.30624 15.3413 4.29707 13.6194ZM31.955 20.0556L22.2312 14.4411L25.5976 12.4981C25.6142 12.4872 25.6333 12.4805 25.6531 12.4787C25.6729 12.4769 25.6928 12.4801 25.7111 12.4879L33.7631 17.1364C34.9967 17.849 36.0017 18.8982 36.6606 20.1613C37.3194 21.4244 37.6047 22.849 37.4832 24.2684C37.3617 25.6878 36.8382 27.0432 35.9743 28.1759C35.1103 29.3086 33.9415 30.1717 32.6047 30.6641C32.6047 30.5947 32.6047 30.4733 32.6047 30.3889V21.188C32.6066 20.9586 32.5474 20.7328 32.4332 20.5338C32.319 20.3348 32.154 20.1698 31.955 20.0556ZM35.3055 15.0128C35.2464 14.9765 35.1431 14.9142 35.069 14.8717L27.1045 10.2712C26.906 10.1554 26.6803 10.0943 26.4504 10.0943C26.2206 10.0943 25.9948 10.1554 25.7963 10.2712L16.0726 15.8858V11.9982C16.0715 11.9783 16.0753 11.9585 16.0837 11.9405C16.0921 11.9225 16.1048 11.9068 16.1207 11.8949L24.1719 7.25025C25.4053 6.53903 26.8158 6.19376 28.2383 6.25482C29.6608 6.31589 31.0364 6.78077 32.2044 7.59508C33.3723 8.40939 34.2842 9.53945 34.8334 10.8531C35.3826 12.1667 35.5464 13.6095 35.3055 15.0128ZM14.2424 21.9419L10.8752 19.9981C10.8576 19.9893 10.8423 19.9763 10.8309 19.9602C10.8195 19.9441 10.8122 19.9254 10.8098 19.9058V10.6071C10.8107 9.18295 11.2173 7.78848 11.9819 6.58696C12.7466 5.38544 13.8377 4.42659 15.1275 3.82264C16.4173 3.21869 17.8524 2.99464 19.2649 3.1767C20.6775 3.35876 22.0089 3.93941 23.1034 4.85067C23.0427 4.88379 22.937 4.94215 22.8668 4.98473L14.9024 9.58517C14.7025 9.69878 14.5366 9.86356 14.4215 10.0626C14.3065 10.2616 14.2466 10.4877 14.2479 10.7175L14.2424 21.9419ZM16.071 17.9991L20.4018 15.4978L24.7325 17.9975V22.9985L20.4018 25.4983L16.071 22.9985V17.9991Z" fill="currentColor"></path></svg></button>

            </div>
            {if $languages|count > 1}
                </div>
            {/if}
        {/foreach}
        {if isset($input.maxchar) && $input.maxchar}
            <script type="text/javascript">
                $(document).ready(function(){
                    {foreach from=$languages item=language}
                    countDown($("#{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}_{$language.id_lang|intval}{else}{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|intval}{/if}"), $("#{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}_{$language.id_lang|intval}{else}{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|intval}{/if}_counter"));
                    {/foreach}
                });
            </script>
        {/if}
    {else}
        {$smarty.block.parent}
    {/if}
{/block}
{block name="field"}
    {if $input.type!='categories2' && $input.type!='exclude_products' &&  $input.type!='select_category' &&  $input.type != 'profile_employee' && $input.type != 'blog_categories' && $input.type != 'products_search' && $input.name !='url_alias'}
        {$smarty.block.parent}
        {if $input.type == 'file' && (!isset($input.imageType) || isset($input.imageType) && $input.imageType!='thumb')&&  isset($display_img) && $display_img}
            <label class="control-label col-lg-3 uploaded_image_label" style="font-style: italic;">{l s='Uploaded image: ' mod='ybc_blog'}</label>
            <div class="col-lg-9 uploaded_img_wrapper">
                <a  class="ybc_fancy" href="{$display_img|escape:'html':'UTF-8'}"><img title="{l s='Click to see full size image' mod='ybc_blog'}" style="display: inline-block; max-width: 200px;" src="{$display_img|escape:'html':'UTF-8'}" /></a>
                {if isset($img_del_link) && $img_del_link && !(isset($input.required) && $input.required)}
                    <a class="delete_url" style="display: inline-block; text-decoration: none!important;" href="{$img_del_link|escape:'html':'UTF-8'}"><span style="color: #666"><i style="font-size: 20px;" class="process-icon-delete"></i></span></a>
                {/if}
            </div>
        {elseif $input.type == 'file' && isset($input.imageType) && $input.imageType=='thumb' &&  isset($display_thumb) && $display_thumb}
            <label class="control-label col-lg-3 uploaded_image_label" style="font-style: italic;">{l s='Uploaded image: ' mod='ybc_blog'}</label>
            <div class="col-lg-9 uploaded_img_wrapper">
                <a  class="ybc_fancy" href="{$display_thumb|escape:'html':'UTF-8'}"><img title="{l s='Click to see full size image' mod='ybc_blog'}" style="display: inline-block; max-width: 200px;" src="{$display_thumb|escape:'html':'UTF-8'}" /></a>
                {if isset($thumb_del_link) && $thumb_del_link && !(isset($input.required) && $input.required)}
                    <a class="delete_url"  style="display: inline-block; text-decoration: none!important;" href="{$thumb_del_link|escape:'html':'UTF-8'}"><span style="color: #666"><i style="font-size: 20px;" class="process-icon-delete"></i></span></a>
                {/if}
            </div>
        {/if}
        {if $input.name=='YBC_BLOG_ENABLE_RSS'}
            <span class="link_rss rss_setting">
                <label class="link-rss-label">{l s='RSS feed URLs' mod='ybc_blog'}:</label>
                <span class="link-rss-lang">
                    {foreach from=$urls_rss item='url_rss'}
                        <span>
                            <img src="{$url_rss.img|escape:'html':'UTF-8'}"/><a href="{$url_rss.link|escape:'html':'UTF-8'}" target="_blank">&nbsp;{$url_rss.link|escape:'html':'UTF-8'}</a>
                        </span>
                    {/foreach}
                </span>
            </span>
        {/if}
        {if $input.name=='YBC_BLOG_ENABLE_SITEMAP'}
            <span class="link_sitemap sitemap_setting">
                <label class="link-sitemap-label">{l s='Sitemap URLs' mod='ybc_blog'}</label>
                <br />
                <span class="main-sitemap">{l s='Main sitemap' mod='ybc_blog'}:&nbsp;<a href="{$url_sitemap|escape:'html':'UTF-8'}" target="_blank">{$url_sitemap|escape:'html':'UTF-8'}</a></span>
                {if $urls_sitemap}
                    <span class="link-sitemap-lang">
                        {foreach from=$urls_sitemap item='url_sitemap'}
                            <span>
                                <img src="{$url_sitemap.img|escape:'html':'UTF-8'}"/><a href="{$url_sitemap.link|escape:'html':'UTF-8'}" target="_blank">&nbsp;{$url_sitemap.link|escape:'html':'UTF-8'}</a><br />
                            </span>
                        {/foreach}
                    </span>
                {/if}
            </span>
        {/if}
    {else}
        {if $input.type=='select_category'}
            <div class="col-lg-9">
                <select id="{$input.name|escape:'html':'UTF-8'}" class=" fixed-width-xl" name="{$input.name|escape:'html':'UTF-8'}">
                    {$input.blogCategoryotpionsHtml nofilter}
                </select>
            </div>
        {elseif $input.type == 'categories2'}
            <div class="col-lg-8">
                {$categories_tree2 nofilter}
            </div>
        {elseif $input.type == 'blog_categories'}
            <div class="col-lg-9">
                <ul style="float: left; padding: 0; margin-top: 5px;">
                    {$input.html_content nofilter}
                </ul>
                {if isset($input.desc) && $input.desc}
                    <p class="help-block">{$input.desc|escape:'html':'UTF-8'} </p>
                {/if}
            </div>
        {elseif $input.type == 'profile_employee'}
            <div class="col-lg-9">
                <ul style="float: left; padding: 0; margin-top: 5px">
                    {if $input.profiles}
                        {foreach from=$input.profiles item='profile'}
                            {if $profile.title}
                                <li style="list-style: none;"><input {if in_array($profile.id, $input.selected_profile)} checked="checked" {/if} style="margin: 2px 7px 0 5px; float: left;" type="checkbox" value="{$profile.id|escape:'html':'UTF-8'}" name="profile_employee[]" id="ybc_input_profile_employee_{str_replace(' ','_',$profile.id)|escape:'html':'UTF-8'}" /><label for="ybc_input_profile_employee_{$profile.id|escape:'html':'UTF-8'}">{$profile.title|escape:'html':'UTF-8'}</label></li>
                            {/if}
                        {/foreach}
                    {/if}
                </ul>
            </div>
        {elseif $input.name == "url_alias"}
            <script type="text/javascript">
                {if isset($PS_ALLOW_ACCENTED_CHARS_URL) && $PS_ALLOW_ACCENTED_CHARS_URL}
                var PS_ALLOW_ACCENTED_CHARS_URL = 1;
                {else}
                var PS_ALLOW_ACCENTED_CHARS_URL = 0;
                {/if}
            </script>
            {$smarty.block.parent}
        {elseif $input.type == 'products_search'}
            <div class="col-lg-9">
                <div id="ajax_choose_product">
                    <input type="hidden" name="inputAccessories" id="inputAccessories" value="{if $input.selected_products}{foreach from=$input.selected_products item=accessory}{$accessory.id_product|intval}-{/foreach}{/if}" />
                    <input type="hidden" name="nameAccessories" id="nameAccessories" value="{if $input.selected_products}{foreach from=$input.selected_products item=accessory}{$accessory.name|escape:'html':'UTF-8'}¤{/foreach}{/if}" />

                    <div class="input-group">
                        <input type="text" id="product_autocomplete_input" name="product_autocomplete_input" placeholder="{l s='Search product by name, reference or ID' mod='ybc_blog'}" />
                        <span class="input-group-addon"><i class="ets_svg search">
                                <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1216 832q0-185-131.5-316.5t-316.5-131.5-316.5 131.5-131.5 316.5 131.5 316.5 316.5 131.5 316.5-131.5 131.5-316.5zm512 832q0 52-38 90t-90 38q-54 0-90-38l-343-342q-179 124-399 124-143 0-273.5-55.5t-225-150-150-225-55.5-273.5 55.5-273.5 150-225 225-150 273.5-55.5 273.5 55.5 225 150 150 225 55.5 273.5q0 220-124 399l343 343q37 37 37 90z"/></svg>
                            </i></span>
                    </div>
                    <div id="divAccessories">
                        {if $input.selected_products}
                            {foreach from=$input.selected_products item=accessory}
                                <div class="form-control-static form-control-static_{$accessory.id_product|intval}">
                                    <button type="button" class="btn btn-default remove_button" onclick="ybcDelAccessory({$accessory.id_product|intval});" name="{$accessory.id_product|intval}">
                                        <i class="icon-remove text-danger"></i>
                                    </button>
                                    <img src="{$accessory.link_image|escape:'html':'UTF-8'}" style="width:32px;" />
                                    <a href="{$accessory.link|escape:'html':'UTF-8'}" target="_blank">
                                        {$accessory.name|escape:'html':'UTF-8'}{if !empty($accessory.reference)}{$accessory.reference|escape:'html':'UTF-8'}{/if}
                                    </a>
                                </div>
                            {/foreach}
                        {/if}
                    </div>
                </div>
            </div>
        {elseif $input.type=='exclude_products'}
            <div class="col-lg-9">
                <div id="ajax_choose_product">
                    <input type="hidden" name="exclude_products" id="exclude_products" value="{if $input.selected_products}{foreach from=$input.selected_products item=accessory}{$accessory.id_product|intval}-{/foreach}{/if}" />
                    <input type="hidden" name="name_exclude_products" id="name_exclude_products" value="{if $input.selected_products}{foreach from=$input.selected_products item=accessory}{$accessory.name|escape:'html':'UTF-8'}¤{/foreach}{/if}" />
                    <div class="input-group">
                        <input type="text" id="exclude_product_autocomplete_input" name="exclude_product_autocomplete_input" placeholder="{l s='Search product by name, reference or ID' mod='ybc_blog'}" />
                        <span class="input-group-addon"><i class="ets_svg search">
                                <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1216 832q0-185-131.5-316.5t-316.5-131.5-316.5 131.5-131.5 316.5 131.5 316.5 316.5 131.5 316.5-131.5 131.5-316.5zm512 832q0 52-38 90t-90 38q-54 0-90-38l-343-342q-179 124-399 124-143 0-273.5-55.5t-225-150-150-225-55.5-273.5 55.5-273.5 150-225 225-150 273.5-55.5 273.5 55.5 225 150 150 225 55.5 273.5q0 220-124 399l343 343q37 37 37 90z"/></svg>
                            </i></span>
                    </div>
                    <div id="divexcludeproduct">
                        {if $input.selected_products}
                            {foreach from=$input.selected_products item=accessory}
                                <div class="form-control-static form-control-static_{$accessory.id_product|intval}">
                                    <button type="button" class="btn btn-default remove_button" onclick="ybcDelExcludeProduct({$accessory.id_product|intval});" name="{$accessory.id_product|intval}">
                                        <i class="icon-remove text-danger"></i>
                                    </button>
                                    <img src="{$accessory.link_image|escape:'html':'UTF-8'}" style="width:32px;" />
                                    <a href="{$accessory.link|escape:'html':'UTF-8'}" target="_blank">
                                        {$accessory.name|escape:'html':'UTF-8'}{if !empty($accessory.reference)}{$accessory.reference|escape:'html':'UTF-8'}{/if}
                                    </a>
                                </div>
                            {/foreach}
                        {/if}
                    </div>
                </div>
            </div>
        {/if}
    {/if}
{/block}

{block name="footer"}
    {capture name='form_submit_btn'}{counter name='form_submit_btn'}{/capture}
    {if isset($fieldset['form']['submit']) || isset($fieldset['form']['buttons'])}
        <div class="panel-footer">
            {if isset($cancel_url) && $cancel_url}
                <a class="btn btn-default cancel_url" href="{$cancel_url|escape:'html':'UTF-8'}"><i class="process-icon-cancel"></i>{l s='Back' mod='ybc_blog'}</a>
            {/if}
            {if isset($cancel_popup) && $cancel_popup}
                <a class="btn btn-default cancel_popup" href="{$cancel_popup|escape:'html':'UTF-8'}"><i class="process-icon-cancel"></i>{l s='Cancel' mod='ybc_blog'}</a>
            {/if}
            {if isset($fieldset['form']['submit']) && !empty($fieldset['form']['submit'])}
                <div class="img_loading_wrapper">
                    {*
                    <img src="{$image_baseurl|escape:'html':'UTF-8'}img/loading-admin.gif" title="{l s='Loading' mod='ybc_blog'}" class="ybc_blog_loading" />
                    *}
                </div>
                <button type="submit" value="1"	id="{if isset($fieldset['form']['submit']['id'])}{$fieldset['form']['submit']['id']|escape:'html':'UTF-8'}{else}{$table|escape:'html':'UTF-8'}_form_submit_btn{/if}{if $smarty.capture.form_submit_btn > 1}_{($smarty.capture.form_submit_btn - 1)|intval}{/if}" name="{if isset($fieldset['form']['submit']['name'])}{$fieldset['form']['submit']['name']|escape:'html':'UTF-8'}{else}{$submit_action|escape:'html':'UTF-8'}{/if}{if isset($fieldset['form']['submit']['stay']) && $fieldset['form']['submit']['stay']}AndStay{/if}" class="{if isset($fieldset['form']['submit']['class'])}{$fieldset['form']['submit']['class']|escape:'html':'UTF-8'}{else}btn btn-default pull-right{/if}">
                    <i class="{if isset($fieldset['form']['submit']['icon'])}{$fieldset['form']['submit']['icon']|escape:'html':'UTF-8'}{else}process-icon-save{/if}"></i> {$fieldset['form']['submit']['title']|escape:'html':'UTF-8'}
                </button>
            {/if}
            {if isset($fieldset['form']['buttons'])}
                <a class="link_preview_post" href="http://localhost/ps1760/en/blog/post/2-test.html" target="_blank">&nbsp;&nbsp;</a>
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
{block name="legend"}
    <div class="panel-heading">
        {if isset($field.image) && isset($field.title)}<img src="{$field.image|escape:'html':'UTF-8'}" alt="{$field.title|escape:'html':'UTF-8'}" />{/if}
        {if isset($field.icon)}<i class="{$field.icon|escape:'html':'UTF-8'}"></i>{/if}
        {$field.title|escape:'html':'UTF-8'}
        {if isset($addNewUrl) || (isset($preview_link) && $preview_link)}
            <span class="panel-heading-action">
                {if isset($preview_link) && $preview_link}
                    <a target="_blank" class="list-toolbar-btn" href="{$preview_link|escape:'html':'UTF-8'}">
                        <span data-placement="top" data-html="true" data-original-title="{l s='View post ' mod='ybc_blog'}" class="label-tooltip" data-toggle="tooltip" title="">
            				<i class="ets_svg search-plus">
        				<svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1088 800v64q0 13-9.5 22.5t-22.5 9.5h-224v224q0 13-9.5 22.5t-22.5 9.5h-64q-13 0-22.5-9.5t-9.5-22.5v-224h-224q-13 0-22.5-9.5t-9.5-22.5v-64q0-13 9.5-22.5t22.5-9.5h224v-224q0-13 9.5-22.5t22.5-9.5h64q13 0 22.5 9.5t9.5 22.5v224h224q13 0 22.5 9.5t9.5 22.5zm128 32q0-185-131.5-316.5t-316.5-131.5-316.5 131.5-131.5 316.5 131.5 316.5 316.5 131.5 316.5-131.5 131.5-316.5zm512 832q0 53-37.5 90.5t-90.5 37.5q-54 0-90-38l-343-342q-179 124-399 124-143 0-273.5-55.5t-225-150-150-225-55.5-273.5 55.5-273.5 150-225 225-150 273.5-55.5 273.5 55.5 225 150 150 225 55.5 273.5q0 220-124 399l343 343q37 37 37 90z"/></svg>
</i>
                        </span>
                    </a>
                {/if}
                {if isset($addNewUrl)}
                    <a class="list-toolbar-btn ybc-blog-add-new" href="{$addNewUrl|escape:'html':'UTF-8'}">
                        <span data-placement="top" data-html="true" data-original-title="{l s='Add new item ' mod='ybc_blog'}" class="label-tooltip" data-toggle="tooltip" title="">
            				<i class="process-icon-new"></i>
                        </span>
                    </a>
                {/if}

            </span>
        {/if}

        {if isset($post_key) && $post_key}<input type="hidden" name="post_key" value="{$post_key|escape:'html':'UTF-8'}" />{/if}
    </div>
    {if isset($configTabs) && $configTabs}
        <ul>
            {foreach from=$configTabs item='tab' key='tabId'}
                <li class="confi_tab config_tab_{$tabId|escape:'html':'UTF-8'}" data-tab-id="{$tabId|escape:'html':'UTF-8'}">
                    {if $tabId == 'chatgpt'}
                        <span class="chatgpt_icon">
                            <svg width="16" height="16" viewBox="0 0 41 41" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M37.5324 16.8707C37.9808 15.5241 38.1363 14.0974 37.9886 12.6859C37.8409 11.2744 37.3934 9.91076 36.676 8.68622C35.6126 6.83404 33.9882 5.3676 32.0373 4.4985C30.0864 3.62941 27.9098 3.40259 25.8215 3.85078C24.8796 2.7893 23.7219 1.94125 22.4257 1.36341C21.1295 0.785575 19.7249 0.491269 18.3058 0.500197C16.1708 0.495044 14.0893 1.16803 12.3614 2.42214C10.6335 3.67624 9.34853 5.44666 8.6917 7.47815C7.30085 7.76286 5.98686 8.3414 4.8377 9.17505C3.68854 10.0087 2.73073 11.0782 2.02839 12.312C0.956464 14.1591 0.498905 16.2988 0.721698 18.4228C0.944492 20.5467 1.83612 22.5449 3.268 24.1293C2.81966 25.4759 2.66413 26.9026 2.81182 28.3141C2.95951 29.7256 3.40701 31.0892 4.12437 32.3138C5.18791 34.1659 6.8123 35.6322 8.76321 36.5013C10.7141 37.3704 12.8907 37.5973 14.9789 37.1492C15.9208 38.2107 17.0786 39.0587 18.3747 39.6366C19.6709 40.2144 21.0755 40.5087 22.4946 40.4998C24.6307 40.5054 26.7133 39.8321 28.4418 38.5772C30.1704 37.3223 31.4556 35.5506 32.1119 33.5179C33.5027 33.2332 34.8167 32.6547 35.9659 31.821C37.115 30.9874 38.0728 29.9178 38.7752 28.684C39.8458 26.8371 40.3023 24.6979 40.0789 22.5748C39.8556 20.4517 38.9639 18.4544 37.5324 16.8707ZM22.4978 37.8849C20.7443 37.8874 19.0459 37.2733 17.6994 36.1501C17.7601 36.117 17.8666 36.0586 17.936 36.0161L25.9004 31.4156C26.1003 31.3019 26.2663 31.137 26.3813 30.9378C26.4964 30.7386 26.5563 30.5124 26.5549 30.2825V19.0542L29.9213 20.998C29.9389 21.0068 29.9541 21.0198 29.9656 21.0359C29.977 21.052 29.9842 21.0707 29.9867 21.0902V30.3889C29.9842 32.375 29.1946 34.2791 27.7909 35.6841C26.3872 37.0892 24.4838 37.8806 22.4978 37.8849ZM6.39227 31.0064C5.51397 29.4888 5.19742 27.7107 5.49804 25.9832C5.55718 26.0187 5.66048 26.0818 5.73461 26.1244L13.699 30.7248C13.8975 30.8408 14.1233 30.902 14.3532 30.902C14.583 30.902 14.8088 30.8408 15.0073 30.7248L24.731 25.1103V28.9979C24.7321 29.0177 24.7283 29.0376 24.7199 29.0556C24.7115 29.0736 24.6988 29.0893 24.6829 29.1012L16.6317 33.7497C14.9096 34.7416 12.8643 35.0097 10.9447 34.4954C9.02506 33.9811 7.38785 32.7263 6.39227 31.0064ZM4.29707 13.6194C5.17156 12.0998 6.55279 10.9364 8.19885 10.3327C8.19885 10.4013 8.19491 10.5228 8.19491 10.6071V19.808C8.19351 20.0378 8.25334 20.2638 8.36823 20.4629C8.48312 20.6619 8.64893 20.8267 8.84863 20.9404L18.5723 26.5542L15.206 28.4979C15.1894 28.5089 15.1703 28.5155 15.1505 28.5173C15.1307 28.5191 15.1107 28.516 15.0924 28.5082L7.04046 23.8557C5.32135 22.8601 4.06716 21.2235 3.55289 19.3046C3.03862 17.3858 3.30624 15.3413 4.29707 13.6194ZM31.955 20.0556L22.2312 14.4411L25.5976 12.4981C25.6142 12.4872 25.6333 12.4805 25.6531 12.4787C25.6729 12.4769 25.6928 12.4801 25.7111 12.4879L33.7631 17.1364C34.9967 17.849 36.0017 18.8982 36.6606 20.1613C37.3194 21.4244 37.6047 22.849 37.4832 24.2684C37.3617 25.6878 36.8382 27.0432 35.9743 28.1759C35.1103 29.3086 33.9415 30.1717 32.6047 30.6641C32.6047 30.5947 32.6047 30.4733 32.6047 30.3889V21.188C32.6066 20.9586 32.5474 20.7328 32.4332 20.5338C32.319 20.3348 32.154 20.1698 31.955 20.0556ZM35.3055 15.0128C35.2464 14.9765 35.1431 14.9142 35.069 14.8717L27.1045 10.2712C26.906 10.1554 26.6803 10.0943 26.4504 10.0943C26.2206 10.0943 25.9948 10.1554 25.7963 10.2712L16.0726 15.8858V11.9982C16.0715 11.9783 16.0753 11.9585 16.0837 11.9405C16.0921 11.9225 16.1048 11.9068 16.1207 11.8949L24.1719 7.25025C25.4053 6.53903 26.8158 6.19376 28.2383 6.25482C29.6608 6.31589 31.0364 6.78077 32.2044 7.59508C33.3723 8.40939 34.2842 9.53945 34.8334 10.8531C35.3826 12.1667 35.5464 13.6095 35.3055 15.0128ZM14.2424 21.9419L10.8752 19.9981C10.8576 19.9893 10.8423 19.9763 10.8309 19.9602C10.8195 19.9441 10.8122 19.9254 10.8098 19.9058V10.6071C10.8107 9.18295 11.2173 7.78848 11.9819 6.58696C12.7466 5.38544 13.8377 4.42659 15.1275 3.82264C16.4173 3.21869 17.8524 2.99464 19.2649 3.1767C20.6775 3.35876 22.0089 3.93941 23.1034 4.85067C23.0427 4.88379 22.937 4.94215 22.8668 4.98473L14.9024 9.58517C14.7025 9.69878 14.5366 9.86356 14.4215 10.0626C14.3065 10.2616 14.2466 10.4877 14.2479 10.7175L14.2424 21.9419ZM16.071 17.9991L20.4018 15.4978L24.7325 17.9975V22.9985L20.4018 25.4983L16.071 22.9985V17.9991Z" fill="currentColor"></path></svg>
                        </span>
                    {elseif $tabId =='design'}
                        <svg width="13" height="13" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1615 0q70 0 122.5 46.5t52.5 116.5q0 63-45 151-332 629-465 752-97 91-218 91-126 0-216.5-92.5t-90.5-219.5q0-128 92-212l638-579q59-54 130-54zm-909 1034q39 76 106.5 130t150.5 76l1 71q4 213-129.5 347t-348.5 134q-123 0-218-46.5t-152.5-127.5-86.5-183-29-220q7 5 41 30t62 44.5 59 36.5 46 17q41 0 55-37 25-66 57.5-112.5t69.5-76 88-47.5 103-25.5 125-10.5z"/></svg>
                    {/if}
                    {$tab|escape:'html':'UTF-8'}
                </li>
            {/foreach}
        </ul>
    {/if}
    {if isset($tab_post) && $tab_post}
        <ul class="tab_post">
            <li class="confi_tab config_tab_basic active" data-tab-id="basic">{l s='Basic content' mod='ybc_blog'}</li>
            <li class="confi_tab config_tab_seo" data-tab-id="seo">{l s='Seo' mod='ybc_blog'}</li>
            <li class="confi_tab config_tab_option" data-tab-id="option">{l s='Options' mod='ybc_blog'}</li>
        </ul>
    {/if}
    {if isset($tab_category) && $tab_category}
        <ul class="tab_post">
            <li class="confi_tab config_tab_basic active" data-tab-id="basic">{l s='Basic info' mod='ybc_blog'}</li>
            <li class="confi_tab config_tab_seo" data-tab-id="seo">{l s='Seo' mod='ybc_blog'}</li>
        </ul>
    {/if}
{/block}

{block name="input_row"}
    {if $input.name=='YBC_BLOG_SIDEBAR_POSITION' || $input.name=='YBC_BLOG_HOME_POST_TYPE'}
        <h3 class="title-elements">{l s='Configuration' mod='ybc_blog'}</h3>
    {/if}
    {if $input.name=='YBC_BLOG_SHOW_LATEST_NEWS_BLOCK'}
        <ul id="sidebar-positions" class="sidebar-positions">
            {foreach from =$position_sidebar key='key' item='position'}
                {assign var ='sidebar' value=$sidebars.$position}
                {if isset($fields_value[$sidebar.name])}
                    {assign var='value_text' value=$fields_value[$sidebar.name]}
                {else}
                    {assign var='value_text' value=0}
                {/if}
                <li id="sidebar-position-{$position|escape:'html':'UTF-8'}">
                    <div>
                        <div class="title-sidebar">
                        <span class="position_number" >
                            <span>
                                {assign var='index' value=$key+1}
                                {$index|intval}
                            </span>
                        </span>
                            {$sidebar.title|escape:'html':'UTF-8'}
                        </div>
                        <div class="setting" data-setting="{$position|escape:'html':'UTF-8'}"><i class="icon-AdminAdmin"></i>{l s='Setting' mod='ybc_blog'}</div>
                        <label class="ets_sl_switch{if $value_text} active{/if}">
                            <input class="ets_sl_slider" type="checkbox" {if $value_text}checked ="checked"{/if} value="1" data-field="{$sidebar.name|escape:'html':'UTF-8'}"/>
                            <span class="ets_sl_slider_label on">{l s='On' mod='ybc_blog'}</span>
                            <span class="ets_sl_slider_label off">{l s='Off' mod='ybc_blog'}</span>
                        </label>
                    </div>
                </li>
            {/foreach}
        </ul>
    {/if}
    {if $input.name=='YBC_BLOG_SHOW_LATEST_BLOCK_HOME'}
        <ul id="sidebar-positions" class="sidebar-positions">
            {foreach from =$position_homepages key='key' item='position'}
                {assign var ='homepage' value=$homepages.$position}
                {assign var='value_text' value=$fields_value[$homepage.name]}
                <li id="sidebar-position-{$position|escape:'html':'UTF-8'}">
                    <div>
                        <div class="title-sidebar">
                            <span class="position_number" >
                                <span>
                                    {assign var='index' value=$key+1}
                                    {$index|intval}
                                </span>
                            </span>
                            {$homepage.title|escape:'html':'UTF-8'}
                        </div>
                        <div class="setting" data-setting="{$position|escape:'html':'UTF-8'}"><i class="icon-AdminAdmin"></i>{l s='Setting' mod='ybc_blog'}</div>
                        <label class="ets_sl_switch{if $value_text} active{/if}">
                            <input class="ets_sl_slider" type="checkbox" {if $value_text} checked ="checked"{/if} value="1" data-field="{$homepage.name|escape:'html':'UTF-8'}" />
                            <span class="ets_sl_slider_label on">{l s='On' mod='ybc_blog'}</span>
                            <span class="ets_sl_slider_label off">{l s='Off' mod='ybc_blog'}</span>
                        </label>
                    </div>
                </li>
            {/foreach}
        </ul>
    {/if}
    {if $input.name=='YBC_BLOG_SHOW_LATEST_BLOCK_HOME' || $input.name=='YBC_BLOG_SHOW_POPULAR_BLOCK_HOME' || $input.name=='YBC_BLOG_SHOW_FEATURED_BLOCK_HOME' || $input.name=="YBC_BLOG_SHOW_CATEGORY_BLOCK_HOME" || $input.name=="YBC_BLOG_SHOW_GALLERY_BLOCK_HOME" ||  $input.name=="YBC_BLOG_SHOW_LATEST_NEWS_BLOCK" ||$input.name=='YBC_BLOG_SHOW_FEATURED_BLOCK' || $input.name=='YBC_BLOG_SHOW_POPULAR_POST_BLOCK' || $input.name=='YBC_BLOG_SHOW_GALLERY_BLOCK' || $input.name=='YBC_BLOG_SHOW_ARCHIVES_BLOCK' || $input.name=='YBC_BLOG_SHOW_CATEGORIES_BLOCK' || $input.name=='YBC_BLOG_SHOW_SEARCH_BLOCK'|| $input.name=='YBC_BLOG_SHOW_TAGS_BLOCK' || $input.name=='YBC_BLOG_SHOW_COMMENT_BLOCK' || $input.name=='YBC_BLOG_SHOW_AUTHOR_BLOCK' || $input.name=='YBC_BLOG_ENABLE_RSS_SIDEBAR' || $input.name=='YBC_BLOG_SHOW_HTML_BOX'}
        <div class="ybc-form-group-sidebar{if isset($input.form_group_class)} {$input.form_group_class|escape:'html':'UTF-8'}{/if}">
        <div class="ets_table">
        <div class="ets_table-cell">
        <div class="ybc-form-group-sidebar-wapper">
        <span class="close-setting-sidebar">{l s='Close' mod='ybc_blog'}</span>
        <div class="setting-title" ><i class="icon-AdminAdmin"></i>{l s='Setting' mod='ybc_blog'}</div>
    {/if}
    {if $input.name=='YBC_BLOG_ENABLE_MAIL'}
        <p><strong>{l s='Send email to Administrator and Authors: ' mod='ybc_blog'}</strong></p>
    {/if}
    {if $input.name=='YBC_BLOG_ENABLE_MAIL_NEW_COMMENT'}
        <p><strong>{l s='Send email to Users/Customers:' mod='ybc_blog'}</strong></p>
    {/if}
    {if $input.type=='image'}
        <div class="form-group">
            <div class="label-text">{$input.label|escape:'html':'UTF-8'}</div>
            <label class="control-label col-lg-3 required">{l s='Width' mod='ybc_blog'}</label>
            <div class="col-lg-9">
                <div class="input-group ">
                    <input id="{$input.name|escape:'html':'UTF-8'}_WIDTH" class="" type="text" required="required" value="{$fields_value[$input.name]['width']|intval}" name="{$input.name|escape:'html':'UTF-8'}_WIDTH" />
                    <span class="input-group-addon"> px </span>
                </div>
                <p class="help-block">{l s='Valid values: 50 - 3000' mod='ybc_blog'} </p>
            </div>
            <label class="control-label col-lg-3 required">{l s='Height' mod='ybc_blog'}</label>
            <div class="col-lg-9">
                <div class="input-group ">
                    <input id="{$input.name|escape:'html':'UTF-8'}_HEIGHT" class="" type="text" required="required" value="{$fields_value[$input.name]['height']|intval}" name="{$input.name|escape:'html':'UTF-8'}_HEIGHT" />
                    <span class="input-group-addon"> px </span>
                </div>
                <p class="help-block">{l s='Valid values: 50 - 3000' mod='ybc_blog'} </p>
            </div>
        </div>
    {elseif (isset($isConfigForm) && $isConfigForm) || (isset($tab_post) && $tab_post) || isset($tab_category) && $tab_category}
        <div class="ybc-form-group{if isset($input.tab) && $input.tab} ybc-blog-tab-{$input.tab|escape:'html':'UTF-8'}{/if}">
            {$smarty.block.parent}
            {if isset($input.info) && $input.info}
                <div class="ybc_tc_info alert alert-warning">{$input.info|escape:'html':'UTF-8'}</div>
            {/if}
        </div>
    {else}
        {$smarty.block.parent}
    {/if}
    {if $input.name=='YBC_BLOG_CUSTOMER_EMAIL_APPROVED_POST'}
        <div class="alert alert-info">
            {l s='You can edit or translate all email templates using a text editor such as Notepad, PhpStorm, etc. All email template files are located in this folder: ' mod='ybc_blog'} <span class="ybc_folder"><strong>modules/ybc_blog/mails/</strong></span>
        </div>
    {/if}
    {if $input.name=='datetime_active'}
        {if isset($form_author_post)}
            <div class="ybc_form_author_post">
                {if $form_author_post}
                    {$form_author_post nofilter}
                {/if}
            </div>
        {/if}
        {if isset($check_suspend) && $check_suspend}
            <div class="ybc-form-group ybc-blog-tab-basic">
                <div class="alert alert-warning">
                    {l s='This post is hidden on the front office because its author is suspended' mod='ybc_blog'}
                </div>
            </div>
        {/if}
    {/if}
    {if $input.name=="YBC_BLOG_LATEST_POST_NUMBER_HOME" || $input.name=='YBC_BLOG_POPULAR_POST_NUMBER_HOME' || $input.name=='YBC_BLOG_FEATURED_POST_NUMBER_HOME' || $input.name=='YBC_BLOG_CATEGORY_POST_NUMBER_HOME' || $input.name=='YBC_BLOG_GALLERY_POST_NUMBER_HOME' || $input.name=='YBC_BLOG_LATES_POST_NUMBER' || $input.name=='YBC_BLOG_PUPULAR_POST_NUMBER' || $input.name=='YBC_BLOG_FEATURED_POST_NUMBER' || $input.name=='YBC_BLOG_GALLERY_POST_NUMBER' || $input.name=='YBC_BLOG_EXPAND_ARCHIVES_BLOCK' || $input.name=='YBC_BLOG_SHOW_CATEGORIES_BLOCK' || $input.name=='YBC_BLOG_SHOW_SEARCH_BLOCK' || $input.name=='YBC_BLOG_TAGS_NUMBER' || $input.name=='YBC_BLOG_COMMENT_LENGTH' || $input.name=='YBC_BLOG_AUTHOR_NUMBER' || $input.name=='YBC_BLOG_ENABLE_RSS_SIDEBAR' || $input.name=='YBC_BLOG_CONTENT_HTML_BOX'}
        <div class="popup_footer">
            <button class="module_form_submit_btn_sidebar btn btn-default pull-right" type="button">
                <i class="process-icon-save"></i>
                {l s='Save' mod='ybc_blog'}
            </button>
        </div>
        </div>
        </div>
        </div>
        </div>
    {/if}
    {if $input.name=='YBC_BLOG_API_GPT'}
        <div class="ybc-form-group ybc-blog-tab-chatgpt">
            <div class="form-group chatgpt list-chatgpt">
                {Module::getInstanceByName('ybc_blog')->displayListTemplateChatGPT() nofilter}
            </div>
        </div>
    {/if}
{/block}
{block name="label"}
    {if isset($input.label)}
        <label class="control-label col-lg-3{if ((isset($input.required) && $input.required) || (isset($input.required2) && $input.required2) ) && $input.type != 'radio'} required{/if}">
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
{/block}
{block name='description'}
    {if $input.type == 'file' && isset($input.is_image) && $input.is_image}
        {$smarty.block.parent}
        <p class="help-block">{l s='Available image types: jpg, png, gif, jpeg' mod='ybc_blog'}. {l s='Limit:' mod='ybc_blog'} {Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE')|escape:'html':'UTF-8'}Mb</p>
    {elseif isset($input.desc) && !is_array($input.desc)}
        <p class="help-block">{$input.desc|replace:'[highlight]':'<code>'|replace:'[end_highlight]':'</code>' nofilter}</p>
    {else}
        {$smarty.block.parent}
    {/if}
{/block}
{block name="after"}
    <script type="text/javascript">
        var Message_is_required_text = '{l s='Message is required' mod='ybc_blog' js=1}';
        var ChatGPT_API_request_success_text= '{l s='ChatGPT API request successfully' mod='ybc_blog' js=1}';
        var ChatGPT_API_request_error_text= '{l s='ChatGPT API request failed' mod='ybc_blog' js=1}';
        var confirm_clear_all_mesasage_chatgpt = '{l s='Do you want to clear all messages?' mod='ybc_blog' js=1}';
        var You_chatgpt_text = '{l s='You' mod='ybc_blog' js=1}';
        var Allplied_successfull_text ='{l s='Applied successfully' mod='ybc_blog' js=1}';
    </script>
    {if isset($YBC_BLOG_ENABLED_GPT) && $YBC_BLOG_ENABLED_GPT}
        {Module::getInstanceByName('ybc_blog')->displayFormChatGPT() nofilter}
    {/if}
{/block}
