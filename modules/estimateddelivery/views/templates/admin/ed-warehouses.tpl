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
<fieldset id="ed_warehouse">
    <legend><img src="{$uri|escape:'htmlall':'UTF-8'}/img/cogs.gif" alt="{l s='Warehouse Aditional Days' mod='estimateddelivery'}" />2.7 {l s='Warehouse Aditional Days' mod='estimateddelivery'}</legend>
{else}
<div class="panel" id="ed_warehouse">
    <div class="panel-heading" data-position="9"><i class="icon-cogs"></i> 2.7 {l s='Warehouse Aditional Days' mod='estimateddelivery'}</div>
{/if}
    {if $ed_wh_types|count gt 0}
    <div class="form-group">
        <label class="control-label col-lg-3">{l s='Quick Guide:' mod='estimateddelivery'}</label>
        <div class="col-lg-9">
           <p>{l s='This additional picking days will be added to the Estimated Delivery before starting the calculation.' mod='estimateddelivery'}</p>
            <p class="warehouse_picking">{l s='In this section you can set the days to add to the estimated delivery depending on the warehouses they are associated with.' mod='estimateddelivery'}</p>
            <br/>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-3">{l s='Select the method:' mod='estimateddelivery'}</label>
        <div class="col-lg-9">
            <select name="ED_WAREHOUSES_MODE" class="input fixed-width-xxl fixed-width-xl" id="ED_WAREHOUSES_MODE">
                {if isset($ed_wh_types.supplier)}
                <option value="1" {if $ed_warehouses_mode == 1}selected="selected"{/if}>{l s='Supplier' mod='estimateddelivery'}</option>
                {/if}
                {if $ed_wh_types.manufacturer}
                <option value="2" {if $ed_warehouses_mode == 2}selected="selected"{/if}>{l s='Manufacturer / Brand' mod='estimateddelivery'}</option>
                {/if}
            </select>
            <div class="desc help-block">{l s='Only one method can be selected to add Out of Stock days' mod='estimateddelivery'},<br>
                {l s='choose between Category, Supplier or Manufacturer' mod='estimateddelivery'}</div>
        </div>
    </div>
        <div class="form-group supplier_wh wh_detect">
            <label class="control-label col-lg-3">{l s='Quick Guide:' mod='estimateddelivery'}</label>
            <div class="col-lg-9">
                <p>{l s='In this section you can set the additional delivery days for the selected warehouse based on the supplier of the product.' mod='estimateddelivery'}</p>
                <!--<p>{l s='This setting can be overwriten individually in the product sheet. ' mod='estimateddelivery'}</p> -->
                <br/>
            </div>
        </div>
        <div class="form-group manufacturer_wh wh_detect">
            <label class="control-label col-lg-3">{l s='Quick Guide:' mod='estimateddelivery'}</label>
            <div class="col-lg-9">
                <p>{l s='In this section you can set the additional delivery days for the selected warehouse based on the manufacturer of the product.' mod='estimateddelivery'}</p>
                <!--<p>{l s='This setting can be overwriten individually in the product sheet. ' mod='estimateddelivery'}</p> -->
                <br/>
            </div>
        </div>
    {foreach from=$ed_wh_types item=ed_type}
        {assign var="type_id" value="id_`$ed_type`"}
        <div class="form-group warehouse_{$ed_type|escape:'htmlall':'UTF-8'}_picking wh_detect">
            <div class="warehouse_navigation">
                <p>
            {foreach from=$ed_warehouses item=warehouse}
                <a class="btn btn-primary" data-toggle="collapse" href="#ed_warehouse_{$warehouse.id_warehouse|intval}_{$ed_type|escape:'htmlall':'UTF-8'}" role="button" aria-expanded="false" aria-controls="collapseExample">
                    {$warehouse.name|escape:'htmlall':'UTF-8'}
                </a>
                {*<div class="panel" id="ed_warehouse_{$warehouse.id_warehouse|intval}">
                <div class="panel-header">
                    <h4>{$warehouse.name|escape:'htmlall':'UTF-8'}</h4>
                </div> *}
            {/foreach}
                </p>
            </div>
            {foreach from=$ed_warehouses item=warehouse}
                <div class="panel collapse" id="ed_warehouse_{$warehouse.id_warehouse|intval}_{$ed_type|escape:'htmlall':'UTF-8'}">
                    <div class="panel-header">
                        <h4>{$warehouse.name|escape:'htmlall':'UTF-8'}</h4>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            {if $warehouse.$ed_type|count gt 0}
                                <ul class="cattree tree">
                                {foreach from=$warehouse.$ed_type item=field}
                                    <li><label for="ed_warehouse[{$warehouse.id_warehouse|intval}][{$ed_type|escape:'htmlall':'UTF-8'}][{$field[$type_id]|intval}]"><span>{$field.name|escape:'htmlall':'UTF-8'}</span></label><input type="text" name="ed_warehouse[{$warehouse.id_warehouse|intval}][{$ed_type|escape:'htmlall':'UTF-8'}][{$field[$type_id]|intval}]" value="{if isset($field.picking_days)}{$field.picking_days|escape:'htmlall':'UTF-8'}{/if}"/>
                                    </li>
                                {/foreach}
                                </ul>
                            {else}
                                {l s='There are no suppliers / providers configured. Create some to be able to use this feature' mod='estimateddelivery'}
                            {/if}
                        </div>
                    </div>
                </div>
                {* {$warehouse_picking nofilter} *} {* it's raw HTML *}
            {/foreach}
        </div>
    {/foreach}
    <div style="clear:both"></div>
    <div class="panel-footer" id="toolbar-footer">
        <button class="btn btn-default pull-right" id="submit-add-picking" name="SubmitAddPicking" type="submit"><i class="process-icon-save"></i> <span>{l s='Save' mod='estimateddelivery'}</span></button>
    </div>
    {else} {* There aren't any suppliers or manufacturers configured *}
    <div class="form-group">
        <div class="col-lg-12">
            <p>{l s='To use this section at least you need to configure a Manufacturer or a Supplier in your shop.' mod='estimateddelivery'}</p>
        </div>
    </div>
    {/if}
{if $old_ps == 1}
    </fieldset>
{else}
    </div>
{/if}
<script type="text/javascript">
// Category Stuff
$(document).ready(function() {
    if (typeof cat_delay == 'undefined') {
       var cat_delay = {if isset($cat_delay) && $cat_delay|count > 0}{$cat_delay nofilter}{else}[]{/if}; {* Can't escape it's HTML *}
    }
    // Initialize the delays
    setTimeout(function() { setDelay(); }, 2000);

    $('.tree-folder-name, .tree, #collapse-all-categories-treeviewed_cat_picking, #expand-all-categories-treeviewed_cat_picking').on('click', function() {
        setTimeout(function() { setDelay() },2000);
    });
    function setDelay() {
        if ($('.cat_picking').length > 0) {
            $('.cat_picking').remove();
        }
        for (var i = 0; i < cat_delay.length; i++) {
            //console.log(cat_delay[i]);
            $('#cat_tree_picking input[value="'+cat_delay[i]['id_category']+'"]').parent().find('label').after(' <span class="cat_picking">('+cat_delay[i]['picking_days']+' {l s='days' mod='estimateddelivery'})</span>');
        }
    }

    whDetectMode($('#ED_WAREHOUSES_MODE'));
    // Brand / Manufacturer Selector
    $(document).on('change', '#ED_WAREHOUSES_MODE', function () {
        whDetectMode($(this));
    });

    function whDetectMode(e) {
        $('.wh_detect').hide();
        if (e.val() == 1) {
            // it's supplier
            $('.supplier_wh').show();
            $('.warehouse_supplier_picking').show();
        } else if (e.val() == 2) {
            // it's brand / manu
            $('.manufacturer_wh').show();
            $('.warehouse_manufacturer_picking').show();
        }
    }
});
</script>
