{*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innova Deluxe SL
* @copyright 2017 Innova Deluxe SL

* @license   INNOVADELUXE
*}

<section class="page-product-box row card">
    <div class="rte" id="component_steps">
        <div id="component_steps_container">
            {foreach from=$steps.components item=step name=foo}
                <div {include file="../../partials/component_data.tpl" step=$step} >
                    {include file="./"|cat:$step.type|cat:".tpl" step=$step}
                </div>
            {/foreach}

            <div id="component_step_last" class="col-lg-12 component_step">

                <div class="panel" style="border-color: {$steps.final_color|escape:'htmlall':'UTF-8'}">

                    <div class="panel-heading" style="background-color: {$steps.final_color|escape:'htmlall':'UTF-8'}">
                        <h4><i class="fa fa-list icon icon-list"></i> {l s='Summary' mod='idxrcustomproduct'}</h4>
                    </div>

                    <div class="panel-body">

                        <b>{l s='Components list' mod='idxrcustomproduct'}</b>
                        <br />

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
                    <tr>
                        <span class='hidden' id='js_tax_ratio'>{if $priceDisplay != 1}{$steps.tax_ratio|escape:'htmlall':'UTF-8'}{else}1{/if}</span>
                        <td colspan="2"><b>{l s='Product with this customization' mod='idxrcustomproduct'}</b></td>
                        <td>
                            <h4 class="pull-right">
                                <input autocomplete="off" type="hidden" id="js_resume_total_price" value="{if $priceDisplay != 1}{$icp_price|escape:'htmlall':'UTF-8'}{else}{$icp_price_wo|escape:'htmlall':'UTF-8'}{/if}">
                                <span id='resume_total_price'>
                                    {if $priceDisplay == 1}
                                        {if isset($icp_price_wo_wd_formated)}{$icp_price_wo_wd_formated|escape:'htmlall':'UTF-8'}{else}{$icp_price_wo_formated|escape:'htmlall':'UTF-8'}{/if}
                                    {else}
                                        {if isset($icp_price_wd_formated)}{$icp_price_wd_formated|escape:'htmlall':'UTF-8'}{else}{$icp_price_formated|escape:'htmlall':'UTF-8'}{/if}
                                    {/if}
                                </span>
                            </h4>
                        </td>
                    </tr>


                </table>    
            </div>
            <div class="panel-footer">
                {if !$steps['button_section']}
                {include file="../../partials/actions.tpl"}
                {/if}
            </div><!-- panel-body -->

        </div><!-- panel-success -->

    </div>

</div>

</div>
</section>