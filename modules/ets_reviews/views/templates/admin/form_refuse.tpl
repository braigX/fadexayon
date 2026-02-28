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

<form class="ets_rv_refuse_form defaultForm form-horizontal" method="post" enctype="multipart/form-data" action="{$action nofilter}">
    <div class="panel">
        <div class="panel-heading">
            <i class="icon-envelope"></i>&nbsp;{l s='Decline' mod='ets_reviews'}
        </div>
        <div class="form-wrapper">
            <input type="hidden" name="submitRefuse" value="1">
            <div class="form-group">
                <label for="message" class="control-label col-lg-4">
                    {l s='Message' mod='ets_reviews'}
                </label>
                <div class="col-lg-8">
                    <textarea name="message" id="message" class="textarea-autosize" style="overflow: hidden; overflow-wrap: break-word; resize: none; height: 80px;"></textarea>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <button type="submit" value="1" id="ets_rv_refuse_form_submit" name="submitRefuse" class="btn btn-default pull-right">
                <i class="process-icon-save"></i> {l s='Decline' mod='ets_reviews'}
            </button>
        </div>
    </div>
</form>