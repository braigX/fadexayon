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

<tr>
    <td>
        <input type="text"
               placeholder="{Teamwant_redis::staticModuleTranslate('Scheme')}"
               name="teamwant_redis_row[{$uniq}][scheme]"
               value=""
        >
    </td>
    <td colspan="2">
        <input type="text"
               placeholder="{Teamwant_redis::staticModuleTranslate('Host')}"
               name="teamwant_redis_row[{$uniq}][host]"
               value=""
        >
    </td>
    <td>
        <input type="text"
               placeholder="{Teamwant_redis::staticModuleTranslate('Port')}"
               name="teamwant_redis_row[{$uniq}][port]"
               value=""
        >
    </td>
    <td class="last-td">
        <button class="btn btn-primary pointer teamwant-redis--button"
                type="button"
                data-action="testAdminRedisConfigurationHost"
        >
            <div class="loader simple-circle"></div>
            {Teamwant_redis::staticModuleTranslate('Test connection')}
        </button>
    </td>
</tr>
<tr>
    <td colspan="999">
        <button class="btn btn-primary pointer teamwant-redis--button showmore"
                data-action="adminRedisShowMore"
                data-textstart="{Teamwant_redis::staticModuleTranslate('Show More')}"
                data-textstop="{Teamwant_redis::staticModuleTranslate('Show Less')}"
                type="button"
        >
            {Teamwant_redis::staticModuleTranslate('Show More')}
        </button>
    </td>
</tr>
<tr class="d-none d-none_1700" aria-labelledby="dropdownMenuButton">
    <td colspan="999">
        <div class="row">
            <div class="col-3 col-3_1700">
                <label class="w-20">{Teamwant_redis::staticModuleTranslate('Alias')}
                <input type="text"
                       placeholder="{Teamwant_redis::staticModuleTranslate('Alias')}"
                       name="teamwant_redis_row[{$uniq}][alias]"
                       value=""
                >
                </label>
            </div>
            <div class="col-3 col-3_1700">
                <label class="w-20">{Teamwant_redis::staticModuleTranslate('Username')}
                    <input type="text"
                           placeholder="{Teamwant_redis::staticModuleTranslate('Username')}"
                           name="teamwant_redis_row[{$uniq}][username]"
                           value=""
                           autocomplete="off"
                    >
                </label>

            </div>
            <div class="col-3 col-3_1700">
                 <label class="w-20">{Teamwant_redis::staticModuleTranslate('Password')}
                <input type="password"
                       placeholder="{Teamwant_redis::staticModuleTranslate('Password')}"
                       name="teamwant_redis_row[{$uniq}][password]"
                       value=""
                       autocomplete="off"
                >
                 </label>

            </div>
            <div class="col-1 col-1_1700">
                <label class="w-20">{Teamwant_redis::staticModuleTranslate('Index')}
                <input type="text"
                       placeholder="{Teamwant_redis::staticModuleTranslate('Index')}"
                       name="teamwant_redis_row[{$uniq}][database]"
                       value=""
                >
                </label>

            </div>
            <div class="col-2 mt-auto mb-auto col-2_1700">
                <button class="btn btn-primary pointer teamwant-redis--button float-right"
                        type="button"
                        data-action="removeAdminRedisConfigurationRow"
                >
                    <i class="material-icons">delete</i> {Teamwant_redis::staticModuleTranslate('Remove row')}
                </button>
            </div>
        </div>
    </td>
</tr>
<tr>
    <td colspan="999" class="pl-0 pr-0 line_1700">
        <hr class="m-0 hr_1700" style="opacity: 0.3">
    </td>
</tr>