<section class="featured-products roy_cc2 clearfix mt-3 {if isset($roythemes.nc_carousel_custom2) && $roythemes.nc_carousel_custom2 == "1"}slider-on{/if}" data-auto="{$roythemes.nc_auto_custom2}" data-max-slides="{$roythemes.nc_items_custom2}">
  <h1 class="h1 products-section-title text-uppercase">
    <a href="{$allProductsLink}">
      {l s='Our products' mod='roy_customcategory2'}
      <i></i>
    </a>
  </h1>
  <div class="products">
    {foreach from=$products item="product"}
      {include file="catalog/_partials/miniatures/product.tpl" product=$product}
    {/foreach}
  </div>
</section>
