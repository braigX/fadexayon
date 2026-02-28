{*
* 2007-2022 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innova Deluxe SL
* @copyright 2022 Innova Deluxe SL

* @license   INNOVADELUXE
*}
<span class="pull-right">
    <input autocomplete="off" type="hidden" class='js_base_price' value="{if $priceDisplay == 1}{$icp_price_wo|escape:'htmlall':'UTF-8'}{else}{$icp_price|escape:'htmlall':'UTF-8'}{/if}" />
    <span id="idx_resume_base_price">
    {if $priceDisplay == 1}
        {if isset($icp_price_wo_wd_formated)}
            <span class="idxrcp_resume_opt_price_wodiscount">
                {$icp_price_wo_formated|escape:'htmlall':'UTF-8'}
            </span>
            {$icp_price_wo_wd_formated|escape:'htmlall':'UTF-8'}
        {else}
            {$icp_price_wo_formated|escape:'htmlall':'UTF-8'}
        {/if}
    {else}
        {if isset($icp_price_wd_formated)}
            <span class="idxrcp_resume_opt_price_wodiscount">
                {$icp_price_formated|escape:'htmlall':'UTF-8'}    
            </span>
            <p>{$icp_price_wd_formated|escape:'htmlall':'UTF-8'}</p>
        {else}
            {$icp_price_formated|escape:'htmlall':'UTF-8'}
        {/if}
    {/if}
    </span>
</span>
