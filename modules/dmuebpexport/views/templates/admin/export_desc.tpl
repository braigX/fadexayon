{**
* NOTICE OF LICENSE
*
* This source file is subject to a commercial license from SARL DREAM ME UP
* Use, copy, modification or distribution of this source file without written
* license agreement from the SARL DREAM ME UP is strictly forbidden.
*
*   .--.
*   |   |.--..-. .--, .--.--.   .--.--. .-.   .  . .,-.
*   |   ;|  (.-'(   | |  |  |   |  |  |(.-'   |  | |   )
*   '--' '   `--'`-'`-'  '  `-  '  '  `-`--'  `--`-|`-'
*        w w w . d r e a m - m e - u p . f r       '
*
*  @author    Dream me up <prestashop@dream-me-up.fr>
*  @copyright 2007 - 2024 Dream me up
*  @license   All Rights Reserved
*}

{if $nb_ok > 0}
    <div class="{$class_alert_ok|escape:'htmlall':'UTF-8'}">{$nb_ok|intval + $nb_corrected|intval} {$txt_ok|escape:'htmlall':'UTF-8'}</div>
{/if}

{if $nb_error > 0}
    <div class="{$class_alert_error|escape:'htmlall':'UTF-8'}">
        <div>{$nb_error} {$txt_error|escape:'htmlall':'UTF-8'}</div><br/>
        {$report_lines_error}
    </div>
{/if}

{if $nb_corrected > 0}
    <div class="{$class_alert_warn|escape:'htmlall':'UTF-8'}">
        <div>{$nb_corrected|escape:'htmlall':'UTF-8'} {$txt_corrected|escape:'htmlall':'UTF-8'}</div><br/>
        {$report_lines_correction}
    </div>
{/if}

{literal}
<script>$(document).ready(function(){$(".fancybox_inline").fancybox();});</script>
{/literal}