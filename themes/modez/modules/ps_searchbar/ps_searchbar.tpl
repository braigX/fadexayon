<!-- Block search module TOP -->
<div  id="search_widget"  class="col-lg-4 col-md-5 col-sm-12 search-widget" data-search-controller-url="{$search_controller_url}">
	<form method="get" action="{$search_controller_url}">
		<input type="hidden" name="controller" value="search">
		<input type="text" name="s" value="{$search_string}" placeholder="{l s='Search our catalog' d='Shop.Theme.Catalog'}" aria-label="{l s='Search' d='Shop.Theme.Catalog'}">
		<button type="submit" class="{if isset($roythemes.search_lay) && ($roythemes.search_lay == "2" || $roythemes.search_lay == "4")}search_nogo {/if}tip_inside">
      <i class="rts" data-size="28" data-color="#000000">{if isset($roythemes.nc_i_search)}{$roythemes.nc_i_search}{else}search1{/if}</i>
      <span class="tip">{l s='Search' d='Shop.Theme.Catalog'}</span>
		</button>
	</form>
</div>
<!-- /Block search module TOP -->
