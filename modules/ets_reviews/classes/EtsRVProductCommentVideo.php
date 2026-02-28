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

class EtsRVProductCommentVideo extends ObjectModel
{
    public $id_ets_rv_product_comment;
    public $video;
    public $position = 0;
    public $type;
    public static $definition = array(
        'table' => 'ets_rv_product_comment_video',
        'primary' => 'id_ets_rv_product_comment_video',
        'multilang' => false,
        'fields' => array(
            'id_ets_rv_product_comment' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'video' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'type' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'position' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
        ),
    );
    public function getLastPosition($id_ets_rv_product_comment)
    {
        return ($lastItem = (int)Db::getInstance()->getValue('SELECT `position` FROM `' . _DB_PREFIX_ . 'ets_rv_product_comment_image` WHERE id_ets_rv_product_comment=' . (int)$id_ets_rv_product_comment . ' ORDER BY `position` DESC')) ? $lastItem : 0;
    }
    public static function getVideos($product_comment_id,$count= false)
    {
        if (!Validate::isUnsignedInt($product_comment_id)) {
            return false;
        }
        $qr = 'FROM `' . _DB_PREFIX_ . 'ets_rv_product_comment_video` WHERE `id_ets_rv_product_comment` = ' . (int)$product_comment_id . ' ORDER BY `position` ASC';
        return $count ? (int)Db::getInstance()->getValue('SELECT COUNT(*) ' . $qr) : Db::getInstance()->executeS('SELECT * ' . $qr);
    }
    public function delete()
    {
        if (parent::delete()) {
            if (@is_dir(($dirname = _PS_IMG_DIR_ . 'ets_reviews/r/'))) {
                if (@file_exists($dirname . $this->video)) {
                    @unlink($dirname . $this->video);
                }
            }
        }

        return true;
    }
}