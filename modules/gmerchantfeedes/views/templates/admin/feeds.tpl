{*
* 2020 ExtraSolutions
*
* NOTICE OF LICENSE
*
* @author    ExtraSolutions
* @copyright 2019 ExtraSolutions
* @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*}

<div class="panel">
  <div class="panel-heading">
    <i class="icon-list-ul"></i>
      {l s='Feeds' mod='gmerchantfeedes'} <span> ( {$feeds|count} )</span>
    <span class="step-info pull-right">{l s='Step 3' mod='gmerchantfeedes'}</span>
  </div>
  <table class="table">
    <thead>
    <tr class="tr">
      <th>{l s='Id' mod='gmerchantfeedes'}</th>
      <th class="fixed-width-xxl">
				<span class="title_box">
					{l s='Feed name' mod='gmerchantfeedes'}
				</span>
      </th>
      <th>
				<span class="title_box">
					{l s='Locale' mod='gmerchantfeedes'}
				</span>
      </th>
      <th>
				<span class="title_box">
					{l s='Country for shipping cost' mod='gmerchantfeedes'}
				</span>
      </th>
      <th>
				<span class="title_box">
					{l s='Currency' mod='gmerchantfeedes'}
				</span>
      </th>
      <th>
				<span class="title_box">
					{l s='Last update' mod='gmerchantfeedes'}
				</span>
      </th>
      <th class="text-right fixed-width-md"></th>
    </tr>
    </thead>
    <tbody>
    {if isset($feeds) && count($feeds)}
        {foreach from=$feeds item=feed}
          <tr>
            <td class="pointer js-toggle-ref" data-ref="view-{$feed['id_gmerchantfeedes']|intval}">
                {$feed['id_gmerchantfeedes']|intval}
            </td>
            <td class="pointer js-toggle-ref" data-ref="view-{$feed['id_gmerchantfeedes']|intval}">
                {$feed['name']|escape:'htmlall':'UTF-8'}
            </td>
            <td class="pointer js-toggle-ref" data-ref="view-{$feed['id_gmerchantfeedes']|intval}">
                {$feed['locale']|escape:'htmlall':'UTF-8'}
            </td>
            <td class="pointer js-toggle-ref" data-ref="view-{$feed['id_gmerchantfeedes']|intval}">
                {if isset($feed['country'])}{$feed['country']|escape:'htmlall':'UTF-8'}{/if}</td>
            <td class="pointer js-toggle-ref" data-ref="view-{$feed['id_gmerchantfeedes']|intval}">
                {if isset($feed['currency']['iso_code']) && !empty($feed['currency']['iso_code'])}{$feed['currency']['iso_code']|escape:'htmlall':'UTF-8'}{/if}
            </td>
            <td class="pointer js-toggle-ref" data-ref="view-{$feed['id_gmerchantfeedes']|intval}">
                {$feed['date_update']|escape:'htmlall':'UTF-8'}
            </td>
            <td class="text-right">
              <div class="btn-group-action">
                <div style="padding-right: 20px;" class="btn-group">
                  <button data-ref="view-{$feed['id_gmerchantfeedes']|intval}"
                          class="js-toggle-ref btn btn-primary btn-default">
                    <i class="icon-search-plus"></i>
                      {l s='Show url' mod='gmerchantfeedes'}
                  </button>
                  <a href="{$currentIndex|escape:'htmlall':'UTF-8'}&updateFeed&id_gmerchantfeedes={$feed['id_gmerchantfeedes']|intval}"
                     class="btn btn-default">
                    <i class="icon-pencil"></i>
                      {l s='Edit' mod='gmerchantfeedes'}
                  </a><a   title="Make a copy"
                         href="{$currentIndex|escape:'htmlall':'UTF-8'}&clone&id_gmerchantfeedes={$feed['id_gmerchantfeedes']|intval}"
                         class="js-toggle-ref btn btn-default">
                        {if (version_compare(_PS_VERSION_, '1.7.0.0', '<'))}
                          <i class="icon-copy"></i>
                        {else}
                          <i class="material-icons">content_copy</i>
                        {/if}
                  </a><a href="{$currentIndex|escape:'htmlall':'UTF-8'}&deleteFeed&id_gmerchantfeedes={$feed['id_gmerchantfeedes']|intval}"
                         title="Delete" class="delete btn btn-default">
                    <i class="icon-trash"></i>
                  </a>
                </div>
              </div>
            </td>
          </tr>
          <tr class="unhover hide-view-container view-{$feed['id_gmerchantfeedes']|escape:'htmlall':'UTF-8'}">
            <td colspan="7">
              <h4 class="ptop_15">{l s='Copy this link and insert it in Google Merchant Center' mod='gmerchantfeedes'}</h4>
              <div class="content-group-links">
                <span>{l s='Rebuild & Download link:' mod='gmerchantfeedes'}</span>
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
                <span class="btn btn-default pull-right"
                      onclick="copyText('{$feed['cron_rebuild']|escape:'html':'UTF-8'}')">
						{l s='Copy to clipboard' mod='gmerchantfeedes'}
					</span>
                <br/>
                <a target="_blank" href="{$feed['cron_rebuild']|escape:'html':'UTF-8'}">
                    {$feed['cron_rebuild']|escape:'html':'UTF-8'}
                </a>

                <hr/>
                  {l s='Download (for Google Merchant Center):' mod='gmerchantfeedes'}
                <span class="btn btn-default pull-right"
                      onclick="copyText('{$feed['cron_download']|escape:'html':'UTF-8'}')">
						{l s='Copy to clipboard' mod='gmerchantfeedes'}
					</span><br/>
                <a target="_blank" href="{$feed['cron_download']|escape:'html':'UTF-8'}">
                    {$feed['cron_download']|escape:'html':'UTF-8'}
                </a>
              </div>

            </td>
          </tr>
            {if isset($feed['cron_inventory_download']) && !empty($feed['cron_inventory_download'])}
              <tr class="unhover hide-view-container view-{$feed['id_gmerchantfeedes']|escape:'htmlall':'UTF-8'}">
                <td style="border-left: 1px solid #eaedef" colspan="7">
                  <h4 class="ptop_15">{l s='Local product inventory feed' mod='gmerchantfeedes'}</h4>
                  <div style="border-left: none;" class="content-group-links">
                    <span>{l s='Rebuild & Download link:' mod='gmerchantfeedes'}</span>
                    <span class="btn btn-default pull-right"
                          onclick="copyText('{$feed['cron_inventory_download']|escape:'html':'UTF-8'}')">
						{l s='Copy to clipboard' mod='gmerchantfeedes'}
					</span>
                    <br/>
                    <a target="_blank" href="{$feed['cron_inventory_download']|escape:'html':'UTF-8'}">
                        {$feed['cron_inventory_download']|escape:'html':'UTF-8'}
                    </a>
                  </div>
                </td>
              </tr>
            {/if}
            {if isset($feed['id_gmerchantfeedes'])}
              <tr class="unhover hide-view-container view-{$feed['id_gmerchantfeedes']|escape:'htmlall':'UTF-8'}">
                <td colspan="7">
                  <br/>
                  <button data-ref="view-{$feed['id_gmerchantfeedes']|intval}"
                          class="btn-close-dropdown js-toggle-ref close-dropdown"></button>
                  <br/>
                </td>
              </tr>
            {/if}
        {/foreach}
    {else}
      <tr>
        <td colspan="7">
          <p class="title-clear">
              {l s='No feeds' mod='gmerchantfeedes'}
          </p>
        </td>
      </tr>
    {/if}

    <tr class="unhover">
      <td class="text-center" colspan="7">
        <div class="btn-group-action add-btn">
          <a class="btn-group btn btn-primary pointer" href="{$currentIndex|escape:'htmlall':'UTF-8'}&addNewFeed">
						<span class="btn-group">
							<i class="process-icon-new"></i>
							<span>{l s='New feed' mod='gmerchantfeedes'}</span>
						</span>
          </a>
        </div>
      </td>
    </tr>
    </tbody>
  </table>
</div>


<script type="text/javascript">
    function copyText(str) {
        const el = document.createElement('textarea');
        el.value = str;
        document.body.appendChild(el);
        el.select();
        document.execCommand('copy');
        document.body.removeChild(el);
    }
</script>
