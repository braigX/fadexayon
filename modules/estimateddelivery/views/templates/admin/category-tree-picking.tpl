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
 *}

{if isset($message)}{$message|escape:'htmlall':'UTF-8'}{/if}
{if $old_ps == 1}
<fieldset id="ed_cat_picking">
    <legend><img src="{$uri|escape:'htmlall':'UTF-8'}/img/cogs.gif" alt="{l s='Configuration' mod='estimateddelivery'}" />2.2 {l s='Aditional Picking Days' mod='estimateddelivery'}</legend>
{else}
<div class="panel" id="ed_cat_picking">
    <div class="panel-heading" data-position="5"><i class="icon-cogs"></i> 2.2 {l s='Aditional Picking Days' mod='estimateddelivery'}</div>
{/if}
    {if $ajax_replace}
        <div class="ajax-replace"
            data-id="{$ed_main_id|escape:'htmlall':'UTF-8'}"
            data-input-name="{$ed_input_name|escape:'htmlall':'UTF-8'}"
            data-selected-cat="{$ed_selected_cat|escape:'htmlall':'UTF-8'}"
            data-token="{$ed_token|escape:'htmlall':'UTF-8'}"
            data-callback="#ED_ADD_PICKING_MODE">
            <i class="icon icon-spinner icon-pulse icon-4x icon-fw"></i>
            <span class="sr-only">{l s='loading...' mod='estimateddelivery'}</span>
            <!-- Will be replaced by ajax content -->
        </div>
    {else}
       <div class="form-group">
            <label class="control-label col-lg-3">{l s='Select the method:' mod='estimateddelivery'}</label>
            <div class="col-lg-9">
                <select name="ED_ADD_PICKING_MODE" class="input fixed-width-xxl fixed-width-xl" id="ED_ADD_PICKING_MODE">
                    <option value="0" {if $ed_add_picking_mode == 0}selected="selected"{/if}>{l s='Category' mod='estimateddelivery'}</option>
                    <option value="1" {if $ed_add_picking_mode == 1}selected="selected"{/if}>{l s='Brand / Manufacturer' mod='estimateddelivery'}</option>
                    <option value="2" {if $ed_add_picking_mode == 2}selected="selected"{/if}>{l s='Supplier' mod='estimateddelivery'}</option>
                </select>
                <div class="desc help-block">{l s='Only one method can be selected to add picking days' mod='estimateddelivery'},<br>
                {l s='choose between Category or Brand/Manufacturer' mod='estimateddelivery'}</div>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-lg-3">{l s='Quick Guide:' mod='estimateddelivery'}</label>
            <div class="col-lg-9">
               <p>{l s='This additional picking days will be added to the Estimated Delivery before starting the calculation.' mod='estimateddelivery'}</p>
                <p class="category_picking">{l s='In this section you can set the days to add to the estimated delivery depending on the categories they are associated with.' mod='estimateddelivery'}</p>
                <p class="category_picking">{l s='The number between parenthesis is the actual values for the category. You can set it to 0 to disable this feature.' mod='estimateddelivery'}</p>
                <p class="category_picking">{l s='Products with multiple categories will calculate the days to add depending on the default category of the product. Keep that in mind.' mod='estimateddelivery'}</p>
                <p class="manufacturer_picking">{l s='In this section you can set the days to add to the estimated delivery depending on the product brand / manufacturer.' mod='estimateddelivery'}</p>
                <p class="manufacturer_picking">{l s='Input the additional picking days for each brand / manufacturer, set it to 0 or leave it blank to not use this feature.' mod='estimateddelivery'}</p>
                <p class="supplier_picking">{l s='In this section you can set the days to add to the estimated delivery depending on the provider / supplier of the product.' mod='estimateddelivery'}</p>
                <p class="supplier_picking">{l s='Input the additional picking days for each supplier, set it to 0 or leave it blank to not use this feature.' mod='estimateddelivery'}</p>
                <p class="category_picking">{l s='This setting can be overwriten individually in the product sheet. ' mod='estimateddelivery'}</p>
                <br/>
            </div>
        </div>
        <div id="cat_tree_picking" class="form-group category_picking">

            <label class="control-label col-lg-3">{l s='Choose the Categories:' mod='estimateddelivery'}</label>
            <div class="col-lg-9">
                {if trim($categories_tree) != ''}
                    {$categories_tree nofilter}{* HTML here, no escaping to prevent issues *}
                {else}
                    <div class="alert alert-warning">
                        {l s='Categories selection is disabled because you have no categories or you are in a "all shops" context.' mod='estimateddelivery'}
                    </div>
                {/if}
            </div>
        </div>
        {if isset($asso_shops)}
        <div class="form-group category_picking">
            <label class="control-label col-lg-3">{l s='Choose shop association:' mod='estimateddelivery'}</label>
            <div class="col-lg-9">{$asso_shops nofilter}{* HTML here, no escaping to prevent issues *}</div>
        </div>
        {/if}

        <div class="form-group category_picking">
            <label class="control-label col-lg-3">{l s='Days to add to the picking:' mod='estimateddelivery'}</label>
            <div class="col-lg-9">
                <input name="ed_cat_picking_days" id="ed_cat_picking_days" value="" class="input fixed-width-lg" type="text">
            </div>
        </div>
        <div style="clear:both"></div>
        <div class="form-group manufacturer_picking">
        {$manufacturer_picking nofilter} {* it's raw HTML *}
        </div>
        <div class="form-group supplier_picking">
        {$supplier_picking nofilter} {* it's raw HTML *}
        </div>
        <div style="clear:both"></div>
        <div class="panel-footer" id="toolbar-footer">
            <button class="btn btn-default pull-right" id="submit-add-picking" name="SubmitAddPicking" type="submit"><i class="process-icon-save"></i> <span>{l s='Save' mod='estimateddelivery'}</span></button>
        </div>
        <script type="text/javascript">
            $(document).ready(function() {
                detectMode($('#ED_ADD_PICKING_MODE'));
                // Brand / Manufacturer Selector
                $(document).on('change','#ED_ADD_PICKING_MODE',function(){
                    detectMode($(this));
                });
                function detectMode(e) {
                    if (e.prop('selectedIndex') == 0) {
                        // it's category
                        $('.manufacturer_picking').hide();
                        $('.supplier_picking').hide();
                        $('.category_picking').show();
                    } else if (e.prop('selectedIndex') == 1) {
                        // it's brand / manu
                        $('.manufacturer_picking').show();
                        $('.supplier_picking').hide();
                        $('.category_picking').hide();
                    } else if (e.prop('selectedIndex') == 2) {
                        // Supplier's
                        $('.manufacturer_picking').hide();
                        $('.supplier_picking').show();
                        $('.category_picking').hide();
                    }
                }
            });
        </script>
    {/if}
    {* </form> *}
{if $old_ps == 1}
    </fieldset>
{else}
    </div>
{/if}
