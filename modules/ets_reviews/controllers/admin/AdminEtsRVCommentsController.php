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

class AdminEtsRVCommentsController extends AdminEtsRVBaseController
{
    public static $cache_langs = array();
    public $qa = 0;
    public $answer = 0;
    public $label;

    public function __construct()
    {
        $this->table = 'ets_rv_comment';
        $this->className = 'EtsRVComment';
        $this->identifier = 'id_ets_rv_comment';

        $this->allow_export = true;
        $this->_redirect = false;
        $this->list_no_link = true;
        $this->lang = true;

        parent::__construct();

        $this->label = $this->answer ? $this->l('Answers', 'AdminEtsRVCommentsController') : ($this->qa ? $this->l('Question Comments', 'AdminEtsRVCommentsController') : $this->l('Comments', 'AdminEtsRVCommentsController'));

        $this->_defaultOrderBy = 'id_ets_rv_comment';
        $this->_defaultOrderWay = 'DESC';

        $this->_pagination = [2, 20, 50, 100, 300, 1000];

        $this->addRowAction('approve');
        $this->addRowAction('view');
        $this->addRowAction('edit');
        $this->addRowAction('private');
        $this->addRowAction('delete');

        $this->bulk_actions = array(
            'approve' => array(
                'text' => $this->l('Approve selected', 'AdminEtsRVCommentsController'),
                'confirm' => $this->l('Approve selected items?', 'AdminEtsRVCommentsController'),
                'icon' => 'icon-check',
            ),
            'delete' => array(
                'text' => $this->l('Delete selected', 'AdminEtsRVCommentsController'),
                'confirm' => $this->l('Do you want to delete selected items?', 'AdminEtsRVCommentsController'),
                'icon' => 'icon-trash',
            ),
        );
        $this->_select = 'pc.id_product
            , IF(a.employee > 0, CONCAT(e.`firstname`, \' \',  e.`lastname`), IF(c.id_customer, CONCAT(c.`firstname`, \' \',  c.`lastname`), IF(c.id_customer is NULL AND a.id_customer > 0, "' . $this->l('Deleted account', 'AdminEtsRVCommentsController') . '" , NULL))) customer_name
            , IF(a.employee > 0, e.id_employee, c.id_customer) `customer_id`
            , pl.`name` `product_name`
            , CONCAT(LEFT(@subquery@, (@len@ - IFNULL(NULLIF(LOCATE(" ", REVERSE(LEFT(@subquery@, @len@))), 0) - 1, 0))), IF(LENGTH(@subquery@) > @len@, "...","")) `content`
            , IF((@usefulness@ = 1) > 0,(@usefulness@ = 1), NULL) AS total_like
            , IF((@usefulness@ = 0) > 0,(@usefulness@ = 0), NULL) AS total_dislike
            , a.id_ets_rv_comment AS replies
            , IF(a.validate=1, 1, 0) `badge_success`
            , IF(a.validate=0, 1, 0) `badge_warning`
            , IF(a.validate=2, 1, 0) `badge_danger`
            , IF(a.validate=3, 1, 0) `badge_reject`
            , pc.id_product `product_id`
        ';

        $this->_select = @preg_replace(['/@subquery@/i', '/@len@/i', '/@usefulness@/i'], [
            'IF(b.`content` != "" AND b.`content` is NOT NULL, b.`content`, pol.`content`)',
            120,
            'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'ets_rv_comment_usefulness` pcu WHERE pcu.`id_ets_rv_comment` = a.`id_ets_rv_comment` AND pcu.`usefulness`',
        ], $this->_select);

        $this->_join = '
            LEFT JOIN `' . _DB_PREFIX_ . 'ets_rv_comment_origin_lang` pol ON (pol.id_ets_rv_comment = a.id_ets_rv_comment)
            LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON (c.`id_customer` = a.`id_customer`)
            LEFT JOIN `' . _DB_PREFIX_ . 'employee` e ON (e.`id_employee` = a.`employee`)
            LEFT JOIN `' . _DB_PREFIX_ . 'ets_rv_product_comment` pc ON (a.`id_ets_rv_product_comment` = pc.`id_ets_rv_product_comment`)
            LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (pl.`id_product` = pc.`id_product` AND pl.`id_lang` = ' . (int)Context::getContext()->language->id . Shop::addSqlRestrictionOnLang('pl') . ')
        ';

        $this->_where = '
            AND pc.`id_ets_rv_product_comment` is NOT NULL
            AND a.question=' . (int)$this->qa . ' 
            AND a.answer=' . (int)$this->answer;
        $this->_group = 'GROUP BY a.id_ets_rv_comment';


        if (!self::$cache_langs) {
            if ($languages = Language::getLanguages(false)) {
                foreach ($languages as $l)
                    self::$cache_langs[(int)$l['id_lang']] = $l['name'];
            }
        }
        $this->fields_list = array(
            'id_ets_rv_comment' => array(
                'title' => $this->l('ID', 'AdminEtsRVCommentsController'),
                'type' => 'int',
                'class' => 'ets-rv-id_ets_rv_comment id',
                'align' => 'ets-rv-id_ets_rv_comment',
            ),
            'content' => array(
                'title' => $this->qa ? $this->l('Content', 'AdminEtsRVCommentsController') : $this->l('Comment content', 'AdminEtsRVCommentsController'),
                'type' => 'text',
                'filter_key' => 'content',
                'class' => 'ets-rv-content comment_content',
                'havingFilter' => true,
                'callback' => 'getCommentsContentIcon',
                'align' => 'ets-rv-content',
            ),
            'replies' => array(
                'title' => $this->qa ? $this->l('Comments', 'AdminEtsRVCommentsController') : $this->l('Replies', 'AdminEtsRVCommentsController'),
                'type' => 'text',
                'filter_key' => 'replies',
                'havingFilter' => true,
                'callback' => 'getRepliesNumberAsField',
                'class' => 'ets-rv-replies ets_replies text-center',
                'orderby' => false,
                'search' => false,
                'align' => 'ets-rv-replies',
            ),
            'total_like' => array(
                'title' => $this->l('Like', 'AdminEtsRVCommentsController'),
                'type' => 'text',
                'class' => 'ets-rv-total_like ets_like text-center',
                'havingFilter' => true,
                'align' => 'ets-rv-total_like',
            ),
            'total_dislike' => array(
                'title' => $this->l('Dislike', 'AdminEtsRVCommentsController'),
                'type' => 'text',
                'class' => 'ets-rv-total_dislike ets_dislike text-center',
                'havingFilter' => true,
                'align' => 'ets-rv-total_dislike',
            ),
            'customer_name' => array(
                'title' => $this->l('Author', 'AdminEtsRVCommentsController'),
                'type' => 'text',
                'havingFilter' => true,
                'callback' => 'buildFieldCustomerLink',
                'ref' => 'customer_id',
                'align' => 'ets-rv-customer_name',
                'class' => 'ets-rv-customer_name',
            ),
            'product_name' => array(
                'title' => $this->l('Product', 'AdminEtsRVCommentsController'),
                'type' => 'text',
                'callback' => 'buildFieldProductLink',
                'havingFilter' => true,
                'ref' => 'product_id',
                'align' => 'ets-rv-product_name',
                'class' => 'ets-rv-product_name',
            ),
            'validate' => array(
                'title' => $this->l('Status', 'AdminEtsRVCommentsController'),
                'type' => 'select',
                'list' => EtsRVDefines::getInstance()->getReviewStatus(),
                'filter_key' => 'a!validate',
                'callback' => 'displayValidate',
                'class' => 'ets-rv-validate review text-center',
                'badge_success' => true,
                'badge_warning' => true,
                'badge_danger' => true,
                'badge_reject' => true,
                'align' => 'ets-rv-validate',
            ),
            'date_add' => array(
                'title' => $this->l('Time of publication', 'AdminEtsRVCommentsController'),
                'type' => 'date',
                'filter_key' => 'a!date_add',
                'align' => 'ets-rv-date_add',
                'class' => 'ets-rv-date_add',
            ),
        );

        if (!$this->answer && $this->qa) {
            unset($this->fields_list['replies']);
        }
    }

    public function getCommentsContentIcon($content)
    {
        $icons = EtsRVComment::getIcons();
        $content = str_replace(array_keys($icons), $icons, $content);
        $content = Tools::nl2br($content);
        return $content;
    }

    public function getRepliesNumberAsField($commentId)
    {
        if ($commentId) {
            $repo = EtsRVReplyCommentRepository::getInstance();
            $idLang = $this->context->language->id;

            return ($replies = $repo->getRepliesNumberByCommentId((int)$commentId, $idLang, null, 1, $this->context, $this->qa)) ? $replies . (($awaiting = $repo->getRepliesNumberByCommentId((int)$commentId, $idLang, 0, 1, $this->context, $this->qa)) ? ' (' . $awaiting . ' ' . $this->l('Awaiting', 'AdminEtsRVCommentsController') . ')' : '') : '--';
        }
    }

    public function initContent()
    {
        parent::initContent();

        if ($this->ajax) {
            if ($this->display == 'edit') {
                $this->jsonRender([
                    'form' => $this->renderForm(),
                ]);
            } elseif ($this->display == 'view') {
                $this->jsonRender([
                        'errors' => count($this->errors) > 0 ? implode(Tools::nl2br("\n"), $this->errors) : false,
                    ] + $this->ajaxRenderView());
            }
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

    public function initProcess()
    {
        parent::initProcess();

        $this->context->smarty->assign([
            'link' => $this->context->link
        ]);
        if (Tools::isSubmit('approve' . $this->table)) {
            $this->action = 'approve';
        } elseif (Tools::isSubmit('private' . $this->table)) {
            $this->action = 'private';
        }

        $this->module->postProcess();
    }

    public function ajaxProcessSave()
    {
        $id_product = (int)Tools::getValue('id_product');
        $identifier = (int)Tools::getValue($this->identifier);
        $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');

        $comment_content = strip_tags(trim(Tools::getValue('content_' . $id_lang_default)));
        if ($comment_content == '') {
            $this->errors[] = $this->l('Content cannot be empty', 'AdminEtsRVCommentsController');
        } elseif (!Validate::isMessage($comment_content)) {
            $this->errors[] = $this->l('Content is invalid', 'AdminEtsRVCommentsController');
        }
        if (!$identifier) {
            $this->errors[] = $this->l('Please select the items you want to update.', 'AdminEtsRVCommentsController');
        }
        $date_add = Tools::getValue('date_add');
        if (trim($date_add) == '')
            $this->errors[] = $this->l('Date add is required', 'AdminEtsRVCommentsController');
        elseif (!Validate::isDate($date_add))
            $this->errors[] = $this->l('Date add is invalid', 'AdminEtsRVCommentsController');
        // Save.
        if (!$this->errors) {
            $comment = new EtsRVComment($identifier);
            $comment->question = (int)Tools::getValue('qa') ? 1 : 0;
            $validateOld = $comment->validate;
            $comment->validate = (int)Tools::getValue('validate');
            $comment->date_add = $date_add;

            if ($comment->id)
                $comment->upd_date = date('Y-m-d H:i:s');

            $languages = Language::getLanguages(false);
            if ($languages) {
                foreach ($languages as $l) {
                    $comment->content[(int)$l['id_lang']] = strip_tags(Tools::getValue('content_' . (int)$l['id_lang']) ?: Tools::getValue('content_' . $id_lang_default));
                }
            }

            // Languages to display.
            if ($comment->save()) {
                if (!$identifier) {
                    if ($this->qa) {
                        $content = $comment->answer ? 'answered_to_a_question' : 'commented_on_a_question';
                    } else {
                        $content = 'commented_on_a_review';
                    }
                    EtsRVCommentEntity::getInstance()->addActivity($comment, !$comment->question ? EtsRVActivity::ETS_RV_TYPE_COMMENT : ($comment->answer ? EtsRVActivity::ETS_RV_TYPE_ANSWER_QUESTION : EtsRVActivity::ETS_RV_TYPE_COMMENT_QUESTION), $comment->answer ? EtsRVActivity::ETS_RV_ACTION_ANSWER : EtsRVActivity::ETS_RV_ACTION_COMMENT, $id_product, $content, $this->context);
                }
                // Origin language only add new.
                EtsRVComment::saveOriginLang($comment->id, (int)$this->context->language->id, $comment_content);

                // Mail approved:
                if ($validateOld == 0 && $comment->validate == 1) {
                    EtsRVCommentEntity::getInstance()->commentMailApproved($comment, true);
                }
            } else
                $this->errors[] = $this->l('Unknown error happened', 'AdminEtsRVCommentsController');
        }
        $hasError = count($this->errors) > 0 ? 1 : 0;
        $this->jsonRender($this->extraJSON([
            'errors' => $hasError ? implode(Tools::nl2br("\n"), $this->errors) : false,
            'msg' => !$hasError ? $this->l('Saved', 'AdminEtsRVCommentsController') : '',
        ]));
    }

    public function initToolbarTitle()
    {
        $this->toolbar_title = is_array($this->breadcrumbs) ? array_unique($this->breadcrumbs) : array($this->breadcrumbs);
        unset($this->toolbar_btn['new']);
    }

    public function getFields()
    {
        $languages = [[
            'id_lang' => 'all',
            'name' => $this->l('All', 'AdminEtsRVCommentsController')
        ]];
        $languages2 = Language::getLanguages(false);
        if ($languages2) {
            foreach ($languages2 as $l) {
                $languages[] = [
                    'id_lang' => (int)$l['id_lang'],
                    'name' => $l['name'],
                ];
            }
        }

        // Review status.
        $validates = [];
        foreach (EtsRVDefines::getInstance()->getReviewStatus() as $key => $val) {
            $validates[] = [
                'id' => $key,
                'name' => $val
            ];
        }
        return array(
            'id_customer' => array(
                'type' => 'hidden',
                'label' => $this->l('Customer', 'AdminEtsRVCommentsController'),
                'name' => 'id_customer',
            ),
            'id_ets_rv_product_comment' => array(
                'type' => 'hidden',
                'label' => $this->l('Product comment ID', 'AdminEtsRVCommentsController'),
                'name' => 'id_ets_rv_product_comment',
            ),
            'content' => array(
                'type' => 'textarea',
                'name' => 'content',
                'label' => $this->l('Content', 'AdminEtsRVCommentsController'),
                'required' => true,
                'lang' => true,
                'autoload_rte' => false,
                'desc' => sprintf($this->l('Maximum length: %s characters', 'AdminEtsRVCommentsController'), EtsRVComment::NAME_MAX_LENGTH),
            ),
            'validate' => array(
                'type' => 'select',
                'name' => 'validate',
                'label' => $this->l('Status', 'AdminEtsRVCommentsController'),
                'options' => array(
                    'query' => $validates,
                    'id' => 'id',
                    'name' => 'name'
                ),
            ),
            'date_add' => [
                'type' => 'datetime',
                'name' => 'date_add',
                'label' => $this->l('Date add', 'AdminEtsRVCommentsController'),
                'required' => true,
            ]
        );
    }

    public function renderForm()
    {
        $id_comment = (int)Tools::getValue($this->identifier);
        $refreshController = trim(Tools::getValue('refreshController'));
        $comment = new EtsRVComment($id_comment);

        $fields_form_1 = array(
            'form' => array(
                'legend' => array(
                    'title' => $id_comment ? sprintf($this->l('Edit %1s #%2s', 'AdminEtsRVCommentsController'), $this->label, $id_comment) : sprintf($this->l('Add new %s', 'AdminEtsRVCommentsController'), $this->label),
                    'icon' => 'icon-cogs',
                ),
                'input' => $this->getFields(),
                'submit' => array(
                    'title' => $this->l('Save', 'AdminEtsRVCommentsController'),
                    'class' => 'btn btn-default pull-right',
                    'name' => 'submitAdd' . $this->table,
                ),
                'buttons' => array(
                    'cancel' => array(
                        'href' => self::$currentIndex . '&token=' . $this->token,
                        'title' => $this->l('Cancel', 'AdminEtsRVCommentsController'),
                        'icon' => 'process-icon-cancel',
                        'class' => 'ets_rv_cancel' . ((int)Tools::getValue('back_to_view') ? ' ets_rv_back_to_view' : ''),
                    ),
                ),
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->module = $this->module;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitAdd' . $this->table;
        $helper->currentIndex = self::$currentIndex . ($this->qa ? '&qa=' . (int)$this->qa : '') . ($this->answer ? '&answer=' . (int)$this->answer : '') . ($refreshController !== '' ? '&refreshController=' . $refreshController : '') . (Tools::getValue('page') ? '&submitFilter' . $this->list_id . '=' . (int)Tools::getValue('page') : '') . (Tools::isSubmit('submitFilter' . $this->list_id) ? '&submitFilter' . $this->list_id . '=' . (int)Tools::getValue('submitFilter' . $this->list_id) : '');
        $helper->token = $this->token;
        $helper->show_cancel_button = false;

        // Re-build fields.
        $fields_value = $this->getCommentsFieldsValues($comment, $fields_form_1['form']['input'], $helper->submit_action);

        // Custom fields_value.
        $tpl_vars = [];
        if ($id_comment) {
            // Primary key.
            $fields_form_1['form']['input'][] = array(
                'type' => 'hidden',
                'name' => 'id_ets_rv_comment',
            );
            $fields_value['id_ets_rv_comment'] = (int)$id_comment;
            $fields_form_1['form']['input'][] = array(
                'type' => 'hidden',
                'name' => 'id_ets_rv_product_comment',
            );
            $fields_value['id_ets_rv_product_comment'] = (int)$comment->id_ets_rv_product_comment;
        }
        if ($id_product = (int)Tools::getValue('id_product')) {
            $fields_value['id_product'] = (int)$id_product;
        }

        $tpl_vars = array_merge($tpl_vars, [
            'fields_value' => $fields_value,
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
            'identifier' => (int)$id_comment,
            'form_id' => 'edit',
        ]);

        $helper->tpl_vars = $tpl_vars;
        return $helper->generateForm(array($fields_form_1));
    }

    public function ajaxRenderView()
    {
        $identifier = (int)Tools::getValue($this->identifier);
        if (!$identifier) {
            $this->errors[] = $this->l('Review ID does not exist.', 'AdminEtsRVCommentsController');
        } else {
            $productComment = new EtsRVProductComment($identifier, $this->context->language->id);
            if (!(int)$productComment->id_product)
                return;
            $p = new Product((int)$productComment->id_product, true, $this->context->language->id);
            $p->link = $this->context->link->getProductLink($p, $p->link_rewrite, $p->category, $p->ean13, $this->context->language->id);
            $image = Product::getCover((int)$productComment->id_product, $this->context);
            if (isset($image['id_image']) && (int)$image['id_image'] > 0)
                $p->image = $this->context->link->getImageLink($p->link_rewrite, (int)$image['id_image'], EtsRVTools::getFormattedName('home'));
            $productComment->product = $p;

            $toolbar_btn_label = $this->qa ? $this->l('questions', 'AdminEtsRVCommentsController') : $this->l('comments', 'AdminEtsRVCommentsController');
            $this->context->smarty->assign([
                'review' => $productComment,
                'buttons' => array(
                    array(
                        'id' => 'delete',
                        'title' => sprintf($this->l('Delete %s', 'AdminEtsRVCommentsController'), $this->label),
                        'name' => $this->l('Delete', 'AdminEtsRVCommentsController'),
                        'icon' => 'process-icon-delete',
                        'class' => 'delete',
                        'confirm' => $this->l('Do you want to delete selected item?', 'AdminEtsRVCommentsController'),
                    ),
                    array(
                        'id' => 'edit',
                        'title' => sprintf($this->l('Edit %s', 'AdminEtsRVCommentsController'), $this->label),
                        'name' => $this->l('Edit', 'AdminEtsRVCommentsController'),
                        'icon' => 'process-icon-edit',
                        'class' => 'edit',
                    ),
                    array(
                        'id' => 'approve',
                        'title' => sprintf($this->l('Approve %s', 'AdminEtsRVCommentsController'), $this->label),
                        'name' => $this->l('Approve', 'AdminEtsRVCommentsController'),
                        'icon' => 'process-icon-check icon-check',
                        'class' => 'ets_rv_approve',
                    ),
                ),
                'toolbar_btn' => array(
                    array(
                        'id' => 'block',
                        'title' => sprintf($this->l('Block this user from submitting %s', 'AdminEtsRVCommentsController'), $toolbar_btn_label),
                        'name' => $this->l('Block', 'AdminEtsRVCommentsController'),
                        'icon' => 'icon-lock',
                        'class' => 'block',
                        'confirm' => sprintf($this->l('Are you sure you want to block this user? After blocking this user, this person will not be able to submit %s on your website', 'AdminEtsRVCommentsController'), $toolbar_btn_label),
                    ),
                    array(
                        'id' => 'unblock',
                        'title' => sprintf($this->l('Unblock this user from submitting %s', 'AdminEtsRVCommentsController'), $toolbar_btn_label),
                        'name' => $this->l('Unblock', 'AdminEtsRVCommentsController'),
                        'icon' => 'icon-unlock',
                        'class' => 'unblock'
                    ),
                ),
                'currentIndex' => self::$currentIndex . (Tools::isSubmit('submitFilter' . $this->list_id) ? '&submitFilter' . $this->list_id . '=' . (int)Tools::getValue('submitFilter' . $this->list_id) : ''),
                'PS_LANG_IMG_DIR' => _PS_IMG_ . 'l/',
                'review_status' => EtsRVDefines::getInstance()->getReviewStatus(),
                'table' => $this->table,
                'identifier' => $this->identifier,
            ]);
            return [
                'form' => $this->context->smarty->fetch($this->module->getLocalPath() . 'views/templates/hook/bo-view-review.tpl'),
                'product_comment' => [
                    'id_product_comment' => $productComment->id,
                    'id_product' => $productComment->id_product,
                    'qa' => $productComment->question,
                    'back_office' => 1,
                ],
            ];
        }
    }

    public function getCommentsFieldsValues($obj, $inputs, $submit_action)
    {
        $fields = array();
        if (empty($inputs) || !is_object($obj))
            return $fields;
        $languages = Language::getLanguages(false);
        $originLang = $obj->id ? EtsRVComment::getOriginContentById($obj->id) : array();

        if (Tools::isSubmit($submit_action)) {
            foreach ($inputs as $config) {
                if (!isset($config['name']) || !$config['name'])
                    continue;
                $key = $config['name'];
                if ($config['type'] == 'checkbox')
                    $fields[$key] = ($vals = Tools::getValue($key)) ? explode(',', $vals) : array();
                elseif (isset($config['lang']) && $config['lang']) {
                    foreach ($languages as $l) {
                        $fields[$key][$l['id_lang']] = Tools::getValue($key . '_' . $l['id_lang']);
                    }
                } elseif (isset($config['lang']) && !$config['lang']) {
                    $fields[$key] = Tools::getValue($key);
                } elseif (($config['type'] == 'select' && isset($config['multiple']) && $config['multiple'])) {
                    $fields[$key . ($config['type'] == 'select' ? '[]' : '')] = Tools::getValue($key, array());
                } elseif (!isset($config['tree']))
                    $fields[$key] = Tools::getValue($key);
            }
        } else {
            foreach ($inputs as $key => $config) {
                if (!isset($config['name']) || !$config['name'])
                    continue;
                $key = $config['name'];
                if ($config['type'] == 'checkbox')
                    $fields[$key] = $obj->id ? explode(',', $obj->$key) : (isset($config['default']) && $config['default'] ? $config['default'] : array());
                elseif (isset($config['lang']) && $config['lang']) {
                    foreach ($languages as $l) {
                        $values = $obj->$key;
                        $fields[$key][$l['id_lang']] = $obj->id && !empty($values[$l['id_lang']]) ? $values[$l['id_lang']] : ($originLang && !empty($originLang[$key]) ? $originLang[$key] : (isset($config['default']) && $config['default'] ? $config['default'] : null));
                    }
                } elseif (isset($config['lang']) && !$config['lang']) {
                    $fields[$key] = $obj->id && $originLang && !empty($originLang[$key]) ? $originLang[$key] : (isset($config['default']) && $config['default'] ? $config['default'] : '');
                } elseif (($config['type'] == 'select' && isset($config['multiple']) && $config['multiple'])) {
                    $fields[$key . ($config['type'] == 'select' ? '[]' : '')] = $obj->id ? ($obj->$key != '' ? explode(',', $obj->$key) : array()) : (isset($config['default']) && $config['default'] ? $config['default'] : array());
                } elseif (!isset($config['tree']) && $key != $this->identifier)
                    $fields[$key] = $obj->id ? $obj->$key : (isset($config['default']) && $config['default'] ? $config['default'] : null);
            }
        }

        return $fields;
    }

    public function processBulkApprove()
    {
        if (EtsRVTools::isArrayWithIds($this->boxes)) {
            if (!(int)EtsRVComment::getCommentsNumber($this->boxes)) {
                $this->errors[] = $this->l('Selected item(s) is approved', 'AdminEtsRVCommentsController');
            } elseif (!EtsRVComment::approveComments($this->boxes)) {
                $this->errors[] = $this->l('Failed to approve selected item(s)', 'AdminEtsRVCommentsController');
            } else {
                foreach ($this->boxes as $id) {
                    EtsRVCommentEntity::getInstance()->commentMailApproved($id, true);
                }
            }
        } else {
            $this->errors[] = $this->l('Please select the item(s) to approve', 'AdminEtsRVCommentsController');
        }
        if (!count($this->errors)) {
            $this->confirmations = $this->l('Approved', 'AdminEtsRVCommentsController');
        }
    }

    public function processBulkDelete()
    {
        if (EtsRVTools::isArrayWithIds($this->boxes)) {
            if (!EtsRVComment::deleteComments($this->boxes)) {
                $this->errors[] = $this->l('Deleting selected item(s) failed', 'AdminEtsRVCommentsController');
            } else {
                if (EtsRVComment::deleteCascade($this->boxes, 'reply_comment', '', 'comment')) {
                    if (!EtsRVComment::deleteAllChildren($this->boxes))
                        $this->errors[] = $this->l('Cannot delete selected item(s)', 'AdminEtsRVCommentsController');
                } else
                    $this->errors[] = $this->l('Cannot delete cascade by the selected item(s)', 'AdminEtsRVCommentsController');
            }
        } else {
            $this->errors[] = $this->l('Please select the item(s) to delete', 'AdminEtsRVCommentsController');
        }
        if (!count($this->errors)) {
            $this->confirmations = $this->l('Deleted successfully', 'AdminEtsRVCommentsController');
        }
    }

    public function ajaxProcessDelete()
    {
        $id_comment = (int)Tools::getValue('id_ets_rv_comment');
        $comment = new EtsRVComment($id_comment);
        if (!$comment->delete()) {
            $this->errors[] = $this->l('Cannot delete selected item(s)', 'AdminEtsRVCommentsController');
        }
        $has_error = count($this->errors);
        $this->jsonRender($this->extraJSON([
            'errors' => $has_error ? implode("\r\n", $this->errors) : false,
            'msg' => !$has_error ? sprintf($this->l('The %s was successfully deleted.', 'AdminEtsRVCommentsController'), $this->label) : '',
        ]));
    }

    public function extraJSON($data = array())
    {
        $data = parent::extraJSON($data);
        if (!isset($data['list']) || !$data['list']) {
            // Process list filtering
            if ($this->filter && $this->action != 'reset_filters') {
                $this->processFilter();
            }
            $data['list'] = $this->renderList();
        }
        $data['qa'] = $this->qa;
        $data['prop'] = 'Comment';

        return $data;
    }

    public function ajaxProcessApprove()
    {
        $id_comment = (int)Tools::getValue('id_ets_rv_comment');
        $comment = new EtsRVComment($id_comment);

        if (!$comment->validate(1))
            $this->errors[] = $this->l('Failed to approve', 'AdminEtsRVCommentsController');
        else {
            EtsRVCommentEntity::getInstance()->commentMailApproved($comment, true);
        }
        $hasError = (bool)count($this->errors);
        $this->jsonRender($this->extraJSON(array(
            'errors' => $hasError ? implode("\r\n", $this->errors) : false,
            'msg' => !$hasError ? $this->l('Approve successfully.', 'AdminEtsRVCommentsController') : '',
        )));
    }

    public function ajaxProcessPrivate()
    {
        $id_comment = (int)Tools::getValue('id_ets_rv_comment');
        $comment = new EtsRVComment($id_comment);

        if (!$comment->validate(2)) {
            $this->errors[] = $this->l('Set to private failed', 'AdminEtsRVCommentsController');
        }

        $hasError = (bool)count($this->errors);
        $this->jsonRender($this->extraJSON([
            'errors' => $hasError ? implode("\r\n", $this->errors) : false,
            'msg' => !$hasError ? $this->l('Set to private successfully.', 'AdminEtsRVCommentsController') : '',
        ]));
    }

    public function displayValidate($value)
    {
        return EtsRVDefines::getInstance()->getReviewStatus($value);
    }

    public function displayApproveLink($token, $id, $name = null)
    {
        if ((int)EtsRVComment::getStatusById($id) != 1) {
            if (!isset(self::$cache_lang['approve'])) {
                self::$cache_lang['approve'] = $name !== null ? $name : $this->l('Approve', 'AdminEtsRVCommentsController');
            }

            $this->context->smarty->assign(array(
                'href' => self::$currentIndex . '&' . $this->identifier . '=' . $id . '&approve' . $this->table . '&token=' . ($token != null ? $token : $this->token) . (Tools::isSubmit('submitFilter' . $this->list_id) ? '&submitFilter' . $this->list_id . '=' . (int)Tools::getValue('submitFilter' . $this->list_id) : ''),
                'action' => self::$cache_lang['approve'],
                'class' => 'ets_rv_approve'
            ));

            return $this->context->smarty->fetch($this->module->getLocalPath() . 'views/templates/admin/list_action_approve.tpl');
        }
    }

    public function displayPrivateLink($token, $id, $name = null)
    {
        if ((int)EtsRVComment::getStatusById($id) != 2) {
            if (!isset(self::$cache_lang['private'])) {
                self::$cache_lang['private'] = $name !== null ? $name : $this->l('Set to private', 'AdminEtsRVCommentsController');
            }

            $this->context->smarty->assign(array(
                'href' => self::$currentIndex .
                    '&' . $this->identifier . '=' . $id .
                    '&private' . $this->table . '&token=' . ($token != null ? $token : $this->token) . (Tools::isSubmit('submitFilter' . $this->list_id) ? '&submitFilter' . $this->list_id . '=' . (int)Tools::getValue('submitFilter' . $this->list_id) : ''),
                'action' => self::$cache_lang['private'],
                'class' => 'ets_rv_private'
            ));

            return $this->context->smarty->fetch($this->module->getLocalPath() . 'views/templates/admin/list_action_private.tpl');
        }
    }

    public function displayViewLink($token, $id)
    {
        $comment = new EtsRVComment($id);
        if ($comment->id_ets_rv_product_comment > 0) {
            if (!isset(self::$cache_langs['view'])) {
                self::$cache_lang['view'] = $this->l('View', 'AdminEtsRVCommentsController');
            }
            $url_params = array(
                'id_ets_rv_product_comment' => $comment->id_ets_rv_product_comment,
                'id_ets_rv_comment' => $comment->id,
                'viewets_rv_product_comment' => 1,
                'refreshController' => $this->controller_name,
            );
            if ($comment->answer) {
                $url_params['answer'] = $comment->answer;
            }
            if (Tools::isSubmit('submitFilter' . $this->list_id)) {
                $url_params['submitFilter' . $this->list_id] = (int)Tools::getValue('submitFilter' . $this->list_id);
            }
            $this->context->smarty->assign(array(
                'href' => EtsRVLink::getAdminLink($this->qa ? 'AdminEtsRVQuestions' : 'AdminEtsRVReviewsRatings', true, array(), $url_params, $this->context),
                'action' => self::$cache_lang['view'],
                'class' => 'ets_rv_view_review',
                'token' => $token
            ));

            return $this->context->smarty->fetch($this->module->getLocalPath() . 'views/templates/admin/list_action_view.tpl');
        }
    }
}