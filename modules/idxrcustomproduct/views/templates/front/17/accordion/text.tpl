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
<div class="card step_content" id="step_content_{$step.id_component|intval}" style="border-color: {$step.color|escape:'htmlall':'UTF-8'};">           
    
    {include file="./card_header.tpl" step=$step}
    
	<div id="step_title_{$step.id_component|intval}" class="panel-collapse collapse" role="tabpanel">

	    <div class="card-block">
	    {include file="../../partials/component_description.tpl"}
	        <div class="input-group">
	            <input type="text" class="form-control accordion_text" id="text_{$step.id_component|intval}" placeholder="{l s='Insert text' mod='idxrcustomproduct'}"/>
                    <span class="input-group-btn">
                        <button class="btn btn-success js-text-next-btn" id="js-text-next-btn-{$step.id_component|intval}" type="button"><i class="material-icons">save</i></button>
                    </span>
	        </div>
	    </div><!-- card-body -->
            
    </div>
    <input type='hidden' id='js_icp_next_opt_{$step.id_component|intval}' data-type="{$step.type|escape:'htmlall':'UTF-8'}" class="js_icp_next_option">
</div><!-- card -->