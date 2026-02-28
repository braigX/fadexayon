{**
* NOTICE OF LICENSE
*
* This file is licenced under the Software License Agreement.
* With the purchase or the installation of the software in your application
* you accept the licence agreement.
*
* @author    Presta.Site
* @copyright 2021 Presta.Site
* @license   LICENSE.txt
*}
<ol class="comment-list">
    {foreach from=$pswp_post.comments item='comment'}
        {if $pswp_psv >= 1.7}
            {include 'module:prestawp/views/templates/front/_comment.tpl' comment=$comment pswp_post=$pswp_post}
        {else}
            {include './_comment.tpl' comment=$comment pswp_post=$pswp_post}
        {/if}
    {/foreach}
</ol>