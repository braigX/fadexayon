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

class LGCookiesLawDisallowModuleFrontController extends ModuleFrontController
{
    protected $valid_token = false;

    public function __construct()
    {
        parent::__construct();

        $this->valid_token = md5(_COOKIE_KEY_ . $this->module->name) == Tools::getValue('token', '');

        if ($this->valid_token) {
            $this->module->deleteCookies();
        }
    }

    public function initContent()
    {
        parent::initContent();

        $context = Context::getContext();

        $template_path = version_compare(_PS_VERSION_, '1.7.0', '>=') ?
            'module:' . $this->module->name . '/views/templates/front/17/' : '';

        $this->setTemplate($template_path . 'view_disallow.tpl');

        $context->smarty->assign([
            'lgcookieslaw_valid_token' => $this->valid_token,
        ]);
    }
}
