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

require_once dirname(__FILE__) . '/AdminEtsRVReviewsController.php';


class AdminEtsRVQuestionsController extends AdminEtsRVReviewsController
{
    public function __construct()
    {
        $this->qa = 1;
        parent::__construct();
    }

    public function initToolbarTitle()
    {
        $this->toolbar_title = $this->l('Questions and Answers', 'AdminEtsRVQuestionsController');
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
        $data['prop'] = 'Question';

        return $data;
    }

    public function getCommentsNumberAsField($productCommentId)
    {
        if ($productCommentId) {
            $repo = EtsRVCommentRepository::getInstance();
            $repo2 = EtsRVReplyCommentRepository::getInstance();
            $idLang = $this->context->language->id;
            return ($comments = $repo->getCommentsNumber((int)$productCommentId, $idLang, null, 1, $this->context, $this->qa) + $repo2->getRepliesNumber((int)$productCommentId, $idLang, null, 1, $this->context, $this->qa)) ? $comments . (($awaiting = $repo->getCommentsNumber((int)$productCommentId, $idLang, 0, 1, $this->context, $this->qa) + $repo2->getRepliesNumber((int)$productCommentId, $idLang, 0, 1, $this->context, $this->qa)) ? ' (' . $awaiting . ' ' . $this->l('Awaiting', 'AdminEtsRVQuestionsController') . ')' : '') : '--';
        }
    }

    public function getRepliesNumberAsField($productCommentId)
    {
        if ($productCommentId) {
            $repo = EtsRVCommentRepository::getInstance();
            $idLang = $this->context->language->id;

            return ($answers = $repo->getCommentsNumber((int)$productCommentId, $idLang, null, 1, $this->context, $this->qa, 1)) ? $answers . (($awaiting = $repo->getCommentsNumber((int)$productCommentId, $idLang, 0, 1, $this->context, $this->qa, 1)) ? ' (' . $awaiting . ' ' . $this->l('Awaiting', 'AdminEtsRVQuestionsController') . ')' : '') : '--';
        }
    }
}