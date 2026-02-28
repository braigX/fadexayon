{*
* 2007-2023 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    Ádalop <contact@prestashop.com>
*  @copyright 2023 Ádalop
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}

{* Product *}
<div role="tabpanel" class="tab-pane" id="product">
    <div class="form-group">
        <label class="control-label col-lg-5">
            <span class="label-tooltip" data-toggle="tooltip" data-placement="bottom" title="{l s='Choose between displaying combinations on a single page or on multiple pages' mod='adpmicrodatos'}">{l s='Displaying combinations on a single page or on multiple pages' mod='adpmicrodatos'}</span>
        </label>
        <div class="col-lg-7">
            <div class="col-md-12">
                <div class="input-group">
                    <label for="set_microdata_type_combinations_product_on">
                        <input type="radio" name="set_microdata_type_combinations_product" id="set_microdata_type_combinations_product_on" value="1" {if $set_microdata_type_combinations_product==1}checked="checked"{/if}>
                        {l s='Multiple pages (Usual operation with inProductGroupWithID)' mod='adpmicrodatos'}
                    </label>&nbsp;&nbsp;
                    <label for="set_microdata_type_combinations_product_off">
                        <input type="radio" name="set_microdata_type_combinations_product" id="set_microdata_type_combinations_product_off" value="0" {if $set_microdata_type_combinations_product==0}checked="checked"{/if}>
                        {l s='Single page (with ProductGroup)' mod='adpmicrodatos'}
                    </label>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-5">
            <span class="label-tooltip" data-toggle="tooltip" data-placement="bottom" title="{l s='This field allows you to define the product id' mod='adpmicrodatos'}">{l s='Simple product identifier (no combinations)' mod='adpmicrodatos'}</span>
        </label>
        <div class="col-lg-7">
            <div class="col-md-12">
                <div class="input-group">
                    <input name="set_microdata_id_product" type="text" id="set_microdata_id_product" value="{$set_microdata_id_product|escape:'htmlall':'UTF-8'}" class="text form-control">
                </div>
                <div class="help-block">{l s='Here are the values you can use to customise this field: {id_product}, {reference_product}, {ean13_product}' mod='adpmicrodatos'}</div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-5">
            <span class="label-tooltip" data-toggle="tooltip" data-placement="bottom" title="{l s='This field allows you to define the product id combination' mod='adpmicrodatos'}">{l s='Product combination identifier' mod='adpmicrodatos'}</span>
        </label>
        <div class="col-lg-7">
            <div class="col-md-12">
                <div class="input-group">
                    <input name="set_microdata_id_product_combination" type="text" id="set_microdata_id_product_combination" value="{$set_microdata_id_product_combination|escape:'htmlall':'UTF-8'}" class="text form-control">
                </div>
                <div class="help-block">{l s='Here are the values you can use to customise this field: {id_product}, {id_product_combination}, {reference_product_combination}, {ean13_product_combination}' mod='adpmicrodatos'}</div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-5">
            <span class="label-tooltip" data-toggle="tooltip" data-placement="bottom" title="{l s='Only works on the Product Page. It will show the microdata of the X related products of the product being displayed.' mod='adpmicrodatos'}">{l s='Number of related products in microdata' mod='adpmicrodatos'}</span>
        </label>
        <div class="col-lg-7">
            <div class="col-md-12">
                <div class="input-group">
                    <input name="view_microdata_products_related" type="text" id="view_microdata_products_related" value="{$adp_num_products_related|escape:'htmlall':'UTF-8'}" class="text form-control">
                </div>
                <div class="help-block">{l s='Set to zero if we want to deactivate the microdata' mod='adpmicrodatos'}</div> 
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-5">
            <span class="label-tooltip" data-toggle="tooltip" title="{l s='Sets the number of days, from the current date, for the microdata date valid until' mod='adpmicrodatos'}" data-placement="bottom">
                {l s='Number of days for microdata "date valid until"' mod='adpmicrodatos'}
            </span>
        </label>
        <div class="col-lg-7">
            <span class="input-group">
                <span class="input-group-addon">{l s='Days' mod='adpmicrodatos'}</span>
                <input maxlength="5" name="num_days_date_valid_until_product" id="num_days_date_valid_until_product" type="text" value="{$num_days_date_valid_until_product|escape:'htmlall':'UTF-8'}">
            </span>
            <div class="help-block">{l s='Google recommends that you don\'t automatically fill in this field. Remember that what is displayed through Google search console is a recommendation, not an error for having this field empty.' mod='adpmicrodatos'}</div>     
        </div>

    </div>

    <div class="form-group">
        <label class="control-label col-lg-5">
            <span class="label-tooltip" data-toggle="tooltip" title="{l s='Do you want to activate or deactivate the microdata of the product features on the product page?' mod='adpmicrodatos'}" data-placement="bottom">
                {l s='Activate microdata of product features' mod='adpmicrodatos'}
            </span>
        </label>
        <div class="col-lg-7">
            <span class="switch prestashop-switch fixed-width-lg">
                <input type="radio" name="active_microdata_features_product" id="active_microdata_features_product_on" value="1" {if $active_microdata_features_product==1}checked="checked"{/if}>
                <label for="active_microdata_features_product_on">{l s='Yes' mod='adpmicrodatos'}</label>
                <input type="radio" name="active_microdata_features_product" id="active_microdata_features_product_off" value="0" {if $active_microdata_features_product==0}checked="checked"{/if}>
                <label for="active_microdata_features_product_off">{l s='No' mod='adpmicrodatos'}</label>
                <a class="slide-button btn"></a>
            </span>
            <div class="help-block">{l s='If you don\'t use features product, we recommend deactivating it' mod='adpmicrodatos'}</div> 
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-lg-5">
            <span class="label-tooltip" data-toggle="tooltip" data-placement="bottom">{l s='Indicate the ids of the features you do not want to display on the product pages.' mod='adpmicrodatos'}</span>
        </label>
        <div class="col-lg-7">
            <div class="col-md-12">
                <div class="input-group">
                    <input name="ids_disable_microdata_features_product" type="text" id="ids_disable_microdata_features_product" value="{$ids_disable_microdata_features_product|escape:'htmlall':'UTF-8'}" class="text form-control">
                </div>
                <div class="help-block">{l s='You can indicate several characteristics by separating them with a comma. Ex: 1,2,3' mod='adpmicrodatos'}</div> 
            </div>
        </div>
    </div>
</div>