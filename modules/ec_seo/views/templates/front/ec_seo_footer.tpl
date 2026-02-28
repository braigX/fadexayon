<div id="ec_seo_footer">
    {if strlen($infoFooter['title']) > 0}
        <h3>{$infoFooter['title']|escape:'htmlall':'UTF-8'}</h3>
    {/if}
    {if strlen($infoFooter['description']) > 0}
        <p>{$infoFooter['description']|escape:'htmlall':'UTF-8'}</p>
    {/if}
    <div id="ec_seo_block_footer" class="col-lg-12">
        {foreach $blocks as $block}
            <div class="col-lg-{$col|escape:'htmlall':'UTF-8'}">
                <h4 class="ec_title_block">{$block['title']|escape:'htmlall':'UTF-8'}</h4>
                <ul>
                    {foreach $block.links as $block_link}
                        <li><a href="{$block_link['link']|escape:'htmlall':'UTF-8'}">{$block_link['title']|escape:'htmlall':'UTF-8'}</a></li>
                    {/foreach}
                </ul>
            </div>
        {/foreach}
    </div>
</div>