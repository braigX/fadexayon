{*
* Google-Friendly FAQ Pages and Lists With Schema Markup module
*
*    @author    Opossum Dev
*    @copyright Opossum Dev
*    @license   You are just allowed to modify this copy for your own use. You are not allowed
* to redistribute it. License is permitted for one Prestashop instance only, except for test
* instances.
*}

<div class="panel op-faq-instructions"><h3><i class="icon-cogs"></i> {l s='Configure' mod='faqop'}
    </h3>

    <div class="op-hello-wrap">
        <div class="op-hello-buttons">
            <div>
                <a href="{if $page_id}{$edit_page_href}{else}{$create_page_href}{/if}"
                   class="op-edit-page-button">{l s='Edit page settings' mod='faqop'}
                    <i class="fas fa-pencil-alt"></i>
                </a>
            </div>

            <div class="op-status-page-wrap">
                <div id="block-for-error-grid">
                    <div class="op-error-wrap" style="display:none"></div>
                </div>
                <div class="op-status-page-row">
                    <div>
                        <i class="fas fa-toggle-on op-toggle-on op-status-show-button op-toggle-status"
                           title="{l s='Disable' mod='faqop'}"
                           {if !$page_status}style="display: none"{/if}
                           data-status="{$page_status}"></i>
                        <i class="fas fa-toggle-off op-toggle-off op-status-show-button op-toggle-status"
                           title="{l s='Enable' mod='faqop'}"
                           {if $page_status}style="display: none"{/if}
                           data-status="{$page_status}"></i>
                        <i id="op-spinner-status"
                           class="fas fa-spinner fa-spin spin-process spin-process-inline op-activate-page-spinner"
                           style="display: none"></i>
                    </div>
                    <div>
                <span class="show-desc-on op-status-show-desc" {if !$page_status}style="display: none"{/if}>
                    {if $multistore_active}
                        {l s='Pages enabled for all shops' mod='faqop'}
                    {else}
                        {l s='Page enabled' mod='faqop'}
                    {/if}
                </span>
                        <span class="show-desc-off op-status-show-desc" {if $page_status}style="display: none"{/if}>
                    {if $multistore_active}
                        {l s='Pages disabled for all shops' mod='faqop'}
                    {else}
                        {l s='Page disabled' mod='faqop'}
                    {/if}
                </span>
                    </div>
                </div>
            </div>
            {if $multistore_active}
                <div>
                    <h4 class="op-multistore-header">
                        Multistore:
                    </h4>
                </div>
                {if $page_id}
                    <div class="op-mb-10">
                        <button id="bulk-button-block-copy-shop-page" class="op-copy-page-to-shops"><i
                                    class="fas fa-store"></i>
                            {l s='Export page settings to other shops' mod='faqop'}
                            <i class="fas fa-external-link-alt"></i>
                        </button>
                    </div>
                {else}
                    <div class="op-mb-10">
                        <div class="op-no-page-message">
                            <i class="fas fa-exclamation"></i>&nbsp;
                            {l s='You have no page settings for this shop yet' mod='faqop'}
                        </div>
                    </div>
                    {if $shops_list_with_settings|@count > 0}
                        <div class="op-mb-10">
                            <button id="op-import-page-settings" class="op-copy-page-to-shops"><i
                                        class="fas fa-store"></i>
                                {l s='Import page settings from another shop' mod='faqop'}
                                <i class="fas fa-file-import"></i>
                            </button>
                        </div>
                    {/if}
                {/if}
            {/if}
        </div>
        <div class="op-hello-img-wrap">
            <div class="op-hello-img"></div>
        </div>
    </div>

</div>


{if $multistore_active}
    {include file=$includeTpl shops_list=$shops_list old_shop=$old_shop page_id=$page_id}
{/if}

{if $multistore_active && !$page_id}
    <div id="op-import-page-settings-body" class="panel" style="display: none"><h3><i class="icon-copy"></i>
            {l s='Import page settings from shop:' mod='faqop'}
        </h3>
        <div class="op-import-shop-selector-wrap">
                <select class="form-select op-import-shop-selector" id="op-import-shop-selector">
                    <option disabled selected>{l s='Choose a shop:' mod='faqop'}</option>
                    {foreach $shops_list_with_settings as $shop}
                        <option value="{$shop.id_shop}-{$shop.id_page}">{$shop.name}</option>
                    {/foreach}
                </select>
        </div>
        <button id="op-import-page-from-shops"
                class="op-copy-to-shops-btn op-popup-button"
                data-new-shop="{$old_shop}">
            <i class="fas fa-store usual-process"></i>
            <span>{l s='Import' mod='faqop'}</span></button>
    </div>
{/if}

<div class="panel op-faq-instructions"><h3><i class="icon-question-sign"></i> {l s='Information' mod='faqop'}
    </h3>
    {if $page_status}
        <p>
            {l s='Link to your FAQ page:' mod='faqop'} <a href="{$link_page}" target="_blank">{$link_page}</a>
        </p>
        <p>
            {l s='Add SEO meta tags and change its URL here:' mod='faqop'}
            <a href="{$link_seo}" target="_blank">{$link_seo}</a>
        </p>
        <p>
            {l s='Change page layout here:' mod='faqop'}
            <a href="{$link_layout}" target="_blank">{$link_layout}</a>
        </p>
    {/if}
    <p>
        {l s='Use markup validator:' mod='faqop'}
        <a href="https://search.google.com/test/rich-results" target="_blank">https://search.google.com/test/rich-results</a>
    </p>

</div>

