{*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License version 3.0
* that is bundled with this package in the file LICENSE.txt
* It is also available through the world-wide-web at this URL:
* https://opensource.org/licenses/AFL-3.0
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your needs
* please refer to CustomizationPolicy.txt file inside our module for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
*}

{if $filteredList|@count gt 0}
<div class="wk-scroll-vertical wk-height wk-min-height">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th class="fixed-width-xs">
                    <span class="title_box">
                        <input type="checkbox" class="wk-checked-products"
                            onclick="checkDelBoxes(this.form, 'searchedProduct[]', this.checked)">
                    </span>
                </th>
                <th class="fixed-width-xs">
                    <label class="title_box">
                        {l s='ID' mod='wksampleproduct'}
                    </label>
                </th>
                <th class="fixed-width-xs">
                    <label class="title_box">
                        {l s='Image' mod='wksampleproduct'}
                    </label>
                </th>
                <th>
                    <label class="title_box">
                        {l s='Product name' mod='wksampleproduct'}
                    </label>
                </th>
            </tr>
        </thead>
        <tbody>
            {if isset($filteredList) && $filteredList}
                {foreach $filteredList as $filterProduct}
                    <tr>
                        <td>
                            <input type="checkbox" name="searchedProduct[]" class="groupBox wk-checked-products"
                                id="searchedProduct_{$filterProduct.id_product|escape:'htmlall':'UTF-8'}"
                                value="{$filterProduct.id_product|escape:'htmlall':'UTF-8'}">
                        </td>
                        <td class="wk-checked-products-row">{$filterProduct.id_product|escape:'htmlall':'UTF-8'}</td>
                        <td class="wk-checked-products-row">
                            <img class="img img-thumbnail" src="{$filterProduct.image|escape:'htmlall':'UTF-8'}" />
                        </td>
                        <td class="wk-checked-products-row">
                            <span for="searchedProduct_{$filterProduct.id_product|escape:'htmlall':'UTF-8'}">
                                {$filterProduct.name|escape:'htmlall':'UTF-8'}
                            </span>
                        </td>
                    </tr>
                {/foreach}
            {/if}
        </tbody>
    </table>
</div>
{else}
<div class="alert alert-warning"  style="margin: 0;">{l s='No products found for selected filters.' mod='wksampleproduct'}</div>
{/if}
