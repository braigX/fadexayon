<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class Assetsversion extends Module
{
    const CONFIG_JS_VERSION = 'ASSETSVERSION_JS_VERSION';
    const CONFIG_CSS_VERSION = 'ASSETSVERSION_CSS_VERSION';

    public function __construct()
    {
        $this->name = 'assetsversion';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Novatis';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->ps_versions_compliancy = array(
            'min' => '1.7.0.0',
            'max' => _PS_VERSION_,
        );

        parent::__construct();

        $this->displayName = $this->l('Assets version');
        $this->description = $this->l('Adds configurable version parameters to local CSS and JS asset URLs.');
    }

    public function install()
    {
        return parent::install()
            && Configuration::updateValue(self::CONFIG_JS_VERSION, '1')
            && Configuration::updateValue(self::CONFIG_CSS_VERSION, '1')
            && $this->registerHook('actionOutputHTMLBefore');
    }

    public function uninstall()
    {
        return Configuration::deleteByName(self::CONFIG_JS_VERSION)
            && Configuration::deleteByName(self::CONFIG_CSS_VERSION)
            && parent::uninstall();
    }

    public function getContent()
    {
        $output = '';

        if (Tools::isSubmit('submitAssetsVersionSettings')) {
            Configuration::updateValue(self::CONFIG_JS_VERSION, $this->sanitizeVersionValue(Tools::getValue(self::CONFIG_JS_VERSION)));
            Configuration::updateValue(self::CONFIG_CSS_VERSION, $this->sanitizeVersionValue(Tools::getValue(self::CONFIG_CSS_VERSION)));
            $output .= $this->displayConfirmation($this->l('Settings updated.'));
        }

        return $output . $this->renderForm();
    }

    public function hookActionOutputHTMLBefore(array $params)
    {
        if (empty($params['html']) || !is_string($params['html'])) {
            return;
        }

        $html = $params['html'];
        $jsVersion = $this->sanitizeVersionValue(Configuration::get(self::CONFIG_JS_VERSION));
        $cssVersion = $this->sanitizeVersionValue(Configuration::get(self::CONFIG_CSS_VERSION));

        if ($jsVersion !== '') {
            $html = preg_replace_callback(
                '/(<script\b[^>]*\bsrc=)(["\'])([^"\']+)(\2[^>]*>)/i',
                function ($matches) use ($jsVersion) {
                    $url = $this->appendVersionToAssetUrl($matches[3], 'js', $jsVersion);
                    return $matches[1] . $matches[2] . $url . $matches[4];
                },
                $html
            );
        }

        if ($cssVersion !== '') {
            $html = preg_replace_callback(
                '/(<link\b[^>]*\bhref=)(["\'])([^"\']+)(\2[^>]*>)/i',
                function ($matches) use ($cssVersion) {
                    $url = $this->appendVersionToAssetUrl($matches[3], 'css', $cssVersion);
                    return $matches[1] . $matches[2] . $url . $matches[4];
                },
                $html
            );
        }

        $params['html'] = $html;
    }

    protected function renderForm()
    {
        $fieldsForm = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Assets version'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('JS version'),
                        'name' => self::CONFIG_JS_VERSION,
                        'required' => false,
                        'desc' => $this->l('Appended as the jsv query parameter on local JavaScript assets.'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('CSS version'),
                        'name' => self::CONFIG_CSS_VERSION,
                        'required' => false,
                        'desc' => $this->l('Appended as the cssv query parameter on local stylesheet assets.'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'name' => 'submitAssetsVersionSettings',
                ),
            ),
        );

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->submit_action = 'submitAssetsVersionSettings';
        $helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');
        $helper->allow_employee_form_lang = (int) Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->fields_value = array(
            self::CONFIG_JS_VERSION => Tools::getValue(self::CONFIG_JS_VERSION, Configuration::get(self::CONFIG_JS_VERSION)),
            self::CONFIG_CSS_VERSION => Tools::getValue(self::CONFIG_CSS_VERSION, Configuration::get(self::CONFIG_CSS_VERSION)),
        );

        return $helper->generateForm(array($fieldsForm));
    }

    protected function appendVersionToAssetUrl($url, $type, $version)
    {
        $url = (string) $url;
        if (!$this->shouldVersionAssetUrl($url, $type)) {
            return $url;
        }

        $fragment = '';
        $hashPos = strpos($url, '#');
        if ($hashPos !== false) {
            $fragment = substr($url, $hashPos);
            $url = substr($url, 0, $hashPos);
        }

        $parts = parse_url($url);
        if ($parts === false) {
            return $url . $fragment;
        }

        $query = array();
        if (!empty($parts['query'])) {
            parse_str($parts['query'], $query);
        }

        $query[$type === 'css' ? 'cssv' : 'jsv'] = $version;
        $parts['query'] = http_build_query($query);

        return $this->buildUrlFromParts($parts) . $fragment;
    }

    protected function shouldVersionAssetUrl($url, $type)
    {
        $url = trim((string) $url);
        if ($url === '' || strpos($url, 'data:') === 0) {
            return false;
        }

        $cleanUrl = preg_replace('/[#?].*$/', '', $url);
        $extension = strtolower((string) pathinfo($cleanUrl, PATHINFO_EXTENSION));
        if ($extension !== $type) {
            return false;
        }

        $parts = parse_url($url);
        if ($parts === false) {
            return false;
        }

        if (!isset($parts['host']) && !isset($parts['scheme'])) {
            return true;
        }

        $allowedHosts = array_filter(array_unique(array(
            Tools::getShopDomain(),
            Tools::getShopDomainSsl(),
            parse_url($this->context->shop->getBaseURL(true), PHP_URL_HOST),
            parse_url($this->context->shop->getBaseURL(false), PHP_URL_HOST),
        )));

        return isset($parts['host']) && in_array(strtolower($parts['host']), array_map('strtolower', $allowedHosts));
    }

    protected function buildUrlFromParts(array $parts)
    {
        $url = '';

        if (!empty($parts['scheme'])) {
            $url .= $parts['scheme'] . '://';
        } elseif (isset($parts['host'])) {
            $url .= '//';
        }

        if (!empty($parts['user'])) {
            $url .= $parts['user'];
            if (isset($parts['pass'])) {
                $url .= ':' . $parts['pass'];
            }
            $url .= '@';
        }

        if (!empty($parts['host'])) {
            $url .= $parts['host'];
        }

        if (!empty($parts['port'])) {
            $url .= ':' . (int) $parts['port'];
        }

        if (!empty($parts['path'])) {
            $url .= $parts['path'];
        }

        if (isset($parts['query']) && $parts['query'] !== '') {
            $url .= '?' . $parts['query'];
        }

        return $url;
    }

    protected function sanitizeVersionValue($value)
    {
        $value = trim((string) $value);
        return preg_replace('/[^A-Za-z0-9._-]/', '', $value);
    }
}
