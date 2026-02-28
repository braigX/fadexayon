{if isset($crazy_products) && $crazy_products}
    {if !empty($section_heading)}
        <p class="title_block">{$section_heading}</p>
    {/if}
    <div class="elementor-image-carousel-wrapper product-carousel-wrapper elementor-slick-slider">
        <div class="elementor-image-carousel products slick-arrows-inside slick-dots-outside" >
            {foreach from=$crazy_products item="product"}
            <div class="slick-slide">
                <div class="slick-slide-inner">
              {include file="$theme_template_path" product=$product}
              </div>
               </div>
            {/foreach}
        </div>
    </div>
{/if}