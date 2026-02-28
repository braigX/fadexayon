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



<div class="clearfix" data-role="adpmicrodatos-admin">
    {if isset($confirmation)}
    <div class="alert alert-success">{l s='Settings updated' mod='adpmicrodatos'}
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    </div>
    {/if}
    {if isset($error)}
        <div class="alert alert-danger">{l s='Oops, there was a problem and your setting weren\'t updated' mod='adpmicrodatos'}
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        </div>
    {/if}
    <h2 id="titulo_admin_adpmicrodatos"><img class="logo_adalop" src="{$module_dir|escape:'htmlall':'UTF-8'}logo.png">{$module_display}</h2>
    <div class="col-lg-3">
        <div class="list-group" id="adpmicrodatos-tabs">
            <a href="#tab_installation" class="list-group-item {if ($active_tab == '#tab_installation')} active {else} '' {/if}" data-toggle="tab"><i class="icon-AdminParentPreferences"></i> {l s='Install information' mod='adpmicrodatos'}</a>
            <a href="#tab_backups" class="list-group-item {if ($active_tab == '#tab_backups')} active {else} '' {/if}" data-toggle="tab"><i class="icon-hdd"></i> {l s='File backup history' mod='adpmicrodatos'}</a>
            <a href="#tab_configuration" class="list-group-item {if ($active_tab == '#tab_configuration')} active {else} '' {/if}" data-toggle="tab"><i class="icon-cog"></i> {l s='Configuration' mod='adpmicrodatos'}</a>
            <a href="#tab_customize" class="list-group-item {if ($active_tab == '#tab_customize')} active {else} '' {/if}" data-toggle="tab"><i class="icon-pencil"></i> {l s='Customice' mod='adpmicrodatos'}</a>
            <a href="#tab_modules_related" class="list-group-item {if ($active_tab == '#tab_modules_related')} active {else} '' {/if}" data-toggle="tab"><i class="icon-plug"></i> {l s='Modules and related services' mod='adpmicrodatos'}</a>
            <a href="#tab_thirdparty_richsnippets_modules" class="list-group-item {if ($active_tab == '#tab_thirdparty_richsnippets_modules')} active {else} '' {/if}" data-toggle="tab"><i class="icon-star"></i> {l s='Third Party Rich Snippets Compatible Modules' mod='adpmicrodatos'}</a>
            <a href="#tab_help" class="list-group-item {if ($active_tab == '#tab_help')} active {else} '' {/if}" data-toggle="tab"><i class="icon-question"></i> {l s='FAQ' mod='adpmicrodatos'}</a>
        </div>
        <div class="list-group">
            <a class="list-group-item" target="_blank" href="{$module_dir|escape:'htmlall':'UTF-8'}{$guide_link|escape:'htmlall':'UTF-8'}">
                <i class="icon-download"></i> {l s='DOCUMENTATION' mod='adpmicrodatos'}
            </a>
        </div>
        <div class="list-group">

            <a class="list-group-item" href="https://addons.prestashop.com/{$iso_code_language|escape:'htmlall':'UTF-8'}/ratings.php" target="_blank"><i class="icon-star"></i> {l s='RATE MODULE' mod='adpmicrodatos'}</a>

        </div>
        <div class="list-group">

            <a class="list-group-item" href="https://addons.prestashop.com/es/contacte-con-nosotros?id_product=42397" target="_blank"><i class="icon-envelope"></i> {l s='CONTACT US' mod='adpmicrodatos'}</a>

        </div>
        <div class="list-group">

            <a class="list-group-item" href="javascript:$('#clearcache').submit();"><i class="icon-trash-o"></i> {l s='CLEAR CACHE' mod='adpmicrodatos'}</a>

            <form method="post" action="" class="hidden" id="clearcache" name="clearcache">

                <input type="hidden" value="1" name="ClearCache">

                <input type="submit" name="submitClearCache" value="1">

            </form>

        </div>
        <div class="list-group">
            <span disabled="true" class="list-group-item"><i class="icon-info"></i> {l s='Version' mod='adpmicrodatos'} {$module_version|escape:'htmlall':'UTF-8'} {l s='developer by ' mod='adpmicrodatos'} Adalop</span> 
        </div>

    </div>
    <div class="tab-content col-lg-9">
        <!-- INSTALLATION TEMPLATE -->
        {include file=$adp_installation_path}
        <!-- CONFIGURATION TEMPLATE -->
        {include file=$adp_configuration_path}
        <!-- CUSTOMIZE TEMPLATE -->
        {include file=$adp_customize_path}
        <!-- LOGS/BACKUPS TEMPLATE -->
        {include file=$adp_backups_path}
        <!-- RELATED MODULES TEMPLATE -->
        {include file=$adp_modules_related_path}
        <!-- THIRD PARTY RICHSNIPPETS MODULES TEMPLATE -->
        {include file=$adp_thirdparty_richsnippets_modules_path}
        <!-- HELP TEMPLATE -->
        {include file=$adp_help_path}
    </div>
{literal}
<script type="text/javascript">
    admin_module_ajax_url = "{/literal}{$admin_controller_url|escape:'htmlall':'UTF-8'}{literal}"; /* url */
    admin_module_controller = "{/literal}{$controller_name|escape:'htmlall':'UTF-8'}{literal}";
</script>
{/literal}
</div>


