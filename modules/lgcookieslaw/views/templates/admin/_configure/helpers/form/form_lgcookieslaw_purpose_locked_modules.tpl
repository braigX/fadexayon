{**
 * Copyright 2024 LÍNEA GRÁFICA E.C.E S.L.
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

<div class="lgcookieslaw-locked-modules toggle_technical_off">
    <table class="table table-locked-modules">
        <tr>
            {foreach from=$lgcookieslaw_module_list item=module name=module_list}
                {if ($smarty.foreach.module_list.iteration - 1) && ($smarty.foreach.module_list.iteration - 1) % 3 == 0}
                    </tr>
                    <tr>
                {/if}

                <td>
                    <div class="table-locked-modules-item">
                        {$path_logo = $smarty.const._PS_ROOT_DIR_|cat:'/modules/'|cat:$module['name']|cat:'/logo.'}

                        {if version_compare($smarty.const._PS_VERSION_, '1.5', '>=')}
                            {$path_logo = $path_logo|cat:'png'}
                        {else}
                            {$path_logo = $path_logo|cat:'gif'}
                        {/if}

                        <img
                            src="{if file_exists($path_logo)}../modules/{$module['name']|escape:'htmlall':'UTF-8'}/logo.{if version_compare($smarty.const._PS_VERSION_, '1.5', '>=')}png{else}gif{/if}{else}../img/questionmark.png{/if}"
                            alt="{$module.display_name|escape:'quotes':'UTF-8'}"
                            {if version_compare($smarty.const._PS_VERSION_, '1.5', '>=')}
                                width="32"
                                height="32"
                            {else}
                                width="16"
                                height="16"
                            {/if}
                        />
                    
                        <input
                            type="checkbox"
                            {if in_array($module.name, $lgcookieslaw_purpose_locked_modules)}checked="checked"{/if}
                            id="{$lgcookieslaw_field_name|escape:'htmlall':'UTF-8'}_module_{$module.name|escape:'html':'UTF-8'}"
                            name="{$lgcookieslaw_field_name|escape:'htmlall':'UTF-8'}[]"
                            value="{$module.name|escape:'html':'UTF-8'}"
                        />

                        <label for="{$lgcookieslaw_field_name|escape:'htmlall':'UTF-8'}_module_{$module.name|escape:'html':'UTF-8'}">{$module.name|escape:'html':'UTF-8'}</label>
                    </div>
                    <div class="table-locked-modules-name"><small>{$module.display_name|escape:'html':'UTF-8'}</small></div>
                </td>
            {/foreach}
        </tr>
    </table>
</div>
<p class="help-block">
    {l s='Here is the list of modules installed in your store. Remember to mark the modules that you want' mod='lgcookieslaw'}
    {l s=' to deactivate for this purpose until the user gives their consent.' mod='lgcookieslaw'} 
</p>
<div class="alert alert-warning warn">
    <span> {l s='Note that if a module is locked for multiple purposes, the user must agree to all of these purposes for the module to be unlocked.' mod='lgcookieslaw'} </span>
</div>
