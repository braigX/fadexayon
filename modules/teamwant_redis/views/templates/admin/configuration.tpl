{**
 * Redis Cache
 * Version: 2.0.0
 * Copyright (c) 2020. Mateusz Szymański Teamwant
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
 * @copyright Copyright 2020 © Teamwant Mateusz Szymański All right reserved
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * @category  Teamwant
 * @package   Teamwant
 *}

{$cache_config}
{include file='module:teamwant_redis/views/templates/admin/configuration_cache_disable.tpl'}
{include file='module:teamwant_redis/views/templates/admin/configuration_cache_blacklist.tpl'}
