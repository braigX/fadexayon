{*
* 2007-2018 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
* @author    SeoSA    <885588@bk.ru>
* @copyright 2012-2022 SeoSA
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
* International Registered Trademark & Property of PrestaShop SA
*}
{if !$id_shop}
    <div class="alert alert-danger">
        {l s='Selected all shops. Please, select one shop from list! ' mod='sitemappro'}
    </div>
{else}
    <script>
        var changefreqs = {$changefreqs|json_encode|no_escape};
        var priorities = {$priorities|json_encode|no_escape};
        var conf_categories = {$conf_categories|json_encode|no_escape};
        var conf_products = {$conf_products|json_encode|no_escape};
        var conf_cms = {$conf_cms|json_encode|no_escape};
        var cms = {$cms|json_encode|no_escape};
        var conf_meta = {$conf_meta|json_encode|no_escape};
        var conf_manufacturers = {$conf_manufacturers|json_encode|no_escape};
        var conf_suppliers = {$conf_suppliers|json_encode|no_escape};
        var meta = {$meta|json_encode|no_escape};
        var url = document.location.href.replace(document.location.hash, '');
        var id_shop = {$id_shop|intval};
        var please_wait = "{l s='Please, wait...' mod='sitemappro'}";
        var user_links = {$user_links|json_encode|no_escape};
        var languages = {$languages|json_encode|no_escape};
        var shop_url = "{$shop_url|no_escape}";
        var id_language = {$id_lang_default|intval};
        var description_cron0 = '{l s='Cron file' mod='sitemappro'}';
        var description_cron1 = '{l s='Same hostings do not support this type cron clink. You can use alt cron link.' mod='sitemappro'}';
        var description_cron2 = '{l s='For this: For Chrome browser Open the sitemap generate tab. Press F12 key.' mod='sitemappro'}';
        var description_cron3 = '{l s='Browser console opens. Select "Network" tab.' mod='sitemappro'}';
        var description_cron4 = '{l s='Run generation of required sitemap:' mod='sitemappro'}';
        var description_cron5 = '{l s='Field with cron will appear in console.' mod='sitemappro'}';
        var description_cron6 = '{l s='Click it:' mod='sitemappro'}';
        var description_cron7 = '{l s='Column opens on right. Select “Header” tab and copy link from “Request URL” field:' mod='sitemappro'}';
        var description_cron8 = '{l s='Use this link for cron tasks.' mod='sitemappro'}';
    </script>
    <script>

        function actionDescription() {

            $.dialog({
                title: description_cron0,
                content: description_cron1 +
                '<br>' + description_cron2 +
                '<br>' + description_cron3 +
                '<br>' + description_cron4 +
                '<br>' + '{get_image_lang_map path = "cron_1.jpg"}' +
                '<br>' + description_cron5 +
                '<br>' + description_cron6 +
                '<br>' + '{get_image_lang_map path = "cron_2.jpg"}' +
                '<br>' + description_cron7 +
                '<br>' + '{get_image_lang_map path = "cron_3.jpg"}' +
                '<br>' + description_cron8,
            });

            setTimeout(function () {
                $('body').find('.jconfirm').addClass('bootstrap');
            }, 1);
        }
    </script>
    <script id="info_sitemap_link" type="text/html">
        <div class="info_sitemap_link_content_js">
            <label class="status-sitemap">
                {l s='Status' mod='sitemappro'}:
            </label>
            <span class="label label-success info_sitemap_link_title">{l s='Exists' mod='sitemappro'}</span><br>
            <a class="info_sitemap_link_link" target="_blank" href="%link%">%link%</a> (%date%)
        </div>
    </script>
    <div class="tab_container custom_bootstrap form-horizontal {if $smarty.const._PS_VESRION_ < 1.6}custom_responsive{/if} style_tabs clearfix">
        <div class="tabs js-tabs col-lg-3 col-md-3">
            <ul class="nav js-nav-tabs">
                <li data-tab="tab1"><a href="#" class="nav-link">{l s='Category' mod='sitemappro'}</a></li>
                <li data-tab="tab2"><a href="#" class="nav-link">{l s='Product' mod='sitemappro'}</a></li>
                <li data-tab="tab3"><a href="#" class="nav-link">{l s='CMS pages' mod='sitemappro'}</a></li>
                <li data-tab="tab4"><a href="#" class="nav-link">{l s='Pages' mod='sitemappro'}</a></li>
                <li data-tab="tab_manufacturer"><a href="#" class="nav-link">{l s='Manufacturers' mod='sitemappro'}</a>
                </li>
                <li data-tab="tab_supplier"><a href="#" class="nav-link">{l s='Suppliers' mod='sitemappro'}</a></li>
                <li data-tab="tab6"><a href="#" class="nav-link">{l s='User links' mod='sitemappro'}</a></li>
                <li data-tab="tab5"><a href="#" class="nav-link">{l s='Generate sitemap' mod='sitemappro'}</a></li>
                <li data-tab="tab7"><a class="SMPro_doc"
                                       href="{$link_to_documentation|escape:'quotes':'UTF-8'}">{l s='Documention' mod='sitemappro'}</a>
                </li>
                <li data-tab="tab8"><a id="seosa_manager_btn"
                                       href="#">{l s='Our modules' mod='sitemappro'}</a></li>
            </ul>
        </div>
        <div class="tabs_content_wrap col-lg-9 col-md-9">
            <div class="tabs_content">
                <div id="tab1">
                    <div id="loader" class="loader lds-dual-ring overlay"></div>
                    {include file="./_partials/default_dettings_row.tpl" type_object='category'}
                    {include file="./_partials/default_settings_category_fields.tpl.tpl" type_object='category'}
                    <div class="tree_custom ">
                        {include file="./tree.tpl"
                        categories=$categories
                        id_category=$id_root_category
                        root=true
                        view_header=true
                        multiple=true
                        selected_categories=$sitemap_categories
                        name='categories'
                        }
                    </div>
                    <div class="row">
                        <div class="overflow-auto">
                            <table class="table table-new sitemap_categories" style="display: none">
                                <thead>
                                <tr>
                                    <th class="name">{l s='Name' mod='sitemappro'}</th>
                                    <th class="priority">{l s='Priority' mod='sitemappro'}</th>
                                    <th class="changefreq">{l s='Changefreq' mod='sitemappro'}</th>
                                    <th class="action">{l s='Action' mod='sitemappro'}</th>
                                    <th class="action">{l s='Delete' mod='sitemappro'}</th>
                                </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="search-category-wrap form-group clearfix">
                        <div class="col-lg-12">
                            <label class="control-label float-left margin-right margin-top">
                                {l s='Search category' mod='sitemappro'}:
                            </label>
                            <div class="float-left margin-right margin-top fixed-width-xxl w-xs-100">
                                <select name="search_category" class="fixed-width-xl"></select>
                            </div>
                            <div class="float-left margin-top">
                                <button type="button" data-add-category class="btn btn-default">
                                    <i class="icon-plus"></i>
                                    {l s='Add' mod='sitemappro'}
                                </button>
                            </div>
                        </div>

                    </div>
                    <div class="row need_save" style="display: none;">
                        <button type="button" class="btn btn-primary btn-lg pull-right saveSiteMapConf">
                            {l s='Save' mod='sitemappro'}
                        </button>
                    </div>
                </div>
                <div id="tab2">
                    {*{include file="./_partials/default_settings_fields.tpl" type_object='product'}*}
                    {include file="./_partials/default_dettings_row.tpl" type_object='product'}
                    <div class="row product_selected_categories_block" style="display: none; margin-top: -5px;">
                        <div class="col-lg-12">
                            <div class="row">
                                <label class="col-xs-12 col-lg-3 control-label margin-top">
                                    {l s='Selected categories' mod='sitemappro'}:
                                </label>
                                <div class="col-xs-12 col-lg-9 margin-top" style="margin-left: -5px; margin-right: -5px;">
                                    <span class="product_selected_categories"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row block_search_product">
                        <div class="col-lg-12">
                            <div class="row">
                                <label class="col-xs-12 col-sm-3 control-label float-left margin-top">
                                    {l s='Begin search product' mod='sitemappro'}:
                                </label>
                                <div class="col-xs-12 col-sm-9 float-left wrapp_search_product margin-top">
                                    <input type="text" class="w-100" name="search_product"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="overflow-auto">
                            <table class="table table-new sitemap_products" style="display: none">
                                <thead>
                                <tr>
                                    <th class="name">{l s='Name' mod='sitemappro'}</th>
                                    <th class="priority">{l s='Priority' mod='sitemappro'}</th>
                                    <th class="changefreq">{l s='Changefreq' mod='sitemappro'}</th>
                                    <th class="action">{l s='Action' mod='sitemappro'}</th>
                                    <th class="action">{l s='Delete' mod='sitemappro'}</th>
                                </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="sitemap-excluded-wrap row">
                            <div class="col-lg-12">
                                <div class="row">
                                    <label class="col-xs-12 col-sm-3 control-label margin-top">
                                        {l s='Excluded products' mod='sitemappro'}:
                                    </label>
                                    <div class="col-xs-12 col-sm-9 margin-top">
                                        <select id="product_excluded" name="sitemap_excluded[]" multiple style="width: 100%;">
                                            {foreach from=$sitemap_excluded item=product}
                                                <option selected
                                                        value="{$product.id|intval}">{$product.text|escape:'quotes':'UTF-8'}</option>
                                            {/foreach}
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row need_save" style="display: none;">
                        <button type="button" class="btn btn-primary btn-lg pull-right saveSiteMapConf">
                            {l s='Save' mod='sitemappro'}
                        </button>
                    </div>
                </div>
                <div id="tab3">
                    <div id="loader" class="loader lds-dual-ring overlay"></div>
                    {include file="./_partials/default_settings_fields.tpl" type_object='cms'}
                    <div class="row">
                        <div class="overflow-auto">
                            <table class="table table-new sitemap_cms form-group">
                                <thead>
                                <tr>
                                    <th class="name">{l s='Name' mod='sitemappro'}</th>
                                    <th class="priority">{l s='Priority' mod='sitemappro'}</th>
                                    <th class="changefreq">{l s='Changefreq' mod='sitemappro'}</th>
                                    <th class="export">{l s='Export?' mod='sitemappro'}</th>
                                    <th class="export">{l s='Delete' mod='sitemappro'}</th>
                                </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <ul id="cms-paginator" class="pagination"></ul>

                        <ul class="pagination float-right">
                            <li>
                                <div class="row need_save" style="display: none;">
                                    <button type="button" class="btn btn-primary btn-lg pull-right saveSiteMapConf">
                                        {l s='Save' mod='sitemappro'}
                                    </button>
                                </div>
                            </li>
                        </ul>

                    </div>
                </div>
                <div id="tab4">
                    {include file="./_partials/default_settings_fields.tpl" type_object='meta'}
                    <div class="row form-group">
                        <div class="overflow-auto">
                            <table class="table table-new sitemap_meta">
                                <thead>
                                <tr>
                                    <th class="name">{l s='Name' mod='sitemappro'}</th>
                                    <th class="priority">{l s='Priority' mod='sitemappro'}</th>
                                    <th class="changefreq">{l s='Changefreq' mod='sitemappro'}</th>
                                    <th class="export">{l s='Export?' mod='sitemappro'}</th>
                                </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row need_save" style="display: none;">
                        <button type="button" class="btn btn-primary btn-lg pull-right saveSiteMapConf">
                            {l s='Save' mod='sitemappro'}
                        </button>
                    </div>
                </div>
                <div id="tab_manufacturer">
                    {include file="./_partials/default_dettings_row.tpl" type_object='manufacturer'}
                    <div class="row">
                        <div class="overflow-auto">
                            <table class="table table-new sitemap_manufacturer" style="display: none">
                                <thead>
                                <tr>
                                    <th class="name">{l s='Name' mod='sitemappro'}</th>
                                    <th class="priority">{l s='Priority' mod='sitemappro'}</th>
                                    <th class="changefreq">{l s='Changefreq' mod='sitemappro'}</th>
                                    <th class="export">{l s='Export?' mod='sitemappro'}</th>
                                    <th class="export">{l s='Delete' mod='sitemappro'}</th>
                                </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="search_manufacturer_wrap form-group clearfix">
                                    <label class="control-label float-left mr-1">
                                        {l s='Search manufacturer' mod='sitemappro'}:
                                    </label>
                                    <div class="float-left mr-1 fixed-width-xxl w-xs-100 form-group">
                                        <select name="search_manufacturer" class=""></select>
                                    </div>
                                    <div class="float-left form-group">
                                        <button type="button" data-add-manufacrurer
                                                class="btn btn-default float-left mr-1">
                                            <i class="icon-plus"></i>
                                            {l s='Add' mod='sitemappro'}
                                        </button>
                                        <button type="button" data-add-all-manufacrurer
                                                class="btn btn-default float-left">
                                            <i class="icon-plus"></i>
                                            {l s='Add all' mod='sitemappro'}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row need_save" style="display: none;">
                        <button type="button" class="btn btn-primary btn-lg pull-right saveSiteMapConf">
                            {l s='Save' mod='sitemappro'}
                        </button>
                    </div>
                </div>
                <div id="tab_supplier">
                    {include file="./_partials/default_dettings_row.tpl" type_object='supplier'}
                    <div class="row">
                        <div class="overflow-auto">
                            <table class="table table-new sitemap_supplier" style="display: none">
                                <thead>
                                <tr>
                                    <th class="name">{l s='Name' mod='sitemappro'}</th>
                                    <th class="priority">{l s='Priority' mod='sitemappro'}</th>
                                    <th class="changefreq">{l s='Changefreq' mod='sitemappro'}</th>
                                    <th class="export">{l s='Export?' mod='sitemappro'}</th>
                                    <th class="export">{l s='Delete' mod='sitemappro'}</th>
                                </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="search_supplier_wrap clearfix">
                        <label class="control-label float-left mr-1">
                            {l s='Search supplier' mod='sitemappro'}:
                        </label>
                        <div class="float-left mr-1 fixed-width-xxl w-xs-100 form-group">
                            <select name="search_supplier" class=""></select>
                        </div>
                        <div class="float-left form-group">
                            <button type="button" data-add-supplier class="btn btn-default float-left mr-1">
                                <i class="icon-plus"></i>
                                {l s='Add' mod='sitemappro'}
                            </button>
                            <button type="button" data-add-all-supplier class="btn btn-default float-left">
                                <i class="icon-plus"></i>
                                {l s='Add all' mod='sitemappro'}
                            </button>
                        </div>
                    </div>
                    <div class="row need_save" style="display: none;">
                        <button type="button" class="btn btn-primary btn-lg pull-right saveSiteMapConf">
                            {l s='Save' mod='sitemappro'}
                        </button>
                    </div>
                </div>
                <div id="tab6">
                    {include file="./_partials/default_settings_fields.tpl" type_object='user_link'}
                    <div class="row">
                        <div class="overflow-auto">
                            <table class="table table-new sitemap_user_link form-group" style="display: none">
                                <thead>
                                <tr>
                                    <th class="link">{l s='Link' mod='sitemappro'}</th>
                                    <th class="priority">{l s='Priority' mod='sitemappro'}</th>
                                    <th class="changefreq">{l s='Changefreq' mod='sitemappro'}</th>
                                    <th class="action-mini">{l s='Action' mod='sitemappro'}</th>
                                </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="form-group clearfix">
                        <button class="btn btn-default addUserLink">
                            {l s='Add link' mod='sitemappro'}
                        </button>
                    </div>
                    <div class="row need_save" style="display: none;">
                        <button type="button" class="btn btn-primary btn-lg pull-right saveSiteMapConf">
                            {l s='Save' mod='sitemappro'}
                        </button>
                    </div>
                </div>
                <div id="tab5">
                    <div class="need_save" style="display: none;">
                        <button type="button" class="btn btn-primary btn-lg pull-right saveSiteMapConf">
                            {l s='Save' mod='sitemappro'}
                        </button>
                    </div>

                    <div class="row form-group">

                        <label class="col-xs-12 col-sm-6 control-label float-left">{l s='In what protocol need generate links?' mod='sitemappro'}</label>

                        <div class="col-xs-12 col-sm-6">

                            <div class="fixed-width-lg float-left margin-right">
                                <div class="switch prestashop-switch prestashop-switch-custom fixed-width-lg">
                                    <input type="radio" {if $protocol == 1}checked{/if} name="type" value="1"
                                           id="type_http">
                                    <label for="type_http">{l s='HTTP' mod='sitemappro'}</label>
                                    <input type="radio" {if $protocol == 0}checked{/if} name="type" value="0"
                                           id="type_https">
                                    <label for="type_https">{l s='HTTPS' mod='sitemappro'}</label>
                                    <a class="slide-button btn"></a>
                                </div>
                            </div>

                            <label class="float-left control-label text-left">{l s="You're using:" mod='sitemappro'} {$label_protocol|escape:'htmlall':'UTF-8'}</label>

                        </div>

                    </div>

                    <div class="row form-group">
                        <div class="col-xs-12 col-sm-12">

                            <div class="row">

                                <label class="1 col-xs-6 control-label float-left ">{l s='Protect cron file?' mod='sitemappro'}</label>

                                <div class="col-xs-6">
                                    <div class="switch prestashop-switch fixed-width-lg float-left">
                                        <input type="radio" {if $protect == 1}checked{/if} name="protect" value="1"
                                               id="protect_on">
                                        <label for="protect_on">{l s='Yes' mod='sitemappro'}</label>
                                        <input type="radio" {if $protect == 0}checked{/if} name="protect" value="0"
                                               id="protect_off">
                                        <label for="protect_off">{l s='No' mod='sitemappro'}</label>
                                        <a class="slide-button btn"></a>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="col-xs-12 col-sm-12">
                            <div class="row">

                                <label class="3 col-xs-6 control-label float-left ">{l s='Protect file xml?' mod='sitemappro'}</label>

                                <div class="col-xs-6">

                                    <div class="switch prestashop-switch fixed-width-lg float-left">
                                        <input type="radio" {if $protect_file == 1}checked{/if} name="protect_file"
                                               value="1" id="protect_on_file">
                                        <label for="protect_on_file">{l s='Yes' mod='sitemappro'}</label>
                                        <input type="radio" {if $protect_file == 0}checked{/if} name="protect_file"
                                               value="0" id="protect_off_file">
                                        <label for="protect_off_file">{l s='No' mod='sitemappro'}</label>
                                        <a class="slide-button btn"></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row form-group secret_block"{if $protect == 0} style="display: none;"{/if}>

                        <label for="secret" class="2 col-xs-12 col-sm-3 control-label cron-sitemap float-left margin-top">
                            {l s='Secret' mod='sitemappro'}:
                        </label>

                        <div class="col-xs-12 col-sm-6 margin-top">
                            <input id="secret" type="text" class="w-100" name="secret"
                                   value="{$secret|escape:'htmlall':'UTF-8'}">
                        </div>

                        <div class="col-xs-12 col-sm-3 margin-top">
                            <div class="float-left">
                                <button id="update_secret"
                                        class="btn btn-info">{l s='Update' mod='sitemappro'}</button>
                            </div>
                        </div>

                    </div>

                    <div class="row form-group secret_file_block" {if $protect_file == 0} style="display: none;"{/if}>

                        <label for="secret_file" class="4 col-xs-12 col-sm-3 control-label cron-sitemap float-left margin-top">
                            {l s='Secret' mod='sitemappro'}:
                        </label>
                        <div class="col-xs-12 col-sm-6 margin-top">
                            <input id="secret_file" type="text" class="w-100"
                                   name="secret_file"
                                   value="{$secret_file|escape:'htmlall':'UTF-8'}">
                        </div>

                        <div class="col-xs-12 col-sm-3 margin-top">
                            <div class="float-left">
                                <button id="update_secret_file" class="btn btn-info">{l s='Update'
                                    mod='sitemappro'}</button>
                            </div>
                        </div>

                    </div>

                    <div class="row form-group">
                        <div class="col-xs-12 text-center">
                            <a href="#additional-settings" class="btn btn-info js-scroll">{l s='Additional settings' mod='sitemappro'}</a>
                        </div>
                    </div>

                    <div class="row">
                        {foreach from=$sitemaps key=sitemap_type item=sitemap}
                            <div class="separator"></div>
                            <div class="clearfix">


                                <div class="form-group clearfix">
                                    <div class="col-lg-12 generate-label">
                                        <label class="control-label mr-1 margin-top">{$sitemap.link.description|escape:'quotes':'UTF-8'}</label>
                                        <button data-sitemap-link="{$sitemap.link.cron_url|escape:'quotes':'UTF-8'}"
                                                class="btn btn-info margin-top">
                                            {l s='Generate' mod='sitemappro'}
                                        </button>
                                    </div>
                                    <div class="col-lg-12">

                                        <div class="title-sitemap">

                                            <span class="label-success-sitemap margin-top"
                                                  data-info-sitemap-link="{$sitemap.link.cron_url|escape:'quotes':'UTF-8'}">
                                                <label class="control-label status-sitemap mr-1">
                                                    {l s='Status' mod='sitemappro'}:
                                                </label>
                                                {if $sitemap.link.date == '0000-00-00 00:00:00'}
                                                    <span class="label label-danger">{l s='Not exists' mod='sitemappro'}</span>
                                                {else}
                                                    <span class="label label-success">{l s='Exists' mod='sitemappro'}</span>
                                                    <br>
                                                    <a target="_blank"
                                                       href="{$sitemap.link.link|escape:'quotes':'UTF-8'}">{$sitemap.link.link|escape:'quotes':'UTF-8'}</a>
                                                    ({$sitemap.link.date|escape:'quotes':'UTF-8'})
                                                {/if}
                                            </span>
                                        </div>

                                        <div class="link-pages-sitemap"
                                             data-pages="{$sitemap.link.cron_url|escape:'quotes':'UTF-8'}">
                                            {if is_array($sitemap.link.pages) && count($sitemap.link.pages)}
                                                <ul>
                                                    {foreach from=$sitemap.link.pages item=page}
                                                        <li>
                                                            <a target="_blank" href="{$page|escape:'quotes':'UTF-8'}">
                                                                {$page|escape:'quotes':'UTF-8'}
                                                            </a>
                                                        </li>
                                                    {/foreach}
                                                </ul>
                                            {/if}
                                        </div>

                                    </div>
                                    <div class="col-lg-12 margin-top">
                                        <label class="control-label cron-sitemap float-left mr-1 margin-top">
                                            {l s='Cron file' mod='sitemappro'}:
                                        </label>
                                        <div class="float-left fixed-width-xxl w-xs-100 mr-1 margin-top">
                                            <input type="text" class="" readonly
                                                   value="{$sitemap.link.cron_url|escape:'htmlall':'UTF-8'}">
                                        </div>
                                        <div class="btn btn-default float-left copy-btn fixed-width-xs mr-1 margin-top">
                                            <i class="icon-copy"></i>
                                        </div>
                                        <div class="btn btn-default float-left fixed-width-xs margin-top"
                                             onclick="actionDescription()">
                                            <i class="icon-file-text-o"></i>
                                        </div>


                                    </div>
                                </div>
                                {if isset($sitemap.lang)}
                                    <div class="form-group clearfix">
                                        <div class="tab_sitemap_{$sitemap_type|escape:'quotes':'UTF-8'} style_tabs clearfix">
                                            <div class="tabs js-tabs col-lg-2 col-md-3">
                                                <ul class="nav js-nav-tabs">
                                                    <li data-tab="all_tabs"><a
                                                                class="nav-link">{l s='All' mod='sitemappro'}</a></li>
                                                    {foreach from=array_keys($sitemap.lang) item=iso_code_item}
                                                        <li data-tab="tab_{$iso_code_item|escape:'quotes':'UTF-8'}"><a
                                                                    class="nav-link">{$iso_code_item|escape:'quotes':'UTF-8'}</a>
                                                        </li>
                                                    {/foreach}
                                                </ul>
                                            </div>
                                            <div class="tabs_content_wrap col-lg-10 col-md-9">
                                                <div class="tabs_content">
                                                    {foreach from=$sitemap.lang key=key item=item}
                                                        <div id="tab_{$key|escape:'quotes':'UTF-8'}">
                                                            <div class="clearfix">
                                                                <div>
                                                                    <label class="control-label mr-1 margin-top">{$item.description|escape:'quotes':'UTF-8'}</label>
                                                                    <button data-sitemap-link="{$item.cron_url|escape:'quotes':'UTF-8'}"
                                                                            class="btn btn-info margin-top">
                                                                        {l s='Generate' mod='sitemappro'}
                                                                    </button>
                                                                </div>
                                                                <div class="cron-sitemap margin-top">
                                                                    <div class="row">
                                                                        <div class="col-lg-12">

                                                                            <label class="control-label cron-sitemap float-left mr-1 margin-top">
                                                                                {l s='Cron file' mod='sitemappro'}:
                                                                            </label>
                                                                            <div class="float-left fixed-width-xxl mr-1 margin-top">
                                                                                <input type="text" class="" readonly
                                                                                       value="{$item.cron_url|escape:'htmlall':'UTF-8'}">
                                                                            </div>
                                                                            <div class="btn btn-default float-left copy-btn fixed-width-xs mr-1 margin-top">
                                                                                <i class="icon-copy"></i>
                                                                            </div>
                                                                            <div class="btn btn-default float-left fixed-width-xs margin-top"
                                                                                 onclick="actionDescription()">
                                                                                <i class="icon-file-text-o"></i>
                                                                            </div>

                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="title-sitemap margin-top">
                                                                    <span class="label-success-sitemap"
                                                                          data-info-sitemap-link="{$item.cron_url|escape:'quotes':'UTF-8'}">
                                                                        <label class="control-label status-sitemap mr-1">
                                                                            {l s='Status' mod='sitemappro'}:
                                                                        </label>
                                                                        {if $item.date == '0000-00-00 00:00:00'}
                                                                            <span class="label label-danger">{l s='Not exists' mod='sitemappro'}</span>
                                                                        {else}
                                                                            <span class="label label-success">{l s='Exists' mod='sitemappro'}</span>
                                                                            <br>
                                                                            <a target="_blank"
                                                                               href="{$item.link|escape:'quotes':'UTF-8'}">{$item.link|escape:'quotes':'UTF-8'}</a>
                                                                            ({$item.date|escape:'quotes':'UTF-8'})
                                                                        {/if}
                                                                </div>
                                                                <div class="link-pages-sitemap"
                                                                     data-pages="{$item.cron_url|escape:'quotes':'UTF-8'}">
                                                                    {if is_array($item.pages) && count($item.pages)}
                                                                        <ul>
                                                                            {foreach from=$item.pages item=page}
                                                                                <li>
                                                                                    <a target="_blank"
                                                                                       href="{$page|escape:'quotes':'UTF-8'}">
                                                                                        {$page|escape:'quotes':'UTF-8'}
                                                                                    </a>
                                                                                </li>
                                                                            {/foreach}
                                                                        </ul>
                                                                    {/if}
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="separator"></div>
                                                    {/foreach}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                {/if}

                            </div>
                            <script>
                                $('.tab_sitemap_{$sitemap_type|escape:'quotes':'UTF-8'}').tabContainer();
                            </script>
                        {/foreach}
                    </div>

                    <div class="separator"></div>

                    <div id="additional-settings" class="row">
                        <div class="col-lg-12">
                            <h2>
                                {l s='Additional settings' mod='sitemappro'}
                            </h2>
                        </div>

                        <div class="settings-block clearfix">
                            <div class="col-md-12">
                                <div class="sitemap_row clearfix">
                                    <label class="control-label margin-right float-left margin-top">{l s='Item per sitemap' mod='sitemappro'}</label>
                                    <input type="text" class="form-control fixed-width-sm float-left margin-top"
                                           name="ITEM_PER_SITEMAP"
                                           value="{ConfSMP::getConf('ITEM_PER_SITEMAP')|escape:'quotes':'UTF-8'}">
                                </div>
                                <div class="sitemap_row clearfix margin-top">
                                    <label class="control-label margin-right float-left margin-top">{l s='The symbol(s) at the end of the image legend that exclude this image from the map' mod='sitemappro'}</label>
                                    <input type="text" class="form-control fixed-width-sm float-left margin-top"
                                           name="SYMBOL_LEGEND"
                                           value="{ConfSMP::getConf('SYMBOL_LEGEND')|escape:'quotes':'UTF-8'}">
                                </div>
                            </div>
                            <div class="col-md-12 margin-top">
                                <div class="sitemap_row clearfix">
                                    <label class="control-label margin-right float-left">{l s='Export sitemaps in robots.txt' mod='sitemappro'}</label>

                                    <div class="switch prestashop-switch fixed-width-lg float-left">
                                        <input type="radio" {if ConfSMP::getConf('EXPORT_IN_ROBOTS')}checked{/if}
                                               name="EXPORT_IN_ROBOTS" value="1" id="EXPORT_IN_ROBOTS_1">
                                        <label for="EXPORT_IN_ROBOTS_1">{l s='Yes' mod='sitemappro'}</label>
                                        <input type="radio" {if !ConfSMP::getConf('EXPORT_IN_ROBOTS')}checked{/if}
                                               name="EXPORT_IN_ROBOTS" value="0" id="EXPORT_IN_ROBOTS_0">
                                        <label for="EXPORT_IN_ROBOTS_0">{l s='No' mod='sitemappro'}</label>
                                        <a class="slide-button btn"></a>
                                    </div>

                                </div>

                                <div class="sitemap_row clearfix margin-top">
                                    <label class="control-label margin-right float-left">{l s='Export category images' mod='sitemappro'}</label>

                                    <div class="switch prestashop-switch fixed-width-lg float-left">
                                        <input type="radio" {if ConfSMP::getConf('EXPORT_CATEGORY_IMAGE')}checked{/if}
                                               name="EXPORT_CATEGORY_IMAGE" value="1" id="EXPORT_CATEGORY_IMAGE_1">
                                        <label for="EXPORT_CATEGORY_IMAGE_1">{l s='Yes' mod='sitemappro'}</label>
                                        <input type="radio" {if !ConfSMP::getConf('EXPORT_CATEGORY_IMAGE')}checked{/if}
                                               name="EXPORT_CATEGORY_IMAGE" value="0" id="EXPORT_CATEGORY_IMAGE_0">
                                        <label for="EXPORT_CATEGORY_IMAGE_0">{l s='No' mod='sitemappro'}</label>
                                        <a class="slide-button btn"></a>
                                    </div>

                                </div>

                                <div class="sitemap_row clearfix margin-top EXPORT_COMBINATION_BLOCK">
                                    <label class="control-label margin-right float-left">{l s='Export combinations products' mod='sitemappro'}</label>

                                    <div class="switch prestashop-switch fixed-width-lg float-left EXPORT_COMBINATION_BLOCK_RADIO">
                                        <input type="radio" {if ConfSMP::getConf('EXPORT_COMBINATION')}checked{/if}
                                               name="EXPORT_COMBINATION" value="1" id="EXPORT_COMBINATION_1">
                                        <label for="EXPORT_COMBINATION_1">{l s='Yes' mod='sitemappro'}</label>
                                        <input type="radio" {if !ConfSMP::getConf('EXPORT_COMBINATION')}checked{/if}
                                               name="EXPORT_COMBINATION" value="0" id="EXPORT_COMBINATION_0">
                                        <label for="EXPORT_COMBINATION_0">{l s='No' mod='sitemappro'}</label>
                                        <a class="slide-button btn"></a>
                                    </div>

                                </div>

                                <!----  экспорт комбинаций по умолчанию -->

                                <div class="sitemap_row clearfix margin-top EXPORT_COMBINATION_DEF_BLOCK">
                                    <label class="control-label margin-right float-left">{l s='Export combinations default' mod='sitemappro'}</label>

                                    <div class="switch prestashop-switch fixed-width-lg float-left">
                                        <input type="radio" {if ConfSMP::getConf('EXPORT_COMBINATION_DEF')}checked{/if}
                                               name="EXPORT_COMBINATION_DEF" value="1" id="EXPORT_COMBINATION_DEF_1">
                                        <label for="EXPORT_COMBINATION_DEF_1">{l s='Yes' mod='sitemappro'}</label>
                                        <input type="radio" {if !ConfSMP::getConf('EXPORT_COMBINATION_DEF')}checked{/if}
                                               name="EXPORT_COMBINATION_DEF" value="0" id="EXPORT_COMBINATION_DEF_0">
                                        <label for="EXPORT_COMBINATION_DEF_0">{l s='No' mod='sitemappro'}</label>
                                        <a class="slide-button btn"></a>
                                    </div>

                                </div>

                                <!----  конец экспорт по умолчанию -->


                                <div class="sitemap_row clearfix margin-top">
                                    <label class="control-label margin-right float-left">{l s='Allow image caption attribute' mod='sitemappro'}</label>

                                    <div class="switch prestashop-switch fixed-width-lg float-left">
                                        <input type="radio"
                                               {if ConfSMP::getConf('ALLOW_IMAGE_CAPTION_ATTR')}checked{/if}
                                               name="ALLOW_IMAGE_CAPTION_ATTR" value="1"
                                               id="ALLOW_IMAGE_CAPTION_ATTR_1">
                                        <label for="ALLOW_IMAGE_CAPTION_ATTR_1">{l s='Yes' mod='sitemappro'}</label>
                                        <input type="radio"
                                               {if !ConfSMP::getConf('ALLOW_IMAGE_CAPTION_ATTR')}checked{/if}
                                               name="ALLOW_IMAGE_CAPTION_ATTR" value="0"
                                               id="ALLOW_IMAGE_CAPTION_ATTR_0">
                                        <label for="ALLOW_IMAGE_CAPTION_ATTR_0">{l s='No' mod='sitemappro'}</label>
                                        <a class="slide-button btn"></a>
                                    </div>

                                </div>
                                <div class="sitemap_row clearfix margin-top">
                                    <label class="control-label margin-right float-left">{l s='Include id in attribute' mod='sitemappro'}</label>

                                    <div class="switch prestashop-switch fixed-width-lg float-left">
                                        <input type="radio"
                                               {if ConfSMP::getConf('INCLUDE_ID_IN_ATTRIBUTE')}checked{/if}
                                               name="INCLUDE_ID_IN_ATTRIBUTE" value="1" id="INCLUDE_ID_IN_ATTRIBUTE_1">
                                        <label for="INCLUDE_ID_IN_ATTRIBUTE_1">{l s='Yes' mod='sitemappro'}</label>
                                        <input type="radio" {if !ConfSMP::getConf('INCLUDE_ID_IN_ATTRIBUTE')}checked{/if}
                                               name="INCLUDE_ID_IN_ATTRIBUTE" value="0" id="INCLUDE_ID_IN_ATTRIBUTE_0">
                                        <label for="INCLUDE_ID_IN_ATTRIBUTE_0">{l s='No' mod='sitemappro'}</label>
                                        <a class="slide-button btn"></a>
                                    </div>

                                </div>
                                <div class="sitemap_row clearfix margin-top float-left">
                                    <label class="control-label font-weight-bold">
                                        <a target="_blank" href="{$link_error_log|escape:'quotes':'UTF-8'}">
                                            {l s='Link on error.log' mod='sitemappro'}
                                        </a>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="tn-box_sitemap_not_created tn-box mv_error">
        <span class="message_mv_content">{l s='Error. Sitemap is not created.' mod='sitemappro'}</span>
    </div>
    <div class="tn-box_copy tn-box">
        <span class="message_mv_content">{l s='Success. Copied.' mod='sitemappro'}</span>
    </div>
    {include file="./templates.tpl"}
    <script src='https://seosaps.com/ru/module/seosamanager/manager?ajax=1&action=script&iso_code={Context::getContext()->language->iso_code|escape:'quotes':'UTF-8'}'></script>
{/if}
<script>
    $('#cms-paginator').pagination({
        total: 100, // всего записей
        current: 1, // текущая кнопка
        length: 20, // количество выводимых записей
        size: 2,
        ajax: function(options, refresh, $target){
            $('.saveSiteMapConf').click();
            $.ajax({
                url: document.location.href.replace('#'+document.location.hash, ''),
                type: 'POST',
                dataType: 'json',
                data: {
                    search: '',
                    ajax: true,
                    current: options.current,
                    length: options.length,
                    action: 'cms_paginator'
                },
                beforeSend: function () {
                    $('.loader').removeClass('hidden');
                },
                    }).done(function(res){
                        refresh({
                            total: res.total,
                            length: res.length
                        });
                        $('.sitemap_cms').html(res.data);
                 $('.loader').addClass('hidden');
            }).fail(function(error){
                $('.loader').addClass('hidden');
                    });
        }
    });
</script>
<style>
    .lds-dual-ring.hidden {
        display: none;
    }
    .overlay {
        position: fixed;
        top: 50%;
        left: 50%;
        width: 100%;
        height: 100vh;
        z-index: 999;
        opacity: 1;
        transition: all 0.5s;
    }

    .lds-dual-ring {
        display: inline-block;
        width: 80px;
        height: 80px;
    }
    .lds-dual-ring:after {
        content: " ";
        display: block;
        width: 64px;
        height: 64px;
        margin: 5% auto;
        border-radius: 50%;
        border: 6px solid #08ff00;
        border-color: #09dbee transparent #09dbee transparent;
        animation: lds-dual-ring 1.2s linear infinite;
    }
    @keyframes lds-dual-ring {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }
</style>