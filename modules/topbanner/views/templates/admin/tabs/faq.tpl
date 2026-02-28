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

{if !empty($apifaq)}
<div class="clearfix"></div>
<div class="tab-pane panel" id="faq">
    <div class="panel-heading"><i class="icon-question"></i> {l s='FAQ' mod='xxxxx'}</div>
    {foreach from=$apifaq item=categorie name='faq'}
        <span class="faq-h1">{$categorie->title|escape:'htmlall':'UTF-8'}</span>
        <ul>
            {foreach from=$categorie->blocks item=QandA}
                {if !empty($QandA->question)}
                    <li>
                        <span class="faq-h2"><i class="icon-info-circle"></i> {$QandA->question|escape:'htmlall':'UTF-8'}</span>
                        <p class="faq-text hide">
                            {$QandA->answer|escape:'htmlall':'UTF-8'|replace:"\n":"<br />"}
                        </p>
                    </li>
                {/if}
            {/foreach}
        </ul>
        {if !$smarty.foreach.faq.last}<hr/>{/if}
    {/foreach}
</div>

{/if}
