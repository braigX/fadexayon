{**
* 2007-2022 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innova Deluxe SL
* @copyright 2022 Innova Deluxe SL
* @license   INNOVADELUXE
*}

                <input 
                    {if $step.multivalue == 'unique' || $step.multivalue == 'unique_qty'}type="radio" {else} type="checkbox" {/if}
                    name="option_{$step.id_component|escape:'htmlall':'UTF-8'}"
                    data-value="{$option->id|intval}" 
                    data-price-impact='{$option->price_impact|escape:'htmlall':'UTF-8'}'
                    {if isset($option->price_impact_wodiscount)}
                    data-price-impact-wodiscount='{$option->price_impact_wodiscount|escape:'htmlall':'UTF-8'}'
                    {/if}
                    {assign var=impact_text value=''}
                    {foreach from=$step.impact_options item=impactopt}
                    {assign var="currentopt" value=$step.id_component|cat:'_'|cat:$option->id nocache}
                    {if $impactopt.option_impacted == $currentopt}
                        {assign var=temp value=$impact_text}
                        {if $impactopt.impact_percent > 0}
                            {assign var=impact_text value="{$impactopt.option_trigger}p{$impactopt.impact_percent}|{$temp}"}
                        {else}
                            {assign var=impact_text value="{$impactopt.option_trigger}f{$impactopt.impact_fixed}|{$temp}"}
                        {/if}
                    {/if}
                    {/foreach}
                    data-price-option-impact='{$impact_text}'
                    data-weight-impact='{$option->weight_impact|escape:'htmlall':'UTF-8'}' 
                    data-reference='{$option->reference|escape:'htmlall':'UTF-8'}' 
                    data-att-product='{if isset($option->att_product)}{$option->att_product|escape:'htmlall':'UTF-8'}{else}{/if}'
                    id="option_{$step.id_component|escape:'htmlall':'UTF-8'}_{$option->id|intval}" 
                    class="hidden js_icp_option chk_{$step.type|escape:'htmlall':'UTF-8'}" 
                    {if $smarty.foreach.foo.iteration > 1}disabled{/if}
                />