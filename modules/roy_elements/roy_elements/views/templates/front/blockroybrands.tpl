<div class="roy-elements-brands-slider title-align-{$title_align} {$layout} columns-desktop-{$per_row}" data-auto="{$is_autoplay}" data-max-slides="{$per_row}">
    {if !empty($title)}    
    <h3 class="h3 products-section-title">
        {$title}
    </h3>
	{/if}

    <div class="brands-slider {if $layout == 'slider'}owl-carousel{/if}">
        {foreach from=$manufacturers item=manufacturer name=manufacturers}
        <div class="brand-slide">
            <div class="brand-slide-inner">
                <a href="{$link->getmanufacturerLink($manufacturer.id_manufacturer, $manufacturer.link_rewrite)}" class="tip_inside">
                    {$imgname=$manufacturer.id_manufacturer}
                    {$imgname=$imgname|cat:'.jpg'}
                    <img src="{$img_manu_dir}{$imgname}" alt="{$manufacturer.name}" title="{$manufacturer.name}" />
                    <span class="tip">{$manufacturer.name}</span>                    
                </a>
            </div>
        </div>
        {/foreach}
    </div>
</div>