<?php
/**
 * Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
 *
 *    @author    Jpresta
 *    @copyright Jpresta
 *    @license   See the license of this module in file LICENSE.txt, thank you.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class FrontController extends FrontControllerCore
{
    protected function displayAjax()
    {
        if (!Tools::getIsset('page_cache_dynamics_mods')) {
            if (is_callable('parent::displayAjax')) {
                // The displayAjax is overrided, we call it
                return parent::displayAjax();
            } else {
                return;
            }
        }
        $this->initHeader();
        $this->assignGeneralPurposeVariables();
        require_once _PS_MODULE_DIR_ . 'jprestaspeedpack/jprestaspeedpack.php';
        $result = Jprestaspeedpack::execDynamicHooks($this);
        if (Tools::version_compare(_PS_VERSION_, '1.6', '>')) {
            $this->context->smarty->assign([
                'js_def' => Jprestaspeedpack::getJsDef($this),
            ]);
            $result['js'] = $this->context->smarty->fetch(_PS_ALL_THEMES_DIR_ . 'javascript.tpl');
        }
        $this->context->cookie->write();
        // Most of configurations to compress requests do not include 'application/json' so we use 'text/html'
        header('Content-Type: text/html');
        header('Cache-Control: no-cache');
        header('X-Robots-Tag: noindex');
        $json = json_encode($result);
        if ($json === false) {
            header('HTTP/1.1 500 Internal Server Error');
            exit(json_last_error_msg());
        }
        exit($json);
    }

    public function isRestrictedCountry()
    {
        return $this->restrictedCountry;
    }

    public function geolocationManagementPublic($default_country)
    {
        $ret = $this->geolocationManagement($default_country);
        if (!$ret) {
            return $default_country;
        }

        return $ret;
    }
}
