{**
 * Web Browser Push Notifications using OneSignal
 *
 * @author    Rolige <www.rolige.com>
 * @copyright Since 2011 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 *}

{foreach from=$list_menu_options item=menu key=key}<li><a id="{$key|escape:'htmlall':'UTF-8'}" class="toolbar_btn" href="{$menu.href|escape:'htmlall':'UTF-8'}" title="{$menu.desc|escape:'htmlall':'UTF-8'}"><i class="{$menu.icon|escape:'htmlall':'UTF-8'}"></i><div>{$menu.desc|escape:'htmlall':'UTF-8'}</div></a></li>{/foreach}
