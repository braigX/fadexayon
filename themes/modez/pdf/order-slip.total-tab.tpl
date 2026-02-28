{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 *}

<table id="total-tab" width="100%">

    {assign var='minimum_cart_fee_amount' value=$minimum_cart_fee_amount|default:0}
    {assign var='minimum_cart_fee_amount_label' value=$minimum_cart_fee_amount_label|default:'Frais de commande'}

    {if $order_slip->shipping_cost_amount > 0}
        <tr>
            <td class="grey" width="70%">
                {if $tax_excluded_display}
                    {if $order->id_lang == 1}Livraison (HT)
                    {elseif $order->id_lang == 2}Verzending (excl. BTW)
                    {elseif $order->id_lang == 3}Versand (ohne MwSt.)
                    {elseif $order->id_lang == 4}Envío (sin IVA)
                    {elseif $order->id_lang == 5}Spedizione (al netto IVA)
                    {else}Livraison (HT)
                    {/if}
                {else}
                    {if $order->id_lang == 1}Livraison (TTC)
                    {elseif $order->id_lang == 2}Verzending (incl. BTW)
                    {elseif $order->id_lang == 3}Versand (inkl. MwSt.)
                    {elseif $order->id_lang == 4}Envío (con IVA)
                    {elseif $order->id_lang == 5}Spedizione (IVA inclusa)
                    {else}Livraison (TTC)
                    {/if}
                {/if}
            </td>
            <td class="white" width="30%">
                - {displayPrice currency=$order->id_currency price=$order_slip->shipping_cost_amount}
            </td>
        </tr>
    {/if}

    {if isset($order_details) && count($order_details) > 0}
        {if (($order->total_paid_tax_incl - $order->total_paid_tax_excl) > 0)}
            <tr>
                <td class="grey" width="70%">
                    {if $tax_excluded_display}
                        {if $order->id_lang == 1}Total produits (HT)
                        {elseif $order->id_lang == 2}Producttotaal (excl. BTW)
                        {elseif $order->id_lang == 3}Produktgesamt (ohne MwSt.)
                        {elseif $order->id_lang == 4}Total productos (sin IVA)
                        {elseif $order->id_lang == 5}Totale prodotti (al netto IVA)
                        {else}Total produits (HT)
                        {/if}
                    {else}
                        {if $order->id_lang == 1}Total produits (TTC)
                        {elseif $order->id_lang == 2}Producttotaal (incl. BTW)
                        {elseif $order->id_lang == 3}Produktgesamt (inkl. MwSt.)
                        {elseif $order->id_lang == 4}Total productos (con IVA)
                        {elseif $order->id_lang == 5}Totale prodotti (IVA inclusa)
                        {else}Total produits (TTC)
                        {/if}
                    {/if}
                </td>
                <td class="white" width="30%">
                    - {if $tax_excluded_display}{$order->total_products}{else}{$order->total_products_wt}{/if}
                </td>
            </tr>
        {else}
            <tr>
                <td class="grey" width="70%">
                    {if $order->id_lang == 1}Total produits
                    {elseif $order->id_lang == 2}Producttotaal
                    {elseif $order->id_lang == 3}Produktgesamt
                    {elseif $order->id_lang == 4}Total productos
                    {elseif $order->id_lang == 5}Totale prodotti
                    {else}Total produits
                    {/if}
                </td>
                <td class="white" width="30%">
                    - {$order->total_products}
                </td>
            </tr>
        {/if}
    {/if}

    {if ($order->total_paid_tax_incl - $order->total_paid_tax_excl) > 0}
        <tr>
            <td class="grey" width="70%">
                {if $order->id_lang == 1}Total TVA
                {elseif $order->id_lang == 2}Totaal BTW
                {elseif $order->id_lang == 3}Gesamt MwSt.
                {elseif $order->id_lang == 4}Total IVA
                {elseif $order->id_lang == 5}Totale IVA
                {else}Total TVA
                {/if}
            </td>
            <td class="white" width="30%">
                - {displayPrice currency=$order->id_currency price=($order->total_paid_tax_incl - $order->total_paid_tax_excl)}
            </td>
        </tr>
    {/if}

    {if $minimum_cart_fee_amount > 0}
        <tr>
            <td class="grey" width="70%">
                {$minimum_cart_fee_amount_label}
            </td>
            <td class="white" width="30%">
                - {displayPrice currency=$order->id_currency price=( $minimum_cart_fee_amount - $order->total_paid_tax_incl)}
            </td>
        </tr>
    {/if}

    {* ─── Total HT ───────────────────────────────── *}
    {if $tax_excluded_display}
        <tr class="bold">
            <td class="grey" width="70%">
                {if $order->id_lang == 1}Total (HT)
                {elseif $order->id_lang == 2}Totaal (excl. BTW)
                {elseif $order->id_lang == 3}Gesamt (ohne MwSt.)
                {elseif $order->id_lang == 4}Total (sin IVA)
                {elseif $order->id_lang == 5}Totale (al netto IVA)
                {else}Total (HT)
                {/if}
            </td>
            <td class="white" width="30%">
                {if $total_cart_rule}
                    {assign var=total_paid value=$order->total_paid_tax_excl - $total_cart_rule}
                {else}
                    {assign var=total_paid value=$order->total_paid_tax_excl}
                {/if}
                - {displayPrice currency=$order->id_currency price=$total_paid}
            </td>
        </tr>
    {/if}

    {* ─── Total TTC ───────────────────────────────── *}
    <tr class="bold">
        <td class="grey" width="70%">
            {if $order->id_lang == 1}Total (TTC)
            {elseif $order->id_lang == 2}Totaal (incl. BTW)
            {elseif $order->id_lang == 3}Gesamt (inkl. MwSt.)
            {elseif $order->id_lang == 4}Total (con IVA)
            {elseif $order->id_lang == 5}Totale (IVA inclusa)
            {else}Total (TTC)
            {/if}
        </td>
        <td class="white" width="30%">
            {if $minimum_cart_fee_amount > 0}
                - {displayPrice currency=$order->id_currency price=$minimum_cart_fee_amount}
            {else}
                {if $total_cart_rule}
                    {assign var=total_paid value=$order->total_paid_tax_incl - $total_cart_rule}
                {else}
                    {assign var=total_paid value=$order->total_paid_tax_incl}
                {/if}
                - {displayPrice currency=$order->id_currency price=$total_paid}
            {/if}
        </td>
    </tr>

</table>
