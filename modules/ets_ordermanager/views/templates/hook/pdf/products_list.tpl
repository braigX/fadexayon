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
<table width="100%" style="border-collapse: collapse;border: 1px solid #cccccc;">
    <thead>
        <tr bgcolor="#eeeeee">
            <th class="text-center sku" style="width:45%;border: 1px solid #cccccc;">
                <table style="padding: 5px;">
                    <tr>
                        <td>{l s='Product name' mod='ets_ordermanager' pdf=true}</td>
                    </tr>
                </table>
            </th>
            <th class="text-center" style="width:20%;text-align:center;border: 1px solid #cccccc;">
                <table style="padding: 5px;">
                    <tr>
                        <td>{l s='Unit Price (Tax excl.)' mod='ets_ordermanager' pdf=true}</td>
                    </tr>
                </table>
            </th>
            <th class="text-center" style="width:15%;text-align:center;border: 1px solid #cccccc;">
                <table style="padding: 5px;">
                    <tr>
                        <td>{l s='Quantity' mod='ets_ordermanager' pdf=true}</td>
                    </tr>
                </table>
            </th>
            <th class="price text-center" style="width:20%;border: 1px solid #cccccc;">
                <table style="padding: 5px;">
                    <tr>
                        <td style="text-align:right;">{l s='Total price (Tax excl.)' mod='ets_ordermanager' pdf=true}</td>
                    </tr>
                </table>
            </th>
        </tr>
    </thead>
    <tbody>
        {if $products}
            {foreach from=$products item='product'}
                <tr>
                    <td style="border: 1px solid #cccccc;">
                        <table style="padding: 5px;">
                            <tr>
                                <td>{$product.product_name|escape:'html':'UTF-8'}</td>
                            </tr>
                        </table>
                    </td>
                    <td style="text-align:center;border: 1px solid #cccccc;">
                        <table style="padding: 5px;">
                            <tr>
                                <td>{displayPrice price=$product.unit_price_tax_excl}</td>
                            </tr>
                        </table>
                    </td>
                    <td style="text-align:center;border: 1px solid #cccccc;">
                        <table style="padding: 5px;">
                            <tr>
                                <td>{$product.product_quantity|intval}</td>
                            </tr>
                        </table>
                    </td>
                    <td style="text-align:center;border: 1px solid #cccccc;">
                        <table style="padding: 5px;">
                            <tr>
                                <td style="text-align:right;">{displayPrice price=$product.total_price_tax_excl}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            {/foreach}
        {/if}
        {if $has_total}
            <tr>
                <td colspan="3" class="text-right" style="text-align:right;border: 1px solid #cccccc;">
                    <table style="padding: 5px;">
                        <tr>
                            <td>{l s='TOTAL (Tax incl.)' mod='ets_ordermanager' pdf=true}</td>
                        </tr>
                    </table>
                </td>
                <td class="price" style="text-align:right;border: 1px solid #cccccc;">
                    <table style="padding: 5px;">
                        <tr>
                            <td>{displayPrice price=$total_order}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        {/if}
    </tbody>
</table>