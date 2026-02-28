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

{* Advanced *}
<div role="tabpanel" class="tab-pane" id="advanced">
    <div class="form-group">
        <label class="control-label col-lg-5">
            <span class="label-tooltip" data-toggle="tooltip" title="{l s='Choose the image type that you want to use as product image' mod='adpmicrodatos'}" data-placement="bottom">
                {l s='Product image type' mod='adpmicrodatos'}
            </span>
        </label>
        <div class="col-lg-7">
            <select name="product_image_type">
                {foreach from=$image_types['product']['types'] item='product_image_type'}
                    <option value="{$product_image_type|escape:'htmlall':'UTF-8'}" {if $image_types['product']['selected'] == $product_image_type}selected{/if}>{$product_image_type|escape:'htmlall':'UTF-8'}</option>
                {/foreach}
            </select>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-5">
            <span class="label-tooltip" data-toggle="tooltip" title="{l s='Choose the image type that you want to use as categoty image' mod='adpmicrodatos'}" data-placement="bottom">
                {l s='Category image type' mod='adpmicrodatos'}
            </span>
        </label>
        <div class="col-lg-7">
            <select name="category_image_type">
                {foreach from=$image_types['category']['types'] item='category_image_type'}
                    <option value="{$category_image_type|escape:'htmlall':'UTF-8'}" {if $image_types['category']['selected'] == $category_image_type}selected{/if}>{$category_image_type|escape:'htmlall':'UTF-8'}</option>
                {/foreach}
            </select>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-5">
            <span class="label-tooltip" data-toggle="tooltip" title="{l s='Choose the image type that you want to use as manufacturer image' mod='adpmicrodatos'}" data-placement="bottom">
                {l s='Manufacturer image type' mod='adpmicrodatos'}
            </span>
        </label>
        <div class="col-lg-7">
            <select name="manufacturer_image_type">
                {foreach from=$image_types['manufacturer']['types'] item='manufacturer_image_type'}
                    <option value="{$manufacturer_image_type|escape:'htmlall':'UTF-8'}" {if $image_types['manufacturer']['selected'] == $manufacturer_image_type}selected{/if}>{$manufacturer_image_type|escape:'htmlall':'UTF-8'}</option>
                {/foreach}
            </select>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-5">
            <span class="label-tooltip" data-toggle="tooltip" title="{l s='Indicate which pages (separated by comma) you do not want the microdata to be displayed' mod='adpmicrodatos'}" data-placement="bottom">
                {l s='Pages without microdata' mod='adpmicrodatos'}
            </span>
        </label>
        <div class="col-lg-7">
            <div class="input-group col-lg-12">
                <textarea name="adp_pages_without_microdata" id="adp_pages_without_microdata" class="textarea-autosize" style="overflow-wrap: break-word; resize: none; height: 150px;">{$adp_pages_without_microdata|escape:'htmlall':'UTF-8'}</textarea>
            </div>
            <div class="help-block">
                {l s='We recommend not modifying these parameters' mod='adpmicrodatos'}:
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-5">
            <span class="label-tooltip" data-toggle="tooltip" title="{l s='Indicate the ids of the products (separated by comma) you do not want to display the microdata' mod='adpmicrodatos'}" data-placement="bottom">
                {l s='Ids products without microdata' mod='adpmicrodatos'}
            </span>
        </label>
        <div class="col-lg-7">
            <div class="input-group col-lg-12">
                <textarea name="adp_ids_product_without_microdata" id="adp_ids_product_without_microdata" class="textarea-autosize" style="overflow-wrap: break-word; resize: none; height: 150px;">{$adp_ids_product_without_microdata|escape:'htmlall':'UTF-8'}</textarea>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-5">
            <span class="label-tooltip" data-toggle="tooltip" title="{l s='Indicate the ids of the manufacturers (separated by comma) you do not want to display the microdata' mod='adpmicrodatos'}" data-placement="bottom">
                {l s='Ids manufacturers without microdata' mod='adpmicrodatos'}
            </span>
        </label>
        <div class="col-lg-7">
            <div class="input-group col-lg-12">
                <textarea name="adp_ids_manufacturers_without_microdata" id="adp_ids_manufacturers_without_microdata" class="textarea-autosize" style="overflow-wrap: break-word; resize: none; height: 150px;">{$adp_ids_manufacturers_without_microdata|escape:'htmlall':'UTF-8'}</textarea>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-5">
            <span class="label-tooltip" data-toggle="tooltip" title="{l s='Indicate the ids of the categories (separated by comma) you do not want to display the microdata' mod='adpmicrodatos'}" data-placement="bottom">
                {l s='Ids categories without microdata' mod='adpmicrodatos'}
            </span>
        </label>
        <div class="col-lg-7">
            <div class="input-group col-lg-12">
                <textarea name="adp_ids_categories_without_microdata" id="adp_ids_categories_without_microdata" class="textarea-autosize" style="overflow-wrap: break-word; resize: none; height: 150px;">{$adp_ids_categories_without_microdata|escape:'htmlall':'UTF-8'}</textarea>
            </div>
        </div>
    </div>
</div>