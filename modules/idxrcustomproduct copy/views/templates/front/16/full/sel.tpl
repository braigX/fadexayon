{*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innova Deluxe SL
* @copyright 2017 Innova Deluxe SL

* @license   INNOVADELUXE
*}

{include file="./card_header.tpl" step=$step}
<div class="panel step_content" id="step_content_{$step.id_component|escape:'htmlall':'UTF-8'}" style="border-color: {$step.color|escape:'htmlall':'UTF-8'};">           
    <div class="panel-heading" style="border-color: {$step.color|escape:'htmlall':'UTF-8'}; background-color: {$step.color|escape:'htmlall':'UTF-8'};"><span>{$step.description|escape:'htmlall':'UTF-8'}</span></div>                        
    <div class="panel-body">
        {foreach from=$step.options->options item=option}
            {include file="../../partials/option_data.tpl"}
            <div 
                id='card_{$step.id_component|escape:'htmlall':'UTF-8'}_{$option->id|intval}' 
                class="option_div col-lg-{math equation="12 / x" x=$step.columns} thumbnail text-center js-card-option {if $option->active != 1}out_of_stock js_out_of_stock{/if}"
                {if $option->active != 1}title="{l s='Option out of stock' mod='idxrcustomproduct'}"{/if}
            >
                <div class="check-symbol">
                    <i class="icon icon-check fa fa-check selected_{$step.id_component|escape:'htmlall':'UTF-8'}" style='display: none;color:green;' id='selected_{$step.id_component|escape:'htmlall':'UTF-8'}_{$option->id|intval}'></i>
                </div>
                
                <label class="option_titles" id="option_{$step.id_component|escape:'htmlall':'UTF-8'}_{$option->id|intval}_name">{$option->name|escape:'htmlall':'UTF-8'}</label>
                {include file="../../partials/option_description.tpl"}
                {if $step.multivalue == 'unique_qty' || $step.multivalue == 'multi_qty'}
                    <div class="option_qty_block">
                        <label>{l s='Qty' mod='idxrcustomproduct'}</label>
                        <input 
                            type="number" step="1"
                            id="option_{$step.id_component|escape:'htmlall':'UTF-8'}_{$option->id|intval}_qty" 
                            name="option_{$step.id_component|escape:'htmlall':'UTF-8'}_{$option->id|intval}_qty" 
                            class="idxcp_option_qty"
                        >
                    </div>
                {/if}
            </div>
        {/foreach}
    </div><!-- panel-body -->
    <div class="panel-footer">

        <div class="clearfix"> <a id='js_icp_next_opt_{$step.id_component|escape:'htmlall':'UTF-8'}' data-type="{$step.type|escape:'htmlall':'UTF-8'}" class="js_icp_next_option btn btn-md btn-default pull-right" style="border-color: {$step.color|escape:'htmlall':'UTF-8'}; background-color: {$step.color|escape:'htmlall':'UTF-8'};">{l s='next' mod='idxrcustomproduct'} <i class="fa fa-chevron-down icon icon-chevron-down"></i></a></div>
    </div>

</div><!-- panel -->