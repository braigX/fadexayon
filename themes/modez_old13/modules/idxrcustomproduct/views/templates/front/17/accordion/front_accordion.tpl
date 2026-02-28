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
                        <a href="#show_table" class="price-structure-link-unique-12345" id="toggleTableLink">
                            <i class="braig_i i-left">
                                <img src="/modules/idxrcustomproduct/img/icon/i_left.png" alt=">">    
                            </i>
                            Afficher la structure de prix
                            <i class="braig_i i-right">
                                <img src="/modules/idxrcustomproduct/img/icon/i_right.png" alt=">">
                            </i>
                        </a>
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
                        <span>Ajouter au panier</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    {include file="../../partials/descmodal.tpl"}
	<!-- Preloader overlay -->
	<div id="preloader-overlay-xyz123" class="preloader-overlay">
    	<div id="spinner-abc456" class="spinner"></div>
	</div>

	<script>
		window.onload = function() {
        	$('#preloader-overlay-xyz123').hide();
            $('#spinner-abc456').hide();


            function updateExistingQuantity() {
                const quantity = $('#quantity_unique_input').val();
                $('#idxrcustomprouct_quantity_wanted').val(quantity).trigger('input');
                if (typeof updateTotal !== 'undefined') {
                    updateTotal();
                }
            }

            $('#quantity_unique_input').on('change', function () {
                let val = parseInt($(this).val(), 10);
                if (isNaN(val) || val < 1) {
                    $(this).val(1);
                }
                updateExistingQuantity();
            });

            $('.increase-qnt-btn').on('click', function() {
                let quantity = parseInt($('#quantity_unique_input').val());
                quantity += 1;
                $('#quantity_unique_input').val(quantity).trigger('change');
            });

            $('.decrease-qnt-btn').on('click', function() {
                let quantity = parseInt($('#quantity_unique_input').val());
                if (quantity > 1) {
                    quantity -= 1;
                    $('#quantity_unique_input').val(quantity).trigger('change');
                }
            });

            $('#add-to-cart-button-unique-12345').on('click', function() {
                $('#add-to-cart-button-unique-12345').prop('disabled', true);
                updateExistingQuantity();
                $('#idxrcustomproduct_send').trigger('click');
            });

            // $("#toggleTableLink").click(function(e) {
            //    e.preventDefault();
            //    $("#collapsibleSection").toggle();
            // });
          
    	};
	</script>
</section>