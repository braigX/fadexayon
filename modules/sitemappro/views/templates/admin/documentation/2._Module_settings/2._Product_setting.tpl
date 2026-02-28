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

<h2 class="text-center">{l s='Product setting' mod='sitemappro'}</h2>
<hr>
{l s='This column will show you selected categories. All products from selected categories, has on default' mod='sitemappro'} <strong>{l s='"Priority"' mod='sitemappro'}</strong> {l s='and' mod='sitemappro'} <strong>{l s='"Changefreq"' mod='sitemappro'}</strong> {l s='as by category. You can change the value. To do this in search box, enter the name of necessary product. The search for products is only among the selected categories you.' mod='sitemappro'}
{get_image_lang_map path = '4.jpg'}
{l s='Select a product. The selected products will appear at the bottom.' mod='sitemappro'}
{l s='Select' mod='sitemappro'} <strong>{l s='"Priority"' mod='sitemappro'}</strong>  {l s='(from 0 to 1). Higher value higher priority.' mod='sitemappro'}
{get_image_lang_map path = '5.jpg'}
{l s='Select' mod='sitemappro'} <strong>{l s='"Changefreq".' mod='sitemappro'}</strong>
{get_image_lang_map path = '6.jpg'}
{l s='In the end click' mod='sitemappro'} <strong>{l s='"Save".' mod='sitemappro'}</strong>
<div class="alert alert-warning">
    {l s='Important: Don’t forget to save your edits.' mod='sitemappro'}
</div>