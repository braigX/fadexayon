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


require_once dirname(__FILE__) . '/../../classes/EtsRVMailLog.php';
require_once(dirname(__FILE__) . '/AdminEtsRVBaseController.php');

class AdminEtsRVQueueController extends AdminEtsRVBaseController
{
    public function __construct()
    {
        $this->table = 'ets_rv_email_queue';
        $this->list_id = $this->table;
        $this->className = 'EtsRvEmailQueue';
        $this->list_simple_header = false;
        $this->show_toolbar = false;
        $this->list_no_link = true;
        $this->_orderWay = 'DESC';

        parent::__construct();

        $this->addRowAction('sendmail');
        $this->addRowAction('delete');

        $this->_default_pagination = 20;
        $this->_select = '
            IF(a.schedule_time > 0, FROM_UNIXTIME(a.schedule_time, \'%Y-%m-%d %H:%i:%s\'), NULL) as schedule_date
            , content `content_html`
            , template `content`
            , CONCAT(a.`to_name`, \'' . pSQL(Tools::nl2br("\n"), true) . '\', a.`to_email`) as `customer`
        ';
        $this->_defaultOrderWay = 'DESC';

        $this->fields_list = [
            'id_ets_rv_email_queue' => [
                'title' => $this->l('ID', 'AdminEtsRVQueueController'),
                'align' => 'ets-rv-id_ets_rv_email_queue center',
                'class' => 'ets-rv-id_ets_rv_email_queue fixed-width-xs',
                'filter_key' => 'a!id_ets_rv_email_queue'
            ],
            'customer' => [
                'title' => $this->l('Customer', 'AdminEtsRVQueueController'),
                'align' => 'ets-rv-customer left',
                'class' => 'ets-rv-customer fixed-width-lg',
                'havingFilter' => true,
                'callback' => 'displayCustomerLink',
                'ref' => 'customer'
            ],
            'template' => [
                'title' => $this->l('Template', 'AdminEtsRVQueueController'),
                'align' => 'ets-rv-template left',
                'class' => 'ets-rv-template fixed-width-lg',
                'filter_key' => 'a!template'
            ],
            'subject' => [
                'title' => $this->l('Subject', 'AdminEtsRVQueueController'),
                'align' => 'ets-rv-template left',
                'class' => 'ets-rv-template',
                'filter_key' => 'a!subject'
            ],
            'content' => [
                'title' => $this->l('Content', 'AdminEtsRVQueueController'),
                'align' => 'ets-rv-content left',
                'filter_key' => 'content',
                'class' => 'ets-rv-content content_column',
                'search' => false,
                'orderby' => false,
                'callback' => 'displayContent',
            ],
            'send_count' => [
                'title' => $this->l('Trying times', 'AdminEtsRVQueueController'),
                'class' => 'ets-rv-send_count fixed-width-xs send_count',
                'align' => 'ets-rv-send_count center',
                'filter_key' => 'a!send_count',
            ],
            'schedule_date' => [
                'title' => $this->l('Schedule time', 'AdminEtsRVQueueController'),
                'align' => 'ets-rv-schedule_date center',
                'class' => 'ets-rv-schedule_date fixed-width-lg schedule_time_column',
                'type' => 'datetime',
                'havingFilter' => 'schedule_date'
            ],
            'date_add' => [
                'title' => $this->l('Queue at', 'AdminEtsRVQueueController'),
                'align' => 'ets-rv-date_add center',
                'class' => 'ets-rv-date_add fixed-width-lg queue_at_column',
                'type' => 'datetime',
                'filter_key' => 'a!date_add'
            ],
        ];

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected', 'AdminEtsRVQueueController'),
                'confirm' => $this->l('Do you want to delete selected items?', 'AdminEtsRVQueueController'),
                'icon' => 'icon-trash',
            )
        );
    }

    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub

        if ($this->ajax)
            $this->context->smarty->assign('link', $this->context->link);
    }

    public function ajaxProcessSendmail()
    {
        /** @var EtsRVEmailQueue $queue */
        $msg = null;
        if (!$queue = $this->loadObject()) {
            $this->errors[] = $this->l('Cannot send mail.', 'AdminEtsRVQueueController');
        } elseif (EtsRVUnsubscribe::isUnsubscribe($queue->to_email)) {
            if ($queue->delete())
                $msg = $this->l('Mail queue is clean because the user is unsubscribed!', 'AdminEtsRVQueueController');
            else
                $this->errors[] = $this->l('Cannot send mail.', 'AdminEtsRVQueueController');
        } else {
            if (!@glob($this->module->getLocalPath() . 'mails/' . Language::getIsoById($queue->id_lang) . '/' . $queue->template . '*[.txt|.html]')) {
                $this->module->_copyMailTmp(new Language($queue->id_lang));
            }

            $idShop = $queue->id_shop ?: $this->context->shop->id;
            $idLang = $queue->id_lang ?: (int)Configuration::get('PS_LANG_DEFAULT');
            $templateVars = $queue->template_vars ? json_decode($queue->template_vars, true) : [];
            $templateVars['{shop_url}'] = $this->context->link->getPageLink('index', true, $idLang, null, false, $idShop);
            $templateVars['{shop_name}'] = Tools::safeOutput(Configuration::get('PS_SHOP_NAME'));
            $templateVars['{content}'] = $queue->content;
            $queue->template_vars = $templateVars;

            EtsRVTools::getInstance()->replaceShortCode($templateVars, $idLang);
            EtsRVMailLog::writeLog($queue, EtsRVMailLog::SEND_MAIL_TIMEOUT);
            if (($delivered = Mail::send(
                    $idLang,
                    $queue->template,
                    $queue->subject,
                    $templateVars,
                    $queue->to_email,
                    $queue->to_name,
                    null, null, null, null,
                    $this->module->getLocalPath() . 'mails/',
                    false,
                    $idShop
                )) || EtsRVEmailQueue::getNbSentMailQueue($queue->id) > (int)Configuration::get('ETS_RV_CRONJOB_MAX_TRY')
            ) {
                EtsRVMailLog::writeLog($queue, EtsRVMailLog::SEND_MAIL_DELIVERED);
                if ($delivered)
                    EtsRVTracking::setDelivered($queue->id);
                if (!$queue->delete())
                    $this->errors[] = $this->l('Clean queue is failed.', 'AdminEtsRVQueueController');
            } else {
                EtsRVMailLog::writeLog($queue, EtsRVMailLog::SEND_MAIL_FAILED);
                $this->errors[] = $this->l('Sending mail failed.', 'AdminEtsRVQueueController');
            }
        }
        $has_error = count($this->errors) > 0 ? 1 : 0;
        die(json_encode([
            'errors' => $has_error ? Tools::nl2br(implode(PHP_EOL, $this->errors)) : false,
            'msg' => !$has_error ? ($msg !== null ? $msg : $this->l('Send mail successfully', 'AdminEtsRVQueueController')) : false,
            'html' => !$has_error ? $this->renderList() : false,
        ]));
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
        $templateVars['{content}'] = isset($tr['content_html']) ? $tr['content_html'] : '';

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
                if (preg_match('/'.'<'.'body(?:[^>]*?)>(.*?)<\/body>/s', $template, $matches) && !empty($matches[0])) {
                    $content = trim($matches[0]);
                    EtsRVTools::getInstance()->replaceShortCode($templateVars, $idLang);
                    $content = str_replace(array_keys($templateVars), array_values($templateVars), $content);

                    return strip_tags($content);
                }
            }
        }

        return '';
    }

    protected function getWhereClause()
    {
        if ($this->_filter)
            $this->_filter = preg_replace('/\s+AND\s+([a-z0-9A-Z]+)\.`(to_name)`\s+LIKE\s+\'%(.+?)%\'/', ' AND ($1.`$2` LIKE \'%$3%\' OR $1.`id_customer`=\'$3\') ', $this->_filter);

        return parent::getWhereClause();
    }

    static $st_customers = [];

    public function displayCustomerLink($to_name, $tr)
    {
        if (isset($tr['to_email']) && trim($tr['to_email']) !== '') {
            $cache_id = $tr['to_email'] . '|' . (int)$tr['employee'];
            if (isset(self::$st_customers[$cache_id]) && self::$st_customers[$cache_id])
                return self::$st_customers[$cache_id];
            $email = trim($tr['to_email']);
            if (Validate::isEmail($email)) {
                if ((int)$tr['employee'] < 1) {
                    $customer = (new Customer())->getByEmail($email);
                    $href = $customer instanceof Customer && $customer->id > 0 ? EtsRVLink::getAdminLink('AdminCustomers', true, $this->module->ps1760 ? ['route' => 'admin_customers_view', 'customerId' => $customer->id] : [], ['viewcustomer' => '', 'id_customer' => $customer->id], $this->context) : '';
                } else {
                    $employee = EtsRVModel::getEmployeeByEmail($email);
                    $href = $employee instanceof Employee && $employee->id > 0 ? EtsRVLink::getAdminLink('AdminEmployees', true, ($this->module->ps1760 ? ['route' => 'admin_employees_edit', 'employeeId' => $employee->id] : []), ['viewemployee' => '', 'id_employee' => $employee->id], $this->context) : '';
                }
            } else
                $href = '#';
            $attrs = [
                'href' => $href,
                'target' => '_bank',
                'title' => $to_name,
                'class' => 'ets_rv_customer_link',
            ];
            self::$st_customers[$cache_id] = EtsRVTools::displayText($to_name, 'a', $attrs);
            return self::$st_customers[$cache_id];
        }

        return $to_name;
    }

    public function displayDelivered($delivered)
    {
        $attrs = [
            'class' => 'badge delivered_' . ($delivered ? 'yes' : 'no'),
        ];
        return EtsRVTools::displayText(($delivered ? $this->l('Yes', 'AdminEtsRVQueueController') : $this->l('No', 'AdminEtsRVQueueController')), 'span', $attrs);
    }

    public function displaySendmailLink($token, $id)
    {
        if (!isset(self::$cache_lang['sendmail'])) {
            self::$cache_lang['sendmail'] = $this->l('Send mail', 'AdminEtsRVQueueController');
        }

        $this->context->smarty->assign(array(
            'href' => self::$currentIndex . '&sendmail&' . $this->identifier . '=' . $id . '&token=' . ($token != null ? $token : $this->token),
            'action' => self::$cache_lang['sendmail'],
        ));

        return $this->createTemplate('helpers/list/list_action_sendmail.tpl')->fetch();
    }
}