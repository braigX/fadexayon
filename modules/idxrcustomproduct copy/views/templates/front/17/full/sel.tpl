{*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innova Deluxe SL
* @copyright 2017 Innova Deluxe SL

* @license   INNOVADELUXE
*}

<h2 class="step_title" id="step_title_{$step.id_component|escape:'htmlall':'UTF-8'}">
    {if $step.icon_exist}
    <img class="step-img" src="{$modules_dir|escape:'htmlall':'UTF-8'}idxrcustomproduct/img/icon/{$step.id_component|escape:'htmlall':'UTF-8'}.png"/> 
    {/if}
    {$step.title|escape:'htmlall':'UTF-8'}
</h2>
<div class="card step_content" id="step_content_{$step.id_component|escape:'htmlall':'UTF-8'}" style="border-color: {$step.color|escape:'htmlall':'UTF-8'};">           
    <div class="card-header" style="background-color: {$step.color|escape:'htmlall':'UTF-8'};"><span>{$step.description|escape:'htmlall':'UTF-8'}</span></div>                        
    <div class="card-block">
        {foreach from=$step.options->options item=option}
            {include file="../../partials/option_data.tpl"}
            <div id='card_{$step.id_component|escape:'htmlall':'UTF-8'}_{$option->id|intval}' class="option_div col-lg-{math equation='12 / x' x=$step.columns} col-md-6 card text-lg-center text-md-center text-sm-center text-xs-center js-card-option {if $option->active != 1}out_of_stock js_out_of_stock{/if}" {if $option->active != 1}title="{l s='Option out of stock' mod='idxrcustomproduct'}"{/if}>
                <div class="check-symbol">
                <i class="done material-icons selected_{$step.id_component|escape:'htmlall':'UTF-8'}" style='display: none;' id='selected_{$step.id_component|escape:'htmlall':'UTF-8'}_{$option->id|intval}'>done</i>
                </div>

                <label class="option_titles" id="option_{$step.id_component|escape:'htmlall':'UTF-8'}_{$option->id|intval}_name">{$option->name|escape:'htmlall':'UTF-8'}</label>
                {if $option->description}<span class="option_description">{$option->description|escape:'htmlall':'UTF-8'}</span>{/if}<br/>
                {if $step.show_price && $option->price_impact}
                    <span class="idxroption_price">{if isset($option->price_impact_wdiscount_formatted)}{$option->price_impact_wdiscount_formatted}{else}{$option->price_impact_formatted}{/if}</span>
                {/if}
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
    <div class="card-footer">
        <div class="clearfix">
            <span class="alert alert-warning hidden" id='next_alert_{$step.id_component|escape:'htmlall':'UTF-8'}'>{l s='Please select a option before continue with the customization' mod='idxrcustomproduct'}</span>
            <a id='js_icp_next_opt_{$step.id_component|escape:'htmlall':'UTF-8'}' data-type="{$step.type|escape:'htmlall':'UTF-8'}" class="js_icp_next_option btn btn-md float-lg-right float-md-right float-sm-right" style="border-color: {$step.color|escape:'htmlall':'UTF-8'}; background-color: {$step.color|escape:'htmlall':'UTF-8'};">
                {if $last}
                    {l s='finish' mod='idxrcustomproduct'}
                {else}
                    {l s='next' mod='idxrcustomproduct'}
                {/if}
                <i class="material-icons">expand_more</i>
            </a>
        </div>
    </div>

</div><!-- panel -->