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

{* OpenGraph *}
<div role="tabpanel" class="tab-pane" id="opengraph">

    <div class="form-group">
        <div class="alert alert-warning" role="alert">
            {l s='The Open Graph development for this module will not receive any more updates. In case you need a more up to date and advanced Open Graph, which covers all your needs, we recommend our other module:' mod='adpmicrodatos'} <a target="_blank" href="https://addons.prestashop.com/{$iso_code_language}/product.php?id_product=47564" class="module_href"><img class="logo_module" src="{$module_dir|escape:'htmlall':'UTF-8'}/views/img/logo_adpopengraph.png"></a>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-3">
            <span class="label-tooltip" data-toggle="tooltip" title="" data-original-title="{l s='Do you want activate o deactivate the pixel of Facebook?' mod='adpmicrodatos'}">
                {l s='Active Pixel Facebook' mod='adpmicrodatos'}
            </span>
        </label>
        <div class="col-lg-9">
            <span class="switch prestashop-switch fixed-width-lg">
                <input type="radio" name="active_pixel_facebook" id="active_pixel_facebook_on" value="1" {if $active_pixel_facebook==1}checked="checked"{/if}>
                <label for="active_pixel_facebook_on">{l s='Yes' mod='adpmicrodatos'}</label>
                <input type="radio" name="active_pixel_facebook" id="active_pixel_facebook_off" value="0" {if $active_pixel_facebook==0}checked="checked"{/if}>
                <label for="active_pixel_facebook_off">{l s='No' mod='adpmicrodatos'}</label>
                <a class="slide-button btn"></a>
            </span>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-3">
            <span class="label-tooltip" data-toggle="tooltip" title="">
                {l s='ID Pixel Facebook' mod='adpmicrodatos'}
            </span>
        </label>
        <div class="col-lg-9">
            <div class="col-md-6">
                <div class="input-group">
                    <input name="facebook_admin_id" type="text" id="facebook_admin_id" value="{$facebook_admin_id}" class="text form-control">
                </div>
                <div class="help-block"><b>{l s='(Optional)' mod='adpmicrodatos'}</b> {l s='ID Pixel Facebook' mod='adpmicrodatos'}</div>
            </div>
        </div>
    </div>

    <div class="form-group row">
        <label class="control-label col-lg-3">
            <span class="label-tooltip" data-toggle="tooltip" data-placement="bottom" title="{l s='Customize the image in Open Graph that you want to show from the home page. By default the logo image will be displayed.' mod='adpmicrodatos'}">{l s='Image Url Home - Open graph' mod='adpmicrodatos'}</span>
        </label>
        <div class="col-lg-9">
            <div class="col-md-6">
                <div class="input-group">
                    <input name="view_microdata_image_home_og" type="text" id="view_microdata_image_home_og" value="{$adp_url_img_home_og}" class="text form-control">
                </div>
                <div class="help-block"><b>{l s='(Optional)' mod='adpmicrodatos'}</b> {l s='Minimum resolution of 200X200' mod='adpmicrodatos'}</div>
            </div>
        </div>
    </div>

    <div class="form-group row">
        <label class="control-label col-lg-3">
            <span class="label-tooltip" data-toggle="tooltip" data-placement="bottom" title="{l s='Customize the image in Open Graph that you want to show from the category page. By default the category image will be displayed.' mod='adpmicrodatos'}">{l s='Image Url Category - Open graph' mod='adpmicrodatos'}</span>
        </label>
        <div class="col-lg-9">
            <div class="col-md-6">
                <div class="input-group">
                    <input name="view_microdata_image_category_og" type="text" id="view_microdata_image_category_og" value="{$adp_url_img_category_og}" class="text form-control">
                </div>
                <div class="help-block"><b>{l s='(Optional)' mod='adpmicrodatos'}</b> {l s='Minimum resolution of 200X200' mod='adpmicrodatos'}</div>
            </div>
        </div>
    </div>

    <div class="form-group row">
        <label class="control-label col-lg-3">
            <span class="label-tooltip" data-toggle="tooltip" data-placement="bottom" title="{l s='Customize the image in Open Graph that you want to show from the manufacturer page. By default the manufacturer image will be displayed.' mod='adpmicrodatos'}">{l s='Image Url Manufacturer - Open graph' mod='adpmicrodatos'}</span>
        </label>
        <div class="col-lg-9">
            <div class="col-md-6">
                <div class="input-group">
                    <input name="view_microdata_image_manufacturer_og" type="text" id="view_microdata_image_manufacturer_og" value="{$adp_url_img_manufacturer_og}" class="text form-control">
                </div>
                <div class="help-block"><b>{l s='(Optional)' mod='adpmicrodatos'}</b> {l s='Minimum resolution of 200X200' mod='adpmicrodatos'}</div>
            </div>
        </div>
    </div>
</div>
