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

class EtsRVProductCommentCriterionRepository
{
    static $instance;

    public static function getInstance()
    {
        if (!self::$instance) {
            return (self::$instance = new self());
        }
        return self::$instance;
    }

    public function getByProduct($idProduct, $idLang, $id_product_comment = 0, $not_deleted = true)
    {
        $qb = new DbQuery();
        $qb
            ->select('pcc.id_ets_rv_product_comment_criterion, pccl.name')
            ->from('ets_rv_product_comment_criterion', 'pcc')
            ->leftJoin('ets_rv_product_comment_criterion_lang', 'pccl', 'pcc.id_ets_rv_product_comment_criterion = pccl.id_ets_rv_product_comment_criterion AND pccl.id_lang=' . (int)$idLang)
            ->leftJoin('ets_rv_product_comment_criterion_product', 'pccp', 'pcc.id_ets_rv_product_comment_criterion = pccp.id_ets_rv_product_comment_criterion')
            ->leftJoin('ets_rv_product_comment_criterion_category', 'pccc', 'pcc.id_ets_rv_product_comment_criterion = pccc.id_ets_rv_product_comment_criterion')
            ->leftJoin('category', 'c', 'pccc.id_category = c.id_category')
            ->leftJoin('category_product', 'cp', 'c.id_category = cp.id_category')
            ->where('((pcc.id_product_comment_criterion_type = ' . (int)EtsRVProductCommentCriterion::ENTIRE_CATALOG_TYPE . ') OR (pccp.id_product = ' . (int)$idProduct . ') OR (cp.id_product = ' . (int)$idProduct . '))')
            ->where('pcc.active = 1');
        if ($id_product_comment > 0) {
            $qb
                ->leftJoin('ets_rv_product_comment_grade', 'pcg', 'pcg.id_ets_rv_product_comment_criterion=pcc.id_ets_rv_product_comment_criterion AND pcg.id_ets_rv_product_comment=' . (int)$id_product_comment)
                ->where('pcc.deleted = 0 OR pcg.id_ets_rv_product_comment_criterion > 0');
        }
        if ($not_deleted) {
            $qb
                ->where('pcc.deleted = 0');
        }
        return Db::getInstance()->executeS($qb);
    }
}
