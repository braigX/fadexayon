{*
* 2007-2019 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innova Deluxe SL
* @copyright 2019 Innova Deluxe SL

* @license   INNOVADELUXE
*}
{if $component->options_lang}
    {assign var=first value = $component->options_lang|@key}
    {foreach $component->options_lang[$first].options as $key => $option}
    <div class='panel col-lg-12' id='js_optionpanel_{$component->id_component|intval}_{$option->id|intval}'>
        <div class="panel-heading"><i class="icon icon-arrows icon-2x"></i> {l s='Click and grab to order elements' mod='idxrcustomproduct'}</div>
        <div class="panel-body">
            <div class="col-lg-5">

                <label class="control-label col-lg-5" for="file_{$option->id|intval}">{l s='Image' mod='idxrcustomproduct'}</label>
                <div class="col-lg-7">
                   <img src="{$images_dir|escape:'htmlall':'UTF-8'}options/{$component->id_component|intval}_{$option->id|intval}.{$option->img_ext|escape:'htmlall':'UTF-8'}" width="75px" alt="Thumbnail Alt">
                </div>
                <div class="form-group col-lg-12">
                    <input id="file_{$component->id_component|intval}_{$option->id|intval}" class="sel_img_file_input" type="file"/>
                </div>

                <div class="form-group col-lg-12">
                    <label class="control-label col-lg-3">{l s='Name' mod='idxrcustomproduct'}</label>
                    <div class="col-lg-9">
                        <div class="form-group">
                            {foreach $languages as $language}
                                {if $languages|count > 1}
                                    <div class="translatable-field lang-{$language.id_lang|intval}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
                                        <div class="col-lg-9">
                                        {/if}
                                        <input type="text"
                                               id="optionname_{$option->id|intval}_{$language.id_lang|intval}"
                                               name="optionname_{$option->id|intval}_{$language.id_lang|intval}"
                                               class=""
                                               {if isset($component->options_lang[$language.id_lang].options[$key])}
                                               value="{$component->options_lang[$language.id_lang].options[$key]->name|escape:'htmlall':'UTF-8'}"
                                               {/if}
                                               />
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
                    </div>
                </div>

                <div class="form-group col-lg-12">
                    <label class="control-label col-lg-3">{l s='Description' mod='idxrcustomproduct'}</label>
                    <div class="col-lg-9">

                        <div class="form-group">

                            {foreach $languages as $language}
                                {if $languages|count > 1}
                                    <div class="translatable-field lang-{$language.id_lang|intval}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
                                        <div class="col-lg-9">
                                        {/if}                                
                                        <input type="text"
                                               id="optiondesc_{$option->id|intval}_{$language.id_lang|intval}"
                                               name="optiondesc_{$option->id|intval}_{$language.id_lang|intval}"
                                               class=""
                                               {if isset($component->options_lang[$language.id_lang].options[$key]->description)}
                                               value="{$component->options_lang[$language.id_lang].options[$key]->description|escape:'htmlall':'UTF-8'}"
                                               {/if}
                                               />
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
                    </div>
                </div>

                <div class="form-group col-lg-12">
                    <label class="control-label col-lg-3" for="option_reference">{l s='Reference' mod='idxrcustomproduct'}</label>
                    <div class="col-lg-9">
                        <input id="option_reference" name="option_reference" value="{$option->reference|escape:'htmlall':'UTF-8'}" type="text">
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="form-group col-lg-12">
                    <label class="control-label col-lg-5" for="productattachtype_{$option->id|intval}"> {l s='Attach product' mod='idxrcustomproduct'} </label>
                    <section class="col-lg-7">
                        <input class="productattachtype_switch option_switch" type="checkbox" id="productattachtype_{$option->id|intval}" {if $option->attach_product_type != 'base'}checked value="1"{else}value="0"{/if}/>
                        <label class="productattachtype_switch_label option_switch_label" for="productattachtype_{$option->id|intval}"></label>                    
                        <span class="productattachtype_switch_p option_switch_p">{l s='Product' mod='idxrcustomproduct'}</span>
                        <span class="productattachtype_switch_p option_switch_p">{l s='Base product' mod='idxrcustomproduct'}</span>
                    </section>
                </div>
                <div class="form-group col-lg-12">
                    <label class="control-label col-lg-5" for="option_product_attached"> {l s='Attach product' mod='idxrcustomproduct'} </label>
                    <div class="col-lg-7">
                        <fieldset class="form-group col-md-12 idxrcustomproduct-autocomplete" {if $option->attach_product_type == 'base'}style="display: none;"{/if}>
                            <div                            
                                class="autocomplete-search"
                                data-formid="option_{$option->id}_product_attached"
                                data-fullname="option_{$option->id}_product_attached"
                                data-function="product_select"
                                data-mappingvalue="id"
                                data-mappingname="name"
                                data-remoteurl="{$urlAjax|escape:'html':'UTF-8'}&action=searchProducts&q=%QUERY"
                                data-limit="0"
                                >
                                <div class="search search-with-icon">
                                    <input 
                                        type="text" 
                                        id="option_{$option->id}_product_attached" 
                                        class="form-control search typeahead idx_product_select" 
                                        placeholder="{l s='Not product attached' mod='idxrcustomproduct'}" 
                                        {if $option->attach_product_name}value="{$option->attach_product_name}"{/if} 
                                        autocomplete="off"
                                    >
                                </div>
                            </div>
                        </fieldset>
                        <input type="hidden" id="option_{$option->id}_product_attached_value" value="{$option->attach_product}"/>

                    </div>
                </div>
                <div class="form-group col-lg-12">
                    <label class="control-label col-lg-5" for="option_product_qty"> {l s='Quantity' mod='idxrcustomproduct'} </label>
                    <div class="col-lg-7">
                        <input id="option_product_qty" name="option_product_qty" type="number" min="1" step=1 value="{$option->attach_product_qty|escape:'htmlall':'UTF-8'}">
                    </div>
                </div>
                
                <div class="form-group col-lg-12">
                    <label class="control-label col-lg-5" for="priceimpacttype_{$option->id|intval}"> {l s='Fixed impact or calculated' mod='idxrcustomproduct'} </label>
                    <section class="col-lg-7">
                        <input class="priceimpacttype_switch" type="checkbox" id="priceimpacttype_{$option->id|intval}" {if $option->price_impact_type != 'fixed'}checked value="1"{else}value="0"{/if}/>
                        <label class="priceimpacttype_switch_label" for="priceimpacttype_{$option->id|intval}"></label>                    
                        <span class="priceimpacttype_switch_p">{l s='Calculated' mod='idxrcustomproduct'}</span>
                        <span class="priceimpacttype_switch_p">{l s='Fixed impact' mod='idxrcustomproduct'}</span>
                    </section>
                </div>
                    
                <div class="form-group col-lg-12 priceimpact_block" {if $option->price_impact_type != 'fixed'}style="display: none;"{/if}>
                    <label class="control-label col-lg-5" for="option_priceimpact_{$option->id|intval}"> {l s='Impact in the price' mod='idxrcustomproduct'} {$default_currency->sign} 
                        {if $impactTax}{l s='(tax incl)' mod='idxrcustomproduct'}
                        {else}{l s='(tax excl)' mod='idxrcustomproduct'}{/if}</label>
                    <div class="col-lg-7">
                        <input id="option_priceimpact_{$option->id|intval}" name="option_priceimpact" type="number" step=0.01 value="{$option->price_impact|escape:'htmlall':'UTF-8'}">
                    </div>
                </div>
                <div class="form-group col-lg-12 priceimpact_block" {if $option->price_impact_type != 'fixed'}style="display: none;"{/if}>
                    <label class="control-label col-lg-5" for="option_priceimpact_wodiscount_{$option->id|intval}"> {l s='Impact without discount' mod='idxrcustomproduct'} {$default_currency->sign} 
                        {if $impactTax}{l s='(tax incl)' mod='idxrcustomproduct'}
                        {else}{l s='(tax excl)' mod='idxrcustomproduct'}{/if}</label>
                    <div class="col-lg-7">
                        <input id="option_priceimpact_wodiscount_{$option->id|intval}" name="option_priceimpact_wodiscount" type="number" step=0.01 value="{$option->price_impact_wodiscount|escape:'htmlall':'UTF-8'}">
                    </div>
                </div>
                    
                <div class="form-group col-lg-12 calcpriceimpact_block" {if $option->price_impact_type == 'fixed'}style="display: none;"{/if}>
                    <label class="control-label col-lg-5" for="option_priceimpactcalc_{$option->id|intval}"> {l s='Impact in the price' mod='idxrcustomproduct'} </label>
                    <div class="col-lg-7">
                        <p class="help-block">{l s='Avalible vars' mod='idxrcustomproduct'} [BasePrice], [AttachedProductPrice]</p>
                        <input id="option_priceimpactcalc_{$option->id|intval}" name="option_priceimpact" type="text" value="{$option->price_impact_calc|escape:'htmlall':'UTF-8'}" placeholder="0.1 * [BasePrice]">
                    </div>
                </div>

                <div class="form-group col-lg-12">
                    <label class="control-label col-lg-5" for="option_weightimpact"> {l s='Impact in the weight' mod='idxrcustomproduct'} </label>
                    <div class="col-lg-7">
                        <input id="option_weightimpact" name="option_weightimpact" value="{$option->weight_impact|escape:'htmlall':'UTF-8'}" type="number" step=0.001>
                    </div>
                </div>
                
                {include file="./partials/tax_selector.tpl"}
            </div>


            <div class="col-lg-2">
                <span class="btn btn-default upd_option btn-block" data-option='{$option->id|intval}' data-component='{$component->id_component|intval}'><i class="icon icon-refresh"></i> {l s='Update' mod='idxrcustomproduct'}</span>
                <span class="btn btn-danger del_option btn-block" data-option='{$option->id|intval}' data-component='{$component->id_component|intval}'><i class="icon icon-trash"></i> {l s='Delete' mod='idxrcustomproduct'}</span>
                <section>
                    <input class="default_switch" type="checkbox" id="default_{$option->id|intval}" {if $option->id == $component->default_opt}checked{/if}/>
                    <label class="default_switch_label" for="default_{$option->id|intval}"></label>
                    <span class="default_switch_p">{l s='Checked as default value' mod='idxrcustomproduct'}</span>
                    <span class="default_switch_p">{l s='Not selected as default' mod='idxrcustomproduct'}</span>
                </section>
            </div>
        </div>
    </div>
    {/foreach}
{else}
    {l s='Actually this component haven\'t any option' mod='idxrcustomproduct'}
{/if}