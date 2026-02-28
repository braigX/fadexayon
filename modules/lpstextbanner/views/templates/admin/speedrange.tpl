{**
 * Loulou66
 * LpsTextBanner module for Prestashop
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php*
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    Loulou66.fr <contact@loulou66.fr>
 *  @copyright loulou66.fr
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *}
<div class="lpstextbannerrange">
    <div class="col-xs-12">
        <div class="col-xs-8">
            <input type="range" class="custom-range {$rangeid}_range" name="{$rangeid|escape:'htmlall':'UTF-8'}" min="{$rangeMin|escape:'htmlall':'UTF-8'}" max="{$rangeMax|escape:'htmlall':'UTF-8'}" step="{$rangeStep|escape:'htmlall':'UTF-8'}" value="{$rangeValue|escape:'htmlall':'UTF-8'}">
        </div>
        <div class="col-xs-2">
            <span class="font-weight-bold text-primary rangevalue">{$rangeValue|escape:'htmlall':'UTF-8'}</span>
            <span class="textbannerrangetext">{$sufix|escape:'htmlall':'UTF-8'}</span>
        </div>
    </div>
</div>
