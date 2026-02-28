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

<div class="hooks-management">
    {foreach $hooks as $hook}
    {if $hook.is_section}
    {if isset($previous_section) && $previous_section}
        </tbody>
        </table>
        <br><br><br>
    {/if}
    <h3 class="hook-section modal-title text-info">{$hook.context}</h3>
    <table class="table">
        <thead>
        <tr>
            <th>{$smarty.const.PS_YES|escape:'html'}</th>
            <th>{$smarty.const.PS_NO|escape:'html'}</th>
            <th>{$smarty.const.PS_DESCRIPTION|escape:'html'}</th>
        </tr>
        </thead>
        <tbody>
        {assign var='previous_section' value=true}
        {else}
        <tr>
            <td>
                    <span class="switch prestashop-switch fixed-width-md">
                        <input type="radio"
                               name="hook_{$hook.hook}"
                               id="hook_{$hook.hook}_on"
                               value="1"
                               {if $hook.is_enabled}checked{/if}
                               class="hook-toggle"
                               data-hook="{$hook.hook}" />
                        <label for="hook_{$hook.hook}_on">{$smarty.const.PS_YES|escape:'html'}</label>
                        <input type="radio"
                               name="hook_{$hook.hook}"
                               id="hook_{$hook.hook}_off"
                               value="0"
                               {if !$hook.is_enabled}checked{/if}
                               class="hook-toggle"
                               data-hook="{$hook.hook}" />
                        <label for="hook_{$hook.hook}_off">{$smarty.const.PS_NO|escape:'html'}</label>
                        <a class="slide-button btn"></a>
                    </span>
            </td>
            <td><strong>{$hook.hook|escape:'html'}</strong></td>
            <td><span class="help-block">{$hook.description|escape:'html'}</span></td>
        </tr>
        {/if}
        {/foreach}
        {if isset($previous_section) && $previous_section}
        </tbody>
    </table>
    {/if}
</div>
