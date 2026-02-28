{*
* Google-Friendly FAQ Pages and Lists With Schema Markup module
*
*    @author    Opossum Dev
*    @copyright Opossum Dev
*    @license   You are just allowed to modify this copy for your own use. You are not allowed
* to redistribute it. License is permitted for one Prestashop instance only, except for test
* instances.
*}

<div class="shortcode-block">
    <div class="shortcode-block-line">
        <input type="text" readonly="readonly"
               class="shortcode-readonly shortcode-text"
               value="{literal}{{/literal}{$shortcode}{literal}}{/literal}">
        </div>
    <div class="shortcode-block-explain-line">
            <span class="small-op italics-op">
                {l s='Insert this shortcode directly into content of:' mod='faqop'}
            </span>
        <ul>
            <li><span class="small-op italics-op">{l s='CMS pages' mod='faqop'}</span></li>
            <li><span class="small-op italics-op">{l s='Product description and summary' mod='faqop'}</span></li>
            <li><span class="small-op italics-op">{l s='Category description' mod='faqop'}</span></li>
        </ul>
        <p class="small-op italics-op">{l s='Display depends on "Hook settings ->
Customer groups" for both hook and shortcode ("All customer groups" by default)' mod='faqop'}</p>
        <p class="small-op italics-op">{l s='Display depends on "Hook settings ->
Currencies" for both hook and shortcode ("All currencies" by default)' mod='faqop'}</p>
    </div>
</div>