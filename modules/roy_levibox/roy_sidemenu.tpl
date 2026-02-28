<div class="menu_selectors">
  {widget name='ps_languageselector'}
  {widget name='ps_currencyselector'}
</div>

{if isset($roythemes.nc_hemo) && $roythemes.nc_hemo == "3"}
  <a class="menu_acc" href="{$urls.pages.my_account}">{l s='Account' d='Modules.Roylevibox.Account'}</a></h4>
{/if}

<div class="menu_mob_wrapper">
</div>

{hook h="displaySideMobileMenu"}
