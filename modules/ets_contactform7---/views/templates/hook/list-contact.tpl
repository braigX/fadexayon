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
{if $ETS_CTF7_ENABLE_TMCE}
    <script type="text/javascript">
    var ad='';
    var iso='en';
    var file_not_found='';
    var pathCSS='{$smarty.const._THEME_CSS_DIR_ nofilter}';
    </script>
    <script src="{$_PS_JS_DIR_|escape:'html':'UTF-8'}tiny_mce/tiny_mce.js"></script>
    {if $is_ps15}
        <script src="{$_PS_JS_DIR_|escape:'html':'UTF-8'}tinymce.inc.js"></script>
    {else}
        <script src="{$_PS_JS_DIR_|escape:'html':'UTF-8'}admin/tinymce.inc.js"></script>
    {/if}
{/if}
{if isset($okimport)&& $okimport}
    <div class="bootstrap">
		<div class="alert alert-success">
			<button data-dismiss="alert" class="close" type="button">×</button>
			{l s='Contact form imported successfully.' mod='ets_contactform7'}
		</div>
	</div>
{/if}
{Module::getInstanceByName('ets_contactform7')->hookContactForm7LeftBlok() nofilter}
<script type="text/javascript">
var text_update_position='{l s='Successful update' mod='ets_contactform7'}';
{if isset($is_ps15) && $is_ps15}
    $(document).on('click','.dropdown-toggle',function(){
       $(this).closest('.btn-group').toggleClass('open'); 
    }); 
{/if}
</script>
<div class="ctf7-right-block">
    <form id="form-contact" class="form-horizontal clearfix products-catalog" action="{$link->getAdminLink('AdminContactFormContactForm',true)|escape:'html':'UTF-8'}" method="post">
        <input id="submitFilterContact" type="hidden" value="0" name="submitFilterContact" />
        <input type="hidden" value="1" name="page" />
        <input type="hidden" value="50" name="selected_pagination" />
        <div class="panel col-lg-12">
            <div class="panel-heading">
                 {l s='Contact forms' mod='ets_contactform7'}
                 {if count($contacts)}<span class="badge">{count($contacts)|intval}</span>{/if}
                 <span class="panel-heading-action">
                    <a id="desc-contactform-new" class="list-toolbar-btn" href="{$url_module|escape:'html':'UTF-8'}&addContact=1">
                        <span title="{l s='Add new' mod='ets_contactform7'}" class="label-tooltip" data-placement="top" data-html="true" data-original-title="{l s='Add new' mod='ets_contactform7'}" data-toggle="tooltip" title="">
                            <i class="process-icon-new"></i>
                        </span>
                    </a>
                 </span>
            </div>
            <div class="table-responsive-row clearfix">
                <table id="table-contact" class="table contact">
                    <thead>
                        <tr class="nodrag nodrop">
                            <th class="fixed-width-xs text-center ctf_id">
                                <span class="title_box">
                                    {l s='ID' mod='ets_contactform7'}
                                    <a {if $sort=='id_contact' && $sort_type=='desc'} class="active"{/if} href="{$link->getAdminLink('AdminContactFormContactForm',true)|escape:'html':'UTF-8'}&sort=id_contact&sort_type=desc{$filter_params nofilter}"><i class="icon-caret-down"></i></a>
                                    <a {if $sort=='id_contact' && $sort_type=='asc'} class="active"{/if} href="{$link->getAdminLink('AdminContactFormContactForm',true)|escape:'html':'UTF-8'}&sort=id_contact&sort_type=asc{$filter_params nofilter}"><i class="icon-caret-up"></i></a>
                                </span>
                            </th>
                            <th class="ctf_title">
                                <span class="title_box">
                                    {l s='Title' mod='ets_contactform7'}
                                    <a {if $sort=='title' && $sort_type=='desc'} class="active"{/if} href="{$link->getAdminLink('AdminContactFormContactForm',true)|escape:'html':'UTF-8'}&sort=title&sort_type=desc{$filter_params nofilter}"><i class="icon-caret-down"></i></a>
                                    <a {if $sort=='title' && $sort_type=='asc'} class="active"{/if} href="{$link->getAdminLink('AdminContactFormContactForm',true)|escape:'html':'UTF-8'}&sort=title&sort_type=asc{$filter_params nofilter}"><i class="icon-caret-up"></i></a>
                                </span>
                            </th>
                            {if !isset($show_shorcode_hook) || (isset($show_shorcode_hook)  && $show_shorcode_hook)}
                                <th class="ctf_shortcode">
                                    <span class="title_box">
                                        {l s='Short code' mod='ets_contactform7'}
                                    </span>
                                </th>
                            {/if}
                            <th class="ct_form_url">
                                <span class="title_box">
                                    {l s='Form URL' mod='ets_contactform7'}
                                </span>
                            </th>
                            <th class="ct_form_views">
                                <span class="title_box">
                                    {l s='Views' mod='ets_contactform7'}
                                </span>
                            </th>
                            <th class="ctf_sort">
                                <span class="title_box">
                                    {l s='Sort order' mod='ets_contactform7'}
                                    <a {if $sort=='position' && $sort_type=='desc'} class="active"{/if} href="{$link->getAdminLink('AdminContactFormContactForm',true)|escape:'html':'UTF-8'}&sort=position&sort_type=desc{$filter_params nofilter}"><i class="icon-caret-down"></i></a>
                                    <a {if $sort=='position' && $sort_type=='asc'} class="active"{/if} href="{$link->getAdminLink('AdminContactFormContactForm',true)|escape:'html':'UTF-8'}&sort=position&sort_type=asc{$filter_params nofilter}"><i class="icon-caret-up"></i></a>
                                </span>
                            </th>
                            <th class="ctf_message">
                                <span class="title_box">
                                    {l s='Save message' mod='ets_contactform7'}
                                    <a {if $sort=='save_message' && $sort_type=='asc'} class="active"{/if} href="{$link->getAdminLink('AdminContactFormContactForm',true)|escape:'html':'UTF-8'}&sort=save_message&sort_type=desc{$filter_params nofilter}"><i class="icon-caret-down"></i></a>
                                    <a {if $sort=='save_message' && $sort_type=='desc'} class="active"{/if} href="{$link->getAdminLink('AdminContactFormContactForm',true)|escape:'html':'UTF-8'}&sort=save_message&sort_type=asc{$filter_params nofilter}"><i class="icon-caret-up"></i></a>
                                </span>
                            </th>
                            <th class="ctf_active">
                                <span class="title_box">
                                    {l s='Active' mod='ets_contactform7'}
                                    <a {if $sort=='active' && $sort_type=='desc'} class="active"{/if} href="{$link->getAdminLink('AdminContactFormContactForm',true)|escape:'html':'UTF-8'}&sort=active&sort_type=desc{$filter_params nofilter}"><i class="icon-caret-down"></i></a>
                                    <a {if $sort=='active' && $sort_type=='asc'} class="active"{/if} href="{$link->getAdminLink('AdminContactFormContactForm',true)|escape:'html':'UTF-8'}&sort=active&sort_type=asc{$filter_params nofilter}"><i class="icon-caret-up"></i></a>
                                </span>
                            </th>
                            <th class="ctf_action">
                                <span class="title_box">
                                    {l s='Action' mod='ets_contactform7'}
                                </span>
                            </th>
                        </tr>
                        <tr class="nodrag nodrop">
                            <th class="fixed-width-xs text-center ctf_id">
                                <span class="title_box">
                                    <input class="form-control" name="id_contact" style="width:25px" value="{if isset($values_submit.id_contact)}{$values_submit.id_contact|escape:'html':'UTF-8'}{/if}" />
                                </span>
                            </th>
                            <th class="ctf_title">
                                <span class="title_box">
                                    <input class="form-control" name="contact_title" style="width:150px" value="{if isset($values_submit.contact_title)}{$values_submit.contact_title|escape:'html':'UTF-8'}{/if}"/>
                                </span>
                            </th>
                            {if !isset($show_shorcode_hook) || (isset($show_shorcode_hook)  && $show_shorcode_hook)}
                                <th class="">

                                </th>
                            {/if}
                            <th class="ct_form_url">
                            </th>
                            <th class="">
                            
                            </th>
                            <th>
                            </th>
                            <th class="ctf_message">
                                <span class="title_box">
                                    <select class="form-control" name="save_message">
                                        <option value="">---</option>
                                        <option value="1" {if isset($values_submit.save_message) && $values_submit.save_message==1} selected="selected"{/if}>{l s='Yes' mod='ets_contactform7'}</option>
                                        <option value="0" {if isset($values_submit.save_message) && $values_submit.save_message==0} selected="selected"{/if}>{l s='No' mod='ets_contactform7'}</option>
                                    </select>
                                </span>
                            </th>
                            <th class="ctf_active">
                                <select class="form-control" name="active_contact">
                                    <option value="">---</option>
                                    <option value="1" {if isset($values_submit.active_contact) && $values_submit.active_contact==1} selected="selected"{/if}>{l s='Yes' mod='ets_contactform7'}</option>
                                    <option value="0" {if isset($values_submit.active_contact) && $values_submit.active_contact==0} selected="selected"{/if}>{l s='No' mod='ets_contactform7'}</option>
                                </select>
                            </th>
                            <th class="ctf_action">
                                <span class="pull-right">
                                    <button id="submitFilterButtonContact" class="btn btn-default" name="submitFilterButtonContact" type="submit">
                                    <i class="icon-search"></i>
                                        {l s='Search' mod='ets_contactform7'}
                                    </button>
                                    {if isset($filter)&& $filter}
                                        <a class="btn btn-warning" href="{$link->getAdminLink('AdminContactFormContactForm',true)|escape:'html':'UTF-8'}">
                                            <i class="icon-eraser"></i>
                                            {l s='Reset' mod='ets_contactform7'}
                                        </a>
                                    {/if}
                                </span>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="list-contactform">
                        {if $contacts}
                            {foreach from=$contacts item='contact'}
                                <tr id="formcontact_{$contact.id_contact|intval}">
                                    <td class="ctf_id">{$contact.id_contact|intval}</td>
                                    <td class="ctf_title">{$contact.title|escape:'html':'UTF-8'}</td>
                                    {if !isset($show_shorcode_hook) || (isset($show_shorcode_hook)  && $show_shorcode_hook)}
                                        <td class="ctf_shortcode">
                                            <div class="short-code">
                                                <input title="{l s='Click to copy' mod='ets_contactform7'}" class="ctf-short-code" type="text" value='[contact-form-7 id="{$contact.id_contact|intval}"]' />
                                                <span class="text-copy">{l s='Copied' mod='ets_contactform7'}</span>
                                            </div>
                                        </td>
                                    {/if}
                                    <td class="ct_form_url">
                                        {if $contact.enable_form_page}
                                            <a href="{$contact.link|escape:'html':'UTF-8'}" target="_blank">{$contact.link|escape:'html':'UTF-8'}</a>
                                        {else}
                                            {l s='Form page is disabled' mod='ets_contactform7'}
                                        {/if}
                                    </td>
                                    <td class="ct_view text-center">
                                        {$contact.count_views|intval}
                                    </td>
                                    <td class="ctf_sort text-center {if $sort=='position' && $sort_type=='asc'}pointer dragHandle center{/if}">
                                        <div class="dragGroup">
                                            <span class="positions">{($contact.position+1)|intval}</span>
                                        </div>
                                    </td>
                                    <td class="text-center ctf_message">
                                        {if $contact.save_message}
                                            <a title="{l s='Click to disable' mod='ets_contactform7'}" href="{$url_module|escape:'html':'UTF-8'}&save_message_update=0&id_contact={$contact.id_contact|intval}">
                                                <i class="material-icons action-enabled">check</i>
                                            </a>
                                            {if $contact.count_message}
                                            <a title="{l s='View messages' mod='ets_contactform7'}" href="{$link->getAdminLink('AdminContactFormMessage')|escape:'html':'UTF-8'}&id_contact={$contact.id_contact|intval}" class="">
                                                ({$contact.count_message|intval})
                                            </a>
                                            {/if}
                                        {else}
                                            <a title="{l s='Click to enable' mod='ets_contactform7'}" href="{$url_module|escape:'html':'UTF-8'}&save_message_update=1&id_contact={$contact.id_contact|intval}">
                                                <i class="material-icons action-disabled">clear</i>
                                            </a>
                                            {if $contact.count_message}
                                                <a href="{$link->getAdminLink('AdminContactFormMessage')|escape:'html':'UTF-8'}&id_contact={$contact.id_contact|intval}" class="">
                                                    ({$contact.count_message|intval})
                                                </a>
                                            {/if}
                                        {/if}
                                    </td>
                                     <td class="text-center ctf_active">
                                        {if $contact.active}
                                            <a  title="{l s='Click to disable' mod='ets_contactform7'}" href="{$url_module|escape:'html':'UTF-8'}&active_update=0&id_contact={$contact.id_contact|intval}">
                                                <i class="material-icons action-enabled">check</i>
                                            </a>
                                        {else}
                                            <a title="{l s='Click to enable' mod='ets_contactform7'}" href="{$url_module|escape:'html':'UTF-8'}&active_update=1&id_contact={$contact.id_contact|intval}">
                                                <i class="material-icons action-disabled">clear</i>
                                            </a>
                                        {/if}
                                    </td>
                                    <td class="text-center ctf_action">
                                        <div class="btn-group-action">
                                            <div class="btn-group">
                                                <a class="btn tooltip-link product-edit" title="" href="{$url_module|escape:'html':'UTF-8'}&editContact=1&id_contact={$contact.id_contact|intval}" title="{l s='Edit' mod='ets_contactform7'}">
                                                    <i class="material-icons">mode_edit</i> {l s='Edit' mod='ets_contactform7'}
                                                </a>
                                                <a class="btn btn-link dropdown-toggle dropdown-toggle-split product-edit" aria-expanded="false" aria-haspopup="true" data-toggle="dropdown"> <i class="icon-caret-down"></i></a>
                                                <div class="dropdown-menu dropdown-menu-right" style="position: absolute; transform: translate3d(-164px, 35px, 0px); top: 0px; left: 0px; will-change: transform;">
                                                   {if $contact.enable_form_page}
                                                        <a  href="{Ets_contactform7::getLinkContactForm($contact.id_contact|intval)|escape:'html':'UTF-8'}" class="dropdown-item product-edit" target="_blank" title="{l s='Open form' mod='ets_contactform7'}">
                                                         <i class="material-icons">remove_red_eye</i>
                                                            {l s='View form' mod='ets_contactform7'}      
                                                        </a>
                                                    {/if}
                                                    <a  href="{$link->getAdminLink('AdminContactFormMessage')|escape:'html':'UTF-8'}&id_contact={$contact.id_contact|intval}" class="dropdown-item product-edit" title="{l s='View messages' mod='ets_contactform7'}">
                                                         <i class="icon icon-comments fa fa-comments"></i>
                                                         {l s='Messages' mod='ets_contactform7'}      
                                                    </a>
                                                    <a  href="{$link->getAdminLink('AdminContactFormStatistics')|escape:'html':'UTF-8'}&id_contact={$contact.id_contact|intval}" class="dropdown-item product-edit" title="{l s='Statistics' mod='ets_contactform7'}">
                                                         <i class="icon icon-line-chart"></i>
                                                            {l s='Statistics' mod='ets_contactform7'}      
                                                    </a>  
                                                    <a href="{$url_module|escape:'html':'UTF-8'}&duplicatecontact=1&id_contact={$contact.id_contact|intval}" class="dropdown-item message-duplidate product-edit" title="{l s='Duplicate' mod='ets_contactform7'}">
                                                    <i class="icon icon-copy"></i>
                                                        {l s='Duplicate' mod='ets_contactform7'}       
                                                    </a>    
                                                    <a href="{$url_module|escape:'html':'UTF-8'}&deletecontact=1&id_contact={$contact.id_contact|intval}" class="dropdown-item message-delete product-edit" title="{l s='Delete' mod='ets_contactform7'}">
                                                        <i class="icon icon-trash fa fa-trash"></i>
                                                        {l s='Delete form' mod='ets_contactform7'}       
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            {/foreach}
                        {else}
                            <tr>
                                <td colspan="10">
                                    <p class="alert alert-warning">{l s='No contact forms available' mod='ets_contactform7'}</p>
                                </td>
                            </tr>
                        {/if}
                    </tbody>
                </table>
                {$pagination_text nofilter}
            </div>
        </div>
    </form>
    <script type="text/javascript">
        $(document).ready(function(){
            if ($("table .datepicker").length > 0) {
				$("table .datepicker").datepicker({
					prevText: '',
					nextText: '',
					dateFormat: 'yy-mm-dd'
				});
			}
        });
    </script>
</div>
<div class="ctf-popup-wapper-admin">
     <div class="fuc"></div>
        <div class="ctf-popup-tablecell">
            <div class="ctf-popup-content">
                <div class="ctf_close_popup">{l s='close' mod='ets_contactform7'}</div>
                <div id="form-contact-preview">
                
                </div>
            </div>
        </div>
</div>