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
 * Class AdminEtsSeoNotFoundUrlController.
 *
 * @property \Ets_Seo $module
 * @property \Context|\ContextCore $context
 *
 */
class AdminEtsSeoNotFoundUrlController extends ModuleAdminController
{
    /**
     * @var array
     */
    private $_orgList;

    public function __construct()
    {
        $this->table = 'ets_seo_not_found_url';
        $this->className = 'EtsSeoNotFoundUrl';
        $this->bootstrap = true;
        $this->show_page_header_toolbar = true;
        parent::__construct();
        $this->bulk_actions = [
            'delete' => [
                'text' => $this->module->l('Delete selected', 'AdminEtsSeoNotFoundUrlController'),
                'confirm' => $this->module->l('Delete selected items?', 'AdminEtsSeoNotFoundUrlController'),
                'icon' => 'icon-trash',
            ],
        ];
        $this->fields_value['id_shop'] = $this->context->shop->id;
        $this->fields_list = [
            'id_ets_seo_not_found_url' => [
                'title' => $this->module->l('ID', 'AdminEtsSeoNotFoundUrlController'),
                'align' => 'center',
                'filter_type' => 'int',
                'remove_onclick' => true,
            ],
            'url' => [
                'title' => $this->module->l('URL', 'AdminEtsSeoNotFoundUrlController'),
                'align' => 'center',
                'float' => true,
                'remove_onclick' => true,
            ],
            'referer' => [
                'title' => $this->module->l('Source URL', 'AdminEtsSeoNotFoundUrlController'),
                'align' => 'center',
                'float' => true,
                'remove_onclick' => true,
            ],
            'visit_count' => [
                'title' => $this->module->l('Visit Count', 'AdminEtsSeoNotFoundUrlController'),
                'align' => 'center',
                'type' => 'int',
                'remove_onclick' => true,
            ],
            'last_visited_at' => [
                'title' => $this->module->l('Last Visit', 'AdminEtsSeoNotFoundUrlController'),
                'align' => 'center',
                'type' => 'datetime',
                'remove_onclick' => true,
            ],
        ];
        $this->actions = ['addRedirect', 'delete'];
        $this->_where = ' AND `id_shop`=' . $this->context->shop->id;
        $this->_orderWay = 'desc';
    }

    /**
     * @param int $id
     *
     * @return array|null
     */
    private function getItemFromList($id)
    {
        $key = 'id_ets_seo_not_found_url';

        return current(array_filter($this->_orgList, static function ($v) use ($id, $key) {
            return $v[$key] == $id;
        }));
    }

    public function initContent()
    {
        $this->module->getJsDefHelper()->addBo('notFoundCtlUrl', $this->context->link->getAdminLink('AdminEtsSeoNotFoundUrl'));
        if (Tools::isSubmit('getSettingBtn')) {
            $value = (bool) Configuration::get('ETS_SEO_ENABLE_RECORD_404_REQUESTS');
            $this->context->smarty->assign(['inpName' => 'ETS_SEO_ENABLE_RECORD_404_REQUESTS', 'inpValue' => $value]);
            $html = $this->module->display($this->module->getLocalPath(), 'parts/_switch_input.tpl');
            $html .= EtsSeoStrHelper::displayText($this->module->l('Record 404 pages (Should be enabled for debugging)', 'AdminEtsSeoNotFoundUrlController'), 'small');
            header('Content-Type: application/json');
            exit(json_encode(['html' => $html, 'ok' => true]));
        }
        if (Tools::isSubmit('setSwitchValue')) {
            Configuration::updateValue('ETS_SEO_ENABLE_RECORD_404_REQUESTS', (int) Tools::getValue('value'));
            header('Content-Type: application/json');
            exit(json_encode(['newValue' => (bool) Tools::getValue('value'), 'ok' => true]));
        }
        parent::initContent();
    }

    /**
     * Get the current objects' list form the database.
     *
     * @param int $id_lang Language used for display
     * @param string|null $order_by ORDER BY clause
     * @param string|null $order_way Order way (ASC, DESC)
     * @param int $start Offset in LIMIT clause
     * @param int|null $limit Row count in LIMIT clause
     * @param int|bool $id_lang_shop
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
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
            $item['url'] = $this->buildClickableUri($item['url']);
            if (!$item['referer']) {
                $item['referer'] = '--';
            }
        }
    }

    /**
     * @param string $uri
     *
     * @return string
     */
    private function buildClickableUri($uri)
    {
        static $shopBaseUrl;
        if (!$shopBaseUrl) {
            $shopBaseUrl = $this->context->shop->getBaseURL(true);
        }

        return EtsSeoStrHelper::displayText($uri, 'a', ['href' => $shopBaseUrl . $uri, 'target' => '_blank']);
    }

    /**
     * @param $token
     * @param int $id
     *
     * @return false|string
     *
     * @throws \SmartyException
     */
    public function displayAddRedirectLink($token, $id)
    {
        if ($item = $this->getItemFromList($id)) {
            $cacheId = $this->module->_getCacheId(['displayAddRedirectLink' => $id]);
            $path = $this->context->smarty->getTemplateDir(0) . 'helpers/list/list_action_edit.tpl';

            if (!$this->module->isCached($path, $cacheId)) {
                if (!array_key_exists('Add Redirect', self::$cache_lang)) {
                    self::$cache_lang['Add Redirect'] = $this->module->l('Add Redirect', 'AdminEtsSeoNotFoundUrlController');
                }
                if (!array_key_exists('Update Redirect', self::$cache_lang)) {
                    self::$cache_lang['Update Redirect'] = $this->module->l('Update Redirect', 'AdminEtsSeoNotFoundUrlController');
                }
                $linkParams = ['url' => $item['url'], 'addets_seo_redirect' => 1];
                $smartyVar = [
                    'action' => self::$cache_lang['Add Redirect'],
                    'id' => $id,
                    'href' => $this->context->link->getAdminLink('AdminEtsSeoUrlRedirect', true, [], $linkParams),
                ];
                $this->context->smarty->assign($smartyVar);
            }

            return $this->module->fetch($path, $cacheId);
        }

        return '';
    }
    public function setHelperDisplay(Helper $helper)
    {
        parent::setHelperDisplay($helper);
        $helper->title = $this->module->l('404 Monitor', 'AdminEtsSeoNotFoundUrlController');
        $helper->toolbar_btn = [];
    }
}
