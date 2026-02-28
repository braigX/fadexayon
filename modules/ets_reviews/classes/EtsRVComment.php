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

class EtsRVComment extends EtsRVModel
{
    const STATUS_APPROVE = 1;
    const STATUS_PRIVATE = 2;
    const STATUS_PENDING = 0;
    public $id;
    public $id_ets_rv_product_comment;
    public $id_customer;
    public $content;
    public $employee = 0;
    public $validate = 0;
    public $question = 0;
    public $answer = 0;
    public $useful_answer = 0;
    public $deleted = 0;
    public $date_add;
    public $upd_date;
    public $origin_content;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'ets_rv_comment',
        'primary' => 'id_ets_rv_comment',
        'multilang' => true,
        'fields' => array(
            'id_ets_rv_product_comment' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_customer' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'employee' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'validate' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'question' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'answer' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'useful_answer' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'deleted' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'date_add' => array('type' => self::TYPE_DATE),
            'upd_date' => array('type' => self::TYPE_DATE),

            //Lang fields.
            'content' => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'),//, 'size' => 65535
        ),
    );

    public static function getIcons()
    {
        if (@file_exists(($filename = dirname(__FILE__) . '/../icon.json'))) {
            return json_decode(Tools::file_get_contents(dirname(__FILE__) . '/../icon.json'), true);
        }
        return [];
    }

    public static function notApprove($question = 0, $answer = 0)
    {
        if (!Validate::isBool($question))
            return 0;
        $dq = new DbQuery();
        $dq
            ->select('COUNT(id_ets_rv_comment)')
            ->from('ets_rv_comment')
            ->where('validate=0')
            ->where('question=' . (int)$question)
            ->where('answer=' . (int)$answer);

        return (int)Db::getInstance()->getValue($dq);
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
            ->select('COUNT(id_ets_rv_comment)')
            ->from('ets_rv_comment')
            ->where('validate=0')
            ->where('id_ets_rv_comment IN (' . implode(',', array_map('intval', $ids)) . ')');

        return (int)Db::getInstance()->getValue($dq);
    }

    public static function approveComments($ids)
    {
        if (!$ids ||
            !Validate::isArrayWithIds($ids)
        ) {
            return false;
        }
        return
            Db::getInstance()->execute('
                UPDATE `' . _DB_PREFIX_ . 'ets_rv_comment`                 SET validate=1 
                WHERE id_ets_rv_comment IN (' . implode(',', array_map('intval', $ids)) . ')
            ');
    }

    public static function deleteComments($ids)
    {
        if (!$ids ||
            !Validate::isArrayWithIds($ids)
        ) {
            return false;
        }
        return Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'ets_rv_comment` WHERE id_ets_rv_comment IN (' . implode(',', array_map('intval', $ids)) . ')');
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
            $res &= Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'ets_rv_comment_' . bqSQL($table) . '` WHERE id_ets_rv_comment IN (' . implode(',', array_map('intval', $ids)) . ');');
        }

        return $res;
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
            ->from('ets_rv_comment')
            ->where('id_ets_rv_comment=' . (int)$id);

        return Db::getInstance()->getValue($db);
    }

    public static function findOneById($id)
    {
        return (int)Db::getInstance()->getValue('SELECT id_ets_rv_comment FROM `' . _DB_PREFIX_ . 'ets_rv_comment` WHERE id_ets_rv_comment=' . (int)$id);
    }

    public function toArray($usefulness = false)
    {
        $usefulnessInfos = $this->id && $usefulness ? EtsRVCommentRepository::getInstance()->getCommentUsefulness($this->id, $this->question) : array();
        return [
            'id_ets_rv_product_comment' => $this->id_ets_rv_product_comment,
            'id_ets_rv_comment' => $this->id,
            'id_customer' => $this->id_customer,
            'date_add' => $this->date_add,
            'upd_date' => $this->upd_date,
            'usefulness' => !empty($usefulnessInfos['usefulness']) ? (int)$usefulnessInfos['usefulness'] : 0,
            'total_usefulness' => !empty($usefulnessInfos['total_usefulness']) ? (int)$usefulnessInfos['total_usefulness'] : 0,
            'employee' => $this->employee,
            'question' => $this->question,
            'answer' => $this->answer,
            'useful_answer' => $this->useful_answer,
            'validate' => $this->validate
        ];
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
            ->select('pc.id_customer, pc.employee, pc.question, pc.id_ets_rv_product_comment, pc.validate, pc.answer')
            ->select('ppc.id_product')
            ->from('ets_rv_comment', 'pc');
        if (!$onlyId) {
            $dq
                ->select('IF(' . (int)$multiLang . ' != 0 AND pcl.`content` != "" AND pcl.`content` is NOT NULL, pcl.`content`, pol.`content`) content')
                ->leftJoin('ets_rv_comment_lang', 'pcl', 'pc.id_ets_rv_comment = pcl.id_ets_rv_comment AND pcl.id_lang = ' . (int)$idLang)
                ->leftJoin('ets_rv_comment_origin_lang', 'pol', 'pc.id_ets_rv_comment = pol.id_ets_rv_comment');
        }
        $dq
            ->leftJoin('ets_rv_product_comment', 'ppc', 'pc.id_ets_rv_product_comment = ppc.id_ets_rv_product_comment')
            ->where('pc.id_ets_rv_comment=' . (int)$id);

        return Db::getInstance()->getRow($dq);
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

    public function delete()
    {
        if (!parent::delete()) {
            return false;
        }
        // Cascade
        return
            $this->deleteUsefulness($this->id) &&
            $this->deleteOriginLang($this->id) &&
            $this->deleteCascade($this->id, 'reply_comment', '', 'comment');
    }

    public static function saveOriginLang($id_comment, $id_lang, $content)
    {
        if (!Validate::isUnsignedId($id_comment) ||
            !Validate::isUnsignedId($id_lang)) {
            return false;
        }
        if (($idLang = (int)Db::getInstance()->getValue('SELECT `id_lang` FROM `' . _DB_PREFIX_ . 'ets_rv_comment_origin_lang` WHERE id_ets_rv_comment = ' . (int)$id_comment))) {
            return Db::getInstance()->execute('
                UPDATE `' . _DB_PREFIX_ . 'ets_rv_comment_origin_lang` 
                SET `content`="' . pSQL($content, true) . '", 
                    `id_lang`="' . (int)$id_lang . '"
                WHERE id_ets_rv_comment = ' . (int)$id_comment . '
            ');
        } elseif (!$idLang) {
            return Db::getInstance()->execute('
                INSERT INTO `' . _DB_PREFIX_ . 'ets_rv_comment_origin_lang`(
                    `id_ets_rv_comment`,
                    `id_lang`,
                    `content`
                ) 
                VALUES(
                    ' . (int)$id_comment . ',
                    ' . (int)$id_lang . ',
                    "' . pSQL($content, true) . '")
               ');
        }
    }

    public static function getOriginContentById($id_comment)
    {
        if (!Validate::isUnsignedId($id_comment)) {
            return false;
        }
        return ($res = Db::getInstance()->getValue('SELECT `content` FROM `' . _DB_PREFIX_ . 'ets_rv_comment_origin_lang` WHERE id_ets_rv_comment = ' . (int)$id_comment)) ? $res : '';
    }

    public static function deleteOriginLang($id_comment)
    {
        if (!Validate::isUnsignedId($id_comment)) {
            return false;
        }

        return Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'ets_rv_comment_origin_lang` WHERE `id_ets_rv_comment` = ' . (int)$id_comment);
    }

    public static function deleteUsefulness($id_comment, $id_customer = null, $employee = null, $question = null)
    {
        if (!Validate::isUnsignedId($id_comment) ||
            $id_customer !== null && !Validate::isUnsignedInt($id_customer) ||
            $employee !== null && !Validate::isUnsignedInt($employee) ||
            $question !== null && !Validate::isUnsignedInt($question)
        ) {
            return false;
        }

        return Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'ets_rv_comment_usefulness` WHERE `id_ets_rv_comment` = ' . (int)$id_comment . ($id_customer !== null ? ' AND `id_customer` = ' . (int)$id_customer : '') . ($employee !== null ? ' AND `employee` = ' . (int)$employee : '') . ($question !== null ? ' AND `question` = ' . (int)$question : ''));
    }

    public static function setCommentUsefulness($id_comment, $usefulness, $id_customer, $employee = 0, $question = 0)
    {
        if (!self::isAlreadyUsefulness($id_comment, $id_customer, $employee, $question)) {
            return Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'ets_rv_comment_usefulness` (`id_ets_rv_comment`, `usefulness`, `id_customer`, `employee`, `question`) VALUES (' . (int)$id_comment . ', ' . (int)$usefulness . ', ' . (int)$id_customer . ', ' . (int)$employee . ', ' . (int)$question . ')');
        } elseif (self::isAlreadyUsefulness($id_comment, $id_customer, $employee, $question, $usefulness)) {
            return !self::deleteUsefulness($id_comment, $id_customer, $employee, $question);
        } else {
            return Db::getInstance()->execute('
                UPDATE `' . _DB_PREFIX_ . 'ets_rv_comment_usefulness` SET `usefulness` = ' . (int)$usefulness . '
                WHERE `id_ets_rv_comment` = ' . (int)$id_comment . ' AND `id_customer` = ' . (int)$id_customer . ' AND `employee` = ' . (int)$employee . ' AND `question` = ' . (int)$question
            );
        }
    }

    public static function isAlreadyUsefulness($id_comment, $id_customer, $employee = 0, $question = 0, $usefulness = null)
    {
        return (bool)Db::getInstance()->getValue('
			SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'ets_rv_comment_usefulness`
			WHERE `id_customer` = ' . (int)$id_customer . ' AND `id_ets_rv_comment` = ' . (int)$id_comment . ' AND employee=' . (int)$employee . ' AND question=' . (int)$question . ($usefulness !== null ? ' AND usefulness=' . (int)$usefulness : '')
        );
    }

    public static function deleteAll($question = 0, $answer = null)
    {
        return Db::getInstance()->delete('ets_rv_comment', 'question = ' . (int)$question . ($answer !== null ? ' AND answer=' . (int)$answer : ''), false);
    }
}
