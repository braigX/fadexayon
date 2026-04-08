{**
* 2007-2022 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innova Deluxe SL
* @copyright 2022 Innova Deluxe SL
* @license   INNOVADELUXE
*}

<tr>                                
<span class='hidden' id='js_tax_ratio'>{$steps.tax_ratio|escape:'htmlall':'UTF-8'}</span>
<td colspan="2"><b>{l s='Product with this customization' mod='idxrcustomproduct'}</b></td>
<td>
    <h4 class="pull-right">
        <input autocomplete="off" type="hidden" id="js_resume_total_price" value="{if $priceDisplay != 1}{$icp_price|escape:'htmlall':'UTF-8'}{else}{$icp_price_wo|escape:'htmlall':'UTF-8'}{/if}">
        <span id='resume_total_price'>
            {if $priceDisplay == 1}
        {if isset($icp_price_wo_wd_formated)}{$icp_price_wo_wd_formated|escape:'htmlall':'UTF-8'}{else}{$icp_price_wo_formated|escape:'htmlall':'UTF-8'}{/if}
    {else}
{if isset($icp_price_wd_formated)}{$icp_price_wd_formated|escape:'htmlall':'UTF-8'}{else}{$icp_price_formated|escape:'htmlall':'UTF-8'}{/if}
{/if}
</span>
</h4>
</td>
</tr>
<tr style="display: none" id="qty_total_price_tr">
    <td colspan="2">{l s='Total' mod='idxrcustomproduct'}</td>
    <td>
        <span class="pull-right">
            <span id='qty_total_price'></span>
        </span>
    </td>
</tr>
