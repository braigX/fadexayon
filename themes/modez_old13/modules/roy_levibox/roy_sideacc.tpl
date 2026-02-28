<div class="side_title h4">{l s='Account' d='Modules.Roylevibox.Account'}</div>

{if $logged}
  <ul class="acc_ul">
    <!-- <li class="name"><a href="{$my_account_url}" title="{l s='View my customer account' d='Shop.Theme.Customeraccount'}" rel="nofollow"><span>{$customerName}</span></a></li> -->
    {foreach from=$my_account_urls item=my_account_url}
        <li><a href="{$my_account_url.url}" title="{$my_account_url.title}" rel="nofollow">{$my_account_url.title}</a></li>
    {/foreach}
    {hook h="displayMyAccountBlock"}
    <li class="logout"><a href="{$logout_url}" rel="nofollow" title="{l s='Log me out' d='Shop.Theme.Customeraccount'}">{l s='Sign out' d='Shop.Theme.Actions'}</a></li>
  </ul>
{else}
  <div class="acc_nolog acc_ul">
    <div class="acc_text_login">
      {l s='You should login to your account:' d='Modules.Roylevibox.Account'}
    </div>
    <a class="btn btn-primary login" href="{$my_account_url}" rel="nofollow" title="{l s='Log in to your customer account' d='Shop.Theme.Customeraccount'}">{l s='Sign in' d='Modules.Roylevibox.Account'}</a>
    <div class="acc_text_create">
      {l s='No account?' d='Modules.Roylevibox.Account'}
    </div>
    <a class="create" href="{$urls.pages.register}" data-link-action="display-register-form">
      {l s='Create one here' d='Modules.Roylevibox.Account'}
    </a>
  </div>
{/if}
