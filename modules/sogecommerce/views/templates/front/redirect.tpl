{**
 * Copyright © Lyra Network.
 * This file is part of Sogecommerce plugin for PrestaShop. See COPYING.md for license details.
 *
 * @author    Lyra Network (https://www.lyra.com/)
 * @copyright Lyra Network
 * @license   https://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 *}

{extends file='checkout/checkout.tpl'}
{block name='content'}
  <section id="content">
    <div class="row">
      <div class="col-xs-12 col-lg-8">
        <section id="sogecommerce_content" class="checkout-step -current" style="text-align: center;">
          <h1 class="step-title h3" style="margin-bottom: 20px;">
            {l s='Redirection to payment gateway' mod='sogecommerce'}
          </h1>

          <div class="content">
            <h3>{$sogecommerce_title|escape:'html':'UTF-8'}</h3>

            <form action="{$sogecommerce_url|escape:'html':'UTF-8'}" method="post" id="sogecommerce_form" name="sogecommerce_form" onsubmit="sogecommerceDisablePayment();">
              {foreach from=$sogecommerce_params key='key' item='value'}
                <input type="hidden" name="{$key|escape:'html':'UTF-8'}" value="{$value|escape:'html':'UTF-8'}" />
              {/foreach}

              <p>
                <img src="{$sogecommerce_logo|escape:'html':'UTF-8'}" style="margin-bottom: 5px" />
                <br />

                {l s='Please wait, you will be redirected to the payment gateway.' mod='sogecommerce'}

                <br /> <br />
                {l s='If nothing happens in 10 seconds, please click the button below.' mod='sogecommerce'}
                <br /><br />
              </p>

              <p class="cart_navigation clearfix">
                <button type="submit" id="sogecommerce_submit_payment" class="button btn btn-default standard-checkout button-medium" >
                  <span>{l s='Pay' mod='sogecommerce'}</span>
                </button>
              </p>
            </form>
          </div>
        </section>
      </div>

       <div class="col-md-4">
      </div>
    </div>

    <script type="text/javascript">
      function sogecommerceDisablePayment() {
        document.getElementById('sogecommerce_submit_payment').disabled = true;
      }

      function sogecommerceSubmitForm() {
        document.getElementById('sogecommerce_submit_payment').click();
      }

      if (window.addEventListener) { // for most browsers
        window.addEventListener('load', sogecommerceSubmitForm, false);
      } else if (window.attachEvent) { // for IE 8 and earlier versions
        window.attachEvent('onload', sogecommerceSubmitForm);
      }
    </script>
  </section>
{/block}