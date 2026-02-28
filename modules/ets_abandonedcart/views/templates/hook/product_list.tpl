{*
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
*}

<ul class="ets-ac-products-list-selected" {if isset($idList) && $idList}id="{$idList|escape:'html':'UTF-8'}"{/if}>
    {if $products}
        {foreach from=$products item='product'}
            <li class="product" data-id="{$product.id_product|intval}">
                <input type="hidden" name="{$input_name|escape:'html':'UTF-8'}[]" value="{$product.id_product|intval}"/>
                {if $showDeleteBtn}
                <button class="btn btn-default del_product_search" type="button">
                    <i class="icon-remove text-danger"></i>
                </button>
                {/if}
                {if $product.url_image}
                    <img src="{$product.url_image|escape:'html':'UTF-8'}" style="width:32px;" />
                {/if}
                <a class="ws_file_uploaded" href="{$product.link_product|escape:'quotes':'UTF-8'}" target="_blank">{$product.name|escape:'html':'UTF-8'} {if $product.reference}({l s='ref' mod='ets_abandonedcart'}: {$product.reference|escape:'html':'UTF-8'}){/if}</a>
            </li>
        {/foreach}
    {/if}
</ul>