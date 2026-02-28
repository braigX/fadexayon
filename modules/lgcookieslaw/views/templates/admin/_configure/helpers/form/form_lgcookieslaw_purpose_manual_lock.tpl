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

<div class="lgcookieslaw-purpose-manual-lock toggle_technical_off">
    <div class="alert alert-info">
        <p>{l s='You can block scripts located in both TPL templates and JS files using the following code:' mod='lgcookieslaw'}</p>
        <br>

        <h4>{l s='Smarty' mod='lgcookieslaw'}</h4>
        <p>
            {literal}
                <pre><strong>
        {if !empty($lgcookieslaw_cookie_values) &&
            isset($lgcookieslaw_cookie_values->{/literal}{if isset($lgcookieslaw_id_lgcookieslaw_purpose) && $lgcookieslaw_id_lgcookieslaw_purpose}lgcookieslaw_purpose_{$lgcookieslaw_id_lgcookieslaw_purpose|escape:'htmlall':'UTF-8'}{else}[id_lgcookieslaw_purpose]{/if}{literal}) &&
            $lgcookieslaw_cookie_values->{/literal}{if isset($lgcookieslaw_id_lgcookieslaw_purpose) && $lgcookieslaw_id_lgcookieslaw_purpose}lgcookieslaw_purpose_{$lgcookieslaw_id_lgcookieslaw_purpose|escape:'htmlall':'UTF-8'}{else}[id_lgcookieslaw_purpose]{/if}{literal} === true
        }</strong>

            __Smarty Code__
            <strong>
        {/if}</strong>
                </pre>
            {/literal}
        </p>
        <br>

        <h4>{l s='JS' mod='lgcookieslaw'}</h4>
        <p>
            {literal}
                <pre><strong>
        if (typeof lgcookieslaw_cookie_values === 'object' &&
            lgcookieslaw_cookie_values.{/literal}{if isset($lgcookieslaw_id_lgcookieslaw_purpose) && $lgcookieslaw_id_lgcookieslaw_purpose}lgcookieslaw_purpose_{$lgcookieslaw_id_lgcookieslaw_purpose|escape:'htmlall':'UTF-8'}{else}[id_lgcookieslaw_purpose]{/if}{literal} === true
        ) {</strong>

            __JS Code__
            <strong>
        }</strong>
                </pre>
            {/literal}
        </p>

        {if !isset($lgcookieslaw_id_lgcookieslaw_purpose) || !$lgcookieslaw_id_lgcookieslaw_purpose}
            <br>
            <h5>{l s='IMPORTANT' mod='lgcookieslaw'}</h5>
            <p><small>{l s='[1][id_lgcookieslaw_purpose][/1]: is the cookie purpose ID.' mod='lgcookieslaw' tags=['<strong>']} {l s='You can get it once the purpose has been created.' mod='lgcookieslaw'}</small></p>
        {/if}
    </div>
</div>
