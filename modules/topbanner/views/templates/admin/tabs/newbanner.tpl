{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 *}
<h3><i class="icon-indent"></i> {l s='Create a new banner' mod='topbanner'} </h3>

<form id="topbanner_form" class="form-horizontal" method="post">

  {if isset($id_banner_edit)}
    <input type="hidden" name="topbanner_banner_id" id="topbanner_banner_id" value="{$id_banner_edit|escape:'htmlall':'UTF-8'}">
  {/if}

  <div class="form-group">
    <label for="topbanner_banner_name" class="col-xs-12 col-sm-2">{l s='Banner name' mod='topbanner'} <sup>*</sup></label>
    <div class="col-xs-12 col-sm-3">
      <input type="text" value="{if isset($banner_edit)}{$banner_edit['name']|escape:'htmlall':'UTF-8'}{/if}" name="topbanner_banner_name" class="topbanner_banner_name" id="topbanner_banner_name" required>
    </div>
  </div>

  <div class="form-group">
    <label for="topbanner_banner_height" class="col-xs-12 col-sm-2">{l s='Banner height' mod='topbanner'} <sup>*</sup></label>
    <div class="col-xs-12 col-sm-3">
      <input type="text" value="{if isset($banner_edit)}{$banner_edit['height']|escape:'htmlall':'UTF-8'}{/if}" name="topbanner_banner_height" class="topbanner_banner_height" id="topbanner_banner_height" required>
    </div>
  </div>

  <div class="form-group">
    <label for="topbanner_banner_background" class="col-xs-12 col-sm-2">{l s='Background color' mod='topbanner'}</label>
    <div class="col-xs-12 col-sm-3">
      <div class="input-group fixed-width-lg">
        <input type="color" data-hex="true" class="color mColorPickerInput mColorPicker topbanner_banner_background" value="{if isset($banner_edit)}{$banner_edit['background']|escape:'htmlall':'UTF-8'}{else}#000000{/if}" name="topbanner_banner_background" id="topbanner_banner_background">
      </div>
    </div>
  </div>

  <div class="form-group">
    <label for="topbanner_banner_type" class="col-xs-12 col-sm-2">{l s='Choose your banner type' mod='topbanner'}</label>
    <div class="col-xs-12 col-sm-10">
      <div class="btn-group" role="group" aria-label="...">
        {foreach from=$bannerTypes key='key' item='bannerType'}
          <button data-type="{$key|escape:'htmlall':'UTF-8'}" type="button" class="btn btn-default topbanner_banner_type bannerTypeBtn {if isset($banner_edit)}{if $banner_edit['type'] == $key}active{/if}{elseif $key == 1}active{/if}">{$bannerType|escape:'htmlall':'UTF-8'}</button>
        {/foreach}
        <input type="hidden" name="topbanner_banner_type" id="topbanner_banner_type" value="{if isset($banner_edit)}{$banner_edit['type']|escape:'htmlall':'UTF-8'}{else}1{/if}">
      </div>
    </div>
    <div class="col-xs-12 col-sm-10 col-sm-offset-2">
      <em class="information-info {if isset($banner_edit)}{if $banner_edit['type'] != 1}hidden{/if}{/if}">{l s='Allows you to highlight general information about your store: new collection launch, upcoming events, etc.' mod='topbanner'}</em><br class="information-info {if isset($banner_edit)}{if $banner_edit['type'] != 1}hidden{/if}{/if}">
      <em class="information-freeshipping {if isset($banner_edit)}{if $banner_edit['type'] != 2}hidden{/if}{else}hidden{/if}">{l s='Allows you to inform your visitors of the condition for your free shipping offer' mod='topbanner'}</em><br class="information-freeshipping {if isset($banner_edit)}{if $banner_edit['type'] != 2}hidden{/if}{else}hidden{/if}">
      <em class="information-sales {if isset($banner_edit)}{if $banner_edit['type'] != 3}hidden{/if}{else}hidden{/if}">{l s='Allows you to promote your promotions, special offers of flash sales' mod='topbanner'}</em><br class="information-sales {if isset($banner_edit)}{if $banner_edit['type'] != 3}hidden{/if}{else}hidden{/if}">
    </div>
  </div>

  <div class="form-group {if isset($banner_edit)}{if $banner_edit['type'] != 2}hidden{/if}{else}hidden{/if} subtype_freeshipping">
    <label for="topbanner_banner_subtype" class="col-xs-12 col-sm-2">{l s='Choose your banner subtype' mod='topbanner'}</label>
    <div class="col-xs-12 col-sm-10">
      <div class="btn-group" role="group" aria-label="...">
        {foreach from=$bannerSubTypes key='key' item='bannerSubType'}
          <button data-type="{$key|escape:'htmlall':'UTF-8'}" type="button" class="btn btn-default topbanner_banner_subtype bannerSubTypeBtn {if isset($banner_edit) && $banner_edit['subtype'] == $key}active{/if}">{$bannerSubType|escape:'htmlall':'UTF-8'}</button>
        {/foreach}
        <input type="hidden" name="topbanner_banner_subtype" id="topbanner_banner_subtype" value="{if isset($banner_edit)}{$banner_edit['subtype']|escape:'htmlall':'UTF-8'}{/if}">
      </div>
    </div>
    <div class="col-xs-12 col-sm-10 col-sm-offset-2">
      <em class="cart-rule-info {if isset($banner_edit)}{if $banner_edit['subtype'] != 1}hidden{/if}{else}hidden{/if}">{l s='To add a new cart rule or modify your existing cart rules:' mod='topbanner'}</em><br class="cart-rule-info hidden">
      {if isset($psversion) && $psversion == '17'}<em class="cart-rule-info {if isset($banner_edit)}{if $banner_edit['subtype'] != 1}hidden{/if}{else}hidden{/if}">{l s='Click on the Catalog tab in the left-hand column of your PrestaShop back office, then choose Promotions' mod='topbanner'}</em><br class="cart-rule-info hidden">{/if}
      {if isset($psversion) && $psversion == '16'}<em class="cart-rule-info {if isset($banner_edit)}{if $banner_edit['subtype'] != 1}hidden{/if}{else}hidden{/if}">{l s='Click on Price Rules tab > Cart Rules' mod='topbanner'}</em><br class="cart-rule-info hidden">{/if}
    </div>
  </div>

  <div class="form-group {if isset($banner_edit)}{if $banner_edit['type'] != 3}hidden{/if}{else}hidden{/if} subtype_cartrule_sales">
    <label for="topbanner_banner_subtype_sales" class="col-sm-2">{l s='Choose your cart rule' mod='topbanner'}</label>
    <div class="col-sm-6">
      <select class="form-control" name="topbanner_banner_subtype_sales" id="topbanner_banner_subtype_sales">
        {foreach from=$salesCartRules item='cartRule'}
        <option value="{$cartRule['id_cart_rule']|escape:'htmlall':'UTF-8'}" {if isset($banner_edit) && $banner_edit['cartrule'] == $cartRule['id_cart_rule']}selected{/if}>
        {$cartRule['name']|escape:'htmlall':'UTF-8'} - {if $cartRule['code'] != ''}{l s='Voucher code' mod='topbanner'} {$cartRule['code']|escape:'htmlall':'UTF-8'}{else}{l s='No voucher code' mod='topbanner'}{/if} - {l s='End date' mod='topbanner'} {$cartRule['date_to']|escape:'htmlall':'UTF-8'}
          </option>
        {/foreach}
      </select>
    </div>
  </div>

  <div class="form-group {if isset($banner_edit)}{if $banner_edit['type'] == 2 && $banner_edit['subtype'] == 1}{else}hidden{/if}{else}hidden{/if} subtype_cartrule">
    <label for="topbanner_banner_cartrule" class="col-sm-2">{l s='Choose your cart rule' mod='topbanner'}</label>
    <div class="col-sm-6">
      <select class="form-control" name="topbanner_banner_cartrule" id="topbanner_banner_cartrule">
        {foreach from=$freeCartRules item='cartRule'}
          <option value="{$cartRule['id_cart_rule']|escape:'htmlall':'UTF-8'}" {if isset($banner_edit) && $banner_edit['cartrule'] == $cartRule['id_cart_rule']}selected{/if}>
            {$cartRule['name']|escape:'htmlall':'UTF-8'} - {if $cartRule['code'] != ''}{l s='Voucher code' mod='topbanner'} {$cartRule['code']|escape:'htmlall':'UTF-8'}{else}{l s='No voucher code' mod='topbanner'}{/if} - {l s='End date' mod='topbanner'} {$cartRule['date_to']|escape:'htmlall':'UTF-8'}
          </option>
        {/foreach}
      </select>
    </div>
  </div>

  <div class="form-group {if isset($banner_edit)}{if $banner_edit['type'] == 1 || ($banner_edit['type'] == 2 && $banner_edit['subtype'] == 2)}hidden{/if}{else}hidden{/if} subtype_cartrule timer_choice">
    <label for="topbanner_banner_timer" class="col-sm-2">{l s='Display a timer' mod='topbanner'} </label>
    <div class="col-sm-3">
      <span class="switch prestashop-switch fixed-width-lg">
        <input type="radio" value="1" name="topbanner_banner_timer" id="topbanner_banner_timer_on" {if isset($banner_edit) && $banner_edit['timer'] == 1}checked="checked"{/if}>
        <label for="topbanner_banner_timer_on">{l s='Yes' mod='topbanner'}</label>
        <input type="radio" value="0" name="topbanner_banner_timer" id="topbanner_banner_timer_off" {if isset($banner_edit)}{if $banner_edit['timer'] == 0}checked="checked"{/if}{else}checked="checked"{/if}>
        <label for="topbanner_banner_timer_off">{l s='No' mod='topbanner'}</label>
        <a class="slide-button btn"></a>
      </span>
    </div>
    <div class="col-sm-3 {if isset($banner_edit)}{if $banner_edit['type'] == 1}hidden{/if}{else}hidden{/if} subtype_cartrule timer_choice">&nbsp;</div>
    <div class="col-sm-9 {if isset($banner_edit)}{if $banner_edit['type'] == 1}hidden{/if}{else}hidden{/if} subtype_cartrule timer_choice">
      <em>{l s='For a better display on mobile, we recommend you to reduce the number of characters on your banner' mod='topbanner'}</em>
    </div>
  </div>

  {foreach from=$languages item=language}
    {if $languages|count > 1}
      <div class="translatable-field lang-{$language.id_lang|escape:'htmlall':'UTF-8'}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
      {/if}
      <div class="form-group {if isset($banner_edit)}{if $banner_edit['timer'] != 1}hidden{/if}{else}hidden{/if} timer_group timer_group_wrapper">
        <div class="col-sm-2">
          <label for="topbanner_banner_timer_left_text">{l s='Add your text:' mod='topbanner'} </label>
        </div>
        <div class="col-xs-10 col-sm-3">
          <input type="text" value="{if isset($banner_edit)}{$banner_edit["timer_left_text-{$language.id_lang|escape:'htmlall':'UTF-8'}"]|escape:'htmlall':'UTF-8'}{/if}" name="topbanner_banner_timer_left_text-{$language.id_lang|escape:'htmlall':'UTF-8'}" class="topbanner_banner_timer_left_text multilang_input" id="topbanner_banner_timer_left_text-{$language.id_lang|escape:'htmlall':'UTF-8'}">
        </div>
        {if $languages|count > 1}
          <div class=" col-xs-1">
            <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
              {$language.iso_code|escape:'htmlall':'UTF-8'}
              <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
              {foreach from=$languages item=lang}
                <li><a href="javascript:hideOtherLanguage({$lang.id_lang|escape:'htmlall':'UTF-8'});" tabindex="-1">{$lang.name|escape:'htmlall':'UTF-8'}</a></li>
              {/foreach}
            </ul>
          </div>
        {/if}
        <div class="col-xs-12 col-sm-1">
          <input type="text" value="{l s='Timer' mod='topbanner'}" disabled>
        </div>
        <div class="col-xs-10 col-sm-3">
          <input type="text" value="{if isset($banner_edit)}{$banner_edit["timer_right_text-{$language.id_lang|escape:'htmlall':'UTF-8'}"]|escape:'htmlall':'UTF-8'}{/if}" name="topbanner_banner_timer_right_text-{$language.id_lang|escape:'htmlall':'UTF-8'}" class="topbanner_banner_timer_right_text multilang_input" id="topbanner_banner_timer_right_text-{$language.id_lang|escape:'htmlall':'UTF-8'}">
        </div>
        {if $languages|count > 1}
          <div class="col-xs-1">
            <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
              {$language.iso_code|escape:'htmlall':'UTF-8'}
              <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
              {foreach from=$languages item=lang}
                <li><a href="javascript:hideOtherLanguage({$lang.id_lang|escape:'htmlall':'UTF-8'});" tabindex="-1">{$lang.name|escape:'htmlall':'UTF-8'}</a></li> 
              {/foreach}
            </ul>
          </div>
        {/if}
      </div>
      <div class="col-xs-12 col-sm-2 {if isset($banner_edit)}{if $banner_edit['timer'] != 1}hidden{/if}{else}hidden{/if} timer_group">&nbsp;</div>
      <div class="col-xs-12 col-sm-9 col-sm-offset-2 {if isset($banner_edit)}{if $banner_edit['timer'] != 1}hidden{/if}{else}hidden{/if} timer_group">
        <em>{l s='Maximum characters recommended on each side of the timer: 20' mod='topbanner'}</em><br>
        <em>{l s='The timer will be automatically set up based on your selected cart rule. Once it reaches its end, the banner will disappear from your website.' mod='topbanner'}</em>
      </div>
      {if $languages|count > 1}
      </div>
    {/if}
  {/foreach}

  <div class="form-group {if isset($banner_edit)}{if $banner_edit['timer'] != 1}hidden{/if}{else}hidden{/if} timer_group">
    <label for="topbanner_banner_timer_background" class="col-xs-12 col-sm-2">{l s='Timer background color' mod='topbanner'} </label>
    <div class="col-xs-12 col-sm-3">
      <div class="input-group fixed-width-lg">
        <input type="color" data-hex="true" class="color mColorPickerInput mColorPicker" value="{if isset($banner_edit)}{$banner_edit['timer_background']|escape:'htmlall':'UTF-8'}{else}#000000{/if}" name="topbanner_banner_timer_background" id="topbanner_banner_timer_background"/>
      </div>
    </div>
  </div>

  <div class="form-group {if isset($banner_edit)}{if $banner_edit['timer'] != 1}hidden{/if}{else}hidden{/if} timer_group">
    <label for="topbanner_banner_timer_text_color" class="col-xs-12 col-sm-2">{l s='Timer text color' mod='topbanner'} </label>
    <div class="col-xs-12 col-sm-3">
      <div class="input-group fixed-width-lg">
        <input type="color" data-hex="true" class="color mColorPickerInput mColorPicker" value="{if isset($banner_edit)}{$banner_edit['timer_text_color']|escape:'htmlall':'UTF-8'}{else}#FFFFFF{/if}" name="topbanner_banner_timer_text_color" id="topbanner_banner_timer_text_color"/>
      </div>
    </div>
  </div>

  <div class="form-group {if isset($banner_edit)}{if $banner_edit['type'] == 2 && $banner_edit['subtype'] == 2}{else}hidden{/if}{else}hidden{/if} text_group_carrier">
    <div class="alert alert-info" role="alert">{l s='Reminder, your free shipping carrier preference is: ' mod='topbanner'} {$shippingFreePrice|escape:'htmlall':'UTF-8'}</div>
    <em>{l s='To change your carrier preference, click on Shipping in the left column of your back office and choose Preferences' mod='topbanner'}</em><br>
    <em>{l s='Use the {{currency}} tag to display the currency your customer has selected in front office.' mod='topbanner'}</em>
  </div>

  {* CHANGE THIS INPUT WITH NEW TEXT EDITOR *}
  {foreach from=$languages item=language}
    {if $languages|count > 1}
      <div class="translatable-field lang-{$language.id_lang|escape:'htmlall':'UTF-8'}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
      {/if}
      <div class="form-group {if isset($banner_edit)}{if $banner_edit['type'] == 2 && $banner_edit['subtype'] == 2}{else}hidden{/if}{else}hidden{/if} text_group_carrier">
        <label for="topbanner_banner_text_carrier_empty" class="col-xs-12 col-sm-2">{l s='Add text for empty cart' mod='topbanner'} <sup>*</sup></label>
        <div class="col-xs-12 col-sm-3">
          <input type="text" value="{if isset($banner_edit)}{$banner_edit["text_carrier_empty-{$language.id_lang|escape:'htmlall':'UTF-8'}"]|escape:'htmlall':'UTF-8'}{/if}" name="topbanner_banner_text_carrier_empty-{$language.id_lang|escape:'htmlall':'UTF-8'}" class="topbanner_banner_text_carrier_empty multilang_input" id="topbanner_banner_text_carrier_empty-{$language.id_lang|escape:'htmlall':'UTF-8'}">
        </div>
        {if $languages|count > 1}
          <div class="col-xs-12 col-sm-2">
            <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
              {$language.iso_code|escape:'htmlall':'UTF-8'}
              <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
              {foreach from=$languages item=lang}
                <li><a href="javascript:hideOtherLanguage({$lang.id_lang|escape:'htmlall':'UTF-8'});" tabindex="-1">{$lang.name|escape:'htmlall':'UTF-8'}</a></li>  
              {/foreach}
            </ul>
          </div>
        {/if}
        <div class="col-xs-12 col-sm-9 col-sm-offset-2 text_group_carrier">
          <em>{l s='Example: Free shipping on orders of $50 or over' mod='topbanner'}</em>
        </div>
      </div>
      {if $languages|count > 1}
      </div>
    {/if}
  {/foreach}

  {* CHANGE THIS INPUT WITH NEW TEXT EDITOR *}
  {foreach from=$languages item=language}
    {if $languages|count > 1}
      <div class="translatable-field lang-{$language.id_lang|escape:'htmlall':'UTF-8'}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
		{/if}
		<div class="form-group {if isset($banner_edit)}{if $banner_edit['type'] == 2 && $banner_edit['subtype'] == 2}{else}hidden{/if}{else}hidden{/if} text_group_carrier">
			<label for="topbanner_banner_text_carrier_between" class="col-xs-12 col-sm-2 smaller-lineheight">{l s='Add text for carts below the minimum amount for free shipping' mod='topbanner'} <sup>*</sup></label>
      <div class="col-xs-12 col-sm-3">
        <input type="text" value="{if isset($banner_edit)}{$banner_edit["text_carrier_between-{$language.id_lang|escape:'htmlall':'UTF-8'}"]|escape:'htmlall':'UTF-8'}{/if}" name="topbanner_banner_text_carrier_between-{$language.id_lang|escape:'htmlall':'UTF-8'}" class="topbanner_banner_text_carrier_between multilang_input" id="topbanner_banner_text_carrier_between-{$language.id_lang|escape:'htmlall':'UTF-8'}">
        </div>
        {if $languages|count > 1}
          <div class="col-xs-12 col-sm-2">
            <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
              {$language.iso_code|escape:'htmlall':'UTF-8'}
              <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
              {foreach from=$languages item=lang}
                <li><a href="javascript:hideOtherLanguage({$lang.id_lang|escape:'htmlall':'UTF-8'});" tabindex="-1">{$lang.name|escape:'htmlall':'UTF-8'}</a></li>
              {/foreach}
            </ul>
          </div>
        {/if}
        <div class="col-xs-12 col-sm-9 col-sm-offset-2 text_group_carrier">
          <em>{l s='Use the {{price}} tag to display automatically the remaining amount before free shipping' mod='topbanner'}</em><br>
          <em>{l s='Example: "Only {{price}} {{currency}} left before free shipping". What your visitors will see: "Only 19€ left before free shipping", depending on the value of their shopping cart' mod='topbanner'}</em>
        </div>
      </div>
      {if $languages|count > 1}
      </div>
    {/if}
  {/foreach}

  {* CHANGE THIS INPUT WITH NEW TEXT EDITOR *}
  {foreach from=$languages item=language}
    {if $languages|count > 1}
      <div class="translatable-field lang-{$language.id_lang|escape:'htmlall':'UTF-8'}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
      {/if}
      <div class="form-group {if isset($banner_edit)}{if $banner_edit['type'] == 2 && $banner_edit['subtype'] == 2}{else}hidden{/if}{else}hidden{/if} text_group_carrier">
        <label for="topbanner_banner_text_carrier_full" class="col-sm-2">{l s='Add text for free shipping fees' mod='topbanner'} <sup>*</sup></label>
        <div class="col-xs-12 col-sm-3">
          <input type="text" value="{if isset($banner_edit)}{$banner_edit["text_carrier_full-{$language.id_lang|escape:'htmlall':'UTF-8'}"]|escape:'htmlall':'UTF-8'}{/if}" name="topbanner_banner_text_carrier_full-{$language.id_lang|escape:'htmlall':'UTF-8'}" class="topbanner_banner_text_carrier_full multilang_input" id="topbanner_banner_text_carrier_full-{$language.id_lang|escape:'htmlall':'UTF-8'}">
        </div>
        {if $languages|count > 1}
          <div class="col-xs-12 col-sm-2">
            <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
              {$language.iso_code|escape:'htmlall':'UTF-8'}
              <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
              {foreach from=$languages item=lang}
                <li><a href="javascript:hideOtherLanguage({$lang.id_lang|escape:'htmlall':'UTF-8'});" tabindex="-1">{$lang.name|escape:'htmlall':'UTF-8'}</a></li>
              {/foreach}
            </ul>
          </div>
        {/if}
        <div class="col-xs-12 col-sm-9 col-sm-offset-2 text_group_carrier">
          <em>{l s='Example: "Congratulations ! Your shipping fees are now free !"' mod='topbanner'}</em>
        </div>
      </div>
      {if $languages|count > 1}
      </div>
    {/if}
  {/foreach}


  <div class="form-group">
    <label for="topbanner_banner_text_size" class="col-sm-2">{l s='Text size' mod='topbanner'}</label>
    <div class="col-xs-12 col-sm-3">
      <select name="topbanner_banner_text_size" id="topbanner_banner_text_size">
        {for $foo=12 to 30}
          {if $foo % 2 == 0}
            <option value="{$foo|escape:'htmlall':'UTF-8'}" {if isset($banner_edit) && $banner_edit['text_size'] == $foo}selected{/if}>{$foo|escape:'htmlall':'UTF-8'} px</option>
          {/if}
        {/for}
      </select>
    </div>
  </div>

  {foreach from=$languages item=language}
    {if $languages|count > 1}
      <div class="translatable-field lang-{$language.id_lang|escape:'htmlall':'UTF-8'}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
      {/if}
      <div class="form-group text_group {if isset($banner_edit)}{if ($banner_edit['type'] == 2 && $banner_edit['subtype'] == 2) || $banner_edit['timer'] == 1}hidden{/if}{/if}">
        <label for="topbanner_banner_text" class="col-sm-2">{l s='Add your text:' mod='topbanner'} <sup>*</sup></label>
        <div {if $languages|count > 1}class="col-sm-8"{else}class="col-sm-10"{/if}>
          <textarea
            name="topbanner_banner_text-{$language.id_lang|escape:'htmlall':'UTF-8'}"
            id="topbanner_banner_text-{$language.id_lang|escape:'htmlall':'UTF-8'}"
            class="topbanner_banner_text multilang_input autoload_rte"
          >
            {if isset($banner_edit)}{$banner_edit["text-{$language.id_lang|escape:'htmlall':'UTF-8'}"]|escape:'htmlall':'UTF-8'}{/if}
          </textarea>
        </div>
        {if $languages|count > 1}
          <div class="col-xs-12 col-sm-2">
            <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
              {$language.iso_code|escape:'htmlall':'UTF-8'}
              <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
              {foreach from=$languages item=lang}
                <li><a href="javascript:hideOtherLanguage({$lang.id_lang|escape:'htmlall':'UTF-8'});" tabindex="-1">{$lang.name|escape:'htmlall':'UTF-8'}</a></li>
              {/foreach}
            </ul>
          </div>
        {/if}
        <div class="col-xs-12 col-sm-10 col-sm-offset-2"><em>{l s='Maximum characters recommended in total: 40 for the best display quality on desktop' mod='topbanner'}</em></div>
      </div>
      {if $languages|count > 1}
      </div>
    {/if}
  {/foreach}

  <div class="form-group mobile_choice text_group">
    <label for="topbanner_banner_with_mobile_text" class="col-sm-2">{l s='Alternative text on mobile' mod='topbanner'}</label>
    <div class="col-xs-12 col-sm-3">
      <span class="switch prestashop-switch fixed-width-lg">
        <input type="radio" value="1" name="topbanner_banner_with_mobile_text" id="topbanner_banner_with_mobile_text_on" {if isset($banner_edit) && $banner_edit['with_mobile_text'] == 1}checked="checked"{/if}>
        <label for="topbanner_banner_with_mobile_text_on">{l s='Yes' mod='topbanner'}</label>
        <input type="radio" value="0" name="topbanner_banner_with_mobile_text" id="topbanner_banner_with_mobile_text_off" {if isset($banner_edit)}{if $banner_edit['with_mobile_text'] == 0}checked="checked"{/if}{else}checked="checked"{/if}>
        <label for="topbanner_banner_with_mobile_text_off">{l s='No' mod='topbanner'}</label>
        <a class="slide-button btn"></a>
      </span>
    </div>
  </div>

  <div class="form-group {if isset($banner_edit)}{if $banner_edit['with_mobile_text'] != 1}hidden{/if}{else}hidden{/if} mobile_group">
    <label for="topbanner_banner_mobile_text_size" class="col-sm-2">{l s='Text size' mod='topbanner'}</label>
    <div class="col-xs-12 col-sm-3">
      <select name="topbanner_banner_mobile_text_size" id="topbanner_banner_mobile_text_size">
        {for $foo=12 to 30}
          {if $foo % 2 == 0}
            <option value="{$foo|escape:'htmlall':'UTF-8'}" {if isset($banner_edit) && $banner_edit['mobile_text_size'] == $foo}selected{/if}>{$foo|escape:'htmlall':'UTF-8'} px</option>
          {/if}
        {/for}
      </select>
    </div>
  </div>

  {* Text on mobile *}
  {foreach from=$languages item=language}
    {if $languages|count > 1}
      <div class="translatable-field lang-{$language.id_lang|escape:'htmlall':'UTF-8'}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
  {/if}
  <div class="form-group mobile_group {if isset($banner_edit)}{if $banner_edit['with_mobile_text'] != 1}hidden{/if}{else}hidden{/if}">
    <label for="topbanner_banner_text_mobile" class="col-sm-2">{l s='Add your text:' mod='topbanner'} <sup>*</sup></label>
      <div {if $languages|count > 1}class="col-sm-8"{else}class="col-sm-10"{/if}>
        <textarea
          name="topbanner_banner_text_mobile-{$language.id_lang|escape:'htmlall':'UTF-8'}"
          id="topbanner_banner_text_mobile-{$language.id_lang|escape:'htmlall':'UTF-8'}"
          class="topbanner_banner_text_mobile multilang_input autoload_rte"
        >
          {if isset($banner_edit["mobile_text-{$language.id_lang|escape:'htmlall':'UTF-8'}"])}{$banner_edit["mobile_text-{$language.id_lang|escape:'htmlall':'UTF-8'}"]|escape:'htmlall':'UTF-8'}{/if}
        </textarea>
        </div>
        {if $languages|count > 1}
          <div class="col-sm-2">
            <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
              {$language.iso_code|escape:'htmlall':'UTF-8'}
              <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
              {foreach from=$languages item=lang}
                <li><a href="javascript:hideOtherLanguage({$lang.id_lang|escape:'htmlall':'UTF-8'});" tabindex="-1">{$lang.name|escape:'htmlall':'UTF-8'}</a></li>
              {/foreach}
            </ul>
          </div>
        {/if}
        <div class="col-sm-2">&nbsp;</div>
        <div class="col-sm-9"><em>{l s='Maximum characters recommended in total: 25 for the best display quality on smartphone' mod='topbanner'}</em></div>
      </div>
      {if $languages|count > 1}
      </div>
    {/if}
  {/foreach}
  {* End of Text on mobile *}

  <div class="form-group cta_choice {if isset($banner_edit) && $banner_edit['type'] != 1 && $banner_edit['type'] != 3}hidden{/if}">
    <label for="topbanner_banner_cta" class="col-sm-2">{l s='Add a call to action' mod='topbanner'}</label>
    <div class="col-sm-3">
      <span class="switch prestashop-switch fixed-width-lg">
        <input type="radio" value="1" name="topbanner_banner_cta" id="topbanner_banner_cta_on" {if isset($banner_edit) && $banner_edit['cta'] == 1}checked="checked"{/if}>
        <label for="topbanner_banner_cta_on">{l s='Yes' mod='topbanner'}</label>
        <input type="radio" value="0" name="topbanner_banner_cta" id="topbanner_banner_cta_off" {if isset($banner_edit)}{if $banner_edit['cta'] == 0}checked="checked"{/if}{else}checked="checked"{/if}>
        <label for="topbanner_banner_cta_off">{l s='No' mod='topbanner'}</label>
        <a class="slide-button btn"></a>
      </span>
    </div>
  </div>

  {foreach from=$languages item=language}
    {if $languages|count > 1}
      <div class="translatable-field lang-{$language.id_lang|escape:'htmlall':'UTF-8'}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
      {/if}
      <div class="form-group {if isset($banner_edit)}{if $banner_edit['cta'] != 1}hidden{/if}{else}hidden{/if} cta_group">
        <label for="topbanner_banner_cta_text" class="col-sm-2">{l s='Add your button text' mod='topbanner'} <sup>*</sup></label>
        <div class="col-xs-10 col-sm-3">
          <input type="text" class="topbanner_banner_cta_text multilang_input" value="{if isset($banner_edit)}{$banner_edit["cta_text-{$language.id_lang|escape:'htmlall':'UTF-8'}"]}{/if}" name="topbanner_banner_cta_text-{$language.id_lang|escape:'htmlall':'UTF-8'}" id="topbanner_banner_cta_text-{$language.id_lang|escape:'htmlall':'UTF-8'}">
        </div>
        {if $languages|count > 1}
          <div class="col-xs-2">
            <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
              {$language.iso_code|escape:'htmlall':'UTF-8'}
              <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
              {foreach from=$languages item=lang}
                <li><a href="javascript:hideOtherLanguage({$lang.id_lang|escape:'htmlall':'UTF-8'});" tabindex="-1">{$lang.name|escape:'htmlall':'UTF-8'}</a></li>
              {/foreach}
            </ul>
          </div>
        {/if}
      </div>
      {if $languages|count > 1}
      </div>
    {/if}
  {/foreach}

  {foreach from=$languages item=language}
    {if $languages|count > 1}
      <div class="translatable-field lang-{$language.id_lang|escape:'htmlall':'UTF-8'}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
      {/if}
      <div class="form-group {if isset($banner_edit)}{if $banner_edit['cta'] != 1}hidden{/if}{else}hidden{/if} cta_group">
        <label for="topbanner_banner_cta_link" class="col-sm-2">{l s='Redirect link' mod='topbanner'} <sup>*</sup></label>
        <div class="col-xs-10 col-sm-3">
          <input type="text" value="{if isset($banner_edit)}{$banner_edit["cta_link-{$language.id_lang|escape:'htmlall':'UTF-8'}"]}{/if}" class="topbanner_banner_cta_link multilang_input" name="topbanner_banner_cta_link-{$language.id_lang|escape:'htmlall':'UTF-8'}" id="topbanner_banner_cta_link-{$language.id_lang|escape:'htmlall':'UTF-8'}">
        </div>
        {if $languages|count > 1}
          <div class="col-xs-2">
            <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
              {$language.iso_code|escape:'htmlall':'UTF-8'}
              <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
              {foreach from=$languages item=lang}
                <li><a href="javascript:hideOtherLanguage({$lang.id_lang|escape:'htmlall':'UTF-8'});" tabindex="-1">{$lang.name|escape:'htmlall':'UTF-8'}</a></li>
              {/foreach}
            </ul>
          </div>
        {/if}
        <div class="col-xs-12 col-sm-9 col-sm-offset-2"><em>{l s='Example: http://..., https://...' mod='topbanner'}</em></div>
      </div>
      {if $languages|count > 1}
      </div>
    {/if}
  {/foreach}

  <div class="form-group {if isset($banner_edit)}{if $banner_edit['cta'] != 1}hidden{/if}{else}hidden{/if} cta_group">
    <label for="topbanner_banner_cta_text_color" class="col-sm-2">{l s='Text color' mod='topbanner'}</label>
    <div class="col-sm-3">
      <div class="input-group fixed-width-lg">
        <input type="color" data-hex="true" class="color mColorPickerInput mColorPicker" value="{if isset($banner_edit)}{$banner_edit['cta_text_color']|escape:'htmlall':'UTF-8'}{else}#FFFFFF{/if}" name="topbanner_banner_cta_text_color" id="topbanner_banner_cta_text_color"/>
      </div>
    </div>
  </div>

  <div class="form-group {if isset($banner_edit)}{if $banner_edit['cta'] != 1}hidden{/if}{else}hidden{/if} cta_group">
    <label for="topbanner_banner_cta_background" class="col-sm-2">{l s='Background color' mod='topbanner'}</label>
    <div class="col-sm-3">
      <div class="input-group fixed-width-lg">
        <input type="color" data-hex="true" class="color mColorPickerInput mColorPicker" value="{if isset($banner_edit)}{$banner_edit['cta_background']|escape:'htmlall':'UTF-8'}{else}#000000{/if}" name="topbanner_banner_cta_background" id="topbanner_banner_cta_background"/>
      </div>
    </div>
  </div>

  <div class="form-group">
    <label for="topbanner_preview" class="col-sm-2">{l s='Preview' mod='topbanner'}</label>
    <div class="col-sm-3">
      <button type="button" class="btn btn-default" id="topbanner_preview">{l s='Preview' mod='topbanner'}</button>
      <input type="hidden" id="topbanner_token" value="{$token|escape:'htmlall':'UTF-8'}">
      <input type="hidden" id="topbanner_ajax_url" value="{$controller_url_ajax|escape:'quotes':'UTF-8'}{* Url, no escape *}">
    </div>
  </div>

  <div id="topbanner_preview_wrapper" class="col-sm-12 form-group">

  </div>

  <div class="form-group">
    <label for="topbanner_banner_status" class="col-sm-2">{l s='Status' mod='topbanner'}</label>
    <div class="col-sm-3">
      <span class="switch prestashop-switch fixed-width-lg">
        <input type="radio" value="1" name="topbanner_banner_status" id="topbanner_banner_status_on" {if isset($banner_edit) && $banner_edit['status'] == 1}checked="checked"{/if}>
        <label for="topbanner_banner_status_on">{l s='Yes' mod='topbanner'}</label>
        <input type="radio" value="0" name="topbanner_banner_status" id="topbanner_banner_status_off" {if isset($banner_edit)}{if $banner_edit['status'] == 0}checked="checked"{/if}{else}checked="checked"{/if}>
        <label for="topbanner_banner_status_off">{l s='No' mod='topbanner'}</label>
        <a class="slide-button btn"></a>
      </span>
    </div>
    <div class="col-sm-3">&nbsp;</div>
    <div class="col-sm-9">
      <em>{l s='Choosing yes, the banner you are configuring will automatically replace the existing banner on your website.' mod='topbanner'}</em>
    </div>
  </div>

  <div class="form-group">
    {*        <div class="alert alert-danger hidden" role="alert">{l s='Attention: please fill in all the required fields' mod='topbanner'}</div>*}
    <div class="alert alert-danger hidden invalid-form" role="alert">{l s='Attention: invalid elements in one or several fields, please check your configuration and make sure you filled all fields in the different languages.' mod='topbanner'}</div>
  </div>

  <div class="form-group">
    <div class="text-left col-sm-6"><button class="btn btn-danger">{l s='Cancel' mod='topbanner'}</button></div>
    <input type="hidden" name="submitNewBanner" value="1">
    <div class="text-right col-sm-6"><button type="submit" class="btn btn-primary">{l s='Save' mod='topbanner'}</button></div>
  </div>

</form>

<script>
  // Because we don't use HelperForm, we duplicate the code loading tinyMCE from the core
  var iso = '{$iso|addslashes}';
  var pathCSS = '{$smarty.const._THEME_CSS_DIR_|addslashes}';
  var ad = '{$ad|addslashes}';

  $(document).ready(function(){
    tinySetup({
      editor_selector :"autoload_rte"
    });
  });
</script>