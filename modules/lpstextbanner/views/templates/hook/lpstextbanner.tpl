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
{if $lpsTextBannerConfig->display_banner && isset($messages)}
    {literal}
        <style>
            .lpstextbanner {
                background-color: {/literal}{$lpsTextBannerConfig->banner_background_color|escape:'htmlall':'UTF-8'}{literal};
                color: {/literal}{$lpsTextBannerConfig->banner_text_color|escape:'htmlall':'UTF-8'}{literal};
            }
            .lpsscrolling .lpsmessages {
                 animation: slide-left  {/literal}{$lpsTextBannerConfig->speedScroll|escape:'htmlall':'UTF-8'}{literal}s linear infinite;
            }
            .lpsscrolling .lpsmessages span a,
            .lpstypewriterspan a,
            .lpsslides a {
                color: {/literal}{$lpsTextBannerConfig->banner_text_color|escape:'htmlall':'UTF-8'}{literal}!important;
            }
        </style>
    {/literal}
    <div class="lpstextbanner{if $lpsTextBannerConfig->fixed_banner} lpsfixed{/if}{if $lpsTextBannerConfig->transition_effect == 'horizontal_slider' || $lpsTextBannerConfig->transition_effect == 'vertical_slider'} lpshideonmobile{/if}{if $lpsTextBannerConfig->transition_effect == 'typewriter'} lpstypewritermobile{/if}">
        <div class="container">
            {if $lpsTextBannerConfig->transition_effect == 'typewriter'}
                <div class="lpstypewriter">
                    <div class="lpstypewritermessages">
                        {foreach $messages as $message}
                            {if $message.display_link && $message.link}
                                <span><a href="{$message.link|escape:'htmlall':'UTF-8'}"{if $message.target} target="_blank"{/if}>{$message.message|escape:'htmlall':'UTF-8'}</a></span>
                            {else}
                                <span>{$message.message|escape:'htmlall':'UTF-8'}</span>
                            {/if}
                        {/foreach}
                    </div>
                    <span class="lpstypewriterspan"></span>
                </div>
            {else if $lpsTextBannerConfig->transition_effect == 'scrolling'}
                <div class="lpsscrolling">
                    {for $foo=1 to 10}
                        <div class="lpsmessages">
                            {foreach $messages as $message}
                                {if $message.display_link && $message.link}
                                    <span><a href="{$message.link|escape:'htmlall':'UTF-8'}"{if $message.target} target="_blank"{/if}>{$message.message|escape:'htmlall':'UTF-8'}</a></span>
                                {else}
                                    <span>{$message.message|escape:'htmlall':'UTF-8'}</span>
                                {/if}
                            {/foreach}
                        </div>
                    {/for}
                </div>
            {else}
                <div
                    class="lps{$lpsTextBannerConfig->transition_effect|escape:'htmlall':'UTF-8'}"
                    data-displayTime="{$lpsTextBannerConfig->displayTime|escape:'htmlall':'UTF-8'}"
                    data-directionH="{$lpsTextBannerConfig->directionH|escape:'htmlall':'UTF-8'}"
                    data-directionV="{$lpsTextBannerConfig->directionV|escape:'htmlall':'UTF-8'}"
                    {if $lpsTextBannerConfig->transition_effect == 'horizontal_slider' && $lpsTextBannerConfig->directionH == 'lefttoright'}dir="rtl"{/if}
                >
                    {if $messages|@count == 1}
                        {foreach $messages as $message}
                            <div class="lpsslides">
                                {if $message.display_link && $message.link}
                                    <a href="{$message.link}"{if $message.target} target="_blank"{/if}>{$message.message|escape:'htmlall':'UTF-8'}</a>
                                {else}
                                    {$message.message|escape:'htmlall':'UTF-8'}
                                {/if}
                            </div>
                        {/foreach}
                    {/if}
                    {foreach $messages as $message}
                        <div class="lpsslides">
                            {if $message.display_link && $message.link}
                                <a href="{$message.link|escape:'htmlall':'UTF-8'}"{if $message.target} target="_blank"{/if}>{$message.message|escape:'htmlall':'UTF-8'}</a>
                            {else}
                                {$message.message|escape:'htmlall':'UTF-8'}
                            {/if}
                        </div>
                    {/foreach}
                </div>
            {/if}
        </div>
    </div>
{/if}
