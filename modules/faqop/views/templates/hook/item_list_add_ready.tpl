{*
* Google-Friendly FAQ Pages and Lists With Schema Markup module
*
*    @author    Opossum Dev
*    @copyright Opossum Dev
*    @license   You are just allowed to modify this copy for your own use. You are not allowed
* to redistribute it. License is permitted for one Prestashop instance only, except for test
* instances.
*}

{literal}
<script type="text/javascript">
    var url_redirect = '{/literal}{$back}{literal}'
</script>
{/literal}

<div class="panel"><h3><i class="icon-list-ul"></i> {$table_title}
        <span class="panel-heading-action">
	</span>
    </h3>
    <div id="opItemsContent" class="opItemsContent">
        <div id="block-for-error-grid">
            <div class="op-error-wrap" style="display:none"></div>
        </div>

        <div id="opItems">
            <div class="panel">
                <table class="table tableDnD op-grid-table op-grid-table-addready">
                    <thead>
                    <tr class="op-table-filter-row">
                        <th class="op-filter-cell" colspan="3">
                            <div class="op-search-add-ready-wrap">
                                <div class="op-filter-search-wrap">
                                    <input id="op-filter-search-input" class="op-filter-search-input" type="text" placeholder="{l s='Search by title' mod='faqop'}" value="{if $searchtext != ''}{$searchtext}{/if}">
                                    <button id="op-filter-search-button" class="btn btn-primary op-filter-search-button" type="button">
                                        <i class="fas fa-search"></i>
                                        {l s='Go' mod='faqop'}
                                    </button>
                                </div>
                                <a class="op-clear-filters"
                                   href="{$clearSearchLink}"><i
                                            class="fas fa-eraser"></i> {l s='Clear search' mod='faqop'}</a>
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <th style="width: 60px">

                        </th>
                        <th style="width: 70px"
                            class="op-sortable-column {if isset($classnames)}{if isset($classnames.id_th_class_name)}{$classnames.id_th_class_name}{/if}{/if}"
                            data-sort-op-name="orderId">{l s='Id' mod='faqop'}
                            <span role="button" class="fas fa-chevron-down"
                                  aria-label="Sort by"></span>
                        </th>
                        <th class="op-sortable-column {if isset($classnames)}{if isset($classnames.title_th_class_name)}{$classnames.title_th_class_name}{/if}{/if}"
                            data-sort-op-name="orderTitle">{l s='Title' mod='faqop'}
                            <span role="button" class="fas fa-chevron-down"
                                  aria-label="Sort by"></span>
                        </th>

                    </tr>
                    </thead>
                    {if $items}
                        <tbody>
                        {foreach $items as $key => $item}
                            <tr class="{if $key%2}alt_row{else}not_alt_row{/if} row_hover">
                                <td>
                                    <i class="grid-checkbox-items grid-checkbox-css far fa-square" data-row-bulk-id="{$item.id_item}"></i>
                                </td>
                                <td>{$item.id_item}</td>
                                <td>{$item.title|escape:'htmlall':'UTF-8'}</td>
                            </tr>
                        {/foreach}
                        </tbody>
                    {else}
                        <tr>
                            <td colspan="11" class="list-empty">
                                <div class="list-empty-msg">
                                    <i class="icon-warning-sign list-empty-icon"></i>
                                    {l s='No items found' mod='faqop'}
                                </div>
                            </td>
                        </tr>
                    {/if}
                </table>
            </div>
        </div>
    </div>
    <div class="panel-footer clearfix">
        <button id="add_selected_items" class="btn btn-default pull-left bulk-button" disabled type="button"
                data-type="{$type}" data-list="{$id_list}">
            <i class="process-icon-ok usual-process"></i>
            <i class="fas fa-spinner fa-spin spin-process spin-process-block" style="display:none"></i>
            {l s='Add Items' mod='faqop'}
        </button>

        <a href="{$back}" class="btn btn-default pull-right">
            <i class="process-icon-cancel"></i>
            {l s='Cancel' mod='faqop'}
        </a>
    </div>
</div>
