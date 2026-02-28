<div id="htmlcontent" class="panel roy_content">
    <div class="rc_title">{$htmlcontent.info.name|escape:'htmlall':'UTF-8'} (v.{$htmlcontent.info.version|escape:'htmlall':'UTF-8'})</div>

    {if isset($error) && $error}
        {include file="{$htmlcontent.admin_tpl_path|escape:'htmlall':'UTF-8'}messages.tpl" id="main" text=$error class='error'}
    {/if}
    {if isset($confirmation) && $confirmation}
        {include file="{$htmlcontent.admin_tpl_path|escape:'htmlall':'UTF-8'}messages.tpl" id="main" text=$confirmation class='conf'}
    {/if}
    <!-- New -->
    {include file="{$htmlcontent.admin_tpl_path|escape:'htmlall':'UTF-8'}new.tpl"}

    <div class="rc_help">
      <p>{l s='If you want to use Banner with hover effect - use Title and HTML for banner title and description.' d='Modules.RoyBanners.Shop'}</p>
      <p>{l s='If you want to use HTML block - just not upload an image' d='Modules.RoyBanners.Shop'}</p>
      <p>{l s='If you want to use Responsive Video banner - upload only video inside HTML block, and check Video banner checkbox' d='Modules.RoyBanners.Shop'}</p>
    </div>

    <!-- Slides -->
    {include file="{$htmlcontent.admin_tpl_path|escape:'htmlall':'UTF-8'}items.tpl"}
</div>
