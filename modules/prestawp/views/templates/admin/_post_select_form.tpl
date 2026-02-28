{**
* NOTICE OF LICENSE
*
* This file is licenced under the Software License Agreement.
* With the purchase or the installation of the software in your application
* you accept the licence agreement.
*
* @author    Presta.Site
* @copyright 2023 Presta.Site
* @license   LICENSE.txt
*}
<div class="col-lg-9">
    <div class="pswp-select-wrp pswp-select-wrp-posts">
        <div class="row">
            <div class="col-lg-12 pswp-col-filters">
                <span class="btn btn-default btn-xs pswp-toggle-category-filter">{if empty($pswp_is_product_page)}<i class="icon-filter"></i>{else}<i class="material-icons">filter_list</i>{/if} {l s='Filter by WordPress category' mod='prestawp'}</span>
                <span class="pswp-category-filter-names"></span>
                <div class="pswp-category-wrp pswp-filter-wrp">
                    <select name="{if isset($input.wp_categories_name) && $input.wp_categories_name}{$input.wp_categories_name|escape:'html':'UTF-8'}[]{else}wp_categories[]{/if}"
                            id="wp_categories{if $input.id_item}{$input.id_item|intval}{/if}"
                            class="{if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if} wp_categories_input"
                            multiple
                            size="10">
                        {foreach from=$input.wp_category_options item="option"}
                            <option value="{$option.id_option|escape:'html':'UTF-8'}"
                                    {if $option.selected || (isset($input.wp_categories_name) && isset($fields_value[$input.wp_categories_name]) && in_array($option.id_option, $fields_value[$input.wp_categories_name])|escape:'html':'UTF-8')}selected{/if}>
                                {$option.name|escape:'html':'UTF-8'}
                            </option>
                        {/foreach}
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <p>{l s='Available posts:' mod='prestawp'}</p>
                <input type="text" class="form-control pswp-post-search" placeholder="{l s='Filter by name' mod='prestawp'}">
                <select id="wp_posts{if $input.id_item}{$input.id_item|intval}{/if}"
                        class="pswp_post_select {if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if} wp_posts_input"
                        multiple
                        size="10">
                    {foreach from=$input.wp_post_options item="option"}
                        {if !($option.selected && (isset($input.wp_posts_name) && isset($fields_value[$input.wp_posts_name]) && in_array($option.id_option, $fields_value[$input.wp_posts_name])))}
                            <option value="{$option.id_option|escape:'html':'UTF-8'}">
                                {$option.name|escape:'html':'UTF-8'}
                            </option>
                        {/if}
                    {/foreach}
                </select>
                <a href="#" class="btn btn-default btn-block pswp_multiple_select_add">
                    {l s='Add' mod='prestawp'} {if empty($pswp_is_product_page)}<i class="icon-arrow-right"></i>{else}<i class="material-icons">arrow_forward</i>{/if}
                </a>
                <a href="#" class="pswp-multiple-select-all">{l s='Reset' mod='prestawp'}</a>
            </div>
            <div class="col-lg-6">
                <p>{l s='Posts that will be displayed:' mod='prestawp'}</p>
                <input type="text" class="form-control pswp-post-search" placeholder="{l s='Filter by name' mod='prestawp'}">
                <select class="pswp_post_selected"
                        multiple
                        size="10">
                    {foreach from=$input.wp_post_options item="option"}
                        {if $option.selected || (isset($input.wp_posts_name) && isset($fields_value[$input.wp_posts_name]) && in_array($option.id_option, $fields_value[$input.wp_posts_name]))}
                            <option value="{$option.id_option|escape:'html':'UTF-8'}">
                                {$option.name|escape:'html':'UTF-8'}
                            </option>
                        {/if}
                    {/foreach}
                </select>
                {strip}
                <div class="pswp-selected-posts-data" data-name="{if isset($input.wp_posts_name) && $input.wp_posts_name}{$input.wp_posts_name|escape:'html':'UTF-8'}[]{else}wp_posts[]{/if}">
                    {foreach from=$input.wp_post_options item="option"}
                        {if $option.selected || (isset($input.wp_posts_name) && isset($fields_value[$input.wp_posts_name]) && in_array($option.id_option, $fields_value[$input.wp_posts_name]))}
                            <input type="hidden" name="{if isset($input.wp_posts_name) && $input.wp_posts_name}{$input.wp_posts_name|escape:'html':'UTF-8'}[]{else}wp_posts[]{/if}" value="{$option.id_option|escape:'html':'UTF-8'}">
                        {/if}
                    {/foreach}
                </div>
                {/strip}
                <a href="#" class="btn btn-default btn-block pswp_multiple_select_del">
                    {l s='Remove' mod='prestawp'} {if empty($pswp_is_product_page)}<i class="icon-arrow-left"></i>{else}<i class="material-icons">arrow_backward</i>{/if}
                </a>
            </div>
        </div>
    </div>
</div>