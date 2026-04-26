{if isset($parsed_fbuilder)}
    {$parsed_fbuilder nofilter}
{else}
    {if file_exists("$theme_dir/checkout/_partials/footer.tpl")}
        {include file="$theme_dir/checkout/_partials/footer.tpl"}
    {else} 
        {include file="$parent_theme_dir/checkout/_partials/footer.tpl"}
    {/if}
{/if}