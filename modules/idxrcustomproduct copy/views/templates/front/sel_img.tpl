{*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innova Deluxe SL
* @copyright 2017 Innova Deluxe SL

* @license   INNOVADELUXE
*}

<h3 class="step_title"  id="step_title_{$step.id_component|intval}"><img src="{$modules_dir|escape:'htmlall':'UTF-8'}idxrcustomproduct/img/icon/{$step.id_component|intval}.png"/> {$step.title|escape:'htmlall':'UTF-8'}</h3>
<div class="panel panel-red step_content" id="step_content_{$step.id_component|intval}" style="border-color: {$step.color|escape:'htmlall':'UTF-8'};">           
    <div class="panel-heading" style="border-color: {$step.color|escape:'htmlall':'UTF-8'}; background-color: {$step.color|escape:'htmlall':'UTF-8'};"><span>{$step.description|escape:'htmlall':'UTF-8'}</span></div>                        
    <div class="panel-body">
        {foreach from=$step.options->options item=option}
            <div class="option_div col-lg-{math equation="12 / x" x=$step.columns} thumbnail text-center">
                <input type="radio" name="option_{$step.id_component|intval}" value="{$option->id|intval}" data-price-impact='{$option->price_impact|escape:'htmlall':'UTF-8'}' data-weight-impact='{$option->weight_impact|escape:'htmlall':'UTF-8'}' data-reference='{$option->reference|escape:'htmlall':'UTF-8'}' id="option_{$step.id_component|intval}_{$option->id|intval}" class="js_icp_option chk_{$step.type|escape:'htmlall':'UTF-8'}" {if $smarty.foreach.foo.iteration > 1}disabled{/if}/>
                <br/>

                <a class="fancybox shown" data-fancybox-group="gallery{$step.id_component|intval}" href="{$opcion_img_dir|escape:'htmlall':'UTF-8'}{$step.id_component|intval}_{$option->id|intval}.{$option->img_ext|escape:'htmlall':'UTF-8'}" title="{$option->description|escape:'htmlall':'UTF-8'}">
                    <img id="option_{$step.id_component|intval}_{$option->id|intval}_img" src="{$opcion_img_dir|escape:'htmlall':'UTF-8'}{$step.id_component|intval}_{$option->id|intval}.{$option->img_ext|escape:'htmlall':'UTF-8'}" class="img-responsive" alt="Thumbnail Alt">
                </a>


                <label class="option_titles" id="option_{$step.id_component|intval}_{$option->id|intval}_name">{$option->name|escape:'htmlall':'UTF-8'}</label>
                {if $option->description}<span class="option_description">{$option->description|escape:'htmlall':'UTF-8'}</span>{/if}<br/>
            </div>                        
        {/foreach}
    </div><!-- panel-body -->
    <div class="panel-footer">

        <div class="clearfix"> <a id='js_icp_next_opt_{$step.id_component|intval}' class="js_icp_next_option btn btn-md btn-default pull-right"><i class="fa fa-arrow-down"></i> {l s='next' mod='idxrcustomproduct'}</a></div>
    </div>

</div><!-- panel -->