{if isset($vc_products) && $vc_products}
    <div class="product-grid-wrapper product-miniature vc-smart-{$elementprefix}-products-grid">
        <div class="products products-masonry owl-carousel">

            {foreach from=$vc_products item="product"}
                {if $product@key == 2 || $product@key == 3}
                    {$column_val = 'col-lg-6'}
                {else}
                    {$column_val = 'col-lg-3'}
                {/if}
                {include file="$theme_template_path" product=$product}
            {/foreach}
        </div>
    </div>
{/if}