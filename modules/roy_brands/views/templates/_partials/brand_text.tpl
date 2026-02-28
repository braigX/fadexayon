<ul class="roy_brands_ul brands_text slideme owl-carousel">
  {foreach from=$brands item=brand name=brand_list}
    {if $smarty.foreach.brand_list.iteration <= $text_list_nb}
      <li>
        <a href="{$brand['link']}" title="">
          {$brand['name']}
        </a>
      </li>
    {/if}
  {/foreach}
</ul>
