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

class EtsRVProductCommentImage extends ObjectModel
{
    const IMAGE_MAX_LENGTH = 500;

    public $id;

    /** @var int ProductComment's id */
    public $id_ets_rv_product_comment;

    /**@var string Object Image's */
    public $image;

    /**@var int Object Position */
    public $position = 0;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'ets_rv_product_comment_image',
        'primary' => 'id_ets_rv_product_comment_image',
        'multilang' => false,
        'fields' => array(
            'id_ets_rv_product_comment' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'image' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => self::IMAGE_MAX_LENGTH),
            'position' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
        ),
    );

    public function delete()
    {
        if (parent::delete()) {
            if (@is_dir(($dirname = _PS_IMG_DIR_ . 'ets_reviews/r/')) && ($imageType = self::getImageTypes())) {
                foreach ($imageType as $type) {
                    if (@file_exists($dirname . $this->image . '-' . $type['name'] . '.jpg')) {
                        @unlink($dirname . $this->image . '-' . $type['name'] . '.jpg');
                    }
                }
            }
        }

        return true;
    }

    public static function deleteImages($id_product_comment)
    {
        if (!$id_product_comment ||
            !@is_dir(($dirname = _PS_IMG_DIR_ . 'ets_reviews/r/')) ||
            !is_array($id_product_comment) && !Validate::isUnsignedInt($id_product_comment)
        ) {
            return false;
        }
        $qr = 'FROM `' . _DB_PREFIX_ . 'ets_rv_product_comment_image` WHERE id_ets_rv_product_comment' . (is_array($id_product_comment) ? ' IN (' . implode(',', array_map('intval', $id_product_comment)) . ')' : '=' . (int)$id_product_comment);
        if (($images = Db::getInstance()->executeS('SELECT image ' . $qr)) && Db::getInstance()->execute('DELETE ' . $qr) && ($imageType = self::getImageTypes())) {
            foreach ($images as $image) {
                foreach ($imageType as $type) {
                    if (@file_exists($dirname . $image['image'] . '-' . $type['name'] . '.jpg')) {
                        @unlink($dirname . $image['image'] . '-' . $type['name'] . '.jpg');
                    }
                }
            }
        }

        return true;
    }


    public static function getImages($product_comment_id, $count = false)
    {
        if (!Validate::isUnsignedInt($product_comment_id)) {
            return false;
        }
        $qr = 'FROM `' . _DB_PREFIX_ . 'ets_rv_product_comment_image` WHERE `id_ets_rv_product_comment` = ' . (int)$product_comment_id . ' ORDER BY `position` ASC';
        return $count ? (int)Db::getInstance()->getValue('SELECT COUNT(*) ' . $qr) : Db::getInstance()->executeS('SELECT * ' . $qr);
    }

    public function toArray()
    {
        return [
            'id_ets_rv_product_comment' => $this->id_ets_rv_product_comment,
            'id_ets_rv_product_comment_image' => $this->id,
            'image' => $this->image,
            'position' => $this->position,
        ];
    }

    public function getLastPosition($id_ets_rv_product_comment)
    {
        return ($lastItem = (int)Db::getInstance()->getValue('SELECT `position` FROM `' . _DB_PREFIX_ . 'ets_rv_product_comment_image` WHERE id_ets_rv_product_comment=' . (int)$id_ets_rv_product_comment . ' ORDER BY `position` DESC')) ? $lastItem : 0;
    }

    public static function getImageTypes()
    {
        return array(
            array(
                'name' => 'thumbnail',
                'width' => 160,
                'height' => 160,
            ),
            array(
                'name' => 'large',
                'width' => 800,
                'height' => 800,
            )
        );
    }
}
