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

{capture name="estimateddelivery"}
    <div class="ed_product_summary estimateddelivery
        data-id_product="{$ed_id_product|intval}" data-id_product_attribute="{$ed_id_product_attribute|intval}">
        <div class="{if $edclass != ''} {$edclass|escape:'htmlall':'UTF-8'}{/if}" {if $edclass == 'custom'}style="background:{$edbackground|escape:'htmlall':'UTF-8'};border:1px solid {$edborder|escape:'htmlall':'UTF-8'}"{/if}">
            {if $delivery}
                {if $delivery->dp->msg != ''}
                    {include file='./ed-special-date.tpl'}
                {else}
                    {if $more_options}<span class="ed_tooltip" title="{l s='Additional Delivery Options may be available once you proceed with the checkout' mod='estimateddelivery'}">{/if}
                    <p class="ed_orderbefore">
                    {if $delivery->delivery_min|escape:'htmlall':'UTF-8' == $delivery->delivery_max|escape:'htmlall':'UTF-8'}
                        <span title="{$delivery->name|escape:'htmlall':'UTF-8'}">{if !isset($delivery->tot)}{l s='on' mod='estimateddelivery'} {/if}<span><strong class="date_green">{$delivery->delivery_min|escape:'htmlall':'UTF-8'}</strong></span>
                    {else}
                        <span title="{$delivery->name|escape:'htmlall':'UTF-8'}">{l s='between' mod='estimateddelivery'} <span><strong class="date_green">{$delivery->delivery_min|escape:'htmlall':'UTF-8'}</strong></span> {l s='and' mod='estimateddelivery'} <span><strong class="date_green">{$delivery->delivery_max|escape:'htmlall':'UTF-8'}</strong></span>
                    {/if}
                    <span class="ed_with_carrier"> {l s='with' mod='estimateddelivery'} <strong>{$delivery->name|escape:'htmlall':'UTF-8'}</strong></span></span>
                    {if $more_options}*</span>{/if}
                    </p>
                {/if}
            {if $enable_custom_days}
                {if ($which_module && $is_customize_cart) || !$which_module}
                    {if $delivery->dp->is_custom && $delivery->dp->add_custom_days > 0 && $delivery->dp->msg != ''}
                        <p class="ed_orderbefore">
                            <span><strong class="date_green">{$delivery->dp->msg|escape:'htmlall':'UTF-8'|replace:'{date}':$delivery->dp->add_custom_days}</strong></span>
                        </p>
                    {/if}
                {/if}
            {/if}
            {/if}
        </div>
    </div>
{/capture}
{if $ed_product_summary == ''}
    <div class="col-lg-12">
        {$smarty.capture.estimateddelivery nofilter}
    </div>
{else}
    {$ed_product_summary|replace:'{estimateddelivery}':$smarty.capture.estimateddelivery nofilter}
{/if}