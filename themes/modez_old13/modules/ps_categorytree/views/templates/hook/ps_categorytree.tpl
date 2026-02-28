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
{function name="categories" nodes=[] depth=0}
  {strip}
    {if $nodes|count}
      <ul class="category-sub-menu">
        {foreach from=$nodes item=node}
          {if $node.children}
          <li data-depth="{$depth}" class="has-children">
          {else}
          <li data-depth="{$depth}">{/if}
            {if $depth===0}
              <a href="{$node.link}">{$node.name}</a>
              {if $node.children}
                <div class="navbar-toggler collapse-icons" data-toggle="collapse" data-target="#exCollapsingNavbar{$node.id}">
                  {*<i class="material-icons add">&#xE145;</i>*}
                  <i aria-hidden="true" class="fas fa-chevron-down add"></i>
                  {*<i class="material-icons remove">&#xE15B;</i>*}
                  <i aria-hidden="true" class="fas fa-chevron-up remove"></i>
                </div>
                <div class="collapse" id="exCollapsingNavbar{$node.id}">
                  {categories nodes=$node.children depth=$depth+1}
                </div>
              {/if}
            {else}
              <a class="category-sub-link" href="{$node.link}">{$node.name}</a>
              {if $node.children}
                <span class="arrows" data-toggle="collapse" data-target="#exCollapsingNavbar{$node.id}">
                  <i class="material-icons arrow-right">&#xE315;</i>
                  <i class="material-icons arrow-down">&#xE313;</i>
                </span>
                <div class="collapse" id="exCollapsingNavbar{$node.id}">
                  {categories nodes=$node.children depth=$depth+1}
                </div>
              {/if}
            {/if}
          </li>
        {/foreach}
      </ul>
    {/if}
  {/strip}
{/function}

<div class="block-categories sidebar-block hidden-sm-down">
  <ul class="category-top-menu">
    {*<li class="sidebar-title hidden-sm-down">
      <a href="{$categories.link nofilter}">{$categories.name}</a>
    </li>*}
    <li class="title clearfix hidden-md-up" data-target="#side_cats" data-toggle="collapse">
      <span class="h3">{if $page.page_name == "category"}{$categories.name}{else}{l s='Categories' d='Shop.Theme.Catalog'}{/if}</span>
      <span class="float-xs-right">
        <span class="navbar-toggler collapse-icons">
          {*<i class="material-icons add">&#xE145;</i>*}
          <i aria-hidden="true" class="fas fa-chevron-down add"></i>
          {*<i class="material-icons remove">&#xE15B;</i>*}
          <i aria-hidden="true" class="fas fa-chevron-up remove"></i>
        </span>
      </span>
    </li>
    <li id="side_cats" class="sidebar-content collapse">
	    <h3 class="widget-title">Nos catégories</h3>
      {categories nodes=$categories.children}
    </li>
  </ul>
</div>
