{*
* 2018 GEODIS
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
*  @author    GEODIS <contact@geodis.com>
*  @copyright 2018 GEODIS
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}

<div class="geodisOptionRow__inputContainer geodisInput js-input-container" data-values-var="code" data-error="{__ s='front.popin.floor.error'|escape:'html'}" data-required-text="{__ s='front.popin.requiredEntry'|escape:'html'}">
    <input
        class="geodisOptionRow__input geodisInput__input"
        type="text"
        data-set-value="getFloor"
        data-values-var="id"
        data-values=""
        data-validate="validateFloor"
        data-process="processFloor"
        placeholder="{__ s='front.popin.floor.placeholder'|escape:'html'}"
        class="geodisOptionRow__floor js-optionFloor"
    />
</div>
