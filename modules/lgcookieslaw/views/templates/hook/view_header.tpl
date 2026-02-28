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

{if isset($lgcookieslaw_consent_mode_content) && !empty($lgcookieslaw_consent_mode_content)}
    {$lgcookieslaw_consent_mode_content nofilter} {* JS CONTENT *}
{/if}

<script type="text/javascript">
    var lgcookieslaw_consent_mode = {$lgcookieslaw_consent_mode|intval};
    var lgcookieslaw_banner_url_ajax_controller = "{$lgcookieslaw_banner_url_ajax_controller nofilter}"; {* URL CONTENT *}
    var lgcookieslaw_cookie_values = {$lgcookieslaw_cookie_values_json nofilter}; {* JSON CONTENT *}
    var lgcookieslaw_saved_preferences = {$lgcookieslaw_saved_preferences|intval};
    var lgcookieslaw_ajax_calls_token = "{$lgcookieslaw_ajax_calls_token|escape:'html':'UTF-8'}";
    var lgcookieslaw_reload = {$lgcookieslaw_reload|intval};
    var lgcookieslaw_block_navigation = {$lgcookieslaw_block_navigation|intval};
    var lgcookieslaw_banner_position = {$lgcookieslaw_banner_position|intval};
    var lgcookieslaw_show_fixed_button = {$lgcookieslaw_show_fixed_button|intval};
    var lgcookieslaw_save_user_consent = {$lgcookieslaw_save_user_consent|intval};
    var lgcookieslaw_reject_cookies_when_closing_banner = {$lgcookieslaw_reject_cookies_when_closing_banner|intval};
</script>

{if isset($lgcookieslaw_cookies_scripts_content) && !empty($lgcookieslaw_cookies_scripts_content)}
    {$lgcookieslaw_cookies_scripts_content nofilter} {* SMARTY OR JS CONTENT *}
{/if}
