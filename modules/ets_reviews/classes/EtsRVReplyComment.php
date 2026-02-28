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

class EtsRVReplyComment extends EtsRVModel
{
    const STATUS_APPROVE = 1;
    const STATUS_PRIVATE = 2;
    const STATUS_PENDING = 0;
    public $id;
    public $id_ets_rv_comment;
    public $id_customer;
    public $content;
    public $employee = 0;
    public $validate = 0;
    public $question = 0;
    public $deleted = 0;
    public $date_add;
    public $upd_date;
    public $origin_content;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'ets_rv_reply_comment',
        'primary' => 'id_ets_rv_reply_comment',
        'multilang' => true,
        'fields' => array(
            'id_ets_rv_comment' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_customer' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'employee' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'validate' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'question' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'deleted' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'date_add' => array('type' => self::TYPE_DATE),
            'upd_date' => array('type' => self::TYPE_DATE),

            //Lang fields.
            'content' => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'),//, 'size' => 65535
        ),
    );

    public static function notApprove($question = 0)
    {
        if (!Validate::isBool($question))
            return 0;
        $dq = new DbQuery();
        $dq
            ->select('COUNT(id_ets_rv_reply_comment)')
            ->from('ets_rv_reply_comment')
            ->where('validate=0')
            ->where('question=' . (int)$question);

        return (int)Db::getInstance()->getValue($dq);
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
            ->from('ets_rv_reply_comment')
            ->where('id_ets_rv_reply_comment=' . (int)$id);

        return Db::getInstance()->getValue($db);
    }

    public static function findOneById($id)
    {
        return (int)Db::getInstance()->getValue('SELECT id_ets_rv_reply_comment FROM `' . _DB_PREFIX_ . 'ets_rv_reply_comment` WHERE id_ets_rv_reply_comment=' . (int)$id);
    }

    public static function getInstanceById($id)
    {
        return new self($id);
    }

    public function toArray($usefulness = false)
    {
        $usefulnessInfos = $this->id && $usefulness ? EtsRVReplyCommentRepository::getInstance()->getReplyCommentUsefulness($this->id, $this->question) : array();
        return [
            'id_ets_rv_reply_comment' => $this->id,
            'id_ets_rv_comment' => $this->id_ets_rv_comment,
            'id_customer' => $this->id_customer,
            'date_add' => $this->date_add,
            'upd_date' => $this->upd_date,
            'usefulness' => !empty($usefulnessInfos['usefulness']) ? (int)$usefulnessInfos['usefulness'] : 0,
            'total_usefulness' => !empty($usefulnessInfos['total_usefulness']) ? (int)$usefulnessInfos['total_usefulness'] : 0,
            'employee' => $this->employee,
            'question' => $this->question,
            'validate' => $this->validate
        ];
    }

    public static function getProductCommentById($id)
    {
        if (!$id ||
            !Validate::isUnsignedInt($id)
        ) {
            return false;
        }
        $dq = new DbQuery();
        $dq->select('ppc.id_ets_rv_product_comment')
            ->from('ets_rv_reply_comment', 'pc')
            ->leftJoin('ets_rv_comment', 'pcc', 'pc.id_ets_rv_comment = pcc.id_ets_rv_comment')
            ->leftJoin('ets_rv_product_comment', 'ppc', 'pcc.id_ets_rv_product_comment = ppc.id_ets_rv_product_comment')
            ->where('pc.id_ets_rv_reply_comment=' . (int)$id);
        $id_product_comment = (int)Db::getInstance()->getValue($dq);
        if ($id_product_comment)
            return new EtsRVProductComment((int)$id_product_comment);
        return 0;
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

        $dq = new DbQuery();

        $dq
            ->select('pc.id_ets_rv_comment, pc.id_customer, pc.employee, pc.question, pcc.id_ets_rv_product_comment, pc.validate, pcc.answer')
            ->select('ppc.id_product')
            ->from('ets_rv_reply_comment', 'pc');

        if (!$onlyId) {
            $dq
                ->select('IF(' . $multiLang . ' != 0 AND pcl.`content` != "" AND pcl.`content` is NOT NULL, pcl.`content`, pol.`content`) content')
                ->leftJoin('ets_rv_reply_comment_lang', 'pcl', 'pc.id_ets_rv_reply_comment = pcl.id_ets_rv_reply_comment AND pcl.id_lang = ' . (int)$idLang)
                ->leftJoin('ets_rv_reply_comment_origin_lang', 'pol', 'pc.id_ets_rv_reply_comment = pol.id_ets_rv_reply_comment');
        }
        $dq
            ->leftJoin('ets_rv_comment', 'pcc', 'pc.id_ets_rv_comment = pcc.id_ets_rv_comment')
            ->leftJoin('ets_rv_product_comment', 'ppc', 'pcc.id_ets_rv_product_comment = ppc.id_ets_rv_product_comment')
            ->where('pc.id_ets_rv_reply_comment=' . (int)$id);

        return Db::getInstance()->getRow($dq);
    }

    public static function getCommentsNumber($ids)
    {
        if (!$ids ||
            !Validate::isArrayWithIds($ids)
        ) {
            return false;
        }
        $dq = new DbQuery();
        $dq
            ->select('COUNT(id_ets_rv_reply_comment)')
            ->from('ets_rv_reply_comment')
            ->where('validate=0')
            ->where('id_ets_rv_reply_comment IN (' . implode(',', $ids) . ')');

        return (int)Db::getInstance()->getValue($dq);
    }

    public static function approveComments($ids = array())
    {
        if (!$ids ||
            !Validate::isArrayWithIds($ids)
        ) {
            return false;
        }
        return
            Db::getInstance()->execute('
                UPDATE `' . _DB_PREFIX_ . 'ets_rv_reply_comment`                 SET validate=1 
                WHERE id_ets_rv_reply_comment IN (' . implode(',', $ids) . ')
            ');
    }

    public static function deleteComments($ids)
    {
        if (!$ids ||
            !Validate::isArrayWithIds($ids)
        ) {
            return false;
        }
        return Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'ets_rv_reply_comment` WHERE id_ets_rv_reply_comment IN (' . implode(',', $ids) . ')');
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
            'usefulness',
            'origin_lang',
        );
        $res = true;
        foreach ($tables as $table) {
            $res &= Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'ets_rv_reply_comment_' . pSQL($table) . '` WHERE id_ets_rv_reply_comment IN (' . implode(',', $ids) . ');');
        }

        return $res;
    }

    public function validate($validate = '1')
    {
        if (!Validate::isUnsignedId($this->id)) {
            return false;
        }

        $this->validate = $validate;
        Hook::exec('actionObjectProductCommentValidateAfter', array('object' => $this));

        return $this->update();
    }

    public function delete()
    {
        if (!parent::delete()) {
            return false;
        }
        return
            $this->deleteUsefulness($this->id) &&
            $this->deleteOriginLang($this->id) &&
            self::deleteActivity($this->id, 'reply_comment');
    }

    public static function saveOriginLang($id_reply_comment, $id_lang, $content)
    {
        if (!Validate::isUnsignedId($id_reply_comment) ||
            !Validate::isUnsignedId($id_lang)) {
            return false;
        };
        if (($idLang = (int)Db::getInstance()->getValue('SELECT `id_lang` FROM `' . _DB_PREFIX_ . 'ets_rv_reply_comment_origin_lang` WHERE id_ets_rv_reply_comment = ' . (int)$id_reply_comment))) {
            return Db::getInstance()->execute('
                UPDATE `' . _DB_PREFIX_ . 'ets_rv_reply_comment_origin_lang` 
                SET `content`="' . pSQL($content, true) . '",
                    `id_lang`="' . (int)$id_lang . '"
                WHERE id_ets_rv_reply_comment = ' . (int)$id_reply_comment . '
            ');
        } elseif (!$idLang) {
            return Db::getInstance()->execute('
                INSERT INTO `' . _DB_PREFIX_ . 'ets_rv_reply_comment_origin_lang`(
                    `id_ets_rv_reply_comment`,
                    `id_lang`,
                    `content`
                ) 
                VALUES(
                    ' . (int)$id_reply_comment . ',
                    ' . (int)$id_lang . ',
                    "' . pSQL($content, true) . '")
               ');
        }
    }

    public static function getOriginContentById($id_reply_comment)
    {
        if (!Validate::isUnsignedId($id_reply_comment)) {
            return false;
        }
        return Db::getInstance()->getValue('SELECT `content` FROM `' . _DB_PREFIX_ . 'ets_rv_reply_comment_origin_lang` WHERE id_ets_rv_reply_comment = ' . (int)$id_reply_comment);
    }

    public static function deleteOriginLang($id_reply_comment)
    {
        if (!Validate::isUnsignedId($id_reply_comment)) {
            return false;
        }

        return Db::getInstance()->execute('
            DELETE FROM `' . _DB_PREFIX_ . 'ets_rv_reply_comment_origin_lang`
            WHERE `id_ets_rv_reply_comment` = ' . (int)$id_reply_comment
        );
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
            ->from('ets_rv_reply_comment_origin_lang', 'pcl')
            ->where('pcl.id_ets_rv_reply_comment=' . (int)$id);
        return Db::getInstance()->getRow($qd);
    }

    public static function deleteUsefulness($id_reply_comment, $id_customer = null, $employee = null, $question = null)
    {
        if (!Validate::isUnsignedId($id_reply_comment) ||
            $id_customer !== null && !Validate::isUnsignedInt($id_customer) ||
            $employee !== null && !Validate::isUnsignedInt($employee) ||
            $question !== null && !Validate::isUnsignedInt($question)
        ) {
            return false;
        }

        return Db::getInstance()->execute('
		DELETE FROM `' . _DB_PREFIX_ . 'ets_rv_reply_comment_usefulness`
		WHERE `id_ets_rv_reply_comment` = ' . (int)$id_reply_comment . ($id_customer !== null ? ' AND `id_customer` = ' . (int)$id_customer : '') . ($employee !== null ? ' AND `employee` = ' . (int)$employee : '') . ($question !== null ? ' AND `question` = ' . (int)$question : ''));
    }

    public static function setCommentUsefulness($id_reply_comment, $usefulness, $id_customer, $employee = 0, $question = 0)
    {
        if (!self::isAlreadyUsefulness($id_reply_comment, $id_customer, $employee, $question)) {
            return Db::getInstance()->execute('
                INSERT INTO `' . _DB_PREFIX_ . 'ets_rv_reply_comment_usefulness` (`id_ets_rv_reply_comment`, `usefulness`, `id_customer`, `employee`, `question`)
                VALUES (' . (int)$id_reply_comment . ', ' . (int)$usefulness . ', ' . (int)$id_customer . ', ' . (int)$employee . ', ' . (int)$question . ')
            ');
        } elseif (self::isAlreadyUsefulness($id_reply_comment, $id_customer, $employee, $question, $usefulness)) {
            return !self::deleteUsefulness($id_reply_comment, $id_customer, $employee, $question);
        } else {
            return Db::getInstance()->execute('
                UPDATE `' . _DB_PREFIX_ . 'ets_rv_reply_comment_usefulness` SET `usefulness` = ' . (int)$usefulness . '
                WHERE `id_ets_rv_reply_comment` = ' . (int)$id_reply_comment . ' 
                    AND `id_customer` = ' . (int)$id_customer . ' 
                    AND `employee` = ' . (int)$employee . ' 
                    AND `question` = ' . (int)$question
            );
        }

        return true;
    }

    public static function isAlreadyUsefulness($id_reply_comment, $id_customer, $employee = 0, $question = 0, $usefulness = null)
    {
        return (bool)Db::getInstance()->getValue('
			SELECT COUNT(*)
			FROM `' . _DB_PREFIX_ . 'ets_rv_reply_comment_usefulness`
			WHERE `id_customer` = ' . (int)$id_customer . '
                AND `id_ets_rv_reply_comment` = ' . (int)$id_reply_comment . '
                AND employee=' . (int)$employee . ' 
                AND question=' . (int)$question . ($usefulness !== null ? ' AND usefulness=' . (int)$usefulness : '')
        );
    }

    public static function deleteAll($question = 0)
    {
        return Db::getInstance()->delete('ets_rv_reply_comment', 'question = ' . (int)$question, false);
    }
}
