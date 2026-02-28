{**
* 2007-2022 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innova Deluxe SL
* @copyright 2022 Innova Deluxe SL
* @license   INNOVADELUXE
*}

<div class="form-group col-lg-12">
    <label class="control-label col-lg-5" for="option_taxchange"> {l s='Change product tax' mod='idxrcustomproduct'} </label>
    <div class="col-lg-7">
        <select class="form-select" name="option_taxchange" id="option_taxchange_{$option->id|intval}">
            <option value="false" {if !$option->tax_change}selected="selected"{/if}>{l s='Inherit from base' mod='idxrcustomproduct'}</option>
            {foreach from=$taxes item=tax}
                <option value="{$tax.id_tax}" {if $tax.id_tax == $option->tax_change}selected="selected"{/if}>{$tax.name}</option>
            {/foreach}
        </select>
    </div>
</div>
