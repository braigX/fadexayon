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

{* Customize product *}
<div role="tabpanel" class="tab-pane" id="customize_product">
    <div class="form-group">
        <div class="alert alert-warning" role="alert">
            {l s='For these configuration options to work, it is necessary to have activated the microdata of the products tab in the configuration section.' mod='adpmicrodatos'}
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-lg-5">
            <span class="label-tooltip" data-toggle="tooltip" title="{l s='Do you want activate o deactivate the microdata description in Product Page?' mod='adpmicrodatos'}" data-placement="bottom">
                {l s='Active Microdata Description' mod='adpmicrodatos'}
            </span>
        </label>
        <div class="col-lg-7">
            <span class="switch prestashop-switch fixed-width-lg">
                <input type="radio" name="active_microdata_page_product_description" id="active_microdata_page_product_description_on" value="1" {if $active_microdata_page_product_description==1}checked="checked"{/if}>
                <label for="active_microdata_page_product_description_on">{l s='Yes' mod='adpmicrodatos'}</label>
                <input type="radio" name="active_microdata_page_product_description" id="active_microdata_page_product_description_off" value="0" {if $active_microdata_page_product_description==0}checked="checked"{/if}>
                <label for="active_microdata_page_product_description_off">{l s='No' mod='adpmicrodatos'}</label>
                <a class="slide-button btn"></a>
            </span>
            <div class="help-block">{l s='Only works on product page' mod='adpmicrodatos'}</div>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-lg-5">
            <span class="label-tooltip" data-toggle="tooltip" data-placement="bottom" title="{l s='Choose the information you want to see in the microdata description on the product page' mod='adpmicrodatos'}">{l s='Product description' mod='adpmicrodatos'}</span>
        </label>
        <div class="col-lg-7">
            <div class="col-md-12">
                <div class="input-group">
                    <label for="set_microdata_description_product_page_on">
                        <input type="radio" name="set_microdata_description_product_page" id="set_microdata_description_product_page_on" value="1" {if $set_microdata_description_product_page==1}checked="checked"{/if}>
                        {l s='Short description' mod='adpmicrodatos'}
                    </label>&nbsp;&nbsp;
                    <label for="set_microdata_description_product_page_off">
                        <input type="radio" name="set_microdata_description_product_page" id="set_microdata_description_product_page_off" value="0" {if $set_microdata_description_product_page==0}checked="checked"{/if}>
                        {l s='Description' mod='adpmicrodatos'}
                    </label>
                </div>
                <div class="help-block">{l s='Only works on product page' mod='adpmicrodatos'}</div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-lg-5">
            <span class="label-tooltip" data-toggle="tooltip" title="{l s='Do you want activate o deactivate the microdata brand in Product Page?' mod='adpmicrodatos'}" data-placement="bottom">
                {l s='Active Microdata Brand' mod='adpmicrodatos'}
            </span>
        </label>
        <div class="col-lg-7">
            <span class="switch prestashop-switch fixed-width-lg">
                <input type="radio" name="active_microdata_page_product_brand" id="active_microdata_page_product_brand_on" value="1" {if $active_microdata_page_product_brand==1}checked="checked"{/if}>
                <label for="active_microdata_page_product_brand_on">{l s='Yes' mod='adpmicrodatos'}</label>
                <input type="radio" name="active_microdata_page_product_brand" id="active_microdata_page_product_brand_off" value="0" {if $active_microdata_page_product_brand==0}checked="checked"{/if}>
                <label for="active_microdata_page_product_brand_off">{l s='No' mod='adpmicrodatos'}</label>
                <a class="slide-button btn"></a>
            </span>
            <div class="help-block">{l s='Only works on product page' mod='adpmicrodatos'}</div>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-lg-5">
            <span class="label-tooltip" data-toggle="tooltip" data-placement="bottom" title="{l s='This field allows you to set a default manufacturer for all products.' mod='adpmicrodatos'}">{l s='Default Manufacturer Name' mod='adpmicrodatos'}</span>
        </label>
        <div class="col-lg-7">
            <div class="col-md-12">
                <div class="input-group">
                    <input name="view_microdata_default_manufacturer" type="text" id="view_microdata_default_manufacturer" value="{$adp_default_manufacturer|escape:'htmlall':'UTF-8'}" class="text form-control">
                </div>
                <div class="help-block">{l s='Only works on product page' mod='adpmicrodatos'}</div>
            </div>
            
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-lg-5">
            <span class="label-tooltip" data-toggle="tooltip" title="{l s='Do you want activate o deactivate the microdata category in Product Page?' mod='adpmicrodatos'}" data-placement="bottom">
                {l s='Active Microdata Category' mod='adpmicrodatos'}
            </span>
        </label>
        <div class="col-lg-7">
            <span class="switch prestashop-switch fixed-width-lg">
                <input type="radio" name="active_microdata_page_product_category" id="active_microdata_page_product_category_on" value="1" {if $active_microdata_page_product_category==1}checked="checked"{/if}>
                <label for="active_microdata_page_product_category_on">{l s='Yes' mod='adpmicrodatos'}</label>
                <input type="radio" name="active_microdata_page_product_category" id="active_microdata_page_product_category_off" value="0" {if $active_microdata_page_product_category==0}checked="checked"{/if}>
                <label for="active_microdata_page_product_category_off">{l s='No' mod='adpmicrodatos'}</label>
                <a class="slide-button btn"></a>
            </span>
            <div class="help-block">{l s='Only works on product page' mod='adpmicrodatos'}</div>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-lg-5">
            <span class="label-tooltip" data-toggle="tooltip" title="{l s='Do you want activate o deactivate the microdata category in Product Page?' mod='adpmicrodatos'}" data-placement="bottom">
                {l s='Mpn = reference' mod='adpmicrodatos'}
            </span>
        </label>
        <div class="col-lg-7">
            <span class="switch prestashop-switch fixed-width-lg">
                <input type="radio" name="active_microdata_mpn_reference_same_value" id="active_microdata_mpn_reference_same_value_on" value="1" {if $active_microdata_mpn_reference_same_value==1}checked="checked"{/if}>
                <label for="active_microdata_mpn_reference_same_value_on">{l s='Yes' mod='adpmicrodatos'}</label>
                <input type="radio" name="active_microdata_mpn_reference_same_value" id="active_microdata_mpn_reference_same_value_off" value="0" {if $active_microdata_mpn_reference_same_value==0}checked="checked"{/if}>
                <label for="active_microdata_mpn_reference_same_value_off">{l s='No' mod='adpmicrodatos'}</label>
                <a class="slide-button btn"></a>
            </span>
            <div class="help-block">{l s='The reference and the mpn have the same value' mod='adpmicrodatos'}</div>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-lg-5">
            <span class="label-tooltip" data-toggle="tooltip" data-placement="bottom" title="{l s='Configure how the GTIN is displayed' mod='adpmicrodatos'}">{l s='GTIN' mod='adpmicrodatos'}</span>
        </label>
        <div class="col-lg-7">
            <div class="col-md-12">
                <div class="input-group">
                    <label for="set_configuration_product_taxes0">
                        <input type="radio" name="set_configuration_product_gtin" id="set_configuration_product_gtin0" value="0" {if $set_configuration_product_gtin==0}checked="checked"{/if}>
                        {l s='Desactive' mod='adpmicrodatos'}
                    </label>&nbsp;&nbsp;
                    <label for="set_configuration_product_gtin1">
                        <input type="radio" name="set_configuration_product_gtin" id="set_configuration_product_gtin1" value="1" {if $set_configuration_product_gtin==1}checked="checked"{/if}>
                        {l s='EAN-13' mod='adpmicrodatos'}
                    </label>&nbsp;&nbsp;
                    <label for="set_configuration_product_gtin2">
                        <input type="radio" name="set_configuration_product_gtin" id="set_configuration_product_gtin2" value="2" {if $set_configuration_product_gtin==2}checked="checked"{/if}>
                        {l s='UPC' mod='adpmicrodatos'}
                    </label>&nbsp;&nbsp;
                    <label for="set_configuration_product_gtin3">
                        <input type="radio" name="set_configuration_product_gtin" id="set_configuration_product_gtin3" value="3" {if $set_configuration_product_gtin==3}checked="checked"{/if}>
                        {l s='ISBN' mod='adpmicrodatos'}
                    </label>
                </div>
            </div> 
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-lg-5">
            <span class="label-tooltip" data-toggle="tooltip" data-placement="bottom" title="{l s='Choose whether you want to view all images of a product or just the front cover' mod='adpmicrodatos'}">{l s='Number of images to display' mod='adpmicrodatos'}</span>
        </label>
        <div class="col-lg-7">
            <div class="col-md-12">
                <div class="input-group">
                    <label for="set_image_product_on">
                        <input type="radio" name="set_image_product" id="set_image_product_on" value="1" {if $set_image_product==1}checked="checked"{/if}>
                        {l s='Only the cover image' mod='adpmicrodatos'}
                    </label>&nbsp;&nbsp;
                    <label for="set_image_product_off">
                        <input type="radio" name="set_image_product" id="set_image_product_off" value="0" {if $set_image_product==0}checked="checked"{/if}>
                        {l s='All images' mod='adpmicrodatos'}
                    </label>
                </div>
            </div>
            
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-lg-5">
            <span class="label-tooltip" data-toggle="tooltip" data-placement="bottom" title="{l s='Configure how you want to display product taxes' mod='adpmicrodatos'}">{l s='Product with taxes' mod='adpmicrodatos'}</span>
        </label>
        <div class="col-lg-7">
            <div class="col-md-12">
                <div class="input-group">
                    <label for="set_configuration_product_taxes0">
                        <input type="radio" name="set_configuration_product_taxes" id="set_configuration_product_taxes0" value="0" {if $set_configuration_product_taxes==0}checked="checked"{/if}>
                        {l s='Default prestashop configuration' mod='adpmicrodatos'}
                    </label>&nbsp;&nbsp;
                    <label for="set_configuration_product_taxes1">
                        <input type="radio" name="set_configuration_product_taxes" id="set_configuration_product_taxes1" value="1" {if $set_configuration_product_taxes==1}checked="checked"{/if}>
                        {l s='Taxes not included' mod='adpmicrodatos'}
                    </label>&nbsp;&nbsp;
                    <label for="set_configuration_product_taxes2">
                        <input type="radio" name="set_configuration_product_taxes" id="set_configuration_product_taxes2" value="2" {if $set_configuration_product_taxes==2}checked="checked"{/if}>
                        {l s='Taxes included' mod='adpmicrodatos'}
                    </label>
                </div>
                <div class="help-block">{l s='The default option gets the information from the prestashop configuration.' mod='adpmicrodatos'}</div>
            </div>
            
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-lg-5">
            <span class="label-tooltip" data-toggle="tooltip" title="{l s='Do you want to display the weight of the product on the product pages?' mod='adpmicrodatos'}" data-placement="bottom">
                {l s='Product weight' mod='adpmicrodatos'}
            </span>
        </label>
        <div class="col-lg-7">
            <span class="switch prestashop-switch fixed-width-lg">
                <input type="radio" name="active_microdata_product_weight" id="active_microdata_product_weight_on" value="1" {if $active_microdata_product_weight==1}checked="checked"{/if}>
                <label for="active_microdata_product_weight_on">{l s='Yes' mod='adpmicrodatos'}</label>
                <input type="radio" name="active_microdata_product_weight" id="active_microdata_product_weight_off" value="0" {if $active_microdata_product_weight==0}checked="checked"{/if}>
                <label for="active_microdata_product_weight_off">{l s='No' mod='adpmicrodatos'}</label>
                <a class="slide-button btn"></a>
            </span>
            <div class="help-block">{l s='Only works on product page' mod='adpmicrodatos'}</div>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-lg-5">
            <span class="label-tooltip" data-toggle="tooltip" title="{l s='You wish to disable the stock in your product sheets?' mod='adpmicrodatos'}" data-placement="bottom">
                {l s='Disable stock' mod='adpmicrodatos'}
            </span>
        </label>
        <div class="col-lg-7">
            <span class="switch prestashop-switch fixed-width-lg">
                <input type="radio" name="desactive_microdata_product_stock" id="desactive_microdata_product_stock_on" value="1" {if $desactive_microdata_product_stock==1}checked="checked"{/if}>
                <label for="desactive_microdata_product_stock_on">{l s='Yes' mod='adpmicrodatos'}</label>
                <input type="radio" name="desactive_microdata_product_stock" id="desactive_microdata_product_stock_off" value="0" {if $desactive_microdata_product_stock==0}checked="checked"{/if}>
                <label for="desactive_microdata_product_stock_off">{l s='No' mod='adpmicrodatos'}</label>
                <a class="slide-button btn"></a>
            </span>
            <div class="help-block">{l s='Only works on product page' mod='adpmicrodatos'}</div>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-lg-5">
            <span class="label-tooltip" data-toggle="tooltip" title="{l s='You wish to disable the price in your product sheets?' mod='adpmicrodatos'}" data-placement="bottom">
                {l s='Disable price' mod='adpmicrodatos'}
            </span>
        </label>
        <div class="col-lg-7">
            <span class="switch prestashop-switch fixed-width-lg">
                <input type="radio" name="desactive_microdata_product_price" id="desactive_microdata_product_price_on" value="1" {if $desactive_microdata_product_price==1}checked="checked"{/if}>
                <label for="desactive_microdata_product_price_on">{l s='Yes' mod='adpmicrodatos'}</label>
                <input type="radio" name="desactive_microdata_product_price" id="desactive_microdata_product_price_off" value="0" {if $desactive_microdata_product_price==0}checked="checked"{/if}>
                <label for="desactive_microdata_product_price_off">{l s='No' mod='adpmicrodatos'}</label>
                <a class="slide-button btn"></a>
            </span>
            <div class="help-block">{l s='Only works on product page' mod='adpmicrodatos'}</div>
        </div>
    </div>
     <div class="form-group">
        <label class="control-label col-lg-5">
            <span class="label-tooltip" data-toggle="tooltip" title="{l s='3D Model structured data' mod='adpmicrodatos'}" data-placement="bottom">{l s='3D Model' mod='adpmicrodatos'}</span>
        </label>
        <div class="col-lg-7">
            <div class="col-md-12">
                <div class="input-group">
                    <input name="id_feature_3d_model" type="text" id="id_feature_3d_model" value="{$id_feature_3d_model|escape:'htmlall':'UTF-8'}" class="text form-control">
                </div>
                <div class="help-block">{l s='Indicate the id of the feature you want to map in order to display the 3D Model url in the product sheets.' mod='adpmicrodatos'}</div> 
            </div>
        </div>
    </div>
</div>