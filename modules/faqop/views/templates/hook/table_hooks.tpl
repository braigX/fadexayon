{*
* Google-Friendly FAQ Pages and Lists With Schema Markup module
*
*    @author    Opossum Dev
*    @copyright Opossum Dev
*    @license   You are just allowed to modify this copy for your own use. You are not allowed
* to redistribute it. License is permitted for one Prestashop instance only, except for test
* instances.
*}

<div class="panel"><h3><i class="icon-list-ul"></i> {$table_title}</h3>

    {if $toDeleteArray}
        <h2>{l s='Delete unused custom hooks' mod='faqop'}</h2>
        <ul class="custom-hooks-to-delete">
            {foreach $toDeleteArray as $row}
                <li><span>{$row.hook_name}</span> <a href="{$linkSelf}&delete_id={$row.id_hook}">
                        <i class="icon-trash"></i></a></li>
            {/foreach}
        </ul>
    {/if}

    {if $infoArray}
        <h2>{l s='Used custom hooks' mod='faqop'}</h2>
        <table class="custom-hooks-info">
            <tr>
                <th>Hook</th>
                <th>Modules</th>
            </tr>
            {foreach $infoArray as $key => $row}
                <tr>
                <td>{$key}</td>
                <td>
                        {foreach $row as $module}
                            <p>{$module.module_name}</p>
                        {/foreach}
                </td>
                </tr>
            {/foreach}
        </table>
    {/if}

    {if !$infoArray && !$toDeleteArray}
        <table class="table">
            <tr>
                <td colspan="11" class="list-empty">
                    <div class="list-empty-msg">
                        <i class="icon-warning-sign list-empty-icon"></i>
                        {l s='No custom hooks found' mod='faqop'}
                    </div>
                </td>
            </tr>
        </table>
    {/if}
</div>
