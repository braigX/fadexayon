{**
 * Copyright © Lyra Network.
 * This file is part of Sogecommerce plugin for PrestaShop. See COPYING.md for license details.
 *
 * @author    Lyra Network (https://www.lyra.com/)
 * @copyright Lyra Network
 * @license   https://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 *}

{capture name=path}<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">{l s='My account'}</a><span class="navigation-pipe">{$navigationPipe}</span><span class="navigation_page">{l s='My payment means' mod='sogecommerce'}</span>{/capture}
{assign var='error_style' value='background: none repeat scroll 0 0 #FFE2E4;border: 1px solid #E6DB5;font-size: 13px;margin: 0 0 10px;padding: 10px;'}
{assign var='confirm_style' value='background: none repeat scroll 0 0 #ddf0de;border: 1px solid #E6DB5;font-size: 13px;margin: 0 0 10px;padding: 10px;'}

<h1 class="page-heading bottom-indent">
    {l s='My payment means' mod='sogecommerce'}
</h1>

{if $sogecommerce_confirm_msg}
  <p style="{$confirm_style|escape:'html':'UTF-8'}">
    <span style="font-weight: bold;"">{$sogecommerce_confirm_msg}</span>
  </p>
{/if}

{if $sogecommerce_error_msg}
  <p style="{$error_style|escape:'html':'UTF-8'}">
    <span style="font-weight: bold">{$sogecommerce_error_msg}</span>
  </p>
{/if}

<div class="container">
    <section class="page_content card card-block">
       <div id="sogecommerce-no-tokens-warning" {if $sogecommerce_show_wallet == true} style="display: none;" {/if} class="alert alert-info" role="alert" data-alert="info">{l s='You have no stored payment means.' mod='sogecommerce'}</div>

    {if $sogecommerce_show_wallet == true}
      <div class="col-md-4">
         {include file='../hook/payment_std_rest.tpl'}
      </div>
    {/if}
    </section>
</div>

<ul class="footer_links clearfix">
    <li>
        <a class="btn btn-default button button-small" href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">
            <span>
                <i class="icon-chevron-left"></i> {l s='Back to your account'}
            </span>
        </a>
    </li>
    <li>
        <a class="btn btn-default button button-small" href="{if isset($force_ssl) && $force_ssl}{$base_dir_ssl}{else}{$base_dir}{/if}">
            <span>
                <i class="icon-chevron-left"></i> {l s='Home'}
            </span>
        </a>
    </li>
</ul>

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