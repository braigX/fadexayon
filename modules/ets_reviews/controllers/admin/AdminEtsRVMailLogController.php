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

if (!defined('_PS_VERSION_')) { exit; }

require_once dirname(__FILE__) . '/../../classes/EtsRVMailLog.php';
require_once(dirname(__FILE__) . '/AdminEtsRVBaseController.php');

class AdminEtsRVMailLogController extends AdminEtsRVBaseController
{
    static $status_mail;

    public function __construct()
    {
        $this->table = 'ets_rv_mail_log';
        $this->className = 'EtsRVMailLog';
        $this->list_id = $this->table;
        $this->identifier = 'id_ets_rv_mail_log';

        $this->allow_export = false;
        $this->_redirect = false;
        $this->list_no_link = true;
        $this->lang = false;
        $this->_orderWay = 'DESC';


        parent::__construct();

        $this->addRowAction('view');
        $this->addRowAction('delete');

        $this->_select = 'template `content`, CONCAT(a.`to_name`, \''.pSQL(Tools::nl2br("\n"), true).'\', a.`to_email`) as `customer`';
        if (Shop::getContext() !== Shop::CONTEXT_ALL)
            $this->_where = 'AND a.id_shop = ' . (int)$this->context->shop->id;

        if (!self::$status_mail) {
            self::$status_mail = [
                EtsRVMailLog::SEND_MAIL_FAILED => $this->l('Failed', 'AdminEtsRVMailLogController'),
                EtsRVMailLog::SEND_MAIL_DELIVERED => $this->l('Delivered', 'AdminEtsRVMailLogController'),
                EtsRVMailLog::SEND_MAIL_TIMEOUT => $this->l('Timeout', 'AdminEtsRVMailLogController')
            ];
        }

        $this->fields_list = array(
            'id_ets_rv_email_queue' => array(
                'title' => $this->l('Queue ID', 'AdminEtsRVMailLogController'),
                'type' => 'int',
                'filter_key' => 'a!id_ets_rv_email_queue',
                'class' => 'ets-rv-id_ets_rv_email_queue fixed-width-xs center id_ets_rv_email_queue',
                'align' => 'ets-rv-id_ets_rv_email_queue',
            ),
            'subject' => array(
                'title' => $this->l('Subject', 'AdminEtsRVMailLogController'),
                'type' => 'text',
                'filter_key' => 'a!subject',
                'class' => 'ets-rv-subject subject',
                'align' => 'ets-rv-subject',
            ),
            'content' => array(
                'title' => $this->l('Content', 'AdminEtsRVMailLogController'),
                'type' => 'text',
                'filter_key' => 'content',
                'havingFilter' => true,
                'callback' => 'displayContent',
                'class' => 'ets-rv-content content',
                'align' => 'ets-rv-content',
            ),
            'customer' => array(
                'title' => $this->l('Customer', 'AdminEtsRVMailLogController'),
                'type' => 'text',
                'havingFilter' => true,
                'callback' => 'buildFieldCustomerLink',
                'class' => 'ets-rv-customer customer',
                'align' => 'ets-rv-customer',
            ),
            'sent_time' => array(
                'title' => $this->l('Time sent', 'AdminEtsRVMailLogController'),
                'type' => 'datetime',
                'align' => 'ets-rv-sent_time center',
                'filter_key' => 'a!sent_time',
                'class' => 'ets-rv-sent_time fixed-width-lg sent_time',
            ),
            'status' => array(
                'title' => $this->l('Status', 'AdminEtsRVMailLogController'),
                'type' => 'select',
                'list' => self::$status_mail,
                'filter_key' => 'a!status',
                'callback' => 'displayStatus',
                'class' => 'ets-rv-status fixed-width-xs center status',
                'align' => 'ets-rv-status',
            ),
        );

        $this->_conf[1] = $this->l('Clean log successfully', 'AdminEtsRVMailLogController');
    }

    public function renderList()
    {
        return $this->renderButtonClean() . parent::renderList();
    }

    public function processCleanLog()
    {
        if (!EtsRVMailLog::cleanLog())
            $this->errors[] = $this->l('Clean log failed!', 'AdminEtsRVMailLogController');
        if (!$this->errors) {
            $this->redirect_after = self::$currentIndex . '&conf=1&token=' . $this->token;
        }
    }

    public function renderButtonClean()
    {
        $this->context->smarty->assign([
            'href' => self::$currentIndex . '&action=cleanLog&token=' . $this->token,
        ]);
        return $this->context->smarty->fetch($this->module->getLocalPath() . 'views/templates/admin/btn-clean-log.tpl');
    }

    public function ajaxProcessRenderView()
    {
        die(json_encode([
            'html' => $this->renderView(),
        ]));
    }

    public function renderView()
    {
        $id = Tools::getValue($this->identifier);
        if ($id < 1)
            return '';
        $object = EtsRVMailLog::getLog($id);
        if (isset($object['template']) && trim($object['template']) !== '') {
            $object['html'] = true;
            $object['content'] = $this->displayContent($object['template'], $object);
        }
        $this->context->smarty->assign([
            'object' => $object,
        ]);
        return parent::renderView();
    }

    public function displayStatus($status)
    {
        $attrs = [
            'class' => 'ets-rv-status-mail status-mail' . $status,
        ];
        return EtsRVTools::displayText(self::$status_mail[$status], 'span', $attrs);
    }

    protected function getWhereClause()
    {
        if ($this->_filter)
            $this->_filter = preg_replace('/\s+AND\s+([a-z0-9A-Z]+)\.`(customer_name)`\s+LIKE\s+\'%(.+?)%\'/', ' AND ($1.`$2` LIKE \'%$3%\' OR $1.`id_customer`=\'$3\') ', $this->_filter);

        return parent::getWhereClause();
    }

    public function displayContent($template, $tr)
    {
        if (trim($template) == '')
            return '';

        $idShop = isset($tr['id_shop ']) && $tr['id_shop '] > 0 ? (int)$tr['id_shop '] : $this->context->shop->id;
        $idLang = isset($tr['id_lang']) && $tr['id_lang'] > 0 ? (int)$tr['id_lang'] : (int)Configuration::get('PS_LANG_DEFAULT');

        $templateVars = isset($tr['template_vars']) ? json_decode($tr['template_vars'], true) : [];
        $templateVars['{shop_url}'] = $this->context->link->getPageLink('index', true, $idLang, null, false, $idShop);
        $templateVars['{shop_name}'] = Tools::safeOutput(Configuration::get('PS_SHOP_NAME'));
        if (!empty($tr['content'])) {
            $templateVars['{content}'] = $tr['content'];
        } elseif (!isset($templateVars['{content}'])) {
            $templateVars['{content}'] = '';
        }
        $shop = new Shop($idShop, $idLang);
        $theme = $this->module->is17 ? $shop->theme->getName() : $shop->getTheme();
        $basePathList = array(
            _PS_ROOT_DIR_ . '/themes/' . $theme . '/modules/' . $this->module->name . '/mails/',
            $this->module->getLocalPath() . 'mails/',
        );
        foreach ($basePathList as $path) {
            $iso_path = $path . $this->context->language->iso_code . '/' . $tr['template'];
            if (@file_exists($iso_path . '.html')) {
                $template = Tools::file_get_contents($iso_path . '.html');
                if (preg_match('/'.'<'.'body(?:[^>]*?)>(.*?)<\/body>/s', $template, $matches) && !empty($matches[1])) {
                    $content = trim($matches[1]);
                    EtsRVTools::getInstance()->replaceShortCode($templateVars, $idLang);
                    $content = str_replace(array_keys($templateVars), array_values($templateVars), $content);
                    return isset($tr['html']) && $tr['html'] ? $content : strip_tags($content);
                }
            }
        }

        return '';
    }

    public function initToolbar()
    {
        parent::initToolbar();
        unset($this->toolbar_btn['new']);
    }
}