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
{if !isset($ed_tooltip)}
    {assign var='ed_tooltip' value=0}
{/if}

{function printEstimatedDeliveryCart delivery=''}
    <p class="ed_orderbefore">
        <span class="ed_order_title" >{l s='Estimated delivery' mod='estimateddelivery'}: </span>
        {if $delivery->delivery_min|escape:'htmlall':'UTF-8' == $delivery->delivery_max|escape:'htmlall':'UTF-8'}
            <span title="{$delivery->name|escape:'htmlall':'UTF-8'}">{if !isset($delivery->tot)}{l s='on' mod='estimateddelivery'} {/if}<span><strong class="date_green">{$delivery->delivery_min|escape:'htmlall':'UTF-8'}</strong></span><span class="ed_with_carrier"> {l s='with' mod='estimateddelivery'} <strong>{$delivery->name|escape:'htmlall':'UTF-8'}</strong></span></span>{if $delivery->delay != ''}</span>{/if}
        {else}
            <span title="{$delivery->name|escape:'htmlall':'UTF-8'}">{l s='between' mod='estimateddelivery'} <span><strong class="date_green">{$delivery->delivery_min|escape:'htmlall':'UTF-8'}</strong></span> {l s='and' mod='estimateddelivery'} <span><strong class="date_green">{$delivery->delivery_max|escape:'htmlall':'UTF-8'}</strong></span><span class="ed_with_carrier"> {l s='with' mod='estimateddelivery'} <strong>{$delivery->name|escape:'htmlall':'UTF-8'}</strong></span></span></span>
        {/if}
    </p>
    {if $enable_custom_days}
    <p class="ed_custom_days">
        {if ($which_module && $is_customize_cart) || !$which_module}
            {if $delivery->dp->is_custom && $delivery->dp->add_custom_days > 0 && $delivery->dp->msg != ''}
                <span>
                    <strong class="date_green">{$delivery->dp->msg|escape:'htmlall':'UTF-8'|replace:'{date}':$delivery->dp->add_custom_days}</strong>
                </span>
            {/if}
        {/if}
    </p>
    {/if}

{/function}

{function printEstimatedDeliveryByProduct deliveries=''}
    <h4 class="ed_order_title">{if $deliveries|count > 0}{l s='Estimated deliveries by product' mod='estimateddelivery'}{else}{l s='Estimated delivery' mod='estimateddelivery'}{/if}</h4>
    {assign var='del_min' value=''}
    {assign var='del_max' value=''}
    {foreach from=$deliveries item=delivery}
        <div class="ed-product-block">
            {if $del_min != $delivery->delivery_cmp_min && $del_max != $delivery->delivery_cmp_max}
                {assign var='del_min' value=$delivery->delivery_cmp_min}
                {assign var='del_max' value=$delivery->delivery_cmp_max}
                <span>
                    <strong class="date_green">{$delivery->delivery_min|ucfirst|escape:'htmlall':'UTF-8'}
                        {if $delivery->delivery_min|escape:'htmlall':'UTF-8' != $delivery->delivery_max|escape:'htmlall':'UTF-8'}
                            - {$delivery->delivery_max|escape:'htmlall':'UTF-8'}
                        {/if}
                    </strong>
                </span>
                {if $enable_custom_days}
                    {if ($which_module && $is_customize_cart) || !$which_module}
                        {if $delivery->dp->is_custom && $delivery->dp->add_custom_days > 0 && $delivery->dp->msg != ''}
                            <span style="display: block">
                                <strong class="date_green">{$delivery->dp->msg|escape:'htmlall':'UTF-8'|replace:'{date}':$delivery->dp->add_custom_days}</strong>
                            </span>
                        {/if}
                    {/if}
                {/if}
            {/if}
            <div class="ed-product">
                <span class="edp-product-name">{$delivery->dp->name|escape:'htmlall':'UTF-8'}</span>
                {if isset($delivery->dp->attributes) && ($delivery->dp->attributes|count > 0) && $delivery->dp->attributes != ''}
                    {foreach from=$delivery->dp->attributes item=attribute}
                        <div class="edp-attributes">
                            <span class="attr-group-name">{$attribute['attr_group_name']|escape:'htmlall':'UTF-8'}: </span>
                            <span class="attr-name">{$attribute['attr_name']|escape:'htmlall':'UTF-8'}</span>
                        </div>
                    {/foreach} 
                {/if}
            </div>
        </div>
    {/foreach}
    {* </p> *}
{/function}

{function printReleaseAvailable item = ''}
   {* Needs more testing *}
    <p>
    {if $hasDeliveries}
        ✔ <strong>{$item.name|escape:'htmlall':'UTF-8'}</strong>: {$item.preorder_msg|escape:'htmlall':'UTF-8'|replace:'{date}':$item.ed_release_date}
    {else}
        {$item.preorder_msg|escape:'htmlall':'UTF-8'|replace:'{date}':$item.ed_release_date}
    {/if}
    </p>
{/function}

<script>
    {if isset($ed_controller) && $ed_controller != ''}var ed_controller = '{$ed_controller|escape:'htmlall':'UTF-8'}';{/if}
    {if isset($ed_hide_delay) && $ed_hide_delay != ''}var ed_hide_delay = {$ed_hide_delay|intval};{else}var ed_hide_delay = 0;{/if}
</script>
{if !isset($willDisplayCalendar)}
    {if isset($ed_cart) && count($ed_cart) > 0}
        <div id="estimateddelivery-cart" class="estimateddelivery{if isset($edclass) && $edclass != '' } {$edclass|escape:'htmlall':'UTF-8'}{/if}{if $display_cart_line} hide-default{/if}">
        {foreach from=$ed_cart item=delivery key=k name="ed_cart"}
            {if is_array($delivery) && isset($dates_by_product) && $dates_by_product}
                <div id="ed-cart-{$delivery[0]->dc->id_carrier|intval}" class="ed-cart-option {if $delivery[0]->dc->id_carrier == $ed_selected}ed-cart-selected{/if}"  {if $delivery[0]->dc->id_carrier != $ed_selected}style="display:none"{/if}>
                    {if $delivery[0]->dp->msg != ''}
                        {include file='./ed-special-date.tpl'}
                    {else}
                        {printEstimatedDeliveryByProduct deliveries = $delivery}
                    {/if}
                </div>
            {elseif isset($delivery->dc) && isset($delivery->dc->id_carrier)}
                <div id="ed-cart-{$delivery->dc->id_carrier|intval}" class="ed-cart-option {if $delivery->dc->id_carrier == $ed_selected}ed-cart-selected{/if}"  {if $delivery->dc->id_carrier != $ed_selected}style="display:none"{/if}>
                    {if $delivery->dp->msg != ''}
                        {include file='./ed-special-date.tpl'}
                    {else}
                        {printEstimatedDeliveryCart delivery = $delivery}
                        {if isset($ed_relandavail) && count($ed_relandavail) > 0}
                            {foreach from=$ed_relandavail item=item}
                                {printReleaseAvailable item = $item}
                            {/foreach}
                        {/if}
                    {/if}
                </div>
            {elseif isset($ed_relandavail) && count($ed_relandavail) > 0}
                <div id="ed-cart-{$k|intval}" class="ed-cart-option" style="display:none">
                    {foreach from=$ed_relandavail item=item}
                        {printReleaseAvailable item = $item}
                    {/foreach}
                </div>
            {/if}
        {/foreach}
        </div>
    {/if}
    {if $ed_notify_oos == 1}
        <div class="estimateddelivery estimateddelivery-lda{if isset($edclass) && $edclass != 'custom' }{$edclass|escape:'htmlall':'UTF-8'}{/if}" {if $edclass == 'custom'} style="background-color:{$edbackground|escape:'htmlall':'UTF-8'}; border-color:{$edborder|escape:'htmlall':'UTF-8'};"{/if}>
           <div class="ed_orderbefore ed_lda">
            <span class="ed_long_msg">{$ed_notify_oos_msg|escape:'htmlall':'UTF-8'}</span>
            </div>
        </div>
    {/if}
{/if}
