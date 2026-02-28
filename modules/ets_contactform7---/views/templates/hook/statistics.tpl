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
<script type="text/javascript" src="{$js_dir_path|escape:'quotes':'UTF-8'}d3.v3.min.js"></script>
<script type="text/javascript" src="{$js_dir_path|escape:'quotes':'UTF-8'}nv.d3.min.js"></script>
<script type="text/javascript" src="{$js_dir_path|escape:'quotes':'UTF-8'}statistics.js"></script>
<script type="text/javascript">
    var text_add_to_black_list = '{l s='Add IP address to blacklist successful' js='1' mod='ets_contactform7' }';
    var detele_log = '{l s='If you clear "View log", view chart will be reset. Do you want to do that?' js='1' mod='ets_contactform7' }';
</script>
{Module::getInstanceByName('ets_contactform7')->hookContactForm7LeftBlok() nofilter}
<div class="ctf7-right-block">
    <div class="panel statics_form">
        <div class="panel-heading">
            <i class="icon icon-line-chart fa fa-line-chart"></i> {l s='Statistics' mod='ets_contactform7'}
        </div>
        <div class="form-wrapper">
            <div class="ets_form_tab_header">
                <span {if $tab_ets=='chart'}class="active"{/if} data-tab="chart">{l s='Chart' mod='ets_contactform7'}</span>
                <span {if $tab_ets=='view-log'}class="active"{/if}  data-tab="view-log">{l s='Views log' mod='ets_contactform7'}</span>
            </div>
            <div class="form-group-wapper">
                <div class="ctf_admin_statistic form-group form_group_contact chart">
                    <div class="ctf_admin_chart">
                        <div class="line_chart">
                            <svg style="width:100%; height: 500px;"></svg>
                        </div>
                    </div>
                    <div class="ctf_admin_filter">
                        <form id="ctf_admin_filter_chart" class="defaultForm form-horizontal"
                              action="{$action|escape:'html':'UTF-8'}" enctype="multipart/form-data" method="POST">
                            <div class="ctf_admin_filter_chart_settings">
                                <div class="ctf_admin_filter_cotactform">
                                    <label>{l s='Contact form' mod='ets_contactform7'}</label>
                                    <select id="id_contact" name="id_contact" class="form-control">
                                        <option value=""{if !$ctf_contact} selected="selected"{/if}>{l s='All contact form' mod='ets_contactform7'}</option>
                                        {foreach from=$contacts item=contact}
                                            <option value="{$contact.id_contact|intval}" {if $ctf_contact == $contact.id_contact} selected="selected"{/if}>{$contact.title|escape:'html':'UTF-8'}</option>
                                        {/foreach}
                                    </select>
                                </div>
                                <div class="ctf_admin_filter_date">
                                    <label>{l s='Month' mod='ets_contactform7'}</label>
                                    <select id="months" name="months" class="form-control">
                                        <option value="" {if !$ctf_month} selected="selected"{/if}>{l s='All' mod='ets_contactform7'}</option>
                                        {foreach from=$months key=k item=month}
                                            <option value="{$k|intval}"{if $ctf_month == $k} selected="selected"{/if}>{l s=$month mod='ets_contactform7'}</option>
                                        {/foreach}
                                    </select>
                                </div>
                                <div class="ctf_admin_filter_date">
                                    <label>{l s='Year' mod='ets_contactform7'}</label>
                                    <select id="years" name="years" class="form-control">
                                        <option value="" {if !$ctf_year} selected="selected"{/if}>{l s='All' mod='ets_contactform7'}</option>
                                        {foreach from=$years item=year}
                                            <option value="{$year|intval}" {if $ctf_year == $year} selected="selected"{/if}>{$year|intval}</option>
                                        {/foreach}
                                    </select>
                                </div>
                                <div class="ctf_admin_filter_button">
                                    <button name="submitFilterChart" class="btn btn-default"
                                            type="submit">{l s='Filter' mod='ets_contactform7'}</button>
                                    {if $show_reset}
                                        <a href="{$action|escape:'html':'UTF-8'}"
                                           class="btn btn-default">{l s='Reset' mod='ets_contactform7'}</a>
                                    {/if}
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="ctf_admin_log form-group form_group_contact view-log">
                    {if $logs}
                        <table id="table-log" class="table log">
                            <thead>
                            <tr class="nodrag nodrop">
                                <th>{l s='IP address' mod='ets_contactform7'}</th>
                                <th>{l s='Browser' mod='ets_contactform7'}</th>
                                <th>{l s='Customer' mod='ets_contactform7'}</th>
                                <th>{l s='Contact form' mod='ets_contactform7'}</th>
                                <th>{l s='Date' mod='ets_contactform7'}</th>
                                <th>{l s='Action' mod='ets_contactform7'}</th>
                            </tr>
                            </thead>
                            <tbody id="list-logs">
                            {foreach from=$logs item='log'}
                                <tr>
                                    <td>{$log.ip|escape:'html':'UTF-8'}</td>
                                    <td>
                                        <span class="browser-icon {$log.class|escape:'html':'UTF-8'}"></span> {$log.browser|escape:'html':'UTF-8'}
                                    </td>
                                    <td>{if $log.id_customer}<a
                                            href="{$link->getAdminLink('AdminCustomers')|escape:'html':'UTF-8'}"
                                            >{$log.firstname|escape:'html':'UTF-8'}
                                            &nbsp;{$log.lastname|escape:'html':'UTF-8'}</a>{else}--{/if}</td>
                                    <td>
                                        {if $log.enable_form_page}
                                            <a href="{Ets_contactform7::getLinkContactForm($log.id_contact|intval)|escape:'html':'UTF-8'}"
                                               class="dropdown-item product-edit" target="_blank"
                                               >
                                        {/if}
                                            {$log.title|escape:'html':'UTF-8'}
                                        {if $log.enable_form_page}
                                            </a>
                                        {/if}
                                    </td>
                                    <td>{dateFormat date=$log.datetime_added full=1}</td>
                                    <td class="statitics_form_action">
                                        <a class="btn btn-default view_location"
                                           title="{l s='View location' mod='ets_contactform7'}"
                                           href="https://www.infobyip.com/ip-{$log.ip|escape:'html':'UTF-8'}.html"
                                           target="_blank">{l s='View location' mod='ets_contactform7'}</a>
                                        {if !$log.black_list}
                                            <a class="btn btn-default addtoblacklist "
                                               data-ip="{$log.ip|escape:'html':'UTF-8'}"
                                               href="{$action|escape:'html':'UTF-8'}&addtoblacklist={$log.ip|escape:'html':'UTF-8'}">{l s='Add to blacklist' mod='ets_contactform7'}</a>
                                        {else}
                                            <span title="{l s='IP added to blacklist' mod='ets_contactform7'}"><i
                                                        class="icon icon-user-times"></i></span>
                                        {/if}
                                    </td>
                                </tr>
                            {/foreach}
                            </tbody>
                        </table>
                        <form action="{$action|escape:'html':'UTF-8'}" enctype="multipart/form-data" method="POST">
                            <input type="hidden" value="1" name="clearLogSubmit"/>
                            <div class="ets_pagination">
                                {$pagination_text nofilter}
                            </div>
                            <button class="clear-log btn btn-default" type="submit"
                                    name="clearLogSubmit">{l s='Clear all views log' mod='ets_contactform7'}</button>
                        </form>
                    {else}
                        {l s='No views log' mod='ets_contactform7'}
                    {/if}
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var ets_ctf_x_days = '{l s='Day' mod='ets_contactform7'}';
    var ets_ctf_x_months = '{l s='Month' mod='ets_contactform7'}';
    var ets_ctf_x_years = '{l s='Year' mod='ets_contactform7'}';
    var ets_ctf_y_label = '{l s='Count' mod='ets_contactform7'}';
    var ets_ctf_line_chart = {$lineChart|json_encode}
</script>