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

class EtsRVProductComment extends EtsRVModel
{
    const TITLE_MAX_LENGTH = 100;

    const STATUS_APPROVE = 1;
    const STATUS_PRIVATE = 2;
    const STATUS_PENDING = 0;
    const STATUS_REFUSE = 3;
    const CUSTOMER_NAME_MAX_LENGTH = 64;
    const DISPLAY_CUSTOMER_FULL_NAME = 1;
    const DISPLAY_CUSTOMER_ACRONYM_FIRSTNAME = 2;

    const DISPLAY_CUSTOMER_ACRONYM_LASTNAME = 3;
    public $id;
    public $id_product;
    public $id_customer;
    public $id_guest;
    public $customer_name;
    public $email;
    public $title;
    public $content;
    public $grade = 0;
    public $validate = 0;
    public $question = 0;
    public $id_country = 0;
    public $verified_purchase;
    public $deleted = 0;
    public $date_add;
    public $ids_language;
    public $criterion;
    public $publish_all_language;
    public $upd_date;

    public static $definition = array(
        'table' => 'ets_rv_product_comment',
        'primary' => 'id_ets_rv_product_comment',
        'multilang' => true,
        'fields' => array(
            'id_product' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_customer' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_guest' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'customer_name' => array('type' => self::TYPE_STRING),
            'email' => array('type' => self::TYPE_STRING),
            'grade' => array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'question' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'validate' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'deleted' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'publish_all_language' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'id_country' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'verified_purchase' => array('type' => self::TYPE_STRING, 'validate' => 'isCatalogName'),
            'date_add' => array('type' => self::TYPE_DATE),
            'upd_date' => array('type' => self::TYPE_DATE),

            //Lang fields.
            'title' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml'),
            'content' => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'),//, 'size' => 65535
        ),
    );

    public static function findOneById($id)
    {
        return (int)Db::getInstance()->getValue('SELECT `id_ets_rv_product_comment` FROM `' . _DB_PREFIX_ . 'ets_rv_product_comment` WHERE `id_ets_rv_product_comment`=' . (int)$id);
    }

    public function toArray($usefulness = false)
    {
        $usefulnessInfos = $this->id && $usefulness ? EtsRVProductCommentRepository::getInstance()->getProductCommentUsefulness($this->id, $this->question) : array();
        return [
            'id_product' => $this->id_product,
            'id_ets_rv_product_comment' => $this->id,
            'id_customer' => $this->id_customer,
            'customer_name' => $this->customer_name,
            'date_add' => $this->date_add,
            'upd_date' => $this->upd_date,
            'grade' => $this->grade,
            'usefulness' => !empty($usefulnessInfos['usefulness']) ? (int)$usefulnessInfos['usefulness'] : 0,
            'total_usefulness' => !empty($usefulnessInfos['total_usefulness']) ? (int)$usefulnessInfos['total_usefulness'] : 0,
            'publish_all_language' => $this->publish_all_language,
            'question' => $this->question,
            'validate' => $this->validate,
            'id_guest' => $this->id_guest
        ];
    }

    public static function getNbReviewsOfUser($id_product, $context = null, $has_rate = false)
    {
        if (!$id_product ||
            !Validate::isUnsignedInt($id_product)) {
            return 0;
        }
        if ($context == null) {
            $context = Context::getContext();
        }
        $dq = new DbQuery();
        $dq
            ->select('COUNT(*)')
            ->from('ets_rv_product_comment')
            ->where('question=0')
            ->where('id_product=' . (int)$id_product);
        if (isset($context->customer->id) && $context->customer->id > 0 && $context->customer->isLogged()) {
            $dq
                ->where('id_customer=' . (int)$context->customer->id);
        } elseif (isset($context->cookie->id_guest) && (int)$context->cookie->id_guest > 0) {
            $dq
                ->where('id_guest=' . (int)$context->cookie->id_guest);
        } else
            return 0;
        if ($has_rate) {
            $dq
                ->where('grade > 0');
        }
        return (int)Db::getInstance()->getValue($dq);
    }

    public static function getNbReviews()
    {
        $dq = new DbQuery();
        $dq
            ->select('COUNT(*)')
            ->from('ets_rv_product_comment')
            ->where('question=0')
            ->where('deleted=0')
            ->where('validate=1');

        return (int)Db::getInstance()->getValue($dq);
    }

    public static function getAverageRate()
    {
        $dq = new DbQuery();
        $dq
            ->select('SUM(grade) / COUNT(IF(grade > 0, 1, NULL)) AS averageGrade')
            ->from('ets_rv_product_comment')
            ->where('question=0')
            ->where('deleted=0')
            ->where('validate=1');

        return (float)Db::getInstance()->getValue($dq);
    }

    static $st_products = [];

    public static function getLatestReviews($context = null)
    {
        if ($context == null) {
            $context = Context::getContext();
        }
        $limit = (int)Configuration::get('ETS_RV_NUMBER_OF_LAST_REVIEWS');
        if ($limit <= 0) {
            $limit = 8;
        }
        $multiLang = (int)Configuration::get('ETS_RV_MULTILANG_ENABLED');
        $publishAllLanguage = (int)Configuration::get('ETS_RV_PUBLISH_ALL_LANGUAGE');
        $dq = new DbQuery();
        $dq
            ->select('pc.*, IF(pc.`id_customer`, CONCAT(c.`firstname`, \' \', c.`lastname`), pc.customer_name) `customer_name`, pcc.`display_name`, pcc.`avatar`')
            ->select('IF(' . (int)$multiLang . ' != 0 AND pcl.`content` != "" AND pcl.`content` is NOT NULL, pcl.`content`, pol.`content`) content')
            ->select('IF(' . (int)$multiLang . ' != 0 AND pcl.`title` != "" AND pcl.`title` is NOT NULL, pcl.`title`, pol.`title`) title')
            ->from('ets_rv_product_comment', 'pc')
            ->leftJoin('customer', 'c', 'c.`id_customer`=pc.`id_customer`')
            ->leftJoin('ets_rv_product_comment_customer', 'pcc', 'c.`id_customer`=pcc.`id_customer`')
            ->leftJoin('ets_rv_product_comment_lang', 'pcl', 'pcl.`id_ets_rv_product_comment`=pc.`id_ets_rv_product_comment` AND pcl.`id_lang`=' . (int)$context->language->id)
            ->leftJoin('ets_rv_product_comment_origin_lang', 'pol', 'pc.id_ets_rv_product_comment = pol.id_ets_rv_product_comment')
            ->leftJoin('ets_rv_product_comment_publish_lang', 'ppl', 'pc.id_ets_rv_product_comment = ppl.id_ets_rv_product_comment')
            ->where('pc.`question`=0')
            ->where('pc.`deleted`=0')
            ->where('pc.`validate`=1')
            ->where('IF(' . (int)$multiLang . '=0, 1, pc.publish_all_language = 1 OR ' . (int)$publishAllLanguage . ' > 0 OR (ppl.id_lang = ' . (int)$context->language->id . ' AND ppl.id_ets_rv_product_comment is NOT NULL))')
            ->orderBy('pc.`id_ets_rv_product_comment` DESC')
            ->limit($limit);

        $res = Db::getInstance()->executeS($dq);
        if ($res) {
            $VERIFIED_PURCHASE_LABEL = trim(Configuration::get('ETS_RV_VERIFIED_PURCHASE_LABEL', $context->language->id));
            $ETS_RV_DISPLAY_NAME = (int)Configuration::get('ETS_RV_DISPLAY_NAME');
            $icons = EtsRVComment::getIcons();
            foreach ($res as &$re) {
                if (isset($re['id_product'])) {
                    if (!isset(self::$st_products[$re['id_product']])) {
                        $p = new Product($re['id_product'], true, $context->language->id);
                        $st_re['product_name'] = $p->name;
                        $cover = Product::getCover($p->id, $context);
                        $st_re['product_cover'] = $cover ? $context->link->getImageLink($p->link_rewrite, $cover['id_image'], EtsRVTools::getFormattedName('cart')) : '';
                        $st_re['product_link'] = $context->link->getProductLink($p, $p->link_rewrite, $p->category, $p->ean13, $context->language->id);
                        self::$st_products[$re['id_product']] = $st_re;
                    } else {
                        $st_re = self::$st_products[$re['id_product']];
                    }
                    $re = array_merge($re, $st_re);
                    $VERIFIED_PURCHASE = isset($re['verified_purchase']) && trim($re['verified_purchase']) !== '' ? trim($re['verified_purchase']) : 'auto';
                    $id_customer = isset($re['id_customer']) ? (int)$re['id_customer'] : 0;
                    $re['verify_purchase'] = $VERIFIED_PURCHASE == 'yes' || $id_customer && $VERIFIED_PURCHASE != 'no' && EtsRVProductComment::verifyPurchase((int)$p->id, $id_customer) && $VERIFIED_PURCHASE_LABEL !== '' ? EtsRVTools::getIcon('check') . ' ' . $VERIFIED_PURCHASE_LABEL : '';
                    if (!$re['customer_name'] || trim($re['customer_name']) == '') {
                        $re['customer_name'] = EtsRVTools::getInstance()->l('Deleted account', 'EtsRVProductComment');
                    }
                    if ($ETS_RV_DISPLAY_NAME !== EtsRVProductComment::DISPLAY_CUSTOMER_FULL_NAME && isset($re['customer_name']) && trim($re['customer_name']) !== '') {
                        $customer_name_tmp = explode(' ', $re['customer_name']);
                        if (count($customer_name_tmp) > 1) {
                            if ($ETS_RV_DISPLAY_NAME === EtsRVProductComment::DISPLAY_CUSTOMER_ACRONYM_FIRSTNAME)
                                $re['customer_name'] = Tools::substr($customer_name_tmp[0], 0, 1) . '. ' . $customer_name_tmp[1];
                            else
                                $re['customer_name'] = $customer_name_tmp[0] . ' ' . Tools::substr($customer_name_tmp[1], 0, 1) . '.';
                        }
                    }
                    $re['avatar'] = !empty($re['avatar']) && @file_exists(_PS_IMG_DIR_ . 'ets_reviews/a/' . trim($re['avatar'])) ? $context->link->getMediaLink(_PS_IMG_ . 'ets_reviews/a/' . $re['avatar']) : '';
                    if ($re['avatar'] === '') {
                        $re['avatar_caption'] = Tools::strtoupper(Tools::substr($re['customer_name'], 0, 1));
                        $re['avatar_color'] = EtsRVTools::geneColor($re['customer_name']);
                    }
                    $re['display_date_add'] = EtsRVProductCommentEntity::getInstance()->timeElapsedString($re['date_add']);
                    $re['content'] = str_replace(array_keys($icons), $icons, Tools::nl2br(Tools::truncateString($re['content'])));
                }
            }
        }

        return $res;
    }

    public static function getData($id, $idLang, $onlyId = false)
    {
        if (!$id ||
            !Validate::isUnsignedInt($id) ||
            !$onlyId && (!$idLang || !Validate::isUnsignedInt($idLang))
        ) {
            return false;
        }
        $multiLang = (int)Configuration::get('ETS_RV_MULTILANG_ENABLED');
        $publishAllLanguage = (int)Configuration::get('ETS_RV_PUBLISH_ALL_LANGUAGE');
        $cacheId = 'EtsRVProductComment::getData' . md5(
                (int)$id .
                (int)$idLang .
                (int)$onlyId .
                $multiLang .
                $publishAllLanguage
            );
        if (!Cache::isStored($cacheId)) {
            $dq = new DbQuery();
            $dq
                ->select('pc.id_product, pc.id_customer, pc.customer_name, pc.email, pc.question, pc.id_ets_rv_product_comment, pc.validate')
                ->from('ets_rv_product_comment', 'pc');
            if (!$onlyId) {
                $dq
                    ->select('IF(' . (int)$multiLang . ' != 0 AND pcl.`content` != "" AND pcl.`content` is NOT NULL, pcl.`content`, pol.`content`) content')
                    ->leftJoin('ets_rv_product_comment_lang', 'pcl', 'pc.id_ets_rv_product_comment = pcl.id_ets_rv_product_comment AND pcl.id_lang = ' . (int)$idLang)
                    ->leftJoin('ets_rv_product_comment_origin_lang', 'pol', 'pc.id_ets_rv_product_comment = pol.id_ets_rv_product_comment')
                    ->leftJoin('ets_rv_product_comment_publish_lang', 'ppl', 'pc.id_ets_rv_product_comment = ppl.id_ets_rv_product_comment')
                    ->where('IF(' . (int)$multiLang . '=0, 1, pc.publish_all_language = 1 OR ' . (int)$publishAllLanguage . ' > 0 OR (ppl.id_lang = ' . (int)$idLang . ' AND ppl.id_ets_rv_product_comment is NOT NULL))');
            }
            $dq
                ->where('pc.id_ets_rv_product_comment=' . (int)$id);

            $result = Db::getInstance()->getRow($dq);
            Cache::store($cacheId, $result);
        } else {
            $result = Cache::retrieve($cacheId);
        }
        return $result;
    }

    public static function getList($id_customer, $idLang, $getTotal = 0, $page = 1, $perPage = 10, $question = 0, $isStaff = false)
    {
        if (!Validate::isUnsignedInt($id_customer) ||
            !Validate::isUnsignedInt($idLang)
        ) {
            return false;
        }
        $multiLang = (int)Configuration::get('ETS_RV_MULTILANG_ENABLED');
        $qb = new DbQuery();
        if ($getTotal)
            $qb->select('COUNT(*)');
        else {
            $qb
                ->select('a.*, b.*')
                ->select('ROUND(a.grade, 1) `grade`,IF(c.id_customer, CONCAT(c.`firstname`, \' \',  c.`lastname`), a.customer_name) customer_name, pl.`name` `product_name`')
                ->select('IF(' . (int)$multiLang . ' != 0 AND b.`title` != "" AND b.`title` is NOT NULL, b.`title`, pol.`title`) title')
                ->select('CONCAT(LEFT(@subquery@, (@len@ - IFNULL(NULLIF(LOCATE(" ", REVERSE(LEFT(@subquery@, @len@))), 0) - 1, 0))), IF(LENGTH(@subquery@) > @len@, "...","")) `content`')
                ->select('
                    IF(a.publish_all_language, 1, (SELECT GROUP_CONCAT(cpl.id_lang SEPARATOR \',\') 
                        FROM `' . _DB_PREFIX_ . 'ets_rv_product_comment_publish_lang` cpl 
                        WHERE cpl.id_ets_rv_product_comment = a.id_ets_rv_product_comment)
                    ) as `publish_lang`
                ')
                ->select('IF(a.validate, 1, 0) `badge_success`, IF(a.validate, 0, 1) `badge_warning`')
                ->select('(@usefulness@ = 1) as total_like, (@usefulness@ = 0) as total_dislike')
                ->select('pl.link_rewrite');
        }
        $qb->from('ets_rv_product_comment', 'a');
        if (!$getTotal) {
            $qb
                ->leftJoin('ets_rv_product_comment_lang', 'b', 'a.id_ets_rv_product_comment = b.id_ets_rv_product_comment AND b.id_lang = ' . (int)$idLang)
                ->leftJoin('ets_rv_product_comment_origin_lang', 'pol', 'pol.id_ets_rv_product_comment = a.id_ets_rv_product_comment')
                ->leftJoin('customer', 'c', 'c.`id_customer` = a.`id_customer`')
                ->leftJoin('product_lang', 'pl', 'pl.`id_product` = a.`id_product` AND pl.`id_lang` = ' . (int)$idLang . Shop::addSqlRestrictionOnLang('pl'));
        }

        $qb
            ->where('a.deleted = 0')
            ->where('a.question = ' . (int)$question);
        if (!$isStaff)
            $qb->where('a.id_customer = ' . (int)$id_customer);

        if ($getTotal)
            return (int)Db::getInstance()->getValue($qb);
        $qb
            ->orderBy('a.date_add DESC')
            ->limit($perPage, ($page - 1) * $perPage);
        return Db::getInstance()->executeS(self::buildSQL($qb, $multiLang));

    }

    public static function buildSQL($qb, $multiLang)
    {
        return @preg_replace(['/@subquery@/i', '/@len@/i', '/@usefulness@/i'], [
            'IF(' . (int)$multiLang . ' != 0 AND b.`content` != "" AND b.`content` is NOT NULL, b.`content`, pol.`content`)',
            120,
            'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'ets_rv_product_comment_usefulness` pcu WHERE pcu.`id_ets_rv_product_comment` = a.`id_ets_rv_product_comment` AND pcu.`usefulness`'
        ], $qb->build());
    }

    public static function notApprove($question = 0, $children = 0)
    {
        if (!Validate::isBool($question)) {
            return 0;
        }
        $tables = [
            'product_comment',
        ];
        if ($children) {
            $tables = array_merge($tables, [
                'comment',
                'reply_comment'
            ]);
        }
        $qr = 'SELECT COUNT(id_ets_rv_@table@) FROM `' . _DB_PREFIX_ . 'ets_rv_@table@` WHERE validate=0 AND question=' . (int)$question;
        $awaitingNb = 0;
        foreach ($tables as $table) {
            $awaitingNb += (int)Db::getInstance()->getValue(@preg_replace('/@table@/i', $table, $qr));
        }
        return $awaitingNb;
    }

    public static function getProductCommentsNumber($ids)
    {
        if (!$ids ||
            !Validate::isArrayWithIds($ids)
        ) {
            return false;
        }
        $dq = new DbQuery();
        $dq
            ->select('COUNT(id_ets_rv_product_comment)')
            ->from('ets_rv_product_comment')
            ->where('validate=0')
            ->where('id_ets_rv_product_comment IN (' . implode(',', $ids) . ')');

        return (int)Db::getInstance()->getValue($dq);
    }

    public static function productCommentValidate($ids = array())
    {
        if (!$ids ||
            !Validate::isArrayWithIds($ids)
        ) {
            return false;
        }
        $dq = new DbQuery();
        $dq
            ->select('id_ets_rv_product_comment, validate')
            ->from('ets_rv_product_comment')
            ->where('id_ets_rv_product_comment IN (' . implode(',', $ids) . ')')
            ->where('validate = 0');
        $validate = array();
        if ($res = Db::getInstance()->executeS($dq)) {
            foreach ($res as $row) {
                $validate[(int)$row['id_ets_rv_product_comment']] = (int)$row['validate'];
            }
        }
        return $validate;
    }

    public static function approveProductComments($ids = array())
    {
        if (!$ids ||
            !Validate::isArrayWithIds($ids)
        ) {
            return false;
        }
        return
            Db::getInstance()->execute('
                UPDATE `' . _DB_PREFIX_ . 'ets_rv_product_comment`                 SET validate=1 
                WHERE id_ets_rv_product_comment IN (' . implode(',', $ids) . ')
            ');
    }

    public static function deleteProductComments($ids)
    {
        if (!$ids ||
            !Validate::isArrayWithIds($ids)
        ) {
            return false;
        }
        return Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'ets_rv_product_comment` WHERE id_ets_rv_product_comment IN (' . implode(',', $ids) . ')');
    }

    public static function deleteAllChildren($ids)
    {
        if (!$ids ||
            !Validate::isArrayWithIds($ids)
        ) {
            return false;
        }
        $tables = array(
            'lang',
            'grade',
            'usefulness',
            'publish_lang',
            'origin_lang',
        );
        $res = true;
        foreach ($tables as $table) {
            $res &= Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'ets_rv_product_comment_' . bqSQL($table) . '` WHERE id_ets_rv_product_comment IN (' . implode(',', $ids) . ');');
        }

        return $res;
    }

    public static function getGradesById($id, $idLang)
    {
        if (!$id ||
            !Validate::isUnsignedInt($id)
        ) {
            return false;
        }
        if (!$idLang)
            $idLang = Context::getContext()->language->id;
        $db = new DbQuery();
        $db
            ->select('cg.*, ccl.name')
            ->from('ets_rv_product_comment_grade', 'cg')
            ->leftJoin('ets_rv_product_comment_criterion_lang', 'ccl', 'ccl.id_ets_rv_product_comment_criterion = cg.id_ets_rv_product_comment_criterion AND ccl.id_lang=' . (int)$idLang)
            ->where('id_ets_rv_product_comment=' . (int)$id);
        return Db::getInstance()->executeS($db);
    }

    public function validate($validate = 1)
    {
        if (!Validate::isUnsignedId($this->id)) {
            return false;
        }
        $this->validate = $validate;
        Hook::exec('actionObjectProductCommentValidateAfter', array('object' => $this));

        return $this->update();
    }

    public static function getStatusById($id)
    {
        if (!$id ||
            !Validate::isUnsignedInt($id)
        ) {
            return 0;
        }
        $db = new DbQuery();
        $db
            ->select('validate')
            ->from('ets_rv_product_comment')
            ->where('id_ets_rv_product_comment=' . (int)$id);

        return Db::getInstance()->getValue($db);
    }

    public function delete()
    {
        if (!parent::delete()) {
            return false;
        }
        return
            $this->deleteGrades($this->id) &&
            $this->deleteUsefulness($this->id) &&
            $this->deletePublishLang($this->id) &&
            $this->deleteOriginLang($this->id) &&
            EtsRVProductCommentImage::deleteImages($this->id) &&
            EtsRVProductCommentOrder::deleteReviewed($this->id) &&
            $this->deleteCascade($this->id, 'reply_comment', 'comment', 'product_comment') &&
            $this->deleteCascade($this->id, 'comment', '', 'product_comment');

    }

    public static function deleteAllReviewByCustomer($id_customer)
    {
        if (!Validate::isUnsignedInt($id_customer))
            return false;
        $reviews = Db::getInstance()->executeS('SELECT id_ets_rv_product_comment FROM `' . _DB_PREFIX_ . 'ets_rv_product_comment` WHERE id_customer = ' . (int)$id_customer);
        if ($reviews) {
            foreach ($reviews as $review) {
                $productComment = new self((int)$review['id_ets_rv_product_comment']);
                if (!$productComment->delete())
                    return false;
            }
        }

        return true;
    }

    public static function savePublishLang($id_product_comment, array $languages)
    {
        if (!Validate::isUnsignedId($id_product_comment) ||
            empty($languages) ||
            !Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'ets_rv_product_comment_publish_lang` WHERE `id_ets_rv_product_comment` = ' . (int)$id_product_comment)) {
            return false;
        }
        $sql = 'INSERT INTO `' . _DB_PREFIX_ . 'ets_rv_product_comment_publish_lang`(`id_ets_rv_product_comment`, `id_lang`) VALUES';
        foreach ($languages as $idLang) {
            if ($idLang != 'all' && (int)$idLang > 0)
                $sql .= '(' . (int)$id_product_comment . ', ' . (int)$idLang . '),';
        }
        return Db::getInstance()->execute(rtrim($sql, ','));
    }

    public static function deletePublishLang($id_product_comment)
    {
        if (!Validate::isUnsignedId($id_product_comment)) {
            return false;
        }

        return Db::getInstance()->execute('
            DELETE FROM `' . _DB_PREFIX_ . 'ets_rv_product_comment_publish_lang`
            WHERE `id_ets_rv_product_comment` = ' . (int)$id_product_comment);

    }

    public static function getPublishLang($id_product_comment, $ids = false)
    {
        if (!$id_product_comment ||
            !Validate::isUnsignedId($id_product_comment)
        ) {
            return false;
        }
        $dq = new DbQuery();
        $dq
            ->from('ets_rv_product_comment_publish_lang')
            ->where('id_ets_rv_product_comment=' . (int)$id_product_comment);
        if ($ids) {
            $dq->select('GROUP_CONCAT(id_lang SEPARATOR \',\')');

            return Db::getInstance()->getValue($dq);
        }
        $dq->select('*');

        return Db::getInstance()->executeS($dq);
    }


    public static function saveOriginLang($id_product_comment, $id_lang, $title, $content, $back_office = false)
    {
        if (!Validate::isUnsignedId($id_product_comment) ||
            !Validate::isUnsignedId($id_lang)) {
            return false;
        }

        if ($idLang = (int)Db::getInstance()->getValue('SELECT `id_lang` FROM `' . _DB_PREFIX_ . 'ets_rv_product_comment_origin_lang` WHERE id_ets_rv_product_comment = ' . (int)$id_product_comment)) {
            if ($back_office && (int)Configuration::get('ETS_RV_MULTILANG_ENABLED'))
                return true;
            return Db::getInstance()->execute('
                UPDATE `' . _DB_PREFIX_ . 'ets_rv_product_comment_origin_lang` 
                SET `title` = "' . pSQL($title) . '",
                    `content` = "' . pSQL($content, true) . '"
                WHERE id_ets_rv_product_comment = ' . (int)$id_product_comment . '
            ');
        } elseif (!$idLang) {
            return Db::getInstance()->execute('
                INSERT INTO `' . _DB_PREFIX_ . 'ets_rv_product_comment_origin_lang`(
                    `id_ets_rv_product_comment`,
                    `id_lang`,
                    `title`,
                    `content`
                ) 
                VALUES(
                    ' . (int)$id_product_comment . ',
                    ' . (int)$id_lang . ',
                    "' . pSQL($title) . '",
                    "' . pSQL($content, true) . '")
               ');
        }
    }

    public static function deleteOriginLang($id_product_comment)
    {
        if (!$id_product_comment ||
            !Validate::isUnsignedId($id_product_comment)
        ) {
            return false;
        }

        return Db::getInstance()->execute('
            DELETE FROM `' . _DB_PREFIX_ . 'ets_rv_product_comment_origin_lang`
            WHERE `id_ets_rv_product_comment` = ' . (int)$id_product_comment);
    }

    public static function getOriginIdLang($id)
    {
        if (!$id || !Validate::isUnsignedId($id))
            return 0;
        $qd = new DbQuery();
        $qd
            ->select('id_lang')
            ->from('ets_rv_product_comment_origin_lang', 'pcl')
            ->where('pcl.id_ets_rv_product_comment=' . (int)$id);
        return (int)Db::getInstance()->getValue($qd);
    }

    public static function getOriginLang($id)
    {
        if (!$id ||
            !Validate::isUnsignedId($id)
        ) {
            return false;
        }
        $qd = new DbQuery();
        $qd
            ->select('*')
            ->from('ets_rv_product_comment_origin_lang', 'pcl')
            ->where('pcl.id_ets_rv_product_comment=' . (int)$id);
        return Db::getInstance()->getRow($qd);
    }

    public static function addGrade($id_ets_rv_product_comment, $id_ets_rv_product_comment_criterion, $grade)
    {
        if (!Validate::isUnsignedId($id_ets_rv_product_comment) ||
            !Validate::isUnsignedId($id_ets_rv_product_comment_criterion) ||
            !Validate::isUnsignedInt($grade)
        ) {
            return false;
        }
        if ((int)Db::getInstance()->getValue('SELECT `id_ets_rv_product_comment_criterion` FROM `' . _DB_PREFIX_ . 'ets_rv_product_comment_grade` WHERE `id_ets_rv_product_comment` = ' . (int)$id_ets_rv_product_comment . ' AND id_ets_rv_product_comment_criterion = ' . (int)$id_ets_rv_product_comment_criterion)) {
            return Db::getInstance()->update('ets_rv_product_comment_grade',
                array('grade' => (int)$grade),
                'id_ets_rv_product_comment = ' . (int)$id_ets_rv_product_comment . ' AND id_ets_rv_product_comment_criterion = ' . (int)$id_ets_rv_product_comment_criterion
            );
        } else {
            return Db::getInstance()->insert('ets_rv_product_comment_grade',
                array(
                    'id_ets_rv_product_comment' => (int)$id_ets_rv_product_comment,
                    'id_ets_rv_product_comment_criterion' => (int)$id_ets_rv_product_comment_criterion,
                    'grade' => (int)$grade
                )
            );
        }
    }

    public static function deleteGrades($id_product_comment)
    {
        if (!Validate::isUnsignedId($id_product_comment)) {
            return false;
        }

        return Db::getInstance()->execute('
		DELETE FROM `' . _DB_PREFIX_ . 'ets_rv_product_comment_grade`
		WHERE `id_ets_rv_product_comment` = ' . (int)$id_product_comment);
    }

    public static function addGrades($id_product_comment, $criterions)
    {
        if (!$id_product_comment ||
            !Validate::isUnsignedId($id_product_comment) ||
            !$criterions
        ) {
            return false;
        }
        if (!is_array($criterions)) {
            $criterions = array($criterions);
        }
        $query = 'INSERT INTO `' . _DB_PREFIX_ . 'ets_rv_product_comment_grade`(`id_ets_rv_product_comment`, `id_ets_rv_product_comment_criterion`, `grade`) VALUES';
        foreach ($criterions as $id_criterion => $grade) {
            $query .= '(' . (int)$id_product_comment . ', ' . (int)$id_criterion . ', ' . (int)$grade . '),';
        }
        return Db::getInstance()->execute(rtrim($query, ','));
    }

    public static function deleteUsefulness($id_product_comment, $id_customer = null, $employee = null, $question = null)
    {
        if (!Validate::isUnsignedId($id_product_comment) ||
            $id_customer !== null && !Validate::isUnsignedInt($id_customer) ||
            $employee !== null && !Validate::isUnsignedInt($employee) ||
            $question !== null && !Validate::isUnsignedInt($question)
        ) {
            return false;
        }

        return Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'ets_rv_product_comment_usefulness` WHERE `id_ets_rv_product_comment` = ' . (int)$id_product_comment . ($id_customer !== null ? ' AND `id_customer` = ' . (int)$id_customer : '') . ($employee !== null ? ' AND `employee` = ' . (int)$employee : '') . ($question !== null ? ' AND `question` = ' . (int)$question : ''));
    }

    /**
     * @param $id_product_comment
     * @param $usefulness
     * @param $id_customer
     * @param int $employee
     * @param int $question
     * @return bool
     */
    public static function setCommentUsefulness($id_product_comment, $usefulness, $id_customer, $employee = 0, $question = 0)
    {
        if (!self::isAlreadyUsefulness($id_product_comment, $id_customer, $employee, $question)) {
            return Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'ets_rv_product_comment_usefulness` (`id_ets_rv_product_comment`, `usefulness`, `id_customer`, `employee`, `question`) VALUES(' . (int)$id_product_comment . ', ' . (int)$usefulness . ', ' . (int)$id_customer . ', ' . (int)$employee . ', ' . (int)$question . ')');
        } elseif (self::isAlreadyUsefulness($id_product_comment, $id_customer, $employee, $question, $usefulness)) {
            return !self::deleteUsefulness($id_product_comment, $id_customer, $employee, $question);
        } else {
            return Db::getInstance()->execute('
                UPDATE `' . _DB_PREFIX_ . 'ets_rv_product_comment_usefulness` SET `usefulness` = ' . (int)$usefulness . '
                WHERE `id_ets_rv_product_comment` = ' . (int)$id_product_comment . ' AND `id_customer` = ' . (int)$id_customer . ' AND `employee` = ' . (int)$employee . ' AND `question` = ' . (int)$question
            );
        }
    }

    /**
     * @param $id_product_comment
     * @param $id_customer
     * @param int $employee
     * @param int $question
     * @param null $usefulness
     * @return bool
     */
    public static function isAlreadyUsefulness($id_product_comment, $id_customer, $employee = 0, $question = 0, $usefulness = null)
    {
        return (bool)Db::getInstance()->getValue('SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'ets_rv_product_comment_usefulness` WHERE `id_customer` = ' . (int)$id_customer . ' AND `id_ets_rv_product_comment` = ' . (int)$id_product_comment . ' AND employee=' . (int)$employee . ' AND question=' . (int)$question . ($usefulness !== null ? ' AND usefulness=' . (int)$usefulness : ''));
    }

    public static function verifyPurchase($id_product, $id_customer)
    {
        if (!($order_states = Configuration::get('ETS_RV_VERIFY_PURCHASE')) ||
            !Validate::isUnsignedId($id_customer) ||
            !Validate::isUnsignedId($id_product)) {
            return false;
        }
        $cacheId = 'EtsRVProductComment::verifyPurchase' . md5(
                (int)$id_product .
                (int)$id_customer .
                trim($order_states)
            );
        if (!Cache::isStored($cacheId)) {
            $result = (bool)Db::getInstance()->getValue('SELECT o.id_order FROM `' . _DB_PREFIX_ . 'orders` o JOIN `' . _DB_PREFIX_ . 'order_detail` od ON(o.id_order = od.id_order) WHERE o.id_customer = ' . (int)$id_customer . ' AND od.product_id = ' . (int)$id_product . ' AND o.current_state IN(' . pSQL($order_states) . ')');
            Cache::store($cacheId, $result);
        } else {
            $result = Cache::retrieve($cacheId);
        }
        return $result;
    }

    public static function findProducts($query, $excludeIds, $excludePackItself = false, $excludeVirtual = 0, $exclude_packs = 0, Context $context = null)
    {
        if ($excludeIds && !Validate::isArrayWithIds($excludeIds)) {
            return false;
        }
        if (!$context) {
            $context = Context::getContext();
        }
        if (!$query or $query == '' or Tools::strlen($query) < 1) {
            die();
        }
        if ($pos = strpos($query, ' (ref:')) {
            $query = Tools::substr($query, 0, $pos);
        }
        $sql = '
            SELECT p.`id_product`, pl.`link_rewrite`, p.`reference`, pl.`name`, MAX(image_shop.`id_image`) id_image, il.`legend`, p.`cache_default_attribute`
            FROM `' . _DB_PREFIX_ . 'product` p
            ' . Shop::addSqlAssociation('product', 'p') . '
            LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (pl.id_product = p.id_product AND pl.id_lang = ' . (int)$context->language->id . Shop::addSqlRestrictionOnLang('pl') . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'image` i ON (i.`id_product` = p.`id_product`)
            LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop ON (i.`id_image` = image_shop.`id_image` AND image_shop.cover=1 AND image_shop.id_shop=' . (int)$context->shop->id . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int)$context->language->id . ')
            WHERE (pl.name LIKE \'%' . pSQL($query) . '%\' OR p.reference LIKE \'%' . pSQL($query) . '%\')' .
            (!empty($excludeIds) ? ' AND p.id_product NOT IN (' . $excludeIds . ') ' : ' ') .
            (!empty($excludePackItself) ? ' AND p.id_product <> ' . $excludePackItself . ' ' : ' ') .
            ($excludeVirtual ? 'AND NOT EXISTS (SELECT 1 FROM `' . _DB_PREFIX_ . 'product_download` pd WHERE (pd.id_product = p.id_product))' : '') .
            ($exclude_packs ? 'AND (p.cache_is_pack IS NULL OR p.cache_is_pack = 0)' : '') .
            ' GROUP BY p.id_product
        ';

        return Db::getInstance()->executeS($sql);
    }

    public static function getActivityList($id_customer, $nb = 0, $page = 1, $perPage = 10, Context $context = null)
    {
        if (!$id_customer)
            return [];
        if (!$context) {
            $context = Context::getContext();
        }
        $dq = new DbQuery();
        if ($nb)
            $dq->select('COUNT(*)');
        else
            $dq
                ->select('a.*, IF(c.id_customer, CONCAT(c.`firstname`, \' \',  c.`lastname`), IF(e.id_employee, CONCAT(e.`firstname`, \' \',  e.`lastname`), \'\')) customer_name')
                ->select('c.id_customer, e.id_employee');
        $dq
            ->from('ets_rv_activity', 'a')
            ->leftJoin('customer', 'c', 'c.id_customer = a.id_customer')
            ->leftJoin('employee', 'e', 'e.id_employee = a.employee')
            ->where('IF(c.id_customer != 0, a.id_customer=' . (int)$id_customer . ', IF(e.id_employee, a.employee=' . (int)$id_customer . ', 0))');

        if (!isset($context->cookie->id_customer) || (int)$context->cookie->id_customer != (int)$id_customer) {
            $dq
                ->leftJoin('ets_rv_product_comment', 'pc', 'pc.id_ets_rv_product_comment = a.id_ets_rv_product_comment')
                ->leftJoin('ets_rv_comment', 'cm', 'cm.id_ets_rv_comment = a.id_ets_rv_comment')
                ->leftJoin('ets_rv_reply_comment', 'rc', 'rc.id_ets_rv_reply_comment = a.id_ets_rv_reply_comment')
                ->where('IF(pc.id_ets_rv_product_comment is NOT NULL, pc.validate = 1, 1) AND IF(cm.id_ets_rv_comment is NOT NULL, cm.validate = 1, 1) AND IF(rc.id_ets_rv_reply_comment is NOT NULL, rc.validate = 1, 1)');
        }
        if ($nb) {
            return (int)Db::getInstance()->getValue($dq);
        }
        $dq
            ->orderBy('a.`id_ets_rv_activity` DESC')
            ->limit($perPage, ($page - 1) * $perPage);

        return Db::getInstance()->executeS($dq);
    }

    public static function isPurchased($id_customer, $id_product, $has_order_state = true)
    {
        if (!$id_customer || !Validate::isUnsignedInt($id_customer) ||
            !$id_product || !Validate::isUnsignedInt($id_product)
        ) {
            return false;
        }

        $order_states = trim(Configuration::get('ETS_RV_VERIFY_PURCHASE'));
        $cacheId = 'EtsRVProductComment::isPurchased' . md5($order_states . (int)$id_customer . (int)$has_order_state);
        if (!Cache::isStored($cacheId)) {
            $query = '
            SELECT o.id_order FROM `' . _DB_PREFIX_ . 'orders` o 
            JOIN `' . _DB_PREFIX_ . 'order_detail` od ON (o.id_order = od.id_order) 
            WHERE o.id_customer=' . (int)$id_customer . ' AND od.product_id=' . (int)$id_product
                . ($order_states && $has_order_state ? ' AND o.current_state IN (' . implode(',', array_map('intval', explode(',', $order_states))) . ')' : '');

            $result = (int)Db::getInstance()->getValue($query) > 0;
            Cache::store($cacheId, $result);
        } else {
            $result = Cache::retrieve($cacheId);
        }
        return $result;

    }

    public static function updateNewLanguage($idLang)
    {
        if (!$idLang ||
            !Validate::isUnsignedInt($idLang)
        ) {
            return false;
        }
        $tables = [
            'product_comment'
        ];
        $res = true;
        foreach ($tables as $table) {
            $res &= Db::getInstance()->execute('
                INSERT INTO `' . _DB_PREFIX_ . 'ets_rv_' . bqSQL($table) . '_publish_lang` (`id_ets_rv_' . bqSQL($table) . '`, `id_lang`)
                SELECT `id_ets_rv_' . bqSQL($table) . '`, ' . (int)$idLang . ' AS  `id_lang`
                FROM `' . _DB_PREFIX_ . 'ets_rv_' . bqSQL($table) . '_publish_lang`
                GROUP BY `id_ets_rv_' . bqSQL($table) . '`;
            ');
        }

        return $res;
    }

    public static function getOrderStateByIds($order_state_ids, $context = null)
    {
        if (!$order_state_ids ||
            !Validate::isArrayWithIds($order_state_ids)
        ) {
            return false;
        }
        if ($context == null) {
            $context = Context::getContext();
        }
        $cacheId = 'EtsRVProductComment::getOrderStateByIds' . md5(implode('', $order_state_ids) . $context->language->id);
        if (!Cache::isStored($cacheId)) {
            $dq = new DbQuery();
            $dq
                ->select('os.*, osl.name')
                ->from('order_state', 'os')
                ->leftJoin('order_state_lang', 'osl', 'os.id_order_state = osl.id_order_state AND osl.id_lang=' . (int)$context->language->id)
                ->where('os.id_order_state IN (' . implode(',', $order_state_ids) . ')');
            $result = Db::getInstance()->executeS($dq);
            Cache::store($cacheId, $result);
        } else {
            $result = Cache::retrieve($cacheId);
        }
        return $result;
    }


    public static function getOrders($id_customer, $nb = 0, $p = 0, $n = 0, $context = null, $maximum_product_comment = '', $purchasedInTime = 0)
    {
        if (!$id_customer ||
            !Validate::isUnsignedInt($id_customer)
        ) {
            return false;
        }
        if ($context == null) {
            $context = Context::getContext();
        }
        $dq = new DbQuery();
        $dq
            ->from('orders', 'o')
            ->leftJoin('order_state', 'os', 'o.current_state=os.id_order_state')
            ->leftJoin('order_state_lang', 'osl', 'os.id_order_state=osl.id_order_state AND osl.id_lang=' . (int)$context->language->id)
            ->leftJoin('order_detail', 'od', 'od.id_order=o.id_order')
            ->leftJoin('ets_rv_product_comment_order', 'pco', 'pco.id_order=o.id_order AND pco.id_product=od.product_id')
            ->leftJoin('product', 'p', 'p.id_product=od.product_id')
            ->innerJoin('customer', 'c', 'o.id_customer = c.id_customer AND c.id_customer=' . (int)$id_customer)
            ->where('pco.id_ets_rv_product_comment is NULL OR pco.id_ets_rv_product_comment <= 0')
            ->where('o.current_state != 6');
        if ($order_state = explode(',', Configuration::get('ETS_RV_VERIFY_PURCHASE'))) {
            $dq->where('o.current_state IN (' . implode(',', $order_state) . ')');
        }
        $dq
            ->leftJoin('product_lang', 'pl', 'p.id_product = pl.id_product AND pl.id_lang=' . (int)$context->language->id)
            ->where('p.id_product is NOT NULL AND p.id_product > 0');
        if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
            $dq->where('p.state=' . Product::STATE_SAVED);
        }
        if (version_compare(_PS_VERSION_, '1.6.1.0', '>=')) {
            $dq
                ->leftJoin('image_shop', 'img_shop', 'img_shop.id_product = p.id_product AND img_shop.cover = 1 AND img_shop.id_shop=' . (int)$context->shop->id);
        } else {
            $dq
                ->leftJoin('image', 'img', 'img.id_product = p.id_product')
                ->leftJoin('image_shop', 'img_shop', 'img.id_image=img_shop.id_image AND img_shop.cover = 1 AND img_shop.id_shop=' . (int)$context->shop->id);
        }
        $dq
            ->leftJoin('image_lang', 'il', 'il.id_image = img_shop.id_image');
        if (trim($maximum_product_comment) !== '') {
            $dq->where((int)$maximum_product_comment . ' > (SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'ets_rv_product_comment` pc WHERE pc.id_product = od.product_id AND id_customer = ' . (int)$id_customer . ')');
        }
        if ($purchasedInTime > 0) {
            $dq->where('(UNIX_TIMESTAMP(o.date_add) + ' . ((int)$purchasedInTime * 86400) . ') > ' . time());
        }
        if ($nb) {
            $dq
                ->select('COUNT(DISTINCT CONCAT(o.id_order, \'-\', od.product_id))');
            return Db::getInstance()->getValue($dq);
        } else {
            $dq
                ->select('DISTINCT o.*, img_shop.id_image, il.legend, p.id_product, pl.name `product_name`, pl.link_rewrite, os.id_order_state, os.color, osl.name `order_state_name`')
                ->groupBy('o.id_order, p.id_product')
                ->orderBy('o.date_add desc');
        }

        if (!$p || !Validate::isUnsignedInt($p)) {
            $p = 1;
        }
        if (!$n || !Validate::isUnsignedInt($n)) {
            $n = 20;
        }
        $dq
            ->limit($n, ($p - 1) * $n);

        return Db::getInstance()->executeS($dq);
    }

    public static function nbReviewOfUserWithProduct($id_customer, $id_product)
    {
        if (!$id_customer || !Validate::isUnsignedInt($id_customer) || !$id_product || !Validate::isUnsignedInt($id_product)) {
            return false;
        }

        return (int)Db::getInstance()->getValue('SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'ets_rv_product_comment` WHERE `id_product` = ' . (int)$id_product . ' AND `id_customer` = ' . (int)$id_customer);
    }

    public static function getLastOrderValid($id_customer, $id_product, $purchasedInTime, $order_state = [])
    {
        if (!$id_customer || !Validate::isUnsignedInt($id_customer) ||
            !$id_product || !Validate::isUnsignedInt($id_product)
        ) {
            return false;
        }

        if (count($order_state) < 1) {
            $order_state = explode(',', Configuration::get('ETS_RV_VERIFY_PURCHASE'));
        }
        $dq = new DbQuery();
        $dq
            ->select('o.date_add')
            ->from('orders', 'o')
            ->leftJoin('order_detail', 'od', 'od.id_order = o.id_order')
            ->where('od.product_id=' . (int)$id_product)
            ->where('o.id_customer=' . (int)$id_customer)
            ->orderBy('o.date_add DESC');
        if ($order_state) {
            $dq->where('o.current_state IN (' . implode(',', $order_state) . ')');
        }
        $date_add_order = Db::getInstance()->getValue($dq);
        if (!$date_add_order)
            return false;
        return (strtotime($date_add_order) + $purchasedInTime * 86400) > time();
    }

    public static function getGradeByIdCustomer($id_customer)
    {
        if (!$id_customer || !Validate::isUnsignedInt($id_customer)) {
            return false;
        }
        return Db::getInstance()->getValue('SELECT ROUND(SUM(pc.grade) / COUNT(IF(pc.grade > 0, 1, NULL)), 1) as `grade` FROM `' . _DB_PREFIX_ . 'ets_rv_product_comment` pc WHERE pc.id_customer=' . (int)$id_customer . ' AND question = 0');
    }

    public static function getAllImages($id_product, $context = null)
    {
        if (!$id_product || !Validate::isUnsignedInt($id_product)) {
            return false;
        }
        if ($context == null) {
            $context = Context::getContext();
        }
        $id_customer = isset($context->customer) && $context->customer->isLogged() ? $context->customer->id : 0;
        $id_guest = isset($context->cookie->id_guest) ? (int)$context->cookie->id_guest : 0;
        $query = '
            SELECT * FROM `' . _DB_PREFIX_ . 'ets_rv_product_comment_image` pcm 
            INNER JOIN `' . _DB_PREFIX_ . 'ets_rv_product_comment` pc ON (pc.id_ets_rv_product_comment = pcm.id_ets_rv_product_comment AND pc.id_product=' . (int)$id_product . ')
            WHERE IF((' . (int)$id_customer . ' > 0 AND pc.`id_customer`=' . (int)$id_customer . ') OR (' . (int)$id_guest . ' > 0 AND pc.`id_guest`=' . (int)$id_guest . '), 1, pc.`validate`=1)
        ';
        return Db::getInstance()->executeS($query);
    }

    public static function getAllReviews($moduleName, $grade = 0, $nb = 0, $page = 1, $perPage = 10, $sortBy = null, Context $context = null)
    {
        static $st_products = [], $st_customers = [];
        if (!$context) {
            $context = Context::getContext();
        }

        $multiLang = (int)Configuration::get('ETS_RV_MULTILANG_ENABLED');
        $publishAllLanguage = (int)Configuration::get('ETS_RV_PUBLISH_ALL_LANGUAGE');

        $dq = new DbQuery();
        if ($nb)
            $dq->select('COUNT(*)');
        else
            $dq
                ->select('pc.*,  IF(pc.`id_customer`, CONCAT(c.`firstname`, \' \', c.`lastname`), pc.customer_name) `customer_name`, pcc.`display_name`, pcc.`avatar`')
                ->select('IF(' . (int)$multiLang . ' != 0 AND pcl.`content` != "" AND pcl.`content` is NOT NULL, pcl.`content`, pol.`content`) content')
                ->select('IF(' . (int)$multiLang . ' != 0 AND pcl.`title` != "" AND pcl.`title` is NOT NULL, pcl.`title`, pol.`title`) title');
        $dq
            ->from('ets_rv_product_comment', 'pc')
            ->leftJoin('customer', 'c', 'c.`id_customer`=pc.`id_customer`')
            ->leftJoin('ets_rv_product_comment_customer', 'pcc', 'c.`id_customer`=pcc.`id_customer`')
            ->leftJoin('ets_rv_product_comment_lang', 'pcl', 'pcl.`id_ets_rv_product_comment`=pc.`id_ets_rv_product_comment` AND pcl.`id_lang`=' . (int)$context->language->id)
            ->leftJoin('ets_rv_product_comment_origin_lang', 'pol', 'pc.id_ets_rv_product_comment = pol.id_ets_rv_product_comment')
            ->leftJoin('ets_rv_product_comment_publish_lang', 'ppl', 'pc.id_ets_rv_product_comment = ppl.id_ets_rv_product_comment')
            ->where('pc.`question`=0')
            ->where('pc.`deleted`=0')
            ->where('pc.`validate`=1')
            ->where('IF(' . (int)$multiLang . '=0, 1, pc.publish_all_language = 1 OR ' . (int)$publishAllLanguage . ' > 0 OR (ppl.id_lang = ' . (int)$context->language->id . ' AND ppl.id_ets_rv_product_comment is NOT NULL))');
        if ($grade) {
            $dq->where('ROUND(pc.`grade`)=' . (int)$grade);
        }
        if ($nb) {
            return (int)Db::getInstance()->getValue($dq);
        }
        if ($sortBy !== null) {
            $sorts = explode('.', $sortBy);
            if (!empty($sorts[0])) {
                $orderWay = !empty($sorts[1]) ? $sorts[1] : 'DESC';
                if ($sorts[0] == 'usefulness') {
                    $dq
                        ->select('((SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'ets_rv_product_comment_usefulness` pcuc WHERE pcuc.`id_ets_rv_product_comment` = pc.`id_ets_rv_product_comment` AND pcuc.usefulness = 1) - (SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'ets_rv_product_comment_usefulness` pcuc WHERE pcuc.`id_ets_rv_product_comment` = pc.`id_ets_rv_product_comment` AND pcuc.usefulness = 0)) AS usefulness')
                        ->orderBy('usefulness ' . $orderWay);
                } else
                    $dq->orderBy('pc.' . $sorts[0] . ' ' . $orderWay);
            }
        } else
            $dq->orderBy('pc.date_add DESC');

        $dq
            ->limit($perPage, ($page - 1) * $perPage);

        $res = Db::getInstance()->executeS($dq);
        if ($res) {
            $VERIFIED_PURCHASE_LABEL = trim(Configuration::get('ETS_RV_VERIFIED_PURCHASE_LABEL', $context->language->id));
            $ETS_RV_DISPLAY_NAME = (int)Configuration::get('ETS_RV_DISPLAY_NAME');
            $ETS_RV_PHOTO_ENABLED = (int)Configuration::get('ETS_RV_UPLOAD_PHOTO_ENABLED');
            $ETS_RV_VIDEO_ENABLED = (int)Configuration::get('ETS_RV_UPLOAD_VIDEO_ENABLED');
            $icons = EtsRVComment::getIcons();
            foreach ($res as &$re) {
                if (isset($re['id_product'])) {
                    $id_product = (int)$re['id_product'];
                    if (!isset($st_products[$id_product]) || !$st_products[$id_product]) {
                        $p = new Product($re['id_product'], true, $context->language->id);
                        $cover = Product::getCover($p->id, $context);
                        $st_products[$id_product]['product_name'] = $p->name;
                        $st_products[$id_product]['product_cover'] = $cover ? $context->link->getImageLink($p->link_rewrite, $cover['id_image'], EtsRVTools::getFormattedName('cart')) : '';
                        $st_products[$id_product]['product_link'] = $context->link->getProductLink($p, $p->link_rewrite, $p->category, $p->ean13, $context->language->id);
                    };
                    $re = array_merge($st_products[$id_product], $re);
                    $VERIFIED_PURCHASE = isset($re['verified_purchase']) && trim($re['verified_purchase']) !== '' ? trim($re['verified_purchase']) : 'auto';
                    $id_customer = isset($re['id_customer']) ? (int)$re['id_customer'] : 0;
                    $re['verify_purchase'] = $VERIFIED_PURCHASE == 'yes' || $id_customer && $VERIFIED_PURCHASE != 'no' && EtsRVProductComment::verifyPurchase((int)$p->id, $id_customer) && $VERIFIED_PURCHASE_LABEL !== '' ? EtsRVTools::getIcon('check') . ' ' . $VERIFIED_PURCHASE_LABEL : '';
                    if (!$re['customer_name'] || trim($re['customer_name']) == '') {
                        $re['customer_name'] = EtsRVTools::getInstance()->l('Deleted account', 'EtsRVProductComment');
                    } else
                        $re['customer_name'] = trim($re['customer_name']);
                    if ($ETS_RV_DISPLAY_NAME !== EtsRVProductComment::DISPLAY_CUSTOMER_FULL_NAME && isset($re['customer_name']) && trim($re['customer_name']) !== '') {
                        $customer_name_tmp = explode(' ', trim($re['customer_name']));
                        if (count($customer_name_tmp) > 1) {
                            if ($ETS_RV_DISPLAY_NAME === EtsRVProductComment::DISPLAY_CUSTOMER_ACRONYM_FIRSTNAME)
                                $re['customer_name'] = Tools::substr($customer_name_tmp[0], 0, 1) . '. ' . $customer_name_tmp[1];
                            else
                                $re['customer_name'] = $customer_name_tmp[0] . ' ' . Tools::substr($customer_name_tmp[1], 0, 1) . '.';
                        }
                    }
                    $re['avatar'] = !empty($re['avatar']) && @file_exists(_PS_IMG_DIR_ . 'ets_reviews/a/' . trim($re['avatar'])) ? $context->link->getMediaLink(_PS_IMG_ . $moduleName . '/a/' . $re['avatar']) : '';
                    if ($re['avatar'] === '') {
                        $re['avatar_caption'] = Tools::strtoupper(Tools::substr($re['customer_name'], 0, 1));
                        $re['avatar_color'] = EtsRVTools::geneColor($re['customer_name']);
                    }
                    $idProductComment = (int)$re['id_ets_rv_product_comment'];
                    $usefulness = EtsRVProductComment::getProductCommentUsefulness($idProductComment);
                    $re = array_merge($re, $usefulness);
                    // Photos:
                    if ($ETS_RV_PHOTO_ENABLED) {
                        $images = EtsRVProductCommentImage::getImages($idProductComment);
                        $path_uri = $context->link->getMediaLink(_PS_IMG_ . $moduleName . '/r/');
                        if ($images) {
                            foreach ($images as &$image) {
                                $image['url'] = $path_uri . $image['image'] . '-thumbnail.jpg';
                            }
                        }
                        $re['images'] = $images;
                    }
                    // Videos:
                    if ($ETS_RV_VIDEO_ENABLED) {
                        $videos = EtsRVProductCommentVideo::getVideos($idProductComment);
                        if ($videos) {
                            foreach ($videos as &$video)
                                $video['url'] = $path_uri . $video['video'];
                        }
                        $re['videos'] = $videos;
                    }
                    $re['display_date_add'] = EtsRVProductCommentEntity::getInstance()->timeElapsedString($re['date_add']);
                    $re['content'] = str_replace(array_keys($icons), $icons, Tools::nl2br($re['content']));
                }
            }
        }

        return $res;
    }

    public static function getGradesNumber($grade = 0)
    {
        $cacheId = 'EtsRVProductComment::getGradesNumber' . md5((int)$grade);
        if (!Cache::isStored($cacheId)) {
            $qb = new DbQuery();
            $qb
                ->select('COUNT(IF(pc.grade > 0, 1, NULL)) as gradeNb')
                ->from('ets_rv_product_comment', 'pc')
                ->where('pc.`deleted`=0')
                ->where('pc.`question`=0')
                ->where('pc.`validate`=1');
            if ($grade) {
                $qb
                    ->where('ROUND(pc.`grade`)=' . (int)$grade);
            }
            $result = (int)Db::getInstance()->getValue($qb);
            Cache::store($cacheId, $result);
        } else {
            $result = Cache::retrieve($cacheId);
        }
        return $result;
    }

    public static function getAverageGrade($grade = 0)
    {
        $cacheId = 'EtsRVProductComment::getAverageGrade' . md5((int)$grade);
        if (!Cache::isStored($cacheId)) {
            /** @var DbQuery $qb */
            $qb = new DbQuery();
            $qb
                ->select('SUM(pc.grade) / COUNT(IF(pc.grade > 0, 1, NULL)) AS averageGrade')
                ->from('ets_rv_product_comment', 'pc')
                ->where('pc.deleted = 0')
                ->where('pc.question = 0')
                ->where('pc.validate = 1');
            if ($grade) {
                $qb
                    ->select('COUNT(IF(pc.grade > 0, 1, NULL)) as countGrade')
                    ->where('ROUND(pc.grade)=' . (int)$grade);
            }
            $result = $grade ? Db::getInstance()->getRow($qb) : (float)Db::getInstance()->getValue($qb);
            Cache::store($cacheId, $result);
        } else {
            $result = Cache::retrieve($cacheId);
        }
        return $result;
    }

    public static function getCommentsNumber($grade = 0)
    {
        $cacheId = 'EtsRVProductComment::getCommentsNumber' . md5((int)$grade);
        if (!Cache::isStored($cacheId)) {
            $qb = new DbQuery();
            $qb
                ->select('COUNT(DISTINCT pc.id_ets_rv_product_comment) AS commentNb')
                ->from('ets_rv_product_comment', 'pc')
                ->where('pc.deleted = 0')
                ->where('pc.question = 0')
                ->where('pc.validate = 1');
            if ($grade)
                $qb->where('pc.grade = ' . (int)$grade);

            $result = (int)Db::getInstance()->getValue($qb);
            Cache::store($cacheId, $result);
        } else {
            $result = Cache::retrieve($cacheId);
        }
        return $result;
    }

    public static function getProductCommentUsefulness($productCommentId)
    {
        $qb = new DbQuery();
        $qb
            ->select('pcu.*')
            ->from('ets_rv_product_comment_usefulness', 'pcu')
            ->where('pcu.question = 0')
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
        }

        return $usefulnessInfos;
    }

    public static function deleteAll($question = 0)
    {
        return Db::getInstance()->delete('ets_rv_product_comment', 'question = ' . (int)$question, false);
    }
}
