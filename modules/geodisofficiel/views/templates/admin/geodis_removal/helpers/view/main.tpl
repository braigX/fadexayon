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

{if ($error)}
    <div class="alert alert-danger">
        {foreach $error as $err}
            <p>{$err|escape:'htmlall':'UTF-8'}</p>
        {/foreach}
    </div>
{/if}

<div class="alert alert-info js_alertDayOff">
    <p>{__ s='Admin.Removal.index.warning.daysOff'}</p>
</div>

<div class="alert alert-info js_alertWsDayOff">
    <p>{__ s='Admin.ShipmentController.index.error.unavailableWSDate'}</p>
</div>

{if ($success)}
    <div class="alert alert-success" role="alert">
        <p>{__ s='Admin.Removal.index.alert.success'}</p>
    </div>
{/if}

{$form}

<div class="panel">
    <h2 class="text-center">{__ s='Admin.Removal.index.table.title'}</h2>
    <table class="table">
        <thead>
            <tr>
                <th>{__ s='Admin.Removal.index.table.head.reference'}</th>
                <th>{__ s='Admin.Removal.index.table.head.removalDate'}</th>
                <th>{__ s='Admin.Removal.index.table.head.prestation'}</th>
                <th>{__ s='Admin.Removal.index.table.head.removalSite'}</th>
                <th>{__ s='Admin.Removal.index.table.head.volume'}</th>
                <th>{__ s='Admin.Removal.index.table.head.weight'}</th>
                <th>{__ s='Admin.Removal.index.table.head.nbBox'}</th>
                <th>{__ s='Admin.Removal.index.table.head.nbPallet'}</th>
                <th>{__ s='Admin.Removal.index.table.head.print'}</th>
                <th>{__ s='Admin.Removal.index.table.head.dateAdd'}</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            {if ($history)}
                {foreach $history as $record}
                    <tr>
                        <td>{($record['removal']->recept_number)|escape:'htmlall':'UTF-8'}</td>
                        <td>{($record['removal']->removal_date)|escape:'htmlall':'UTF-8'}</td>
                        <td>{$record['prestationName']|escape:'htmlall':'UTF-8'}</td>
                        <td>{$record['siteName']|escape:'htmlall':'UTF-8'} {$record['siteZipCode']|escape:'htmlall':'UTF-8'} {$record['siteCity']|escape:'htmlall':'UTF-8'}</td>
                        <td>{($record['removal']->volume)|escape:'htmlall':'UTF-8'}</td>
                        <td>{($record['removal']->weight)|escape:'htmlall':'UTF-8'}</td>
                        <td>{($record['removal']->number_of_box)|escape:'htmlall':'UTF-8'}</td>
                        <td>{($record['removal']->number_of_pallet)|escape:'htmlall':'UTF-8'}</td>
                        <td><a href="{$record['printUrl']|escape:'htmlall':'UTF-8'}" class="btn btn-primary">{__ s='Admin.Removal.index.table.action.print'}</a></td>
                        <td>{($record['removal']->date_add)|escape:'htmlall':'UTF-8'}</td>
                    </tr>
                {/foreach}
            {else}
                <tr>
                    <td class="list-empty" colspan="11">
                        <div class="list-empty-msg">
                            <i class="icon-warning-sign list-empty-icon"></i>
                            {__ s='Admin.Removal.index.noRecord'}
                        </div>
                    </td>
                </tr>
            {/if}
        </tbody>
    </table>
</div>
