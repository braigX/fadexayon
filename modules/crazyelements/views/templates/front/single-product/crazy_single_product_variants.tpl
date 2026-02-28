{block name='product_variants'}
    <div class="crazy-product-variant crazy-product-variant-{$orientation}">
        {include file='catalog/_partials/product-variants.tpl'}
    </div>
{/block}
<style>
    .crazy-product-variant{
        display: flex;
    }
    .crazy-product-variant-stacked .product-variants{
        flex-direction: column;
    }
    .crazy-product-variant .product-variants{
        display: flex;
    }
</style>