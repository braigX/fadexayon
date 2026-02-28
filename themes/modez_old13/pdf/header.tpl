{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 *}


<table style="width: 100%; margin-bottom: 30px;">
<tr style="margin-bottom: 30px;">
	<td style="width: 35%">
		{if $logo_path}
			<img src="{$logo_path}" style="width:100px; height:40px; object-fit: contain;" />
		{/if}
	</td>
	<td style="width: 45%; text-align: left;">
		<table style="width: 100%">
			<tr>
				<td style="font-weight: 400; font-size: 9pt; color: #444; width: 100%;">BFP CINDAR S.A.S</td>
			</tr>
			<tr>
				<td style="font-weight: 400; font-size: 9pt; color: #444; width: 100%;">48, avenue du Général de Gaulle<br>94500 Champigny-sur-Marne - France</td>
			</tr>
			<tr>
				<td style="font-weight: 400; font-size: 9pt; color: #444; width: 100%;">R.C.S. : 330 731 043</td>
			</tr>
			<tr>
				<td style="font-weight: 400; font-size: 9pt; color: #444; width: 100%;">TVA Intracommunautaire : FR 26 330 731 043</td>
			</tr>
			<tr>
				<td style="font-weight: 400; font-size: 9pt; color: #444; width: 100%;">Tél. : 01 48 83 87 40</td>
			</tr>
			<tr>
				<td style="font-weight: 400; font-size: 9pt; color: #444 !important; width: 100%;"><a href="https://www.plexi-cindar.com">www.plexi-cindar.com</a></td>
			</tr>
		</table>
	</td>
	<td style="width: 20%; text-align: right;">
		<table style="width: 100%">
			<tr>
				<td style="font-weight: bold; font-size: 14pt; color: #444; width: 100%;">{if isset($header)}{$header|escape:'html':'UTF-8'|upper}{/if}</td>
			</tr>
			<tr>
				<td style="font-size: 14pt; color: #9E9F9E">{$date|escape:'html':'UTF-8'}</td>
			</tr>
			<tr>
				<td style="font-size: 14pt; color: #9E9F9E">{$title|escape:'html':'UTF-8'}</td>
			</tr>
		</table>
	</td>
</tr>
 <tr>
	<td style="width: 30%">
	</td>
 </tr>
</table>

