{** * Estimated Delivery - Front Office Feature
 *
 * NOTICE OF LICENSE
 *
 * @author    Pol Rué
 * @copyright Smart Modules 2015
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * @category Transport & Logistics
 * Registered Trademark & Property of smart-modules.com
 *
 * ***************************************************
 * *               Estimated Delivery                *
 * *          http://www.smart-modules.com           *
 * *                                                  *
 * ***************************************************
*}
<style>
.date_green {
    color: #44B449;
}
</style>

{function printEstimatedDeliveryByProduct deliveries=''}
<table width="100%">
	<tr class="big bold">
		<td>{if $deliveries|count > 0}{l s='Estimated deliveries by product' mod='estimateddelivery'}{else}{l s='Estimated delivery' mod='estimateddelivery'}{/if}</td>
    </tr>
    <tr>
		<td colspan="12" height="5">&nbsp;</td>
	</tr>
    {assign var='del_min' value=''}
    {assign var='del_max' value=''}
    {foreach from=$deliveries item=delivery}
        <tr>
            <td>
                {if $del_min != $delivery->delivery_cmp_min && $del_max != $delivery->delivery_cmp_max}
                {assign var='del_min' value=$delivery->delivery_cmp_min}
                {assign var='del_max' value=$delivery->delivery_cmp_max}
                <span class="date_green bold">
                        {$delivery->delivery_min|escape:'htmlall':'UTF-8'}
                        {if $delivery->delivery_min|escape:'htmlall':'UTF-8' != $delivery->delivery_max|escape:'htmlall':'UTF-8'} 
                            - {$delivery->delivery_max|escape:'htmlall':'UTF-8'}
                        {/if}
                        {if $delivery->dp->is_custom && (int)$delivery->dp->add_custom_days > 0 && $delivery->dp->msg != ''}
                            <br>{$delivery->dp->msg|escape:'htmlall':'UTF-8'|replace:'{date}':$delivery->dp->add_custom_days}
                        {/if}
                </span>
                {/if}
            </td>
        </tr>
        <tr>
            <td>
                <span class="bold">{$delivery->dp->name|escape:'htmlall':'UTF-8'}</span>
            </td>
        </tr>
        <tr>
            <td>
                <table class="border-spacing: 0px 10px;">
                    {if isset($delivery->dp->attributes) && ($delivery->dp->attributes|count > 0) && $delivery->dp->attributes != ''}
                        {foreach from=$delivery->dp->attributes item=attribute}
                            <tr>
                                <td>
                                    <span class="bold">{$attribute['attr_group_name']|escape:'htmlall':'UTF-8'}: </span>
                                    <span>{$attribute['attr_name']|escape:'htmlall':'UTF-8'}</span>
                                </td>
                            </tr>
                        {/foreach} 
                    {/if}
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="12" height="3">&nbsp;</td>
        </tr>
    {/foreach}
</table>
{/function}
<table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin:0;">
	<tr>
		<td>
            {if $dates_by_product}
            {foreach from=$deliveries item=delivery key=k name="ed_order"}
                {printEstimatedDeliveryByProduct deliveries=$delivery}
            {/foreach}
            {else}
            <table width="100%">
                <tr class="big bold">
                    <td>{l s='Estimated delivery' mod='estimateddelivery'}</td>
                </tr>
                <tr>
                    <td colspan="12" height="5">&nbsp;</td>
                </tr>
                <tr>
                    <td>
                        <span class="bold">{l s='Minimum delivery' mod='estimateddelivery'}:  </span><span class="date_green bold">{$edcarrier.delivery_min|escape:'htmlall':'UTF-8'}</span><br \>
                        <span class="bold">{l s='Maximum delivery' mod='estimateddelivery'}:  </span><span class="date_green bold">{$edcarrier.delivery_max|escape:'htmlall':'UTF-8'}</span>
                    </td>
                </tr>
            </table> 
            {/if}
		</td>
	</tr>
</table>
