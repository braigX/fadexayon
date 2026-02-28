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

<div class="tw-redis-configuration-section">
    <div class="tw-table-section">
        <table class="table-auto teamwant-redis-table">
            <thead>
            <tr>
                <th width="10%">{Teamwant_redis::staticModuleTranslate('Scheme')}</th>
                <th width="70%" colspan="2">{Teamwant_redis::staticModuleTranslate('Host')}</th>
                <th width="10%">{Teamwant_redis::staticModuleTranslate('Port')}</th>
                <th width="10%">{Teamwant_redis::staticModuleTranslate('Actions')}</th>
            </tr>
            </thead>
            <tbody>
            {if $config}
                {foreach from=$config['_servers'] key=k item=elem}
                    <tr>
                        <td>
                            <input type="text"
                                   placeholder="{Teamwant_redis::staticModuleTranslate('Scheme')}"
                                   name="teamwant_redis_row[{$k}][scheme]"
                                   value="{if isset($elem['scheme'])}{$elem['scheme']}{/if}"
                                   style="max-width: 70px;"
                            >
                        </td>
                        <td colspan="2">
                            <input type="text"
                                   placeholder="{Teamwant_redis::staticModuleTranslate('Host')}"
                                   name="teamwant_redis_row[{$k}][host]"
                                   value="{if isset($elem['host'])}{$elem['host']}{/if}"
                                   style="min-width: 50%;"
                            >
                        </td>
                        <td>
                            <input type="text"
                                   placeholder="{Teamwant_redis::staticModuleTranslate('Port')}"
                                   name="teamwant_redis_row[{$k}][port]"
                                   value="{if isset($elem['port'])}{$elem['port']}{/if}"
                                   style="max-width: 70px;"
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
                                   name="teamwant_redis_row[{$k}][alias]"
                                   value="{if isset($elem['alias'])}{$elem['alias']}{/if}"
                            >
                                    </label>
                                </div>
                                <div class="col-3 col-3_1700">
                                    <label class="w-20">{Teamwant_redis::staticModuleTranslate('Username')}
                                    <input type="text"
                                   placeholder="{Teamwant_redis::staticModuleTranslate('Username')}"
                                   name="teamwant_redis_row[{$k}][username]"
                                   value="{if isset($elem['username'])}{$elem['username']}{/if}"
                                    autocomplete="off"
                            >
                                        </label>
                                </div>
                                <div class="col-3 col-3_1700">
                                     <label class="w-20">{Teamwant_redis::staticModuleTranslate('Password')}
                                    <input type="password"
                                   placeholder="{Teamwant_redis::staticModuleTranslate('Password')}"
                                   name="teamwant_redis_row[{$k}][password]"
                                   value="{if isset($elem['password'])}{$elem['password']}{/if}"
                                    autocomplete="off"
                            >
                                         </label>
                                </div>
                                <div class="col-1 col-1_1700">
                                    <label class="w-20">{Teamwant_redis::staticModuleTranslate('Index')}
                                    <input type="text"
                                   placeholder="{Teamwant_redis::staticModuleTranslate('Index')}"
                                   name="teamwant_redis_row[{$k}][database]"
                                   value="{if isset($elem['database'])}{$elem['database']}{/if}"
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


                {/foreach}
            {/if}
            <tr class="btn-section-for-table">
                <td colspan="2">
                    <button class="btn btn-primary pointer teamwant-redis--button"
                            id="addNextAdminRedisConfigurationRow"
                    >
                        {Teamwant_redis::staticModuleTranslate('Add server')}
                    </button>
                </td>
                <td colspan="3">
                    <p style="width: 100%; display: flex;">
                        <label for="url_healtcheck" style=" min-width: 120px; padding-top: 5px; ">
                           {Teamwant_redis::staticModuleTranslate('Url to healthcheck:')}
                        </label>
                        <input id="url_healtcheck"
                               type="text"
                               value="{$url_healthcheck}"
                               style=" min-width: unset !important; "
                        />
                    </p>
                </td>
            </tr>
            </tbody>
        </table>


        {*1.3.0*}
        <div class="tw-configuration-section">
            <div class="card-text" style="width: 100%;padding: 0;margin: 60px 0 0 0;max-width: 100%;">
                <div class="row">
                    <div class="col">
                        <div class="alert alert-warning" role="alert">
                            <p class="alert-text">
                                {Teamwant_redis::staticModuleTranslate('If your site is still running slowly, try changing your server settings to <b><a target="_blank" href="https://gist.github.com/zixxus/6d7b1a8af93dec53d5ce22cbbdd313c0">unix socket</a></b>. You can also change your database engine to <b><a target="_blank" href="https://docs.keydb.dev/docs/download">KeyDB</a></b> or <b><a target="_blank" href="https://www.dragonflydb.io/docs/getting-started">Dragonflydb</a></b>.')|html_entity_decode}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="alert alert-info" role="alert">
                            <p class="alert-text">
                                {Teamwant_redis::staticModuleTranslate('Additional configuration')}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="form-control-label ">
                        {Teamwant_redis::staticModuleTranslate('Use prefix for keys?')}
                    </label>
                    <div class="col-sm">
                        <span class="ps-switch">
                            <input
                                    id="form_twredis_use_prefix_0"
                                    class="ps-switch"
                                    name="form[twredis][use_prefix]"
                                    value="0"
                                    {if !empty($config['_config']['use_prefix']) and ($config['_config']['use_prefix'] === false or empty($config['_config']['use_prefix']) ) }checked=""{/if}
                                    type="radio"
                            />
                            <label for="form_twredis_use_prefix_0">No</label>
                            <input
                                    id="form_twredis_use_prefix_1"
                                    class="ps-switch"
                                    name="form[twredis][use_prefix]"
                                    {if !empty($config['_config']['use_prefix']) and ($config['_config']['use_prefix'] === true or $config['_config']['use_prefix'] === 1) }checked=""{/if}
                                    value="1"
                                    type="radio"
                            />
                            <label for="form_twredis_use_prefix_1">Yes</label>
                            <span class="slide-button"></span>
                        </span>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="form-control-label ">
                        {Teamwant_redis::staticModuleTranslate('Key prefix')}
                        <p style=" font-weight: 900; font-size: 9px; ">{Teamwant_redis::staticModuleTranslate('Leave blank to generate automatically')}</p>
                    </label>
                    <div class="col-sm">
                        <input type="text" id="twredis_prefix"
                               name="form[twredis][prefix]" class="form-control"
                               {if !empty($config['_config']['prefix'])}value="{$config['_config']['prefix']|html_entity_decode}"{/if}
                        >
                    </div>
                </div>
                <div class="form-group row">
                    <label class="form-control-label ">
                        {Teamwant_redis::staticModuleTranslate('Default cache timeout')}
                        <p style=" font-weight: 900; font-size: 9px; ">{Teamwant_redis::staticModuleTranslate('blank or 0 for unlimited (minutes)')}</p>
                    </label>
                    <div class="col-sm">
                        <input type="number" id="twredis_defalut_ttl"
                               name="form[twredis][defalut_ttl]" class="form-control"
                               {if !empty($config['_config']['defalut_ttl'])}value="{$config['_config']['defalut_ttl']|html_entity_decode}"{/if}
                        >
                    </div>
                </div>
                {if $enableEngineSwitch}
                    <div class="form-group row">
                        <label class="form-control-label ">
                            {Teamwant_redis::staticModuleTranslate('Redis Engine')}
                            <p style=" font-weight: 900; font-size: 9px; "></p>
                        </label>
                        <div class="col-sm">
                            <select name="form[twredis][redis_engine]" class="form-control">
                                    <option value="predis">predis</option>
                                    {if $phpredis_enable}
                                    <option value="phpredis"
                                        {if !empty($config['_config']['redis_engine']) && $config['_config']['redis_engine'] == 'phpredis'}selected="select"{/if}
                                    >phpredis (May be faster)</option>
                                    {/if}
                            </select>
                        </div>
                    </div>
                {/if}
                <div class="form-group row">
                    <label class="form-control-label ">
                        {Teamwant_redis::staticModuleTranslate('Use multistore?')}
                        <p style="font-weight: 900;font-size: 9px;max-width: 250px;margin-left: auto;">{Teamwant_redis::staticModuleTranslate('If your store uses multistore, checking this option can resolve duplicate data errors.')}</p>
                    </label>
                    <div class="col-sm">
                        <span class="ps-switch">
                            <input
                                    id="form_twredis_use_multistore_0"
                                    class="ps-switch"
                                    name="form[twredis][use_multistore]"
                                    value="0"
                                    {if !empty($config['_config']['use_multistore']) and ($config['_config']['use_multistore'] === false or empty($config['_config']['use_multistore']) ) }checked=""{/if}
                                    type="radio"
                            />
                            <label for="form_twredis_use_multistore_0">No</label>
                            <input
                                    id="form_twredis_use_multistore_1"
                                    class="ps-switch"
                                    name="form[twredis][use_multistore]"
                                    {if !empty($config['_config']['use_multistore']) and ($config['_config']['use_multistore'] === true or $config['_config']['use_multistore'] === 1) }checked=""{/if}
                                    value="1"
                                    type="radio"
                            />
                            <label for="form_twredis_use_multistore_1">Yes</label>
                            <span class="slide-button"></span>
                        </span>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="form-control-label ">
                        {Teamwant_redis::staticModuleTranslate('Limit data size')}
                        <p style="font-weight: 900;font-size: 9px;max-width: 250px;margin-left: auto;">{Teamwant_redis::staticModuleTranslate('If the data sent to Redis exceeds 15MB, Redis will skip caching it.')}</p>
                    </label>
                    <div class="col-sm">
                        <span class="ps-switch">
                            <input
                                    id="form_twredis_limit_data_size_0"
                                    class="ps-switch"
                                    name="form[twredis][limit_data_size]"
                                    value="0"
                                    {if !empty($config['_config']['limit_data_size']) and ($config['_config']['limit_data_size'] === false or empty($config['_config']['limit_data_size']) ) }checked=""{/if}
                                    type="radio"
                            />
                            <label for="form_twredis_limit_data_size_0">No</label>
                            <input
                                    id="form_twredis_limit_data_size_1"
                                    class="ps-switch"
                                    name="form[twredis][limit_data_size]"
                                    {if !empty($config['_config']['limit_data_size']) and ($config['_config']['limit_data_size'] === true or $config['_config']['limit_data_size'] === 1) }checked=""{/if}
                                    value="1"
                                    type="radio"
                            />
                            <label for="form_twredis_limit_data_size_1">Yes</label>
                            <span class="slide-button"></span>
                        </span>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="form-control-label ">
                        {Teamwant_redis::staticModuleTranslate('Use cache in admin panel?')}
                        <p style="font-weight: 900; font-size: 9px; max-width: 250px;margin-left: auto;">{Teamwant_redis::staticModuleTranslate('We recommend that this option be turned off, it can cause problems with invoice numbering, etc.')}</p>
                    </label>
                    <div class="col-sm">
                        <span class="ps-switch">
                            <input
                                    id="form_twredis_use_cache_admin_0"
                                    class="ps-switch"
                                    name="form[twredis][use_cache_admin]"
                                    value="0"
                                    {if !empty($config['_config']['use_cache_admin']) and ($config['_config']['use_cache_admin'] === false or empty($config['_config']['use_cache_admin']) ) }checked=""{/if}
                                    type="radio"
                            />
                            <label for="form_twredis_use_cache_admin_0">No</label>
                            <input
                                    id="form_twredis_use_cache_admin_1"
                                    class="ps-switch"
                                    name="form[twredis][use_cache_admin]"
                                    value="1"
                                    {if !empty($config['_config']['use_cache_admin']) and ($config['_config']['use_cache_admin'] === true or $config['_config']['use_cache_admin'] === 1) }checked=""{/if}
                                    type="radio"
                            />
                            <label for="form_twredis_use_cache_admin_1">Yes</label>
                            <span class="slide-button"></span>
                        </span>
                    </div>
                </div>
                <div class="form-group row" style=" display: flex; flex-direction: row-reverse; ">
                    <button class="btn btn-primary pointer teamwant-redis--button"
                            id="saveAdminRedisConfigurationRow"
                            type="button"
                            style=" background: #5680e2; border-color: #5e6aec; margin-right: 18px; "
                    >
                        <div class="loader simple-circle"></div>
                        {Teamwant_redis::staticModuleTranslate('Save')}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>