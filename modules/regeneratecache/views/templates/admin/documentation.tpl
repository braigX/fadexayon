{*
* 2007-2017 PrestaShop
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
*  @copyright  2007-2017 PrestaShop SA
*  @version  Release: $Revision: 14011 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<div class="tab-pane panel " id="documentation"  >
    <div class="panel-heading"><i class="icon-book"></i> {l s='Documentation' mod='regeneratecache'}</div>
		<div class="alert alert-info"><span class="alert_close"></span>
			{l s='To offer a better experience to your customers build your entire cache right after you clean it' mod='regeneratecache'}
			<br/> 
			{l s='Build each type, one at a time to prevent because it uses multiple parallel request and your hosting can block your IP. ' mod='regeneratecache'}
			<br/>
			{l s='The product cache is also generated when your product is updated.' mod='regeneratecache'}
			<br/><br/>

			{l s='Requirements - contact your hosting to make sure the following are set' mod='regeneratecache'}
			<br/>
			<ul>
				<li>{l s='cURL enabled' mod='regeneratecache'}</li>
				<li>{l s='safe_mode=off in the php.ini file' mod='regeneratecache'}</li>
			</ul>

			<br/><br/>
			{l s='You can you the module with any cache system that requires the page to be accesed before it`s built, below are a few examples' mod='regeneratecache'}
			<ul>
				<li>{l s='Prestashop Native Cache -> From the Preferences -> Perfomance Tab' mod='regeneratecache'}</li>
				<li>{l s='Page Cache module' mod='regeneratecache'}</li>
				<li>{l s='Cache Manager module' mod='regeneratecache'}</li>
				<li>{l s='All cache module that require a page load to build the cache' mod='regeneratecache'}</li>
			</ul>
		</div>
    <div class="clear"></div>
</div>
<div class="clear"></div>