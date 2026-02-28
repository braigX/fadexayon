{*
* Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
*
*    @author    Jpresta
*    @copyright Jpresta
*    @license   See the license of this module in file LICENSE.txt, thank you.
*}
<div class="panel">
<h3>{if $avec_bootstrap}<i class="icon-gear"></i>{else}<img width="16" height="16" src="../img/admin/AdminPreferences.gif" alt=""/>{/if}&nbsp;{l s='Minification and other options' mod='jprestaspeedpack'}</h3>
<form id="pagecache_form_options" action="{$request_uri|escape:'html':'UTF-8'}" method="post">
    <input type="hidden" name="submitModule" value="true"/>
    <input type="hidden" name="pctab" value="otheroptions"/>
    <fieldset>
        <div style="clear: both;">
            <div class="form-group">
                <div class="alert alert-warning">
                    <p><strong>{l s='Before enabling the HTML minifier, you must first activate test mode to ensure it works properly on your shop.' mod='jprestaspeedpack'}</strong></p>
                    <p>{l s='Minification can break the HTML if it is not well-formed—for example, if a DIV is placed inside the HEAD section, or if a tag is not properly closed.' mod='jprestaspeedpack'}</p>
                    <p>{l s='If you\'re unable to get it working correctly, simply disable the option.' mod='jprestaspeedpack'}</p>
                </div>
                <div id="pagecache_minifyhtml">
                    <label class="control-label col-lg-2">
                        {l s='Minify HTML' mod='jprestaspeedpack'}
                    </label>
                    <div class="col-lg-10">
                        {if !$pagecache_minifyhtml_enabled}
                            <label class="radioCheck">{l s='Disabled because you need PHP 7 minimum' mod='jprestaspeedpack'}</label>
                        {else}
                            <span class="switch prestashop-switch fixed-width-lg">
                                <input type="radio" name="pagecache_minifyhtml" id="pagecache_minifyhtml_on" value="1" {if $pagecache_minifyhtml}checked{/if}>
                                <label for="pagecache_minifyhtml_on" class="radioCheck">{l s='Yes' mod='jprestaspeedpack'}</label>
                                <input type="radio" name="pagecache_minifyhtml" id="pagecache_minifyhtml_off" value="0" {if !$pagecache_minifyhtml}checked{/if}>
                                <label for="pagecache_minifyhtml_off" class="radioCheck">{l s='No' mod='jprestaspeedpack'}</label>
                                <a class="slide-button btn"></a>
                            </span>
                        {/if}
                    </div>
                    <div class="col-lg-10 col-lg-offset-2">
                        <div class="help-block">
                            {l s='Minify HTML and inline CSS (while preserving inline JavaScript to prevent errors). This reduces the cache size on disk and decreases download size, resulting in faster load times for visitors.' mod='jprestaspeedpack'}
                        </div>
                    </div>
                </div>
            </div>

        <div class="bootstrap">
            <button type="submit" value="1" id="submitModuleOtherOptions" name="submitModuleOtherOptions" class="btn btn-default pull-right">
                <i class="process-icon-save"></i> {l s='Save' mod='jprestaspeedpack'}
            </button>
        </div>
    </fieldset>
</form>
</div>
