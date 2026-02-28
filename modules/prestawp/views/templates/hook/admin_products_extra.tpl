{**
* NOTICE OF LICENSE
*
* This file is licenced under the Software License Agreement.
* With the purchase or the installation of the software in your application
* you accept the licence agreement.
*
* @author    Presta.Site
* @copyright 2017 Presta.Site
* @license   LICENSE.txt
*}
<div id="module_prestawp" class="{if $psv == 1.6}panel product-tab{/if} pswp{$psv*10|intval}">
    <input type="hidden" name="submitted_tabs[]" value="{$module_name|escape:'html':'UTF-8'}" />
    <input type="hidden" name="{$module_name|escape:'html':'UTF-8'}-submit" value="1" />

    {if $psv == 1.6}
        <h3>{l s='PrestaShop-WordPress' mod='prestawp'}</h3>
    {/if}
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            {include file=$post_select_form_tpl input=$wp_input}
        </div>
    </div>

    {if $psv == 1.6}
        <div class="panel-footer">
            <a href="{$link->getAdminLink('AdminProducts')|escape:'html':'UTF-8'}" class="btn btn-default"><i class="process-icon-cancel"></i> {l s='Cancel' mod='prestawp'}</a>
            <button type="submit" name="submitAddproduct" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='Save' mod='prestawp'}</button>
            <button type="submit" name="submitAddproductAndStay" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='Save and stay' mod='prestawp'}</button>
        </div>
    {/if}
</div>
