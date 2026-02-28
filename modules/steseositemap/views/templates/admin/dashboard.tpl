<div class="panel">
    <div class="panel-heading">
        <i class="icon-cogs"></i> {l s='STE SEO Master' mod='steseositemap'}
    </div>
    
    <div class="row">
        <div class="col-lg-12">
            <div class="alert alert-info">
                {l s='This module generates isolated sitemaps for each shop. Use the buttons below to manage them.' mod='steseositemap'}
            </div>
            <div class="margin-form">
                <a href="{$ste_generate_url}" class="btn btn-primary">
                    <i class="icon-refresh"></i> {l s='Generate All Sitemaps' mod='steseositemap'}
                </a>
                <a href="{$ste_update_robots_url}" class="btn btn-default">
                     <i class="icon-file-text"></i> {l s='Update robots.txt (Add Sitemap Link)' mod='steseositemap'}
                </a>
                <a href="{$ste_clean_robots_url}" class="btn btn-danger" onclick="return confirm('{l s='Remove all sitemap entries from physical robots.txt?' mod='steseositemap'}');">
                    <i class="icon-trash"></i> {l s='Clean robots.txt' mod='steseositemap'}
                </a>
            </div>
        </div>
    </div>

    <hr/>

    <h4>{l s='Shops and Sitemaps' mod='steseositemap'}</h4>
    <table class="table">
        <thead>
            <tr>
                <th>{l s='ID' mod='steseositemap'}</th>
                <th>{l s='Shop Name' mod='steseositemap'}</th>
                <th>{l s='Sitemap URL' mod='steseositemap'}</th>
                <th>{l s='File' mod='steseositemap'}</th>
                <th>{l s='Status' mod='steseositemap'}</th>
                <th>{l s='Last Modified' mod='steseositemap'}</th>
                <th class="text-right">{l s='Actions' mod='steseositemap'}</th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$ste_shops item=shop}
            <tr>
                <td>{$shop.id_shop}</td>
                <td><strong>{$shop.name}</strong><br/><small>{$shop.url}</small></td>
                <td><a href="{$shop.url}sitemap.xml" target="_blank">/sitemap.xml</a></td>
                <td><code>{$shop.filename}</code></td>
                <td>
                    {if $shop.file_exists}
                        <span class="label label-success">{l s='Active' mod='steseositemap'}</span>
                    {else}
                        <span class="label label-danger">{l s='Missing' mod='steseositemap'}</span>
                    {/if}
                </td>
                <td>{$shop.last_mod}</td>
                <td class="text-right">
                    <a href="{$ste_generate_shop_url}&id_shop={$shop.id_shop}" class="btn btn-default btn-sm">
                        <i class="icon-refresh"></i> {l s='Regenerate' mod='steseositemap'}
                    </a>
                    <button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#modalRobots-{$shop.id_shop}">
                        <i class="icon-pencil"></i> {l s='Edit Robots' mod='steseositemap'}
                    </button>
                </td>
            </tr>

            <!-- Robots Modal -->
            <div class="modal fade" id="modalRobots-{$shop.id_shop}" tabindex="-1">
                <div class="modal-dialog">
                    <form action="{$ste_submit_settings_url}" method="post">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">{l s='Robots.txt Content' mod='steseositemap'} - {$shop.name}</h4>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="id_shop_robots" value="{$shop.id_shop}">
                                <div class="form-group">
                                    <label>{l s='Custom Directives' mod='steseositemap'}</label>
                                    <textarea name="robots_content" class="form-control" rows="10" style="font-family:monospace;">{$shop.robots_content|escape:'html':'UTF-8'}</textarea>
                                    <p class="help-block">{l s='The Sitemap directive for this shop will be added automatically if not already present.' mod='steseositemap'}</p>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">{l s='Close' mod='steseositemap'}</button>
                                <button type="submit" name="submitSteRobotsContent" class="btn btn-primary">{l s='Save' mod='steseositemap'}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            {/foreach}
        </tbody>
    </table>

    <hr/>

    <form action="{$ste_submit_settings_url}" method="post" class="panel">
        <div class="panel-heading">{l s='Configuration' mod='steseositemap'}</div>
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
            </div>
        </div>
        <div class="panel-footer">
            <button type="submit" name="submitSteSettings" class="btn btn-default pull-right">
                <i class="process-icon-save"></i> {l s='Save' mod='steseositemap'}
            </button>
        </div>
    </form>
</div>
