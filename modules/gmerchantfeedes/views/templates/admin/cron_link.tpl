{*
 * 2019 ExtraSolutions
 *
 * NOTICE OF LICENSE
 *
 * @author    ExtraSolutions
 * @copyright 2019 ExtraSolutions
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*}

<div class="panel topLoaderScroll">
    <div class="row">
        <div class="col-lg-6 g-taxonomy-title">
            <p class="f-middle help-block">
                <a class="fl-module-back toolbar_btn pull-left" href="{$currentIndex|escape:'htmlall':'UTF-8'}">
                    <i class="process-icon-back"></i>
                    <span>{l s='Back' mod='gmerchantfeedes'}</span>
                </a>
            </p>
        </div>
        <div class="col-lg-6">
            {if isset($feed) && count($feed)}
            <div class="btn-group pull-right">
                <button style="color: #ffffff;" class="btn btn-default js-toggle-content-links btn-primary">
                    <i style="color: #ffffff;" class="icon-search-plus"></i>
                    {l s='View URLs' mod='gmerchantfeedes'}
                </button>
            </div>
            {/if}
        </div>
    </div>
    {if isset($feed) && count($feed)}
    <div class="content-links">
        <h4>{l s='Copy this link and insert it in Google Merchant Center' mod='gmerchantfeedes'}</h4>
        <div class="content-group-links">
            <span>{l s='Rebuild and Download link:' mod='gmerchantfeedes'}</span>
            <span class="btn btn-default pull-right" onclick="copyText('{$feed['cron']|escape:'html':'UTF-8'}')">
                {l s='Copy to clipboard' mod='gmerchantfeedes'}
            </span>
            <br/>
            <a target="_blank" href="{$feed['cron']|escape:'html':'UTF-8'}">
                {$feed['cron']|escape:'html':'UTF-8'}
            </a>

            <hr/>
            <h4>{l s='For more products quantity' mod='gmerchantfeedes'}</h4>

            {l s='Rebuild (for cronjob):' mod='gmerchantfeedes'}
            <span class="btn btn-default pull-right" onclick="copyText('{$feed['cron_rebuild']|escape:'html':'UTF-8'}')">
                {l s='Copy to clipboard' mod='gmerchantfeedes'}
            </span>
            <br/>
            <a target="_blank" href="{$feed['cron_rebuild']|escape:'html':'UTF-8'}">
                {$feed['cron_rebuild']|escape:'html':'UTF-8'}
            </a>

            <hr/>
            {l s='Download (for Google Merchant Center):' mod='gmerchantfeedes'}
            <span class="btn btn-default pull-right" onclick="copyText('{$feed['cron_download']|escape:'html':'UTF-8'}')">
                {l s='Copy to clipboard' mod='gmerchantfeedes'}
            </span><br/>
            <a target="_blank" href="{$feed['cron_download']|escape:'html':'UTF-8'}">
                {$feed['cron_download']|escape:'html':'UTF-8'}
            </a>

            {if isset($feed['cron_inventory_download']) && !empty($feed['cron_inventory_download'])}
                <hr/>
                <h4>{l s='Local product inventory feed' mod='gmerchantfeedes'}</h4>
                {l s='Rebuild & Download link:' mod='gmerchantfeedes'}
                <span class="btn btn-default pull-right" onclick="copyText('{$feed['cron_inventory_download']|escape:'html':'UTF-8'}')">
                    {l s='Copy to clipboard' mod='gmerchantfeedes'}
                </span>
                <br/>
                <a target="_blank" href="{$feed['cron_inventory_download']|escape:'html':'UTF-8'}">
                    {$feed['cron_inventory_download']|escape:'html':'UTF-8'}
                </a>
            {/if}
        </div>
        <br/><br/>
    </div>
    {/if}
</div>

<script type="text/javascript">
    function copyText(str){
      const el = document.createElement('textarea');
      el.value = str;
      document.body.appendChild(el);
      el.select();
      document.execCommand('copy');
      document.body.removeChild(el);
    }
</script>
