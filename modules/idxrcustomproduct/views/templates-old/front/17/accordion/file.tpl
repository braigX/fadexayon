{*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innova Deluxe SL
* @copyright 2017 Innova Deluxe SL

* @license   INNOVADELUXE
*}
<div class="card step_content" id="step_content_{$step.id_component|intval}" style="border-color: {$step.color|escape:'htmlall':'UTF-8'};">           

    {include file="./card_header.tpl" step=$step}

    <div id="step_title_{$step.id_component|intval}" class="panel-collapse collapse" role="tabpanel">
        <div class="card-block">
            {include file="../../partials/component_description.tpl"}
            <div class="card-block">
                <div class="form-group">
                    <input type="file" class="form-control idxrcustomproduct-fileoption" id="file_{$step.id_component|intval}" data-maxsize="{$step.options->size}"/>
                    <div class="idxrcustomproduct-fileoption-error alert alert-danger"></div>
                    {if isset($step.options) && isset($step.options->allowed_extension)}
                    <span>{l s='Allowed files:' mod='idxrcustomproduct'} {foreach from=$step.options->allowed_extension item=extension name=foo}<b>.{$extension}</b>{if !$smarty.foreach.foo.last},{/if} {/foreach} {l s='maximum size' mod='idxrcustomproduct'} <b>{$step.options->size}MB</b> </span>
                    {/if}
                </div>
            </div><!-- panel-body -->
            <div class="card-footer">
                <div class="clearfix">
                    <span class="alert alert-warning hidden" id='next_alert_{$step.id_component|intval}'>{l s='Please select a file before continue with the customization' mod='idxrcustomproduct'}</span>
                    <a id='js_icp_next_opt_{$step.id_component|intval}' data-type="{$step.type|escape:'htmlall':'UTF-8'}" class="js_icp_next_option btn btn-md btn-danger float-lg-right float-md-right float-sm-right" style="border-color: {$step.color|escape:'htmlall':'UTF-8'};background-color: {$step.color|escape:'htmlall':'UTF-8'};">
                        {if $last}{l s='finish' mod='idxrcustomproduct'}{else}{l s='next' mod='idxrcustomproduct'}{/if}
                        <i class="material-icons">expand_more</i>
                    </a>
                </div>
            </div>
        </div><!-- panel-body -->
        
    </div>
    <input type='hidden' id='js_icp_next_opt_{$step.id_component|intval}' data-type="{$step.type|escape:'htmlall':'UTF-8'}" class="js_icp_next_option">
</div><!-- panel -->