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
 * *                                                 *
 * ***************************************************
 *}

<div class="panel" id="{$ed_type|escape:'htmlall':'UTF-8'}_{$ed_method|escape:'htmlall':'UTF-8'}">
    {if $ed_results|count gt 0}
        <ul class="cattree tree">
            {foreach from=$ed_results item=result}
                {capture assign="tmp_id"}id_{$ed_type|escape:'htmlall':'UTF-8'}{/capture}
                <li>
                    {if $ed_input_type == 'checkbox'}
                        <input type="checkbox"
                               id="{$ed_method|escape:'htmlall':'UTF-8'}{Tools::ucfirst($ed_type)}_{$result[$tmp_id]|intval}"
                               name="{$ed_method|escape:'htmlall':'UTF-8'}_{$ed_type|escape:'htmlall':'UTF-8'}[{$result[$tmp_id]|intval}]"
                               {if $result.undefined_delivery}checked="checked"{/if}/>
                    {/if}
                    <label for="{$ed_method|escape:'htmlall':'UTF-8'}{Tools::ucfirst($ed_type)}_{$result[$tmp_id]|intval}">
                        <span>{$result['name']|escape:'htmlall':'UTF-8'}</span>
                    </label>
                    {if $ed_input_type == 'text'}
                        <input type="text"
                               name="{$ed_method|escape:'htmlall':'UTF-8'}{Tools::ucfirst($ed_type)}[{$result[$tmp_id]|intval}]"
                               value="{$result[$ed_column]|escape:'htmlall':'UTF-8'}"/>
                    {/if}
                </li>
            {/foreach}
        </ul>
    {else}
        {capture assign="method_name"}
            {if $ed_method == 'supplier'}
                {l s='Suppliers' mod='estimateddelivery'}
            {else}
                {l s='Manufacturers' mod='estimateddelivery'}
            {/if}
        {/capture}
        <p>{l s='Haven\'t found any %s to show here, please chose another option or create and configure them' mod='estimateddelivery' sprintf=[$method_name]}</p>
    {/if}
</div>