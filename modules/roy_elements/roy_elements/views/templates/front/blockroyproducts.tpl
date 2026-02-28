{if isset($vc_products) && $vc_products}
    <div class="roy-elements-products featured-products title-align-{$title_align} columns-desktop-{$per_row} {$layout} product-grid-wrapper product-miniature vc-smart-{$elementprefix}-products-grid" data-auto="{$is_autoplay}" data-max-slides="{$per_row}">
	{if !empty($vc_title)}
    <h3 class="h3 products-section-title">
        <a href="{$allProductsLink}">            
            {$vc_title}
            <i></i>
        </a>
    </h3>
	{/if}
        <div class="products {if $layout == 'slider'}owl-carousel{/if}">
            {foreach from=$vc_products item="product"}
              {include file="$theme_template_path" product=$product}
            {/foreach}
        </div>
    </div>
{/if}