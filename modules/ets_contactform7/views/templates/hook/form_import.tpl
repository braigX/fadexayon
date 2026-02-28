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
{Module::getInstanceByName('ets_contactform7')->hookContactForm7LeftBlok() nofilter}
<div class="ctf7-right-block">
    <form id="module_form" class="defaultForm form-horizontal" novalidate="" enctype="multipart/form-data" method="post" action="">
        <div id="fieldset_0" class="panel">
            <div class="panel-heading"><i class="icon-exchange"></i>&nbsp;
            {l s='Import/export' mod='ets_contactform7'}</div>
            <div class="form-wrapper">
                <div class="form-group export_import">
                    <div class="ctf_export_form_content">            
                        <div class="ctf_export_option">
                            <div class="export_title">{l s='Export contact forms' mod='ets_contactform7'}</div>
                            <p>{l s='Export form configurations of all contact forms of the current shop that you are viewing' mod='ets_contactform7'}</p>
                            <a  href="{$link->getAdminlink('AdminModules',true)|escape:'html':'UTF-8'}&configure=ets_contactform7&tab_module=front_office_features&module_name=ets_contactform7&exportContactForm=1" class="btn btn-default mm_export_menu">
                                <i class="fa fa-download"></i>{l s='Export contact forms' mod='ets_contactform7'}
                            </a>
                        </div>                       
                        <div class="ctf_import_option">
                            <div class="export_title">{l s='Import contact forms' mod='ets_contactform7'}</div>
                            <p>{l s='Import contact forms to the current shop that you are viewing for quick configuration. This is useful when you want to migrate contact forms between websites' mod='ets_contactform7'}</p>    
                                <div class="ctf_import_option_updata">
                                    <label for="contactformdata">{l s='Data file' mod='ets_contactform7'}</label>
                                    <input type="file" name="contactformdata" id="contactformdata" />
                                </div>
                                <div class="cft_import_option_clean">
                                    <input type="checkbox" name="importdeletebefore" id="importdeletebefore" value="1" />
                                    <label class="cursor_pointer" for="importdeletebefore">{l s='Delete all contact forms before importing' mod='ets_contactform7'}</label>
                                </div>
                                <div class="cft_import_option_clean">
                                    <input type="checkbox" name="importoverride" id="importoverride" value="1" />
                                    <label class="cursor_pointer" for="importoverride">{l s='Override all forms with the same IDs' mod='ets_contactform7'}</label>
                                </div>
                                <div class="cft_import_option_button">
                                    <input type="hidden" value="1" name="importContactform" />
                                    <div class="cft_import_contact_submit cursor_pointer">
                                        <i class="fa fa-compress"></i>
                                        <input type="submit" class="btn btn-default cft_import_menu" name="cft_import_contact_submit" value="{l s='Import contact forms' mod='ets_contactform7'}" />
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>