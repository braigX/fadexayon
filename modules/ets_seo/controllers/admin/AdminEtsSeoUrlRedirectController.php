<?php
/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Class AdminEtsSeoUrlRedirectController
 *
 * @property \Ets_Seo $module
 *
 * @mixin \ModuleAdminControllerCore
 */
class AdminEtsSeoUrlRedirectController extends ModuleAdminController
{
    /**
     * @var array
     */
    public $_orgList;

    /**
     * __construct.
     *
     * @return void
     */
    public function __construct()
    {
        $this->table = 'ets_seo_redirect';
        $this->className = 'EtsSeoRedirect';
        $this->bootstrap = true;

        parent::__construct();

        $seoDef = Ets_Seo_Define::getInstance();
        $this->fields_options = [
            'setting' => [
                'title' => $this->module->l('Settings', 'AdminEtsSeoUrlRedirectController'),
                'fields' => $seoDef->fields_config()['url_redirect_setting'],
                'icon' => '',
                'submit' => [
                    'title' => $this->module->l('Save', 'AdminEtsSeoUrlRedirectController'),
                ],
            ],
        ];
        $this->bulk_actions = [
            'delete' => [
                'text' => $this->module->l('Delete selected', 'AdminEtsSeoUrlRedirectController'),
                'confirm' => $this->module->l('Delete selected items?', 'AdminEtsSeoUrlRedirectController'),
                'icon' => 'icon-trash',
            ],
        ];
        $this->fields_value['id_shop'] = $this->context->shop->id;
        $this->fields_list = [
            'id_ets_seo_redirect' => [
                'title' => $this->module->l('ID', 'AdminEtsSeoUrlRedirectController'),
                'align' => 'center',
                'filter_type' => 'int',
                'remove_onclick' => true,
            ],
            'name' => [
                'title' => $this->module->l('Redirect name', 'AdminEtsSeoUrlRedirectController'),
                'align' => 'center',
                'remove_onclick' => true,
            ],
            'url' => [
                'title' => $this->module->l('Source URL', 'AdminEtsSeoUrlRedirectController'),
                'align' => 'center',
                'float' => true,
                'remove_onclick' => true,
            ],
            'target' => [
                'title' => $this->module->l('Target URL', 'AdminEtsSeoUrlRedirectController'),
                'align' => 'center',
                'float' => true,
                'remove_onclick' => true,
            ],
            'type' => [
                'title' => $this->module->l('Redirect type', 'AdminEtsSeoUrlRedirectController'),
                'align' => 'center',
                'type' => 'select',
                'filter_key' => 'type',
                'list' => [
                    '301' => '301',
                    '302' => '302',
                    '303' => '303',
                ],
            ],
            'active' => [
                'title' => $this->module->l('Active', 'AdminEtsSeoUrlRedirectController'),
                'align' => 'center',
                'type' => 'bool',
                'filter_key' => 'active',
                'active' => 'status',
                'remove_onclick' => true,
            ],
        ];
        $id_redirect = (int) Tools::getValue('id_ets_seo_redirect');
        $this->fields_form = [
            'legend' => [
                'title' => Tools::getIsset('updateets_seo_redirect') ? $this->module->l('Edit', 'AdminEtsSeoUrlRedirectController') . ($id_redirect ? ' #' . (int) $id_redirect : '') : $this->module->l('Add new', 'AdminEtsSeoUrlRedirectController'),
                'icon' => '',
            ],
            'input' => [
                [
                    'type' => 'hidden',
                    'name' => 'id_shop',
                    'required' => true,
                    'validate' => 'isString',
                ],
                [
                    'type' => 'text',
                    'name' => 'name',
                    'label' => $this->module->l('Redirect name (optional)', 'AdminEtsSeoUrlRedirectController'),
                    'validate' => 'isString',
                ],
                [
                    'type' => 'text',
                    'name' => 'url',
                    'label' => $this->module->l('Source URL', 'AdminEtsSeoUrlRedirectController'),
                    'prefix' => $this->context->shop->getBaseURL(true, true),
                    'required' => true,
                    'validate' => 'isString',
                ],
                [
                    'type' => 'text',
                    'name' => 'target',
                    'label' => $this->module->l('Target URL', 'AdminEtsSeoUrlRedirectController'),
                    'required' => true,
                    'validate' => 'isAbsoluteUrl',
                ],
                [
                    'type' => 'select',
                    'name' => 'type',
                    'label' => $this->module->l('Redirect type', 'AdminEtsSeoUrlRedirectController'),
                    'validate' => 'isString',
                    'default_value' => '301',
                    'options' => [
                        'id' => 'type',
                        'name' => 'name',
                        'query' => [
                            [
                                'name' => $this->module->l('301 Moved Permanently (recommended once you have gone live)', 'AdminEtsSeoUrlRedirectController'),
                                'type' => '301',
                            ],
                            [
                                'name' => $this->module->l('302 Moved Temporarily (recommended while setting up your store)', 'AdminEtsSeoUrlRedirectController'),
                                'type' => '302',
                            ],
                            [
                                'name' => $this->module->l('303 Do not link to the newly uploaded resources (for advanced user only)', 'AdminEtsSeoUrlRedirectController'),
                                'type' => '303',
                            ],
                        ],
                    ],
                ],

                [
                    'type' => 'switch',
                    'name' => 'active',
                    'validate' => 'isBool',
                    'is_bool' => true,
                    'label' => $this->module->l('Active', 'AdminEtsSeoUrlRedirectController'),
                    'values' => [
                        [
                            'id' => 'ets_seo_redirect_active_1',
                            'value' => 1,
                        ],
                        [
                            'id' => 'ets_seo_redirect_active_0',
                            'value' => 0,
                        ],
                    ],
                    'default_value' => '1',
                ],
            ],
            'submit' => [
                'title' => $this->module->l('Save', 'AdminEtsSeoUrlRedirectController'),
            ],
        ];

        $this->actions = ['edit', 'delete'];
        $this->_where = ' AND `id_shop`=' . $this->context->shop->id;

        if (!Module::isEnabled('ets_seo')) {
            $this->warnings[] = $this->module->l('You must enable module SEO Audit to configure its features', 'AdminEtsSeoUrlRedirectController');
        }
    }

    public function initPageHeaderToolbar()
    {
        if ('add' !== $this->display) {
            $this->page_header_toolbar_btn['new_redirect'] = [
                'href' => self::$currentIndex . '&addets_seo_redirect&token=' . $this->token,
                'desc' => $this->module->l('Add new url redirect', 'AdminEtsSeoUrlRedirectController'),
                'icon' => 'process-icon-new',
            ];
        }

        parent::initPageHeaderToolbar();
    }

    public function getList(
        $id_lang,
        $order_by = null,
        $order_way = null,
        $start = 0,
        $limit = null,
        $id_lang_shop = false
    ) {
        parent::getList($id_lang, $order_by, $order_way, $start, $limit, $id_lang_shop);
        $this->_orgList = $this->_list;
        foreach ($this->_list as &$item) {
            $item['url'] = $this->context->shop->getBaseURL(true, true) . $item['url'];
            $item['url'] = $this->getLinkUrlRedirect($item, 'url');
            $item['target'] = $this->getLinkUrlRedirect($item);
        }
    }

    private function getLinkUrlRedirect($item, $type = 'target')
    {
        if (!isset($item[$type])) {
            return '';
        }
        $uri = preg_match('/http(s?):\/\//', $item[$type]) ? $item[$type] : 'http://' . $item[$type];

        return EtsSeoStrHelper::displayText($item[$type], 'a', ['href' => $uri, 'target' => '_blank', 'class' => 'ets_seo_admin_redirect_target']);
    }

    public function postProcess()
    {
        if (isset($this->context->cookie->ets_seo_redirect_errors)) {
            $this->errors = [$this->context->cookie->__get('ets_seo_redirect_errors')];
            $this->context->cookie->__unset('ets_seo_redirect_errors');
        }
        if (Tools::isSubmit('submitAddets_seo_redirect')) {
            $id = (int) Tools::getValue('id_ets_seo_redirect');
            if (isset($this->fields_form['input']) && $this->fields_form['input']) {
                foreach ($this->fields_form['input'] as $config) {
                    $val = Tools::getValue($config['name']);
                    if (isset($config['required']) && $config['required'] && !$val) {
                        $this->errors[] = $this->module->l('The', 'AdminEtsSeoUrlRedirectController') . ' ' . $config['name'] . ' ' . $this->module->l('is required', 'AdminEtsSeoUrlRedirectController');
                    }
                    if ('target' == $config['name'] && !Validate::isAbsoluteUrl($val)) {
                        $this->errors[] = $this->module->l('The', 'AdminEtsSeoUrlRedirectController') . ' ' . $config['name'] . ' ' . $this->module->l('must be an url', 'AdminEtsSeoUrlRedirectController');
                    } elseif ($val && !Validate::isCleanHtml($val)) {
                        $this->errors[] = sprintf($this->module->l('The %s is not valid', 'AdminEtsSeoUrlRedirectController'), $config['name']);
                    }
                }
            }
            if (!$this->errors) {
                $url = Tools::getValue('url');
                $url = urldecode(ltrim($url, '/'));
                if (!$url) {
                    $this->errors[] = $this->module->l('The Source URL is required', 'AdminEtsSeoUrlRedirectController');
                }
                if (preg_match('#^https?://#i', $url)) {
                    $this->errors[] = $this->module->l('The Source URL must be a relative path from your shop base (does not have http:// or https://).', 'AdminEtsSeoUrlRedirectController');
                }
                $full_url = rtrim($this->context->shop->getBaseURL(true, true), '/') . '/' . $url;
                if ('' === $url || '*' === $url || !(preg_match('#^https?:/#i', $full_url) && Validate::isUrl($full_url))) {
                    $this->errors[] = $this->module->l('The Source URL is not valid', 'AdminEtsSeoUrlRedirectController');
                }
                $url = ltrim($url, ' /');
                $_POST['url'] = $url;
                if ($url) {
                    if (EtsSeoRedirect::checkSeoUrl($url, $id)) {
                        $this->errors[] = $this->module->l('The URL has been taken.', 'AdminEtsSeoUrlRedirectController');
                    }
                }
                $target = ($target = Tools::getValue('target')) && Validate::isAbsoluteUrl($target) ? $target : '';
                if ($target && $full_url == trim($target)) {
                    $this->errors[] = $this->module->l('The target URL cannot be the same source URL', 'AdminEtsSeoUrlRedirectController');
                }
                $this->module->_clearCache('*');
            }
            if ($this->errors) {
                if ($id) {
                    $link = $this->context->link->getAdminLink('AdminEtsSeoUrlRedirect', true) . '&updateets_seo_redirect&id_ets_seo_redirect=' . $id;
                } else {
                    $link = $this->context->link->getAdminLink('AdminEtsSeoUrlRedirect', true) . '&addets_seo_redirect';
                }
                $this->context->cookie->__set('ets_seo_redirect_errors', $this->errors[0]);
                $this->context->cookie->__set('ets_seo_redirect_values', json_encode([
                    'name' => ($name = Tools::getValue('name')) && Validate::isCleanHtml($name) ? $name : '',
                    'url' => ($url = Tools::getValue('url', '')) && Validate::isCleanHtml($url) ? $url : '',
                    'target' => ($target = Tools::getValue('target', '')) && Ets_Seo::isLink($target) ? $target : '',
                    'type' => ($type = Tools::getValue('type', '')) && Validate::isCleanHtml($type) ? $type : '',
                    'active' => ($active = Tools::getValue('active', '')) && Validate::isCleanHtml($active) ? $active : '',
                    'id_shop' => ($id_shop = Tools::getValue('id_shop', '')) && Validate::isUnsignedId($id_shop) ? $id_shop : '',
                ]));
                Tools::redirect($link);
                exit;
            }
        }

        return parent::postProcess();
    }

    public function init()
    {
        if ('GET' === $_SERVER['REQUEST_METHOD'] && Tools::isSubmit('addets_seo_redirect') && Tools::getValue('url')) {
            $id = EtsSeoRedirect::getIdByUrl(Tools::getValue('url'), $this->context->shop->id);
            if ($id) {
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminEtsSeoUrlRedirect', true, [], ['updateets_seo_redirect' => 1, 'id_ets_seo_redirect' => $id]));
                exit;
            }
        }
        parent::init();
    }

    public function setHelperDisplay(Helper $helper)
    {
        parent::setHelperDisplay($helper);
        $helper->title = $this->module->l('URL redirects', 'AdminEtsSeoUrlRedirectController');
    }

    public function renderList()
    {
        $form = parent::renderOptions();
        $this->display = 'list';
        $this->initToolbar();
        if (isset($this->toolbar_btn['save'])) {
            unset($this->toolbar_btn['save']);
        }

        return $form . parent::renderList();
    }

    public function renderOptions()
    {
        $this->fields_options = [];

        return parent::renderOptions();
    }
}
