{**
* 2007-2020 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innova Deluxe SL
* @copyright 2020 Innova Deluxe SL
* @license   INNOVADELUXE
*}

<section class="page-product-box row">
    <div class="rte" id="component_steps">
        <div id="component_steps_container" data-type='minified'>
            {foreach from=$steps.components item=step name=foo}
            <input type='hidden' value='{$step.color}' id='minified_color'>
            <div {include file="../../partials/component_data.tpl" step=$step} >
                {include file="./"|cat:$step.type|cat:".tpl" step=$step}
            </div>
            {/foreach}

            <div id="component_step_last" class="col-lg-12 component_step">
                
                
                
                <div class="card" style="border-color: {$steps.final_color|escape:'htmlall':'UTF-8'}">

                    <div class="card-header" style="background-color: {$steps.final_color|escape:'htmlall':'UTF-8'}">
                        <h3><i class="material-icons">list</i>molkhas 4</h3>
                    </div>
    
                    <div class="card-block">

                    <p>{l s='Components list' mod='idxrcustomproduct'}</p>

                        <table class="table">                            
                            <tr {if $icp_price == 0}class="hidden"{/if}>
                                <td>{l s='Base product' mod='idxrcustomproduct'}</td>
                                <td>{$product_name|escape:'htmlall':'UTF-8'}</td>
                                <td>
                                    <span class="pull-right">
                                        <input autocomplete="off" type="hidden" class='js_base_price' value="{if $priceDisplay == 1}{$icp_price_wo|escape:'htmlall':'UTF-8'}{else}{$icp_price|escape:'htmlall':'UTF-8'}{/if}" />
                                        <span id="idx_resume_base_price">
                                        {if $priceDisplay == 1}
                                            {if isset($icp_price_wo_wd_formated)}{$icp_price_wo_wd_formated|escape:'htmlall':'UTF-8'}{else}{$icp_price_wo_formated|escape:'htmlall':'UTF-8'}{/if}
                                        {else}
                                            {if isset($icp_price_wd_formated)}{$icp_price_wd_formated|escape:'htmlall':'UTF-8'}{else}{$icp_price_formated|escape:'htmlall':'UTF-8'}{/if}
                                        {/if}
                                        </span>
                                    </span>
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
                    </div>

                </div><!--card -->

            </div><!-- component_step_last -->

        </div><!-- component_step_container -->


    </div><!-- rte -->   
    {include file="../../partials/descmodal.tpl"}
</section>