{**
* 2007-2022 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innova Deluxe SL
* @copyright 2022 Innova Deluxe SL
* @license   INNOVADELUXE
*}

<div class="option_description_block">
{if $option->description}<span class="option_description">{$option->description|escape:'htmlall':'UTF-8'}</span>{/if}<br/>
{if $step.show_price}
    <span class="idxroption_price{if isset($option->price_impact_wodiscount) && $option->price_impact_wodiscount>0 && $option->price_impact_wodiscount!=$option->price_impact} idxroption_price_discounted{/if}">
    {if $option->price_impact}
        {if isset($option->price_impact_wdiscount_formatted)}
            {$option->price_impact_wdiscount_formatted}
        {else}
            {$option->price_impact_formatted}
        {/if}    
    {/if}
    </span>
    {if isset($option->price_impact_wodiscount) && $option->price_impact_wodiscount>0 && $option->price_impact_wodiscount!=$option->price_impact}
    <span class="idxroption_old_price">
        {$option->price_impact_wodiscount_formatted}
    </span>
    {/if}
{/if}
</div>