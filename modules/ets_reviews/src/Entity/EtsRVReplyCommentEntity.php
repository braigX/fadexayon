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

class EtsRVReplyCommentEntity extends EtsRVEntity
{
    public static function getInstance()
    {
        if (empty(self::$_INSTANCE['entity_reply']) || !self::$_INSTANCE['entity_reply'] instanceof EtsRVCommentEntity) {
            self::$_INSTANCE['entity_reply'] = new EtsRVReplyCommentEntity();
        }

        return self::$_INSTANCE['entity_reply'];
    }

    public function getReplyComment()
    {
        $replyComment = new EtsRVReplyComment((int)Tools::getValue('id_reply_comment'));
        $replyComment->origin_content = EtsRVReplyComment::getOriginContentById($replyComment->id);

        $this->ajaxRender(json_encode($replyComment));
    }

    public function getReplyComments()
    {
        $idProduct = (int)Tools::getValue('id_product');
        $commentId = (int)Tools::getValue('id_comment');
        $page = (int)Tools::getValue('page', 1);
        if ($page < 1)
            $page = 1;
        $begin = (int)Tools::getValue('begin');
        if ($begin < 0)
            $begin = 0;
        if (($replies_per_page = (int)Tools::getValue('replies_per_page')) < 1 && ($replies_per_page = (int)Configuration::get('ETS_RV_' . $this->sf . 'REPLIES_PER_PAGE')) < 1) {
            $replies_per_page = 5;
        }
        if ($this->qa)
            $default_sort_by = trim(Configuration::get('ETS_RV_QA_DEFAULT_SORT_BY')) ?: false;
        else
            $default_sort_by = trim(Configuration::get('ETS_RV_DEFAULT_SORT_BY')) ?: false;
        $sortBy = trim(Tools::getValue('sort_by')) ?: $default_sort_by;
        $firstOnly = (int)Tools::getValue('first') ? 1 : 0;
        $replies_initial = (int)Configuration::get('ETS_RV_' . $this->sf . 'REPLIES_INITIAL');
        if ($replies_initial < 1)
            $replies_initial = 1;

        $validateOnly = ($this->backOffice || (int)Configuration::get('ETS_RV_' . $this->sf . 'AUTO_APPROVE')) ? null : 1;
        // forward:
        $rest = (int)Tools::getValue('rest');

        $replyCommentRepository = EtsRVReplyCommentRepository::getInstance();
        $replyCommentsNb = $rest > 0 ? $rest : $replyCommentRepository->getReplyCommentsNumber(
            $commentId,
            $this->context->language->id,
            $validateOnly,
            $this->backOffice,
            $this->context,
            $this->qa
        );

        if ($begin > $replyCommentsNb) {
            $this->ajaxRender(json_encode([
                'success' => false,
                'errors' => $this->l('The reply is empty.', 'EtsRVReplyCommentEntity'),
            ]));
        }
        $replyComments = $replyCommentRepository->paginate(
            $commentId,
            $this->context->language->id,
            $page,
            ($begin > 0 || $rest > 0 ? $replies_per_page : $replies_initial),
            $validateOnly,
            $this->backOffice,
            $firstOnly,
            $begin,
            $sortBy,
            $this->context,
            $this->qa
        );
        if ($rest > 0) {
            $replyComments = EtsRVTools::quickSort($replyComments, 'id_ets_rv_reply_comment');
            $begin -= $replies_per_page;
            $rest -= $replies_per_page;
            $replyCommentsNb = $rest;
            if ($begin > 0) {
                $replies_per_page = $begin >= $rest ? 0 : ($rest - $begin >= $replies_per_page ? $replies_per_page : ($rest - $begin));
            } else {
                $begin = 0;
                $replies_per_page = $rest > $replies_per_page ? $replies_per_page : $rest;
            }
        } else {
            if ($begin > 0) {
                $begin += $replies_per_page;
                $replies_per_page = $begin >= $replyCommentsNb ? 0 : ($replyCommentsNb - $begin >= $replies_per_page ? $replies_per_page : ($replyCommentsNb - $begin));
            } else
                $begin += $replies_initial;
        }

        $responseArray = [
            'success' => true,
            'begin' => $begin,
            'nb' => $replyCommentsNb,
            'replies_per_page' => $replies_per_page,
            'replies' => [],
        ];
        foreach ($replyComments as $replyComment) {
            $this->formatItem($idProduct, $replyComment);
            // Like or Dislike:
            $usefulness = $replyCommentRepository->getReplyCommentUsefulness($replyComment['id_ets_rv_reply_comment'], $this->qa, isset($this->context->customer->id) ? $this->context->customer->id : 0, $this->employee);
            $replyComment = array_merge($replyComment, $usefulness);
            $responseArray['replies'][] = $replyComment;
        }
        $this->ajaxRender(json_encode($responseArray));
    }

    public function hasPrivateReplyComment($commentId)
    {
        if ($this->backOffice)
            return true;
        $comment = new EtsRVComment($commentId);
        return
            (int)$comment->validate == 1 ||
            (int)$comment->id_customer > 0 && isset($this->context->customer->id) && (int)$this->context->customer->id == (int)$comment->id_customer;
    }

    public function postReplyComment()
    {
        $replyCommentId = (int)Tools::getValue('id_reply_comment');
        $this->viewAccess($replyCommentId ? 'edit' : null);
        if (!$this->commentAllowed(isset($this->context->cookie->id_customer) ? (int)$this->context->cookie->id_customer : 0, $this->qa)) {
            $this->ajaxRender(json_encode([
                'success' => false,
                'error' => $this->l('Permission denied', 'EtsRVReplyCommentEntity'),
            ]));
        }

        $idProduct = (int)Tools::getValue('id_product');
        if (!$this->backOffice && (!isset($this->context->cookie->id_customer) || !(int)$this->context->cookie->id_customer)) {
            $this->ajaxRender(json_encode([
                'success' => false,
                'error' => sprintf(
                    $this->l('You need to be %s or %s to post your reply comment.', 'EtsRVReplyCommentEntity')
                    , EtsRVTools::displayText($this->l('signed in', 'EtsRVReplyCommentEntity'), 'a', ['href' => $this->context->link->getPageLink('authentication') . ($idProduct ? '?back=' . $this->context->link->getProductLink($idProduct) : ''), 'rel' => 'nofollow'])
                    , EtsRVTools::displayText($this->l('create an account', 'EtsRVReplyCommentEntity'), 'a', ['href' => $this->context->link->getPageLink('authentication&create_account=1') . ($idProduct ? '&back=' . $this->context->link->getProductLink($idProduct) : ''), 'rel' => 'nofollow'])
                ),
            ]));
        }

        $replyComment = new EtsRVReplyComment($replyCommentId);
        if (!$this->backOffice && $replyComment->id &&
            (
                $replyComment->id_customer && (int)$replyComment->id_customer !== (int)$this->context->cookie->id_customer ||
                (int)$replyComment->validate == 1 && !Configuration::get('ETS_RV' . ($replyComment->question ? '_QA' : '') . '_CUSTOMER_EDIT_APPROVED')
            )
        ) {
            $this->ajaxRender(json_encode([
                'success' => false,
                'error' => $this->l('You cannot edit the reply comment', 'EtsRVReplyCommentEntity'),
            ]));
        }
        if ((int)Configuration::get('ETS_RV_RECAPTCHA_ENABLED') && !$this->verifyReCAPTCHA($this->_errors)) {
            $this->ajaxRender(json_encode([
                'success' => false,
                'error' => Tools::nl2br(implode(PHP_EOL, $this->_errors)),
            ]));
        }
        $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
        $language = new Language($id_lang_default);
        $commentId = (int)Tools::getValue('id_comment');
        $comment_content = trim(Tools::getValue('comment_content', Tools::getValue('comment_content_' . $id_lang_default)));
        $date_add = trim(Tools::getValue('date_add'));

        if (!$commentId) {
            $this->_errors[] = $this->l('Topic cannot be empty', 'EtsRVReplyCommentEntity');
        } elseif (!$this->hasPrivateReplyComment($commentId)) {
            $this->_errors[] = $this->l('Permission denied.', 'EtsRVReplyCommentEntity');
        } else {
            $maximum_character = Configuration::get('ETS_RV_' . ($this->qa ? 'QA_' : '') . 'MAX_LENGTH');
            if (trim($maximum_character) === '')
                $maximum_character = EtsRVModel::NAME_MAX_LENGTH;
            $minimum_character = Configuration::get('ETS_RV_' . ($this->qa ? 'QA_' : '') . 'MIN_LENGTH');
            if (trim($minimum_character) === '')
                $minimum_character = EtsRVModel::NAME_MIN_LENGTH;

            if ($comment_content == '') {
                $this->_errors[] = $this->l('The reply cannot be empty', 'EtsRVReplyCommentEntity');
            } elseif (!Validate::isCleanHtml($comment_content) || preg_match('/[{}]/i', $comment_content) || !preg_match('/^(?!.*<[^bi\/>]+>).*$/s', $comment_content)) {
                $this->_errors[] = sprintf($this->l('The reply comment %s is invalid', 'EtsRVReplyCommentEntity'), ($this->backOffice ? ' ' . sprintf($this->l('language (%s)', 'EtsRVReplyCommentEntity'), $language->iso_code) : ''));
            } elseif ((int)$maximum_character > 0 && Tools::strlen($comment_content) > (int)$maximum_character) {
                $this->_errors[] = sprintf($this->l('Reply %s cannot be longer than %s characters', 'EtsRVReplyCommentEntity'), ($this->backOffice ? ' ' . sprintf($this->l('language (%s)', 'EtsRVReplyCommentEntity'), $language->iso_code) : ''), $maximum_character);
            } elseif ((int)$minimum_character > 0 && Tools::strlen($comment_content) < (int)$minimum_character) {
                $this->_errors[] = sprintf($this->l('Content %s cannot be shorter than %s characters', 'EtsRVReplyCommentEntity'), ($this->backOffice ? ' ' . sprintf($this->l('language (%s)', 'EtsRVReplyCommentEntity'), $language->iso_code) : ''), $minimum_character);
            } elseif (Tools::getIsset('date_add') && $date_add === '')
                $this->_errors[] = $this->l('Date add is required', 'EtsRVReplyCommentEntity');
            elseif ($date_add !== '' && !Validate::isDate($date_add))
                $this->_errors[] = $this->l('Date add is invalid', 'EtsRVReplyCommentEntity');
            if ($this->backOffice) {
                foreach (Language::getLanguages(false) as $l) {
                    if ((int)$l['id_lang'] !== $id_lang_default && ($comment_content_lang = trim(Tools::getValue('comment_content', Tools::getValue('comment_content_' . $l['id_lang'])))) !== '') {
                        if (!Validate::isCleanHtml($comment_content_lang) || preg_match('/[{}]/i', $comment_content_lang) || !preg_match('/^(?!.*<[^bi\/>]+>).*$/s', $comment_content_lang)) {
                            $this->_errors[] = sprintf($this->l('The reply in language (%s) is invalid', 'EtsRVReplyCommentEntity'), $l['iso_code']);
                        } elseif ((int)$maximum_character > 0 && Tools::strlen($comment_content_lang) > (int)$maximum_character) {
                            $this->_errors[] = sprintf($this->l('The reply in language (%s) cannot be longer than %s characters', 'EtsRVReplyCommentEntity'), $l['iso_code'], $maximum_character);
                        } elseif ((int)$minimum_character > 0 && Tools::strlen($comment_content_lang) < (int)$minimum_character) {
                            $this->_errors[] = sprintf($this->l('Content in language (%s) cannot be shorter than %s characters', 'EtsRVReplyCommentEntity'), $l['iso_code'], $minimum_character);
                        }
                    }
                }
            }
        }
        if (count($this->_errors) > 0) {
            $this->ajaxRender(json_encode([
                'success' => false,
                'error' => Tools::nl2br(implode(PHP_EOL, $this->_errors)),
            ]));
        }
        if (!$replyComment->id) {
            $replyComment->id_ets_rv_comment = $commentId;
            $replyComment->id_customer = !$this->employee ? (int)$this->context->cookie->id_customer : 0;
            $replyComment->employee = $this->employee;
            $replyComment->date_add = date('Y-m-d H:i:s');
            $replyComment->question = $this->qa;
            $validateOnly = (int)Configuration::get('ETS_RV_' . $this->sf . 'AUTO_APPROVE') ? 1 : 0;
            $replyComment->validate = ($this->backOffice || $validateOnly || (!$this->qa && (int)Configuration::get('ETS_RV_AUTO_APPROVE_PURCHASED') && (int)$this->context->cookie->id_customer && EtsRVProductComment::verifyPurchase((int)$this->context->cookie->id_customer, $idProduct))) ? EtsRVReplyComment::STATUS_APPROVE : EtsRVReplyComment::STATUS_PENDING;
        } else {
            $replyComment->upd_date = date('Y-m-d H:i:s');
            if ($date_add !== '')
                $replyComment->date_add = $date_add;
        }
        $languages = Language::getLanguages(false);
        if ($languages) {
            $multiLang = (int)Configuration::get('ETS_RV_MULTILANG_ENABLED');
            foreach ($languages as $l) {
                $replyComment->content[(int)$l['id_lang']] = $multiLang ? (trim(Tools::getValue('comment_content_' . (int)$l['id_lang'])) ?: $comment_content) : null;
            }
            if (($errors = $replyComment->validateFields(false, true)) === true && $replyComment->save()) {
                // Add activity
                if (!$replyCommentId) {
                    $content = $this->qa ? 'commented_on_an_answer' : 'replied_to_a_comment';
                    $this->addActivity($replyComment, $this->qa ? EtsRVActivity::ETS_RV_TYPE_COMMENT_ANSWER : EtsRVActivity::ETS_RV_TYPE_REPLY_COMMENT, $this->qa ? EtsRVActivity::ETS_RV_ACTION_COMMENT : EtsRVActivity::ETS_RV_ACTION_REPLY, $idProduct, $content, $this->context);
                }
                EtsRVReplyComment::saveOriginLang($replyComment->id, $this->context->language->id, $comment_content);
            } elseif (is_array($errors))
                $this->_errors = $errors;
        }

        $customer = !empty($this->context->cookie->id_customer) ? $this->context->customer : (!empty($this->context->employee) ? $this->context->employee : false);
        if ($customer === false)
            $this->_errors = $this->l('User cannot be empty', 'EtsRVReplyCommentEntity');

        // Send mail:
        if (!$this->_errors && !$replyCommentId) {
            $this->replyCommentMailToPosted($replyComment, $idProduct, $customer);
        }
        // End:
        $json = [];
        if (!$this->_errors) {
            $replyCommentArray = $replyComment->toArray();
            $id_address = $replyComment->id_customer > 0 ? Address::getFirstCustomerAddressId($customer->id) : 0;
            if ($id_address > 0) {
                $address = new Address($id_address);
                $country = new Country($address->id_country, $this->context->language->id);
            } else
                $country = null;
            $this->formatItem($idProduct
                , $replyCommentArray
                , $customer
                , $comment_content
                , $country
            );
            $json['comment'] = $replyCommentArray;
        }
        $hasError = count($this->_errors) > 0;
        $json += [
            'success' => !$hasError,
            'error' => $hasError ? Tools::nl2br(implode(PHP_EOL, $this->_errors)) : false
        ];
        $this->ajaxRender(json_encode($this->jsonExtra($json)));
    }

    /**** sendmail ****/

    public function replyCommentMailToPosted($replyComment, $idProduct = 0, $customer = null)
    {
        if (!$replyComment || $idProduct && !Validate::isUnsignedInt($idProduct))
            return false;

        if (!$replyComment instanceof EtsRVReplyComment && Validate::isUnsignedInt($replyComment))
            $replyComment = new EtsRVReplyComment($replyComment);

        if ($customer == null)
            $customer = (int)$replyComment->employee ? new Employee((int)$replyComment->employee) : new Customer((int)$replyComment->id_customer);

        $from_person_name = $customer->firstname . ' ' . $customer->lastname;
        $languageObj = new Language($customer->id_lang);
        $idLang = $languageObj->id ?: $this->context->language->id;

        if ($idProduct < 1) {
            $commentData = EtsRVReplyComment::getData($replyComment->id, $idLang);
            $idProduct = !empty($commentData['id_product']) ? (int)$commentData['id_product'] : 0;
        }

        $templateVars = [];
        if ($replyComment->question) {
            $productComment = EtsRVReplyComment::getProductCommentById($replyComment->id);
            if (Validate::isLoadedObject($productComment))
                $originLang = EtsRVProductComment::getOriginLang($productComment->id);
        }

        $template = 'person_' . ($replyComment->question ? 'commented' : 'replied');
        if ($replyComment->validate && EtsRVEmailTemplate::isEnabled($template)) {
            $comment = new EtsRVComment((int)$replyComment->id_ets_rv_comment);
            if ($comment->employee > 0 && ($replyComment->employee !== $comment->employee || $replyComment->id_customer > 0) || $comment->id_customer > 0 && ((int)$replyComment->id_customer !== (int)$comment->id_customer || $replyComment->employee > 0)) {

                $person = (int)$comment->id_customer ? new Customer((int)$comment->id_customer) : new Employee((int)$comment->employee);
                $person_name = $person->firstname . ' ' . $person->lastname;

                $languageObj = new Language($person->id_lang);
                $idLang = $languageObj->id ?: $this->context->language->id;
                $product = new Product($idProduct, false, $idLang);
                $productLink = $this->context->link->getProductLink($product, $product->link_rewrite, $product->category, $product->ean13, $idLang);
                $object = [
                    'og' => $replyComment->question ? 'answer' : 'comment',
                    't' => $replyComment->question ? $this->l('answer', 'EtsRVReplyCommentEntity') : $this->l('comment', 'EtsRVReplyCommentEntity')
                ];
                $templateVars = [
                    '{object_content}' => isset($productComment) && $productComment && $productComment instanceof EtsRVProductComment && isset($productComment->title[$idLang]) && $productComment->title[$idLang] ? $productComment->title[$idLang] : (isset($originLang) && isset($originLang['title']) ? $originLang['title'] : ''),
                    '{content}' => isset($replyComment->content[$idLang]) ? $replyComment->content[$idLang] : EtsRVReplyComment::getOriginContentById($replyComment->id),
                    '{from_person_name}' => $from_person_name,
                    '{person_name}' => $person_name,
                    '{product_name}' => $product->name,
                    '{product_link}' => $productLink,
                    '{object}' => EtsRVCore::trans($object['og'], $languageObj->iso_code, 'EtsRVReplyCommentEntity'),
                ];
                EtsRVMail::send(
                    $idLang
                    , $template
                    , null
                    , $templateVars
                    , $person->email
                    , $person_name
                    , true
                    , $person->id
                    , $comment->employee
                    , $product->id
                    , $this->context->shop->id
                );
            }
        } elseif (
            $replyComment->validate == 0
            && $this->employee == 0
            && EtsRVEmailTemplate::isEnabled('toadmin_awaiting')
            && ($staffs = array_merge(EtsRVStaff::getAll($this->context, 1), EtsRVProductCommentCustomer::getAll($this->context)))
        ) {
            $object = [
                'og' => $replyComment->question ? 'a comment' : 'a reply',
                't' => $replyComment->question ? $this->l('a comment', 'EtsRVReplyCommentEntity') : $this->l('a reply', 'EtsRVReplyCommentEntity')
            ];
            foreach ($staffs as $s) {
                $languageObj = new Language(isset($s['id_lang']) ? $s['id_lang'] : 0);
                $idLang = $languageObj->id ?: $this->context->language->id;
                if ($idProduct < 1 || !Validate::isUnsignedInt($idProduct)) {
                    $commentData = EtsRVReplyComment::getData($replyComment->id, $idLang);
                    $idProduct = !empty($commentData['id_product']) ? (int)$commentData['id_product'] : 0;
                }
                $templateVars = [
                    '{admin_name}' => $s['name'],
                    '{object}' => EtsRVCore::trans($object['og'], $languageObj->iso_code, 'EtsRVReplyCommentEntity'),
                    '{customer_name}' => $from_person_name,
                    '{content}' => isset($replyComment->content[$idLang]) ? $replyComment->content[$idLang] : EtsRVReplyComment::getOriginContentById($replyComment->id),
                    '{product}' => '',
                ];
                EtsRVMail::send(
                    $idLang
                    , 'toadmin_awaiting'
                    , null
                    , $templateVars
                    , $s['email']
                    , $s['name']
                    , true
                    , $customer->id
                    , $s['id']
                    , $idProduct
                    , $this->context->shop->id
                );
            }
        }
    }

    public function replyCommentMailToCustomer($id, $id_customer, $template, $id_employee = 0)
    {
        if (!$id ||
            !Validate::isUnsignedInt($id) ||
            !$id_customer && !$id_employee ||
            $id_customer && !Validate::isUnsignedInt($id_customer) ||
            $id_employee && !Validate::isUnsignedInt($id_employee) ||
            !$template
        ) {
            return false;
        }
        if (($reply = EtsRVReplyComment::getData($id, $this->context->language->id)) &&
            (
                !empty($reply['id_customer']) && (
                    $id_employee > 0 ||
                    (int)$reply['id_customer'] !== (int)$id_customer
                ) ||
                !empty($reply['employee']) && (
                    $id_customer > 0 ||
                    (int)$reply['employee'] !== (int)$id_customer
                )
            )
        ) {

            $from_person = $id_employee > 0 ? new Employee($id_employee) : new Customer($id_customer);
            $from_person_name = $from_person->firstname . ' ' . $from_person->lastname;

            $person = !empty($reply['id_customer']) ? new Customer((int)$reply['id_customer']) : new Employee((int)$reply['employee']);
            $person_name = $person->firstname . ' ' . $person->lastname;

            $languageObj = new Language($person->id_lang);
            $idLang = $languageObj->id ?: $this->context->language->id;
            $product = new Product((int)$reply['id_product'], false, $idLang);

            $object = [
                'og' => $this->qa ? 'comment' : 'reply',
                't' => $this->qa ? $this->l('comment', 'EtsRVReplyCommentEntity') : $this->l('reply', 'EtsRVReplyCommentEntity'),
            ];
            $templateVars = [
                '{from_person}' => $from_person,
                '{from_person_name}' => $from_person_name,
                '{product_link}' => $this->context->link->getProductLink($product, $product->link_rewrite, $product->category, $product->ean13, $idLang),
                '{product_name}' => $product->name,
                '{content}' => isset($reply['content']) ? trim($reply['content']) : '',
                '{object}' => EtsRVCore::trans($object['og'], $languageObj->iso_code, 'EtsRVReplyCommentEntity')
            ];
            return EtsRVMail::send(
                $idLang
                , $template
                , null
                , $templateVars
                , $person->email
                , $person_name
                , true
                , $person->id
                , (!empty($reply['employee']) ? (int)$reply['employee'] : 0)
                , $product->id
                , $this->context->shop->id
            );
        }

        return 0;
    }

    public function replyCommentMailApproved($reply, $manual = false)
    {
        if (!$reply instanceof EtsRVReplyComment && Validate::isUnsignedInt($reply))
            $reply = new EtsRVReplyComment($reply);

        $this->replyCommentMailToPosted($reply);

        if ($reply->id > 0 &&
            (
                (!(int)Configuration::get('ETS_RV_AUTO_APPROVE') || $manual) && EtsRVEmailTemplate::isEnabled('tocustomer_approved') ||
                (!(int)Configuration::get('ETS_RV_QA_AUTO_APPROVE') || $manual) && EtsRVEmailTemplate::isEnabled('tocustomer_approved')
            )
        ) {
            if (!$reply instanceof EtsRVReplyComment && Validate::isUnsignedInt($reply))
                $reply = new EtsRVReplyComment((int)$reply);

            if ($reply->id > 0 && $reply->validate == 1) {

                $customer = new Customer($reply->id_customer);
                $customer_name = $customer->firstname . ' ' . $customer->lastname;

                $languageObj = new Language($customer->id_lang);
                $idLang = $languageObj->id ?: $this->context->language->id;
                $commentData = EtsRVReplyComment::getData($reply->id, $idLang);

                $product = new Product(!empty($commentData['id_product']) ? (int)$commentData['id_product'] : 0, false, $idLang);
                $object = [
                    'og' => $reply->question ? 'comment' : 'reply',
                    't' => $reply->question ? $this->l('comment', 'EtsRVReplyCommentEntity') : $this->l('reply', 'EtsRVReplyCommentEntity')
                ];
                $templateVars = [
                    '{customer_name}' => $customer_name,
                    '{product_link}' => $this->context->link->getProductLink($product, $product->link_rewrite, $product->category, $product->ean13, $idLang),
                    '{product_name}' => $product->name,
                    '{object}' => EtsRVCore::trans($object['og'], $languageObj->iso_code, 'EtsRVReplyCommentEntity'),
                    '{content}' => isset($commentData['content']) ? trim($commentData['content']) : '',
                ];
                return EtsRVMail::send(
                    $idLang
                    , 'tocustomer_approved'
                    , null
                    , $templateVars
                    , $customer->email
                    , $customer_name
                    , true
                    , $customer->id
                    , $reply->employee
                    , $product->id
                    , $this->context->shop->id
                );
            }
        }

        return 0;
    }

    /**** end sendmail ****/

    public function updateReplyCommentUsefulness()
    {
        $this->viewAccess();

        if (!Configuration::get('ETS_RV_' . $this->sf . 'USEFULNESS')) {
            $this->ajaxRender(json_encode([
                'success' => false,
                'error' => $this->l('This feature is not enabled.', 'EtsRVReplyCommentEntity'),
            ]));
        }

        $customerId = (int)$this->context->cookie->id_customer;
        $idProduct = (int)Tools::getValue('id_product');

        if ($this->employee <= 0 && $customerId <= 0) {
            $this->ajaxRender(json_encode([
                'success' => false,
                'error' => sprintf(
                    $this->l('You need to be %s or %s to give your appreciation of %s.', 'EtsRVReplyCommentEntity')
                    , EtsRVTools::displayText($this->l('signed in ', 'EtsRVReplyCommentEntity'), 'a', ['href' => $this->context->link->getPageLink('authentication') . ($idProduct ? '?back=' . $this->context->link->getProductLink($idProduct) : ''), 'rel' => 'nofollow'])
                    , EtsRVTools::displayText($this->l('create an account', 'EtsRVReplyCommentEntity'), 'a', ['href' => $this->context->link->getPageLink('authentication&create_account=1') . ($idProduct ? '&back=' . $this->context->link->getProductLink($idProduct) : ''), 'rel' => 'nofollow'])
                    , $this->qa ? $this->l('a comment', 'EtsRVReplyCommentEntity') : $this->l('a reply', 'EtsRVReplyCommentEntity')
                ),
            ]));
        }

        $id_reply_comment = (int)Tools::getValue('id_ets_rv_reply_comment');
        $usefulness = (int)Tools::getValue('usefulness');

        $id_reply_comment = EtsRVReplyComment::findOneById($id_reply_comment);
        if (!$id_reply_comment) {
            $this->ajaxRender(json_encode([
                'success' => false,
                'error' => $this->l('Cannot find the requested product review.', 'EtsRVReplyCommentEntity'),
            ]));
        }

        $result = EtsRVReplyComment::setCommentUsefulness(
            $id_reply_comment
            , (bool)$usefulness
            , $customerId
            , $this->employee
            , $this->qa
        );

        if ($result) {
            if ($this->qa) {
                $content = $usefulness ? 'like_a_comment_answer' : 'dislike_a_comment_answer';
            } else {
                $content = $usefulness ? 'like_a_reply' : 'dislike_a_reply';
            }
            $this->addActivity(EtsRVReplyComment::getInstanceById($id_reply_comment), $this->qa ? EtsRVActivity::ETS_RV_TYPE_COMMENT_ANSWER : EtsRVActivity::ETS_RV_TYPE_REPLY_COMMENT, $usefulness ? EtsRVActivity::ETS_RV_ACTION_LIKE : EtsRVActivity::ETS_RV_ACTION_DISLIKE, $idProduct, $content, $this->context);
        }

        $commentUsefulness = EtsRVReplyCommentRepository::getInstance()->getReplyCommentUsefulness($id_reply_comment, $this->qa, isset($this->context->customer->id) ? $this->context->customer->id : 0, $this->employee);
        $template = 'tocustomer_' . ($usefulness ? '' : 'dis') . 'like';
        if ($result && EtsRVEmailTemplate::isEnabled($template)) {
            $this->replyCommentMailToCustomer($id_reply_comment
                , $customerId
                , $template
                , $this->employee
            );
        }
        // End:

        $this->ajaxRender(json_encode(array_merge([
            'success' => true,
            'id_ets_rv_reply_comment' => $id_reply_comment,
        ], $commentUsefulness)));
    }

    public function deleteReplyComment()
    {
        $id = (int)Tools::getValue('id_reply_comment');
        $this->viewAccess($id ? 'delete' : null);

        $customerId = (int)$this->context->cookie->id_customer;
        if (!$customerId && !$this->backOffice) {
            $idProduct = (int)Tools::getValue('id_product');
            $this->ajaxRender(json_encode([
                'success' => false,
                'error' => sprintf(
                    $this->l('You need to be %s or %s to give your appreciation.', 'EtsRVReplyCommentEntity')
                    , EtsRVTools::displayText($this->l('signed in', 'EtsRVReplyCommentEntity'), 'a', ['href' => $this->context->link->getPageLink('authentication') . ($idProduct ? '?back=' . $this->context->link->getProductLink($idProduct) : ''), 'rel' => 'nofollow'])
                    , EtsRVTools::displayText($this->l('create an account', 'EtsRVReplyCommentEntity'), 'a', ['href' => $this->context->link->getPageLink('authentication&create_account=1') . ($idProduct ? '&back=' . $this->context->link->getProductLink($idProduct) : ''), 'rel' => 'nofollow'])
                ),
            ]));
        }
        $replyComment = new EtsRVReplyComment($id);
        if (!$this->backOffice && $replyComment->id &&
            (
                $replyComment->id_customer && $customerId != $replyComment->id_customer ||
                (int)$replyComment->validate == 1 && !Configuration::get('ETS_RV' . ($replyComment->question ? '_QA' : '') . '_CUSTOMER_DELETE_APPROVED')
            )
        ) {
            $this->_errors[] = $this->l('Cannot delete the reply comment', 'EtsRVReplyCommentEntity');
        } elseif (!$replyComment->delete()) {
            $this->_errors[] = $this->l('Delete failed.', 'EtsRVReplyCommentEntity');
        }
        $hasError = (bool)count($this->_errors);
        $response = [
            'success' => !$hasError,
            'error' => implode(Tools::nl2br("\n"), $this->_errors),
            'msg' => !$hasError ? sprintf($this->l('Reply #%1s has been deleted', 'EtsRVReplyCommentEntity'), $replyComment->id) : ''
        ];
        $this->ajaxRender(json_encode($this->jsonExtra($response)));
    }

    public function privateReplyComment()
    {
        $this->viewAccess();

        $customerId = (int)$this->context->cookie->id_customer;
        if (!$customerId && !$this->backOffice) {
            $idProduct = (int)Tools::getValue('id_product');
            $this->ajaxRender(json_encode([
                'success' => false,
                'error' => sprintf(
                    $this->l('You need to be %s or %s to set your reply as private.', 'EtsRVReplyCommentEntity')
                    , EtsRVTools::displayText($this->l('signed in', 'EtsRVReplyCommentEntity'), 'a', ['href' => $this->context->link->getPageLink('authentication') . ($idProduct ? '?back=' . $this->context->link->getProductLink($idProduct) : ''), 'rel' => 'nofollow'])
                    , EtsRVTools::displayText($this->l('create an account', 'EtsRVReplyCommentEntity'), 'a', ['href' => $this->context->link->getPageLink('authentication&create_account=1') . ($idProduct ? '&back=' . $this->context->link->getProductLink($idProduct) : ''), 'rel' => 'nofollow'])
                ),
            ]));
        }
        $replyComment = new EtsRVReplyComment((int)Tools::getValue('id_reply_comment'));
        if (!$this->backOffice && $customerId != $replyComment->id_customer) {
            $this->_errors[] = $this->qa ? $this->l('Cannot set this reply to private', 'EtsRVReplyCommentEntity') : $this->l('Cannot set this reply to private', 'EtsRVReplyCommentEntity');
        } else {
            $replyComment->validate = EtsRVReplyComment::STATUS_PRIVATE;
            if (!$replyComment->update()) {
                $this->_errors[] = $this->l('Set to private failed.', 'EtsRVReplyCommentEntity');
            }
        }
        $hasError = (bool)count($this->_errors);
        $response = [
            'success' => !$hasError,
            'error' => implode(Tools::nl2br("\n"), $this->_errors),
            'msg' => !$hasError ? sprintf($this->l('The reply #%1s has been set to private', 'EtsRVReplyCommentEntity'), $replyComment->id) : ''
        ];
        $this->ajaxRender(json_encode($this->jsonExtra($response)));
    }

    public function approveReplyComment()
    {
        $this->viewAccess();

        $customerId = (int)$this->context->cookie->id_customer;
        if (!$customerId && !$this->backOffice) {
            $idProduct = (int)Tools::getValue('id_product');
            $this->ajaxRender(json_encode([
                'success' => false,
                'error' => sprintf(
                    $this->l('You need to be %s or %s to give your appreciation.', 'EtsRVReplyCommentEntity')
                    , EtsRVTools::displayText($this->l('signed in', 'EtsRVReplyCommentEntity'), 'a', ['href' => $this->context->link->getPageLink('authentication') . ($idProduct ? '?back=' . $this->context->link->getProductLink($idProduct) : ''), 'rel' => 'nofollow'])
                    , EtsRVTools::displayText($this->l('create an account', 'EtsRVReplyCommentEntity'), 'a', ['href' => $this->context->link->getPageLink('authentication&create_account=1') . ($idProduct ? '&back=' . $this->context->link->getProductLink($idProduct) : ''), 'rel' => 'nofollow'])
                ),
            ]));
        }
        $replyComment = new EtsRVReplyComment((int)Tools::getValue('id_reply_comment'));
        if (!$this->backOffice && $replyComment->id_customer && $customerId != $replyComment->id_customer) {
            $this->_errors[] = $this->qa ? $this->l('Cannot approve comment', 'EtsRVReplyCommentEntity') : $this->l('Cannot approve the reply comment', 'EtsRVReplyCommentEntity');
        } else {
            $replyComment->validate = EtsRVReplyComment::STATUS_APPROVE;
            if (!$replyComment->update()) {
                $this->_errors[] = $this->l('Approve failed.', 'EtsRVReplyCommentEntity');
            } else
                $this->replyCommentMailApproved($replyComment);
        }
        $hasError = (bool)count($this->_errors);
        $response = [
            'success' => !$hasError,
            'error' => implode(Tools::nl2br("\n"), $this->_errors),
            'msg' => !$hasError ? sprintf($this->l('Reply #%1s has been approved', 'EtsRVReplyCommentEntity'), $replyComment->id) : ''
        ];
        $this->ajaxRender(json_encode($this->jsonExtra($response)));
    }

    public function updateDateReplyComment()
    {
        $reply = new EtsRVReplyComment((int)Tools::getValue('id_reply_comment'));
        if (!$this->backOffice || !(int)Configuration::get('ETS_RV_' . (!$this->qa ? 'REVIEW' : 'QUESTION') . '_ENABLED')) {
            $this->_errors[] = $this->l('Permission denied.', 'EtsRVReplyCommentEntity');
        } else {
            if (!($date_add = trim(Tools::getValue('date_add')))) {
                $this->_errors[] = $this->l('"Date add" value is required.', 'EtsRVReplyCommentEntity');
            } elseif (!Validate::isDate($date_add) || strtotime($date_add) > time()) {
                $this->_errors[] = $this->l('"Date add" value is invalid.', 'EtsRVReplyCommentEntity');
            } else {
                $reply->date_add = date('Y-m-d H:i:s', strtotime($date_add));
                if (!$reply->update()) {
                    $this->_errors[] = $this->l('Update failed.', 'EtsRVReplyCommentEntity');
                }
            }
        }
        $hasError = (bool)count($this->_errors);
        $response = [
            'success' => !$hasError,
            'errors' => Tools::nl2br(implode(PHP_EOL, $this->_errors)),
            'msg' => !$hasError ? sprintf($this->l('The adding time of comment #%1s is updated successfully', 'EtsRVReplyCommentEntity'), $reply->id) : '',
            'date_add' => !$hasError ? $this->timeElapsedString($reply->date_add) : '',
        ];
        $this->ajaxRender(json_encode($this->jsonExtra($response)));
    }
}