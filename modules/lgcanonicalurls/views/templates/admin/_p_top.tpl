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

<div class="lg-block">
    <div class="pull-left">
        <div class="lg-block-icon">
            <a class="support" href="https://addons.prestashop.com/es/Write-to-developper?id_product={$lg_id_product|escape:'htmlall':'UTF-8'}" target="_blank">
                <img src="{$base_url|escape:'htmlall':'UTF-8'}views/img/publi/ico_support.png">
                <span class="text">{l s='Support' mod='lgcanonicalurls'}</span>
            </a>
        </div>
        <div class="lg-block-icon">
            <a class="opinion" href="https://addons.prestashop.com/en/ratings.php" target="_blank">
                <img src="{$base_url|escape:'htmlall':'UTF-8'}views/img/publi/ico_opinion.png">
                <span class="text">{l s='Leave a comment' mod='lgcanonicalurls'}</span>
            </a>
        </div>
    </div>
    <div class="pull-right">
        <img src="{$base_url|escape:'htmlall':'UTF-8'}views/img/publi/logo_lgaddons.png"/>
        <div class="lg-block-icon right">
            <a class="see-modules" href="https://addons.prestashop.com/es/22_linea-grafica" target="_blank">
                <img src="{$base_url|escape:'htmlall':'UTF-8'}views/img/publi/ico_see_modules.png">
                <span class="text">{l s='See modules' mod='lgcanonicalurls'}</span>
            </a>
        </div>
    </div>
</div>
{if isset($show_errors) && count($show_errors)}
    <div class="alert alert-danger lgmodule-errors">
        {l s="Please, fix next errors:" mod='lgcanonicalurls'}
        <ul>
            {foreach from=$show_errors item='error'}
                <li>
                    {$error|escape:'html':'UTF-8'}
                </li>
            {/foreach}
        </ul>
    </div>
{else}
    {if isset($show_message) && $show_message}
        <div class="alert alert-success lgmodule-success">
            {l s='Your configuration have been saved successfully!' mod='lgcanonicalurls'}
        </div>
    {/if}
{/if}
<div class="lgmodule_wrapper">
    <ul class="lgmodule_menu">
        {foreach $lg_menu as $tab}
            <li>
                <a href="{$tab.link|escape:'html':'UTF-8'}" class="{if $tab.active}active{/if} {if isset($tab.image)}image{/if}" >
                    {if isset($tab.image)}
                        <img src="{$tab.image|escape:'htmlall':'UTF-8'}" alt="{$tab.label|escape:'html':'UTF-8'}">
                    {else}
                        {$tab.label|escape:'html':'UTF-8'}
                    {/if}
                </a>
            </li>
        {/foreach}
    </ul>
</div>