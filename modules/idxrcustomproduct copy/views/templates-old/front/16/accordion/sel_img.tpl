{*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innova Deluxe SL
* @copyright 2017 Innova Deluxe SL

* @license   INNOVADELUXE
*}

<div class="panel step_content" id="step_content_{$step.id_component|escape:'htmlall':'UTF-8'}" style="border-color: {$step.color|escape:'htmlall':'UTF-8'};">           
    <div class="panel-heading" style="border-color: {$step.color|escape:'htmlall':'UTF-8'}; background-color: {$step.color|escape:'htmlall':'UTF-8'};">
        <a data-toggle="collapse" data-parent="#component_steps_container" href="#step_title_{$step.id_component|escape:'htmlall':'UTF-8'}">
            <div id="alignment" >
                <p class="panel-title">                
                    {if $step.icon_exist}
                    <img src="{$modules_dir|escape:'htmlall':'UTF-8'}idxrcustomproduct/img/icon/{$step.id_component|escape:'htmlall':'UTF-8'}.png"/> 
                    {/if}
                    {$step.title|escape:'htmlall':'UTF-8'}                
                </p>            
                <span id="toggle_block_{$step.id_component|escape:'htmlall':'UTF-8'}" class="toggle_arrow">  </span>
            </div>
        </a>
        <div class="result">
            <span id="title_selected_{$step.id_component|escape:'htmlall':'UTF-8'}" class="value_selected"></span>
        </div>
        
    </div>

    <div id="step_title_{$step.id_component|escape:'htmlall':'UTF-8'}" class="panel-collapse collapse">

        <div class="panel-body">

            {include file="../../partials/component_description.tpl"}

            {foreach from=$step.options->options item=option}
                {include file="../../partials/option_data.tpl"}
                <div 
                    id='card_{$step.id_component|escape:'htmlall':'UTF-8'}_{$option->id|intval}' 
                    class="option_div col-lg-{math equation="12 / x" x=$step.columns} thumbnail text-center js-card-option {if $option->active != 1}js_out_of_stock{/if}"
                    {if $option->active != 1}title="{l s='Option out of stock' mod='idxrcustomproduct'}"{/if}
                >
                    {if $option->active != 1}
                    <p class="no-stock-label">{$option->ofs_text}</p>
                    {/if}
                    <div class="check-symbol">
                        <i class="icon icon-check fa fa-check selected_{$step.id_component|escape:'htmlall':'UTF-8'}" style='display: none;color:green;' id='selected_{$step.id_component|escape:'htmlall':'UTF-8'}_{$option->id|intval}'></i>
                    </div>
                    <div class="image-container {if $option->active != 1}out_of_stock{/if}">
                        {if $step.zoom}
                        <a class="fancybox shown" data-fancybox-group="gallery{$step.id_component|escape:'htmlall':'UTF-8'}" href="{$opcion_img_dir|escape:'htmlall':'UTF-8'}{$step.id_component|escape:'htmlall':'UTF-8'}_{$option->id|intval}.{$option->img_ext|escape:'htmlall':'UTF-8'}"" title="{$option->description|escape:'htmlall':'UTF-8'}">
                            <img 
                                id="option_{$step.id_component|escape:'htmlall':'UTF-8'}_{$option->id|intval}_img" 
                                src="{$opcion_img_dir|escape:'htmlall':'UTF-8'}{$step.id_component|escape:'htmlall':'UTF-8'}_{$option->id|intval}.{$option->img_ext|escape:'htmlall':'UTF-8'}" 
                                class="img-responsive" 
                                alt="{$step.title|escape:'htmlall':'UTF-8'} {$option->name|escape:'htmlall':'UTF-8'}"
                            >
                        </a>
                        {else}
                        <img 
                            id="option_{$step.id_component|escape:'htmlall':'UTF-8'}_{$option->id|intval}_img" 
                            src="{$opcion_img_dir|escape:'htmlall':'UTF-8'}{$step.id_component|escape:'htmlall':'UTF-8'}_{$option->id|intval}.{$option->img_ext|escape:'htmlall':'UTF-8'}" 
                            class="img-responsive" 
                            alt="{$step.title|escape:'htmlall':'UTF-8'} {$option->name|escape:'htmlall':'UTF-8'}"
                        >
                        {/if}
                    </div>                        
                
                    <label class="option_collapse {if $option->active != 1}out_of_stock{/if}" id="option_{$step.id_component|escape:'htmlall':'UTF-8'}_{$option->id|intval}_name">{$option->name|escape:'htmlall':'UTF-8'}</label>
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
      </div>


</div><!-- panel -->