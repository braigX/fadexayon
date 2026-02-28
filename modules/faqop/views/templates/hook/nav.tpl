{*
* Google-Friendly FAQ Pages and Lists With Schema Markup module
*
*    @author    Opossum Dev
*    @copyright Opossum Dev
*    @license   You are just allowed to modify this copy for your own use. You are not allowed
* to redistribute it. License is permitted for one Prestashop instance only, except for test
* instances.
*}

<div class="productTabs">
    <div class="list-group">
        <ul class="nav nav-pills op_tabs">
            <li {if $active_url === 'page'}class="active"{/if}>
                <a href="{$page_url|escape:'html':'UTF-8'}"
                   class="list-group-item">
                    <i class="far fa-file-alt"></i>&nbsp;
                    {l s='FAQ Page' mod='faqop'}</a>
            </li>
            <li {if $active_url === 'list'}class="active"{/if}>
                <a href="{$list_url|escape:'html':'UTF-8'}"
                   class="list-group-item">
                    <i class="fas fa-th-large"></i>&nbsp;
                    {l s='FAQ Lists' mod='faqop'}</a>
            </li>
            <li {if $active_url === 'items'}class="active"{/if}>
                <a href="{$items_url|escape:'html':'UTF-8'}"
                   class="list-group-item">
                    <i class="fas fa-grip-vertical"></i>&nbsp;
                    {l s='All Items' mod='faqop'}</a>
            </li>
            <li {if $active_url === 'hook'}class="active"{/if}><a href="{$hook_url|escape:'html':'UTF-8'}"
                   class="list-group-item"><i class="fas fa-link"></i>&nbsp;
                    {l s='Custom Hooks' mod='faqop'}</a></li>

            <li {if $active_url === 'help'}class="active"{/if}><a href="{$help_url|escape:'html':'UTF-8'}"
                   class="list-group-item"><i class="fas fa-info"></i>&nbsp;
                    {l s='Help' mod='faqop'}</a></li>
        </ul>
    </div>
</div>