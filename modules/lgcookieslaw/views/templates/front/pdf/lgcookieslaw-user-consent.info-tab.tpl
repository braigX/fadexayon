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

<h2>{l s='User Consent Information' mod='lgcookieslaw'}</h2>

<p>{l s='Your preferences were last updated on' mod='lgcookieslaw'}: {$lgcookieslaw_user_consent->consent_date|date_format:'%d/%m/%Y %H:%M:%S'|escape:'htmlall':'UTF-8'}</p>

<table class="border" width="100%" style="vertical-align: middle; white-space: nowrap; padding: 4px; width: 100%; border-collapse: collapse; border: 1px solid #F0F0F0;">
    <thead>
        <tr>
            <th class="header" valign="middle" style="vertical-align: middle; white-space: nowrap; padding: 4px; border: 1px solid #F0F0F0; height: 20px; background-color: #F0F0F0; vertical-align: middle; text-align: center; font-weight: bold;">{l s='Field' mod='lgcookieslaw'}</th>
            <th class="header" valign="middle" style="vertical-align: middle; white-space: nowrap; padding: 4px; border: 1px solid #F0F0F0; height: 20px; background-color: #F0F0F0; vertical-align: middle; text-align: center; font-weight: bold;">{l s='Value' mod='lgcookieslaw'}</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="border: 1px solid #F0F0F0;">
                {l s='Download Hash' mod='lgcookieslaw'}
            </td>
            <td style="border: 1px solid #F0F0F0;">
                {$lgcookieslaw_user_consent->download_hash|escape:'htmlall':'UTF-8'}
            </td>
        </tr>
        <tr>
            <td style="border: 1px solid #F0F0F0;">
                {l s='Consent Date' mod='lgcookieslaw'}
            </td>
            <td style="border: 1px solid #F0F0F0;">
                {$lgcookieslaw_user_consent->consent_date|date_format:'%d/%m/%Y %H:%M:%S'|escape:'htmlall':'UTF-8'}
            </td>
        </tr>
        <tr>
            <td style="border: 1px solid #F0F0F0;">
                {l s='IP Address' mod='lgcookieslaw'}
            </td>
            <td style="border: 1px solid #F0F0F0;">
                {$lgcookieslaw_user_consent->ip_address|escape:'htmlall':'UTF-8'}
            </td>
        </tr>
    </tbody>
</table>
