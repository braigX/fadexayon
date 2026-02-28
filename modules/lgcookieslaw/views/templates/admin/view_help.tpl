{**
 * Copyright 2024 LÍNEA GRÁFICA E.C.E S.L.
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
            <label for="installation" class="active-tab">{l s='Installation' mod='lgcookieslaw'}</label>
            <label for="configuration">{l s='Configuration' mod='lgcookieslaw'}</label>
            <label for="general_settings">{l s='General Settings' mod='lgcookieslaw'}</label>
            <label for="banner_settings">{l s='Banner Settings' mod='lgcookieslaw'}</label>
            <label for="button_settings">{l s='Button Settings' mod='lgcookieslaw'}</label>
            <label for="purposes">{l s='Purposes' mod='lgcookieslaw'}</label>
            <label for="cookies">{l s='Cookies' mod='lgcookieslaw'}</label>
            <label for="user_consents">{l s='User Consents' mod='lgcookieslaw'}</label>
            <label for="troubleshooting">{l s='Troubleshooting' mod='lgcookieslaw'}</label>
        </section>

        <input name="tab" id="installation" type="radio" checked />
        <section class="tab-content">
            <h2>{l s='Installation' mod='lgcookieslaw'}</h2>
            <p>{l s='Installation of this module is simple and fast. Attending these steps we will have installed it in a few minutes in our Prestashop platform.' mod='lgcookieslaw'}</p>
            <ol>
                <li>{l s='Go to the Modules Menu > Modules and Services' mod='lgcookieslaw'}</li>
                <li>{l s='Click on Add a new module (on the top right corner)' mod='lgcookieslaw'}</li>
                <li>{l s='Click on choose a file' mod='lgcookieslaw'}</li>
                <li>{l s='Select the file:' mod='lgcookieslaw'} <strong>{l s='lgcookieslaw.zip' mod='lgcookieslaw'}</strong></li>
                <li>{l s='Click on upload this module' mod='lgcookieslaw'}</li>
                <li>{l s='Find the' mod='lgcookieslaw'} <strong>{l s='EU Cookie Law (Notification Banner + Cookie Blocker)' mod='lgcookieslaw'}</strong></li>
                <li>{l s='Click on Install' mod='lgcookieslaw'}</li>
                <li>{l s='Click on Configure' mod='lgcookieslaw'}</li>
            </ol>
            <p>{l s='If you get an error during the installation, please read the section:' mod='lgcookieslaw'} <a href="{$lg_help_url|escape:'quotes':'UTF-8'}&help_tab=troubleshooting">{l s='Troubleshooting' mod='lgcookieslaw'}</a></p>
        </section>

        <input name="tab" id="configuration" type="radio" />
        <section class="tab-content">
            <h2>{l s='Configuration' mod='lgcookieslaw'}</h2>
            <ol>
                <li><a href="{$lg_help_url|escape:'quotes':'UTF-8'}&help_tab=general_settings">{l s='General Settings' mod='lgcookieslaw'}</a></li>
                <li><a href="{$lg_help_url|escape:'quotes':'UTF-8'}&help_tab=banner_settings">{l s='Banner Settings' mod='lgcookieslaw'}</a></li>
                <li><a href="{$lg_help_url|escape:'quotes':'UTF-8'}&help_tab=button_settings">{l s='Button Settings' mod='lgcookieslaw'}</a></li>
                <li><a href="{$lg_help_url|escape:'quotes':'UTF-8'}&help_tab=purposes">{l s='Purposes' mod='lgcookieslaw'}</a></li>
                <li><a href="{$lg_help_url|escape:'quotes':'UTF-8'}&help_tab=cookies">{l s='Cookies' mod='lgcookieslaw'}</a></li>
                <li><a href="{$lg_help_url|escape:'quotes':'UTF-8'}&help_tab=user_consents">{l s='User Consents' mod='lgcookieslaw'}</a></li>
            </ol>
        </section>

        <input name="tab" id="general_settings" type="radio" />
        <section class="tab-content">
            <h2>{l s='General Settings' mod='lgcookieslaw'}</h2>
            <ol>
                <li><strong>{l s='Hook position' mod='lgcookieslaw'}</strong>
                    <p>{l s='Position where the banner will be installed in the store. If your store does not have these hooks, consult the section:' mod='lgcookieslaw'} <a href="{$lg_help_url|escape:'quotes':'UTF-8'}&help_tab=troubleshooting"><strong>{l s='Troubleshooting' mod='lgcookieslaw'}</strong></a></p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}hook_position.png" />
                </li>
                <li><strong>{l s='Enable by default third parties cookies' mod='lgcookieslaw'}</strong>
                    <p>{l s='This option is to leave the default purposes enabled. If the user has already saved their preferences, these will prevail over this option.' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}third_parties.png" />
                </li>
                <li><strong>{l s='Reload the page after accepting cookies' mod='lgcookieslaw'}</strong>
                    <p>{l s='This option allows the page to reload when a user saves their preferences. Useful if you need cookies to be added immediately without the user continuing to browse.' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}reload_page.png" />
                </li>
                <li><strong>{l s='Block site navigation' mod='lgcookieslaw'}</strong>
                    <p>{l s='Allows the user to lock the screen through an overlay so that they can only interact with the banner.' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}block_navigation.png" />
                    <p>{l s='On the front it looks like this:' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}block_navigation_overlay.png" />
                </li>
                <li><strong>{l s='Show button to close banner' mod='lgcookieslaw'}</strong>
                    <p>{l s='Allows you to close the banner so that the user can continue browsing even without saving their preferences.' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}close_banner_button.png" />
                    <p>{l s='On the front it looks like this:' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}close_banner_button_active.png" />
                </li>
                <li><strong>{l s='Reject cookies when closing the banner' mod='lgcookieslaw'}</strong>
                    <p>{l s='This option allows customers to reject all cookies when closing the banner by clicking on the close button. If it is not enabled, the user will simply continue browsing and as soon as he reloads or accesses another area of the store, the banner will appear again.' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}reject_cookies_when_closing_banner.png" />
                </li>
                <li><strong>{l s='Show fixed button to open banner' mod='lgcookieslaw'}</strong>
                    <p>{l s='Once the preferences are saved, you can access them again if you have this button activated.' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}fixed_button.png" />
                    <p>{l s='On the front it looks like this:' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}fixed_button_active.png" />
                    <p>{l s='If you have activated it, you can select which side it is on: left or right.' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}fixed_button_position.png" />
                    <p>{l s='You can also choose the color of the button cookie icon.' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}fixed_button_svg_color.png" />
                </li>
                <li><strong>{l s='Add revoke consent button' mod='lgcookieslaw'}</strong>
                    <p>{l s='This option enables a button to access the user preferences revocation URL.' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}revoke_consent_button.png" />
                    <p>{l s='On the front it looks like this:' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}revoke_consent_button_active.png" />
                    <p>{l s='If you don\'t turn it on, you can still tell your users what the revocation URL is. This link can be taken directly from the configuration of the module:' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}disallow_url.png" />
                </li>
                <li><strong>{l s='Show the banner in the CMS of the Cookies Policy' mod='lgcookieslaw'}</strong>
                    <p>{l s='This option will cause the banner to be displayed or not in the selected CMS to show the text of the Cookies Policy.' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}show_banner_in_cms.png" />
                </li>
                <li><strong>{l s='Save user consent' mod='lgcookieslaw'}</strong>
                    <p>{l s='This option allows you to save the user\'s consent in the database (related by their IP address) and to have the possibility of printing it in a PDF file.' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}save_user_content.png" />
                </li>
                <li><strong>{l s='Anonymize IP' mod='lgcookieslaw'}</strong>
                    <p>{l s='This option can be used to save the consent IP address in a masked way to preserve the user\'s rights.' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}anonymize_ip.png" />
                </li>
                <li><strong>{l s='Delete user consent' mod='lgcookieslaw'}</strong>
                    <p>{l s='It simply removes from the database (and the file if it exists) those expired consents.' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}delete_user_content.png" />
                </li>
                <li><strong>{l s='Enable Consent Mode' mod='lgcookieslaw'}</strong>
                    <p>{l s='If a module is compatible with "Consent Mode" it will not be necessary to block it, it will manage itself based on the saved preferences.' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}enable_consent_mode.png" />
                    <p>{l s='You can get more information about "Consent Mode" at the following link:' mod='lgcookieslaw'} <a title="{l s='Google Consent Mode' mod='lgcookieslaw'}" target="_blank" href="https://support.google.com/analytics/answer/9976101?hl={l s='en' mod='lgcookieslaw'}">{l s='Google Consent Mode' mod='lgcookieslaw'}</a></p>
                </li>
                <li><strong>{l s='Preview mode' mod='lgcookieslaw'}</strong>
                    <p>{l s='This mode allows only one person (the webmaster) to preview the notification banner and configure the module: the banner won\'t disappear and will only be visible by you.' mod='lgcookieslaw'}</p>
                    <p>{l s='The module is in Preview mode by default.' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}preview_mode.png" />
                    <p>{l s='Enable this option to preview the cookie banner in your front-office without bothering your customers (when the preview mode is enabled, the banner doesn\'t disappear, the module doesn\'t block cookies and only the person using the IP below is able to see the cookie banner).' mod='lgcookieslaw'}</p>
                    <p>{l s='You have to click on Add IP and Save to be this person.' mod='lgcookieslaw'}</p>
                    <p><strong>{l s='IMPORTANT: Disable the Preview mode when you have finished configuring the banner.' mod='lgcookieslaw'}</strong></p>
                </li>
                <li><strong>{l s='Cookie lifetime (seconds)' mod='lgcookieslaw'}</strong>
                    <p>{l s='Set the cookie lifetime (in seconds) during which the user content will be saved (1 year = 31536000s).' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}cookie_lifetime.png" />
                    <p>{l s='It allows the module not to ask users their consent for the use of cookies every time they come to your store.' mod='lgcookieslaw'}</p>
                    <p>{l s='The module only asks the user consent at the first visit and then keeps the consent during the time set above.' mod='lgcookieslaw'}</p>
                    <p>{l s='Once the time is over, the module will ask again the user consent. According to the CNIL (French organisation), the cookie lifetime can not be longer than 13 months.' mod='lgcookieslaw'}</p>
                </li>
                <li><strong>{l s='Cookie name' mod='lgcookieslaw'}</strong>
                    <p>{l s='Give a relevant name to the cookie used by our module to keep the user content (name by default: lgcookieslaw).' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}cookie_name.png" />
                    <p>{l s='Don\'t use space inside the cookie name (use _ or - instead).' mod='lgcookieslaw'}</p>
                </li>
                <li><strong>{l s='Use $_COOKIE var' mod='lgcookieslaw'}</strong>
                    <p>{l s='Enabling this option saves the user\'s preferences in the system cookie and not in the Prestashop one.' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}use_cookie_var.png" />
                    <p>{l s='We recommend that if you use this configuration, you add this cookie, by default it has the name lgcookieslaw, within the purpose of functional cookies with the expiration time that you have applied in the configuration.' mod='lgcookieslaw'}</p>
                    <p><strong>{l s='IMPORTANT: If you do not have any problems or do not have the necessary knowledge, do not activate this option.' mod='lgcookieslaw'}</strong></p>
                </li>
                <li><strong>{l s='Load the Fancybox plugin from this module' mod='lgcookieslaw'}</strong>
                    <p>{l s='This plugin comes by default in all stores but in some cases it does not load. If this happens to you, activate this option, if on the contrary your store works fine, leave it deactivated.' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}load_fancybox.png" />
                    <p><strong>{l s='IMPORTANT: Do not activate this option if the Fancybox plugin is loading from your store correctly.' mod='lgcookieslaw'}</strong></p>
                </li>
                <li><strong>{l s='Enables compatibility with the Page Ultimate Cache or Super Speed module' mod='lgcookieslaw'}</strong>
                    <p>{l s='This option makes it compatible with the Page Ultimate Cache or Super Speed module, only enable it if you have this module, otherwise leave it disabled.' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}puc_compatibility.png" />
                </li>
                <li><strong>{l s='SEO protection' mod='lgcookieslaw'}</strong>
                    <p>{l s='The module is made to prevent the search engine bots below from seeing the cookie warning banner when they crawl your website.' mod='lgcookieslaw'}</p>
                    <p><strong>{l s='Don\'t clear out the field below it is made to protect the SEO of your store.' mod='lgcookieslaw'}</strong></p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}seo_protection.png" />
                    <p>{l s='This list includes the main search engine bots in order to hide the notification banner when they crawl your web to protect the SEO of your store.' mod='lgcookieslaw'}</p>
                    <p>{l s='Don\'t hesitate to update this list at any time if you know other search engine bots.' mod='lgcookieslaw'}</p>
                </li>
            </ol>
        </section>

        <input name="tab" id="banner_settings" type="radio" />
        <section class="tab-content">
            <h2>{l s='Banner Settings' mod='lgcookieslaw'}</h2>
            <ol>
                <li><strong>{l s='Banner position' mod='lgcookieslaw'}</strong>
                    <p>{l s='Choose the position of the cookie banner.' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}banner_position.png" />
                    <p>{l s='It will be displayed differently if you choose the "Top" or "Bottom" position.' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}banner_position_top.png" />
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}banner_position_bottom.png" />
                    <p>{l s='A if you choose the "Floating / Centered" option.' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}banner_position_floating.png" />
                </li>
                <li><strong>{l s='Background color' mod='lgcookieslaw'}</strong>
                    <p>{l s='Choose the background colour of the cookie banner.' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}background_color.png" />
                </li>
                <li><strong>{l s='Background opacity' mod='lgcookieslaw'}</strong>
                    <p>{l s='Set the level of opacity (transparency) of the banner.' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}background_opacity.png" />
                </li>
                <li><strong>{l s='Shadow color' mod='lgcookieslaw'}</strong>
                    <p>{l s='Choose the shadow colour of the cookie banner.' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}shadow_color.png" />
                    <p>{l s='Example in red.' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}example_box_shadow.png" />
                </li>
                <li><strong>{l s='Font color' mod='lgcookieslaw'}</strong>
                    <p>{l s='Choose the text colour of the cookie banner.' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}font_color.png" />
                </li>
                <li><strong>{l s='Banner message' mod='lgcookieslaw'}</strong>
                    <p>{l s='Write and customize the style of the cookie text. Use the edition bar to customoze the text (bold, italic, underline, colour, font, size, link, center...).' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}banner_message.png" />
                    <p>{l s='If you have a multilingual store, click on the button next to the text field to switch languages (automatic detection of the languages of the store) and add a different text by language.' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}banner_message_lang.png" />
                </li>
            </ol>
        </section>

        <input name="tab" id="button_settings" type="radio" />
        <section class="tab-content">
            <h2>{l s='Button Settings' mod='lgcookieslaw'}</h2>
            <ol>
                <li><strong>{l s='"Accept" button title' mod='lgcookieslaw'}</strong>
                    <p>{l s='Accept button title.' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}accept_button_title.png" />
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}accept_button_title_front.png" />
                    <p>{l s='If you have a multilingual store, click on the button at the right of the field to switch languages (automatic detection of the languages of your store) and customize the name of the button in all the languages of your store.' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}accept_button_title_lang.png" />
                </li>
                <li><strong>{l s='"Accept" button background color' mod='lgcookieslaw'}</strong>
                    <p>{l s='Choose the background colour of the button "Accept".' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}accept_button_background_color.png" />
                </li>
                <li><strong>{l s='"Accept" button font color' mod='lgcookieslaw'}</strong>
                    <p>{l s='Choose the text colour of the button "Accept".' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}accept_button_font_color.png" />
                </li>
                <li><strong>{l s='"Cookie policy" link title' mod='lgcookieslaw'}</strong>
                    <p>{l s='Choose the title of the "Cookie policy" link.' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}info_link_title.png" />
                    <p>{l s='If you have a multilingual store, click on the button at the right of the field to switch languages (automatic detection of the languages of your store) and customize the name of the button in all the languages of your store.' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}info_link_title_lang.png" />
                </li>
                <li><strong>{l s='CMS URL of "Cookie policy" link' mod='lgcookieslaw'}</strong>
                    <p>{l s='Choose the cms page to which you want users to go when they click on the "Cookie policy" link.' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}info_link_id_cms.png" />
                    <p>{l s='To customize the content of this page, go to the menu "Preferences → CMS".' mod='lgcookieslaw'}</p>
                    <p>{l s='You can edit an existing page or create a new page dedicated to the use of cookies.' mod='lgcookieslaw'}</p>
                </li>
                <li><strong>{l s='"Cookie policy link target' mod='lgcookieslaw'}</strong>
                    <p>{l s='Choose the way to open the "Cookie policy link (in a new or in the same window).' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}info_link_target.png" />
                </li>
                <li><strong>{l s='Show "Reject" button' mod='lgcookieslaw'}</strong>
                    <p>{l s='If you enable this option, the "Reject" button will be displayed on the banner.' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}show_reject_button.png" />
                </li>
                <li><strong>{l s='"Reject" button title' mod='lgcookieslaw'}</strong>
                    <p>{l s='Reject button title.' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}reject_button_title.png" />
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}reject_button_title_front.png" />
                    <p>{l s='If you have a multilingual store, click on the button at the right of the field to switch languages (automatic detection of the languages of your store) and customize the name of the button in all the languages of your store.' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}reject_button_title_lang.png" />
                </li>
                <li><strong>{l s='"Reject" button background color' mod='lgcookieslaw'}</strong>
                    <p>{l s='Choose the background colour of the button "Reject".' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}reject_button_background_color.png" />
                </li>
                <li><strong>{l s='"Reject" button font color' mod='lgcookieslaw'}</strong>
                    <p>{l s='Choose the text colour of the button "Reject".' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}reject_button_font_color.png" />
                </li>
            </ol>
        </section>

        <input name="tab" id="purposes" type="radio" />
        <section class="tab-content">
            <h2>{l s='Purposes' mod='lgcookieslaw'}</h2>
            <p>{l s='In this section you configure the purpose that best corresponds to your cookies. By default you will find 5 types:' mod='lgcookieslaw'}</p>
            <ol>
                <li><strong>{l s='Functional cookies' mod='lgcookieslaw'}</strong>
                    <p>{l s='Functional cookies are strictly necessary to provide the services of the shop, as well as for its correct functioning, for this reason you must not delete this functionality and it must always be activated and the Technical" field must be activated.' mod='lgcookieslaw'}</p>
                </li>
                <li><strong>{l s='Advertising cookies' mod='lgcookieslaw'}</strong>
                    <p>{l s='Advertising cookies collect information about the advertisements shown to users of the website.' mod='lgcookieslaw'}</p>
                </li>
                <li><strong>{l s='Performance cookies' mod='lgcookieslaw'}</strong>
                    <p>{l s='Analytical cookies collect information about the user\'s browsing experience in the shop. It will be the default purpose that will activate Google\'s "Consent Mode". For more information, please read the section:' mod='lgcookieslaw'} <a href="{$lg_help_url|escape:'quotes':'UTF-8'}&help_tab=general_settings"><strong>{l s='General Settings' mod='lgcookieslaw'}</strong></a></p>
                </li>
                <li><strong>{l s='Performance cookies' mod='lgcookieslaw'}</strong>
                    <p>{l s='Performance cookies are used to improve the browsing experience and optimize the operation of the shop.' mod='lgcookieslaw'}</p>
                </li>
                <li><strong>{l s='Other cookies' mod='lgcookieslaw'}</strong>
                    <p>{l s='These are cookies without a clear purpose or those that we are still in the process of classifying.' mod='lgcookieslaw'}</p>
                </li>
            </ol>
            <p><strong>{l s='REMEMBER: You can modify the default cookie types, add new ones or delete existing ones. We remind you not to delete functional cookies.' mod='lgcookieslaw'}</strong></p>
            <h3>{l s='Configuration purposes' mod='lgcookieslaw'}</h3>
            <ol>
                <li><strong>{l s='Active' mod='lgcookieslaw'}</strong>
                    <p>{l s='This option allows to show or not show the purpose on the banner.' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}purpose_active.png" />
                </li>
                <li><strong>{l s='Name' mod='lgcookieslaw'}</strong>
                    <p>{l s='Choose the name of the purpose.' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}purpose_name.png" />
                    <p>{l s='If you have a multilingual store, click on the button at the right of the field to switch languages (automatic detection of the languages of your store) and customize the name of the purpose in all the languages of your store.' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}purpose_name_lang.png" />
                </li>
                <li><strong>{l s='Description' mod='lgcookieslaw'}</strong>
                    <p>{l s='Describes the purpose of cookies.' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}purpose_description.png" />
                    <p>{l s='If you have a multilingual store, click on the button at the right of the field to switch languages (automatic detection of the languages of your store) and customize the description of the purpose in all the languages of your store.' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}purpose_description_lang.png" />
                </li>
                <li><strong>{l s='Technical' mod='lgcookieslaw'}</strong>
                    <p>{l s='Does it contain technical cookies?' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}purpose_technical.png" />
                    <p>{l s='Select "Yes" if you want this purpose to be mandatory.' mod='lgcookieslaw'}</p>
                </li>
                <li><strong>{l s='Locked modules' mod='lgcookieslaw'}</strong>
                    <p>{l s='This block you can see the modules that you can block for this purpose.' mod='lgcookieslaw'}</p>
                    <p><strong>{l s='IMPORTANT: You can only block modules in a purpose if it is not a purpose that installs technical cookies.' mod='lgcookieslaw'}</strong></p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}purpose_modules_block.png" />
                </li>
                <li><strong>{l s='Associated cookies' mod='lgcookieslaw'}</strong>
                    <p>{l s='This block shows the cookies created in the Cookies tab and associated with a purpose. You can modify or delete them.' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}purpose_cookies_block.png" />
                </li>
                <li><strong>{l s='Manual lock' mod='lgcookieslaw'}</strong>
                    <p>{l s='You can block scripts located in both TPL templates and JS files using the following code:' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}purpose_manual_lock.png" />
                    <p><strong>{l s='IMPORTANT: You can only block scripts in a purpose if it is not a purpose that installs technical cookies.' mod='lgcookieslaw'}</strong></p>
                </li>
                <li><strong>{l s='For Consent Mode' mod='lgcookieslaw'}</strong>
                    <p>{l s='This option is necessary to enable compatibility with the "Consent Mode" of Google applications for this purpose.' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}purpose_consent_mode.png" />
                </li>
                <li><strong>{l s='Consent Type' mod='lgcookieslaw'}</strong>
                <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}purpose_consent_type.png" />
                <p>{l s='You must choose one of the consent types from Google\'s "Consent Mode". Below, we show you the available consents and for what purposes it is recommended that you use them:' mod='lgcookieslaw'}</p>
                <table class="table table-responsive" id="table_recent_orders">
                    <thead>
                        <tr>
                            <th><span class="title_box">{l s='Name' mod='lgcookieslaw'}</span></th>
                            <th><span class="title_box">{l s='Description' mod='lgcookieslaw'}</span></th>
                            <th><span class="title_box">{l s='Purpose' mod='lgcookieslaw'}</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>functionality_storage</strong></td>
                            <td>{l s='Enables storage that supports the functionality of the website or app e.g. language settings' mod='lgcookieslaw'}</td>
                            <td>{l s='For technical purposes' mod='lgcookieslaw'}</td>
                        </tr>
                        <tr>
                            <td><strong>ad_storage</strong></td>
                            <td>{l s='Enables storage (such as cookies) related to advertising. With this option the parameters ad_user_data and ad_personalization will also be configured.' mod='lgcookieslaw'}</td>
                            <td>{l s='For advertising purposes' mod='lgcookieslaw'}</td>
                        </tr>
                            <td><strong>analytics_storage</strong></td>
                            <td>{l s='Enables storage (such as cookies) related to analytics e.g. visit duration' mod='lgcookieslaw'}</td>
                            <td>{l s='For analytics purposes' mod='lgcookieslaw'}</td>
                        </tr>
                    </tbody>
                </table>
                <div class="alert alert-warning warn">
                    <p>{l s='For the module\'s consent mode to work in your shop, a GTM script must be pre-loaded and your GTM container must be correctly configured to comply with consent mode v2.' mod='lgcookieslaw'}</p>
                </div>
            </li>
            </ol>
        </section>

        <input name="tab" id="cookies" type="radio" />
        <section class="tab-content">
            <h2>{l s='Cookies' mod='lgcookieslaw'}</h2>
            <p>{l s='This section you can create, edit or delete the cookies generated by your shop, associate them with a purpose and block them.' mod='lgcookieslaw'}</p>
            <h3>{l s='Configuration cookies' mod='lgcookieslaw'}</h3>
            <ol>
                <li><strong>{l s='Active' mod='lgcookieslaw'}</strong>
                    <p>{l s='This option allows to display this cookie within the purpose. It will also allow you to delete the cookie when the consent is revoked in whole or in part.' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}create_cookie_active.png" />
                </li>
                <li><strong>{l s='Name' mod='lgcookieslaw'}</strong>
                    <p>{l s='Enter the name of the cookie.' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}create_cookie_cookie_name.png" />
                    <p><strong>{l s='IMPORTANT: If the name of the cookie is not fixed but is created dynamically, example: prestashop-123456789, you must enter the name as prestashop-#, replacing the dynamic part with a "#". But you can also put the cookie name literally in case you know it. In this way the module will be able to eliminate the cookie.' mod='lgcookieslaw'}</strong></p>
                </li>
                <li><strong>{l s='Purpose' mod='lgcookieslaw'}</strong>
                    <p>{l s='Select the purpose of the cookie.' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}create_cookie_purpose.png" />
                </li>
                <li><strong>{l s='Provider' mod='lgcookieslaw'}</strong>
                    <p>{l s='Enter the name of the provider that is associated with the cookie.' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}create_cookie_provider.png" />
                </li>
                <li><strong>{l s='Provider URL' mod='lgcookieslaw'}</strong>
                    <p>{l s='Type the URL of the provider that is associated to the cookie.' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}create_cookie_provider_url.png" />
                </li>
                <li><strong>{l s='Cookie Purpose' mod='lgcookieslaw'}</strong>
                    <p>{l s='In this field you can write a short description of the purpose of this cookie in the shop to let users know more about it.' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}create_cookie_purpose_description.png" />
                </li>
                <li><strong>{l s='Expiry Time' mod='lgcookieslaw'}</strong>
                    <p>{l s='Set the expiry time of the cookie. This value is only informative for the user and will be displayed on the front end.' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}create_cookie_expiry_time.png" />
                </li>
            </ol>
            <h3>{l s='Advanced Settings' mod='lgcookieslaw'}</h3>
            <p>{l s='In this section you will be able to enable it if you want your cookie to add a script.' mod='lgcookieslaw'}</p>
            <ol>
                <li><strong>{l s='Install Script' mod='lgcookieslaw'}</strong>
                    <p>{l s='This option allows you to enable advanced cookie settings, necessary to install a script directly from the module.' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}create_cookie_install_script.png" />
                    <p><strong>{l s='IMPORTANT: The module is only capable of blocking the scripts that install these cookies or blocking modules that install cookies.' mod='lgcookieslaw'}</strong></p>
                </li>
                <li><strong>{l s='Script Hook' mod='lgcookieslaw'}</strong>
                    <p>{l s='In which hook you want the script to install.' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}create_cookie_script_hook.png" />
                    <p><strong>{l s='IMPORTANT: In case of not having enough knowledge, leave the option checked by default.' mod='lgcookieslaw'}</strong></p>
                </li>
                <li><strong>{l s='Script Code' mod='lgcookieslaw'}</strong>
                    <p>{l s='Code of the script that will install the cookie. You can use HTML, JS or Smarty code.' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}create_cookie_script_code.png" />
                    <p>{l s='If you have a multilingual store, click on the button next to the text field to switch languages (automatic detection of the languages of the store) and add a different text by language.' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}create_cookie_script_code_lang.png" />
                </li>
                <li><strong>{l s='Add Script Tag' mod='lgcookieslaw'}</strong>
                    <p>{l s='Add the script inside the code:' mod='lgcookieslaw'}</p>
                    <code>{'<script type="text/javascript"> SCRIPT </script>'|escape:'htmlall':'UTF-8'}</code><br>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}create_cookie_add_script_tag.png" />
                </li>
                <li><strong>{l s='Add Script Literal' mod='lgcookieslaw'}</strong>
                    <p>{l s='If the code already has the tags SCRIPT or it is not a SMARTY code, you must check this option so that it adds the code literally to the chosen area.' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}create_cookie_add_script_literal.png" />
                    <p><strong>{l s='IMPORTANT: In case of not having enough knowledge, leave the option checked by default.' mod='lgcookieslaw'}</strong></p>
                </li>
                <li><strong>{l s='Script Notes' mod='lgcookieslaw'}</strong>
                    <p>{l s='This field is only used to leave a reminder note regarding the scripts that only the administrator will see.' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}create_cookie_script_notes.png" />
                </li>
            </ol>
        </section>

        <input name="tab" id="user_consents" type="radio" />
        <section class="tab-content">
            <h2>{l s='User Consents' mod='lgcookieslaw'}</h2>
            <p>{l s='In this section you will see a record of user consents. You have the option to download the consent or to delete it. Removing a user\'s consent means that you are revoking their consent and the banner for that IP address will reappear.' mod='lgcookieslaw'}</p>
            <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}user_consents_list.png" />
            <p>{l s='Once the user has saved their preferences, they can download the consent registered in the store.' mod='lgcookieslaw'}</p>
            <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}user_consents_front.png" />
            <p>{l s='All the data that has been saved from the user\'s consent is recorded in the PDF.' mod='lgcookieslaw'}</p>
            <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}user_consents_pdf.png" />
        </section>

        <input name="tab" id="troubleshooting" type="radio" />
        <section class="tab-content troubleshooting">
            <h2>{l s='Troubleshooting' mod='lgcookieslaw'}</h2>
            <p>{l s='All the common errors about this module have been reported below and we explain to you in details how to solve them.' mod='lgcookieslaw'}</p>
            <p>{l s='Please read the section that corresponds to your problema BEFORE getting in touch with us, you will probably find the answer to your problem in it.' mod='lgcookieslaw'}</p>
            <ol>
                <li><a href="#block1">{l s='I don\'t manage to install the module' mod='lgcookieslaw'}</a>
                    <ol>
                        <li><a href="#block1_1">{l s='File too large' mod='lgcookieslaw'}</a></li>
                        <li><a href="#block1_2">{l s='Method already overriden' mod='lgcookieslaw'}</a></li>
                    </ol>
                </li>
                <li><a href="#block2">{l s='The banner doesn\'t appear on my store' mod='lgcookieslaw'}</a></li>
                <li><a href="#block3">{l s='The banner never disappears on my store' mod='lgcookieslaw'}</a></li>
                <li><a href="#block4">{l s='Google consent mode v2 is not working on my store' mod='lgcookieslaw'}</a></li>
                <li><a href="#block5">{l s='The disabled cookies keep appearing' mod='lgcookieslaw'}</a></li>
                <li><a href="#block6">{l s='The banner and the module fields are empty' mod='lgcookieslaw'}</a></li>
                <li><a href="#block7">{l s='I have not disabled the cookies on my store' mod='lgcookieslaw'}</a></li>
                <li><a href="#block8">{l s='The page is refreshed after accepting cookies' mod='lgcookieslaw'}</a></li>
                <li><a href="#block9">{l s='After saving the consent, a 502 error appears in some areas of the store' mod='lgcookieslaw'}</a></li>
                <li>
                    <a href="#block10">{l s='Consent is not saved when I have an active cache module' mod='lgcookieslaw'}</a>
                    <ol>
                        <li><a href="#block10_1">{l s='JPresta - Page Cache Ultimate / Super Speed' mod='lgcookieslaw'}</a></li>
                        <li><a href="#block10_2">{l s='LiteSpeed Cache Plugin' mod='lgcookieslaw'}</a></li>
                        <li><a href="#block10_3">{l s='Advanced page cache' mod='lgcookieslaw'}</a></li>
                    </ol>
                </li>
            </ol>
            <p>{l s='If the problem you are having is not listed, then please get in touch with us.' mod='lgcookieslaw'}</p>
            <ol>
                <li id="block1"><strong>{l s='I don\'t manage to install the module' mod='lgcookieslaw'}</strong>
                    <ol>
                        <li id="block1_1"><strong>{l s='File too large' mod='lgcookieslaw'}</strong>
                            <p><i>{l s='Oops.. Upload failed.' mod='lgcookieslaw'}</i></p>
                            <p><strong>{l s='Reason of the error:' mod='lgcookieslaw'}</strong> {l s='The problem comes from the value of your "upload_max_filesize" variable that is not large enough compared to the module size.' mod='lgcookieslaw'}</p>
                            <p><strong>{l s='To solve the problem, you can either.' mod='lgcookieslaw'}</strong></p>
                            <p>{l s='- Go to your FTP and increase the value of your "upload_max_filesize" variable in the file "/config/ config.inc.php" or in the file "php.ini"' mod='lgcookieslaw'} <i>{l s='ini_set("upload_max_filesize", "100M");' mod='lgcookieslaw'}</i></p>
                            <p>{l s='- Or install the module directly from your FTP, you just need to unzip the module zip file and copy/paste the folder "lgcookieslaw" inside the folder "modules" of your FTP (the module will appear in your back-office as soon as the module folder is added into the "modules" folder).' mod='lgcookieslaw'}</p>
                        </li>
                        <li id="block1_2"><strong>{l s='Method getHookModuleExecList already overriden' mod='lgcookieslaw'}</strong>
                            <p>{l s='When installing the module, you may encounter this error message:' mod='lgcookieslaw'} <i>{l s='lgcookieslaw: Unable to install override: The method getHookModuleExecList in the classHook is already overriden.' mod='lgcookieslaw'}</i></p>
                            <p><strong>{l s='Reason of the error:' mod='lgcookieslaw'}</strong> {l s='On PrestaShop 1.5, 1.6 and 1.7, the module automatically creates an override (required to make sure the module work) inside the folder override/classes/Hook.php. The problem is that you already have an override with the same name (created by another module), which prevents our module from installing.' mod='lgcookieslaw'}</p>
                            <p><strong>{l s='To solve the problem, you have to:' mod='lgcookieslaw'}</strong></p>
                            <p>{l s='a) Connect to your FTP' mod='lgcookieslaw'}</p>
                            <p>{l s='b) Go to the folder' mod='lgcookieslaw'} <i>{l s='Override → Classes' mod='lgcookieslaw'}</i> {l s='and find the file' mod='lgcookieslaw'} <i>{l s='Hook.php' mod='lgcookieslaw'}</i></p>
                            <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}compatibility_hook.png" />
                            <p>{l s='c) Rename the file' mod='lgcookieslaw'} <i>{l s='Hook.php' mod='lgcookieslaw'}</i> {l s='to' mod='lgcookieslaw'} <i>{l s='Hook2.php' mod='lgcookieslaw'}</i></p>
                            <p>{l s='d) Go to your backoffice and install the cookie module' mod='lgcookieslaw'}</p>
                            <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}install_module.png" />
                            <p>{l s='e) The module will be installed and a new file Hook.php will be created on your FTP' mod='lgcookieslaw'}</p>
                            <p>{l s='f) Combine both overrides manually: open the old override' mod='lgcookieslaw'} <i>{l s='Hook2.php' mod='lgcookieslaw'}</i> {l s=', copy its content and paste it into the new override' mod='lgcookieslaw'} <i>{l s='Hook.php' mod='lgcookieslaw'}</i></p>
                            <p><strong>{l s='WARNING:' mod='lgcookieslaw'}</strong></p>
                            <p>{l s='To do it correctly, it\'s important to respect the file structure' mod='lgcookieslaw'}</p>
                            <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}hook_estructure.png" />
                            <p>{l s='If you also have the same functions in the old override, don\'t duplicate the functions but combine them.' mod='lgcookieslaw'}</p>
                        </li>
                    </ol>
                </li>
                <li id="block2"><strong>{l s='The banner doesn\'t appear on my store' mod='lgcookieslaw'}</strong>
                    <ol>
                        <li><strong>{l s='The preview mode should be disabled' mod='lgcookieslaw'}</strong>
                            <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}disabled_preview_mode.png" />
                        </li>
                        <li><strong>{l s='Clear out the cache of your store' mod='lgcookieslaw'}</strong>
                            <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}clear_cache.png" />
                        </li>
                        <li><strong>{l s='Check your template (show hook Footer)' mod='lgcookieslaw'}</strong>
                            <p>{l s='a) Connect to your FTP' mod='lgcookieslaw'}</p>
                            <p>{l s='b) Go to the folder "themes"' mod='lgcookieslaw'}</p>
                            <p>{l s='c) Go to the folder of your current template' mod='lgcookieslaw'}</p>
                            <p>{l s='d) Open the file footer.tpl(templates/_partials/footer.tpl in versions 1.7)' mod='lgcookieslaw'}</p>
                            <p>{l s='e) And find the following line:' mod='lgcookieslaw'} <code>{literal}{hook h='displayFooter'}{/literal}</code></p>
                            <p>{l s='If you don\'t find this line it means that the problem comes from your template. It is possible that this hook is blocked by the configurator of your template and you have a similar hook like hookDisplayFooterBefore or hookDisplayFooterBefore. If this is the case you would only have to install it in one of these two hooks.' mod='lgcookieslaw'}</p>
                            <p>{l s='To solve the problem: If you don\'t have one of these hooks just add the following line inside the footer.tpl file:' mod='lgcookieslaw'}</p>
                            <p><code>{literal}{hook h='displayFooter' mod='lgcookieslaw'}{/literal}</code></p>
                        </li>
                        <li><strong>{l s='Delete the existing cookies of your browser' mod='lgcookieslaw'}</strong>
                            <p>{l s='If you have already accepted the use of cookies, the consent cookie will keep your consent during the duration set in the module and the banner will only appear again when the time is over. To avoid waiting until then, you just need to delete the existing cookies of your browser to see the banner again.' mod='lgcookieslaw'}</p>
                        </li>
                        <li><strong>{l s='Make sure that the banner is not hidden by another module' mod='lgcookieslaw'}</strong>
                            <p>{l s='Go to the menu "Modules" → "Positions" and put our cookie module in first position' mod='lgcookieslaw'}</p>
                            <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}hooks_position.png" />
                        </li>
                        <li><strong>{l s='Non PrestaShop modules should not be disabled (PrestaShop 1.6 only)' mod='lgcookieslaw'}</strong>
                            <p>{l s='Go the menu' mod='lgcookieslaw'} <i>{l s='Advanced parameters → Performance' mod='lgcookieslaw'}</i></p>
                            <p>{l s='You should have this configuration:' mod='lgcookieslaw'}</p>
                            <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}debug_mode.png" />
                        </li>
                        <li><strong>{l s='Creative Elements or similar' mod='lgcookieslaw'}</strong>
                            <p>{l s='If you have installed a module like Creative Elements or similar you should check if the footer of your shop is generated by this module.' mod='lgcookieslaw'}
                            <p>{l s='You must add a shortcode to the footer that you have generated with your Creative Elements module or similar.' mod='lgcookieslaw'}</p>
                            <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}creative_elements.png" />
                            <p>{l s='Add the following code to the shortcode:' mod='lgcookieslaw'}</p>
                            <p><code>{literal}{hook h='displayFooter' mod='lgcookieslaw'}{/literal}</code></p>
                            <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}creative_elements_shortcode.png" />
                        </li>
                    </ol>
                </li>
                <li id="block3"><strong>{l s='The banner never disappears on my store' mod='lgcookieslaw'}</strong>
                    <ol>
                        <li><strong>{l s='The preview mode should be disabled' mod='lgcookieslaw'}</strong>
                            <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}disabled_preview_mode.png" />
                        </li>
                        <li><strong>{l s='The overrides should not be disabled (PrestaShop 1.6 only)' mod='lgcookieslaw'}</strong>
                            <p>{l s='Go the menu' mod='lgcookieslaw'} <i>{l s='Advanced parameters → Performance' mod='lgcookieslaw'}</i></p>
                            <p>{l s='You should have this configuration:' mod='lgcookieslaw'}</p>
                            <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}debug_mode.png" />
                        </li>
                        <li><strong>{l s='Don\'t put any space into the cookie name (use _ or - instead)' mod='lgcookieslaw'}</strong>
                            <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}cookie_name.png" />
                        </li>
                        <li><strong>{l s='Check the override of the Hook.php file' mod='lgcookieslaw'}</strong>
                            <p>{l s='a) Connect to your FTP' mod='lgcookieslaw'}</p>
                            <p>{l s='b) Go to the folder' mod='lgcookieslaw'} <i>{l s='Override → Classes' mod='lgcookieslaw'}</i> {l s='and find the file' mod='lgcookieslaw'} <i>{l s='Hook.php' mod='lgcookieslaw'}</i> {l s='(path: /override/classes/Hook.php)' mod='lgcookieslaw'}</p>
                            <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}compatibility_hook.png" />
                            <p>{l s='c) Open the file Hook.php and check if it contains the expression "lgcookieslaw" (CTRL+F)' mod='lgcookieslaw'}</p>
                        </li>
                    </ol>
                </li>
                <li id="block4"><strong>{l s='Google consent mode v2 is not working on my store' mod='lgcookieslaw'}</strong>
                    <ol>
                        <li><strong>{l s='Check your template (show hook displayAfterTitleTag)' mod='lgcookieslaw'}</strong>
                        <p>{l s='a) Connect to your FTP' mod='lgcookieslaw'}</p>
                        <p>{l s='b) Go to the folder "themes"' mod='lgcookieslaw'}</p>
                        <p>{l s='c) Go to the folder of your current template' mod='lgcookieslaw'}</p>
                        <p>{l s='d) Open the file head.tpl(templates/_partials/head.tpl in versions 1.7)' mod='lgcookieslaw'}</p>
                        <p>{l s='e) And find the following line:' mod='lgcookieslaw'} <code>{literal}{hook h='displayAfterTitleTag'}{/literal}</code></p>
                        <p>{l s='If you don\'t find this line it means that the problem comes from your template. It is possible that this hook is blocked by the configurator of your template and you have a similar hook like displayAfterTitle. If this is the case, you only need to install this hook.' mod='lgcookieslaw'}</p>
                        <p>{l s='To solve the problem: If you don\'t have one of these hooks just add the following line inside the head.tpl file after the <title> tag:' mod='lgcookieslaw'}</p>
                        <p><code>{literal}{hook h='displayAfterTitleTag'}{/literal}</code></p>
                        <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}consent_mode_v2.png" />
                    </li>
                    </ol>
                </li>
                <li id="block5"><strong>{l s='The disabled cookies keep appearing' mod='lgcookieslaw'}</strong>
                    <ol>
                        <li><strong>{l s='The preview mode should be disabled' mod='lgcookieslaw'}</strong>
                            <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}disabled_preview_mode.png" />
                        </li>
                        <li><strong>{l s='Delete the cookies of your browser and try again' mod='lgcookieslaw'}</strong></li>
                        <li><strong>{l s='The overrides should not be disabled (PS 1.6 y 1.7)' mod='lgcookieslaw'}</strong>
                            <p>{l s='Go the menu' mod='lgcookieslaw'} <i>{l s='Advanced parameters → Performance' mod='lgcookieslaw'}</i></p>
                            <p>{l s='You should have this configuration:' mod='lgcookieslaw'}</p>
                            <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}debug_mode.png" />
                        </li>
                        <li><strong>{l s='Check the override of the Hook.php file' mod='lgcookieslaw'}</strong>
                            <p>{l s='a) Connect to your FTP' mod='lgcookieslaw'}</p>
                            <p>{l s='b) Go to the folder' mod='lgcookieslaw'} <i>{l s='Override → Classes' mod='lgcookieslaw'}</i> {l s='and find the file' mod='lgcookieslaw'} <i>{l s='Hook.php' mod='lgcookieslaw'}</i> {l s='(path: /override/classes/Hook.php)' mod='lgcookieslaw'}</p>
                            <p>{l s='c) Open the file Hook.php and check if it contains the expression "lgcookieslaw" (CTRL+F)' mod='lgcookieslaw'}</p>
                        </li>
                        <li><strong>{l s='Check the cache of your store' mod='lgcookieslaw'}</strong>
                            <p>{l s='Connect to your FTP, go to the Cache folder and if you have a file named class_index.php, delete it' mod='lgcookieslaw'}</p>
                        </li>
                    </ol>
                </li>
                <li id="block6"><strong>{l s='The banner and the module fields are empty' mod='lgcookieslaw'}</strong>
                    <p>{l s='If the module doesn\'t save the configuration, you just need to reset the module (uninstall + reinstall) to solve the problem.' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}reset_module.png" />
                </li>
                <li id="block7"><strong>{l s='I have not disabled the cookies on my store' mod='lgcookieslaw'}</strong>
                    <p>{l s='The module doesn\'t block cookies automatically, you must tell it which modules it has to block.' mod='lgcookieslaw'}</p>
                    <p>{l s='We explain to you in details in this guide how to identify cookies and how to block them.' mod='lgcookieslaw'}</p>
                    <p>{l s='We will gladly help you if you need help configuring the module but we decline any responsibility if you don\'t follow these instructions and don\'t block correctly the cookies on your store.' mod='lgcookieslaw'}</p>
                </li>
                <li id="block8"><strong>{l s='The page is refreshed after accepting cookies' mod='lgcookieslaw'}</strong>
                    <p>{l s='Once customers give their consent and accept the use of cookies, our module activates the cookies that were previously blocked, but it\'s technically not possible to do it without reloading the page, the page has to be reloaded to load the blocked modules.' mod='lgcookieslaw'}</p>
                </li>
                <li id="block9"><strong>{l s='After saving the consent, a 502 error appears in some areas of the store' mod='lgcookieslaw'}</strong>
                    <p>{l s='If when saving cookies a 502 error appears in some areas, such as when logging in as a client:' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}error_502.png" />
                    <p>{l s='You could simply solve it by enabling the General Settings > Use $_COOKIE var option, it is a problem in the Prestashop cookie encoding and it happens in very few stores rarely.' mod='lgcookieslaw'}</p>
                    <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}use_cookie_var.png" />
                </li>
                <li id="block10"><strong>{l s='Consent is not saved when I have an active cache module' mod='lgcookieslaw'}</strong>
                    <p>{l s='You may notice module malfunction when you have any caching module installed, for example: JPresta - Page Cache Ultimate or LiteSpeed Cache Plugin.' mod='lgcookieslaw'}</p>
                    <p>{l s='We recommend that you contact the technical support of your module directly and that they help you configure it, but if you have sufficient knowledge you can do it yourself.' mod='lgcookieslaw'}</p>
                    <ol>
                        <li id="block10_1"><strong>{l s='JPresta - Page Cache / Super Speed' mod='lgcookieslaw'}</strong>
                            <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}jpresta_page_cache.png" />
                            <p><strong>{l s='Reason of the error:' mod='lgcookieslaw'}</strong> {l s='The module becomes static, the consent is not saved or once saved it cannot be revoked and the banner displayed again.' mod='lgcookieslaw'}</p>
                            <p><strong>{l s='To solve the problem, you can either.' mod='lgcookieslaw'}</strong></p>
                            <p>{l s='- To solve this problem you only need to upgrade to Page Cache Ultimate (or Speed Pack) v7.9.11 (at least) and check the lgcookieslaw as dynamic:' mod='lgcookieslaw'}</p>
                            <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}jpresta_page_cache_dynamic_module.png" />
                            <p>{l s='- They also have to add the following javascript code in the configuration of Page Cache Ultimate:' mod='lgcookieslaw'}</p>
                            <code>
                                {literal}if (typeof LGCookiesLawFront == 'function') {{/literal}<br>
                                {literal}&nbsp;&nbsp;&nbsp;&nbsp;var object_lgcookieslaw_front = new LGCookiesLawFront();{/literal}<br>
                                {literal}&nbsp;&nbsp;&nbsp;&nbsp;object_lgcookieslaw_front.init();{/literal}<br>
                                {literal}}{/literal}
                            </code><br>
                            <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}jpresta_page_cache_js_code.png" />
                            <p>{l s='Configure your Super Speed module with the same settings as indicated for the Page Cache Ultimate module. Do not forget to activate in the general settings of the cookie module the option "Enables compatibility with the Page Ultimate Cache or Super Speed module".' mod='lgcookieslaw'}</p>
                            <p><strong>{l s='IMPORTANT: We strongly recommend that if you do not have the necessary knowledge, you contact the module support. We cannot be held responsible for problems due to configuration errors, we only intend to help our customers.' mod='lgcookieslaw'}</strong></p>
                        </li>
                        <li id="block10_2"><strong>{l s='LiteSpeed Cache Plugin' mod='lgcookieslaw'}</strong>
                            <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}litespeed_cache_plugin.png" />
                            <p><strong>{l s='Reason of the error:' mod='lgcookieslaw'}</strong> {l s='The module becomes static, the consent is not saved or once saved it cannot be revoked and the banner displayed again.' mod='lgcookieslaw'}</p>
                            <p><strong>{l s='To solve the problem, you can either.' mod='lgcookieslaw'}</strong></p>
                            <p>{l s='From the support of the module they have sent us some recommendations that work in most of our clients:' mod='lgcookieslaw'}</p>
                            <p><strong>a)</strong> {l s='Disable LiteSpeed Cache Plugin (default settings).' mod='lgcookieslaw'}</p>
                            <p><strong>b)</strong> {l s='Configure "EU Cookie Law (Notification Banner + Cookie Blocker)": "Use $_COOKIE var": NO → YES' mod='lgcookieslaw'}</p>
                            <p><strong>c)</strong> {l s='Enable LiteSpeed Cache Plugin (default settings).' mod='lgcookieslaw'}</p>
                            <p><strong>d)</strong> {l s='Clear LiteSpeed Cache in backoffice.' mod='lgcookieslaw'}</p>
                            <p><strong>e)</strong>
                                {l s='In the .htaccess file add after:' mod='lgcookieslaw'}<br><br>
                                <code>
                                    {literal}### LITESPEED_CACHE_END{/literal}
                                </code><br><br>
                                {l s='the following code:' mod='lgcookieslaw'}<br><br>
                                <code>
                                    {literal}&lt;IfModule LiteSpeed&gt;{/literal}<br>
                                    {literal}&nbsp;&nbsp;&nbsp;&nbsp;RewriteRule .* - [E=Cache-Vary:lgcookieslaw]{/literal}<br>
                                    {literal}&lt;/IfModule&gt;{/literal}
                                </code><br><br>
                            </p>
                            <p>{l s='You can see more information in his' mod='lgcookieslaw'} <a href="https://docs.litespeedtech.com/lscache/lscps/settings/#customization-for-prestashop-17" target="_blank">{l s='documentation.' mod='lgcookieslaw'}</a></p>
                            <p><strong>{l s='IMPORTANT: We strongly recommend that if you do not have the necessary knowledge, you contact the module support. We cannot be held responsible for problems due to configuration errors, we only intend to help our customers.' mod='lgcookieslaw'}</strong></p>
                            <p><strong>{l s='IMPORTANT: We strongly recommend that if this solution does not work for you, you contact LiteSpeed support directly for a solution.' mod='lgcookieslaw'}</strong></p>
                        </li>
                        <li id="block10_3"><strong>{l s='Advanced page cache' mod='lgcookieslaw'}</strong>
                            <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}advanced_page_cache.png" />
                            <p><strong>{l s='Reason of the error:' mod='lgcookieslaw'}</strong> {l s='The module becomes static, the consent is not saved or once saved it cannot be revoked and the banner displayed again.' mod='lgcookieslaw'}</p>
                            <p><strong>{l s='To solve the problem, you can either.' mod='lgcookieslaw'}</strong></p>
                            <p>{l s='You must update the version of your module to 1.0.6 where the author has made the compatibility of both modules.' mod='lgcookieslaw'}</p>
                            <p>{l s='Check that the following hooks are not cached:' mod='lgcookieslaw'}</p>
                            <img src="{$lg_help_path|escape:'htmlall':'UTF-8'}advanced_page_cache_dynamic_content.png" />
                        </li>
                    </ol>
                </li>
            </ol>
        </section>
        <div class="clearfix"></div>
    </div>
</div>
