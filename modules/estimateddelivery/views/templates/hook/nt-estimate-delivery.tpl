{*
/** * Estimated Delivery - Front Office Feature
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
*/
*}

<div class="form-group row">
    <div class="col-md-4">
       <p class="request-text-small"> <i class="fa fa-truck" aria-hidden="true"></i>
 {l s='Liferdatum:' mod='estimateddelivery'}</p>
    </div>
    <div class="col-md-8">
        {l s='bei heutiger bestellung' mod='estimateddelivery'}
        <span class="row">
            {capture name="estimateddelivery"}
                <div class="ed_product_summary estimateddelivery{if $edclass != ''} {$edclass|escape:'htmlall':'UTF-8'}{/if}" {if $edclass == 'custom'}style="background:{$edbackground|escape:'htmlall':'UTF-8'};border:1px solid {$edborder|escape:'htmlall':'UTF-8'}"{/if}>
                    <div>
                        <p class="ed_orderbefore">
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
                                    <span title="{$delivery->name|escape:'htmlall':'UTF-8'}">{l s='between' mod='estimateddelivery'} <span><strong class="date_green">{$delivery->delivery_min|escape:'htmlall':'UTF-8'}</strong></span> {l s='and' mod='estimateddelivery'} <span><strong class="date_green">{$delivery->delivery_max|escape:'htmlall':'UTF-8'}</strong></span>
                                {/if}
                                <span class="ed_with_carrier"> {l s='with' mod='estimateddelivery'} <strong>{$delivery->name|escape:'htmlall':'UTF-8'}</strong></span></span>
                            {/if}
                            {if $more_options}*</span>{/if}
                        </p>
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
        </span>
    </div>
</div>