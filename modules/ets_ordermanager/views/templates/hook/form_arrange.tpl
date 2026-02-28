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
<form id="form_arrange" class="defaultForm form-horizontal" action="" method="post" enctype="multipart/form-data" novalidate="">
    <div class="panel" id="fieldset_0">											
        <div class="panel-heading"><i class="icon-cog"></i> {l s='Customize order list' mod='ets_ordermanager'}</div>
        <div class="form-wrapper">
            <div class="form-group">
                <div class="col-lg-9">
                    <div class="form-group">
                        {foreach from=$title_fields key='key' item='field'}
                            {if isset($field.beggin)}
                                <div class="list-group">
                                    <div class="group-title">{$field.group|escape:'html':'UTF-8'} </div>
                                    <span class="open_close_list list_open">{l s='Open/Close' mod='ets_ordermanager'}</span>
                                    <div class="list-group-content row" style="display: block;">
                                        {if isset($field.all) && $field.all}
                                            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-6">
                                                <label class="label_all_arrange_list_order">
                                                    <input type="checkbox" class="all_arrange_list_order"/>
                                                    <i class="md-checkbox-control"></i>
                                                    {l s='All' mod='ets_ordermanager'}
                                                </label>
                                            </div>
                                        {/if}
                            {/if}
                            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-6">
                                <label for="list_{$key|escape:'html':'UTF-8'}" >
                                    <input class="arrange_list_order" type="checkbox" value="{$key|escape:'html':'UTF-8'}" id="list_{$key|escape:'html':'UTF-8'}" name="listOrders[]"{if in_array($key,$list_fields)} checked="checked"{/if} data-title="{$field.title|escape:'html':'UTF-8'}"/>
                                    <i class="md-checkbox-control"></i>
                                    {$field.title|escape:'html':'UTF-8'}
                                </label>
                            </div>
                            {if isset($field.end)}
                                </div>
                            </div>
                            {/if}
                        {/foreach}
                    </div> 
                </div>
                <div class="col-lg-3">
                    <button class="clear_all_fields" type="button">{l s='Clear all' mod='ets_ordermanager'}</button>
                    <ul id="list-order-fields">
                        {if $list_fields}
                            {foreach from=$list_fields item='field'}
                                {if isset($title_fields.$field)}
                                    <li class="field_{$field|escape:'html':'UTF-8'}">
                                        <label> 
                                        <input type="hidden" name="listFieldOrders[]" value="{$field|escape:'html':'UTF-8'}"/>
                                            {assign var='title_field' value= $title_fields.$field}
                                            {$title_field.title|escape:'html':'UTF-8'}
                                        </label>
                                        <span class="close_field" data-field="{$field|escape:'html':'UTF-8'}">{l s='Close' mod='ets_ordermanager'}</span>
                                    </li>
                                {/if}
                            {/foreach}
                        {/if}
                    </ul>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <button type="submit" class="btn btn-default pull-left" name="btnSubmitRessetToDefaultList">
                <i class="process-icon-repeat fa fa-repeat"></i>{l s='Reset to default' mod='ets_ordermanager'}
            </button>
			<button type="submit" value="1" id="module_form_submit_btn" name="btnSubmitArrangeListOrder" class="btn btn-default pull-right">
				<i class="process-icon-save"></i> {l s='Save' mod='ets_ordermanager'}
			</button>
        </div>
    </div>
</form>
<script type="text/javascript">
    $(document).ready(function(){
        var $myFields = $("#list-order-fields");
        $myFields.sortable({
            opacity: 0.6,
            cursor: "move",
            update: function () {
            },
            stop: function (event, ui) {
            }
        });
        $myFields.hover(
            function () {
                $(this).css("cursor", "move");
            },
            function () {
                $(this).css("cursor", "auto");
            }
        ); 
        {literal}
        $('.all_arrange_list_order').each(function(){
            var $list_group = $(this).closest('.list-group');
            if($list_group.find('input.arrange_list_order:checked').length == $list_group.find('input.arrange_list_order').length)
            {
                $(this).attr('checked','checked');
            }
        });
        {/literal}
    });
</script>