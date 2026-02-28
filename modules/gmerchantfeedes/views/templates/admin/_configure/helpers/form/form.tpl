{*
 * 2019 ExtraSolutions
 *
 * NOTICE OF LICENSE
 *
 * @author    ExtraSolutions
 * @copyright 2019 ExtraSolutions
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*}

{extends file="helpers/form/form.tpl"}

{block name="input"}
    {if $input.type == 'custom_param'}
      <div class="row">
        <div class="col-md-6">
          <input type="text" id="added_custom_param" value="">
        </div>
        <div class="col-md-5">
            {if isset($input.features) && count($input.features)}
              <select id="added_custom_param_feature">
                  {foreach from=$input.features item=feature}
                    <option value="{$feature['id_feature']|intval}">{$feature['name']|escape:'htmlall':'UTF-8'}</option>
                  {/foreach}
              </select>
            {/if}
        </div>
        <div class="col-md-1">
          <span class="btn btn-default w100 add_new_custom_param">
              {l s='add' mod='gmerchantfeedes'}
          </span>
        </div>
        <input type="hidden" class="remove_tr_msg"
               value="{l s='These param will be deleted for good. Please confirm.' mod='gmerchantfeedes'}">
        <div class="col-lg-12">
          <p class="help-block">
              {l s='Append new config row before close "</entry>" tag, example: "g:energy_efficiency_class"' mod='gmerchantfeedes'}
          </p>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <ul id="features_custom_selected">
              {if isset($fields_value.features_custom_mod)
              && is_array($fields_value.features_custom_mod)
              && count($fields_value.features_custom_mod)}
                  {foreach from=$fields_value.features_custom_mod item=feature}
                    <li>
                      <input type="hidden" name="feature_custom_inheritage[]"
                             value="{$feature['id_feature']|escape:'htmlall':'UTF-8'}">
                      <input type="hidden" name="feature_custom_inheritage_param[]"
                             value="{$feature['unit']|urlencode|escape:'htmlall':'UTF-8'}">
                      <span class="feature_custom">
                                    &lt;{$feature['unit']|escape:'htmlall':'UTF-8'}> {$feature['name']|escape:'htmlall':'UTF-8'} &lt;/{$feature['unit']|escape:'htmlall':'UTF-8'}>
                                </span>
                      <span class="feature_removed"><i class="material-icons">delete</i></span>
                    </li>
                  {/foreach}
              {/if}
          </ul>
        </div>
      </div>
    {elseif $input.type == 'custom_product_param'}
      <div class="row">
        <div class="col-md-6">
          <input type="text" id="{$input.name|escape:'htmlall':'UTF-8'}_param" value="">
        </div>
        <div class="col-md-5">
            {if isset($input.options) && count($input.options)}
              <select id="{$input.name|escape:'htmlall':'UTF-8'}_select">
                  {foreach from=$input.options key=optionId item=optionValue}
                    <option value="{$optionId|escape:'htmlall':'UTF-8'}">{$optionValue|escape:'htmlall':'UTF-8'}</option>
                  {/foreach}
              </select>
            {/if}
        </div>
        <div class="col-md-1">
          <span data-ref="{$input.name|escape:'htmlall':'UTF-8'}" class="btn btn-default w100 add_new_option">
              {l s='add' mod='gmerchantfeedes'}
          </span>
        </div>
        <input type="hidden" class="remove_tr_msg"
               value="{l s='These param will be deleted for good. Please confirm.' mod='gmerchantfeedes'}">
        <div class="col-lg-12">
          <p class="help-block">
              {l s='Append new config row before close "</entry>" tag, example: "g:custom_label_0"' mod='gmerchantfeedes'}
          </p>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <ul style="padding: 0; margin: 0; list-style: none;" id="{$input.name|escape:'htmlall':'UTF-8'}_selected">
              {if isset($fields_value.custom_product_row)
              && is_array($fields_value.custom_product_row)
              && count($fields_value.custom_product_row)}
                  {foreach from=$fields_value.custom_product_row item=feature}
                    <li class="custom-option-item">
                      <input type="hidden" name="{$input.name|escape:'htmlall':'UTF-8'}[]"
                             value="{$feature['id_param']|escape:'htmlall':'UTF-8'}">
                      <input type="hidden" name="{$input.name|escape:'htmlall':'UTF-8'}_param[]"
                             value="{$feature['unit']|urlencode|escape:'htmlall':'UTF-8'}">
                      <span class="feature_custom">
                        &lt;{$feature['unit']|escape:'htmlall':'UTF-8'}> {$input.options[$feature['id_param']]|escape:'htmlall':'UTF-8'} &lt;/{$feature['unit']|escape:'htmlall':'UTF-8'}>
                      </span>
                      <span class="feature_removed"><i class="material-icons">delete</i></span>
                    </li>
                  {/foreach}
              {/if}
          </ul>
        </div>
      </div>
    {elseif $input.type == 'textarea_clean'}
      <div class="row">
        <div class="col-lg-9">
          <textarea{if isset($input.readonly) && $input.readonly} readonly="readonly"{/if} name="{$input.name|escape:'htmlall':'UTF-8'}"
                                                                                           id="{if isset($input.id)}{$input.id|escape:'htmlall':'UTF-8'}{else}{$input.name|escape:'htmlall':'UTF-8'}{/if}" {if isset($input.cols)}cols="{$input.cols|escape:'htmlall':'UTF-8'}"{/if} {if isset($input.rows)}rows="{$input.rows|escape:'htmlall':'UTF-8'}"{/if} class="{if isset($input.class)} {$input.class|escape:'htmlall':'UTF-8'}{/if}">{$fields_value[$input.name]|escape:'html':'UTF-8'}</textarea>
        </div>
        <div class="col-lg-2">
        </div>
      </div>
    {elseif $input.type == 'custom_attribute'}
      <div class="custom_attribute">
        <div class="row attribute-mod-container">
          <div class="col-lg-5">
            <div style="width: 100%;" class="input-group input">
              <input type="text" id="custom_attribute_name" placeholder="{l s='key-name' mod='gmerchantfeedes'}"
                     value="" class="custom_attribute_name input">
              <p class="help-block"></p>
            </div>
          </div>
          <div class="col-lg-5">
            <select style="width: 100%;" class="custom_attribute_section">
                {foreach $input.options.query AS $option}
                    {if $option == "-"}
                      <option value="">-</option>
                    {else}
                      <option value="{$option[$input.options.id]|escape:'htmlall':'UTF-8'}">{$option[$input.options.name]|escape:'htmlall':'UTF-8'}</option>
                    {/if}
                {/foreach}
            </select>
          </div>
          <div class="col-lg-1">
            <button onclick="return false;" class="btn btn-default js-add-new-custom-atr">
                {l s='Add' mod='gmerchantfeedes'}
            </button>
          </div>
          <div class="col-lg-12">
            <button onclick="return false;" style="margin-top: 5px; min-width: 120px;" class="btn btn-default js-add-all-custom-atr">
                {l s='Add all' mod='gmerchantfeedes'}
            </button>
          </div>
          <div class="col-lg-10">
            <p class="help-block">
                {l s='Append new custom attribute row,  example: "g:energy_efficiency_class"' mod='gmerchantfeedes'}
            </p>
          </div>
        </div>
          {if isset($fields_value.custom_attribute)}
              {foreach from=$fields_value.custom_attribute item='customAttr'}
                <div class="row dec-row">
                  <input type="hidden" name="custom_attr_key[]" value="{$customAttr['unit']|escape:'htmlall':'UTF-8'}">
                  <input type="hidden" name="custom_attr_id[]"
                         value="{$customAttr['id_attribute']|escape:'htmlall':'UTF-8'}">
                  <div class="col-md-11">
                    <span class="example-row">
                     &lt;{$customAttr['unit']|escape:'htmlall':'UTF-8'}> {$customAttr['name']|escape:'htmlall':'UTF-8'}  &lt;/{$customAttr['unit']|escape:'htmlall':'UTF-8'}>
                    </span>
                  </div>
                  <div class="col-lg-1">
                    <span class="js-remove-attr-line"><i class="material-icons">delete</i></span>
                  </div>
                </div>
              {/foreach}
          {/if}
      </div>
    {elseif $input.type == 'separator'}
      <hr/>
    {elseif $input.type == 'switch_with_inp'}
      <div class="row">
        <div class="col-md-12">
          <div class="btn-group-switch-with-inp">
                    <span class="switch prestashop-switch fixed-width-lg">
                        {foreach $input.values as $value}
                          <input type="radio"
                                 name="{$input.name|escape:'htmlall':'UTF-8'}"{if $value.value == 1} id="{$input.name|escape:'htmlall':'UTF-8'}_on"{else} id="{$input.name|escape:'htmlall':'UTF-8'}_off"{/if} value="{$value.value|escape:'htmlall':'UTF-8'}"{if $fields_value[$input.name] == $value.value} checked="checked"{/if}{if (isset($input.disabled) && $input.disabled) or (isset($value.disabled) && $value.disabled)} disabled="disabled"{/if}/>

{strip}
                          <label {if $value.value == 1} for="{$input.name|escape:'htmlall':'UTF-8'}_on"{else} for="{$input.name|escape:'htmlall':'UTF-8'}_off"{/if}>
                            {if $value.value == 1}
                                {l s='Yes' d='Admin.Global' mod='gmerchantfeedes'}
                            {else}
                                {l s='No' d='Admin.Global' mod='gmerchantfeedes'}
                            {/if}
                        </label>
                        {/strip}
                        {/foreach}
                        <a class="slide-button btn"></a>
                    </span>
              {$temporary_inp_field="`$input['name']`_inp"}
            <input class="append-inp-with-with" value="{$fields_value[$temporary_inp_field]|escape:'htmlall':'UTF-8'}"
                   name="{$input.name|escape:'htmlall':'UTF-8'}_inp"
                   placeholder="{$input.placeholder|escape:'htmlall':'UTF-8'}" type="text">
          </div>
        </div>
      </div>
    {elseif $input.type == 'discount_type'}
      <div class="row">
        <div class="col-lg-3 col-md-4">
          <div style="width: 100%;" class="input-group input">
            <input type="text" id="{$input.name|escape:'htmlall':'UTF-8'}" name="{$input.name|escape:'htmlall':'UTF-8'}"
                   value="{$fields_value[$input.name]|escape:'htmlall':'UTF-8'}" class="input">
            <p class="help-block"></p>
          </div>
        </div>
        <div class="col-lg-1 col-md-1">
            {assign var="discount_type" value="{$input.name|escape:'htmlall':'UTF-8'}_type"}
          <select name="{$input.name|escape:'htmlall':'UTF-8'}_type">
            <option {if $fields_value[$discount_type] === 'value'}selected="selected" {/if} value="value">€</option>
            <option {if $fields_value[$discount_type] === 'percent'}selected="selected" {/if} value="percent">%</option>
          </select>
        </div>
      </div>
    {elseif $input.type == 'file_csv'}
      <div class="row">
        <div class="col-md-7">
          <input id="file_{$input.name|escape:'htmlall':'UTF-8'}" type="file"
                 name="file_{$input.name|escape:'htmlall':'UTF-8'}" class="hide"/>
          <div class="dummyfile input-group">
            <span class="input-group-addon"><i class="icon-file"></i></span>
            <input id="file-name_{$input.name|escape:'htmlall':'UTF-8'}" type="text" class="disabled"
                   name="filename_{$input.name|escape:'htmlall':'UTF-8'}" readonly/>
            <span class="input-group-btn">
              <button id="file-selectbutton_{$input.name|escape:'htmlall':'UTF-8'}" type="button"
                      name="submitAddAttachments_{$input.name|escape:'htmlall':'UTF-8'}"
                      class="btn btn-default">
                <i class="icon-folder-open"></i> {l s='Choose a file' mod='gmerchantfeedes'}
              </button>
            </span>
          </div>
        </div>
        {if isset($input.value.products_ids) && is_numeric($input.value.products_ids) && $input.value.products_ids > 0}
          <span style="line-height: 31px;display: flex;background: #f6f6f6;padding-left: 10px;border-radius: 7px;justify-content: space-between; max-width: 280px;">
            <strong style="white-space: nowrap; display:inline-block;">[ {$input.value.products_ids|escape:'htmlall':'UTF-8'} ]</strong> {l s='products loaded in total' mod='gmerchantfeedes'}
            <button type="submit" class="btn btn-default" name="btnActionRemoveExcludeFile">
              {l s='Remove' mod='gmerchantfeedes'}
            </button>
          </span>
        {/if}
      </div>
      <div class="row">
        <div class="col-lg-12">
          <a style="margin-top: 7px; display:inline-block;" href="{$input.example_url|escape:'htmlall':'UTF-8'}">{l s='.CSV Example' mod='gmerchantfeedes'}</a>
        </div>
      </div>
      <script type="text/javascript">
          $(document).ready(function () {
              $('#file-selectbutton_{$input.name|escape:'htmlall':'UTF-8'}').click(function (e) {
                  $('#file_{$input.name|escape:'htmlall':'UTF-8'}').trigger('click');
              });
              $('#file-name_{$input.name|escape:'htmlall':'UTF-8'}').click(function (e) {
                  $('#file_{$input.name|escape:'htmlall':'UTF-8'}').trigger('click');
              });
              $('#file_{$input.name|escape:'htmlall':'UTF-8'}').change(function (e) {
                  var val = $(this).val();
                  var file = val.split(/[\\/]/);
                  $('#file-name_{$input.name|escape:'htmlall':'UTF-8'}').val(file[file.length - 1]);
              });
          });
      </script>
    {else}
        {$smarty.block.parent}
    {/if}
{/block}