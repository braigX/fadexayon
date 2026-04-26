{*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innova Deluxe SL
* @copyright 2017 Innova Deluxe SL

* @license   INNOVADELUXE
*}


<div class="panel step_content" id="step_content_{$step.id_component|intval}" style="border-color: {$step.color|escape:'htmlall':'UTF-8'};">
    <div class="panel-heading" style="border-color: {$step.color|escape:'htmlall':'UTF-8'}; background-color: {$step.color|escape:'htmlall':'UTF-8'};">
        <a data-toggle="collapse" data-parent="#component_steps_container" href="#step_title_{$step.id_component|intval}">
            <div id="alignment" >
                <p class="panel-title">
                    {if $step.icon_exist}
                    <img src="{$modules_dir|escape:'htmlall':'UTF-8'}idxrcustomproduct/img/icon/{$step.id_component|intval}.png"/> 
                    {/if}
                    {$step.title|escape:'htmlall':'UTF-8'}            
                </p>
                <span id="toggle_block_{$step.id_component|intval}" class="toggle_arrow icp_rotate">  </span>
            </div>
        </a>
        <div class="result">
            <span id="title_selected_{$step.id_component|intval}" class="value_selected"></span>
        </div>
    </div>                        

    <div id="step_title_{$step.id_component|intval}" class="panel-collapse collapse">   
        <div class="panel-body">
            <div class="form-group">
                <input type="file" class="form-control js-card-fileoption idxrcustomproduct-fileoption" id="file_{$step.id_component|intval}" data-maxsize="{$step.options->size}"/>
                <div class="idxrcustomproduct-fileoption-error alert alert-danger"></div>
                <span>{l s='Allowed files:' mod='idxrcustomproduct'} {foreach from=$step.options->allowed_extension item=extension name=foo}<b>.{$extension}</b>{if !$smarty.foreach.foo.last},{/if} {/foreach} {l s='maximum size' mod='idxrcustomproduct'} <b>{$step.options->size}MB</b> </span>
            </div>
        </div><!-- panel-body -->
        <input type='hidden' id='js_icp_next_opt_{$step.id_component|intval}' data-type="{$step.type|escape:'htmlall':'UTF-8'}" class="js_icp_next_option">
    </div>
</div><!-- panel -->