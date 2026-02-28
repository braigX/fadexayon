{*
* Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
*
*    @author    Jpresta
*    @copyright Jpresta
*    @license   See the license of this module in file LICENSE.txt, thank you.
*}
<div class="panel">
    <h3>{if $avec_bootstrap}<i class="icon-link"></i>{else}<img width="16" height="16" src="../img/admin/subdomain.gif" alt=""/>{/if}&nbsp;{l s='API (URLs to clear the cache)' mod='jprestaspeedpack'}</h3>
    <form id="pagecache_form_cron" action="{$request_uri|escape:'html':'UTF-8'}" method="post">
        <input type="hidden" name="submitModule" value="true"/>
        <input type="hidden" name="pctab" value="cron"/>
        <fieldset>
            {if $avec_bootstrap}
                <div class="bootstrap"><div class="alert alert-info" style="display: block;">&nbsp;{l s='Here you will find URLs to clear the cache from a script (you can do it manually in the statistics table). Be aware that the cache cleans itself continuously, these URLs are only for users modifying the database not with Prestashop (hooks are not called).' mod='jprestaspeedpack'}</div></div>
            {else}
                <div class="hint clear" style="display: block;">&nbsp;{l s='Here you will find URLs to clear the cache from a script (you can do it manually in the statistics table). Be aware that the cache cleans itself continuously, these URLs are only for users modifying the database not with Prestashop (hooks are not called).' mod='jprestaspeedpack'}</div>
            {/if}
            <p>{l s='People who want to clear cache can use the following URLs (one per shop, returns 200 if OK, 404 if there is an error): ' mod='jprestaspeedpack'}</p>
            <ul>
                {foreach $pagecache_cron_urls as $controller_name => $cron_url}
                    <li><pre>{$cron_url|escape:'javascript':'UTF-8'}</pre></li>
                {/foreach}
            </ul>

            <p>
                {l s='To refresh cache of a specific product add "&product=<product\'s ids separated by commas>", for a category add "&category=<category\'s ids separated by commas>", for home page add "&index", etc.' mod='jprestaspeedpack'}
                {l s='Available controller (type of page) are' mod='jprestaspeedpack'}
            </p>
            <ul>
                <li>index (no IDs)</li>
                <li>category</li>
                <li>product</li>
                <li>cms</li>
                <li>newproducts (no IDs)</li>
                <li>bestsales (no IDs)</li>
                <li>supplier</li>
                <li>manufacturer</li>
                <li>contact (no IDs)</li>
                <li>pricesdrop (no IDs)</li>
                <li>sitemap (no IDs)</li>
            </ul>
            <p>
                {l s='When you refresh the cache of a product, or a category, etc. then the pages that have a link to this product or category will also be refreshed except if you add this parameter in the URL' mod='jprestaspeedpack'}:
                <pre>&delete_linking_pages=0</pre>
            </p>
        </fieldset>
    </form>
    <h4>{l s='Purge of the cache' mod='jprestaspeedpack'}</h4>
    <p>{l s='As we explained above the cache cleans itself continously but it can be a good thing to purge the cache once a week with this URL.' mod='jprestaspeedpack'}</p>
    <p>{l s='A purge will delete all obsolete datas from the cache, it will not decrease the hit rate.' mod='jprestaspeedpack'}</p>
    <ul>
        {foreach $pagecache_cron_urls as $controller_name => $cron_url}
            <li><pre>{$cron_url|escape:'javascript':'UTF-8'}&purge</pre></li>
        {/foreach}
    </ul>
    {if $avec_bootstrap}
        <div class="bootstrap"><div class="alert alert-info" style="display: block;">&nbsp;{l s='If you subscribed to the JPresta-Cache-Warmer service, the purge is processed automatically; there\'s no need to schedule a CRON job.' mod='jprestaspeedpack'}</div></div>
    {else}
        <div class="hint clear" style="display: block;">&nbsp;{l s='If you subscribed to the JPresta-Cache-Warmer service, the purge is processed automatically; there\'s no need to schedule a CRON job.' mod='jprestaspeedpack'}</div>
    {/if}

</div>
