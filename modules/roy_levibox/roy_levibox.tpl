{*
* 2019 RoyThemes Copyright. This module is part of MODEZ Theme. You can't copy or distribute it. You can't install it separatelly of theme and use it on another domain.
*}

<!-- Roy LeviBox -->
<div id="roy_levibox" class="roy_levibox position2 stick_lb {if isset($roythemes.levi_position)}{$roythemes.levi_position}{/if}">

    {if !$configuration.is_catalog}
    <div class="box-one box-cart tip_inside{if isset($box_cart) && !$box_cart} hidden-lg-up{/if}">
      <i>
        <i class="rts shopping-cart" data-size="28" data-color="#000000">{if isset($roythemes.cart_icon)}{$roythemes.cart_icon}{else}cart1{/if}</i>
      </i>
      <span class="prod_count {if $cart.products_count > 0}active{/if}">{$cart.products_count}</span>
      <span class="tip">{l s='Cart' d='Modules.Roylevibox.Main'}</span>
    </div>
    {/if}
    <div class="box-one box-search tip_inside{if isset($box_search) && !$box_search} hidden-lg-up{/if}">
      <i>
        <i class="rts" data-size="28" data-color="#000000">{if isset($roythemes.nc_i_search)}{$roythemes.nc_i_search}{else}search1{/if}</i>
      </i>
      <span class="tip">{l s='Search' d='Modules.Roylevibox.Main'}</span>
    </div>
    <div class="box-one box-acc tip_inside{if isset($box_acc) && !$box_acc} hidden-lg-up{/if}">
      <i>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><title>3</title><g id="Layer_4" data-name="Layer 4"><circle cx="12" cy="8" r="5" style="fill:none;stroke:#000;stroke-linecap:round;stroke-linejoin:round;stroke-width:2.299999952316284px"/></g><g id="Layer_1" data-name="Layer 1"><path d="M6.31,14.5A7.79,7.79,0,0,0,4,20.13,11,11,0,0,0,4.16,22H19.84A11,11,0,0,0,20,20.13a7.79,7.79,0,0,0-2.31-5.62" style="fill:none;stroke:#000;stroke-linecap:round;stroke-linejoin:round;stroke-width:2.299999952316284px"/></g></svg>
      </i>
      <span class="tip">{l s='Account' d='Modules.Roylevibox.Main'}</span>
    </div>
  {if isset($box_mail) && $box_mail}
    <div class="box-one box-mail tip_inside hidden-md-down">
      <i>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><g id="Layer_4" data-name="Layer 4"><rect x="2" y="4" width="20" height="16" rx="3" ry="3" style="fill:none;stroke:#000;stroke-linecap:round;stroke-linejoin:round;stroke-width:2.29999995231628px"/><path d="M2.06,6.94,10,14s2.31,1.73,4,0l7.75-7.75" style="fill:none;stroke:#000;stroke-linecap:round;stroke-linejoin:round;stroke-width:2px"/></g></svg>
      </i>
      <span class="tip">{l s='Contact us' d='Modules.Roylevibox.Main'}</span>
    </div>
  {/if}
  {if isset($box_arrow) && $box_arrow}
    <div class="box-one box-arrow">
      <i>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><g id="Layer_4" data-name="Layer 4"><path d="M19.78,14,14.83,9,12,6.19,9.17,9,4.22,14a1,1,0,0,0,0,1.41L5.64,16.8a1,1,0,0,0,1.41,0L12,11.85l4.95,4.95a1,1,0,0,0,1.41,0l1.41-1.41A1,1,0,0,0,19.78,14Z" style="fill:none;stroke:#000;stroke-linecap:round;stroke-linejoin:round;stroke-width:2px"/></g></svg>
      </i>
    </div>
  {/if}
    <div class="box-one box-menu tip_inside hidden-lg-up">
      <i>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><title>4</title><g id="Layer_4" data-name="Layer 4"><rect x="6" y="14" width="16" height="4" rx="1.6" ry="1.6" style="fill:none;stroke:#000;stroke-linecap:round;stroke-linejoin:round;stroke-width:2.20000004768372px"/><rect x="2" y="6" width="20" height="4" rx="2" ry="2" style="fill:none;stroke:#000;stroke-linecap:round;stroke-linejoin:round;stroke-width:2.20000004768372px"/></g></svg>
      </i>
      <span class="tip">{l s='Info' d='Modules.Roylevibox.Main'}</span>
    </div>
</div>
<!-- /Roy LeviBox -->
