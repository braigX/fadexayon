<?php
/**
* Since 2013 Ovidiu Cimpean.
*
* Ovidiu Cimpean - Newsletter Pro © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at addons4prestashop@gmail.com.
*
* @author    Ovidiu Cimpean <addons4prestashop@gmail.com>
* @copyright Since 2013 Ovidiu Cimpean
* @license   Do not edit, modify or copy this file
*
* @version   Release: 4
*/

if (!defined('_PS_VERSION_')) {
	exit;
}

class NewsletterProApiCss extends NewsletterProApi
{
    public function call()
    {
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: text/css');

        @ob_clean();
        @ob_end_clean();

        $context = Context::getContext();

        if ($this->request->has('getSubscriptionCSS')) {
            $idTemplate = $this->request->get('idTemplate');

            if ($this->request->has('idShop')) {
                $id_shop = (int) $this->request->get('idShop');
                $shop = Shop::getShop($id_shop);

                if ($shop) {
                    $context->shop = new Shop((int) $shop['id_shop']);
                }
            }

            $template = new NewsletterProSubscriptionTpl((int) $idTemplate);
            if (Validate::isLoadedObject($template)) {
                exit((string) $template->css_style);
            }
        } elseif ($this->request->has('getNewsletterTemplateCSS') && $this->request->has('name')) {
            $templateName = $this->request->get('name');
            $idLang = (int) $this->request->get('id_lang');

            try {
                $template = NewsletterProTemplate::newFile($templateName)->load($idLang);
                exit($template->css());
            } catch (Exception $e) {
                pqnp_log()->write($e->__toString(), NewsletterProLog::ERROR_FILE);
            }
        }

        return $this->output->render();
    }
}
