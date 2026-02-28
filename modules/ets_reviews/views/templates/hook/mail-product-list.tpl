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
{if isset($products) && is_array($products) && $products|count > 0}
    <table class="ets-rv-product-list" style="list-style: none;padding: 0;width:100%;display:table;border:1px solid #ccc;border-collapse: collapse;vertical-align: middle;">
        {foreach from=$products item='product'}
            <tr class="ets-rv-product-item" style="border:1px solid #ccc;">
                {if isset($product.image) && $product.image|trim !==''}
                    <td style="border:1px solid #ccc;vertical-align: middle;padding: 10px 15px;">
                        <a href="{$product.link nofilter}">
                            <img src="{$product.image nofilter}" alt="{$product.name|escape:'html':'UTF-8'}" style="width:80px;display:inline-block;margin-right: 10px;"/>
                        </a>
                    </td>
                {/if}
                <td style="border:1px solid #ccc;vertical-align: middle;padding: 10px 15px;">
                    <a href="{$product.link nofilter}" style="text-decoration: none;color: #000;" title="{$product.name|escape:'html':'UTF-8'}">
                        {$product.name|escape:'html':'UTF-8'}
                    </a>
                </td>
                <td style="border:1px solid #ccc;vertical-align: middle;padding: 10px 15px;">
                    <a target="_blank" href="{$product.link nofilter}?ets_rv_add_review=1" style="display: inline-block;text-align: left;color: #fff;text-decoration: none;font-weight: 600;background: #25B9D7;padding: 8px 18px;border-radius: 4px;">
                        <span>{if isset($ETS_RV_MAIL_RATE_NOW_TEXT)}{$ETS_RV_MAIL_RATE_NOW_TEXT|escape:'html':'UTF-8'}{else}Rate now{/if}</span>
                    </a>
                </td>
            </tr>
        {/foreach}
    </table>
{/if}