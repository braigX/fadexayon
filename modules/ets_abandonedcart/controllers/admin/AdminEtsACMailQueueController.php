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


require_once(dirname(__FILE__) . '/AdminEtsACFormController.php');

class AdminEtsACMailQueueController extends AdminEtsACFormController
{
    public function __construct()
    {
        $this->table = 'ets_abancart_email_queue';
        $this->list_id = $this->table;
        $this->lang = false;
        $this->_orderBy = 'date_add';
        $this->_orderWay = 'DESC';
        $this->list_no_link = true;
        $this->allow_export = false;

        parent::__construct();

        $this->addRowAction('sendmail');
        $this->addRowAction('view');
        $this->addRowAction('delete');

        $this->tpl_folder = 'etsac_mail_queue/';
        $this->override_folder = 'etsac_mail_queue/';

        $this->_where = 'AND a.id_shop = ' . (int)$this->context->shop->id;

        $this->fields_list = array(
            'id_ets_abancart_email_queue' => array(
                'title' => $this->l('Queue ID', 'AdminEtsACMailQueueController'),
                'type' => 'int',
                'filter_key' => 'a!id_ets_abancart_email_queue',
                'class' => 'fixed-width-xs center',
            ),
            'id_cart' => array(
                'title' => $this->l('Cart ID', 'AdminEtsACMailQueueController'),
                'type' => 'int',
                'filter_key' => 'a!id_cart',
                'class' => 'fixed-width-xs center',
            ),
            'subject' => array(
                'title' => $this->l('Title', 'AdminEtsACMailQueueController'),
                'type' => 'text',
                'filter_key' => 'a!subject',
            ),
            'content' => array(
                'title' => $this->l('Content', 'AdminEtsACMailQueueController'),
                'type' => 'text',
                'filter_key' => 'a!content',
                'callback' => 'displayContent'
            ),
            'email' => array(
                'title' => $this->l('Email', 'AdminEtsACMailQueueController'),
                'type' => 'text',
                'filter_key' => 'a!email',
            ),
            'send_count' => array(
                'title' => $this->l('Trying times', 'AdminEtsACMailQueueController'),
                'type' => 'int',
                'align' => 'center',
                'filter_key' => 'a!send_count',
            ),
            'date_add' => array(
                'title' => $this->l('Queue at', 'AdminEtsACMailQueueController'),
                'type' => 'datetime',
                'align' => 'center',
                'filter_key' => 'a!date_add',
            ),
        );

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected', 'AdminEtsACMailQueueController'),
                'confirm' => $this->l('Delete selected items?', 'AdminEtsACMailQueueController'),
                'icon' => 'icon-trash',
            )
        );
    }

    public function setMedia($isNewTheme = false)
    {
        Media::addJsDef([
            'CLEAR_ALL_QUEUE_MESSAGE' => $this->l('Do you want to clean all queues?', 'AdminEtsACMailQueueController'),
            'DELETE_QUEUE_MESSAGE' => $this->l('Delete selected item?', 'AdminEtsACMailQueueController')
        ]);
        parent::setMedia($isNewTheme);
    }

    public function displayContent($html)
    {
        if (trim($html) == '')
            return null;

        $this->context->smarty->assign([
            'html_strip_tags' => $html,
        ]);
        return $this->createTemplate('html.tpl')->fetch();
    }

    public function displaySendmailLink($token, $id)
    {
        if (!isset(self::$cache_lang['sendmail'])) {
            self::$cache_lang['sendmail'] = $this->l('Send mail', 'AdminEtsACMailQueueController');
        }

        $this->context->smarty->assign(array(
            'href' => self::$currentIndex . '&sendmail&' . $this->identifier . '=' . $id . '&token=' . ($token != null ? $token : $this->token),
            'action' => self::$cache_lang['sendmail'],
        ));

        return $this->createTemplate('helpers/list/list_action_sendmail.tpl')->fetch();
    }

    public function ajaxProcessSendmail()
    {
        $id = (int)Tools::getValue('id_ets_abancart_email_queue');
        if (!$id ||
            !Validate::isUnsignedInt($id) ||
            !($queue = EtsAbancartTools::getQueue($id))
        ) {
            $this->errors[] = $this->l('Cannot send mail.', 'AdminEtsACMailQueueController');
        } else {
            $url_params = [
                'id_ets_abancart_reminder' => $queue['id_ets_abancart_reminder'],
                'mtime' => microtime()
            ];
            if ((int)$queue['id_cart'])
                $url_params['c'] = (int)$queue['id_cart'];
            elseif ((int)$queue['id_customer'])
                $url_params['cus'] = (int)$queue['id_customer'];
            if (!@glob($this->module->getLocalPath() . 'mails/' . Language::getIsoById((int)$queue['id_lang']) . '/abandoned_cart*[.txt|.html]')) {
                $this->module->_installMail(new Language((int)$queue['id_lang']));
            }
            if (EtsAbancartMail::send(
                    (int)$queue['id_lang'],
                    'abandoned_cart',
                    $queue['subject'],
                    array(
                        '{tracking}' => $this->context->link->getModuleLink($this->module->name, 'image', ['rewrite' => EtsAbancartTools::getInstance()->encrypt(json_encode($url_params))], (int)Configuration::get('PS_SSL_ENABLED_EVERYWHERE')) . '&' . md5(time()),
                        '{content}' => $queue['content'],
                        '{url_params}' => json_encode($url_params)
                    ),
                    Tools::strtolower($queue['email']),
                    $queue['customer_name'], null, null, null, null,
                    $this->module->getLocalPath() . 'mails/', false,
                    (int)$queue['id_shop']
                ) || EtsAbancartTools::getNbSentMailQueue($queue['id_ets_abancart_email_queue']) > 5
            ) {
                $updateTracking = EtsAbancartTracking::updateTrackingData('`delivered` = 1, 
                        `id_customer` = ' . (int)$queue['id_customer'] . ',
                        `total_execute_times` = `total_execute_times` + 1',
                    ((int)$queue['id_cart'] ? 'id_cart = ' . (int)$queue['id_cart'] : 'id_customer = ' . (int)$queue['id_customer']) . ' AND id_ets_abancart_reminder = ' . (int)$queue['id_ets_abancart_reminder']);
                if ($updateTracking) {
                    EtsAbancartTracking::deleteTrackingById($queue['id_ets_abancart_email_queue']);
                }
            } else {
                $this->errors[] = $this->l('Sending mail failed.', 'AdminEtsACMailQueueController');
            }
        }

        $hasError = count($this->errors) > 0 ? 1 : 0;
        $this->toJson([
            'errors' => $hasError ? Tools::nl2br(implode(PHP_EOL, $this->errors)) : false,
            'msg' => !$hasError ? $this->l('Send mail successfully', 'AdminEtsACMailQueueController') : false,
            'html' => !$hasError ? $this->renderList() : false,
        ]);
    }

    public function ajaxProcessClearAllQueue()
    {
        if (!EtsAbancartQueue::clearAllQueue())
            $this->errors[] = $this->l('Clean all queues are failed', 'AdminEtsACMailQueueController');
        $hasError = count($this->errors) > 0 ? 1 : 0;
        die(json_encode([
            'errors' => $hasError ? Tools::nl2br(implode(PHP_EOL, $this->errors)) : false,
            'msg' => !$hasError ? $this->l('Clean all queues successfully', 'AdminEtsACMailQueueController') : false,
            'html' => !$hasError ? $this->renderList() : false,
        ]));
    }

    public function renderList()
    {
        $this->context->smarty->assign([
            'clear_all_queue_url' => self::$currentIndex . '&action=clearAllQueue&token=' . $this->token . ''
        ]);
        $html = $this->context->smarty->fetch($this->module->getLocalPath() . 'views/templates/admin/etsac_email_template/helpers/list/toolbar.tpl');
        return $html . parent::renderList();
    }

    public function ajaxProcessRenderView()
    {
        $this->toJson([
            'html' => $this->renderView()
        ]);
    }

    public function renderView()
    {
        $id = (int)Tools::getValue('id_ets_abancart_email_queue');
        $content = $id && Validate::isUnsignedInt($id) ? EtsAbancartTools::getContentQueue($id) : '';
        $content = EtsAbancartEmailTemplate::formatEmailTemplate($content);

        $this->context->smarty->assign([
            'html' => $content,
        ]);
        return $this->createTemplate('helpers/view/view.tpl')->fetch();
    }

    public function initToolbar()
    {
        parent::initToolbar();
        unset($this->toolbar_btn['new']);
    }

    public function displayDeleteLink($token, $id)
    {
        $this->context->smarty->assign([
            'delete_queue_url' => self::$currentIndex . '&action=deleteQueue&ajax=1&id_ets_abancart_email_queue=' . $id . '&token=' . ($token ?: $this->token),
        ]);
        return $this->createTemplate('helpers/list/list_action_delete.tpl')->fetch();
    }

    public function ajaxProcessDeleteQueue()
    {
        $id = (int)Tools::getValue('id_ets_abancart_email_queue');
        if (!EtsAbancartQueue::deleteQueue($id))
            $this->errors[] = $this->l('Clean all queues are failed', 'AdminEtsACMailQueueController');
        $hasError = count($this->errors) > 0 ? 1 : 0;
        die(json_encode([
            'errors' => $hasError ? Tools::nl2br(implode(PHP_EOL, $this->errors)) : false,
            'msg' => !$hasError ? $this->l('Delete queue successfully', 'AdminEtsACMailQueueController') : false,
            'html' => !$hasError ? $this->renderList() : false,
        ]));
    }
}