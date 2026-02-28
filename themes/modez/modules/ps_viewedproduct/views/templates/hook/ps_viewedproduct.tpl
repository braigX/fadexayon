<section class="featured-products slider-on clearfix mt-3">
  <div class="pp_products_wrapper">  
    <h2 class="products-section-title">{l s='Viewed products' d='Modules.Viewedproduct.Shop'}</h2>
    <div class="products">
        {foreach from=$products item="product"}
        {include file="catalog/_partials/miniatures/product.tpl" product=$product}
        {/foreach}
    </div>
  </div>
</section>
