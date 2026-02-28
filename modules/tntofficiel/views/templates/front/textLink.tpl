{*
 * TNT OFFICIAL MODULE FOR PRESTASHOP.
 *
 * @author    Inetum <inetum.com>
 * @copyright 2016-2024 Inetum, 2016-2024 TNT
 * @license   https://opensource.org/licenses/MIT MIT License
 *}
{if $strName}{$strName|escape:'html':'UTF-8'} : {/if}{if array_key_exists('href', $arrAttr) && $arrAttr.href}<a
    {foreach from=$arrAttr key=strAttrName item=strAttrValue}
        {$strAttrName|escape:'html':'UTF-8'}="{$strAttrValue|escape:'htmlall':'UTF-8'}"
    {/foreach}
>{/if}{$strMessage|escape:'html':'UTF-8'}{if array_key_exists('href', $arrAttr) && $arrAttr.href}</a>{/if}
