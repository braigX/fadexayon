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
<style>
    td,
    th {
        text-align: center;
    }
</style>

<table class="payment-tab" cellpadding="4" width="100%" style='border: 1px solid #000 !important;'>
    <tr>
        <td class="payment center bold" style='border: 1px solid #000 !important;' width="100%">
            {$title|escape:'html':'UTF-8'}
        </td>
    </tr>
</table>

<table class="product" width="100%" cellpadding="4" cellspacing="0">
    <thead>
        <tr>
            <th class="product header small" width="50%">{$table_title_ref|escape:'html':'UTF-8'}</th>
            <th class="product header small" width="50%">{$table_title_product|escape:'html':'UTF-8'}</th>
        </tr>
    </thead>

    <tbody>
        {if !isset($sampleProductPurchaseDetails) || count($sampleProductPurchaseDetails) == 0}
            <tr class="product" colspan="4">
                <td class="product center">
                    {l s='No details' d='Shop.Pdf' pdf='true'}
                </td>
            </tr>
        {else}
            {foreach $sampleProductPurchaseDetails as $sample_order_detail}
                {cycle values=["color_line_even", "color_line_odd"] assign=bgcolor_class}
                <tr class="product {$bgcolor_class|escape:'html':'UTF-8'}">
                    <td class="product center">
                        {$sample_order_detail.reference|escape:'html':'UTF-8'}
                    </td>
                    <td class="product center">
                        {$sample_order_detail.product|escape:'html':'UTF-8'}
                    </td>
                </tr>
            {/foreach}
        {/if}
    </tbody>
</table>
