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

<div id="lgcookieslaw_external_module_notice" class="module_info info alert alert-info hide">
    <button id="lgcookieslaw_external_module_notice_close_button" class="close hide" data-dismiss="alert">&times;</button>
    <strong>{l s='"%s" module reports:' sprintf=[$lgcookieslaw_display_name] mod='lgcookieslaw'}</strong><br><br>
    {l s='This module has not yet been classified according to the type of Cookies it uses. Do not forget if this module does not use cookies in the client\'s browser you can ignore this notice, otherwise make sure to include it according to the type of Cookie you use' mod='lgcookieslaw'} (<a href="{$lgcookieslaw_url_purposes|escape:'quotes':'UTF-8'}" target="_blank">{l s='access purposes' mod='lgcookieslaw'}</a>).<br><br>
    <button id="lgcookieslaw_external_module_notice_hide_button" class="btn btn-default pull-left" data-external-module-name="{$lgcookieslaw_external_module_name|escape:'htmlall':'UTF-8'}">{l s='Hide notice' mod='lgcookieslaw'}</button><br><br>
</div>
