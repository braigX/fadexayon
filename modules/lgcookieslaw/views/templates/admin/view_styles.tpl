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

{literal}
#lgcookieslaw_fixed_button,
.lgcookieslaw-banner {
    background-color: rgba({/literal}{$lgcookieslaw_banner_bg_color|escape:'html':'UTF-8'}{literal});
    color: {/literal}{$lgcookieslaw_banner_font_color|escape:'html':'UTF-8'}{literal} !important;
    -webkit-box-shadow: 0px 1px 5px 0px {/literal}{$lgcookieslaw_banner_shadow_color|escape:'html':'UTF-8'}{literal};
    -moz-box-shadow: 0px 1px 5px 0px {/literal}{$lgcookieslaw_banner_shadow_color|escape:'html':'UTF-8'}{literal};
    box-shadow: 0px 1px 5px 0px {/literal}{$lgcookieslaw_banner_shadow_color|escape:'html':'UTF-8'}{literal};
}
#lgcookieslaw_fixed_button svg {
    fill: {/literal}{$lgcookieslaw_fixed_button_svg_color|escape:'html':'UTF-8'}{literal};
}
#lgcookieslaw_banner .lgcookieslaw-banner-message p {
    color: {/literal}{$lgcookieslaw_banner_font_color|escape:'html':'UTF-8'}{literal} !important;
}
#lgcookieslaw_banner .lgcookieslaw-banner-message a {
    color: {/literal}{$lgcookieslaw_banner_font_color|escape:'html':'UTF-8'}{literal} !important;
    border-bottom: 1px solid {/literal}{$lgcookieslaw_banner_font_color|escape:'html':'UTF-8'}{literal};
}
.lgcookieslaw-modal-header {
    border-top: 4px solid {/literal}{$lgcookieslaw_accept_button_bg_color|escape:'html':'UTF-8'}{literal} !important;
}
.lgcookieslaw-slider-checked {
    box-shadow: 0 0 1px {/literal}{$lgcookieslaw_accept_button_bg_color|escape:'html':'UTF-8'}{literal} !important;
}
.lgcookieslaw-button-container .lgcookieslaw-accept-button,
.lgcookieslaw-modal .lgcookieslaw-accept-button,
.lgcookieslaw-modal .lgcookieslaw-badge,
.lgcookieslaw-slider.lgcookieslaw-slider-checked {
    color: {/literal}{$lgcookieslaw_accept_button_font_color|escape:'html':'UTF-8'}{literal} !important;
    background: {/literal}{$lgcookieslaw_accept_button_bg_color|escape:'html':'UTF-8'}{literal} !important;
    border-color: {/literal}{$lgcookieslaw_accept_button_bg_color|escape:'html':'UTF-8'}{literal} !important;
}
.lgcookieslaw-button-container .lgcookieslaw-reject-button,
.lgcookieslaw-modal .lgcookieslaw-reject-button {
    color: {/literal}{$lgcookieslaw_reject_button_font_color|escape:'html':'UTF-8'}{literal} !important;
    background: {/literal}{$lgcookieslaw_reject_button_bg_color|escape:'html':'UTF-8'}{literal} !important;
    border-color: {/literal}{$lgcookieslaw_reject_button_bg_color|escape:'html':'UTF-8'}{literal} !important;
}
{/literal}
