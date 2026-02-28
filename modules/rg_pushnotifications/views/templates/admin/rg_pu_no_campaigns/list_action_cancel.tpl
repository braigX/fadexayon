{**
 * Web Browser Push Notifications using OneSignal
 *
 * @author    Rolige <www.rolige.com>
 * @copyright Since 2011 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 *}

<a href="{$href|escape:'html':'UTF-8'}"{if isset($confirm)} onclick="if (confirm('{$confirm}')){ldelim}return true;{rdelim} else{ldelim}event.stopPropagation();event.preventDefault();{rdelim};"{/if} title="{$action|escape:'html':'UTF-8'}" class="cancel">
  <i class="icon-close"></i> {$action|escape:'html':'UTF-8'}
</a>
