{**
 * Web Browser Push Notifications using OneSignal
 *
 * @author    Rolige <www.rolige.com>
 * @copyright Since 2011 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 *}

<div id="configure_content" class="clearfix">
  <div class="col-lg-2 configure-menu">
    {foreach from=$rg_pushnotifications.menu.items item=group}
      <div class="list-group">
        {foreach from=$group item=item key=key}
          <a
            href="{$rg_pushnotifications.menu.link|escape:'htmlall':'UTF-8'}&menu_active={$key|escape:'htmlall':'UTF-8'}"
            class="list-group-item{if $key == $rg_pushnotifications.menu.active} active{/if}"
          >
            <i class="{$item.icon|escape:'htmlall':'UTF-8'}"></i>
            <span class="title">{$item.title|escape:'htmlall':'UTF-8'}</span>
            {if $key == 'dashboard' && $rg_pushnotifications.new_version}
              <span class="badge badge-warning badge-pill">{l s='update' mod='rg_pushnotifications'}</span>
            {/if}
          </a>
        {/foreach}
      </div>
    {/foreach}
    <div class="list-group">
      <span class="list-group-item">
        <i class="icon-info"></i>
        <span class="title">{l s='Version' mod='rg_pushnotifications'} {$rg_pushnotifications.version|escape:'htmlall':'UTF-8'}</span>
      </span>
    </div>
  </div>

  <div class="col-lg-10 configure-form">
    {$rg_pushnotifications.form nofilter}
  </div>
</div>
