{*
* Google-Friendly FAQ Pages and Lists With Schema Markup module
*
*    @author    Opossum Dev
*    @copyright Opossum Dev
*    @license   You are just allowed to modify this copy for your own use. You are not allowed
* to redistribute it. License is permitted for one Prestashop instance only, except for test
* instances.
*}

{block name='head' append}
{literal}
    <script type="application/ld+json">
            {
                "@context": "https://schema.org",
                "@type": "FAQPage",
                "mainEntity": [{/literal}
                    {foreach from=$markup_items item=item name=itemName}
    {literal}
                {
                    "@type": "Question",
                    "name": "{/literal}{$item.question nofilter}{literal}",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "{/literal}{$item.answer nofilter}{literal}"
                    }
                }{/literal}{if not $smarty.foreach.itemName.last},{/if}{/foreach}{literal}]
            }


        </script>
{/literal}
{/block}