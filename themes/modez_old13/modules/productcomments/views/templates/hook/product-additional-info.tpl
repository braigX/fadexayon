    {if $nb_comments != 0 || $post_allowed}
    <div class="product-comments-additional-info">

        {include file='module:productcomments/views/templates/hook/average-grade-stars.tpl' grade=$average_grade}

        {if isset($nb_comments) && $nb_comments > 0}
        <a class="nb-comments noeffect goreviews" href="#tabsection"><span >{$nb_comments}</span> {if isset($nb_comments) && $nb_comments == 1}{l s='Review'}{else}{l s='Reviews'}{/if}</a>
        {/if}

        {* Rich snippet rating*}
        <div   >
            <meta  content="{$nb_comments}" />
            <meta  content="{$average_grade}" />
        </div>

    </div>
    {/if}