{*
* 2007-2022 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innova Deluxe SL
* @copyright 2022 Innova Deluxe SL

* @license   INNOVADELUXE
*}

<div class='sel_min_block'>
    <label class="step_title" id="step_title_{$step.id_component|escape:'htmlall':'UTF-8'}">
        {if $step.icon_exist}
        <img src="{$modules_dir|escape:'htmlall':'UTF-8'}idxrcustomproduct/img/icon/{$step.id_component|escape:'htmlall':'UTF-8'}.png"/> 
        {/if}
        {$step.title|escape:'htmlall':'UTF-8'}
        {include file="../../partials/component_description.tpl"}
    </label>
    <select class="minified_sel {if $step.multivalue == 'unique_qty' || $step.multivalue == 'multi_qty'}qty_input{/if}" {if $step.multivalue == 'multi_simple' || $step.multivalue == 'multi_qty'}multiple{/if} id="option_{$step.id_component|escape:'htmlall':'UTF-8'}" name="option_{$step.id_component|escape:'htmlall':'UTF-8'}" data-type="{$step.type|escape:'htmlall':'UTF-8'}">        
        <option value="" {if $step.default_opt == -1}selected{/if} disabled>{l s='Please select option' mod='idxrcustomproduct'}</option>
        {foreach from=$step.options->options item=option}
            <option
                class ="idxcp_minified_option" 
                id = "option_{$step.id_component|escape:'htmlall':'UTF-8'}_{$option->id|intval}"
                name = "option_{$step.id_component|escape:'htmlall':'UTF-8'}"
                data-price-impact='{$option->price_impact|escape:'htmlall':'UTF-8'}' 
                {foreach from=$step.impact_options item=impactopt}
                    {assign var="currentopt" value=$step.id_component|cat:'_'|cat:$option->id nocache}
                    {if $impactopt.option_impacted == $currentopt}
                    data-price-option-impact='{$impactopt.option_trigger}{if $impactopt.impact_percent > 0}p{$impactopt.impact_percent}{else}f{$impactopt.impact_fixed}{/if}'
                    {/if}
                {/foreach}
                data-weight-impact='{$option->weight_impact|escape:'htmlall':'UTF-8'}' 
                data-reference='{$option->reference|escape:'htmlall':'UTF-8'}'
                data-att-product='{if isset($option->att_product)}{$option->att_product|escape:'htmlall':'UTF-8'}{else}{/if}' 
                value = "{$option->id|intval}"
                {if $step.default_opt == $option->id}selected{/if}
                {if $option->active != 1}disabled{/if}
                {if $option->description && $step.show_price && $option->price_impact && ($step.multivalue == 'unique_qty' || $step.multivalue == 'multi_qty')}class="minified_extraheight_option" {/if}
                data-content='<span id="option_name_{$step.id_component|escape:'htmlall':'UTF-8'}_{$option->id|intval}">{$option->name|escape:'htmlall':'UTF-8'}
                {if $option->description}</span> - <span class="min-desc option_description">{$option->description|escape:'htmlall':'UTF-8'}</span>{/if}
                {if $step.show_price && $option->price_impact} 
                    <span class="idxroption_price{if isset($option->price_impact_wodiscount) && $option->price_impact_wodiscount>0} idxroption_price_discounted{/if}">
                    {if isset($option->price_impact_wdiscount_formatted)}
                        {$option->price_impact_wdiscount_formatted}
                    {else}
                        {$option->price_impact_formatted}
                    {/if}    
                    </span>
                    {if isset($option->price_impact_wodiscount) && $option->price_impact_wodiscount>0}
                    <span class="idxroption_old_price">
                        {$option->price_impact_wodiscount_formatted}
                    </span>
                    {/if}
                {/if}
                {if $step.multivalue == 'unique_qty' || $step.multivalue == 'multi_qty'}
                <span class="option_qty_block minified_qty_block{if $option->description && $step.show_price && $option->price_impact} full_with_qty{/if}">
                    <label>{l s='Qty' mod='idxrcustomproduct'}</label>
                    <input 
                        type="number" step="1"
                        id="option_{$step.id_component|escape:'htmlall':'UTF-8'}_{$option->id|intval}_qty" 
                        name="option_{$step.id_component|escape:'htmlall':'UTF-8'}_{$option->id|intval}_qty" 
                        class="idxcp_option_qty"
                    >
                </span>
                {/if}'
            >
            </option>                   
        {/foreach}
    </select>
    <input type="hidden" id='js_icp_next_opt_{$step.id_component|escape:'htmlall':'UTF-8'}' data-type="{$step.type|escape:'htmlall':'UTF-8'}">
</div>