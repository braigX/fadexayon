{**
 * Copyright © Lyra Network.
 * This file is part of Sogecommerce plugin for PrestaShop. See COPYING.md for license details.
 *
 * @author    Lyra Network (https://www.lyra.com/)
 * @copyright Lyra Network
 * @license   https://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 *}

{if version_compare($smarty.const._PS_VERSION_, '1.6', '>=')}
  <div class="row"><div class="col-xs-12{if version_compare($smarty.const._PS_VERSION_, '1.6.0.11', '<')} col-md-6{/if}">
{/if}

<div class="payment_module sogecommerce {$sogecommerce_tag|escape:'html':'UTF-8'}">
{if ! $sogecommerce_is_valid_sepa_identifier}
  <a href="javascript: $('#sogecommerce_sepa').submit();" title="{l s='Click here to pay with SEPA' mod='sogecommerce'}">
    <img class="logo" src="{$sogecommerce_logo|escape:'html':'UTF-8'}" />{$sogecommerce_title|escape:'html':'UTF-8'}
{else}
  <a class="unclickable" title="{l s='Choose pay with registred means of payment or pay with with another means of payment and click « Pay » button' mod='sogecommerce'}">
    <img class="logo" src="{$sogecommerce_logo|escape:'html':'UTF-8'}" />{$sogecommerce_title|escape:'html':'UTF-8'}
  {include file="./payment_sepa_oneclick.tpl"}
{/if}

    <form action="{$link->getModuleLink('sogecommerce', 'redirect', array(), true)|escape:'html':'UTF-8'}" method="post" id="sogecommerce_sepa">
      <input type="hidden" name="sogecommerce_payment_type" value="sepa" />
      <input id="sogecommerce_sepa_payment_by_identifier" type="hidden" name="sogecommerce_sepa_payment_by_identifier" value="1" />
      {if $sogecommerce_is_valid_sepa_identifier}
        <br />
        {if version_compare($smarty.const._PS_VERSION_, '1.6', '<')}
          <input id="sogecommerce_submit_form" type="submit" name="submit" value="{l s='Pay' mod='sogecommerce'}" class="button"/>
        {else}
          <button id="sogecommerce_submit_form" type="submit" name="submit" class="button btn btn-default standard-checkout button-medium">
            <span>{l s='Pay' mod='sogecommerce'}</span>
          </button>
        {/if}
      {/if}
    </form>
  </a>
</div>

{if version_compare($smarty.const._PS_VERSION_, '1.6', '>=')}
  </div></div>
{/if}