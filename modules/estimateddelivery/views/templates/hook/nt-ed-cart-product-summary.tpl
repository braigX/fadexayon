{** * Estimated Delivery - Front Office Feature
 *
 * NOTICE OF LICENSE
 *
 * @author    Pol Rué
 * @copyright Smart Modules 2015
 * @license   One time purchase Licence (You can modify or resell the product but just one time per licence)
 * @version 3.3.1
 * @category Transport & Logistics
 * Registered Trademark & Property of Smart-Modules.pro
 *
 * ***************************************************
 * *               Estimated Delivery                *
 * *          http://www.smart-modules.com           *
 * *                    V 3.3.1                      *
 * ***************************************************
*}

{capture name="estimateddelivery"}
    <div class="ed_product_summary estimateddelivery{if $edclass != ''} {$edclass|escape:'htmlall':'UTF-8'}{/if}" {if $edclass == 'custom'}style="background:{$edbackground|escape:'htmlall':'UTF-8'};border:1px solid {$edborder|escape:'htmlall':'UTF-8'}"{/if}>
        <div>
            <p class="ed_orderbefore"><small>
                {if $more_options}<span class="ed_tooltip" title="{l s='Additional Delivery Options may be available once you proceed with the checkout' mod='estimateddelivery'}">{/if}
                {if $delivery->dp->is_available && $delivery->dp->msg != ''}
                    {if $delivery->dp->msg != ''}
                        {$delivery->dp->msg|escape:'htmlall':'UTF-8'|replace:'{date}':$delivery->dp->available_date}
                    {/if}
                {elseif $delivery->dp->is_release}
                   {if $delivery->dp->msg != ''}
                       {$delivery->dp->msg|escape:'htmlall':'UTF-8'|replace:'{date}':$delivery->dp->release_date}
                   {/if}
                {elseif $delivery->dp->is_virtual}
                   {if $delivery->dp->msg != ''}
                       {$delivery->dp->msg|escape:'htmlall':'UTF-8'}
                   {/if}
                {else}
                    {if $delivery->delivery_min|escape:'htmlall':'UTF-8' == $delivery->delivery_max|escape:'htmlall':'UTF-8'}
                        <span title="{$delivery->name|escape:'htmlall':'UTF-8'}">{if !isset($delivery->tot)}{l s='on' mod='estimateddelivery'} {/if}<span><strong class="date_green">{$delivery->delivery_min|escape:'htmlall':'UTF-8'}</strong></span>
                    {else}
                        <span title="{$delivery->name|escape:'htmlall':'UTF-8'}">{l s='Lieferzeit: vorauss. bis' mod='estimateddelivery'} <span><strong class="date_green">{$delivery->delivery_min|escape:'htmlall':'UTF-8'}</strong></span> {l s='bei' mod='estimateddelivery'} <strong>{l s='heutiger Druckfreigabe' mod='estimateddelivery'}</strong> {l s='bis 17 Uhr (Zwischenverkauf vorbehalten)' mod='estimateddelivery'}
                    {/if}
                    {*<span class="ed_with_carrier"> {l s='with' mod='estimateddelivery'} <strong>{$delivery->name|escape:'htmlall':'UTF-8'}</strong></span></span>*}
                {/if}
                {if $more_options}*</span>{/if}
            </small></p>
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

<div class="row">
    <div class="col-md-12">
       <p class="request-text-small"> 
            <hr />
            {* <span>{l s='ODER ' mod='estimateddelivery'}</span> *}
            <span class="nt-blue-color">{l s='Wunschlieferdatum ' mod='estimateddelivery'}</span>
            <span>{l s='wahlen:' mod='estimateddelivery'}</span>
            <span class="nt_picker_container">
                <i class="fa fa-calendar nt-blue-color" aria-hidden="true"></i>
                <input type="text" data-min-date="{if isset($delivery->delivery_min)}{$delivery->delivery_min|escape:'htmlall':'UTF-8'}{else}0{/if}" data-max-date="{if isset($delivery->delivery_max)}{$delivery->delivery_max|escape:'htmlall':'UTF-8'}{else}0{/if}" name="delivery_date" class="nt_delivery_date" value="{if isset($delivery->delivery_min)}{$delivery->delivery_min|escape:'htmlall':'UTF-8'}{/if}" />
            </span>
        </p>
    </div>
</div>