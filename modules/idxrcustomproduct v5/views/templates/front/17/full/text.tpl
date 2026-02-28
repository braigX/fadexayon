{*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innova Deluxe SL
* @copyright 2017 Innova Deluxe SL

* @license   INNOVADELUXE
*}
<h2 class="step_title card-title"  id="step_title_{$step.id_component|intval}">
    {if $step.icon_exist}
    <img class="step-img" src="{$modules_dir|escape:'htmlall':'UTF-8'}idxrcustomproduct/img/icon/{$step.id_component|intval}.png"/> 
    {/if}
    {$step.title|escape:'htmlall':'UTF-8'}
</h2>
<div class="card step_content" id="step_content_{$step.id_component|intval}" style="border-color: {$step.color|escape:'htmlall':'UTF-8'};">           
    <div class="card-header" style="background-color: {$step.color|escape:'htmlall':'UTF-8'};"><span>{$step.description|escape:'htmlall':'UTF-8'}</span></div>                        
    <div class="card-block">
        <div class="form-group">
            <input type="text" class="form-control" id="text_{$step.id_component|intval}" placeholder="{l s='Insert text' mod='idxrcustomproduct'}"/>
        </div>
    </div><!-- panel-body -->
    <div class="card-footer">
        <div class="clearfix"> <a id='js_icp_next_opt_{$step.id_component|intval}' data-type="{$step.type|escape:'htmlall':'UTF-8'}" class="js_icp_next_option btn btn-md btn-danger float-lg-right float-md-right float-sm-right" style="border-color: {$step.color|escape:'htmlall':'UTF-8'};background-color: {$step.color|escape:'htmlall':'UTF-8'};">{if $last}{l s='finish' mod='idxrcustomproduct'}{else}{l s='next' mod='idxrcustomproduct'}{/if}<i class="material-icons">expand_more</i></a></div>
    </div>
</div><!-- panel -->