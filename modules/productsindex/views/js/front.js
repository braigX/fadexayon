/*
* 2007 - 2018 ZLabSolutions
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
* Do not edit or add to this file if you wish to upgrade module to newer
* versions in the future. If you wish to customize module for your
* needs please contact developer at http://zlabsolutions.com for more information.
*
*  @author    Eugene Zubkov <magrabota@gmail.com>
*  @copyright 2018 ZLab Solutions
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Property of ZLab Solutions https://www.facebook.com/ZlabSolutions/
*
*/

var zlc_lang_pac = '';
function readAjaxFields(){
	var raw = $('.zlc-front-lang-pac').val();
	var json = decodeURIComponent(raw);
	zlc_lang_pac = JSON.parse(json);
}


$(document).ready(function(){
	//readAjaxFields();
});

tools = {
	getParameterByName: function(name, url) {
		if (!url) {
		  url = window.location.href;
		}
		name = name.replace(/[\[\]]/g, "\\$&");
		var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
			results = regex.exec(url);
		if (!results) return null;
		if (!results[2]) return '';
		return decodeURIComponent(results[2].replace(/\+/g, " "));
	},
}