{**
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innova Deluxe SL
* @copyright 2015 Innova Deluxe SL
* @license   INNOVADELUXE
*}

<div class="panel col-lg-12">
    <div class="panel-heading"> 
        {l s='Component options' mod='idxrcustomproduct'} - {$component->name|escape:'htmlall':'UTF-8'}
    </div>
    <form action="#" id="option-form" method="post" enctype="multipart/form-data" target="upload_target" class="defaultForm form-horizontal">
 
        <div class="form-group">
            <label class="control-label col-lg-3">{l s='Name for the new option' mod='idxrcustomproduct'}</label>
            <div class="col-lg-9">

                <div class="form-group">

                {foreach $languages as $language}
                    {if $languages|count > 1}
                    <div class="translatable-field lang-{$language.id_lang|intval}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
                        <div class="col-lg-9">
                    {/if}   
                            <input type="text"
                                id="optionname_{$language.id_lang|intval}"
                                name="optionname_{$language.id_lang|intval}"
                                class=""
                                value=""/>
                    {if $languages|count > 1}
                        </div>
                        <div class="col-lg-2">
                                <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                                        {$language.iso_code|escape:'htmlall':'UTF-8'}
                                        <i class="icon-caret-down"></i>
                                </button>
                                <ul class="dropdown-menu">
                                        {foreach from=$languages item=language}
                                        <li><a href="javascript:hideOtherLanguage({$language.id_lang|intval});" tabindex="-1">{$language.name|escape:'htmlall':'UTF-8'}</a></li>
                                        {/foreach}
                                </ul>
                        </div>
                    </div>
                    {/if}
                {/foreach}
                </div>
                <p class="help-block"> {l s='Enter a name for this option' mod='idxrcustomproduct'} </p>
            </div>
        </div>
            
        <div class="form-group">
            <label class="control-label col-lg-3">{l s='Description for the new option' mod='idxrcustomproduct'}</label>
            <div class="col-lg-9">
                
                <div class="form-group">
                
                {foreach $languages as $language}
                    {if $languages|count > 1}
                    <div class="translatable-field lang-{$language.id_lang|intval}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
                        <div class="col-lg-9">
                    {/if}   
                            <textarea 
                                id="optiondesc_{$language.id_lang|intval}"
                                name="optiondesc_{$language.id_lang|intval}"
                                class=""
                                value=""/></textarea>
                    {if $languages|count > 1}
                        </div>
                        <div class="col-lg-2">
                                <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                                        {$language.iso_code|escape:'htmlall':'UTF-8'}
                                        <i class="icon-caret-down"></i>
                                </button>
                                <ul class="dropdown-menu">
                                        {foreach from=$languages item=language}
                                        <li><a href="javascript:hideOtherLanguage({$language.id_lang|intval});" tabindex="-1">{$language.name|escape:'htmlall':'UTF-8'}</a></li>
                                        {/foreach}
                                </ul>
                        </div>
                    </div>
                    {/if}
                {/foreach}
                </div>
                <p class="help-block"> {l s='Enter a little description for this option' mod='idxrcustomproduct'} </p>
            </div>
        </div>
        
        {if $component->type == 'sel_img'}   
        <div class="form-group">        
            <label class="control-label col-lg-3" for="myfile"> {l s='Image for this option' mod='idxrcustomproduct'} </label>
            <div class="col-lg-9"> 
                <p id="f1_upload_process" style='display:none'>Loading...<br/></p>
                <p id="f1_upload_form">
                    <input name="myfile" type="file" size="30" />
                </p>
            </div>
        </div>
        {/if}
            
        {if $component->type == 'sel_img' || $component->type == 'sel'}
        <div class="form-group">
            <label class="control-label col-lg-3" for="option_priceimpact"> {l s='Impact in the price' mod='idxrcustomproduct'} </label>
            <div class="col-lg-3">
                <input id="option_priceimpact" name="option_priceimpact" value="" type="number" step="0.01" /> {$default_currency->sign}
                {if $impactTax}{l s='(tax incl)' mod='idxrcustomproduct'}
                {else}{l s='(tax excl)' mod='idxrcustomproduct'}{/if}
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-lg-3" for="option_priceimpact_wodiscount"> {l s='Impact price without discount' mod='idxrcustomproduct'} </label>
            <div class="col-lg-3">
                <input id="option_priceimpact_wodiscount" name="option_priceimpact_wodiscount" value="" type="number" step="0.01" /> {$default_currency->sign}
                {if $impactTax}{l s='(tax incl)' mod='idxrcustomproduct'}
                {else}{l s='(tax excl)' mod='idxrcustomproduct'}{/if}
                <p class="help-block">{l s='leave blank if you do not want to show discount on impact' mod='idxrcustomproduct'}</p>
            </div>
        </div>
        {/if}
            
        {if $component->type == 'sel_img'  || $component->type == 'sel'}
        <div class="form-group">
            <label class="control-label col-lg-3" for="option_weightimpact"> {l s='Impact in the weight' mod='idxrcustomproduct'} </label>
            <div class="col-lg-3">
                <input id="option_weightimpact" name="option_weightimpact" value="" type="number" step="0.001" />
            </div>
        </div>
        {/if}
            
        {if $component->type == 'sel_img'  || $component->type == 'sel'}
        <div class="form-group">
            <label class="control-label col-lg-3" for="option_reference"> {l s='Change the reference' mod='idxrcustomproduct'} </label>
            <div class="col-lg-3">
                <input id="option_reference" name="option_reference" value="" type="text" />
            </div>
        </div>
        {/if}
        
        {if $component->type == 'sel_img'  || $component->type == 'sel'}
        <div class="form-group">
            <label class="control-label col-lg-3" for="option_product_qty"> {l s='Quantity per custom product unit' mod='idxrcustomproduct'} </label>
            <div class="col-lg-9">
                <input id="option_product_qty" name="option_product_qty" value="" type="number" min="1" step="1" />
                <p class="help-block">{l s='Select the quantity of stock decremented per every customized product with this option selected' mod='idxrcustomproduct'}</p>
            </div>            
        </div>
        {/if}
            
        <div class="col-lg-9 col-lg-offset-3">
            <div class="form-group">                                        
                <div class="col-lg-9">                        
                    <input type="submit" class='btn btn-primary' name="submitBtn" value="{l s='Add the option' mod='idxrcustomproduct'}" />
                </div>
            </div>
        </div>
        <input type="hidden" name="componentid" value="{$component->id_component|intval}" />
    </form>
    <div id="option_list" class="col-lg-12">
        {if isset($component->options_lang)}
            <h3>{l s='Options for this component' mod='idxrcustomproduct'}</h3>
            <div id="option_list_sortable">
            {if $component->type == 'sel_img'}
                {include file="./sel_img.tpl" component=$component languages=$languages}
            {elseif $component->type == 'sel'}
                {include file="./sel.tpl" component=$component languages=$languages}
            {/if}
            </div>
        {/if}
    </div>
</div>