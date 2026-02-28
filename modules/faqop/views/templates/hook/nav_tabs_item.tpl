{*
* Google-Friendly FAQ Pages and Lists With Schema Markup module
*
*    @author    Opossum Dev
*    @copyright Opossum Dev
*    @license   You are just allowed to modify this copy for your own use. You are not allowed
* to redistribute it. License is permitted for one Prestashop instance only, except for test
* instances.
*}

<div class="faq-nav-row">
    <ul class="faq-nav-tabs">
        <li class="faq-nav-tab {if $active_url === 'general'}faq-nav-tab-active{/if}">
            <a href="{$general_url|escape:'html':'UTF-8'}" class="faq-nav-link">
                <i class="fas fa-cogs"></i>&nbsp;{l s='General Settings' mod='faqop'}</a>
        </li>
        <li class="faq-nav-tab {if $active_url === 'styles'}faq-nav-tab-active{/if}">
            <a href="{$styles_url|escape:'html':'UTF-8'}" class="faq-nav-link">
                <i class="fas fa-palette"></i>&nbsp;{l s='Styles' mod='faqop'}</a>
        </li>
        <li class="faq-nav-tab {if $active_url === 'bindings'}faq-nav-tab-active{/if}">
            <a href="{$bindings_url|escape:'html':'UTF-8'}" class="faq-nav-link">
                <i class="fas fa-link"></i>&nbsp;{l s='Bind to Lists' mod='faqop'}</a>
        </li>
    </ul>
</div>