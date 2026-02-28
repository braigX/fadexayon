<?php
/**
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
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_4_26()
{
    Configuration::deleteByName('PS_LGCOOKIES_SETTING_BUTTON');
    Configuration::deleteByName('PS_LGCOOKIES_SHOW_CLOSE');
    Configuration::deleteByName('PS_LGCOOKIES_NAVIGATION_BTN');
    Configuration::deleteByName('PS_LGCOOKIES_NAVIGATION');
    Configuration::deleteByName('PS_LGCOOKIES_BTN2_FONT_COLOR');
    Configuration::deleteByName('PS_LGCOOKIES_BTN2_BG_COLOR');
    Configuration::deleteByName('PS_LGCOOKIES_SEL_TAB');

    Configuration::updateValue('PS_LGCOOKIES_POSITION', '3');

    if (version_compare(_PS_VERSION_, '1.6.0', '>=')) {
        Configuration::updateValue('PS_LGCOOKIES_SHOW_REJECT_ALL_BUTTON', '1');
    }

    return true;
}
