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
<div class="panel step_content" id="step_content_{$step.id_component|intval}" style="border-color: {$step.color|escape:'htmlall':'UTF-8'};">
    <div class="panel-heading" style="border-color: {$step.color|escape:'htmlall':'UTF-8'}; background-color: {$step.color|escape:'htmlall':'UTF-8'};"><span>{$step.description|escape:'htmlall':'UTF-8'}</span></div>                       

    <div class="panel-body">
        <div class="form-group">
            <input type="file" class="form-control idxrcustomproduct-fileoption" id="file_{$step.id_component|intval}" data-maxsize="{$step.options->size}"/>
            <div class="idxrcustomproduct-fileoption-error alert alert-danger"></div>
            <span>{l s='Allowed files:' mod='idxrcustomproduct'} {foreach from=$step.options->allowed_extension item=extension name=foo}<b>.{$extension}</b>{if !$smarty.foreach.foo.last},{/if} {/foreach} {l s='maximum size' mod='idxrcustomproduct'} <b>{$step.options->size}MB</b> </span>
        </div>
    </div><!-- panel-body -->
    <div class="panel-footer">
        <div class="clearfix"> <a id='js_icp_next_opt_{$step.id_component|intval}' data-type="{$step.type|escape:'htmlall':'UTF-8'}" class="js_icp_next_option btn btn-md btn-default pull-right" style="border-color: {$step.color|escape:'htmlall':'UTF-8'}; background-color: {$step.color|escape:'htmlall':'UTF-8'};">{l s='next' mod='idxrcustomproduct'} <i class="fa fa-chevron-down icon icon-chevron-down"></i></a></div>
    </div>
</div><!-- panel -->