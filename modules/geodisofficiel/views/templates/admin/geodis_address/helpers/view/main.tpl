{*
* 2018 GEODIS
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
*  @author    GEODIS <contact@geodis.com>
*  @copyright 2018 GEODIS
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}

{$menu}

{if !$general_error}
    <h1>{__ s='Admin.Address.index.title.removalSites'}</h1>
    {foreach $removalSites as $site}
        <div class="panel col-sm-5 panelSite js-panelSite">
            <input type="hidden" class="js-siteId" value="{($site->id)|escape:'htmlall':'UTF-8'}" name="siteId" />
            <div class="col-md-8 col-lg-9 col-xs-8">
                <p>{($site->name)|escape:'htmlall':'UTF-8'}</p>
                <p>{($site->address1)|escape:'htmlall':'UTF-8'}</p>
                <P>{($site->address2)|escape:'htmlall':'UTF-8'}</p>
                <p>{($site->zip_code)|escape:'htmlall':'UTF-8'} {($site->city)|escape:'htmlall':'UTF-8'}</p>
                <p>{($site->country_name)|escape:'htmlall':'UTF-8'}</p>
            </div>
            <div class="col-md-4 col-lg-3 col-xs-4 panelSite-default text-right">
                {if ($site->default[1] == 1) }
                    <p class="btn btn-success btn-small">{__ s='Admin.Address.index.defaultSite'}</p>
                {else}
                    <a href="{$url|escape:'htmlall':'UTF-8'}&id_site={($site->id)|escape:'htmlall':'UTF-8'}" class="btn btn-primary btn-small">{__ s='Admin.Address.index.setAsDefault'}</a>
                {/if}
            </div>
        </div>
    {/foreach}
{/if}
