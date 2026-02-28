{*
* 2007-2023 PrestaShop
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    Ádalop <contact@prestashop.com>
*  @copyright 2023 Ádalop
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}

<div class="tab-pane panel {if ($active_tab == '#tab_thirdparty_richsnippets_modules')} active {else} '' {/if}" id="tab_thirdparty_richsnippets_modules">
    <div class="panel-heading"><i class="icon-plug"></i> {l s='Third Party Rich Snippets Compatible Modules' mod='adpmicrodatos'}</div>
    <div class="block_related_modules">
        <ul class="block_modules_related related">
            {foreach from=$richSnippetsImplementedModules item=item key=key name=name}
                <li class="item_module"> 
                    <img class="logo_module" src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/thirdpartyrichsnippet/{$key|escape:'htmlall':'UTF-8'}.png">
                    <div class="module_title"><span>{$key|escape:'htmlall':'UTF-8'}</span></div>
                    <div class="module_description"><span>{$item['name']|escape:'htmlall':'UTF-8'}</span></div>
                    {if (!empty($item['link']))}
                    <div class="module_button"> 
                        <a target="_blank" href={$item['link']|escape:'htmlall':'UTF-8'} class="module_href">{l s='Discover' mod='adpmicrodatos'}</a> 
                    </div>
                    {/if}
                </li>
            {/foreach}
        </ul>
    </div>
</div>
