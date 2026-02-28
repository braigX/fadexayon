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
    <i class="icon-cog"></i>
    {l s='Configuration' mod='adpsearchlocatemicrodatos'}
    <small>{$module_display}</small>
</h3>
<form action="" class="form-horizontal " id="form_options_adpsl" name="form_options_adpsl" method="post">
    <div class="form-group">
        <label class="control-label col-sm-3">{l s='Include deactivated modules' mod='adpsearchlocatemicrodatos'}</label>
        <div class="col-sm-9">
            <span class="switch prestashop-switch fixed-width-lg">
                <input type="radio" id="includeDisabled_1"  name="includeDisabled" value="1" {if $active_modulo_disabled==1}checked="checked"{/if}>
                <label for="includeDisabled_1">{l s='Yes' mod='adpsearchlocatemicrodatos'}</label>
                <input type="radio" id="includeDisabled_0"  name="includeDisabled" value="0" {if $active_modulo_disabled==0}checked="checked"{/if}>
                <label for="includeDisabled_0">{l s='No' mod='adpsearchlocatemicrodatos'}</label>
                <a class="slide-button btn"></a>
            </span>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-sm-3">{l s='Include uninstalled modules' mod='adpsearchlocatemicrodatos'}</label>
        <div class="col-sm-9">
            <span class="switch prestashop-switch fixed-width-lg">
                <input type="radio" id="includeUninstalled_1"  name="includeUninstalled" value="1" {if $active_modulo_unistall==1}checked="checked"{/if}>
                <label for="includeUninstalled_1">{l s='Yes' mod='adpsearchlocatemicrodatos'}</label>
                <input type="radio" id="includeUninstalled_0"  name="includeUninstalled" value="0" {if $active_modulo_unistall==0}checked="checked"{/if}>
                <label for="includeUninstalled_0">{l s='No' mod='adpsearchlocatemicrodatos'}</label>
                <a class="slide-button btn"></a>
            </span>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-sm-3">{l s='Show search results without microdata' mod='adpsearchlocatemicrodatos'}</label>
        <div class="col-sm-9">
            <span class="switch prestashop-switch fixed-width-lg">
                <input type="radio" id="showCleanItems_1"  name="showCleanItems" value="true" {if $show_clean_items}checked="checked"{/if}>
                <label for="showCleanItems_1">{l s='Yes' mod='adpsearchlocatemicrodatos'}</label>
                <input type="radio" id="showCleanItems_0"  name="showCleanItems" value="false" {if !$show_clean_items}checked="checked"{/if}>
                <label for="showCleanItems_0">{l s='No' mod='adpsearchlocatemicrodatos'}</label>
                <a class="slide-button btn"></a>
            </span>
        </div>
    </div>
      <div class="form-group">
          <button type="submit" value="1" id="option_form_submit_btn_adpsl" name="option_form_submit_btn_adpsl" class="btn btn-default pull-right">
              <i class="process-icon-save"></i> {l s='Save' mod='adpsearchlocatemicrodatos'}
          </button>
      </div>
</form>


