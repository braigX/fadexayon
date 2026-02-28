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

<a{if isset($btn.class) && $btn.class} class="{$btn.class|escape:'html':'UTF-8'}"{/if} target="{$btn.target|escape:'html':'UTF-8'}" href="{$btn.href|escape:'quotes':'UTF-8'}"{if isset($btn.title) && $btn.title} title="{$btn.title|escape:'html':'UTF-8'}"{/if}>
    {if isset($avatar) && $avatar|trim !==''}
        <img src="{$avatar nofilter}" title="{$btn.title|escape:'html':'UTF-8'}" width="60" />
    {/if}
    <span class="author_info">
        {$btn.title|escape:'html':'UTF-8'}
        {if isset($profile_name) && $profile_name}
            <span class="ets_rv_admin_profile help-block">{$profile_name|escape:'html':'UTF-8'}</span>
        {/if}
    </span>
</a>