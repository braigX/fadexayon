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
<fieldset id="ed_cat_oos">
    <legend><img src="{$uri|escape:'htmlall':'UTF-8'}/img/cogs.gif" alt="{l s='Configuration' mod='estimateddelivery'}" />2.3 {l s='OOS Days - Additional delivery days for products Out of Stock' mod='estimateddelivery'}</legend>
{else}
<div class="panel" id="ed_cat_oos">
    <div class="panel-heading" data-position="6"><i class="icon-cogs"></i> 2.3 {l s='Additional delivery days for products Out of Stock' mod='estimateddelivery'}</div>
{/if}
    {if $ajax_replace}
    <div class="ajax-replace"
         data-id="{$ed_main_id|escape:'htmlall':'UTF-8'}"
         data-input-name="{$ed_input_name|escape:'htmlall':'UTF-8'}"
         data-selected-cat="{$ed_selected_cat|escape:'htmlall':'UTF-8'}"
         data-token="{$ed_token|escape:'htmlall':'UTF-8'}"
         data-callback="#ED_ADD_OOS_DAYS_MODE">
        <i class="icon icon-spinner icon-pulse icon-4x icon-fw"></i>
        <span class="sr-only">{l s='loading...' mod='estimateddelivery'}</span>
        <!-- Will be replaced by ajax content -->
    </div>
    {else}
    <div class="col-lg-3"></div>
    <div class="col-lg-9">
        <!-- <h2 class="text-primary">{l s='OOS Days' mod='estimateddelivery'} - {l s='Additional delivery days for products Out of Stock' mod='estimateddelivery'}</h2> -->
        <h3 class="modal-title text-info">{l s='Global setting' mod='estimateddelivery'}</h3>
    </div>
    <div class="form-group">
        <label class="control-label col-lg-3">
            <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{l s='Set up this value if you can sell items when they are out of stock and you need to increase the delivery time for those items.' mod='estimateddelivery'}
            ">{l s='Extra delay for products Out of Stock' mod='estimateddelivery'}
            </span>
        </label>
        <div class="col-lg-9">
            <input type="text" name="ed_oos" id="ed_oos" value="{$ed_oos|intval}" class="input fixed-width-lg">
        </div>
    </div>
    <hr>
    <div class="col-lg-3"></div>
    <div class="col-lg-9">
        <!-- <h2 class="text-primary">{l s='OOS Days' mod='estimateddelivery'} - {l s='Additional delivery days for products Out of Stock' mod='estimateddelivery'}</h2> -->
        <h3 class="modal-title text-info">{l s='Additional delivery days by category, brand or supplier' mod='estimateddelivery'}</h3>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-3">{l s='Select the method:' mod='estimateddelivery'}</label>
        <div class="col-lg-9">
            <select name="ED_ADD_OOS_DAYS_MODE" class="input fixed-width-xxl fixed-width-xl" id="ED_ADD_OOS_DAYS_MODE">
                <option value="0" {if $ed_add_oos_days_mode == 0}selected="selected"{/if}>{l s='Category' mod='estimateddelivery'}</option>
                <option value="1" {if $ed_add_oos_days_mode == 1}selected="selected"{/if}>{l s='Supplier' mod='estimateddelivery'}</option>
                <option value="2" {if $ed_add_oos_days_mode == 2}selected="selected"{/if}>{l s='Manufacturer / Brand' mod='estimateddelivery'}</option>
            </select>
            <div class="desc help-block">{l s='Only one method can be selected to add Out of Stock days' mod='estimateddelivery'},<br>
            {l s='choose between Category, Supplier or Manufacturer' mod='estimateddelivery'}</div>
        </div>
    </div>
    <div class="form-group category_oos oos_detect">
        <label class="control-label col-lg-3">{l s='Quick Guide:' mod='estimateddelivery'}</label>
        <div class="col-lg-9">
           <p>{l s='For products without stock, but with sales allowed' mod='estimateddelivery'}.</p>
            <p>{l s='In this section you can set the days to add to the estimated delivery depending on the categories they are associated with.' mod='estimateddelivery'}</p>
            <p>{l s='The number between parenthesis is the actual values for the category. You can set it to 0 to disable this feature.' mod='estimateddelivery'}</p>
            <p>{l s='Products with multiple categories will calculate the days to add depending on the default category of the product. Keep that in mind.' mod='estimateddelivery'}</p>
            <p>{l s='This setting can be overwriten individually in the product sheet. ' mod='estimateddelivery'}</p>
            <br/>
        </div>
    </div>
    <div class="form-group supplier_oos oos_detect">
        <label class="control-label col-lg-3">{l s='Quick Guide:' mod='estimateddelivery'}</label>
        <div class="col-lg-9">
           <p>{l s='For products without stock, but with sales allowed' mod='estimateddelivery'}.</p>
            <p>{l s='In this section you can set the days to add to the estimated delivery depending on the supplier of the product.' mod='estimateddelivery'}</p>
            <p>{l s='This setting can be overwriten individually in the product sheet. ' mod='estimateddelivery'}</p>
            <br/>
        </div>
    </div>
    <div class="form-group manufacturer_oos oos_detect">
        <label class="control-label col-lg-3">{l s='Quick Guide:' mod='estimateddelivery'}</label>
        <div class="col-lg-9">
           <p>{l s='For products without stock, but with sales allowed' mod='estimateddelivery'}.</p>
            <p>{l s='In this section you can set the days to add to the estimated delivery depending on the manufacturer of the product.' mod='estimateddelivery'}</p>
            <p>{l s='This setting can be overwriten individually in the product sheet. ' mod='estimateddelivery'}</p>
            <br/>
        </div>
    </div>
    <div id="cat_tree_oos" class="oos_detect">
        <div class="form-group">
            <div class="content-replace">
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
        </div>
        <div class="form-group">
            <label class="control-label col-lg-3">{l s='Days to add when out of stock:' mod='estimateddelivery'}</label>
            <div class="col-lg-9">
                <input name="ed_cat_oos_days" id="ed_cat_oos_days" value="" class="input fixed-width-lg" type="text">
            </div>
        </div>
    </div>
    <div id="sup_tree_oos" class="oos_detect">
        <div class="form-group">
            {$supplier_oos nofilter} {* it's raw HTML *}
        </div>
    </div>
    <div id="manufacturer_tree_oos" class="oos_detect">
        <div class="form-group">
            {$manufacturer_oos nofilter} {* it's raw HTML *}
        </div>
    </div>
    <div style="clear:both"></div>
    <div class="panel-footer" id="toolbar-footer">
        <button class="btn btn-default pull-right" id="submit-cat-oos" name="SubmitCatOOs" type="submit"><i class="process-icon-save"></i> <span>{l s='Save' mod='estimateddelivery'}</span></button>
    </div>
        <script type="text/javascript">
            $(document).ready(function() {
                OOSDetectMode($('#ED_ADD_OOS_DAYS_MODE'));
                // Brand / Manufacturer Selector
                $(document).on('change', '#ED_ADD_OOS_DAYS_MODE', function () {
                    OOSDetectMode($(this));
                });

                function OOSDetectMode(e) {
                    $('.oos_detect').hide();
                    if (e.prop('selectedIndex') == 0) {
                        // it's category
                        $('.category_oos').show();
                        $('#cat_tree_oos').show();
                    } else if (e.prop('selectedIndex') == 1) {
                        // it's brand / manu
                        $('.supplier_oos').show();
                        $('#sup_tree_oos').show();
                    } else if (e.prop('selectedIndex') == 2) {
                        // it's brand / manu
                        $('.manufacturer_oos').show();
                        $('#manufacturer_tree_oos').show();
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
