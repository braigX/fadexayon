{*
* 2007-2023 ETS-Soft
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
* versions in the future. If you wish to customize PrestaShop for your
* needs, please contact us for extra customization service at an affordable price
*
*  @author ETS-Soft <etssoft.jsc@gmail.com>
*  @copyright  2007-2023 ETS-Soft
*  @license    Valid for 1 website (or project) for each purchase of license
*  International Registered Trademark & Property of ETS-Soft
*}
{if $products}
    {if $no_html}
        {foreach from = $products item='product'}
            {$product.product_name|escape:'html':'UTF-8'} ({$product.product_quantity|intval} {if $product.product_quantity >1}{l s='items' mod='ets_ordermanager'}{else}{l s='item' mod='ets_ordermanager'}{/if}), 
        {/foreach}
    {else}
        {if !$exportorder}
            <div class="list-order-products">
            {/if}
                {foreach from = $products item='product'}
                    {if !$exportorder && $product.product_id}
                        <a target="_blank" href="{$link->getProductLink($product.product_id,null,null,null,null,null,$product.product_attribute_id)|escape:'html':'UTF-8'}" title="{$product.product_name|escape:'html':'UTF-8'} ({$product.product_quantity|intval} {if $product.product_quantity >1}{l s='items' mod='ets_ordermanager'}{else}{l s='item' mod='ets_ordermanager'}{/if})">
                        {if $product.image}
                            <img src="{$product.image|escape:'html':'UTF-8'}" alt="{$product.product_name|escape:'html':'UTF-8'}" title="{$product.product_name|escape:'html':'UTF-8'} ({$product.product_quantity|intval} {if $product.product_quantity >1}{l s='items' mod='ets_ordermanager'}{else}{l s='item' mod='ets_ordermanager'}{/if})" />
                        {else}
                            {$product.product_name|escape:'html':'UTF-8'}
                        {/if}
                        </a>
                    {else}
                        {$product.product_name|escape:'html':'UTF-8'} ({$product.product_quantity|intval} {if $product.product_quantity >1}{l s='items' mod='ets_ordermanager'}{else}{l s='item' mod='ets_ordermanager'}{/if}),
                    {/if}
                {/foreach}
            {if !$exportorder}
                </div>
            {/if}
    {/if}
    
{/if}