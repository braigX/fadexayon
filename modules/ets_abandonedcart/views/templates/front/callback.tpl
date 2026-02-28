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

<!doctype html>
<html lang="{$locale|escape:'html':'UTF-8'}">
    <head>
        <link rel="stylesheet" href="{$css nofilter}" type="text/css" media="all">
    </head>
    <body id="callback">
        {if isset($error) && $error}
            <p id="ets-abancart-error">
                <span class="icon_timex">
                    <svg xmlns="http://www.w3.org/2000/svg" height="20" viewBox="0 -960 960 960" width="20" fill="#5f6368"><path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224-224 224Z"/></svg>
                </span>
                {$error nofilter}
            </p>
        {else}
            <p id="ets-abancart-success">
                <span class="icon_success">
                    <svg xmlns="http://www.w3.org/2000/svg" height="20" viewBox="0 -960 960 960" width="20" fill="#5f6368"><path d="M382-240 154-468l57-57 171 171 367-367 57 57-424 424Z"></path></svg>
                </span>
                {l s='Authentication successful.' mod='ets_abandonedcart'}
            </p>
        {/if}
        <script type="text/javascript" src="{$js nofilter}"></script>
    </body>
</html>