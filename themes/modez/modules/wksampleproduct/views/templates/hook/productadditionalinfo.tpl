{*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License version 3.0
* that is bundled with this package in the file LICENSE.txt
* It is also available through the world-wide-web at this URL:
* https://opensource.org/licenses/AFL-3.0
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your needs
* please refer to CustomizationPolicy.txt file inside our module for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
*}

<div class="wk-sample-block" style="clear: both;width: 100%;">
    <div class="alert alert-danger" id="wk_sp_standard_product_error" {if !isset($standardAdded) || !$standardAdded} style="display:none"{/if}>
        <i class="material-icons product-last-items" style="color:#ff9a52;">&#xE002;</i>
        {l s='You have added this standard product in cart. Please proceed or delete that cart then you can buy the sample product' mod='wksampleproduct'}
    </div>
    <div class="alert alert-danger" id="wk_sp_ajax_error_wrap" style="display:none">
        <i class="material-icons product-last-items" style="color:#ff9a52;">&#xE002;</i>
        <span id="wk_sp_ajax_error">
        </span>
    </div>
     {*<div class="h4">{l s='Commandez votre échantillon' mod='wksampleproduct'}</div>*}
       <div class="h4" id="commandez-votre-echantillon" ></div>

    <div class="sample-block">
    <div class="col-12 col-sm-6 ">
     <div class="row-sample">

        <div class="sample-description col-md-6">
        <div class="row-sample">
        <img class="block-image" loading="lazy" src="/themes/modez/assets/img/sample-card/epaisseur-echantillon.webp" alt="Épaisseur" with="38" height="38">
        </div>
          {$sample.description nofilter}
        </div>
        <div class="sample-price col-md-6">
                <div class="row-sample">
        <img class="block-image" loading="lazy" src="/themes/modez/assets/img/sample-card/echantillon-produit.webp" alt="Épaisseur" with="38" height="38">
        </div>
        <div>
         <span class="control-label">
          {if isset($samplePrice) && isset($sampleOrgPrice)}
            <span class="product-price">{if $sampleOrgPrice == 0}{l s='Free' mod='wksampleproduct'}{else}{$samplePrice} {if (($sample.price_type == 4) && ($sample.price_tax == 0)) || ($isTaxExclDisplay)}({l s='Tax excluded' mod='wksampleproduct'}){else}({l s='Tax included' mod='wksampleproduct'}){/if}{/if}
            </span>
          {/if}
         </span> 
         {* <p>{l s='Le tarif de cet échantillon inclut les frais de port' mod='wksampleproduct'}</p> *}
            <p id="tarif-echantillon-inclut-les-frais-de-port"></p>
         </div>
        </div>
     </div>

        <div class="product-quantity clearfix" {if isset($standardAdded) && $standardAdded} style="display:none"{/if}>
          {if $wkShowQtySpin}
         <div class="qty">
            <div class="wktouchspin input-group bootstrap-touchspin"style="height: auto;min-width: 100%;">
                <input type="text" name="wkqty" id="wkquantity_wanted" min="1" value="1" class="input-group form-control">
                <span class="input-group-btn-vertical" style="margin-right: 10px;" >
                    <button class="btn btn-touchspin wkjs-touchspin wkbootstrap-touchspin-up" type="button">
                        <i class="material-icons touchspin-up"></i>
                    </button>
                    <button class="btn btn-touchspin wkjs-touchspin wkbootstrap-touchspin-down" type="button">
                        <i class="material-icons touchspin-down"></i>
                    </button>
                </span>
            </div>
        </div>
        {else}
            <input type="hidden" name="wkqty" id="wkquantity_wanted" value="1">
        {/if}
        <button class="btn btn-primary add-to-cart"
            id="wksamplebuybtn"
            data-id-product="{$wkIdProduct}"
            data-id-customer="{$wkIdCustomer}"
            data-id-product-attr="{$wkIdProductAttr}"
            data-cart-url="{$cartPageURL}"
            {if $sampleFullInCart}disabled{/if}
            style="background: {$wkSampleBg};color: {$wkSampleColor}!important;"
        >
        {if empty($sample.button_label)}
                {l s='Buy sampless' mod='wksampleproduct'}
            {else}{$sample.button_label}{/if}
        </button>
      </div>
    </div>
    <div class="col-12 col-sm-6"> 
				<div class="block-cta__group block-cta__group--image">
					<div class="block-cta__image">
						<div class="block-cta__thumbnail">
							<img class="block-cta__image" loading="lazy" src="/themes/modez/assets/img/sample-card/sample-card.webp" alt="" with="330" height="300">
								</div>
					</div>
				</div>
			</div>
    </div>
      </div>
    {if !$addToCartEnabled || $sampleFullInCart}
        <span  id="wksampleproductqty_stockerror" class="wksampleproduct-lineerror">
            <i class="material-icons wkproduct-unavailable"></i>
            {*{l s='Out-of-stock' mod='wksampleproduct'}*}
        </span>
    {/if}
</div>

<script>
  const fallbackLang = "fr";
  const htmlLang = document.documentElement.lang || navigator.language || navigator.userLanguage;
  const shortLang = htmlLang.split('-')[0]; 
  const orderSampleTranslations = {
    "fr": "Commandez votre échantillon",
    "it": "Ordina il tuo campione",
    "en": "Order your sample",
    "de": "Bestellen Sie Ihr Muster",
    "es": "Solicita tu muestra",
    "pt": "Peça a sua amostra",
    "nl": "Bestel uw monster",
    "pl": "Zamów próbkę"
  };
  const shippingIncludedTranslations = {
    "fr": "Le tarif de cet échantillon inclut les frais de port",
    "it": "Il prezzo di questo campione include le spese di spedizione",
    "en": "The price of this sample includes shipping costs",
    "de": "Der Preis für dieses Muster beinhaltet die Versandkosten",
    "es": "El precio de esta muestra incluye los gastos de envío",
    "pt": "O preço desta amostra inclui os custos de envio",
    "nl": "De prijs van dit monster is inclusief verzendkosten",
    "pl": "Cena tej próbki obejmuje koszty wysyłki"
  };
  document.getElementById("commandez-votre-echantillon").textContent =
    orderSampleTranslations[shortLang] || orderSampleTranslations[fallbackLang];
  document.getElementById("tarif-echantillon-inclut-les-frais-de-port").textContent =
    shippingIncludedTranslations[shortLang] || shippingIncludedTranslations[fallbackLang];
</script>