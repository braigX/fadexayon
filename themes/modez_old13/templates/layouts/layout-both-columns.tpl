{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 *}
    <!doctype html>
    <html lang="{$language.locale}">

    <head>
        {block name='head'}
        {include file='_partials/head.tpl'}
        {/block}
    </head>

    <body id="{$page.page_name}" class="shop-{$shop.id} {$page.body_classes|classnames}{if isset($roythemes.o_add)} add{$roythemes.o_add}{/if}" data-layout="{if isset($roythemes.g_lay)}{$roythemes.g_lay}{/if}">
        {*<div class="overlay" style="background-color: rgba(0, 0, 0, 0.227); display: none;"></div>*}
        {if isset($roythemes.nc_loader) && $roythemes.nc_loader == "1"}
        <div class="roy-loader">
            {if isset($roythemes.nc_loader_logo) && $roythemes.nc_loader_logo !== "1"}
            <img class="logo_loader" src="{$urls.base_url}modules/roy_customizer/upload/logo-loader-{Context::getContext()->shop->id}.{if isset($roythemes.nc_loader_logo_ext)}{$roythemes.nc_loader_logo_ext}{else}png?format=webp{/if}" alt="{$shop.name}">
            {/if}
        </div>
        {/if}

        {block name='hook_after_body_opening_tag'}
        {hook h='displayAfterBodyOpeningTag'}
        {/block}

        <main class="roy-reload {if isset($roythemes.nc_loader) && $roythemes.nc_loader == " 1"}animsition{/if}">

            {if isset($roythemes.g_lay) && $roythemes.g_lay == "4"}<div class="lay_boxed">{/if}

                {block name='product_activation'}
                {include file='catalog/_partials/product-activation.tpl'}
                {/block}

                <header id="header">
                    {block name='header'}
                    {include file='_partials/header.tpl'}
                    {/block}
                </header>

                {block name='notifications'}
                {include file='_partials/notifications.tpl'}
                {/block}

                {block name='breadcrumb'}
                {include file='_partials/breadcrumb.tpl'}
                {/block}

                <div id="wrapper" class="stick_parent_lb">
                    {hook h="displayWrapperTop"}
                    <div class="{if $page.page_name == 'product'}page_product {else} container {/if}">

                        {hook h="displayLeviBox"}

                        {block name="top_column"}
                        <div id="top_column">
                            {hook h='displayTopColumn'}
                        </div>
                        {/block}

                        {block name="left_column"}
                        <div id="left-column" class="col-md-12 col-lg-3 side-column">
                            {if $page.page_name == 'product'}
                            {hook h='displayLeftColumnProduct'}
                            {else}
                            {hook h="displayLeftColumn"}
                            {/if}
                        </div>
                        {/block}

                        {block name="content_wrapper"}
                        <div id="content-wrapper" class="left-column right-column col-sm-4 col-md-12">
                            {hook h="displayContentWrapperTop"}
                            {block name="content"}
                            <p>Hello world! This is HTML5 Boilerplate.</p>
                            {/block}
                            {hook h="displayContentWrapperBottom"}
                        </div>
                        {/block}

                        {block name="right_column"}
                        <div id="right-column" class="col-md-12 col-lg-3 side-column">
                            {if $page.page_name == 'product'}
                            {hook h='displayRightColumnProduct'}
                            {else}
                            {hook h="displayRightColumn"}
                            {/if}
                        </div>
                        {/block}
                    </div>
                    {hook h="displayWrapperBottom"}
                </div>
                 {*Add with team wassim novatis*}
                {if $page.page_name == 'category'}
                {if $category.additional_description}
                {*<nav class="flex-nav ">
                 <div class="flex-nav__inner">
				 <ul>
					<li><a href="#fc_index_3" class="hidden">description</a></li>
				 </ul>
	             </div>
                </nav>*}
                <section id="category-additional-description" class="section--grey">
                    <div class="container">
                        <div class="flexible-content__blocks">
                          <div id="additional-description">{$category.additional_description nofilter}</div>
                        </div>
                    </div>
                </section>
                {/if}
                {/if}
                {*End*}

                <footer id="footer">
                    {block name="footer"}
                    {include file="_partials/footer.tpl"}
                    {/block}
                </footer>

                <div class="side_menu">
                    <div class="side_menu_rel">
                        <div id="side_cart_wrap">
                            {widget name='ps_shoppingcart'}
                        </div>
                        <div id="side_search_wrap">
                            <div class="side_title h4">{l s='Search' d='Shop.Theme.Catalog'}</div>
                            {widget name='ps_searchbar'}
                            <div id="search_results">
                            </div>
                            {hook h="displaySideSearch"}
                        </div>
                        <div id="side_menu_wrap">
                            {hook h="displaySideMenu"}
                        </div>
                        <div id="side_mail_wrap">
                            {hook h="displaySideMail"}
                        </div>
                        <div id="side_acc_wrap">
                            {hook h="displaySideAcc"}
                        </div>
                    </div>
                </div>
                <div class="side_close">
                    <i>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </i>
                </div>

                {if isset($roythemes.g_lay) && $roythemes.g_lay == "4"}
            </div>{/if}

        </main>

        {block name='javascript_bottom'}
        {include file="_partials/javascript.tpl" javascript=$javascript.bottom}
        {/block}

        {block name='hook_before_body_closing_tag'}
        {hook h='displayBeforeBodyClosingTag'}
        {/block}
    </body>

    </html>