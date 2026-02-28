{**
 * Copyright 2022 LÍNEA GRÁFICA E.C.E S.L.
 *
 * @author    Línea Gráfica E.C.E. S.L.
 * @copyright Lineagrafica.es - Línea Gráfica E.C.E. S.L. all rights reserved.
 * @license   https://www.apache.org/licenses/LICENSE-2.0
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *}

<div class="panel">
    <div class="panel-body lgmodule-container-help">
        <section class="tabs-container">
            <label for="tab-first-steps">{l s='After Installing' mod='lgcanonicalurls'}</label>
            <label for="tab-17-prestashop">{l s='New in PS17' mod='lgcanonicalurls'}</label>
            <label for="tab-custom">{l s='Custom canonical URL' mod='lgcanonicalurls'}</label>
            <label for="tab-alternate">{l s='Alternate url by lang' mod='lgcanonicalurls'}</label>
        </section>

        <input name="tab" id="tab-first-steps" type="radio" checked />
        <section class="tab-content">
            <h2>{l s='After Installing' mod='lgcanonicalurls'}</h2>
            <h3>{l s='Configuration' mod='lgcanonicalurls'}</h3>
            <p>{l s='On this page, you can set the general configuration for canonical URLs (this default behavior will apply to all your pages)' mod='lgcanonicalurls'}</p>
            <p>{l s='If you want to set a custom canonical URL for a specific product, category, CMS, page… then go to the configuration of this page and choose the option “Custom URL”' mod='lgcanonicalurls'}</p>
            <ol>
                <li><strong>{l s='Write your favourite domain (without the last slash “/”, o el sufijo “/index.php”, or the “/index.php” suffix, or the “http(s)://”prefix)' mod='lgcanonicalurls' tags=['<strong>']}</strong>
                    <img src="{$lg_help_url|escape:'htmlall':'UTF-8'}01_domain.jpg">
                    <p>{l s='If you are on the page www.domain2.com/product1.html and you set www.domain.com as your main domain, the canonical URL displayed on the code of this page will be' mod='lgcanonicalurls' tags=['<strong>']}
                       {"<link rel=”canonical” href=”www.domain.com/product1.html/”/>"|escape:'htmlall':'UTF-8'}
                       {l s='(the module replaces the domain) in order to tell Google that the main domain is this one.' mod='lgcanonicalurls' tags=['<strong>']}
                    </p>
                </li>
                <li><strong>{l s='Force to add http:// o https:// before the domain' mod='lgcanonicalurls'}</strong>
                    <img src="{$lg_help_url|escape:'htmlall':'UTF-8'}02_force_https.jpg">
                    <p>{l s='The module will force to add “http://” or “https://” in the canonical URL' mod='lgcanonicalurls' tags=['<strong>']}
                    {"<link rel=”canonical” href=”https://www.domain.com/product1.html” />"|escape:'htmlall':'UTF-8'}
                    <p>{l s='to avoid duplicate content between http://www.domain.com/product1.html and https://www.domain.com/product1.html for example' mod='lgcanonicalurls' tags=['<strong>']}</p>
                </li>
                <li><strong>{l s='Ignore parameters in the canonical URL' mod='lgcanonicalurls'}</strong>
                    <img src="{$lg_help_url|escape:'htmlall':'UTF-8'}03_parameters.jpg">
                    <p>{l s='If you have urls with parameters and want to remove them from the canonical URL to avoid duplicate content, as for example between https://www.domain.com/product1.html and https://www.domain.com/product1html?live_configurator_token=00&id_employee=1, you just need to list the parameters (without ? and & and separate them with a coma and a space) and these parameters will be automatically removed from the canonical URLs." /> to avoid duplicate content between http://www.domain.com/product1.html and https://www.domain.com/product1.html for example' mod='lgcanonicalurls' tags=['<strong>']}</p>
                </li>
                <li><strong>{l s='Enable this option if you want to make the canonical URL visible by web browsers in the HTTP header of the page.' mod='lgcanonicalurls'}</strong>
                    <img src="{$lg_help_url|escape:'htmlall':'UTF-8'}04_header.jpg">
                    <p>{l s='This option is visible only by browser. You can test it on the website' mod='lgcanonicalurls'}
                        <a href="http://web-sniffer.net/" target="_blank">http://web-sniffer.net/</a>.
                       {l s='If this option is enabled, an extra ligne will be displayed' mod='lgcanonicalurls'}
                    </p>
                    <img src="{$lg_help_url|escape:'htmlall':'UTF-8'}05_test.png">
                </li>
                <li><strong>{l s='Click on the button Save.' mod='lgcanonicalurls'}</strong>
                </li>
            </ol>
            <p>
                {l s='To see the canonical URL, you just need to open the page in your front-office, do a right click, click on “Source code of the page” and look for the expression “canonical” (you can use CTRL + F)' mod='lgcanonicalurls'}
            </p>
        </section>
        <input name="tab" id="tab-17-prestashop" type="radio" />
        <section class="tab-content">
            <h2>{l s='New in PS17' mod='lgcanonicalurls'}</h2>
            <p>{l s='This module has new features in versión 1.7 of Prestashop.' mod='lgcanonicalurls'}</p>
            <p>{l s='You can see the following new functions:' mod='lgcanonicalurls'}</p>
            <ol>
                <li><strong>{l s='Ignore Attribute' mod='lgcanonicalurls'}:</strong>
                    {l s='This option must always be selected as "NO". You must activate this option only if you want to delete the attribute id from the product canonical url.' mod='lgcanonicalurls'}
                    <br>
                    <img src="{$lg_help_url|escape:'htmlall':'UTF-8'}06_attributes.png">
                </li>
                <li><strong>{l s='Do not use Core canonical option' mod='lgcanonicalurls'}:</strong>
                    {l s='Only enable this option if your Prestashop theme has not variables activated. Then you can select this option.' mod='lgcanonicalurls'}
                    <br>
                    <img src="{$lg_help_url|escape:'htmlall':'UTF-8'}07_core.png">
                </li>
            </ol>
        </section>
        <input name="tab" id="tab-custom" type="radio" />
        <section class="tab-content">
            <h2>{l s='Custom canonical URL' mod='lgcanonicalurls'}</h2>
            <ol>
                <li>{l s='Go to the Menu “Catalog / Products”, “Catalog / Categories” o “Preferences / CMS” and click on “Edit”.' mod='lgcanonicalurls'}
                </li>
                <li>{l s='Click on the tab “Canonical URLs”' mod='lgcanonicalurls'}
                    <br>
                    <img src="{$lg_help_url|escape:'htmlall':'UTF-8'}08_canonical.jpg">
                </li>
                <li>{l s='Choose the option “Custom URL”' mod='lgcanonicalurls'}
                    <br>
                    <img src="{$lg_help_url|escape:'htmlall':'UTF-8'}09_custom.jpg">
                </li>
                <li>{l s='Set the custom canonical URL (without http(s)://) in all your languages' mod='lgcanonicalurls'}
                    <br>
                    <img src="{$lg_help_url|escape:'htmlall':'UTF-8'}10_url.jpg">
                </li>
                <li>{l s='Click on the button Save' mod='lgcanonicalurls'}
                </li>
            </ol>
            <p>
                {l s='To see the canonical URL, you just need to open the page in your front-office, do a rightclick, click on “Source code of the page” and look for the expression “canonical” (you can use CTRL + F)' mod='lgcanonicalurls'}
            </p>
        </section>
        <input name="tab" id="tab-alternate" type="radio" />
        <section class="tab-content">
            <h2>{l s='Alternate url by lang' mod='lgcanonicalurls'}</h2>
            <h3>{l s='Tell Google about localized versions of your page' mod='lgcanonicalurls'}</h3>
            <p>
                {l s='If you have multiple versions of a page for different languages or regions, tell Google about these different variations. Doing so will help Google Search point users to the most appropriate version of your page by language or region.' mod='lgcanonicalurls'}
            </p>
            <p>
                {l s='Note that even without taking action, Google might still find alternate language versions of your page, but it is usually best for you to explicitly indicate your language- or region-specific pages.' mod='lgcanonicalurls'}
            </p>
            <p>
                {l s='Some example scenarios where indicating alternate pages is recommended:' mod='lgcanonicalurls'}
            </p>
            <ul>
                <li>{l s='If your content has small regional variations with similar content, in a single language. For example, you might have English-language content targeted to the US, GB, and Ireland.' mod='lgcanonicalurls'}</li>
                <li>{l s='If your site content is fully translated into multiple languages. For example, you have both German and English versions of each page.' mod='lgcanonicalurls'}</li>
            </ul>
            <h3>{l s='Configure your hreflang tag' mod='lgcanonicalurls'}</h3>
            <ol>
                <li>{l s='Enable hreflag tag in header' mod='lgcanonicalurls'}
                    <br>
                    <img src="{$lg_help_url|escape:'htmlall':'UTF-8'}12_hreflang.png">
                    <br>
                    {l s='Enable this option if you want show to Google a alternate urls for each language' mod='lgcanonicalurls'}
                </li>
                <li>{l s='Include lang/region code' mod='lgcanonicalurls'}
                    <br>
                    <img src="{$lg_help_url|escape:'htmlall':'UTF-8'}13_region.png">
                    <br>
                    {l s='If you have multiple versions of a page for different regions, enable this option if you want add region code in hreflang tag ("en-GB" instead of "en")' mod='lgcanonicalurls'}
                </li>
                <li>{l s='Add a alternative link to language default' mod='lgcanonicalurls'}
                    <br>
                    <img src="{$lg_help_url|escape:'htmlall':'UTF-8'}14_default.png">
                    <br>
                    {l s='The reserved hreflang="x-default" value is used when no other language/region matches the user\'s browser setting. This value is optional, but recommended, as a way for you to control the page when no languages match. A good use is to target your site\'s homepage where there is a clickable map that enables the user to select their country.' mod='lgcanonicalurls'}
                </li>
            </ol>
        </section>
        <div class="clearfix"></div>
    </div>
</div>