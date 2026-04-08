{*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innova Deluxe SL
* @copyright 2017 Innova Deluxe SL

* @license   INNOVADELUXE
*}
{*<h3 class="step_title card-title"  id="step_title_{$step.id_component|intval}"><img src="{$modules_dir|escape:'htmlall':'UTF-8'}idxrcustomproduct/img/icon/{$step.id_component|intval}.png"/> {$step.title|escape:'htmlall':'UTF-8'}</h3>*}
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
                <span id="toggle_block_{$step.id_component|intval}" class="toggle_arrow">  </span>
            </div>
        </a>
    </div>                        

	<div id="step_title_{$step.id_component|intval}" class="panel-collapse collapse">    

	    <div class="panel-body">

	    {include file="../../partials/component_description.tpl"}
            
	        <div class="input-group">                     
	            <input type="text" class="form-control accordion_text" id="text_{$step.id_component|intval}" placeholder="{l s='Insert text' mod='idxrcustomproduct'}">
                    <span class="input-group-btn">
                        <button class="btn btn-success js-text-next-btn" id="js-text-next-btn-{$step.id_component|intval}" type="button"><i class="icon icon-2x icon-save fa fa-2x fa-save"></i></button>
                    </span>
                </div>
	    </div><!-- panel-body -->
	    <input type='hidden' id='js_icp_next_opt_{$step.id_component|intval}' data-type="{$step.type|escape:'htmlall':'UTF-8'}" class="js_icp_next_option">

	</div>

</div><!-- panel -->