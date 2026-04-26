{if isset($crazy_products) && $crazy_products}
    <div class="product-grid-wrapper product-miniature vc-smart-{$elementprefix}-products-grid">
	{if !empty($section_heading)}
		<p class="title_block">{$section_heading}</p>
	{/if}
        <div class="products">
            {foreach from=$crazy_products item="product"}
                {if file_exists("$theme_dir/catalog/_partials/miniatures/product.tpl")}
                    {include file="$theme_dir/catalog/_partials/miniatures/product.tpl" product=$product}
                {else} 
                    {include file="$parent_theme_dir/catalog/_partials/miniatures/product.tpl" product=$product}
                {/if}
            {/foreach}
        </div>
    </div>
{/if}