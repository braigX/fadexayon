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

<h2>{l s='Purposes' mod='lgcookieslaw'}</h2>

{foreach $lgcookieslaw_purposes as $lgcookieslaw_purpose}
    <h4>{$lgcookieslaw_purpose['name']|escape:'htmlall':'UTF-8'} <strong>{if $lgcookieslaw_purpose['t'] || $lgcookieslaw_purpose['c']}<span class="lgcookieslaw-purpose-enabled" style="color: #8AC954;">{l s='Enabled' mod='lgcookieslaw'}</span>{else}<span class="lgcookieslaw-purpose-disabled" style="color: #CCCCCC;">{l s='Disabled' mod='lgcookieslaw'}</span>{/if}</strong></h4>

        <p>{$lgcookieslaw_purpose['description']|escape:'htmlall':'UTF-8'}</p>

    {if $lgcookieslaw_purpose['associated_cookies']|count > 0}
        <p>
            <table class="border" width="100%" style="vertical-align: middle; white-space: nowrap; padding: 4px; width: 100%; border-collapse: collapse; border: 1px solid #F0F0F0;">
                <thead>
                    <tr>
                        <th width="20%" class="header" valign="middle" style="vertical-align: middle; white-space: nowrap; padding: 4px; border: 1px solid #F0F0F0; height: 20px; background-color: #F0F0F0; vertical-align: middle; text-align: center; font-weight: bold;">{l s='Cookie' mod='lgcookieslaw'}</th>
                        <th width="20%" class="header" valign="middle" style="vertical-align: middle; white-space: nowrap; padding: 4px; border: 1px solid #F0F0F0; height: 20px; background-color: #F0F0F0; vertical-align: middle; text-align: center; font-weight: bold;">{l s='Provider' mod='lgcookieslaw'}</th>
                        <th width="40%" class="header" valign="middle" style="vertical-align: middle; white-space: nowrap; padding: 4px; border: 1px solid #F0F0F0; height: 20px; background-color: #F0F0F0; vertical-align: middle; text-align: center; font-weight: bold;">{l s='Purpose' mod='lgcookieslaw'}</th>
                        <th width="20%" class="header" valign="middle" style="vertical-align: middle; white-space: nowrap; padding: 4px; border: 1px solid #F0F0F0; height: 20px; background-color: #F0F0F0; vertical-align: middle; text-align: center; font-weight: bold;">{l s='Expiry' mod='lgcookieslaw'}</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach $lgcookieslaw_purpose['associated_cookies'] as $lgcookieslaw_cookie}
                        <tr>
                            <td width="20%" style="border: 1px solid #F0F0F0;">{$lgcookieslaw_cookie['name']|escape:'htmlall':'UTF-8'}</td>
                            <td width="20%" style="border: 1px solid #F0F0F0;">{if !empty($lgcookieslaw_cookie['provider_url'])}<a href="{$lgcookieslaw_cookie['provider_url']|escape:'htmlall':'UTF-8'}">{/if}{$lgcookieslaw_cookie['provider']|escape:'htmlall':'UTF-8'}{if !empty($lgcookieslaw_cookie['provider_url'])}</a>{/if}</td>
                            <td width="40%" style="border: 1px solid #F0F0F0;">{$lgcookieslaw_cookie['cookie_purpose'] nofilter}</td>
                            <td width="20%" style="border: 1px solid #F0F0F0;">{$lgcookieslaw_cookie['expiry_time']|escape:'htmlall':'UTF-8'}</td>
                        </tr>
                    {/foreach}
                </tbody>
            </table>
        </p>
    {/if}
{/foreach}
