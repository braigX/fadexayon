{*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innova Deluxe SL
* @copyright 2017 Innova Deluxe SL

* @license   INNOVADELUXE
*}

<div class="card step_content" id="step_content_{$step.id_component}" style="border-color: {$step.color|escape:'htmlall':'UTF-8'};">           
    
    {include file="./card_header.tpl" step=$step}
    <div id="step_title_{$step.id_component}" class="panel-collapse collapse" role="tabpanel">
        <div class="card-block">
            {include file="../../partials/component_description.tpl"}
            {foreach from=$step.options->options item=option}
                {include file="../../partials/option_data.tpl"}
                <div id='card_{$step.id_component|escape:'htmlall':'UTF-8'}_{$option->id|intval}' class="option_div col-lg-{math equation='12 / x' x=$step.columns} col-md-6 card text-lg-center text-md-center text-sm-center text-xs-center js-card-option {if $option->active != 1}js_out_of_stock{/if}" {if $option->active != 1}title="{l s='Option out of stock' mod='idxrcustomproduct'}"{/if}>
                    {if $option->active != 1 && $option->ofs_text}
                    <p class="no-stock-label">{$option->ofs_text}</p>
                    {/if}
                    <div class="check-symbol">
                        <i class="done material-icons selected_{$step.id_component|escape:'htmlall':'UTF-8'}" style='display: none;' id='selected_{$step.id_component|escape:'htmlall':'UTF-8'}_{$option->id|intval}'>done</i>
                    </div>
                    <div class="image-container {if $option->active != 1}out_of_stock{/if}">
                        {if $step.zoom}
                        <a class="zoom">
                            <img id="option_{$step.id_component|escape:'htmlall':'UTF-8'}_{$option->id|intval}_img" src="" data_src="{$opcion_img_dir|escape:'htmlall':'UTF-8'}{$step.id_component|escape:'htmlall':'UTF-8'}_{$option->id|intval}.{$option->img_ext|escape:'htmlall':'UTF-8'}" class="img-responsive" alt="Thumbnail Alt">
                            <i class="material-icons zoom-imagen" data-toggle="modal" data-target="#image{$step.id_component|escape:'htmlall':'UTF-8'}_{$option->id|intval}" href="{$opcion_img_dir|escape:'htmlall':'UTF-8'}{$step.id_component|escape:'htmlall':'UTF-8'}_{$option->id|intval}.{$option->img_ext|escape:'htmlall':'UTF-8'}" title="{$option->description|escape:'htmlall':'UTF-8'}">zoom_in</i>               
                        </a>

                        <div id='image{$step.id_component|escape:'htmlall':'UTF-8'}_{$option->id|intval}' class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true">
                          <div class="modal-dialog">
                            <div class="modal-content">
                              <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                  <i class="material-icons" aria-hidden="true">close</i>
                                </button>
                              </div>
                              <div class="modal-body">
                                <img id="option_{$step.id_component|escape:'htmlall':'UTF-8'}_{$option->id|intval}_img" src="" data_src="{$opcion_img_dir|escape:'htmlall':'UTF-8'}{$step.id_component|escape:'htmlall':'UTF-8'}_{$option->id|intval}.{$option->img_ext|escape:'htmlall':'UTF-8'}" class="img-responsive" alt="Thumbnail Alt">
                              </div>
                            </div>
                          </div>
                        </div>
                            
                        {else}
                            <img id="option_{$step.id_component|escape:'htmlall':'UTF-8'}_{$option->id|intval}_img" src="" data_src="{$opcion_img_dir|escape:'htmlall':'UTF-8'}{$step.id_component|escape:'htmlall':'UTF-8'}_{$option->id|intval}.{$option->img_ext|escape:'htmlall':'UTF-8'}" class="img-responsive" alt="Thumbnail Alt">
                        {/if}
                    </div>

                    <div class="d-block">
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
        </div><!-- card-body -->
    </div>
</div><!-- card -->



