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

<ul>
    {foreach from=$report_lines item=line}
    <li>
        <a href="#fancy{$line.id_report|escape:'htmlall':'UTF-8'}" class="fancybox_inline">
        Commande n°{$line.id_order|escape:'htmlall':'UTF-8'}, 
        {if $line.is_avoir == 1}
            Avoir n°{$line.num_piece|escape:'htmlall':'UTF-8'},
        {else}
            Facture n°{$line.num_piece|escape:'htmlall':'UTF-8'},
        {/if}
        Différence : {$line.difference|escape:'htmlall':'UTF-8'}
        </a>
        <div id="fancy{$line.id_report|escape:'htmlall':'UTF-8'}" class="inline_fancybox" style="display:none">
            <pre>{$line.ecriture|escape:'htmlall':'UTF-8'}</pre>
        </div>
   </li>
    {/foreach}
</ul>