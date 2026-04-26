{*
* 2007-2022 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innova Deluxe SL
* @copyright 2022 Innova Deluxe SL

* @license   INNOVADELUXE
*}

<div class='sel_min_img_block'>
    <label class="step_title" id="step_title_{$step.id_component|escape:'htmlall':'UTF-8'}">
        {if $step.icon_exist}
        <img src="{$modules_dir|escape:'htmlall':'UTF-8'}idxrcustomproduct/img/icon/{$step.id_component|escape:'htmlall':'UTF-8'}.png"/> 
        {/if}
        {$step.title|escape:'htmlall':'UTF-8'}
        {include file="../../partials/component_description.tpl"}
    </label>    
    <select class="minified_sel_img {if $step.multivalue == 'unique_qty' || $step.multivalue == 'multi_qty'}qty_input{/if}" {if $step.multivalue == 'multi_simple' || $step.multivalue == 'multi_qty'}multiple{/if} id="option_{$step.id_component|escape:'htmlall':'UTF-8'}" data-type="{$step.type|escape:'htmlall':'UTF-8'}">
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
                
                data-content='{if $step.zoom}<span class="zoom">{/if}<img id="option_{$step.id_component|escape:'htmlall':'UTF-8'}_{$option->id|intval}_img" class="img-minified" src="{$opcion_img_dir|escape:'htmlall':'UTF-8'}{$step.id_component|escape:'htmlall':'UTF-8'}_{$option->id|intval}.{$option->img_ext|escape:'htmlall':'UTF-8'}" alt="Thumbnail Alt"> 
                {if $step.zoom}<i class="material-icons zoom-imagen" data-toggle="modal" data-target="#image{$step.id_component|escape:'htmlall':'UTF-8'}_{$option->id|intval}" href="{$opcion_img_dir|escape:'htmlall':'UTF-8'}{$step.id_component|escape:'htmlall':'UTF-8'}_{$option->id|intval}.{$option->img_ext|escape:'htmlall':'UTF-8'}" title="{$option->description|escape:'htmlall':'UTF-8'}">zoom_in</i></span>{/if}
                <span id="option_name_{$step.id_component|escape:'htmlall':'UTF-8'}_{$option->id|intval}">
                {$option->name|escape:'htmlall':'UTF-8'}
                </span>
                {if $option->description}
                 - <span class="min-desc option_description">{$option->description|escape:'htmlall':'UTF-8'}</span>
                {/if}
                {if $step.show_price && $option->price_impact} <span class="idxroption_price">{$option->price_impact_formatted}</span>{/if}
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
    {if $step.zoom}
    {foreach from=$step.options->options item=option}
        <div id='image{$step.id_component|escape:'htmlall':'UTF-8'}_{$option->id|intval}' class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="material-icons" aria-hidden="true">close</i>
                  </button>
                </div>
                <div class="modal-body">
                  <img id="option_{$step.id_component|escape:'htmlall':'UTF-8'}_{$option->id|intval}_img" src="{$opcion_img_dir|escape:'htmlall':'UTF-8'}{$step.id_component|escape:'htmlall':'UTF-8'}_{$option->id|intval}.{$option->img_ext|escape:'htmlall':'UTF-8'}" class="img-responsive" alt="Thumbnail Alt">
                </div>
              </div>
            </div>
          </div>
    {/foreach}
    {/if}
</div>