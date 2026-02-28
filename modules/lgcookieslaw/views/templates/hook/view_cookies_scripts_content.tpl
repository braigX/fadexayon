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

{if isset($lgcookieslaw_cookies_scripts) && !empty($lgcookieslaw_cookies_scripts)}
    {foreach $lgcookieslaw_cookies_scripts as $lgcookieslaw_cookies_script}
        {if $lgcookieslaw_cookies_script.add_script_tag}
            <script type="text/javascript">
                {if $lgcookieslaw_cookies_script.add_script_literal}
                    {$lgcookieslaw_cookies_script.script_code nofilter} {* SMARTY OR JS CONTENT *}
                {else}
                    {assign var="lgcookieslaw_script_content" value=$lgcookieslaw_cookies_script.script_code}

                    {include file="string:$lgcookieslaw_script_content"}
                {/if}
            </script>
        {else}
            {if $lgcookieslaw_cookies_script.add_script_literal}
                {$lgcookieslaw_cookies_script.script_code nofilter} {* SMARTY OR JS CONTENT *}
            {else}
                {assign var="lgcookieslaw_script_content" value=$lgcookieslaw_cookies_script.script_code}

                {include file="string:$lgcookieslaw_script_content"}
            {/if}
        {/if}
    {/foreach}
{/if}
