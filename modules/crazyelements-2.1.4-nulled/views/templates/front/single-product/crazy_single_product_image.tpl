{if isset($crazy_products) && $crazy_products}
    <div class="product-grid-wrapper product-miniature vc-smart-{$elementprefix}-products-grid crazy-{$elementprefix}">
        <div class="products">
            {foreach from=$crazy_products item="product"}
                <section class="page-content" id="content">
                    {block name='page_content'}
                        <!-- @todo: use include file='catalog/_partials/product-flags.tpl'} -->
                        {if $show_flag == 'yes'}
                            {block name='product_flags'}
                                <ul class="product-flags">
                                    {foreach from=$product.flags item=flag}
                                        <li class="product-flag {$flag.type}">{$flag.label}</li>
                                    {/foreach}
                                </ul>
                            {/block}
                        {/if}
                        {block name='product_cover_thumbnails'}
                        {include file='catalog/_partials/product-cover-thumbnails.tpl'}
                        {/block}
                        <div class="scroll-box-arrows">
                        <i class="material-icons left">&#xE314;</i>
                        <i class="material-icons right">&#xE315;</i>
                        </div>
                    {/block}
                </section>
            {/foreach}
        </div>
    </div>
{else}
    <div class="crazy-{$elementprefix}">
        {block name='page_content_container'}
            <section class="page-content" id="content">
                {block name='page_content'}
                    <!-- @todo: use include file='catalog/_partials/product-flags.tpl'} -->
                    {if $show_flag == 'yes'}
                        {block name='product_flags'}
                            <ul class="product-flags">
                                {foreach from=$product.flags item=flag}
                                    <li class="product-flag {$flag.type}">{$flag.label}</li>
                                {/foreach}
                            </ul>
                        {/block}
                    {/if}
                    {block name='product_cover_thumbnails'}
                    {include file='catalog/_partials/product-cover-thumbnails.tpl'}
                    {/block}
                    <div class="scroll-box-arrows">
                    <i class="material-icons left">&#xE314;</i>
                    <i class="material-icons right">&#xE315;</i>
                    </div>
                {/block}
            </section>
        {/block}
    </div>
{/if}