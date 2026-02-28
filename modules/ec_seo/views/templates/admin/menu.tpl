{if $version16}
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons"  rel="stylesheet">
{/if}
<div id="menuec_seo" class="productTabs" >
    <div class="list-group">
        <div class="list-group-item {if $active =='dashboard'}active{/if}"><a id="menuDashboard" href="#"><i class="dashboard"></i>{l s='Dashboard' mod='ec_seo'}</a></div>
        <div class="list-group-item {if $active =='metagenerator'}active{/if}">
            <a id="menuMetaGenerator" href="#"><i class="metagenerator"></i>{l s='Meta tags' mod='ec_seo'} <span class="material-icons chevron">expand_more</span></a>
            <div class="submenu" id="menuec_seo_meta">
                {foreach $list_menu as $key => $menu}
                    <a class="sub-item" data-type="{$key|escape:'htmlall':'UTF-8'}" href="#">{$menu.trad|escape:'htmlall':'UTF-8'}</a>
                {/foreach}
            </div>
        </div>
        <div class="list-group-item {if $active =='opengraph'}active{/if}"><a id="menuOpenGraph" href="#"><i class="opengraph"></i>{l s='Open graph tags' mod='ec_seo'}</a></div>
        <div class="list-group-item {if $active =='balisealt'}active{/if}"><a id="menubalisealt" href="#"><i class="balisealt"></i>{l s='Image alt attributes' mod='ec_seo'}</a></div>
        <div class="list-group-item {if $active =='footerseo'}active{/if}">
            <a id="menuFooterSeo" href="#"><i class="footerseo"></i>{l s='Footer SEO' mod='ec_seo'} <span class="material-icons chevron">expand_more</span></a>
            <div class="submenu" id="menuec_seo_footer">
                {foreach $list_menu as $key => $menu}
                    {if $key != 'product'}
                        <a class="sub-item" data-type="{$key|escape:'htmlall':'UTF-8'}" href="#">{$menu.trad|escape:'htmlall':'UTF-8'}</a>
                    {/if}
                {/foreach}
            </div>
        </div>
        <div class="list-group-item {if $active =='blockhtml'}active{/if}"><a id="menuBlockHtml" href="#"><i class="blockhtml"></i>{l s='Block HTML' mod='ec_seo'}</a></div>
        <div class="list-group-item {if $active =='pagenoindex'}active{/if}"><a id="menuPageNoIndex" href="#"><i class="pagenoindex"></i>{l s='Page noindex' mod='ec_seo'}</a></div>
        <div class="list-group-item">
            <a id="menuRedirection" href="#"><i class="redirection"></i>{l s='Redirection' mod='ec_seo'} <span class="material-icons chevron">expand_more</span></a>
            <div class="submenu" id="menuec_redirection">
                <a class="sub-item {if $active =='redirectionsub' || $active =='redirection'}active{/if} " id="menuRedirectionSub" data-menu="RedirectionSub" href="#">{l s='Page' mod='ec_seo'}</a>
                <a class="sub-item {if $active =='redirectionimage'}active{/if}" id="menuRedirectionImage" data-menu="RedirectionImage" href="#">{l s='Image' mod='ec_seo'}</a>
            </div>
        </div>
{*         <div class="list-group-item {if $active =='redirectionimage'}active{/if}"><a id="menuRedirectionImage" href="#"><i class="redirectionimage"></i>{l s='Image Redirection' mod='ec_seo'}</a></div> *}
        <div class="list-group-item {if $active =='internalmesh'}active{/if}">
            <a id="menuinternalmesh" href="#"><i class="internalmesh"></i>{l s='Internal mesh' mod='ec_seo'} <span class="material-icons chevron">expand_more</span></a>
            <div class="submenu" id="menuec_seo_mi">
                {foreach $list_menu as $key => $menu}
                    <a class="sub-item" data-type="{$key|escape:'htmlall':'UTF-8'}" href="#">{$menu.trad|escape:'htmlall':'UTF-8'}</a>
                {/foreach}
            </div>
        </div>
        
        <div class="list-group-item {if $active =='robot'}active{/if}"><a id="menurobot" href="#"><i class="robot"></i>{l s='Robots.txt' mod='ec_seo'}</a></div>
        <div class="list-group-item {if $active =='config'}active{/if}">
            <a id="menuConfig" href="#"><i class="config"></i>{l s='Admin' mod='ec_seo'} <span class="material-icons chevron">expand_more</span></a>
            <div class="submenu" id="menuec_config">
                    <a class="sub-item {if $active =='config' || $active =='configsub'}active{/if}" id="menuConfigsub" data-menu="configsub" href="#">{l s='Configuration' mod='ec_seo'}</a>
                    <a class="sub-item {if $active =='report'}active{/if}" id="menuReport" data-menu="report" href="#">{l s='Reports' mod='ec_seo'}</a>
                    <a class="sub-item {if $active =='task'}active{/if}" id="menuTask" data-menu="task" href="#">{l s='Task' mod='ec_seo'}</a>
                    <a class="sub-item {if $active =='backup'}active{/if}" id="menuBackup" data-menu="backup" href="#">{l s='Backups' mod='ec_seo'}</a>
            </div>
        </div>
{*         <div class="list-group-item {if $active =='task'}active{/if}"><a id="menuTask" href="#"><i class="task"></i>{l s='Task' mod='ec_seo'}</a></div>
        <div class="list-group-item {if $active =='backup'}active{/if}"><a id="menuBackup" href="#"><i class="backup"></i>{l s='Backups' mod='ec_seo'}</a></div>
        <div class="list-group-item {if $active =='report'}active{/if}"><a id="menuReport" href="#"><i class="report"></i>{l s='Reports' mod='ec_seo'}</a></div>
        <div class="list-group-item {if $active =='config'}active{/if}"><a id="menuConfig" href="#"><i class="config"></i>{l s='Configuration' mod='ec_seo'}</a></div> *}
    </div>
</div>
{* <div id="menuec_seo_meta" class="ec-tab-content col-lg-9 col-md-9" style="display: none;">
    <div class="list-group">
        {foreach $list_menu as $key => $menu}
            <a class="list-group-item {if $key=='product'}active{/if}" data-type="{$key|escape:'htmlall':'UTF-8'}" href="#">{$menu.trad|escape:'htmlall':'UTF-8'}</a>
        {/foreach}
    </div>
</div> *}
{* <div id="menuec_seo_mi" class="ec-tab-content col-lg-9 col-md-9" style="display: none;">
    <div class="list-group">
        {foreach $list_menu as $key => $menu}
            <a class="list-group-item {if $key=='product'}active{/if}" data-type="{$key|escape:'htmlall':'UTF-8'}" href="#">{$menu.trad|escape:'htmlall':'UTF-8'}</a>
        {/foreach}
    </div>
</div> *}
{foreach $list_menu as $key => $menu}
    <div id="menuec_seo_meta_{$key|escape:'htmlall':'UTF-8'}" class="menuec_seo_meta">
        <div class="list-group">
            <a class="list-group-item active" data-type="{$key|escape:'htmlall':'UTF-8'}" data-id="gen" href="#">{l s='General' mod='ec_seo'}</a>
            {if $menu.spe}
                <a class="list-group-item" data-type="{$key|escape:'htmlall':'UTF-8'}" data-id="prod" href="#">{l s='Specific' mod='ec_seo'}</a>
            {/if}
        </div>
    </div>
    {if $key != 'product'}
        <div id="menuec_seo_footer_{$key|escape:'htmlall':'UTF-8'}" class="menuec_seo_footer">
            <div class="list-group">
                <a class="list-group-item active" data-type="{$key|escape:'htmlall':'UTF-8'}" data-id="gen" href="#">{l s='General' mod='ec_seo'}</a>
                <a class="list-group-item" data-type="{$key|escape:'htmlall':'UTF-8'}" data-id="spe" href="#">{l s='Specific' mod='ec_seo'}</a>
            </div>
        </div>
    {/if}
    <div id="ec_meta_{$key|escape:'htmlall':'UTF-8'}" class="ec_meta_generator ec-tab-content" style="display:none;">

    </div>
    <div id="ec_mi_{$key|escape:'htmlall':'UTF-8'}" class="ec_mi_generator ec-tab-content" style="display:none;">

    </div>
    {if $key != 'product'}
    <div id="ec_footerseo_{$key|escape:'htmlall':'UTF-8'}" class="ec_footerseo ec-tab-content" style="display:none;"></div>
    {/if}
{/foreach}
<div id="backup_form" style="display:none;">
    {$back_up nofilter}
</div>
<div id="report_form" style="display:none;">
    {$report nofilter}
</div>

