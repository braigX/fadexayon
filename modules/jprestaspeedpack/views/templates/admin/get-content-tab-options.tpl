{*
* Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
*
*    @author    Jpresta
*    @copyright Jpresta
*    @license   See the license of this module in file LICENSE.txt, thank you.
*}
<div class="panel">
<h3>{if $avec_bootstrap}<i class="icon-gear"></i>{else}<img width="16" height="16" src="../img/admin/AdminPreferences.gif" alt=""/>{/if}&nbsp;{l s='Options' mod='jprestaspeedpack'}</h3>
<form id="pagecache_form_options" action="{$request_uri|escape:'html':'UTF-8'}" method="post">
    <input type="hidden" name="submitModule" value="true"/>
    <input type="hidden" name="pctab" value="options"/>
    <fieldset>
        <div style="clear: both;">
            <div class="form-group">
                <div id="pagecache_skiplogged">
                    <label class="control-label col-lg-3">
                        {l s='Cache for logged in users' mod='jprestaspeedpack'}
                    </label>
                    <div class="col-lg-9">
                        <span class="switch prestashop-switch fixed-width-lg">
                            <input type="radio" name="pagecache_skiplogged" id="pagecache_skiplogged_on" value="0" {if !$pagecache_skiplogged}checked{/if}>
                            <label for="pagecache_skiplogged_on" class="radioCheck">{l s='Yes' mod='jprestaspeedpack'}</label>
                            <input type="radio" name="pagecache_skiplogged" id="pagecache_skiplogged_off" value="1" {if $pagecache_skiplogged}checked{/if}>
                            <label for="pagecache_skiplogged_off" class="radioCheck">{l s='No' mod='jprestaspeedpack'}</label>
                            <a class="slide-button btn"></a>
                        </span>
                    </div>
                    <div class="col-lg-9 col-lg-offset-3">
                        <div class="help-block">
                            {l s='Enable cache for visitors that are logged in (recommended)' mod='jprestaspeedpack'}
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div id="pagecache_cache_customizable">
                    <label class="control-label col-lg-3">
                        {l s='Cache customizable products' mod='jprestaspeedpack'}
                    </label>
                    <div class="col-lg-9">
                        <span class="switch prestashop-switch fixed-width-lg">
                            <input type="radio" name="pagecache_cache_customizable" id="pagecache_cache_customizable_on" value="1" {if $pagecache_cache_customizable}checked{/if}>
                            <label for="pagecache_cache_customizable_on" class="radioCheck">{l s='Yes' mod='jprestaspeedpack'}</label>
                            <input type="radio" name="pagecache_cache_customizable" id="pagecache_cache_customizable_off" value="0" {if !$pagecache_cache_customizable}checked{/if}>
                            <label for="pagecache_cache_customizable_off" class="radioCheck">{l s='No' mod='jprestaspeedpack'}</label>
                            <a class="slide-button btn"></a>
                        </span>
                    </div>
                    <div class="col-lg-9 col-lg-offset-3">
                        <div class="help-block">
                            {l s='Here you can force the customizable products to be cached but make sure that customizations of visitors are not stored into the cache.' mod='jprestaspeedpack'}
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div id="pagecache_normalize_urls">
                    <label class="control-label col-lg-3">
                        {l s='Normalize URLs' mod='jprestaspeedpack'}
                    </label>
                    <div class="col-lg-9">
                        <span class="switch prestashop-switch fixed-width-lg">
                            <input type="radio" name="pagecache_normalize_urls" id="pagecache_normalize_urls_on" value="1" {if $pagecache_normalize_urls}checked{/if}>
                            <label for="pagecache_normalize_urls_on" class="radioCheck">{l s='Yes' mod='jprestaspeedpack'}</label>
                            <input type="radio" name="pagecache_normalize_urls" id="pagecache_normalize_urls_off" value="0" {if !$pagecache_normalize_urls}checked{/if}>
                            <label for="pagecache_normalize_urls_off" class="radioCheck">{l s='No' mod='jprestaspeedpack'}</label>
                            <a class="slide-button btn"></a>
                        </span>
                    </div>
                    <div class="col-lg-9 col-lg-offset-3">
                        <div class="help-block">
                            {l s='Avoid same page linked with different URLs to use different cache. Should only be disabled when you have a lot of links in a page (> 500).' mod='jprestaspeedpack'}
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div id="pagecache_logout_nocache">
                    <label class="control-label col-lg-3">
                        {l s='Force no cache at logout' mod='jprestaspeedpack'}
                    </label>
                    <div class="col-lg-9">
                        <span class="switch prestashop-switch fixed-width-lg">
                            <input type="radio" name="pagecache_logout_nocache" id="pagecache_logout_nocache_on" value="1" {if $pagecache_logout_nocache}checked{/if}>
                            <label for="pagecache_logout_nocache_on" class="radioCheck">{l s='Yes' mod='jprestaspeedpack'}</label>
                            <input type="radio" name="pagecache_logout_nocache" id="pagecache_logout_nocache_off" value="0" {if !$pagecache_logout_nocache}checked{/if}>
                            <label for="pagecache_logout_nocache_off" class="radioCheck">{l s='No' mod='jprestaspeedpack'}</label>
                            <a class="slide-button btn"></a>
                        </span>
                    </div>
                    <div class="col-lg-9 col-lg-offset-3">
                        <div class="help-block">
                            {l s='Add a "nocache" parameter in the URL after logout to avoid the browser cache to be used.' mod='jprestaspeedpack'}
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div id="pagecache_logs_debug">
                    <label class="control-label col-lg-3">
                        {l s='Enable logs' mod='jprestaspeedpack'}
                    </label>
                    <div class="col-lg-9">
                        <span class="switch prestashop-switch fixed-width-lg">
                            <input type="radio" name="pagecache_logs" id="pagecache_logs_debug_2" value="2" {if $pagecache_logs > 0}checked{/if}>
                            <label for="pagecache_logs_debug_2" class="radioCheck">{l s='Yes' mod='jprestaspeedpack'}</label>
                            {*<input type="radio" name="pagecache_logs" id="pagecache_logs_debug_1" value="1" {if $pagecache_logs == 1}checked{/if}>
                            <label for="pagecache_logs_debug_1" class="radioCheck">{l s='Info' mod='jprestaspeedpack'}</label>*}
                            <input type="radio" name="pagecache_logs" id="pagecache_logs_debug_0" value="0" {if $pagecache_logs == 0}checked{/if}>
                            <label for="pagecache_logs_debug_0" class="radioCheck">{l s='No' mod='jprestaspeedpack'}</label>
                            <a class="slide-button btn"></a>
                        </span>
                    </div>
                    <div class="col-lg-9 col-lg-offset-3">
                        <div class="help-block">
                            {l s='Logs informations into the Prestashop logger. You should only enable it to debug or understand how the cache works.' mod='jprestaspeedpack'}
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div id="pagecache_logs_debug">
                    <label class="control-label col-lg-3">
                        {l s='Ignored URL parameters' mod='jprestaspeedpack'}
                    </label>
                    <div class="col-lg-9">
                        <input type="text" name="pagecache_ignored_params" id="pagecache_ignored_params" value="{$pagecache_ignored_params|escape:'html':'UTF-8'}" size="100">
                    </div>
                    <div class="col-lg-9 col-lg-offset-3">
                        <div class="help-block">
                            {l s='URL parameters are used to identify a unique page content. Some URL parameters do not affect page content like tracking parameters for analytics (utm_source, utm_campaign, etc.) so we can ignore them. You can set a comma separated list of these parameters here.' mod='jprestaspeedpack'}
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div id="pagecache_always_infosbox">
                    <label class="control-label col-lg-3">
                        {l s='Always display infos box' mod='jprestaspeedpack'}
                    </label>
                    <div class="col-lg-9">
                        <span class="switch prestashop-switch fixed-width-lg">
                            <input type="radio" name="pagecache_always_infosbox" id="pagecache_always_infosbox_on" value="1" {if $pagecache_always_infosbox}checked{/if}>
                            <label for="pagecache_always_infosbox_on" class="radioCheck">{l s='Yes' mod='jprestaspeedpack'}</label>
                            <input type="radio" name="pagecache_always_infosbox" id="pagecache_always_infosbox_off" value="0" {if !$pagecache_always_infosbox}checked{/if}>
                            <label for="pagecache_always_infosbox_off" class="radioCheck">{l s='No' mod='jprestaspeedpack'}</label>
                            <a class="slide-button btn"></a>
                        </span>
                    </div>
                    <div class="col-lg-9 col-lg-offset-3">
                        <div class="help-block">
                            {l s='Only used for demo' mod='jprestaspeedpack'}
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div id="pagecache_exec_header_hook">
                    <label class="control-label col-lg-3">
                        {l s='Executes "header" hook in dynamic modules request' mod='jprestaspeedpack'}
                    </label>
                    <div class="col-lg-9">
                        <span class="switch prestashop-switch fixed-width-lg">
                            <input type="radio" name="pagecache_exec_header_hook" id="pagecache_exec_header_hook_on" value="1" {if $pagecache_exec_header_hook}checked{/if}>
                            <label for="pagecache_exec_header_hook_on" class="radioCheck">{l s='Yes' mod='jprestaspeedpack'}</label>
                            <input type="radio" name="pagecache_exec_header_hook" id="pagecache_exec_header_hook_off" value="0" {if !$pagecache_exec_header_hook}checked{/if}>
                            <label for="pagecache_exec_header_hook_off" class="radioCheck">{l s='No' mod='jprestaspeedpack'}</label>
                            <a class="slide-button btn"></a>
                        </span>
                    </div>
                    <div class="col-lg-9 col-lg-offset-3">
                        <div class="help-block">
                            {l s='If checked, the header hook will be executed so javascript variables added in this hook by other modules will be refreshed' mod='jprestaspeedpack'}
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div id="pagecache_use_dispatcher_hook">
                    <label class="control-label col-lg-3">
                        {l s='Check cache in "dispatcher" hook' mod='jprestaspeedpack'}
                    </label>
                    <div class="col-lg-9">
                        <span class="switch prestashop-switch fixed-width-lg">
                            <input type="radio" name="pagecache_use_dispatcher_hook" id="pagecache_use_dispatcher_hook_on" value="1" {if $pagecache_use_dispatcher_hook}checked{/if}>
                            <label for="pagecache_use_dispatcher_hook_on" class="radioCheck">{l s='Yes' mod='jprestaspeedpack'}</label>
                            <input type="radio" name="pagecache_use_dispatcher_hook" id="pagecache_use_dispatcher_hook_off" value="0" {if !$pagecache_use_dispatcher_hook}checked{/if}>
                            <label for="pagecache_use_dispatcher_hook_off" class="radioCheck">{l s='No' mod='jprestaspeedpack'}</label>
                            <a class="slide-button btn"></a>
                        </span>
                    </div>
                    <div class="col-lg-9 col-lg-offset-3">
                        <div class="help-block">
                            {l s='If checked, the cache will be checked in dispatcher hook (not dispatcherBefore) which is slower but compatible with some modules like securitypro' mod='jprestaspeedpack'}
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div id="pagecache_product_refreshEveryX">
                    <label class="control-label col-lg-3">
                        {l s='Refresh product page every X sales' mod='jprestaspeedpack'}
                    </label>
                    <div class="col-lg-9">
                        {l s='Every' mod='jprestaspeedpack'}
                        <select style="display: inline-block; width: fit-content;" name="pagecache_product_refreshEveryX" class="form-control">
                            <option value="1" {if $pagecache_product_refreshEveryX == 1} selected{/if}>1</option>
                            <option value="5" {if $pagecache_product_refreshEveryX == 5} selected{/if}>5</option>
                            <option value="10" {if $pagecache_product_refreshEveryX == 10} selected{/if}>10</option>
                            <option value="50" {if $pagecache_product_refreshEveryX == 50} selected{/if}>50</option>
                            <option value="100" {if $pagecache_product_refreshEveryX == 100} selected{/if}>100</option>
                        </select>
                        {l s='sales' mod='jprestaspeedpack'}
                    </div>
                    <div class="col-lg-9 col-lg-offset-3">
                        <div class="help-block">
                            {l s='When stock is not displayed on product page then you can set how often the cache of the product page should be refreshed when the quantity is greater than the quantity that displays a "last items..."' mod='jprestaspeedpack'}
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div id="pagecache_instockisadd">
                    <label class="control-label col-lg-3">
                        {l s='Back in stock refreshes like new product' mod='jprestaspeedpack'}
                    </label>
                    <div class="col-lg-9">
                        <span class="switch prestashop-switch fixed-width-lg">
                            <input type="radio" name="pagecache_instockisadd" id="pagecache_instockisadd_on" value="1" {if $pagecache_instockisadd}checked{/if}>
                            <label for="pagecache_instockisadd_on" class="radioCheck">{l s='Yes' mod='jprestaspeedpack'}</label>
                            <input type="radio" name="pagecache_instockisadd" id="pagecache_instockisadd_off" value="0" {if !$pagecache_instockisadd}checked{/if}>
                            <label for="pagecache_instockisadd_off" class="radioCheck">{l s='No' mod='jprestaspeedpack'}</label>
                            <a class="slide-button btn"></a>
                        </span>
                    </div>
                    <div class="col-lg-9 col-lg-offset-3">
                        <div class="help-block">
                            {l s='When a product is back in stock the cache will be refreshed like if the product was a new one' mod='jprestaspeedpack'}
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div id="pagecache_max_exec_time">
                    <label class="control-label col-lg-3">
                        {l s='Max execution time in seconds' mod='jprestaspeedpack'}
                    </label>
                    <div class="col-lg-9">
                        <input type="number" name="pagecache_max_exec_time" id="pagecache_max_exec_time" value="{$pagecache_max_exec_time|escape:'html':'UTF-8'}" max="480" min="1">
                    </div>
                    <div class="col-lg-9 col-lg-offset-3">
                        <div class="help-block">
                            {l s='Used by the cache warmer to split the list of URLs to browse if it takes much time to generate. Must be between 1s and 480s, we recommend 30s.' mod='jprestaspeedpack'}
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div id="pagecache_maxrows">
                    <label class="control-label col-lg-3">
                        {l s='Max pages in cache' mod='jprestaspeedpack'}
                    </label>
                    <div class="col-lg-9">
                        <input type="number" name="pagecache_maxrows" id="pagecache_maxrows" value="{$pagecache_maxrows|escape:'html':'UTF-8'}" min="0">
                    </div>
                    <div class="col-lg-9 col-lg-offset-3">
                        <div class="help-block">
                            {l s='Let this value to 0 until you have a really good reason to limit the number of rows into the cache' mod='jprestaspeedpack'}
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div id="pagecache_ignore_before_pattern">
                    <label class="control-label col-lg-3">
                        {l s='Ignore backlinks before this string' mod='jprestaspeedpack'}
                    </label>
                    <div class="col-lg-9">
                        <input type="text" name="pagecache_ignore_before_pattern" id="pagecache_ignore_before_pattern" value="{$pagecache_ignore_before_pattern|escape:'html':'UTF-8'}">
                    </div>
                    <div class="col-lg-9 col-lg-offset-3">
                        <div class="help-block">
                            {l s='Usefull to ignore links of a mega menu (for exemple) that are not necessary for automatic refreshment. This will decrease the size of the backlinks table (jm_pagecache_bl). Exemple: </header>.' mod='jprestaspeedpack'}
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div id="pagecache_ignore_after_pattern">
                    <label class="control-label col-lg-3">
                        {l s='Ignore backlinks after this string' mod='jprestaspeedpack'}
                    </label>
                    <div class="col-lg-9">
                        <input type="text" name="pagecache_ignore_after_pattern" id="pagecache_ignore_after_pattern" value="{$pagecache_ignore_after_pattern|escape:'html':'UTF-8'}">
                    </div>
                    <div class="col-lg-9 col-lg-offset-3">
                        <div class="help-block">
                            {l s='Usefull to ignore links of a side mobile menu (for exemple) that are not necessary for automatic refreshment. This will decrease the size of the backlinks table (jm_pagecache_bl). Exemple: </footer>.' mod='jprestaspeedpack'}
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div id="pagecache_ignore_url_regex">
                    <label class="control-label col-lg-3">
                        {l s='Ignore URLs matching this regex' mod='jprestaspeedpack'}
                    </label>
                    <div class="col-lg-9">
                        <input type="text" name="pagecache_ignore_url_regex" id="pagecache_ignore_url_regex" value="{$pagecache_ignore_url_regex|escape:'html':'UTF-8'}">
                    </div>
                    <div class="col-lg-9 col-lg-offset-3">
                        <div class="help-block">
                            {l s='You can avoid some pages to be cached. Setup a regular expression that will match URLs that must not be cached. Read https://www.php.net/manual/en/reference.pcre.pattern.syntax.php for more informations. Use https://regex101.com/ to test your regular expression.' mod='jprestaspeedpack'}
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div id="pagecache_ignore_referers">
                    <label class="control-label col-lg-3">
                        {l s='Disable cache for referers' mod='jprestaspeedpack'}
                    </label>
                    <div class="col-lg-9">
                        <input type="text" name="pagecache_ignore_referers" id="pagecache_ignore_referers" value="{$pagecache_ignore_referers|escape:'html':'UTF-8'}">
                    </div>
                    <div class="col-lg-9 col-lg-offset-3">
                        <div class="help-block">
                            {l s='Disable the cache when the referer (previous page URL) matches one of the URLs in this comma-separated list. This can be useful when using SSO.' mod='jprestaspeedpack'}
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div id="pagecache_currencies_to_cache">
                    <label class="control-label col-lg-3">
                        {l s='Currencies to cache' mod='jprestaspeedpack'}
                    </label>
                    <div class="col-lg-9">
                        {if count($pagecache_currencies_to_cache) === 0}
                            <i>{l s='No currency are enabled on the shop' mod='jprestaspeedpack'}</i>
                        {/if}
                        {foreach $pagecache_currencies_to_cache as $cur_iso_code => $cur_state}
                            <span style="margin-right: 1rem;white-space: nowrap;">
                                <input type="checkbox"
                                       style="vertical-align: middle; margin: 0 2px;"
                                       id="pagecache_currencies_to_cache_{$cur_iso_code|escape:'html':'UTF-8'}"
                                       name="pagecache_currencies_to_cache[]"
                                       {if $cur_state}checked="checked" {/if}
                                       value="{$cur_iso_code|escape:'html':'UTF-8'}">
                                <label for="pagecache_currencies_to_cache_{$cur_iso_code|escape:'html':'UTF-8'}">{$cur_iso_code|escape:'html':'UTF-8'}</label>
                            </span>
                        {/foreach}
                    </div>
                    <div class="col-lg-9 col-lg-offset-3">
                        <div class="help-block">
                            {l s='Here you can avoid some currencies to be cached, usefull when the rate changes everyday.' mod='jprestaspeedpack'}
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div id="pagecache_statsttfb">
                    <label class="control-label col-lg-3">
                        {l s='Enable statistics on TTFB' mod='jprestaspeedpack'}
                    </label>
                    <div class="col-lg-9">
                        <span class="switch prestashop-switch fixed-width-lg">
                            <input type="radio" name="pagecache_statsttfb" id="pagecache_statsttfb_on" value="1" {if $pagecache_statsttfb}checked{/if}>
                            <label for="pagecache_statsttfb_on" class="radioCheck">{l s='Yes' mod='jprestaspeedpack'}</label>
                            <input type="radio" name="pagecache_statsttfb" id="pagecache_statsttfb_off" value="0" {if !$pagecache_statsttfb}checked{/if}>
                            <label for="pagecache_statsttfb_off" class="radioCheck">{l s='No' mod='jprestaspeedpack'}</label>
                            <a class="slide-button btn"></a>
                        </span>
                    </div>
                    <div class="col-lg-9 col-lg-offset-3">
                        <div class="help-block">
                            {l s='Store statistics on TTFB, recommended if you subscribed to the cache-warmer but you should disable it if you have a large amount of visitors every day' mod='jprestaspeedpack'}
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="bootstrap">
            <button type="submit" value="1" id="submitModuleOptions" name="submitModuleOptions" class="btn btn-default pull-right">
                <i class="process-icon-save"></i> {l s='Save' mod='jprestaspeedpack'}
            </button>
        </div>
    </fieldset>
</form>
</div>
