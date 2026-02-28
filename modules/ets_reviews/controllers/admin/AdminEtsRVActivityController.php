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

require_once dirname(__FILE__) . '/AdminEtsRVBaseController.php';

class AdminEtsRVActivityController extends AdminEtsRVBaseController
{
    public $product_comment;
    public $customer_id;
    public $type = [];

    /**
     * @var Ets_reviews
     */
    public $module;

    public function __construct()
    {
        $this->table = 'ets_rv_activity';
        $this->identifier = 'id_ets_rv_activity';
        $this->className = 'EtsRVActivity';
        $this->list_id = $this->table;

        parent::__construct();

        $this->allow_export = true;
        $this->_redirect = false;
        $this->list_no_link = true;
        $this->lang = false;
        $this->list_simple_header = false;

        $this->_defaultOrderBy = 'id_ets_rv_activity';
        $this->_defaultOrderWay = 'DESC';

        $this->addRowAction('approve');
        $this->addRowAction('view');
        $this->addRowAction('edit');
        $this->addRowAction('private');
        $this->addRowAction('delete');
        if ((int)Configuration::get('ETS_RV_REFUSE_REVIEW')) {
            $this->addRowAction('refuse');
        }

        $this->bulk_actions = array(
            'approve' => array(
                'text' => $this->l('Approve selected', 'AdminEtsRVActivityController'),
                'confirm' => $this->l('Approve selected items?', 'AdminEtsRVActivityController'),
                'icon' => 'icon-check',
            ),
            'read' => array(
                'text' => $this->l('Mark as read', 'AdminEtsRVActivityController'),
                'confirm' => $this->l('Mark selected items as read?', 'AdminEtsRVActivityController'),
                'icon' => 'icon-eye',
            ),
            'delete' => array(
                'text' => $this->l('Delete selected', 'AdminEtsRVActivityController'),
                'confirm' => $this->l('Do you want to delete selected items?', 'AdminEtsRVActivityController'),
                'icon' => 'icon-trash',
            )
        );

        $this->customer_id = (int)Tools::getValue('customer_id');
        if ($this->customer_id == '' ||
            !Validate::isUnsignedInt($this->customer_id) ||
            $this->customer_id < 0
        ) {
            $this->customer_id = -1;
        }
        $this->type = explode('-', trim(Tools::getValue('activity_type')));
        if ($this->type) {
            $activityTypes = EtsRVDefines::getInstance()->getActivityTypes();
            if ($activityTypes) {
                $array_keys = array_keys($activityTypes);
                $loop = 0;
                foreach ($this->type as &$type) {
                    if (!in_array(trim($type), $array_keys))
                        unset($this->type[$loop]);
                    $loop++;
                }
            }
        }
        $this->_select = '
            IF(
                e.id_employee AND (!c.id_customer OR !a.id_guest)
                , CONCAT(e.`firstname`, \' \',  e.`lastname`)
                , IF(c.id_customer, CONCAT(c.`firstname`, \' \',  c.`lastname`), IF(pc.id_ets_rv_product_comment > 0 AND pc.customer_name is NOT NULL, pc.customer_name, \'' . $this->l('Guest', 'AdminEtsRVActivityController') . '\'))
            ) customer_name
            , IF(e.id_employee AND (!c.id_customer OR !a.id_guest), e.id_employee, c.id_customer) `author_id`
            , pl.`name` `product_name`
            , pl.`id_product` `product_id`
            , IF(a.`id_ets_rv_product_comment` is NOT NULL AND a.`id_ets_rv_product_comment` > 0, pc.validate, IF(cm.`id_ets_rv_comment` is NOT NULL AND cm.`id_ets_rv_comment` > 0, cm.validate, rcm.validate)) `validate`
            , i.id_image
            , il.legend `image_name`
            , epl.name `profile_name`
            , c.id_customer `customer_id`
            , pc.grade
            , IFNULL(sa.`read`, 0) `is_read`
        ';
        $this->_join = '
            LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON (c.`id_customer` = a.`id_customer`)
            LEFT JOIN `' . _DB_PREFIX_ . 'employee` e ON (e.`id_employee` = a.`employee`)
            LEFT JOIN `' . _DB_PREFIX_ . 'profile_lang` epl ON (epl.`id_profile` = e.`id_profile` AND epl.id_lang = ' . (int)$this->context->language->id . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (pl.`id_product` = a.`id_product` AND pl.id_lang=' . (int)$this->context->language->id . Shop::addSqlRestrictionOnLang('pl') . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'image` i ON (i.`id_product` = a.`id_product` AND i.cover = 1)
            LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int)$this->context->language->id . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop ON (i.`id_image` = image_shop.`id_image` AND image_shop.`id_shop` = ' . (int)$this->context->shop->id . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'ets_rv_product_comment` pc ON (pc.`id_ets_rv_product_comment` = a.`id_ets_rv_product_comment`)
            LEFT JOIN `' . _DB_PREFIX_ . 'ets_rv_comment` cm ON (cm.`id_ets_rv_comment` = a.`id_ets_rv_comment`)
            LEFT JOIN `' . _DB_PREFIX_ . 'ets_rv_reply_comment` rcm ON (rcm.`id_ets_rv_reply_comment` = a.`id_ets_rv_reply_comment`)
            LEFT JOIN `' . _DB_PREFIX_ . 'ets_rv_staff_activity` sa ON (sa.`id_ets_rv_activity` = a.`id_ets_rv_activity` AND sa.`id_employee`=' . (int)$this->context->employee->id . ')
        ';
        $this->_where = ' AND (pc.`id_ets_rv_product_comment` > 0 OR cm.`id_ets_rv_comment` > 0 OR rcm.`id_ets_rv_reply_comment` > 0) AND a.`employee` != ' . (int)$this->context->employee->id;
        $this->_group = 'GROUP BY a.id_ets_rv_activity';

        $this->fields_list = array(
            'is_read' => array(
                'title' => $this->l('Read', 'AdminEtsRVActivityController'),
                'type' => 'bool',
                'orderby' => false,
                'class' => 'ets-rv-is_read fixed-width-xs text-center',
                'callback' => 'displayRead',
                'havingFilter' => true,
                'align' => 'ets-rv-is_read',
            ),
            'action' => array(
                'title' => $this->l('Action'),
                'type' => 'select',
                'list' => EtsRVDefines::getInstance()->getActivityActions(),
                'filter_key' => 'a!action',
                'orderby' => false,
                'align' => 'ets-rv-action text-center',
                'callback' => 'displayAction',
                'class' => 'ets-rv-action',
            ),
            'customer_name' => array(
                'title' => $this->l('Author', 'AdminEtsRVActivityController'),
                'type' => 'text',
                'havingFilter' => true,
                'callback' => 'displayCustomer',
                'class' => 'ets-rv-customer_name fixed-width-lg',
                'ref' => 'author_id',
                'align' => 'ets-rv-customer_name',
            ),
            'content' => array(
                'title' => $this->l('Content', 'AdminEtsRVActivityController'),
                'type' => 'text',
                'orderby' => false,
                'havingFilter' => true,
                'callback' => 'displayContent',
                'class' => 'ets-rv-content',
                'align' => 'ets-rv-content',
            ),
            'product_name' => array(
                'title' => $this->l('Product', 'AdminEtsRVActivityController'),
                'type' => 'text',
                'callback' => 'buildFieldProductLink',
                'havingFilter' => true,
                'ref' => 'product_id',
                'class' => 'ets-rv-product_name',
                'align' => 'ets-rv-product_name',
            ),
            'validate' => array(
                'title' => $this->l('Status', 'AdminEtsRVActivityController'),
                'type' => 'select',
                'list' => EtsRVDefines::getInstance()->getReviewStatus(),
                'filter_key' => 'validate',
                'callback' => 'displayStatus',
                'havingFilter' => true,
                'class' => 'ets-rv-validate status',
                'align' => 'ets-rv-validate',
            ),
            'date_add' => array(
                'title' => $this->l('Date', 'AdminEtsRVActivityController'),
                'align' => 'ets-rv-date_add text-left',
                'type' => 'datetime',
                'class' => 'ets-rv-date_add fixed-width-lg ',
                'filter_key' => 'a!date_add',
                'callback' => 'displayDateAdd',
            ),
        );
        if ($this->type || $this->customer_id > 0) {
            if ($this->context->cookie->__get('submitFilter' . $this->list_id)) {
                $this->processResetFilters($this->list_id);
            }
            $prefix = $this->getCookieFilterPrefix();
            if ($this->customer_id > 0) {
                $this->context->cookie->{$prefix . $this->list_id . 'Filter_id_customer'} = $this->customer_id;
                $this->_filter .= ' AND a.id_customer=' . $this->customer_id . ' AND a.employee=0';
            }
            if ($this->type) {
                $this->context->cookie->{$prefix . $this->list_id . 'Filter_a!type'} = @json_encode($this->type);
                $this->_filter .= ' AND FIND_IN_SET(a.type, \'' . implode(',', $this->type) . '\')';
            }
            $this->processFilter();
        }
    }

    public function displayDateAdd($date_add)
    {
        return trim($date_add) !== '' ? EtsRVTools::displayText(EtsRVProductCommentEntity::getInstance()->timeElapsedString($date_add), 'span', ['title' => $date_add]) : $date_add;
    }

    public function processFilter()
    {
        parent::processFilter();

        //Customer filter customer name:
        $prefix = $this->getCookieFilterPrefix();

        $id_customer = $prefix . $this->list_id . 'Filter_customer_name';
        if (isset($this->context->cookie->$id_customer) && $this->context->cookie->$id_customer !== '' && Validate::isUnsignedInt($this->context->cookie->$id_customer)) {
            $this->_filterHaving .= ' OR (a.id_customer=' . (int)$this->context->cookie->$id_customer . ' AND a.employee=0)';
        }

        $id_product = $prefix . $this->list_id . 'Filter_product_name';
        if (isset($this->context->cookie->$id_product) && $this->context->cookie->$id_product !== '') {
            $this->_filterHaving .= ' OR a.id_product=' . (int)$this->context->cookie->$id_product;
        }
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        $this->addJS(array(
            _PS_JS_DIR_ . 'admin/tinymce.inc.js',
            _PS_JS_DIR_ . 'tiny_mce/tiny_mce.js',
            _PS_JS_DIR_ . 'jquery/plugins/autocomplete/jquery.autocomplete.js',
        ));
        $this->addCss(array(
            $this->module->getPathUri() . 'views/css/productcomments.css',
        ));
    }

    public function initToolbar()
    {
        parent::initToolbar();

        unset($this->toolbar_btn['new']);
    }

    public function initProcess()
    {
        parent::initProcess();
        $this->context->smarty->assign([
            'link' => $this->context->link
        ]);
        $this->module->postProcess();
    }

    public function initContent()
    {
        parent::initContent();

        if ($this->display == null || $this->display == 'list') {
            $lastId = EtsRVActivity::getLastID();
            if ($lastId > 0) {
                EtsRVStaff::lastViewer($this->context->employee->id, $lastId);
                EtsRVActivity::makeReadLastViewer($this->context->employee->id, $lastId);
            }
        }
    }

    public function displayAction($action, $tr)
    {
        if ($action !== '') {
            $tpl_vars = [
                'action' => $action,
                'name' => EtsRVDefines::getInstance()->getActivityActions($action)
            ];
            if (trim($action) == EtsRVActivity::ETS_RV_ACTION_REVIEW) {
                $tpl_vars['grade'] = isset($tr['grade']) ? (int)$tr['grade'] : 0;
            }
            $this->context->smarty->assign($tpl_vars);
            return $this->context->smarty->fetch($this->module->getLocalPath() . 'views/templates/admin/action-svg.tpl');
        }

        return $action;
    }

    public function displayRead($read, $tr)
    {
        $attrs = array(
            'class' => 'ets_rv_make ' . ($read ? '' : 'un') . 'read',
        );
        return EtsRVTools::displayText($read, 'span', $attrs);
    }

    public function displayStatus($value)
    {
        switch ($value) {
            case '3':
                $badge = 'badge-reject';
                break;
            case '1':
                $badge = 'badge-success';
                break;
            case '0':
                $badge = 'badge-warning';
                break;
            default:
                $badge = 'badge-danger';
        }
        $attrs = array(
            'class' => 'badge ' . $badge
        );

        return EtsRVTools::displayText(EtsRVDefines::getInstance()->getReviewStatus($value), 'span', $attrs);
    }

    public function displayContent($content, $item)
    {
        return EtsRVActivityEntity::getInstance()->activityProperties($content, $item);
    }

    static $st_customers, $st_employees = [];

    public function displayCustomer($customer_name, $tr)
    {
        $profile_photo = $href = '';
        $is_employee = false;
        if (isset($tr['employee']) && $tr['employee'] > 0) {
            if (isset(self::$st_employees[(int)$tr['employee']]) && self::$st_employees[(int)$tr['employee']])
                return self::$st_employees[(int)$tr['employee']];
            $is_employee = true;
            $info = EtsRVStaff::getInfos($tr['employee']);
            if ($info) {
                if (isset($info['display_name']) && trim($info['display_name']) !== '')
                    $customer_name = $info['display_name'];
                if (isset($info['avatar']) && trim($info['avatar']) !== '' && @file_exists(_PS_IMG_DIR_ . $this->module->name . '/a/' . trim($info['avatar'])))
                    $profile_photo = EtsRVLink::getMediaLink(_PS_IMG_ . $this->module->name . '/a/' . $info['avatar'], $this->context);
            }
            if (trim($profile_photo) == '' && @file_exists($this->module->getLocalPath() . 'views/img/employee_avatar_default.jpg')) {
                $profile_photo = EtsRVLink::getMediaLink($this->module->getPathUri() . 'views/img/employee_avatar_default.jpg', $this->context);
            }
            $href = EtsRVLink::getAdminLink('AdminEmployees', true, ($this->module->ps1760 ? ['route' => 'admin_employees_edit', 'employeeId' => (int)$tr['employee']] : []), ['viewemployee' => '', 'id_employee' => (int)$tr['employee']], $this->context);
        } elseif (isset($tr['id_customer']) && $tr['id_customer'] > 0) {
            if (isset(self::$st_employees[(int)$tr['id_customer']]) && self::$st_employees[(int)$tr['id_customer']])
                return self::$st_employees[(int)$tr['id_customer']];
            $info = EtsRVProductCommentCustomer::getCustomer($tr['id_customer']);
            if ($info) {
                if (isset($info['display_name']) && trim($info['display_name']) !== '')
                    $customer_name = $info['display_name'];
                if (isset($info['avatar']) && trim($info['avatar']) !== '' && @file_exists(_PS_IMG_DIR_ . $this->module->name . '/a/' . trim($info['avatar'])))
                    $profile_photo = EtsRVLink::getMediaLink(_PS_IMG_ . $this->module->name . '/a/' . trim($info['avatar']), $this->context);
            }
            if (trim($profile_photo) == '' && @file_exists($this->module->getLocalPath() . 'views/img/customer_avatar_default.jpg')) {
                $profile_photo = EtsRVLink::getMediaLink($this->module->getPathUri() . 'views/img/customer_avatar_default.jpg', $this->context);
            }
            $href = EtsRVLink::getAdminLink('AdminCustomers', true, $this->module->ps1760 ? ['route' => 'admin_customers_view', 'customerId' => (int)$tr['id_customer']] : [], ['viewcustomer' => '', 'id_customer' => (int)$tr['id_customer']], $this->context);
        } else
            return $customer_name;
        $this->context->smarty->assign([
            'btn' => [
                'href' => $href,
                'target' => '_bank',
                'title' => $customer_name,
                'class' => 'item-custom-link',
            ],
            'profile_name' => $tr['profile_name'],
            'avatar' => $profile_photo,
        ]);
        $customer_link = $this->context->smarty->fetch($this->module->getLocalPath() . 'views/templates/admin/customer-link.tpl');
        if ($is_employee) {
            self::$st_employees[(int)$tr['employee']] = $customer_link;
        } else
            self::$st_customers[(int)$tr['id_customer']] = $customer_link;
        return $customer_link;
    }

    public function displayApproveLink($token, $id)
    {
        $data = $this->getProductCommentId($id, true);
        if ($data
            && isset($data['validate'])
            && (int)$data['validate'] !== 1
            && isset($data['href'])
            && $data['href'] != ''
        ) {
            if (!array_key_exists('approve', self::$cache_lang)) {
                self::$cache_lang['approve'] = $this->l('Approve', 'AdminEtsRVActivityController');
            }
            $this->context->smarty->assign(array(
                'href' => $data['href'],
                'action' => self::$cache_lang['approve'],
                'class' => 'ets_rv_approve',
                'token' => $token,
            ));

            return $this->context->smarty->fetch($this->module->getLocalPath() . 'views/templates/admin/list_action_approve.tpl');
        }
    }

    public function displayRefuseLink($token, $id)
    {
        $data = $this->getProductCommentId($id, true, 'refuse');
        if ($data
            && isset($data['validate'])
            && !in_array((int)$data['validate'], [1, 3])
            && isset($data['href'])
            && $data['href'] != ''
        ) {
            if (!array_key_exists('refuse', self::$cache_lang)) {
                self::$cache_lang['refuse'] = $this->l('Decline', 'AdminEtsRVActivityController');
            }
            $this->context->smarty->assign(array(
                'href' => $data['href'],
                'action' => self::$cache_lang['refuse'],
                'class' => 'ets_rv_refuse',
                'token' => $token,
            ));

            return $this->context->smarty->fetch($this->module->getLocalPath() . 'views/templates/admin/list_action_refuse.tpl');
        }
    }

    public function displayPrivateLink($token, $id)
    {
        $data = $this->getProductCommentId($id, true, 'private');
        if ($data
            && isset($data['validate'])
            && (int)$data['validate'] !== 2
            && isset($data['href'])
            && $data['href'] != ''
        ) {
            if (!array_key_exists('private', self::$cache_lang)) {
                self::$cache_lang['private'] = $this->l('Set to private', 'AdminEtsRVActivityController');
            }
            $this->context->smarty->assign(array(
                'href' => $data['href'],
                'action' => self::$cache_lang['private'],
                'class' => 'ets_rv_private',
                'token' => $token,
            ));

            return $this->context->smarty->fetch($this->module->getLocalPath() . 'views/templates/admin/list_action_private.tpl');
        }
    }

    public function ajaxProcessNotify()
    {
        $this->jsonRender([
            'activity' => EtsRVActivity::getActivitiesFromID(EtsRVStaff::getLastActivityId($this->context->employee->id), $this->context->employee->id)
        ]);
    }

    static $st_activities = [];

    public function getProductCommentId($activity_id, $ret_href = false, $action = 'approve')
    {
        if (!$activity_id ||
            !Validate::isUnsignedInt($activity_id)
        ) {
            return false;
        }
        if (isset(self::$st_activities[$activity_id]) && self::$st_activities[$activity_id]) {
            $ret = self::$st_activities[$activity_id];
        }
        if (!isset($ret)) {
            $ret = [];
            $activity = new EtsRVActivity($activity_id);
            $data = array();
            if ($activity->id_ets_rv_reply_comment) {
                $data = EtsRVReplyComment::getData($activity->id_ets_rv_reply_comment, 0, true);
            } elseif ($activity->id_ets_rv_comment) {
                $data = EtsRVComment::getData($activity->id_ets_rv_comment, 0, true);
            } else if ($activity->id_ets_rv_product_comment > 0) {
                $data = EtsRVProductComment::getData($activity->id_ets_rv_product_comment, 0, true);
            }
            if ($data) {
                $ret = array(
                    'id' => $data['id_ets_rv_product_comment'],
                    'qa' => $data['question'],
                    'comment_id' => isset($data['id_ets_rv_comment']) ? $data['id_ets_rv_comment'] : $activity->id_ets_rv_comment,
                    'reply_comment_id' => isset($data['id_ets_rv_reply_comment']) ? $data['id_ets_rv_reply_comment'] : $activity->id_ets_rv_reply_comment,
                    'validate' => $data['validate'],
                    'answer' => isset($data['answer']) && $data['answer'] ? 1 : 0,
                );
            }
            self::$st_activities[$activity_id] = $ret;
        }
        if ($ret && $ret_href) {
            $url_params = array(
                'qa' => $ret['qa'],
                'refreshController' => $this->controller_name
            );
            if ($ret['reply_comment_id'] > 0) {
                $url_params['id_ets_rv_reply_comment'] = (int)$ret['reply_comment_id'];
                $url_params[$action . 'ets_rv_reply_comment'] = 1;
                $href = EtsRVLink::getAdminLink(Ets_reviews::TAB_PREFIX . ($ret['qa'] > 0 ? 'AnswerComments' : 'Replies'), true, array(), $url_params, $this->context);
            } elseif ($ret['comment_id'] > 0) {
                $url_params['id_ets_rv_comment'] = (int)$ret['comment_id'];
                $url_params[$action . 'ets_rv_comment'] = 1;
                $href = EtsRVLink::getAdminLink(Ets_reviews::TAB_PREFIX . ($ret['qa'] > 0 ? (!empty($data['answer']) ? 'Answers' : 'QuestionComments') : 'Comments'), true, array(), $url_params, $this->context);
            } elseif ($ret['id'] > 0) {
                $url_params['id_ets_rv_product_comment'] = (int)$ret['id'];
                $url_params[$action . 'ets_rv_product_comment'] = 1;
                $href = EtsRVLink::getAdminLink(Ets_reviews::TAB_PREFIX . ($ret['qa'] > 0 ? 'Questions' : 'Reviews'), true, array(), $url_params, $this->context);
            }
            $ret['href'] = isset($href) ? $href : '';
        }
        return isset($ret) ? $ret : null;
    }

    public function displayViewLink($token, $id)
    {
        $data = $this->getProductCommentId($id);
        if ($data) {
            if (!array_key_exists('view', self::$cache_lang)) {
                self::$cache_lang['view'] = $this->l('View', 'AdminEtsRVActivityController');
            }
            $url_params = array(
                'id_ets_rv_activity' => $id,
                'id_ets_rv_product_comment' => $data['id'],
                'id_ets_rv_comment' => $data['comment_id'],
                'id_ets_rv_reply_comment' => $data['reply_comment_id'],
                'viewets_rv_product_comment' => 1,
                'refreshController' => $this->controller_name,
                'read' => 1,
            );
            if (isset($data['answer']) && $data['answer']) {
                $url_params['answer'] = 1;
            }
            $this->context->smarty->assign(array(
                'href' => EtsRVLink::getAdminLink(Ets_reviews::TAB_PREFIX . ($data['qa'] ? 'Questions' : 'Reviews'), true, array(), $url_params, $this->context),
                'action' => self::$cache_lang['view'],
                'class' => 'ets_rv_view_review',
                'token' => $token,
            ));

            return $this->context->smarty->fetch($this->module->getLocalPath() . 'views/templates/admin/list_action_view.tpl');
        }
    }

    public function displayEditLink($token, $id)
    {
        $data = $this->getProductCommentId($id, true, 'update');
        if ($data) {
            if (!array_key_exists('edit', self::$cache_lang)) {
                self::$cache_lang['edit'] = $this->l('Edit', 'AdminEtsRVActivityController');
            }

            $this->context->smarty->assign(array(
                'href' => $data['href'],
                'action' => self::$cache_lang['edit'],
                'class' => 'ets_rv_edit',
                'token' => $token,
                'id' => $id
            ));

            return $this->context->smarty->fetch($this->module->getLocalPath() . 'views/templates/admin/list_action_edit.tpl');
        }
    }

    public function displayDeleteLink($token, $id)
    {
        $data = $this->getProductCommentId($id, true, 'delete');
        if ($data) {
            if (!array_key_exists('delete', self::$cache_lang)) {
                self::$cache_lang['delete'] = $this->l('Delete', 'AdminEtsRVActivityController');
            }

            $this->context->smarty->assign(array(
                'href' => $data['href'],
                'action' => self::$cache_lang['delete'],
                'class' => 'ets_rv_delete',
                'confirm' => $this->l('Do you want to delete selected items?', 'AdminEtsRVActivityController'),
                'token' => $token,
            ));

            return $this->context->smarty->fetch($this->module->getLocalPath() . 'views/templates/admin/list_action_delete.tpl');
        }
    }

    public function processBulkRead()
    {
        if (EtsRVTools::isArrayWithIds($this->boxes)) {
            if (!EtsRVActivity::makeReadAll($this->context->employee->id, $this->boxes)) {
                $this->errors[] = $this->l('Failed to mark selected item(s) as read', 'AdminEtsRVActivityController');
            }
        } else {
            $this->errors[] = $this->l('Please select the item(s) to mark as read', 'AdminEtsRVActivityController');
        }
        if (!count($this->errors)) {
            $this->confirmations = $this->l('Marked all selected item(s) as read successfully', 'AdminEtsRVActivityController');
        }
    }

    public function processBulkDelete()
    {
        if (EtsRVTools::isArrayWithIds($this->boxes)) {

            list($review_ids, $comment_ids, $reply_ids) = $this->getFilterIds($this->boxes);

            if ($review_ids) {
                if (!EtsRVProductComment::deleteProductComments($review_ids)) {
                    $this->errors[] = $this->l('Deleting selected items failed', 'AdminEtsRVActivityController');
                } else {
                    if (EtsRVProductCommentImage::deleteImages($review_ids) &&
                        EtsRVProductComment::deleteCascade($review_ids, 'reply_comment', 'comment', 'product_comment') &&
                        EtsRVProductComment::deleteCascade($review_ids, 'comment', '', 'product_comment')
                    ) {
                        if (!EtsRVProductComment::deleteAllChildren($review_ids))
                            $this->errors[] = $this->l('Cannot delete selected item(s)', 'AdminEtsRVActivityController');
                    } else
                        $this->errors[] = $this->l('Cannot delete cascade by the selected item(s)', 'AdminEtsRVActivityController');

                }
            }

            if ($comment_ids) {
                if (!EtsRVComment::deleteComments($comment_ids)) {
                    $this->errors[] = $this->l('Deleting selected items failed', 'AdminEtsRVActivityController');
                } else {
                    if (EtsRVComment::deleteCascade($comment_ids, 'reply_comment', '', 'comment')) {
                        if (!EtsRVComment::deleteAllChildren($comment_ids))
                            $this->errors[] = $this->l('Cannot delete selected item(s)', 'AdminEtsRVActivityController');
                    } else
                        $this->errors[] = $this->l('Cannot delete cascade by the selected item(s)', 'AdminEtsRVActivityController');
                }
            }

            if ($reply_ids) {
                if (!EtsRVReplyComment::deleteComments($reply_ids)) {
                    $this->errors[] = $this->l('Deleting selected items failed', 'AdminEtsRVActivityController');
                } else {
                    if (!EtsRVReplyComment::deleteAllChildren($reply_ids))
                        $this->errors[] = $this->l('Cannot delete selected item(s)', 'AdminEtsRVActivityController');
                }
            }


            if (count($this->errors) < 1) {
                parent::processBulkDelete();

                $this->confirmations = $this->l('Deleted successfully', 'AdminEtsRVRepliesController');
            }
        }
    }

    public function getFilterIds($ids)
    {
        $review_ids = $comment_ids = $reply_ids = array();
        if (is_array($ids) && Validate::isArrayWithIds($ids)) {
            foreach ($ids as $id) {
                $activity = new EtsRVActivity($id);
                if ($activity->id_ets_rv_reply_comment > 0 && !in_array($activity->id_ets_rv_reply_comment, $reply_ids)) {
                    $reply_ids[] = $activity->id_ets_rv_reply_comment;
                } elseif ($activity->id_ets_rv_comment > 0 && !in_array($activity->id_ets_rv_comment, $comment_ids)) {
                    $comment_ids[] = $activity->id_ets_rv_comment;
                } elseif ($activity->id_ets_rv_product_comment > 0 && !in_array($activity->id_ets_rv_product_comment, $review_ids)) {
                    $review_ids[] = $activity->id_ets_rv_product_comment;
                }
            }
        }
        return [$review_ids, $comment_ids, $reply_ids];
    }

    public function processBulkApprove()
    {
        if (EtsRVTools::isArrayWithIds($this->boxes)) {

            list($review_ids, $comment_ids, $reply_ids) = $this->getFilterIds($this->boxes);

            if ($review_ids) {
                $validateOlds = EtsRVProductComment::productCommentValidate($review_ids);
                if (!EtsRVProductComment::approveProductComments($review_ids)) {
                    $this->errors[] = $this->l('Failed to approve selected item(s)', 'AdminEtsRVActivityController');
                } else {
                    foreach ($review_ids as $id) {
                        $productComment = new EtsRVProductComment($id);
                        if ($productComment->id > 0) {
                            EtsRVProductCommentEntity::getInstance()->productCommentMailApproved($productComment, true);
                            if (isset($validateOlds[$productComment->id])) {
                                EtsRVProductCommentEntity::getInstance()->productCommentMailVoucher($productComment);
                            }
                        }
                    }
                }
            }

            if ($comment_ids) {
                if (!EtsRVComment::approveComments($comment_ids)) {
                    $this->errors[] = $this->l('Failed to approve selected item(s)', 'AdminEtsRVActivityController');
                } else {
                    foreach ($comment_ids as $id) {
                        EtsRVCommentEntity::getInstance()->commentMailApproved($id, true);
                    }
                }
            }

            if ($reply_ids) {
                if (!EtsRVReplyComment::approveComments($reply_ids)) {
                    $this->errors[] = $this->l('Failed to approve selected item(s)', 'AdminEtsRVActivityController');
                } else {
                    foreach ($reply_ids as $id) {
                        EtsRVReplyCommentEntity::getInstance()->replyCommentMailApproved($id, true);
                    }
                }
            }

            if ($this->errors) {
                $this->errors[] = array_shift($this->errors);
            }
        } else {
            $this->errors[] = $this->l('Please select the item(s) to approve', 'AdminEtsRVActivityController');
        }
        if (!count($this->errors)) {
            $this->confirmations = $this->l('Approved', 'AdminEtsRVActivityController');
        }
    }

    public function setHelperDisplay(Helper $helper)
    {
        parent::setHelperDisplay($helper);

        if ($helper->listTotal > 1) {
            $helper->title = $this->l('Activities', 'AdminEtsRVActivityController');
        }
    }
}