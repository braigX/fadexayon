{*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innova Deluxe SL
* @copyright 2017 Innova Deluxe SL

* @license   INNOVADELUXE
*}

<div class='sel_min_block' >
    <label class="step_title" id="step_title_{$step.id_component|intval}">
        {if $step.icon_exist}
        <img src="{$modules_dir|escape:'htmlall':'UTF-8'}idxrcustomproduct/img/icon/{$step.id_component|intval}.png"/> 
        {/if}
        {$step.title|escape:'htmlall':'UTF-8'}
    </label>    
    <input type="file" class="form-control js-card-fileoption idxrcustomproduct-fileoption" id="file_{$step.id_component|intval}" data-maxsize="{$step.options->size}"/>
    <div class="idxrcustomproduct-fileoption-error alert alert-danger"></div>
    <span>{l s='Allowed files:' mod='idxrcustomproduct'} {foreach from=$step.options->allowed_extension item=extension name=foo}<b>.{$extension}</b>{if !$smarty.foreach.foo.last},{/if} {/foreach} {l s='maximum size' mod='idxrcustomproduct'} <b>{$step.options->size}MB</b> </span>
    <div class="hidden">
        <div class="clearfix">
            <span class="alert alert-warning hidden" id='next_alert_{$step.id_component|intval}'>{l s='Please select a file before continue with the customization' mod='idxrcustomproduct'}</span>
            <a id='js_icp_next_opt_{$step.id_component|intval}' data-type="{$step.type|escape:'htmlall':'UTF-8'}" class="js_icp_next_option btn btn-md btn-danger float-lg-right float-md-right float-sm-right" style="border-color: {$step.color|escape:'htmlall':'UTF-8'};background-color: {$step.color|escape:'htmlall':'UTF-8'};">
                {if isset($last) && $last}{l s='finish' mod='idxrcustomproduct'}{else}{l s='next' mod='idxrcustomproduct'}{/if}
                <i class="material-icons">expand_more</i>
            </a>
        </div>
    </div>
</div>
