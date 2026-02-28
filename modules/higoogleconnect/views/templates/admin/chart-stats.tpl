{**
 * 2012 - 2024 HiPresta
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0).
 * It is also available through the world-wide-web at this URL: https://opensource.org/licenses/AFL-3.0
 *
 * @author    HiPresta <support@hipresta.com>
 * @copyright HiPresta 2024
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 *
 * @website   https://hipresta.com
 *}
<div class="panel hi-google-users-chart">
    <div class="panel-heading">
        <i class="icon-pie-chart"></i>
        {l s='Stats' mod='higoogleconnect'}

        <span class="panel-heading-action">
            <a class="list-toolbar-btn" id="desc-googleConnectChart-help" href="#">
                <span title="{l s='Help' mod='higoogleconnect'}" data-toggle="tooltip" class="label-tooltip" data-html="true" data-placement="top">
                    <i class="process-icon-help"></i>
                </span>
            </a>
        </span>
    </div>
    <div class="form-wrapper">
        <div class="form-group">
            <div class="btn-group" role="group">
                <div class="radio btn btn-default">
                    <label><input type="radio" name="pieChartDate" id="pieChartDay" value="day" {if $type == 'day'}checked{/if}>{l s='Day' mod='higoogleconnect'}</label>
                </div>
                <div class="radio btn btn-default">
                    <label><input type="radio" name="pieChartDate" id="pieChartMonth" value="month" {if $type == 'month'}checked{/if}>{l s='Month' mod='higoogleconnect'}</label>
                </div>
                <div class="radio btn btn-default">
                    <label><input type="radio" name="pieChartDate" id="pieChartYear" value="year" {if $type == 'year'}checked{/if}>{l s='Year' mod='higoogleconnect'}</label>
                </div>
                <div class="radio btn btn-default">
                    <label><input type="radio" name="pieChartDate" id="pieChartAll" value="all" {if $type == 'all'}checked{/if}>{l s='All' mod='higoogleconnect'}</label>
                </div>
                <div class="radio btn btn-default">
                    <label><input type="radio" name="pieChartDate" id="pieChartCustom" value="custom" {if $type == 'custom'}checked{/if}>{l s='Custom' mod='higoogleconnect'}</label>
                </div>
            </div>
        </div>

        <div class="form-group chart-custom-dates {if $type != 'custom'}hi-hide{/if}">
            <div class="row">
                <div class="col-xs-6">
                    <div class="input-group">
                        <label class="input-group-addon">{l s='From' mod='higoogleconnect'}:</label>
                        <input type="text" name="chartDatepickerFrom" id="chartDatepickerFrom" value="{$from|escape:'htmlall':'UTF-8'}" class="datepicker form-control" placeholder="{l s='From' mod='higoogleconnect'}">
                    </div>
                </div>
                <div class="col-xs-6">
                    <div class="input-group">
                        <label class="input-group-addon">{l s='To' mod='higoogleconnect'}:</label>
                        <input type="text" name="chartDatepickerTo" id="chartDatepickerTo" value="{$to|escape:'htmlall':'UTF-8'}" class="datepicker form-control" placeholder="{l s='To' mod='higoogleconnect'}">
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group" style="width:350px; height: 350px;">
            <canvas
                id="hi-google-connect-users-chart"
                data-total-registrations="{$registrationsData.totalCustomers|intval}"
                data-other-registrations="{$registrationsData.totalOtherRegistrations|intval}"
                data-google-registrations="{$registrationsData.totalGoogleUsers|intval}"
            >
            </canvas> 
        </div>
    </div>
</div>