{*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innova Deluxe SL
* @copyright 2017 Innova Deluxe SL

* @license   INNOVADELUXE
*}

<div id="idxrcustomProductPanel" class="box">
    {foreach from=$notes item=note}
        <div class="well">
            <table class="table table-bordered">
                <thead class="thead-default">
                    <th colspan="2">
                        {l s='Customized product details' mod='idxrcustomproduct'}
                    </th>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <b>{l s='Customization:' mod='idxrcustomproduct'}</b>
                        </td>         
                        <td>
                            <table class="table table-bordered">
                                {foreach from=$note.notes_a item=line}                                    
                                    {if $line != ''}
                                        <tr>
                                            <td>
                                                <p>
                                                {if !is_array($line)}
                                                    {$line|escape:'htmlall':'UTF-8'}
                                                {elseif isset($line.file_key)}
                                                    {$line.title|escape:'htmlall':'UTF-8'}<a href="{$file_controller}{$line.file_key}" target="_blank">{$line.file_name|escape:'htmlall':'UTF-8'}</a>
                                                {elseif isset($line.texts)}
                                                    <div class="idxrcustomproduct-notes">
                                                        <div class="idxrcustomproduct-notes-title">{$line.title|escape:'htmlall':'UTF-8'}:</div>
                                                        <div class="idxrcustomproduct-notes-texts">
                                                        {foreach from=$line.texts item=text}
                                                            <div class="idxrcustomproduct-notes-text">{$text|escape:'htmlall':'UTF-8'}</div>
                                                        {/foreach}
                                                        </div>
                                                    </div>
                                                {/if}
                                                
                                                </p> 
                                            </td>
                                        </tr>
                                    {/if}
                                {/foreach}
                            </table>
                        </td>
                    </tr>
                </tbody>    
            </table>
        </div>
    {/foreach}
</div>