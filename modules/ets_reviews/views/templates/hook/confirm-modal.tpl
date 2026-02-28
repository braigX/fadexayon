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
  ets_rv_confirmModal = $('#{$modal_id|escape:'html':'UTF-8'}');
  ets_rv_confirmModal.on('hidden.bs.ets-rv-modal', function () {
    ets_rv_confirmModal.ETSModal('hide');
    ets_rv_confirmModal.trigger('ets-rv-modal:confirm', false);
  });

  $('.confirm-button', ets_rv_confirmModal).click(function() {
    ets_rv_confirmModal.trigger('ets-rv-modal:confirm', true);
  });
  $('.refuse-button', ets_rv_confirmModal).click(function() {
    ets_rv_confirmModal.trigger('ets-rv-modal:confirm', false);
  });
</script>

<div id="{$modal_id|escape:'html':'UTF-8'}" class="ets-rv-modal fade ets-rv-product-comment-modal" role="dialog" aria-hidden="true"  data-keyboard="false" data-backdrop="static">
  <div class="ets-rv-modal-dialog" role="document">
  <div class="ets_table-cell">
    <div class="ets-rv-modal-content">
      <div class="ets-rv-modal-header">
        <h2>
          <i class="fa {$icon|escape:'html':'UTF-8'}"></i>
          {$modal_title|escape:'html':'UTF-8'}
        </h2>
      </div>
      <div class="ets-rv-modal-body">
        <div class="row">
          <div class="col-md-12  col-sm-12" id="{$modal_id|escape:'html':'UTF-8'}-message">
            {$modal_message nofilter}
          </div>
        </div>
        <div class="row">
          <div class="col-md-12  col-sm-12 post-comment-buttons">
            <button type="button" class="btn btn-primary ets-rv-btn-comment-inverse ets-rv-btn-comment-huge refuse-button ets_button_gray" data-dismiss="ets-rv-modal" aria-label="{l s='No' mod='ets_reviews'}">
              {l s='No' mod='ets_reviews'}
            </button>
            <button type="button" class="btn btn-primary ets-rv-btn-comment ets-rv-btn-comment-huge confirm-button{if !empty($ETS_RV_DESIGN_COLOR2)} background2{/if}{if !empty($ETS_RV_DESIGN_COLOR3)} bg_hover3 bd_hover3{/if}" data-dismiss="ets-rv-modal" aria-label="{l s='Yes' mod='ets_reviews'}">
              {l s='Yes' mod='ets_reviews'}
            </button>
          </div>
        </div>
      </div>
    </div>
    </div>
  </div>
</div>
