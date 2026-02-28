{**
 * Copyright © Lyra Network.
 * This file is part of Sogecommerce plugin for PrestaShop. See COPYING.md for license details.
 *
 * @author    Lyra Network (https://www.lyra.com/)
 * @copyright Lyra Network
 * @license   https://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 *}

{extends file='customer/page.tpl'}

{block name='page_title'}
    {l s='My payment means' mod='sogecommerce'}
{/block}

{block name='page_content'}
  <div class="container">
    <section class="page_content">
      <div id="sogecommerce-no-tokens-warning" {if $sogecommerce_show_wallet == true} style="display: none;" {/if} class="alert alert-info" role="alert" data-alert="info">{l s='You have no stored payment means.' mod='sogecommerce'}</div>

    {if $sogecommerce_show_wallet == true}
      <div class="col-md-4">
        {include file="module:sogecommerce/views/templates/hook/payment_std_rest.tpl"}
      </div>
    {/if}
    </section>
  </div>

  <script type="text/javascript">
    function sogecommerceManageWalletDisplay() {
        if ($('.kr-smart-form-wallet').length == 0) {
            $('#sogecommerce_standard_rest_wrapper').addClass('sogecommerce_hide-wallet-elements');
            $('#sogecommerce-no-tokens-warning').show();

            return;
        }

        $('div.kr-methods-list-options-item.kr-cards').addClass('sogecommerce_hide-wallet-elements');
        $('div.kr-smart-form-list-section-name--other').addClass('sogecommerce_hide-wallet-elements');
        $('div.kr-smart-form-list-section-name').addClass('sogecommerce_hide-wallet-elements');

        $('.kr-methods-list-options--wallet').each(function() {
          if (! $(this).hasClass('kr-methods-list-options--extra')) {
              $(this).addClass('sogecommerce_hide-wallet-elements');
          }
        });
    }

    {if $sogecommerce_show_tokens_only == true}
      KR.onLoaded(() => {
        sogecommerceManageWalletDisplay();
      })
    {/if}
  </script>
{/block}

