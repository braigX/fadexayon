{block name='product_buy'}
    {block name='product_pack'}
        {if $packItems}
            {assign 'showPackProductsPrice'  $product.show_price}
            <section class="product-pack crazy-product-pack">
                <p class="h4">{$heading}</p>
                {foreach from=$packItems item="product_pack"}
                    {block name='product_miniature'}
                        {block name='pack_miniature_item'}
                            <article>
                                <div class="card">
                                    <div class="pack-product-container">
                                        <div class="thumb-mask">
                                            <div class="mask">
                                                <a href="{$product_pack.url}" title="{$product_pack.name}">
                                                {if $product_pack.default_image}
                                                    <img
                                                    src="{$product_pack.default_image.medium.url}"
                                                    alt="{$product_pack.default_image.legend}"
                                                    data-full-size-image-url="{$product_pack.default_image.large.url}"
                                                    >
                                                {else}
                                                    <img src="{$urls.no_picture_image.bySize.cart_default.url}" />
                                                {/if}
                                                </a>
                                            </div>
                                        </div>
                                        <div class="pack-product-name">
                                            <a href="{$product_pack.url}" title="{$product_pack.name}">
                                                {$product_pack.name}
                                            </a>
                                        </div>
                                        {if $showPackProductsPrice}
                                            <div class="pack-product-price">
                                                <strong>{$product_pack.price}</strong>
                                            </div>
                                        {/if}
                                        <div class="pack-product-quantity">
                                            <span>{$q_sign}{$product_pack.pack_quantity}</span>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        {/block}
                    {/block}
                {/foreach}
            </section>
        {/if}
    {/block}
{/block}