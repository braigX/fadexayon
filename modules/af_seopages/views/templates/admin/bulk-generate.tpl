{*
*  @author    Amazzing <mail@mirindevo.com>
*  @copyright Amazzing
*  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}

<div class="tab-title">{l s='Bulk page actions' mod='af_seopages'}</div>
<div class="spg-step step-1">
	<div class="spg-subtitle">
		<span class="step-num">1</span>{l s='Select criteria' mod='af_seopages'}
		<button type="button" class="btn btn-primary toggleParamsForm"><i class="icon-plus"></i></button>
	</div>
	<form class="available-params clearfix">
		<div class="col-lg-3 cr-lvl cr-lvl-1">
			<select class="crLvl" name="type" data-lvl="1">
				<option value="">--</option>
				{foreach $sp.g.criteria as $key => $cr}
					<option value="{$key|escape:'html':'UTF-8'}">{$cr.name|escape:'html':'UTF-8'}</option>
				{/foreach}
			</select>
		</div>
		{foreach $sp.g.criteria as $key => $cr}
			{if !empty($cr.groups)}
				<div class="col-lg-3 cr-lvl cr-lvl-2 hidden cr-{$key|escape:'html':'UTF-8'}">
					<select class="crLvl" name="type"  data-lvl="2">
						<option value="">--</option>
						{foreach $cr.groups as $k => $v}
							<option value="{$k|escape:'html':'UTF-8'}">{$v.name|escape:'html':'UTF-8'}</option>
						{/foreach}
					</select>
				</div>
			{/if}
		{/foreach}
		<div class="cr-lvl cr-lvl-3 basic-item">{* filled dynamically *}</div>
		<div class="col-lg-1 show-on-ready">
			<a href="#" class="btn btn-primary addParams">{l s='Add' mod='af_seopages'}</a>
		</div>
	</form>
	<form class="bulk-generate-params bulk-selected-groups">{* filled dynamically *}</form>
	<div class="params-summary">{l s='Possible number of pages' mod='af_seopages'}: <span class="dynamic b">0</span></div>
</div>
<div class="spg-step step-2">
	<div class="spg-subtitle"><span class="step-num">2</span>{l s='Configure fields' mod='af_seopages'}</div>
	<div class="available-vars">
		{l s='You can use the following variables' mod='af_seopages'}: <span class="dynamic"></span>
	</div>
	<form class="bulk-generate-params sp-form">
		{foreach $sp.g.configurable_fields as $name => $field}
			{include file="../../../../amazzingfilter/views/templates/admin/form-group.tpl"
				name="fields[$name]"
				group_class="form-group $name clearfix"
				label_class="col-lg-2"
				input_wrapper_class="col-lg-10"
			}
			{if $name == 'header'}
				<a href="#" class="toggleOtherFields text-right">{l s='Other fields' mod='af_seopages'} <i class="icon-angle-down"></i></a>
				<div class="other-fields hidden">
			{/if}
		{/foreach}
		</div>{* /other fields *}
		<div class="spg-footer clearfix">
			<div class="col-lg-2"></div>
			<div class="col-lg-10 bulk-generate-actions">
				<select class="bulk-action-name">
					<option value="generate">{l s='Generate new pages' mod='af_seopages'}</option>
					<option value="update">{l s='Update fields of pages matching selected criteria' mod='af_seopages'}</option>
					<option value="delete">{l s='Delete pages matching selected criteria' mod='af_seopages'}</option>
				</select>
				<button type="button" class="btn btn-primary runAction"><span class="txt">{l s='Start' mod='af_seopages'}</span></button>
				<span class="process-info grey-note"></span>
				<div class="bulk-update-options hidden">
					{$options_tpl = "../../../../amazzingfilter/views/templates/admin/options.tpl"}
					<div class="basic-item field-names">
						{$field_names = array_column($sp.g.configurable_fields, 'display_name')}
						{$field_data = ['options' => array_combine(array_keys($sp.g.configurable_fields), $field_names), 'value' => []]}
						{capture name='field_options_label'}{l s='All fields' mod='af_seopages'}{/capture}
						{include file=$options_tpl name="upd_fields" data=$field_data label=$smarty.capture.field_options_label autohide=1 no_ids=1}
					</div>
					<div class="basic-item lang-ids">
						{$lang_data = ['options' => $available_languages, 'value' => []]}
						{capture name='lang_options_label'}{l s='All languages' mod='af_seopages'}{/capture}
						{include file=$options_tpl name="upd_langs" data=$lang_data label=$smarty.capture.lang_options_label autohide=1}
					</div>
				</div>
				<div class="skip-info alert-info hidden"></div>
			</div>
		</div>
	</form>
</div>
{* since 1.0.1 *}
