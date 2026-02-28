
<div class="panel reportlist">
        <h3>{l s='Reports' mod='ec_seo'}</h3>
        <div style="overflow-x:auto;">

        <div class="tablereport">
            <table class="table tablereport" width="100%">
                <thead>
                    <tr>
                        <th>{l s='File name' mod='ec_seo'}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$reports item=file}
                        
                        <tr id="">
                            <td>
                            <a href="{$uriec_seo|escape:'htmlall':'UTF-8'}report/{$file|escape:'htmlall':'UTF-8'}" target="_blank">{$file|escape:'htmlall':'UTF-8'}</a>
                            </td>
                            <td>
                                <button class="button btn btn-danger deletereport" data-file="report/{$file|escape:'htmlall':'UTF-8'}" type="submit" ><i class="icon-trash"></i>&nbsp;{l s='Delete' mod='ec_seo'}
                        </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>
        </div>
    {*    <label from="deleteSelected"></label>
        <button id="deleteSelectedProductVideo" class="button btn btn-default" style="float:left;">
            <i class="icon-trash"></i> {l s='Delete selection' mod='ec_seo'}
        </button>
    *}
    </div>
    <div class="panel taskReport">
        <div class="task-grp">
            <label class="control-label task-label">
                {l s='Generate an Excel report' mod='ec_seo'}
            </label>
            <button class="btn btn-default task-btn" onclick="javascript:$.post('{$link_task|escape:'htmlall':'UTF-8'}');showNoticeMessage('{l s='Task launched' mod='ec_seo'}');return false;">
                {l s='Launch' mod='ec_seo'}
            </button>
            <div class="margin-form task-form">
                <input type="text" readonly value="{$link_task|escape:'htmlall':'UTF-8'}" style="cursor: initial;"/>
            </div>
        </div>
        <div class="ec_suivi_task refreshReportPanel" >
            <i>{l s='(leave the mouse in the frame for a real-time display update)' mod='ec_seo'}</i>
            <div class="tabrefreshReportpanel"></div>
        </div>
    </div>
