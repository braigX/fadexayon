{*
* Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
*
*    @author    Jpresta
*    @copyright Jpresta
*    @license   See the license of this module in file LICENSE.txt, thank you.
*}
<div id="queryAnalysis{$id_query|intval}">
    {if isset($tables) && (count($tables) > 0)}
        <div style="width: fit-content; float: right; margin: 0 1rem;">
            <div class="sqlprofsubtitle" style="margin-top: 0">{l s='Tables used in this query' mod=$module_name}</div>
            <table>
                <thead>
                    <tr>
                        <th>{l s='Table' mod=$module_name}</th>
                        <th class="tdright">{l s='Row count' mod=$module_name}</th>
                    </tr>
                </thead>
                <tbody>
                {foreach $tables as $tableName => $rowCount}
                    <tr>
                        <td>{$tableName|escape:'html':'UTF-8'}</td>
                        <td class="tdright">{$rowCount|intval}</td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        </div>
    {/if}
    <div>
        <div class="sqlprofsubtitle">{l s='Example of query with real parameters' mod=$module_name}</div>
        {if !empty($sql)}
            <div class="sql">{$sql nofilter}</div>
        {else}
            <div class="sql"><pre>{$query->getExampleQuery()|trim|escape:'html':'UTF-8'}</pre></div>
        {/if}
    </div>
    {if isset($explain) && (count($explain) > 0)}
        <div>
            <div class="sqlprofsubtitle">{l s='"EXPLAIN" of the query' mod=$module_name}</div>
            <table>
                <thead>
                <tr>
                    {foreach $explain[0] as $colName => $colValue}
                        <th>{$colName|escape:'html':'UTF-8'}</th>
                    {/foreach}
                </tr>
                </thead>
                <tbody>
                {foreach $explain as $row}
                    <tr>
                        {foreach $row as $col}
                            <td>{$col|escape:'html':'UTF-8'}</td>
                        {/foreach}
                    </tr>
                {/foreach}
                </tbody>
            </table>
        </div>
    {/if}
    {if isset($indexes) && count($indexes) > 0}
        <div>
            <div class="sqlprofsubtitle">{l s='Indexes created from here' mod=$module_name}</div>
            <div class="alert alert-info">{l s='Suggested indexes do not always improve SQL queries. If you do not see any improvements, then you can delete them.' mod=$module_name}</div>
            <table>
                <thead>
                <tr>
                    <th>{l s='Index name' mod=$module_name}</th>
                    <th>{l s='Table' mod=$module_name}</th>
                    <th>{l s='Columns' mod=$module_name}</th>
                    <th style="text-align: center">{l s='Actions' mod=$module_name}</th>
                </tr>
                </thead>
                <tbody>
                {foreach $indexes as $index}
                    <tr>
                        <td>{$index['index']|escape:'html':'UTF-8'}</td>
                        <td>{$index['table']|escape:'html':'UTF-8'}</td>
                        <td>{$index['columns']|escape:'html':'UTF-8'}</td>
                        <td><a class="btn btn-sm btn-danger" onclick="deleteIndex({$id_query|intval}, '{$index['table']|escape:'html':'UTF-8'}', '{$index['index']|escape:'html':'UTF-8'}'); return false;"><i class="material-icons" style="font-size: 1rem; vertical-align: bottom;">delete</i> {l s='Delete this index' mod=$module_name}</a></td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        </div>
    {/if}
    {if isset($suggestions)}
        <div>
            <div class="sqlprofsubtitle">{l s='Suggestions or tips' mod=$module_name}</div>
            {if count($suggestions) === 0}
                <div>:-( {l s='Sorry, no suggestion found for this query' mod=$module_name}</div>
            {else}
                {foreach $suggestions as $suggestion}
                    <div class="suggestions suggestions-{$suggestion.type|escape:'html':'UTF-8'}">
                        {$suggestion.msg|escape:'html':'UTF-8'}
                        {if isset($suggestion.action) && isset($suggestion.action_label) }
                            <a class="btn btn-sm btn-primary" style="margin-left: 2rem" onclick="{$suggestion.action|escape:'html':'UTF-8'};return false;"><span class="material-icons">arrow_right_alt</span> {$suggestion.action_label|escape:'javascript':'UTF-8'}</a>
                        {/if}
                    </div>
                {/foreach}
            {/if}
        </div>
    {/if}
    {if count($callstacks) > 0}
        <div>
            <div class="sqlprofsubtitle">{l s='Callstacks' mod=$module_name}</div>
            <table>
                <thead>
                    <tr>
                        <th>{l s='Executions count' mod=$module_name}</th>
                        <th>{l s='Callstacks' mod=$module_name}</th>
                    </tr>
                </thead>
                <tbody>
                {foreach $callstacks as $callstack}
                    <tr>
                        <td style="text-align: center; vertical-align: top">{$callstack->getExecutedCount()|intval}</td>
                        <td class="callstack">
                            <a href="#" onclick="$('#cs{$callstack->getIdStack()|intval}').toggle();return false;">{$callstack->getCaller()|escape:'html':'UTF-8'}</a>
                            <div id="cs{$callstack->getIdStack()|intval}" style="display: none; margin-top: 5px">
                                {foreach $callstack->getCallstack() as $call}
                                    {if isset($call.file)}
                                        <div class="cs-call">
                                            <span class="cs-file">{$call.file|escape:'html':'UTF-8'}</span>:<span class="cs-line">{$call.line|intval}</span> - <span class="cs-function">{$call.class|default:''|escape:'html':'UTF-8'}::{$call.function|default:''|escape:'html':'UTF-8'}()</span>
                                        </div>
                                    {/if}
                                {/foreach}
                            </div>
                        </td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        </div>
    {/if}
    <div>
        <div class="sqlprofsubtitle">{l s='Actions' mod=$module_name}</div>
    {if isset($duration_ms)}
        <div class="suggestions {if $duration_percent > 5 && $duration_diff < -2}suggestions-success{elseif $duration_percent < -5 && $duration_diff > 2}suggestions-error{/if}" style="margin-bottom: 1rem;">
            <p><i class="material-icons" style="vertical-align: bottom;font-size: 1.1rem;color: #25b9d7;">warning</i> {l s='Please note, this duration may vary depending on the current server load. We recommend running the query multiple times with and without optimizations to be able to compare.' mod=$module_name}</p>
            {l s='Current duration of the query' mod=$module_name}: <b>{$duration_ms|escape:'html':'UTF-8'}ms</b> ({$query->getDurationMaxMs()|round}ms)<br/>
            {if $duration_percent > 5 && $duration_diff < -2}
                {l s='The query is faster than during the profiling' mod=$module_name}: <b style="color: green">{$duration_percent|escape:'html':'UTF-8'}%</b> ({$duration_diff|intval}ms)<br/>
            {elseif $duration_percent < -5 && $duration_diff > 2}
                {l s='The query is slower than during the profiling' mod=$module_name}: <b style="color: red">{$duration_percent|escape:'html':'UTF-8'}%</b> ({$duration_diff|intval}ms)<br/>
            {else}
                {l s='The query is almost as fast as during the profiling' mod=$module_name}: <b>{$duration_percent|escape:'html':'UTF-8'}%</b> ({$duration_diff|intval}ms)<br/>
            {/if}
        </div>
    {/if}
        <button onclick="displayQueryAnalysis({$id_query|intval}, 1);$(this).prop('disabled', true);return false;" class="btn btn-primary"><i class="material-icons">refresh</i>&nbsp;{l s='Refresh analysis' mod=$module_name}</button>
    </div>
</div>

