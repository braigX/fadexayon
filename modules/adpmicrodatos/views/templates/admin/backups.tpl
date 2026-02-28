{*
* 2007-2023 PrestaShop
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
*  @author    Ádalop <contact@prestashop.com>
*  @copyright 2023 Ádalop
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}
<div class="tab-pane {if ($active_tab == '#tab_backups')} active {else} '' {/if}" id="tab_backups">
    <div class="panel">
        <h3>
            <i class="icon-hdd"></i>
            {l s='File backup history' mod='adpmicrodatos'}
        </h3>
        <div class="alert alert-info">
            {l s='Through this section you can download a backup of all the files that have been modified by the module. Please note that the backup is performed before performing any action of this type: install, uninstall or reescan (process responsible for searching for structured data in the template files and automatically deleting it).' mod='adpmicrodatos'}
        </div>
        <table class="table table-striped table-condensed table-break-words">
            <thead>
                <th><span class="title_box">{l s='Date' mod='adpmicrodatos'}</span></th>
                <th><span class="title_box">{l s='Type' mod='adpmicrodatos'}</span></th>
                <th><span class="title_box">{l s='Path' mod='adpmicrodatos'}</span></th>
                <th style="width:96px;text-align:right;"></th>
            </thead>
            <tbody>
                {foreach from=$backup_files item=file}
                <tr>
                    <td>{$file['date']|escape:'htmlall':'UTF-8'}</td>
                    <td>{$file['displayType']|escape:'htmlall':'UTF-8'}</td>
                    <td>{$file['link']|escape:'htmlall':'UTF-8'}</td>
                    <td style="text-align:right;">
                        <a href="{$file['link']|escape:'htmlall':'UTF-8'}" class="btn btn-default btn-sm" title="{l s='Download' mod='adpmicrodatos'}">
                            <i class="icon-download"></i>
                        </a>
                    </td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
</div>
