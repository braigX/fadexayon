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

abstract class EtsRVEntity extends EtsRVCore
{
    const CACERT_LOCATION = 'https://curl.haxx.se/ca/cacert.pem';
    static $_INSTANCE;
    static $translate = [];
    /**
     * @var false|Ets_reviews
     */
    protected $module;
    protected $backOffice = 0;
    protected $qa = 0;
    protected $sf = '';
    protected $employee = 0;
    public $_errors = [];
    static $initialize = false;
    /**
     * @var Context
     */
    public $context;

    public function __construct()
    {
        parent::__construct();

        $this->module = Module::getInstanceByName('ets_reviews');
        $this->init();
    }

    public static function getInstance()
    {
    }

    public function init()
    {
        if (!self::$initialize) {
            self::$translate = [
                'ago' => $this->l('ago', 'EtsRVEntity'),
                'just_now' => $this->l('Just now', 'EtsRVEntity'),
                'year' => $this->l('year', 'EtsRVEntity'),
                'years' => $this->l('years', 'EtsRVEntity'),
                'month' => $this->l('month', 'EtsRVEntity'),
                'months' => $this->l('months', 'EtsRVEntity'),
                'week' => $this->l('week', 'EtsRVEntity'),
                'weeks' => $this->l('weeks', 'EtsRVEntity'),
                'day' => $this->l('day', 'EtsRVEntity'),
                'days' => $this->l('days', 'EtsRVEntity'),
                'hour' => $this->l('hour', 'EtsRVEntity'),
                'hours' => $this->l('hours', 'EtsRVEntity'),
                'minute' => $this->l('minute', 'EtsRVEntity'),
                'minutes' => $this->l('minutes', 'EtsRVEntity'),
                'second' => $this->l('second', 'EtsRVEntity'),
                'seconds' => $this->l('seconds', 'EtsRVEntity'),
            ];

            self::$initialize = true;
        }
    }

    public function setEmployee($employee)
    {
        $this->employee = $employee;

        return $this;
    }

    public function setBackOffice($backOffice)
    {
        $this->backOffice = $backOffice;

        return $this;
    }

    public function setQA($qa)
    {
        $this->qa = $qa;
        $this->sf = $this->qa ? 'QA_' : '';

        return $this;
    }

    public function jsonExtra(&$response, $refreshController = null)
    {
        if ($this->employee) {
            $controllerName = $refreshController !== null ? $refreshController : trim(Tools::getValue('refreshController'));
            if ($controllerName !== '') {
                $currentIndex = $this->context->link->getAdminLink($controllerName);
                $controllerName .= 'Controller';
                if (!class_exists($controllerName)) {
                    require dirname(__FILE__) . '/../../controllers/admin/' . $controllerName . '.php';
                }
                $controller = new $controllerName();
                if ($controller instanceof AdminController) {
                    $controller::$currentIndex = $currentIndex;
                    $controller->processFilter();//modify
                    if ($this->context->cookie->__get('submitFilter' . $controller->list_id)) {

                    }
                }
            } else {
                $controller = $this->context->controller;
            }
            $controller->initBreadcrumbs();
            $controller->initToolbarFlags();
            // Process list filtering
            if ($this->context->cookie->__get('submitFilter' . $controller->list_id)) {
                $controller->processFilter();
            }
            $response['list'] = $controller->renderList();
            $response['qa'] = $this->qa;
        }

        return $response;
    }

    public function extraParams(&$url_params)
    {
        $list_ids = ['ets_rv_comment', 'ets_rv_reply_comment'];
        foreach ($list_ids as $list_id) {
            if (Tools::isSubmit('submitFilter' . $list_id)) {
                $url_params['submitFilter' . $list_id] = (int)Tools::getValue('submitFilter' . $list_id);
            }
        }
    }

    public function ajaxRender($value = null)
    {
        die($value);
    }

    /**
     * @param $id_product
     * @param $row
     * @param null $customer
     * @param null $content
     * @param Country $country
     * @param null $title
     */
    public function formatItem($id_product, &$row, $customer = null, $content = null, $country = null, $title = null)
    {
        $id_customer = isset($row['id_customer']) ? (int)$row['id_customer'] : 0;
        if (($customer instanceof Customer || $customer instanceof Employee) &&
            Validate::isLoadedObject($customer)
        ) {
            $row['firstname'] = $customer->firstname;
            $row['lastname'] = $customer->lastname;
            if (!$id_customer) {
                $id_customer = (int)$customer->id;
            }
        }
        if ($id_customer) {
            $row['id_customer'] = $id_customer;
            if ($this->employee) {
                $table = 'ets_rv_activity';
                $row['activity_link'] = EtsRVLink::getAdminLink(Ets_reviews::TAB_PREFIX . 'Activity', true, [], ['submitFilter' . $table => 1, $table . 'Filter_customer_name' => $id_customer], $this->context);
            }
        }
        $qa = isset($row['question']) && $row['question'] ? 1 : 0;
        $validateOnly = isset($row['validate']) ? (int)$row['validate'] : 0;

        $row['comment_allowed'] = $this->commentAllowed($id_customer, $qa);
        $row['action_allowed'] = $this->rawActions($id_customer, isset($row['id_guest']) ? (int)$row['id_guest'] : 0);
        $row['customer_edit_approved'] = Configuration::get('ETS_RV_' . ($qa ? 'QA_' : '') . 'CUSTOMER_EDIT_APPROVED');
        $row['edit'] = !($validateOnly == 3) && $row['action_allowed'] && ($row['customer_edit_approved'] || $validateOnly != 1);
        $row['customer_delete_approved'] = Configuration::get('ETS_RV_' . ($qa ? 'QA_' : '') . 'CUSTOMER_DELETE_APPROVED');
        $row['delete'] = $row['action_allowed'] && ($row['customer_delete_approved'] || $validateOnly != 1);

        if (!empty($row['customer_name'])) {
            $row['customer_name'] = htmlentities($row['customer_name']);
        }
        if ($this->employee && (int)Configuration::get('ETS_RV_MULTILANG_ENABLED')) {
            $languages = Language::getLanguages(false);
            if (!empty($row['id_ets_rv_reply_comment'])) {
                $object = new EtsRVReplyComment((int)$row['id_ets_rv_reply_comment']);
            } elseif (!empty($row['id_ets_rv_comment'])) {
                $object = new EtsRVComment((int)$row['id_ets_rv_comment']);
            } elseif (!empty($row['id_ets_rv_product_comment'])) {
                $object = new EtsRVProductComment((int)$row['id_ets_rv_product_comment']);
            }
            if ($languages && !empty($object)) {
                $icons = EtsRVComment::getIcons();
                foreach ($languages as $l) {
                    if (isset($object->title))
                        $object->title[(int)$l['id_lang']] = htmlentities($object->title[(int)$l['id_lang']]);
                    $object->content[(int)$l['id_lang']] = str_replace(array_keys($icons), $icons, $object->content[(int)$l['id_lang']]);
                }
                if (isset($object->title))
                    $row['title'] = $object->title;
                $row['content'] = $object->content;
            }
        } else {
            if ($title)
                $row['title'] = htmlentities($title);
            elseif (!empty($row['title']))
                $row['title'] = htmlentities($row['title']);
            $row['content'] = $content !== null && trim($content) != '' ? $content : $row['content'];
            $icons = EtsRVComment::getIcons();
            $row['content'] = str_replace(array_keys($icons), $icons, $row['content']);
        }
        $row['time_elapsed'] = $this->timeElapsedString($row['date_add']);
        $row['date_add'] = date($this->context->language->date_format_full ?: 'Y-m-d H:i:s', strtotime($row['date_add']));
        $row['upd_date'] = self::dateFormat($row['upd_date'], Context::getContext()->language->date_format_full);

        $VERIFIED_PURCHASE = isset($row['verified_purchase']) && trim($row['verified_purchase']) !== '' ? trim($row['verified_purchase']) : 'auto';
        $VERIFIED_PURCHASE_LABEL = trim(Configuration::get('ETS_RV_VERIFIED_PURCHASE_LABEL', $this->context->language->id));
        $row['verify_purchase'] = $VERIFIED_PURCHASE == 'yes' || $id_customer && $VERIFIED_PURCHASE != 'no' && EtsRVProductComment::verifyPurchase((int)$id_product, $id_customer) && $VERIFIED_PURCHASE_LABEL !== '' ? EtsRVTools::getIcon('check') . ' ' . $VERIFIED_PURCHASE_LABEL : '';

        $row['edit_profile'] = 0;
        if ($validateOnly == EtsRVProductComment::STATUS_REFUSE) {
            $row['comment_refuse'] = $this->l('Declined', 'EtsRVEntity');
        }
        $row['comment_no_approve'] = $validateOnly < 1 ? ($this->employee || $this->module->isStaffLogged() && $this->context->customer->id !== $id_customer ? $this->l('Pending for approval', 'EtsRVEntity') : $this->l('Pending for approval, only you can see it', 'EtsRVEntity')) : '';

        if (isset($row['employee']) && (int)$row['employee'] > 0) {
            if ($this->employee && $this->employee == (int)$row['employee']) {
                $row['author_profile'] = $this->l('(You)', 'EtsRVEntity');
                $row['edit_profile'] = 1;
            } else {
                $employee = new Employee((int)$row['employee']);
                if ($employee->id_profile > 0) {
                    $profile = new Profile($employee->id_profile, $this->context->language->id);
                    $row['author_profile'] = '(' . $profile->name . ')';
                } else
                    $row['author_profile'] = '';
            }
            $info = EtsRVStaff::getInfos($row['employee']);
            if ($info) {
                if (isset($info['display_name']) && trim($info['display_name']) !== '')
                    $row['customer_name'] = $info['display_name'];
                if (isset($info['avatar']) && trim($info['avatar']) !== '' && @file_exists(_PS_IMG_DIR_ . $this->module->name . '/a/' . trim($info['avatar'])))
                    $row['avatar'] = $info['avatar'];
            }
        } elseif ($id_customer > 0 && !$this->employee && ($this->context->customer->id == $id_customer || $this->module->isStaffLogged($id_customer))) {

            $author = $this->module->isStaffLogged($id_customer);
            $row['author_profile'] = sprintf($this->l('(%s)', 'EtsRVEntity'), ($this->context->customer->id == $id_customer ? $this->l('You', 'EtsRVEntity') . ($author ? ' - ' : '') : '') . ($author ? $this->l('Support staff', 'EtsRVEntity') : ''));
            $row['my_account'] = $this->context->link->getPageLink('identity', true);
            $row['edit_profile'] = 1;
        }
        if ($id_customer > 0) {
            $info = EtsRVProductCommentCustomer::getCustomer($id_customer);
            if ($info) {
                if (isset($info['display_name']) && trim($info['display_name']) !== '')
                    $row['customer_name'] = $info['display_name'];
                if (empty($row['avatar']) && isset($info['avatar']) && trim($info['avatar']) !== '' && @file_exists(_PS_IMG_DIR_ . $this->module->name . '/a/' . trim($info['avatar'])))
                    $row['avatar'] = $info['avatar'];
            }
        }
        $row['employee'] = (!empty($row['employee']) || ($id_customer && $this->module->isBackOffice($id_customer))) ? 1 : 0;
        if (empty($row['avatar']) && $id_customer) {
            $row['avatar'] = EtsRVProductCommentCustomer::getAvatarByIdCustomer($id_customer);
        }
        $row['avatar'] = !empty($row['avatar']) ? $this->module->getMediaLink(_PS_IMG_ . $this->module->name . '/a/' . $row['avatar']) : '';
        if (empty($row['customer_name']) && !isset($row['firstname']) && !isset($row['lastname'])) {
            $row['customer_name'] = $this->l('Deleted account', 'EtsRVEntity');
        }
        //Format customer name
        $this->formatCustomerName($row);
        if (isset($row['id_country'])
            && (int)$row['id_country'] > 0
            && ($country = new Country((int)$row['id_country'], $this->context->language->id))
        ) {
            $row['iso_code'] = $country->iso_code;
            $row['country_name'] = $country->name;
        } elseif ($country instanceof Country) {
            $row['iso_code'] = $country->iso_code;
            if (is_array($country->name)) {
                if (isset($country->name[$this->context->language->id]))
                    $row['country_name'] = $country->name[$this->context->language->id];
                else
                    $row['country_name'] = $country->name[(int)Configuration::get('PS_LANG_DEFAULT')];
            } else
                $row['country_name'] = $country->name;
        } elseif (!empty($row['iso_code'])) {
            $id_country = Country::getByIso(trim($row['iso_code']));
            $country = new Country($id_country, $this->context->language->id);
            $row['country_name'] = $country->name;
        }
        $row['iso_code'] = !empty($row['iso_code']) && @file_exists(dirname(__FILE__) . '/../../views/img/flag/' . Tools::strtolower($row['iso_code']) . '.gif') ? $this->module->getMediaLink($this->module->getPathUri() . 'views/img/flag/' . Tools::strtolower($row['iso_code']) . '.gif') : '';
        $row['id_product'] = (int)$id_product;

        // Set as Private:
        if (isset($row['id_ets_rv_reply_comment']) && (int)$row['id_ets_rv_reply_comment']) {
            $row['private'] = ($comment = EtsRVReplyComment::getParent((int)$row['id_ets_rv_reply_comment'], 'ets_rv_reply_comment', 'ets_rv_comment')) && isset($comment['validate']) && $comment['validate'] == 2 || ($productComment = EtsRVComment::getParent((int)$comment['id_ets_rv_comment'], 'ets_rv_comment', 'ets_rv_product_comment')) && isset($productComment['validate']) && (int)$productComment['validate'] == 2 || isset($row['validate']) && (int)$row['validate'] == 2;
        } elseif (isset($row['id_ets_rv_comment']) && (int)$row['id_ets_rv_comment']) {
            $row['private'] = ($productComment = EtsRVComment::getParent((int)$row['id_ets_rv_comment'], 'ets_rv_comment', 'ets_rv_product_comment')) && isset($productComment['validate']) && (int)$productComment['validate'] == 2 || isset($row['validate']) && (int)$row['validate'] == 2;
        } else {
            $row['private'] = isset($row['validate']) && (int)$row['validate'] == 2;
        }
    }

    public function formatCustomerName(&$row)
    {
        $displayName = (int)Configuration::get('ETS_RV_DISPLAY_NAME');
        if ($displayName === EtsRVProductComment::DISPLAY_CUSTOMER_FULL_NAME)
            return;
        $row['customer_name'] = isset($row['customer_name']) && $row['customer_name'] ? trim($row['customer_name']) : '';
        if (!empty($row['customer_name']) && ($nameParts = explode(' ', $row['customer_name']))) {
            $firstName = $nameParts[0];
            $lastName = isset($nameParts[1]) ? $nameParts[1] : null;
        } else {
            $firstName = $row['firstname'];
            $lastName = $row['lastname'];
        }
        if (Tools::strlen($firstName) <= 1 || Tools::strlen($lastName) <= 1)
            return;
        if ($displayName === EtsRVProductComment::DISPLAY_CUSTOMER_ACRONYM_FIRSTNAME)
            $row['customer_name'] = Tools::substr($firstName, 0, 1) . '. ' . $lastName;
        else
            $row['customer_name'] = $firstName . ' ' . Tools::substr($lastName, 0, 1) . '.';
    }

    public static function dateFormat($date, $date_format_full = null)
    {
        return (null === $date || $date === '0000-00-00 00:00:00' || $date === '0000-00-00' ? '' : date(($date_format_full ?: Context::getContext()->language->date_format_full), strtotime($date)));
    }

    public function commentAllowed($idCustomer, $question = 0)
    {
        $access = trim(Configuration::get('ETS_RV_' . ($question ? 'QA_' : '') . 'WHO_COMMENT_REPLY'));
        return
            $this->backOffice ||
            $idCustomer > 0 && $access == 'admin_author' && isset($this->context->customer->id) && $this->context->customer->isLogged() && $this->context->customer->id == $idCustomer ||
            $access == 'user' && isset($this->context->customer->id) && $this->context->customer->isLogged();
    }

    public function rawActions($idCustomer, $idGuest)
    {
        if ($this->backOffice)
            return true;
        if (isset($this->context->customer->id) || isset($this->context->cookie->id_guest)) {
            return
                $idCustomer > 0 && isset($this->context->customer->id) && $this->context->customer->isLogged() && (int)$this->context->customer->id == (int)$idCustomer ||
                $idGuest > 0 && isset($this->context->cookie->id_guest) && (int)$this->context->cookie->id_guest == (int)$idGuest;
        }

        return false;
    }

    public function timeElapsedString($datetime, $full = false)
    {
        if (!$datetime)
            return 0;
        if (!(int)Configuration::get('ETS_RV_DISPLAY_TIME_PERIOD')) {
            return date(Context::getContext()->language->date_format_full, strtotime($datetime));
        }

        $this->init();

        $now = new DateTime(date('Y-m-d H:i:s'));
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        //$diff->w = floor($diff->d / 7);
        //$diff->d -= $diff->w * 7;

        $string = array(
            'y' => self::$translate['year'],
            'm' => self::$translate['month'],
            //'w' => self::$translate['week'],
            'd' => self::$translate['day'],
            'h' => self::$translate['hour'],
            'i' => self::$translate['minute'],
            's' => self::$translate['second'],
        );

        $string2 = array(
            'y' => self::$translate['years'],
            'm' => self::$translate['months'],
            //'w' => self::$translate['weeks'],
            'd' => self::$translate['days'],
            'h' => self::$translate['hours'],
            'i' => self::$translate['minutes'],
            's' => self::$translate['seconds'],
        );

        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . ($diff->$k > 1 ? $string2[$k] : $v);
            } else {
                unset($string[$k]);
            }
        }

        if (!$full)
            $string = array_slice($string, 0, 1);

        return $string ? implode(', ', $string) . ' ' . self::$translate['ago'] : self::$translate['just_now'];
    }

    public function refreshCACertFile()
    {
        if ((time() - @filemtime(_PS_CACHE_CA_CERT_FILE_) > 0)) {
            $stream_context = @stream_context_create(array(
                'http' => array('timeout' => 3),
                'ssl' => array(
                    'cafile' => $this->getBundledCaBundlePath(),
                ),
            ));
            $ca_cert_content = Tools::file_get_contents(self::CACERT_LOCATION, false, $stream_context);
            if (empty($ca_cert_content)) {
                $ca_cert_content = Tools::file_get_contents($this->getBundledCaBundlePath());
            }
            if (preg_match('/(.*-----BEGIN CERTIFICATE-----.*-----END CERTIFICATE-----){50}$/Uims', $ca_cert_content) && Tools::substr(rtrim($ca_cert_content), -1) == '-') {
                @file_put_contents(_PS_CACHE_CA_CERT_FILE_, $ca_cert_content);
            }
        }
    }

    public function getBundledCaBundlePath()
    {
        $caBundleFile = _PS_CACHE_CA_CERT_FILE_;
        if (0 === strpos($caBundleFile, 'phar://')) {
            @file_put_contents(
                $tempCaBundleFile = tempnam(sys_get_temp_dir(), 'openssl-ca-bundle-'),
                \Tools::file_get_contents($caBundleFile)
            );
            register_shutdown_function(function () use ($tempCaBundleFile) {
                if (@file_exists($tempCaBundleFile))
                    @unlink($tempCaBundleFile);
            });
            $caBundleFile = $tempCaBundleFile;
        }
        return $caBundleFile;
    }

    public function verifyReCAPTCHA(&$errors)
    {
        if ($this->backOffice)
            return true;
        $configs = $this->module->getReCaptchaConfigs();
        if (!($reCaptchaFor = trim(Tools::getValue('reCaptchaFor'))) || !(int)$configs['ETS_RV_RECAPTCHA_ENABLED'] || !in_array($reCaptchaFor, $configs['ETS_RV_RECAPTCHA_FOR']) || (int)$configs['ETS_RV_RECAPTCHA_USER_REGISTERED'] && $this->context->customer->isLogged())
            return true;
        if (Tools::getIsset('g-recaptcha-response')) {
            $reCaptcha = Tools::getValue('g-recaptcha-response');
            if (!$reCaptcha)
                $errors[] = $this->l('reCAPTCHA is error', 'EtsRVEntity');
            if (!count($errors)) {
                $secret = Configuration::get('ETS_RV_RECAPTCHA_SECRET_KEY_V' . (Configuration::get('ETS_RV_RECAPTCHA_TYPE') != 'recaptcha_v3' ? '2' : '3'));
                $http_build_query = http_build_query(array(
                    'secret' => $secret,
                    'response' => $reCaptcha
                ));
                $curl_timeout = 5;
                $this->refreshCACertFile();
                $stream_context = @stream_context_create(array(
                    'http' => array('timeout' => $curl_timeout),
                    'ssl' => array(
                        'verify_peer' => true,
                        'cafile' => $this->getBundledCaBundlePath(),
                    ),
                ));
                $response = Tools::file_get_contents('https://www.google.com/recaptcha/api/siteverify?' . $http_build_query, false, $stream_context, $curl_timeout);
                $response = json_decode($response);
                if (!$response || (property_exists($response, 'success') && $response->success == false) || (property_exists($response, 'score') && $response->score < 0.5)) {
                    $errors[] = $this->l('reCAPTCHA is invalid.', 'EtsRVEntity');
                }
            }
        } else
            $errors[] = $this->l('404 not found!', 'EtsRVEntity');

        return count($errors) > 0 ? false : true;
    }

    public function viewAccess($action = null)
    {
        if ($this->backOffice) {
            return true;
        }
        if (
            !(int)Configuration::get('ETS_RV_' . (!$this->qa ? 'REVIEW' : 'QUESTION') . '_ENABLED') ||
            $action !== null
            && (
                trim($action) == 'edit'
                && !(int)Configuration::get('ETS_RV_' . $this->sf . 'ALLOW_EDIT_COMMENT') ||
                trim($action) == 'delete'
                && !(int)Configuration::get('ETS_RV' . ($this->qa ? '_QA' : '') . '_ALLOW_DELETE_COMMENT')
            )
        ) {
            $this->_errors[] = $this->l('Permission denied!', 'EtsRVEntity');
        } elseif (!$this->backOffice && $this->context->customer->id && EtsRVProductCommentCustomer::isBlockByIdCustomer((int)$this->context->customer->id)) {
            $this->_errors[] = $this->l('You\'re not allowed to leave a comment', 'EtsRVEntity');
        }
        if ($this->_errors) {
            $this->ajaxRender(json_encode([
                'success' => false,
                'error' => Tools::nl2br(implode(PHP_EOL, $this->_errors)),
            ]));
        }
    }

    public function addActivity($object, $type, $action, $id_product, $content, $context = null, $customer_name = null)
    {
        if ((int)Configuration::get('ETS_RV_RECORD_ADMIN') > 0 && $this->employee ||
            !Validate::isLoadedObject($object) ||
            !($object instanceof EtsRVComment || $object instanceof EtsRVProductComment || $object instanceof EtsRVReplyComment) ||
            !$id_product ||
            !Validate::isUnsignedInt($id_product)
            || !EtsRVDefines::getInstance()->getActivityTypes($type)
            || !EtsRVDefines::getInstance()->getActivityActions($action)
            || trim($content) == ''
        ) {
            return false;
        }

        $recorded_type = null;
        if (in_array($action, [EtsRVActivity::ETS_RV_ACTION_REVIEW, EtsRVActivity::ETS_RV_ACTION_REPLY]) || $type == EtsRVActivity::ETS_RV_TYPE_COMMENT && $action == EtsRVActivity::ETS_RV_ACTION_COMMENT)
            $recorded_type = EtsRVActivity::ETS_RV_RECORDED_REVIEWS;
        elseif (in_array($action, [EtsRVActivity::ETS_RV_ACTION_QUESTION, EtsRVActivity::ETS_RV_ACTION_ANSWER]) || ($type == EtsRVActivity::ETS_RV_TYPE_COMMENT_ANSWER || $type == EtsRVActivity::ETS_RV_TYPE_COMMENT_QUESTION) && $action == EtsRVActivity::ETS_RV_ACTION_COMMENT)
            $recorded_type = EtsRVActivity::ETS_RV_RECORDED_QUESTIONS;
        elseif (in_array($action, [EtsRVActivity::ETS_RV_ACTION_LIKE, EtsRVActivity::ETS_RV_ACTION_DISLIKE]))
            $recorded_type = EtsRVActivity::ETS_RV_RECORDED_USEFULNESS;

        $recorded_activities = array_map('intval', explode(',', trim(Configuration::get('ETS_RV_RECORDED_ACTIVITIES'))));

        if (!in_array($recorded_type, $recorded_activities)) {
            return true;
        }
        if ($context == null) {
            $context = Context::getContext();
        }
        if ((int)$object->id > 0) {
            $data = array(
                'id_customer' => isset($context->customer->id) && $context->customer->id > 0 && $context->customer->isLogged() ? (int)$context->customer->id : (isset($object->id_customer) && $object->id_customer > 0 ? (int)$object->id_customer : 0),
                'id_guest' => isset($context->cookie->id_guest) ? (int)$context->cookie->id_guest : 0,
                'employee' => $this->employee ?: 0,
                'id_product' => $id_product,
                'type' => $type,
                'action' => $action,
                'id_ets_rv_product_comment' => 0,
                'id_ets_rv_comment' => 0,
                'id_ets_rv_reply_comment' => 0,
                'content' => ($content !== '' ? ($customer_name !== null ? preg_replace('/%user%/', $customer_name, $content) : $content) : ''),
                'date_add' => date('Y-m-d H:i:s')
            );
            if ($object instanceof EtsRVReplyComment) {
                $data['id_ets_rv_reply_comment'] = (int)$object->id;
            } elseif ($object instanceof EtsRVComment) {
                $data['id_ets_rv_comment'] = (int)$object->id;
            } elseif ($object instanceof EtsRVProductComment) {
                $data['id_ets_rv_product_comment'] = (int)$object->id;
            }
            $exec = Db::getInstance()->insert('ets_rv_activity', $data);

            if ($exec && $this->employee)
                EtsRVActivity::makeRead($this->employee, Db::getInstance()->Insert_ID());

            return $exec;
        }
    }

    public function isGuestLogin()
    {
        $customerId = (int)$this->context->cookie->id_customer;
        if (!$customerId && !$this->backOffice) {
            $idProduct = (int)Tools::getValue('id_product');
            $this->ajaxRender(json_encode([
                'success' => false,
                'error' => sprintf($this->l('You need to be %s or%s to give your appreciation of a comment.', 'EtsRVCommentEntity')
                    , EtsRVTools::displayText($this->l('signed in', 'EtsRVCommentEntity'), 'a', ['href' => $this->context->link->getPageLink('authentication') . ($idProduct ? '?back=' . $this->context->link->getProductLink($idProduct) : ''), 'rel' => 'nofollow'])
                    , EtsRVTools::displayText($this->l('create an account', 'EtsRVCommentEntity'), 'a', ['href' => $this->context->link->getPageLink('authentication&create_account=1') . ($idProduct ? '&back=' . $this->context->link->getProductLink($idProduct) : ''), 'rel' => 'nofollow'])
                ),
            ]));
        }
    }

    public function isGuest()
    {
        return !$this->isCustomerLogged() && isset($this->context->cookie->id_guest) && $this->context->cookie->id_guest > 0;
    }

    public function isCustomerLogged()
    {
        return $this->backOffice || isset($this->context->customer) && $this->context->customer->id > 0 && $this->context->customer->isLogged();
    }

    public function isCustomerBlocked()
    {
        return $this->isCustomerLogged() && EtsRVProductCommentCustomer::isBlockByIdCustomer($this->context->customer->id);
    }
}