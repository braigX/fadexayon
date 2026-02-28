<?php
/**
 * Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
 *
 *    @author    Jpresta
 *    @copyright Jpresta
 *    @license   See the license of this module in file LICENSE.txt, thank you.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

if (file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
    include_once dirname(__FILE__) . '/vendor/autoload.php';
}
if (!class_exists('voku\helper\HtmlMin')) {
    // If Page Cache is installed we don't want to fail badly
    if (version_compare(PHP_VERSION, '7.0.0', '>=')
        && file_exists(dirname(__FILE__) . '/vendor/php7/autoload.php')) {
        include_once dirname(__FILE__) . '/vendor/php7/autoload.php';
    }
}
