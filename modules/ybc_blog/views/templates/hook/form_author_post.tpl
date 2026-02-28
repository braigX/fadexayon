{*
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
*}
<div class="ybc-form-group ybc-blog-tab-basic active ybc-form-group-author">
    {if isset($author) && $author}
        <div class="form-group">
            <label class="control-label col-lg-3"> {l s='Author' mod='ybc_blog'}: </label>
            <div class="col-lg-9">
                <div class="customer_author_name"><a href="{$author.link|escape:'html':'UTF-8'}">{if $author.name}{$author.name|escape:'html':'UTF-8'}{else}{$author.firstname|escape:'html':'UTF-8'}&nbsp;{$author.lastname|escape:'html':'UTF-8'}{/if}</a></div>
                <button class="ybc_display_form_author btn btn-default"><i class="ets_svg pencil">
                        <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M491 1536l91-91-235-235-91 91v107h128v128h107zm523-928q0-22-22-22-10 0-17 7l-542 542q-7 7-7 17 0 22 22 22 10 0 17-7l542-542q7-7 7-17zm-54-192l416 416-832 832h-416v-416zm683 96q0 53-37 90l-166 166-416-416 166-165q36-38 90-38 53 0 91 38l235 234q37 39 37 91z"/></svg>
                    </i>{l s='Change' mod='ybc_blog'}</button>
            </div>
        </div>
    {else}
        <div class="form-group">
            <label class="control-label col-lg-3">{l s='Author' mod='ybc_blog'}:</label>
            <div class="col-lg-9">
                <button class="ybc_display_form_author btn btn-default"><i class="ets_svg pencil">
                        <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M491 1536l91-91-235-235-91 91v107h128v128h107zm523-928q0-22-22-22-10 0-17 7l-542 542q-7 7-7 17 0 22 22 22 10 0 17-7l542-542q7-7 7-17zm-54-192l416 416-832 832h-416v-416zm683 96q0 53-37 90l-166 166-416-416 166-165q36-38 90-38 53 0 91 38l235 234q37 39 37 91z"/></svg>
                    </i>{l s='Set author' mod='ybc_blog'}</button>
            </div>
        </div>
    {/if}
    {if $YBC_BLOG_ALLOW_CUSTOMER_AUTHOR}
        <div class="form-group form_author">
            <div class="control-label col-lg-3"></div>
            <div class="col-lg-9">
                <label for="is_customer"><input type="radio" name="is_customer" value="0" id="is_customer" {if $post.is_customer==0}checked="checked"{/if} />{l s='Administrator - Authors' mod='ybc_blog'}</label>
                <label for="is_customer_1"><input type="radio" name="is_customer" value="1" id="is_customer_1" {if $post.is_customer==1}checked="checked"{/if} />{l s='Community - Authors' mod='ybc_blog'}</label>
            </div>
        </div>
    {/if}
    <div class="form-group form_author">
        <div class="from_admin_author{if !$YBC_BLOG_ALLOW_CUSTOMER_AUTHOR || $post.is_customer==0} show{/if}">
            <div class="control-label col-lg-3">{l s='Administrator - Author' mod='ybc_blog'}</div>
            <div class="col-lg-9">
                <select id="admin_author" name="admin_author" class="fixed-width-xl">
                    <option value="">{l s='--' mod='ybc_blog'}</option>
                    {foreach from=$admin_authors item='admin_author'}
                        <option data-link="{$admin_author.link|escape:'html':'UTF-8'}" value="{$admin_author.id_employee|intval}" {if $post.is_customer==0 && isset($author) &&  $author['id_employee']==$admin_author.id_employee}selected="selected"{/if}>{if $admin_author.name}{$admin_author.name|escape:'html':'UTF-8'}{else}{$admin_author.firstname|escape:'html':'UTF-8'}&nbsp;{$admin_author.lastname|escape:'html':'UTF-8'}{/if}</option>
                    {/foreach}
                </select>
            </div>
        </div>
    </div>
    {if $YBC_BLOG_ALLOW_CUSTOMER_AUTHOR}
        <div class="form-group form_author">
            <div class="from_customer_author{if $post.is_customer==1} show{/if}">
                <div class="control-label col-lg-3">{l s='Community - Author' mod='ybc_blog'}</div>
                <div class="col-lg-9">
                    <div class="input-group">
                        <input type="hidden" value="{if $post.is_customer==1 && isset($author) &&  $author['id_customer']}{$author['id_customer']|intval}{/if}" name="customer_author" id="customer_author"/>
        				{if $post.is_customer==1&& isset($author) &&  $author['id_customer']}
                            <div class="customer_author_name_choose">{if $author.name}{$author.name|escape:'html':'UTF-8'}{else}{$author.firstname|escape:'html':'UTF-8'}&nbsp;{$author.lastname|escape:'html':'UTF-8'}{/if}<span class="close_choose">x</span></div>
                        {/if}
                        <input id="customer_autocomplete_input" name="customer_autocomplete_input" placeholder="{l s='Search Community - Author by ID or name or email' mod='ybc_blog'}" autocomplete="off" class="ac_input" type="text" />
                        <span class="input-group-addon"><i class="ets_svg search">
                                <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1216 832q0-185-131.5-316.5t-316.5-131.5-316.5 131.5-131.5 316.5 131.5 316.5 316.5 131.5 316.5-131.5 131.5-316.5zm512 832q0 52-38 90t-90 38q-54 0-90-38l-343-342q-179 124-399 124-143 0-273.5-55.5t-225-150-150-225-55.5-273.5 55.5-273.5 150-225 225-150 273.5-55.5 273.5 55.5 225 150 150 225 55.5 273.5q0 220-124 399l343 343q37 37 37 90z"/></svg>
                            </i></span>
        			</div>
                </div>
            </div>
        </div>
    {/if}
</div>
<style>
    .from_admin_author,.from_customer_author{
        display:none;
    }
    .from_admin_author.show,.from_customer_author.show{
        display:block;
    }
    .form-group.form_author{
        display:none;
    }
    .form-group.form_author.show{
        display:block;
    }
</style>