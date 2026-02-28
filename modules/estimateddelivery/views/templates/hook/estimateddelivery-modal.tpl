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

{if $edstyle < 2}
    {include file='./estimateddelivery.tpl'}
{elseif $edstyle <= 2 && $edstyle >= 3}
    {include file='./estimateddelivery-ob.tpl'}
{elseif $edstyle == 4}
    {include file='./estimateddelivery-ob-picking-day.tpl'}
{elseif $edstyle == 5}
    {include file='./estimateddelivery-double-display.tpl'}
{/if}

{*
{if $ed_popup}
    {include file='./estimateddelivery-popup.tpl'}
{/if}
*}