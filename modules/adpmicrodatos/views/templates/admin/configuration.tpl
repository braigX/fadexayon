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
<div class="tab-pane {if ($active_tab == '#tab_configuration')} active {else} '' {/if}" id="tab_configuration">
    <div class="panel">
        <h3>
            <i class="icon-cog"></i>
            {l s='Configuration' mod='adpmicrodatos'}
        </h3>
        <form action="" class="form-horizontal "id="form_options_adp" name="form_options_adp" method="post">
            <fieldset>
                {* Tabs *}
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#general" aria-controls="general" role="tab" data-toggle="tab">{l s='General' mod='adpmicrodatos'}</a></li>
                    <li role="presentation"><a href="#customize_breadcrumb" aria-controls="customize_breadcrumb" role="tab" data-toggle="tab">{l s='Breadcrumb' mod='adpmicrodatos'}</a></li>
                    <li role="presentation"><a href="#product" aria-controls="product" role="tab" data-toggle="tab">{l s='Product page' mod='adpmicrodatos'}</a></li>
                    <li role="presentation"><a href="#richsnippets" aria-controls="richsnippets" role="tab" data-toggle="tab">{l s='Reviews' mod='adpmicrodatos'}</a></li>
                    <li role="presentation"><a href="#advanced" aria-controls="advanced" role="tab" data-toggle="tab">{l s='Advanced' mod='adpmicrodatos'}</a></li>
                </ul>

                {* Tab panes *}
                <div class="tab-content panel">

                    {* General *}
                    {include file="./configuration_general.tpl"}

                    {* Customize Breadcrumb Microdata *}
                    {include file="./customize_breadcrumb.tpl"}

                    {* Product *}
                    {include file="./configuration_product.tpl"}

                    {* Rich Snippets *}
                    {include file="./configuration_richsnippets.tpl"}

                    {* Advanced *}
                    {include file="./configuration_advanced.tpl"}
                    
                </div>
                <button type="submit" value="1" id="option_form_submit_btn" name="option_form_submit_btn" class="btn btn-default pull-right">
                    <i class="process-icon-save"></i>
                    {l s='Save' mod='adpmicrodatos'}
                </button>
            </fieldset>
        </form>
    </div>
</div>
