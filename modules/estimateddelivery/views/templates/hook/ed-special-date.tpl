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
{* Normalize the Delivery object if the special message has to be printed *}

{function printEstimatedMessage message='' date=''}
    <p class="ed_orderbefore">{$message|escape:'htmlall':'UTF-8'|replace:'{date}':$date}</p>
{/function}

{if is_array($delivery)}
    {assign var=deli value=$delivery[0]}
{else}
    {assign var=deli value=$delivery}
{/if}
{if ($deli->dp->is_available || $deli->dp->is_release || $deli->dp->is_custom)}
    {if $deli->dp->is_available || $deli->dp->is_release}
        {printEstimatedMessage message=$deli->dp->msg date=$deli->dp->formatted_date}
    {elseif $deli->dp->add_custom_days|intval > 0}
        {printEstimatedMessage message=$deli->dp->msg date=$deli->dp->add_custom_days}
    {else}
        {printEstimatedMessage message=$deli->dp->msg date=$deli->dp->formatted_date}
    {/if}
{elseif $deli->dp->is_virtual || $deli->dp->is_undefined_delivery}
    {if $deli->dp->msg != ''}
        {printEstimatedMessage message=$deli->dp->msg msg = ''}
    {/if}
{/if}
