{*
*  @author    Amazzing <mail@mirindevo.com>
*  @copyright Amazzing
*  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}

<form action="" class="sp-form">
	{foreach $fields as $name => $field}
		{*include file=$af_tpl.form_group label_class='full-width' input_wrapper_class='full-width-input'*}
		{include file=$af_tpl.form_group group_class='form-group clearfix sp-'|cat:$name label_class='col-lg-2' input_wrapper_class='col-lg-10'}
	{/foreach}
	<div class="form-group clearfix">
		<div class="col-lg-2"></div>
		<div class="col-lg-10">
			<div class="sp-duplicates multilang-wrapper">{* updated dynamically *}</div>
		</div>
	</div>
	{include file=$af_tpl.form_footer cls='spSave'}
</form>
{* since 0.2.3 *}
