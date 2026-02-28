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

{* Customize refunds policy *}
<div role="tabpanel" class="tab-pane" id="customize_refunds_policy">
    <div class="form-group">
        <div class="alert alert-warning" role="alert">
            {l s='Customise structured data for product return policies. For more information' mod='adpmicrodatos'} <a href="https://schema.org/MerchantReturnPolicy" target="_blank">https://schema.org/MerchantReturnPolicy</a>
        </div>
    </div>

    {foreach from=$adp_return_policy_information item=value_return_policy key=key_pais}
        <div class="container">
            <a href="#panel_option_{$key_pais}" class="btn btn-info panel_pais" data-toggle="collapse"><i class="material-icons mi-language">language</i> <span class="label_panel_pais">{l s='Country' mod='adpmicrodatos'} {$value_return_policy['name_country']} ({$key_pais}) {if $pais_defecto == $key_pais} - {l s='DEFAULT' mod='adpmicrodatos'} {/if}</span></a>
            <div id="panel_option_{$key_pais}" class="collapse {if $pais_defecto == $key_pais}in{/if}">
                <div class="form-group">
                    <label class="control-label col-lg-5">
                        <span class="label-tooltip" data-toggle="tooltip" title="{l s='Do you want to activate or deactivate the structured data of product return policies?' mod='adpmicrodatos'}" data-placement="bottom">
                            {l s='Active Refund policy' mod='adpmicrodatos'}
                        </span>
                    </label>
                    <div class="col-lg-7">
                        <span class="switch prestashop-switch fixed-width-lg">
                            <input type="radio" name="active_microdata_refund_policy_{$key_pais}" id="active_microdata_refund_policy_on_{$key_pais}" value="1" {if $value_return_policy['active']==1}checked="checked"{/if}>
                            <label for="active_microdata_refund_policy_on_{$key_pais}">{l s='Yes' mod='adpmicrodatos'}</label>
                            <input type="radio" name="active_microdata_refund_policy_{$key_pais}" id="active_microdata_refund_policy_off_{$key_pais}" value="0" {if $value_return_policy['active']==0}checked="checked"{/if}>
                            <label for="active_microdata_refund_policy_off_{$key_pais}">{l s='No' mod='adpmicrodatos'}</label>
                            <a class="slide-button btn"></a>
                        </span>
                        <div class="help-block">{l s='Only works on product page' mod='adpmicrodatos'}</div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-lg-5">
                        <span class="label-tooltip" data-toggle="tooltip" title="{l s='Specifies an applicable return policy' mod='adpmicrodatos'}" data-placement="bottom">
                            {l s='Field returnPolicyCategory' mod='adpmicrodatos'}
                        </span>
                    </label>
                    <div class="col-lg-7">
                        <select name="adp_return_policy_categories_{$key_pais}">
                            {foreach from=$refund_policies_types['returnpolicycategory']['types'] item='adp_return_policy_categories'}
                                <option value="{$adp_return_policy_categories|escape:'htmlall':'UTF-8'}" {if $adp_return_policy_categories == $value_return_policy['return_policy_categories']}selected{/if}>{$adp_return_policy_categories|escape:'htmlall':'UTF-8'}</option>
                            {/foreach}
                        </select>
                        <div class="help-block">{l s='For more information' mod='adpmicrodatos'} <a href="https://schema.org/returnPolicyCategory" target="_blank">https://schema.org/returnPolicyCategory</a></div> 
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-lg-5">
                        <span class="label-tooltip" data-toggle="tooltip" title="{l s='Specifies either a fixed return date or the number of days (from the delivery date) that a product can be returned.' mod='adpmicrodatos'}" data-placement="bottom">
                            {l s='Field merchantReturnDays ' mod='adpmicrodatos'}
                        </span>
                    </label>
                    <div class="col-lg-7">
                        <div class="col-md-12">
                            <div class="input-group">
                                <input name="adp_merchant_return_days_{$key_pais}" type="text" id="adp_merchant_return_days_{$key_pais}" value="{$value_return_policy['merchant_return_days']|escape:'htmlall':'UTF-8'}" class="text form-control">
                            </div>
                            <div class="help-block">{l s='For more information' mod='adpmicrodatos'} <a href="https://schema.org/merchantReturnDays" target="_blank">https://schema.org/merchantReturnDays</a></div> 
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-lg-5">
                        <span class="label-tooltip" data-toggle="tooltip" title="{l s='The type of return method offered' mod='adpmicrodatos'}" data-placement="bottom">
                            {l s='Field returnMethod ' mod='adpmicrodatos'}
                        </span>
                    </label>
                    <div class="col-lg-7">
                        <select name="adp_return_method_{$key_pais}">
                            {foreach from=$refund_policies_types['returnMethod']['types'] item='adp_return_method'}
                                <option value="{$adp_return_method|escape:'htmlall':'UTF-8'}" {if $adp_return_method == $value_return_policy['return_method']}selected{/if}>{$adp_return_method|escape:'htmlall':'UTF-8'}</option>
                            {/foreach}
                        </select>
                        <div class="help-block">{l s='For more information' mod='adpmicrodatos'} <a href="https://schema.org/returnMethod" target="_blank">https://schema.org/returnMethod</a></div> 
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-lg-5">
                        <span class="label-tooltip" data-toggle="tooltip" title="{l s='The type of return method offered' mod='adpmicrodatos'}" data-placement="bottom">
                            {l s='Field returnFees ' mod='adpmicrodatos'}
                        </span>
                    </label>
                    <div class="col-lg-7">
                        <select name="adp_return_fees_{$key_pais}">
                            {foreach from=$refund_policies_types['returnFees']['types'] item='adp_return_fees'}
                                <option value="{$adp_return_fees|escape:'htmlall':'UTF-8'}" {if $adp_return_fees == $value_return_policy['return_fees']}selected{/if}>{$adp_return_fees|escape:'htmlall':'UTF-8'}</option>
                            {/foreach}
                        </select>
                        <div class="help-block">{l s='For more information' mod='adpmicrodatos'} <a href="https://schema.org/returnFees" target="_blank">https://schema.org/returnFees</a></div> 
                    </div>
                </div>
            </div>
        </div>
    {/foreach}
</div>