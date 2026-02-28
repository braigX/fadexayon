{*
* Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
*
*    @author    Jpresta
*    @copyright Jpresta
*    @license   See the license of this module in file LICENSE.txt, thank you.
*}
<ul>
{foreach $urls as $url}
<li style="line-height: 2rem;"><span class="cron_url">{$url|escape:'html':'UTF-8'}</span></li>
{/foreach}
</ul>
