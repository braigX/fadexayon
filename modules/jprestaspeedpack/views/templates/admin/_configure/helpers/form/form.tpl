{*
* Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
*
*    @author    Jpresta
*    @copyright Jpresta
*    @license   See the license of this module in file LICENSE.txt, thank you.
*}

{extends file="helpers/form/form.tpl"}
{block name="input_row"}
    {if $input.type == 'alert_info'}
        <div class="alert alert-info">{$input.text|escape:'quotes':'UTF-8'}</div>
    {elseif $input.type == 'alert_warn'}
        <div class="alert alert-warning">{$input.text|escape:'quotes':'UTF-8'}</div>
    {elseif $input.type == 'alert_error'}
        <div class="alert alert-danger">{$input.text|escape:'quotes':'UTF-8'}</div>
    {elseif $input.type == 'logs'}
        <a href="#" onclick="$('#{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}').toggle()"><i class="material-icons">bug_report</i><span style="vertical-align: super;">{l s='See logs' mod='jprestaspeedpack'}</span></a>
        <div class="panel" id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}" style="display: none">
            <div class="pre">
                {foreach $input.logs as $log}
                    <div class="log_{$log->type|escape:'html':'UTF-8'}"><b>{$log->date|escape:'html':'UTF-8'} &gt;</b> {$log->msg|escape:'html':'UTF-8'}</div>
                {/foreach}
            </div>
        </div>
    {elseif $input.type == 'converters_report'}
        <div class="alert {$input.typeAlert|escape:'html':'UTF-8'}">{$input.text|escape:'quotes':'UTF-8'}
            <a class="btntoggle" data-toggle="collapse" href="#collapseConverters" role="button" aria-expanded="false" aria-controls="collapseConverters">
                {l s='Details' mod='jprestaspeedpack'}
            </a>
            <div class="collapse" id="collapseConverters">
                <div class="alert alert-info" style="margin-top: 0.5rem;">
                    <b>{l s='The following errors or warnings can be ignored.' mod='jprestaspeedpack'}</b>
                    {l s='They simply show how the compressor was selected in your store.' mod='jprestaspeedpack'}
                </div>
                {foreach $input.values as $key => $value}
                    {if is_array($value) && $key !== 'firstActiveConverter'}
                        <div class="reportHead alert alert-{if !$value.disabled}success{else}warning{/if}">{$value.label|escape:'quotes':'UTF-8'}{if !$value.disabled} ({$value.duration_ms|intval}ms){/if}
                            <a class="btntoggle" data-toggle="collapse" href="#collapse{$value.id|escape:'html':'UTF-8'}" role="button" aria-expanded="false" aria-controls="collapse{$value.id|escape:'html':'UTF-8'}">
                                {l s='Details' mod='jprestaspeedpack'}
                            </a>
                        </div>
                        <div class="collapse report" id="collapse{$value.id|escape:'html':'UTF-8'}">
                            {if isset($value.error) && $value.error}<div class="alert alert-warning">{$value.error|escape:'html':'UTF-8'}</div>{/if}
                            {$value.log|escape:'quotes':'UTF-8'}
                        </div>
                    {/if}
                {/foreach}
            </div>
        </div>
    {elseif $input.type == 'check_header_vary'}
        <script type="application/javascript">
            function jprestaIsCrossDomain(urlToFetch) {
                let currentUrl = new URL(window.location.href);
                let otherUrl = new URL(urlToFetch);
                return currentUrl.host !== otherUrl.host;
            }
            function jprestaContainsAccept(val) {
                if (!val) {
                    return false;
                }
                let vals = val.split(',');
                for (let i = 0; i < vals.length; i++) {
                    if(vals[i].trim().toLowerCase() === 'accept') {
                        return true;
                    }
                }
                return false;
            }
            try {
                $.ajax({
                    url: '{$input.url_to_test|escape:'javascript':'UTF-8'}', cache: false, headers: { 'Accept': 'image/webp,image/avif,*/*' }, complete: function (jqXHR) {
                        console.log(jqXHR);
                        if (jqXHR.status >= 200 && jqXHR.status < 300) {
                            // Get the raw header string
                            let allHeaders = jqXHR.getAllResponseHeaders();

                            // Convert the header string into an array
                            // of individual headers
                            let allHeadersArray = allHeaders.trim().split(/[\r\n]+/);

                            // Create a map of header names to values
                            let allHeadersMap = [];
                            allHeadersArray.forEach(function (line) {
                                let parts = line.split(': ');
                                let headerName = parts.shift().toLowerCase();
                                let headerValue = parts.join(': ').toLowerCase();
                                allHeadersMap[headerName] = headerValue;
                            });

                            if (!('vary' in allHeadersMap) && jprestaIsCrossDomain('{$input.url_to_test|escape:'javascript':'UTF-8'}')) {
                                $('#vary_check_dontknow').show();
                            }
                            else if (!('vary' in allHeadersMap) || !jprestaContainsAccept(allHeadersMap['vary'])) {
                                $('#vary_check_error').show();
                            }
                            else {
                                $('#vary_check_ok').show();
                            }
                        }
                        else {
                            console.log("Speed pack cannot analyze HTTP headers: status=" + jqXHR.status + " " + jqXHR.statusText);
                        }
                    }
                });
            }
            catch (e) {
                console.error("Speed pack cannot analyze HTTP headers: " + e.message, e);
            }
        </script>
        <div id="vary_check_error" class="alert alert-warning" style="display: none">
            {$input.text_error|escape:'quotes':'UTF-8'}
            <pre>
location ~ (.+)\.(png|jpe?g)$ {
    add_header Vary Accept;
}
</pre>
        </div>
        <div id="vary_check_ok" class="alert alert-success" style="display: none">
            {$input.text_ok|escape:'quotes':'UTF-8'}
        </div>
        <div id="vary_check_dontknow" class="alert alert-default" style="display: none">
            {$input.text_dontknow|escape:'quotes':'UTF-8'}
        </div>
    {else}
        {$smarty.block.parent}
    {/if}
{/block}
{block name="input"}
    {if $input.type == 'integer'}
        {if isset($input.prefix) || isset($input.suffix)}
            <div class="input-group{if isset($input.class)} {$input.class|escape:'html':'UTF-8'}{/if}">
        {/if}
        {if isset($input.prefix)}
            <span class="input-group-addon">{$input.prefix|escape:'html':'UTF-8'}</span>
        {/if}
        <input type="number"
               id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}"
               name="{$input.name|escape:'html':'UTF-8'}"
               class="form-control text-right{if isset($input.class)} {$input.class|escape:'html':'UTF-8'}{/if}"
               value="{$fields_value[$input.name]|intval}"
                {if isset($input.size)} size="{$input.size|intval}"{/if}
                {if isset($input.max)} max="{$input.max|intval}"{/if}
                {if isset($input.min)} min="{$input.min|intval}"{/if}
                {if isset($input.readonly) && $input.readonly} readonly="readonly"{/if}
                {if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}
                {if isset($input.required) && $input.required} required="required" {/if}
                {if isset($input.placeholder) && $input.placeholder} placeholder="{$input.placeholder|escape:'html':'UTF-8'}"{/if} />
        {if isset($input.suffix)}
            <span class="input-group-addon">{$input.suffix|escape:'html':'UTF-8'}</span>
        {/if}
        {if isset($input.maxchar) || isset($input.prefix) || isset($input.suffix)}
            </div>
        {/if}
    {/if}
    {if $input.type == 'webp_slider_quality'}
        {assign var='value_text' value=$fields_value[$input.name]}
        <style type="text/css">
            .ui-slider .ui-slider-handle {
                height: 1.5rem;
                width: 2.5rem;
                top: -0.5rem;
                text-align: center;
                line-height: 1.4rem;
                margin-left: -.1rem;
            }
        </style>
        <script type="application/javascript">
            $(function() {
                let inputField = $("#{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}");
                let cursor = $("#cursor{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}");
                $( "#slider{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}" ).slider({
                    {if isset($input.min)}min: {$input.min|intval},{/if}
                    {if isset($input.max)}max: {$input.max|intval},{/if}
                    {if isset($input.step)}step: {$input.step|intval},{/if}
                    animate: "fast",
                    {if isset($input.disabled) && $input.disabled}disabled: true,{/if}
                    value: {$value_text|escape:'html':'UTF-8'},
                    slide: function(event, ui) {
                        inputField.val(ui.value);
                        cursor.text(ui.value {if isset($input.unit)} + "{$input.unit|escape:'html':'UTF-8'}"{/if});
                    },
                    change: function (event, ui) {
                        $('#afterSlider{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}').attr('src', '{$input.after.url|escape:'html':'UTF-8'}&quality=' + ui.value);
                    }
                });

                // Initialize the slider
                $("#beforeAfterSlider{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}").twentytwenty({ before_label: 'JPG', after_label: '{$input.after.label|escape:'html':'UTF-8'|default:'WEBP'}' });

            } );
        </script>
        <div style="width: {$input.before.width|intval}px;{if isset($input.disabled) && $input.disabled}display:none{/if}" id="beforeAfterSlider{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}" class='twentytwenty-container'>
            <img id="beforeSlider{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}" {if isset($input.before.width)}width="{$input.before.width|intval}px"{/if} {if isset($input.before.height)}height="{$input.before.height|intval}px"{/if} src="{$input.before.url|escape:'html':'UTF-8'}">
            <img id="afterSlider{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}" {if isset($input.after.width)}width="{$input.before.width|intval}px"{/if} {if isset($input.after.height)}height="{$input.before.height|intval}px"{/if} src="{$input.after.url|escape:'html':'UTF-8'}&quality={$input.after.quality|intval}">
        </div>
        <input type="hidden"
               name="{$input.name|escape:'html':'UTF-8'}"
               id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}"
               value="{$value_text|escape:'html':'UTF-8'}"
        />
        <div style="width: {$input.before.width|intval}px; margin: 1rem 0;" id="slider{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}">
            <div id="cursor{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}" class="ui-slider-handle">{$value_text|escape:'html':'UTF-8'}{if isset($input.unit)}&nbsp;{$input.unit|escape:'html':'UTF-8'}{/if}</div>
        </div>
    {else}
        {$smarty.block.parent}
    {/if}
{/block}
