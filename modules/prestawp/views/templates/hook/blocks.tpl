{**
* NOTICE OF LICENSE
*
* This file is licenced under the Software License Agreement.
* With the purchase or the installation of the software in your application
* you accept the licence agreement.
*
* @author    Presta.Site
* @copyright 2018 Presta.Site
* @license   LICENSE.txt
*}

{if isset($pswp_blocks) && count($pswp_blocks)}
    {foreach from=$pswp_blocks item='pswp_block'}
        {include file=$pswp_block_tpl_file pswp_block=$pswp_block}
    {/foreach}
{/if}
