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
  <a class="unclickable"
    {if $sogecommerce_is_valid_std_identifier}
        title="{l s='Choose pay with registred means of payment or enter payment information and click « Pay » button' mod='sogecommerce'}"
    {else}
        {if $sogecommerce_std_rest_popin_mode == 'True'}
            title="{l s='Click on « Pay » button to enter payment information in a popin mode' mod='sogecommerce'}"
        {else}
            title="{l s='Enter payment information and click « Pay » button' mod='sogecommerce'}"
        {/if}
    {/if}
  >
    <div id="sogecommerce_std_title"><img class="logo" src="{$sogecommerce_logo|escape:'html':'UTF-8'}" />{$sogecommerce_title|escape:'html':'UTF-8'}</div>

    {if $sogecommerce_std_card_data_mode == '7' || $sogecommerce_std_card_data_mode === '8' || $sogecommerce_std_card_data_mode === '9'}
        <div class="kr-smart-form"{if $sogecommerce_std_rest_popin_mode == 'True'} kr-popin {/if} {if $sogecommerce_std_card_data_mode === '8' || $sogecommerce_std_card_data_mode === '9'} kr-card-form-expanded {/if} {if $sogecommerce_std_card_data_mode === '9'} kr-no-card-logo-header {/if} kr-form-token="{$sogecommerce_rest_identifier_token|escape:'html':'UTF-8'}"
          {if isset($sogecommerce_set_std_rest_kr_public_key)}kr-public-key="{$sogecommerce_set_std_rest_kr_public_key|escape:'html':'UTF-8'}"{/if}
          {if isset($sogecommerce_set_std_rest_return_url)}kr-post-url-success="{$sogecommerce_set_std_rest_return_url|escape:'html':'UTF-8'}"{/if}
          {if isset($sogecommerce_set_std_rest_return_url)}kr-post-url-refused="{$sogecommerce_set_std_rest_return_url|escape:'html':'UTF-8'}"{/if}></div>
    {/if}

    <script type="text/javascript">
        $(document).ready(function(){
            {if $sogecommerce_std_display_title != 'True'}
                $paymentOptions = $('.payment_module');
                if ($paymentOptions && $paymentOptions.length == 1) {
                    $('#sogecommerce_std_title').hide();
                }
            {/if}
        });

        var whenDefined = function(context, variableName, cb, interval) {
            if (interval === null) {
                interval = 150;
            }

            var checkVariable = function() {
                if (context[variableName]) {
                    cb();
                } else {
                    setTimeout(checkVariable, interval);
                }
            }

            setTimeout(checkVariable, 0);
        };

        whenDefined(window, 'KR', function() {
            KR.setFormConfig({ formToken: "{$sogecommerce_rest_identifier_token|escape:'html':'UTF-8'}", language: "{$sogecommerce_set_std_rest_language|escape:'html':'UTF-8'}" });

            {if $sogecommerce_std_smartform_compact_mode == 'True'}
              KR.setFormConfig({ cardForm: { layout: 'compact' }, smartForm: { layout: 'compact'} });
            {/if}

            {if $sogecommerce_std_smartform_payment_means_grouping_threshold != 'False'}
              KR.setFormConfig({ smartForm: { groupingThreshold: "{$sogecommerce_std_smartform_payment_means_grouping_threshold|escape:'html':'UTF-8'}" } });
            {/if}

            KR.onFormCreated(sogecommerceInitRestEvents);
        });
    </script>

    {if $sogecommerce_is_valid_std_identifier}
      {include file="./payment_std_oneclick.tpl"}
      <input id="sogecommerce_payment_by_identifier" type="hidden" name="sogecommerce_payment_by_identifier" value="1" />
    {/if}
  </a>
</div>

{if version_compare($smarty.const._PS_VERSION_, '1.6', '>=')}
  </div></div>
{/if}