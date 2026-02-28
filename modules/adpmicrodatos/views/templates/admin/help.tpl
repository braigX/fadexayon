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

<div class="tab-pane panel {if ($active_tab == '#tab_help')} active {else} '' {/if}" id="tab_help">
    <div class="panel-heading"><i class="icon-question"></i> {l s='FAQ' mod='adpmicrodatos'}</div>
    
    <span class="faq-h1">{l s='Important' mod='adpmicrodatos'}</span>
    <ul>
        <li>
            <span class="faq-h2"><i class="icon-info-circle"></i>{l s='What should I do if I change my template?' mod='adpmicrodatos'}</span>
            <p class="faq-text hide">
               {l s='If you change the template you must reinstall the module.' mod='adpmicrodatos'}
               {l s='You also have an option to "clean the microdata again" in case new code with microdata has been introduced due to automatic update processes.' mod='adpmicrodatos'}
            </p>
        </li>
    </ul>
    <ul>
        <li>
            <span class="faq-h2"><i class="icon-info-circle"></i>{l s='What security measures should I take if I uninstall the module?' mod='adpmicrodatos'}</span>
            <p class="faq-text hide">
               {l s='Keep in mind that when uninstalling the module, the system will recover the same files that it had originally before installing our module. So if you have modified these files, you will lose those changes. To recover them you only have to make use of the security backup made in ../modules/adpmicrodatos/tmp/' mod='adpmicrodatos'}
            </p>
        </li>
    </ul>
    <ul>
        <li>
            <span class="faq-h2"><i class="icon-info-circle"></i>{l s='What should I do if I update my rich snippets rating module?' mod='adpmicrodatos'}</span>
            <p class="faq-text hide">
               {l s='If you update your rich snippets rating module, most often certain structured data will be duplicated again. It is best to rescan your rich snippets module files for duplicate rich snippets.' mod='adpmicrodatos'}
            </p>
        </li>
    </ul>
    <ul>
        <li>
            <span class="faq-h2"><i class="icon-info-circle"></i>{l s='What should I do if I am a developer and I modify the same files that the module has cleaned of microdata?' mod='adpmicrodatos'}</span>
            <p class="faq-text hide">
               {l s='We recommend you make those changes on the destination file and on the file with extension *.adpmicrodatos.backup, that way when uninstalling, you will not lose any changes you have made.' mod='adpmicrodatos'}
               {l s='You also have an option to "clean the microdata again" in case new code with microdata has been introduced due to automatic update processes.' mod='adpmicrodatos'}
            </p>
        </li>
    </ul>
    <span class="faq-h1">{l s='How it works' mod='adpmicrodatos'}</span>
    <ul>
        <li>
            <span class="faq-h2"><i class="icon-info-circle"></i>{l s='What microdata does this module eliminate?' mod='adpmicrodatos'}</span>
            <p class="faq-text hide">
               {l s='This module only removes microdata from the installed theme. If you have microdata in third party modules, we recommend you to extend the functionality of this module with this one' mod='adpmicrodatos'} <a href="https://addons.prestashop.com/es/product.php?id_product=44883" target="_blank">https://addons.prestashop.com/es/product.php?id_product=44883</a
            </p>
        </li>
    </ul>
    <ul>
        <li>
            <span class="faq-h2"><i class="icon-info-circle"></i>{l s='How can we check if the microdata is working correctly?' mod='adpmicrodatos'}</span>
            <p class="faq-text hide">
               {l s='We recommend using this link to verify its operation:' mod='adpmicrodatos'} <a href="https://validator.schema.org/" target="_blank">https://validator.schema.org/</a>. <br> {l s='You can also use this link to check the rich snippets:' mod='adpmicrodatos'} <a href="https://search.google.com/test/rich-results" target="_blank">https://search.google.com/test/rich-results</a>
            </p>
        </li>
    </ul>
    <ul>
        <li>
            <span class="faq-h2"><i class="icon-info-circle"></i>{l s='Where can I get information about microdata?' mod='adpmicrodatos'}</span>
            <p class="faq-text hide">
               {l s='We recommend you to visit the official reference of google in this link:' mod='adpmicrodatos'} <a href="https://developers.google.com/search/docs/data-types/product" target="_blank">https://developers.google.com/search/docs/data-types/product</a>
            </p>
        </li>
    </ul>
    <span class="faq-h1">{l s='Configuration' mod='adpmicrodatos'}</span>
    <ul>
        <li>
            <span class="faq-h2"><i class="icon-info-circle"></i>{l s='How can I deactivate some kind of specific microdata?' mod='adpmicrodatos'}</span>
            <p class="faq-text hide">
               {l s='In our configuration section you will be able to deactivate those microdata that you consider not necessary.' mod='adpmicrodatos'}
               <br>
               {l s='In the customize section you can decide which microdata to display on each page.' mod='adpmicrodatos'}
            </p>
        </li>
    </ul>
    <ul>
        <li>
            <span class="faq-h2"><i class="icon-info-circle"></i>{l s='What do I need to activate the rich snippets from Trusted Shop?' mod='adpmicrodatos'}</span>
            <p class="faq-text hide">
               {l s='You must request your ID from trusted shop. In this link you will find all the necessary information:' mod='adpmicrodatos'} <a href="https://business.trustedshops.es/productos/valoraciones#shop" target="_blank">https://business.trustedshops.es/productos/valoraciones#shop</a>
            </p>
        </li>
    </ul>
    <ul>
        <li>
            <span class="faq-h2"><i class="icon-info-circle"></i>{l s='Is it important to fill in the Valid until field in the product sheet?' mod='adpmicrodatos'}</span>
            <p class="faq-text hide">
               {l s='Google recommends filling in this field only if the product has an expiration date in your online store. If the value is zero, the attribute is disabled.' mod='adpmicrodatos'}
            </p>
        </li>
    </ul>
    <span class="faq-h1">{l s='General questions' mod='adpmicrodatos'}</span>
    <ul>
        <li>
            <span class="faq-h2"><i class="icon-info-circle"></i>{l s='Is it necessary to eliminate the microdata in the default template?' mod='adpmicrodatos'}</span>
            <p class="faq-text hide">
               {l s='For a correct operation, we recommend to eliminate them, using our installation system.' mod='adpmicrodatos'}
            </p>
        </li>
    </ul>
    <ul>
        <li>
            <span class="faq-h2"><i class="icon-info-circle"></i>{l s='Can this module coexist with other SEO modules installed?' mod='adpmicrodatos'}</span>
            <p class="faq-text hide">
               {l s='Thanks to the configuration options, we can enable and disable certain microdata that give problems with other modules SEO' mod='adpmicrodatos'}
            </p>
        </li>
    </ul>
    <ul>
        <li>
            <span class="faq-h2"><i class="icon-info-circle"></i>{l s='What evaluation modules (Rich snippets) are compatible with this module?' mod='adpmicrodatos'}</span>
            <p class="faq-text hide">
               {l s='In the configuration section, under the richsnippets tab, you will find an informative tooltip with all compatible modules.' mod='adpmicrodatos'}
            </p>
        </li>
    </ul>
</div>
