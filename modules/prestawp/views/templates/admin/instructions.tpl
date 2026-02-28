{**
* NOTICE OF LICENSE
*
* This file is licenced under the Software License Agreement.
* With the purchase or the installation of the software in your application
* you accept the licence agreement.
*
* @author    Presta.Site
* @copyright 2017 Presta.Site
* @license   LICENSE.txt
*}
<div id="pswp-instructions">
    {if $psv == 1.5}
        <br/><fieldset><legend>{l s='Additional instructions' mod='prestawp'}</legend>
    {else}
        <div class="panel">
        <div class="panel-heading">
            <i class="icon-cogs"></i> {l s='Additional instructions' mod='prestawp'}
        </div>
    {/if}

        <div class="form-wrapper">
            <p><a href="{$path|escape:'quotes':'UTF-8'}wordpress/prestawp-wordpress.zip">{l s='Download the WordPress plugin' mod='prestawp'} <i class="icon-download"></i></a></p>
            <br>
            <h4>{l s='How to connect PrestaShop and WordPress:' mod='prestawp'}</h4>
            <p>{l s='1) Install our plugin "PrestaShop-WordPress integration" into your WordPress blog.' mod='prestawp'}</p>
            <p>{l s='2) Enter your WordPress URL in the main settings of the PrestaShop module (this page).' mod='prestawp'}</p>
            <p>{l s='3) Enter your PrestaShop URL in the settings of the WordPress plugin.' mod='prestawp'}</p>
            <p>{l s='4) Copy the "Secure key" value from the PrestaShop module settings to the WordPress plugin settings.' mod='prestawp'}</p>
            <p>{l s='5) Save changes. All done.' mod='prestawp'}</p>
            <hr/>

            <h4>{l s='If you need to display your WordPress data in some non-standard place, you can use these hooks:' mod='prestawp'}</h4>
            <p><b>{literal}{hook h="PSWPposts"}{/literal}</b> - {l s='displays WordPress posts' mod='prestawp'} (<a href="#" id="pswp-compose-btn">{l s='compose' mod='prestawp'} <i class="icon icon-edit"></i></a>)</p>
            <div id="pswp-compose-wrp" style="display: none;">
                <div class="row form-group {if $psv == 1.5}margin-form wp-content-ps15{/if}">
                    <div class="col-md-4 {if $psv == 1.5}wpc-col-15{/if}">
                        <label for="wp_categories{if $wp_input.id_item}{$wp_input.id_item|intval}{/if}">{l s='WordPress categories' mod='prestawp'}</label>
                        <select name="{if isset($wp_input.wp_categories_name) && $wp_input.wp_categories_name}{$wp_input.wp_categories_name|escape:'html':'UTF-8'}[]{else}wp_categories[]{/if}"
                                id="wp_categories{if $wp_input.id_item}{$wp_input.id_item|intval}{/if}"
                                class="{if isset($wp_input.class)}{$wp_input.class|escape:'html':'UTF-8'}{/if} wp_categories_input"
                                multiple
                                size="10">
                            {foreach from=$wp_input.wp_category_options item="option"}
                                <option value="{$option.id_option|escape:'html':'UTF-8'}"
                                        {if $option.selected || (isset($wp_input.wp_categories_name) && isset($fields_value[$wp_input.wp_categories_name]) && in_array($option.id_option, $fields_value[$wp_input.wp_categories_name])|escape:'html':'UTF-8')}selected{/if}>
                                    {$option.name|escape:'html':'UTF-8'}
                                </option>
                            {/foreach}
                        </select>
                    </div>
                    <div class="col-md-4 {if $psv == 1.5}wpc-col-15{/if}">
                        <label for="wp_posts{if $wp_input.id_item}{$wp_input.id_item|intval}{/if}">{l s='WordPress posts' mod='prestawp'}</label>
                        <select name="{if isset($wp_input.wp_posts_name) && $wp_input.wp_posts_name}{$wp_input.wp_posts_name|escape:'html':'UTF-8'}[]{else}wp_posts[]{/if}"
                                id="wp_posts{if $wp_input.id_item}{$wp_input.id_item|intval}{/if}"
                                class="{if isset($wp_input.class)}{$wp_input.class|escape:'html':'UTF-8'}{/if} wp_posts_input"
                                multiple
                                size="10">
                            {foreach from=$wp_input.wp_post_options item="option"}
                                <option value="{$option.id_option|escape:'html':'UTF-8'}"
                                        {if $option.selected || (isset($wp_input.wp_posts_name) && isset($fields_value[$wp_input.wp_posts_name]) && in_array($option.id_option, $fields_value[$wp_input.wp_posts_name])|escape:'html':'UTF-8')}selected{/if}>
                                    {$option.name|escape:'html':'UTF-8'}
                                </option>
                            {/foreach}
                        </select>
                    </div>
                    <div class="col-md-4 {if $psv == 1.5}wpc-col-15{/if} pswp-hook-options-wrp">
                        <label>{l s='Options' mod='prestawp'}</label>
                        <input type="text" class="form-control wp_limit_input" placeholder="{l s='Max number of posts' mod='prestawp'}">
                        <input type="text" class="form-control wp_columns_input" placeholder="{l s='Number of columns' mod='prestawp'}">
                    </div>
                </div>
                <span class="pswp_cpp_close">&times;</span>
                <div class="pswp_cpp_wrp">
                    <div class="pswp_cpp_h">{l s='Generated code:' mod='prestawp'}</div>
                    <input type="text" readonly class="pswp_cpp_code" value='{literal}{hook h="PSWPposts"}{/literal}'>
                </div>
            </div>

            <p><b>{literal}{hook h="PSWPproduct"}{/literal}</b> - {l s='a hook for the product page, displays a list of WordPress posts linked to the current product.' mod='prestawp'} {l s='If necessary you can additionally specify the "id_product" parameter.' mod='prestawp'}</p>
            <p><b>{literal}{hook h="PSWPshortposts"}{/literal}</b> - {l s='displays a list of WordPress posts, only titles without post content' mod='prestawp'}</p>
            <p><b>{literal}{hook h="PSWPcategories"}{/literal}</b> - {l s='displays a list of WordPress categories' mod='prestawp'}</p>
            <p><b>{literal}{hook h="PSWPcomments"}{/literal}</b> - {l s='displays the latest comments from your blog' mod='prestawp'}</p>
            {if $psv >= 1.7}
                <p>
                    <b>{literal}{widget name="prestawp" hook="custom"}{/literal}</b>
                    - {l s='displays the custom blocks that belong to the specified hook' mod='prestawp'}
                </p>
            {/if}
        </div>

    {if $psv == 1.5}
        </fieldset><br/>
    {else}
        </div>
    {/if}
</div>
