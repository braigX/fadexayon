{**
 * Web Browser Push Notifications using OneSignal
 *
 * @author    Rolige <www.rolige.com>
 * @copyright Since 2011 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 *}

<div class="info_tags {$rg_pushnotifications_icon_selector.form_group_class|escape:'htmlall':'UTF-8'}">
  <p>
    {l s='The following tags will help you to personalize your notification\'s texts:' mod='rg_pushnotifications'}
  </p>
  <p>
    <ul>
      <li><code>&#123;firstname&#125;</code>: <i>{l s='Generates a text with the first name of the customer associated to order' mod='rg_pushnotifications'}</i></li>
      <li><code>&#123;lastname&#125;</code>: <i>{l s='Generates a text with the last name of the customer associated to order' mod='rg_pushnotifications'}</i></li>
      <li><code>&#123;order_reference&#125;</code>: <i>{l s='Generates a text with the order reference' mod='rg_pushnotifications'}</i></li>
      <li><code>&#123;order_id&#125;</code>: <i>{l s='Generates a text with the order identifier' mod='rg_pushnotifications'}</i></li>
      <li><code>&#123;order_state&#125;</code>: <i>{l s='Generates a text with the order current state' mod='rg_pushnotifications'}</i></li>
    </ul>
  </p>
</div>
