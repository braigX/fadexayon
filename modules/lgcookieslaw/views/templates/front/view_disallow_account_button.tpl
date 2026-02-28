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

{if isset($lgcookieslaw_ps_version)}
    {if $lgcookieslaw_ps_version == '15'}
        <li class="lgcookieslaw-account-button">
            <a href="{$lgcookieslaw_disallow_url|escape:'quotes':'UTF-8'}" title="{l s='Revoke my consent to cookies' mod='lgcookieslaw'}">
                <span>
                    <img src="{$lgcookieslaw_image_path|escape:'quotes':'UTF-8'}" class="icon" alt="{l s='Revoke my consent to cookies' mod='lgcookieslaw'}">
                    {l s='Revoke my consent to cookies' mod='lgcookieslaw'}
                </span>
            </a>
        </li>
    {elseif $lgcookieslaw_ps_version == '16'}
        <li class="lgcookieslaw-account-button">
            <a href="{$lgcookieslaw_disallow_url|escape:'quotes':'UTF-8'}" title="{l s='Revoke my consent to cookies' mod='lgcookieslaw'}">
                <img src="{$lgcookieslaw_image_path|escape:'quotes':'UTF-8'}">
                <span>{l s='Revoke my consent to cookies' mod='lgcookieslaw'}</span>
            </a>
        </li>
    {else}
        <a class="col-lg-4 col-md-6 col-sm-6 col-xs-12 lgcookieslaw-account-button" href="{$lgcookieslaw_disallow_url|escape:'html':'UTF-8'}" title="{l s='Revoke my consent to cookies' mod='lgcookieslaw'}">
            <span class="link-item">
                <img src="{$lgcookieslaw_image_path|escape:'quotes':'UTF-8'}">
                {l s='Revoke my consent to cookies' mod='lgcookieslaw'}
            </span>
        </a>
    {/if}
{/if}
