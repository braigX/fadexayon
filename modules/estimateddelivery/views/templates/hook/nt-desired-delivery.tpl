{*
* Estimated Delivery - Front Office Feature
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

<div class="row nt-mt-1">
    <div class="col-md-12">
       <p class="request-text-small"> 
            <span class="nt-blue-color">{l s='Wunschlieferdatum ' mod='estimateddelivery'}</span>
            <span>{l s='wahlen:' mod='estimateddelivery'}</span>
            <span class="nt_picker_container">
                <i class="fa fa-calendar nt-blue-color" aria-hidden="true"></i>
                <input type="text" data-min-date="{if isset($delivery->delivery_min)}{$delivery->delivery_min|escape:'htmlall':'UTF-8'}{else}0{/if}" data-max-date="{if isset($delivery->delivery_max)}{$delivery->delivery_max|escape:'htmlall':'UTF-8'}{else}0{/if}" name="delivery_date" id="nt_delivery_date" value="{if isset($delivery->delivery_min)}{$delivery->delivery_min|escape:'htmlall':'UTF-8'}{/if}" />
            </span>
        </p>
    </div>
</div>