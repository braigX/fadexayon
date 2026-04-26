{if isset($parsed_hbuilder)}
    {$parsed_hbuilder nofilter}
{else}
    {if file_exists("$theme_dir/_partials/header.tpl")}
        {include file="$theme_dir/_partials/header.tpl"}
    {else} 
        {include file="$parent_theme_dir/_partials/header.tpl"}
    {/if}
{/if}