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
<script type="text/javascript">
	

	jQuery(document).ready(function(){

		if(jQuery('#toolbar-nav').length > 0 )
		{
			jQuery('#toolbar-nav').prepend("<li><a id='page-header-desc-configuration-clear_cache' class='toolbar_btn  pointer' href='index.php?controller=AdminModules&token={$tokenModule|escape:'htmlall':'UTF-8'}&configure=leveragebrowsercache&tab_module=administration&module_name=leveragebrowsercache' ><i class='process-icon-refresh'></i><div>{l s='Regenerate Cache' mod='leveragebrowsercache'}</div></a></li>");
		}

		if(jQuery('.toolbarBox .cc_button ').length > 0 )
		{
			jQuery('.toolbarBox .cc_button ').prepend("<li><a id='desc-configuration-modules-list' class='toolbar_btn' href='index.php?controller=AdminModules&token={$tokenModule|escape:'htmlall':'UTF-8'}&configure=leveragebrowsercache&tab_module=administration&module_name=leveragebrowsercache' ><span style='background-image:url(../modules/leveragebrowsercache/logo.gif); background-size:cover;' class='process-icon-refresh'></span><div>{l s='Regenerate Cache' mod='leveragebrowsercache'}</div></a></li>");
		}


		if(jQuery('.installstep .bootstrap.actions').length > 0 )
		{
			jQuery('.installstep .bootstrap.actions ').append("<a id='desc-configuration-modules-list' class='btn btn-default' href='index.php?controller=AdminModules&token={$tokenModule|escape:'htmlall':'UTF-8'}&configure=leveragebrowsercache&tab_module=administration&module_name=leveragebrowsercache' ><span class='process-icon-refresh'></span><div>{l s='Regenerate Cache' mod='leveragebrowsercache'}</div></a>");
		}




	});

</script>