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

if (!defined('_PS_VERSION_')) {exit;}

/** @var Ets_abandonedcart $object */
function upgrade_module_4_6_9($object)
{
    $directories = [
        'module' => glob(_PS_MODULE_DIR_ . $object->name . '/mails/*/*.{txt,html}', GLOB_BRACE),
        'theme' => glob(_PS_THEME_DIR_ . 'modules/' . $object->name . '/mails/*/*.{txt,html}', GLOB_BRACE),
    ];
    foreach ($directories as $files) {
        foreach ($files as $file) {
            $content = EtsAbancartHelper::file_get_contents(realpath($file));
            $content = preg_replace('/\{context\}/', '{content}', $content);
            EtsAbancartHelper::file_put_contents(realpath($file), $content, null, [], LOCK_EX);
        }
    }
    return true;
}