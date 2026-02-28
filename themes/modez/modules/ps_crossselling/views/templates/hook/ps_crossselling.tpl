<section class="featured-products slider-on clearfix mt-3">
  <div class="pp_products_wrapper">
    <div class="products-section-title h2">{l s='Customers who bought this product also bought:' d='Shop.Theme.Catalog'}</div>
    <div class="products">
      {foreach from=$products item="product"}
        {include file="catalog/_partials/miniatures/product.tpl" product=$product}
      {/foreach}
    </div>
  </div>
</section>
