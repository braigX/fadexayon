{*
* 2007-2018 PrestaShop
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
* @author    SeoSA    <885588@bk.ru>
* @copyright 2012-2022 SeoSA
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
* International Registered Trademark & Property of PrestaShop SA
*}

<div class="default-settings clearfix">
    <div class="row">
        <label class="col-xs-12 col-sm-6 control-label text-left">
            <h2 class="mr-1 mb-0 mt-0">{l s='Default settings' mod='sitemappro'}:</h2>
        </label>
        <div class="col-xs-12 col-sm-6">
            <label class="control-label margin-right">{l s='Export changefreq' mod='sitemappro'}?</label>
            <span class="switch prestashop-switch fixed-width-lg">
                {foreach [1,0] as $value}
                    <input type="radio" name="default_settings[{$type_object}][is_changefreq]" value="{$value|escape:'quotes':'UTF-8'}"
                            {if $value == 1} id="disable_on-{$type_object}" {else} id="disable_off-{$type_object}" {/if}
                            {if $value == 1}
                                {if $default_settings["{$type_object}"]['is_changefreq']}checked="checked"{/if}
                            {else}
                                {if !$default_settings["{$type_object}"]['is_changefreq']}checked="checked"{/if}
                            {/if}
                    />
                    <label {if $value == 1} for="disable_on-{$type_object}" {else} for="disable_off-{$type_object}" {/if}>
                        {if $value == 1}{l s='Yes' mod='sitemappro'}{else}{l s='No' mod='sitemappro'}{/if}
                    </label>
                {/foreach}
                <a class="slide-button btn"></a>
            </span>
        </div>
    </div>
</div>
