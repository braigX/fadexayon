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

class EtsRVProductCommentEntity extends EtsRVEntity
{
    public static function getInstance()
    {
        if (empty(self::$_INSTANCE['entity_product_comment']) || !self::$_INSTANCE['entity_product_comment'] instanceof EtsRVProductCommentEntity) {
            self::$_INSTANCE['entity_product_comment'] = new EtsRVProductCommentEntity();
        }

        return self::$_INSTANCE['entity_product_comment'];
    }

    public function getProductComments()
    {
        $idProduct = (int)Tools::getValue('id_product');
        $page = (int)Tools::getValue('page', 1);
        if ($page < 1)
            $page = 1;
        if (($reviews_per_page = (int)Tools::getValue('comments_per_page')) < 1 && ($reviews_per_page = (int)Configuration::get('ETS_RV_' . $this->sf . 'REVIEWS_PER_PAGE')) < 1)
            $reviews_per_page = 5;
        if (($reviews_initial = (int)Configuration::get('ETS_RV_' . $this->sf . 'REVIEWS_INITIAL')) < 1)
            $reviews_initial = 1;
        $validateOnly = $this->module->validateOnly($this->qa);
        $begin = (int)Tools::getValue('begin');
        if ($begin < 0) {
            $begin = 0;
        }
        if ($this->qa)
            $default_sort_by = trim(Configuration::get('ETS_RV_QA_DEFAULT_SORT_BY')) ?: false;
        else
            $default_sort_by = trim(Configuration::get('ETS_RV_DEFAULT_SORT_BY')) ?: false;
        $sortBy = trim(Tools::getValue('sort_by')) ?: $default_sort_by;
        $grade = Tools::getValue('grade');
        if ($grade == 'has_video_image') {
            $grade = 0;
            $has_video_image = true;
        } else {
            $grade = (int)$grade;
            $has_video_image = false;
            if ($grade > 5)
                $grade = 5;
            elseif ($grade < 0)
                $grade = 0;
        }

        // from comment id:
        $from_comment_id = Tools::getValue('comment_id');
        if ($from_comment_id == '' || !Validate::isUnsignedInt($from_comment_id) || (int)$from_comment_id < 0) {
            $from_comment_id = 0;
        }
        // from comment reply id:
        $from_comment_reply_id = Tools::getValue('comment_reply_id');
        if ($from_comment_reply_id == '' || !Validate::isUnsignedInt($from_comment_reply_id) || (int)$from_comment_reply_id < 0) {
            $from_comment_reply_id = 0;
        }

        $productCommentId = (int)Tools::getValue('id_product_comment');
        $firstOnly = (int)Tools::getValue('first') ? 1 : 0;
        $objectOnly = (int)Tools::getValue('object') ? 1 : 0;
        $answer = (int)Tools::getValue('answer') ? 1 : 0;

        $productCommentRepository = EtsRVProductCommentRepository::getInstance();
        $commentRepository = EtsRVCommentRepository::getInstance();
        $replyRepository = EtsRVReplyCommentRepository::getInstance();

        $productCommentsNb = $productCommentRepository->getCommentsNumber($idProduct, $this->context->language->id, $productCommentId, $validateOnly, $this->backOffice, $grade, $this->context, $this->qa, $has_video_image);
        if ($begin > $productCommentsNb) {
            $this->ajaxRender(json_encode([
                'success' => false,
                'errors' => sprintf($this->l('%s is empty', 'EtsRVProductCommentEntity'), $this->qa ? $this->l('Question', 'EtsRVProductCommentEntity') : $this->l('Review', 'EtsRVProductCommentEntity')),
            ]));
        }

        $productComments = $productCommentRepository->paginate(
            $idProduct,
            $this->context->language->id,
            $page,
            ($begin > 0 ? $reviews_per_page : $reviews_initial),
            $productCommentId,
            $validateOnly,
            $this->backOffice,
            $firstOnly,
            $begin,
            $sortBy,
            $grade,
            $this->context,
            $this->qa,
            $has_video_image
        );
        if ($begin > 0) {
            $begin += $reviews_per_page;
            $reviews_per_page = $begin >= $productCommentsNb ? 0 : ($productCommentsNb - $begin >= $reviews_per_page ? $reviews_per_page : ($productCommentsNb - $begin));
        } else
            $begin += $reviews_initial;

        $responseArray = [
            'id' => $productCommentId,
            'success' => true,
            'begin' => $begin,
            'reviews_nb' => $productCommentsNb,
            'reviews_per_page' => $reviews_per_page,
            'reviews_initial' => $reviews_initial,
            'question' => $this->qa,
            'comments' => [],
        ];
        if (!$this->qa)
            $responseArray['photos'] = htmlentities($this->module->displayAllPhotos($idProduct, true));

        $comments_initial = (int)Configuration::get('ETS_RV_' . $this->sf . 'COMMENTS_INITIAL');
        if ($comments_initial <= 0) {
            $comments_initial = 1;
        }
        $replies_initial = (int)Configuration::get('ETS_RV_' . $this->sf . 'REPLIES_INITIAL');
        if ($replies_initial <= 0) {
            $replies_initial = 1;
        }
        $autoApproved = $this->backOffice || (int)Configuration::get('ETS_RV_' . $this->sf . 'AUTO_APPROVE') ? null : 1;

        if ($productComments) {
            foreach ($productComments as $productComment) {
                $this->formatItem($idProduct, $productComment);
                // Like or Dislike:
                $usefulness = $productCommentRepository->getProductCommentUsefulness($productComment['id_ets_rv_product_comment'], $this->qa, isset($this->context->customer->id) ? $this->context->customer->id : 0, $this->employee);
                $productComment = array_merge($productComment, $usefulness);
                if (!$this->qa) {
                    $productComment['criterion'] = EtsRVProductComment::getGradesById((int)$productComment['id_ets_rv_product_comment'], $this->context->language->id);
                    $productComment['images'] = $this->module->displayPCListImages((int)$productComment['id_ets_rv_product_comment'], (bool)$objectOnly);
                    $productComment['videos'] = $this->module->displayPCListVideos((int)$productComment['id_ets_rv_product_comment'], (bool)$objectOnly);
                }
                if (!$objectOnly) {
                    // Comments.
                    $productComment['comments_nb'] = $commentRepository->getCommentsNumber(
                        (int)$productComment['id_ets_rv_product_comment']
                        , $this->context->language->id
                        , $autoApproved
                        , $this->backOffice
                        , $this->context
                        , $this->qa
                        , 0
                        , !$answer ? (int)$from_comment_id : 0
                    );
                    if (!$answer && (int)$from_comment_id > 0) {
                        $productComment['comments_nb_forward'] = $commentRepository->getCommentsNumber(
                            (int)$productComment['id_ets_rv_product_comment']
                            , $this->context->language->id
                            , $autoApproved
                            , $this->backOffice
                            , $this->context
                            , $this->qa
                            , 0
                            , (int)$from_comment_id
                            , '<='
                        );
                    }
                    $comments = $commentRepository->paginate(
                        (int)$productComment['id_ets_rv_product_comment']
                        , $this->context->language->id
                        , $page
                        , $comments_initial
                        , $autoApproved
                        , $this->backOffice
                        , false
                        , 0
                        , false
                        , $this->context
                        , $this->qa
                        , 0
                        , !$answer ? (int)$from_comment_id : 0
                    );
                    if ($comments) {
                        foreach ($comments as &$comment) {
                            $this->formatItem($idProduct, $comment);
                            $usefulness = $commentRepository->getCommentUsefulness($comment['id_ets_rv_comment'], $this->qa, isset($this->context->customer->id) ? $this->context->customer->id : 0, $this->employee);
                            $comment = array_merge($comment, $usefulness);
                            $has_replies_forward = (int)$comment['id_ets_rv_comment'] == (int)$from_comment_id;
                            if ($has_replies_forward) {
                                $comment['scroll'] = (int)$from_comment_id;
                            }
                            // Reply comments.
                            if (!$this->qa) {
                                $comment['replies_nb'] = $replyRepository->getReplyCommentsNumber(
                                    (int)$comment['id_ets_rv_comment']
                                    , $this->context->language->id
                                    , $autoApproved
                                    , $this->backOffice
                                    , $this->context
                                    , $this->qa
                                    , $has_replies_forward ? (int)$from_comment_reply_id : 0
                                );
                                if ($has_replies_forward && (int)$from_comment_reply_id > 0) {
                                    $comment['replies_nb_forward'] = $replyRepository->getReplyCommentsNumber(
                                        (int)$comment['id_ets_rv_comment']
                                        , $this->context->language->id
                                        , $autoApproved
                                        , $this->backOffice
                                        , $this->context
                                        , $this->qa
                                        , (int)$from_comment_reply_id
                                        , '<='
                                    );
                                }
                                $replies = $replyRepository->paginate(
                                    (int)$comment['id_ets_rv_comment']
                                    , $this->context->language->id
                                    , $page
                                    , $replies_initial
                                    , $autoApproved
                                    , $this->backOffice
                                    , false
                                    , 0
                                    , false
                                    , $this->context
                                    , $this->qa
                                    , (int)$comment['id_ets_rv_comment'] == (int)$from_comment_id ? (int)$from_comment_reply_id : 0
                                );
                                if ($replies) {
                                    foreach ($replies as &$reply) {
                                        $this->formatItem($idProduct, $reply);
                                        $usefulness = $replyRepository->getReplyCommentUsefulness($reply['id_ets_rv_reply_comment'], $this->qa, isset($this->context->customer->id) ? $this->context->customer->id : 0, $this->employee);
                                        $reply = array_merge($reply, $usefulness);
                                        if ((int)$reply['id_ets_rv_reply_comment'] == (int)$from_comment_reply_id) {
                                            $reply['scroll'] = (int)$from_comment_reply_id;
                                        }
                                    }
                                }
                                $comment['replies'] = $replies;
                            }
                            // End Reply comment:
                        }
                    }
                    $productComment['comments'] = $comments;

                    // Questions.
                    if ($this->qa) {
                        $productComment['answers_nb'] = $commentRepository->getCommentsNumber(
                            (int)$productComment['id_ets_rv_product_comment']
                            , $this->context->language->id
                            , $autoApproved
                            , $this->backOffice
                            , $this->context
                            , $this->qa
                            , 1
                            , $answer ? (int)$from_comment_id : 0
                        );
                        if ($answer && (int)$from_comment_id > 0) {
                            $productComment['answers_nb_forward'] = $commentRepository->getCommentsNumber(
                                (int)$productComment['id_ets_rv_product_comment']
                                , $this->context->language->id
                                , $autoApproved
                                , $this->backOffice
                                , $this->context
                                , $this->qa
                                , 1
                                , (int)$from_comment_id
                                , '<='
                            );
                        }
                        // Answer's.
                        $answers = $commentRepository->paginate(
                            (int)$productComment['id_ets_rv_product_comment']
                            , $this->context->language->id
                            , $page
                            , $comments_initial
                            , $autoApproved
                            , $this->backOffice
                            , false
                            , 0
                            , $sortBy
                            , $this->context
                            , $this->qa
                            , 1
                            , $answer ? $from_comment_id : 0
                        );
                        if ($answers) {
                            foreach ($answers as &$answer) {
                                $this->formatItem($idProduct, $answer);
                                $usefulness = $commentRepository->getCommentUsefulness($answer['id_ets_rv_comment'], $this->qa, isset($this->context->customer->id) ? $this->context->customer->id : 0, $this->employee);
                                $answer = array_merge($answer, $usefulness);
                                $has_replies_forward = (int)$answer['id_ets_rv_comment'] == (int)$from_comment_id;
                                if ($has_replies_forward) {
                                    $answer['scroll'] = (int)$from_comment_id;
                                }
                                $answer['replies_nb'] = $replyRepository->getReplyCommentsNumber(
                                    (int)$answer['id_ets_rv_comment']
                                    , $this->context->language->id
                                    , $autoApproved
                                    , $this->backOffice
                                    , $this->context
                                    , $this->qa
                                    , $has_replies_forward ? (int)$from_comment_reply_id : 0
                                );
                                if ($has_replies_forward && (int)$from_comment_reply_id > 0) {
                                    $answer['replies_nb_forward'] = $replyRepository->getReplyCommentsNumber(
                                        (int)$answer['id_ets_rv_comment']
                                        , $this->context->language->id
                                        , $autoApproved
                                        , $this->backOffice
                                        , $this->context
                                        , $this->qa
                                        , (int)$from_comment_reply_id
                                        , '<='
                                    );
                                }
                                // Reply comment's.
                                $replies = $replyRepository->paginate(
                                    (int)$answer['id_ets_rv_comment']
                                    , $this->context->language->id
                                    , $page
                                    , $replies_initial
                                    , $autoApproved
                                    , $this->backOffice
                                    , false
                                    , 0
                                    , $sortBy
                                    , $this->context
                                    , $this->qa
                                    , $has_replies_forward ? (int)$from_comment_reply_id : 0
                                );
                                if ($replies) {
                                    foreach ($replies as &$reply) {
                                        $this->formatItem($idProduct, $reply);
                                        $usefulness = $replyRepository->getReplyCommentUsefulness($reply['id_ets_rv_reply_comment'], $this->qa, isset($this->context->customer->id) ? $this->context->customer->id : 0, $this->employee);
                                        $reply = array_merge($reply, $usefulness);
                                        if ((int)$reply['id_ets_rv_reply_comment'] == (int)$from_comment_reply_id) {
                                            $reply['scroll'] = (int)$from_comment_reply_id;
                                        }
                                    }
                                }
                                $answer['replies'] = $replies;
                            }
                            $productComment['answers'] = $answers;
                        }
                    }

                    // Push to list Review's.
                    $responseArray['comments'][] = $productComment;
                } else {
                    $responseArray['comments'] = $productComment;
                    break;
                }
            }
        }

        $this->ajaxRender(json_encode($responseArray));
    }

    public function postProductComment()
    {
        $productCommentId = (int)Tools::getValue('id_product_comment');
        $this->viewAccess($productCommentId ? 'edit' : null);

        $freeDownload = (int)Configuration::get('ETS_RV_FREE_DOWNLOADS_ENABLED') > 0;
        $id_product = (int)Tools::getValue('id_product');
        $product = new Product($id_product, true);
        $id_order = trim(Tools::getValue('id_order'));
        if ($product->id <= 0) {
            $this->_errors[] = $this->l('The product does not exist.', 'EtsRVProductCommentEntity');
        }
        if (Validate::isUnsignedInt($id_order) && (int)$id_order > 0 && ($order = new Order($id_order))) {
            if ($order->id <= 0) {
                $this->_errors[] = $this->l('The order does not exist.', 'EtsRVProductCommentEntity');
            } elseif ((int)$order->id_customer > 0 && (int)$order->id_customer !== (int)$this->context->customer->id) {
                $this->_errors[] = $this->l('You do not have permission to access.', 'EtsRVProductCommentEntity');
            } else {
                $order_detail = $order->getOrderDetailList();
                if (!$order_detail || !is_array($order_detail) || !count($order_detail)) {
                    $this->_errors[] = $this->l('You do not have permission to review the product.', 'EtsRVProductCommentEntity');
                } else {
                    $product_id = 0;
                    foreach ($order_detail as $od) {
                        if ((int)$od['product_id'] === (int)$id_product) {
                            $product_id = (int)$od['product_id'];
                            break;
                        }
                    }
                    if ($product_id <= 0) {
                        $this->_errors[] = $this->l('You do not have permission to review the product.', 'EtsRVProductCommentEntity');
                    }
                }
            }
        }
        if (count($this->_errors)) {
            $this->ajaxRender(json_encode([
                'success' => false,
                'error' => implode(PHP_EOL, $this->_errors),
            ]));
        }

        $ETS_RV_PURCHASED_PRODUCT = EtsRVTools::reviewGrand('purchased');
        $ETS_RV_CUSTOMER = EtsRVTools::reviewGrand('no_purchased');
        $ETS_RV_CUSTOMER_INCL = EtsRVTools::reviewGrand('no_purchased_incl');
        $ETS_RV_CUSTOMER_EXCL = EtsRVTools::reviewGrand('no_purchased_excl');
        $ETS_RV_ALLOW_GUESTS = EtsRVTools::reviewGrand('guest');


        $purchasedInTime = (int)Configuration::get('ETS_RV_REVIEW_AVAILABLE_TIME');
        $customerPurchasedTime = EtsRVTools::isCustomerPurchased() && $purchasedInTime > 0;

        $purchased = $purchasedTime = $orderNotValid = false;
        if (!$this->qa && $this->isCustomerLogged()) {
            $purchased = EtsRVProductComment::isPurchased($this->context->customer->id, $id_product);
            $purchasedTime = $customerPurchasedTime ? EtsRVProductComment::getLastOrderValid($this->context->customer->id, $id_product, $purchasedInTime) : $purchased;
            $orderNotValid = EtsRVProductComment::isPurchased($this->context->customer->id, $id_product, false);
        }
        if ($this->qa && !$this->isCustomerLogged() && !(int)Configuration::get('ETS_RV_QA_ALLOW_GUESTS') || !$this->qa && !$this->isCustomerLogged() && !$ETS_RV_ALLOW_GUESTS && (!$freeDownload && $ETS_RV_CUSTOMER || $freeDownload && ($ETS_RV_CUSTOMER_INCL || $ETS_RV_CUSTOMER_EXCL) || $ETS_RV_PURCHASED_PRODUCT)) {
            $this->_errors[] = sprintf($this->l('You need to be %s or %s to post your %s.', 'EtsRVProductCommentEntity')
                , EtsRVTools::displayText($this->l('signed in', 'EtsRVProductCommentEntity'), 'a', ['href' => $this->context->link->getPageLink('authentication') . ($id_product ? '?back=' . $this->context->link->getProductLink($id_product) : ''), 'rel' => 'nofollow'])
                , EtsRVTools::displayText($this->l('create an account', 'EtsRVProductCommentEntity'), 'a', ['href' => $this->context->link->getPageLink('authentication&create_account=1') . ($id_product ? '&back=' . $this->context->link->getProductLink($id_product) : ''), 'rel' => 'nofollow'])
                , $this->qa ? $this->l('question', 'EtsRVProductCommentEntity') : $this->l('review', 'EtsRVProductCommentEntity')
            );
        } elseif (!$this->qa && !$productCommentId) {
            if (!$ETS_RV_ALLOW_GUESTS && !(!$freeDownload && $ETS_RV_CUSTOMER || $freeDownload && ($ETS_RV_CUSTOMER_INCL || $ETS_RV_CUSTOMER_EXCL)) && !$ETS_RV_PURCHASED_PRODUCT) {
                $this->_errors[] = $this->l('You are not allowed to post reviews at the moment. Please contact the store admin for more details', 'EtsRVProductCommentEntity');
            } elseif ($this->isCustomerLogged() && EtsRVProductCommentCustomer::isBlockByIdCustomer($this->context->customer->id)) {
                $this->_errors[] = $this->l('Your account was blocked and not allowed to write a review', 'EtsRVProductCommentEntity');
            } elseif ($this->isCustomerLogged() && !((!$purchased || $freeDownload && $product->price <= 0) && (!$freeDownload && $ETS_RV_CUSTOMER || $freeDownload && ($ETS_RV_CUSTOMER_INCL && $product->price <= 0 || $ETS_RV_CUSTOMER_EXCL && $product->price > 0)) || $ETS_RV_PURCHASED_PRODUCT && $purchasedTime)) {
                if ($ETS_RV_ALLOW_GUESTS && !(!$freeDownload && $ETS_RV_CUSTOMER || $freeDownload && ($ETS_RV_CUSTOMER_INCL || $ETS_RV_CUSTOMER_EXCL)) && !$ETS_RV_PURCHASED_PRODUCT) {
                    $this->_errors[] = $this->l('Only guest user can write review', 'EtsRVProductCommentEntity');//4.
                } elseif ($ETS_RV_PURCHASED_PRODUCT && !$purchased && $orderNotValid) {
                    $this->_errors[] = $this->l('Your order is waiting to be verified by the store admin. You have not been able to write a review or rate this product yet.', 'EtsRVProductCommentEntity');
                } elseif ($freeDownload && $product->price > 0 && !$purchased && $ETS_RV_PURCHASED_PRODUCT || !$freeDownload && $ETS_RV_PURCHASED_PRODUCT && !$purchased) {
                    $this->_errors[] = $this->l('You can only leave a review after purchasing this product', 'EtsRVProductCommentEntity');//6.
                } elseif ($freeDownload && !$purchasedTime && $product->price > 0 && $ETS_RV_PURCHASED_PRODUCT || !$freeDownload && $ETS_RV_PURCHASED_PRODUCT && !$purchasedTime) {
                    $this->_errors[] = $this->l('You have exceeded the available time to write a review for this product', 'EtsRVProductCommentEntity');//8.
                } elseif ($freeDownload && !$purchased && $product->price <= 0 && ($ETS_RV_PURCHASED_PRODUCT || $ETS_RV_CUSTOMER_EXCL)) {
                    $this->_errors[] = $this->l('Cannot write review for free product', 'EtsRVProductCommentEntity');//1.
                } elseif ($freeDownload && $purchased && $product->price > 0 && $ETS_RV_CUSTOMER_EXCL || !$freeDownload && $ETS_RV_CUSTOMER && $purchased) {
                    $this->_errors[] = $this->l('Only user who has not purchased can write review.', 'EtsRVProductCommentEntity');//3.
                } elseif ($freeDownload && $ETS_RV_CUSTOMER_INCL && $product->price > 0) {
                    $this->_errors[] = $this->l('Cannot write review for paid product', 'EtsRVProductCommentEntity');//2.
                }
            } elseif (($maximum_review = trim(Configuration::get('ETS_RV_MAXIMUM_REVIEW_PER_USER'))) !== '' && (int)$maximum_review <= EtsRVProductComment::getNbReviewsOfUser($id_product, $this->context)) {
                $this->_errors[] = sprintf($this->l('You are only allowed to leave %d review(s) for this product', 'EtsRVProductCommentEntity'), (int)$maximum_review);
            }
        }
        if (!count($this->_errors) && Configuration::get('ETS_RV_RECAPTCHA_ENABLED')) {
            $this->verifyReCAPTCHA($this->_errors);
        }

        $productComment = new EtsRVProductComment($productCommentId);
        if (!count($this->_errors)) {
            $isPostAllowed = EtsRVProductCommentRepository::getInstance()->isPostAllowed($id_product, (int)$this->context->cookie->id_customer, (int)$this->context->cookie->id_guest, $this->qa);
            if (!$productCommentId && !$isPostAllowed) {
                $this->_errors[] = sprintf(
                    $this->l('You are not allowed to post a %s at the moment, please try again after %d seconds.', 'EtsRVProductCommentEntity')
                    , (!$this->qa ? $this->l('review', 'EtsRVProductCommentEntity') : $this->l('question', 'EtsRVProductCommentEntity'))
                    , EtsRVProductCommentRepository::getInstance()->getMinimalTime($id_product, (int)$this->context->cookie->id_customer, (int)$this->context->cookie->id_guest, $this->qa)
                );
            } elseif (!$this->backOffice && $productComment->id && (($productComment->id_customer && (int)$productComment->id_customer !== (int)$this->context->cookie->id_customer) || (!$productComment->id_customer && $productComment->id_guest && (int)$productComment->id_guest !== (int)$this->context->cookie->id_guest))) {
                $this->_errors[] = sprintf($this->l('You are not allowed to edit %s, please try again later.', 'EtsRVProductCommentEntity'), (!$this->qa ? $this->l('review', 'EtsRVProductCommentEntity') : $this->l('question', 'EtsRVProductCommentEntity')));
            } elseif (!$this->backOffice && $productComment->id && (int)$productComment->validate == 1 && !Configuration::get('ETS_RV_' . ($this->qa ? 'QA_' : '') . 'CUSTOMER_EDIT_APPROVED')) {
                $this->_errors[] = sprintf($this->l('You are not allowed to post a/an %s.', 'EtsRVProductCommentEntity'), (!$this->qa ? $this->l('review', 'EtsRVProductCommentEntity') : $this->l('question', 'EtsRVProductCommentEntity')));
            }
        }
        if (count($this->_errors) > 0) {
            $this->ajaxRender(json_encode([
                'success' => false,
                'error' => implode(PHP_EOL, $this->_errors),
            ]));
        }
        // Validate fields:
        if (($upload_photo_enabled = Configuration::get('ETS_RV_UPLOAD_PHOTO_ENABLED') && Configuration::get('ETS_RV_MAX_UPLOAD_PHOTO')) && !$this->validateUpload('image', $this->_errors)) {
            if (count($this->_errors) > 0) {
                $this->ajaxRender(json_encode([
                    'success' => false,
                    'errors' => $this->_errors,
                ]));
            }
        }
        if (($upload_video_enabled = Configuration::get('ETS_RV_UPLOAD_VIDEO_ENABLED') && Configuration::get('ETS_RV_MAX_UPLOAD_VIDEO')) && !$this->validateUpload('video', $this->_errors)) {
            if (count($this->_errors) > 0) {
                $this->ajaxRender(json_encode([
                    'success' => false,
                    'errors' => $this->_errors,
                ]));
            }
        }
        $comment_title = strip_tags(trim(Tools::getValue('comment_title')));
        if ($comment_title == '' && ((int)Configuration::get('ETS_RV_REQUIRE_TITLE') || $this->qa)) {
            $this->_errors[] = $this->l('Title cannot be empty', 'EtsRVProductCommentEntity');
        } elseif (!Validate::isCleanHtml($comment_title)) {
            $this->_errors[] = $this->l('Title is invalid', 'EtsRVProductCommentEntity');
        } elseif (Tools::strlen($comment_title) > EtsRVProductComment::TITLE_MAX_LENGTH) {
            $this->_errors[] = sprintf($this->l('Title cannot be longer than %s characters', 'EtsRVProductCommentEntity'), EtsRVProductComment::TITLE_MAX_LENGTH);
        }

        $comment_content = trim(Tools::getValue('comment_content'));
        $maximum_character = Configuration::get('ETS_RV_' . ($this->qa ? 'QA_' : '') . 'MAX_LENGTH');
        if (trim($maximum_character) === '')
            $maximum_character = EtsRVModel::NAME_MAX_LENGTH;
        $minimum_character = Configuration::get('ETS_RV_' . ($this->qa ? 'QA_' : '') . 'MIN_LENGTH');
        if (trim($minimum_character) === '')
            $minimum_character = EtsRVModel::NAME_MIN_LENGTH;

        if ($comment_content == '') {
            $this->_errors[] = $this->l('Comment cannot be empty', 'EtsRVProductCommentEntity');
        } elseif (!Validate::isCleanHtml($comment_content) || preg_match('/[{}]/i', $comment_content) || !preg_match('/^(?!.*<[^bi\/>]+>).*$/s', $comment_content)) {
            $this->_errors[] = $this->l('Comment is invalid', 'EtsRVProductCommentEntity');
        } elseif ((int)$maximum_character > 0 && Tools::strlen($comment_content) > (int)$maximum_character) {
            $this->_errors[] = sprintf($this->l('Content cannot be longer than %s characters', 'EtsRVProductCommentEntity'), $maximum_character);
        } elseif ((int)$minimum_character > 0 && Tools::strlen($comment_content) < (int)$minimum_character) {
            $this->_errors[] = sprintf($this->l('Content cannot be shorter than %s characters', 'EtsRVProductCommentEntity'), $minimum_character);
        }

        $customer_name = trim(Tools::getValue('customer_name'));
        $email = trim(Tools::getValue('email'));
        if ((int)$this->context->cookie->id_customer <= 0 || !$this->context->customer->isLogged()) {
            if ($customer_name == '') {
                $this->_errors[] = $this->l('Customer name cannot be empty', 'EtsRVProductCommentEntity');
            } elseif (!Validate::isName($customer_name)) {
                $this->_errors[] = $this->l('Customer name is invalid', 'EtsRVProductCommentEntity');
            }
            if ($email == '') {
                $this->_errors[] = $this->l('Email cannot be empty', 'EtsRVProductCommentEntity');
            } elseif (!Validate::isEmail($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->_errors[] = $this->l('Email is invalid', 'EtsRVProductCommentEntity');
            }
        }

        $criterions = Tools::getValue('criterion');
        $product = new Product($id_product);

        $ETS_RV_CUSTOMER_RATE = EtsRVTools::ratingGrand('no_purchased');
        $ETS_RV_PURCHASED_PRODUCT_RATE = EtsRVTools::ratingGrand('purchased');
        $ETS_RV_CUSTOMER_RATE_INCL = EtsRVTools::ratingGrand('no_purchased_incl');
        $ETS_RV_CUSTOMER_RATE_EXCL = EtsRVTools::ratingGrand('no_purchased_excl');
        $ETS_RV_ALLOW_GUESTS_RATE = EtsRVTools::ratingGrand('guest');
        $ETS_RV_MAXIMUM_RATING_PER_USER = trim(Configuration::get('ETS_RV_MAXIMUM_RATING_PER_USER'));

        if ($criterions
            && is_array($criterions)
            && ($productComment->id > 0 && count(EtsRVProductComment::getGradesById($productComment->id, $this->context->language->id)) > 0 || $productComment->id < 1 && ((
                        $ETS_RV_ALLOW_GUESTS && $ETS_RV_ALLOW_GUESTS_RATE && $this->isGuest() ||
                        $this->isCustomerLogged() && (
                            $ETS_RV_PURCHASED_PRODUCT && $ETS_RV_PURCHASED_PRODUCT_RATE && $purchased || !$purchased && (
                                !$freeDownload && $ETS_RV_CUSTOMER && $ETS_RV_CUSTOMER_RATE ||
                                $freeDownload && ($ETS_RV_CUSTOMER_INCL && $ETS_RV_CUSTOMER_RATE_INCL && $product->price <= 0 || $ETS_RV_CUSTOMER_EXCL && $ETS_RV_CUSTOMER_RATE_EXCL && $product->price > 0)
                            )
                        )
                    ) && (trim($ETS_RV_MAXIMUM_RATING_PER_USER) === '' || (int)$ETS_RV_MAXIMUM_RATING_PER_USER > (int)EtsRVProductComment::getNbReviewsOfUser($id_product, $this->context, true)))
            )
        ) {
            foreach ($criterions as $id => $criterion) {
                if (!$id || !Validate::isUnsignedInt($id) || (int)$criterion == 0)
                    $this->_errors[] = $this->l('Rating is required.', 'EtsRVProductCommentEntity');
                elseif (!($criterionObj = new EtsRVProductCommentCriterion($id, $this->context->language->id)) || !$criterionObj->id)
                    $this->_errors[] = $this->l('Rating does not exist.', 'EtsRVProductCommentEntity');
                elseif (!Validate::isUnsignedInt($criterion) || !$criterion > 5 || $criterion < 0)
                    $this->_errors[] = sprintf($this->l('Rating for "%s" is invalid', 'EtsRVProductCommentEntity'), $criterionObj->name);
            }
        } else
            $criterions = [];
        if (count($this->_errors) > 0) {
            $this->ajaxRender(json_encode([
                'success' => false,
                'errors' => $this->_errors,
            ]));
        }

        if (!$productComment->id) {
            $productComment->id_product = $id_product;
            $productComment->id_customer = $this->context->cookie->id_customer;
            $productComment->id_guest = $this->context->cookie->id_guest;
            $productComment->date_add = date('Y-m-d H:i:s');
            $productComment->validate = $this->backOffice || (
                !$this->qa &&
                (
                    !(int)Configuration::get('ETS_RV_MODERATE') ||
                    (int)Configuration::get('ETS_RV_PURCHASED_PRODUCT_APPROVE') && EtsRVProductComment::verifyPurchase($id_product, (int)$productComment->id_customer)
                ) ||
                $this->qa && !(int)Configuration::get('ETS_RV_QA_MODERATE')
            ) ? EtsRVProductComment::STATUS_APPROVE : EtsRVProductComment::STATUS_PENDING;
            $productComment->publish_all_language = (int)Configuration::get('ETS_RV_PUBLISH_ALL_LANGUAGE') ? 1 : 0;
            $productComment->question = $this->qa;
            if ($productComment->id_customer > 0 && ($id_address = (int)Address::getFirstCustomerAddressId($productComment->id_customer))) {
                $address = new Address($id_address);
                if ($address->id_country > 0)
                    $productComment->id_country = $address->id_country;
            }
        } else
            $productComment->upd_date = date('Y-m-d H:i:s');

        $productComment->customer_name = $customer_name;
        $productComment->email = $email;
        $productComment->grade = 0;

        $languages = Language::getLanguages(false);
        $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
        if ($languages) {
            $multiLang = (int)Configuration::get('ETS_RV_MULTILANG_ENABLED');
            foreach ($languages as $l) {
                $isValidLang = $multiLang && ((int)$l['id_lang'] == $id_lang_default || (int)$l['id_lang'] == $this->context->language->id);
                $productComment->title[(int)$l['id_lang']] = $isValidLang ? $comment_title : null;
                $productComment->content[(int)$l['id_lang']] = $isValidLang ? $comment_content : null;
            }
        }
        $verified_purchase = trim(Tools::getValue('verified_purchase'));
        $productComment->verified_purchase = $verified_purchase !== '' && $this->backOffice ? $verified_purchase : 'auto';

        if (!empty($this->_errors = $this->validateComment($productComment))) {
            $this->ajaxRender(json_encode([
                'success' => false,
                'errors' => $this->_errors,
            ]));
        }
        $product_comment_order_added = 0;
        if ($productComment->save(true, false)) {
            // Add activity:
            if (!$productCommentId) {
                $content = $this->qa ? 'asked_a_question_about_product' : 'wrote_a_review_for_product';
                $type = $this->qa ? EtsRVActivity::ETS_RV_TYPE_QUESTION : EtsRVActivity::ETS_RV_TYPE_REVIEW;
                $this->addActivity($productComment, $type, $type, $id_product, $content, $this->context, !$productComment->id_customer ? $customer_name : null);
            }
            // Origin lang:
            EtsRVProductComment::saveOriginLang(
                $productComment->id,
                $this->context->language->id,
                $comment_title,
                $comment_content,
                $this->employee
            );
            // Publish lang:
            if (!$productComment->publish_all_language) {
                EtsRVProductComment::savePublishLang($productComment->id, array($this->context->language->id));
            }
            // Product order comment:
            if ($id_order == '' ||
                !Validate::isUnsignedInt($id_order) ||
                !($order = new Order((int)$id_order)) ||
                $order->id <= 0
            ) {
                $id_order = EtsRVProductCommentOrder::getOldestOrder($productComment->id_product);
            }
            if ($id_order > 0 && !EtsRVProductCommentOrder::getReviewed((int)$id_order, $productComment->id_product)) {
                if (EtsRVProductCommentOrder::saveData(array(
                    'id_ets_rv_product_comment' => (int)$productComment->id,
                    'id_order' => (int)$id_order,
                    'id_product' => $productComment->id_product,
                ))) {
                    $product_comment_order_added = $id_order;
                }
            }
        }

        if ($criterions && ($productCommentId || $this->backOffice || ($maximum_rating = Configuration::get('ETS_RV_MAXIMUM_RATING_PER_USER')) == '' || $maximum_rating > (int)EtsRVProductComment::getNbReviewsOfUser($id_product, $this->context, true))) {
            $this->addCommentGrades($productComment, $criterions);
        }

        if ($upload_photo_enabled && !$this->processUploadImage($productComment, 'image', $this->_errors)) {
            $this->ajaxRender(json_encode([
                'success' => false,
                'errors' => $this->_errors,
            ]));
        }
        if ($upload_video_enabled && !$this->processUploadVideo($productComment, 'video', $this->_errors)) {
            $this->ajaxRender(json_encode([
                'success' => false,
                'errors' => $this->_errors,
            ]));
        }

        if (!$productCommentId && $productComment->id && !$this->employee && ($productComment->id_customer || trim($productComment->customer_name) !== '' && trim($productComment->email) !== '')) {
            $cart_rule = null;
            if ((int)$productComment->id_customer > 0) {
                $customer = new Customer((int)$productComment->id_customer);
                $customer_name = $customer->firstname . ' ' . $customer->lastname;
            } else
                $customer_name = $productComment->customer_name;
            $voucher = $this->qa || (int)$productComment->id_customer <= 0 || !isset($customer) ? '' : EtsRVCartRule::getInstance()->doShortCode($id_product, $productComment->grade, $customer, $cart_rule, $this->backOffice);
            $originLang = EtsRVProductComment::getOriginLang($productComment->id);

            $object = [
                'og' => $this->qa ? 'a question' : 'a review',
                't' => $this->qa ? $this->l('a question', 'EtsRVProductCommentEntity') : $this->l('a review', 'EtsRVProductCommentEntity')
            ];
            // Send email to admin:
            if ($productComment->validate == 0
                && EtsRVEmailTemplate::isEnabled('toadmin_awaiting')
                && ($staffs = array_merge(EtsRVStaff::getAll($this->context, 1), EtsRVProductCommentCustomer::getAll($this->context)))
                && $this->employee == 0
            ) {
                foreach ($staffs as $s) {
                    $languageObj = new Language(isset($s['id_lang']) ? $s['id_lang'] : 0);
                    $idLang = $languageObj->id ?: $this->context->language->id;
                    $templateVars = [
                        '{admin_name}' => $s['name'],
                        '{object}' => EtsRVCore::trans($object['og'], $languageObj->iso_code, 'EtsRVProductCommentEntity'),
                        '{customer_name}' => $customer_name,
                        '{content}' => !empty($productComment->content[$idLang]) ? $productComment->content[$idLang] : $comment_content,
                        '{product}' => $this->module->displayProductInfo($id_product, $productComment->grade, $idLang, $this->context->shop->id),
                    ];
                    EtsRVMail::send(
                        $idLang
                        , 'toadmin_awaiting'
                        , null
                        , $templateVars
                        , $s['email']
                        , $s['name']
                        , true
                        , 0
                        , $s['id']
                        , $product->id
                        , $this->context->shop->id
                        , $productComment->question ? 0 : $productComment->id
                    );
                }
            }
            // Send to customer:
            $template = 'tocustomer_' . ($productComment->validate == 1 ? 'approved' : 'awaiting');
            if (EtsRVEmailTemplate::isEnabled($template) || EtsRVEmailTemplate::isEnabled('tocustomer_get_voucher')) {
                $shop_name = Configuration::get('PS_SHOP_NAME');
                if (isset($customer) && $customer instanceof Customer && $customer->id > 0) {
                    $languageObj = new Language($customer->id_lang);
                    $customer_email = $customer->email;
                } else {
                    $languageObj = $this->context->language;
                    $customer_email = $productComment->email;
                }
                $idLang = $languageObj->id ?: $this->context->language->id;
                $product = new Product($id_product, false, $idLang);
                $object = [
                    'og' => $this->qa ? 'question' : 'review',
                    't' => $this->qa ? $this->l('question', 'EtsRVProductCommentEntity') : $this->l('review', 'EtsRVProductCommentEntity')
                ];
                $templateVars = [
                    '{customer_name}' => $customer_name,
                    '{object}' => EtsRVCore::trans($object['og'], $languageObj->iso_code, 'EtsRVProductCommentEntity'),
                    '{content}' => isset($productComment->content[$idLang]) ? $productComment->content[$idLang] : (isset($originLang) && isset($originLang['content']) ? $originLang['content'] : ''),
                ];
                if (EtsRVEmailTemplate::isEnabled($template)) {
                    $templateVars['{product_link}'] = $this->context->link->getProductLink($product, $product->link_rewrite, $product->category, $product->ean13, $idLang);
                    $templateVars['{product_name}'] = $product->name;
                    EtsRVMail::send(
                        $idLang
                        , $template
                        , null
                        , $templateVars
                        , $customer_email
                        , $customer_name
                        , true
                        , isset($customer) ? $customer->id : 0
                        , 0
                        , $product->id
                        , $this->context->shop->id
                        , $productComment->question ? 0 : $productComment->id
                    );
                }

                if (!$this->qa && $voucher && isset($customer) && $cart_rule !== null && Validate::isLoadedObject($cart_rule) && EtsRVEmailTemplate::isEnabled('tocustomer_get_voucher')) {
                    $templateVars['{voucher_code}'] = $cart_rule->code;
                    $templateVars['{voucher_value}'] = trim(Configuration::get('ETS_RV_APPLY_DISCOUNT')) !== 'percent' ? Tools::displayPrice(Tools::convertPriceFull($cart_rule->reduction_amount, new Currency($cart_rule->reduction_currency), $this->context->currency)) . ' ' . ($cart_rule->reduction_tax !== 0 ? $this->l('(incl. tax)', 'EtsRVProductCommentEntity') : $this->l('(excl. tax)', 'EtsRVProductCommentEntity')) : $cart_rule->reduction_percent . '%';
                    $templateVars['{available_date}'] = EtsRVCartRule::getInstance()->getDateToString($cart_rule);
                    EtsRVMail::send(
                        $idLang
                        , 'tocustomer_get_voucher'
                        , sprintf(EtsRVEmailTemplate::getSubjectByLangShop('tocustomer_get_voucher', $idLang, $this->context->shop->id), $shop_name)
                        , $templateVars
                        , $customer->email
                        , $customer_name
                        , true
                        , $customer->id
                        , 0
                        , $product->id
                        , $this->context->shop->id
                        , $productComment->id
                        , $cart_rule->id
                    );
                }
            }
        }

        $json = [];
        if (!$this->_errors) {
            $productCommentArray = $productComment->toArray();
            $id_address = $productComment->id_customer > 0 ? Address::getFirstCustomerAddressId($productComment->id_customer) : 0;
            if ($id_address > 0) {
                $address = new Address($id_address);
                $country = new Country($address->id_country, $this->context->language->id);
            } else
                $country = null;
            $this->formatItem($id_product
                , $productCommentArray
                , $this->context->customer
                , $comment_content
                , $country
                , $comment_title
            );
            $productCommentArray['criterion'] = EtsRVProductComment::getGradesById((int)$productComment->id, $this->context->language->id);
            $productCommentArray['images'] = $this->qa ? [] : $this->module->displayPCListImages($productComment->id);
            $productCommentArray['videos'] = $this->qa ? [] : $this->module->displayPCListVideos($productComment->id);
            $productCommentArray['comments_nb'] = 0;
            $productCommentArray['comments'] = array();
            if (!$this->qa) {
                $productCommentArray['review_allowed'] = ($max = trim(Configuration::get('ETS_RV_MAXIMUM_REVIEW_PER_USER'))) !== '' && (int)$max <= EtsRVProductComment::getNbReviewsOfUser($productComment->id_product, $this->context) ? 0 : 1;
                $productCommentArray['nb_rate'] = EtsRVProductComment::getNbReviewsOfUser($productComment->id_product, $this->context, true);
            }
            if ($product_comment_order_added > 0) {
                $productCommentArray['id_order'] = $product_comment_order_added;
            }
            $json = [
                'product_comment' => $productCommentArray,
                'stats' => $this->module->getGradeStats($id_product),
                'voucher' => isset($voucher) ? $voucher : '',
            ];
            if (!$this->qa) {
                $json['photos'] = htmlentities($this->module->displayAllPhotos($id_product, true));
            }
        }
        $hasError = count($this->_errors) > 0;
        $json += [
            'success' => !$hasError,
            'error' => $hasError ? Tools::nl2br(implode(PHP_EOL, $this->_errors)) : false
        ];
        if ($productCommentId && (int)$productComment->validate) {
            $json['msg'] = $this->qa ? $this->l('Edit question successfully', 'EtsRVProductCommentEntity') : $this->l('Edit review successfully', 'EtsRVProductCommentEntity');
        }
        $this->ajaxRender(json_encode($json));
    }

    public function validateUpload($key, &$errors)
    {
        if (trim($key) == 'video') {
            $file_dest = _PS_IMG_DIR_ . $this->module->name . DIRECTORY_SEPARATOR . '/r/';
            $post_content_size = EtsRVTools::getServerVars('CONTENT_LENGTH');

            if (($post_max_size = EtsRVTools::getPostMaxSizeBytes()) && ($post_content_size > $post_max_size)) {
                $errors[] = sprintf($this->l('The uploaded file(s) exceeds the post_max_size directive in php.ini (%s > %s)', 'EtsRVProductCommentEntity'), EtsRVTools::formatBytes($post_content_size), EtsRVTools::formatBytes($post_max_size));
            } elseif (!@is_writable($file_dest) && !empty($_FILES[$key]['name'])) {
                $errors[] = sprintf($this->l('The directory "%s" is not able to write.', 'EtsRVProductCommentEntity'), $file_dest);
            } elseif (isset($_FILES[$key]) && !empty($_FILES[$key]['name'])) {
                for ($ik = 1; $ik <= (int)Configuration::get('ETS_RV_MAX_UPLOAD_VIDEO'); $ik++) {
                    if (!isset($_FILES[$key]['name'][$ik]) || !$_FILES[$key]['name'][$ik] || isset($_FILES[$key]['error'][$ik]) && (int)$_FILES[$key]['error'][$ik] === 4)
                        continue;
                    if ($uploadError = EtsRVTools::getInstance()->checkUploadError($_FILES[$key]['error'][$ik], $_FILES[$key]['name'][$ik])) {
                        $errors[] = $ik . '. ' . $uploadError;
                    } elseif ($_FILES[$key]['size'][$ik] > $post_max_size) {
                        $errors[] = $ik . '. ' . sprintf($this->l('File is too large. Maximum size allowed: %sMb', 'EtsRVProductCommentEntity'), EtsRVTools::formatBytes($post_max_size));
                    } elseif ($_FILES[$key]['size'][$ik] > Ets_reviews::DEFAULT_MAX_SIZE) {
                        $errors[] = $ik . '. ' . sprintf($this->l('File is too large. Current size is %1s, maximum size is %2s.', 'EtsRVProductCommentEntity'), $_FILES[$key]['size'][$ik], Ets_reviews::DEFAULT_MAX_SIZE);
                    } elseif (isset($_FILES[$key]['name'][$ik]) && $_FILES[$key]['name'][$ik]) {
                        if (!Validate::isFileName(EtsRVTools::formatFileName($_FILES[$key]['name'][$ik]))) {
                            $errors[] = $ik . '. ' . sprintf($this->l('File name "%s" is invalid', 'EtsRVProductCommentEntity'), $_FILES[$key]['name'][$ik]);
                        } else {
                            $type = Tools::strtolower(Tools::substr(strrchr($_FILES[$key]['name'][$ik], '.'), 1));
                            if (!in_array($type, array('mp4', 'webm', 'mov'))) {
                                $errors[] = $ik . '. ' . sprintf($this->l('File "%s" type is not allowed', 'EtsRVProductCommentEntity'), $_FILES[$key]['name'][$ik]);
                            }
                        }
                    }
                }
            }
            return !(count($errors) > 0);
        } else {
            $file_dest = _PS_IMG_DIR_ . $this->module->name . DIRECTORY_SEPARATOR . '/r/';
            $post_content_size = EtsRVTools::getServerVars('CONTENT_LENGTH');
            if (
                ($post_max_size = EtsRVTools::getPostMaxSizeBytes())
                && ($post_content_size > $post_max_size)
            ) {
                $errors[] = sprintf($this->l('The uploaded file(s) exceeds the post_max_size directive in php.ini (%s > %s)', 'EtsRVProductCommentEntity'), EtsRVTools::formatBytes($post_content_size), EtsRVTools::formatBytes($post_max_size));
            } elseif (
                !@is_writable($file_dest)
                && !empty($_FILES[$key]['name'])) {
                $errors[] = sprintf($this->l('The directory "%s" is not able to write.', 'EtsRVProductCommentEntity'), $file_dest);
            } elseif (
                isset($_FILES[$key])
                && !empty($_FILES[$key]['name'])
                && count($_FILES[$key]['name']) <= (int)Configuration::get('ETS_RV_MAX_UPLOAD_PHOTO')
            ) {
                $photos = array_keys($_FILES[$key]['name']);
                foreach ($photos as $ik) {
                    if (!isset($_FILES[$key]['name'][$ik]) || !$_FILES[$key]['name'][$ik] || isset($_FILES[$key]['error'][$ik]) && (int)$_FILES[$key]['error'][$ik] === 4)
                        continue;
                    if ($uploadError = EtsRVTools::getInstance()->checkUploadError($_FILES[$key]['error'][$ik], $_FILES[$key]['name'][$ik])) {
                        $errors[] = $ik . '. ' . $uploadError;
                    } elseif ($_FILES[$key]['size'][$ik] > $post_max_size) {
                        $errors[] = $ik . '. ' . sprintf($this->l('File is too large. Maximum size allowed: %sMb', 'EtsRVProductCommentEntity'), EtsRVTools::formatBytes($post_max_size));
                    } elseif ($_FILES[$key]['size'][$ik] > Ets_reviews::DEFAULT_MAX_SIZE) {
                        $errors[] = $ik . '. ' . sprintf($this->l('File is too large. Current size is %1s, maximum size is %2s.', 'EtsRVProductCommentEntity'), $_FILES[$key]['size'][$ik], Ets_reviews::DEFAULT_MAX_SIZE);
                    } else {
                        $types = array('jpg', 'gif', 'jpeg', 'png');
                        $type = Tools::strtolower(Tools::substr(strrchr($_FILES[$key]['name'][$ik], '.'), 1));
                        if (!Validate::isFileName(EtsRVTools::formatFileName($_FILES[$key]['name'][$ik]))) {
                            $errors[] = $ik . '. ' . sprintf($this->l('File name "%s" is invalid', 'EtsRVProductCommentEntity'), $_FILES[$key]['name'][$ik]);
                        } elseif (!ImageManager::isRealImage($_FILES[$key]['tmp_name'][$ik], $type) || !ImageManager::isCorrectImageFileExt($_FILES[$key]['name'][$ik], $types) || preg_match('/\%00/', $_FILES[$key]['name'][$ik])) {
                            $errors[] = $ik . '. ' . sprintf($this->l('Image "%s" format is not recognized, allowed formats are: %s'), $_FILES[$key]['name'][$ik], implode(', ', array_map(function ($ft) {
                                    return '.' . $ft;
                                }, $types)));
                        } elseif (!in_array($type, $types)) {
                            $errors[] = $ik . '. ' . sprintf($this->l('File "%s" type is not allowed', 'EtsRVProductCommentEntity'), $_FILES[$key]['name'][$ik]);
                        }
                    }
                }
            }

            return !(count($errors) > 0);
        }

    }

    /**
     * @param EtsRVProductComment $productComment
     * @param $key
     * @param $errors
     * @param null $images
     * @return bool
     */
    public function processUploadImage($productComment, $key, &$errors, &$images = null)
    {
        if (!$productComment instanceof EtsRVProductComment ||
            !Validate::isLoadedObject($productComment) ||
            trim($key) == ''
        ) {
            return false;
        }
        if (isset($_FILES[$key]['name']) && !empty($_FILES[$key]['name'])) {
            $file_dest = _PS_IMG_DIR_ . $this->module->name . DIRECTORY_SEPARATOR . '/r/';
            $imageTypes = EtsRVProductCommentImage::getImageTypes();
            $photos = array_keys($_FILES[$key]['name']);
            foreach ($photos as $ik) {
                if (!isset($_FILES[$key]['name'][$ik]) || !$_FILES[$key]['name'][$ik] || isset($_FILES[$key]['error'][$ik]) && (int)$_FILES[$key]['error'][$ik] > 0) {
                    continue;
                }
                $image = new EtsRVProductCommentImage();
                $image->id_ets_rv_product_comment = (int)$productComment->id;
                $image->position = (int)$image->getLastPosition((int)$productComment->id) + 1;
                $salt = Tools::strtolower(Tools::passwdGen(20));
                $type = Tools::strtolower(Tools::substr(strrchr($_FILES[$key]['name'][$ik], '.'), 1));
                list($sourceWidth, $sourceHeight) = @getimagesize($_FILES[$key]['tmp_name'][$ik]);
                if (isset($_FILES[$key]) && !empty($_FILES[$key]['tmp_name'][$ik]) && $sourceWidth > 0 && in_array($type, array('jpg', 'gif', 'jpeg', 'png'))) {
                    if (!($temp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS')) || !move_uploaded_file($_FILES[$key]['tmp_name'][$ik], $temp_name)) {
                        $errors[] = $this->l('An error occurred while uploading the image.', 'EtsRVProductCommentEntity');
                    } else {
                        foreach ($imageTypes as $imageType) {
                            $destinationWidth = $sourceWidth > $imageType['width'] ? $imageType['width'] : $sourceWidth;
                            $destinationHeight = Tools::ps_round($destinationWidth * $sourceHeight) / $sourceWidth;
                            if (!@ImageManager::resize($temp_name, $file_dest . $salt . '-' . Tools::stripslashes($imageType['name']) . '.jpg', $destinationWidth, $destinationHeight, 'jpg'))
                                $errors[] = sprintf($this->l('An error occurred while copying this image: %s', 'EtsRVProductCommentEntity'), Tools::stripslashes($imageType['name']));
                        }
                    }
                    if (file_exists($temp_name))
                        @unlink($temp_name);
                }
                if (!$errors) {
                    $image->image = $salt;
                    if (!$image->add()) {
                        foreach ($imageTypes as $imageType) {
                            $file_name = $file_dest . $salt . '-' . Tools::stripslashes($imageType['name']) . '.jpg';
                            if (@file_exists($file_name))
                                unlink($file_name);
                        }
                    } else
                        $images[$ik] = $image;
                }
            }
        }

        return !(count($errors) > 0);
    }

    public function postProductCommentImages()
    {
        $this->viewAccess();

        if (!($id = (int)Tools::getValue('id'))) {
            $this->_errors[] = $this->l('Review does not exist.', 'EtsRVProductCommentEntity');
        } elseif (!($productComment = new EtsRVProductComment($id)) || !$productComment->id) {
            $this->_errors[] = $this->l('Review does not exist or has been deleted.', 'EtsRVProductCommentEntity');
        } elseif (
            !$this->backOffice &&
            ($productComment->id_customer && (int)$this->context->cookie->id_customer && (int)$this->context->cookie->id_customer !== (int)$productComment->id_customer ||
                $productComment->id_guest && (int)$this->context->cookie->id_guest && (int)$this->context->cookie->id_guest !== (int)$productComment->id_guest)
        ) {
            $this->_errors[] = $this->l('You do not have permission to upload photo.', 'EtsRVProductCommentEntity');
        }
        $images = [];
        if (!$this->_errors && (int)Configuration::get('ETS_RV_UPLOAD_PHOTO_ENABLED') && (int)Configuration::get('ETS_RV_MAX_UPLOAD_PHOTO') && $this->validateUpload('image', $this->_errors)) {
            $this->processUploadImage($productComment, 'image', $this->_errors, $images);
        }
        $videos = [];
        if (!$this->_errors && (int)Configuration::get('ETS_RV_UPLOAD_VIDEO_ENABLED') && (int)Configuration::get('ETS_RV_MAX_UPLOAD_VIDEO') && $this->validateUpload('video', $this->_errors)) {
            $this->processUploadVideo($productComment, 'video', $this->_errors, $videos);
        }
        $hasError = $this->_errors ? 1 : 0;
        $this->ajaxRender(json_encode([
            'errors' => $hasError ? Tools::nl2br(implode(PHP_EOL, $this->_errors)) : false,
            'msg' => !$hasError ? $this->l('Upload photo successfully', 'EtsRVProductCommentEntity') : '',
            'images' => $images ? array_shift($images) : [],
            'videos' => $videos ? array_shift($videos) : [],
        ]));
    }

    public function productCommentMailApproved($productComment, $manual = false)
    {
        if (!$productComment)
            return false;

        if (!is_object($productComment) && Validate::isUnsignedInt($productComment))
            $productComment = new EtsRVProductComment((int)$productComment);

        if ((
                $productComment->question == 0 && ((int)Configuration::get('ETS_RV_MODERATE') || $manual) ||
                $productComment->question == 1 && ((int)Configuration::get('ETS_RV_QA_MODERATE') || $manual)
            ) && EtsRVEmailTemplate::isEnabled('tocustomer_approved')
        ) {
            if ($productComment->validate && ($productComment->id_customer || trim($productComment->customer_name) !== '' && trim($productComment->email) !== '')) {
                $originLang = EtsRVProductComment::getOriginLang($productComment->id);
                if ((int)$productComment->id_customer > 0) {
                    $customer = new Customer($productComment->id_customer);
                    $customer_name = $customer->firstname . ' ' . $customer->lastname;
                    $languageObj = new Language($customer->id_lang);
                    $customer_email = $customer->email;
                } else {
                    $customer_name = trim($productComment->customer_name);
                    $languageObj = new Language(isset($originLang['id_lang']) && (int)$originLang['id_lang'] > 0 ? (int)$originLang['id_lang'] : 0);
                    $customer_email = $productComment->email;
                }

                $idLang = $languageObj->id ?: $this->context->language->id;
                $product = new Product((int)$productComment->id_product, false, $idLang);

                $object = [
                    'og' => $productComment->question == 1 ? 'question' : 'review',
                    't' => $productComment->question == 1 ? $this->l('question', 'EtsRVProductCommentEntity') : $this->l('review', 'EtsRVProductCommentEntity'),
                ];
                $templateVars = [
                    '{customer_name}' => $customer_name,
                    '{product_link}' => $this->context->link->getProductLink($product, $product->link_rewrite, $product->category, $product->ean13, $idLang),
                    '{product_name}' => $product->name,
                    '{object}' => EtsRVCore::trans($object['og'], $languageObj->iso_code, 'EtsRVProductCommentEntity'),
                    '{content}' => !empty($productComment->content[$idLang]) ? $productComment->content[$idLang] : (isset($originLang['content']) ? $originLang['content'] : ''),
                ];
                return EtsRVMail::send(
                    $idLang
                    , 'tocustomer_approved'
                    , null
                    , $templateVars
                    , $customer_email
                    , $customer_name
                    , true
                    , (isset($customer) ? $customer->id : 0)
                    , 0
                    , $product->id
                    , $this->context->shop->id
                );
            }
        }
        return 0;
    }

    public function productCommentMailVoucher($productComment)
    {
        if (!$productComment instanceof EtsRVProductComment ||
            !$productComment->id ||
            $productComment->question ||
            !$productComment->id_customer ||
            $productComment->validate !== 1 ||
            !EtsRVEmailTemplate::isEnabled('tocustomer_get_voucher')
        ) {
            return false;
        }
        $cart_rule = null;
        $customer = new Customer((int)$productComment->id_customer);
        $customer_name = $customer->firstname . ' ' . $customer->lastname;
        $voucher = $this->qa ? '' : EtsRVCartRule::getInstance()->doShortCode($productComment->id_product, $productComment->grade, $customer, $cart_rule, $this->backOffice);

        $shop_name = Configuration::get('PS_SHOP_NAME');
        $languageObj = new Language($customer->id_lang);
        $idLang = $languageObj->id ?: $this->context->language->id;
        $templateVars = [
            '{customer_name}' => $customer_name,
            '{shop_name}' => Tools::safeOutput($shop_name),
        ];
        if ($voucher && $cart_rule !== null && Validate::isLoadedObject($cart_rule)) {
            $templateVars['{voucher_code}'] = $cart_rule->code;
            $tax_label = [
                'og' => $cart_rule->reduction_tax !== 0 ? '(incl. tax)' : '(excl. tax)',
                't' => $cart_rule->reduction_tax !== 0 ? $this->l('(incl. tax)', 'EtsRVProductCommentEntity') : $this->l('(excl. tax)', 'EtsRVProductCommentEntity'),
            ];
            $templateVars['{voucher_value}'] = trim(Configuration::get('ETS_RV_APPLY_DISCOUNT')) !== 'percent' ? Tools::displayPrice(Tools::convertPriceFull($cart_rule->reduction_amount, new Currency($cart_rule->reduction_currency), $this->context->currency)) . ' ' . EtsRVCore::trans($tax_label['og'], $languageObj->iso_code, 'EtsRVProductCommentEntity') : $cart_rule->reduction_percent . '%';
            $templateVars['{available_date}'] = EtsRVCartRule::getInstance()->getDateToString($cart_rule);
            EtsRVMail::send(
                $idLang
                , 'tocustomer_get_voucher'
                , sprintf(EtsRVEmailTemplate::getSubjectByLangShop('tocustomer_get_voucher', $idLang, $this->context->shop->id), $shop_name)
                , $templateVars
                , $customer->email
                , $customer_name
                , true
                , $customer->id
                , 0
                , 0
                , $this->context->shop->id
                , $productComment->id
                , $cart_rule->id
            );
        }
    }

    public function processUploadVideo($productComment, $key, &$errors, &$videos = null)
    {
        if (!Validate::isLoadedObject($productComment) ||
            !$key
        ) {
            return false;
        }
        if (!empty($_FILES[$key]['name'])) {
            $file_dest = _PS_IMG_DIR_ . $this->module->name . DIRECTORY_SEPARATOR . '/r/';
            for ($ik = 1; $ik <= (int)Configuration::get('ETS_RV_MAX_UPLOAD_VIDEO'); $ik++) {

                if (!isset($_FILES[$key]['name'][$ik]) || !$_FILES[$key]['name'][$ik] || isset($_FILES[$key]['error'][$ik]) && (int)$_FILES[$key]['error'][$ik] > 0)
                    continue;
                $video_type = $_FILES[$key]['type'][$ik];
                $video = new EtsRVProductCommentVideo();
                $video->id_ets_rv_product_comment = (int)$productComment->id;
                $video->position = (int)$video->getLastPosition((int)$productComment->id) + 1;
                $video->type = $video_type;
                $salt = Tools::strtolower(Tools::passwdGen(20));
                $type = Tools::strtolower(Tools::substr(strrchr($_FILES[$key]['name'][$ik], '.'), 1));
                $video_name = $salt . '.' . $type;
                if (isset($_FILES[$key]) && !empty($_FILES[$key]['tmp_name'][$ik]) && in_array($type, array('mp4', 'webm', 'mov'))) {
                    if (!move_uploaded_file($_FILES[$key]['tmp_name'][$ik], $file_dest . $video_name)) {
                        $errors[] = $this->l('An error occurred while uploading the video.', 'EtsRVProductCommentEntity');
                    }
                }
                if (!$errors) {
                    $video->video = $video_name;
                    if (!$video->add()) {
                        $file_name = $file_dest . $video_name;
                        if (@file_exists($file_name))
                            unlink($file_name);
                    } else
                        $videos[$ik] = $video;
                }
            }
        }
        return !(count($errors) > 0);
    }

    public function updateProductCommentUsefulness()
    {
        $this->viewAccess();

        if (!(int)Configuration::get('ETS_RV_' . $this->sf . 'USEFULNESS')) {
            $this->ajaxRender(json_encode([
                'success' => false,
                'error' => $this->l('This feature is not enabled.', 'EtsRVProductCommentEntity'),
            ]));
        }
        $customerId = (int)$this->context->cookie->id_customer;
        if ($this->employee <= 0 && $customerId <= 0) {
            $id_product = (int)Tools::getValue('id_product');
            $this->ajaxRender(json_encode([
                'success' => false,
                'error' => sprintf(
                    $this->l('You need to be %s or %s to give your appreciation of %s.', 'EtsRVProductCommentEntity')
                    , EtsRVTools::displayText($this->l('signed in', 'EtsRVProductCommentEntity'), 'a', ['href' => $this->context->link->getPageLink('authentication') . ($id_product ? '?back=' . $this->context->link->getProductLink($id_product) : ''), 'rel' => 'nofollow'])
                    , EtsRVTools::displayText($this->l('create an account', 'EtsRVProductCommentEntity'), 'a', ['href' => $this->context->link->getPageLink('authentication&create_account=1') . ($id_product ? '&back=' . $this->context->link->getProductLink($id_product) : ''), 'rel' => 'nofollow'])
                    , $this->qa ? $this->l('a question', 'EtsRVProductCommentEntity') : $this->l('a review', 'EtsRVProductCommentEntity')
                ),
            ]));
        }

        $id_product_comment = (int)Tools::getValue('id_ets_rv_product_comment');
        $usefulness = (int)Tools::getValue('usefulness');

        $productComment = new EtsRVProductComment($id_product_comment);
        if (!$productComment->id) {
            $this->ajaxRender(json_encode([
                'success' => false,
                'error' => $this->l('Cannot find the requested product review.', 'EtsRVProductCommentEntity'),
            ]));
        }
        $result = EtsRVProductComment::setCommentUsefulness(
            $id_product_comment
            , (bool)$usefulness
            , $customerId
            , $this->employee
            , $this->qa
        );

        if ($result) {
            if ($this->qa) {
                $content = $usefulness ? 'like_a_question' : 'dislike_a_question';
            } else {
                $content = $usefulness ? 'like_a_review' : 'dislike_a_review';
            }
            $this->addActivity($productComment, $this->qa ? EtsRVActivity::ETS_RV_TYPE_QUESTION : EtsRVActivity::ETS_RV_TYPE_REVIEW, $usefulness ? EtsRVActivity::ETS_RV_ACTION_LIKE : EtsRVActivity::ETS_RV_ACTION_DISLIKE, (int)Tools::getValue('id_product'), $content, $this->context);
        }

        $commentUsefulness = EtsRVProductCommentRepository::getInstance()->getProductCommentUsefulness($id_product_comment, $this->qa, isset($this->context->customer->id) ? $this->context->customer->id : 0, $this->employee);
        $template = 'person_' . ($usefulness ? '' : 'dis') . 'like';
        if ($result && EtsRVEmailTemplate::isEnabled($template)) {
            $this->productCommentMailToCustomer($id_product_comment
                , $customerId
                , $template
                , $this->employee
            );
        }
        $response = array_merge([
            'success' => true,
            'id_ets_rv_product_comment' => $id_product_comment,
        ], $commentUsefulness);
        $this->ajaxRender(json_encode($this->jsonExtra($response)));
    }

    public function productCommentMailToCustomer($id, $id_customer, $template, $id_employee = 0)
    {
        if (!$id ||
            !Validate::isUnsignedInt($id) ||
            $id_customer <= 0 && $id_employee <= 0 ||
            $id_customer > 0 && !Validate::isUnsignedInt($id_customer) ||
            $id_employee > 0 && !Validate::isUnsignedInt($id_employee) ||
            !$template
        ) {
            return false;
        }
        if (($product_comment = EtsRVProductComment::getData($id, $this->context->language->id)) &&
            (
                isset($product_comment['id_customer']) && (int)$product_comment['id_customer'] > 0 ||
                isset($product_comment['email']) && trim($product_comment['email']) !== ''
            ) &&
            (
                (int)$id_customer > 0 &&
                (
                    isset($product_comment['id_customer']) && (int)$product_comment['id_customer'] > 0 && (int)$product_comment['id_customer'] != (int)$id_customer ||
                    isset($product_comment['email']) && trim($product_comment['email']) !== ''
                ) ||
                $id_employee > 0
            )
        ) {
            if ($id_employee > 0) {
                $from = new Employee($id_employee);
            } else {
                $from = new Customer($id_customer);
            }
            $from_name = $from->firstname . ' ' . $from->lastname;

            if ((int)$product_comment['id_customer'] > 0) {
                $customer = new Customer((int)$product_comment['id_customer']);
                $customer_name = $customer->firstname . ' ' . $customer->lastname;
                $customer_email = $customer->email;
                $languageObj = new Language($customer->id_lang);
            } else {
                $customer_name = trim($product_comment['customer_name']);
                $customer_email = $product_comment['email'];
                $idLang = EtsRVProductComment::getOriginIdLang($id);
                $languageObj = new Language($idLang);
            }
            $idLang = $languageObj->id ?: $this->context->language->id;
            $product = new Product((int)$product_comment['id_product'], false, $idLang);
            $object = [
                'og' => $this->qa ? 'question' : 'review',
                't' => $this->qa ? $this->l('question', 'EtsRVProductCommentEntity') : $this->l('review', 'EtsRVProductCommentEntity')
            ];
            $templateVars = [
                '{product_link}' => $this->context->link->getProductLink($product, $product->link_rewrite, $product->category, $product->ean13, $idLang),
                '{content}' => isset($product_comment['content']) ? trim($product_comment['content']) : '',
                '{person_name}' => $customer_name,
                '{from_person_name}' => $from_name,
                '{product_name}' => $product->name,
                '{object}' => EtsRVCore::trans($object['og'], $languageObj->iso_code, 'EtsRVProductCommentEntity')
            ];
            return EtsRVMail::send(
                $idLang
                , $template
                , null
                , $templateVars
                , $customer_email
                , $customer_name
                , true
                , isset($customer) ? $customer->id : 0
                , 0
                , $product->id
                , $this->context->shop->id
                , $this->qa ? 0 : $id
            );
        }

        return 0;
    }

    private function addCommentGrades(EtsRVProductComment $productComment, array $criterions)
    {
        $averageGrade = 0;
        foreach ($criterions as $criterionId => $grade) {
            EtsRVProductComment::addGrade($productComment->id, $criterionId, $grade);
            $averageGrade += $grade;
        }
        $averageGrade /= count($criterions);
        $productComment->grade = $averageGrade;
        $productComment->update(true);
    }

    private function validateComment(EtsRVProductComment $productComment)
    {
        if (!$productComment->id_customer) {
            if (empty($productComment->customer_name)) {
                $this->_errors[] = $this->l('Customer name cannot be empty', 'EtsRVProductCommentEntity');
            } elseif (Tools::strlen($productComment->customer_name) > EtsRVProductComment::CUSTOMER_NAME_MAX_LENGTH) {
                $this->_errors[] = sprintf($this->l('Customer name cannot be longer than %s characters', 'EtsRVProductCommentEntity'), EtsRVProductComment::CUSTOMER_NAME_MAX_LENGTH);
            }
        } elseif ($errors = $productComment->validateFields(false, true)) {
            $this->_errors = ($errors !== true ? $errors : []);
        }
        return $this->_errors;
    }

    public function getProductCommentGrade()
    {
        $idProduct = (int)Tools::getValue('id_product');
        $validateOnly = $this->module->validateOnly($this->qa);

        $productCommentRepository = EtsRVProductCommentRepository::getInstance();
        $productCommentsNb = $productCommentRepository->getCommentsNumber($idProduct, $this->context->language->id, 0, $validateOnly, $this->backOffice, 0, $this->context, 0);
        $averageGrade = $productCommentRepository->getAverageGrade($idProduct, $this->context->language->id, $validateOnly, $this->backOffice, $this->context, 0);

        $this->ajaxRender(json_encode([
            'id_product' => $idProduct,
            'average_grade' => $averageGrade,
            'comments_nb' => $productCommentsNb,
        ]));
    }

    public function deleteProductComment()
    {
        $id = (int)Tools::getValue('id_product_comment');
        $this->viewAccess($id ? 'delete' : null);

        $productComment = new EtsRVProductComment($id);
        if (!$this->backOffice && (($productComment->id_customer && (int)$productComment->id_customer !== (int)$this->context->cookie->id_customer) || (!$productComment->id_customer && $productComment->id_guest && (int)$productComment->id_guest !== (int)$this->context->cookie->id_guest))) {
            $this->_errors[] = $productComment->question ? $this->l('Cannot delete question', 'EtsRVProductCommentEntity') : $this->l('Cannot delete product comment', 'EtsRVProductCommentEntity');
        } elseif (!$this->backOffice && $productComment->id && (int)$productComment->validate == 1 && !Configuration::get('ETS_RV_' . ($productComment->question ? 'QA_' : '') . 'CUSTOMER_DELETE_APPROVED')) {
            $this->_errors[] = $productComment->question ? $this->l('You are not allowed to delete this question', 'EtsRVProductCommentEntity') : $this->l('You are not allowed to delete this product comment', 'EtsRVProductCommentEntity');
        } elseif (!$productComment->delete()) {
            $this->_errors[] = $this->l('Delete failed.', 'EtsRVProductCommentEntity');
        }
        $repo = EtsRVProductCommentRepository::getInstance();
        $hasError = (bool)count($this->_errors);
        $response = [
            'success' => !$hasError,
            'error' => implode(Tools::nl2br("\n"), $this->_errors),
            'msg' => !$hasError ? sprintf($this->l('Comment #%1s has been deleted', 'EtsRVProductCommentEntity'), $productComment->id) : '',
            'review_enabled' => (int)Configuration::get('ETS_RV_REVIEW_ENABLED'),
            'question_enabled' => (int)Configuration::get('ETS_RV_QUESTION_ENABLED'),
            'nb_reviews' => $repo->getCommentsNumber($productComment->id_product, $this->context->language->id, 0, $this->module->validateOnly(), $this->backOffice, 0, $this->context, 0),
            'nb_questions' => $repo->getCommentsNumber($productComment->id_product, $this->context->language->id, 0, $this->module->validateOnly(1), $this->backOffice, 0, $this->context, 1),
        ];
        if (!$this->qa) {
            $response['stats'] = $this->module->getGradeStats($productComment->id_product);
            $response['review_allowed'] = ($max = trim(Configuration::get('ETS_RV_MAXIMUM_REVIEW_PER_USER'))) !== '' && (int)$max <= EtsRVProductComment::getNbReviewsOfUser($productComment->id_product, $this->context) ? 0 : 1;
            $response['nb_rate'] = EtsRVProductComment::getNbReviewsOfUser($productComment->id_product, $this->context, true);
            $response['photos'] = htmlentities($this->module->displayAllPhotos($productComment->id_product, true));
        }
        $this->ajaxRender(json_encode($this->jsonExtra($response)));
    }

    public function deleteProductCommentImage()
    {
        $id = (int)Tools::getValue('id_product_comment');
        $this->viewAccess($id ? 'delete' : null);

        $productComment = new EtsRVProductComment($id);
        if (!$this->backOffice && (($productComment->id_customer && (int)$productComment->id_customer !== (int)$this->context->cookie->id_customer) || (!$productComment->id_customer && $productComment->id_guest && (int)$productComment->id_guest !== (int)$this->context->cookie->id_guest))) {
            $this->_errors[] = $this->l('Cannot delete selected image', 'EtsRVProductCommentEntity');
        } elseif (!$this->backOffice && $productComment->id && (int)$productComment->validate == 1 && !Configuration::get('ETS_RV_CUSTOMER_DELETE_APPROVED')) {
            $this->_errors[] = $this->l('You are not allowed to delete this image.', 'EtsRVProductCommentEntity');
        } else {
            $productCommentImage = new EtsRVProductCommentImage((int)Tools::getValue('id_product_comment_image'));
            if (!$productCommentImage->delete())
                $this->_errors[] = $this->l('Delete failed.', 'EtsRVProductCommentEntity');
        }
        $hasError = (bool)count($this->_errors);
        $this->ajaxRender(json_encode([
            'success' => !$hasError,
            'error' => Tools::nl2br(implode(PHP_EOL, $this->_errors)),
            'msg' => !$hasError ? $this->l('Image has been deleted', 'EtsRVProductCommentEntity') : ''
        ]));
    }

    public function deleteProductCommentVideo()
    {
        $id = (int)Tools::getValue('id_product_comment');
        $this->viewAccess($id ? 'delete' : null);

        $productComment = new EtsRVProductComment($id);
        if (!$this->backOffice && (($productComment->id_customer && (int)$productComment->id_customer !== (int)$this->context->cookie->id_customer) || (!$productComment->id_customer && $productComment->id_guest && (int)$productComment->id_guest !== (int)$this->context->cookie->id_guest))) {
            $this->_errors[] = $this->l('Cannot delete selected video', 'EtsRVProductCommentEntity');
        } elseif (!$this->backOffice && $productComment->id && (int)$productComment->validate == 1 && !Configuration::get('ETS_RV_CUSTOMER_DELETE_APPROVED')) {
            $this->_errors[] = $this->l('You are not allowed to delete this video.', 'EtsRVProductCommentEntity');
        } else {
            $productCommentVideo = new EtsRVProductCommentVideo((int)Tools::getValue('id_product_comment_video'));
            if (!$productCommentVideo->delete())
                $this->_errors[] = $this->l('Delete failed.', 'EtsRVProductCommentEntity');
        }
        $hasError = (bool)count($this->_errors);
        $this->ajaxRender(json_encode([
            'success' => !$hasError,
            'error' => Tools::nl2br(implode(PHP_EOL, $this->_errors)),
            'msg' => !$hasError ? sprintf($this->l('Comment #%1s has been deleted', 'EtsRVProductCommentEntity'), $productComment->id) : ''
        ]));
    }

    public function privateProductComment()
    {
        $this->viewAccess();

        $productComment = new EtsRVProductComment((int)Tools::getValue('id_product_comment'));
        if (!$this->backOffice && (($productComment->id_customer && (int)$productComment->id_customer !== (int)$this->context->cookie->id_customer) || (!$productComment->id_customer && $productComment->id_guest && (int)$productComment->id_guest !== (int)$this->context->cookie->id_guest))) {
            $this->_errors[] = $this->qa ? $this->l('Cannot set this question to private', 'EtsRVProductCommentEntity') : $this->l('Cannot set this review to private', 'EtsRVProductCommentEntity');
        } else {
            $productComment->validate = EtsRVProductComment::STATUS_PRIVATE;
            if (!$productComment->update()) {
                $this->_errors[] = $this->l('Set to private failed.', 'EtsRVProductCommentEntity');
            }
        }
        $hasError = (bool)count($this->_errors);
        $response = [
            'success' => !$hasError,
            'error' => Tools::nl2br(implode(PHP_EOL, $this->_errors)),
            'msg' => !$hasError ? sprintf($this->l('The review #%1s has been set to private', 'EtsRVProductCommentEntity'), $productComment->id) : ''
        ];
        $this->ajaxRender(json_encode($this->jsonExtra($response)));
    }

    public function approveProductComment()
    {
        $this->viewAccess();

        $productComment = new EtsRVProductComment((int)Tools::getValue('id_product_comment'));
        $validateOld = $productComment->validate;
        if (!$this->backOffice && (($productComment->id_customer && (int)$productComment->id_customer !== (int)$this->context->cookie->id_customer) || (!$productComment->id_customer && $productComment->id_guest && (int)$productComment->id_guest !== (int)$this->context->cookie->id_guest))) {
            $this->_errors[] = $this->qa ? $this->l('Cannot approve this question', 'EtsRVProductCommentEntity') : $this->l('Cannot approve this review', 'EtsRVProductCommentEntity');
        } else {
            $productComment->validate = EtsRVProductComment::STATUS_APPROVE;
            if (!$productComment->update()) {
                $this->_errors[] = $this->l('Approve failed.', 'EtsRVProductCommentEntity');
            } else {
                $this->productCommentMailApproved($productComment);
                if ($validateOld == 0)
                    $this->productCommentMailVoucher($productComment);
            }
        }
        $hasError = (bool)count($this->_errors);
        $response = [
            'success' => !$hasError,
            'error' => Tools::nl2br(implode(PHP_EOL, $this->_errors)),
            'msg' => !$hasError ? sprintf($this->l('Review #%1s has been approved', 'EtsRVProductCommentEntity'), $productComment->id) : ''
        ];
        $this->ajaxRender(json_encode($this->jsonExtra($response)));
    }

    public function updateDateProductComment()
    {
        $productComment = new EtsRVProductComment((int)Tools::getValue('id_product_comment'));
        if (!$this->backOffice || !(int)Configuration::get('ETS_RV_' . (!$this->qa ? 'REVIEW' : 'QUESTION') . '_ENABLED')) {
            $this->_errors[] = $this->qa ? $this->l('Cannot update the adding date of question', 'EtsRVProductCommentEntity') : $this->l('Cannot update the adding date of review', 'EtsRVProductCommentEntity');
        } else {
            if (!($date_add = trim(Tools::getValue('date_add')))) {
                $this->_errors[] = $this->l('"Date add" value is required.', 'EtsRVProductCommentEntity');
            } elseif (!Validate::isDate($date_add) || strtotime($date_add) > time()) {
                $this->_errors[] = $this->l('"Date add" value is invalid.', 'EtsRVProductCommentEntity');
            } else {
                $productComment->date_add = date('Y-m-d H:i:s', strtotime($date_add));
                if (!$productComment->update()) {
                    $this->_errors[] = $this->l('Update failed.', 'EtsRVProductCommentEntity');
                }
            }
        }
        $hasError = (bool)count($this->_errors);
        $response = [
            'success' => !$hasError,
            'errors' => Tools::nl2br(implode(PHP_EOL, $this->_errors)),
            'msg' => !$hasError ? sprintf($this->l('The adding time of review #%1s is updated', 'EtsRVProductCommentEntity'), $productComment->id) : '',
            'date_add' => !$hasError ? $this->timeElapsedString($productComment->date_add) : '',
        ];
        $this->ajaxRender(json_encode($this->jsonExtra($response)));
    }
}