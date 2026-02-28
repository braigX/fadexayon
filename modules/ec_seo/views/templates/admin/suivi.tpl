<table class="table" style="">
    <thead>
        <tr>
            <th style="text-align:center;">{l s='Start' mod='ec_seo'}</th>
            <th style="text-align:center;">{l s='End' mod='ec_seo'}</th>
            <th style="text-align:center;">{l s='State' mod='ec_seo'}</th>
            <th style="text-align:center;">{l s='Step' mod='ec_seo'}</th>
            <th style="text-align:center;">{l s='Counter' mod='ec_seo'}</th>
        </tr>
    </thead>
    <tbody>
            <td style="text-align:center;">{$START_TIME|escape:'htmlall':'UTF-8'}</td>
            <td style="text-align:center;">{$END_TIME|escape:'htmlall':'UTF-8'}</td>
            <td style="text-align:center;">{if $STATE != "done"}{if $STATE == "running"}{l s='Running' mod='ec_seo'}{else}<span class="restock">{l s='stop' mod='ec_seo'}{/if}{/if}</td>
            <td style="text-align:center;">{if $STATE != "done"}{$STAGE|escape:'htmlall':'UTF-8'}{/if}</td>
            <td style="text-align:center;">{if $STATE != "done"}{$PROGRESS|escape:'htmlall':'UTF-8'}{/if}</td>
    </tbody>
</table>