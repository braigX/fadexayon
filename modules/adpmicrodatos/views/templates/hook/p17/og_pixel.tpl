{*
* 2007-2023 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    Ádalop <contact@prestashop.com>
*  @copyright 2023 Ádalop
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}


{literal}
<script>

!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?

n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;

n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;

t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,

document,'script','https://connect.facebook.net/en_US/fbevents.js');

fbq('init', '{/literal}{$adp_facebook_admin_id|escape:'javascript':'UTF-8'}{literal}'); // Insert your pixel ID here.

fbq('track', 'PageView');

{/literal}
    {if $page.page_name == 'product'}
        {literal}fbq('track', 'ViewContent', { content_name: '{/literal}{$product_name|escape:'javascript':'UTF-8'}{literal}', content_ids: [{/literal}{$content_ids|escape:'javascript':'UTF-8'}{literal}],content_type: '{/literal}{$content_type|escape:'javascript':'UTF-8'}{literal}',value: '{/literal}{$value|escape:'javascript':'UTF-8'}{literal}', currency: '{/literal}{$currency|escape:'javascript':'UTF-8'}{literal}'});{/literal}
    {/if}
{literal}

</script>
{/literal}

<noscript><img height="1" width="1" style="display:none"

src="https://www.facebook.com/tr?id={$adp_facebook_admin_id|escape:'javascript':'UTF-8'}&ev=PageView&noscript=1"

/></noscript>
