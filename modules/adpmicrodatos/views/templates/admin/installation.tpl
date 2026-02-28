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

<div class="tab-pane {if ($active_tab == '#tab_installation')} active {else} '' {/if}" id="tab_installation">
    
    <div class="panel">
        <h3>
            <i class="icon-AdminParentPreferences"></i>
            {l s='Installation' mod='adpmicrodatos'}
            <small>
                {if isset($stage_number_installation) && $stage_number_installation == '0'}
                    {l s='Invalid installation' mod='adpmicrodatos'}
                {elseif isset($stage_number_installation) && $stage_number_installation == '1'}
                    {l s='Processed theme files' mod='adpmicrodatos'}
                {elseif isset($stage_number_installation) && $stage_number_installation == '2'}
                    {l s='Correct Theme' mod='adpmicrodatos'}
                {/if}
            </small>
        </h3>
        {if isset($stage_number_installation) && $stage_number_installation == '0'}
            <div class="alert alert-danger" role="alert">
                {l s='The installation process was not successful.' mod='adpmicrodatos'}
            </div>
            {if $temp_folder_unwriteble}
                <div class="alert alert-danger" role="alert">
                    {l s='The tmp folder has not write permissions. Change the folder permissions and reinstall module.' mod='adpmicrodatos'}
                </div>
            {/if}
        {elseif isset($stage_number_installation) && $stage_number_installation == '1'}
            <div class="installation_adpmicrodatos_sucessful">{l s='Installation successfully completed. You can look at the log to see the affected files' mod='adpmicrodatos'}</div>
        {else if isset($stage_number_installation) && $stage_number_installation == '2'}
            <div class="installation_adpmicrodatos_sucessful">{l s='Installation successfully completed. The template is compatible, no file has been modified.' mod='adpmicrodatos'}</div>
        {/if}
        <br />
        <br />
        {if isset($stage_number_installation) && ($stage_number_installation == '1')}
            <form action="" class="form-horizontal "id="adpmicrodatos_form_log" name="adpmicrodatos_form_log" method="post">
                <div class="form-group">
                    <table class="table table-striped table-condensed table-break-words">
                        <thead>
                            <th><span class="title_box">{l s='File path' mod='adpmicrodatos'}</span></th>
                            <th style="width:96px;text-align:right;"></th>
                        </thead>
                        <tbody>
                         {foreach from=$results_scan_files item=file}
                            <tr>
                                <td>{$file|escape:'htmlall':'UTF-8'}</td>
                                <td style="text-align:right;">
                                    <button type="button" class="btn btn-default btn-sm" title="{l s='Show diff' mod='adpmicrodatos'}" data-async-action="getDiff" data-file-path="{$file|escape:'htmlall':'UTF-8'}">
                                        <i class="icon-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        {/foreach}
                        </tbody>
                    </table>
                </div>
                <div class="form-group">
                    <div class="alert alert-warning" role="alert">
                        {l s='A backup copy of all the files listed above with the *.adpmicrodatos.backup file extension has been generated, we RECOMMEND NOT DELETING these files.' mod='adpmicrodatos'}
                    </div>
                </div>
                <div class="form-group">
                    <button onclick="return confirm('{l s='This action will rescan the theme to remove embedded microdata. Are you sure you want to continue?' mod='adpmicrodatos'}')" type="submit" value="1" id="adpmicrodatos_form_reescan_submit_btn" name="adpmicrodatos_form_reescan_submit_btn" class="btn btn-primary pull-right">
                        <i class="icon-refresh"></i>  {l s='Return to clean microdata' mod='adpmicrodatos'}
                    </button>
                    <button type="submit" value="1" id="adpmicrodatos_form_log_submit_btn" name="adpmicrodatos_form_log_submit_btn" class="btn btn-default pull-right">
                        <i class="icon-download"></i>  {l s='Log download' mod='adpmicrodatos'}
                    </button>
                </div>
            </form>
        {/if}
    </div>
    <div class="panel">
        <h3>
            <i class="icon-AdminParentPreferences"></i>
            {l s='Module analysis' mod='adpmicrodatos'}
            <small>
                {l s='Analyses third-party modules with microdata' mod='adpmicrodatos'}
            </small>
        </h3>
        <form action="" class="form-horizontal "id="adpmicrodatos_form_scan_modules" name="adpmicrodatos_form_scan_modules" method="post">
            <div class="alert alert-info">
                {l s='Below you can see a list of modules that could contain microdata. Be sure to check the configuration of these modules to avoid conflicts.' mod='adpmicrodatos'}
            </div>
            {if !empty($result_scan_modules)}
                <div class="form-group">
                    <table class="table table-striped table-condensed table-break-words">
                        <thead>
                            <tr>
                                <th><span class="title_box">{l s='Module' mod='adpmicrodatos'}</span></th>
                                <th style="width:96px;text-align:right;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach from=$result_scan_modules item=item}
                            <tr>
                                <td>
                                    <img style='max-width: 24px;max-height: 24px;padding: 2px;background-color: white;border-radius: 3px;' src="{$item['logoUrl']|escape:'htmlall':'UTF-8'}">
                                    <span>{$item['fullName']|escape:'htmlall':'UTF-8'} ({$item['name']|escape:'htmlall':'UTF-8'})</span>
                                </td>
                                <td style="width:96px;text-align:right;">
                                    <a href="{$item['url']|escape:'htmlall':'UTF-8'}" target='blank'>
                                        <i class="icon-external-link"></i>
                                    </a>
                                </td>
                            </tr>
                            {/foreach}
                        </tbody>
                    </table>
                </div>
            {/if}

            {if $result_scan_modules_timeout}
                <div class="form-group">
                    <div class="alert alert-warning" role="alert">
                        {l s='The process of scanning the modules is getting too far and you have not finished scanning all the modules. Try again to continue scanning the remaining modules.' mod='adpmicrodatos'}
                    </div>
                </div>
            {/if}
            <div class="form-group">
                <label class="checkbox label-tooltip pull-right"  data-toggle="tooltip" title="{l s='Check this option to rescan all modules even if they were previously scanned.' mod='adpmicrodatos'}">
                    <input type="checkbox" name="adpmicrodatos_fullscanmodules">
                    {l s='Rescan all' mod='adpmicrodatos'}
                    <i class="icon-info-circle"></i>
                </label>
            </div>
            <div class="form-group">
                <button type="submit" onclick="return confirm('{l s='This operation may take too long. Are you sure you want to continue?' mod='adpmicrodatos'}')" value="1" id="adpmicrodatos_form_scan_modules_btn" name="adpmicrodatos_form_scan_modules_btn" class="btn btn-default pull-right">
                    <i class="icon-search"></i> {l s='Scan modules' mod='adpmicrodatos'}
                </button>
            </div>
            <div class="block_related_modules">
                <ul class="block_modules_related related">
                    <li class="item_module"> 
                        <img class="logo_module" src="{$module_dir|escape:'htmlall':'UTF-8'}/views/img/logo_adpsearchlocate.png">
                        <div class="module_title"><span> {l s='Search and Clean Incorrect Microdata - SEO' mod='adpmicrodatos'}</span></div>
                        <div class="module_description"><span>{l s='Locate duplicate or incorrectly configured microdata throughout your online store and delete them. Easy to install and compatible with any template or third party module, will help you configure microdata in your ecommerce.' mod='adpmicrodatos'}</span></div>
                        <div class="module_button"> 
                            {if $iso_code_language == 'es'}
                                <a target="_blank" href="https://prestashop.pxf.io/nL199o" class="module_href">{l s='Discover' mod='adpmicrodatos'}</a>
                            {else if $iso_code_language == 'en'}
                                <a target="_blank" href="https://prestashop.pxf.io/NkkGjO" class="module_href">{l s='Discover' mod='adpmicrodatos'}</a>
                            {else if $iso_code_language == 'fr'}
                                <a target="_blank" href="https://prestashop.pxf.io/oqqNAe" class="module_href">{l s='Discover' mod='adpmicrodatos'}</a>
                            {else if $iso_code_language == 'it'}
                                <a target="_blank" href="https://prestashop.pxf.io/VmmOnJ" class="module_href">{l s='Discover' mod='adpmicrodatos'}</a>
                            {else if $iso_code_language == 'de'}  
                                <a target="_blank" href="https://prestashop.pxf.io/OrrYkK" class="module_href">{l s='Discover' mod='adpmicrodatos'}</a>
                            {else}  
                                <a target="_blank" href="https://prestashop.pxf.io/NkkGjO" class="module_href">{l s='Discover' mod='adpmicrodatos'}</a>
                            {/if}
                        </div>
                    </li>
                </ul>
            </div>
        </form>
    </div>

    <div id="adpmicrodatos_filedetailsmodal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <button onclick="$('#diff-legend').toggle()" style="margin-right: 10px;" type="button" class="close"><span>?</span></button>
                <h4 class="modal-title">{l s='File details' mod='adpmicrodatos'}</h4>
                <table id='diff-legend' style="display: none;" class="table table-condensed table-striped">
                    <caption>{l s='Help legend' mod='adpmicrodatos'}</caption>
                    <thead>
                        <th class="col-xs-1">{l s='Line' mod='adpmicrodatos'}</th>
                        <th class="col-xs-11">{l s='Content' mod='adpmicrodatos'}</th>
                    </thead>
                    <tbody>
                        <tr>
                            <td>41</td>
                            <td><span class=''>{l s='This line has not been modified by our module. Its content is exactly the same as before the cleanup.' mod='adpmicrodatos'}</span><br></td>
                        </tr>
                        <tr>
                            <td>42</td>
                            <td><span class='diff-deleted'>{l s='This line has been modified by our module. Its content shows the original content of this line.' mod='adpmicrodatos'}</span><br></td>
                        </tr>
                        <tr>
                            <td>42</td>
                            <td><span class='diff-inserted'>{l s='This line has been modified by our module. Its content shows the current content of this line.' mod='adpmicrodatos'}</span><br></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-condensed table-striped adpmicrodatos-results">
                    <thead>
                        <th class="col-xs-1">{l s='Line' mod='adpmicrodatos'}</th>
                        <th class="col-xs-11">{l s='Content' mod='adpmicrodatos'}</th>
                    </thead>
                    <tbody data-role="adpmicrodatos-results"></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">{l s='Close' mod='adpmicrodatos'}</button>
            </div>
        </div>
        </div>
    </div>
</div>
