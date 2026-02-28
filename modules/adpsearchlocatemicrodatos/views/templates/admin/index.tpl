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

<div class="clearfix adpsearchlocatemicrodatos-admin" data-role="adpsearchlocatemicrodatos-admin">
    {if isset($confirmation)}
    <div class="alert alert-success">{l s='Settings updated' mod='adpsearchlocatemicrodatos'}
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    </div>
    {/if}
    {if isset($error)}
        <div class="alert alert-danger">{l s='Oops, there was a problem and your setting weren\'t updated' mod='adpsearchlocatemicrodatos'}
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        </div>
    {/if}
    <h2 id="titulo_admin_adpsearchlocatemicrodatos"><img class="logo_adalop" src="{$module_dir|escape:'htmlall':'UTF-8'}logo.png">{$module_display}</h2>
    <div class="col-lg-3">
        <div class="list-group" id="adpsearchlocatemicrodatos-tabs">
            <a href="#tab_search" class="list-group-item {if ($active_tab == '#tab_search')} active {/if}" data-toggle="tab"><i class="icon-search"></i> {l s='Search microdata' mod='adpsearchlocatemicrodatos'}</a>
            <a href="#tab_analyzes" class="list-group-item {if ($active_tab == '#tab_analyzes')} active {/if}" data-toggle="tab"><i class="icon-eye"></i> {l s='Analyzes microdata' mod='adpsearchlocatemicrodatos'}</a>
            <a href="#tab_configuration" class="list-group-item {if ($active_tab == '#tab_configuration')} active {/if}" data-toggle="tab"><i class="icon-cog"></i> {l s='Configuration' mod='adpsearchlocatemicrodatos'}</a>
            <a href="#tab_modules_related" class="list-group-item {if ($active_tab == '#tab_modules_related')} active {else} '' {/if}" data-toggle="tab"><i class="icon-plug"></i> {l s='Modules developed by Ádalop' mod='adpsearchlocatemicrodatos'}</a>
        </div>
        <div class="list-group">

            <a class="list-group-item" href="javascript:$('#clearcache').submit();"><i class="icon-trash-o"></i> {l s='CLEAR CACHE' mod='adpsearchlocatemicrodatos'}</a>

            <form method="post" action="" class="hidden" id="clearcache" name="clearcache">

                <input type="hidden" value="1" name="ClearCache">

                <input type="submit" name="submitClearCache" value="1">

            </form>

        </div>
        <div class="list-group">
            <a class="list-group-item" target="_blank" href="{$module_dir|escape:'htmlall':'UTF-8'}{$guide_link|escape:'htmlall':'UTF-8'}">
                <i class="icon-download"></i> {l s='DOCUMENTATION' mod='adpsearchlocatemicrodatos'}
            </a>
        </div>
        <div class="list-group">

            <a class="list-group-item" href="https://addons.prestashop.com/{$iso_code_language}/ratings.php" target="_blank"><i class="icon-star"></i> {l s='RATE MODULE' mod='adpsearchlocatemicrodatos'}</a>

        </div>
        <div class="list-group">

            <a class="list-group-item" href="https://addons.prestashop.com/es/contacte-con-nosotros?id_product=47564" target="_blank"><i class="icon-envelope"></i> {l s='CONTACT US' mod='adpsearchlocatemicrodatos'}</a>

        </div>
        <div class="list-group">
            <span disabled="true" class="list-group-item"><i class="icon-info"></i> {l s='Version' mod='adpsearchlocatemicrodatos'} {$module_version|escape:'htmlall':'UTF-8'} {l s='developer by ' mod='adpsearchlocatemicrodatos'} Adalop</span> 
        </div>
    </div>
    <div class="tab-content col-lg-9">
        <!-- SEARCH TEMPLATE -->
        <div class="tab-pane panel {if ($active_tab == '#tab_search')} active {/if}" id="tab_search">
            {include file="./search.tpl"}
        </div>
        <!-- VERIFY TEMPLATE -->
        <div class="tab-pane panel {if ($active_tab == '#tab_analyzes')} active {/if}" id="tab_analyzes">
            {include file="./analysis.tpl"}
        </div>
        <!-- CONFIGURATION TEMPLATE -->
        <div class="tab-pane panel {if ($active_tab == '#tab_configuration')} active {/if}" id="tab_configuration">
            {include file="./configuration.tpl"}
        </div>
        <!-- RELATED MODULES TEMPLATE -->
        <div class="tab-pane panel {if ($active_tab == '#tab_modules_related')} active {/if}" id="tab_modules_related">
            {include file="./modules_related.tpl"}
        </div>
    </div>
</div>


