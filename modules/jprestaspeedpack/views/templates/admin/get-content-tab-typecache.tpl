{*
* Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
*
*    @author    Jpresta
*    @copyright Jpresta
*    @license   See the license of this module in file LICENSE.txt, thank you.
*}
<script type="application/javascript">
    $(function () {
        $("input[name=pagecache_typecache]").click(function(){
            $('.pagecache_typecache_conf').hide();
            $('#pagecache_typecache_conf_'+$(this).val()).show();
            $('#pagecache_typecache_conf_'+$(this).val()+' input').first().focus();
            checkMemcacheConf();
            checkMemcachedConf();
        });
    });
    function checkMemcacheConf() {
        $('#memcache_test').prop('disabled', !$('#memcache_host').val() || !$('#memcache_port').val());
        $('#testMemcacheResult').html('').hide();
    }
    function testMemcache() {
        $('#memcache_test').prop('disabled', true);
        $('#testMemcacheResult').html('{l s='Checking connection...' mod='jprestaspeedpack'}').show();
        $.ajax({ url: '{$pagecache_typecache_memcache_testurl|escape:'javascript':'UTF-8'}', cache: true, data: { host:$('#memcache_host').val(), port:$('#memcache_port').val() },
            success: function(response) {
                let result = JSON.parse(response);
                if (result.status === 1) {
                    $('#testMemcacheResult').html('<div class="alert alert-success" role="alert"><strong>'+result.host+':'+result.port+'</strong> '+result.comments+'</div>');
                }
                else {
                    $('#testMemcacheResult').html('<div class="alert alert-danger" role="alert"><strong>'+result.host+':'+result.port+'</strong> '+result.comments+'</div>');
                }

            },
            error: function(result, status, error) {
                $('#testMemcacheResult').html(result + ' - ' + status + ' - ' + error);
            }});
        $('#memcache_test').prop('disabled', false);
    }
    function checkMemcachedConf() {
        $('#memcached_test').prop('disabled', !$('#memcached_host').val() || !$('#memcached_port').val());
        $('#testMemcachedResult').html('').hide();
    }
    function testMemcached() {
        $('#memcached_test').prop('disabled', true);
        $('#testMemcachedResult').html('{l s='Checking connection...' mod='jprestaspeedpack'}').show();
        $.ajax({ url: '{$pagecache_typecache_memcached_testurl|escape:'javascript':'UTF-8'}', cache: true, data: { host:$('#memcached_host').val(), port:$('#memcached_port').val() },
            success: function(response) {
                let result = JSON.parse(response);
                if (result.status === 1) {
                    $('#testMemcachedResult').html('<div class="alert alert-success" role="alert"><strong>'+result.host+':'+result.port+'</strong> '+result.comments+'</div>');
                }
                else {
                    $('#testMemcachedResult').html('<div class="alert alert-danger" role="alert"><strong>'+result.host+':'+result.port+'</strong> '+result.comments+'</div>');
                }

            },
            error: function(result, status, error) {
                $('#testMemcachedResult').html(result + ' - ' + status + ' - ' + error);
            }});
        $('#memcached_test').prop('disabled', false);
    }
</script>
<div class="panel">
    <h3>{if $avec_bootstrap}<i class="icon-gear"></i>{else}<img width="16" height="16" src="../img/admin/AdminPreferences.gif" alt=""/>{/if}&nbsp;{l s='Caching system' mod='jprestaspeedpack'}</h3>
    {if count($pagecache_shopsinfos) > 1}
        <div class="alert alert-info">
            {l s='Be aware that the caching system is the same for all shops' mod='jprestaspeedpack'}
        </div>
    {/if}
    <form id="pagecache_form_typecache" action="{$request_uri|escape:'html':'UTF-8'}" method="post">
        <input type="hidden" name="submitModule" value="true"/>
        <input type="hidden" name="pctab" value="typecache"/>
        <fieldset>
            <div style="clear: both;">
                <div class="margin-form">
                    <h4>{l s='Recommended ones' mod='jprestaspeedpack'}</h4>
                    <div class="radio">
                        <label>
                            <input type="radio" {if !$pagecache_typecache_static}disabled="true"{/if} name="pagecache_typecache" value="static" {if $pagecache_typecache === 'static'}checked{/if}>{l s='Prestashop Static!' mod='jprestaspeedpack'}
                        </label>
                        <p class="help-block">{l s='Fastest cache, consummes a lot of files on the disk but they are compressed' mod='jprestaspeedpack'}</p>
                    </div>
                    <div class="radio">
                        <label>
                            <input type="radio" name="pagecache_typecache" value="std" {if $pagecache_typecache === 'std'}checked{/if}>{l s='Standard file system' mod='jprestaspeedpack'}
                        </label>
                        <p class="help-block">{l s='Fast but is consumming a lot of files on the disk' mod='jprestaspeedpack'}</p>
                    </div>
                    <div class="radio">
                        <label>
                            <input type="radio" {if !$pagecache_typecache_stdzip}disabled="true"{/if} name="pagecache_typecache" value="stdzip" {if $pagecache_typecache === 'stdzip'}checked{/if}>{l s='Zipped Standard file system' mod='jprestaspeedpack'}
                        </label>
                        <p class="help-block">{l s='Same as "Standard file system" but files are compressed with ZIP to consumme less disk space, but of course it is a bit slower.' mod='jprestaspeedpack'}</p>
                    </div>
                    <h4>{l s='Not recommended ones' mod='jprestaspeedpack'}</h4>
                    <p>{l s='When using a Memcache server, make sure it is correctly configured and sized or the hit rate could be limited' mod='jprestaspeedpack'}</p>
                    <div class="radio">
                        <label>
                            <input type="radio" id="pagecache_typecache_memcache" {if !$pagecache_typecache_memcache}disabled="true" {/if}name="pagecache_typecache" value="memcache" {if $pagecache_typecache === 'memcache'}checked{/if}>{l s='PHP Memcache' mod='jprestaspeedpack'}
                        </label>
                        <p class="help-block">{if !$pagecache_typecache_memcache}{if $avec_bootstrap}<i class="icon-exclamation-circle"></i>{else}<img width="16" height="16" src="../img/admin/warning.gif" alt=""/>{/if}&nbsp;{l s='You must install PHP memcache extension in order to use this cache; ask to your hosting provider for more informations.' mod='jprestaspeedpack'}{else}{l s='Please, provide the hostname and port of the memcache server to use. If you don\'t know what it is then choose an other caching system. Be aware that if your memcache server is down this will slow down your shop.' mod='jprestaspeedpack'}{/if}</p>
                        <div id="pagecache_typecache_conf_memcache" class="form-inline pagecache_typecache_conf" {if $pagecache_typecache !== 'memcache'}style="display:none"{/if}>
                            <div class="form-group">
                                <label for="memcache_host">{l s='Server IP/Hostname' mod='jprestaspeedpack'}</label>
                                <input type="text" onchange="checkMemcacheConf()" onkeyup="checkMemcacheConf()" class="form-control" id="memcache_host" name="pagecache_typecache_memcache_host" value="{$pagecache_typecache_memcache_host|escape:'html':'UTF-8'}">
                            </div>
                            <div class="form-group">
                                <label for="memcache_port">{l s='Server port' mod='jprestaspeedpack'}</label>
                                <input type="number" onchange="checkMemcacheConf()" onkeyup="checkMemcacheConf()" class="form-control" id="memcache_port" name="pagecache_typecache_memcache_port" value="{$pagecache_typecache_memcache_port|escape:'html':'UTF-8'}">
                            </div>
                            <button type="button" onclick="testMemcache(); return false;" id="memcache_test" class="btn btn-default">Test</button>
                            <div id="testMemcacheResult" style="padding: 20px; display:none;"></div>
                        </div>
                    </div>
                    <div class="radio">
                        <label>
                            <input type="radio" id="pagecache_typecache_memcached" {if !$pagecache_typecache_memcached}disabled="true" {/if}name="pagecache_typecache" value="memcached" {if $pagecache_typecache === 'memcached'}checked{/if}>{l s='PHP Memcached' mod='jprestaspeedpack'}
                        </label>
                        <p class="help-block">{if !$pagecache_typecache_memcached}{if $avec_bootstrap}<i class="icon-exclamation-circle"></i>{else}<img width="16" height="16" src="../img/admin/warning.gif" alt=""/>{/if}&nbsp;{l s='You must install PHP memcached extension in order to use this cache; ask to your hosting provider for more informations.' mod='jprestaspeedpack'}{else}{l s='Please, provide the hostname and port of the memcache server to use. If you don\'t know what it is then choose an other caching system. Be aware that if your memcache server is down this will slow down your shop.' mod='jprestaspeedpack'}{/if}</p>
                        <div id="pagecache_typecache_conf_memcached" class="form-inline pagecache_typecache_conf" {if $pagecache_typecache !== 'memcached'}style="display:none"{/if}>
                            <div class="form-group">
                                <label for="memcached_host">{l s='Server IP/Hostname' mod='jprestaspeedpack'}</label>
                                <input type="text" onchange="checkMemcachedConf()" onkeyup="checkMemcachedConf()" class="form-control" id="memcached_host" name="pagecache_typecache_memcached_host" value="{$pagecache_typecache_memcached_host|escape:'html':'UTF-8'}">
                            </div>
                            <div class="form-group">
                                <label for="memcached_port">{l s='Server port' mod='jprestaspeedpack'}</label>
                                <input type="number" onchange="checkMemcachedConf()" onkeyup="checkMemcachedConf()" class="form-control" id="memcached_port" name="pagecache_typecache_memcached_port" value="{$pagecache_typecache_memcached_port|escape:'html':'UTF-8'}">
                            </div>
                            <button type="button" onclick="testMemcached(); return false;" id="memcached_test" class="btn btn-default">Test</button>
                            <div id="testMemcachedResult" style="padding: 20px; display:none;"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bootstrap">
                <button type="submit" value="1" id="submitModuleTypeCache" name="submitModuleTypeCache" class="btn btn-default pull-right">
                    <i class="process-icon-save"></i> {l s='Save' mod='jprestaspeedpack'}
                </button>
            </div>
        </fieldset>
    </form>
</div>
