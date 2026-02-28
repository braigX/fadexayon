{*
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
*}
<span class="wpcf7-form-control-wrap {$html_class|escape:'html':'UTF-8'}">
{if $atts.type=='range' && isset($atts.min)}
    <span class="rang-min">{$atts.min|escape:'html':'UTF-8'}</span>
{/if}
{if $atts.type=='range' && isset($atts.max)}
    <span class="rang-max">{$atts.max|escape:'html':'UTF-8'}</span>
{/if}
{if $atts.type=='range'}
    <span class="rang-value">&nbsp;</span>
{/if}
<input 
    {foreach from=$atts key='key' item='item'}
        {if $item}
            {$key|escape:'html':'UTF-8'}="{$item|escape:'html':'UTF-8'}"
        {/if}
    {/foreach}
/>
{$validation_error|escape:'html':'UTF-8'}</span>