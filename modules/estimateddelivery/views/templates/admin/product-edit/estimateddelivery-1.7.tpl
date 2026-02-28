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
<br>
{capture name="product_text" assign="product_text"}{l s='product' mod='estimateddelivery'}{/capture}
{capture name="combination_text" assign="combination_text"}{l s='combination' mod='estimateddelivery'}{/capture}
<div class="panel product-tab" id="estimatedDelivery">
    {if isset($productid)}
        <div class="row">
            <div class="col-lg-12">
                <h2>{l s='Estimated Delivery' mod='estimateddelivery'}</h2>
            </div>
            <div class="col-lg-12">
                <div class="alert alert-info">
                    <p>{l s='Here you can set additional options for Estimated Delivery module.' mod='estimateddelivery'}</p>
                </div>
            </div>

            {* 1.7 Switch style*}
            <div class="col-lg-12">
                <div class="form-group">
                    <div class="control-label col-lg-12">
                        <div class="checkbox">
                            <input type="checkbox" data-toggle="switch" class="tiny" id="ed_prod_dis"
                                   name="estimateddelivery[ed_prod_dis]" value="1"
                                   {if isset($ed_prod_dis) && $ed_prod_dis}checked="checked"{/if}>
                            <h3 class="d-inline"><label
                                        for="ed_prod_dis">{l s='Disable the ED for this product' mod='estimateddelivery'}</label>
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="disabled-warning col-lg-12 {if !isset($ed_prod_dis) || !$ed_prod_dis}d-none{/if}">
                <div class="alert alert-warning">
                    <p>{l s='Estimated Delivery generation disabled for this product. This product won\'t generate a Estimated Delivery date.' mod='estimateddelivery'}</p>
                </div>
            </div>
        </div>
    <div class="ed_status {if isset($ed_prod_dis) && $ed_prod_dis}ed_disabled{/if}">
        <div class="row">
            <div class="col-lg-6 col-xs-12">
                <div class="form-group">
                    <label class="control-label" for="ed_prod_picking">
                        <h3><span data-original-title=" {l s='Product Picking days' mod='estimateddelivery'}"
                                  class="label-tooltip" data-toggle="tooltip"
                                  title=""> {l s='Product Picking days' mod='estimateddelivery'} </span><span
                                    class="help-box" data-toggle="popover"
                                    data-content="{l s='Set up the number of additional days you need to prepare this %s' mod='estimateddelivery' sprintf=[$product_text]}">
                        </h3>
                    </label>
                    <input id="ed_prod_picking" name="estimateddelivery[ed_prod_picking]"
                           value="{if isset($ed_prod_picking)}{$ed_prod_picking|intval}{/if}"
                           class="col-lg-11 form-control" type="text"/>
                    <p class="desc description">{l s='Use this setting if your product need some days to be prepared for the shipping' mod='estimateddelivery'}
                        .<br/>
                        {l s='If you have entered a value set it to 0 to ignore it.' mod='estimateddelivery'}</p>
                </div>
            </div>
            <div class="col-lg-6 col-xs-12">
                <div class="form-group">
                    <label for="ed_prod_oos">
                        <h3><span data-original-title=" {l s='Out of stock additional days' mod='estimateddelivery'}"
                                  class="label-tooltip" data-toggle="tooltip"
                                  title=""> {l s='Out of stock additional days' mod='estimateddelivery'} </span><span
                                    class="help-box" data-toggle="popover"
                                    data-content="{l s='Set up the number of additional days if this %s is out of stock' mod='estimateddelivery' sprintf=[$product_text]}"></span>
                        </h3>
                    </label>
                    <input id="ed_prod_oos" name="estimateddelivery[ed_prod_oos]"
                           value="{if isset($ed_prod_oos)}{$ed_prod_oos|intval}{/if}" class="col-lg-11 form-control"
                           type="text"/>
                    <p class="desc description">{l s='This setting overrides all configurations made globaly on the module configuration page.' mod='estimateddelivery'}
                        <br/>
                        {l s='If you have entered a value set it to 0 to ignore it.' mod='estimateddelivery'}</p>
                </div>
            </div>
            <div class="col-lg-6 col-xs-12">
                <div class="form-group">
                    <label for="ed_prod_custom_days">
                        <h3><span data-original-title=" {l s='Customization days' mod='estimateddelivery'}"
                                  class="label-tooltip" data-toggle="tooltip"
                                  title=""> {l s='Customization days' mod='estimateddelivery'} </span><span
                                    class="help-box" data-toggle="popover"
                                    data-content="{l s='Set up the number of additional days if this %s gets customized' mod='estimateddelivery' sprintf=[$product_text]}"></span>
                        </h3>
                    </label>
                    <input id="ed_prod_custom_days" name="estimateddelivery[ed_prod_custom_days]"
                           value="{if isset($ed_prod_custom_days)}{$ed_prod_custom_days|intval}{/if}"
                           class="col-lg-11 form-control" type="text"/>
                    <p class="desc description">{l s='This setting overrides all configurations made globaly on the module configuration page.' mod='estimateddelivery'}
                        <br/>
                        {l s='If you have entered a value set it to 0 to ignore it.' mod='estimateddelivery'}</p>
                </div>
            </div>
            <div class="col-lg-6 col-xs-12">
                <div class="form-group">
                    <label class="control-label" for="ed_prod_release">
                        <h3>
                            <span data-original-title=" {l s='Set a date to activate this feature, once a product has a release date it will set the estimated delivery to that date, once passed it will use it\'s default behaviour' mod='estimateddelivery'}"
                                  class="label-tooltip" data-toggle="tooltip"
                                  title=""> {l s='Set a release date' mod='estimateddelivery'} </span><span
                                    class="help-box" data-toggle="popover"
                                    data-content="{l s='Does this %s have a public release date? Set it here' mod='estimateddelivery' sprintf=[$product_text]}"></span>
                        </h3>
                    </label>
                    <div class="input-group datepicker">
                        <input id="ed_prod_release" name="estimateddelivery[ed_prod_release]"
                               class="datepicker form-control"
                               value="{if isset($ed_prod_release)}{$ed_prod_release|escape:'htmlall':'UTF-8'}{/if}"
                               style="text-align: center" type="text">
                        <div class="input-group-append">
                            <div class="input-group-text"><i class="material-icons">date_range</i></div>
                        </div>
                    </div>
                    <p class="desc description"> {l s='Set a date to activate this feature, once a product has a release date it will set the estimated delivery to that date, once passed it will use it\'s default behaviour' mod='estimateddelivery'}
                        <br/>
                        {l s='Leave empty to ignore this field.' mod='estimateddelivery'}</p>
                </div>
            </div>
        </div>
        <!-- /.row -->
        {if isset($combiList) && count($combiList) > 0}
            <br>
            <br>
            <br>
            <hr>
            <div class="row">
                <div class="col-lg-12">
                    <h3>{l s='Set Estimated Delivery settings by combination' mod='estimateddelivery'}</h3>
                    <p>{l s='Set up this parameters to have a combination control of each parameter for the ED.' mod='estimateddelivery'}</p>
                </div>
                <div class="col-lg-12">
                    <div class="form-group">
                        <fieldset>
                            <table class="table">
                                <thead>
                                <tr>
                                    <th class="col-lg-4 col-sm-2">{l s='Combination' mod='estimateddelivery'}
                                        <span class="help-box" data-toggle="popover"
                                              data-content="{l s='Combinations are the different variations of a product, here you will see a list of each combination available for this product' mod='estimateddelivery'}"></span>
                                    </th>
                                    <th class="col-lg-1 col-sm-1">{l s='Disabled' mod='estimateddelivery'}
                                        <span class="help-box" data-toggle="popover"
                                              data-content="{l s='Disable the date generation for this %s?' mod='estimateddelivery' sprintf=[$combination_text]}"></span>
                                    </th>
                                    <th class="col-lg-1 col-sm-2">{l s='Picking Days' mod='estimateddelivery'}
                                        <span class="help-box" data-toggle="popover"
                                              data-content="{l s='Set up the number of additional days you need to prepare this %s' mod='estimateddelivery' sprintf=[$combination_text]}"></span>
                                    </th>
                                    <th class="col-lg-1 col-sm-2">{l s='OOS Days' mod='estimateddelivery'}
                                        <span class="help-box" data-toggle="popover"
                                              data-content="{l s='Set up the number of additional days if this %s is out of stock' mod='estimateddelivery' sprintf=[$combination_text]}"></span>
                                    </th>
                                    <th class="col-lg-1 col-sm-2">{l s='Customization Days' mod='estimateddelivery'}
                                        <span class="help-box" data-toggle="popover"
                                              data-content="{l s='Set up the number of additional days if this %s gets customized' mod='estimateddelivery' sprintf=[$combination_text]}"></span>
                                    </th>
                                    <th class="col-lg-2 col-sm-2">{l s='Restock Date' mod='estimateddelivery'}
                                        <span class="help-box" data-toggle="popover"
                                              data-content="{l s='If this %s does not have stock, which should be the restock date?' mod='estimateddelivery' sprintf=[$combination_text]}"></span>
                                    </th>
                                    <th class="col-lg-2 col-sm-2">{l s='Release Date' mod='estimateddelivery'}
                                        <span class="help-box" data-toggle="popover"
                                              data-content="{l s='Does this %s have a public release date? Set it here' mod='estimateddelivery' sprintf=[$combination_text]}"></span>
                                    </th>
                                </tr>
                                </thead>
                                <tbody>

                                {foreach from=$combiList item=combi}
                                    {if $combi.combiName != ''}
                                        <tr {if $combi.disabled == 1}class="ed_disabled"{/if}>
                                            <td>
                                                <div class="edCombiName">({isset($combi.disabled) && $combi.disabled|var_dump}{$combi.disabled|intval}){$combi.combiName|escape:'htmlall':'UTF-8'}</div>
                                            </td>
                                            <td>
                                                <div class="edDisableCombi">
                                                    <div class="checkbox">
                                                        <input type="checkbox" data-toggle="switch"
                                                               class="tiny ed_combi_dis"
                                                               name="estimateddelivery[combi][{$combi.id|intval}][disabled]"
                                                               value="1"
                                                               {if isset($combi.disabled) && $combi.disabled}checked="checked"{/if}>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="edSmallInput">
                                                    <input class="form-control text-sm-right" type="text"
                                                           name="estimateddelivery[combi][{$combi.id|intval}][picking_days]"
                                                           value="{if isset($combi.picking_days)}{$combi.picking_days|escape:'htmlall':'UTF-8'}{/if}"/>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="edSmallInput">
                                                    <input class="form-control text-sm-right" type="text"
                                                           name="estimateddelivery[combi][{$combi.id|intval}][delay]"
                                                           value="{if isset($combi.delay)}{$combi.delay|escape:'htmlall':'UTF-8'}{/if}"/>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="edSmallInput">
                                                    <input class="form-control text-sm-right" type="text"
                                                           name="estimateddelivery[combi][{$combi.id|intval}][customization_days]"
                                                           value="{if isset($combi.customization_days)}{$combi.customization_days|escape:'htmlall':'UTF-8'}{/if}"/>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="edSmallDate">
                                                    <div class="input-group datepicker">
                                                        <input type="text" class="datepicker form-control text-md-right"
                                                               placeholder="YYYY-MM-DD"
                                                               name="estimateddelivery[combi][{$combi.id|intval}][restock_date]"
                                                               value="{if isset($combi.restock_date)}{$combi.restock_date|escape:'htmlall':'UTF-8'}{/if}"
                                                               value="0000-00-00">
                                                        <div class="input-group-append">
                                                            <div class="input-group-text"><i class="material-icons">date_range</i>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="edSmallDate">
                                                    <div class="input-group datepicker">
                                                        <input type="text" class="datepicker form-control text-md-right"
                                                               placeholder="YYYY-MM-DD"
                                                               name="estimateddelivery[combi][{$combi.id|intval}][release_date]"
                                                               value="{if isset($combi.release_date)}{$combi.release_date|escape:'htmlall':'UTF-8'}{/if}"/>
                                                        <div class="input-group-append">
                                                            <div class="input-group-text"><i class="material-icons">date_range</i>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    {/if}
                                {/foreach}
                                </tbody>
                            </table>
                        </fieldset>
                    </div>
                </div>
            </div>
            </div>
            <!-- /.row -->
    {else}
        </div>
    {/if}
    {else}
        <div class="alert alert-warning">{l s='Warning: To add additional delivery days to your product you have to Save it First.' mod='estimateddelivery'}</div>
    {/if}
    <script type="text/javascript">
        /*$(document).ready(function() {
            if ($("#estimatedDelivery .datepicker").length > 0)
                $("#estimatedDelivery .datepicker").datepicker({
                    prevText: '',
                    nextText: '',
                    dateFormat: 'yy-mm-dd'
                });
        });*/
    </script>
</div>
