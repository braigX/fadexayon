{*
* Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
*
*    @author    Jpresta
*    @copyright Jpresta
*    @license   See the license of this module in file LICENSE.txt, thank you.
*}
<div class="panel">
    <h3>{if $avec_bootstrap}<i class="icon-sitemap"></i>{else}<img width="16" height="16" src="../img/admin/multishop_config.png" alt=""/>{/if}&nbsp;{l s='Multistore' mod='jprestaspeedpack'}</h3>
    <form id="pagecache_form_shopsinfos" action="{$request_uri|escape:'html':'UTF-8'}" method="post" onsubmit='return confirm("{l s='WARNING! This will replace the configuration of selected shops, are you sure?' mod='jprestaspeedpack'}");'>
        <input type="hidden" name="submitModule" value="true"/>
        <input type="hidden" name="pctab" value="shopsinfos"/>
        <fieldset>
            {if $avec_bootstrap}
                <div class="bootstrap"><div class="alert alert-info" style="display: block;">&nbsp;{l s='It is usually a pain to configure all shops so here you can copy the configuration of the current shop to other shops.' mod='jprestaspeedpack'}</div></div>
            {else}
                <div class="hint clear" style="display: block;">&nbsp;{l s='It is usually a pain to configure all shops so here you can copy the configuration of the current shop to other shops.' mod='jprestaspeedpack'}</div>
            {/if}
            <table id="shopsinfosTable" class="table table-bordered table-striped">
                <colgroup>
                    <col width="0*">
                    <col width="0*">
                    <col width="*">
                    <col width="0*">
                    <col width="0*">
                    <col width="0*">
                </colgroup>
                <thead>
                <tr>
                    <th></th>
                    <th style="text-align: center">{l s='ID' d='Admin.Global'}</th>
                    <th>{l s='Name' d='Admin.Global'}</th>
                    <th style="text-align: center">{l s='Theme' mod='jprestaspeedpack'}</th>
                    <th style="text-align: center">{l s='Status' d='Admin.Global'}</th>
                    <th style="text-align: center">{l s='Settings' d='Admin.Global'}</th>
                </tr>
                </thead>
                <tbody>
                {foreach $pagecache_shopsinfos as $shopinfos}
                    <tr>
                        <td style="text-align: center">{if !$shopinfos['is_current']}<input type="checkbox" name="id_shops[]" value="{$shopinfos['id_shop']|intval}">{else}({l s='Current shop' mod='jprestaspeedpack'}){/if}</td>
                        <td style="text-align: center">{$shopinfos['id_shop']|intval}</td>
                        <td>{$shopinfos['name']|escape:'html':'UTF-8'}</td>
                        <td style="text-align: center">{$shopinfos['theme_name']|escape:'html':'UTF-8'}</td>
                        <td style="text-align: center">
                            {if $shopinfos['module_enabled']}
                                <i class="material-icons" style="color: green">check</i>
                            {else}
                                <i class="material-icons" style="color: red">clear</i>
                            {/if}
                        </td>
                        <td style="text-align: center">
                            {if $shopinfos['module_install_step'] == 9}
                                <i class="material-icons" style="color: green">check</i>
                            {else}
                                {$shopinfos['module_install_step']|intval} / 9
                            {/if}
                        </td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
            <button type="submit" id="submitModuleShopsinfos" name="submitModuleShopsinfos" class="btn btn-primary pull-right">
                <i class="process-icon-duplicate"></i> {l s='Copy configuration to selected shops' mod='jprestaspeedpack'}
            </button>
        </fieldset>
    </form>
</div>
