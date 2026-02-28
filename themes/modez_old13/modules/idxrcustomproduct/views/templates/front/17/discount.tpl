{*
* 2007-2020 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innova Deluxe SL
* @copyright 2020 Innova Deluxe SL

* @license   INNOVADELUXE
*}

<tr {if !$show_discount_line || !$conf.discount }style="display:none;"{/if}>
    <input type="hidden" id="idxcp_discount_type" value="{$conf.discount_type}"/>
    <input type="hidden" id="idxcp_discount_amount" value="{$conf.discount_amount}"/>
    <input type="hidden" id="show_discount_line" value="{$show_discount_line}"/>
    <td  colspan="2">{l s='Discount:' mod='idxrcustomproduct'} {if $conf.discount_type == 'percentage'}{$conf.discount_amount|round:2|escape:'htmlall':'UTF-8'}%{/if}</td>
    <td><span class="pull-right"><span id="idxcp_discount_value"></span> {$currency.sign|escape:'htmlall':'UTF-8'}</span></td>
</tr>
