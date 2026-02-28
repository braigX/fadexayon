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
  {if $sogecommerce_std_card_data_mode == 1 && !$sogecommerce_is_valid_std_identifier}
    <a href="javascript: $('#sogecommerce_standard').submit();" title="{l s='Click here to pay by credit card' mod='sogecommerce'}">
  {else}
    <a class="unclickable"
      {if $sogecommerce_is_valid_std_identifier}
        title="{l s='Choose pay with registred means of payment or enter payment information and click « Pay » button' mod='sogecommerce'}"
      {else}
        title="{l s='Enter payment information and click « Pay » button' mod='sogecommerce'}"
      {/if}
    >
  {/if}
    <img class="logo" src="{$sogecommerce_logo|escape:'html':'UTF-8'}" />{$sogecommerce_title|escape:'html':'UTF-8'}
    {if $sogecommerce_is_valid_std_identifier}
      <br /><br />
      {include file="./payment_std_oneclick.tpl"}
    {/if}

    <form action="{$link->getModuleLink('sogecommerce', 'redirect', array(), true)|escape:'html':'UTF-8'}"
          method="post" id="sogecommerce_standard"
          {if $sogecommerce_is_valid_std_identifier} style="display: none;"{/if}
    >

      <input type="hidden" name="sogecommerce_payment_type" value="standard" />

      {if $sogecommerce_is_valid_std_identifier}
        <input id="sogecommerce_payment_by_identifier" type="hidden" name="sogecommerce_payment_by_identifier" value="1" />
      {/if}

      {if ($sogecommerce_std_card_data_mode == 2)}
        <br />

        {assign var=first value=true}
        {foreach from=$sogecommerce_avail_cards key="key" item="card"}
          <div class="sogecommerce-pm">
            {if $sogecommerce_avail_cards|@count == 1}
              <input type="hidden" id="sogecommerce_card_type_{$key|escape:'html':'UTF-8'}" name="sogecommerce_card_type" value="{$key|escape:'html':'UTF-8'}" >
            {else}
              <input type="radio" id="sogecommerce_card_type_{$key|escape:'html':'UTF-8'}" name="sogecommerce_card_type" value="{$key|escape:'html':'UTF-8'}" style="vertical-align: middle;"{if $first == true} checked="checked"{/if} >
            {/if}

            <label for="sogecommerce_card_type_{$key|escape:'html':'UTF-8'}">
              <img src="{$card['logo']}" alt="{$card['label']|escape:'html':'UTF-8'}" title="{$card['label']|escape:'html':'UTF-8'}" >
            </label>

            {assign var=first value=false}
          </div>
        {/foreach}
        <br />
        <div style="margin-bottom: 12px;"></div>

        {if $sogecommerce_is_valid_std_identifier}
            <div>
                <ul>
                    {if $sogecommerce_std_card_data_mode == 2}
                        <li>
                            <span class="sogecommerce_span">{l s='You will enter payment data after order confirmation.' mod='sogecommerce'}</span>
                        </li>
                    {/if}
                    <li style="margin: 8px 0px 8px;">
                        <span class="sogecommerce_span">{l s='OR' mod='sogecommerce'}</span>
                    </li>
                    <li>
                        <p class="sogecommerce_link" onclick="sogecommerceOneclickPaymentSelect(1)">{l s='Click here to pay with your registered means of payment.' mod='sogecommerce'}</p>
                    </li>
                </ul>
            </div>
        {/if}

        {if version_compare($smarty.const._PS_VERSION_, '1.6', '<')}
          <input id="sogecommerce_submit_form" type="submit" name="submit" value="{l s='Pay' mod='sogecommerce'}" class="button"/>
        {else}
          <button id="sogecommerce_submit_form" type="submit" name="submit" class="button btn btn-default standard-checkout button-medium">
            <span>{l s='Pay' mod='sogecommerce'}</span>
          </button>
        {/if}
      {/if}
    </form>

    {if $sogecommerce_is_valid_std_identifier}
      <script type="text/javascript">
        $('#sogecommerce_standard_link').click(function(){
          {if ($sogecommerce_std_card_data_mode == 2)}
            $('#sogecommerce_submit_form').click();
          {else}
            $('#sogecommerce_standard').submit();
          {/if}
        });
      </script>
    {/if}
  </a>
</div>

{if version_compare($smarty.const._PS_VERSION_, '1.6', '>=')}
  </div></div>
{/if}