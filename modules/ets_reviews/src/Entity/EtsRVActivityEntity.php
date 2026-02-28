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

class EtsRVActivityEntity extends EtsRVEntity
{
    public static function getInstance()
    {
        if (!isset(self::$_INSTANCE['ets_rv_activity']) || !self::$_INSTANCE['ets_rv_activity'] instanceof EtsRVActivityEntity) {
            self::$_INSTANCE['ets_rv_activity'] = new EtsRVActivityEntity();
        }

        return self::$_INSTANCE['ets_rv_activity'];
    }

    public function __construct()
    {
        parent::__construct();

        $this->module = Module::getInstanceByName('ets_reviews');
    }

    static $max_character = 255;

    public function displayUserName($username)
    {
        $attrs = [
            'class' => 'ets-rv-username' . (Configuration::get('ETS_RV_DESIGN_COLOR5') ? ' color5' : '')
        ];
        return EtsRVTools::displayText($username, 'span', $attrs);
    }

    public function activityProperties($content, &$item)
    {
        if (trim($content) === '')
            return $content;
        $content = $this->templateContent(trim($content), $this->context->language->iso_code ?: Language::getIsoById(Configuration::get('PS_LANG_DEFAULT')), 'EtsRVActivityEntity');
        // Product:
        $product = new Product((int)$item['id_product'], false, $this->context->language->id);
        if (!$product->id) {
            return sprintf($this->l('Product #1%s has been deleted', 'EtsRVActivityEntity'), (int)$item['id_product']);
        }

        //Username:
        if (isset($item['customer_name']) && trim($item['customer_name']) !== '') {
            $content = preg_replace('/%user%/', $this->displayUserName($item['customer_name']), $content);
        } else {
            $content = preg_replace('/%user%/', $this->displayUserName($this->l('Guest', 'EtsRVActivityEntity')), $content);
        }

        // Review & Question:
        if (isset($item['id_ets_rv_product_comment']) && ($id_product_comment = (int)$item['id_ets_rv_product_comment'])) {

            $content = preg_replace('/%product%/', $product->id ? EtsRVTools::displayText(Tools::truncateString($product->name, self::$max_character), 'a', ['href' => $this->context->link->getProductLink($product)]) : $this->toStrong(sprintf($this->l('Product #%1s has been deleted', 'EtsRVActivityEntity'), (int)$item['id_product'])), $content);
            $productComment = EtsRVProductComment::getData($id_product_comment, $this->context->language->id);
            $title = !empty($productComment['content']) ? Tools::truncateString($productComment['content'], self::$max_character) : '';

            $content = preg_replace('/%title%|%review%|%question%/', $title, $content);
        }

        // Comment & Answer:
        if (isset($item['id_ets_rv_comment']) && ($id_comment = (int)$item['id_ets_rv_comment'])) {

            $comment = EtsRVComment::getData($id_comment, $this->context->language->id);
            $title = !empty($comment['content']) ? Tools::truncateString($comment['content'], self::$max_character) : '';
            $content = preg_replace('/%title%|%comment%|%answer%/', $title, $content);

            if (isset($comment['id_ets_rv_product_comment']) && ($id_product_comment = (int)$comment['id_ets_rv_product_comment'])) {
                $productComment = EtsRVProductComment::getData($id_product_comment, $this->context->language->id);
                $content = preg_replace('/%review%|%question%/', !empty($productComment['content']) ? Tools::truncateString($productComment['content'], self::$max_character) : '', $content);
            } else {
                $content = preg_replace('/%review%|%question%/', '', $content);
            }
        }

        // Replied & Comment answer:
        if (isset($item['id_ets_rv_reply_comment']) && ($id_reply_comment = (int)$item['id_ets_rv_reply_comment'])) {

            $reply = EtsRVReplyComment::getData($id_reply_comment, $this->context->language->id);
            $title = !empty($reply['content']) ? Tools::truncateString($reply['content'], self::$max_character) : '';
            $content = preg_replace('/%title%|%reply%/', $title, $content);

            if (isset($reply['id_ets_rv_comment']) && ($id_comment = (int)$reply['id_ets_rv_comment'])) {
                $comment = EtsRVComment::getData($id_comment, $this->context->language->id);
                $content = preg_replace('/%comment%|%answer%/', !empty($comment['content']) ? Tools::truncateString($comment['content'], self::$max_character) : '', $content);
            } else {
                $content = preg_replace('/%comment%|%answer%/', '', $content);
            }
        }
        if (isset($id_product_comment) && $id_product_comment > 0 && trim($item['type']) == EtsRVActivity::ETS_RV_TYPE_REVIEW && trim($item['action']) == EtsRVActivity::ETS_RV_ACTION_REVIEW) {
            $criterion = array();
            $reviewGrade = EtsRVProductComment::getGradesById($id_product_comment, $this->context->language->id);
            if ($reviewGrade) {
                foreach ($reviewGrade as $grade) {
                    $criterion[(int)$grade['id_ets_rv_product_comment_criterion']] = $grade['grade'];
                }
            }
            $nb_grade = count($criterion);
            $this->context->smarty->assign([
                'id_product_comment' => $id_product_comment,
                'criterion' => $criterion,
                'grade' => $nb_grade > 0 ? array_sum($criterion) / $nb_grade : 0,
                'ETS_RV_DESIGN_COLOR1' => trim(Configuration::get('ETS_RV_DESIGN_COLOR1')),
            ]);
        }
        $this->context->smarty->assign([
            'content' => $content,
            'type' => $item['type'],
            'action' => $item['action'],
        ]);

        return $this->context->smarty->fetch($this->module->getLocalPath() . 'views/templates/admin/list-content-activity.tpl');
    }

    public function toStrong($content)
    {
        if (!$content)
            return '';
        return EtsRVTools::displayText($content, 'strong');
    }

    static $cache_activity_type = array();

    public function templateContent($template, $iso_code, $specific = null)
    {
        if (!self::$cache_activity_type) {
            self::$cache_activity_type = array(

                // Review
                'wrote_a_review_for_product' => $this->l('%user% wrote a review for product: %product% "%title%"', 'EtsRVActivityEntity'),
                'like_a_review' => $this->l('%user% liked a review: %review%', 'EtsRVActivityEntity'),
                'dislike_a_review' => $this->l('%user% disliked a review: %review%', 'EtsRVActivityEntity'),

                // Comment of review
                'commented_on_a_review' => $this->l('%user% commented on a review: "%title%"', 'EtsRVActivityEntity'),
                'like_a_comment' => $this->l('%user% liked a comment: %comment%', 'EtsRVActivityEntity'),
                'dislike_a_comment' => $this->l('%user% disliked a comment: %comment%', 'EtsRVActivityEntity'),

                // Reply
                'replied_to_a_comment' => $this->l('%user% replied to a comment: "%title%"', 'EtsRVActivityEntity'),
                'like_a_reply' => $this->l('%user% liked a reply: %reply%', 'EtsRVActivityEntity'),
                'dislike_a_reply' => $this->l('%user% disliked a reply: %reply%', 'EtsRVActivityEntity'),

                // Question
                'asked_a_question_about_product' => $this->l('%user% asked a question about product: %product% "%title%"', 'EtsRVActivityEntity'),
                'like_a_question' => $this->l('%user% liked a question: %question%', 'EtsRVActivityEntity'),
                'dislike_a_question' => $this->l('%user% disliked a question: %question%', 'EtsRVActivityEntity'),

                // Comment of question
                'commented_on_a_question' => $this->l('%user% commented on a question: "%title%"', 'EtsRVActivityEntity'),
                'like_comment_of_question' => $this->l('%user% liked a comment on a question: %comment%', 'EtsRVActivityEntity'),
                'dislike_comment_of_question' => $this->l('%user% disliked a comment on a question: %comment%', 'EtsRVActivityEntity'),

                // Answer
                'answered_to_a_question' => $this->l('%user% answered to a question: "%title%"', 'EtsRVActivityEntity'),
                'dislike_an_answer' => $this->l('%user% disliked an answer: %answer%', 'EtsRVActivityEntity'),
                'like_an_answer' => $this->l('%user% liked an answer: %answer%', 'EtsRVActivityEntity'),

                // Comment of answer
                'commented_on_an_answer' => $this->l('%user% commented on an answer: "%title%"', 'EtsRVActivityEntity'),
                'like_a_comment_answer' => $this->l('%user% liked a comment %reply%', 'EtsRVActivityEntity'),
                'dislike_a_comment_answer' => $this->l('%user% disliked a comment: %reply%', 'EtsRVActivityEntity'),

            );
        }

        if (!$iso_code || !Validate::isLangIsoCode($iso_code) || !isset(self::$cache_activity_type[$template])) {
            return $template;
        }

        return self::trans(self::$cache_activity_type[$template], $iso_code, $specific);
    }
}