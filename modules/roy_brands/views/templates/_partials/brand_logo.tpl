<ul class="roy_brands_ul brands_logo slideme owl-carousel">
  {foreach from=$brands item=brand name=brand_list}
    {if $smarty.foreach.brand_list.iteration <= $text_list_nb}
      <li>
        <a href="{$brand['link']}" title="" class="tip_inside">
            <img src = "{$link->getManufacturerImageLink($brand.id_manufacturer, 'brand_default')}" alt = "{$brand.name|escape:html:'UTF-8'}">
            <span class="tip">{$brand['name']}</span>
        </a>
      </li>
    {/if}
  {/foreach}
</ul>
