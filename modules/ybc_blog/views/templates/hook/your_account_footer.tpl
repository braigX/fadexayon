{*
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
*}
{if isset($is_17) && $is_17}
    <footer class="page-footer">
      <a href="{if isset($my_account_link) && $my_account_link}{$my_account_link|escape:'html':'UTF-8'}{else}#{/if}" class="account-link">
        <i class="material-icons">chevron_left</i>
        <span>{l s='Back to your account' mod='ybc_blog'}</span>
      </a>
      <a href="{if isset($home_link) && $home_link}{$home_link|escape:'html':'UTF-8'}{else}#{/if}" class="account-link">
        <i class="material-icons">home</i>
        <span>{l s='Home' mod='ybc_blog'}</span>
      </a>
    </footer>
{else}
    <ul class="footer_links clearfix">
    	<li>
    		<a class="btn btn-default button button-small" href="{if isset($my_account_link) && $my_account_link}{$my_account_link|escape:'html':'UTF-8'}{else}#{/if}">
    			<span><i class="ets_svg chevron-left">
                        <svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1427 301l-531 531 531 531q19 19 19 45t-19 45l-166 166q-19 19-45 19t-45-19l-742-742q-19-19-19-45t19-45l742-742q19-19 45-19t45 19l166 166q19 19 19 45t-19 45z"/></svg>
                    </i> {l s='Back to your account' mod='ybc_blog'}</span>
    		</a>
    	</li>
    	<li>
    		<a class="btn btn-default button button-small" href="{if isset($home_link) && $home_link}{$home_link|escape:'html':'UTF-8'}{else}#{/if}">
    			<span><i class="ets_svg chevron-left">
                        <svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1427 301l-531 531 531 531q19 19 19 45t-19 45l-166 166q-19 19-45 19t-45-19l-742-742q-19-19-19-45t19-45l742-742q19-19 45-19t45 19l166 166q19 19 19 45t-19 45z"/></svg>
                    </i> {l s='Home' mod='ybc_blog'}</span>
    		</a>
    	</li>
    </ul>
{/if}
