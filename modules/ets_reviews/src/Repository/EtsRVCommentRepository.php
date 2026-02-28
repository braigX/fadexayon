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

class EtsRVCommentRepository
{
    static $instance;
    private $multiLang;

    public function __construct($multiLang)
    {
        $this->multiLang = $multiLang;
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            return (self::$instance = new self((int)Configuration::get('ETS_RV_MULTILANG_ENABLED')));
        }
        return self::$instance;
    }

    public function paginate($productCommentId, $idLang, $page, $commentsPerPage, $validatedOnly = null, $backOffice = null, $firstOnly = false, $firstResult = false, $sortBy = false, Context $context = null, $question = 0, $answer = 0, $from_comment_id = 0)
    {
        $qb = new DbQuery();
        $qb
            ->select('pc.id_ets_rv_product_comment, pc.id_ets_rv_comment, pc.id_customer, pc.employee, IF(pc.employee, CONCAT(e.firstname, " ", e.lastname) , CONCAT(c.firstname, " ", c.lastname)) customer_name, pc.date_add, pc.upd_date, pcc.avatar, pc.validate, country.iso_code, pc.question, pc.answer, pc.useful_answer')
            ->select('IF(' . (int)$this->multiLang . ' != 0 AND pcl.`content` != "" AND pcl.`content` is NOT NULL, pcl.`content`, pol.`content`) content')
            ->select('IF(pc.employee, e.firstname , c.firstname) `firstname`, IF(pc.employee, e.lastname , c.lastname) `lastname`')
            ->from('ets_rv_comment', 'pc')
            ->leftJoin('ets_rv_comment_lang', 'pcl', 'pc.id_ets_rv_comment = pcl.id_ets_rv_comment AND pcl.id_lang = ' . (int)$idLang)
            ->leftJoin('ets_rv_comment_origin_lang', 'pol', 'pc.id_ets_rv_comment = pol.id_ets_rv_comment')
            ->leftJoin('customer', 'c', 'pc.id_customer = c.id_customer AND c.deleted = 0')
            ->leftJoin('address', 'a', 'c.id_customer = a.id_customer AND a.deleted = 0')
            ->leftJoin('country', 'country', 'a.id_country = country.id_country')
            ->leftJoin('ets_rv_product_comment_customer', 'pcc', 'pcc.id_customer = c.id_customer')
            ->leftJoin('employee', 'e', 'e.id_employee = pc.employee')
            ->where('pc.id_ets_rv_product_comment = ' . (int)$productCommentId)
            ->where('pc.deleted = 0')
            ->where('pc.question = ' . (int)$question)
            ->where('pc.answer = ' . (int)$answer)
            ->groupBy('pc.id_ets_rv_comment');

        if ($firstOnly) {
            $qb->limit(1);
        } elseif ($firstResult !== false) {
            $qb->limit($commentsPerPage, $firstResult);
        } else {
            $qb->limit($commentsPerPage, ($page - 1) * $commentsPerPage);
        }
        if ($validatedOnly !== null && $context !== null && isset($context->cookie->id_customer) && $context->cookie->id_customer || !$backOffice) {
            if (isset($context->cookie->id_customer) && $context->cookie->id_customer)
                $qb
                    ->where('IF(IF(' . (int)$context->cookie->id_customer . ' > 0, pc.id_customer=' . (int)$context->cookie->id_customer . ' OR (pc.employee != 0 AND (SELECT id_ets_rv_product_comment FROM `' . _DB_PREFIX_ . 'ets_rv_product_comment` WHERE id_ets_rv_product_comment=pc.id_ets_rv_product_comment AND id_customer=' . (int)$context->cookie->id_customer . ')), 0), 1, pc.validate' . ($validatedOnly !== null ? '=' . (int)$validatedOnly : '!=2') . ')');
            else
                $qb
                    ->where('pc.validate' . ($validatedOnly !== null ? '=' . (int)$validatedOnly : '!=2'));
        } elseif ($validatedOnly !== null) {
            $qb
                ->where('pc.validate =' . (int)$validatedOnly);
        }
        if ($answer)
            $qb->orderBy('pc.useful_answer DESC');
        if ($sortBy !== false && strpos($sortBy, 'grade') === false) {
            $sorts = explode('.', $sortBy);
            if (!empty($sorts[0])) {
                $orderWay = !empty($sorts[1]) ? $sorts[1] : 'DESC';
                if ($sorts[0] == 'usefulness') {
                    $qb
                        ->select('((SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'ets_rv_comment_usefulness` pcuc WHERE pcuc.`id_ets_rv_comment` = pc.`id_ets_rv_comment` AND pcuc.usefulness = 1) - (SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'ets_rv_comment_usefulness` pcuc WHERE pcuc.`id_ets_rv_comment` = pc.`id_ets_rv_comment` AND pcuc.usefulness = 0)) AS usefulness')
                        ->orderBy('usefulness ' . $orderWay);
                } else
                    $qb->orderBy('pc.' . $sorts[0] . ' ' . $orderWay);
            }
        } else
            $qb->orderBy('pc.date_add DESC');

        if ($from_comment_id > 0) {
            $qb
                ->where('pc.id_ets_rv_comment <= ' . (int)$from_comment_id);
        }
        return Db::getInstance()->executeS($qb);
    }

    public function getCommentUsefulness($commentId, $question = 0, $id_customer = 0, $id_employee = 0)
    {
        $qb = new DbQuery();
        $qb
            ->select('pcu.*')
            ->from('ets_rv_comment_usefulness', 'pcu')
            ->where('pcu.question = ' . (int)$question)
            ->where('pcu.id_ets_rv_comment = ' . (int)$commentId);

        $usefulnessInfos = [
            'usefulness' => 0,
            'total_usefulness' => 0,
            'current' => 0
        ];
        $customerAppreciations = Db::getInstance()->executeS($qb);
        foreach ($customerAppreciations as $customerAppreciation) {
            if ((int)$customerAppreciation['usefulness']) {
                ++$usefulnessInfos['usefulness'];
            }
            ++$usefulnessInfos['total_usefulness'];
            if ($usefulnessInfos['current'] < 1 && ($id_customer > 0 && $id_customer == (int)$customerAppreciation['id_customer'] || $id_employee > 0 && $id_employee == (int)$customerAppreciation['employee'])) {
                $usefulnessInfos['current'] = 1;
            }
        }

        return $usefulnessInfos;
    }

    public function getCommentsNumber($productCommentId, $idLang, $validatedOnly = null, $backOffice = null, Context $context = null, $question = 0, $answer = 0, $from_comment_id = 0, $operator = '>')
    {
        $qb = new DbQuery();
        $qb
            ->select('COUNT(pc.id_ets_rv_comment) AS commentNb')
            ->from('ets_rv_comment', 'pc')
            ->leftJoin('ets_rv_comment_lang', 'pcl', 'pc.id_ets_rv_comment = pcl.id_ets_rv_comment AND pcl.id_lang=' . (int)$idLang)
            ->where('pc.id_ets_rv_product_comment = ' . (int)$productCommentId)
            ->where('pc.deleted = 0')
            ->where('pc.question = ' . (int)$question)
            ->where('pc.answer = ' . (int)$answer);

        if ($validatedOnly !== null && $context !== null && isset($context->cookie->id_customer) && $context->cookie->id_customer || !$backOffice) {
            if (isset($context->cookie->id_customer) && $context->cookie->id_customer)
                $qb
                    ->where('IF(IF(' . (int)$context->cookie->id_customer . ' > 0, pc.id_customer=' . (int)$context->cookie->id_customer . ' OR (pc.employee != 0 AND (SELECT id_ets_rv_product_comment FROM `' . _DB_PREFIX_ . 'ets_rv_product_comment` WHERE id_ets_rv_product_comment=pc.id_ets_rv_product_comment AND id_customer=' . (int)$context->cookie->id_customer . ')), 0), 1, pc.validate' . ($validatedOnly !== null ? '=' . (int)$validatedOnly : '!=2') . ')');
            else
                $qb
                    ->where('pc.validate' . ($validatedOnly !== null ? '=' . (int)$validatedOnly : '!=2'));
        } elseif ($validatedOnly !== null) {
            $qb
                ->where('pc.validate =' . (int)$validatedOnly);
        }
        if ($from_comment_id > 0) {
            $qb
                ->where('pc.id_ets_rv_comment ' . trim($operator) . ' ' . (int)$from_comment_id);
        }
        return (int)Db::getInstance()->getValue($qb);
    }

    public function cleanCustomerData($customerId)
    {
        //We anonymize the customer comment by unlinking them (the name won't be visible any more but the grade and comment are still visible)
        Db::getInstance()->update('ets_rv_comment',
            array(
                'id_customer' => 0,
            )
            , 'pc.id_customer = ' . (int)$customerId
        );
        $qb = new DbQuery();
        $qb
            ->type('DELETE')
            ->from('ets_rv_comment_usefulness')
            ->where('id_customer = ' . (int)$customerId);
        Db::getInstance()->execute($qb);
    }

    public function getCustomerData($customerId, $langId)
    {
        $qb = new DbQuery();
        $qb
            ->select('pc.id_ets_rv_product_comment, pc.id_ets_rv_comment, pc.validate, pc.deleted, pcu.usefulness, pc.date_add, pc.question')
            ->select('IF(' . (int)$this->multiLang . ' != 0 AND pcl.`content` != "" AND pcl.`content` is NOT NULL, pcl.`content`, pol.`content`) content')
            ->from('ets_rv_comment', 'pc')
            ->leftJoin('ets_rv_comment_lang', 'pcl', 'pc.id_ets_rv_comment = pcl.id_ets_rv_comment AND pcl.id_lang = ' . (int)$langId)
            ->leftJoin('ets_rv_comment_origin_lang', 'pol', 'pc.id_ets_rv_comment = pol.id_ets_rv_comment')
            ->leftJoin('ets_rv_comment_usefulness', 'pcu', 'pc.id_ets_rv_comment = pcu.id_ets_rv_comment')
            ->where('pc.id_customer = ' . (int)$customerId)
            ->groupBy('pc.id_ets_rv_comment')
            ->orderBy('pc.date_add ASC');

        return Db::getInstance()->executeS($qb);
    }
}
