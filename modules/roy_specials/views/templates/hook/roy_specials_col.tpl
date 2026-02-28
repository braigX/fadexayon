<section class="specials-col featured-products clearfix mt-3">
  <h1 class="h1 products-section-title text-uppercase">
    <a href="{$allSpecialProductsLink}">
      <i></i>
    </a>
  </h1>
  <div class="products">
    {foreach from=$products item="product"}
      {include file="catalog/_partials/miniatures/product.tpl" product=$product}
    {/foreach}
  </div>
</section>
