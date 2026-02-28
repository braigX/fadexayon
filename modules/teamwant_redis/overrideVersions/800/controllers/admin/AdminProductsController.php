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
        Hook::exec(
            'actionTeamwantRedisUpdateProductImage',
            ['id_product' => $idProduct]
        );
        return parent::ajaxProcessaddProductImage($idProduct, $inputFileName, $die);
    }

    public function ajaxProcessDeleteProductImage($id_image = null) {
        Hook::exec(
            'actionTeamwantRedisUpdateProductImage',
            ['id_product' => null, 'id_image' => $id_image]
        );
        return parent::ajaxProcessDeleteProductImage($id_image);
    }
}
