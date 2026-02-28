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

class EtsRVCommentEntity extends EtsRVEntity
{
    public static function getInstance()
    {
        if (empty(self::$_INSTANCE['entity_comment']) || !self::$_INSTANCE['entity_comment'] instanceof EtsRVCommentEntity) {
            self::$_INSTANCE['entity_comment'] = new EtsRVCommentEntity();
        }

        return self::$_INSTANCE['entity_comment'];
    }

    public function getComment()
    {
        $comment = new EtsRVComment((int)Tools::getValue('id_comment'));
        $comment->origin_content = EtsRVComment::getOriginContentById($comment->id);

        $this->ajaxRender(json_encode($comment));
    }

    public function getComments()
    {
        $idProduct = (int)Tools::getValue('id_product');
        $productCommentId = (int)Tools::getValue('id_product_comment');

        // current page:
        $page = (int)Tools::getValue('page');
        if ($page == '' || !Validate::isUnsignedInt($page) || $page < 1) {
            $page = 1;
        }

        // item start:
        $begin = Tools::getValue('begin');
        if ($begin == '' || !Validate::isUnsignedInt($begin) || (int)$begin < 0) {
            $begin = 0;
        }

        // per page:
        $comments_per_page = Tools::getValue('comments_per_page');
        $config_comments_per_page = (int)Configuration::get('ETS_RV_' . $this->sf . 'COMMENTS_PER_PAGE');
        if ($comments_per_page == '' || !Validate::isUnsignedInt($comments_per_page) || $comments_per_page < 1) {
            $comments_per_page = $config_comments_per_page;
        }
        if ($this->qa)
            $default_sort_by = trim(Configuration::get('ETS_RV_QA_DEFAULT_SORT_BY')) ?: false;
        else
            $default_sort_by = trim(Configuration::get('ETS_RV_DEFAULT_SORT_BY')) ?: false;
        $sortBy = trim(Tools::getValue('sort_by')) ?: $default_sort_by;
        $firstOnly = (int)Tools::getValue('first') ? 1 : 0;

        // initial item:
        $comments_initial = (int)Configuration::get('ETS_RV_' . $this->sf . 'COMMENTS_INITIAL');
        if ($comments_initial < 1) {
            $comments_initial = 1;
        }

        // is answer?
        $answer = (int)Tools::getValue('answer') ? 1 : 0;

        // forward:
        $rest = (int)Tools::getValue('rest');

        $commentRepository = EtsRVCommentRepository::getInstance();
        $replyCommentRepository = EtsRVReplyCommentRepository::getInstance();

        // comment status: validate, pending or private:
        $validateOnly = ($this->backOffice || (int)Configuration::get('ETS_RV_' . $this->sf . 'AUTO_APPROVE')) ? null : 1;

        $commentsNb = $rest > 0 ? $rest : $commentRepository->getCommentsNumber(
            $productCommentId,
            $this->context->language->id,
            $validateOnly,
            $this->backOffice,
            $this->context,
            $this->qa,
            $answer
        );

        if ($begin > $commentsNb) {
            $this->ajaxRender(json_encode([
                'success' => false,
                'errors' => $this->l('Comment is empty', 'EtsRVCommentEntity'),
            ]));
        }

        $comments = $commentRepository->paginate(
            $productCommentId
            , $this->context->language->id
            , $page
            , ($begin > 0 || $rest > 0 ? $comments_per_page : $comments_initial)
            , $validateOnly
            , $this->backOffice
            , $firstOnly
            , $begin
            , $sortBy
            , $this->context
            , $this->qa
            , $answer
        );

        // calc start item more:
        if ($rest > 0) {
            $comments = EtsRVTools::quickSort($comments, 'id_ets_rv_comment');
            $begin -= $comments_per_page;
            $rest -= $comments_per_page;
            $commentsNb = $rest;
            if ($begin > 0) {
                $comments_per_page = $begin >= $rest ? 0 : ($rest - $begin >= $comments_per_page ? $comments_per_page : ($rest - $begin));
            } else {
                $begin = 0;
                $comments_per_page = $rest > $comments_per_page ? $comments_per_page : $rest;
            }
        } else {
            if ($begin > 0) {
                $begin += $comments_per_page;
                $comments_per_page = $begin >= $commentsNb ? 0 : ($commentsNb - $begin >= $comments_per_page ? $comments_per_page : ($commentsNb - $begin));
            } else {
                $begin += $comments_initial;
            }
        }
        $responseArray = [
            'success' => true,
            'begin' => $begin,
            'nb' => $commentsNb,
            'comments_per_page' => $comments_per_page,
            'comments' => [],
        ];
        if ($comments) {
            $replies_initial = (int)Configuration::get('ETS_RV_' . $this->sf . 'REPLIES_INITIAL');
            if ($replies_initial <= 0) {
                $replies_initial = 1;
            }
            foreach ($comments as &$comment) {
                $this->formatItem($idProduct, $comment);
                // Like or Dislike:
                $usefulness = $commentRepository->getCommentUsefulness($comment['id_ets_rv_comment'], $this->qa, isset($this->context->customer->id) ? $this->context->customer->id : 0, $this->employee);
                $comment = array_merge($comment, $usefulness);
                if ($this->qa && $answer || !$this->qa) {

                    // Reply comments of number:
                    $comment['replies_nb'] = $replyCommentRepository->getReplyCommentsNumber(
                        (int)$comment['id_ets_rv_comment']
                        , $this->context->language->id
                        , $validateOnly
                        , $this->backOffice
                        , $this->context
                        , $this->qa
                    );

                    // Reply comments pagination:
                    $replyComments = $replyCommentRepository->paginate(
                        (int)$comment['id_ets_rv_comment']
                        , $this->context->language->id
                        , $page
                        , $replies_initial
                        , $validateOnly
                        , $this->backOffice
                        , false
                        , 0
                        , false
                        , $this->context
                        , $this->qa
                    );

                    // Format reply items:
                    if ($replyComments) {
                        foreach ($replyComments as &$replyComment) {
                            $this->formatItem($idProduct, $replyComment);
                            $usefulness = $replyCommentRepository->getReplyCommentUsefulness($replyComment['id_ets_rv_reply_comment'], $this->qa, isset($this->context->customer->id) ? $this->context->customer->id : 0, $this->employee);
                            $replyComment = array_merge($replyComment, $usefulness);
                        }
                    }
                    $comment['replies'] = $replyComments;
                }
            }
        }
        $responseArray['comments'] = $comments;

        $this->ajaxRender(json_encode($responseArray));
    }

    public function hasPrivateComment($productCommentId)
    {
        if ($this->backOffice)
            return true;
        $productComment = new EtsRVProductComment($productCommentId);
        return
            (int)$productComment->validate == 1 ||
            (int)$productComment->id_customer > 0 && isset($this->context->customer->id) && (int)$this->context->customer->id == (int)$productComment->id_customer ||
            (int)$productComment->id_guest && isset($this->context->cookie->id_guest) && (int)$this->context->cookie->id_guest == (int)$productComment->id_guest;
    }

    public function postComment()
    {
        $commentId = (int)Tools::getValue('id_comment');
        $this->viewAccess($commentId ? 'edit' : null);
        if (!$this->commentAllowed(isset($this->context->cookie->id_customer) ? (int)$this->context->cookie->id_customer : 0, $this->qa)) {
            $this->ajaxRender(json_encode([
                'success' => false,
                'error' => $this->l('Permission denied.', 'EtsRVCommentEntity'),
            ]));
        }
        $idProduct = (int)Tools::getValue('id_product');
        if (!$this->backOffice && (!isset($this->context->cookie->id_customer) || !(int)$this->context->cookie->id_customer)) {
            $this->ajaxRender(json_encode([
                'success' => false,
                'error' => sprintf(
                    $this->l('You need to be %s or %s to post your comment.', 'EtsRVCommentEntity')
                    , EtsRVTools::displayText($this->l('signed in', 'EtsRVCommentEntity'), 'a', ['href' => $this->context->link->getPageLink('authentication') . ($idProduct ? '?back=' . $this->context->link->getProductLink($idProduct) : ''), 'rel' => 'nofollow'])
                    , EtsRVTools::displayText($this->l('create an account', 'EtsRVCommentEntity'), 'a', ['href' => $this->context->link->getPageLink('authentication&create_account=1') . ($idProduct ? '&back=' . $this->context->link->getProductLink($idProduct) : ''), 'rel' => 'nofollow'])
                ),
            ]));
        }

        $comment = new EtsRVComment($commentId);
        if (!$this->backOffice && $comment->id &&
            (
                $comment->id_customer && (int)$comment->id_customer !== (int)$this->context->cookie->id_customer ||
                (int)$comment->validate == 1 && !Configuration::get('ETS_RV' . ($comment->question ? '_QA' : '') . '_CUSTOMER_EDIT_APPROVED')
            )
        ) {
            $this->ajaxRender(json_encode([
                'success' => false,
                'error' => $this->l('You cannot edit comment', 'EtsRVCommentEntity'),
            ]));
        }
        if (Configuration::get('ETS_RV_RECAPTCHA_ENABLED') && !$this->verifyReCAPTCHA($this->_errors)) {
            $this->ajaxRender(json_encode([
                'success' => false,
                'error' => Tools::nl2br(implode(PHP_EOL, $this->_errors)),
            ]));
        }

        $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
        $language = new Language($id_lang_default);
        $comment_content = trim(Tools::getValue('comment_content', Tools::getValue('comment_content_' . $id_lang_default)));
        $date_add = trim(Tools::getValue('date_add'));

        if (!$productCommentId = (int)Tools::getValue('id_product_comment')) {
            $this->_errors[] = $this->l('Topic cannot be empty', 'EtsRVCommentEntity');
        } elseif (!$this->hasPrivateComment($productCommentId)) {
            $this->_errors[] = $this->l('Permission denied.', 'EtsRVCommentEntity');
        } else {
            $maximum_character = Configuration::get('ETS_RV_' . ($this->qa ? 'QA_' : '') . 'MAX_LENGTH');
            if (trim($maximum_character) === '')
                $maximum_character = EtsRVModel::NAME_MAX_LENGTH;
            $minimum_character = Configuration::get('ETS_RV_' . ($this->qa ? 'QA_' : '') . 'MIN_LENGTH');
            if (trim($minimum_character) === '')
                $minimum_character = EtsRVModel::NAME_MIN_LENGTH;

            if ($comment_content == '') {
                $this->_errors[] = $this->l('Comment cannot be empty', 'EtsRVCommentEntity');
            } elseif (!Validate::isCleanHtml($comment_content) || preg_match('/[{}]/i', $comment_content) || !preg_match('/^(?!.*<[^bi\/>]+>).*$/s', $comment_content)) {
                $this->_errors[] = sprintf($this->l('Comment %s is invalid', 'EtsRVCommentEntity'), ($this->backOffice ? ' ' . sprintf($this->l('language (%s)', 'EtsRVCommentEntity'), $language->iso_code) : ''));
            } elseif ((int)$maximum_character > 0 && Tools::strlen($comment_content) > (int)$maximum_character) {
                $this->_errors[] = sprintf($this->l('Comment %s cannot be longer than %s characters', 'EtsRVCommentEntity'), ($this->backOffice ? ' ' . sprintf($this->l('language (%s)', 'EtsRVCommentEntity'), $language->iso_code) : ''), $maximum_character);
            } elseif ((int)$minimum_character > 0 && Tools::strlen($comment_content) < (int)$minimum_character) {
                $this->_errors[] = sprintf($this->l('Content %s cannot be shorter than %s characters', 'EtsRVProductCommentEntity'), ($this->backOffice ? ' ' . sprintf($this->l('language (%s)', 'EtsRVCommentEntity'), $language->iso_code) : ''), $minimum_character);
            } elseif (Tools::getIsset('date_add') && $date_add == '')
                $this->_errors[] = $this->l('Date add is required', 'EtsRVCommentEntity');
            elseif ($date_add !== '' && !Validate::isDate($date_add))
                $this->_errors[] = $this->l('Date add is invalid', 'EtsRVCommentEntity');
            if ($this->backOffice) {
                foreach (Language::getLanguages(false) as $l) {
                    if ((int)$l['id_lang'] !== $id_lang_default && ($comment_content_lang = trim(Tools::getValue('comment_content', Tools::getValue('comment_content_' . $l['id_lang'])))) !== '') {
                        if (!Validate::isCleanHtml($comment_content_lang) || preg_match('/[{}]/i', $comment_content_lang) || !preg_match('/^(?!.*<[^bi\/>]+>).*$/s', $comment_content_lang)) {
                            $this->_errors[] = sprintf($this->l('Comment in language (%s) is invalid', 'EtsRVCommentEntity'), $l['iso_code']);
                        } elseif ((int)$maximum_character > 0 && Tools::strlen($comment_content_lang) > (int)$maximum_character) {
                            $this->_errors[] = sprintf($this->l('Comment in language (%s) cannot be longer than %s characters', 'EtsRVCommentEntity'), $l['iso_code'], $maximum_character);
                        } elseif ((int)$minimum_character > 0 && Tools::strlen($comment_content_lang) < (int)$minimum_character) {
                            $this->_errors[] = sprintf($this->l('Content in language (%s) cannot be shorter than %s characters', 'EtsRVCommentEntity'), $l['iso_code'], $minimum_character);
                        }
                    }
                }
            }
        }
        if (count($this->_errors) > 0) {
            $this->ajaxRender(json_encode([
                'success' => false,
                'error' => implode(Tools::nl2br("\n"), $this->_errors),
            ]));
        }

        if (!$comment->id) {
            $comment->id_ets_rv_product_comment = $productCommentId;
            $comment->id_customer = !$this->employee ? (int)$this->context->cookie->id_customer : 0;
            $comment->employee = $this->employee;
            $comment->date_add = date('Y-m-d H:i:s');
            $comment->question = $this->qa;
            $comment->answer = (int)Tools::getValue('answer') ? 1 : 0;
            $validateOnly = (int)Configuration::get('ETS_RV_' . $this->sf . 'AUTO_APPROVE') ? 1 : 0;
            $comment->validate = (
                $this->backOffice ||
                $validateOnly ||
                (
                    !$this->qa
                    && (int)Configuration::get('ETS_RV_AUTO_APPROVE_PURCHASED')
                    && (int)$this->context->cookie->id_customer
                    && EtsRVProductComment::verifyPurchase((int)$this->context->cookie->id_customer, $idProduct)
                )
            ) ? EtsRVComment::STATUS_APPROVE : EtsRVComment::STATUS_PENDING;
        } else {
            $comment->upd_date = date('Y-m-d H:i:s');
            if ($date_add !== '')
                $comment->date_add = $date_add;
        }
        $languages = Language::getLanguages(false);
        if ($languages) {
            $multiLang = (int)Configuration::get('ETS_RV_MULTILANG_ENABLED');
            foreach ($languages as $l) {
                $comment->content[(int)$l['id_lang']] = $multiLang ? (Tools::getValue('comment_content_' . (int)$l['id_lang']) ?: $comment_content) : null;
            }
            if (($errors = $comment->validateFields(false, true)) === true && $comment->save()) {
                // Add activity:
                if (!$commentId) {
                    if ($this->qa) {
                        $content = $comment->answer ? 'answered_to_a_question' : 'commented_on_a_question';
                    } else {
                        $content = 'commented_on_a_review';
                    }
                    $this->addActivity($comment, $this->qa ? ($comment->answer ? EtsRVActivity::ETS_RV_TYPE_ANSWER_QUESTION : EtsRVActivity::ETS_RV_TYPE_COMMENT_QUESTION) : EtsRVActivity::ETS_RV_TYPE_COMMENT, $comment->answer ? EtsRVActivity::ETS_RV_ACTION_ANSWER : EtsRVActivity::ETS_RV_ACTION_COMMENT, $idProduct, $content, $this->context);
                }
                EtsRVComment::saveOriginLang($comment->id, $this->context->language->id, $comment_content);
            } elseif (is_array($errors))
                $this->_errors = $errors;
        }
        $customer = null;
        if (!$this->_errors) {
            $customer = $comment->employee > 0 ? new Employee($comment->employee) : new Customer((int)$comment->id_customer);
            if (!$commentId) {
                $this->commentMailToPosted($comment, $idProduct, $customer);
            }
        }
        $json = [];
        if (!$this->_errors) {
            $commentArray = $comment->toArray();
            $id_address = $comment->id_customer > 0 ? Address::getFirstCustomerAddressId($comment->id_customer) : 0;
            if ($id_address > 0) {
                $address = new Address($id_address);
                $country = new Country($address->id_country, $this->context->language->id);
            } else
                $country = null;
            $this->formatItem($idProduct
                , $commentArray
                , $customer
                , $comment_content
                , $country
            );
            $commentArray['replies_nb'] = 0;
            $commentArray['replies'] = array();
            $json['comment'] = $commentArray;
        }
        $hasError = count($this->_errors) > 0;
        $json += [
            'success' => !$hasError,
            'error' => $hasError ? Tools::nl2br(implode(PHP_EOL, $this->_errors)) : false
        ];
        $this->ajaxRender(json_encode($this->jsonExtra($json)));
    }

    /**** sendmail ****/

    public function commentMailToPosted($comment, $idProduct = 0, $customer = null)
    {
        if (!$comment instanceof EtsRVComment && Validate::isUnsignedInt($comment))
            $comment = new EtsRVComment($comment);

        if ($customer == null)
            $customer = $comment->employee > 0 ? new Employee((int)$comment->employee) : new Customer((int)$comment->id_customer);

        $from_person_name = $customer->firstname . ' ' . $customer->lastname;
        $template = $comment->question && $comment->answer ? 'person_answer' : 'person_commented';

        if ($comment->validate && EtsRVEmailTemplate::isEnabled($template)) {
            $object = [
                'og' => $comment->question && $comment->answer ? 'an answer' : 'review',
                't' => $comment->question && $comment->answer ? $this->l('an answer', 'EtsRVCommentEntity') : $this->l('review', 'EtsRVCommentEntity')
            ];
            if ($comment->question) {
                $productComment = new EtsRVProductComment($comment->id_ets_rv_product_comment);
                $originLang = EtsRVProductComment::getOriginLang($comment->id_ets_rv_product_comment);
            }
            if (!isset($productComment))
                $productComment = new EtsRVProductComment($comment->id_ets_rv_product_comment);

            if ($productComment->id_customer > 0 && (int)$productComment->id_customer !== (int)$comment->id_customer || trim($productComment->email) !== '' || $comment->employee > 0) {

                if ($productComment->id_customer > 0) {
                    $person = new Customer($productComment->id_customer);
                    $person_name = $person->firstname . ' ' . $person->lastname;
                    $person_email = $person->email;
                    $languageObj = new Language($person->id_lang);
                } else {
                    $person_name = $productComment->customer_name;
                    $person_email = $productComment->email;
                    if (isset($originLang['id_lang']) && (int)$originLang['id_lang'] > 0)
                        $idLang = $originLang['id_lang'];
                    else
                        $idLang = EtsRVProductComment::getOriginIdLang($productComment->id);
                    $languageObj = new Language($idLang);
                }

                $idLang = $languageObj->id ?: $this->context->language->id;
                if ($idProduct < 1 || !Validate::isUnsignedInt($idProduct)) {
                    $commentData = EtsRVComment::getData($comment->id, $idLang);
                    $idProduct = !empty($commentData['id_product']) ? (int)$commentData['id_product'] : 0;
                }
                $product = new Product($idProduct, false, $idLang);
                $productLink = $this->context->link->getProductLink($product, $product->link_rewrite, $product->category, $product->ean13, $idLang);
                $templateVars = [
                    '{object_content}' => isset($productComment->title[$idLang]) && $productComment->title[$idLang] ? $productComment->title[$idLang] : (isset($originLang) && isset($originLang['title']) ? $originLang['title'] : ''),
                    '{content}' => !empty($comment->content[$idLang]) ? $comment->content[$idLang] : EtsRVComment::getOriginContentById($comment->id),
                    '{person_name}' => $person_name,
                    '{from_person_name}' => $from_person_name,
                    '{product_link}' => $productLink,
                    '{product_name}' => $product->name,
                    '{object}' => EtsRVCore::trans($object['og'], $languageObj->iso_code, 'EtsRVCommentEntity'),
                ];
                if ($comment->question)
                    $templateVars['{question_content}'] = $templateVars['{object_content}'];
                EtsRVMail::send(
                    $idLang
                    , $template
                    , null
                    , $templateVars
                    , $person_email
                    , $person_name
                    , true
                    , isset($person) ? $person->id : 0
                    , 0
                    , $product->id
                    , $this->context->shop->id
                );
            }
        } elseif ($comment->validate == 0
            && $this->employee == 0
            && EtsRVEmailTemplate::isEnabled('toadmin_awaiting')
            && ($staffs = array_merge(EtsRVStaff::getAll($this->context, 1), EtsRVProductCommentCustomer::getAll($this->context)))
        ) {
            $object = [
                'og' => $comment->question && $comment->answer ? 'an answer' : 'a comment',
                't' => $comment->question && $comment->answer ? $this->l('an answer', 'EtsRVCommentEntity') : $this->l('a comment', 'EtsRVCommentEntity')
            ];
            foreach ($staffs as $s) {
                $languageObj = new Language(isset($s['id_lang']) ? $s['id_lang'] : 0);
                $idLang = $languageObj->id ?: $this->context->language->id;
                if ($idProduct < 1 || !Validate::isUnsignedInt($idProduct)) {
                    $commentData = EtsRVComment::getData($comment->id, $idLang);
                    $idProduct = !empty($commentData['id_product']) ? (int)$commentData['id_product'] : 0;
                }
                $templateVars = [
                    '{admin_name}' => $s['name'],
                    '{object}' => EtsRVCore::trans($object['og'], $languageObj->iso_code, 'EtsRVCommentEntity'),
                    '{customer_name}' => $from_person_name,
                    '{content}' => !empty($comment->content[$idLang]) ? $comment->content[$idLang] : EtsRVComment::getOriginContentById($comment->id),
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

    public function commentMailToCustomer($id, $id_customer, $template, $id_employee = 0)
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
        if (($comment = EtsRVComment::getData($id, $this->context->language->id)) &&
            (
                !empty($comment['id_customer']) && (
                    $id_employee > 0 ||
                    (int)$comment['id_customer'] !== (int)$id_customer
                ) ||
                !empty($comment['employee']) && (
                    $id_customer > 0 ||
                    (int)$comment['employee'] !== (int)$id_employee
                )
            )
        ) {
            $from_person = $id_employee > 0 ? new Employee($id_employee) : new Customer($id_customer);
            $from_person_name = $from_person->firstname . ' ' . $from_person->lastname;

            $person = !empty($comment['id_customer']) ? new Customer((int)$comment['id_customer']) : new Employee((int)$comment['employee']);
            $person_name = $person->firstname . ' ' . $person->lastname;

            $languageObj = new Language($person->id_lang);
            $idLang = $languageObj->id ?: $this->context->language->id;
            $product = new Product(!empty($comment['id_product']) ? (int)$comment['id_product'] : 0, false, $idLang);
            $object = [
                'og' => 'comment',
                't' => $this->l('comment', 'EtsRVCommentEntity'),
            ];
            $templateVars = [
                '{content}' => isset($comment['content']) ? trim($comment['content']) : '',
                '{person_name}' => $person_name,
                '{from_person_name}' => $from_person_name,
                '{product_link}' => $this->context->link->getProductLink($product, $product->link_rewrite, $product->category, $product->ean13, $idLang),
                '{product_name}' => $product->name,
                '{object}' => EtsRVCore::trans($object['og'], $languageObj->iso_code, 'EtsRVCommentEntity')
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
                , (!empty($comment['employee']) ? (int)$comment['employee'] : 0)
                , $product->id
                , $this->context->shop->id
            );
        }

        return 0;
    }

    public function commentMailApproved($comment, $manual = false)
    {
        $this->commentMailToPosted($comment);

        if (!$comment instanceof EtsRVComment && Validate::isUnsignedInt($comment))
            $comment = new EtsRVComment((int)$comment);

        if ($comment->id > 0 &&
            (
                (!(int)Configuration::get('ETS_RV_AUTO_APPROVE') || $manual) && EtsRVEmailTemplate::isEnabled('tocustomer_approved') ||
                ((int)Configuration::get('ETS_RV_QA_AUTO_APPROVE') || $manual) && EtsRVEmailTemplate::isEnabled('tocustomer_approved')
            )
        ) {
            if ($comment->id > 0 && $comment->validate == 1) {

                $customer = new Customer($comment->id_customer);
                $customer_name = $customer->firstname . ' ' . $customer->lastname;

                $languageObj = new Language($customer->id_lang);
                $idLang = $languageObj->id ?: $this->context->language->id;
                $commentData = EtsRVComment::getData($comment->id, $idLang);
                $product = new Product(!empty($commentData['id_product']) ? (int)$commentData['id_product'] : 0, false, $idLang);
                $object = [
                    'og' => $comment->question && $comment->answer ? 'answer' : 'comment',
                    't' => $comment->question && $comment->answer ? $this->l('answer', 'EtsRVCommentEntity') : $this->l('comment', 'EtsRVCommentEntity')
                ];
                $templateVars = [
                    '{customer_name}' => $customer_name,
                    '{product_link}' => $this->context->link->getProductLink($product, $product->link_rewrite, $product->category, $product->ean13, $idLang),
                    '{product_name}' => $product->name,
                    '{object}' => EtsRVCore::trans($object['og'], $languageObj->iso_code, 'EtsRVCommentEntity'),
                    '{content}' => isset($commentData['content']) ? trim($commentData['content']) : ''
                ];
                return EtsRVMail::send(
                    $idLang
                    , 'person_approved'
                    , null
                    , $templateVars
                    , $customer->email
                    , $customer_name
                    , true
                    , $customer->id
                    , $comment->employee
                    , $product->id
                    , $this->context->shop->id
                );
            }
        }

        return 0;
    }

    /**** end sendmail ****/

    public function updateCommentUsefulness()
    {
        $this->viewAccess();

        if (!Configuration::get('ETS_RV_' . $this->sf . 'USEFULNESS')) {
            $this->ajaxRender(json_encode([
                'success' => false,
                'error' => $this->l('This feature is not enabled.', 'EtsRVCommentEntity'),
            ]));
        }

        $customerId = (int)$this->context->cookie->id_customer;
        $idProduct = (int)Tools::getValue('id_product');
        $id_comment = (int)Tools::getValue('id_ets_rv_comment');
        $comment = new EtsRVComment($id_comment);

        if ($this->employee <= 0 && $customerId <= 0) {
            $this->ajaxRender(json_encode([
                'success' => false,
                'error' => sprintf(
                    $this->l('You need to be %s or %s to give your appreciation of %s.', 'EtsRVCommentEntity')
                    , EtsRVTools::displayText($this->l('signed in', 'EtsRVCommentEntity'), 'a', ['href' => $this->context->link->getPageLink('authentication') . ($idProduct ? '?back=' . $this->context->link->getProductLink($idProduct) : ''), 'rel' => 'nofollow'])
                    , EtsRVTools::displayText($this->l('create an account', 'EtsRVCommentEntity'), 'a', ['href' => $this->context->link->getPageLink('authentication&create_account=1') . ($idProduct ? '&back=' . $this->context->link->getProductLink($idProduct) : ''), 'rel' => 'nofollow'])
                    , $this->qa ? ($comment->answer ? $this->l('an answer', 'EtsRVCommentEntity') : $this->l('a comment of question', 'EtsRVCommentEntity')) : $this->l('a comment', 'EtsRVCommentEntity')
                )
            ]));
        }
        $usefulness = (int)Tools::getValue('usefulness');
        if (!$comment->id) {
            $this->ajaxRender(json_encode([
                'success' => false,
                'error' => sprintf($this->l('Cannot find the requested product %s.', 'EtsRVCommentEntity'), ($this->qa ? $this->l('question', 'EtsRVCommentEntity') : $this->l('review', 'EtsRVCommentEntity'))),
            ]));
        }

        $result = EtsRVComment::setCommentUsefulness(
            $id_comment
            , (bool)$usefulness
            , $customerId
            , $this->employee
            , $this->qa
        );
        if ($result) {
            if ($this->qa) {
                if ($comment->answer)
                    $content = $usefulness ? 'like_an_answer' : 'dislike_an_answer';
                else
                    $content = $usefulness ? 'like_comment_of_question' : 'dislike_comment_of_question';
            } else
                $content = $usefulness ? 'like_a_comment' : 'dislike_a_comment';

            $this->addActivity($comment, ($comment->answer ? EtsRVActivity::ETS_RV_TYPE_ANSWER_QUESTION : ($this->qa ? EtsRVActivity::ETS_RV_TYPE_COMMENT_QUESTION : EtsRVActivity::ETS_RV_TYPE_COMMENT)), $usefulness ? EtsRVActivity::ETS_RV_ACTION_LIKE : EtsRVActivity::ETS_RV_ACTION_DISLIKE, (int)Tools::getValue('id_product'), $content, $this->context);
        }

        $commentUsefulness = EtsRVCommentRepository::getInstance()->getCommentUsefulness($id_comment, $this->qa, isset($this->context->customer->id) ? $this->context->customer->id : 0, $this->employee);

        $template = 'person_' . ($usefulness ? '' : 'dis') . 'like';
        if ((!$this->qa || !$comment->answer) && $result && EtsRVEmailTemplate::isEnabled($template)) {
            $this->commentMailToCustomer($id_comment
                , $customerId
                , $template
                , $this->employee
            );
        }

        $this->ajaxRender(json_encode(array_merge([
            'success' => true,
            'id_ets_rv_comment' => $id_comment,
        ], $commentUsefulness)));
    }

    public function updateAnswerUsefulness()
    {
        $this->viewAccess();

        if (!$this->backOffice) {
            $this->ajaxRender(json_encode([
                'success' => false,
                'error' => $this->l('Permission has been denied.', 'EtsRVCommentEntity'),
            ]));
        }

        $id_comment = (int)Tools::getValue('id_ets_rv_comment');
        $usefulness = (int)Tools::getValue('usefulness');
        $comment = new EtsRVComment($id_comment);

        if (!$this->qa || !$comment->id) {
            $this->ajaxRender(json_encode([
                'success' => false,
                'error' => $this->l('Sorry! You do not have permission to like or dislike this review.', 'EtsRVCommentEntity'),
            ]));
        }
        $comment->useful_answer = $usefulness;
        if (!$comment->save()) {
            $this->ajaxRender(json_encode([
                'success' => false,
                'error' => $this->l('Unknown error!', 'EtsRVCommentEntity'),
            ]));
        } elseif ($comment->id && $comment->id_ets_rv_product_comment)
            Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'ets_rv_comment` SET useful_answer=0 WHERE id_ets_rv_product_comment=' . (int)$comment->id_ets_rv_product_comment . ' AND id_ets_rv_comment != ' . (int)$comment->id);

        $this->ajaxRender(json_encode(array_merge([
            'success' => true,
            'id_ets_rv_comment' => $id_comment,
        ], $comment->toArray())));
    }

    public function deleteComment()
    {
        $id = (int)Tools::getValue('id_comment');
        $this->viewAccess($id ? 'delete' : null);

        $customerId = (int)$this->context->cookie->id_customer;
        if (!$customerId && !$this->backOffice) {
            $idProduct = (int)Tools::getValue('id_product');
            $this->ajaxRender(json_encode([
                'success' => false,
                'error' => sprintf(
                    $this->l('You need to be %s or %s to give your appreciation of a comment.', 'EtsRVCommentEntity')
                    , EtsRVTools::displayText($this->l('signed in', 'EtsRVCommentEntity'), 'a', ['href' => $this->context->link->getPageLink('authentication') . ($idProduct ? '?back=' . $this->context->link->getProductLink($idProduct) : ''), 'rel' => 'nofollow'])
                    , EtsRVTools::displayText($this->l('create an account', 'EtsRVCommentEntity'), 'a', ['href' => $this->context->link->getPageLink('authentication&create_account=1') . ($idProduct ? '&back=' . $this->context->link->getProductLink($idProduct) : ''), 'rel' => 'nofollow'])
                ),
            ]));
        }
        $comment = new EtsRVComment($id);
        if (!$this->backOffice && $comment->id &&
            (
                $comment->id_customer && $customerId != $comment->id_customer ||
                (int)$comment->validate == 1 && !Configuration::get('ETS_RV' . ($comment->question ? '_QA' : '') . '_CUSTOMER_DELETE_APPROVED')
            )
        ) {
            $this->_errors[] = $this->qa && $comment->answer ? $this->l('Cannot delete answer', 'EtsRVCommentEntity') : $this->l('Cannot delete comment', 'EtsRVCommentEntity');
        } elseif (!$comment->delete()) {
            $this->_errors[] = $this->l('Delete failed.', 'EtsRVCommentEntity');
        }
        $hasError = (bool)count($this->_errors);
        $response = [
            'success' => !$hasError,
            'error' => implode(Tools::nl2br("\n"), $this->_errors),
            'msg' => !$hasError ? sprintf($this->l('Comment #%1s has been deleted', 'EtsRVCommentEntity'), $comment->id) : ''
        ];
        $this->ajaxRender(json_encode($this->jsonExtra($response)));
    }

    public function privateComment()
    {
        $this->viewAccess();

        $customerId = (int)$this->context->cookie->id_customer;
        if (!$customerId && !$this->backOffice) {
            $idProduct = (int)Tools::getValue('id_product');
            $this->ajaxRender(json_encode([
                'success' => false,
                'error' => sprintf(
                    $this->l('You need to be %s or %s to set your comment to private.', 'EtsRVCommentEntity')
                    , EtsRVTools::displayText($this->l('signed in', 'EtsRVCommentEntity'), 'a', ['href' => $this->context->link->getPageLink('authentication') . ($idProduct ? '?back=' . $this->context->link->getProductLink($idProduct) : ''), 'rel' => 'nofollow'])
                    , EtsRVTools::displayText($this->l('create an account', 'EtsRVCommentEntity'), 'a', ['href' => $this->context->link->getPageLink('authentication&create_account=1') . ($idProduct ? '&back=' . $this->context->link->getProductLink($idProduct) : ''), 'rel' => 'nofollow'])
                ),
            ]));
        }
        $comment = new EtsRVComment((int)Tools::getValue('id_comment'));
        if (!$this->backOffice && $customerId != $comment->id_customer) {
            $this->_errors[] = $this->qa && $comment->answer ? $this->l('Cannot set this answer to private', 'EtsRVCommentEntity') : $this->l('Cannot set this comment to private', 'EtsRVCommentEntity');
        } else {
            $comment->validate = EtsRVComment::STATUS_PRIVATE;
            if (!$comment->update()) {
                $this->_errors[] = $this->l('Set to private failed.', 'EtsRVCommentEntity');
            }
        }
        $hasError = (bool)count($this->_errors);
        $response = [
            'success' => !$hasError,
            'error' => implode(Tools::nl2br("\n"), $this->_errors),
            'msg' => !$hasError ? sprintf($this->l('Comment #%1s has been set to private', 'EtsRVCommentEntity'), $comment->id) : ''
        ];
        $this->ajaxRender(json_encode($this->jsonExtra($response)));
    }

    public function approveComment()
    {
        $this->viewAccess();

        $customerId = (int)$this->context->cookie->id_customer;
        if (!$customerId && !$this->backOffice) {
            $idProduct = (int)Tools::getValue('id_product');
            $this->ajaxRender(json_encode([
                'success' => false,
                'error' => sprintf(
                    $this->l('You need to be %s or %s to give your appreciation of a comment.', 'EtsRVCommentEntity')
                    , EtsRVTools::displayText($this->l('signed in', 'EtsRVCommentEntity'), 'a', ['href' => $this->context->link->getPageLink('authentication') . ($idProduct ? '?back=' . $this->context->link->getProductLink($idProduct) : ''), 'rel' => 'nofollow'])
                    , EtsRVTools::displayText($this->l('create an account', 'EtsRVCommentEntity'), 'a', ['href' => $this->context->link->getPageLink('authentication&create_account=1') . ($idProduct ? '&back=' . $this->context->link->getProductLink($idProduct) : ''), 'rel' => 'nofollow'])
                ),
            ]));
        }
        $comment = new EtsRVComment((int)Tools::getValue('id_comment'));
        if (!$this->backOffice && $customerId != $comment->id_customer) {
            $this->_errors[] = $this->qa && $comment->answer ? $this->l('Cannot approve this answer', 'EtsRVCommentEntity') : $this->l('Cannot approve this comment', 'EtsRVCommentEntity');
        } else {
            $comment->validate = EtsRVComment::STATUS_APPROVE;
            if (!$comment->update()) {
                $this->_errors[] = $this->l('Approve failed.', 'EtsRVCommentEntity');
            } else
                $this->commentMailApproved($comment);
        }
        $hasError = (bool)count($this->_errors);
        $response = [
            'success' => !$hasError,
            'error' => implode(Tools::nl2br("\n"), $this->_errors),
            'msg' => !$hasError ? sprintf($this->l('Comment #%1s has been approved', 'EtsRVCommentEntity'), $comment->id) : ''
        ];
        $this->ajaxRender(json_encode($this->jsonExtra($response)));
    }

    public function updateDateComment()
    {
        $comment = new EtsRVComment((int)Tools::getValue('id_comment'));
        if (!$this->backOffice || !(int)Configuration::get('ETS_RV_' . (!$this->qa ? 'REVIEW' : 'QUESTION') . '_ENABLED')) {
            $this->_errors[] = $this->l('Permission denied.', 'EtsRVCommentEntity');
        } else {
            if (!($date_add = trim(Tools::getValue('date_add')))) {
                $this->_errors[] = $this->l('"Date add" value is required.', 'EtsRVCommentEntity');
            } elseif (!Validate::isDate($date_add) || strtotime($date_add) > time()) {
                $this->_errors[] = $this->l('"Date add" value is invalid.', 'EtsRVCommentEntity');
            } else {
                $comment->date_add = date('Y-m-d H:i:s', strtotime($date_add));
                if (!$comment->update()) {
                    $this->_errors[] = $this->l('Update failed.', 'EtsRVCommentEntity');
                }
            }
        }
        $hasError = (bool)count($this->_errors);
        $response = [
            'success' => !$hasError,
            'errors' => Tools::nl2br(implode(PHP_EOL, $this->_errors)),
            'msg' => !$hasError ? sprintf($this->l('The comment #%1s adding time has been updated successfully', 'EtsRVCommentEntity'), $comment->id) : '',
            'date_add' => !$hasError ? $this->timeElapsedString($comment->date_add) : '',
        ];
        $this->ajaxRender(json_encode($this->jsonExtra($response)));
    }
}