<div class="panel">
        <h3>{l s='Backups' mod='ec_seo'}</h3>
        <div style="overflow-x:auto;">
        {foreach from=$backups key=k item=files}
        <div class="tablebackup{$k|escape:'htmlall':'UTF-8'}">
            <table class="table tablebackup" width="100%">
                <thead>
                    <tr>
                        <th>{$k|escape:'htmlall':'UTF-8'}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$files item=file}
                        
                        <tr id="">
                            <td>
                            <a href="{$uriec_seo|escape:'htmlall':'UTF-8'}backup/{$k|escape:'htmlall':'UTF-8'}/{$file|escape:'htmlall':'UTF-8'}" target="_blank">{$file|escape:'htmlall':'UTF-8'}</a>
                            </td>
                            <td align="right">
                                <button class="button btn btn-danger deletebackup" data-file="backup/{$k|escape:'htmlall':'UTF-8'}/{$file|escape:'htmlall':'UTF-8'}" type="submit" ><i class="icon-trash"></i>&nbsp;{l s='Delete' mod='ec_seo'}
                                <button class="button btn btn-default execbackup" onclick="javascript:$.post('{$ec_seo_controller_uri|escape:'htmlall':'UTF-8'}backup?ec_token={$token|escape:'htmlall':'UTF-8'}&id_shop={$ec_id_shop|escape:'htmlall':'UTF-8'}&file={$k|escape:'htmlall':'UTF-8'}/{$file|escape:'htmlall':'UTF-8'}&object={$k|escape:'htmlall':'UTF-8'}');return false;"  ><i class="icon-refresh"></i>&nbsp;{l s='Restore' mod='ec_seo'}</button></td>
                        </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>
        {/foreach}
        </div>
    {*    <label from="deleteSelected"></label>
        <button id="deleteSelectedProductVideo" class="button btn btn-default" style="float:left;">
            <i class="icon-trash"></i> {l s='Delete selection' mod='ec_seo'}
        </button>
    *}
    </div>
    <div class="panel refreshBackUpPanel">
        <h3>{l s='BackUp control panel' mod='ec_seo'}</h3> <i>{l s='(leave the mouse in the frame for a real-time display update)' mod='ec_seo'}</i>
        <div class="tabrefreshbackuppanel"></div>
    </div>
