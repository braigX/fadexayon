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

{* Customize Types of microdata *}
<div role="tabpanel" class="tab-pane active" id="customize_type_microdata">
    <div class="form-group">
        <label class="control-label col-lg-5" for='customize_types_microdata_homepage'>
            <span class="label-tooltip" data-toggle="tooltip" title="{l s='Decide what microdata types you want to display on the home page' mod='adpmicrodatos'}">
                {l s='Microdata types on home page' mod='adpmicrodatos'}
            </span>
        </label>
        <div class="col-lg-7">
            <div class="input-group">
                <select multiple="multiple" name="customize_types_microdata_homepage[]" id="customize_types_microdata_homepage" class="text form-control" required>
                    {foreach from=$types_microdata_homepage item=type_microdata_homepage}
                        <option value="{$type_microdata_homepage.id|escape:'htmlall':'UTF-8'}" {if in_array($type_microdata_homepage.id, $types_microdata_homepage_selected)}selected{/if}>{$type_microdata_homepage.name|escape:'htmlall':'UTF-8'}</option>
                    {/foreach}
                </select>
                <div class="help-block">{l s='Select one or more type of microdata. CTRL for multiple selection' mod='adpmicrodatos'}</div> 
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-lg-5" for='customize_types_microdata_product_list'>
            <span class="label-tooltip" data-toggle="tooltip" title="{l s='Decide what microdata types you want to display on the products list' mod='adpmicrodatos'} {l s='Included prices drop list, best sales list, new products list and manufacturer product list' mod='adpmicrodatos'}">
                {l s='Microdata types on Products List' mod='adpmicrodatos'}
            </span>
        </label>
        <div class="col-lg-7">
            <div class="input-group">
                <select multiple="multiple" name="customize_types_microdata_product_list[]" id="customize_types_microdata_product_list" class="text form-control" required>
                    {foreach from=$types_microdata_product_list item=type_microdata_product_list}
                        <option value="{$type_microdata_product_list.id|escape:'htmlall':'UTF-8'}" {if in_array($type_microdata_product_list.id, $types_microdata_product_list_selected)}selected{/if}>{$type_microdata_product_list.name|escape:'htmlall':'UTF-8'}</option>
                    {/foreach}
                </select>
                <div class="help-block">{l s='Select one or more type of microdata. CTRL for multiple selection' mod='adpmicrodatos'}</div> 
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-lg-5" for='customize_types_microdata_product_page'>
            <span class="label-tooltip" data-toggle="tooltip" title="{l s='Decide what microdata types you want to display on the product page' mod='adpmicrodatos'}">
                {l s='Microdata types on Product Page' mod='adpmicrodatos'}
            </span>
        </label>
        <div class="col-lg-7">
            <div class="input-group">
                <select multiple="multiple" name="customize_types_microdata_product_page[]" id="customize_types_microdata_product_page" class="text form-control" required>
                    {foreach from=$types_microdata_product_page item=type_microdata_product_page}
                        <option value="{$type_microdata_product_page.id|escape:'htmlall':'UTF-8'}" {if in_array($type_microdata_product_page.id, $types_microdata_product_page_selected)}selected{/if}>{$type_microdata_product_page.name|escape:'htmlall':'UTF-8'}</option>
                    {/foreach}
                </select>
                <div class="help-block">{l s='Select one or more type of microdata. CTRL for multiple selection' mod='adpmicrodatos'}</div> 
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-lg-5" for='customize_types_microdata_other_pages'>
            <span class="label-tooltip" data-toggle="tooltip" title="{l s='Decide what microdata types you want to display on the other pages' mod='adpmicrodatos'}">
                {l s='Microdata types on other Pages' mod='adpmicrodatos'}
            </span>
        </label>
        <div class="col-lg-7">
            <div class="input-group">
                <select multiple="multiple" name="customize_types_microdata_other_pages[]" id="customize_types_microdata_other_pages" class="text form-control" required>
                    {foreach from=$types_microdata_other_pages item=type_microdata_other_pages}
                        <option value="{$type_microdata_other_pages.id|escape:'htmlall':'UTF-8'}" {if in_array($type_microdata_other_pages.id, $types_microdata_other_pages_selected)}selected{/if}>{$type_microdata_other_pages.name|escape:'htmlall':'UTF-8'}</option>
                    {/foreach}
                </select>
                <div class="help-block">{l s='Select one or more type of microdata. CTRL for multiple selection' mod='adpmicrodatos'}</div> 
            </div>
        </div>
    </div>
</div>