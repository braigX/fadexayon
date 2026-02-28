{*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innova Deluxe SL
* @copyright 2017 Innova Deluxe SL

* @license   INNOVADELUXE
*}

<div id="idxrcustomProductPanel" class="box herrre">
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
                                                    {* Replace file paths with URLs *}
                                                    {assign var="line" value=$line|replace:'/var/www/vhosts/plexi-cindar.com/httpdocs/':'https://www.plexi-cindar.com/'}
                                                    {* Check if the line contains an <img> tag *}
                                                    {if $line|strpos:'<img' !== false}
                                                        {* Output the line as raw HTML *}
                                                        {$line nofilter}
                                                    {else}
                                                        {* Escape HTML for safety *}
                                                        {$line|escape:'htmlall':'UTF-8'}
                                                    {/if}
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
                    {*Add with team wassim novatis*}
                    {if $note.product_depth != 0}
                        <tr>
                            <td><b>{l s='Épaisseur : ' mod='idxrcustomproduct'}</b></td>
                            <td>{$note.product_depth|number_format:0|escape:'htmlall':'UTF-8'} mm</td>
                        </tr>
                    {/if}
                    <tr>
                        <td>
                            <b>{l s='Volume:' mod='idxrcustomproduct'}</b>
                        </td>
                        <td>
                            {if isset($note.product_volume)}
                                {$note.product_volume|number_format:6|escape:'htmlall':'UTF-8'} m³
                            {/if}
                        </td>
                    </tr>
                    {*End*}
                </tbody>    
            </table>
        </div>
    {/foreach}
</div>