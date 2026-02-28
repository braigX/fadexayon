{*
* 2007-2023 ETS-Soft
*
* NOTICE OF LICENSE
*
* This file is not open source! Each license that you purchased is only available for 1 website only.
* If you want to use this file on more websites (or projects), you need to purchase additional licenses. 
* You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
* 
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs, please contact us for extra customization service at an affordable price
*
*  @author ETS-Soft <etssoft.jsc@gmail.com>
*  @copyright  2007-2023 ETS-Soft
*  @license    Valid for 1 website (or project) for each purchase of license
*  International Registered Trademark & Property of ETS-Soft
*}
<script type="text/javascript">
var use_cronjob={$ETS_ODE_USE_CRONJOB|intval};
var token_request= '{$token|escape:'html':'UTF-8'}';
var ets_ode_module_link = "{$module_link nofilter}";
var ets_odm_link_customer_search ="{$ets_odm_link_customer_search nofilter}";
var ets_ode_msg_confirm = "{l s='Do you want to delete this product?' mod='ets_ordermanager'}";
var Click_to_copy_text = "{l s='Click to copy' mod='ets_ordermanager' js=1}";
var Copied_text = "{l s='Copied' mod='ets_ordermanager' js=1}";
</script>
<div id="customerlist" style="display:none;">
    <ul>
        {if isset($customers) && $customers}
            {foreach from=$customers item='customer'}
                <li>
                    <span class="customer-name">{$customer.firstname|escape:'html':'UTF-8'} {$customer.lastname|escape:'html':'UTF-8'} ({$customer.email|escape:'html':'UTF-8'})</span>
                    <span class="delete-customer-id" data-id="{$customer.id_customer|intval}">{l s='Delete' mod='ets_ordermanager'}</span>
                </li>
            {/foreach}
        {/if}
    </ul>
</div>
{if $ets_ordermanager_error_message} 
    {$ets_ordermanager_error_message nofilter}
{/if}
{if isset($sendmail) && $sendmail}
    <div class="bootstrap">
		<div class="alert alert-success">
			<button data-dismiss="alert" class="close_popup" type="button">×</button>
			{l s='The email has been sent successfully to receivers' mod='ets_ordermanager'}
		</div>
	</div>
{/if}
<div class="bootstrap{if isset($is_ps16) && $is_ps16} is_ps16{else} is_ps17{/if}">
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="blog_center_content col-lg-12">
                    {$ets_ordermanager_body_html nofilter}
                    <div id="exported_list_fields">
                        <div class="ets-form-group ets-custom-payment-tab-exported_fields" >
                            <label>{l s='Selected fields' mod='ets_ordermanager'}</label>
                            <ul id="list_fields">
                                {if isset($list_fields) && $list_fields}
                                    {foreach from=$list_fields item='field'}
                                        <li id="fields-{$field.val|escape:'html':'UTF-8'}" class="exported_fields_{$field.class|escape:'html':'UTF-8'}">{$field.name|escape:'html':'UTF-8'}</li>
                                    {/foreach}
                                {/if}
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>