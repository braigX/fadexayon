{**
 * Copyright © Lyra Network.
 * This file is part of Sogecommerce plugin for PrestaShop. See COPYING.md for license details.
 *
 * @author    Lyra Network (https://www.lyra.com/)
 * @copyright Lyra Network
 * @license   https://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 *}

<tr id="sogecommerce_extra_payment_means_option_{$key|escape:'html':'UTF-8'}">
  <td>
    <input id="SOGECOMMERCE_EXTRA_PAYMENT_MEANS_{$key|escape:'html':'UTF-8'}_code"
        name="SOGECOMMERCE_EXTRA_PAYMENT_MEANS[{$key|escape:'html':'UTF-8'}][code]"
        value="{$option.code|escape:'html':'UTF-8'}"
        style="width: 150px;"
        type="text">
  </td>
  <td>
    <input id="SOGECOMMERCE_EXTRA_PAYMENT_MEANS_{$key|escape:'html':'UTF-8'}title"
        name="SOGECOMMERCE_EXTRA_PAYMENT_MEANS[{$key|escape:'html':'UTF-8'}][title]"
        value="{$option.title|escape:'html':'UTF-8'}"
        style="width: 300px;"
        type="text">
  </td>
  <td>
    <button type="button" onclick="javascript: sogecommerceDeleteExtraPaymentMeansOption({$key|escape:'html':'UTF-8'});">{l s='Delete' mod='sogecommerce'}</button>
  </td>
</tr>
