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

{* Rich Snippets *}
<div role="tabpanel" class="tab-pane" id="richsnippets">
    <div class="form-group">
        <label class="control-label col-lg-5">
            <span class="label-tooltip" data-toggle="tooltip" title="{l s='Do you want activate o deactivate the microdata in Rich Snippets?' mod='adpmicrodatos'}" data-placement="bottom">
                {l s='Active Microdata Reviews' mod='adpmicrodatos'}
            </span>
        </label>
        <div class="col-lg-7">
            <span class="switch prestashop-switch fixed-width-lg">
                <input type="radio" name="active_microdata_rich_snippets" id="active_microdata_rich_snippets_on" value="1" {if $active_microdata_rich_snippets==1}checked="checked"{/if}>
                <label for="active_microdata_rich_snippets_on">{l s='Yes' mod='adpmicrodatos'}</label>
                <input type="radio" name="active_microdata_rich_snippets" id="active_microdata_rich_snippets_off" value="0" {if $active_microdata_rich_snippets==0}checked="checked"{/if}>
                <label for="active_microdata_rich_snippets_off">{l s='No' mod='adpmicrodatos'}</label>
                <a class="slide-button btn"></a>
            </span>
            <div class="help-block">
                {l s='You need to have installed some of the following modules' mod='adpmicrodatos'}:
                <a href="javascript:void(0)" data-role="thirparty-richsnippets-modules-tooltip-link" class="label-tooltip" data-html="true" data-toggle="tooltip" title="{$microdata_richsnippets_implemented_modules|escape:'htmlall':'UTF-8'}">
                    <i class='icon-info-sign'></i>
                </a>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-5">
            <span class="label-tooltip" data-toggle="tooltip" data-placement="bottom" title="{l s='Add here your Trusted Shops id (TS-ID) for the selected language' mod='adpmicrodatos'}">{l s='Trusted Shops ID' mod='adpmicrodatos'}</span>
        </label>

        <div class="col-lg-7">
            <div class="col-md-12">
                <div class="input-group">
                    {foreach from=$languages item=language}
                        <div class="translatable-field lang-{$language.id_lang}" {if $language.id_lang != $default_language}style="display:none"{/if}>
                            <div class="col-lg-10">
                                <input type="text"
                                    id="view_microdata_code_ts_{$language.id_lang}"
                                    name="view_microdata_code_ts_{$language.id_lang}"
                                    value="{$adp_rich_snippets_ts_cod[$language.id_lang]|escape:'htmlall':'UTF-8'}"
                                    class="text form-control">
                            </div>
                            <div class="col-lg-2">
                                <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                                    {$language.iso_code}
                                    <i class="icon-caret-down"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    {foreach from=$languages item=language}
                                        <li><a href="javascript:hideOtherLanguage({$language.id_lang});" tabindex="-1">{$language.name}</a></li>
                                    {/foreach}
                                </ul>
                            </div>
                        </div>
                    {/foreach}
                </div>
                <div class="help-block">{l s='Add here your Trusted Shops id (TS-ID) for the selected language' mod='adpmicrodatos'}</div> 
            </div>
        </div>
    </div>
</div>