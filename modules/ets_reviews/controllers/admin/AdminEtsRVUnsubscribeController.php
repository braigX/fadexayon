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

require_once dirname(__FILE__) . '/AdminEtsRVBaseController.php';

class AdminEtsRVUnsubscribeController extends AdminEtsRVBaseController
{
    public function __construct()
    {
        $this->table = 'ets_rv_unsubscribe';
        $this->className = 'EtsRVUnsubscribe';
        $this->identifier = 'id_ets_rv_unsubscribe';

        parent::__construct();

        $this->allow_export = true;
        $this->_redirect = false;
        $this->list_no_link = true;
        $this->lang = false;

        $this->addRowAction('delete');

        $this->_defaultOrderBy = 'id_ets_rv_unsubscribe';
        $this->_defaultOrderWay = 'DESC';
        $this->fields_list = array(
            'id_ets_rv_unsubscribe' => array(
                'title' => $this->l('ID', 'AdminEtsRVUnsubscribeController'),
                'type' => 'int',
                'filter_key' => 'a!id_ets_rv_unsubscribe',
                'class' => 'ets-rv-id_ets_rv_unsubscribe fixed-width-xs text-center',
                'align' => 'ets-rv-id_ets_rv_unsubscribe',
            ),
            'email' => array(
                'title' => $this->l('Email', 'AdminEtsRVUnsubscribeController'),
                'type' => 'text',
                'filter_key' => 'a!email',
                'class' => 'ets-rv-email',
                'align' => 'ets-rv-email',
            ),
            'active' => array(
                'title' => $this->l('Unsubscribed', 'AdminEtsRVUnsubscribeController'),
                'type' => 'bool',
                'filter_key' => 'a!active',
                'active' => 'status',
                'class' => 'ets-rv-active fixed-width-lg text-center',
                'align' => 'ets-rv-active',
            ),
            'date_add' => array(
                'title' => $this->l('Date', 'AdminEtsRVUnsubscribeController'),
                'filter_key' => 'a!date',
                'type' => 'date',
                'class' => 'ets-rv-date_add fixed-width-lg text-center',
                'align' => 'ets-rv-date_add',
            ),
        );
    }

    public function initToolbar()
    {
        parent::initToolbar();

        if (!empty($this->toolbar_btn['new'])) {
            unset($this->toolbar_btn['new']);
        }
    }

    public function initProcess()
    {
        $submit_bulk_actions = [
            'enableSelection',
            'disableSelection'
        ];
        foreach ($submit_bulk_actions as $submit_bulk_action) {
            if (Tools::isSubmit('submitBulk' . $submit_bulk_action . $this->table) || Tools::isSubmit('submitBulk' . $submit_bulk_action)) {
                $this->action = 'bulk' . $submit_bulk_action;
                $this->boxes = Tools::getValue($this->table . 'Box');
            }
        }
        parent::initProcess();
    }
}