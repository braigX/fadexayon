<div class="search_tags_roy">
    {l s='You can try some popular tags here:' d='Modules.Roylevibox.Search'}
    <ul>
        {foreach from=$box_tags_array item=tag}
        <li>
            <a class="hover_under" href="{$urls.current_url}search?controller=search&s={$tag}">{$tag}</a>
        </li>
        {/foreach}
    </ul>
</div>

{if isset($products) && $products}
<div class="search_products_roy">
    <span class="search_prod_title">{l s='Featured products:' d='Modules.Roylevibox.Search'}</span>
    <ul>
        {foreach from=$products item=product}
        <li class="search_prod tip_inside">
            <a href="{$product.url|escape:'html':'UTF-8'}" title="">
                <img src="{if $product.cover}{$product.cover.bySize.medium_default.url}{/if}" alt="{if !empty($product.cover.legend)}{$product.cover.legend}{else}{$product.name|truncate:30:'...'}{/if}" data-full-size-image-url="{if $product.cover}{$product.cover.bySize.large_default.url}{/if}">
                <span class="tip">{$product.name|escape:'html':'UTF-8'}</span>
            </a>
        </li>
        {/foreach}
    </ul>
</div>
{/if}