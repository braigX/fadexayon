{if isset($parsed_hbuilder)}
    {$parsed_hbuilder nofilter}
{else}
    {if file_exists("$theme_dir/checkout/_partials/header.tpl")}
        {include file="$theme_dir/checkout/_partials/header.tpl"}
    {else} 
        {include file="$parent_theme_dir/checkout/_partials/header.tpl"}
    {/if}
{/if}

