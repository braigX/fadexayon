{if $importcsv}{$postprocess nofilter}{/if}
<input id="id_version" type="hidden" rel="6"/>
<input id="id_shop" type="hidden" rel="{$shop|escape:'htmlall':'UTF-8'}"/>
<div id="redirection_form" class="bootstrap" id="ec_content" style="display: none;">
{if $disableoverride == 1}{$displayerror|escape:'htmlall':'UTF-8'}{/if}
    <div {if $submitok || $submitaddurl}{else}style="display:none"{/if}
    class="module_confirmation conf confirm alert alert-success">
        <button class="close" data-dismiss="alert" type="button">x</button>
        {l s='Settings updated' mod='ec_seo'}
    </div>
    <ul id="workTabs" class="nav nav-tabs">
   {*  <li class="{if $onglet == 5}active{/if}">
        <a href="#tab-5" data-toggle="tab">
            <span class="icon-flask"></span>
             {l s='Ec Seo' mod='ec_seo'}
        </a>
    </li> *}
    {foreach from=$tabsmarty item=row}
        <li class="{if $onglet == $row.0 || $row.0 == 0}active{/if}">
                        <a href="#tab-{$row.0|escape:'htmlall':'UTF-8'}" data-toggle="tab">
                            <span class="icon-cogs"></span>
                             {$row.1|escape:'htmlall':'UTF-8'}
                        </a>
                    </li>
    {/foreach}
    <li class="{if $onglet == 6}active{/if}">
                        <a href="#tab-6" data-toggle="tab">
                            <span class="icon-cogs"></span>
                             {l s='Other' mod='ec_seo'}
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
              {*   <div id="tab-5" class="tab-pane fade {if $onglet == 5}information active in{/if}">
                {$this->cronDisplay($shop)}
                </div> *}
    {foreach from=$tabbsmarty item=row}
        <div id="tab-{$row.0|escape:'htmlall':'UTF-8'}" class="tab-pane fade {if $row.0 == $onglet || $row.0 == 0}information active in{/if}">
        <div class="displayAddForm_default" style="display:none">
        {$this->defaultDisplay($row.1|escape:'htmlall':'UTF-8', $row.0|escape:'htmlall':'UTF-8')}
        </div>
        <div class="displayAddForm_filter" style="display:none">
        {$this->option($row.1|escape:'htmlall':'UTF-8', $shop|escape:'htmlall':'UTF-8')}
        </div>
        <div class="displayAddForm_import" style="display:none">'
        {$this->addCSV($row.0|escape:'htmlall':'UTF-8')}
        </div>
        <div class="displayAddForm_{$row.1|escape:'htmlall':'UTF-8'}" style="display:none">
        {$this->add($row.1|escape:'htmlall':'UTF-8', $row.0|escape:'htmlall':'UTF-8')}
        </div>
        {$this->tab($row.1|escape:'htmlall':'UTF-8', $shop|escape:'htmlall':'UTF-8')}
        </div>
    {/foreach}
    <div id="tab-6" class="tab-pane fade {if $onglet == 6}information active in{/if}">
    <div class="displayAddForm_filter" style="display:none">
    {$this->option('url', $shop|escape:'htmlall':'UTF-8')}
    </div>
    <div class="displayAddForm_url" style="display:none">
    {$this->addRed(6) nofilter}
    </div>
    <div class="displayAddForm_import" style="display:none">
    {$this->addCSV(6) nofilter}
    </div>
    {$this->uploadTabDisplay($shop|escape:'htmlall':'UTF-8')}
    </div>
    </div>
</div>


