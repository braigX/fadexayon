{**
 * Web Browser Push Notifications using OneSignal
 *
 * @author    Rolige <www.rolige.com>
 * @copyright Since 2011 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 *}

<div class="log-form">
  <div class="col-lg-12">
    <div class="panel">
      <div class="panel-heading">
        <i class="icon icon-history"></i> {l s='Log' mod='rg_pushnotifications'}
      </div>
      <div class="form-wrapper">
      {foreach from=$rg_pushnotifications.logs item=log name=foo}
        <div class="log">
          <h4 class="title">{$log.title|escape:'htmlall':'UTF-8'}</h4>
          {if $log.desc}
            <p class="desc">{$log.desc|escape:'htmlall':'UTF-8'}</p>
          {/if}
          <p class="url">
            <a href="{$log.url|escape:'htmlall':'UTF-8'}" target="_blank">{$log.url|escape:'htmlall':'UTF-8'}</a>
          </p>
          {if $log.comments}
            <p class="comments">
              <strong>{l s='NOTE:' mod='rg_pushnotifications'}</strong> {$log.comments|escape:'htmlall':'UTF-8'}
            </p>
          {/if}
        </div>
        {if !$smarty.foreach.foo.last}
          <hr>
        {/if}
      {/foreach}
      </div>
    </div>
  </div>
</div>
