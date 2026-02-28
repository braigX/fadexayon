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

<h4 class="ets_abancart_title">
    <i class="icon-pencil-square"></i>
    {l s='Write note' mod='ets_abandonedcart'}
</h4>
<div class="content_reminder panel">
    <form id="form_write_note_manual" class="defaultForm form-horizontal" action="" enctype="multipart/form-data" method="post" novalidate>
        <div class="form-wrapper">
            <div class="form-group">
                <label class="control-label col-lg-3">{l s='Cart ID' mod='ets_abandonedcart'}</label>
                <div class="col-lg-8">
                    <input class="form-control" name="cart_id" value="{$cart_id|intval}" readonly/>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-lg-3">{l s='Date' mod='ets_abandonedcart'}</label>
                <div class="col-lg-8">
                    <input class="form-control" disabled name="date_add" value="{$cart_date_add|escape:'html':'UTF-8'}"/>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-lg-3">{l s='Total cart value' mod='ets_abandonedcart'}</label>
                <div class="col-lg-8">
                    <input class="form-control" disabled name="cart_total" value="{$cart_total|escape:'html':'UTF-8'}"/>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-lg-3 required">{l s='Note' mod='ets_abandonedcart'}</label>
                <div class="col-lg-8">
                    <textarea class="form-control" name="note" class="autoresize">{$cart_note nofilter}</textarea>
                </div>
            </div>
        </div>
        <div class="footer">
            <button type="button" value="1" name="writeNote" class="btn btn-default pull-right">
                <i class="icon-save"></i> {l s='Save' mod='ets_abandonedcart'}
            </button>
            <button type="button" value="1" name="cancelFormWriteNote" class="btn btn-default pull-left">
                <i class="icon-remove"></i> {l s='Cancel' mod='ets_abandonedcart'}
            </button>
        </div>
    </form>
</div>