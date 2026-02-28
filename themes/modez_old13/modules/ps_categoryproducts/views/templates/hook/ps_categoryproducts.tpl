<section class="featured-products slider-on clearfix mt-3 second">
  <div class="container">
  <div class="pp_products_wrapper">
    <h2 class="products-section-title ">
      {if $products|@count == 1}
        {*l s='%s other product in the same category:' sprintf=[$products|@count] d='Shop.Theme.Catalog'*}
        {l s='Produits apparentés' sprintf=[$products|@count] d='Shop.Theme.Catalog'}
      {else}
        {*l s='%s other products in the same category:' sprintf=[$products|@count] d='Shop.Theme.Catalog'*}
        {l s='Produits apparentés' sprintf=[$products|@count] d='Shop.Theme.Catalog'}
      {/if}
    </h2>
    <div class="products">
        {foreach from=$products item="product"}
            {include file="catalog/_partials/miniatures/product.tpl" product=$product}
        {/foreach}
    </div>
  </div>
  </div>
</section>
