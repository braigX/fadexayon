{*
* 2007-2022 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innova Deluxe SL
* @copyright 2022 Innova Deluxe SL

* @license   INNOVADELUXE
*}
<section class="page-product-box row ">
    <div class="rte" id="component_steps">
        <div id="component_steps_container" data-type='accordion'>            
           {* <h3>{l s='Configurator' mod='idxrcustomproduct'}</h3>*}
            {foreach from=$steps.components item=step name=foo}
                <div {include file="../../partials/component_data.tpl" step=$step} >
                    {include file="./"|cat:$step.type|cat:".tpl" step=$step last=$smarty.foreach.foo.last}
                </div>
            {/foreach}
            <div class="col-lg-12 component_step" id="component_step_last">
                <div class="card" style="border-color: {$steps.final_color|escape:'htmlall':'UTF-8'}">
                    <div class="card-header" style="background-color: {$steps.final_color|escape:'htmlall':'UTF-8'}">
                        <div class="card-header-h5"> 
                            <a class="d-block" data-toggle="collapse" data-parent="#component_steps_container" href="#component_step_resume" ><i class="material-icons">list</i> molkhass <i class="material-icons float-lg-right float-md-right float-sm-right float-xs-right">expand_more</i></a>
                        </div>  
                    </div>
                    <div id="component_step_resume" class="{if $steps.resume_open == 0}panel-collapse collapse{/if}" role="tabpanel">
                        <div class="card-block aaaaaa">
                            <p>{l s='components list' mod='idxrcustomproduct'}</p>
                            <hr />
                            <table class="table">
                                <tr {if $icp_price == 0}class="hidden"{/if}>
                                    <td>{l s='Base product' mod='idxrcustomproduct'}</td>
                                    <td>{$product_name|escape:'htmlall':'UTF-8'}</td>
                                    <td>
                                        {include file="../../partials/resume_base_price.tpl"}
                                    </td>
                                </tr>
                                {foreach from=$steps.components item=step name=foo}
                                    {include file="../../partials/resume_line.tpl" step=$step}
                                {/foreach}
                                {include file="../discount.tpl" conf=$steps}
                                {include file="../../partials/totals.tpl"}
                            </table>
                            {if !$steps['button_section']}
                            {include file="../../partials/actions.tpl"}
                            {/if}
                        </div><!-- card-block -->
                    </div>
                </div><!-- card -->
            </div>
        </div>
    </div>
    {include file="../../partials/descmodal.tpl"}
</section>
