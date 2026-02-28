{**
 * Web Browser Push Notifications using OneSignal
 *
 * @author    Rolige <www.rolige.com>
 * @copyright Since 2011 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 *}

<div class="cron-form">
  <div class="col-lg-12">
    <div class="panel">
      <div class="panel-heading">
        <i class="icon icon-clock-o"></i> {l s='Cron Jobs' mod='rg_pushnotifications'}
      </div>
      <div class="form-wrapper">
      {foreach from=$rg_pushnotifications.crons item=cron name=foo}
        <div class="cron">
          <h4 class="title">{$cron.title|escape:'htmlall':'UTF-8'}</h4>
          {if $cron.desc}
            <p class="desc">{$cron.desc|escape:'htmlall':'UTF-8'}</p>
          {/if}
          <code class="command">
            {$cron.command|escape:'htmlall':'UTF-8'}
          </code>
          <p class="periodicity">
            {l s='Periodicity of recommended execution:' mod='rg_pushnotifications'} <strong>{$cron.periodicity|escape:'htmlall':'UTF-8'}</strong>
          </p>
          {if $cron.comments}
            <p class="comments">
              <strong>{l s='NOTE:' mod='rg_pushnotifications'}</strong> {$cron.comments|escape:'htmlall':'UTF-8'}
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
