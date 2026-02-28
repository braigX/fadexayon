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

// todo: poogarniac consty w wolnej chwili
require_once _PS_MODULE_DIR_ . 'teamwant_redis/const.php';

function load_Teamwant_redis_OverrideSrc()
{
    require_once TEAMWANT_REDIS_ROOT_DIR . '/override_src/OverrideSrc.php';
    require_once TEAMWANT_REDIS_ROOT_DIR . '/override_src/OverrideSrcParametersFile.php';
    require_once TEAMWANT_REDIS_ROOT_DIR . '/override_src/OverrideHookFile.php';

    if (version_compare(_PS_VERSION_, '1.7.7.5', '<=')) {
        require_once TEAMWANT_REDIS_ROOT_DIR . '/override_src/OverrideSrcCachingType.php';
    }
}
