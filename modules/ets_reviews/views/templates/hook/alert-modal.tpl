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

{assign var='icon' value=$icon|default:'fa-check-circle'}
{assign var='modal_message' value=$modal_message|default:''}

<script type="text/javascript">
    ets_rv_alertModal = $('#{$modal_id|escape:'html':'UTF-8'}');
    ets_rv_alertModal.on('hidden.bs.ets-rv-modal', function () {
        ets_rv_alertModal.ETSModal('hide');
    });
</script>

<div id="{$modal_id|escape:'html':'UTF-8'}" class="ets-rv-modal fade ets-rv-product-comment-modal" role="dialog"
     aria-hidden="true" data-keyboard="false" data-backdrop="static">
    <div class="ets-rv-modal-dialog" role="document">
        <div class="ets_table-cell">
            <div class="ets-rv-modal-content">
                <div class="ets-rv-modal-header">
                    <div class="h2">
                        <i class="ets_svg_fill_grayblack">
                            <svg width="20" height="20" style="vertical-align: -3px;"
                                 viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg">
                                <path d="M1412 734q0-28-18-46l-91-90q-19-19-45-19t-45 19l-408 407-226-226q-19-19-45-19t-45 19l-91 90q-18 18-18 46 0 27 18 45l362 362q19 19 45 19 27 0 46-19l543-543q18-18 18-45zm252 162q0 209-103 385.5t-279.5 279.5-385.5 103-385.5-103-279.5-279.5-103-385.5 103-385.5 279.5-279.5 385.5-103 385.5 103 279.5 279.5 103 385.5z"/>
                            </svg>
                        </i>
                        {$modal_title|escape:'html':'UTF-8'}
                    </div>
                </div>
                <div class="ets-rv-modal-body">
                    <div class="row">
                        <div class="col-md-12  col-sm-12" id="{$modal_id|escape:'html':'UTF-8'}-message">
                            {$modal_message nofilter}
                        </div>
                    </div>

                </div>
                <div class="ets-rv-modal-footer">
                    <div class="col-md-12  col-sm-12 post-comment-buttons button_center">
                        <button type="button" class="btn ets-rv-btn-comment ets-rv-btn-comment-huge ets_button_gray"
                                data-dismiss="ets-rv-modal" aria-label="{l s='OK' mod='ets_reviews'}">
                            {l s='OK' mod='ets_reviews'}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
