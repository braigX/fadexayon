{** * Estimated Delivery - Front Office Feature
 *
 * NOTICE OF LICENSE
 *
 * @author    Pol Rué
 * @copyright Smart Modules 2015
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * @category Transport & Logistics
 * Registered Trademark & Property of smart-modules.com
 *
 * ***************************************************
 * *               Estimated Delivery                *
 * *          http://www.smart-modules.com           *
 * *                                                  *
 * ***************************************************
**}
<div id="estimatedDelivery"> 
    {if $old_ps}
    <fieldset>
        <legend><i class="icon-info"></i> {l s='Estimated Delivery' mod='estimateddelivery'}</legend>
    {else}
    <div class="separation panel product-tab">
        <div class="panel-heading">{l s='Estimated Delivery' mod='estimateddelivery'}</div>
    {/if}
        {if isset($productid)}
            <div class="alert alert-info">
                {l s='Here you can set additional options for Estimated Delivery module.' mod='estimateddelivery'}
            </div>
            <label class="control-label col-lg-3" for="ed_prod_picking">
                <span data-original-title=" {l s='Disable the ED for this product' mod='estimateddelivery'}" class="label-tooltip" data-toggle="tooltip" title="">
                    {l s='Disable the ED for this product' mod='estimateddelivery'}
                </span>
            </label>
            <div class="col-lg-9">
                <span class="switch prestashop-switch fixed-width-lg">
                <input type="radio" name="ed_prod_dis" id="ed_prod_dis_on" value="1">
                <label for="ed_prod_dis_on">{l s='Yes' mod='estimateddelivery'}</label>
                <input type="radio" name="ed_prod_dis" id="ed_prod_dis_off" value="0" checked="checked">
                <label for="ed_prod_dis_off">{l s='No' mod='estimateddelivery'}</label>
                <a class="slide-button btn"></a>
                </span>
                <p class="help-block">{l s='Enable this setting to disable the Estimated Delivery calculation for this product' mod='estimateddelivery'}</p>
			</div>
            <label class="control-label col-lg-3" for="ed_prod_picking"> 
                <span data-original-title=" {l s='Product Picking days' mod='estimateddelivery'}" class="label-tooltip" data-toggle="tooltip" title=""> 
                    {l s='Product Picking days' mod='estimateddelivery'} 
                </span>
            </label>
            <div class="col-lg-8">
                <input id="ed_prod_picking" name="ed_prod_picking" value="{if isset($ed_prod_picking)}{$ed_prod_picking|intval}{/if}" class="col-lg-11" type="text" />
                <p class="desc description">
                    {l s='Use this setting if your product need some days to be prepared for the shipping' mod='estimateddelivery'}.<br />
                    {l s='If you have entered a value set it to 0 to ignore it.' mod='estimateddelivery'}
                </p>
            </div>
            <label class="control-label col-lg-3" for="ed_prod_oos"> 
                <span data-original-title=" {l s='Out of stock additional days' mod='estimateddelivery'}" class="label-tooltip" data-toggle="tooltip" title="">
                    {l s='Out of stock additional days' mod='estimateddelivery'} 
                </span> 
            </label>
            <div class="col-lg-8">
                <input id="ed_prod_oos" name="ed_prod_oos" value="{if isset($ed_prod_oos)}{$ed_prod_oos|intval}{/if}" class="col-lg-11" type="text" />
                <p class="desc description">
                    {l s='This setting overrides all configurations made globaly on the module configuration page.' mod='estimateddelivery'}<br />
                    {l s='If you have entered a value set it to 0 to ignore it.' mod='estimateddelivery'}
                </p>
            </div>
            <!-- customization days -->
            <label class="control-label col-lg-3" for="ed_prod_custom_days"> 
                <span data-original-title=" {l s='Customization days' mod='estimateddelivery'}" class="label-tooltip" data-toggle="tooltip" title="">
                    {l s='Customization days' mod='estimateddelivery'} 
                </span> 
            </label>
            <div class="col-lg-8">
                <input id="ed_prod_custom_days" name="ed_prod_custom_days" value="{if isset($ed_prod_custom_days)}{$ed_prod_custom_days|intval}{/if}" class="col-lg-11" type="text" />
                <p class="desc description">
                    {l s='This setting overrides all configurations made globaly on the module configuration page.' mod='estimateddelivery'}<br />
                    {l s='If you have entered a value set it to 0 to ignore it.' mod='estimateddelivery'}
                </p>
            </div> <!-- end of customizatin days -->
            <label class="control-label col-lg-3" for="ed_prod_release"> 
                <span data-original-title=" {l s='Set a date to activate this feature, once a product has a release date it will set the estimated delivery to that date, once passed it will use it\'s default behaviour' mod='estimateddelivery'}"
                    class="label-tooltip" data-toggle="tooltip" title="">
                    {l s='Set a release date' mod='estimateddelivery'} 
                </span> 
            </label>
            <div class="col-lg-9">
                <div class="input-group fixed-width-xxl">
                    <input id="ed_prod_release" name="ed_prod_release" class="datepicker" value="{if isset($ed_prod_release)}{$ed_prod_release|escape:'htmlall':'UTF-8'}{/if}" style="text-align: center" type="text">
                    <div class="input-group-addon"> <i class="icon-calendar-empty"></i> </div>
                </div>
                <p class="desc description">
                    {l s='Set a date to activate this feature, once a product has a release date it will set the estimated delivery to that date, once passed it will use it\'s default behaviour' mod='estimateddelivery'}<br />
                    {l s='Leave empty to ignore this field.' mod='estimateddelivery'}
                </p>
            </div>
            <div style="clear:both"></div>

            {if $version >= 1.6}
            <div class="panel-footer"> 
                <a href="{$link->getAdminLink('AdminProducts')|escape:'htmlall':'UTF-8'}" class="btn btn-default">
                    <i class="process-icon-cancel"></i> {l s='Cancel' mod='estimateddelivery'}
                </a>
                <button type="submit" name="submitAddproduct" class="btn btn-default pull-right">
                    <i class="process-icon-save"></i> {l s='Save' mod='estimateddelivery'}
                </button>
                <button type="submit" name="submitAddproductAndStay" class="btn btn-default pull-right">
                    <i class="process-icon-save"></i> {l s='Save and stay' mod='estimateddelivery'}
                </button>
            </div>

        {if $old_ps}
        </fieldset>
        {else}
        </div>
        {/if}
    {/if}
    {if isset($combiList) && count($combiList) > 0}
        {if $old_ps}
            <fieldset>
                <legend><i class="icon-info"></i> {l s='Set Estimated Delivery settings by combination' mod='estimateddelivery'}
                </legend>
            {else}
                <div class="separation panel product-tab">
                    <div class="panel-heading">{l s='Set Estimated Delivery settings by combination' mod='estimateddelivery'}</div>
                {/if}
                <div class="row">
                    <div class="col-lg-12">
                        <p>{l s='Set up this parameters to have a combination control of each parameter for the ED.' mod='estimateddelivery'}
                        </p>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group">
                            <fieldset>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th class="col-lg-6">{l s='Combination' mod='estimateddelivery'}</th>
                                            <th class="col-lg-1">{l s='Customization Days' mod='estimateddelivery'}</th>
                                            <th class="col-lg-1">{l s='Product Picking Days' mod='estimateddelivery'}</th>
                                            <th class="col-lg-1">{l s='OOS Days' mod='estimateddelivery'}</th>
                                            <th class="col-lg-2">{l s='Restock Date' mod='estimateddelivery'}</th>
                                            <th class="col-lg-2">{l s='Release Date' mod='estimateddelivery'}</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        {foreach from=$combiList item=combi}
                                            <tr>
                                                <td>
                                                    <div class="edCombiName">{$combi.combiName|escape:'htmlall':'UTF-8'}</div>
                                                </td>
                                                <td>
                                                    <div class="edSmallInput">
                                                        <input class="form-control text-sm-right" type="text" name="estimateddelivery[combi][{$combi.id|intval}][customization_days]"
                                                            value="{$combi.customization_days|escape:'htmlall':'UTF-8'}" />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="edSmallInput">
                                                        <input class="form-control text-sm-right" type="text" name="estimateddelivery[combi][{$combi.id|intval}][picking_days]"
                                                            value="{$combi.picking_days|escape:'htmlall':'UTF-8'}" />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="edSmallInput">
                                                        <input class="form-control text-sm-right" type="text" name="estimateddelivery[combi][{$combi.id|intval}][delay]"
                                                            value="{$combi.delay|escape:'htmlall':'UTF-8'}" />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="edSmallDate">
                                                        <input type="text" class="datepicker form-control text-md-right"
                                                            placeholder="YYYY-MM-DD"
                                                            name="estimateddelivery[combi][{$combi.id|intval}][restock_date]"
                                                            value="{$combi.restock_date|escape:'htmlall':'UTF-8'}"
                                                            value="0000-00-00">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="edSmallDate">
                                                        <input type="text" class="datepicker form-control text-md-right"
                                                            placeholder="YYYY-MM-DD"
                                                            name="estimateddelivery[combi][{$combi.id|intval}][release_date]"
                                                            value="{$combi.release_date|escape:'htmlall':'UTF-8'}" />
                                                    </div>
                                                </td>
                                            </tr>
                                        {/foreach}
                                    </tbody>

                                </table>
                            </fieldset>
                        </div>
                    </div>
                </div><!-- /.row -->
            {/if}
        {else}
            <div class="alert alert-warning">
                {l s='Warning: To add additional delivery days to your product you have to Save it First.' mod='estimateddelivery'}
            </div>
        {/if}
        {if $old_ps}
    </fieldset>
{else}
    </div>
{/if}
<script type="text/javascript">
    $(document).ready(function() {
        if ($(".datepicker").length > 0)
            $(".datepicker").datepicker({
                prevText: '',
                nextText: '',
                dateFormat: 'yy-mm-dd'
            });
    });
</script>
</div>