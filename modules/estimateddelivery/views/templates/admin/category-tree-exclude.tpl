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
<fieldset id="ed_cat_exclude">
    <legend><img src="{$uri|escape:'htmlall':'UTF-8'}/img/cogs.gif" alt="{l s='Category & Product Exclusion' mod='estimateddelivery'}" />2.4 {l s='Category & Product Exclusion' mod='estimateddelivery'}</legend>
{else}
<div class="panel" id="ed_cat_exclude">
    <div class="panel-heading" data-position="7"><i class="icon-cogs"></i> 2.4 {l s='Category & Product Exclusion' mod='estimateddelivery'}</div>
{/if}
    <div class="form-group">
        <label class="control-label col-lg-3">{l s='Product Exclusion:' mod='estimateddelivery'}</label>
        <div class="col-lg-9">
            <input type="text" name="ed_prod_dis" id="ed_prod_dis" value="{$ed_prod_dis|escape:'htmlall':'UTF-8'}">
            <p>{l s='Enter a comma separated list of product IDs to exclude' mod='estimateddelivery'}.</p>
            <p>{l s='This list will sync automatically with the option to exclude the product from the Estimated Delivery inside the Product Edit page' mod='estimateddelivery'}</p>
        </div>
    </div>
    {if $ajax_replace}
    <div class="ajax-replace"
        data-id="{$ed_main_id|escape:'htmlall':'UTF-8'}"
        data-input-name="{$ed_input_name|escape:'htmlall':'UTF-8'}"
        data-selected-cat="{$ed_selected_cat|escape:'htmlall':'UTF-8'}"
        data-token="{$ed_token|escape:'htmlall':'UTF-8'}"
        data-callback-fn="setExcludedCategories();">
        <i class="icon icon-spinner icon-pulse icon-4x icon-fw"></i>
        <span class="sr-only">{l s='loading...' mod='estimateddelivery'}</span>
        <!-- Will be replaced by ajax content -->
    </div>
    {else}
        <div class="form-group">
            <label class="control-label col-lg-3">{l s='Quick Guide:' mod='estimateddelivery'}</label>
            <div class="col-lg-9">
               <p>{l s='Mark the categories you want to exclude' mod='estimateddelivery'}.</p>
                <p>{l s='In this section you can disable the ED for the products inside the categories. This feature will always be applied by checking the product\'s main category' mod='estimateddelivery'}</p>
            </div>
        </div>
        <div class="form-group">

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
        <div class="form-group">
            <label class="control-label col-lg-3">{l s='Choose shop association:' mod='estimateddelivery'}</label>
            <div class="col-lg-9">{$asso_shops nofilter}{* HTML here, no escaping to prevent issues *}</div>
        </div>
        {/if}
        <div style="clear:both"></div>
        <div class="panel-footer" id="toolbar-footer">
            <button class="btn btn-default pull-right" id="submit-cat-exclude" name="SubmitCatExclude" type="submit"><i class="process-icon-save"></i> <span>{l s='Save' mod='estimateddelivery'}</span></button>
        </div>
    {/if}
    {* </form> *}
{if $old_ps == 1}
    </fieldset>
{else}
    </div>
{/if}
<script type="text/javascript">
    // Category Stuff
    $(document).ready(function() {
        setExcludedCategories();
    });
    function setExcludedCategories() {
        {if isset($excludedCategories)}
        var excludedCategories = new Array({$excludedCategories|escape:'htmlall':'UTF-8'});
        setTimeout( function() {
            if ($("#ed_cat_exclude input:checked").length == 0) {
                $("#ed_cat_exclude input").each(function() {
                    // parseInt was needed, otherwise indexOf returns always -1
                    if (excludedCategories.indexOf(parseInt($(this).val())) != -1) {
                        $(this).prop("checked", true);
                        $(this).parent().addClass("tree-selected");
                        $(this).parents('ul.tree').each(function(){
                            $(this).show();
                            $(this).prev().find('.icon-folder-close').removeClass('icon-folder-close').addClass('icon-folder-open');
                        });
                    }
                });
            }
        }, 800);
        {/if}
    }
</script>
