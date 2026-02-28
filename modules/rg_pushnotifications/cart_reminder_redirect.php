<?php
/**
 * Web Browser Push Notifications using OneSignal
 *
 * @author    Rolige <www.rolige.com>
 * @copyright Since 2011 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 */

require_once dirname(__FILE__) . '/../../config/config.inc.php';
require_once dirname(__FILE__) . '/../../init.php';
require_once dirname(__FILE__) . '/rg_pushnotifications.php';

CartRule::autoRemoveFromCart();
CartRule::autoAddToCart();
$url = trim(RgPuNoConfig::get('CART_URL'));

if (!$url) {
    $url = Context::getContext()->link->getPageLink('order', true);
}

Tools::redirect($url);
