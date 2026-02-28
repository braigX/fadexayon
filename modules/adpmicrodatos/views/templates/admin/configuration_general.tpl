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

{* General *}
<div role="tabpanel" class="tab-pane active" id="general">

    <div class="form-group">
        <label class="control-label col-lg-5">
            <span class="label-tooltip" data-toggle="tooltip" title="{l s='Do you want activate o deactivate the microdata Organization?' mod='adpmicrodatos'}" data-placement="bottom">
                {l s='Active Microdata Organization/Localbusiness' mod='adpmicrodatos'}
            </span>
        </label>
        <div class="col-lg-7">
            <span class="switch prestashop-switch fixed-width-lg">
                <input type="radio" name="active_microdata_organization" id="active_microdata_organization_on" value="1" {if $active_microdata_organization==1}checked="checked"{/if}>
                <label for="active_microdata_organization_on">{l s='Yes' mod='adpmicrodatos'}</label>
                <input type="radio" name="active_microdata_organization" id="active_microdata_organization_off" value="0" {if $active_microdata_organization==0}checked="checked"{/if}>
                <label for="active_microdata_organization_off">{l s='No' mod='adpmicrodatos'}</label>
                <a class="slide-button btn"></a>
            </span>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-5">
            <span class="label-tooltip" data-toggle="tooltip" data-placement="bottom" title="{l s='Choose to activate the microdata Organization or Localbusiness' mod='adpmicrodatos'}">{l s='Choose between Organization or Localbusiness' mod='adpmicrodatos'}</span>
        </label>
        <div class="col-lg-7">
            <div class="col-md-12">
                <div class="input-group">
                    <label for="set_microdata_organization_on">
                        <input type="radio" name="set_microdata_organization" id="set_microdata_organization_on" value="1" {if $set_microdata_organization==1}checked="checked"{/if}>
                        {l s='Organization' mod='adpmicrodatos'}
                    </label>&nbsp;&nbsp;
                    <label for="set_microdata_organization_off">
                        <input type="radio" name="set_microdata_organization" id="set_microdata_organization_off" value="0" {if $set_microdata_organization==0}checked="checked"{/if}>
                        {l s='LocalBusiness' mod='adpmicrodatos'}
                    </label>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-5">
            <span class="label-tooltip" data-toggle="tooltip" title="{l s='Do you want activate o deactivate the microdata in Breadcrumbs?' mod='adpmicrodatos'}" data-placement="bottom">
                {l s='Active Microdata Breadcrumbs' mod='adpmicrodatos'}
            </span>
        </label>
        <div class="col-lg-7">
            <span class="switch prestashop-switch fixed-width-lg">
                <input data-toggle="disable-tab" href="#customize_breadcrumb" type="radio" name="active_microdata_breadcrumbs" id="active_microdata_breadcrumbs_on" value="1" {if $active_microdata_breadcrumbs==1}checked="checked"{/if}>
                <label for="active_microdata_breadcrumbs_on">{l s='Yes' mod='adpmicrodatos'}</label>
                <input data-toggle="disable-tab" href="#customize_breadcrumb" type="radio" name="active_microdata_breadcrumbs" id="active_microdata_breadcrumbs_off" value="0" {if $active_microdata_breadcrumbs==0}checked="checked"{/if}>
                <label for="active_microdata_breadcrumbs_off">{l s='No' mod='adpmicrodatos'}</label>
                <a class="slide-button btn"></a>
            </span>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-5">
            <span class="label-tooltip" data-toggle="tooltip" title="{l s='Do you want activate o deactivate the microdata WebPage?' mod='adpmicrodatos'}" data-placement="bottom">
                {l s='Active Microdata WebPage' mod='adpmicrodatos'}
            </span>
        </label>
        <div class="col-lg-7">
            <span class="switch prestashop-switch fixed-width-lg">
                <input type="radio" name="active_microdata_webpage" id="active_microdata_webpage_on" value="1" {if $active_microdata_webpage==1}checked="checked"{/if}>
                <label for="active_microdata_webpage_on">{l s='Yes' mod='adpmicrodatos'}</label>
                <input type="radio" name="active_microdata_webpage" id="active_microdata_webpage_off" value="0" {if $active_microdata_webpage==0}checked="checked"{/if}>
                <label for="active_microdata_webpage_off">{l s='No' mod='adpmicrodatos'}</label>
                <a class="slide-button btn"></a>
            </span>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-5">
            <span class="label-tooltip" data-toggle="tooltip" title="{l s='Do you want activate o deactivate the microdata WebSite?' mod='adpmicrodatos'}" data-placement="bottom">
                {l s='Active Microdata WebSite' mod='adpmicrodatos'}
            </span>
        </label>
        <div class="col-lg-7">
            <span class="switch prestashop-switch fixed-width-lg">
                <input type="radio" name="active_microdata_website" id="active_microdata_website_on" value="1" {if $active_microdata_website==1}checked="checked"{/if}>
                <label for="active_microdata_website_on">{l s='Yes' mod='adpmicrodatos'}</label>
                <input type="radio" name="active_microdata_website" id="active_microdata_website_off" value="0" {if $active_microdata_website==0}checked="checked"{/if}>
                <label for="active_microdata_website_off">{l s='No' mod='adpmicrodatos'}</label>
                <a class="slide-button btn"></a>
            </span>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-5">
            <span class="label-tooltip" data-toggle="tooltip" title="{l s='Do you want activate o deactivate the microdata Store?' mod='adpmicrodatos'}" data-placement="bottom">
                {l s='Active Microdata Store' mod='adpmicrodatos'}
            </span>
        </label>
        <div class="col-lg-7">
            <span class="switch prestashop-switch fixed-width-lg">
                <input type="radio" name="active_microdata_store" id="active_microdata_store_on" value="1" {if $active_microdata_store==1}checked="checked"{/if}>
                <label for="active_microdata_store_on">{l s='Yes' mod='adpmicrodatos'}</label>
                <input type="radio" name="active_microdata_store" id="active_microdata_store_off" value="0" {if $active_microdata_store==0}checked="checked"{/if}>
                <label for="active_microdata_store_off">{l s='No' mod='adpmicrodatos'}</label>
                <a class="slide-button btn"></a>
            </span>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-5">
            <span class="label-tooltip" data-toggle="tooltip" data-placement="bottom" title="{l s='Choose to activate the microdata Store or Localbusiness' mod='adpmicrodatos'}">{l s='Choose between Store or Localbusiness' mod='adpmicrodatos'}</span>
        </label>
        <div class="col-lg-7">
            <div class="col-md-12">
                <div class="input-group">
                    <label for="set_microdata_store_on">
                        <input type="radio" name="set_microdata_store" id="set_microdata_store_on" value="1" {if $set_microdata_store==1}checked="checked"{/if}>
                         {l s='Store' mod='adpmicrodatos'}
                    </label>&nbsp;&nbsp;
                    <label for="set_microdata_store_off">
                        <input type="radio" name="set_microdata_store" id="set_microdata_store_off" value="0" {if $set_microdata_store==0}checked="checked"{/if}>
                        {l s='LocalBusiness' mod='adpmicrodatos'}
                    </label>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-5">
            <span class="label-tooltip" data-toggle="tooltip" data-placement="bottom" title="{l s='Microdato Store - Hourly separation character' mod='adpmicrodatos'}">
                {l s='Microdato Store - Hourly separation character' mod='adpmicrodatos'}
            </span>
        </label>
        <div class="col-lg-7">
            <div class="col-md-12">
                <div class="input-group">
                    <input name="set_microdata_store_character_separation_hours" type="text" id="set_microdata_store_character_separation_hours" value="{$set_microdata_store_character_separation_hours|escape:'htmlall':'UTF-8'}" class="text form-control">
                </div>
                <div class="help-block">{l s='Separation character used between opening and closing hours (Example 09:00 - 14:00, separation character \'-\')' mod='adpmicrodatos'}</div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-5">
            <span class="label-tooltip" data-toggle="tooltip" title="{l s='Do you want activate o deactivate the microdata in Page Product?' mod='adpmicrodatos'}" data-placement="bottom">
                {l s='Active Microdata Page Product' mod='adpmicrodatos'}
            </span>
        </label>
        <div class="col-lg-7">
            <span class="switch prestashop-switch fixed-width-lg">
                <input data-toggle="disable-tab" href="#product" type="radio" name="active_microdata_page_product" id="active_microdata_page_product_on" value="1" {if $active_microdata_page_product==1}checked="checked"{/if}>
                <label for="active_microdata_page_product_on">{l s='Yes' mod='adpmicrodatos'}</label>
                <input data-toggle="disable-tab" href="#product" type="radio" name="active_microdata_page_product" id="active_microdata_page_product_off" value="0" {if $active_microdata_page_product==0}checked="checked"{/if}>
                <label for="active_microdata_page_product_off">{l s='No' mod='adpmicrodatos'}</label>
                <a class="slide-button btn"></a>
            </span>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-5">
            <span class="label-tooltip" data-toggle="tooltip" title="{l s='Do you want activate o deactivate the microdata in List Product?' mod='adpmicrodatos'}" data-placement="bottom">
                {l s='Active Microdata List Product' mod='adpmicrodatos'}
            </span>
        </label>
        <div class="col-lg-7">
            <span class="switch prestashop-switch fixed-width-lg">
                <input type="radio" name="active_microdata_list_product" id="active_microdata_list_product_on" value="1" {if $active_microdata_list_product==1}checked="checked"{/if}>
                <label for="active_microdata_list_product_on">{l s='Yes' mod='adpmicrodatos'}</label>
                <input type="radio" name="active_microdata_list_product" id="active_microdata_list_product_off" value="0" {if $active_microdata_list_product==0}checked="checked"{/if}>
                <label for="active_microdata_list_product_off">{l s='No' mod='adpmicrodatos'}</label>
                <a class="slide-button btn"></a>
            </span>
            <div class="help-block">{l s='Only compatible with paginated listings. Does not work with infinite scroll' mod='adpmicrodatos'}</div> 
        </div>
    </div>

</div>