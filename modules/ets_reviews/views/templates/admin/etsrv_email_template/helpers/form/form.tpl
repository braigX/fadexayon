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

{extends file="helpers/form/form.tpl"}
{block name="legend"}
    {$smarty.block.parent}
    <div class="ets_rv_template_type">
        <label for="template_type">{l s='Template type' mod='ets_reviews'}</label>
        <select name="template_type" class=" fixed-width-xl" id="template_type">
            <option value="html" selected="selected">{l s='HTML' mod='ets_reviews'}</option>
            <option value="txt">{l s='TXT' mod='ets_reviews'}</option>
        </select>
    </div>
{/block}
{block name="input"}
    {$smarty.block.parent}
    {if ($input.name == 'content_html' || $input.name == 'content_txt')&& isset($variables) || $input.name == 'subject' && isset($short_codes)}
        {if $input.name == 'subject'}
            {assign var='short_codes' value=$short_codes}
        {else}
            {assign var='short_codes' value=$variables}
        {/if}
        {if $short_codes}
            {assign var="ik" value=0}
            <p class="help-block">
                {l s='Variables' mod='ets_reviews'}:&nbsp;
                {foreach from=$short_codes item='shortcode'}
                    {assign var="ik" value=$ik+1}
                    <span class="ets-rv-short-code" rel="{$input.name|escape:'html':'UTF-8'}">{$shortcode nofilter}</span>{if $ik < $variables|count}&nbsp;{/if}
                {/foreach}
            </p>
        {/if}
    {/if}
{/block}
{block name="input_row"}
    {$smarty.block.parent}
{/block}
{block name="after"}
    <div class="ets_rv_preview_template">
        <h3 class="panel-heading">{l s='Email preview' mod='ets_reviews'}</h3>
        <div class="template_type template_html"></div>
        <div class="template_type template_txt"></div>
    </div>
    <div class="clearfix"></div>
{/block}
{block name="autoload_tinyMCE"}
{*    <script>*}
    tinySetup({
        editor_selector: 'autoload_rte',
        verify_html: false,
        force_br_newlines: true,
        force_p_newlines: false,
        forced_root_block: '',
        setup: function (ed) {
            ed.on('keyup change blur', function (ed) {
                tinyMCE.triggerSave();
                if (typeof ets_rv_op !== typeof undefined)
                    ets_rv_op.previewIframe();
            });
        },
        resize: false,
        min_height: 350,
    });
{*    </script>*}
{/block}
