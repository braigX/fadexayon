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

class LGCookiesLawDownloadModuleFrontController extends ModuleFrontController
{
    protected $download_hash;
    protected $configuration;

    public function __construct()
    {
        $this->configuration = LGCookiesLaw::getModuleConfiguration();

        parent::__construct();

        $context = Context::getContext();

        $id_shop = Tools::getValue('id_shop', null);

        if (is_null($id_shop)) {
            $id_shop = $context->shop->id;
        }

        $this->download_hash = Tools::getValue('download_hash', false);

        if (!Validate::isMd5($this->download_hash)
            || !(bool) $this->configuration['PS_LGCOOKIES_SAVE_USER_CONSENT']
            || !LGCookiesLawUserConsent::existDownloadHash($this->download_hash, $id_shop)
        ) {
            Tools::redirect('index');
        }
    }

    public function postProcess()
    {
        $context = Context::getContext();

        $id_shop = Tools::getValue('id_shop', null);
        $id_lang = $context->language->id;

        if (is_null($id_shop)) {
            $id_shop = $context->shop->id;
        }

        $user_consent_file_name = $this->download_hash . '%00.jpg.pdf';
        $user_consent_file_path = sys_get_temp_dir() . '/' . $user_consent_file_name;

        $id_lgcookieslaw_user_consent = LGCookiesLawUserConsent::getIdByDownloadHash($this->download_hash);

        $lgcookieslaw_user_consent = new LGCookiesLawUserConsent((int) $id_lgcookieslaw_user_consent);

        if (Validate::isLoadedObject($lgcookieslaw_user_consent)
            && !empty($lgcookieslaw_user_consent->purposes)
        ) {
            $lgcookieslaw_purposes_info = LGCookiesLawPurpose::getPurposes((int) $id_lang, (int) $id_shop, true);
            $lgcookieslaw_purposes = LGCookiesLaw::jsonDecode($lgcookieslaw_user_consent->purposes, true);

            foreach ($lgcookieslaw_purposes as $key => $lgcookieslaw_purpose) {
                foreach ($lgcookieslaw_purposes_info as &$lgcookieslaw_purpose_info) {
                    if ($lgcookieslaw_purpose['id'] == $lgcookieslaw_purpose_info['id_lgcookieslaw_purpose']) {
                        $lgcookieslaw_purposes[$key]['name'] = $lgcookieslaw_purpose_info['name'];
                        $lgcookieslaw_purposes[$key]['description'] = $lgcookieslaw_purpose_info['description'];

                        $associated_cookies = LGCookiesLawCookie::getCookiesByPurpose(
                            (int) $lgcookieslaw_purpose['id'],
                            (int) $id_lang,
                            (int) $id_shop,
                            true
                        );

                        $lgcookieslaw_purposes[$key]['associated_cookies'] =
                            !empty($associated_cookies) ? $associated_cookies : [];
                    }
                }
            }

            $template_vars = [];

            $template_vars['template_vars']['lgcookieslaw_user_consent'] = $lgcookieslaw_user_consent;
            $template_vars['template_vars']['lgcookieslaw_purposes'] = $lgcookieslaw_purposes;

            $pdf = new PDF(
                $template_vars,
                'LGCookiesLawUserConsent',
                $context->smarty
            );

            $pdf_renderer = $pdf->render(false);

            file_put_contents($user_consent_file_path, $pdf_renderer);

            unset($lgcookieslaw_user_consent);

            if (ob_get_level() && ob_get_length() > 0) {
                ob_end_clean();
            }

            header('Content-Transfer-Encoding: binary');
            header('Content-type: application/pdf');
            header('Content-Length: ' . filesize($user_consent_file_path));
            header('Content-Disposition: attachment; filename="' . mb_convert_encoding($user_consent_file_name, 'ISO-8859-1', 'UTF-8') . '"');

            @set_time_limit(0);

            $this->module->readfileChunked($user_consent_file_path);

            unlink($user_consent_file_path);

            exit;
        } else {
            Tools::redirect('index');
        }
    }
}
