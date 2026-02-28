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
<div role="tabpanel" class="tab-pane" id="customize_breadcrumb">
    <div class="form-group">
        <label class="control-label col-lg-5">
            <span class="label-tooltip" data-toggle="tooltip" title="{l s='Do you want activate o deactivate the root category from breadcrumb?' mod='adpmicrodatos'}" data-placement="bottom">
                {l s='Active Breadcrumb Root Category' mod='adpmicrodatos'}
            </span>
        </label>
        <div class="col-lg-7">
            <span class="switch prestashop-switch fixed-width-lg">
                <input type="radio" name="active_microdata_rootcategory" id="active_microdata_rootcategory_on" value="1" {if $active_microdata_rootcategory==1}checked="checked"{/if}>
                <label for="active_microdata_rootcategory_on">{l s='Yes' mod='adpmicrodatos'}</label>
                <input type="radio" name="active_microdata_rootcategory" id="active_microdata_rootcategory_off" value="0" {if $active_microdata_rootcategory==0}checked="checked"{/if}>
                <label for="active_microdata_rootcategory_off">{l s='No' mod='adpmicrodatos'}</label>
                <a class="slide-button btn"></a>
            </span>
            <div class="help-block">{l s='Only works on MultiStore Mode' mod='adpmicrodatos'}</div>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-lg-5">
            <span class="label-tooltip" data-toggle="tooltip" title="{l s='Do you want activate o deactivate the home categoty from breadcrumb?' mod='adpmicrodatos'}" data-placement="bottom">
                {l s='Active Breadcrumb Home Category' mod='adpmicrodatos'}
            </span>
        </label>
        <div class="col-lg-7">
            <span class="switch prestashop-switch fixed-width-lg">
                <input type="radio" name="active_microdata_homecategory" id="active_microdata_homecategory_on" value="1" {if $active_microdata_homecategory==1}checked="checked"{/if}>
                <label for="active_microdata_homecategory_on">{l s='Yes' mod='adpmicrodatos'}</label>
                <input type="radio" name="active_microdata_homecategory" id="active_microdata_homecategory_off" value="0" {if $active_microdata_homecategory==0}checked="checked"{/if}>
                <label for="active_microdata_homecategory_off">{l s='No' mod='adpmicrodatos'}</label>
                <a class="slide-button btn"></a>
            </span>
            <div class="help-block">{l s='Only works on Breadcrumb' mod='adpmicrodatos'}</div>
        </div>
    </div>
</div>