<!-- Module UrlSeoManager -->
{if isset($usm_robots) && $usm_robots}
<meta name="robots" content="{$usm_robots|escape:'html':'UTF-8'}" />
{/if}

{if isset($usm_canonical) && $usm_canonical}
<link rel="canonical" href="{$usm_canonical|escape:'html':'UTF-8'}" />
{/if}

{if isset($usm_hreflang) && $usm_hreflang}
    {foreach from=$usm_hreflang item=href key=lang_code}
<link rel="alternate" hreflang="{$lang_code|escape:'html':'UTF-8'}" href="{$href|escape:'html':'UTF-8'}" />
    {/foreach}
{/if}
<!-- End Module UrlSeoManager -->
