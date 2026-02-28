<div class="table_product_list">
    <div class="header_block">
        <div class="title">
            {l s='Products list' mod='quantityupdate'}
        </div>
        <div class="filter_button">
            <button type="submit" id="submitResetButtonquantityupdate" class="btn btn-warning">
                <i class="icon-eraser"></i>    {l s='Reset' mod='quantityupdate'}
            </button>
            <button type="submit" id="submitFilterButtonquantityupdate" class="btn btn-primary" name="products_filter_submit" title="Filter">
                <i class="icon-search"></i>
                {l s='Search' mod='quantityupdate'}
            </button>
        </div>
    </div>

    <table class="table_product_block table product">
        <thead>
            <tr class="nodrag nodrop">
                <th class="center fixed-width-xs"></th>
                {foreach $fields_list as $value}
                    <th class="{if isset($value['align']) && $value['align'] == 'center'}center{/if}"> <span class="title_boxtitle_box">{$value['title']|escape:'htmlall':'UTF-8'}</span></th>
                {/foreach}
            </tr>
            <tr class="nodrag nodrop filter row_hover">
                <th class="text-center">  -- </th>
                {foreach $fields_list as $key => $value}
                    <th>
                     {if isset($value['search']) && $value['search']}
                         {if isset($value['type']) && $value['type'] == 'select'}
                             <select class="filter{if isset($value['align']) && $value['align'] == 'center'}center{/if}"  name="quantityupdateFilter_{$key|escape:'htmlall':'UTF-8'}" {if isset($value['width'])} style="width:{$value['width']|escape:'htmlall':'UTF-8'}px"{/if}>
                                 <option value="" >-</option>
                                 {if isset($value['list']) && is_array($value['list'])}
                                     {foreach $value['list'] AS $option_value => $option_display}
                                         <option {if isset($value['search_value']) && $value['search_value'] == $option_value}selected{/if} value="{$option_value|escape:'htmlall':'UTF-8'}">{$option_display|escape:'htmlall':'UTF-8'}</option>
                                     {/foreach}
                                 {/if}
                             </select>
                         {elseif isset($value['type']) && $value['type'] == 'min_max'}
                             <div class="{$value['type']|escape:'htmlall':'UTF-8'}">
                                 <input type="text" class="filter min" name="quantityupdateFilter_{$key|escape:'htmlall':'UTF-8'}_min" value="{if isset($value['search_value']['min']) && $value['search_value']['min'] !== ""}{$value['search_value']['min']|escape:'htmlall':'UTF-8'}{/if}" placeholder="{l s='Min' mod='quantityupdate'}">
                                 <input type="text" class="filter max" name="quantityupdateFilter_{$key|escape:'htmlall':'UTF-8'}_max" value="{if isset($value['search_value']['max']) && $value['search_value']['max'] !== ""}{$value['search_value']['max']|escape:'htmlall':'UTF-8'}{/if}" placeholder="{l s='Max' mod='quantityupdate'}" >
                             </div>
                         {elseif isset($value['type']) && $value['type'] == 'bool'}
                             <select class="filter fixed-width-sm center" name="quantityupdateFilter_{if isset($key)}{$key|escape:'htmlall':'UTF-8'}{/if}">
                                 <option value="">-</option>
                                 <option {if isset($value['search_value']) && $value['search_value'] == 1}selected{/if} value="1">{l s='Yes' mod='quantityupdate'}</option>
                                 <option {if isset($value['search_value']) && $value['search_value'] == 2}selected{/if} value="2" >{l s='No' mod='quantityupdate'}</option>
                             </select>
                         {else}
                             <input type="text" class="filter" name="quantityupdateFilter_{$key|escape:'htmlall':'UTF-8'}" value="{if isset($value['search_value']) && $value['search_value']}{$value['search_value']|escape:'htmlall':'UTF-8'}{/if}" {if isset($value['width'])}style="width:{$value['width']|escape:'htmlall':'UTF-8'}px"{/if} >
                         {/if}
                     {else}
                         <span class="center-text">--</span>
                     {/if}
                    </th>
                {/foreach}
            </tr>
        </thead>

        <tbody>
            {if count($products)>0}
                {foreach $products as $product}
                    <tr id="tr_{$product['id_product']|escape:'htmlall':'UTF-8'}">
                        <td class="row-selector text-center">
                            <input type="checkbox" name="selected_products[]" {if isset($selected_products[$product['id_product']]) && $selected_products[$product['id_product']]}checked{/if} value="{$product['id_product']|escape:'htmlall':'UTF-8'}"  class="noborder checkbox_product">
                        </td>
                        {foreach $fields_list as $key => $value}
                            <td class="{if isset($value['align']) && $value['align'] == 'center'}center{/if}">
                                {if isset($value['type']) && $value['type'] == 'bool'}
                                    <a class="list-action-enable ajax_table_link  {if $product[$key] == 1} action-enabled {else} action-disabled {/if}">
                                        {if $product[$key] == 1}
                                            <i class="icon-check"></i>
                                        {else}
                                            <i class="icon-remove"></i>
                                        {/if}
                                    </a>
                                {else}
                                    {if $key === 'image_link'}
                                        {if $product[$key]}
                                            <img src="{$product[$key]|escape:'htmlall':'UTF-8'}" alt="product image"/>
                                        {/if}
                                    {else}
                                        {$product[$key]|escape:'htmlall':'UTF-8'}
                                    {/if}
                                {/if}
                            </td>
                        {/foreach}
                    </tr>
                {/foreach}
            {else}
                <tr>
                    <td class="list-empty" colspan="10">
                        <div class="list-empty-msg">
                            <i class="icon-warning-sign list-empty-icon"></i>
                            {l s='No records found' mod='quantityupdate'}
                        </div>
                    </td>
                </tr>
            {/if}
        </tbody>
    </table>
    <div class="pagination_left_block">
        <input type="hidden" name="pagination_page" value="{$p|escape:'htmlall':'UTF-8'}">
        <input type="hidden" name="pagination_show" value="{$n|escape:'htmlall':'UTF-8'}">
        <select class="bulk_actions" id="bulk_actions" name="bulk_actions">
            <option value="0">{l s='Bulk actions' mod='quantityupdate'} </option>
            <option value="1">{l s='Select all' mod='quantityupdate'} </option>
            <option value="2">{l s='Unselect all' mod='quantityupdate'} </option>
        </select>
        {if $start!=$stop}
            <div id="display_pagination">
                <span>{l s='Display' mod='quantityupdate'}</span>
                <select class="display_pagination" name="display_pagination">
                    <option {if $n == 20}selected{/if} value="20">20</option>
                    <option  {if $n == 50}selected{/if} value="50">50</option>
                    <option {if $n == 100}selected{/if} value="100">100</option>
                    <option {if $n == 300}selected{/if} value="300">300</option>
                    <option {if $n == 1000}selected{/if} value="1000">1000</option>
                </select>
                <div class="display_pagination_all">
                    / {$count|escape:'htmlall':'UTF-8'}{l s=' result(s)' mod='quantityupdate'}
                </div>
            </div>
        {/if}
    </div>
    <div class="pagination_right_block">
        {if $start!=$stop}
            <div style="clear: "></div>
            <div id="quantityupdate_pagination">
                <ul class="quantityupdate_pagination">
                    {if $start==3}
                        <li>
                            <a href="{$path_pagination|escape:'htmlall':'UTF-8'}1">
                                <span>1</span>
                            </a>
                        </li>
                        <li>
                            <a href="{$path_pagination|escape:'htmlall':'UTF-8'}2">
                                <span>2</span>
                            </a>
                        </li>
                    {/if}
                    {if $start==2}
                        <li>
                            <a href="{$path_pagination|escape:'htmlall':'UTF-8'}1">
                                <span>1</span>
                            </a>
                        </li>
                    {/if}
                    {if $start>3}
                        <li>
                            <a href="{$path_pagination|escape:'htmlall':'UTF-8'}1">
                                <span>1</span>
                            </a>
                        </li>
                        <li class="truncate">
                        <span>
                          <span>...</span>
                        </span>
                        </li>
                    {/if}
                    {section name=pagination start=$start loop=$stop+1 step=1}
                        {if $p == $smarty.section.pagination.index}
                            <li data-p="{$p|escape:'html':'UTF-8'}" class="active current">
                          <span>
                            <span>{$p|escape:'html':'UTF-8'}</span>
                          </span>
                            </li>
                        {else}
                            <li>
                                <a href="{$path_pagination|escape:'htmlall':'UTF-8'}{$smarty.section.pagination.index|escape:'htmlall':'UTF-8'}" >
                                    <span>{$smarty.section.pagination.index|escape:'html':'UTF-8'}</span>
                                </a>
                            </li>
                        {/if}
                    {/section}
                    {if $pages_nb>$stop+2}
                        <li class="truncate">
                            <span>
                              <span>...</span>
                            </span>
                        </li>
                        <li>
                            <a href="{$path_pagination|escape:'htmlall':'UTF-8'}{$pages_nb|escape:'htmlall':'UTF-8'}">
                                <span>{$pages_nb|intval}</span>
                            </a>
                        </li>
                    {/if}
                    {if $pages_nb==$stop+1}
                        <li>
                            <a href="{$path_pagination|escape:'htmlall':'UTF-8'}{$pages_nb|escape:'htmlall':'UTF-8'}">
                                <span>{$pages_nb|intval}</span>
                            </a>
                        </li>
                    {/if}
                    {if $pages_nb==$stop+2}
                        <li>
                            <a href="{$path_pagination|escape:'htmlall':'UTF-8'}{($pages_nb-1)|escape:'htmlall':'UTF-8'}">
                                <span>{$pages_nb-1|intval}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{$path_pagination|escape:'htmlall':'UTF-8'}{$pages_nb|escape:'htmlall':'UTF-8'}">
                                <span>{$pages_nb|intval}</span>
                            </a>
                        </li>
                    {/if}
                </ul>
            </div>
        {/if}
    </div>
<div style="clear: both"></div>
</div>