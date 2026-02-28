{*
 * 2020 ExtraSolutions
 *
 * NOTICE OF LICENSE
 *
 * @author    ExtraSolutions
 * @copyright 2020 ExtraSolutions
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*}

<style>
  .dropdown-menu.c-styled > li > a {
    padding: 3px 20px;
    clear: both;
    font-weight: normal;
    line-height: 1.42857;
    white-space: nowrap;
    color: #202020;
  }

  .p-gmerchantfeedes-form {
    background #ffffff;
    border: 1px solid #25b9d7;
    display: inline-block;
    width: 100%;
    margin-left: 0;
    margin-right: 0;
    margin-bottom: 20px;
  }

  .gmc-justify-content {
    display: flex;
    flex-direction: row;
    align-items: center;
  }

  .gmc-justify-item {
    padding-left: 5px;
    padding-right: 5px;
  }

  .gmc-w100 {
    width: 100%;
  }
</style>

<script type="text/javascript">
    var dataProductText = {{$field_default_value|json_encode nofilter}};

    function gmcSetDefaultText(field, id_lang) {
        var inputName = field + '_' + id_lang;
        var inputValue = '';
        switch (field) {
            case 'gmerchantfeedes_title':
                inputValue = dataProductText[id_lang]['default_title'] ?? '';
                break;
            case 'gmerchantfeedes_short_description':
                inputValue = dataProductText[id_lang]['default_short_description'] ?? '';
                break;
            case 'gmerchantfeedes_description':
                inputValue = dataProductText[id_lang]['default_description'] ?? '';
                break;
        }

        $('input[name=' + inputName + '], textarea[name=' + inputName + ']').val(inputValue);
    }
</script>

<div class="panel">
  <div style="background: #ffffff;" class="form-group p-gmerchantfeedes-form row" id="gmerchantfeedes">
      {foreach $fields as $f => $fields_list}
          {if (is_array($fields_list) && isset($fields_list['group_name']) && !empty($fields_list['group_name']))}
            <div style="background: #f0f0f0; padding: 5px 15px; margin-bottom: 15px;" class="col-lg-12">
              <h2 style="text-align: left; margin-top: 0; margin-bottom: 0; padding: 0; line-height: 2; font-size: 17px"
                  class="control-label">
                <span>
                    {$fields_list['group_name']|escape:'html':'UTF-8'}
                </span>
              </h2>
            </div>
              {foreach $fields_list['fields'] as $f => $fieldset}
                  {*                  {if $languages|count > 1}*}
                <div style="width: 100%;" class="form-group row">
                    {*                  {/if}*}
                  <label class="control-label text-right col-lg-3">
                    <span style="line-height: 1.2; font-size: 13px">
                        {$fieldset.field_name|escape:'html':'UTF-8'}
                    </span>
                  </label>
                  <div class="col-lg-8">
                      {if (isset($fieldset['lang']) && !$fieldset['lang'])}
                        <div class="col-lg-11 col-md-10 col-sm-9 pr-0 pl-0">
                      <textarea rows="{$fieldset['rows']|escape:'html':'UTF-8'}"
                                class="form-control gmc-w100"
                                id="{if isset($fieldset.id)}{$fieldset.id|escape:'html':'UTF-8'}{else}{$fieldset.name|escape:'html':'UTF-8'}{/if}"
                                name="{$fieldset.name|escape:'html':'UTF-8'}">{if isset($fields_value[$fieldset.name])}{$fields_value[$fieldset.name]|escape:'html':'UTF-8'}{/if}</textarea>
                        </div>
                      {else}
                          {foreach $languages as $language}
                              {*                        {if $languages|count > 1}*}
                            <div class="translatable-field row lang-{$language.id_lang|intval}"
                                 {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
                              <div class="col-lg-11 col-md-10 col-sm-9">
                                  {*                        {else}*}
                                  {*                          <div style="padding-bottom: 15px;">*}
                                  {*                        {/if}*}
                                  {if ($fieldset['type'] == 'textarea')}
                                    <textarea rows="{$fieldset['rows']|escape:'html':'UTF-8'}"
                                              class="form-control gmc-w100"
                                              id="{if isset($fieldset.id)}{$fieldset.id|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{else}{$fieldset.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{/if}"
                                              name="{$fieldset.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}">{if isset($fields_value[$language.id_lang][$fieldset.name])}{$fields_value[$language.id_lang][$fieldset.name]|escape:'html':'UTF-8'}{/if}</textarea>
                                  {else}
                                    <input type="text" class="form-control gmc-w100"
                                           id="{if isset($fieldset.id)}{$fieldset.id|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{else}{$fieldset.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{/if}"
                                           name="{$fieldset.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}"
                                           value="{if isset($fields_value[$language.id_lang][$fieldset.name])}{$fields_value[$language.id_lang][$fieldset.name]|escape:'html':'UTF-8'}{/if}"/>
                                  {/if}
                                  {if isset($fieldset.desc) && !empty($fieldset.desc)}
                                    <small class="help-block form-text pb-0 mb-0">
                                        {$fieldset.desc|escape:'html':'UTF-8'}
                                    </small>
                                  {/if}
                                  {*                        {if $languages|count > 1}*}
                              </div>
                              <div class="col-lg-1 col-md-2 col-sm-3 text-right">
                                <div class="gmc-justify-content">
                                  <a class="gmc-justify-item" href="#" title="Take original"
                                     onclick="gmcSetDefaultText('{$fieldset.name|escape:'html':'UTF-8'}', {$language.id_lang|escape:'html':'UTF-8'}); return false;">
                                      {if (version_compare(_PS_VERSION_, '1.7.0.0', '<'))}
                                        <i class="icon-copy"></i>
                                      {else}
                                        <i class="material-icons">content_copy</i>
                                      {/if}
                                  </a>
                                  <div class="gmc-justify-item">
                                    <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1"
                                            data-toggle="dropdown">
                                        {$language.iso_code|escape:'html':'UTF-8'}
                                      <i class="icon-caret-down"></i>
                                    </button>
                                    <ul class="dropdown-menu c-styled">
                                        {foreach from=$languages item=language}
                                          <li><a href="javascript:hideOtherLanguage({$language.id_lang|intval});"
                                                 tabindex="-1">{$language.name|escape:'html':'UTF-8'}</a></li>
                                        {/foreach}
                                    </ul>
                                  </div>
                                </div>
                              </div>
                            </div>
                              {*                        {else}*}
                              {*                          </div>*}
                              {*                        {/if}*}
                          {/foreach}


                      {/if}

                  </div>
                    {*                  {if $languages|count > 1}*}
                </div>
                  {*                  {/if}*}
              {/foreach}
          {/if}
      {/foreach}
  </div>
    {if isset($with_submit) && $with_submit}
        {include file="./product_field_submit.tpl"}
    {/if}
</div>
