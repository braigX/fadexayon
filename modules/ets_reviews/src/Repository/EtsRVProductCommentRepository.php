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

class EtsRVProductCommentRepository
{
    static $instance;
    private $guestCommentsAllowed;
    private $guestQuestionsAllowed;
    private $commentsMinimalTime;
    private $questionsMinimalTime;
    private $multiLang;
    private $publishAllLanguage;

    public function __construct($guestCommentsAllowed, $guestQuestionsAllowed, $commentsMinimalTime, $questionsMinimalTime, $multiLang, $publishAllLanguage)
    {
        $this->guestCommentsAllowed = (bool)$guestCommentsAllowed;
        $this->guestQuestionsAllowed = (bool)$guestQuestionsAllowed;
        $this->commentsMinimalTime = (int)$commentsMinimalTime;
        $this->questionsMinimalTime = (int)$questionsMinimalTime;
        $this->multiLang = $multiLang;
        $this->publishAllLanguage = $publishAllLanguage;
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            return (self::$instance = new self(
                EtsRVTools::reviewGrand('guest')
                , (int)Configuration::get('ETS_RV_QA_ALLOW_GUESTS')
                , (int)Configuration::get('ETS_RV_MINIMAL_TIME')
                , (int)Configuration::get('ETS_RV_QA_MINIMAL_TIME')
                , !empty(Context::getContext()->employee->id) ? 0 : (int)Configuration::get('ETS_RV_MULTILANG_ENABLED')
                , (int)Configuration::get('ETS_RV_PUBLISH_ALL_LANGUAGE')
            ));
        }
        return self::$instance;
    }

    public function paginate($productId, $idLang, $page, $commentsPerPage, $productCommentId = 0, $validatedOnly = null, $backOffice = null, $firstOnly = false, $firstResult = false, $sortBy = false, $grade = 0, Context $context = null, $question = 0, $has_video_image = false)
    {
        $qb = new DbQuery();
        $qb
            ->select('pc.id_product
                , pc.id_ets_rv_product_comment
                , pc.customer_name
                , pc.email
                , pc.id_customer
                , pc.date_add
                , pc.upd_date
                , pc.grade
                , pcc.avatar
                , pc.validate
                , country.iso_code
                , pc.id_guest
                , pc.question
                , pc.id_country
                , pc.verified_purchase
            ')
            ->select('IF(' . (int)$this->multiLang . ' != 0 AND pcl.`content` != "" AND pcl.`content` is NOT NULL, pcl.`content`, pol.`content`) content')
            ->select('c.firstname, c.lastname')
            ->select('IF(' . (int)$this->multiLang . ' != 0 AND pcl.`title` != "" AND pcl.`title` is NOT NULL, pcl.`title`, pol.`title`) title')
            ->from('ets_rv_product_comment', 'pc')
            ->leftJoin('ets_rv_product_comment_image', 'pcm', 'pc.id_ets_rv_product_comment=pcm.id_ets_rv_product_comment')
            ->leftJoin('ets_rv_product_comment_video', 'pcv', 'pc.id_ets_rv_product_comment=pcv.id_ets_rv_product_comment')
            ->leftJoin('ets_rv_product_comment_lang', 'pcl', 'pc.id_ets_rv_product_comment = pcl.id_ets_rv_product_comment AND pcl.id_lang = ' . (int)$idLang)
            ->leftJoin('ets_rv_product_comment_origin_lang', 'pol', 'pc.id_ets_rv_product_comment = pol.id_ets_rv_product_comment')
            ->leftJoin('ets_rv_product_comment_publish_lang', 'ppl', 'pc.id_ets_rv_product_comment = ppl.id_ets_rv_product_comment')
            ->leftJoin('customer', 'c', 'pc.id_customer = c.id_customer AND c.deleted = 0')
            ->leftJoin('address', 'a', 'c.id_customer = a.id_customer AND a.deleted = 0')
            ->leftJoin('country', 'country', 'a.id_country = country.id_country')
            ->leftJoin('ets_rv_product_comment_customer', 'pcc', 'pcc.id_customer = c.id_customer')
            ->where('pc.id_product = ' . (int)$productId)
            ->where('pc.deleted = 0')
            ->where('pc.question = ' . (int)$question)
            ->where('IF(' . (int)$this->multiLang . '=0, 1, pc.publish_all_language = 1 OR ' . (int)$this->publishAllLanguage . ' > 0 OR (ppl.id_lang = ' . (int)$idLang . ' AND ppl.id_ets_rv_product_comment is NOT NULL))')
            ->groupBy('pc.id_ets_rv_product_comment');

        if ($productCommentId)
            $qb->where('pc.id_ets_rv_product_comment=' . (int)$productCommentId);
        if ($has_video_image) {
            $qb->where('pcm.id_ets_rv_product_comment is not null OR pcv.id_ets_rv_product_comment is not null');
        }
        if ($firstOnly && $productCommentId)
            $qb->select('pc.id_guest, pol.`title` `origin_title`, pol.`content` `origin_content`');
        if ($firstOnly) {
            $qb->limit(1);
        } elseif ($firstResult !== false) {
            $qb->limit($commentsPerPage, $firstResult);
        } else {
            $qb->limit($commentsPerPage, ($page - 1) * $commentsPerPage);
        }

        $this->buildQueryValidated($qb, $backOffice, $validatedOnly, $context);

        if ($grade) {
            $qb->where('pc.grade = ' . (int)$grade);
        }
        if ($sortBy !== false) {
            $sorts = explode('.', $sortBy);
            if (!empty($sorts[0])) {
                $orderWay = !empty($sorts[1]) ? $sorts[1] : 'DESC';
                if ($sorts[0] == 'usefulness') {
                    $qb
                        ->select('((SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'ets_rv_product_comment_usefulness` pcuc WHERE pcuc.`id_ets_rv_product_comment` = pc.`id_ets_rv_product_comment` AND pcuc.usefulness = 1) - (SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'ets_rv_product_comment_usefulness` pcuc WHERE pcuc.`id_ets_rv_product_comment` = pc.`id_ets_rv_product_comment` AND pcuc.usefulness = 0)) AS usefulness')
                        ->orderBy('usefulness ' . $orderWay);
                } else
                    $qb->orderBy('pc.' . $sorts[0] . ' ' . $orderWay);
            }
        } else
            $qb->orderBy('pc.date_add DESC');
        return Db::getInstance()->executeS($qb);
    }

    public function getProductCommentUsefulness($productCommentId, $question = 0, $id_customer = 0, $id_employee = 0)
    {
        $qb = new DbQuery();
        $qb
            ->select('pcu.*')
            ->from('ets_rv_product_comment_usefulness', 'pcu')
            ->where('pcu.question = ' . (int)$question)
            ->where('pcu.id_ets_rv_product_comment = ' . (int)$productCommentId);

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

    public function getAverageGrade($productId, $idLang, $validatedOnly = null, $backOffice = null, Context $context = null, $question = 0, $grade = 0)
    {
        if ($context === null)
            $context = Context::getContext();
        $cacheId = 'EtsRVProductComment::getAverageGrade' . md5(
                (int)$productId .
                (int)$idLang .
                (int)$validatedOnly .
                (int)$backOffice .
                (int)$question .
                (int)$grade .
                $context->cookie->id_guest .
                $context->cookie->id_customer
            );
        if (!Cache::isStored($cacheId)) {
            /** @var DbQuery $qb */
            $qb = new DbQuery();
            $qb
                ->select('SUM(pc.grade) / COUNT(IF(pc.grade > 0, 1, NULL)) AS averageGrade')
                ->from('ets_rv_product_comment', 'pc')
                ->leftJoin('ets_rv_product_comment_lang', 'pcl', 'pc.id_ets_rv_product_comment = pcl.id_ets_rv_product_comment AND pcl.id_lang=' . (int)$idLang)
                ->leftJoin('ets_rv_product_comment_publish_lang', 'ppl', 'pc.id_ets_rv_product_comment = ppl.id_ets_rv_product_comment')
                ->where('pc.id_product = ' . (int)$productId)
                ->where('pc.deleted = 0')
                ->where('IF(' . (int)$this->multiLang . '=0, 1, pc.publish_all_language = 1 OR ' . (int)$this->publishAllLanguage . ' > 0 OR (ppl.id_lang = ' . (int)$idLang . ' AND ppl.id_ets_rv_product_comment is NOT NULL))')
                ->where('pc.question = ' . (int)$question);
            if ($grade) {
                $qb
                    ->select('COUNT(IF(pc.grade > 0, 1, NULL)) as countGrade')
                    ->where('ROUND(pc.grade)=' . (int)$grade);
            }
            $this->buildQueryValidated($qb, $backOffice, $validatedOnly, $context);
            $result = $grade ? Db::getInstance()->getRow($qb) : (float)Db::getInstance()->getValue($qb);
            Cache::store($cacheId, $result);
        } else {
            $result = Cache::retrieve($cacheId);
        }
        return $result;
    }

    public function getCategoryAverageGrade($categoryId, $idLang, $validatedOnly = null, $backOffice = null, Context $context = null, $question = 0, $grade = 0)
    {
        if ($context === null)
            $context = Context::getContext();
        $cacheId = 'EtsRVProductComment::getCategoryAverageGrade' . md5(
                (int)$categoryId .
                (int)$idLang .
                (int)$validatedOnly .
                (int)$backOffice .
                (int)$question .
                (int)$grade .
                $context->cookie->id_guest .
                $context->cookie->id_customer
            );
        if (!Cache::isStored($cacheId)) {
            /** @var DbQuery $qb */
            $qb = new DbQuery();
            $qb
                ->select('SUM(pc.grade) / COUNT(IF(pc.grade > 0, 1, NULL)) AS averageGrade')
                ->from('ets_rv_product_comment', 'pc')
                ->leftJoin('ets_rv_product_comment_lang', 'pcl', 'pc.id_ets_rv_product_comment = pcl.id_ets_rv_product_comment AND pcl.id_lang=' . (int)$idLang)
                ->leftJoin('ets_rv_product_comment_publish_lang', 'ppl', 'pc.id_ets_rv_product_comment = ppl.id_ets_rv_product_comment')
                ->leftJoin('category_product', 'cp', 'pc.id_product = cp.id_product')
                ->where('cp.id_category = ' . (int)$categoryId)
                ->where('pc.deleted = 0')
                ->where('IF(' . (int)$this->multiLang . '=0, 1, pc.publish_all_language = 1 OR ' . (int)$this->publishAllLanguage . ' > 0 OR (ppl.id_lang = ' . (int)$idLang . ' AND ppl.id_ets_rv_product_comment is NOT NULL))')
                ->where('pc.question = ' . (int)$question);
            if ($grade) {
                $qb
                    ->select('COUNT(IF(pc.grade > 0, 1, NULL)) as countGrade')
                    ->where('ROUND(pc.grade)=' . (int)$grade);
            }
            $this->buildQueryValidated($qb, $backOffice, $validatedOnly, $context);
            $result = $grade ? Db::getInstance()->getRow($qb) : (float)Db::getInstance()->getValue($qb);
            Cache::store($cacheId, $result);
        } else {
            $result = Cache::retrieve($cacheId);
        }
        return $result;
    }

    /**
     * @param DbQuery $qb
     */
    public function buildQueryValidated(&$qb, $backOffice, $validatedOnly, $context = null)
    {
        if ($context === null)
            $context = Context::getContext();
        if (!$backOffice && $context !== null && (isset($context->cookie->id_customer) && $context->cookie->id_customer || isset($context->cookie->id_guest) && $context->cookie->id_guest)) {
            if (isset($context->cookie->id_customer) && $context->cookie->id_customer)
                $qb->where('IF(pc.id_customer=' . (int)$context->cookie->id_customer . ', 1, pc.validate=' . ($validatedOnly !== null ? (int)$validatedOnly : 1) . ')');
            else
                $qb->where('IF(pc.id_guest=' . (int)$context->cookie->id_guest . ', 1, pc.validate=' . ($validatedOnly !== null ? (int)$validatedOnly : 1) . ')');
        } elseif (!$backOffice)
            $qb->where('pc.validate=' . ($validatedOnly !== null ? (int)$validatedOnly : 1));
    }

    public function getGradesNumber($productId, $idLang, $validatedOnly = null, $backOffice = null, Context $context = null, $grade = 0)
    {
        $cacheId = 'EtsRVProductCommentRepository::getGradesNumber' . md5(
                (int)$productId .
                (int)$idLang .
                ($validatedOnly != null ? (int)$validatedOnly : '') .
                ($backOffice != null ? (int)$backOffice : '') .
                (int)$grade
            );
        if (!Cache::isStored($cacheId)) {
            /** @var DbQuery $qb */
            $qb = new DbQuery();
            $qb
                ->select('COUNT(IF(pc.grade > 0, 1, NULL)) as gradeNb')
                ->from('ets_rv_product_comment', 'pc')
                ->leftJoin('ets_rv_product_comment_lang', 'pcl', 'pc.id_ets_rv_product_comment = pcl.id_ets_rv_product_comment AND pcl.id_lang=' . (int)$idLang)
                ->leftJoin('ets_rv_product_comment_publish_lang', 'ppl', 'pc.id_ets_rv_product_comment = ppl.id_ets_rv_product_comment')
                ->where('pc.id_product = ' . (int)$productId)
                ->where('pc.deleted = 0')
                ->where('IF(' . (int)$this->multiLang . '=0, 1, pc.publish_all_language = 1 OR ' . (int)$this->publishAllLanguage . ' > 0 OR (ppl.id_lang = ' . (int)$idLang . ' AND ppl.id_ets_rv_product_comment is NOT NULL))')
                ->where('pc.question = 0');
            if ($grade) {
                $qb
                    ->where('ROUND(pc.grade)=' . (int)$grade);
            }
            $this->buildQueryValidated($qb, $backOffice, $validatedOnly, $context);
            $result = (int)Db::getInstance()->getValue($qb);
            Cache::store($cacheId, $result);
        } else {
            $result = Cache::retrieve($cacheId);
        }
        return $result;
    }

    public function getCommentsNumber($productId, $idLang, $productCommentId = 0, $validatedOnly = null, $backOffice = null, $grade = 0, \Context $context = null, $question = 0, $has_video_image = false)
    {
        $cacheId = 'EtsRVProductCommentRepository::getCommentsNumber' . md5(
                (int)$productId .
                (int)$idLang .
                (int)$productCommentId .
                ($validatedOnly != null ? (int)$validatedOnly : '') .
                ($backOffice != null ? (int)$backOffice : '') .
                (int)$grade .
                (int)$question .
                (int)$has_video_image
            );
        if (!Cache::isStored($cacheId)) {
            $qb = new DbQuery();
            $qb
                ->select('COUNT(DISTINCT pc.id_ets_rv_product_comment) AS commentNb')
                ->from('ets_rv_product_comment', 'pc')
                ->leftJoin('ets_rv_product_comment_image', 'pcm', 'pc.id_ets_rv_product_comment=pcm.id_ets_rv_product_comment')
                ->leftJoin('ets_rv_product_comment_video', 'pcv', 'pc.id_ets_rv_product_comment=pcv.id_ets_rv_product_comment')
                ->leftJoin('ets_rv_product_comment_lang', 'pcl', 'pc.id_ets_rv_product_comment = pcl.id_ets_rv_product_comment AND pcl.id_lang=' . (int)$idLang)
                ->leftJoin('ets_rv_product_comment_publish_lang', 'ppl', 'pc.id_ets_rv_product_comment = ppl.id_ets_rv_product_comment')
                ->where('pc.id_product = ' . (int)$productId)
                ->where('pc.deleted = 0')
                ->where('pc.question = ' . (int)$question)
                ->where('IF(' . (int)$this->multiLang . '=0, 1, pc.publish_all_language = 1 OR ' . (int)$this->publishAllLanguage . ' > 0 OR (ppl.id_lang = ' . (int)$idLang . ' AND ppl.id_ets_rv_product_comment is NOT NULL))');
            if ($has_video_image) {
                $qb->where('pcm.id_ets_rv_product_comment is not null OR pcv.id_ets_rv_product_comment is not null');
            }
            if ($productCommentId)
                $qb->where('pc.id_ets_rv_product_comment=' . (int)$productCommentId);
            $this->buildQueryValidated($qb, $backOffice, $validatedOnly, $context);
            if ($grade)
                $qb->where('pc.grade = ' . (int)$grade);

            $result = (int)Db::getInstance()->getValue($qb);
            Cache::store($cacheId, $result);
        } else {
            $result = Cache::retrieve($cacheId);
        }
        return $result;
    }

    public function getCategoryCommentsNumber($categoryId, $idLang, $productCommentId = 0, $validatedOnly = null, $backOffice = null, $grade = 0, \Context $context = null, $question = 0, $has_video_image = false)
    {
        $cacheId = 'EtsRVProductCommentRepository::getCategoryCommentsNumber' . md5(
                (int)$categoryId .
                (int)$idLang .
                (int)$productCommentId .
                ($validatedOnly != null ? (int)$validatedOnly : '') .
                ($backOffice != null ? (int)$backOffice : '') .
                (int)$grade .
                (int)$question .
                (int)$has_video_image
            );
        if (!Cache::isStored($cacheId)) {
            $qb = new DbQuery();
            $qb
                ->select('COUNT(DISTINCT pc.id_ets_rv_product_comment) AS commentNb')
                ->from('ets_rv_product_comment', 'pc')
                ->leftJoin('ets_rv_product_comment_image', 'pcm', 'pc.id_ets_rv_product_comment=pcm.id_ets_rv_product_comment')
                ->leftJoin('ets_rv_product_comment_video', 'pcv', 'pc.id_ets_rv_product_comment=pcv.id_ets_rv_product_comment')
                ->leftJoin('ets_rv_product_comment_lang', 'pcl', 'pc.id_ets_rv_product_comment = pcl.id_ets_rv_product_comment AND pcl.id_lang=' . (int)$idLang)
                ->leftJoin('ets_rv_product_comment_publish_lang', 'ppl', 'pc.id_ets_rv_product_comment = ppl.id_ets_rv_product_comment')
                ->leftJoin('category_product', 'cp', 'pc.id_product = cp.id_product')
                ->where('cp.id_category = ' . (int)$categoryId)
                ->where('pc.deleted = 0')
                ->where('pc.question = ' . (int)$question)
                ->where('IF(' . (int)$this->multiLang . '=0, 1, pc.publish_all_language = 1 OR ' . (int)$this->publishAllLanguage . ' > 0 OR (ppl.id_lang = ' . (int)$idLang . ' AND ppl.id_ets_rv_product_comment is NOT NULL))');
            if ($has_video_image) {
                $qb->where('pcm.id_ets_rv_product_comment is not null OR pcv.id_ets_rv_product_comment is not null');
            }
            if ($productCommentId)
                $qb->where('pc.id_ets_rv_product_comment=' . (int)$productCommentId);
            $this->buildQueryValidated($qb, $backOffice, $validatedOnly, $context);
            if ($grade)
                $qb->where('pc.grade = ' . (int)$grade);

            $result = (int)Db::getInstance()->getValue($qb);
            Cache::store($cacheId, $result);
        } else {
            $result = Cache::retrieve($cacheId);
        }
        return $result;
    }

    public function isPostAllowed($productId, $idCustomer, $idGuest, $qa = 0)
    {
        if (!$idCustomer && (!$qa && !$this->guestCommentsAllowed || $qa && !$this->guestQuestionsAllowed)) {
            $postAllowed = false;
        } else {
            self::$cache_minimal_time = $this->getMinimalTime($productId, $idCustomer, $idGuest, $qa);
            $postAllowed = self::$cache_minimal_time <= 0;
        }

        return $postAllowed;
    }

    static $cache_minimal_time = null;

    public function getMinimalTime($productId, $idCustomer, $idGuest, $qa = 0)
    {
        if (self::$cache_minimal_time !== null)
            return self::$cache_minimal_time;

        $lastPost = null;
        if ($idCustomer) {
            $lastPost = $qa ? $this->getLastCustomerQA($productId, $idCustomer) : $this->getLastCustomerComment($productId, $idCustomer);
        } elseif ($idGuest) {
            $lastPost = $qa ? $this->getLastGuestCommentQA($productId, $idGuest) : $this->getLastGuestComment($productId, $idGuest);
        }
        if (null !== $lastPost && isset($lastPost['date_add']) && $lastPost['date_add'] !== '') {
            $minimalTime = $qa ? $this->questionsMinimalTime : $this->commentsMinimalTime;
            $nextTime = time() - strtotime($lastPost['date_add']);
            if ($nextTime < $minimalTime) {
                return $minimalTime - $nextTime;
            }
        }
        return 0;
    }

    public function getLastCustomerComment($productId, $idCustomer)
    {
        return $this->getLastComment(['id_product' => $productId, 'id_customer' => $idCustomer]);
    }

    public function getLastCustomerQA($productId, $idCustomer)
    {
        return $this->getLastComment(['id_product' => $productId, 'id_customer' => $idCustomer], 1);
    }

    public function getLastGuestComment($productId, $idGuest)
    {
        return $this->getLastComment(['id_product' => $productId, 'id_guest' => $idGuest]);
    }

    public function getLastGuestCommentQA($productId, $idGuest)
    {
        return $this->getLastComment(['id_product' => $productId, 'id_guest' => $idGuest], 1);
    }

    public function cleanCustomerData($customerId)
    {
        $qb = new DbQuery();
        $qb
            ->type('DELETE')
            ->from('ets_rv_product_comment_usefulness')
            ->where('id_customer = ' . (int)$customerId);
        Db::getInstance()->execute($qb);
    }

    public function getCustomerData($customerId, $langId)
    {
        $cacheId = 'EtsRVProductCommentRepository::getCustomerData' . md5(
                (int)$customerId .
                (int)$langId
            );
        if (!Cache::isStored($cacheId)) {
            $qb = new DbQuery();
            $qb
                ->select('pl.name, pc.id_product, pc.id_ets_rv_product_comment, pc.grade, pc.validate, pc.deleted, pcu.usefulness, pc.date_add, pc.question')
                ->select('IF(' . (int)$this->multiLang . ' != 0 AND pcl.`content` != "" AND pcl.`content` is NOT NULL, pcl.`content`, pol.`content`) content')
                ->select('IF(' . (int)$this->multiLang . ' != 0 AND pcl.`title` != "" AND pcl.`title` is NOT NULL, pcl.`title`, pol.`title`) title')
                ->from('ets_rv_product_comment', 'pc')
                ->leftJoin('ets_rv_product_comment_lang', 'pcl', 'pc.id_ets_rv_product_comment = pcl.id_ets_rv_product_comment AND pcl.id_lang = ' . (int)$langId)
                ->leftJoin('ets_rv_product_comment_origin_lang', 'pol', 'pc.id_ets_rv_product_comment = pol.id_ets_rv_product_comment')
                ->leftJoin('ets_rv_product_comment_publish_lang', 'ppl', 'pc.id_ets_rv_product_comment = ppl.id_ets_rv_product_comment')
                ->leftJoin('ets_rv_product_comment_usefulness', 'pcu', 'pc.id_ets_rv_product_comment = pcu.id_ets_rv_product_comment')
                ->leftJoin('product', 'p', 'pc.id_product = p.id_product')
                ->leftJoin('product_lang', 'pl', 'p.id_product = pl.id_product')
                ->leftJoin('lang', 'l', 'pl.id_lang = l.id_lang')
                ->where('pc.id_customer = ' . (int)$customerId)
                ->where('l.id_lang = ' . (int)$langId)
                ->groupBy('pc.id_ets_rv_product_comment')
                ->orderBy('pc.date_add ASC');
            $result = Db::getInstance()->executeS($qb);
            Cache::store($cacheId, $result);
        } else {
            $result = Cache::retrieve($cacheId);
        }
        return $result;
    }

    private function getLastComment(array $criteria, $qa = 0)
    {
        $qb = new DbQuery();
        $qb
            ->select('pc.*')
            ->from('ets_rv_product_comment', 'pc')
            ->where('pc.deleted = 0')
            ->where('pc.question = ' . (int)$qa)
            ->orderBy('pc.date_add DESC')
            ->limit(1);

        foreach ($criteria as $field => $value) {
            $qb
                ->where(sprintf('pc.%s = %s', $field, $value));
        }

        $comments = Db::getInstance()->executeS($qb);

        return empty($comments) ? [] : $comments[0];
    }
}
