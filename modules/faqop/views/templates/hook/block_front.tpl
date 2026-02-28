{*
* Google-Friendly FAQ Pages and Lists With Schema Markup module
*
*    @author    Opossum Dev
*    @copyright Opossum Dev
*    @license   You are just allowed to modify this copy for your own use. You are not allowed
* to redistribute it. License is permitted for one Prestashop instance only, except for test
* instances.
*}

{if $block.show_markup AND $block.items|count > 0}
    {include file=$block.includeTpl markup_items=$block.markup_items}
{/if}

<{$block.block_tag} class="{$block.block_class}">

{if $block.show_title}
    <{$block.title_tag} class="op-faq-title {$block.title_class}">
        {$block.title}
    </{$block.title_tag}>
{/if}

{if $block.show_description}
    <{$block.description_tag} class="op-faq-description {$block.description_class}">
        {$block.description nofilter}
    </{$block.description_tag}>
{/if}

<{$block.content_tag} class="{$block.content_class}">

{foreach from=$block.items item=item}
    <{$block.item_tag} class="op-faq-item {$block.item_class} {$item.i_class} {$block.accordion_wrap}">
        <{$block.question_tag} class="op-faq-question {$block.question_class} {$item.q_class} {$block.accordion_toggle}">
            {$item.question nofilter}
        </{$block.question_tag}>
        <{$block.answer_tag} class="{$block.answer_class} {$item.a_class} {$block.accordion_panel}">
            {$item.answer nofilter}
        </{$block.question_tag}>
    </{$block.item_tag}>
{/foreach}

</{$block.content_tag}>
</{$block.block_tag}>