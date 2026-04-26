<div class="crazy-{$elementprefix} crazy-{$elementprefix}--{$orientation}">
    {block name='product_flags'}
        <ul class="product-flags">
            {foreach from=$product.flags item=flag}
                <li class="product-flag {$flag.type}">{$flag.label}</li>
            {/foreach}
        </ul>
    {/block}
</div>
<style>
    .crazy-single-product-badge--inline .product-flags{
        flex-direction: unset;
    }
    .crazy-single-product-badge .product-flags{
        position: relative;
    }
</style>