<?php
/**
 * Redis Cache
 * Version: 3.0.0
 * Copyright (c) 2020-2023. Mateusz Szymański Teamwant
 * https://teamwant.pl
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @author    Teamwant <kontakt@teamwant.pl>
 * @copyright Copyright 2020-2023 © Teamwant Mateusz Szymański All right reserved
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *
 * @category  Teamwant
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminProductsController extends AdminProductsControllerCore
{
    public function ajaxProcessaddProductImage($idProduct = null, $inputFileName = 'file', $die = true) {
        $this->clearImageCache($idProduct);
        return parent::ajaxProcessaddProductImage($idProduct, $inputFileName, $die);
    }

    public function ajaxProcessDeleteProductImage($id_image = null) {
        $this->clearImageCache(null, $id_image);
        return parent::ajaxProcessDeleteProductImage($id_image);
    }

    private function clearImageCache($idProduct = null, $id_image = null) {
        $cache = Cache::getInstance();
                
        if ($cache instanceof Teamwant\Prestashop17\Redis\Classes\Cache\Redis) {
            $cache->setForceSave(true);
            $idProduct = $idProduct ? $idProduct : Tools::getValue('id_product');

            if (!$idProduct) {
                $id_image = $id_image ? $id_image : (int) Tools::getValue('id_image');
                if ($id_image) {
                    $image = new Image($id_image);
                    $idProduct = (int) $image->id_product;
                }
            }

            if (!$idProduct) {
                return false;
            }

            foreach (Language::getIDs() as $id_lang) {
                $sql = '
                    SELECT image_shop.`cover`, i.`id_image`, il.`legend`, i.`position`
                    FROM `' . _DB_PREFIX_ . 'image` i
                    ' . Shop::addSqlAssociation('image', 'i') . '
                    LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int) $id_lang . ')
                    WHERE i.`id_product` = ' . (int) $idProduct . '
                    ORDER BY `position`';

                $cache->clearQuery($sql);

                $sql = 'SELECT image_shop.`id_image`
                FROM `' . _DB_PREFIX_ . 'image` i
                ' . Shop::addSqlAssociation('image', 'i') . '
                WHERE i.`id_product` = ' . (int) $idProduct . '
                AND image_shop.`cover` = 1';
                $cache->clearQuery($sql);
            }

            return true;
        }

        return false;
    }
}
