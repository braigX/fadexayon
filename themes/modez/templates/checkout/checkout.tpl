{**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
<!doctype html>
<html lang="{$language.iso_code}">

<head>
  {block name='head'}
    {include file='_partials/head.tpl'}
  {/block}
</head>

<body id="{$page.page_name}" class="{$page.body_classes|classnames}{if isset($roythemes.o_add)} add{$roythemes.o_add}{/if}" data-layout="{if isset($roythemes.g_lay)}{$roythemes.g_lay}{/if}">

  {if isset($roythemes.nc_loader) && $roythemes.nc_loader == "1"}
    <div class="roy-loader">
      {if isset($roythemes.nc_loader_logo) && $roythemes.nc_loader_logo !== "1"}
        <img class="logo_loader" src="{$urls.base_url}modules/roy_customizer/upload/logo-loader-{Context::getContext()->shop->id}.{if isset($roythemes.nc_loader_logo_ext)}{$roythemes.nc_loader_logo_ext}{else}png{/if}" alt="{$shop.name}">
      {/if}
    </div>
  {/if}

  {block name='hook_after_body_opening_tag'}
    {hook h='displayAfterBodyOpeningTag'}
  {/block}

  <main class="roy-reload {if isset($roythemes.nc_loader) && $roythemes.nc_loader == "1"}animsition{/if}">

    {if isset($roythemes.g_lay) && $roythemes.g_lay == "4"}<div class="lay_boxed">{/if}

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
      <div class="container">

        {hook h="displayLeviBox"}

      {block name='content'}
        <section id="content">
          <div class="row stick_parent">
            <div class="col-md-8 co_main">
              {block name='cart_summary'}
                {render file='checkout/checkout-process.tpl' ui=$checkout_process}
              {/block}
            </div>
            <div class="col-md-4 stick_static co_right">
              <div class="stick_it">

                {block name='cart_summary'}
                  {include file='checkout/_partials/cart-summary.tpl' cart = $cart}
                {/block}

                {hook h='displayReassurance'}

              </div>
            </div>
          </div>
        </section>
      {/block}
      </div>
      {hook h="displayWrapperBottom"}
    </div>

    <footer id="footer">
      {block name='footer'}
        {include file='_partials/footer.tpl'}
      {/block}
    </footer>

          <div class="side_menu">
            <div class="side_menu_rel">
              <div id="side_cart_wrap">
                {widget name='ps_shoppingcart'}
              </div>
              <div id="side_search_wrap">
                <h4 class="side_title">{l s='Search' d='Shop.Theme.Catalog'}</h4>
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
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><g id="Layer_4" data-name="Layer 4"><path d="M19.78,17,14.83,12l4.95-4.95a1,1,0,0,0,0-1.41L18.36,4.24a1,1,0,0,0-1.41,0L12,9.19,7.05,4.24a1,1,0,0,0-1.41,0L4.22,5.65a1,1,0,0,0,0,1.41L9.17,12,4.22,17a1,1,0,0,0,0,1.41L5.64,19.8a1,1,0,0,0,1.41,0L12,14.85l4.95,4.95a1,1,0,0,0,1.41,0l1.41-1.41A1,1,0,0,0,19.78,17Z" style="fill:none;stroke:#000;stroke-linecap:round;stroke-linejoin:round;stroke-width:2px"/></g></svg>
            </i>
          </div>

          {if isset($roythemes.g_lay) && $roythemes.g_lay == "4"}</div>{/if}

        </main>

    {block name='javascript_bottom'}
      {include file="_partials/javascript.tpl" javascript=$javascript.bottom}
    {/block}

    {block name='hook_before_body_closing_tag'}
      {hook h='displayBeforeBodyClosingTag'}
    {/block}

  </body>

</html>
