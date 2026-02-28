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
    <a class="unclickable" href="javascript: void(0);">
      <img class="logo" src="{$sogecommerce_logo|escape:'html':'UTF-8'}" />{$sogecommerce_title|escape:'html':'UTF-8'}

      <form action="{$link->getModuleLink('sogecommerce', 'redirect', array(), true)|escape:'html':'UTF-8'}" method="post" id="sogecommerce_grouped_other">
        <input type="hidden" name="sogecommerce_payment_type" value="grouped_other" />
        <br />

        {assign var=first value=true}
        {foreach from=$sogecommerce_other_options key="key" item="option"}
          <label class="sogecommerce_card_click">
            <input type="radio"
                   name="sogecommerce_card_type"
                   value="{$key|escape:'html':'UTF-8'}"
                   {if $first == true} checked="checked"{/if}
                   onclick="javascript: $('#sogecommerce_grouped_other').submit();" />
            <img src="{$option['logo']}"
                 alt="{$option['label']|escape:'html':'UTF-8'}"
                 title="{$option['label']|escape:'html':'UTF-8'}" />
          </label>

          {assign var=first value=false}
        {/foreach}
      </form>
    </a>
</div>

{if version_compare($smarty.const._PS_VERSION_, '1.6', '>=')}
  </div></div>
{/if}