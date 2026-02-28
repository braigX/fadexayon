{**
 * Copyright © Lyra Network.
 * This file is part of Sogecommerce plugin for PrestaShop. See COPYING.md for license details.
 *
 * @author    Lyra Network (https://www.lyra.com/)
 * @copyright Lyra Network
 * @license   https://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 *}

<script type="text/javascript">
  $(function() {
    $('#accordion').accordion({
      active: false,
      collapsible: true,
      autoHeight: false,
      heightStyle: 'content',
      header: 'h4',
      animated: false
    });

    {if $sogecommerce_plugin_features['support']}
      $('contact-support').on('sendmail', function(e){
        $.ajax({
          type: 'POST',
          url: "{$sogecommerce_request_uri}",
          data: e.originalEvent.detail,
          success: function(res) {
            location.reload();
          },
          dataType: 'html'
        });
      });
    {/if}
  });
</script>

<script type="text/javascript">
  function sogecommerceCardEntryChanged() {
    var cardDataMode = $('select#SOGECOMMERCE_STD_CARD_DATA_MODE option:selected').val();

    switch (cardDataMode) {
      case '7':
      case '8':
      case '9':
       $('#SOGECOMMERCE_REST_SETTINGS').show();
       $('#SOGECOMMERCE_STD_SMARTFORM_CUSTOMIZATION_SETTINGS').show();
       sogecommerceOneClickMenuDisplay();
       toggleEmbeddedCheckbox(true);
       break;
        $('#SOGECOMMERCE_REST_SETTINGS').show();
        $('#SOGECOMMERCE_STD_SMARTFORM_CUSTOMIZATION_SETTINGS').show();
        $('#SOGECOMMERCE_STD_CANCEL_IFRAME_MENU').hide();
        sogecommerceOneClickMenuDisplay();
        toggleEmbeddedCheckbox(true);
        break;
      default:
        $('#SOGECOMMERCE_REST_SETTINGS').hide();
        $('#SOGECOMMERCE_STD_USE_WALLET_MENU').hide();
        toggleEmbeddedCheckbox(false);
    }
  }

  function toggleEmbeddedCheckbox(show) {
    if (show) {
      $('.SOGECOMMERCE_OTHER_PAYMENT_MEANS_EMBEDDED').show();
      $('input').filter(function(){ return this.id.match(/SOGECOMMERCE_OTHER_PAYMENT_MEANS_\d*_embedded/); }).parent().show();
    } else {
      $('.SOGECOMMERCE_OTHER_PAYMENT_MEANS_EMBEDDED').hide();
      $('input').filter(function(){ return this.id.match(/SOGECOMMERCE_OTHER_PAYMENT_MEANS_\d*_embedded/); }).parent().hide();
      $('select').filter(function(){ return this.id.match(/SOGECOMMERCE_OTHER_PAYMENT_MEANS_\d*_validation/); }).prop("disabled", false);
      $('input').filter(function(){ return this.id.match(/SOGECOMMERCE_OTHER_PAYMENT_MEANS_\d*_capture/); }).prop("disabled", false);
    }
  }
</script>

<script type="text/javascript">
  function onEmbeddedCheckboxChange(checkbox) {
    var embeddedMode = checkbox.checked;
    var key = checkbox.className;
    $("#SOGECOMMERCE_OTHER_PAYMENT_MEANS_" + key + "_capture").prop("disabled", embeddedMode);
    $("#SOGECOMMERCE_OTHER_PAYMENT_MEANS_" + key + "_validation").prop("disabled", embeddedMode);
  }
</script>

<form method="POST" action="{$sogecommerce_request_uri|escape:'html':'UTF-8'}" class="defaultForm form-horizontal">
  <div style="width: 100%;">
    <fieldset>
      <legend>
        <img style="width: 20px; vertical-align: middle;" src="../modules/sogecommerce/logo.png">Sogecommerce
      </legend>

      <div style="padding: 5px;">{l s='Developed by' mod='sogecommerce'} <b><a href="https://www.lyra.com/" target="_blank">Lyra Network</a></b></div>
      <div style="padding: 5px;">{l s='Contact us' mod='sogecommerce'} <span style="display: inline-table;"><b>{$sogecommerce_formatted_support_email|unescape:'html':'UTF-8'}</b></span></div>
      <div style="padding: 5px;">{l s='Module version' mod='sogecommerce'} <b>{if $smarty.const._PS_HOST_MODE_}Cloud{/if}{$sogecommerce_plugin_version|escape:'html':'UTF-8'}</b></div>
      <div style="padding: 5px;">{l s='Gateway version' mod='sogecommerce'} <b>{$sogecommerce_gateway_version|escape:'html':'UTF-8'}</b></div>

      {if !empty($sogecommerce_doc_files)}
        <div style="padding: 5px;"><span style="color: red; font-weight: bold; text-transform: uppercase;">{l s='Click to view the module configuration documentation :' mod='sogecommerce'}</span>
        {foreach from=$sogecommerce_doc_files key="lang" item="url"}
          <a style="margin-left: 10px; font-weight: bold; text-transform: uppercase;" href="{$url|escape:'html':'UTF-8'}" target="_blank">{$lang|escape:'html':'UTF-8'}</a>
        {/foreach}
        </div>
      {/if}

      {if $sogecommerce_plugin_features['support']}
        <div style="padding: 5px;"><contact-support
          shop-id="{$SOGECOMMERCE_SITE_ID|escape:'html':'UTF-8'}"
          context-mode="{$SOGECOMMERCE_MODE|escape:'html':'UTF-8'}"
          sign-algo="{$SOGECOMMERCE_SIGN_ALGO|escape:'html':'UTF-8'}"
          contrib="{$sogecommerce_contrib|escape:'html':'UTF-8'}"
          integration-mode="{$sogecommerce_card_data_entry_modes[$SOGECOMMERCE_STD_CARD_DATA_MODE]|escape:'html':'UTF-8'}"
          plugins="{$sogecommerce_installed_modules|escape:'html':'UTF-8'}"
          title=""
          first-name="{$sogecommerce_employee->firstname|escape:'html':'UTF-8'}"
          last-name="{$sogecommerce_employee->lastname|escape:'html':'UTF-8'}"
          from-email="{$sogecommerce_employee->email|escape:'html':'UTF-8'}"
          to-email="{$sogecommerce_support_email|escape:'html':'UTF-8'}"
          cc-emails=""
          phone-number=""
          language="{$prestashop_lang.iso_code|escape:'html':'UTF-8'}">
        </contact-support></div>
      {/if}
    </fieldset>
  </div>

  <br /><br />

  <div id="accordion" style="width: 100%; float: none;">
    <h4 style="font-weight: bold; margin-bottom: 0; overflow: hidden; line-height: unset !important;">
      <a href="#">{l s='GENERAL CONFIGURATION' mod='sogecommerce'}</a>
    </h4>
    <div>
      <fieldset>
        <legend>{l s='BASE SETTINGS' mod='sogecommerce'}</legend>

        <label for="SOGECOMMERCE_ENABLE_LOGS">{l s='Logs' mod='sogecommerce'}</label>
        <div class="margin-form">
          <select id="SOGECOMMERCE_ENABLE_LOGS" name="SOGECOMMERCE_ENABLE_LOGS">
            {foreach from=$sogecommerce_enable_disable_options key="key" item="option"}
              <option value="{$key|escape:'html':'UTF-8'}"{if $SOGECOMMERCE_ENABLE_LOGS === $key} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
            {/foreach}
          </select>
          <p>{l s='Enable / disable module logs.' mod='sogecommerce'}</p>
        </div>
      </fieldset>
      <div class="clear">&nbsp;</div>

      <fieldset>
        <legend>{l s='PAYMENT GATEWAY ACCESS' mod='sogecommerce'}</legend>

        <label for="SOGECOMMERCE_SITE_ID">{l s='Site ID' mod='sogecommerce'}</label>
        <div class="margin-form">
          <input type="text" id="SOGECOMMERCE_SITE_ID" name="SOGECOMMERCE_SITE_ID" value="{$SOGECOMMERCE_SITE_ID|escape:'html':'UTF-8'}" autocomplete="off">
          <p>{l s='The identifier provided by your bank.' mod='sogecommerce'}</p>
        </div>

        {if !$sogecommerce_plugin_features['qualif']}
          <label for="SOGECOMMERCE_KEY_TEST">{l s='Key in test mode' mod='sogecommerce'}</label>
          <div class="margin-form">
            <input type="text" id="SOGECOMMERCE_KEY_TEST" name="SOGECOMMERCE_KEY_TEST" value="{$SOGECOMMERCE_KEY_TEST|escape:'html':'UTF-8'}" autocomplete="off">
            <p>{l s='Key provided by your bank for test mode (available in your store Back Office).' mod='sogecommerce'}</p>
          </div>
        {/if}

        <label for="SOGECOMMERCE_KEY_PROD">{l s='Key in production mode' mod='sogecommerce'}</label>
        <div class="margin-form">
          <input type="text" id="SOGECOMMERCE_KEY_PROD" name="SOGECOMMERCE_KEY_PROD" value="{$SOGECOMMERCE_KEY_PROD|escape:'html':'UTF-8'}" autocomplete="off">
          <p>{l s='Key provided by your bank (available in your store Back Office after enabling production mode).' mod='sogecommerce'}</p>
        </div>

        <label for="SOGECOMMERCE_MODE">{l s='Mode' mod='sogecommerce'}</label>
        <div class="margin-form">
          <select id="SOGECOMMERCE_MODE" name="SOGECOMMERCE_MODE" {if $sogecommerce_plugin_features['qualif']} disabled="disabled"{/if}>
            {foreach from=$sogecommerce_mode_options key="key" item="option"}
              <option value="{$key|escape:'html':'UTF-8'}"{if $SOGECOMMERCE_MODE === $key} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
            {/foreach}
          </select>
          <p>{l s='The context mode of this module.' mod='sogecommerce'}</p>
        </div>

        <label for="SOGECOMMERCE_SIGN_ALGO">{l s='Signature algorithm' mod='sogecommerce'}</label>
        <div class="margin-form">
          <select id="SOGECOMMERCE_SIGN_ALGO" name="SOGECOMMERCE_SIGN_ALGO">
            <option value="SHA-1"{if $SOGECOMMERCE_SIGN_ALGO === 'SHA-1'} selected="selected"{/if}>SHA-1</option>
            <option value="SHA-256"{if $SOGECOMMERCE_SIGN_ALGO === 'SHA-256'} selected="selected"{/if}>HMAC-SHA-256</option>
          </select>
          <p>
            {l s='Algorithm used to compute the payment form signature. Selected algorithm must be the same as one configured in your store Back Office.' mod='sogecommerce'}<br />
            {if !$sogecommerce_plugin_features['shatwo']}
              <b>{l s='The HMAC-SHA-256 algorithm should not be activated if it is not yet available in your store Back Office, the feature will be available soon.' mod='sogecommerce'}</b>
            {/if}
          </p>
        </div>

        <label>{l s='Instant Payment Notification URL' mod='sogecommerce'}</label>
        <div class="margin-form">
          <span style="font-weight: bold;">{$SOGECOMMERCE_NOTIFY_URL|escape:'html':'UTF-8'}</span><br />
          <p>
            <img src="{$smarty.const._MODULE_DIR_|escape:'html':'UTF-8'}sogecommerce/views/img/warn.png">
            <span style="color: red; display: inline-block;">
              {l s='URL to copy into your bank Back Office > Settings > Notification rules.' mod='sogecommerce'}<br />
              {l s='In multistore mode, notification URL is the same for all the stores.' mod='sogecommerce'}
            </span>
          </p>
        </div>

        <label for="SOGECOMMERCE_PLATFORM_URL">{l s='Payment page URL' mod='sogecommerce'}</label>
        <div class="margin-form">
          <input type="text" id="SOGECOMMERCE_PLATFORM_URL" name="SOGECOMMERCE_PLATFORM_URL" value="{$SOGECOMMERCE_PLATFORM_URL|escape:'html':'UTF-8'}" style="width: 470px;">
          <p>{l s='Link to the payment page.' mod='sogecommerce'}</p>
        </div>
      </fieldset>
      <div class="clear">&nbsp;</div>

      <fieldset>
        <legend onclick="javascript: sogecommerceAdditionalOptionsToggle(this);" style="cursor: pointer;">
          <span class="ui-icon ui-icon-triangle-1-e" style="display: inline-block; vertical-align: middle;"></span>
          {l s='REST API KEYS' mod='sogecommerce'}
        </legend>

        <p style="font-size: .85em; color: #7F7F7F;">
         {l s='REST API keys are available in your store Back Office (menu: Settings > Shops > REST API keys).' mod='sogecommerce'}
        </p>

        <section style="display: none; padding-top: 15px;">
          <p style="font-size: .85em; color: #7F7F7F;">
           {l s='Configure this section if you are using order operations from Prestashop Back Office or if you are using embedded payment fields modes.' mod='sogecommerce'}
          </p>
          <label for="SOGECOMMERCE_PRIVKEY_TEST">{l s='Test password' mod='sogecommerce'}</label>
          <div class="margin-form">
            <input type="password" id="SOGECOMMERCE_PRIVKEY_TEST" name="SOGECOMMERCE_PRIVKEY_TEST" value="{$SOGECOMMERCE_PRIVKEY_TEST|escape:'html':'UTF-8'}" style="width: 470px;" autocomplete="off" />
          </div>
          <p></p>

          <label for="SOGECOMMERCE_PRIVKEY_PROD">{l s='Production password' mod='sogecommerce'}</label>
          <div style="border-bottom: 5px;" class="margin-form">
            <input type="password" id="SOGECOMMERCE_PRIVKEY_PROD" name="SOGECOMMERCE_PRIVKEY_PROD" value="{$SOGECOMMERCE_PRIVKEY_PROD|escape:'html':'UTF-8'}" style="width: 470px;" autocomplete="off">
          </div>
          <p></p>

          <label for="SOGECOMMERCE_REST_SERVER_URL">{l s='REST API server URL' mod='sogecommerce'}</label>
          <div class="margin-form">
            <input type="text" id="SOGECOMMERCE_REST_SERVER_URL" name="SOGECOMMERCE_REST_SERVER_URL" value="{$SOGECOMMERCE_REST_SERVER_URL|escape:'html':'UTF-8'}" style="width: 470px;" autocomplete="off">
          </div>
          <p></p>

          <p style="font-size: .85em; color: #7F7F7F;">
           {l s='Configure this section only if you are using embedded payment fields modes.' mod='sogecommerce'}
          </p>
          <p></p>

          <label for="SOGECOMMERCE_PUBKEY_TEST">{l s='Public test key' mod='sogecommerce'}</label>
          <div class="margin-form">
            <input type="text" id="SOGECOMMERCE_PUBKEY_TEST" name="SOGECOMMERCE_PUBKEY_TEST" value="{$SOGECOMMERCE_PUBKEY_TEST|escape:'html':'UTF-8'}" style="width: 470px;" autocomplete="off">
          </div>
          <p></p>

          <label for="SOGECOMMERCE_PUBKEY_PROD">{l s='Public production key' mod='sogecommerce'}</label>
          <div class="margin-form">
            <input type="text" id="SOGECOMMERCE_PUBKEY_PROD" name="SOGECOMMERCE_PUBKEY_PROD" value="{$SOGECOMMERCE_PUBKEY_PROD|escape:'html':'UTF-8'}" style="width: 470px;" autocomplete="off">
          </div>
          <p></p>

          <label for="SOGECOMMERCE_RETKEY_TEST">{l s='HMAC-SHA-256 test key' mod='sogecommerce'}</label>
          <div class="margin-form">
            <input type="password" id="SOGECOMMERCE_RETKEY_TEST" name="SOGECOMMERCE_RETKEY_TEST" value="{$SOGECOMMERCE_RETKEY_TEST|escape:'html':'UTF-8'}" style="width: 470px;" autocomplete="off">
          </div>
          <p></p>

          <label for="SOGECOMMERCE_RETKEY_PROD">{l s='HMAC-SHA-256 production key' mod='sogecommerce'}</label>
          <div class="margin-form">
            <input type="password" id="SOGECOMMERCE_RETKEY_PROD" name="SOGECOMMERCE_RETKEY_PROD" value="{$SOGECOMMERCE_RETKEY_PROD|escape:'html':'UTF-8'}" style="width: 470px;" autocomplete="off">
          </div>
          <p></p>

          <label>{l s='API REST Notification URL' mod='sogecommerce'}</label>
          <div class="margin-form">
            {$SOGECOMMERCE_REST_NOTIFY_URL|escape:'html':'UTF-8'}<br />
            <p>
              <img src="{$smarty.const._MODULE_DIR_|escape:'html':'UTF-8'}sogecommerce/views/img/warn.png">
              <span style="color: red; display: inline-block;">
                {l s='URL to copy into your bank Back Office > Settings > Notification rules.' mod='sogecommerce'}<br />
                {l s='In multistore mode, notification URL is the same for all the stores.' mod='sogecommerce'}
              </span>
            </p>
          </div>

          <label for="SOGECOMMERCE_REST_JS_CLIENT_URL">{l s='JavaScript client URL' mod='sogecommerce'}</label>
          <div class="margin-form">
            <input type="text" id="SOGECOMMERCE_REST_JS_CLIENT_URL" name="SOGECOMMERCE_REST_JS_CLIENT_URL" value="{$SOGECOMMERCE_REST_JS_CLIENT_URL|escape:'html':'UTF-8'}" style="width: 470px;" autocomplete="off">
          </div>
        </section>
      </fieldset>
      <div class="clear">&nbsp;</div>

      <fieldset>
        <legend>{l s='PAYMENT PAGE' mod='sogecommerce'}</legend>

        <label for="SOGECOMMERCE_DEFAULT_LANGUAGE">{l s='Default language' mod='sogecommerce'}</label>
        <div class="margin-form">
          <select id="SOGECOMMERCE_DEFAULT_LANGUAGE" name="SOGECOMMERCE_DEFAULT_LANGUAGE">
            {foreach from=$sogecommerce_language_options key="key" item="option"}
              <option value="{$key|escape:'html':'UTF-8'}"{if $SOGECOMMERCE_DEFAULT_LANGUAGE === $key} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
            {/foreach}
          </select>
          <p>{l s='Default language on the payment page.' mod='sogecommerce'}</p>
        </div>

        <label for="SOGECOMMERCE_AVAILABLE_LANGUAGES">{l s='Available languages' mod='sogecommerce'}</label>
        <div class="margin-form">
          <select id="SOGECOMMERCE_AVAILABLE_LANGUAGES" name="SOGECOMMERCE_AVAILABLE_LANGUAGES[]" multiple="multiple" size="8">
            {foreach from=$sogecommerce_language_options key="key" item="option"}
              <option value="{$key|escape:'html':'UTF-8'}"{if in_array($key, $SOGECOMMERCE_AVAILABLE_LANGUAGES)} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
            {/foreach}
          </select>
          <p>{l s='Languages available on the payment page. If you do not select any, all the supported languages will be available.' mod='sogecommerce'}</p>
        </div>

        <label for="SOGECOMMERCE_DELAY">{l s='Capture delay' mod='sogecommerce'}</label>
        <div class="margin-form">
          <input type="text" id="SOGECOMMERCE_DELAY" name="SOGECOMMERCE_DELAY" value="{$SOGECOMMERCE_DELAY|escape:'html':'UTF-8'}">
          <p>{l s='The number of days before the bank capture (adjustable in your store Back Office).' mod='sogecommerce'}</p>
        </div>

        <label for="SOGECOMMERCE_VALIDATION_MODE">{l s='Validation mode' mod='sogecommerce'}</label>
        <div class="margin-form">
          <select id="SOGECOMMERCE_VALIDATION_MODE" name="SOGECOMMERCE_VALIDATION_MODE">
            {foreach from=$sogecommerce_validation_mode_options key="key" item="option"}
              <option value="{$key|escape:'html':'UTF-8'}"{if $SOGECOMMERCE_VALIDATION_MODE === (string)$key} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
            {/foreach}
          </select>
          <p>{l s='If manual is selected, you will have to confirm payments manually in your bank Back Office.' mod='sogecommerce'}</p>
        </div>
      </fieldset>
      <div class="clear">&nbsp;</div>

      <fieldset>
        <legend>{l s='PAYMENT PAGE CUSTOMIZE' mod='sogecommerce'}</legend>

        <label>{l s='Theme configuration' mod='sogecommerce'}</label>
        <div class="margin-form">
          {include file="./input_text_lang.tpl"
              languages=$prestashop_languages
              current_lang=$prestashop_lang
              input_name="SOGECOMMERCE_THEME_CONFIG"
              input_value=$SOGECOMMERCE_THEME_CONFIG
              style="width: 470px;"
           }
          <p>{l s='The theme configuration to customize the payment page.' mod='sogecommerce'}</p>
        </div>

        <label for="SOGECOMMERCE_SHOP_NAME">{l s='Shop name' mod='sogecommerce'}</label>
        <div class="margin-form">
          <input type="text" id="SOGECOMMERCE_SHOP_NAME" name="SOGECOMMERCE_SHOP_NAME" value="{$SOGECOMMERCE_SHOP_NAME|escape:'html':'UTF-8'}">
          <p>{l s='Shop name to display on the payment page. Leave blank to use gateway configuration.' mod='sogecommerce'}</p>
        </div>

        <label for="SOGECOMMERCE_SHOP_URL">{l s='Shop URL' mod='sogecommerce'}</label>
        <div class="margin-form">
          <input type="text" id="SOGECOMMERCE_SHOP_URL" name="SOGECOMMERCE_SHOP_URL" value="{$SOGECOMMERCE_SHOP_URL|escape:'html':'UTF-8'}" style="width: 470px;">
          <p>{l s='Shop URL to display on the payment page. Leave blank to use gateway configuration.' mod='sogecommerce'}</p>
        </div>
      </fieldset>
      <div class="clear">&nbsp;</div>

      <fieldset>
        <legend>{l s='CUSTOM 3DS' mod='sogecommerce'}</legend>

        <label for="SOGECOMMERCE_3DS_MIN_AMOUNT">{l s='Manage 3DS by customer group' mod='sogecommerce'}</label>
        <div class="margin-form">
          {include file="./table_amount_group.tpl"
            groups=$prestashop_groups
            input_name="SOGECOMMERCE_3DS_MIN_AMOUNT"
            input_value=$SOGECOMMERCE_3DS_MIN_AMOUNT
            min_only=true
          }
          <p>{l s='Amount by customer group below which customer could be exempt from strong authentication. Needs subscription to « Selective 3DS1 » or « Frictionless 3DS2 » options. For more information, refer to the module documentation.' mod='sogecommerce'}</p>
        </div>
      </fieldset>
      <div class="clear">&nbsp;</div>

      <fieldset>
        <legend>{l s='RETURN TO SHOP' mod='sogecommerce'}</legend>

        <label for="SOGECOMMERCE_REDIRECT_ENABLED">{l s='Automatic redirection' mod='sogecommerce'}</label>
        <div class="margin-form">
          <select id="SOGECOMMERCE_REDIRECT_ENABLED" name="SOGECOMMERCE_REDIRECT_ENABLED" onchange="javascript: sogecommerceRedirectChanged();">
            {foreach from=$sogecommerce_enable_disable_options key="key" item="option"}
              <option value="{$key|escape:'html':'UTF-8'}"{if $SOGECOMMERCE_REDIRECT_ENABLED === $key} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
            {/foreach}
          </select>
          <p>{l s='If enabled, the buyer is automatically redirected to your site at the end of the payment.' mod='sogecommerce'}</p>
        </div>

        <section id="sogecommerce_redirect_settings">
          <label for="SOGECOMMERCE_REDIRECT_SUCCESS_T">{l s='Redirection timeout on success' mod='sogecommerce'}</label>
          <div class="margin-form">
            <input type="text" id="SOGECOMMERCE_REDIRECT_SUCCESS_T" name="SOGECOMMERCE_REDIRECT_SUCCESS_T" value="{$SOGECOMMERCE_REDIRECT_SUCCESS_T|escape:'html':'UTF-8'}">
            <p>{l s='Time in seconds (0-300) before the buyer is automatically redirected to your website after a successful payment.' mod='sogecommerce'}</p>
          </div>

          <label>{l s='Redirection message on success' mod='sogecommerce'}</label>
          <div class="margin-form">
            {include file="./input_text_lang.tpl"
              languages=$prestashop_languages
              current_lang=$prestashop_lang
              input_name="SOGECOMMERCE_REDIRECT_SUCCESS_M"
              input_value=$SOGECOMMERCE_REDIRECT_SUCCESS_M
              style="width: 470px;"
            }
            <p>{l s='Message displayed on the payment page prior to redirection after a successful payment.' mod='sogecommerce'}</p>
          </div>

          <label for="SOGECOMMERCE_REDIRECT_ERROR_T">{l s='Redirection timeout on failure' mod='sogecommerce'}</label>
          <div class="margin-form">
            <input type="text" id="SOGECOMMERCE_REDIRECT_ERROR_T" name="SOGECOMMERCE_REDIRECT_ERROR_T" value="{$SOGECOMMERCE_REDIRECT_ERROR_T|escape:'html':'UTF-8'}">
            <p>{l s='Time in seconds (0-300) before the buyer is automatically redirected to your website after a declined payment.' mod='sogecommerce'}</p>
          </div>

          <label>{l s='Redirection message on failure' mod='sogecommerce'}</label>
          <div class="margin-form">
            {include file="./input_text_lang.tpl"
              languages=$prestashop_languages
              current_lang=$prestashop_lang
              input_name="SOGECOMMERCE_REDIRECT_ERROR_M"
              input_value=$SOGECOMMERCE_REDIRECT_ERROR_M
              style="width: 470px;"
            }
            <p>{l s='Message displayed on the payment page prior to redirection after a declined payment.' mod='sogecommerce'}</p>
          </div>
        </section>

        <script type="text/javascript">
          sogecommerceRedirectChanged();
        </script>

        <label for="SOGECOMMERCE_RETURN_MODE">{l s='Return mode' mod='sogecommerce'}</label>
        <div class="margin-form">
          <select id="SOGECOMMERCE_RETURN_MODE" name="SOGECOMMERCE_RETURN_MODE">
            <option value="GET"{if $SOGECOMMERCE_RETURN_MODE === 'GET'} selected="selected"{/if}>GET</option>
            <option value="POST"{if $SOGECOMMERCE_RETURN_MODE === 'POST'} selected="selected"{/if}>POST</option>
          </select>
          <p>{l s='Method that will be used for transmitting the payment result from the payment page to your shop.' mod='sogecommerce'}</p>
        </div>

        <label for="SOGECOMMERCE_FAILURE_MANAGEMENT">{l s='Payment failed management' mod='sogecommerce'}</label>
        <div class="margin-form">
          <select id="SOGECOMMERCE_FAILURE_MANAGEMENT" name="SOGECOMMERCE_FAILURE_MANAGEMENT">
            {foreach from=$sogecommerce_failure_management_options key="key" item="option"}
              <option value="{$key|escape:'html':'UTF-8'}"{if $SOGECOMMERCE_FAILURE_MANAGEMENT === $key} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
            {/foreach}
          </select>
          <p>{l s='How to manage the buyer return to shop when the payment is failed.' mod='sogecommerce'}</p>
        </div>

        <label for="SOGECOMMERCE_CART_MANAGEMENT">{l s='Cart management' mod='sogecommerce'}</label>
        <div class="margin-form">
          <select id="SOGECOMMERCE_CART_MANAGEMENT" name="SOGECOMMERCE_CART_MANAGEMENT">
            {foreach from=$sogecommerce_cart_management_options key="key" item="option"}
              <option value="{$key|escape:'html':'UTF-8'}"{if $SOGECOMMERCE_CART_MANAGEMENT === $key} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
            {/foreach}
          </select>
          <p>{l s='We recommend to choose the option « Empty cart » in order to avoid amount inconsistencies. In case of return back from the browser button the cart will be emptied. However in case of cancelled or refused payment, the cart will be recovered. If you do not want to have this behavior but the default PrestaShop one which is to keep the cart, choose the second option.' mod='sogecommerce'}</p>
        </div>

        <label for="SOGECOMMERCE_ENABLE_CUST_MSG">{l s='Customer service messages' mod='sogecommerce'}</label>
        <div class="margin-form">
          <select id="SOGECOMMERCE_ENABLE_CUST_MSG" name="SOGECOMMERCE_ENABLE_CUST_MSG">
            {foreach from=$sogecommerce_enable_disable_options key="key" item="option"}
              <option value="{$key|escape:'html':'UTF-8'}"{if $SOGECOMMERCE_ENABLE_CUST_MSG === $key} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
            {/foreach}
          </select>
          <p>{l s='Enable / disable the customer service messages generated at the end of the payment (concerns PrestaShop 1.7.1.2 and above).' mod='sogecommerce'}</p>
         </div>
      </fieldset>
      <div class="clear">&nbsp;</div>

      <fieldset>
        <legend onclick="javascript: sogecommerceAdditionalOptionsToggle(this);" style="cursor: pointer;">
          <span class="ui-icon ui-icon-triangle-1-e" style="display: inline-block; vertical-align: middle;"></span>
          {l s='ADDITIONAL OPTIONS' mod='sogecommerce'}
        </legend>
        <p style="font-size: .85em; color: #7F7F7F;">{l s='Configure this section if you use advanced risk assessment module or if you have an Oney contract.' mod='sogecommerce'}</p>

        <section style="display: none; padding-top: 15px;">
          <label for="SOGECOMMERCE_SEND_CART_DETAIL">{l s='Send shopping cart details' mod='sogecommerce'}</label>
          <div class="margin-form">
            <select id="SOGECOMMERCE_SEND_CART_DETAIL" name="SOGECOMMERCE_SEND_CART_DETAIL">
              {foreach from=$sogecommerce_enable_disable_options key="key" item="option"}
                <option value="{$key|escape:'html':'UTF-8'}"{if $SOGECOMMERCE_SEND_CART_DETAIL === $key} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
              {/foreach}
            </select>
            <p>{l s='If you disable this option, the shopping cart details will not be sent to the gateway. Attention, in some cases, this option has to be enabled. For more information, refer to the module documentation.' mod='sogecommerce'}</p>
          </div>

          <label for="SOGECOMMERCE_COMMON_CATEGORY">{l s='Category mapping' mod='sogecommerce'}</label>
          <div class="margin-form">
            <select id="SOGECOMMERCE_COMMON_CATEGORY" name="SOGECOMMERCE_COMMON_CATEGORY" style="width: 220px;" onchange="javascript: sogecommerceCategoryTableVisibility();">
              <option value="CUSTOM_MAPPING"{if $SOGECOMMERCE_COMMON_CATEGORY === 'CUSTOM_MAPPING'} selected="selected"{/if}>{l s='(Use category mapping below)' mod='sogecommerce'}</option>
              {foreach from=$sogecommerce_category_options key="key" item="option"}
                <option value="{$key|escape:'html':'UTF-8'}"{if $SOGECOMMERCE_COMMON_CATEGORY === $key} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
              {/foreach}
            </select>
            <p>{l s='Use the same category for all products.' mod='sogecommerce'}</p>

            <table cellpadding="10" cellspacing="0" class="table sogecommerce_category_mapping" style="margin-top: 15px;{if $SOGECOMMERCE_COMMON_CATEGORY != 'CUSTOM_MAPPING'} display: none;{/if}">
            <thead>
              <tr>
                <th>{l s='Product category' mod='sogecommerce'}</th>
                <th>{l s='Bank product category' mod='sogecommerce'}</th>
              </tr>
            </thead>
            <tbody>
              {foreach from=$prestashop_categories item="category"}
                {if $category.id_parent === 0}
                  {continue}
                {/if}

                {assign var="category_id" value=$category.id_category}

                {if isset($SOGECOMMERCE_CATEGORY_MAPPING[$category_id])}
                  {assign var="exists" value=true}
                {else}
                  {assign var="exists" value=false}
                {/if}

                {if $exists}
                  {assign var="sogecommerce_category" value=$SOGECOMMERCE_CATEGORY_MAPPING[$category_id]}
                {else}
                  {assign var="sogecommerce_category" value="FOOD_AND_GROCERY"}
                {/if}

                <tr id="sogecommerce_category_mapping_{$category_id|escape:'html':'UTF-8'}">
                  <td>{$category.name|escape:'html':'UTF-8'}{if $exists === false}<span style="color: red;">*</span>{/if}</td>
                  <td>
                    <select id="SOGECOMMERCE_CATEGORY_MAPPING_{$category_id|escape:'html':'UTF-8'}" name="SOGECOMMERCE_CATEGORY_MAPPING[{$category_id|escape:'html':'UTF-8'}]"
                        style="width: 220px;"{if $SOGECOMMERCE_COMMON_CATEGORY != 'CUSTOM_MAPPING'} disabled="disabled"{/if}>
                      {foreach from=$sogecommerce_category_options key="key" item="option"}
                        <option value="{$key|escape:'html':'UTF-8'}"{if $sogecommerce_category === $key} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
                      {/foreach}
                    </select>
                  </td>
                </tr>
              {/foreach}
            </tbody>
            </table>
            <p class="sogecommerce_category_mapping"{if $SOGECOMMERCE_COMMON_CATEGORY != 'CUSTOM_MAPPING'} style="display: none;"{/if}>{l s='Match each product category with a bank product category.' mod='sogecommerce'} <b>{l s='Entries marked with * are newly added and must be configured.' mod='sogecommerce'}</b></p>
          </div>

          <label for="SOGECOMMERCE_SEND_SHIP_DATA">{l s='Always send advanced shipping data' mod='sogecommerce'}</label>
          <div class="margin-form">
            <select id="SOGECOMMERCE_SEND_SHIP_DATA" name="SOGECOMMERCE_SEND_SHIP_DATA">
              {foreach from=$sogecommerce_yes_no_options key="key" item="option"}
                <option value="{$key|escape:'html':'UTF-8'}"{if $SOGECOMMERCE_SEND_SHIP_DATA === $key} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
              {/foreach}
            </select>
            <p>{l s='Select « Yes » to send advanced shipping data for all payments (carrier name, delivery type and delivery rapidity).' mod='sogecommerce'}</p>
          </div>

          <label>{l s='Shipping options' mod='sogecommerce'}</label>
          <div class="margin-form">
            <table class="table" cellpadding="10" cellspacing="0">
            <thead>
              <tr>
                <th>{l s='Method title' mod='sogecommerce'}</th>
                <th>{l s='Type' mod='sogecommerce'}</th>
                <th>{l s='Rapidity' mod='sogecommerce'}</th>
                <th>{l s='Delay' mod='sogecommerce'}</th>
                <th style="width: 270px;" colspan="3">{l s='Address' mod='sogecommerce'}</th>
              </tr>
            </thead>
            <tbody>
              {foreach from=$prestashop_carriers item="carrier"}
                {assign var="carrier_id" value=$carrier.id_carrier}

                {if isset($SOGECOMMERCE_ONEY_SHIP_OPTIONS[$carrier_id])}
                  {assign var="exists" value=true}
                {else}
                  {assign var="exists" value=false}
                {/if}

                {if $exists}
                  {assign var="ship_option" value=$SOGECOMMERCE_ONEY_SHIP_OPTIONS[$carrier_id]}
                {/if}

                <tr>
                  <td>{$carrier.name|escape:'html':'UTF-8'}{if $exists === false}<span style="color: red;">*</span>{/if}</td>
                  <td>
                    <select id="SOGECOMMERCE_ONEY_SHIP_OPTIONS_{$carrier_id|escape:'html':'UTF-8'}_type" name="SOGECOMMERCE_ONEY_SHIP_OPTIONS[{$carrier_id|escape:'html':'UTF-8'}][type]" onchange="javascript: sogecommerceDeliveryTypeChanged({$carrier_id|escape:'html':'UTF-8'});" style="width: 150px;">
                      {foreach from=$sogecommerce_delivery_type_options key="key" item="option"}
                        <option value="{$key|escape:'html':'UTF-8'}"{if (isset($ship_option) && $ship_option.type === $key) || ('PACKAGE_DELIVERY_COMPANY' === $key)} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
                      {/foreach}
                    </select>
                  </td>
                  <td>
                    <select id="SOGECOMMERCE_ONEY_SHIP_OPTIONS_{$carrier_id|escape:'html':'UTF-8'}_speed" name="SOGECOMMERCE_ONEY_SHIP_OPTIONS[{$carrier_id|escape:'html':'UTF-8'}][speed]" onchange="javascript: sogecommerceDeliverySpeedChanged({$carrier_id|escape:'html':'UTF-8'});">
                      {foreach from=$sogecommerce_delivery_speed_options key="key" item="option"}
                        <option value="{$key|escape:'html':'UTF-8'}"{if (isset($ship_option) && $ship_option.speed === $key) || ('STANDARD' === $key)} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
                      {/foreach}
                    </select>
                  </td>
                  <td>
                    <select
                        id="SOGECOMMERCE_ONEY_SHIP_OPTIONS_{$carrier_id|escape:'html':'UTF-8'}_delay"
                        name="SOGECOMMERCE_ONEY_SHIP_OPTIONS[{$carrier_id|escape:'html':'UTF-8'}][delay]"
                        style="{if !isset($ship_option) || ($ship_option.speed != 'PRIORITY')} display: none;{/if}">
                      {foreach from=$sogecommerce_delivery_delay_options key="key" item="option"}
                        <option value="{$key|escape:'html':'UTF-8'}"{if (isset($ship_option) && isset($ship_option.delay) && ($ship_option.delay === $key)) || 'INFERIOR_EQUALS' === $key} selected="selected"{/if}>{$option|escape:'quotes':'UTF-8'}</option>
                      {/foreach}
                    </select>
                  </td>
                  <td>
                    <input
                        id="SOGECOMMERCE_ONEY_SHIP_OPTIONS_{$carrier_id|escape:'html':'UTF-8'}_address"
                        name="SOGECOMMERCE_ONEY_SHIP_OPTIONS[{$carrier_id|escape:'html':'UTF-8'}][address]"
                        placeholder="{l s='Address' mod='sogecommerce'}"
                        value="{if isset($ship_option)}{$ship_option.address|escape:'html':'UTF-8'}{/if}"
                        style="width: 160px;{if !isset($ship_option) || $ship_option.type != 'RECLAIM_IN_SHOP'} display: none;{/if}"
                        type="text">
                  </td>
                  <td>
                    <input
                        id="SOGECOMMERCE_ONEY_SHIP_OPTIONS_{$carrier_id|escape:'html':'UTF-8'}_zip"
                        name="SOGECOMMERCE_ONEY_SHIP_OPTIONS[{$carrier_id|escape:'html':'UTF-8'}][zip]"
                        placeholder="{l s='Zip code' mod='sogecommerce'}"
                        value="{if isset($ship_option)}{$ship_option.zip|escape:'html':'UTF-8'}{/if}"
                        style="width: 50px;{if !isset($ship_option) || $ship_option.type != 'RECLAIM_IN_SHOP'} display: none;{/if}"
                        type="text">
                  </td>
                  <td>
                    <input
                        id="SOGECOMMERCE_ONEY_SHIP_OPTIONS_{$carrier_id|escape:'html':'UTF-8'}_city"
                        name="SOGECOMMERCE_ONEY_SHIP_OPTIONS[{$carrier_id|escape:'html':'UTF-8'}][city]"
                        placeholder="{l s='City' mod='sogecommerce'}"
                        value="{if isset($ship_option)}{$ship_option.city|escape:'html':'UTF-8'}{/if}"
                        style="width: 160px;{if !isset($ship_option) || $ship_option.type != 'RECLAIM_IN_SHOP'} display: none;{/if}"
                        type="text">
                  </td>
                </tr>
              {/foreach}
            </tbody>
            </table>
            <p>
              {l s='Define the information about all shipping methods.' mod='sogecommerce'}<br />
              <b>{l s='Type' mod='sogecommerce'} : </b>{l s='The delivery type of shipping method.' mod='sogecommerce'}<br />
              <b>{l s='Rapidity' mod='sogecommerce'} : </b>{l s='Select the delivery rapidity.' mod='sogecommerce'}<br />
              <b>{l s='Delay' mod='sogecommerce'} : </b>{l s='Select the delivery delay if speed is « Priority ».' mod='sogecommerce'}<br />
              <b>{l s='Address' mod='sogecommerce'} : </b>{l s='Enter address if it is a reclaim in shop.' mod='sogecommerce'}<br />
              <b>{l s='Entries marked with * are newly added and must be configured.' mod='sogecommerce'}</b>
            </p>
          </div>
          {if $sogecommerce_plugin_features['brazil']}
            <label for="SOGECOMMERCE_DOCUMENT">{l s='CPF/CNPJ field' mod='sogecommerce'}</label>
            <div class="margin-form">
              <select id="SOGECOMMERCE_DOCUMENT" name="SOGECOMMERCE_DOCUMENT">
                {foreach from=$sogecommerce_extra_options key="key" item="option"}
                  <option value="{$key|escape:'html':'UTF-8'}"{if $SOGECOMMERCE_DOCUMENT === $key} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
                {/foreach}
              </select>
              <p>{l s='Custom field where CPF/CNPJ is saved on shop.' mod='sogecommerce'}</p>
            </div>
            <label for="SOGECOMMERCE_NUMBER">{l s='Address number field' mod='sogecommerce'}</label>
            <div class="margin-form">
              <select id="SOGECOMMERCE_NUMBER" name="SOGECOMMERCE_NUMBER">
                {foreach from=$sogecommerce_extra_options key="key" item="option"}
                  <option value="{$key|escape:'html':'UTF-8'}"{if $SOGECOMMERCE_NUMBER === $key} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
                {/foreach}
              </select>
              <p>{l s='Custom field where address number is saved on shop.' mod='sogecommerce'}</p>
            </div>
            <label for="SOGECOMMERCE_NEIGHBORHOOD">{l s='Neighborhood field' mod='sogecommerce'}</label>
            <div class="margin-form">
              <select id="SOGECOMMERCE_NEIGHBORHOOD" name="SOGECOMMERCE_NEIGHBORHOOD">
                {foreach from=$sogecommerce_extra_options key="key" item="option"}
                  <option value="{$key|escape:'html':'UTF-8'}"{if $SOGECOMMERCE_NEIGHBORHOOD === $key} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
                {/foreach}
              </select>
              <p>{l s='Custom field where address neighborhood is saved on shop.' mod='sogecommerce'}</p>
            </div>
          {/if}
        </section>
      </fieldset>
      <div class="clear">&nbsp;</div>
    </div>

    <h4 style="font-weight: bold; margin-bottom: 0; overflow: hidden; line-height: unset !important;">
      <a href="#">{l s='STANDARD PAYMENT' mod='sogecommerce'}</a>
    </h4>
    <div>
      <fieldset>
        <legend>{l s='MODULE OPTIONS' mod='sogecommerce'}</legend>

       <label for="SOGECOMMERCE_STD_ENABLED">{l s='Activation' mod='sogecommerce'}</label>
        <div class="margin-form">
          <select id="SOGECOMMERCE_STD_ENABLED" name="SOGECOMMERCE_STD_ENABLED">
            {foreach from=$sogecommerce_enable_disable_options key="key" item="option"}
              <option value="{$key|escape:'html':'UTF-8'}"{if $SOGECOMMERCE_STD_ENABLED === $key} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
            {/foreach}
          </select>
          <p>{l s='Enables / disables this payment method.' mod='sogecommerce'}</p>
        </div>

        <label>{l s='Payment method title' mod='sogecommerce'}</label>
        <div class="margin-form">
          {include file="./input_text_lang.tpl"
            languages=$prestashop_languages
            current_lang=$prestashop_lang
            input_name="SOGECOMMERCE_STD_TITLE"
            input_value=$SOGECOMMERCE_STD_TITLE
            style="width: 330px;"
          }
          <p>{l s='Method title to display on payment means page.' mod='sogecommerce'}</p>
        </div>
      </fieldset>
      <div class="clear">&nbsp;</div>

      <fieldset>
        <legend>{l s='RESTRICTIONS' mod='sogecommerce'}</legend>

        <label for="SOGECOMMERCE_STD_COUNTRY">{l s='Restrict to some countries' mod='sogecommerce'}</label>
        <div class="margin-form">
          <select id="SOGECOMMERCE_STD_COUNTRY" name="SOGECOMMERCE_STD_COUNTRY" onchange="javascript: sogecommerceCountriesRestrictMenuDisplay('SOGECOMMERCE_STD_COUNTRY')">
            {foreach from=$sogecommerce_countries_options key="key" item="option"}
              <option value="{$key|escape:'html':'UTF-8'}"{if $SOGECOMMERCE_STD_COUNTRY === (string)$key} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
            {/foreach}
          </select>
          <p>{l s='Buyer\'s billing countries in which this payment method is available.' mod='sogecommerce'}</p>
        </div>

        <div id="SOGECOMMERCE_STD_COUNTRY_MENU" {if $SOGECOMMERCE_STD_COUNTRY === '1'} style="display: none;"{/if}>
          <label for="SOGECOMMERCE_STD_COUNTRY_LST">{l s='Authorized countries' mod='sogecommerce'}</label>
          <div class="margin-form">
            <select id="SOGECOMMERCE_STD_COUNTRY_LST" name="SOGECOMMERCE_STD_COUNTRY_LST[]" multiple="multiple" size="7">
              {foreach from=$sogecommerce_countries_list['ps_countries'] key="key" item="option"}
                <option value="{$key|escape:'html':'UTF-8'}"{if in_array($key, $SOGECOMMERCE_STD_COUNTRY_LST)} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
              {/foreach}
            </select>
          </div>
        </div>

        <label>{l s='Customer group amount restriction' mod='sogecommerce'}</label>
        <div class="margin-form">
          {include file="./table_amount_group.tpl"
            groups=$prestashop_groups
            input_name="SOGECOMMERCE_STD_AMOUNTS"
            input_value=$SOGECOMMERCE_STD_AMOUNTS
          }
          <p>{l s='Define amount restriction for each customer group.' mod='sogecommerce'}</p>
        </div>
      </fieldset>
      <div class="clear">&nbsp;</div>

      <fieldset>
        <legend>{l s='PAYMENT PAGE' mod='sogecommerce'}</legend>

        <label for="SOGECOMMERCE_STD_DELAY">{l s='Capture delay' mod='sogecommerce'}</label>
        <div class="margin-form">
          <input id="SOGECOMMERCE_STD_DELAY" name="SOGECOMMERCE_STD_DELAY" value="{$SOGECOMMERCE_STD_DELAY|escape:'html':'UTF-8'}" type="text">
          <p>{l s='The number of days before the bank capture. Enter value only if different from « Base settings ».' mod='sogecommerce'}</p>
        </div>

        <label for="SOGECOMMERCE_STD_VALIDATION">{l s='Validation mode' mod='sogecommerce'}</label>
        <div class="margin-form">
          <select id="SOGECOMMERCE_STD_VALIDATION" name="SOGECOMMERCE_STD_VALIDATION">
            <option value="-1"{if $SOGECOMMERCE_STD_VALIDATION === '-1'} selected="selected"{/if}>{l s='Base settings configuration' mod='sogecommerce'}</option>
            {foreach from=$sogecommerce_validation_mode_options key="key" item="option"}
              <option value="{$key|escape:'html':'UTF-8'}"{if $SOGECOMMERCE_STD_VALIDATION === (string)$key} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
            {/foreach}
          </select>
          <p>{l s='If manual is selected, you will have to confirm payments manually in your bank Back Office.' mod='sogecommerce'}</p>
        </div>

        <label for="SOGECOMMERCE_STD_PAYMENT_CARDS">{l s='Card Types' mod='sogecommerce'}</label>
        <div class="margin-form">
          <select id="SOGECOMMERCE_STD_PAYMENT_CARDS" name="SOGECOMMERCE_STD_PAYMENT_CARDS[]" multiple="multiple" size="7">
            {foreach from=$sogecommerce_payment_cards_options key="key" item="option"}
              <option value="{$key|escape:'html':'UTF-8'}"{if in_array($key, $SOGECOMMERCE_STD_PAYMENT_CARDS)} selected="selected"{/if}>{if $key !== ""} {$key|escape:'html':'UTF-8'} - {/if}{$option|escape:'html':'UTF-8'}</option>
            {/foreach}
          </select>
          <p>{l s='The card type(s) that can be used for the payment. Select none to use gateway configuration.' mod='sogecommerce'}</p>
        </div>

        </fieldset>
        <div class="clear">&nbsp;</div>

      <fieldset>
        <legend>{l s='ADVANCED OPTIONS' mod='sogecommerce'}</legend>

        <label for="SOGECOMMERCE_STD_CARD_DATA_MODE">{l s='Payment data entry mode' mod='sogecommerce'}</label>
        <div class="margin-form">
          <select id="SOGECOMMERCE_STD_CARD_DATA_MODE" name="SOGECOMMERCE_STD_CARD_DATA_MODE" onchange="javascript: sogecommerceCardEntryChanged();">
            {foreach from=$sogecommerce_card_data_mode_options key="key" item="option"}
              <option value="{$key|escape:'html':'UTF-8'}"{if $SOGECOMMERCE_STD_CARD_DATA_MODE === (string)$key} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
            {/foreach}
          </select>
          <input type="hidden" id="SOGECOMMERCE_STD_CARD_DATA_MODE_OLD" name="SOGECOMMERCE_STD_CARD_DATA_MODE_OLD" value="{$SOGECOMMERCE_STD_CARD_DATA_MODE|escape:'html':'UTF-8'}"/>
          <p>{l s='Select how the payment data will be entered. Attention, to use bank data acquisition on the merchant site, you must ensure that you have subscribed to this option with your bank.' mod='sogecommerce'}</p>
        </div>

        <div id="SOGECOMMERCE_REST_SETTINGS" {if $SOGECOMMERCE_STD_CARD_DATA_MODE !== '7' && $SOGECOMMERCE_STD_CARD_DATA_MODE !== '8' && $SOGECOMMERCE_STD_CARD_DATA_MODE !== '9'} style="display: none;"{/if}>
          <p></p>
          <label for="SOGECOMMERCE_STD_REST_POPIN_MODE">{l s='Display in a pop-in' mod='sogecommerce'}</label>
          <div class="margin-form">
            <select id="SOGECOMMERCE_STD_REST_POPIN_MODE" name="SOGECOMMERCE_STD_REST_POPIN_MODE">
                {foreach from=$sogecommerce_yes_no_options key="key" item="option"}
                    <option value="{$key|escape:'html':'UTF-8'}"{if $SOGECOMMERCE_STD_REST_POPIN_MODE === $key} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
                {/foreach}
            </select>
            <p>{l s='This option allows to display the embedded payment fields in a pop-in.' mod='sogecommerce'}</p>
          </div>
          <p></p>
          <p></p>
          <label for="SOGECOMMERCE_STD_REST_THEME">{l s='Theme' mod='sogecommerce'}</label>
          <div class="margin-form">
            <select id="SOGECOMMERCE_STD_REST_THEME" name="SOGECOMMERCE_STD_REST_THEME">
                {foreach from=$sogecommerce_std_rest_theme_options key="key" item="option"}
                    <option value="{$key|escape:'html':'UTF-8'}"{if $SOGECOMMERCE_STD_REST_THEME === $key} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
                {/foreach}
            </select>
            <p>{l s='Select a theme to use to display the embedded payment fields.' mod='sogecommerce'}</p>
          </div>
          <p></p>
          <div id="SOGECOMMERCE_STD_SMARTFORM_CUSTOMIZATION_SETTINGS" {if $SOGECOMMERCE_STD_CARD_DATA_MODE !== '7' && $SOGECOMMERCE_STD_CARD_DATA_MODE !== '8' && $SOGECOMMERCE_STD_CARD_DATA_MODE !== '9'} style="display: none;"{/if}>
            <label for="SOGECOMMERCE_STD_SF_COMPACT_MODE">{l s='Compact mode' mod='sogecommerce'}</label>
            <div class="margin-form">
              <select id="SOGECOMMERCE_STD_SF_COMPACT_MODE" name="SOGECOMMERCE_STD_SF_COMPACT_MODE">
                  {foreach from=$sogecommerce_yes_no_options key="key" item="option"}
                      <option value="{$key|escape:'html':'UTF-8'}"{if $SOGECOMMERCE_STD_SF_COMPACT_MODE === $key} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
                  {/foreach}
              </select>
              <p>{l s='This option allows to display the embedded payment fields in a compact mode.' mod='sogecommerce'}</p>
            </div>
            <p></p>
            <label for="SOGECOMMERCE_STD_SF_THRESHOLD">{l s='Payment means grouping threshold' mod='sogecommerce'}</label>
            <div class="margin-form">
              <input type="text" id="SOGECOMMERCE_STD_SF_THRESHOLD" name="SOGECOMMERCE_STD_SF_THRESHOLD" value="{$SOGECOMMERCE_STD_SF_THRESHOLD|escape:'html':'UTF-8'}" style="width: 150px;" />
              <p>{l s='Number of means of payment from which they will be grouped.' mod='sogecommerce'}</p>
            </div>
            <p></p>
            <label for="SOGECOMMERCE_STD_SF_DISPLAY_TITLE">{l s='Display title' mod='sogecommerce'}</label>
            <div class="margin-form">
              <select id="SOGECOMMERCE_STD_SF_DISPLAY_TITLE" name="SOGECOMMERCE_STD_SF_DISPLAY_TITLE">
                  {foreach from=$sogecommerce_yes_no_options key="key" item="option"}
                      <option value="{$key|escape:'html':'UTF-8'}"{if $SOGECOMMERCE_STD_SF_DISPLAY_TITLE === $key} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
                  {/foreach}
              </select>
              <p>{l s='Display payment method title when it is the only one activated.' mod='sogecommerce'}</p>
            </div>
            <p></p>
          </div>
          <p></p>
          <label for="SOGECOMMERCE_STD_REST_PLACEHLDR">{l s='Custom fields placeholders' mod='sogecommerce'}</label>
          <div class="margin-form">
            <table class="table" cellspacing="0" cellpadding="10">
              <tbody>
                <tr>
                  <td>{l s='Card number' mod='sogecommerce'}</td>
                  <td>
                    {include file="./input_text_lang.tpl"
                      languages=$prestashop_languages
                      current_lang=$prestashop_lang
                      input_name="SOGECOMMERCE_STD_REST_PLACEHLDR[pan]"
                      field_id="SOGECOMMERCE_STD_REST_PLACEHLDR_pan"
                      input_value=$SOGECOMMERCE_STD_REST_PLACEHLDR.pan
                      style="width: 150px;"
                    }
                  </td>
                </tr>

                <tr>
                  <td>{l s='Expiry date' mod='sogecommerce'}</td>
                  <td>
                    {include file="./input_text_lang.tpl"
                      languages=$prestashop_languages
                      current_lang=$prestashop_lang
                      input_name="SOGECOMMERCE_STD_REST_PLACEHLDR[expiry]"
                      field_id="SOGECOMMERCE_STD_REST_PLACEHLDR_expiry"
                      input_value=$SOGECOMMERCE_STD_REST_PLACEHLDR.expiry
                      style="width: 150px;"
                    }
                  </td>
                </tr>

                <tr>
                  <td>{l s='CVV' mod='sogecommerce'}</td>
                  <td>
                    {include file="./input_text_lang.tpl"
                      languages=$prestashop_languages
                      current_lang=$prestashop_lang
                      input_name="SOGECOMMERCE_STD_REST_PLACEHLDR[cvv]"
                      field_id="SOGECOMMERCE_STD_REST_PLACEHLDR_cvv"
                      input_value=$SOGECOMMERCE_STD_REST_PLACEHLDR.cvv
                      style="width: 150px;"
                    }
                  </td>
                </tr>

              </tbody>
            </table>
            <p>{l s='Texts to use as placeholders for embedded payment fields.' mod='sogecommerce'}</p>
          </div>
          <p></p>

          <label>{l s='Register card label' mod='sogecommerce'}</label>
          <div class="margin-form">
            {include file="./input_text_lang.tpl"
              languages=$prestashop_languages
              current_lang=$prestashop_lang
              input_name="SOGECOMMERCE_STD_REST_LBL_REGIST"
              input_value=$SOGECOMMERCE_STD_REST_LBL_REGIST
              style="width: 330px;"
            }
            <p>{l s='Label displayed to invite buyers to register their card data.' mod='sogecommerce'}</p>
          </div>
          <p></p>
          <label for="SOGECOMMERCE_STD_REST_ATTEMPTS">{l s='Payment attempts number for cards' mod='sogecommerce'}</label>
          <div class="margin-form">
            <input type="text" id="SOGECOMMERCE_STD_REST_ATTEMPTS" name="SOGECOMMERCE_STD_REST_ATTEMPTS" value="{$SOGECOMMERCE_STD_REST_ATTEMPTS|escape:'html':'UTF-8'}" style="width: 150px;" />
            <p>{l s='Maximum number of payment by cards retries after a failed payment (between 0 and 2). If blank, the gateway default value is 2.' mod='sogecommerce'}</p>
          </div>
          <p></p>
        </div>

        <div id="SOGECOMMERCE_STD_1_CLICK_PAYMENT_MENU">
          <label for="SOGECOMMERCE_STD_1_CLICK_PAYMENT">{l s='Payment by token' mod='sogecommerce'}</label>
          <div class="margin-form">
            <select id="SOGECOMMERCE_STD_1_CLICK_PAYMENT" name="SOGECOMMERCE_STD_1_CLICK_PAYMENT" onchange="javascript: sogecommerceOneClickMenuDisplay()">
              {foreach from=$sogecommerce_yes_no_options key="key" item="option"}
                <option value="{$key|escape:'html':'UTF-8'}"{if $SOGECOMMERCE_STD_1_CLICK_PAYMENT === $key} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
              {/foreach}
            </select>
            <p>{l s='This option allows to pay orders without re-entering bank data at each payment. The "payment by token" option should be enabled on your %s store to use this feature.' sprintf='Sogecommerce' mod='sogecommerce'}</p>
          </div>
        </div>

        <div id="SOGECOMMERCE_STD_USE_WALLET_MENU" {if ($SOGECOMMERCE_STD_CARD_DATA_MODE !== '7' && $SOGECOMMERCE_STD_CARD_DATA_MODE !== '8' && $SOGECOMMERCE_STD_CARD_DATA_MODE !== '9') || $SOGECOMMERCE_STD_1_CLICK_PAYMENT !== 'True'} style="display: none;"{/if}>
          <label for="SOGECOMMERCE_STD_USE_WALLET">{l s='Use buyer wallet to manage tokens' mod='sogecommerce'}</label>
          <div class="margin-form">
            <select id="SOGECOMMERCE_STD_USE_WALLET" name="SOGECOMMERCE_STD_USE_WALLET">
              {foreach from=$sogecommerce_yes_no_options key="key" item="option"}
                <option value="{$key|escape:'html':'UTF-8'}"{if $SOGECOMMERCE_STD_USE_WALLET === $key} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
              {/foreach}
            </select>
            <p>{l s='A wallet allows a buyer to store multiple payment cards and choose which one to use at the time of purchase, without having to enter a card number. The buyer can request the deletion of a card stored in his wallet at any time.' mod='sogecommerce'}</p>
          </div>
        </div>

        <div id="SOGECOMMERCE_STD_USE_PAYMENT_MEAN_AS_TITLE_MENU">
          <label for="SOGECOMMERCE_STD_USE_PAYMENT_MEAN_AS_TITLE">{l s='Display payment means as payment method' mod='sogecommerce'}</label>
          <div class="margin-form">
            <select id="SOGECOMMERCE_STD_USE_PAYMENT_MEAN_AS_TITLE" name="SOGECOMMERCE_STD_USE_PAYMENT_MEAN_AS_TITLE">
              {foreach from=$sogecommerce_yes_no_options key="key" item="option"}
                <option value="{$key|escape:'html':'UTF-8'}"{if $SOGECOMMERCE_STD_USE_PAYMENT_MEAN_AS_TITLE === $key} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
              {/foreach}
            </select>
            <p>{l s='If you enable this option, the used means of payment will be set as the payment method in the order details in PrestaShop Back Office.' mod='sogecommerce'}</p>
          </div>
        </div>

        {if version_compare($smarty.const._PS_VERSION_, '1.7', '>=')}
          <div id="SOGECOMMERCE_STD_SELECT_BY_DEFAULT_MENU">
            <label for="SOGECOMMERCE_STD_SELECT_BY_DEFAULT">{l s='Select by default' mod='sogecommerce'}</label>
            <div class="margin-form">
              <select id="SOGECOMMERCE_STD_SELECT_BY_DEFAULT" name="SOGECOMMERCE_STD_SELECT_BY_DEFAULT">
                {foreach from=$sogecommerce_yes_no_options key="key" item="option"}
                  <option value="{$key|escape:'html':'UTF-8'}"{if $SOGECOMMERCE_STD_SELECT_BY_DEFAULT === $key} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
                {/foreach}
              </select>
              <p>{l s='If you enable this option, this payment method will be selected by default on the checkout page.' mod='sogecommerce'}</p>
            </div>
          </div>
        {/if}
      </fieldset>
      <div class="clear">&nbsp;</div>
    </div>

    {if $sogecommerce_plugin_features['multi']}
      <h4 style="font-weight: bold; margin-bottom: 0; overflow: hidden; line-height: unset !important;">
        <a href="#">{l s='PAYMENT IN INSTALLMENTS' mod='sogecommerce'}</a>
      </h4>
      <div>
        {if $sogecommerce_plugin_features['restrictmulti']}
          <p style="background: none repeat scroll 0 0 #FFFFE0; border: 1px solid #E6DB55; font-size: 13px; margin: 0 0 20px; padding: 10px;">
            {l s='ATTENTION: The payment in installments feature activation is subject to the prior agreement of Société Générale.' mod='sogecommerce'}<br />
            {l s='If you enable this feature while you have not the associated option, an error 10000 – INSTALLMENTS_NOT_ALLOWED or 07 - PAYMENT_CONFIG will occur and the buyer will not be able to pay.' mod='sogecommerce'}
          </p>
        {/if}

        <fieldset>
          <legend>{l s='MODULE OPTIONS' mod='sogecommerce'}</legend>

          <label for="SOGECOMMERCE_MULTI_ENABLED">{l s='Activation' mod='sogecommerce'}</label>
          <div class="margin-form">
            <select id="SOGECOMMERCE_MULTI_ENABLED" name="SOGECOMMERCE_MULTI_ENABLED">
              {foreach from=$sogecommerce_enable_disable_options key="key" item="option"}
                <option value="{$key|escape:'html':'UTF-8'}"{if $SOGECOMMERCE_MULTI_ENABLED === $key} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
              {/foreach}
            </select>
            <p>{l s='Enables / disables this payment method.' mod='sogecommerce'}</p>
          </div>

          <label>{l s='Payment method title' mod='sogecommerce'}</label>
          <div class="margin-form">
            {include file="./input_text_lang.tpl"
              languages=$prestashop_languages
              current_lang=$prestashop_lang
              input_name="SOGECOMMERCE_MULTI_TITLE"
              input_value=$SOGECOMMERCE_MULTI_TITLE
              style="width: 330px;"
            }
            <p>{l s='Method title to display on payment means page.' mod='sogecommerce'}</p>
          </div>
        </fieldset>
        <div class="clear">&nbsp;</div>

        <fieldset>
          <legend>{l s='RESTRICTIONS' mod='sogecommerce'}</legend>

          <label for="SOGECOMMERCE_MULTI_COUNTRY">{l s='Restrict to some countries' mod='sogecommerce'}</label>
          <div class="margin-form">
            <select id="SOGECOMMERCE_MULTI_COUNTRY" name="SOGECOMMERCE_MULTI_COUNTRY" onchange="javascript: sogecommerceCountriesRestrictMenuDisplay('SOGECOMMERCE_MULTI_COUNTRY')">
              {foreach from=$sogecommerce_countries_options key="key" item="option"}
                <option value="{$key|escape:'html':'UTF-8'}"{if $SOGECOMMERCE_MULTI_COUNTRY === (string)$key} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
              {/foreach}
            </select>
            <p>{l s='Buyer\'s billing countries in which this payment method is available.' mod='sogecommerce'}</p>
          </div>

          <div id="SOGECOMMERCE_MULTI_COUNTRY_MENU" {if $SOGECOMMERCE_MULTI_COUNTRY === '1'} style="display: none;"{/if}>
            <label for="SOGECOMMERCE_MULTI_COUNTRY_LST">{l s='Authorized countries' mod='sogecommerce'}</label>
            <div class="margin-form">
              <select id="SOGECOMMERCE_MULTI_COUNTRY_LST" name="SOGECOMMERCE_MULTI_COUNTRY_LST[]" multiple="multiple" size="7">
                {foreach from=$sogecommerce_countries_list['ps_countries'] key="key" item="option"}
                  <option value="{$key|escape:'html':'UTF-8'}"{if in_array($key, $SOGECOMMERCE_MULTI_COUNTRY_LST)} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
                {/foreach}
              </select>
            </div>
          </div>

          <label>{l s='Customer group amount restriction' mod='sogecommerce'}</label>
          <div class="margin-form">
            {include file="./table_amount_group.tpl"
              groups=$prestashop_groups
              input_name="SOGECOMMERCE_MULTI_AMOUNTS"
              input_value=$SOGECOMMERCE_MULTI_AMOUNTS
            }
            <p>{l s='Define amount restriction for each customer group.' mod='sogecommerce'}</p>
          </div>
        </fieldset>
        <div class="clear">&nbsp;</div>

        <fieldset>
          <legend>{l s='PAYMENT PAGE' mod='sogecommerce'}</legend>

          <label for="SOGECOMMERCE_MULTI_DELAY">{l s='Capture delay' mod='sogecommerce'}</label>
          <div class="margin-form">
            <input id="SOGECOMMERCE_MULTI_DELAY" name="SOGECOMMERCE_MULTI_DELAY" value="{$SOGECOMMERCE_MULTI_DELAY|escape:'html':'UTF-8'}" type="text">
            <p>{l s='The number of days before the bank capture. Enter value only if different from « Base settings ».' mod='sogecommerce'}</p>
          </div>

          <label for="SOGECOMMERCE_MULTI_VALIDATION">{l s='Validation mode' mod='sogecommerce'}</label>
          <div class="margin-form">
            <select id="SOGECOMMERCE_MULTI_VALIDATION" name="SOGECOMMERCE_MULTI_VALIDATION">
              <option value="-1"{if $SOGECOMMERCE_MULTI_VALIDATION === '-1'} selected="selected"{/if}>{l s='Base settings configuration' mod='sogecommerce'}</option>
              {foreach from=$sogecommerce_validation_mode_options key="key" item="option"}
                <option value="{$key|escape:'html':'UTF-8'}"{if $SOGECOMMERCE_MULTI_VALIDATION === (string)$key} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
              {/foreach}
            </select>
            <p>{l s='If manual is selected, you will have to confirm payments manually in your bank Back Office.' mod='sogecommerce'}</p>
          </div>

          <label for="SOGECOMMERCE_MULTI_PAYMENT_CARDS">{l s='Card Types' mod='sogecommerce'}</label>
          <div class="margin-form">
            <select id="SOGECOMMERCE_MULTI_PAYMENT_CARDS" name="SOGECOMMERCE_MULTI_PAYMENT_CARDS[]" multiple="multiple" size="7">
              {foreach from=$sogecommerce_multi_payment_cards_options key="key" item="option"}
                <option value="{$key|escape:'html':'UTF-8'}"{if in_array($key, $SOGECOMMERCE_MULTI_PAYMENT_CARDS)} selected="selected"{/if}>{if $key !== ""} {$key|escape:'html':'UTF-8'} - {/if}{$option|escape:'html':'UTF-8'}</option>
              {/foreach}
            </select>
            <p>{l s='The card type(s) that can be used for the payment. Select none to use gateway configuration.' mod='sogecommerce'}</p>
          </div>
        </fieldset>
        <div class="clear">&nbsp;</div>

        <fieldset>
          <legend>{l s='ADVANCED OPTIONS' mod='sogecommerce'}</legend>

          <label for="SOGECOMMERCE_MULTI_CARD_MODE">{l s='Card type selection' mod='sogecommerce'}</label>
          <div class="margin-form">
            <select id="SOGECOMMERCE_MULTI_CARD_MODE" name="SOGECOMMERCE_MULTI_CARD_MODE">
              {foreach from=$sogecommerce_card_selection_mode_options key="key" item="option"}
                <option value="{$key|escape:'html':'UTF-8'}"{if $SOGECOMMERCE_MULTI_CARD_MODE === (string)$key} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
              {/foreach}
            </select>
            <p>{l s='Select where the card type will be selected by the buyer.' mod='sogecommerce'}</p>
          </div>
        </fieldset>
        <div class="clear">&nbsp;</div>

        <fieldset>
          <legend>{l s='PAYMENT OPTIONS' mod='sogecommerce'}</legend>

          <label>{l s='Payment options' mod='sogecommerce'}</label>
          <div class="margin-form">
            <script type="text/html" id="sogecommerce_multi_row_option">
              {include file="./row_multi_option.tpl"
                languages=$prestashop_languages
                current_lang=$prestashop_lang
                key="SOGECOMMERCE_MULTI_KEY"
                option=$sogecommerce_default_multi_option
              }
            </script>

            <button type="button" id="sogecommerce_multi_options_btn"{if !empty($SOGECOMMERCE_MULTI_OPTIONS)} style="display: none;"{/if} onclick="javascript: sogecommerceAddMultiOption(true, '{l s='Delete' mod='sogecommerce'}');">{l s='Add' mod='sogecommerce'}</button>

            <table id="sogecommerce_multi_options_table"{if empty($SOGECOMMERCE_MULTI_OPTIONS)} style="display: none;"{/if} class="table" cellpadding="10" cellspacing="0">
              <thead>
                <tr>
                  <th style="font-size: 10px;">{l s='Label' mod='sogecommerce'}</th>
                  <th style="font-size: 10px;">{l s='Min amount' mod='sogecommerce'}</th>
                  <th style="font-size: 10px;">{l s='Max amount' mod='sogecommerce'}</th>
                  {if in_array('CB', $sogecommerce_multi_payment_cards_options)}
                    <th style="font-size: 10px;">{l s='Contract' mod='sogecommerce'}</th>
                  {/if}
                  <th style="font-size: 10px;">{l s='Count' mod='sogecommerce'}</th>
                  <th style="font-size: 10px;">{l s='Period' mod='sogecommerce'}</th>
                  <th style="font-size: 10px;">{l s='1st installment' mod='sogecommerce'}</th>
                  <th style="font-size: 10px;"></th>
                </tr>
              </thead>

              <tbody>
                {foreach from=$SOGECOMMERCE_MULTI_OPTIONS key="key" item="option"}
                  {include file="./row_multi_option.tpl"
                    languages=$prestashop_languages
                    current_lang=$prestashop_lang
                    key=$key
                    option=$option
                  }
                {/foreach}

                <tr id="sogecommerce_multi_option_add">
                  <td colspan="{if in_array('CB', $sogecommerce_multi_payment_cards_options)}7{else}6{/if}"></td>
                  <td>
                    <button type="button" onclick="javascript: sogecommerceAddMultiOption(false, '{l s='Delete' mod='sogecommerce'}');">{l s='Add' mod='sogecommerce'}</button>
                  </td>
                </tr>
              </tbody>
            </table>
            <p>
              {l s='Click on « Add » button to configure one or more payment options.' mod='sogecommerce'}<br />
              <b>{l s='Label' mod='sogecommerce'} : </b>{l s='The option label to display on the frontend.' mod='sogecommerce'}<br />
              <b>{l s='Min amount' mod='sogecommerce'} : </b>{l s='Minimum amount to enable the payment option.' mod='sogecommerce'}<br />
              <b>{l s='Max amount' mod='sogecommerce'} : </b>{l s='Maximum amount to enable the payment option.' mod='sogecommerce'}<br />
              {if in_array('CB', $sogecommerce_multi_payment_cards_options)}
                <b>{l s='Contract' mod='sogecommerce'} : </b>{l s='ID of the contract to use with the option (Leave blank preferably).' mod='sogecommerce'}<br />
              {/if}
              <b>{l s='Count' mod='sogecommerce'} : </b>{l s='Total number of installments.' mod='sogecommerce'}<br />
              <b>{l s='Period' mod='sogecommerce'} : </b>{l s='Delay (in days) between installments.' mod='sogecommerce'}<br />
              <b>{l s='1st installment' mod='sogecommerce'} : </b>{l s='Amount of first installment, in percentage of total amount. If empty, all installments will have the same amount.' mod='sogecommerce'}<br />
              <b>{l s='Do not forget to click on « Save » button to save your modifications.' mod='sogecommerce'}</b>
            </p>
          </div>
        </fieldset>
        <div class="clear">&nbsp;</div>
      </div>
    {/if}

    {if $sogecommerce_plugin_features['choozeo']}
      <h4 style="font-weight: bold; margin-bottom: 0; overflow: hidden; line-height: unset !important;">
        <a href="#">{l s='CHOOZEO PAYMENT' mod='sogecommerce'}</a>
      </h4>
      <div>
        <fieldset>
          <legend>{l s='MODULE OPTIONS' mod='sogecommerce'}</legend>

          <label for="SOGECOMMERCE_CHOOZEO_ENABLED">{l s='Activation' mod='sogecommerce'}</label>
          <div class="margin-form">
            <select id="SOGECOMMERCE_CHOOZEO_ENABLED" name="SOGECOMMERCE_CHOOZEO_ENABLED">
              {foreach from=$sogecommerce_enable_disable_options key="key" item="option"}
                <option value="{$key|escape:'html':'UTF-8'}"{if $SOGECOMMERCE_CHOOZEO_ENABLED === $key} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
              {/foreach}
            </select>
            <p>{l s='Enables / disables this payment method.' mod='sogecommerce'}</p>
          </div>

          <label>{l s='Payment method title' mod='sogecommerce'}</label>
          <div class="margin-form">
            {include file="./input_text_lang.tpl"
              languages=$prestashop_languages
              current_lang=$prestashop_lang
              input_name="SOGECOMMERCE_CHOOZEO_TITLE"
              input_value=$SOGECOMMERCE_CHOOZEO_TITLE
              style="width: 330px;"
            }
            <p>{l s='Method title to display on payment means page.' mod='sogecommerce'}</p>
          </div>
        </fieldset>
        <div class="clear">&nbsp;</div>

        <fieldset>
          <legend>{l s='RESTRICTIONS' mod='sogecommerce'}</legend>

          {if isset ($sogecommerce_countries_list['CHOOZEO'])}
            <label for="SOGECOMMERCE_CHOOZEO_COUNTRY">{l s='Restrict to some countries' mod='sogecommerce'}</label>
            <div class="margin-form">
              <select id="SOGECOMMERCE_CHOOZEO_COUNTRY" name="SOGECOMMERCE_CHOOZEO_COUNTRY" onchange="javascript: sogecommerceCountriesRestrictMenuDisplay('SOGECOMMERCE_CHOOZEO_COUNTRY')">
                {foreach from=$sogecommerce_countries_options key="key" item="option"}
                  <option value="{$key|escape:'html':'UTF-8'}"{if $SOGECOMMERCE_CHOOZEO_COUNTRY === (string)$key} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
                {/foreach}
              </select>
              <p>{l s='Buyer\'s billing countries in which this payment method is available.' mod='sogecommerce'}</p>
            </div>

            <div id="SOGECOMMERCE_CHOOZEO_COUNTRY_MENU" {if $SOGECOMMERCE_CHOOZEO_COUNTRY === '1'} style="display: none;"{/if}>
              <label for="SOGECOMMERCE_CHOOZEO_COUNTRY_LST">{l s='Authorized countries' mod='sogecommerce'}</label>
              <div class="margin-form">
                <select id="SOGECOMMERCE_CHOOZEO_COUNTRY_LST" name="SOGECOMMERCE_CHOOZEO_COUNTRY_LST[]" multiple="multiple" size="7">
                  {if isset ($sogecommerce_countries_list['CHOOZEO'])}
                      {foreach from=$sogecommerce_countries_list['CHOOZEO'] key="key" item="option"}
                          <option value="{$key|escape:'html':'UTF-8'}"{if in_array($key, $SOGECOMMERCE_CHOOZEO_COUNTRY_LST)} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
                      {/foreach}
                  {/if}
                </select>
              </div>
            </div>
          {else}
            <input type="hidden" name="SOGECOMMERCE_CHOOZEO_COUNTRY" value="1">
            <input type="hidden" name="SOGECOMMERCE_CHOOZEO_COUNTRY_LST[]" value ="">
            <p style="background: none repeat scroll 0 0 #FFFFE0; border: 1px solid #E6DB55; font-size: 13px; margin: 0 0 20px; padding: 10px;">
                {l s='Payment method unavailable for the list of countries defined on your PrestaShop store.' mod='sogecommerce'}
            </p>
          {/if}

          <label>{l s='Customer group amount restriction' mod='sogecommerce'}</label>
          <div class="margin-form">
            {include file="./table_amount_group.tpl"
              groups=$prestashop_groups
              input_name="SOGECOMMERCE_CHOOZEO_AMOUNTS"
              input_value=$SOGECOMMERCE_CHOOZEO_AMOUNTS
            }
            <p>{l s='Define amount restriction for each customer group.' mod='sogecommerce'}</p>
          </div>
        </fieldset>
        <div class="clear">&nbsp;</div>

        <fieldset>
          <legend>{l s='PAYMENT PAGE' mod='sogecommerce'}</legend>

          <label for="SOGECOMMERCE_CHOOZEO_DELAY">{l s='Capture delay' mod='sogecommerce'}</label>
          <div class="margin-form">
            <input id="SOGECOMMERCE_CHOOZEO_DELAY" name="SOGECOMMERCE_CHOOZEO_DELAY" value="{$SOGECOMMERCE_CHOOZEO_DELAY|escape:'html':'UTF-8'}" type="text">
            <p>{l s='The number of days before the bank capture. Enter value only if different from « Base settings ».' mod='sogecommerce'}</p>
          </div>
        </fieldset>
        <div class="clear">&nbsp;</div>

        <fieldset>
          <legend>{l s='PAYMENT OPTIONS' mod='sogecommerce'}</legend>

          <label>{l s='Payment options' mod='sogecommerce'}</label>
          <div class="margin-form">
            <table class="table" cellpadding="10" cellspacing="0">
              <thead>
                <tr>
                  <th>{l s='Activation' mod='sogecommerce'}</th>
                  <th>{l s='Label' mod='sogecommerce'}</th>
                  <th>{l s='Min amount' mod='sogecommerce'}</th>
                  <th>{l s='Max amount' mod='sogecommerce'}</th>
                </tr>
              </thead>

              <tbody>
                <tr>
                  <td>
                    <input name="SOGECOMMERCE_CHOOZEO_OPTIONS[EPNF_3X][enabled]"
                      style="width: 100%;"
                      type="checkbox"
                      value="True"
                      {if !isset($SOGECOMMERCE_CHOOZEO_OPTIONS.EPNF_3X.enabled) || ($SOGECOMMERCE_CHOOZEO_OPTIONS.EPNF_3X.enabled ==='True')}checked{/if}>
                  </td>
                  <td>Choozeo 3X CB</td>
                  <td>
                    <input name="SOGECOMMERCE_CHOOZEO_OPTIONS[EPNF_3X][min_amount]"
                      value="{if isset($SOGECOMMERCE_CHOOZEO_OPTIONS['EPNF_3X'])}{$SOGECOMMERCE_CHOOZEO_OPTIONS['EPNF_3X']['min_amount']|escape:'html':'UTF-8'}{/if}"
                      style="width: 200px;"
                      type="text">
                  </td>
                  <td>
                    <input name="SOGECOMMERCE_CHOOZEO_OPTIONS[EPNF_3X][max_amount]"
                      value="{if isset($SOGECOMMERCE_CHOOZEO_OPTIONS['EPNF_3X'])}{$SOGECOMMERCE_CHOOZEO_OPTIONS['EPNF_3X']['max_amount']|escape:'html':'UTF-8'}{/if}"
                      style="width: 200px;"
                      type="text">
                  </td>
                </tr>

                <tr>
                  <td>
                    <input name="SOGECOMMERCE_CHOOZEO_OPTIONS[EPNF_4X][enabled]"
                      style="width: 100%;"
                      type="checkbox"
                      value="True"
                      {if !isset($SOGECOMMERCE_CHOOZEO_OPTIONS.EPNF_4X.enabled) || ($SOGECOMMERCE_CHOOZEO_OPTIONS.EPNF_4X.enabled ==='True')}checked{/if}>
                  </td>
                  <td>Choozeo 4X CB</td>
                  <td>
                    <input name="SOGECOMMERCE_CHOOZEO_OPTIONS[EPNF_4X][min_amount]"
                      value="{if isset($SOGECOMMERCE_CHOOZEO_OPTIONS['EPNF_4X'])}{$SOGECOMMERCE_CHOOZEO_OPTIONS['EPNF_4X']['min_amount']|escape:'html':'UTF-8'}{/if}"
                      style="width: 200px;"
                      type="text">
                  </td>
                  <td>
                    <input name="SOGECOMMERCE_CHOOZEO_OPTIONS[EPNF_4X][max_amount]"
                      value="{if isset($SOGECOMMERCE_CHOOZEO_OPTIONS['EPNF_4X'])}{$SOGECOMMERCE_CHOOZEO_OPTIONS['EPNF_4X']['max_amount']|escape:'html':'UTF-8'}{/if}"
                      style="width: 200px;"
                      type="text">
                  </td>
                </tr>
              </tbody>
            </table>
            <p>{l s='Define amount restriction for each card.' mod='sogecommerce'}</p>
          </div>
        </fieldset>
        <div class="clear">&nbsp;</div>
      </div>
    {/if}

    {if $sogecommerce_plugin_features['oney']}
      <h4 style="font-weight: bold; margin-bottom: 0; overflow: hidden; line-height: unset !important;">
        <a href="#">{l s='ONEY PAYMENT' mod='sogecommerce'}</a>
      </h4>
      <div>
        <fieldset>
          <legend>{l s='MODULE OPTIONS' mod='sogecommerce'}</legend>

          <label for="SOGECOMMERCE_ONEY34_ENABLED">{l s='Activation' mod='sogecommerce'}</label>
          <div class="margin-form">
            <select id="SOGECOMMERCE_ONEY34_ENABLED" name="SOGECOMMERCE_ONEY34_ENABLED">
              {foreach from=$sogecommerce_enable_disable_options key="key" item="option"}
                <option value="{$key|escape:'html':'UTF-8'}"{if $SOGECOMMERCE_ONEY34_ENABLED === $key} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
              {/foreach}
            </select>
            <p>{l s='Enables / disables this payment method.' mod='sogecommerce'}</p>
          </div>

          <label>{l s='Payment method title' mod='sogecommerce'}</label>
          <div class="margin-form">
            {include file="./input_text_lang.tpl"
              languages=$prestashop_languages
              current_lang=$prestashop_lang
              input_name="SOGECOMMERCE_ONEY34_TITLE"
              input_value=$SOGECOMMERCE_ONEY34_TITLE
              style="width: 330px;"
            }
            <p>{l s='Method title to display on payment means page.' mod='sogecommerce'}</p>
          </div>
        </fieldset>
        <div class="clear">&nbsp;</div>

        <fieldset>
          <legend>{l s='RESTRICTIONS' mod='sogecommerce'}</legend>

          {if isset ($sogecommerce_countries_list['ONEY34'])}
            <label for="SOGECOMMERCE_ONEY34_COUNTRY">{l s='Restrict to some countries' mod='sogecommerce'}</label>
            <div class="margin-form">
              <select id="SOGECOMMERCE_ONEY34_COUNTRY" name="SOGECOMMERCE_ONEY34_COUNTRY" onchange="javascript: sogecommerceCountriesRestrictMenuDisplay('SOGECOMMERCE_ONEY34_COUNTRY')">
                {foreach from=$sogecommerce_countries_options key="key" item="option"}
                  <option value="{$key|escape:'html':'UTF-8'}"{if $SOGECOMMERCE_ONEY34_COUNTRY === (string)$key} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
                {/foreach}
              </select>
              <p>{l s='Buyer\'s billing countries in which this payment method is available.' mod='sogecommerce'}</p>
            </div>

            <div id="SOGECOMMERCE_ONEY34_COUNTRY_MENU" {if $SOGECOMMERCE_ONEY34_COUNTRY === '1'} style="display: none;"{/if}>
              <label for="SOGECOMMERCE_ONEY34_COUNTRY_LST">{l s='Authorized countries' mod='sogecommerce'}</label>
              <div class="margin-form">
                <select id="SOGECOMMERCE_ONEY34_COUNTRY_LST" name="SOGECOMMERCE_ONEY34_COUNTRY_LST[]" multiple="multiple" size="7">
                  {if isset ($sogecommerce_countries_list['ONEY34'])}
                      {foreach from=$sogecommerce_countries_list['ONEY34'] key="key" item="option"}
                          <option value="{$key|escape:'html':'UTF-8'}"{if in_array($key, $SOGECOMMERCE_ONEY34_COUNTRY_LST)} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
                      {/foreach}
                  {/if}
                </select>
              </div>
            </div>
          {else}
            <input type="hidden" name="SOGECOMMERCE_ONEY34_COUNTRY" value="1">
            <input type="hidden" name="SOGECOMMERCE_ONEY34_COUNTRY_LST[]" value ="">
            <p style="background: none repeat scroll 0 0 #FFFFE0; border: 1px solid #E6DB55; font-size: 13px; margin: 0 0 20px; padding: 10px;">
                {l s='Payment method unavailable for the list of countries defined on your PrestaShop store.' mod='sogecommerce'}
            </p>
          {/if}

          <label>{l s='Customer group amount restriction' mod='sogecommerce'}</label>
          <div class="margin-form">
            {include file="./table_amount_group.tpl"
              groups=$prestashop_groups
              input_name="SOGECOMMERCE_ONEY34_AMOUNTS"
              input_value=$SOGECOMMERCE_ONEY34_AMOUNTS
            }
            <p>{l s='Define amount restriction for each customer group.' mod='sogecommerce'}</p>
          </div>
        </fieldset>
        <div class="clear">&nbsp;</div>

        <fieldset>
          <legend>{l s='PAYMENT PAGE' mod='sogecommerce'}</legend>

          <label for="SOGECOMMERCE_ONEY34_DELAY">{l s='Capture delay' mod='sogecommerce'}</label>
          <div class="margin-form">
            <input id="SOGECOMMERCE_ONEY34_DELAY" name="SOGECOMMERCE_ONEY34_DELAY" value="{$SOGECOMMERCE_ONEY34_DELAY|escape:'html':'UTF-8'}" type="text">
            <p>{l s='The number of days before the bank capture. Enter value only if different from « Base settings ».' mod='sogecommerce'}</p>
          </div>

          <label for="SOGECOMMERCE_ONEY34_VALIDATION">{l s='Validation mode' mod='sogecommerce'}</label>
          <div class="margin-form">
            <select id="SOGECOMMERCE_ONEY34_VALIDATION" name="SOGECOMMERCE_ONEY34_VALIDATION">
              <option value="-1"{if $SOGECOMMERCE_ONEY34_VALIDATION === '-1'} selected="selected"{/if}>{l s='Base settings configuration' mod='sogecommerce'}</option>
              {foreach from=$sogecommerce_validation_mode_options key="key" item="option"}
                <option value="{$key|escape:'html':'UTF-8'}"{if $SOGECOMMERCE_ONEY34_VALIDATION === (string)$key} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
              {/foreach}
            </select>
            <p>{l s='If manual is selected, you will have to confirm payments manually in your bank Back Office.' mod='sogecommerce'}</p>
          </div>
        </fieldset>
        <div class="clear">&nbsp;</div>

        <fieldset>
          <legend>{l s='PAYMENT OPTIONS' mod='sogecommerce'}</legend>

          <label>{l s='Payment options' mod='sogecommerce'}</label>
          <div class="margin-form">
            <script type="text/html" id="sogecommerce_oney34_row_option">
              {include file="./row_oney_option.tpl"
                languages=$prestashop_languages
                current_lang=$prestashop_lang
                key="SOGECOMMERCE_ONEY34_KEY"
                option=$sogecommerce_default_oney_option
                suffix='34'
              }
            </script>

            <button type="button" id="sogecommerce_oney34_options_btn"{if !empty($SOGECOMMERCE_ONEY34_OPTIONS)} style="display: none;"{/if} onclick="javascript: sogecommerceAddOneyOption(true, '34');">{l s='Add' mod='sogecommerce'}</button>

            <table id="sogecommerce_oney34_options_table"{if empty($SOGECOMMERCE_ONEY34_OPTIONS)} style="display: none;"{/if} class="table" cellpadding="10" cellspacing="0">
              <thead>
                <tr>
                  <th style="font-size: 10px;">{l s='Label' mod='sogecommerce'}</th>
                  <th style="font-size: 10px;">{l s='Code' mod='sogecommerce'}</th>
                  <th style="font-size: 10px;">{l s='Means of payment' mod='sogecommerce'}</th>
                  <th style="font-size: 10px;">{l s='Min amount' mod='sogecommerce'}</th>
                  <th style="font-size: 10px;">{l s='Max amount' mod='sogecommerce'}</th>
                  <th style="font-size: 10px;">{l s='Count' mod='sogecommerce'}</th>
                  <th style="font-size: 10px;">{l s='Rate' mod='sogecommerce'}</th>
                  <th style="font-size: 10px;"></th>
                </tr>
              </thead>

              <tbody>
                {foreach from=$SOGECOMMERCE_ONEY34_OPTIONS key="key" item="option"}
                  {include file="./row_oney_option.tpl"
                    languages=$prestashop_languages
                    current_lang=$prestashop_lang
                    key=$key
                    option=$option
                    suffix='34'
                  }
                {/foreach}

                <tr id="sogecommerce_oney34_option_add">
                  <td colspan="6"></td>
                  <td>
                    <button type="button" onclick="javascript: sogecommerceAddOneyOption(false, '34');">{l s='Add' mod='sogecommerce'}</button>
                  </td>
                </tr>
              </tbody>
            </table>
            <p>
              {l s='Click on « Add » button to configure one or more payment options.' mod='sogecommerce'}<br />
              <b>{l s='Label' mod='sogecommerce'} : </b>{l s='The option label to display on the frontend (the %c and %r patterns will be respectively replaced by payments count and option rate).' mod='sogecommerce'}<br />
              <b>{l s='Code' mod='sogecommerce'} : </b>{l s='The option code as defined in your Oney contract.' mod='sogecommerce'}<br />
              <b>{l s='Means of payment' mod='sogecommerce'} : </b>{l s='Choose the means of payment you want to propose.' mod='sogecommerce'}<br />
              <b>{l s='Min amount' mod='sogecommerce'} : </b>{l s='Minimum amount to enable the payment option.' mod='sogecommerce'}<br />
              <b>{l s='Max amount' mod='sogecommerce'} : </b>{l s='Maximum amount to enable the payment option.' mod='sogecommerce'}<br />
              <b>{l s='Count' mod='sogecommerce'} : </b>{l s='Total number of installments.' mod='sogecommerce'}<br />
              <b>{l s='Rate' mod='sogecommerce'} : </b>{l s='The interest rate in percentage.' mod='sogecommerce'}<br />
              <b>{l s='Do not forget to click on « Save » button to save your modifications.' mod='sogecommerce'}</b>
            </p>
          </div>
        </fieldset>
        <div class="clear">&nbsp;</div>
      </div>
    {/if}

    {if $sogecommerce_plugin_features['franfinance']}
      <h4 style="font-weight: bold; margin-bottom: 0; overflow: hidden; line-height: unset !important;">
        <a href="#">{l s='FRANFINANCE PAYMENT' mod='sogecommerce'}</a>
      </h4>
      <div>
        <fieldset>
          <legend>{l s='MODULE OPTIONS' mod='sogecommerce'}</legend>

          <label for="SOGECOMMERCE_FFIN_ENABLED">{l s='Activation' mod='sogecommerce'}</label>
          <div class="margin-form">
            <select id="SOGECOMMERCE_FFIN_ENABLED" name="SOGECOMMERCE_FFIN_ENABLED">
              {foreach from=$sogecommerce_enable_disable_options key="key" item="option"}
                <option value="{$key|escape:'html':'UTF-8'}"{if $SOGECOMMERCE_FFIN_ENABLED === $key} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
              {/foreach}
            </select>
            <p>{l s='Enables / disables this payment method.' mod='sogecommerce'}</p>
          </div>

          <label>{l s='Payment method title' mod='sogecommerce'}</label>
          <div class="margin-form">
            {include file="./input_text_lang.tpl"
              languages=$prestashop_languages
              current_lang=$prestashop_lang
              input_name="SOGECOMMERCE_FFIN_TITLE"
              input_value=$SOGECOMMERCE_FFIN_TITLE
              style="width: 330px;"
            }
            <p>{l s='Method title to display on payment means page.' mod='sogecommerce'}</p>
          </div>
        </fieldset>
        <div class="clear">&nbsp;</div>

        <fieldset>
          <legend>{l s='RESTRICTIONS' mod='sogecommerce'}</legend>

          {if isset ($sogecommerce_countries_list['FFIN'])}
            <label for="SOGECOMMERCE_FFIN_COUNTRY">{l s='Restrict to some countries' mod='sogecommerce'}</label>
            <div class="margin-form">
              <select id="SOGECOMMERCE_FFIN_COUNTRY" name="SOGECOMMERCE_FFIN_COUNTRY" onchange="javascript: sogecommerceCountriesRestrictMenuDisplay('SOGECOMMERCE_FFIN_COUNTRY')">
                {foreach from=$sogecommerce_countries_options key="key" item="option"}
                  <option value="{$key|escape:'html':'UTF-8'}"{if $SOGECOMMERCE_FFIN_COUNTRY === (string)$key} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
                {/foreach}
              </select>
              <p>{l s='Buyer\'s billing countries in which this payment method is available.' mod='sogecommerce'}</p>
            </div>

            <div id="SOGECOMMERCE_FFIN_COUNTRY_MENU" {if $SOGECOMMERCE_FFIN_COUNTRY === '1'} style="display: none;"{/if}>
              <label for="SOGECOMMERCE_FFIN_COUNTRY_LST">{l s='Authorized countries' mod='sogecommerce'}</label>
              <div class="margin-form">
                <select id="SOGECOMMERCE_FFIN_COUNTRY_LST" name="SOGECOMMERCE_FFIN_COUNTRY_LST[]" multiple="multiple" size="7">
                  {if isset ($sogecommerce_countries_list['FFIN'])}
                      {foreach from=$sogecommerce_countries_list['FFIN'] key="key" item="option"}
                          <option value="{$key|escape:'html':'UTF-8'}"{if in_array($key, $SOGECOMMERCE_FFIN_COUNTRY_LST)} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
                      {/foreach}
                  {/if}
                </select>
              </div>
            </div>
          {else}
            <input type="hidden" name="SOGECOMMERCE_FFIN_COUNTRY" value="1">
            <input type="hidden" name="SOGECOMMERCE_FFIN_COUNTRY_LST[]" value ="">
            <p style="background: none repeat scroll 0 0 #FFFFE0; border: 1px solid #E6DB55; font-size: 13px; margin: 0 0 20px; padding: 10px;">
                {l s='Payment method unavailable for the list of countries defined on your PrestaShop store.' mod='sogecommerce'}
            </p>
          {/if}

          <label>{l s='Customer group amount restriction' mod='sogecommerce'}</label>
          <div class="margin-form">
            {include file="./table_amount_group.tpl"
              groups=$prestashop_groups
              input_name="SOGECOMMERCE_FFIN_AMOUNTS"
              input_value=$SOGECOMMERCE_FFIN_AMOUNTS
            }
            <p>{l s='Define amount restriction for each customer group.' mod='sogecommerce'}</p>
          </div>
        </fieldset>
        <div class="clear">&nbsp;</div>

        <fieldset>
          <legend>{l s='PAYMENT OPTIONS' mod='sogecommerce'}</legend>

          <label>{l s='Payment options' mod='sogecommerce'}</label>
          <div class="margin-form">
            <script type="text/html" id="sogecommerce_ffin_row_option">
              {include file="./row_franfinance_option.tpl"
                languages=$prestashop_languages
                current_lang=$prestashop_lang
                key="SOGECOMMERCE_FFIN_KEY"
                option=$sogecommerce_default_franfinance_option
              }
            </script>

            <button type="button" id="sogecommerce_ffin_options_btn"{if !empty($SOGECOMMERCE_FFIN_OPTIONS)} style="display: none;"{/if} onclick="javascript: sogecommerceAddFranfinanceOption(true, '{l s='Delete' mod='sogecommerce'}');">{l s='Add' mod='sogecommerce'}</button>

            <table id="sogecommerce_ffin_options_table"{if empty($SOGECOMMERCE_FFIN_OPTIONS)} style="display: none;"{/if} class="table" cellpadding="10" cellspacing="0">
              <thead>
                <tr>
                  <th style="font-size: 10px;">{l s='Label' mod='sogecommerce'}</th>
                  <th style="font-size: 10px;">{l s='Count' mod='sogecommerce'}</th>
                  <th style="font-size: 10px;">{l s='Fees' mod='sogecommerce'}</th>
                  <th style="font-size: 10px;">{l s='Min amount' mod='sogecommerce'}</th>
                  <th style="font-size: 10px;">{l s='Max amount' mod='sogecommerce'}</th>
                  <th style="font-size: 10px;"></th>
                </tr>
              </thead>

              <tbody>
                {foreach from=$SOGECOMMERCE_FFIN_OPTIONS key="key" item="option"}
                  {include file="./row_franfinance_option.tpl"
                    languages=$prestashop_languages
                    current_lang=$prestashop_lang
                    key=$key
                    option=$option
                  }
                {/foreach}

                <tr id="sogecommerce_ffin_option_add">
                  <td colspan="7"></td>
                  <td>
                    <button type="button" onclick="javascript: sogecommerceAddFranfinanceOption(false, '{l s='Delete' mod='sogecommerce'}');">{l s='Add' mod='sogecommerce'}</button>
                  </td>
                </tr>
              </tbody>
            </table>
            <p>
              {l s='Click on « Add » button to configure one or more payment options.' mod='sogecommerce'}<br />
              <b>{l s='Label' mod='sogecommerce'} : </b>{l s='The option label to display on the frontend (the %c pattern will be replaced by payments count).' mod='sogecommerce'}<br />
              <b>{l s='Count' mod='sogecommerce'} : </b>{l s='Total number of installments.' mod='sogecommerce'}<br />
              <b>{l s='Fees' mod='sogecommerce'} : </b>{l s='Enable or disables fees application.' mod='sogecommerce'}<br />
              <b>{l s='Min amount' mod='sogecommerce'} : </b>{l s='Minimum amount to enable the payment option.' mod='sogecommerce'}<br />
              <b>{l s='Max amount' mod='sogecommerce'} : </b>{l s='Maximum amount to enable the payment option.' mod='sogecommerce'}<br />
              <b>{l s='Do not forget to click on « Save » button to save your modifications.' mod='sogecommerce'}</b>
            </p>
          </div>
        </fieldset>
        <div class="clear">&nbsp;</div>
      </div>
    {/if}

    {if $sogecommerce_plugin_features['fullcb']}
      <h4 style="font-weight: bold; margin-bottom: 0; overflow: hidden; line-height: unset !important;">
        <a href="#">{l s='FULLCB PAYMENT' mod='sogecommerce'}</a>
      </h4>
      <div>
        <fieldset>
          <legend>{l s='MODULE OPTIONS' mod='sogecommerce'}</legend>

          <label for="SOGECOMMERCE_FULLCB_ENABLED">{l s='Activation' mod='sogecommerce'}</label>
          <div class="margin-form">
            <select id="SOGECOMMERCE_FULLCB_ENABLED" name="SOGECOMMERCE_FULLCB_ENABLED">
              {foreach from=$sogecommerce_enable_disable_options key="key" item="option"}
                <option value="{$key|escape:'html':'UTF-8'}"{if $SOGECOMMERCE_FULLCB_ENABLED === $key} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
              {/foreach}
            </select>
            <p>{l s='Enables / disables this payment method.' mod='sogecommerce'}</p>
          </div>

          <label>{l s='Payment method title' mod='sogecommerce'}</label>
          <div class="margin-form">
            {include file="./input_text_lang.tpl"
              languages=$prestashop_languages
              current_lang=$prestashop_lang
              input_name="SOGECOMMERCE_FULLCB_TITLE"
              input_value=$SOGECOMMERCE_FULLCB_TITLE
              style="width: 330px;"
            }
            <p>{l s='Method title to display on payment means page.' mod='sogecommerce'}</p>
          </div>
        </fieldset>
        <div class="clear">&nbsp;</div>

        <fieldset>
          <legend>{l s='RESTRICTIONS' mod='sogecommerce'}</legend>

          <div id="SOGECOMMERCE_FULLCB_COUNTRY_MENU">
            <input type="hidden" name="SOGECOMMERCE_FULLCB_COUNTRY" value="1">
            <input type="hidden" name="SOGECOMMERCE_FULLCB_COUNTRY_LST[]" value ="FR">
            <label for="SOGECOMMERCE_FULLCB_COUNTRY_LST">{l s='Authorized countries' mod='sogecommerce'}</label>
            <div class="margin-form">
              <span style="font-size: 13px; padding-top: 5px; vertical-align: middle;"><b>{$sogecommerce_countries_list['FULLCB']['FR']|escape:'html':'UTF-8'}</b></span>
            </div>
          </div>

          <label>{l s='Customer group amount restriction' mod='sogecommerce'}</label>
          <div class="margin-form">
            {include file="./table_amount_group.tpl"
              groups=$prestashop_groups
              input_name="SOGECOMMERCE_FULLCB_AMOUNTS"
              input_value=$SOGECOMMERCE_FULLCB_AMOUNTS
            }
            <p>{l s='Define amount restriction for each customer group.' mod='sogecommerce'}</p>
          </div>
        </fieldset>
        <div class="clear">&nbsp;</div>

        <fieldset>
          <legend>{l s='PAYMENT OPTIONS' mod='sogecommerce'}</legend>

          <label for="SOGECOMMERCE_FULLCB_ENABLE_OPTS">{l s='Enable options selection' mod='sogecommerce'}</label>
          <div class="margin-form">
            <select id="SOGECOMMERCE_FULLCB_ENABLE_OPTS" name="SOGECOMMERCE_FULLCB_ENABLE_OPTS" onchange="javascript: sogecommerceFullcbEnableOptionsChanged();">
              {foreach from=$sogecommerce_yes_no_options key="key" item="option"}
                <option value="{$key|escape:'html':'UTF-8'}"{if $SOGECOMMERCE_FULLCB_ENABLE_OPTS === $key} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
              {/foreach}
            </select>
            <p>{l s='Enable payment options selection on merchant site.' mod='sogecommerce'}</p>
          </div>

          <section id="sogecommerce_fullcb_options_settings">
            <label>{l s='Payment options' mod='sogecommerce'}</label>
            <div class="margin-form">
              <table class="table" cellpadding="10" cellspacing="0">
                <thead>
                  <tr>
                    <th style="font-size: 10px;">{l s='Activation' mod='sogecommerce'}</th>
                    <th style="font-size: 10px;">{l s='Label' mod='sogecommerce'}</th>
                    <th style="font-size: 10px;">{l s='Min amount' mod='sogecommerce'}</th>
                    <th style="font-size: 10px;">{l s='Max amount' mod='sogecommerce'}</th>
                    <th style="font-size: 10px;">{l s='Rate' mod='sogecommerce'}</th>
                    <th style="font-size: 10px;">{l s='Cap' mod='sogecommerce'}</th>
                  </tr>
                </thead>

                <tbody>
                  {foreach from=$SOGECOMMERCE_FULLCB_OPTIONS key="key" item="option"}
                  <tr>
                    <td>
                      <input name="SOGECOMMERCE_FULLCB_OPTIONS[{$key|escape:'html':'UTF-8'}][enabled]"
                        style="width: 100%;"
                        type="checkbox"
                        value="True"
                        {if !isset($option.enabled) || ($option.enabled === 'True')}checked{/if}>
                    </td>
                    <td>
                      {include file="./input_text_lang.tpl"
                        languages=$prestashop_languages
                        current_lang=$prestashop_lang
                        input_name="SOGECOMMERCE_FULLCB_OPTIONS[{$key|escape:'html':'UTF-8'}][label]"
                        field_id="SOGECOMMERCE_FULLCB_OPTIONS_{$key|escape:'html':'UTF-8'}_label"
                        input_value=$option['label']
                        style="width: 140px;"
                      }
                      <input name="SOGECOMMERCE_FULLCB_OPTIONS[{$key|escape:'html':'UTF-8'}][count]" value="{$option['count']|escape:'html':'UTF-8'}" type="text" style="display: none; width: 0px;">
                    </td>
                    <td>
                      <input name="SOGECOMMERCE_FULLCB_OPTIONS[{$key|escape:'html':'UTF-8'}][min_amount]"
                        value="{if isset($option)}{$option['min_amount']|escape:'html':'UTF-8'}{/if}"
                        style="width: 75px;"
                        type="text">
                    </td>
                    <td>
                      <input name="SOGECOMMERCE_FULLCB_OPTIONS[{$key|escape:'html':'UTF-8'}][max_amount]"
                        value="{if isset($option)}{$option['max_amount']|escape:'html':'UTF-8'}{/if}"
                        style="width: 75px;"
                        type="text">
                    </td>
                    <td>
                      <input name="SOGECOMMERCE_FULLCB_OPTIONS[{$key|escape:'html':'UTF-8'}][rate]"
                        value="{if isset($option)}{$option['rate']|escape:'html':'UTF-8'}{/if}"
                        style="width: 70px;"
                        type="text">
                    </td>
                    <td>
                      <input name="SOGECOMMERCE_FULLCB_OPTIONS[{$key|escape:'html':'UTF-8'}][cap]"
                        value="{if isset($option)}{$option['cap']|escape:'html':'UTF-8'}{/if}"
                        style="width: 70px;"
                        type="text">
                    </td>
                  </tr>
                  {/foreach}
                </tbody>
              </table>
              <p>
                {l s='Configure FullCB payment options.' mod='sogecommerce'}<br />
                <b>{l s='Activation' mod='sogecommerce'} : </b>{l s='Enable / disable the payment option.' mod='sogecommerce'}<br />
                <b>{l s='Min amount' mod='sogecommerce'} : </b>{l s='Minimum amount to enable the payment option.' mod='sogecommerce'}<br />
                <b>{l s='Max amount' mod='sogecommerce'} : </b>{l s='Maximum amount to enable the payment option.' mod='sogecommerce'}<br />
                <b>{l s='Rate' mod='sogecommerce'} : </b>{l s='The interest rate in percentage.' mod='sogecommerce'}<br />
                <b>{l s='Cap' mod='sogecommerce'} : </b>{l s='Maximum fees amount of payment option.' mod='sogecommerce'}<br />
                <b>{l s='Do not forget to click on « Save » button to save your modifications.' mod='sogecommerce'}</b>
              </p>
            </div>
          </section>

          <script type="text/javascript">
            sogecommerceFullcbEnableOptionsChanged();
          </script>
         </fieldset>
        <div class="clear">&nbsp;</div>
      </div>
    {/if}

    {if $sogecommerce_plugin_features['ancv']}
      <h4 style="font-weight: bold; margin-bottom: 0; overflow: hidden; line-height: unset !important;">
        <a href="#">{l s='ANCV PAYMENT' mod='sogecommerce'}</a>
      </h4>
      <div>
        <fieldset>
          <legend>{l s='MODULE OPTIONS' mod='sogecommerce'}</legend>

          <label for="SOGECOMMERCE_ANCV_ENABLED">{l s='Activation' mod='sogecommerce'}</label>
          <div class="margin-form">
            <select id="SOGECOMMERCE_ANCV_ENABLED" name="SOGECOMMERCE_ANCV_ENABLED">
              {foreach from=$sogecommerce_enable_disable_options key="key" item="option"}
                <option value="{$key|escape:'html':'UTF-8'}"{if $SOGECOMMERCE_ANCV_ENABLED === $key} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
              {/foreach}
            </select>
            <p>{l s='Enables / disables this payment method.' mod='sogecommerce'}</p>
          </div>

          <label>{l s='Payment method title' mod='sogecommerce'}</label>
          <div class="margin-form">
            {include file="./input_text_lang.tpl"
              languages=$prestashop_languages
              current_lang=$prestashop_lang
              input_name="SOGECOMMERCE_ANCV_TITLE"
              input_value=$SOGECOMMERCE_ANCV_TITLE
              style="width: 330px;"
            }
            <p>{l s='Method title to display on payment means page.' mod='sogecommerce'}</p>
          </div>
        </fieldset>
        <div class="clear">&nbsp;</div>

        <fieldset>
          <legend>{l s='RESTRICTIONS' mod='sogecommerce'}</legend>

          <label for="SOGECOMMERCE_ANCV_COUNTRY">{l s='Restrict to some countries' mod='sogecommerce'}</label>
          <div class="margin-form">
            <select id="SOGECOMMERCE_ANCV_COUNTRY" name="SOGECOMMERCE_ANCV_COUNTRY" onchange="javascript: sogecommerceCountriesRestrictMenuDisplay('SOGECOMMERCE_ANCV_COUNTRY')">
              {foreach from=$sogecommerce_countries_options key="key" item="option"}
                <option value="{$key|escape:'html':'UTF-8'}"{if $SOGECOMMERCE_ANCV_COUNTRY === (string)$key} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
              {/foreach}
            </select>
            <p>{l s='Buyer\'s billing countries in which this payment method is available.' mod='sogecommerce'}</p>
          </div>

          <div id="SOGECOMMERCE_ANCV_COUNTRY_MENU" {if $SOGECOMMERCE_ANCV_COUNTRY === '1'} style="display: none;"{/if}>
            <label for="SOGECOMMERCE_ANCV_COUNTRY_LST">{l s='Authorized countries' mod='sogecommerce'}</label>
            <div class="margin-form">
              <select id="SOGECOMMERCE_ANCV_COUNTRY_LST" name="SOGECOMMERCE_ANCV_COUNTRY_LST[]" multiple="multiple" size="7">
                {foreach from=$sogecommerce_countries_list['ps_countries'] key="key" item="option"}
                  <option value="{$key|escape:'html':'UTF-8'}"{if in_array($key, $SOGECOMMERCE_ANCV_COUNTRY_LST)} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
                {/foreach}
              </select>
            </div>
          </div>

          <label>{l s='Customer group amount restriction' mod='sogecommerce'}</label>
          <div class="margin-form">
            {include file="./table_amount_group.tpl"
              groups=$prestashop_groups
              input_name="SOGECOMMERCE_ANCV_AMOUNTS"
              input_value=$SOGECOMMERCE_ANCV_AMOUNTS
            }
            <p>{l s='Define amount restriction for each customer group.' mod='sogecommerce'}</p>
          </div>
        </fieldset>
        <div class="clear">&nbsp;</div>

        <fieldset>
          <legend>{l s='PAYMENT PAGE' mod='sogecommerce'}</legend>

          <label for="SOGECOMMERCE_ANCV_DELAY">{l s='Capture delay' mod='sogecommerce'}</label>
          <div class="margin-form">
            <input id="SOGECOMMERCE_ANCV_DELAY" name="SOGECOMMERCE_ANCV_DELAY" value="{$SOGECOMMERCE_ANCV_DELAY|escape:'html':'UTF-8'}" type="text">
            <p>{l s='The number of days before the bank capture. Enter value only if different from « Base settings ».' mod='sogecommerce'}</p>
          </div>

          <label for="SOGECOMMERCE_ANCV_VALIDATION">{l s='Validation mode' mod='sogecommerce'}</label>
          <div class="margin-form">
            <select id="SOGECOMMERCE_ANCV_VALIDATION" name="SOGECOMMERCE_ANCV_VALIDATION">
              <option value="-1"{if $SOGECOMMERCE_ANCV_VALIDATION === '-1'} selected="selected"{/if}>{l s='Base settings configuration' mod='sogecommerce'}</option>
              {foreach from=$sogecommerce_validation_mode_options key="key" item="option"}
                <option value="{$key|escape:'html':'UTF-8'}"{if $SOGECOMMERCE_ANCV_VALIDATION === (string)$key} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
              {/foreach}
            </select>
            <p>{l s='If manual is selected, you will have to confirm payments manually in your bank Back Office.' mod='sogecommerce'}</p>
          </div>
        </fieldset>
        <div class="clear">&nbsp;</div>
      </div>
    {/if}

    {if $sogecommerce_plugin_features['sepa']}
      <h4 style="font-weight: bold; margin-bottom: 0; overflow: hidden; line-height: unset !important;">
        <a href="#">{l s='SEPA PAYMENT' mod='sogecommerce'}</a>
      </h4>
      <div>
        <fieldset>
          <legend>{l s='MODULE OPTIONS' mod='sogecommerce'}</legend>

          <label for="SOGECOMMERCE_SEPA_ENABLED">{l s='Activation' mod='sogecommerce'}</label>
          <div class="margin-form">
            <select id="SOGECOMMERCE_SEPA_ENABLED" name="SOGECOMMERCE_SEPA_ENABLED">
              {foreach from=$sogecommerce_enable_disable_options key="key" item="option"}
                <option value="{$key|escape:'html':'UTF-8'}"{if $SOGECOMMERCE_SEPA_ENABLED === $key} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
              {/foreach}
            </select>
            <p>{l s='Enables / disables this payment method.' mod='sogecommerce'}</p>
          </div>

          <label>{l s='Payment method title' mod='sogecommerce'}</label>
          <div class="margin-form">
            {include file="./input_text_lang.tpl"
              languages=$prestashop_languages
              current_lang=$prestashop_lang
              input_name="SOGECOMMERCE_SEPA_TITLE"
              input_value=$SOGECOMMERCE_SEPA_TITLE
              style="width: 330px;"
            }
            <p>{l s='Method title to display on payment means page.' mod='sogecommerce'}</p>
          </div>
        </fieldset>
        <div class="clear">&nbsp;</div>

        <fieldset>
          <legend>{l s='RESTRICTIONS' mod='sogecommerce'}</legend>

          {if isset ($sogecommerce_countries_list['SEPA'])}
            <label for="SOGECOMMERCE_SEPA_COUNTRY">{l s='Restrict to some countries' mod='sogecommerce'}</label>
            <div class="margin-form">
              <select id="SOGECOMMERCE_SEPA_COUNTRY" name="SOGECOMMERCE_SEPA_COUNTRY" onchange="javascript: sogecommerceCountriesRestrictMenuDisplay('SOGECOMMERCE_SEPA_COUNTRY')">
                {foreach from=$sogecommerce_countries_options key="key" item="option"}
                  <option value="{$key|escape:'html':'UTF-8'}"{if $SOGECOMMERCE_SEPA_COUNTRY === (string)$key} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
                {/foreach}
              </select>
              <p>{l s='Buyer\'s billing countries in which this payment method is available.' mod='sogecommerce'}</p>
            </div>

            <div id="SOGECOMMERCE_SEPA_COUNTRY_MENU" {if $SOGECOMMERCE_SEPA_COUNTRY === '1'} style="display: none;"{/if}>
              <label for="SOGECOMMERCE_SEPA_COUNTRY_LST">{l s='Authorized countries' mod='sogecommerce'}</label>
              <div class="margin-form">
                <select id="SOGECOMMERCE_SEPA_COUNTRY_LST" name="SOGECOMMERCE_SEPA_COUNTRY_LST[]" multiple="multiple" size="7">
                  {if isset ($sogecommerce_countries_list['SEPA'])}
                      {foreach from=$sogecommerce_countries_list['SEPA'] key="key" item="option"}
                          <option value="{$key|escape:'html':'UTF-8'}"{if in_array($key, $SOGECOMMERCE_SEPA_COUNTRY_LST)} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
                      {/foreach}
                  {/if}
                </select>
              </div>
            </div>
          {else}
            <input type="hidden" name="SOGECOMMERCE_SEPA_COUNTRY" value="1">
            <input type="hidden" name="SOGECOMMERCE_SEPA_COUNTRY_LST[]" value ="">
            <p style="background: none repeat scroll 0 0 #FFFFE0; border: 1px solid #E6DB55; font-size: 13px; margin: 0 0 20px; padding: 10px;">
                {l s='Payment method unavailable for the list of countries defined on your PrestaShop store.' mod='sogecommerce'}
            </p>
          {/if}

          <label>{l s='Customer group amount restriction' mod='sogecommerce'}</label>
          <div class="margin-form">
            {include file="./table_amount_group.tpl"
              groups=$prestashop_groups
              input_name="SOGECOMMERCE_SEPA_AMOUNTS"
              input_value=$SOGECOMMERCE_SEPA_AMOUNTS
            }
            <p>{l s='Define amount restriction for each customer group.' mod='sogecommerce'}</p>
          </div>
         </fieldset>
        <div class="clear">&nbsp;</div>

        <fieldset>
          <legend>{l s='PAYMENT PAGE' mod='sogecommerce'}</legend>

          <label for="SOGECOMMERCE_SEPA_DELAY">{l s='Capture delay' mod='sogecommerce'}</label>
          <div class="margin-form">
            <input id="SOGECOMMERCE_SEPA_DELAY" name="SOGECOMMERCE_SEPA_DELAY" value="{$SOGECOMMERCE_SEPA_DELAY|escape:'html':'UTF-8'}" type="text">
            <p>{l s='The number of days before the bank capture. Enter value only if different from « Base settings ».' mod='sogecommerce'}</p>
          </div>

          <label for="SOGECOMMERCE_SEPA_VALIDATION">{l s='Validation mode' mod='sogecommerce'}</label>
          <div class="margin-form">
            <select id="SOGECOMMERCE_SEPA_VALIDATION" name="SOGECOMMERCE_SEPA_VALIDATION">
              <option value="-1"{if $SOGECOMMERCE_SEPA_VALIDATION === '-1'} selected="selected"{/if}>{l s='Base settings configuration' mod='sogecommerce'}</option>
              {foreach from=$sogecommerce_validation_mode_options key="key" item="option"}
                <option value="{$key|escape:'html':'UTF-8'}"{if $SOGECOMMERCE_SEPA_VALIDATION === (string)$key} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
              {/foreach}
            </select>
            <p>{l s='If manual is selected, you will have to confirm payments manually in your bank Back Office.' mod='sogecommerce'}</p>
          </div>
        </fieldset>
        <div class="clear">&nbsp;</div>

        <fieldset>
          <legend>{l s='PAYMENT OPTIONS' mod='sogecommerce'}</legend>

          <label for="SOGECOMMERCE_SEPA_MANDATE_MODE">{l s='SEPA direct debit mode' mod='sogecommerce'}</label>
          <div class="margin-form">
            <select id="SOGECOMMERCE_SEPA_MANDATE_MODE" name="SOGECOMMERCE_SEPA_MANDATE_MODE" onchange="javascript: sogecommerceSepa1clickPaymentMenuDisplay('SOGECOMMERCE_SEPA_MANDATE_MODE')">
              {foreach from=$sogecommerce_sepa_mandate_mode_options key="key" item="option"}
                <option value="{$key|escape:'html':'UTF-8'}"{if $SOGECOMMERCE_SEPA_MANDATE_MODE === $key} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
              {/foreach}
            </select>
            <p>{l s='Select SEPA direct debit mode. Attention, the two last choices require the payment by token option on %s.' sprintf='Sogecommerce' mod='sogecommerce'}</p>
          </div>

          <div id="SOGECOMMERCE_SEPA_1_CLICK_PAYMNT_MENU"  {if $SOGECOMMERCE_SEPA_MANDATE_MODE !== 'REGISTER_PAY'} style="display: none;"{/if}>
            <label for="SOGECOMMERCE_SEPA_1_CLICK_PAYMNT">{l s='1-Click payment' mod='sogecommerce'}</label>
            <div class="margin-form">
              <select id="SOGECOMMERCE_SEPA_1_CLICK_PAYMNT" name="SOGECOMMERCE_SEPA_1_CLICK_PAYMNT">
                {foreach from=$sogecommerce_yes_no_options key="key" item="option"}
                  <option value="{$key|escape:'html':'UTF-8'}"{if $SOGECOMMERCE_SEPA_1_CLICK_PAYMNT === $key} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
                {/foreach}
              </select>
              <p>{l s='This option allows to pay orders without re-entering bank data at each payment. The "payment by token" option should be enabled on your %s store to use this feature.' sprintf='Sogecommerce' mod='sogecommerce'}</p>
            </div>
          </div>
        </fieldset>
        <div class="clear">&nbsp;</div>
      </div>
    {/if}

    {if $sogecommerce_plugin_features['paypal']}
      <h4 style="font-weight: bold; margin-bottom: 0; overflow: hidden; line-height: unset !important;">
        <a href="#">{l s='PAYPAL PAYMENT' mod='sogecommerce'}</a>
      </h4>
      <div>
        <fieldset>
          <legend>{l s='MODULE OPTIONS' mod='sogecommerce'}</legend>

          <label for="SOGECOMMERCE_PAYPAL_ENABLED">{l s='Activation' mod='sogecommerce'}</label>
          <div class="margin-form">
            <select id="SOGECOMMERCE_PAYPAL_ENABLED" name="SOGECOMMERCE_PAYPAL_ENABLED">
              {foreach from=$sogecommerce_enable_disable_options key="key" item="option"}
                <option value="{$key|escape:'html':'UTF-8'}"{if $SOGECOMMERCE_PAYPAL_ENABLED === $key} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
              {/foreach}
            </select>
            <p>{l s='Enables / disables this payment method.' mod='sogecommerce'}</p>
          </div>

          <label>{l s='Payment method title' mod='sogecommerce'}</label>
          <div class="margin-form">
            {include file="./input_text_lang.tpl"
              languages=$prestashop_languages
              current_lang=$prestashop_lang
              input_name="SOGECOMMERCE_PAYPAL_TITLE"
              input_value=$SOGECOMMERCE_PAYPAL_TITLE
              style="width: 330px;"
            }
            <p>{l s='Method title to display on payment means page.' mod='sogecommerce'}</p>
          </div>
        </fieldset>
        <div class="clear">&nbsp;</div>

        <fieldset>
          <legend>{l s='RESTRICTIONS' mod='sogecommerce'}</legend>

          <label for="SOGECOMMERCE_PAYPAL_COUNTRY">{l s='Restrict to some countries' mod='sogecommerce'}</label>
          <div class="margin-form">
            <select id="SOGECOMMERCE_PAYPAL_COUNTRY" name="SOGECOMMERCE_PAYPAL_COUNTRY" onchange="javascript: sogecommerceCountriesRestrictMenuDisplay('SOGECOMMERCE_PAYPAL_COUNTRY')">
              {foreach from=$sogecommerce_countries_options key="key" item="option"}
                <option value="{$key|escape:'html':'UTF-8'}"{if $SOGECOMMERCE_PAYPAL_COUNTRY === (string)$key} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
              {/foreach}
            </select>
            <p>{l s='Buyer\'s billing countries in which this payment method is available.' mod='sogecommerce'}</p>
          </div>

          <div id="SOGECOMMERCE_PAYPAL_COUNTRY_MENU" {if $SOGECOMMERCE_PAYPAL_COUNTRY === '1'} style="display: none;"{/if}>
            <label for="SOGECOMMERCE_PAYPAL_COUNTRY_LST">{l s='Authorized countries' mod='sogecommerce'}</label>
            <div class="margin-form">
              <select id="SOGECOMMERCE_PAYPAL_COUNTRY_LST" name="SOGECOMMERCE_PAYPAL_COUNTRY_LST[]" multiple="multiple" size="7">
                {foreach from=$sogecommerce_countries_list['ps_countries'] key="key" item="option"}
                  <option value="{$key|escape:'html':'UTF-8'}"{if in_array($key, $SOGECOMMERCE_PAYPAL_COUNTRY_LST)} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
                {/foreach}
              </select>
            </div>
          </div>

          <label>{l s='Customer group amount restriction' mod='sogecommerce'}</label>
          <div class="margin-form">
            {include file="./table_amount_group.tpl"
              groups=$prestashop_groups
              input_name="SOGECOMMERCE_PAYPAL_AMOUNTS"
              input_value=$SOGECOMMERCE_PAYPAL_AMOUNTS
            }
            <p>{l s='Define amount restriction for each customer group.' mod='sogecommerce'}</p>
          </div>
        </fieldset>
        <div class="clear">&nbsp;</div>

        <fieldset>
          <legend>{l s='PAYMENT PAGE' mod='sogecommerce'}</legend>

          <label for="SOGECOMMERCE_PAYPAL_DELAY">{l s='Capture delay' mod='sogecommerce'}</label>
          <div class="margin-form">
            <input id="SOGECOMMERCE_PAYPAL_DELAY" name="SOGECOMMERCE_PAYPAL_DELAY" value="{$SOGECOMMERCE_PAYPAL_DELAY|escape:'html':'UTF-8'}" type="text">
            <p>{l s='The number of days before the bank capture. Enter value only if different from « Base settings ».' mod='sogecommerce'}</p>
          </div>

          <label for="SOGECOMMERCE_PAYPAL_VALIDATION">{l s='Validation mode' mod='sogecommerce'}</label>
          <div class="margin-form">
            <select id="SOGECOMMERCE_PAYPAL_VALIDATION" name="SOGECOMMERCE_PAYPAL_VALIDATION">
              <option value="-1"{if $SOGECOMMERCE_PAYPAL_VALIDATION === '-1'} selected="selected"{/if}>{l s='Base settings configuration' mod='sogecommerce'}</option>
              {foreach from=$sogecommerce_validation_mode_options key="key" item="option"}
                <option value="{$key|escape:'html':'UTF-8'}"{if $SOGECOMMERCE_PAYPAL_VALIDATION === (string)$key} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
              {/foreach}
            </select>
            <p>{l s='If manual is selected, you will have to confirm payments manually in your bank Back Office.' mod='sogecommerce'}</p>
          </div>
        </fieldset>
        <div class="clear">&nbsp;</div>
      </div>
    {/if}

    {if $sogecommerce_plugin_features['sofort']}
      <h4 style="font-weight: bold; margin-bottom: 0; overflow: hidden; line-height: unset !important;">
        <a href="#">{l s='SOFORT BANKING PAYMENT' mod='sogecommerce'}</a>
      </h4>
      <div>
        <fieldset>
          <legend>{l s='MODULE OPTIONS' mod='sogecommerce'}</legend>

          <label for="SOGECOMMERCE_SOFORT_ENABLED">{l s='Activation' mod='sogecommerce'}</label>
          <div class="margin-form">
            <select id="SOGECOMMERCE_SOFORT_ENABLED" name="SOGECOMMERCE_SOFORT_ENABLED">
              {foreach from=$sogecommerce_enable_disable_options key="key" item="option"}
                <option value="{$key|escape:'html':'UTF-8'}"{if $SOGECOMMERCE_SOFORT_ENABLED === $key} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
              {/foreach}
            </select>
            <p>{l s='Enables / disables this payment method.' mod='sogecommerce'}</p>
          </div>

          <label>{l s='Payment method title' mod='sogecommerce'}</label>
          <div class="margin-form">
            {include file="./input_text_lang.tpl"
              languages=$prestashop_languages
              current_lang=$prestashop_lang
              input_name="SOGECOMMERCE_SOFORT_TITLE"
              input_value=$SOGECOMMERCE_SOFORT_TITLE
              style="width: 330px;"
            }
            <p>{l s='Method title to display on payment means page.' mod='sogecommerce'}</p>
          </div>
        </fieldset>
        <div class="clear">&nbsp;</div>

        <fieldset>
          <legend>{l s='RESTRICTIONS' mod='sogecommerce'}</legend>

          {if isset ($sogecommerce_countries_list['SOFORT'])}
            <label for="SOGECOMMERCE_SOFORT_COUNTRY">{l s='Restrict to some countries' mod='sogecommerce'}</label>
            <div class="margin-form">
              <select id="SOGECOMMERCE_SOFORT_COUNTRY" name="SOGECOMMERCE_SOFORT_COUNTRY" onchange="javascript: sogecommerceCountriesRestrictMenuDisplay('SOGECOMMERCE_SOFORT_COUNTRY')">
                {foreach from=$sogecommerce_countries_options key="key" item="option"}
                  <option value="{$key|escape:'html':'UTF-8'}"{if $SOGECOMMERCE_SOFORT_COUNTRY === (string)$key} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
                {/foreach}
              </select>
              <p>{l s='Buyer\'s billing countries in which this payment method is available.' mod='sogecommerce'}</p>
            </div>

            <div id="SOGECOMMERCE_SOFORT_COUNTRY_MENU" {if $SOGECOMMERCE_SOFORT_COUNTRY === '1'} style="display: none;"{/if}>
              <label for="SOGECOMMERCE_SOFORT_COUNTRY_LST">{l s='Authorized countries' mod='sogecommerce'}</label>
              <div class="margin-form">
                <select id="SOGECOMMERCE_SOFORT_COUNTRY_LST" name="SOGECOMMERCE_SOFORT_COUNTRY_LST[]" multiple="multiple" size="7">
                  {if isset ($sogecommerce_countries_list['SOFORT'])}
                      {foreach from=$sogecommerce_countries_list['SOFORT'] key="key" item="option"}
                          <option value="{$key|escape:'html':'UTF-8'}"{if in_array($key, $SOGECOMMERCE_SOFORT_COUNTRY_LST)} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
                      {/foreach}
                  {/if}
                </select>
              </div>
            </div>
          {else}
            <input type="hidden" name="SOGECOMMERCE_SOFORT_COUNTRY" value="1">
            <input type="hidden" name="SOGECOMMERCE_SOFORT_COUNTRY_LST[]" value ="">
            <p style="background: none repeat scroll 0 0 #FFFFE0; border: 1px solid #E6DB55; font-size: 13px; margin: 0 0 20px; padding: 10px;">
                {l s='Payment method unavailable for the list of countries defined on your PrestaShop store.' mod='sogecommerce'}
            </p>
          {/if}

          <label>{l s='Customer group amount restriction' mod='sogecommerce'}</label>
          <div class="margin-form">
            {include file="./table_amount_group.tpl"
              groups=$prestashop_groups
              input_name="SOGECOMMERCE_SOFORT_AMOUNTS"
              input_value=$SOGECOMMERCE_SOFORT_AMOUNTS
            }
            <p>{l s='Define amount restriction for each customer group.' mod='sogecommerce'}</p>
          </div>
        </fieldset>
        <div class="clear">&nbsp;</div>
      </div>
    {/if}

    <h4 style="font-weight: bold; margin-bottom: 0; overflow: hidden; line-height: unset !important;">
      <a href="#">{l s='OTHER PAYMENT MEANS' mod='sogecommerce'}</a>
    </h4>
    <div>
      <fieldset>
        <legend>{l s='MODULE OPTIONS' mod='sogecommerce'}</legend>

        <label for="SOGECOMMERCE_OTHER_ENABLED">{l s='Activation' mod='sogecommerce'}</label>
        <div class="margin-form">
          <select id="SOGECOMMERCE_OTHER_ENABLED" name="SOGECOMMERCE_OTHER_ENABLED">
            {foreach from=$sogecommerce_enable_disable_options key="key" item="option"}
              <option value="{$key|escape:'html':'UTF-8'}"{if $SOGECOMMERCE_OTHER_ENABLED === $key} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
            {/foreach}
          </select>
          <p>{l s='Enables / disables this payment method.' mod='sogecommerce'}</p>
        </div>

        <label>{l s='Payment method title' mod='sogecommerce'}</label>
        <div class="margin-form">
          {include file="./input_text_lang.tpl"
            languages=$prestashop_languages
            current_lang=$prestashop_lang
            input_name="SOGECOMMERCE_OTHER_TITLE"
            input_value=$SOGECOMMERCE_OTHER_TITLE
            style="width: 330px;"
          }
          <p>{l s='Method title to display on payment means page. Used only if « Regroup payment means » option is enabled.' mod='sogecommerce'}</p>
        </div>
      </fieldset>
      <div class="clear">&nbsp;</div>

      <fieldset>
        <legend>{l s='RESTRICTIONS' mod='sogecommerce'}</legend>

        <label for="SOGECOMMERCE_OTHER_COUNTRY">{l s='Restrict to some countries' mod='sogecommerce'}</label>
        <div class="margin-form">
          <select id="SOGECOMMERCE_OTHER_COUNTRY" name="SOGECOMMERCE_OTHER_COUNTRY" onchange="javascript: sogecommerceCountriesRestrictMenuDisplay('SOGECOMMERCE_OTHER_COUNTRY')">
            {foreach from=$sogecommerce_countries_options key="key" item="option"}
              <option value="{$key|escape:'html':'UTF-8'}"{if $SOGECOMMERCE_OTHER_COUNTRY === (string)$key} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
            {/foreach}
          </select>
          <p>{l s='Buyer\'s billing countries in which this payment method is available.' mod='sogecommerce'}</p>
        </div>

        <div id="SOGECOMMERCE_OTHER_COUNTRY_MENU" {if $SOGECOMMERCE_OTHER_COUNTRY === '1'} style="display: none;"{/if}>
        <label for="SOGECOMMERCE_OTHER_COUNTRY_LST">{l s='Authorized countries' mod='sogecommerce'}</label>
        <div class="margin-form">
          <select id="SOGECOMMERCE_OTHER_COUNTRY_LST" name="SOGECOMMERCE_OTHER_COUNTRY_LST[]" multiple="multiple" size="7">
            {foreach from=$sogecommerce_countries_list['ps_countries'] key="key" item="option"}
              <option value="{$key|escape:'html':'UTF-8'}"{if in_array($key, $SOGECOMMERCE_OTHER_COUNTRY_LST)} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
            {/foreach}
          </select>
        </div>
        </div>

        <label>{l s='Customer group amount restriction' mod='sogecommerce'}</label>
        <div class="margin-form">
          {include file="./table_amount_group.tpl"
            groups=$prestashop_groups
            input_name="SOGECOMMERCE_OTHER_AMOUNTS"
            input_value=$SOGECOMMERCE_OTHER_AMOUNTS
          }
          <p>{l s='Define amount restriction for each customer group.' mod='sogecommerce'}</p>
        </div>
      </fieldset>
      <div class="clear sogecommerce-grouped">&nbsp;</div>

      <fieldset>
        <legend>{l s='PAYMENT OPTIONS' mod='sogecommerce'}</legend>

        <label for="SOGECOMMERCE_OTHER_GROUPED_VIEW">{l s='Regroup payment means ' mod='sogecommerce'}</label>
        <div class="margin-form">
          <select id="SOGECOMMERCE_OTHER_GROUPED_VIEW" name="SOGECOMMERCE_OTHER_GROUPED_VIEW" onchange="javascript: sogecommerceGroupedViewChanged();">
            {foreach from=$sogecommerce_enable_disable_options key="key" item="option"}
              <option value="{$key|escape:'html':'UTF-8'}"{if $SOGECOMMERCE_OTHER_GROUPED_VIEW === $key} selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
            {/foreach}
          </select>
          <p>{l s='If this option is enabled, all the payment means added in this section will be displayed within the same payment submodule.' mod='sogecommerce'}</p>
        </div>

        <label>{l s='Payment means' mod='sogecommerce'}</label>
        <div class="margin-form">
          {assign var=merged_array_cards value=$sogecommerce_payment_cards_options}
          {assign var=VALID_SOGECOMMERCE_EXTRA_PAYMENT_MEANS value=[]}
          {foreach from=$SOGECOMMERCE_EXTRA_PAYMENT_MEANS key="key_card" item="option_card"}
              {if ! isset($merged_array_cards[$option_card.code])}
                  {append var='merged_array_cards' value=$option_card.title index=$option_card.code}
                  {$VALID_SOGECOMMERCE_EXTRA_PAYMENT_MEANS.$key_card = $option_card}
              {/if}
          {/foreach}

          <script type="text/html" id="sogecommerce_other_payment_means_row_option">
            {include file="./row_other_payment_means_option.tpl"
              payment_means_cards=$merged_array_cards
              countries_list=$sogecommerce_countries_list['ps_countries']
              validation_mode_options=$sogecommerce_validation_mode_options
              enable_disable_options=$sogecommerce_enable_disable_options
              languages=$prestashop_languages
              current_lang=$prestashop_lang
              key="SOGECOMMERCE_OTHER_PAYMENT_SCRIPT_MEANS_KEY"
              option=$sogecommerce_default_other_payment_means_option
            }
          </script>

          <button type="button" id="sogecommerce_other_payment_means_options_btn"{if !empty($SOGECOMMERCE_OTHER_PAYMENT_MEANS)} style="display: none;"{/if} onclick="javascript: sogecommerceAddOtherPaymentMeansOption(true, '{l s='Delete' mod='sogecommerce'}');">{l s='Add' mod='sogecommerce'}</button>

          <table id="sogecommerce_other_payment_means_options_table"{if empty($SOGECOMMERCE_OTHER_PAYMENT_MEANS)} style="display: none;"{/if} class="table" cellpadding="10" cellspacing="0">
          <thead>
            <tr>
              <th style="font-size: 10px;">{l s='Label' mod='sogecommerce'}</th>
              <th style="font-size: 10px;">{l s='Means of payment' mod='sogecommerce'}</th>
              <th style="font-size: 10px;">{l s='Countries' mod='sogecommerce'}</th>
              <th style="font-size: 10px;">{l s='Min amount' mod='sogecommerce'}</th>
              <th style="font-size: 10px;">{l s='Max amount' mod='sogecommerce'}</th>
              <th style="font-size: 10px;">{l s='Capture' mod='sogecommerce'}</th>
              <th style="font-size: 10px;">{l s='Validation mode' mod='sogecommerce'}</th>
              <th style="font-size: 10px;">{l s='Cart data' mod='sogecommerce'}</th>
              <th style="font-size: 10px; {if $SOGECOMMERCE_STD_CARD_DATA_MODE !== '7' && $SOGECOMMERCE_STD_CARD_DATA_MODE !== '8' && $SOGECOMMERCE_STD_CARD_DATA_MODE !== '9'} display: none;{/if}" class="SOGECOMMERCE_OTHER_PAYMENT_MEANS_EMBEDDED">{l s='Integrated mode' mod='sogecommerce'}</th>
              <th style="font-size: 10px;"></th>
            </tr>
          </thead>

          <tbody>
            {foreach from=$SOGECOMMERCE_OTHER_PAYMENT_MEANS key="key" item="option"}
              {include file="./row_other_payment_means_option.tpl"
                payment_means_cards=$merged_array_cards
                countries_list=$sogecommerce_countries_list['ps_countries']
                validation_mode_options=$sogecommerce_validation_mode_options
                enable_disable_options=$sogecommerce_enable_disable_options
                languages=$prestashop_languages
                current_lang=$prestashop_lang
                key=$key
                option=$option
              }
            {/foreach}

            <tr id="sogecommerce_other_payment_means_option_add">
              <td colspan="8"></td>
              <td>
                <button type="button" onclick="javascript: sogecommerceAddOtherPaymentMeansOption(false, '{l s='Delete' mod='sogecommerce'}');">{l s='Add' mod='sogecommerce'}</button>
              </td>
            </tr>
          </tbody>
          </table>

          {if empty($SOGECOMMERCE_OTHER_PAYMENT_MEANS)}
            <input type="hidden" id="SOGECOMMERCE_OTHER_PAYMENT_MEANS" name="SOGECOMMERCE_OTHER_PAYMENT_MEANS" value="">
          {/if}

          <p>
            {l s='Click on « Add » button to configure one or more payment means.' mod='sogecommerce'}<br />
            <b>{l s='Label' mod='sogecommerce'} : </b>{l s='The label of the means of payment to display on your site.' mod='sogecommerce'}<br />
            <b>{l s='Means of payment' mod='sogecommerce'} : </b>{l s='Choose the means of payment you want to propose.' mod='sogecommerce'}<br />
            <b>{l s='Countries' mod='sogecommerce'} : </b>{l s='Countries where the means of payment will be available. Keep blank to authorize all countries.' mod='sogecommerce'}<br />
            <b>{l s='Min amount' mod='sogecommerce'} : </b>{l s='Minimum amount to enable the means of payment.' mod='sogecommerce'}<br />
            <b>{l s='Max amount' mod='sogecommerce'} : </b>{l s='Maximum amount to enable the means of payment.' mod='sogecommerce'}<br />
            <b>{l s='Capture' mod='sogecommerce'} : </b>{l s='The number of days before the bank capture. Enter value only if different from « Base settings ».' mod='sogecommerce'}<br />
            <b>{l s='Validation mode' mod='sogecommerce'} : </b>{l s='If manual is selected, you will have to confirm payments manually in your bank Back Office.' mod='sogecommerce'}<br />
            <b>{l s='Cart data' mod='sogecommerce'} : </b>{l s='If you disable this option, the shopping cart details will not be sent to the gateway. Attention, in some cases, this option has to be enabled. For more information, refer to the module documentation.' mod='sogecommerce'}<br />
            <b style="{if $SOGECOMMERCE_STD_CARD_DATA_MODE !== '7' && $SOGECOMMERCE_STD_CARD_DATA_MODE !== '8' && $SOGECOMMERCE_STD_CARD_DATA_MODE !== '9'}display: none;{/if}" class="SOGECOMMERCE_OTHER_PAYMENT_MEANS_EMBEDDED">{l s='Integrated mode' mod='sogecommerce'} : </b><span style="{if $SOGECOMMERCE_STD_CARD_DATA_MODE !== '7' && $SOGECOMMERCE_STD_CARD_DATA_MODE !== '8' && $SOGECOMMERCE_STD_CARD_DATA_MODE !== '9'}display: none;{/if}" class="SOGECOMMERCE_OTHER_PAYMENT_MEANS_EMBEDDED">{l s='If you enable this option, the payment mean will be displayed in the embedded payment fields. Attention, not all available payment means are supported by the embedded payment fields. For more information, refer to the module documentation.' mod='sogecommerce'}</span><br />
            <b>{l s='Do not forget to click on « Save » button to save your modifications.' mod='sogecommerce'}</b>
          </p>
        </div>

        <label>{l s='Add payment means' mod='sogecommerce'}</label>
        <div class="margin-form">
          <script type="text/html" id="sogecommerce_add_payment_means_row_option">
            {include file="./row_extra_means_of_payment.tpl"
              key="SOGECOMMERCE_EXTRA_PAYMENT_MEANS_SCRIPT_KEY"
              option=$sogecommerce_default_extra_payment_means_option
            }
          </script>

          <button type="button" id="sogecommerce_extra_payment_means_options_btn"{if !empty($VALID_SOGECOMMERCE_EXTRA_PAYMENT_MEANS)} style="display: none;"{/if} onclick="javascript: sogecommerceAddExtraPaymentMeansOption(true, '{l s='Delete' mod='sogecommerce'}');">{l s='Add' mod='sogecommerce'}</button>

          <table id="sogecommerce_extra_payment_means_options_table"{if empty($VALID_SOGECOMMERCE_EXTRA_PAYMENT_MEANS)} style="display: none;"{/if} class="table" cellpadding="10" cellspacing="0">
          <thead>
            <tr>
              <th style="font-size: 10px;">{l s='Code' mod='sogecommerce'}</th>
              <th style="font-size: 10px; width: 350px;">{l s='Label' mod='sogecommerce'}</th>
              <th style="font-size: 10px;">{l s='Action' mod='sogecommerce'}</th>
            </tr>
          </thead>

          <tbody>
            {foreach from=$VALID_SOGECOMMERCE_EXTRA_PAYMENT_MEANS key="key" item="option"}
                {include file="./row_extra_means_of_payment.tpl"
                    key=$key
                    option=$option
                }
            {/foreach}

            <tr id="sogecommerce_extra_payment_means_option_add">
              <td colspan="2"></td>
              <td>
                <button type="button" onclick="javascript: sogecommerceAddExtraPaymentMeansOption(false, '{l s='Delete' mod='sogecommerce'}');">{l s='Add' mod='sogecommerce'}</button>
              </td>
            </tr>
          </tbody>
          </table>

          {if empty($VALID_SOGECOMMERCE_EXTRA_PAYMENT_MEANS)}
            <input type="hidden" id="SOGECOMMERCE_EXTRA_PAYMENT_MEANS" name="SOGECOMMERCE_EXTRA_PAYMENT_MEANS" value="">
          {/if}

          <p>
            {l s='Click on « Add » button to add one or more new payment means.' mod='sogecommerce'}<br />
            <b>{l s='Code' mod='sogecommerce'} : </b>{l s='The code of the means of payment as expected by %s gateway.' sprintf='Sogecommerce' mod='sogecommerce'}<br />
            <b>{l s='Label' mod='sogecommerce'} : </b>{l s='The default label of the means of payment.' mod='sogecommerce'}<br />
            <b>{l s='Do not forget to click on « Save » button to save your modifications.' mod='sogecommerce'}</b>
          </p>
        </div>
      </fieldset>
      <div class="clear">&nbsp;</div>
    </div>

   </div>

  {if version_compare($smarty.const._PS_VERSION_, '1.6', '<')}
    <div class="clear" style="width: 100%;">
      <input type="submit" class="button" name="sogecommerce_submit_admin_form" value="{l s='Save' mod='sogecommerce'}" style="float: right;">
    </div>
  {else}
    <div class="panel-footer" style="width: 100%;">
      <button type="submit" value="1" name="sogecommerce_submit_admin_form" class="btn btn-default pull-right" style="float: right !important;">
        <i class="process-icon-save"></i>
        {l s='Save' mod='sogecommerce'}
      </button>
    </div>
  {/if}
</form>

<br />
<br />