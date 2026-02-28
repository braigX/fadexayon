{*
 * 2019 ExtraSolutions
 *
 * NOTICE OF LICENSE
 *
 * @author    ExtraSolutions
 * @copyright 2019 ExtraSolutions
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*}

{extends file="helpers/list/list_header.tpl"}

{block name="override_header"}
    {if isset($fields_value['tableKey']) && $list_id == "{$fields_value['tableKey']}_taxonomy"}
        <div class="panel step_container_1">
            <div class="row">
                <div class="col-lg-6 g-taxonomy-title">
                    <p class="f-middle help-block">
                        <a class="fl-module-back toolbar_btn pull-left" href="{$currentIndex|escape:'htmlall':'UTF-8'}">
                            <i class="process-icon-back"></i>
                            <span>{l s='Back' mod='gmerchantfeedes'}</span>
                        </a>
                        <span class="pull-right g-taxonomy-w-title">
                            {l s='Associate google-taxonomy categories' mod='gmerchantfeedes'}
                            <span>({$fields_value['language_iso']|escape:'htmlall':'UTF-8'})</span>
                        </span>
                    </p>
                    <input type="hidden" value="{$fields_value['language_id']|intval}" data-currentindex="{$currentIndex|escape:'htmlall':'UTF-8'}"
                           class="lang_google_lists" name="lang_google_lists">
                </div>
                <div class="col-lg-6 g-taxonomy-bulk-action">
                    <button class="load-bulk-taxonomy-js btn btn-default pull-right">
                        {l s='Bulk Categories Updates' mod='gmerchantfeedes'}
                    </button>
                    <div class="row">
                        <div class="col-md-12 bulk-taxonomy-upd-container"></div>
                    </div>
                </div>
            </div>
        </div>
        {strip}
            {addJsDef currentIndex=$currentIndex}
        {/strip}
    {elseif ($list_id == 'gmerchantfeed_taxonomy_lang_list')}
        <div class="panel text-center step_container_1">
            <span data-tab="gmerchantfeed_taxonomy_lang_list"
                  class="step-title tab">{l s='Taxonomy Addons configurations' mod='gmerchantfeedes'}</span>
            <p class="help-block">
                {l s='Google Taxonomy set' mod='gmerchantfeedes'}
            </p>
        </div>
    {/if}
{/block}
