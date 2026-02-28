{*
* 2007-2025 PrestaShop
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2025 PrestaShop SA

*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

	    		<td></td>
	    		<td><a href="{$uit_files.url|escape:'htmlall':'UTF-8'}" target="_blank"><img style="max-height: 30px; max-width: 50px; width:auto !important;" src="{$uit_files.url|escape:'htmlall':'UTF-8'}"></a></td>
	    		<td><span title="{$uit_files.path|escape:'htmlall':'UTF-8'}">{$uit_files.path|escape:'htmlall':'UTF-8'|truncate:20}</span></td>
	    		<td>{if $uit_files.before == 0}{l s='Under 1' mod='ultimateimagetool'}{else}{$uit_files.before|escape:'htmlall':'UTF-8'}{/if} {l s='Kb' mod='ultimateimagetool'}</td>
	    		<td>{if $uit_files.after == 0}{l s='Under 1' mod='ultimateimagetool'}{else}{$uit_files.after|escape:'htmlall':'UTF-8'}{/if} {l s='Kb' mod='ultimateimagetool'}</td>
	    		<td><i class="icon-check"></i></td>
