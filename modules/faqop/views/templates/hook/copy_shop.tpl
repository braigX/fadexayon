{*
* Google-Friendly FAQ Pages and Lists With Schema Markup module
*
*    @author    Opossum Dev
*    @copyright Opossum Dev
*    @license   You are just allowed to modify this copy for your own use. You are not allowed
* to redistribute it. License is permitted for one Prestashop instance only, except for test
* instances.
*}

<div id="op-faqop-copy-to-shops" class="panel" style="display: none"><h3><i class="icon-copy"></i>
        {if is_null($page_id)}
            {l s='Copy selected blocks to shops:' mod='faqop'}
        {else}
            {l s='Copy page settings to shops:' mod='faqop'}
        {/if}
    </h3>
    <div class="form-group op-copy-to-shops-wrap checkbox-block-custom">
        {foreach $shops_list as $shop}
            <p class="op-checkbox">
                <label for="copy-shop-{$shop.id_shop}">
                    <input type="checkbox" id="copy-shop-{$shop.id_shop}" class="op-faqop-tick-shop"
                           value="{$shop.id_shop}">
                    <span>{$shop.name}</span>
                    <i class="op-checkbox-control"></i>
                </label>
            </p>
        {/foreach}
        <button id="{if is_null($page_id)}op-copy-to-shops{else}op-copy-page-to-shops{/if}"
                class="op-copy-to-shops-btn op-popup-button"
                data-old-shop="{$old_shop}"
                data-page-id="{$page_id}">
            <i class="fas fa-store usual-process"></i>
            <span>{if is_null($page_id)}
                        {l s='Copy lists' mod='faqop'}
                    {else}
                        {l s='Copy page settings' mod='faqop'}

                    {/if}</span></button>
    </div>
</div>

<div id="op-popup-copy-shop" class="op-undercover">
    <div class="op-window-wrap">
        <div class="op-window">
            <div class="op-popup-h">
                {l s='Copying, please wait' mod='faqop'}
            </div>
            <i class="fas fa-spinner fa-spin spin-process spin-process-inline op-copy-shop-spinner"></i>
            <div class="op-popup-stat">
                <span class="op-copy-cur">0</span>&nbsp;/&nbsp;<span class="op-copy-all">0</span>
            </div>
        </div>
    </div>
</div>