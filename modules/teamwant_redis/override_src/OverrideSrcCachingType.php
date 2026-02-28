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

class OverrideSrcCachingType implements OverrideSrc
{
    public const FILE = _PS_ROOT_DIR_ . '/src/PrestaShopBundle/Form/Admin/AdvancedParameters/Performance/CachingType.php';

    public static function install()
    {
        $filecontent = \Tools::file_get_contents(self::FILE);
        $override = 0;

        // add: $this->extensionsList += \Dispatcher::teamwantRedisGetExtensionsListCachingType();
        $re = '/\$this\-\>extensionsList \+\= \\\\Dispatcher\:\:teamwantRedisGetExtensionsListCachingType\(\)\;/s';
        preg_match($re, $filecontent, $matches);

        if (!count($matches)) {
            $override = 1;
            $re = '/public function buildForm\(FormBuilderInterface \$builder\, array \$options\)\n    \{/s';
            $subst = '
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $redisArray = [];
        if (method_exists(\Dispatcher::class, \'teamwantRedisGetExtensionsListCachingType\')) {
        $this->extensionsList += \Dispatcher::teamwantRedisGetExtensionsListCachingType();
        }
        if (method_exists(\Dispatcher::class, \'teamwantRedisGetAvailableCachingType\')) {
        $redisArray = \Dispatcher::teamwantRedisGetAvailableCachingType();
        }
';
            $filecontent = preg_replace($re, $subst, $filecontent, 1);
        }

        // add: + \Dispatcher::teamwantRedisGetAvailableCachingType()
        $re = '/\+ \$redisArray/s';
        preg_match($re, $filecontent, $matches);

        if (!count($matches)) {
            $override = 1;
            $re = '/\$builder\n\s+\-\>add(.*?)\,\n\s+\'choice_label\' \=\> function \(\$value\, \$key\, \$index\) \{/s';
            $subst = '
        $builder
            ->add$1 + $redisArray,
                \'choice_label\' => function ($value, $key, $index) {';

            $filecontent = preg_replace($re, $subst, $filecontent, 1);
        }

        if ($override) {
            file_put_contents(self::FILE, $filecontent);
        }
    }

    public static function uninstall()
    {
        $filecontent = \Tools::file_get_contents(self::FILE);

        $filecontent = str_replace(' + $redisArray', '', $filecontent);
        $filecontent = str_replace('
        $redisArray = [];
        if (method_exists(\Dispatcher::class, \'teamwantRedisGetExtensionsListCachingType\')) {
        $this->extensionsList += \Dispatcher::teamwantRedisGetExtensionsListCachingType();
        }
        if (method_exists(\Dispatcher::class, \'teamwantRedisGetAvailableCachingType\')) {
        $redisArray = \Dispatcher::teamwantRedisGetAvailableCachingType();
        }', '', $filecontent);

        file_put_contents(self::FILE, $filecontent);
    }
}
