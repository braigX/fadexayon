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

<div class="lg-block">
    <div class="pull-left">
        <div class="lg-block-icon">
            <a class="support" href="https://clientes.lineagrafica.es/submitticket.php?step=2&deptid=10" target="_blank">
                <img src="{$lg_base_url|escape:'htmlall':'UTF-8'}views/img/publi/ico_support.png">
                <span class="text">{l s='Support' mod='lgcookieslaw'}</span>
            </a>
        </div>
    </div>
    <div class="pull-right">
        <img src="{$lg_base_url|escape:'htmlall':'UTF-8'}views/img/publi/logo_lgaddons.png"/>
        <div class="lg-block-icon right">
            <a class="see-modules" href="{l s='https://www.lineagrafica.es/en/prestashop-modules/' mod='lgcookieslaw'}" target="_blank">
                <img src="{$lg_base_url|escape:'htmlall':'UTF-8'}views/img/publi/ico_see_modules.png">
                <span class="text">{l s='See modules' mod='lgcookieslaw'}</span>
            </a>
        </div>
    </div>
</div>

{if isset($display_errors) && is_array($display_errors) && count($display_errors)}
    <div class="bootstrap">
        <div class="module_error alert alert-danger">
            <button type="button" class="close" data-dismiss="alert">&times;</button>

            <ul>
                {foreach $display_errors as $error}
                    <li>{$error nofilter}</li>
                {/foreach}
            </ul>
        </div>
    </div>
{/if}

{if isset($display_informations) && is_array($display_informations) && count($display_informations)}
    <div class="bootstrap">
        <div class="module_info info alert alert-info">
            <button type="button" class="close" data-dismiss="alert">&times;</button>

            <ul>
                {foreach $display_informations as $information}
                    <li>{$information nofilter}</li>
                {/foreach}
            </ul>
        </div>
    </div>
{/if}

{if isset($display_warnings) && is_array($display_warnings) && count($display_warnings)}
    <div class="bootstrap">
        <div class="module_warning alert alert-warning">
            <button type="button" class="close" data-dismiss="alert">&times;</button>

            <ul>
                {foreach $display_warnings as $warning}
                    <li>{$warning nofilter}</li>
                {/foreach}
            </ul>
        </div>
    </div>
{/if}

{if isset($display_confirmation) && $display_confirmation}
    <div class="bootstrap">
        <div class="module_confirmation conf confirm alert alert-success lgmodule-alert">
            <button type="button" class="close" data-dismiss="alert">&times;</button>

            {$display_confirmation nofilter}</li>
        </div>
    </div>
{/if}

{if isset($lg_menu) && is_array($lg_menu) && count($lg_menu)}
    <div class="lgmodule_wrapper">
        <ul class="lgmodule_menu">
            {foreach $lg_menu as $tab}
                <li>
                    <a href="{$tab.link|escape:'html':'UTF-8'}" {if $tab.active}class="active"{/if}>{$tab.label|escape:'html':'UTF-8'}</a>
                </li>
            {/foreach}
        </ul>
    </div>
{/if}
