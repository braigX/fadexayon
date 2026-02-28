{*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innova Deluxe SL
* @copyright 2017 Innova Deluxe SL

* @license   INNOVADELUXE
*}

<section class="page-product-box row">

    <h3 class="page-product-heading">{l s='Configure your product' mod='idxrcustomproduct'}</h3>
    <div class="rte" id="component_steps">
        <div id="component_steps_container">
            {foreach from=$steps.components item=step name=foo}
                <div id="component_step_{$smarty.foreach.foo.iteration|escape:'htmlall':'UTF-8'}" class="col-lg-12 component_step {if $smarty.foreach.foo.iteration == 1}front3d{else if $smarty.foreach.foo.iteration == 2 }right3d{else}other3d{/if}">
                    <h3>{$step.title|escape:'htmlall':'UTF-8'}</h3>
                    <div class="row">                
                        <span>{$step.description|escape:'htmlall':'UTF-8'}</span><br/>                        
                        {foreach from=$step.options->options item=option}                            
                            <div class="thumbnail option_div">
                                <input type="radio" name="option_{$step.id_component|intval}" value="{$option->id|intval}" id="option_{$step.id_component|intval}_{$option->id|intval}" class="js_icp_option chk_{$step.type|escape:'htmlall':'UTF-8'}"/>
                                <label for="option_{$step.id_component|intval}_{$option->id|intval}" id="option_{$step.id_component|intval}_{$option->id|intval}_name">{$option->name|escape:'htmlall':'UTF-8'}</label><br/>
                                
                                <a class="fancybox shown" href="{$modules_dir|escape:'htmlall':'UTF-8'}idxrcustomproduct/img/{$step.id_component|intval}_{$option->id|intval}.{$option->img_ext|escape:'htmlall':'UTF-8'}" title="{$option->description|escape:'htmlall':'UTF-8'}">
                                    <img id="option_{$step.id_component|intval}_{$option->id|intval}_img" src="{$modules_dir|escape:'htmlall':'UTF-8'}idxrcustomproduct/img/{$step.id_component|intval}_{$option->id|intval}.{$option->img_ext|escape:'htmlall':'UTF-8'}" width="75px" alt="Thumbnail Alt">
                                </a>
                                <br/>
                                <span>{$option->description|escape:'htmlall':'UTF-8'}</span><br/>
                            </div>
                        {/foreach}
                    </div>
                </div>
            {/foreach}
            <div id="component_step_{$steps.components|count+1}" class="col-lg-12 component_step {if ($steps.components|count) == 1}right3d{else}other3d{/if}">
                <h3>End</h3>
                {foreach from=$steps.components item=step name=foo}
                <div class="row">
                    <span class='hidden sel_opt' id='js_opt_{$step.id_component|intval}_value'>false</span>
                    {$step.title|escape:'htmlall':'UTF-8'} : <span id='js_resume_opt_{$step.id_component|intval}' class='opt_type_{$step.type|escape:'htmlall':'UTF-8'}'>{l s='Not selected' mod='idxrcustomproduct'}</span>
                </div>
                {/foreach}
                <div class="row">
                    <span id='icp_price'>{$icp_price|escape:'htmlall':'UTF-8'}</span>{$currency->sign|escape:'htmlall':'UTF-8'} <br/>
                    <span id="submit_idxrcustomproduct">{l s='You must fill all the customization options before to make the order' mod='idxrcustomproduct'}</span>
                </div>
            </div>
        </div>
        {if $steps.visualization == '3D'}
        <div id="component_step_breadcrum">
            <span id="component_step_pointer" data-index="1" data-total="{$steps.components|count+1}" style="display: none"></span>
            <span id="prev" class="btn btn-defaul">prev</span>
            <span id="component_step_actual">1</span>/<span id="component_step_total">{$steps.components|count+1}</span>
            <span id="next" class="btn btn-defaul">next</span>
        </div>
        {/if}
    </div>
</section>