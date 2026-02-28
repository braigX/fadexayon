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

  <div class="payment_module sogecommerce sogecommerce_choozeo {$sogecommerce_tag|escape:'html':'UTF-8'}">
    {if {$sogecommerce_choozeo_options|@count} == 1}
      <a href="javascript: $('#sogecommerce_choozeo').submit();" title="{l s='Click here to pay with Choozeo' mod='sogecommerce'}">
    {else}
      <a class="unclickable" title="{l s='Click on a payment option to pay with Choozeo' mod='sogecommerce'}" href="javascript: void(0);">
    {/if}
        <img class="logo" src="{$sogecommerce_logo|escape:'html':'UTF-8'}" />{$sogecommerce_title|escape:'html':'UTF-8'}

        <form action="{$link->getModuleLink('sogecommerce', 'redirect', array(), true)|escape:'html':'UTF-8'}" method="post" id="sogecommerce_choozeo">
          <input type="hidden" name="sogecommerce_payment_type" value="choozeo" />
          <br />

          {foreach from=$sogecommerce_choozeo_options key="key" item="option"}
            <label class="sogecommerce_card_click" for="sogecommerce_card_type_{$key|escape:'html':'UTF-8'}">
              <input type="radio"
                     name="sogecommerce_card_type"
                     id="sogecommerce_card_type_{$key|escape:'html':'UTF-8'}"
                     value="{$key|escape:'html':'UTF-8'}" />
              <img src="{$option['logo']}"
                   alt="{$option['label']|escape:'html':'UTF-8'}"
                   title="{$option['label']|escape:'html':'UTF-8'}" />
            </label>
          {/foreach}
        </form>
      </a>
  </div>

  <script type="text/javascript">
    $('div.payment_module.sogecommerce_choozeo a img').on('click', function(e) {
      $(this).parent().find('input').prop('checked', true);
      $('#sogecommerce_choozeo').submit();
    });
  </script>

  {if {$sogecommerce_choozeo_options|@count} == 1}
    <script type="text/javascript">
      $('div.payment_module.sogecommerce_choozeo a').on('hover', function(e) {
        $('div.payment_module.sogecommerce_choozeo a form .sogecommerce_card_click img').toggleClass('hover');
      });
    </script>
  {/if}

{if version_compare($smarty.const._PS_VERSION_, '1.6', '>=')}
  </div></div>
{/if}