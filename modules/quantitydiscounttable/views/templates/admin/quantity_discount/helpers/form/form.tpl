{*
 * DISCLAIMER
 *
 * Do not edit or add to this file.
 * You are not authorized to modify, copy or redistribute this file.
 * Permissions are reserved by FME Modules.
 *
 *  @author    FMM Modules
 *  @copyright FME Modules 2024
 *  @license   Single domain
*}
{extends file="helpers/form/form.tpl"}
{block name="input_row"}
{if $input.name == 'product_id'}
    <div class="form-group" id="pquote_product_list" style="display: block">
    <br/>
    <label class="control-label {if $ps_version == 1} col-lg-4 {else} col-lg-3{/if}">{l s='Select Products' mod='quantitydiscounttable'}</label>

    <div class=" {if $ps_version == 1} col-lg-8 {else} col-lg-9{/if}">
        <div class="fmm_relative col-lg-6 placeholder_holder">
            <input type="text" placeholder="{l s='Example' mod='quantitydiscounttable'}: Blue XL shirt" onkeyup="getRelProducts(this);"  required/>
            <p class="help-block">{l s='Find Products to Add Specific Pricing Rule' mod='quantitydiscounttable'}</p>
            <div class="" id="rel_holder"></div>
            <div id="rel_holder_temp">
                <ul>
                    {if (!empty($products))}
                        {foreach from=$products item=product}
                            <li id="row_{$product->id|escape:'htmlall':'UTF-8'}" class="media">
                                <div class="media-left">
                                    <img src="{Context::getContext()->link->getImageLink($product->link_rewrite, $product->id_image, 'home_default')|escape:'htmlall':'UTF-8'}" class="media-object image">
                                </div>
                                <div class="media-body media-middle">
                                    <span class="label">{$product->name|escape:'htmlall':'UTF-8'}&nbsp;(ID:{$product->id|escape:'htmlall':'UTF-8'})</span><i onclick="relDropThis(this);" class="material-icons delete">clear</i>
                                </div>
                                <input type="hidden" value="{$product->id|escape:'htmlall':'UTF-8'}" name="product_id[]">
                            </li>
                        {/foreach}
                    {/if}
                </ul>
            </div>
        </div>
    </div>
</div>
{/if}
{$smarty.block.parent}
{/block}