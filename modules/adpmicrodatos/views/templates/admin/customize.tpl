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
<div class="tab-pane {if ($active_tab == '#tab_customize')} active {else} '' {/if}" id="tab_customize">
    <div class="panel">
        <h3>
            <i class="icon-pencil"></i>
            {l s='Customize' mod='adpmicrodatos'}
        </h3>

        <form action="" class="form-horizontal "id="form_customize_adp" name="form_customize_adp" method="post">
            <fieldset>
                {* Tabs *}
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#customize_type_microdata" aria-controls="customize_type_microdata" role="tab" data-toggle="tab">{l s='Types of microdata' mod='adpmicrodatos'}</a></li>
                    <li role="presentation"><a href="#customize_product" aria-controls="customize_product" role="tab" data-toggle="tab">{l s='Product page' mod='adpmicrodatos'}</a></li>
                    <li role="presentation"><a href="#customize_refunds_policy" aria-controls="customize_refunds_policy" role="tab" data-toggle="tab">{l s='Refund policy' mod='adpmicrodatos'}</a></li>
                    <li role="presentation"><a href="#customize_shipping_details" aria-controls="customize_shipping_details" role="tab" data-toggle="tab">{l s='Shipping details' mod='adpmicrodatos'}</a></li>
                </ul>

                {* Tab panes *}
                <div class="tab-content panel">

                    {* Customize Type Microdata *}
                    {include file="./customize_type_microdata.tpl"}

                    {* Customize Product Microdata *}
                    {include file="./customize_product.tpl"}

                    {* Customize Product Microdata *}
                    {include file="./customize_refund_policy.tpl"}

                    {* Customize Shipping details *}
                    {include file="./customize_shipping_details.tpl"}
                    
                </div>
                <button type="submit" value="1" id="customize_form_submit_btn" name="customize_form_submit_btn" class="btn btn-default pull-right">
                    <i class="process-icon-save"></i>
                    {l s='Save' mod='adpmicrodatos'}
                </button>
                
                <button type="submit" value="1" id="customize_reset_form_submit_btn" name="customize_reset_form_submit_btn" class="btn btn-default pull-right">
                    <i class="process-icon-update"></i>
                    {l s='Reset' mod='adpmicrodatos'}
                </button>
            </fieldset>
        </form>
    </div>
</div>
