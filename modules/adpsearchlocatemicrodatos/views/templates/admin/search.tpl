{*
* 2007-2022 PrestaShop
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
*  @copyright 2022 Ádalop
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}

<h3>
    <i class="icon-search"></i>
    {l s='Search microdata' mod='adpsearchlocatemicrodatos'}
    <small>{$module_display}</small>
    <a class="btn pull-right" role="button" data-toggle="modal" href="#adpsearchlocate-legend" aria-expanded="false" aria-controls="adpsearchlocate-legend" title="{l s='Help legend' mod='adpsearchlocatemicrodatos'}">
        <i class="icon-question"></i>
    </a>
</h3>

{if $temp_folder_unwriteble}
    <div class="alert alert-danger" role="alert">
        {l s='The backups folder has not write permissions. Change the folder permissions.' mod='adpsearchlocatemicrodatos'}
    </div>
{else}

<div class="modal fade" id="adpsearchlocate-legend" tabindex="-1" role="dialog" aria-labelledby="adpsearchlocate-legend-label">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="adpsearchlocate-legend-label">{l s='Help legend' mod='adpsearchlocatemicrodatos'}</h4>
            </div>
            <div class="modal-body">
                <div class="panel">
                    <div class="panel-heading">
                        {l s='Theme/Module global state colors' mod='adpsearchlocatemicrodatos'}
                    </div>
                    <div class="alert alert-caption" role="alert" style="background">
                        <i class="icon-puzzle-piece"></i>{l s='This status color means that the theme/module has not been scanned.' mod='adpsearchlocatemicrodatos'}
                    </div>
                    <div class="alert alert-caption analyzed" role="alert">
                        <i class="icon-puzzle-piece"></i>{l s='This status color means that the theme/module contains microdata and it is advisable to clean it.' mod='adpsearchlocatemicrodatos'}
                    </div>
                    <div class="alert alert-caption clean" role="alert">
                        <i class="icon-puzzle-piece"></i>{l s='This status color means that the theme/module has been scanned and does not contain microdata.' mod='adpsearchlocatemicrodatos'}
                    </div>
                    <div class="alert alert-caption fixed" role="alert">
                        <i class="icon-puzzle-piece"></i>{l s='This status color means that the theme/module contained microdata and has been cleaned.' mod='adpsearchlocatemicrodatos'}
                    </div>
                    <div class="alert alert-caption partial-fixed" role="alert">
                        <i class="icon-puzzle-piece"></i>{l s='This status color means that the theme/module has been partially cleaned, it still contains files with microdata.' mod='adpsearchlocatemicrodatos'}
                    </div>
                </div>

                <div class="panel">
                    <div class="panel-heading">
                        {l s='Action buttons' mod='adpsearchlocatemicrodatos'}
                    </div>
                    <div class="input-group">
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="button"><i class="icon-search"></i></button>
                        </span>
                        <span  class="form-control">{l s='Scan the theme/module to locate embedded microdata in the source code files.' mod='adpsearchlocatemicrodatos'}</span>
                    </div>
                    <br />
                    <div class="input-group">
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="button"><i class="icon-magic"></i></button>
                        </span>
                        <span  class="form-control">{l s='Removes the microdata embedded in the theme/module files.' mod='adpsearchlocatemicrodatos'}</span>
                    </div>
                    <br />
                    <div class="input-group">
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="button"><i class="icon-undo"></i></button>
                        </span>
                        <span  class="form-control">{l s='Restores the theme/module files to their original state before microdata cleaning.' mod='adpsearchlocatemicrodatos'}</span>
                    </div>
                    <br />
                    <div class="input-group">
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="button"><i class="icon-download"></i></button>
                        </span>
                        <span  class="form-control">{l s='Download a log file about the changes made by our module in the theme/module.' mod='adpsearchlocatemicrodatos'}</span>
                    </div>
                    <br />
                    <div class="input-group">
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="button"><i class="icon-eye"></i></button>
                        </span>
                        <span  class="form-control">{l s='Displays the microdata found in the theme/module files or the corrections made by our module.' mod='adpsearchlocatemicrodatos'}</span>
                    </div>
                </div>

                <div class="panel">
                    <div class="panel-heading">
                        {l s='File state labels' mod='adpsearchlocatemicrodatos'}
                    </div>
                    <div>
                        <span class="label label-success">{l s='fixed' mod='adpsearchlocatemicrodatos'}</span>
                        {l s='The file has been cleaned.' mod='adpsearchlocatemicrodatos'}
                    </div>
                    <br />
                    <div>
                        <span class="label label-danger">{l s='error' mod='adpsearchlocatemicrodatos'}</span>
                        {l s='The file contains microdata. It should be cleaned.' mod='adpsearchlocatemicrodatos'}
                    </div>
                    <br />
                    <div>
                        <span class="label label-warning">{l s='unwritable' mod='adpsearchlocatemicrodatos'}</span>
                        {l s='Write permissions are not available to clean the file.' mod='adpsearchlocatemicrodatos'}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="form-horizontal" data-role="search-section">
    <div class="form-group">
        <label class="control-label col-sm-3">{l s='Select the folder to examine' mod='adpsearchlocatemicrodatos'}</label>
        <div class="col-sm-9">
            <div class="input-group">
                <select name="searchContext">
                    <option value="1" selected="selected">{l s='All' mod='adpsearchlocatemicrodatos'}</option>
                    <option value="2">{l s='Themes' mod='adpsearchlocatemicrodatos'}</option>
                    <option value="3">{l s='Modules' mod='adpsearchlocatemicrodatos'}</option>
                </select>
                <span class="input-group-btn">
                    <button class="btn btn-default" data-confirm-message="{l s='This operation may take too long. Are you sure you want to continue?' mod='adpsearchlocatemicrodatos'}" type="button" data-async-action="scanAll" data- data-search-context="1" title="{l s='Search' mod='adpsearchlocatemicrodatos'}"><i class="icon-search"></i></button>
                </span>
            </div>
        </div>
    </div>

    <div class="form-group" data-role="search-themes-section">
        <h4>{l s='Themes' mod='adpsearchlocatemicrodatos'}</h4>

        {if count($scanResult['themes']) == 0}
            <div class="alert alert-info">{l s='No results' mod='adpsearchlocatemicrodatos'}</div>
        {else}
            {foreach from=$scanResult['themes'] item=themeData key=themeName}
                <table class="table table-striped table-condensed table-break-words">
                    {if !$themeData.analyzed}
                        <caption>
                    {elseif count($themeData.files) == 0}
                        <caption class="clean">
                    {elseif $themeData.fixed > 0 && $themeData.fixed == count($themeData.files)}
                        <caption class="fixed">
                    {else}
                        <caption class="analyzed">
                    {/if}
                        <span>
                            <i class="icon-puzzle-piece"></i>
                            {$themeName}
                        </span>
                        <div class="btn-group pull-right">
                            {if $themeData.analyzed && count($themeData.files) > 0} 
                                <a data-role="download-button" href="{$smarty.server.REQUEST_URI}&asyncAction=downloadThemeScanResult&themeName={$themeName}" class="btn btn-default btn-sm ">
                                    <i class="icon-download"></i>  {l s='Log download' mod='adpsearchlocatemicrodatos'}
                                </a>
                            {/if}
                            {if $themeData.microdataCount > 0}
                                <button type="button" title="{l s='Fix theme' mod='adpsearchlocatemicrodatos'}" data-async-action="fixTheme" data-theme-name="{$themeName}" class="btn btn-default btn-sm">
                                    <i class="icon-magic"></i>
                                </button>
                            {/if}
                            {if $themeData.fixed}
                                <button type="button" title="{l s='Recover theme' mod='adpsearchlocatemicrodatos'}" data-async-action="recoveryTheme" data-theme-name="{$themeName}" class="btn btn-default btn-sm"  data-modified="{$themeData.modified}" data-confirm-message="{l s='Are you sure you want to restore all the files? Some files have been modified since your last backup made by our module and you could lose the changes you made to them.' mod='adpsearchlocatemicrodatos'}">
                                    <i class="icon-undo"></i>
                                </button>
                            {/if}
                            <button type="button" class="btn btn-default btn-sm " data-async-action="scanTheme" data-theme-name="{$themeName}" title="{l s='Search' mod='adpsearchlocatemicrodatos'}"><i class="icon-search"></i></button>
                        </div>
                    </caption>
                {if $themeData.analyzed && count($themeData.files) > 0}
                    <thead>
                        <th><span class="title_box">{l s='Status' mod='adpsearchlocatemicrodatos'}</span></th>
                        <th><span class="title_box">{l s='File name' mod='adpsearchlocatemicrodatos'}</span></th>
                        <th><span class="title_box">{l s='File path' mod='adpsearchlocatemicrodatos'}</span></th>
                        <th><span class="title_box">{l s='Microdata count' mod='adpsearchlocatemicrodatos'}</span></th>
                        <th style="width:96px;text-align:right;"></th>
                    </thead>
                    <tbody>
                    {foreach from=$themeData.files item=themeFile key=fileIndex}
                        <tr>
                            <td>
                                {if !$themeFile.hasPermissions}
                                    <span class="label label-warning">{l s='unwritable' mod='adpsearchlocatemicrodatos'}</span>
                                {elseif $themeFile.fixed}
                                    <span class="label label-success">{l s='fixed' mod='adpsearchlocatemicrodatos'}</span>
                                {else}
                                    <span class="label label-danger">{l s='error' mod='adpsearchlocatemicrodatos'}</span>
                                {/if}
                            </td>
                            <td>{$themeFile.fileName}</td>
                            <td>{$themeFile.filePath}</td>
                            <td>{$themeFile.microdataCount}</td>
                            <td style="text-align:right;">
                                <div class="btn-group btn-group-sm">
                                    {if $themeFile.fixed}
                                        <button type="button" class="btn btn-default btn-sm" title="{l s='Show diff' mod='adpsearchlocatemicrodatos'}" data-async-action="getDiff" data-file-path="{$themeFile.filePath}">
                                            <i class="icon-eye"></i>
                                        </button>
                                    {else}
                                            <button type="button" class="btn btn-default btn-sm" data-async-action="getScanResult" data-scan-type="themes" data-scan-name="{$themeName}" data-file-index="{$fileIndex}">
                                            <i class="icon-eye"></i>
                                        </button>
                                    {/if}
                                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="icon-caret-down"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right">
                                        {if $themeFile.microdataCount > 0}
                                            <li>
                                                <a href="javascript: void(0)" title="{l s='Fix file' mod='adpsearchlocatemicrodatos'}" data-async-action="fixFile" data-theme-name="{$themeName}" data-file-path="{$themeFile.filePath}">
                                                    <i class="icon-magic"></i> 
                                                    {l s='Fix file' mod='adpsearchlocatemicrodatos'}
                                                </a>
                                            </li>
                                        {/if}
                                        {if $themeFile.fixed}
                                            <li>
                                                <a href="javascript: void(0)" title="{l s='Recover file' mod='adpsearchlocatemicrodatos'}" data-async-action="recoveryFile" data-theme-name="{$themeName}" data-file-path="{$themeFile.filePath}" data-modified="{$themeFile.modified}" data-confirm-message="{l s='Are you sure you want to restore the file? The file has been modified since the last backup made by our module and you could lose any changes you have made to it.' mod='adpsearchlocatemicrodatos'}">
                                                    <i class="icon-undo"></i> 
                                                    {l s='Recover file' mod='adpsearchlocatemicrodatos'}
                                                </a>
                                            </li>
                                        {/if}
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    {/foreach}
                    </tbody>
                {/if}
                </table>
            {/foreach}
        {/if}
    </div>
    
    <div class="form-group" data-role="search-modules-section">
        <h4>{l s='Modules' mod='adpsearchlocatemicrodatos'}</h4>
        {if count($scanResult['modules']) == 0}
            <div class="alert alert-info">{l s='No results' mod='adpsearchlocatemicrodatos'}</div>
        {else}
            {foreach from=$scanResult['modules'] item=moduleData key=moduleName}
                <table class="table table-striped table-condensed table-break-words">
                    {if !$moduleData.analyzed}
                        <caption>
                    {elseif count($moduleData.files) == 0}
                        <caption class="clean">
                    {elseif $moduleData.fixed > 0 && $moduleData.fixed == count($moduleData.files)}
                        <caption class="fixed">
                    {elseif $moduleData.fixed > 0 && $moduleData.fixed != count($moduleData.files)}
                        <caption class="partial-fixed">
                    {else}
                        <caption class="analyzed">
                    {/if}
                        {assign var="ruta_imagen" value="{$modulos_dir|escape:'html':'UTF-8'}{$moduleName}/logo.png"}
                        {if file_exists({$ruta_imagen})}
                            <img src="{$base_dir_ssl|escape:'html':'UTF-8'}modules/{$moduleName}/logo.png">
                        {/if}

                        <span>{$moduleData.displayName}</span>
                        <div class="btn-group pull-right">
                            {if $moduleData.analyzed && count($moduleData.files) > 0}
                                <a data-role="download-button" href="{$smarty.server.REQUEST_URI}&asyncAction=downloadModuleScanResult&moduleName={$moduleName}" class="btn btn-default btn-sm">
                                    <i class="icon-download"></i> {l s='Log download' mod='adpsearchlocatemicrodatos'}
                                </a>
                            {/if}
                            {if $moduleData.microdataCount > 0}
                                <button type="button" title="{l s='Fix module' mod='adpsearchlocatemicrodatos'}" data-async-action="fixModule" data-module-name="{$moduleName}" class="btn btn-default btn-sm">
                                    <i class="icon-magic"></i>
                                </button>
                            {/if}
                            {if $moduleData.fixed}
                                <button type="button" title="{l s='Recover module' mod='adpsearchlocatemicrodatos'}" data-async-action="recoveryModule" data-module-name="{$moduleName}" class="btn btn-default btn-sm" data-modified="{$moduleData.modified}" data-confirm-message="{l s='Are you sure you want to restore all the files? Some files have been modified since your last backup made by our module and you could lose the changes you made to them.' mod='adpsearchlocatemicrodatos'}">
                                    <i class="icon-undo"></i>
                                </button>
                            {/if}
                            <button type="button" class="btn btn-default btn-sm" data-async-action="scanModule" data-module-name="{$moduleName}" title="{l s='Search' mod='adpsearchlocatemicrodatos'}"><i class="icon-search"></i></button>
                        </div>
                    </caption>
                {if $moduleData.analyzed && count($moduleData.files) > 0}
                    <thead>
                        <th>{l s='Status' mod='adpsearchlocatemicrodatos'}</th>
                        <th>{l s='File name' mod='adpsearchlocatemicrodatos'}</th>
                        <th>{l s='File path' mod='adpsearchlocatemicrodatos'}</th>
                        <th>{l s='Microdata count' mod='adpsearchlocatemicrodatos'}</th>
                        <th style="width:96px;text-align:right;"></th>
                    </thead>
                    <tbody>
                    {foreach from=$moduleData.files item=moduleFile key=fileIndex}
                        <tr>
                            <td>
                                {if !$moduleFile.hasPermissions}
                                    <span class="label label-warning">{l s='unwritable' mod='adpsearchlocatemicrodatos'}</span>
                                {elseif $moduleFile.fixed}
                                    <span class="label label-success">{l s='fixed' mod='adpsearchlocatemicrodatos'}</span>
                                {/if}
                                {if $moduleFile.microdataCount > 0}
                                    <span class="label label-danger">{l s='error' mod='adpsearchlocatemicrodatos'}</span>
                                {/if}
                            </td>
                            <td>{$moduleFile.fileName}</td>
                            <td>{$moduleFile.filePath}</td>
                            <td>{$moduleFile.microdataCount}</td>
                            <td style="text-align:right;">
                                <div class="btn-group btn-group-sm">
                                    {if $moduleFile.fixed}
                                        <button type="button" class="btn btn-default btn-sm" title="{l s='Show diff' mod='adpsearchlocatemicrodatos'}" data-async-action="getDiff" data-file-path="{$moduleFile.filePath}">
                                            <i class="icon-eye"></i>
                                        </button>
                                    {else}
                                        <button type="button" class="btn btn-default btn-sm" title="Show scan result" data-async-action="getScanResult" data-scan-type="modules" data-scan-name="{$moduleName}" data-file-index="{$fileIndex}">
                                            <i class="icon-eye"></i>
                                        </button>
                                    {/if}
                                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="icon-caret-down"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right">
                                        {if $moduleFile.microdataCount > 0}
                                            <li>
                                                <a href="javascript: void(0)" title="{l s='Fix file' mod='adpsearchlocatemicrodatos'}" data-async-action="fixFile" data-module-name="{$moduleName}" data-file-path="{$moduleFile.filePath}">
                                                    <i class="icon-magic"></i> 
                                                    {l s='Fix file' mod='adpsearchlocatemicrodatos'}
                                                </a>
                                            </li>
                                        {/if}
                                        {if $moduleFile.fixed}
                                            <li>
                                                <a href="javascript: void(0)" title="{l s='Recover file' mod='adpsearchlocatemicrodatos'}" data-async-action="recoveryFile" data-module-name="{$moduleName}" data-file-path="{$moduleFile.filePath}" data-modified="{$moduleFile.modified}" data-confirm-message="{l s='Are you sure you want to restore the file? The file has been modified since the last backup made by our module and you could lose any changes you have made to it.' mod='adpsearchlocatemicrodatos'}">
                                                    <i class="icon-undo"></i> 
                                                    {l s='Recover file' mod='adpsearchlocatemicrodatos'}
                                                </a>
                                            </li>
                                        {/if}
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    {/foreach}
                    </tbody>
                {/if}
                </table>
            {/foreach}
        {/if}
    </div>

    <div id="adpsearchlocatemicrodatos_filedetailsmodal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <button onclick="$('#diff-legend').toggle()" style="margin-right: 10px;" type="button" class="close"><span>?</span></button>
                <h4 class="modal-title">{l s='File details' mod='adpsearchlocatemicrodatos'}</h4>
                <table id='diff-legend' style="display: none;" class="table table-condensed table-striped">
                    <caption>{l s='Help legend' mod='adpsearchlocatemicrodatos'}</caption>
                    <thead>
                        <th class="col-xs-1">{l s='Line' mod='adpsearchlocatemicrodatos'}</th>
                        <th class="col-xs-11">{l s='Content' mod='adpsearchlocatemicrodatos'}</th>
                    </thead>
                    <tbody>
                        <tr>
                            <td>41</td>
                            <td><span class=''>{l s='This line has not been modified by our module. Its content is exactly the same as before the cleanup.' mod='adpsearchlocatemicrodatos'}</span><br></td>
                        </tr>
                        <tr>
                            <td>42</td>
                            <td><span class='diff-deleted'>{l s='This line has been modified by our module. Its content shows the original content of this line.' mod='adpsearchlocatemicrodatos'}</span><br></td>
                        </tr>
                        <tr>
                            <td>42</td>
                            <td><span class='diff-inserted'>{l s='This line has been modified by our module. Its content shows the current content of this line.' mod='adpsearchlocatemicrodatos'}</span><br></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-condensed table-striped adpsearchlocate-results">
                    <thead>
                        <th class="col-xs-1">{l s='Line' mod='adpsearchlocatemicrodatos'}</th>
                        <th class="col-xs-11">{l s='Content' mod='adpsearchlocatemicrodatos'}</th>
                    </thead>
                    <tbody data-role="adpsearchlocate-results"></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">{l s='Close' mod='adpsearchlocatemicrodatos'}</button>
            </div>
        </div>
        </div>
    </div>
</div>

{/if}