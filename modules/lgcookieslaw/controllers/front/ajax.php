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

class LGCookiesLawAJAXModuleFrontController extends ModuleFrontController
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->ajax) {
            Tools::redirect('index');
        }
    }

    protected function displayAjaxSaveUserPreferences()
    {
        $json = [
            'errors' => [],
        ];

        $success = Tools::getValue('token', '') == LGCookiesLaw::getToken($this->module->name);

        if ($success) {
            $lgcookieslaw_cookie_values = $this->module->processSaveUserPreferences();

            $success = !empty($lgcookieslaw_cookie_values);

            if ($success) {
                $user_consent_consent_date_text = $this->module->ps_version == '8' ?
                    Tools::displayDate($lgcookieslaw_cookie_values['lgcookieslaw_user_consent_consent_date'], true) :
                    Tools::displayDate(
                        $lgcookieslaw_cookie_values['lgcookieslaw_user_consent_consent_date'],
                        null,
                        true
                    );

                $json['user_consent_consent_date_text'] = $user_consent_consent_date_text;
                $json['user_consent_consent_date_content'] =
                    $this->module->l('Last updated', 'ajax') . ': ' . $json['user_consent_consent_date_text'];
                $json['user_consent_download_url'] =
                    $lgcookieslaw_cookie_values['lgcookieslaw_user_consent_download_url'];
            } else {
                $json['errors'][] = $this->module->l('Your preferences couldn\'t be saved.', 'ajax');
            }
        } else {
            $json['errors'][] = $this->module->l('Wrong security token.', 'ajax');
        }

        $json['status'] = (bool) $success;

        LGCookiesLaw::returnResponse($json);
    }
}
