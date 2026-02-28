{*
 * Copyright ETS Software Technology Co., Ltd
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
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
*}

{extends file="helpers/form/form.tpl"}
{block name="legend"}
    {if isset($form_id) && $form_id == 'edit' && isset($product) && $product}
	    <div class="panel-heading">
		    <i class="icon-cogs"></i>&nbsp;{if !empty($question)}{l s='Edit question' mod='ets_reviews'}{else}{l s='Edit review' mod='ets_reviews'}{/if}&nbsp;#{$identifier|intval} - {l s='Product' mod='ets_reviews'}: <a href="{$product->link nofilter}">{$product->name|escape:'html':'UTF-8'}</a>
	    </div>
    {else}{$smarty.block.parent}{/if}
    {if isset($config_tabs) && $config_tabs|count > 0}
		<ul class="ets-pc-nav-tabs">
            {foreach from=$config_tabs key='tab' item='item'}
				<li class="ets-pc-nav-item {$tab|escape:'quotes':'UTF-8'}{if $current_tab_active|trim==$tab|trim} active{/if}" data-tab="{$tab|escape:'quotes':'UTF-8'}">
					<a href="#{$tab|escape:'quotes':'UTF-8'}">
						{$item|escape:'quotes':'UTF-8'}
					</a>
				</li>
            {/foreach}
		</ul>
	    <input id="current_tab_active" name="current_tab_active" value="{$current_tab_active|escape:'html':'UTF-8'}" type="hidden">
    {/if}
{/block}
{block name="input"}
    {if $input.type == 'products'}
		<table id="{$input.name|escape:'html':'UTF-8'}">
			<tr>
				<th class="text-center"></th>
				<th class="text-center">{l s='ID' mod='ets_reviews'}</th>
				<th class="text-left" width="80%">{l s='Product name' mod='ets_reviews'}</th>
			</tr>
            {foreach $input.values as $value}
				<tr>
					<td class="text-center">
						<input type="checkbox" name="{$input.name|escape:'html':'UTF-8'}[]"
						       value="{$value.id_product|intval}"{if isset($value.selected) && $value.selected == 1} checked {/if} />
					</td>
					<td class="text-center">{$value.id_product|intval}</td>
					<td width="80%">{$value.name|escape:'html':'UTF-8'}</td>
				</tr>
            {/foreach}
		</table>
    {elseif $input.type == 'switch' && $smarty.const._PS_VERSION_|@addcslashes:'\'' < '1.6'}
        {foreach $input.values as $value}
			<input type="radio" name="{$input.name|escape:'html':'UTF-8'}" id="{$value.id|intval}"
			       value="{$value.value|escape:'html':'UTF-8'}"
                   {if $fields_value[$input.name] == $value.value}checked="checked"{/if}
                    {if isset($input.disabled) && $input.disabled}disabled="disabled"{/if} />
			<label class="t" for="{$value.id|intval}">
                {if isset($input.is_bool) && $input.is_bool == true}
                    {if $value.value == 1}
						<img src="../img/admin/enabled.gif" alt="{$value.label|escape:'html':'UTF-8'}"
						     title="{$value.label|escape:'html':'UTF-8'}"/>
                    {else}
						<img src="../img/admin/disabled.gif" alt="{$value.label|escape:'html':'UTF-8'}"
						     title="{$value.label|escape:'html':'UTF-8'}"/>
                    {/if}
                {else}
                    {$value.label|escape:'html':'UTF-8'}
                {/if}
			</label>
            {if isset($input.br) && $input.br}<br/>{/if}
            {if isset($value.p) && $value.p}<p>{$value.p|escape:'html':'UTF-8'}</p>{/if}
        {/foreach}
    {elseif $input.type=='criterion'}
        {if isset($input.options) && $input.options|@count > 0}
			<div class="form-control-static">
				<ul id="criterions_list">
                    {foreach from=$input.options item='criterion'}
						<li class="criterion-item">
							<div class="criterion-rating">
								<label style="font-weight:normal;margin-bottom: 0;">{$criterion.name|escape:'html':'UTF-8'}
									:</label>
								<div
										class="ets-rv-grade-stars"
										data-grade="{if isset($fields_value[$input.name][$criterion.id_ets_rv_product_comment_criterion]) && $fields_value[$input.name][$criterion.id_ets_rv_product_comment_criterion]}{$fields_value[$input.name][$criterion.id_ets_rv_product_comment_criterion]|intval}{else}{$input.default|intval}{/if}"
										data-input="criterion[{$criterion.id_ets_rv_product_comment_criterion|intval}]">
								</div>
								<span class="ets-rv-criterion-clear btn btn-action" title="{l s='Clear' mod='ets_reviews'}">{l s='Clear' mod='ets_reviews'}</span>
							</div>
						</li>
                    {/foreach}
				</ul>
			</div>
        {/if}
	{elseif $input.name == 'customer_name'}
		{if isset($languages) && is_array($languages) && $languages|count > 1}
			<div class="form-group">
			<div class="col-lg-9">
		{/if}
		<input type="text" name="customer_name" id="customer_name" value="{if isset($fields_value[$input.name])}{$fields_value[$input.name]|escape:'html':'UTF-8'}{/if}" placeholder="{if isset($input.placeholder) && $input.placeholder}{$input.placeholder|escape:'html':'UTF-8'}{/if}" autocomplete="off">
		{if isset($languages) && is_array($languages) && $languages|count > 1}
			</div>
			</div>
		{/if}
	{elseif $input.name == 'email'}
		{if isset($languages) && is_array($languages) && $languages|count > 1}
			<div class="form-group">
			<div class="col-lg-9">
		{/if}
		<input type="text" name="email" id="email" value="{if isset($fields_value[$input.name])}{$fields_value[$input.name]|escape:'html':'UTF-8'}{/if}" placeholder="{if isset($input.placeholder) && $input.placeholder}{$input.placeholder|escape:'html':'UTF-8'}{/if}" autocomplete="off">
		{if isset($languages) && is_array($languages) && $languages|count > 1}
			</div>
			</div>
		{/if}
    {elseif $input.name == 'id_product'}
		{if isset($languages) && is_array($languages) && $languages|count > 1}
			<div class="form-group">
				<div class="col-lg-9">
		{/if}
		<div class="ets_rv_product_search_form">
				<div class="ets_rv_product_search"></div>
				<input type="text" name="search_product" class="search_product"
					   placeholder="{$input.placeholder|escape:'html':'UTF-8'}" autocomplete="off">
				<input type="hidden" name="id_product" class="id_product" value="{if isset($fields_value[$input.name])}{$fields_value[$input.name]|escape:'html':'UTF-8'}{/if}">
		</div>
		{if isset($languages) && is_array($languages) && $languages|count > 1}
				</div>
			</div>
		{/if}
    {elseif $input.name == 'id_customer'}
		{if isset($languages) && is_array($languages) && $languages|count > 1}
			<div class="form-group">
				<div class="col-lg-9">
		{/if}
					<div class="ets_rv_customer_search_form">
						<div class="ets_rv_customer_search"></div>
						<input type="text" name="search_customer" class="search_customer" placeholder="{$input.placeholder|escape:'html':'UTF-8'}" autocomplete="off">
						<input type="hidden" name="id_customer" value="{if isset($fields_value[$input.name])}{$fields_value[$input.name]|escape:'html':'UTF-8'}{/if}"/>
					</div>
		{if isset($languages) && is_array($languages) && $languages|count > 1}
				</div>
			</div>
		{/if}
    {elseif $input.type == 'group'}
        {assign var=groups value=$input.values}
        {include file='./form_group.tpl'}
    {elseif $input.type == 'checkboxes'}
        {if isset($input.values) && $input.values && is_array($input.values)}
            {foreach $input.values as $value}
                {assign var=id_checkbox value=$input.name|cat:'_'|cat:$value.id}
				<div class="checkbox{if isset($value.class) && $value.class != 'parent'} sub{/if}">
                    {strip}
						<label for="{$id_checkbox|escape:'html':'UTF-8'}">
							<input type="checkbox" name="{$input.name|cat:'[]'|escape:'html':'UTF-8'}" id="{$id_checkbox|escape:'html':'UTF-8'}" class="{if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if}"{if isset($value.id)} value="{$value.id|escape:'html':'UTF-8'}"{/if}{if isset($fields_value[$input.name]) && (in_array($value.id, $fields_value[$input.name]) || in_array('all', $fields_value[$input.name]))} checked="checked"{/if} />
                            {$value.name nofilter}
						</label>
                    {/strip}
				</div>
            {/foreach}
        {/if}
    {elseif $input.type == 'radios'}
        {if isset($input.options.query) && $input.options.query}
		    <ul style="padding: 0; margin-top: 5px;">
                {foreach $input.options.query as $option}
				    <li class="ets_rv_{$input.name|escape:'html':'UTF-8'}" style="list-style: none; padding-bottom: 5px">
					    <input {if $option.id_option == $fields_value[$input.name]} checked="checked" {elseif !$fields_value[$input.name] && $input.default == $option.id_option}checked="checked"{/if}
							    style="margin: 2px 7px 0 5px; float: left;"
							    type="radio"
							    id="{$input.name|cat:'_'|cat:$option.id_option|escape:'html':'UTF-8'}"
							    value="{$option.id_option|escape:'html':'UTF-8'}"
							    name="{$input.name|escape:'html':'UTF-8'}"/>
                        {if $option.id_option == 'off'}
						    <i class="icon-remove color_danger"></i>
                        {/if}
					    <label for="{$input.name|cat:'_'|cat:$option.id_option|escape:'html':'UTF-8'}">{$option.name|escape:'quotes':'UTF-8'}</label>
				    </li>
                {/foreach}
		    </ul>
        {/if}
    {elseif $input.name == 'ETS_RV_REDUCTION_AMOUNT'}
	    <div class="row">
            {if $input.name == 'ETS_RV_REDUCTION_AMOUNT'}<div class="col-lg-4">
			    <input type="text" id="{$input.name|escape:'html':'UTF-8'}" name="{$input.name|escape:'html':'UTF-8'}" value="{if isset($fields_value[$input.name])}{$fields_value[$input.name]|escape:'html':'UTF-8'}{/if}" onchange="this.value = this.value.replace(/,/g, '.');">
			    </div>{/if}
		    <div class="col-lg-4">
                {if !empty($input.currencies)}<select name="ETS_RV_ID_CURRENCY">
                    {foreach from=$input.currencies item='currency'}
					    <option value="{$currency.id_currency|intval}"{if isset($fields_value['ETS_RV_ID_CURRENCY']) && $fields_value['ETS_RV_ID_CURRENCY'] == $currency.id_currency} selected="selected"{/if}>{$currency.iso_code|escape:'html':'UTF-8'}</option>
                    {/foreach}
				    </select>{/if}
		    </div>
		    <div class="col-lg-4">
                {if !empty($input.tax)}<select name="ETS_RV_REDUCTION_TAX">
                    {foreach from=$input.tax item='option'}
					    <option value="{$option.id_option|intval}"{if isset($fields_value['ETS_RV_REDUCTION_TAX']) && $fields_value['ETS_RV_REDUCTION_TAX'] == $option.id_option} selected="selected"{/if}>{$option.name|escape:'html':'UTF-8'}</option>
                    {/foreach}
				    </select>{/if}
		    </div>
	    </div>
    {elseif $input.name == 'ETS_RV_MANAGITOR'}
	    <div class="input-group">
		    <span class="input-group-addon"><i class="icon-search-plus"></i></span>
		    <input type="text" name="ets_rv_managitor_email" id="ets_rv_managitor_email" value="" placeholder="{l s='Search by id or name' mod='ets_reviews'}">
	    </div>
	    <input type="hidden" name="{$input.name|escape:'html':'UTF-8'}" id="{$input.name|escape:'html':'UTF-8'}" value="{if !empty($fields_value[$input.name])}{$fields_value[$input.name]|escape:'html':'UTF-8'}{/if}">
	    {if isset($input.customer_search) && $input.customer_search}
			<ul class="ets_rv_customers">
				{if isset($fields_value[$input.name|cat:'_customers']) && $fields_value[$input.name|cat:'_customers']}
					{foreach from=$fields_value[$input.name|cat:'_customers'] item='customer'}
						<li class="ets_rv_customer" data-id="{$customer.id_customer|intval}">
							{$customer.id_customer|intval} - {$customer.firstname|cat:' '|cat: $customer.lastname|escape:'quotes':'UTF-8'}({$customer.email|escape:'quotes':'UTF-8'})<span class="remove_ctm"></span>
						</li>
					{/foreach}
				{/if}
			</ul>
		{/if}
	{elseif $input.name == 'ETS_RV_SECURE_TOKEN'}
		<div class="input-group">
			<input type="text" name="ets_rv_secure_token" id="ets_rv_secure_token" value="{if !empty($fields_value[$input.name])}{$fields_value[$input.name]|escape:'html':'UTF-8'}{/if}" placeholder="">
			<span class="input-group-addon"><i class="ets_icon_svg">
					<svg width="12" height="12" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M666 481q-60 92-137 273-22-45-37-72.5t-40.5-63.5-51-56.5-63-35-81.5-14.5h-224q-14 0-23-9t-9-23v-192q0-14 9-23t23-9h224q250 0 410 225zm1126 799q0 14-9 23l-320 320q-9 9-23 9-13 0-22.5-9.5t-9.5-22.5v-192q-32 0-85 .5t-81 1-73-1-71-5-64-10.5-63-18.5-58-28.5-59-40-55-53.5-56-69.5q59-93 136-273 22 45 37 72.5t40.5 63.5 51 56.5 63 35 81.5 14.5h256v-192q0-14 9-23t23-9q12 0 24 10l319 319q9 9 9 23zm0-896q0 14-9 23l-320 320q-9 9-23 9-13 0-22.5-9.5t-9.5-22.5v-192h-256q-48 0-87 15t-69 45-51 61.5-45 77.5q-32 62-78 171-29 66-49.5 111t-54 105-64 100-74 83-90 68.5-106.5 42-128 16.5h-224q-14 0-23-9t-9-23v-192q0-14 9-23t23-9h224q48 0 87-15t69-45 51-61.5 45-77.5q32-62 78-171 29-66 49.5-111t54-105 64-100 74-83 90-68.5 106.5-42 128-16.5h256v-192q0-14 9-23t23-9q12 0 24 10l319 319q9 9 9 23z"/></svg>
				</i> {l s='Generate' mod='ets_reviews'}</span>
		</div>
		<input type="hidden" name="{$input.name|escape:'html':'UTF-8'}" id="{$input.name|escape:'html':'UTF-8'}" value="{if !empty($fields_value[$input.name])}{$fields_value[$input.name]|escape:'html':'UTF-8'}{/if}">
	{elseif $input.name == 'ETS_RV_CRONJOB_LOG'}
		<textarea readonly id="{$input.name|escape:'html':'UTF-8'}" name="_{$input.name|escape:'html':'UTF-8'}" rows="20">{if isset($cronjobLog) && $cronjobLog}{$cronjobLog nofilter}{/if}</textarea>
		<button class="ets_rv_clear_log btn btn-default" name="ets_rv_clear_log" type="button">
			<i class="icon-trash"></i> {l s='Clear log' mod='ets_reviews'}
		</button
    {else}
        {$smarty.block.parent}
	    {if $input.name == 'ETS_RV_DISCOUNT_MESSAGE'}
		    <p class="help-block">
			    {l s='Available tags' mod='ets_reviews'}: <span class="ets_rv_short_code" title="{l s='Click to copy' mod='ets_reviews'}">[discount_value]</span>, <span class="ets_rv_short_code" title="{l s='Click to copy' mod='ets_reviews'}">[date_from]</span>, <span class="ets_rv_short_code" title="{l s='Click to copy' mod='ets_reviews'}">[date_to]</span>, <span class="ets_rv_short_code" title="{l s='Click to copy' mod='ets_reviews'}">[discount_code]</span>
		    </p>
			<p class="help-block">
				{l s='This message will be displayed in a popup after customer writes a review and that review is approved automatically. If the review needs to be validated by the Administrator, once the review is approved, an email will be sent to that customer instead of displaying this message on the popup' mod='ets_reviews'}
			</p>
		{elseif $input.name=='ETS_RV_CACHE_LIFETIME'}
			<p class="help-block">
				<a class="ets_rv_clear_cache" href="{$clear_cache_link nofilter}">{l s='Clear product review cache' mod='ets_reviews'}</a>
			</p>
        {elseif $input.name == 'ETS_RV_RECAPTCHA_SECRET_KEY_V2' || $input.name == 'ETS_RV_RECAPTCHA_SECRET_KEY_V3'}
            <p class="help-block">
                <a target="_blank" rel="noreferrer noopener"
				   href="https://www.google.com/recaptcha/admin/create">{l s='Get Site key and Secret key' mod='ets_reviews'}</a>
            </p>
		{elseif $input.name=='ETS_RV_AVERAGE_RATE_POSITION'}
			<p class="help-block ets_rv_hook_custom">
				{l s='Put' mod='ets_reviews'}&nbsp;<span class="ets_rv_shortcode">{literal}{hook h='displayCustomETSReviews'}{/literal}</span>&nbsp;{l s='to your template tpl file where you want the instagram images to be displayed' mod='ets_reviews'}
			</p>
	    {/if}
    {/if}
{/block}
{block name="input_row"}
	{if $input.name == 'ETS_RV_MANAGITOR'}
		<input type="hidden" name="{$input.name|escape:'html':'UTF-8'}_COMPARE" id="{$input.name|escape:'html':'UTF-8'}_COMPARE" value="{if !empty($fields_value[$input.name])}{$fields_value[$input.name]|escape:'html':'UTF-8'}{/if}">
	{/if}
	{if $input.name != 'ETS_RV_ID_CURRENCY' && $input.name != 'ETS_RV_REDUCTION_TAX'}
		{if $input.name == 'ETS_RV_MINIMUM_AMOUNT'}
			<div class="form-group discount discount_option auto form_group_minimum_amount">
		{/if}
        {if isset($config_tabs) && $config_tabs}
            {if !isset($ik)}{assign var="ik" value=0}{else}{assign var="ik" value=$ik+1}{/if}
			{if !isset($form_group_tab)}
				{assign var="form_group_tab" value=$input.tab}
				<div class="form-wrapper-group-item {$input.tab|escape:'quotes':'UTF-8'}" data-tab-id="{$input.tab|escape:'quotes':'UTF-8'}">
			{/if}
	        {if $form_group_tab == $input.tab}
                {if !empty($input.group_title)}<h4 class="ets-pc-title-parent">{$input.group_title|escape:'quotes':'UTF-8'}</h4>{/if}
				{*ETS_RV_CRONJOB_EMAILS*}
                {$smarty.block.parent}
				{*ETS_RV_SECURE_TOKEN*}
	        {elseif $form_group_tab != $input.tab}
				{assign var="form_group_tab" value=$input.tab}
				</div>
				<div class="form-wrapper-group-item {$input.tab|escape:'quotes':'UTF-8'}" data-tab-id="{$input.tab|escape:'quotes':'UTF-8'}">
		        {if $form_group_tab == 'design'}
			        <input type="hidden" id="ets_rv_change_color" name="change_color" value="0">
		        {/if}
                {if !empty($input.group_title)}<h4 class="ets-pc-title-parent">{$input.group_title|escape:'quotes':'UTF-8'}</h4>{/if}
                {$smarty.block.parent}
			{/if}
	        {if $ik == $field|count}
		        </div>
	        {/if}
        {elseif isset($controller) && $controller == 'ie'}
            {if $input.name=='ETS_RV_IE_DATA_IMPORT'}
	            {include file='./form-import-export.tpl'}
	            {$smarty.block.parent}
	        {elseif $input.name=='ETS_RV_IE_DELETE_ALL'}
	            {$smarty.block.parent}
	            {include file='./form-import-export.tpl'}
            {else}
                {$smarty.block.parent}
            {/if}
        {else}
            {if $table=='ets_rv_product_comment' && ($input.name=='date_add' && $input.name=='ids_language[]')}
                {if $input.name=='date_add'}
                    <div class="form-group row row-ets-custom-language">
                        <div class="col-lg-12">
						<div class="row form-group">
                        <div class="col-lg-6 ets-cm-custom-status">
                            {$smarty.block.parent}
                        </div>
                {/if}
                {if $input.name=='ids_language[]'}
					{if (!isset($question) || !$question) && $input.name == 'ids_language[]'}
						<div class="col-lg-6 ets-cm-custom-language">
							{$smarty.block.parent}
						</div>
						</div>
                        </div>
                    </div>
					{elseif (isset($question) || $question) && $input.name == 'ids_language[]'}
						{$smarty.block.parent}
					{/if}
                {/if}
            {else}
				{if $input.name == 'ETS_RV_CRONJOB_MAIL_LOG'}{*ETS_RV_CRONJOB_EMAILS*}
					<div class="form-group ets_rv_cronjob_emails_info">
						<p class="alert alert-info">{l s='Configure cronjob feature to automatically send notification emails and automatically delete expired discount codes and/or used discount codes.' mod='ets_reviews'}</p>
						<h4><span class="required">*</span> {l s='Some important notes:' mod='ets_reviews'}</h4>
						<ul>
							<li>{l s='The recommended frequency is ' mod='ets_reviews'}<b>{l s='once per minute' mod='ets_reviews'}</b></li>
							<li>{l s='How to set up a cronjob is different depending on your server. If you are using a Cpanel hosting, watch this video for reference: ' mod='ets_reviews'}
								<a target="_blank" href="https://www.youtube.com/watch?v=bmBjg1nD5yA" rel="noreferrer noopener">https://www.youtube.com/watch?v=bmBjg1nD5yA</a><br/>
								{l s='If your cpanel software is Plesk, see this:' mod='ets_reviews'} <a href="https://docs.plesk.com/en-US/obsidian/customer-guide/scheduling-tasks.65207/" target="_blank" rel="noreferrer noopener">https://docs.plesk.com/en-US/obsidian/customer-guide/scheduling-tasks.65207/</a><br/>
								{l s='If your server is Ubuntu, see this:' mod='ets_reviews'} <a href="https://www.digitalocean.com/community/tutorials/how-to-use-cron-to-automate-tasks-ubuntu-1804" target="_blank" rel="noreferrer noopener">https://www.digitalocean.com/community/tutorials/how-to-use-cron-to-automate-tasks-ubuntu-1804</a><br/>
								{l s='If your server is Centos, see this:' mod='ets_reviews'} <a href="https://www.digitalocean.com/community/tutorials/how-to-use-cron-to-automate-tasks-centos-8" target="_blank" rel="noreferrer noopener">https://www.digitalocean.com/community/tutorials/how-to-use-cron-to-automate-tasks-centos-8</a><br/>
								{l s='You can also contact your hosting provider to ask them for support on setting up the cronjob' mod='ets_reviews'}
							</li>
							<li>{l s='Configure SMTP for your website (instead of using default PHP mail() function) to send email better. If you can afford, buy professional marketing email hosting to send a large number of emails' mod='ets_reviews'}</li>
						</ul>
					</div>
				{/if}
                {$smarty.block.parent}
				{if $input.name=='ETS_RV_SECURE_TOKEN'}
					<div class="form-group ets_rv_help">
						<label class="control-label col-lg-3"></label>
						<div class="col-lg-9">
							<label class="control-label mt_0">
								<span class="required">*</span> {l s='Set up a cronjob as below on your server to send emails and delete discount codes automatically' mod='ets_reviews'}
							</label><br/><em><span id="ets_abd_cronjob_path">{$path nofilter}</span></em><br/>
							<label class="control-label"><span class="required">*</span> {l s='Execute the cronjob manually by clicking on the button below' mod='ets_reviews'}</label> <br/>
							<a id="ets_rv_cronjob_link" class="btn btn-default" href="{$url|escape:'quotes':'UTF-8'}" target="_blank">{l s='Execute cronjob manually' mod='ets_reviews'}</a>
						</div>
					</div>
				{/if}
            {/if}
        {/if}
		{if $input.name == 'ETS_RV_MINIMUM_AMOUNT_SHIPPING'}
			</div>
		{/if}
	{/if}
    {if (!isset($question) || !$question) && $input.name == 'ids_language[]' && isset($identifier)}
		{(Module::getInstanceByName('ets_reviews')->renderUploadImage(['product_comment_id' => $identifier])) nofilter}
    {/if}
{/block}

{block name="autoload_tinyMCE"}
	tinySetup({
		editor_selector :"autoload_rte",
		resize : 'both',
		height : 180
	});
{/block}
