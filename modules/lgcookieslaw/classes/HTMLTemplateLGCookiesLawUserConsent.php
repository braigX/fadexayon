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
class HTMLTemplateLGCookiesLawUserConsent extends HTMLTemplate
{
    public $template_vars;

    public function __construct($template_vars, Smarty $smarty)
    {
        $context = Context::getContext();

        $id_shop = $context->shop->id;

        $this->template_vars = $template_vars;

        $this->smarty = $smarty;
        $this->shop = new Shop((int) $id_shop);
    }

    /**
     * Returns the template's HTML footer.
     *
     * @return string HTML footer
     */
    public function getFooter()
    {
        $shop_address = $this->getShopAddress();

        $this->smarty->assign([
            'available_in_your_account' => false,
            'shop_address' => $shop_address,
            'shop_fax' => Configuration::get('PS_SHOP_FAX'),
            'shop_phone' => Configuration::get('PS_SHOP_PHONE'),
            'shop_details' => Configuration::get('PS_SHOP_DETAILS'),
            'free_text' => '',
        ]);

        return $this->smarty->fetch($this->getTemplate('footer'));
    }

    /**
     * Returns the template's HTML content.
     *
     * @return string HTML content
     */
    public function getContent()
    {
        $this->smarty->assign([
            'lgcookieslaw_user_consent' => $this->template_vars['lgcookieslaw_user_consent'],
            'lgcookieslaw_purposes' => $this->template_vars['lgcookieslaw_purposes'],
        ]);

        $this->smarty->assign([
            'info_tab' => $this->smarty->fetch($this->getLGTemplate('lgcookieslaw-user-consent.info-tab')),
            'purposes_tab' => $this->smarty->fetch($this->getLGTemplate('lgcookieslaw-user-consent.purposes-tab')),
        ]);

        return $this->smarty->fetch($this->getLGTemplate('lgcookieslaw-user-consent'));
    }

    /**
     * Returns the template filename.
     *
     * @return string filename
     */
    public function getFilename()
    {
        $lgcookieslaw_user_consent = $this->template_vars['lgcookieslaw_user_consent'];

        $user_consent_file_name = $lgcookieslaw_user_consent->download_hash . '.pdf';

        return $user_consent_file_name;
    }

    /**
     * Returns the template filename when using bulk rendering.
     *
     * @return string filename
     */
    public function getBulkFilename()
    {
        return $this->getFilename();
    }

    /**
     * If the template is not present in the theme directory, it will return the default template
     * in _PS_PDF_DIR_ directory.
     *
     * @param $template_name
     *
     * @return string
     */
    protected function getLGTemplate($template_name)
    {
        return _LGCOOKIESLAW_PDF_DIR_ . $template_name . '.tpl';
    }

    /**
     * Returns the template's HTML pagination block.
     *
     * @return string HTML pagination block
     */
    public function getPagination()
    {
        return $this->smarty->fetch($this->getTemplate('pagination'));
    }
}
