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

{extends file="helpers/list/list_header.tpl"}

{block name="override_header"}
    <div id="{$form_id|escape:'html':'UTF-8'}-wrp" class="panel">
    {if isset($preTable)}
        {$preTable nofilter} {* HTML *}
    {/if}
    <div style="display: none;">
        <div id="pst_modal_content" class="bootstrap {if $psv == 1.5}ps15{/if}"></div>
    </div>
    <div id="{$form_id|escape:'html':'UTF-8'}-list-wrp">
{/block}