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

class AdminEtsRVReviewsController extends AdminEtsRVBaseController
{
    public static $cache_langs = array();
    public $qa = 0;
    public $label;

    public function __construct()
    {
        $this->table = 'ets_rv_product_comment';
        $this->className = 'EtsRVProductComment';
        $this->identifier = 'id_ets_rv_product_comment';

        $this->allow_export = false;
        $this->_redirect = false;
        $this->list_no_link = true;
        $this->lang = true;

        parent::__construct();

        $this->label = $this->qa ? $this->l('question', 'AdminEtsRVReviewsController') : $this->l('review', 'AdminEtsRVReviewsController');

        $this->_defaultOrderBy = 'id_ets_rv_product_comment';
        $this->_defaultOrderWay = 'DESC';

        $this->_pagination = [2, 20, 50, 100, 300, 1000];

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
                'text' => $this->l('Approve selected', 'AdminEtsRVReviewsController'),
                'confirm' => $this->l('Approve selected items?', 'AdminEtsRVReviewsController'),
                'icon' => 'icon-check',
            ),
            'delete' => array(
                'text' => $this->l('Delete selected', 'AdminEtsRVReviewsController'),
                'confirm' => $this->l('Do you want to delete selected items?', 'AdminEtsRVReviewsController'),
                'icon' => 'icon-trash',
            ),
        );
        $multiLang = (int)Configuration::get('ETS_RV_MULTILANG_ENABLED');
        $this->_select = '
            ROUND(a.grade, 1) `grade`,
            IF(c.id_customer, CONCAT(c.`firstname`, \' \',  c.`lastname`), IF(c.id_customer is NULL AND a.id_customer > 0,"' . $this->l('Deleted account', 'AdminEtsRVReviewsController') . '" , a.customer_name)) customer_name,
            c.id_customer `customer_id`,
            pl.`name` `product_name`,
            pl.`id_product` `product_id`,
            CONCAT(LEFT(@subquery@, (@len@ - IFNULL(NULLIF(LOCATE(" ", REVERSE(LEFT(@subquery@, @len@))), 0) - 1, 0))), IF(LENGTH(@subquery@) > @len@, "...","")) `content`,
            IF(a.publish_all_language, 1, (SELECT GROUP_CONCAT(cpl.id_lang SEPARATOR \',\') 
                FROM `' . _DB_PREFIX_ . 'ets_rv_product_comment_publish_lang` cpl 
                WHERE cpl.id_ets_rv_product_comment = a.id_ets_rv_product_comment)
            ) as `publish_lang`,
            i.id_image, 
            il.legend `image_name`,
            IF((@usefulness@ = 1) > 0,(@usefulness@ = 1), NULL) AS total_like,
            IF((@usefulness@ = 0) > 0,(@usefulness@ = 0), NULL) AS total_dislike,
            a.id_ets_rv_product_comment AS comments,
            a.id_ets_rv_product_comment AS replies,
            IF(' . (int)$multiLang . ' != 0 AND b.`title` != "" AND b.`title` is NOT NULL, b.`title`, IF(pol.`title`, pol.`title`, NULL)) title,
            IF(a.validate=1, 1, 0) `badge_success`,
            IF(a.validate=0, 1, 0) `badge_warning`,
            IF(a.validate=2, 1, 0) `badge_danger`
        ';

        $this->_select = @preg_replace(['/@subquery@/i', '/@len@/i', '/@usefulness@/i'], [
            'IF(' . (int)$multiLang . ' != 0 AND b.`content` != "" AND b.`content` is NOT NULL, b.`content`, pol.`content`)',
            120,
            'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'ets_rv_product_comment_usefulness` pcu WHERE pcu.`id_ets_rv_product_comment` = a.`id_ets_rv_product_comment` AND pcu.`usefulness`',
        ], $this->_select);

        $this->_join = 'LEFT JOIN `' . _DB_PREFIX_ . 'ets_rv_product_comment_origin_lang` pol ON (pol.id_ets_rv_product_comment = a.id_ets_rv_product_comment)
            LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON (c.`id_customer` = a.`id_customer`)
            LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (pl.`id_product` = a.`id_product` AND pl.`id_lang` = ' . (int)$this->context->language->id . Shop::addSqlRestrictionOnLang('pl') . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'image` i ON (i.`id_product` = a.`id_product` AND i.cover = 1)
            LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int)$this->context->language->id . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop ON (i.`id_image` = image_shop.`id_image` AND image_shop.`id_shop` = ' . (int)$this->context->shop->id . ')
        ';

        $this->_where = 'AND a.question=' . (int)$this->qa . (($id_customer = Tools::getValue('id_customer', null)) !== null && Validate::isUnsignedInt($id_customer) && !Tools::isSubmit('submitAdd' . $this->table) ? ' AND a.id_customer=' . (int)$id_customer : '');
        $this->_group = 'GROUP BY a.id_ets_rv_product_comment';


        if (!self::$cache_langs) {
            if ($languages = Language::getLanguages(false)) {
                foreach ($languages as $l)
                    self::$cache_langs[(int)$l['id_lang']] = $l['name'];
            }
        }
        $this->fields_list = EtsRVDefines::getInstance()->getFieldsList($this->qa);
    }

    public function getCommentsNumberAsField($productCommentId)
    {
        if ($productCommentId) {
            $repo = EtsRVCommentRepository::getInstance();
            $idLang = $this->context->language->id;

            return ($comments = $repo->getCommentsNumber((int)$productCommentId, $idLang, null, 1, $this->context)) ? $comments . (($awaiting = $repo->getCommentsNumber((int)$productCommentId, $idLang, 0, 1, $this->context)) ? ' (' . $awaiting . ' ' . $this->l('Awaiting', 'AdminEtsRVReviewsController') . ')' : '') : '--';
        }

        return null;
    }

    public function getRepliesNumberAsField($productCommentId)
    {
        if ($productCommentId) {
            $repo = EtsRVReplyCommentRepository::getInstance();
            $idLang = $this->context->language->id;

            return ($replies = $repo->getRepliesNumber((int)$productCommentId, $idLang, null, 1, $this->context)) ? $replies . (($awaiting = $repo->getRepliesNumber((int)$productCommentId, $idLang, 0, 1, $this->context)) ? ' (' . $awaiting . ' ' . $this->l('Awaiting', 'AdminEtsRVReviewsController') . ')' : '') : '--';
        }

        return null;
    }

    public function getCommentsContentIcon($content)
    {
        return $content;
    }

    public function buildFieldPublishLang($value, $tr)
    {
        if (isset($tr['publish_all_language']) && (int)$tr['publish_all_language'] || (int)Configuration::get('ETS_RV_PUBLISH_ALL_LANGUAGE')) {
            return $this->l('All', 'AdminEtsRVReviewsController');
        }
        if ($value) {
            if (!is_array($value)) {
                $value = explode(',', $value);
            }
            $this->context->smarty->assign(array(
                'publish_language' => $value,
                'languages' => self::$cache_langs,
                'PS_LANG_IMG_DIR' => _PS_IMG_ . 'l/'
            ));

            return $this->context->smarty->fetch($this->module->getLocalPath() . 'views/templates/admin/flag.tpl');
        }
    }

    public function displayDeleteLink($token, $id)
    {
        if (!isset(self::$cache_lang['delete'])) {
            self::$cache_lang['delete'] = $this->l('Delete', 'AdminEtsRVReviewsController');
        }
        $this->context->smarty->assign(array(
            'href' => self::$currentIndex . '&' . $this->identifier . '=' . $id . '&delete' . $this->table . '&token=' . $this->token,
            'action' => self::$cache_lang['delete'],
            'confirm' => $this->l('Do you want to delete selected item?', 'AdminEtsRVReviewsController'),
            'class' => 'ets_rv_delete delete',
            'token' => $token,
        ));

        return $this->context->smarty->fetch($this->module->getLocalPath() . 'views/templates/admin/list_action_delete.tpl');
    }

    public function initContent()
    {
        parent::initContent();

        if ($this->ajax) {
            if ($this->display == 'edit' || $this->display == 'add') {
                $id_product_comment = (int)Tools::getValue($this->identifier);
                $this->jsonRender([
                    'form' => $this->renderForm(),
                    'images' => $this->module->displayPCListImages($id_product_comment, true),
                    'videos' => $this->module->displayPCListVideos($id_product_comment, true)
                ]);
            } elseif ($this->display == 'view') {
                $ret = $this->ajaxRenderView();
                $ret['errors'] = count($this->errors) > 0 ? implode(Tools::nl2br("\n"), $this->errors) : false;
                $this->jsonRender($ret);
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
        } elseif (Tools::isSubmit('block' . $this->table)) {
            $this->action = 'block';
        } elseif (Tools::isSubmit('unblock' . $this->table)) {
            $this->action = 'unblock';
        } elseif (Tools::isSubmit('delete_all' . $this->table)) {
            $this->action = 'delete_all';
        } elseif (Tools::isSubmit('searchProduct')) {
            $this->action = 'searchProduct';
        } elseif (Tools::isSubmit('searchCustomer')) {
            $this->action = 'searchCustomer';
        }
        $this->module->postProcess();
    }

    public function ajaxProcessSearchProduct()
    {
        $query = ($q = Tools::getValue('q', false)) && Validate::isCleanHtml($q) ? $q : false;
        $excludeIds = ($ids = Tools::getValue('excludeIds', '')) && Validate::isCleanHtml($ids) ? $ids : '';
        $excludePackItself = ($pack = Tools::getValue('packItself', false)) && Validate::isCleanHtml($pack) ? $pack : false;
        if ($excludeIds && $excludeIds != 'NaN') {
            $excludeIds = implode(',', array_map('intval', explode(',', $excludeIds)));
        }
        $excludeVirtual = (bool)Tools::getValue('excludeVirtual');
        $exclude_packs = (bool)Tools::getValue('exclude_packs');
        if ($items = EtsRVProductComment::findProducts($query, $excludeIds, $excludePackItself, $excludeVirtual, $exclude_packs)) {
            foreach ($items as $item) {
                $product = array(
                    'id' => (int)($item['id_product']),
                    'name' => $item['name'],
                    'ref' => (!empty($item['reference']) ? $item['reference'] : ''),
                    'image' => !empty($item['id_image']) ? str_replace('http://', Tools::getShopProtocol(), $this->context->link->getImageLink($item['link_rewrite'], $item['id_image'], $this->module->is17 ? ImageType::getFormattedName('home') : ImageType::getFormatedName('home'))) : '',
                );
                echo implode('|', $product) . "\r\n";
            }
        }
        die;
    }

    public function ajaxProcessSearchCustomer()
    {
        EtsRVTools::ajaxSearchCustomer(Tools::getValue('q', false));
    }

    public function ajaxProcessFirstCustomerAddress()
    {
        $customerId = (int)Tools::getValue('id_customer');
        $id_address = 0;
        if ($customerId > 0)
            $id_address = (int)Address::getFirstCustomerAddressId($customerId);
        die(json_encode([
            'address' => new Address($id_address)
        ]));
    }

    public function ajaxProcessSave()
    {
        $id_product_comment = (int)Tools::getValue($this->identifier);
        $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
        $default_language = new Language($id_lang_default);
        $multiLang = (int)Configuration::get('ETS_RV_MULTILANG_ENABLED') ? 1 : 0;
        $isQuestion = (int)Tools::getValue('qa') ? 1 : 0;

        // Validate fields.
        $comment_title = strip_tags(trim($multiLang ? Tools::getValue('title_' . $id_lang_default) : Tools::getValue('title')));
        if (trim($comment_title) == '' && ((int)Configuration::get('ETS_RV_REQUIRE_TITLE') || $this->qa)) {
            $this->errors[] = sprintf($this->l('Title (%s) cannot be empty', 'AdminEtsRVReviewsController'), $default_language->iso_code);
        } elseif (Tools::strlen($comment_title) > EtsRVProductComment::TITLE_MAX_LENGTH) {
            $this->errors[] = sprintf($this->l('Title cannot be longer than %s characters', 'AdminEtsRVReviewsController'), EtsRVProductComment::TITLE_MAX_LENGTH);
        }

        $comment_content = strip_tags(trim($multiLang ? Tools::getValue('content_' . $id_lang_default) : Tools::getValue('content')));
        if (trim($comment_content) == '') {
            $this->errors[] = sprintf($this->l('Content (%s) cannot be empty', 'AdminEtsRVReviewsController'), $default_language->iso_code);
        } elseif (!Validate::isMessage($comment_content)) {
            $this->errors[] = $this->l('Content is invalid', 'AdminEtsRVReviewsController');
        }
        $allow_guests = trim(Tools::getValue('customer_type')) == 'guest';
        $customer_name = trim(Tools::getValue('customer_name'));
        $email = trim(Tools::getValue('email'));
        $id_product = (int)Tools::getValue('id_product');
        $id_customer = (int)Tools::getValue('id_customer');

        if (!$id_product_comment) {
            if ($allow_guests && !$id_customer) {
                if (trim($customer_name) == '') {
                    $this->errors[] = $this->l('Customer name is required', 'AdminEtsRVReviewsController');
                } elseif (!Validate::isName($customer_name)) {
                    $this->errors[] = $this->l('Customer name is invalid', 'AdminEtsRVReviewsController');
                }
                if (trim($email) !== '' && (!Validate::isEmail($email) || !filter_var($email, FILTER_VALIDATE_EMAIL))) {
                    $this->errors[] = $this->l('Email is invalid', 'AdminEtsRVReviewsController');
                }
            } elseif (!$allow_guests && !$id_customer)
                $this->errors[] = $this->l('Customer is invalid', 'AdminEtsRVReviewsController');
            if (!$id_product)
                $this->errors[] = $this->l('Product is required', 'AdminEtsRVReviewsController');
        }

        if (($upload_photo_enabled = Configuration::get('ETS_RV_UPLOAD_PHOTO_ENABLED') && Configuration::get('ETS_RV_MAX_UPLOAD_PHOTO'))) {
            EtsRVProductCommentEntity::getInstance()->validateUpload('image', $this->errors);
        }
        if (($upload_video_enabled = Configuration::get('ETS_RV_UPLOAD_VIDEO_ENABLED') && Configuration::get('ETS_RV_MAX_UPLOAD_VIDEO'))) {
            EtsRVProductCommentEntity::getInstance()->validateUpload('video', $this->errors);
        }
        $date_add = trim(Tools::getValue('date_add'));
        if ($date_add == '')
            $this->errors[] = $this->l('Date is required', 'AdminEtsRVReviewsController');
        elseif (!Validate::isDate($date_add) || strtotime($date_add) > time()) {
            $this->errors[] = $this->l('Date is invalid', 'AdminEtsRVReviewsController');
        }
        // Save.
        $jsonData = [];
        if (!$this->errors) {
            $productComment = new EtsRVProductComment($id_product_comment);
            $productComment->id_product = $id_product;
            $productComment->id_customer = $id_customer;
            $productComment->customer_name = $customer_name;
            $productComment->email = $email;
            $productComment->question = $isQuestion;
            $criterions = Tools::getValue('criterion');
            $averageGrade = 0;
            if (is_array($criterions) && count($criterions) > 0) {
                foreach ($criterions as $criterion) {
                    $averageGrade += (int)$criterion;
                }
                $averageGrade /= count($criterions);
            }

            $productComment->grade = $averageGrade;

            $validateOld = $productComment->validate;
            $productComment->validate = (int)Tools::getValue('validate');

            $id_country = (int)Tools::getValue('id_country');
            if ($id_country > 0) {
                $productComment->id_country = $id_country;
            } elseif ($id_customer > 0 && ($id_address = Address::getFirstCustomerAddressId($id_customer))) {
                $address = new Address($id_address);
                if ($address->id_country > 0) {
                    $productComment->id_country = $address->id_country;
                }
            }
            $productComment->verified_purchase = trim(Tools::getValue('verified_purchase')) ?: 'auto';
            $productComment->date_add = $date_add !== '' ? $date_add : date('Y-m-d H:i:s');

            if ($productComment->id)
                $productComment->upd_date = date('Y-m-d H:i:s');

            $languages = Language::getLanguages(false);
            if ($languages) {
                foreach ($languages as $l) {
                    $productComment->title[(int)$l['id_lang']] = $multiLang ? strip_tags(trim(Tools::getValue('title_' . $l['id_lang']))) : '';
                    $productComment->content[(int)$l['id_lang']] = $multiLang ? strip_tags(trim(Tools::getValue('content_' . $l['id_lang']))) : '';
                }
            }

            // Languages to display.
            $publishLanguage = Tools::getValue('ids_language', null);
            $productComment->publish_all_language = ($publishLanguage === null || is_array($publishLanguage) && in_array('all', $publishLanguage)) ? 1 : 0;

            if ($productComment->save(true, false)) {
                if (!$id_product_comment) {
                    // Add activity:
                    $content = $this->qa ? 'asked_a_question_about_product' : 'wrote_a_review_for_product';
                    $type = $this->qa ? EtsRVActivity::ETS_RV_TYPE_QUESTION : EtsRVActivity::ETS_RV_TYPE_REVIEW;
                    EtsRVProductCommentEntity::getInstance()->addActivity($productComment, $type, $type, $id_product, $content, $this->context, !$productComment->id_customer ? $customer_name : null);
                }
                if (is_array($criterions) && count($criterions) > 0) {
                    if ($id_product_comment && $productComment->id) {
                        EtsRVProductComment::deleteGrades($id_product_comment);
                    }
                    if (!EtsRVProductComment::addGrades($productComment->id, $criterions)) {
                        $this->errors[] = $this->l('Unknown error', 'AdminEtsRVReviewsController');
                    }
                }

                // Origin language only add new.
                EtsRVProductComment::saveOriginLang($productComment->id, (int)$this->context->language->id, $comment_title, $comment_content);

                // Languages to display.
                if (!$productComment->publish_all_language && is_array($publishLanguage))
                    EtsRVProductComment::savePublishLang($productComment->id, $publishLanguage);

                // Upload image:
                if (isset($upload_photo_enabled) && $upload_photo_enabled) {
                    EtsRVProductCommentEntity::getInstance()->processUploadImage($productComment, 'image', $this->errors);
                }
                if (isset($upload_video_enabled) && $upload_video_enabled) {
                    EtsRVProductCommentEntity::getInstance()->processUploadVideo($productComment, 'video', $this->errors);
                }

                // Mail approved:
                if ($validateOld && $validateOld !== $productComment->validate) {
                    EtsRVProductCommentEntity::getInstance()->productCommentMailApproved($productComment, true);
                    if ($validateOld == 0) {
                        EtsRVProductCommentEntity::getInstance()->productCommentMailVoucher($productComment);
                    }
                }
            } else
                $this->errors[] = $this->l('Unknown error happened', 'AdminEtsRVReviewsController');

            if (!count($this->errors) && !$id_product_comment) {
                $jsonData['id_product_comment'] = $productComment->id;
                $product = new Product($id_product, false, $this->context->language->id);
                $jsonData['form_title'] = sprintf($this->l('Edit %1s #%2s', 'AdminEtsRVReviewsController'), $this->label, $productComment->id) . '  -  ' . sprintf($this->l('Product: %s', 'AdminEtsRVReviewsController'), EtsRVTools::displayText($product->name, 'a', ['href' => $this->context->link->getProductLink($product)]));
            }
        }

        $hasError = count($this->errors) > 0 ? 1 : 0;
        $jsonData['errors'] = $hasError ? implode(Tools::nl2br("\n"), $this->errors) : false;
        $jsonData['msg'] = !$hasError ? $this->l('Saved', 'AdminEtsRVReviewsController') : '';
        $this->jsonRender($this->extraJSON($jsonData));
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
        $data['prop'] = 'Review';

        return $data;
    }

    public function initToolbarTitle()
    {
        $this->toolbar_title = is_array($this->breadcrumbs) ? array_unique($this->breadcrumbs) : array($this->breadcrumbs);
    }

    public function getCriterionByProduct($id_product, $id_product_comment = 0, $not_deleted = true)
    {
        $criterionRepository = EtsRVProductCommentCriterionRepository::getInstance();
        return $criterionRepository->getByProduct(
            $id_product,
            $this->context->language->id,
            $id_product_comment,
            $not_deleted
        );
    }

    public function getFields(EtsRVProductComment $productComment)
    {
        $languages = [[
            'id_lang' => 'all',
            'name' => $this->l('All', 'AdminEtsRVReviewsController')
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
        $multiLang = (bool)((int)Configuration::get('ETS_RV_MULTILANG_ENABLED'));
        $type = !$productComment->id ? 'text' : 'hidden';

        // Review status.
        $validates = [];
        foreach (EtsRVDefines::getInstance()->getReviewStatus() as $key => $val) {
            $validates[] = [
                'id' => $key,
                'name' => $val
            ];
        }
        $countries = Country::getCountries($this->context->language->id);
        $array_country = [
            [
                'id_country' => 0,
                'name' => $this->l('--')
            ]
        ];
        if ($countries) {
            foreach ($countries as $country) {
                $array_country[] = ['id_country' => $country['id_country'], 'name' => $country['name']];
            }
        }
        return array_merge(
            !$productComment->id ? [
                'customer_type' => array(
                    'type' => 'radio',
                    'label' => $this->l('Customer type', 'AdminEtsRVReviewsController'),
                    'name' => 'customer_type',
                    'values' => array(
                        array(
                            'label' => $this->l('Customer', 'AdminEtsRVReviewsController'),
                            'value' => 'customer',
                            'id' => 'customer_guest_customer',
                        ),
                        array(
                            'label' => $this->l('Guest', 'AdminEtsRVReviewsController'),
                            'value' => 'guest',
                            'id' => 'customer_type_guest',
                        )
                    ),
                )
            ] : [],
            [
                'customer_name' => array(
                    'type' => $productComment->id_customer ? 'hidden' : 'text',
                    'label' => $this->l('Customer name', 'AdminEtsRVReviewsController'),
                    'name' => 'customer_name',
                    'required' => $productComment->id_customer < 1,
                    'form_group_class' => 'customer_type guest',
                ),
                'id_customer' => array(
                    'type' => $type,
                    'label' => $this->l('Customer', 'AdminEtsRVReviewsController'),
                    'name' => 'id_customer',
                    'placeholder' => $this->l('Search for customer by first name, last name, email or id', 'AdminEtsRVReviewsController'),
                    'required' => true,
                    'form_group_class' => 'customer_type customer',
                ),
                'email' => array(
                    'type' => $productComment->id_customer ? 'hidden' : 'text',
                    'label' => $this->l('Email', 'AdminEtsRVReviewsController'),
                    'name' => 'email',
                    'form_group_class' => 'customer_type guest',
                ),
                'id_product' => array(
                    'type' => $type,
                    'label' => $this->l('Product', 'AdminEtsRVReviewsController'),
                    'name' => 'id_product',
                    'placeholder' => $this->l('Search for product by name, reference or id', 'AdminEtsRVReviewsController'),
                    'required' => true,
                ),
            ],
            !$this->qa ? [
                'criterion' => array(
                    'type' => 'criterion',
                    'label' => $this->l('Rating', 'AdminEtsRVReviewsController'),
                    'name' => 'criterion',
                    'options' => $this->getCriterionByProduct((int)$productComment->id_product, $productComment->id, $productComment->id <= 0),
                    'default' => !$productComment->id ? (int)Configuration::get('ETS_RV_DEFAULT_RATE') : 0,
                ),
            ] : [],
            [
                'title' => array(
                    'type' => 'text',
                    'label' => $this->l('Title', 'AdminEtsRVReviewsController'),
                    'name' => 'title',
                    'lang' => $multiLang,
                    'required' => (int)Configuration::get('ETS_RV_REQUIRE_TITLE') || $this->qa,
                ),
                'content' => array(
                    'type' => 'textarea',
                    'name' => 'content',
                    'label' => sprintf($this->l('%s content', 'AdminEtsRVReviewsController'), Tools::ucfirst($this->label)),
                    'required' => true,
                    'lang' => $multiLang,
                    'autoload_rte' => false,
                    'desc' => sprintf($this->l('Maximum length: %s characters', 'AdminEtsRVReviewsController'), EtsRVProductComment::NAME_MAX_LENGTH),
                    'strip_tag' => false,
                ),
            ],
            [
                'date_add' => array(
                    'type' => 'datetime',
                    'label' => $this->l('Date', 'AdminEtsRVReviewsController'),
                    'name' => 'date_add',
                    'required' => true,
                    'default' => date('Y-m-d H:i:s'),
                ),
                'ids_language' => array(
                    'type' => 'select',
                    'label' => $this->l('Languages to display', 'AdminEtsRVReviewsController'),
                    'options' => array(
                        'query' => $languages,
                        'id' => 'id_lang',
                        'name' => 'name'
                    ),
                    'default' => 'all',
                    'multiple' => true,
                    'name' => 'ids_language',
                    'class' => 'ets_pr_chooselang',
                    'form_group_class' => Configuration::get('ETS_RV_PUBLISH_ALL_LANGUAGE') ? 'hide' : ''
                ),
            ],
            !$this->qa ? [
                'id_country' => array(
                    'type' => 'select',
                    'name' => 'id_country',
                    'label' => $this->l('Country', 'AdminEtsRVReviewsController'),
                    'options' => array(
                        'query' => $array_country,
                        'id' => 'id_country',
                        'name' => 'name'
                    ),
                    'default' => $productComment->id_country ?: 0,
                ),
                'verified_purchase' => array(
                    'type' => 'radio',
                    'name' => 'verified_purchase',
                    'is_bool' => true,
                    'label' => $this->l('Verified purchase', 'AdminEtsRVReviewsController'),
                    'values' => [
                        [
                            'id' => 0,
                            'label' => $this->l('Auto'),
                            'value' => 'auto',
                        ],
                        [
                            'id' => 1,
                            'label' => $this->l('Yes'),
                            'value' => 'yes',
                        ],
                        [
                            'id' => 2,
                            'label' => $this->l('No'),
                            'value' => 'no',
                        ],
                    ],
                    'default' => 'auto',
                    'form_group_class' => 'ets_rv_free_product',
                ),
            ] : [],
            [
                'validate' => array(
                    'type' => 'select',
                    'name' => 'validate',
                    'label' => sprintf($this->l('%s status', 'AdminEtsRVReviewsController'), Tools::ucfirst($this->label)),
                    'options' => array(
                        'query' => $validates,
                        'id' => 'id',
                        'name' => 'name'
                    ),
                    'default' => 1,
                ),
            ]
        );
    }

    public function renderForm()
    {
        $id_product_comment = (int)Tools::getValue($this->identifier);
        $refreshController = trim(Tools::getValue('refreshController'));
        $product_comment = new EtsRVProductComment($id_product_comment);
        $back_to_view = (int)Tools::getValue('back_to_view');
        $url_params = [];
        if ($refreshController) {
            $url_params['refreshController'] = $refreshController;
            EtsRVProductCommentEntity::getInstance()->extraParams($url_params);
        }
        if (Tools::isSubmit('submitFilter' . $this->list_id)) {
            $url_params['submitFilter' . $this->list_id] = (int)Tools::getValue('submitFilter' . $this->list_id);
        } elseif (Tools::getValue('page')) {
            $url_params['submitFilter' . $this->list_id] = (int)Tools::getValue('page');
        }
        $fields = $this->getFields($product_comment);
        $fields_form_1 = array(
            'form' => array(
                'legend' => array(
                    'title' => $id_product_comment ? sprintf($this->l('Edit %1s #%2s', 'AdminEtsRVReviewsController'), $this->label, $id_product_comment) : sprintf($this->l('Add new %s', 'AdminEtsRVReviewsController'), $this->label),
                    'icon' => 'icon-cogs',
                ),
                'input' => $fields,
                'submit' => array(
                    'title' => $this->l('Save', 'AdminEtsRVReviewsController'),
                    'class' => 'btn btn-default pull-right',
                    'name' => 'submitAdd' . $this->table,
                ),
                'buttons' => array(
                    'cancel' => array(
                        'href' => self::$currentIndex . '&token=' . $this->token . '&' . http_build_query($url_params),
                        'title' => $back_to_view ? $this->l('Back', 'AdminEtsRVReviewsController') : $this->l('Cancel', 'AdminEtsRVReviewsController'),
                        'icon' => 'process-icon-' . ($back_to_view ? 'cancel' : 'cancel'),
                        'class' => 'ets_rv_cancel' . ($back_to_view ? ' ets_rv_back_to_view' : ''),
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
        $helper->currentIndex = self::$currentIndex . ($this->qa ? '&qa=' . (int)$this->qa : '') . '&' . http_build_query($url_params);
        $helper->token = $this->token;
        $helper->show_cancel_button = false;

        // Re-build fields.
        $fields_value = $this->getCommentsFieldsValues($product_comment, $fields_form_1['form']['input'], $helper->submit_action);

        // Custom fields_value.
        $tpl_vars = [];
        if ($id_product_comment) {
            // Primary key.
            $fields_form_1['form']['input'][] = array(
                'type' => 'hidden',
                'name' => 'id_ets_rv_product_comment',
            );
            $fields_value['id_ets_rv_product_comment'] = (int)$id_product_comment;

            // Languages to display.
            $publishLang = EtsRVProductComment::getPublishLang($id_product_comment, true);
            $fields_value['ids_language[]'] = $product_comment->publish_all_language ? array('all') : explode(',', $publishLang);

            // Criterion
            if (!$this->qa) {
                $criterion = array();
                $commentGrade = EtsRVProductComment::getGradesById($id_product_comment, $this->context->language->id);
                if ($commentGrade) {
                    foreach ($commentGrade as $grade) {
                        $criterion[(int)$grade['id_ets_rv_product_comment_criterion']] = $grade['grade'];
                    }
                }
                $fields_value['criterion'] = $criterion;
            }
            if ($product_comment->id_product) {
                $p = new Product($product_comment->id_product, false, $this->context->language->id);
                $p->link = $this->context->link->getProductLink($p, $p->link_rewrite, $p->category, $p->ean13, $this->context->language->id);
                $tpl_vars['product'] = $p;
            }
        }

        $tpl_vars = array_merge($tpl_vars, [
            'fields_value' => $fields_value,
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
            'identifier' => (int)$id_product_comment,
            'form_id' => 'edit',
            'question' => $this->qa,
            'ETS_RV_DEFAULT_RATE' => (int)Configuration::get('ETS_RV_DEFAULT_RATE'),
        ]);

        $helper->tpl_vars = $tpl_vars;
        return $helper->generateForm(array($fields_form_1));
    }

    public function ajaxRenderView()
    {
        $identifier = (int)Tools::getValue($this->identifier);
        if (!$identifier) {
            $this->errors[] = $this->l('Review does not exist.', 'AdminEtsRVReviewsController');
        } else {
            $productComment = new EtsRVProductComment($identifier, $this->context->language->id);
            if (!(int)$productComment->id_product) {
                $this->errors[] = $this->l('Product does not exist.', 'AdminEtsRVReviewsController');

                return [];
            }
            $p = new Product((int)$productComment->id_product, true, $this->context->language->id);
            $p->link = $this->context->link->getProductLink($p, $p->link_rewrite, $p->category, $p->ean13, $this->context->language->id);

            $image = Product::getCover((int)$productComment->id_product, $this->context);
            if (isset($image['id_image']) && (int)$image['id_image'] > 0) {
                $p->image = $this->context->link->getImageLink($p->link_rewrite, (int)$image['id_image'], $this->module->is17 ? ImageType::getFormattedName('home') : ImageType::getFormatedName('home'));
            }
            $productComment->product = $p;

            $toolbar_btn_label = $this->qa ? $this->l('questions', 'AdminEtsRVReviewsController') : $this->l('comments', 'AdminEtsRVReviewsController');
            $url_params = array(
                'id_product' => $productComment->product->id,
                'id_product_comment' => $productComment->id
            );

            $refreshParams = [];
            $refreshController = trim(Tools::getValue('refreshController'));
            if ($refreshController !== '' && Validate::isControllerName($refreshController)) {
                $refreshParams['refreshController'] = $refreshController;
                EtsRVProductCommentEntity::getInstance()->extraParams($refreshParams);
                $url_params = array_merge($url_params, $refreshParams);
            }
            if (Tools::isSubmit('submitFilter' . $this->list_id)) {
                $url_params['submitFilter' . $this->list_id] = (int)Tools::getValue('submitFilter' . $this->list_id);
            }

            $this->context->smarty->assign([
                'title' => $this->qa ? $this->l('View question', 'AdminEtsRVReviewsController') : $this->l('View review', 'AdminEtsRVReviewsController'),
                'review' => $productComment,
                'buttons' => array(
                    array(
                        'id' => 'delete',
                        'title' => sprintf($this->l('Delete %s', 'AdminEtsRVReviewsController'), $this->label),
                        'name' => $this->l('Delete', 'AdminEtsRVReviewsController'),
                        'icon' => 'process-icon-delete',
                        'class' => 'delete',
                        'confirm' => $this->l('Delete selected item(s)?', 'AdminEtsRVReviewsController'),
                    ),
                    array(
                        'id' => 'edit',
                        'title' => sprintf($this->l('Edit %s', 'AdminEtsRVReviewsController'), $this->label),
                        'name' => $this->l('Edit', 'AdminEtsRVReviewsController'),
                        'icon' => 'process-icon-edit',
                        'class' => 'edit',
                    ),
                    array(
                        'id' => 'approve',
                        'title' => sprintf($this->l('Approve %s', 'AdminEtsRVReviewsController'), $this->label),
                        'name' => $this->l('Approve', 'AdminEtsRVReviewsController'),
                        'icon' => 'process-icon-check icon-check',
                        'class' => 'ets_rv_approve',
                    ),
                ),
                'toolbar_btn' => array(
                    array(
                        'id' => 'block',
                        'title' => sprintf($this->l('Block this user from submitting %s', 'AdminEtsRVReviewsController'), $toolbar_btn_label),
                        'name' => $this->l('Block', 'AdminEtsRVReviewsController'),
                        'icon' => 'icon-lock',
                        'class' => 'block',
                        'confirm' => sprintf($this->l('Are you sure you want to block this user? After blocking this user, this person will not be able to submit %s on your website', 'AdminEtsRVReviewsController'), $toolbar_btn_label),
                    ),
                    array(
                        'id' => 'unblock',
                        'title' => sprintf($this->l('Unblock this user from submitting %s', 'AdminEtsRVReviewsController'), $toolbar_btn_label),
                        'name' => $this->l('Unblock', 'AdminEtsRVReviewsController'),
                        'icon' => 'icon-unlock',
                        'class' => 'unblock'
                    ),
                ),
                'currentIndex' => self::$currentIndex . ($refreshParams ? '&' . http_build_query($refreshParams) : '') . (Tools::isSubmit('submitFilter' . $this->list_id) ? '&submitFilter' . $this->list_id . '=' . (int)Tools::getValue('submitFilter' . $this->list_id) : ''),
                'PS_LANG_IMG_DIR' => _PS_IMG_ . 'l/',
                'review_status' => EtsRVDefines::getInstance()->getReviewStatus(),
                'table' => $this->table,
                'identifier' => $this->identifier,
                'list' => $this->qa ? $this->module->displayProductQuestionsList($url_params) : $this->module->displayProductCommentsList($url_params),
                'question' => $this->qa,
                'refreshController' => $refreshController,
                'languages' => Language::getLanguages(false),
                'defaultFormLanguage' => $this->context->language->id
            ]);
            $ret = array(
                'form' => $this->context->smarty->fetch($this->module->getLocalPath() . 'views/templates/admin/view-review.tpl'),
                'product_comment' => [
                    'id_product_comment' => $productComment->id,
                    'id_product' => $productComment->id_product,
                    'qa' => $productComment->question,
                    'back_office' => 1,
                ],
            );

            // comment:
            $comment_id = Tools::getValue('id_ets_rv_comment');
            if ($comment_id !== '' && Validate::isUnsignedInt($comment_id)) {
                $ret['product_comment']['comment_id'] = (int)$comment_id;
            }

            // reply comment:
            $comment_reply_id = Tools::getValue('id_ets_rv_reply_comment');
            if ($comment_reply_id !== '' && Validate::isUnsignedInt($comment_reply_id)) {
                $ret['product_comment']['comment_reply_id'] = (int)$comment_reply_id;
            }

            // answer:
            $answer = Tools::getValue('answer');
            if ($answer !== '' && Validate::isUnsignedInt($answer)) {
                $ret['product_comment']['answer'] = (int)$answer;
            }

            // activity:
            $activity_id = Tools::getValue('id_ets_rv_activity');
            if ($activity_id !== '' && Validate::isUnsignedInt($activity_id)) {
                $ret['read'] = EtsRVActivity::makeRead($this->context->employee->id, (int)$activity_id);
            }

            return $ret;
        }

        return [];
    }

    public function getCommentsFieldsValues($obj, $inputs, $submit_action)
    {
        $fields = array();
        if (empty($inputs) || !is_object($obj))
            return $fields;
        $languages = Language::getLanguages(false);
        $originLang = $obj->id ? EtsRVProductComment::getOriginLang($obj->id) : array();

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
                        $fields[$key][$l['id_lang']] = $obj->id && !empty($values[$l['id_lang']]) ? $values[$l['id_lang']] : (isset($config['default']) && $config['default'] ? $config['default'] : null);//($originLang && !empty($originLang[$key]) ? $originLang[$key] : (isset($config['default']) && $config['default'] ? $config['default'] : null))
                    }
                } elseif (isset($config['lang'])) {
                    $fields[$key] = $obj->id && $originLang && !empty($originLang[$key]) ? $originLang[$key] : (isset($config['default']) && $config['default'] ? $config['default'] : '');
                } elseif (($config['type'] == 'select' && isset($config['multiple']) && $config['multiple'])) {
                    $fields[$key . '[]'] = $obj->id ? ($obj->$key != '' ? explode(',', $obj->$key) : array()) : (isset($config['default']) && $config['default'] ? $config['default'] : array());
                } elseif (!isset($config['tree']) && $key != $this->identifier)
                    $fields[$key] = $obj->id ? $obj->$key : (isset($config['default']) && $config['default'] ? $config['default'] : null);
            }
        }
        $fields['customer_type'] = 'customer';
        return $fields;
    }

    public function processBulkApprove()
    {
        if (EtsRVTools::isArrayWithIds($this->boxes)) {
            $validateOlds = EtsRVProductComment::productCommentValidate($this->boxes);
            if (!EtsRVProductComment::getProductCommentsNumber($this->boxes)) {
                $this->errors[] = $this->l('Item(s) selected is approved', 'AdminEtsRVReviewsController');
            } elseif (!EtsRVProductComment::approveProductComments($this->boxes)) {
                $this->errors[] = $this->l('Failed to approve selected item(s)', 'AdminEtsRVReviewsController');
            } else {
                foreach ($this->boxes as $id) {
                    $productComment = new EtsRVProductComment($id);
                    if ($productComment->id > 0) {
                        EtsRVProductCommentEntity::getInstance()->productCommentMailApproved($productComment, true);
                        if (isset($validateOlds[$productComment->id])) {
                            EtsRVProductCommentEntity::getInstance()->productCommentMailVoucher($productComment);
                        }
                    }
                }
            }
        } else {
            $this->errors[] = $this->l('Please select item(s) to approve', 'AdminEtsRVReviewsController');
        }
        if (!count($this->errors)) {
            $this->confirmations = $this->l('Approved', 'AdminEtsRVReviewsController');
        }
    }

    public function processBulkDelete()
    {
        if (EtsRVTools::isArrayWithIds($this->boxes)) {
            if (!EtsRVProductComment::deleteProductComments($this->boxes)) {
                $this->errors[] = $this->l('Deleting selected items is failed', 'AdminEtsRVReviewsController');
            } else {
                if (EtsRVProductCommentImage::deleteImages($this->boxes) &&
                    EtsRVProductComment::deleteCascade($this->boxes, 'reply_comment', 'comment', 'product_comment') &&
                    EtsRVProductComment::deleteCascade($this->boxes, 'comment', '', 'product_comment')
                ) {
                    if (!EtsRVProductComment::deleteAllChildren($this->boxes))
                        $this->errors[] = $this->l('Cannot delete selected item(s)', 'AdminEtsRVReviewsController');
                } else
                    $this->errors[] = $this->l('Cannot delete cascade by the selected item(s)', 'AdminEtsRVReviewsController');

            }
        } else {
            $this->errors[] = $this->l('Please select item(s) to delete', 'AdminEtsRVReviewsController');
        }
        if (!count($this->errors)) {
            $this->confirmations = $this->l('Deleted successfully', 'AdminEtsRVReviewsController');
        }
    }

    public function ajaxProcessDelete()
    {
        $id_product_comment = (int)Tools::getValue('id_ets_rv_product_comment');
        $comment = new EtsRVProductComment($id_product_comment);
        if (!$comment->delete())
            $this->errors[] = $this->l('Cannot delete selected item(s)', 'AdminEtsRVReviewsController');
        $hasError = (bool)count($this->errors);
        $this->jsonRender($this->extraJSON([
            'errors' => $hasError ? implode("\r\n", $this->errors) : false,
            'msg' => !$hasError ? sprintf($this->l('The %s was successfully deleted.', 'AdminEtsRVReviewsController'), $this->label) : '',
        ]));
    }

    public function ajaxProcessApprove()
    {
        $id_product_comment = (int)Tools::getValue('id_ets_rv_product_comment');
        $productComment = new EtsRVProductComment($id_product_comment);
        $validateOld = $productComment->validate;
        if ($productComment->id <= 0) {
            $this->errors[] = $this->l('Review does not exist.', 'AdminEtsRVReviewsController');
        } elseif (!$productComment->validate(1))
            $this->errors[] = $this->l('Failed to approve', 'AdminEtsRVReviewsController');
        else {
            EtsRVProductCommentEntity::getInstance()->productCommentMailApproved($productComment, true);
            if ($validateOld == 0) {
                EtsRVProductCommentEntity::getInstance()->productCommentMailVoucher($productComment);
            }
        }

        $hasError = (bool)count($this->errors);
        $this->jsonRender($this->extraJSON([
            'errors' => $hasError ? implode("\r\n", $this->errors) : false,
            'msg' => !$hasError ? $this->l('Approve successfully.', 'AdminEtsRVReviewsController') : '',
            'id' => $id_product_comment,
        ]));
    }

    public function ajaxProcessPrivate()
    {
        $id_product_comment = (int)Tools::getValue('id_ets_rv_product_comment');
        $productComment = new EtsRVProductComment($id_product_comment);

        if ($productComment->id <= 0) {
            $this->errors[] = $this->l('Review does not exist.', 'AdminEtsRVReviewsController');
        } elseif (!$productComment->validate(2)) {
            $this->errors[] = $this->l('Set to private failed', 'AdminEtsRVReviewsController');
        } else {
            EtsRVProductCommentEntity::getInstance()->productCommentMailApproved($productComment, true);
        }

        $hasError = (bool)count($this->errors);
        $this->jsonRender($this->extraJSON([
            'errors' => $hasError ? implode("\r\n", $this->errors) : false,
            'msg' => !$hasError ? $this->l('Set to private successfully.', 'AdminEtsRVReviewsController') : '',
        ]));
    }

    public function ajaxProcessDeleteAll()
    {
        $id_customer = Tools::getIsset('id_customer') ? (int)Tools::getValue('id_customer') : null;
        if ($id_customer === null && ($identifier = (int)Tools::getValue($this->identifier))) {
            $productComment = new EtsRVProductComment($identifier);
            $id_customer = (int)$productComment->id_customer;
        }
        if (!Validate::isUnsignedInt($id_customer))
            $this->errors[] = $this->l('This user ID does not exist', 'AdminEtsRVReviewsController');
        else {
            if (!EtsRVProductComment::deleteAllReviewByCustomer($id_customer))
                $this->errors[] = $this->l('Unknown error happened', 'AdminEtsRVReviewsController');
        }
        $hasError = (bool)count($this->errors);
        $jsons = [
            'errors' => $hasError ? implode(Tools::nl2br("\n"), $this->errors) : false,
            'msg' => !$hasError ? sprintf($this->l('Delete all %ss submitted by this user.', 'AdminEtsRVReviewsController'), $this->label) : '',
        ];
        if (!Tools::getIsset('id_customer')) {
            // Process list filtering
            if ($this->filter && $this->action != 'reset_filters') {
                $this->processFilter();
            }
            $jsons['list'] = $this->renderList();
        } else
            $jsons['customer'] = (int)$id_customer;

        $this->jsonRender($jsons);
    }

    public function ajaxProcessRefuse()
    {
        $id = (int)Tools::getValue($this->identifier);
        $token = Tools::getValue('token');
        $refreshController = Tools::getValue('refreshController');
        $this->context->smarty->assign([
            'action' => self::$currentIndex . '&' . $this->identifier . '=' . $id . ($refreshController !== '' ? '&refreshController=' . $refreshController : '') . '&qa=0&action=sendMailRefuse&token=' . $token . (Tools::isSubmit('submitFilter' . $this->list_id) ? '&submitFilter' . $this->list_id . '=' . (int)Tools::getValue('submitFilter' . $this->list_id) : ''),
        ]);
        $this->jsonRender([
            'form' => $this->context->smarty->fetch($this->module->getLocalPath() . 'views/templates/admin/form_refuse.tpl')
        ]);
    }

    public function ajaxProcessSendMailRefuse()
    {
        $id = (int)Tools::getValue($this->identifier);
        if (!$id || !Validate::isUnsignedInt($id) || !Validate::isLoadedObject(($productComment = new EtsRVProductComment($id)))) {
            $this->errors[] = $this->l('Product review does not exist.', 'AdminEtsRVReviewsController');
        }
        $message = Tools::getValue('message');
        if (trim($message) == '') {
            $this->errors[] = $this->l('Message content is required.', 'AdminEtsRVReviewsController');
        } elseif (!Validate::isMessage($message)) {
            $this->errors[] = $this->l('Message is invalid.', 'AdminEtsRVReviewsController');
        }
        if (!$this->errors) {
            if ((int)$productComment->id_customer > 0) {
                $customer = new Customer((int)$productComment->id_customer);
                $customerName = $customer->firstname . ' ' . $customer->lastname;
            } else {
                $customerName = $productComment->customer_name;
            }
            if (isset($customer) && $customer instanceof Customer && $customer->id > 0) {
                $languageObj = new Language($customer->id_lang);
                $customerEmail = $customer->email;
            } else {
                $languageObj = $this->context->language;
                $customerEmail = $productComment->email;
            }
            $idLang = $languageObj->id ?: $this->context->language->id;
            $originLang = EtsRVProductComment::getOriginLang($productComment->id);
            $product = new Product((int)$productComment->id_product, false, $idLang);
            $templateVars = [
                '{customer_name}' => $customerName,
                '{product_link}' => $this->context->link->getProductLink($product, $product->link_rewrite, $product->category, $product->ean13, $idLang),
                '{product_name}' => $product->name,
                '{content}' => isset($productComment->content[$idLang]) ? $productComment->content[$idLang] : (isset($originLang['content']) ? $originLang['content'] : ''),
                '{title}' => isset($productComment->title[$idLang]) ? $productComment->title[$idLang] : (isset($originLang['title']) ? $originLang['title'] : ''),
                '{reason}' => $message,
            ];
            if (EtsRVMail::send(
                $idLang
                , 'tocustomer_refuse'
                , null
                , $templateVars
                , $customerEmail
                , $customerName
                , false
                , isset($customer) ? $customer->id : 0
                , 0
                , $productComment->id_product
                , $this->context->shop->id
                , $productComment->question ? 0 : $productComment->id
            )) {
                $productComment->validate = EtsRVProductComment::STATUS_REFUSE;
                if (!$productComment->save()) {
                    $this->errors[] = $this->l('Failed to decline.', 'AdminEtsRVReviewsController');
                }
            } else
                $this->errors[] = $this->l('Failed to send decline email.', 'AdminEtsRVReviewsController');
        }
        $hasError = count($this->errors) > 0;
        $this->jsonRender($this->extraJSON([
            'errors' => $hasError ? implode("\r\n", $this->errors) : false,
            'msg' => !$hasError ? $this->l('Decline successfully.', 'AdminEtsRVReviewsController') : '',
            'id' => $id,
        ]));
    }

    public function displayValidate($value)
    {
        $status = EtsRVDefines::getInstance()->getReviewStatus($value);
        if ((int)$value == EtsRVProductComment::STATUS_REFUSE) {
            return EtsRVTools::displayText($status, 'span', ['class' => 'badge badge-reject']);
        }
        return $status;
    }

    public function displayRefuseLink($token, $id, $name = null)
    {
        if (!in_array((int)EtsRVProductComment::getStatusById($id), [EtsRVProductComment::STATUS_APPROVE, EtsRVProductComment::STATUS_REFUSE])) {
            if (!isset(self::$cache_lang['refuse'])) {
                self::$cache_lang['refuse'] = $name !== null ? $name : $this->l('Decline', 'AdminEtsRVReviewsController');
            }

            $this->context->smarty->assign(array(
                'href' => self::$currentIndex . '&' . $this->identifier . '=' . $id . '&refuse' . $this->table . '&token=' . ($token != null ? $token : $this->token) . (Tools::isSubmit('submitFilter' . $this->list_id) ? '&submitFilter' . $this->list_id . '=' . (int)Tools::getValue('submitFilter' . $this->list_id) : ''),
                'action' => self::$cache_lang['refuse'],
                'class' => 'ets_rv_refuse'
            ));

            return $this->context->smarty->fetch($this->module->getLocalPath() . 'views/templates/admin/list_action_refuse.tpl');
        }
    }

    public function displayApproveLink($token, $id, $name = null)
    {
        if ((int)EtsRVProductComment::getStatusById($id) != EtsRVProductComment::STATUS_APPROVE) {
            if (!isset(self::$cache_lang['approve'])) {
                self::$cache_lang['approve'] = $name !== null ? $name : $this->l('Approve', 'AdminEtsRVReviewsController');
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
        if ((int)EtsRVProductComment::getStatusById($id) != EtsRVProductComment::STATUS_PRIVATE) {
            if (!isset(self::$cache_lang['private'])) {
                self::$cache_lang['private'] = $name !== null ? $name : $this->l('Set to private', 'AdminEtsRVReviewsController');
            }

            $this->context->smarty->assign(array(
                'href' => self::$currentIndex . '&' . $this->identifier . '=' . $id . '&private' . $this->table . '&token=' . ($token != null ? $token : $this->token) . (Tools::isSubmit('submitFilter' . $this->list_id) ? '&submitFilter' . $this->list_id . '=' . (int)Tools::getValue('submitFilter' . $this->list_id) : ''),
                'action' => self::$cache_lang['private'],
                'class' => 'ets_rv_private'
            ));

            return $this->context->smarty->fetch($this->module->getLocalPath() . 'views/templates/admin/list_action_private.tpl');
        }
    }

    public function displayViewLink($token, $id)
    {
        if ($id > 0) {
            if (!array_key_exists('view', self::$cache_lang)) {
                self::$cache_lang['view'] = $this->l('View', 'AdminEtsRVReviewsController');
            }

            $this->context->smarty->assign(array(
                'href' => self::$currentIndex . '&' . $this->identifier . '=' . $id . '&view' . $this->table . '&token=' . ($token != null ? $token : $this->token) . (Tools::isSubmit('submitFilter' . $this->list_id) ? '&submitFilter' . $this->list_id . '=' . (int)Tools::getValue('submitFilter' . $this->list_id) : ''),
                'action' => self::$cache_lang['view'],
                'class' => 'ets_rv_view_review'
            ));

            return $this->context->smarty->fetch($this->module->getLocalPath() . 'views/templates/admin/list_action_view.tpl');
        }
    }

    public function getList(
        $id_lang,
        $order_by = null,
        $order_way = null,
        $start = 0,
        $limit = null,
        $id_lang_shop = false
    )
    {
        parent::getList($id_lang, $order_by, $order_way, $start, $limit, $id_lang_shop);
        if ($this->_list) {
            foreach ($this->_list as &$list) {
                $icons = EtsRVComment::getIcons();
                $list['content'] = str_replace(array_keys($icons), $icons, $list['content']);
            }
        }
    }
}