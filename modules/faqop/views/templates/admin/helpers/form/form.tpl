{*
* Google-Friendly FAQ Pages and Lists With Schema Markup module
*
*    @author    Opossum Dev
*    @copyright Opossum Dev
*    @license   You are just allowed to modify this copy for your own use. You are not allowed
* to redistribute it. License is permitted for one Prestashop instance only, except for test
* instances.
*}

{extends file="helpers/form/form.tpl"}

{block name="label"}
    {if isset($input.label)}
        <label class="control-label col-lg-3{if isset($input.required) && $input.required && $input.type != 'radio'} required{/if}">
            {if isset($input.hint)}
            <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="{if is_array($input.hint)}
													{foreach $input.hint as $hint}
														{if is_array($hint)}
															{$hint.text|escape:'html':'UTF-8'}
														{else}
															{$hint|escape:'html':'UTF-8'}
														{/if}
													{/foreach}
												{else}
													{$input.hint|escape:'html':'UTF-8'}
												{/if}">
										{/if}
                {$input.label}
                {if isset($input.hint)}
										</span>
            {/if}
        </label>
    {/if}
{/block}
{block name="field"}
{if $input.type == 'text_hook_op'}
<div class="col-lg-9">
    <div class="custom-hook-text-wrap">
        <input type="text" name="{$input.name}" id="{$input.name}" class="{$input.class} readonly-focus"
               value="{$fields_value[$input.name]}"
               readonly>
        <i id="clear-custom-hook" class="icon-eraser clear-custom-hook" title="Clear input"></i>
    </div>
</div>
{elseif $input.type == 'smarty_hook_op'}
<div class="col-lg-9">
    <input type="text" name="{$input.name}" id="{$input.name}" class="shortcode-readonly smarty-code"
           value="{literal}{{/literal}hook h='{$fields_value[$input.name]}'{literal}}{/literal}"
           readonly>
</div>
{elseif $input.type == 'select_op'}
<div class="col-lg-3"></div>
<div class="col-lg-9">
    <div class="form-group form-group-custom-hook">
        <div id="form-group-select-hook-label"
             class="form-group-custom-hook-label form-group-hook-label-active"><span
                    class="form-group-custom-hook-label-span">{l s='Select hook' mod='faqop'}&nbsp;
                        <i class="icon-angle-down cuop-arrow-down" style="display:none"></i>
                        <i class="icon-angle-up cuop-arrow-up"></i>
                    </span></div>
        <div id="form-group-select-hook-body" class="form-group-custom-hook-body">
            {if isset($input.options.query) && !$input.options.query && isset($input.empty_message)}
                {$input.empty_message}
                {$input.required = false}
                {$input.desc = null}
            {else}
                <select name="{$input.name|escape:'html':'UTF-8'}"
                        class="{if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if} fixed-width-xl op-select"
                        id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}"
                        {if isset($input.multiple) && $input.multiple} multiple="multiple"{/if}
                        {if isset($input.size)} size="{$input.size|escape:'html':'UTF-8'}"{/if}
                        {if isset($input.onchange)} onchange="{$input.onchange|escape:'html':'UTF-8'}"{/if}
                        {if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}
                >
                    {if isset($input.options.default)}
                        <option value="{$input.options.default.value|escape:'html':'UTF-8'}">{$input.options.default.label|escape:'html':'UTF-8'}</option>
                    {/if}
                    {if isset($input.options.optiongroup)}
                        {foreach $input.options.optiongroup.query AS $optiongroup}
                            <optgroup label="{$optiongroup[$input.options.optiongroup.label]}">
                                {foreach $optiongroup[$input.options.options.query] as $option}
                                    <option value="{$option[$input.options.options.id]}"
                                            {if isset($input.multiple)}
                                                {foreach $fields_value[$input.name] as $field_value}
                                                    {if $field_value == $option[$input.options.options.id]}selected="selected"{/if}
                                                {/foreach}
                                            {else}
                                                {if $fields_value[$input.name] == $option[$input.options.options.id]}selected="selected"{/if}
                                            {/if}
                                    >{$option[$input.options.options.name]}</option>
                                {/foreach}
                            </optgroup>
                        {/foreach}
                    {else}
                        {foreach $input.options.query AS $option}
                            {if is_object($option)}
                                <option value="{$option->$input.options.id}"
                                        {if isset($input.multiple)}
                                            {foreach $fields_value[$input.name] as $field_value}
                                                {if $field_value == $option->$input.options.id}
                                                    selected="selected"
                                                {/if}
                                            {/foreach}
                                        {else}
                                            {if $fields_value[$input.name] == $option->$input.options.id}
                                                selected="selected"
                                            {/if}
                                        {/if}
                                >{$option->$input.options.name}</option>
                            {elseif $option == "-"}
                                <option value="">-</option>
                            {else}
                                <option value="{$option[$input.options.id]}"
                                        {if isset($input.multiple)}
                                            {foreach $fields_value[$input.name] as $field_value}
                                                {if $field_value == $option[$input.options.id]}
                                                    selected="selected"
                                                {/if}
                                            {/foreach}
                                        {else}
                                            {if $fields_value[$input.name] == $option[$input.options.id]}
                                                selected="selected"
                                            {/if}
                                        {/if}
                                >{$option[$input.options.name]}</option>
                            {/if}
                        {/foreach}
                    {/if}
                </select>
            {/if}
        </div>
    </div>
    <div class="form-group form-group-custom-hook">
        <div id="form-group-custom-hook-label" class="form-group-custom-hook-label"><span
                    class="form-group-custom-hook-label-span">{l s='Or create your custom hook' mod='faqop'}&nbsp;
                        <i class="icon-angle-down cuop-arrow-down"></i>
                        <i class="icon-angle-up cuop-arrow-up" style="display:none"></i>
                    </span></div>
        <div id="form-group-custom-hook-body" class="form-group-custom-hook-body" style="display:none">
            <div id="ajax_add_custom_hook" class="ajax-add_op">
                <input type="text" value="" id="custom_hook_name_input" class="id-input_op custom-hook-input"
                       autocomplete="off"/>
                <button type="button" id="custom_hook_name_add" class="btn btn-primary custom_hook_name_add">
                    <i class="fas fa-spinner fa-spin spin-process spin-process-inline" style="display:none"></i>
                    <span class="spinner-hide-txt">{l s='ADD' mod='faqop'}</span>
                </button>
            </div>
            <div id="block-for-error-hook">
                <div class="op-error-wrap" style="display:none"></div>
            </div>
            <span class="form-group-custom-hook-small-letters">{l s='Write your custom hook name and click "ADD" button (Do NOT press Enter!)' mod='faqop'}</span>
            <br>
            <span class="form-group-custom-hook-small-letters">
                {l s='Symbols allowed only: [a-zA-Z0-9_-]' mod='faqop'}</span>
            <br>
            <span class="form-group-custom-hook-small-letters">
                {l s='May not contain words: action, admin, delete, filter, hook, object, save, update, validate'
                mod='faqop'}</span>
        </div>

    </div>
</div>
{elseif $input.type == 'radio_op'}
<div class="col-lg-9">
    {foreach $input.values as $value}
        <div class="radio op-radio {if isset($input.class)}{$input.class}{/if}">
            {strip}
                <label>
                    <input type="radio" name="{$input.name}" id="{$value.id}"
                           value="{$value.value|escape:'html':'UTF-8'}"{if $fields_value[$input.name] == $value.value} checked="checked"{/if}{if (isset($input.disabled) && $input.disabled) or (isset($value.disabled) && $value.disabled)} disabled="disabled"{/if}/>
                    <span>{$value.label}</span>
                    <i class="op-radio-control"></i>
                </label>
            {/strip}
        </div>
        {if isset($value.p) && $value.p}<p class="help-block">{$value.p}</p>{/if}
    {/foreach}
</div>
{elseif $input.type == 'op_products'}
<div class="col-lg-9 margin-form">
    <div class="form-group">
        <div id="products-choose-block" class="col-lg-9">
            <input type="hidden" name="product_ids" id="product_ids" value="{$input.selected_products_raw}"/>

            <div id="ajax_add_product_id" class="ajax-add_op">

                <input type="text" value="" id="product_id_input" class="id-input_op" autocomplete="off"/>
                <button type="button" id="product_id_add" class="btn btn-primary"><i
                            class="fas fa-spinner fa-spin spin-process spin-process-inline"
                            style="display:none"></i>
                    <span class="spinner-hide-txt">{l s='OK' mod='faqop'}</span></button>

            </div>
            <div id="block-for-error-product">
                <div class="op-error-wrap" style="display:none"></div>
            </div>

            {if isset($input.descr) && !empty($input.descr)}
                <p class="help-block">
                    {$input.descr}
                </p>
            {/if}
            <div id="divProducts_op" class="divItems_op">
                {foreach $input.selected_products as $product}
                    <div class="divItem_op">
                        {$product.name} (id: {$product.id_product})
                        <span class="delProduct_op delItem_op" name="{$product.id_product}">
                                    <i class="fa fa-trash"></i>
                                </span>
                    </div>
                {/foreach}
            </div>
        </div>

    </div>


</div>
{elseif $input.type == 'op_categories'}
<div class="col-lg-9 margin-form">
    <div class="form-group">
        <div id="categories-choose-block" class="col-lg-9">
            <input type="hidden" name="category_ids" id="category_ids"
                   value="{$input.selected_categories_raw}"/>

            <div id="ajax_add_category_id" class="ajax-add_op">

                <input type="text" value="" id="category_id_input" class="id-input_op" autocomplete="off"/>
                <button type="button" id="category_id_add" class="btn btn-primary"><i
                            class="fas fa-spinner fa-spin spin-process spin-process-inline"
                            style="display:none"></i>
                    <span class="spinner-hide-txt">{l s='OK' mod='faqop'}</span></button>

            </div>
            <div id="block-for-error-category">
                <div class="op-error-wrap" style="display:none"></div>
            </div>
            {if isset($input.descr) && !empty($input.descr)}
                <p class="help-block">
                    {$input.descr}
                </p>
            {/if}
            <div id="divCategories_op" class="divItems_op">
                {foreach $input.selected_categories as $category}
                    <div class="divItem_op">
                        {$category.name} (id: {$category.id_category})
                        <span class="delCategory_op delItem_op" name="{$category.id_category}">
                                    <i class="fa fa-trash"></i>
                                </span>
                    </div>
                {/foreach}
            </div>
        </div>
    </div>
</div>
{elseif $input.type == 'op_brands'}
<div class="col-lg-9 margin-form">
    <div class="form-group">
        <div id="brands-choose-block" class="col-lg-9">
            <input type="hidden" name="brand_ids" id="brand_ids"
                   value="{$input.selected_brands_raw}"/>
            <div id="ajax_add_brand_id" class="ajax-add_op">
                <input type="text" value="" id="brand_id_input" class="id-input_op" autocomplete="off"/>
                <button type="button" id="brand_id_add" class="btn btn-primary"><i
                            class="fas fa-spinner fa-spin spin-process spin-process-inline"
                            style="display:none"></i>
                    <span class="spinner-hide-txt">{l s='OK' mod='faqop'}</span></button>
            </div>
            <div id="block-for-error-brand">
                <div class="op-error-wrap" style="display:none"></div>
            </div>
            {if isset($input.descr) && !empty($input.descr)}
                <p class="help-block">
                    {$input.descr}
                </p>
            {/if}
            <div id="divBrands_op" class="divItems_op">
                {foreach $input.selected_brands as $brand}
                    <div class="divItem_op">
                        {$brand.name} (id: {$brand.id_manufacturer})
                        <span class="delBrand_op delItem_op" name="{$brand.id_manufacturer}">
                                    <i class="fa fa-trash"></i>
                                </span>
                    </div>
                {/foreach}
            </div>
        </div>
    </div>
</div>
{elseif $input.type == 'op_brands_p'}
<div class="col-lg-9 margin-form">
    <div class="form-group">
        <div id="brands-p-choose-block" class="col-lg-9">
            <input type="hidden" name="brand_ids_p" id="brand_ids_p"
                   value="{$input.selected_brands_p_raw}"/>
            <div id="ajax_add_brand_p_id" class="ajax-add_op">
                <input type="text" value="" id="brand_p_id_input" class="id-input_op" autocomplete="off"/>
                <button type="button" id="brand_p_id_add" class="btn btn-primary"><i
                            class="fas fa-spinner fa-spin spin-process spin-process-inline"
                            style="display:none"></i>
                    <span class="spinner-hide-txt">{l s='OK' mod='faqop'}</span></button>
            </div>
            <div id="block-for-error-brand-p">
                <div class="op-error-wrap" style="display:none"></div>
            </div>
            {if isset($input.descr) && !empty($input.descr)}
                <p class="help-block">
                    {$input.descr}
                </p>
            {/if}
            <div id="divBrands_p_op" class="divItems_op">
                {foreach $input.selected_brands_p as $brand}
                    <div class="divItem_op">
                        {$brand.name} (id: {$brand.id_manufacturer})
                        <span class="delBrand_p_op delItem_op" name="{$brand.id_manufacturer}">
                                    <i class="fa fa-trash"></i>
                                </span>
                    </div>
                {/foreach}
            </div>
        </div>
    </div>
</div>
{elseif $input.type == 'op_tags_p'}
<div class="col-lg-9 margin-form">
    <div class="form-group">
        <div id="tags-choose-block" class="col-lg-9">
            <input type="hidden" name="tag_ids_p" id="tag_ids_p"
                   value="{$input.selected_tags_p_raw}"/>
            <div id="ajax_add_tag_p_id" class="ajax-add_op">
                <input type="text" value="" id="tag_p_id_input" class="id-input_op" autocomplete="off"/>
                <button type="button" id="tag_p_id_add" class="btn btn-primary"><i
                            class="fas fa-spinner fa-spin spin-process spin-process-inline"
                            style="display:none"></i>
                    <span class="spinner-hide-txt">{l s='OK' mod='faqop'}</span></button>
            </div>
            <div id="block-for-error-tag-p">
                <div class="op-error-wrap" style="display:none"></div>
            </div>
            {if isset($input.descr) && !empty($input.descr)}
                <p class="help-block">
                    {$input.descr}
                </p>
            {/if}
            <div id="divTags_p_op" class="divItems_op">
                {foreach $input.selected_tags_p as $tag}
                    <div class="divItem_op">
                        {$tag.name} (id: {$tag.id_tag})
                        <span class="delTag_p_op delItem_op" name="{$tag.id_tag}">
                                    <i class="fa fa-trash"></i>
                                </span>
                    </div>
                {/foreach}
            </div>
        </div>
    </div>
</div>
{elseif $input.type == 'op_features_p'}
<div class="col-lg-9 margin-form">
    <div class="form-group">
        <div id="features-choose-block" class="col-lg-9">
            <input type="hidden" name="feature_ids_p" id="feature_ids_p"
                   value="{$input.selected_features_p_raw}"/>
            <div id="ajax_add_feature_p_id" class="ajax-add_op">
                <input type="text" value="" id="feature_p_id_input" class="id-input_op" autocomplete="off"/>
                <button type="button" id="feature_p_id_add" class="btn btn-primary"><i
                            class="fas fa-spinner fa-spin spin-process spin-process-inline"
                            style="display:none"></i>
                    <span class="spinner-hide-txt">{l s='OK' mod='faqop'}</span></button>
            </div>
            <div id="block-for-error-feature-p">
                <div class="op-error-wrap" style="display:none"></div>
            </div>
            {if isset($input.descr) && !empty($input.descr)}
                <p class="help-block">
                    {$input.descr}
                </p>
            {/if}
            <div id="divFeatures_p_op" class="divItems_op">
                {foreach $input.selected_features_p as $feature}
                    <div class="divItem_op">
                        {$feature.name} (id: {$feature.id_feature})
                        <span class="delFeature_p_op delItem_op" name="{$feature.id_feature}">
                                    <i class="fa fa-trash"></i>
                                </span>
                    </div>
                {/foreach}
            </div>
        </div>
    </div>
</div>
{elseif $input.type == 'op_cms_pages'}
<div class="col-lg-9 margin-form">
    <div class="form-group">
        <div id="cms_page-choose-block" class="col-lg-9">
            <input type="hidden" name="cms_page_ids" id="cms_page_ids" value="{$input.selected_cms_pages_raw}"/>

            <div id="ajax_add_cms_page_id" class="ajax-add_op">

                <input type="text" value="" id="cms_page_id_input" class="id-input_op" autocomplete="off"/>
                <button type="button" id="cms_page_id_add" class="btn btn-primary"><i
                            class="fas fa-spinner fa-spin spin-process spin-process-inline"
                            style="display:none"></i>
                    <span class="spinner-hide-txt">{l s='OK' mod='faqop'}</span></button>
            </div>
            <div id="block-for-error-cms">
                <div class="op-error-wrap" style="display:none"></div>
            </div>
            {if isset($input.descr) && !empty($input.descr)}
                <p class="help-block">
                    {$input.descr}
                </p>
            {/if}
            <div id="divCmsPages_op" class="divItems_op">
                {foreach $input.selected_cms_pages as $page}
                    <div class="divItem_op">
                        {$page.meta_title} (id: {$page.id_cms})
                        <span class="delCmsPage_op delItem_op" name="{$page.id_cms}">
                                    <i class="fa fa-trash"></i>
                                </span>
                    </div>
                {/foreach}
            </div>
        </div>

    </div>
</div>
{elseif $input.type == 'special_pages_checkbox'}
<div class="col-lg-9 margin-form">
    <div class="form-group">
        <div id="special_pages-choose-block" class="col-lg-9">
            {foreach $input.values as $value}
                <p class="op-checkbox">
                    <label for="{$value.id}">
                        <input type="checkbox" id="{$value.id}" name="{$input.name}" value="{$value.id}"
                               {if $value.checked}checked="checked"{/if}>
                        <span>{$value.label}</span>
                        <i class="op-checkbox-control"></i>
                    </label>
                </p>
            {/foreach}
        </div>
    </div>
</div>
{elseif $input.type == 'checkbox_how_selected'}
<div class="col-lg-9">
    <div class="form-group how-select-products-form-group">
        {foreach $input.values as $value}
            <p class="op-checkbox">
                <label for="{$value.id}">
                    <input type="checkbox" id="{$value.id}" name="{$value.name}"
                           {if $value.val}checked="checked"{/if}>
                    <span>{$value.label}</span>
                    <i class="op-checkbox-control"></i>
                </label>
            </p>
        {/foreach}
    </div>
</div>
{elseif $input.type == 'languages_checkbox'}
<div class="col-lg-9 margin-form">
    <div class="form-group">
        <div id="languages-choose-block" class="col-lg-9">
            {foreach $input.values as $value}
                <p class="op-checkbox">
                    <label for="language-{$value.id}">
                        <input type="checkbox" id="language-{$value.id}" name="{$input.name}"
                               value="{$value.id}"
                               {if $value.checked}checked="checked"{/if}>
                        <span>{$value.label}</span>
                        <i class="op-checkbox-control"></i>
                    </label>
                </p>
            {/foreach}
        </div>
    </div>
</div>
{elseif $input.type == 'currencies_checkbox'}
<div class="col-lg-9 margin-form">
    <div class="form-group">
        <div id="currencies-choose-block" class="col-lg-9">
            {foreach $input.values as $value}
                <p class="op-checkbox">
                    <label for="currency-{$value.id}">
                        <input type="checkbox" id="currency-{$value.id}" name="{$input.name}"
                               value="{$value.id}"
                               {if $value.checked}checked="checked"{/if}>
                        <span>{$value.label}</span>
                        <i class="op-checkbox-control"></i>
                    </label>
                </p>
            {/foreach}
        </div>
    </div>
</div>
{elseif $input.type == 'customer_groups_checkbox'}
<div class="col-lg-9 margin-form">
    <div class="form-group">
        <div id="customer_groups-choose-block" class="col-lg-9">
            {foreach $input.values as $value}
                <p class="op-checkbox">
                    <label for="customer-group-{$value.id}">
                        <input type="checkbox" id="customer-group-{$value.id}" name="{$input.name}"
                               value="{$value.id}"
                               {if $value.checked}checked="checked"{/if}>
                        <span>{$value.label}</span>
                        <i class="op-checkbox-control"></i>
                    </label>
                </p>
            {/foreach}
        </div>
    </div>
</div>
{elseif $input.type == 'op_bind_blocks_for_item_checkbox'}

<div class="col-lg-12">
    <table class="table tableDnD op-grid-table op-grid-table-add-blocks-to-item">
        <thead>
        <tr class="nodrag nodrop">
            <th style="width: 60px">

            </th>
            <th style="width: 70px" class="op-sortable-column-js" data-sort-op-name="orderId">{l s='Id' mod='faqop'}
                <span role="button" class="fas fa-chevron-down"
                      aria-label="Sort by"></span>
            </th>
            <th class="op-sortable-column-js" data-sort-op-name="orderTitle">{l s='Title' mod='faqop'}
                <span role="button" class="fas fa-chevron-down"
                      aria-label="Sort by"></span>
            </th>

            <th class="op-sortable-column-js" data-sort-op-name="orderHook">{l s='Hook' mod='faqop'}
                <span role="button" class="fas fa-chevron-down"
                      aria-label="Sort by"></span>
            </th>

        </tr>
        </thead>
        {if !empty($input.values)}
            <tbody class="body-table-blocks">
            {foreach $input.values as $key => $block}
                <tr class="{if $key%2}alt_row{else}not_alt_row{/if} row_hover"
                    id="op_row-blocks-for-item_{$block.id}">
                    <td>
                        <div class="op-checkbox-table">
                            <label for="block-for-item-{$block.id}">
                                <input type="checkbox" id="block-for-item-{$block.id}" name="{$input.name}"
                                       value="{$block.id}"
                                       {if $block.checked}checked="checked"{/if}>
                                <i class="op-checkbox-control"></i>
                            </label>
                        </div>

                    </td>
                    <td class="orderId">{$block.id}</td>
                    <td class="orderTitle">{$block.title|escape:'htmlall':'UTF-8'}</td>

                    <td class="orderHook">{$block.hook_name|escape:'htmlall':'UTF-8'}</td>
                </tr>
            {/foreach}
            </tbody>
        {else}
            <tr>
                <td colspan="4" class="list-empty">
                    <div class="list-empty-msg">
                        <i class="icon-warning-sign list-empty-icon"></i>
                        {l s='No blocks found' mod='faqop'}
                    </div>
                </td>
            </tr>
        {/if}
    </table>
</div>
{elseif $input.type == 'is_only_default_category_checkbox'}
    <div id="is_only_default_category_wrap" class="col-lg-9">
        <div class="form-group">
            <div class="col-lg-9">
                <p class="op-checkbox">
                    <label for="is_only_default_category_checkbox">
                        <input type="checkbox" id="is_only_default_category_checkbox" name="{$input.name}"
                               value="{$input.value.id}"
                               {if $input.value.checked}checked="checked"{/if}>
                        <span>{$input.value.label}</span>
                        <i class="op-checkbox-control"></i>
                    </label>
                </p>
            </div>
        </div>
    </div>
{elseif $input.type == 'number'}
<div class="col-lg-9">
    <input type="number" name="{$input.name|escape:'htmlall':'UTF-8'}"
           id="{if isset($input.id)}{$input.id|escape:'htmlall':'UTF-8'}{else}{$input.name|escape:'htmlall':'UTF-8'}{/if}"
           class="{if isset($input.class)}{$input.class|escape:'htmlall':'UTF-8'}{/if}"
           value="{$input.value|escape:'html':'UTF-8'}"
    >
</div>
    {/if}

    {$smarty.block.parent}

    {/block}
