{*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innova Deluxe SL
* @copyright 2017 Innova Deluxe SL

* @license   INNOVADELUXE
*}

<div class="card step_content" id="step_content_{$step.id_component|escape:'htmlall':'UTF-8'}" style="border-color: {$step.color|escape:'htmlall':'UTF-8'};">           
    
    {include file="./card_header.tpl" step=$step}    
    <div id="step_title_{$step.id_component|escape:'htmlall':'UTF-8'}" class="panel-collapse collapse" role="tabpanel">

        <div class="card-block">
        {include file="../../partials/component_description.tpl"}

            {foreach from=$step.options->options item=option}
                {include file="../../partials/option_data.tpl"}
                    
                <div id='card_{$step.id_component|escape:'htmlall':'UTF-8'}_{$option->id|intval}' class="option_div col-lg-{math equation='12 / x' x=$step.columns} col-md-6 card text-lg-center text-md-center text-sm-center text-xs-center js-card-option {if $option->active != 1}out_of_stock js_out_of_stock{/if}" {if $option->active != 1}title="{l s='Option out of stock' mod='idxrcustomproduct'}"{/if}>
                    
                    <div class="check-symbol">
                    <span class="Add btn" style='display: none;'>Ajouter</span>
                    <i class="done material-icons selected_{$step.id_component|escape:'htmlall':'UTF-8'}" style='display: none;' id='selected_{$step.id_component|escape:'htmlall':'UTF-8'}_{$option->id|intval}'>done</i>
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
                            min="1"
                            value="1"
                        >
                    </div>
                    {/if}
                </div>                     
            {/foreach}
            {if $step.multivalue == 'unique' || $step.multivalue == 'unique_qty'}
            <input type='hidden' id='js_icp_next_opt_{$step.id_component|escape:'htmlall':'UTF-8'}' data-type="{$step.type|escape:'htmlall':'UTF-8'}" class="js_icp_next_option">
            {else}
            <a id='js_icp_next_opt_{$step.id_component|escape:'htmlall':'UTF-8'}' data-type="{$step.type|escape:'htmlall':'UTF-8'}" class="js_icp_next_option btn btn-md float-lg-right float-md-right float-sm-right" style="border-color: {$step.color|escape:'htmlall':'UTF-8'}; background-color: {$step.color|escape:'htmlall':'UTF-8'};">
                {if $last}
                    {l s='finish' mod='idxrcustomproduct'}
                {else}
                    {l s='next' mod='idxrcustomproduct'}
                {/if}
            </a>
            {/if}
        </div><!-- panel-body -->
    </div>    
</div><!-- panel -->
