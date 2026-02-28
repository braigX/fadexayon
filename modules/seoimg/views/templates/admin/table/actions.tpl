{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 *}

<div class="btn-group-action">
	<div class="btn-group pull-right">
		<a role-id="{$prod.id_rule|intval}" data-role="{$role|escape:'htmlall':'UTF-8'}" data-type="{$type[$count_rule-1]|escape:'htmlall':'UTF-8'}" class="pointer generate btn btn-default" {if $prod.active|intval == 0}disabled="disabled"{/if}>
			<i class="icon-magic"></i> {l s='Apply rule' mod='seoimg'}
		</a>
		<button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
			<i class="icon-caret-down"></i>&nbsp;
		</button>
		<ul class="dropdown-menu" role="menu">
			<li class="pointer">
				<a role-id="{$prod.id_rule|intval}" data-role="{$role|escape:'htmlall':'UTF-8'}" data-type="{$type[$count_rule-1]|escape:'htmlall':'UTF-8'}" class="pointer edit">
					<i class="icon-pencil"></i> {l s='Edit' mod='seoimg'}
				</a>
			</li>
			<li class="pointer">
				<a role-id="{$prod.id_rule|intval}" data-role="{$role|escape:'htmlall':'UTF-8'}" data-type="{$type[$count_rule-1]|escape:'htmlall':'UTF-8'}" class="pointer delete">
					<i class="icon-trash"></i> {l s='Delete' mod='seoimg'}
				</a>
			</li>
		</ul>
	</div>
</div>
