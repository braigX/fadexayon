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

class AdminEtsRVUsersController extends AdminEtsRVBaseController
{
    public $product_comment;
    /**
     * @var EtsRVProductCommentCustomer
     */
    public $object;

    public function __construct()
    {
        $this->table = 'ets_rv_product_comment_customer';
        $this->className = 'EtsRVProductCommentCustomer';
        $this->identifier = 'id_customer';

        parent::__construct();

        $this->allow_export = true;
        $this->_redirect = false;
        $this->list_no_link = true;
        $this->lang = false;

        $this->_defaultOrderBy = 'id_customer';
        $this->_defaultOrderWay = 'DESC';

        $this->addRowAction('view');

        $this->_select = '
            c.id_customer
            , c.id_customer `customer_id`
            , IF(c.id_customer, CONCAT(c.`firstname`, \' \',  c.`lastname`), \'' . $this->l('Unknown', 'AdminEtsRVUsersController') . '\') customer_name
            , c.email `customer_email`
            , IF(c.id_customer is NOT NULL AND a.is_block is NULL, 0, a.is_block) as is_block
            , ROUND(SUM(pc.grade) / COUNT(IF(pc.grade > 0, 1, NULL)), 1) as `grade`
            , (SELECT COUNT(rv.id_ets_rv_product_comment) FROM  `' . _DB_PREFIX_ . 'ets_rv_product_comment` rv WHERE IF(c.`id_customer` > 0, rv.`id_customer` = c.`id_customer`, rv.`id_customer` = 0) AND rv.question = 0) as total_reviews
            , (SELECT COUNT(cm.id_ets_rv_comment) FROM  `' . _DB_PREFIX_ . 'ets_rv_comment` cm WHERE cm.`id_customer` = c.`id_customer` AND cm.question = 0 AND cm.answer = 0) as total_review_comments
            , (SELECT COUNT(rc.id_ets_rv_reply_comment) FROM  `' . _DB_PREFIX_ . 'ets_rv_reply_comment` rc WHERE rc.`id_customer` = c.`id_customer` AND rc.question = 0) as total_review_replies
            , (SELECT COUNT(qa.id_ets_rv_product_comment) FROM  `' . _DB_PREFIX_ . 'ets_rv_product_comment` qa WHERE qa.`id_customer` = c.`id_customer` AND qa.question = 1) as total_questions
            , (SELECT COUNT(an.id_ets_rv_comment) FROM  `' . _DB_PREFIX_ . 'ets_rv_comment` an WHERE an.`id_customer` = c.`id_customer` AND an.question = 1 AND an.answer = 1) as total_answers
            , ((SELECT COUNT(qc.id_ets_rv_comment) FROM  `' . _DB_PREFIX_ . 'ets_rv_comment` qc WHERE qc.`id_customer` = c.`id_customer` AND qc.question = 1 AND qc.answer = 0) + (SELECT COUNT(ca.id_ets_rv_reply_comment) FROM  `' . _DB_PREFIX_ . 'ets_rv_reply_comment` ca WHERE ca.`id_customer` = c.`id_customer` AND ca.question = 1)) as total_qa_comments
        ';
        $this->_join = '
            RIGHT JOIN `' . _DB_PREFIX_ . 'customer` c ON (c.`id_customer` = a.`id_customer`)
            LEFT JOIN `' . _DB_PREFIX_ . 'ets_rv_product_comment` pc ON (pc.`id_customer` = c.`id_customer`)
            LEFT JOIN `' . _DB_PREFIX_ . 'ets_rv_comment` cm ON (cm.`id_customer` = c.`id_customer`)
            LEFT JOIN `' . _DB_PREFIX_ . 'ets_rv_reply_comment` rc ON (rc.`id_customer` = c.`id_customer`)
        ';
        $this->_where = 'AND IF(pc.id_customer != 0 OR cm.id_customer != 0 OR rc.id_customer != 0, c.`id_customer` is NOT NULL, c.`id_customer` is NULL) OR a.id_customer > 0';
        $this->_group = 'GROUP BY c.id_customer';

        $this->fields_list = array(
            'customer_id' => array(
                'title' => $this->l('Customer ID', 'AdminEtsRVUsersController'),
                'type' => 'int',
                'havingFilter' => true,
                'class' => 'ets-rv-id_customer fixed-width-xs text-center',
                'callback' => 'displayCustomer',
                'align' => 'ets-rv-id_customer',
            ),
            'customer_name' => array(
                'title' => $this->l('Author', 'AdminEtsRVUsersController'),
                'type' => 'text',
                'havingFilter' => true,
                'callback' => 'buildFieldCustomerLink',
                'class' => 'ets-rv-customer_name',
                'align' => 'ets-rv-customer_name',
            ),
            'customer_email' => array(
                'title' => $this->l('Email', 'AdminEtsRVUsersController'),
                'type' => 'text',
                'havingFilter' => true,
                'class' => 'ets-rv-email',
                'align' => 'ets-rv-email',
            ),
            'grade' => array(
                'title' => $this->l('Rating', 'AdminEtsRVUsersController'),
                'type' => 'text',
                'class' => 'ets-rv-grade fixed-width-xs text-center',
                'havingFilter' => true,
                'callback' => 'displayGrade',
                'align' => 'ets-rv-grade',
            ),
            'total_reviews' => array(
                'title' => $this->l('Total reviews', 'AdminEtsRVUsersController'),
                'type' => 'text',
                'class' => 'ets-rv-total_reviews fixed-width-xs text-center',
                'havingFilter' => true,
                'callback' => 'displayTotalReviews',
                'align' => 'ets-rv-total_reviews',
            ),
            'total_review_comments' => array(
                'title' => $this->l('Total review comments', 'AdminEtsRVUsersController'),
                'type' => 'text',
                'class' => 'ets-rv-total_review_comments fixed-width-xs text-center',
                'havingFilter' => true,
                'callback' => 'displayTotalReviewComments',
                'align' => 'ets-rv-total_review_comments',
            ),
            'total_review_replies' => array(
                'title' => $this->l('Total review replies', 'AdminEtsRVUsersController'),
                'type' => 'text',
                'class' => 'ets-rv-total_review_replies fixed-width-xs text-center',
                'havingFilter' => true,
                'callback' => 'displayTotalReviewReplies',
                'align' => 'ets-rv-total_review_replies',
            ),
            'total_questions' => array(
                'title' => $this->l('Total questions', 'AdminEtsRVUsersController'),
                'type' => 'text',
                'class' => 'ets-rv-total_questions fixed-width-xs text-center',
                'havingFilter' => true,
                'callback' => 'displayTotalQuestions',
                'align' => 'ets-rv-total_questions',
            ),
            'total_answers' => array(
                'title' => $this->l('Total answers', 'AdminEtsRVUsersController'),
                'type' => 'text',
                'class' => 'ets-rv-total_answers fixed-width-xs text-center',
                'havingFilter' => true,
                'callback' => 'displayTotalAnswers',
                'align' => 'ets-rv-total_answers',
            ),
            'total_qa_comments' => array(
                'title' => $this->l('Total Q&A comments', 'AdminEtsRVUsersController'),
                'type' => 'text',
                'class' => 'ets-rv-total_qa_comments fixed-width-xs text-center',
                'havingFilter' => true,
                'callback' => 'displayTotalQAComments',
                'align' => 'ets-rv-total_qa_comments',
            ),
            'is_block' => array(
                'title' => $this->l('Is blocked', 'AdminEtsRVUsersController'),
                'type' => 'bool',
                'havingFilter' => true,
                'class' => 'ets-rv-is_block fixed-width-xs text-center',
                'callback' => 'displayIsBlock',
                'align' => 'ets-rv-is_block',
            ),
        );
    }

    public function ajaxProcessDelete()
    {
        $id_customer = (int)Tools::getValue('id_customer');
        $qa = (int)Tools::getValue('question');
        $answer = Tools::getIsset('answer') ? (int)Tools::getValue('answer') : -1;

        if ($tables = trim(Tools::getValue('table'))) {
            $tables = explode(',', $tables);
            if ($tables) {
                foreach ($tables as $table) {
                    if (trim($table) !== 'ets_rv_product_comment' && $id_customer <= 0)
                        continue;
                    if ($ids = EtsRVModel::getIds($table, $id_customer, $qa, $answer)) {
                        if (EtsRVModel::deleteByIds($table, $ids)) {
                            $group_tables = array(
                                'lang',
                                'usefulness',
                                'origin_lang',
                            );
                            $boxes = explode(',', $ids);
                            switch ($table) {
                                case 'ets_rv_product_comment':
                                    if (!$qa && !EtsRVProductCommentImage::deleteImages($boxes))
                                        $this->errors[] = $this->l('Cannot delete image.', 'AdminEtsRVUsersController');
                                    if (!EtsRVProductComment::deleteCascade($boxes, 'reply_comment', 'comment', 'product_comment') ||
                                        !EtsRVProductComment::deleteCascade($boxes, 'comment', '', 'product_comment')
                                    ) {
                                        $this->errors[] = $this->l('Cannot delete item.', 'AdminEtsRVUsersController');
                                    }
                                    if (!$this->errors) {
                                        $group_tables += array(
                                            'grade',
                                            'publish_lang',
                                        );
                                        if (!EtsRVModel::deleteGroupTables($group_tables, $table, 'id_' . pSQL($table) . ' IN (' . $ids . ')')) {
                                            $this->errors[] = $this->l('Cannot delete item(s)', 'AdminEtsRVUsersController');
                                        }
                                    }
                                    break;
                                case 'ets_rv_comment':
                                    if (EtsRVProductComment::deleteCascade($boxes, 'reply_comment', '', 'comment')) {
                                        if (!EtsRVModel::deleteGroupTables($group_tables, $table, 'id_' . pSQL($table) . ' IN (' . $ids . ')')) {
                                            $this->errors[] = $this->l('Cannot delete item(s)', 'AdminEtsRVUsersController');
                                        }
                                    }
                                    break;
                                default:
                                    if (!EtsRVModel::deleteGroupTables($group_tables, $table, 'id_' . pSQL($table) . ' IN (' . $ids . ')')) {
                                        $this->errors[] = $this->l('Cannot delete item(s)', 'AdminEtsRVUsersController');
                                    }
                                    break;
                            }
                        } else
                            $this->errors[] = $this->l('Cannot find the item that needs to be deleted.', 'AdminEtsRVUsersController');
                    }
                }
            }
        }

        $hasError = count($this->errors) > 0 ? 1 : 0;
        $user = new EtsRVProductCommentCustomer($id_customer);
        $this->jsonRender([
            'errors' => $hasError ? Tools::nl2br(implode(PHP_EOL, $this->errors)) : false,
            'msg' => !$hasError ? $this->l('Delete successfully.', 'AdminEtsRVUsersController') : '',
            'total' => EtsRVProductCommentCustomer::getTotal($user),
            'list' => $this->renderList()
        ]);
    }

    public function ajaxProcessBlock()
    {
        $this->loadObject(true);
        if ($this->object->id_customer < 1)
            $this->errors[] = $this->l('This user ID does not exist', 'AdminEtsRVUsersController');
        if (count($this->errors) < 1) {
            $this->object->is_block = 1;
            if (!$this->object->save())
                $this->errors[] = $this->l('Failed to block this user.', 'AdminEtsRVUsersController');
        }
        $hasError = (bool)count($this->errors);;
        $this->jsonRender([
            'errors' => $hasError ? implode(PHP_EOL, $this->errors) : false,
            'msg' => !$hasError ? $this->l('This user was blocked.', 'AdminEtsRVUsersController') : '',
            'list' => $this->renderList(),
        ]);
    }

    public function ajaxProcessUnBlock()
    {
        $this->loadObject(true);
        if ($this->object->id < 1)
            $this->errors[] = $this->l('This user ID does not exist', 'AdminEtsRVUsersController');
        if (count($this->errors) < 1) {
            $this->object->is_block = 0;
            if (!$this->object->update())
                $this->errors[] = $this->l('Failed to unblock this user.', 'AdminEtsRVUsersController');
        }
        $hasError = (bool)count($this->errors);
        $this->jsonRender([
            'errors' => $hasError ? implode(PHP_EOL, $this->errors) : false,
            'msg' => !$hasError ? $this->l('This user was unblocked.', 'AdminEtsRVUsersController') : '',
            'list' => $this->renderList(),
        ]);
    }

    public function renderList()
    {
        $this->context->smarty->assign(['link' => $this->context->link]);

        return parent::renderList();
    }

    public function displayTotalReviews($total, $row)
    {
        if (!empty($row['id_customer'])) {
            return $this->module->toLink(EtsRVLink::getAdminLink(Ets_reviews::TAB_PREFIX . 'Activity', true, [], ['activity_type' => EtsRVActivity::ETS_RV_TYPE_REVIEW, 'customer_id' => (int)$row['id_customer']], $this->context), $total, '');
        }
        return $total;
    }

    public function displayTotalReviewComments($total, $row)
    {
        if (!empty($row['id_customer'])) {
            return $this->module->toLink(EtsRVLink::getAdminLink(Ets_reviews::TAB_PREFIX . 'Activity', true, [], ['activity_type' => EtsRVActivity::ETS_RV_TYPE_COMMENT, 'customer_id' => (int)$row['id_customer']], $this->context), $total, '');
        }
        return null;
    }

    public function displayTotalReviewReplies($total, $row)
    {
        if (!empty($row['id_customer'])) {
            return $this->module->toLink(EtsRVLink::getAdminLink(Ets_reviews::TAB_PREFIX . 'Activity', true, [], ['activity_type' => EtsRVActivity::ETS_RV_TYPE_REPLY_COMMENT, 'customer_id' => (int)$row['id_customer']], $this->context), $total, '');
        }
        return null;
    }

    public function displayTotalQuestions($total, $row)
    {
        if (!empty($row['id_customer'])) {
            return $this->module->toLink(EtsRVLink::getAdminLink(Ets_reviews::TAB_PREFIX . 'Activity', true, [], ['activity_type' => EtsRVActivity::ETS_RV_TYPE_QUESTION, 'customer_id' => (int)$row['id_customer']], $this->context), $total, '');
        }
        return null;
    }

    public function displayTotalAnswers($total, $row)
    {
        if (!empty($row['id_customer'])) {
            return $this->module->toLink(EtsRVLink::getAdminLink(Ets_reviews::TAB_PREFIX . 'Activity', true, [], ['activity_type' => EtsRVActivity::ETS_RV_TYPE_ANSWER_QUESTION, 'customer_id' => (int)$row['id_customer']], $this->context), $total, '');
        }
        return null;
    }

    public function displayTotalQAComments($total, $row)
    {
        if (!empty($row['id_customer'])) {
            return $this->module->toLink(EtsRVLink::getAdminLink(Ets_reviews::TAB_PREFIX . 'Activity', true, [], ['activity_type' => EtsRVActivity::ETS_RV_TYPE_COMMENT_QUESTION . '-' . EtsRVActivity::ETS_RV_TYPE_COMMENT_ANSWER, 'customer_id' => (int)$row['id_customer']], $this->context), $total, '');
        }
        return null;
    }

    public function displayIsBlock($is_block)
    {
        return (int)$is_block ? $this->l('Yes', 'AdminEtsRVUsersController') : $this->l('No', 'AdminEtsRVUsersController');
    }

    public function initToolbar()
    {
        parent::initToolbar(); // TODO: Change the autogenerated stub

        unset($this->toolbar_btn['new']);
    }

    public function initToolbarTitle()
    {
        $this->toolbar_title = $this->l('Authors', 'AdminEtsRVUsersController');
    }

    public function loadObject($opt = false)
    {
        if (empty($this->className)) {
            return true;
        }
        unset($opt);
        $id = (int)Tools::getValue($this->identifier);
        $this->object = new $this->className($id);

        return $this->object;
    }

    public function initContent()
    {
        if ($this->display == 'view') {
            $this->jsonRender([
                'errors' => count($this->errors) > 0 ? implode(Tools::nl2br("\n"), $this->errors) : false,
                'form' => $this->renderView(),
            ]);
        }

        parent::initContent();

    }

    public function renderView()
    {
        $identifier = (int)Tools::getValue($this->identifier);
        $userComment = new EtsRVProductCommentCustomer($identifier);

        if ($identifier)
            $userComment->customer = new Customer($identifier);

        if (!$userComment->id_customer)
            $userComment->customer_name = $this->l('Unknown', 'AdminEtsRVUsersController');

        $userComment->link = (int)$userComment->id_customer > 0 ? EtsRVLink::getAdminLink('AdminCustomers', true, $this->module->ps1760 ? ['route' => 'admin_customers_view', 'customerId' => (int)$userComment->id_customer] : [], ['viewcustomer' => '', 'id_customer' => (int)$userComment->id_customer], $this->context) : '';
        $userComment->grade = EtsRVProductComment::getGradeByIdCustomer($identifier);

        EtsRVProductCommentCustomer::getTotal($userComment);

        $this->context->smarty->assign([
            'user' => $userComment,
            'buttons' => array(
                array(
                    'id' => 'block',
                    'title' => $this->l('Block this user from submitting comments', 'AdminEtsRVUsersController'),
                    'name' => $this->l('Block', 'AdminEtsRVUsersController'),
                    'icon' => 'process-icon-lock icon-lock',
                    'class' => 'block',
                    'confirm' => $this->l('Are you sure you want to block this user? After blocking this user, this person will not be able to submit comments on your website', 'AdminEtsRVUsersController'),
                    'href' => self::$currentIndex . '&blockets_rv_product_comment&token=' . $this->token,
                ),
                array(
                    'id' => 'unblock',
                    'title' => $this->l('Unblock this user from submitting comments', 'AdminEtsRVUsersController'),
                    'name' => $this->l('Unblock', 'AdminEtsRVUsersController'),
                    'icon' => 'process-icon-unlock icon-unlock',
                    'class' => 'unblock',
                    'href' => self::$currentIndex . '&unblockets_rv_product_comment&token=' . $this->token,
                ),
            ),
            'toolbar_btn' => array(),
            'currentIndex' => self::$currentIndex,
            'table' => $this->table,
            'identifier' => $this->identifier,
            'token' => $this->token
        ]);

        return $this->context->smarty->fetch($this->module->getLocalPath() . 'views/templates/admin/view-user.tpl');
    }
    static $st_employees = [];
    static $st_customers = [];

    public function buildFieldCustomerLink($customer_name, $tr)
    {
        $profile_photo = $href = '';
        $is_employee = false;
        if (isset($tr['employee']) && $tr['employee'] > 0) {
            if (isset(self::$st_employees[(int)$tr['employee']]) && self::$st_employees[(int)$tr['employee']])
                return self::$st_employees[(int)$tr['employee']];
            $is_employee = true;
            if (@file_exists($this->module->getLocalPath() . 'views/img/employee_avatar_default.jpg')) {
                $profile_photo = EtsRVLink::getMediaLink($this->module->getPathUri() . 'views/img/employee_avatar_default.jpg', $this->context);
            }
            $href = EtsRVLink::getAdminLink('AdminEmployees', true, ($this->module->ps1760 ? ['route' => 'admin_employees_edit', 'employeeId' => (int)$tr['employee']] : []), ['viewemployee' => '', 'id_employee' => (int)$tr['employee']], $this->context);
        } elseif (isset($tr['id_customer']) && $tr['id_customer'] > 0) {
            if (isset(self::$st_employees[(int)$tr['id_customer']]) && self::$st_employees[(int)$tr['id_customer']])
                return self::$st_employees[(int)$tr['id_customer']];
            $profile_photo = EtsRVProductCommentCustomer::getAvatarByIdCustomer((int)$tr['id_customer']);
            if (trim($profile_photo) !== '' && file_exists(_PS_IMG_DIR_ . $this->module->name . '/a/' . $profile_photo)) {
                $profile_photo = EtsRVLink::getMediaLink(_PS_IMG_ . $this->module->name . '/a/' . $profile_photo, $this->context);
            } elseif (@file_exists($this->module->getLocalPath() . 'views/img/customer_avatar_default.jpg')) {
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
            'avatar' => $profile_photo,
        ]);
        $customer_link = $this->context->smarty->fetch($this->module->getLocalPath() . 'views/templates/admin/customer-link.tpl');
        if ($is_employee) {
            self::$st_employees[(int)$tr['employee']] = $customer_link;
        } else
            self::$st_customers[(int)$tr['id_customer']] = $customer_link;
        return $customer_link;
    }

    public function displayCustomer($id_customer)
    {
        $attrs = [
            'class' => 'ets_rv_customer_id_' . $id_customer
        ];
        return EtsRVTools::displayText(($id_customer ?: '--'), 'span', $attrs);
    }
}