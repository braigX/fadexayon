{**
 * Copyright © Lyra Network.
 * This file is part of Sogecommerce plugin for PrestaShop. See COPYING.md for license details.
 *
 * @author    Lyra Network (https://www.lyra.com/)
 * @copyright Lyra Network
 * @license   https://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 *}

<!-- This meta tag is mandatory to avoid encoding problems caused by \PrestaShop\PrestaShop\Core\Payment\PaymentOptionFormDecorator -->
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<form action="{$link->getModuleLink('sogecommerce', 'redirect', array(), true)|escape:'html':'UTF-8'}" method="post" style="margin-left: 2.875rem; margin-top: 1.25rem; margin-bottom: 1rem;">
  <input type="hidden" name="sogecommerce_payment_type" value="fullcb">

  {assign var=first value=true}
  {foreach from=$sogecommerce_fullcb_options key="key" item="option"}
    <div style="padding-bottom: 5px;">
      {if $sogecommerce_fullcb_options|@count == 1}
        <input type="hidden" id="sogecommerce_fullcb_option_{$key|escape:'html':'UTF-8'}" name="sogecommerce_card_type" value="{$key|escape:'html':'UTF-8'}" >
      {else}
        <input type="radio"
               id="sogecommerce_fullcb_option_{$key|escape:'html':'UTF-8'}"
               name="sogecommerce_card_type"
               value="{$key|escape:'html':'UTF-8'}"
               style="vertical-align: middle;"
               {if $first == true} checked="checked"{/if}
               onclick="javascript: $('.sogecommerce_fullcb_review').hide(); $('#sogecommerce_fullcb_review_{$key|escape:'html':'UTF-8'}').show();">
      {/if}

      <label for="sogecommerce_fullcb_option_{$key|escape:'html':'UTF-8'}" style="display: inline;">
        <span style="vertical-align: middle;">{$option.localized_label|escape:'html':'UTF-8'}</span>
      </label>

      <table class="sogecommerce_fullcb_review sogecommerce_review" id="sogecommerce_fullcb_review_{$key|escape:'html':'UTF-8'}" {if $first != true} style="display: none;"{/if}>
        <tr>
          <td>
            <table>
              <tbody>
                <tr>
                  <td>{l s='Order amount :' mod='sogecommerce'}</td>
                  <td class="amount">{$option.order_amount|escape:'html':'UTF-8'}</td>
                </tr>
                <tr>
                  <td>{l s='Fees :' mod='sogecommerce'}</td>
                  <td class="amount">{$option.fees|escape:'html':'UTF-8'}</td>
                </tr>
                <tr>
                  <td colspan="2"><hr style="margin: 0px;" /></td>
                </tr>
                <tr>
                  <td>{l s='Total amount :' mod='sogecommerce'}</td>
                  <td class="amount">{$option.total_amount|escape:'html':'UTF-8'}</td>
                </tr>
              </tbody>
            </table>
          </td>
          <td>
            <table>
              <tbody>
                <tr>
                  <th colspan="2">{l s='Installments' mod='sogecommerce'}</th>
                </tr>

                <tr>
                  <td>{$smarty.now|date_format:'%d/%m/%Y'|escape:'html':'UTF-8'}</td>
                  <td class="amount">{$option.first_payment|escape:'html':'UTF-8'}</td>
                </tr>
                {section name=row start=1 loop=$option.count step=1}
                  {assign var=i value={$smarty.section.row.index|intval}}
                  <tr>
                    <td>{"+{$i|escape:'html':'UTF-8'} months"|date_format:'%d/%m/%Y'}</td>
                    <td class="amount">{$option.monthly_payment|escape:'html':'UTF-8'}</td>
                  </tr>
                {/section}
              </tbody>
            </table>
          </td>
        </tr>
      </table>

    {assign var=first value=false}
    </div>
  {/foreach}
</form>

<script type="text/javascript">
  window.onload = function(e) {
    options = document.getElementsByClassName('payment-option');
    if ((typeof options !== null) && (options.length == 1)) {
      document.getElementById('pay-with-payment-option-1-form').classList.add('sogecommerce-show-options');
    } else {
      document.getElementById('pay-with-payment-option-1-form').classList.remove('sogecommerce-show-options');
    }
  };
</script>