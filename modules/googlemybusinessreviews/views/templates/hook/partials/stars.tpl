{*
* 2020 AN Eshop Group
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0).
* It is available through the world-wide-web at this URL:
* https://opensource.org/licenses/osl-3.0.php
* If you are unable to obtain it through the world-wide-web, please send an email
* to contact@payplug.com so we can send you a copy immediately.
*
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PayPlug module to newer
 * versions in the future.
*
*  @author  AN Eshop Group
*  @copyright  2020 AN Eshop Group
*  @license   Private
*  AN Eshop Group
*}
<span class="Review__Rating_Stars">
    <ol class="section-star-array">

        {for $foo=1 to $rating}
            {if $foo > $rating}
                <li class="section-star section-star-half"></li>
             {else}
                <li class="section-star"></li>
            {/if}
        {/for}
        {assign var="rating_round" value=$rating|ceil}
        {for $foo= $rating_round+1 to 5}
            <li class="section-star section-star-empty"></li>
        {/for}
    </ol>
</span>
