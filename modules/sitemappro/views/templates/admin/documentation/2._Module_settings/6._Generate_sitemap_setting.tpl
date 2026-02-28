{*
* 2007-2018 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    SeoSA <885588@bk.ru>
*  @copyright 2012-2022 SeoSA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<h2 class="text-center">{l s='Generate sitemap setting' mod='sitemappro'}</h2>
<hr>
{l s='Select the protocol for generation of' mod='sitemappro'} <strong>{l s='"HTTP"' mod='sitemappro'}</strong> {l s='or' mod='sitemappro'} <strong>{l s='"HTTPS".' mod='sitemappro'}</strong><br>
{l s='If you have a weak computer or low speed of the server, you can split the sitemap into several parts. Enter the quantity of items (products, categories, CMS pages) to the site map. The module generates several site maps from the appropriate value.' mod='sitemappro'}<br>
{get_image_lang_map path = '16.jpg'}
{l s='You can create sitemap for all languages. For this in' mod='sitemappro'} <strong>{l s='"Simple sitemap with all active languages"' mod='sitemappro'}</strong> {l s='сlick' mod='sitemappro'} <strong>{l s='"Generate".' mod='sitemappro'}</strong><br>
{get_image_lang_map path = '9.jpg'}
{l s='After generating sitemap will appear link and status is updated to' mod='sitemappro'} <strong>{l s='"Exists".' mod='sitemappro'}</strong>
{l s='You can generate site map for each language. For this select tab with desired language and click' mod='sitemappro'} <strong>{l s='"Generate".' mod='sitemappro'}</strong>
{get_image_lang_map path = '10.jpg'}
{l s='After generating sitemap will appear link and status is updated to' mod='sitemappro'} <strong>{l s='"Exists".' mod='sitemappro'}</strong>
{l s='You can generate sitemap with product images. For this follow the same steps above.' mod='sitemappro'}
{l s='You can create additional' mod='sitemappro'} <strong>{l s='sitemap with alternative links' mod='sitemappro'}</strong> {l s='and' mod='sitemappro'} <strong>{l s='sitemap with product images and alternate links.' mod='sitemappro'}</strong> {l s='Click' mod='sitemappro'} <strong>{l s='"Generate"' mod='sitemappro'}</strong> {l s='in desired section.' mod='sitemappro'}
{get_image_lang_map path = '11.jpg'}
{l s='After generating the desired site map, open link' mod='sitemappro'} <em>{l s='(link in the' mod='sitemappro'} <strong>{l s='status' mod='sitemappro'}</strong> {l s='line)' mod='sitemappro'}</em>
{get_image_lang_map path = '12.jpg'}
{l s='In page that opens in field "loc" will the link (or several links) to XML file of your settings sitemap.' mod='sitemappro'}
{get_image_lang_map path = '13.jpg'}
{l s='Enter in your browser this address. Page will open of XML file with your settings sitemap.' mod='sitemappro'}
{get_image_lang_map path = '14.jpg'}