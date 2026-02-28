<div class="panel">
    <div class="panel-heading">
        <i class="icon-cogs"></i> {l s='STE SEO Master Management' mod='steseositemap'}
    </div>
    
    <div class="row">
        <div class="col-md-12">
             <form action="{$ste_submit_settings_url}" method="post" class="form-horizontal well">
                <h4>{l s='Settings' mod='steseositemap'}</h4>
                <div class="form-group">
                    <label class="control-label col-lg-3">{l s='Skip Default Shop' mod='steseositemap'}</label>
                    <div class="col-lg-9">
                        <span class="switch prestashop-switch fixed-width-lg">
                            <input type="radio" name="STE_SKIP_DEFAULT" id="STE_SKIP_DEFAULT_on" value="1" {if $ste_config.skip_default}checked="checked"{/if}>
                            <label for="STE_SKIP_DEFAULT_on">{l s='Yes' mod='steseositemap'}</label>
                            <input type="radio" name="STE_SKIP_DEFAULT" id="STE_SKIP_DEFAULT_off" value="0" {if !$ste_config.skip_default}checked="checked"{/if}>
                            <label for="STE_SKIP_DEFAULT_off">{l s='No' mod='steseositemap'}</label>
                            <a class="slide-button btn"></a>
                        </span>
                        <p class="help-block">{l s='Do not generate sitemap for the main (default) shop ID.' mod='steseositemap'}</p>
                    </div>
                </div>
                <!-- 
                <div class="form-group">
                    <label class="control-label col-lg-3">{l s='Manage Robots Physically' mod='steseositemap'}</label>
                    <div class="col-lg-9">
                         <span class="switch prestashop-switch fixed-width-lg">
                            <input type="radio" name="STE_MANAGE_ROBOTS" id="STE_MANAGE_ROBOTS_on" value="1" {if $ste_config.manage_robots}checked="checked"{/if}>
                            <label for="STE_MANAGE_ROBOTS_on">{l s='Yes' mod='steseositemap'}</label>
                            <input type="radio" name="STE_MANAGE_ROBOTS" id="STE_MANAGE_ROBOTS_off" value="0" {if !$ste_config.manage_robots}checked="checked"{/if}>
                            <label for="STE_MANAGE_ROBOTS_off">{l s='No' mod='steseositemap'}</label>
                            <a class="slide-button btn"></a>
                        </span>
                    </div>
                </div> 
                -->
                <div class="panel-footer">
                    <button type="submit" name="submitSteSettings" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='Save' mod='steseositemap'}</button>
                </div>
             </form>
        </div>
    
        <div class="col-md-12">
            <div class="alert alert-info">
                {l s='Generate isolated sitemaps for each shop context. This ensures domains are strictly respected.' mod='steseositemap'}
            </div>
            
            <a href="{$ste_generate_url}" class="btn btn-primary btn-lg">
                <i class="icon-refresh"></i> {l s='Generate ALL Sitemaps' mod='steseositemap'}
            </a>
            
            <div class="btn-group pull-right">
                <a href="{$ste_update_robots_url}" class="btn btn-default btn-lg">
                    <i class="icon-file-text"></i> {l s='Update Robots.txt' mod='steseositemap'}
                </a>
                <a href="{$ste_clean_robots_url}" class="btn btn-danger btn-lg" onclick="return confirm('{l s='Are you sure you want to remove legacy sitemap entries?' mod='steseositemap'}');">
                    <i class="icon-trash"></i> {l s='Clean Legacy Entries' mod='steseositemap'}
                </a>
            </div>
        </div>
    </div>

    <hr/>

    <h3>{l s='Shop Status' mod='steseositemap'}</h3>
    <table class="table">
        <thead>
            <tr>
                <th>{l s='ID' mod='steseositemap'}</th>
                <th>{l s='Shop Name' mod='steseositemap'}</th>
                <th>{l s='URL' mod='steseositemap'}</th>
                <th>{l s='Sitemap File' mod='steseositemap'}</th>
                <th>{l s='Status' mod='steseositemap'}</th>
                <th>{l s='Last Modified' mod='steseositemap'}</th>
                <th>{l s='Actions' mod='steseositemap'}</th>
            </tr>
        </thead>
        <tbody>
        {foreach from=$ste_shops item=shop}
            <tr>
                <td>{$shop.id_shop}</td>
                <td>{$shop.name}</td>
                <td>{$shop.url}</td>
                <td>{$shop.filename}</td>
                <td>
                    {if $shop.file_exists}
                        <span class="label label-success">{l s='Exists' mod='steseositemap'}</span>
                    {else}
                        <span class="label label-danger">{l s='Missing' mod='steseositemap'}</span>
                    {/if}
                </td>
                <td>{$shop.last_mod}</td>
                <td>
                    <a href="{$ste_generate_shop_url}&id_shop={$shop.id_shop}" class="btn btn-default btn-sm">
                        <i class="icon-refresh"></i> {l s='Regenerate' mod='steseositemap'}
                    </a>
                    {if $shop.file_exists}
                    <a href="{$shop.url}{$shop.filename}" target="_blank" class="btn btn-default btn-sm">
                        <i class="icon-external-link"></i> {l s='View' mod='steseositemap'}
                    </a>
                    {/if}
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>

    <hr/>

    <h3>{l s='Robots.txt Diagnostics' mod='steseositemap'}</h3>
    {if $ste_robot_issues}
        <div class="alert alert-warning">
            <ul>
            {foreach from=$ste_robot_issues item=issue}
                <li><strong>{$issue.level|upper}:</strong> {$issue.msg}</li>
            {/foreach}
            </ul>
        </div>
    {else}
        <div class="alert alert-success">
            {l s='Robots.txt looks healthy.' mod='steseositemap'}
        </div>
    {/if}
</div>
