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
  {if $page.page_name == 'index'}
   <link rel="stylesheet" href="/modules/crazyelements/assets/css/frontend/css/post-page-2-1-1.css" type="text/css" media="all">
   <link rel="stylesheet" href="/modules/crazyelements/assets/css/frontend/css/post-page-25-1-1.css" type="text/css" media="all">
{/if}


{block name='header_top'}
  <div class="header-top">
    <div class="container">
       <div class="row action header_lay{if isset($roythemes.header_lay)}{$roythemes.header_lay}{/if}">
       {if isset($roythemes.header_lay) && $roythemes.header_lay == "1"}
        <div class="col-md-2" id="_desktop_logo">
          <a href="{$urls.base_url}" title="Plexiglass sur mesure – Plexi Cindar, spécialiste du PMMA">
            <img class="logo img-responsive" src="/modules/roy_customizer/upload/logo-plexi.svg" alt="Plexi Cindar – Plexiglass sur mesure, plaque plexi en PMMA">
          </a>
        </div>
        <div class="col-md-10 col-sm-12 position-static hidden-sm-down">
          <div class="row">
            {hook h='displayTop'}
            <div class="clearfix"></div>
          </div>
        </div>
      {/if}
      </div>
    </div>
  </div>
  {hook h='displayNavFullWidth'}
{/block}

