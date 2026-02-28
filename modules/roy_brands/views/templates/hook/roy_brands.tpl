{*
* 2007-2016 PrestaShop
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2018 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div id="roy_brands" data-in-row="{if isset($roythemes.brand_per_row)}{$roythemes.brand_per_row}{else}6{/if}">
  <h1 class="h1 products-section-title text-uppercase">
    {if $display_link_brand}<a href="{$page_link}" title="{l s='Brands' mod='roy_brands'}">{/if}
      {l s='Brands' mod='roy_brands'}
      <i></i>
    {if $display_link_brand}</a>{/if}
  </h1>

  <div class="row">
    {if isset($roythemes.brand_w) && $roythemes.brand_w == "1"}<div class="container">{/if}
      {if $brands}
        {include file="module:roy_brands/views/templates/_partials/$roy_brand_display_type.tpl" brands=$brands}
      {else}
        <p>{l s='No brand' mod='roy_brands'}</p>
      {/if}
    {if isset($roythemes.brand_w) && $roythemes.brand_w == "1"}</div>{/if}
  </div>
</div>
