{*
* 2007-2022 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innova Deluxe SL
* @copyright 2022 Innova Deluxe SL

* @license   INNOVADELUXE
*}
<section class="page-product-box row" id="idxr_customisation_section">
    <div class="rte" id="component_steps">
        <div id="component_steps_container" data-type='accordion'>            
           {* <h3>{l s='Configurator' mod='idxrcustomproduct'}</h3>*}
            {foreach from=$steps.components item=step name=foo}
                <div {include file="../../partials/component_data.tpl" step=$step} >
                    {include file="./"|cat:$step.type|cat:".tpl" step=$step last=$smarty.foreach.foo.last}
                </div>
            {/foreach}

            <div id="component_step_last" class="product-summary-unique-12345">
                <div class="product-header-unique-12345">
                    <div class="product-info-unique-12345">
                        <div id="product-title-unique-12345">...</div>
                        <p id="product-size-unique-12345">.. x .. cm</p>
                        <div id="toggleTableLinkDiv" class="price-structure-link-unique-12345" style="cursor: pointer;">
                            <i class="braig_i i-left">
                                <img src="/modules/idxrcustomproduct/img/icon/i_left.png" alt=">">    
                            </i>
                            <span id="front_tr_show_struct">{l s='Afficher la structure de prix' mod='idxrcustomproduct'}</span>
                            <i class="braig_i i-right">
                                <img src="/modules/idxrcustomproduct/img/icon/i_right.png" alt=">">
                            </i>
                        </div>
                    </div>
                    <div class="product-pricing-unique-12345">
                        <div class="price-section-unique-12345">
                            <span class="price-amount-unique-12345"  id="braig_ttc_price_set">00.00 €</span>
                            <span class="price-label-unique-12345">TTC</span>
                        </div>
                        <div class="price-section-unique-12345">
                            <span class="price-amount-unique-12345" id="braig_ht_price_set">00.00 €</span>
                            <span class="price-label-unique-12345">HT</span>
                        </div>
                    </div>
                </div>
                <div class="collapsible-section-unique-12345" id="collapsibleSection">
                    <table class="table table-unique-12345">
                        <tr {if $icp_price == 0}class="hidden"{/if} id="base_price_show_braigue">
                            <td>{l s='Base product' mod='idxrcustomproduct'}</td>
                            <td>{$product_name|escape:'htmlall':'UTF-8'}</td>
                            <td>
                                {include file="../../partials/resume_base_price.tpl"}
                            </td>
                        </tr>
                        {foreach from=$steps.components item=step name=foo}
                            {include file="../../partials/resume_line.tpl" step=$step}
                        {/foreach}
                        {include file="../../partials/poids_line.tpl" step=$step}
                        {include file="../discount.tpl" conf=$steps}
                        {include file="../../partials/totals.tpl"}
                    </table>
                    {if !$steps['button_section']}
                    {include file="../../partials/actions.tpl"}
                    {/if}
                </div>
            </div>

            {assign var="idxr_customer_logged" value=(isset($customer) && isset($customer.is_logged) && $customer.is_logged)}
            <div class="braig_addtocart_section">
                <div id="submit_idxrcustomproduct_unique_12345" class="cart-container-unique-12345">
                    <div class="quantity-controls-unique-12345">
                        <input type="number" id="quantity_unique_input" value="1">
                        <div class="qty-buttons-unique-12345">
                            <button class="qty-change-unique-12345 increase-qnt-btn">+</button>
                            <button class="qty-change-unique-12345 decrease-qnt-btn">-</button>
                        </div>
                    </div>
                    <button class="add-to-cart-button-unique-12345 disabled" id="add-to-cart-button-unique-12345">
                        <i class="cart-icon-unique-12345"><img src="/modules/idxrcustomproduct/img/icon/panier.png" alt=">"></i> 
                        <span id="front_tr_add_to_cart">{l s='Ajouter au panier' mod='idxrcustomproduct'}</span>
                    </button>
                </div>
                <div class="cart-container-unique-12345" style="margin-top:10px; display:flex; gap:10px;">
                    <button
                        type="button"
                        class="add-to-cart-button-unique-12345{if !$idxr_customer_logged} disabled{/if}"
                        id="save-customization-button-unique-12345"
                        style="background-color:#2e48c4; flex:1;"
                        {if !$idxr_customer_logged}disabled="disabled" title="{l s='Login to save customisations' mod='idxrcustomproduct'}"{/if}
                    >
                        <i class="cart-icon-unique-12345" style="background-color:#1e0978;">
                            <img src="/modules/idxrcustomproduct/img/icon/save.png" alt=">">
                        </i>
                        <span id="front_tr_save_customization">{l s='Save customization' mod='idxrcustomproduct'}</span>
                    </button>
                    <button
                        type="button"
                        class="add-to-cart-button-unique-12345{if !$idxr_customer_logged} disabled{/if}"
                        id="restore-customization-button-unique-12345"
                        style="background-color:#3b5bd6; flex:1;"
                        {if !$idxr_customer_logged}disabled="disabled" title="{l s='Login to restore customisations' mod='idxrcustomproduct'}"{/if}
                    >
                        <i class="cart-icon-unique-12345" style="background-color:#1e0978;">
                            <img src="/modules/idxrcustomproduct/img/icon/restore.png" alt=">">
                        </i>
                        <span id="front_tr_restore_customization">{l s='Restore customization' mod='idxrcustomproduct'}</span>
                    </button>
                </div>
                <div style="margin-top:8px; text-align:center;">
                    <a
                        href="{$link->getModuleLink('idxrcustomproduct','simulations')|escape:'htmlall':'UTF-8'}"
                        target="_blank"
                        rel="noopener noreferrer"
                        style="font-size:12px; color:#2e48c4; text-decoration:underline;"
                    >
                        {l s='View all customisations' mod='idxrcustomproduct'}
                    </a>
                </div>
            </div>
        </div>
    </div>
    {include file="../../partials/descmodal.tpl"}
	<!-- Preloader overlay -->
	<div id="preloader-overlay-xyz123" class="preloader-overlay">
    	<div id="spinner-abc456" class="spinner"></div>
	</div>
    <style>
            .idxr-c-item-name input {
            height: .8em;
        }
        #save-customization-button-unique-12345 span,
        #restore-customization-button-unique-12345 span {
            font-size: 14px !important;
            margin-left: 34px;
            display: inline-block;
        }
    </style>
<script>

  function initContainer() {
    // i18n
    $('#front_tr_add_to_cart').text(window.idxr_tr_add_to_cart || $('#front_tr_add_to_cart').text());
    $('#front_tr_save_customization').text(window.idxr_tr_save_customization || $('#front_tr_save_customization').text());
    $('#front_tr_restore_customization').text(window.idxr_tr_restore_customization || $('#front_tr_restore_customization').text());
    $('#front_tr_eppaisseur').text(window.idxr_tr_epaisseur || $('#front_tr_eppaisseur').text());
    $('#front_tr_volume').text(window.idxr_tr_volume || $('#front_tr_volume').text());
    $('#front_tr_surface').text(window.idxr_tr_surface || $('#front_tr_surface').text());
    $('#front_tr_poids').text(window.idxr_tr_weight || $('#front_tr_poids').text());
    $('#front_tr_show_struct').text(window.idxr_tr_show_price_structure || $('#front_tr_show_struct').text());

    // preloader
    $('#preloader-overlay-xyz123, #spinner-abc456').hide();

    function updateExistingQuantity() {
      const quantity = parseInt($('#quantity_unique_input').val(), 10) || 1;
      $('#idxrcustomprouct_quantity_wanted').val(quantity).trigger('input');
      if (typeof window.updateTotal === 'function') window.updateTotal();
    }

    // --- remove old handlers (namespace `.idxr`) then reattach ---

    $('#quantity_unique_input')
      .off('change.idxr')
      .on('change.idxr', function () {
        let val = parseInt($(this).val(), 10);
        if (isNaN(val) || val < 1) $(this).val(1);
        updateExistingQuantity();
      });

    $('.increase-qnt-btn')
      .off('click.idxr')
      .on('click.idxr', function () {
        let quantity = parseInt($('#quantity_unique_input').val(), 10) || 1;
        $('#quantity_unique_input').val(quantity + 1).trigger('change');
      });

    $('.decrease-qnt-btn')
      .off('click.idxr')
      .on('click.idxr', function () {
        let quantity = parseInt($('#quantity_unique_input').val(), 10) || 1;
        if (quantity > 1) $('#quantity_unique_input').val(quantity - 1).trigger('change');
      });

    $('#add-to-cart-button-unique-12345')
      .off('click.idxr')
      .on('click.idxr', function () {
        $(this).prop('disabled', true);
        updateExistingQuantity();
        $('#idxrcustomproduct_send').trigger('click');
      });

    $('#toggleTableLinkDiv')
      .off('click.idxr')
      .on('click.idxr', function (e) {
        e.preventDefault();
        $('#collapsibleSection').toggle();
      });

    // optional: one-time log (won't spam because we rebind with .off/.on)
    // console.log('[idxr] handlers (re)attached');
  }

  // FIRST TIME: wait for full window load; if already loaded, run immediately
  function bootAfterLoad() { setTimeout(initContainer, 0); }
  if (document.readyState === 'complete') {
    console.log('Second time');
    bootAfterLoad();
  } else {
    console.log('First time');
    window.addEventListener('load', bootAfterLoad, { once: true });
  }

  // RERENDER (variant change): re-attach handlers (DOM may be swapped)
  // if (window.prestashop && typeof window.prestashop.on === 'function') {
    // small delay to let the new DOM land
   //  window.prestashop.on('updatedProduct', function () { setTimeout(initContainer, 80); });
  // }
</script>

</section>
