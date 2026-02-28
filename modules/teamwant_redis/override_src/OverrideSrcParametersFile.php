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

namespace Teamwant_redis\OverrideSrc;

if (!defined('_PS_VERSION_')) {
    exit;
}

class OverrideSrcParametersFile implements OverrideSrc
{
    public const FILE = _PS_ROOT_DIR_ . '/app/config/parameters.php';

    public static function install()
    {
        return true;
    }

    public static function uninstall()
    {
        $conf = include self::FILE;

        if ($conf['parameters']['ps_cache_enable'] === true && $conf['parameters']['ps_caching'] === 'Redis') {
            $filecontent = \Tools::file_get_contents(self::FILE);
            $re = '/\'ps_cache_enable\' => true/s';
            $subst = '\'ps_cache_enable\' => false';
            $filecontent = preg_replace($re, $subst, $filecontent);

            file_put_contents(self::FILE, $filecontent);
        }
    }
}
