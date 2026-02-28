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
        $(function() {
            var opCells = $("#opCells");
            opCells.sortable({
                opacity: 0.6,
                cursor: "move",
                update: function() {
                    var order = JSON.stringify($(this).sortable("toArray"));
                    updateItemsPosition(order);
                }
            });
            opCells.hover(function() {
                    $(this).css("cursor","move");
                },
                function() {
                    $(this).css("cursor","auto");
                });
        })
    </script>
{/literal}

    <div class="panel op-faq-panel"><h3><i class="icon-list-ul"></i> {$table_title}
        </h3>
        <div id="opItemsContent" class="opItemsContent">
            <div id="block-for-error-grid">
                <div class="op-error-wrap" style="display:none"></div>
            </div>
            <div id="block-for-success-grid">
                <div class="op-success-wrap" style="display:none"></div>
            </div>

            <div id="opItems">
                <div class="panel">
                    <div class="table-responsive-row clearfix">
                    <table class="table tableDnD op-grid-table">
                        <thead>
                        <tr>
                            <th style="width: 40px">

                            </th>

                            <th style="width: 40px">{l s='Id' mod='faqop'}
                            </th>
                            <th style="width: 90px" class="op-hide-on-mobile-view">{l s='Position' mod='faqop'}
                            </th>
                            <th>{l s='Title' mod='faqop'}
                            </th>

                            <th style="width: 90px"></th>

                        </tr>
                        </thead>
                        {if $items}
                            <tbody id="opCells" class="ui-sortable" data-type="{$type}" data-list="{$id_list}">
                            {foreach $items as $key => $item}
                                <tr class="{if $key%2}alt_row{else}not_alt_row{/if} row_hover"
                                    id="op_row-{$item.id}">
                                    <td>
                                        <i class="grid-checkbox-items grid-checkbox-css far fa-square" data-row-bulk-id="{$item.id}"></i>
                                    </td>
                                    <td>{$item.id}</td>
                                    <td class="op-td-position op-hide-on-mobile-view"><i class="icon-arrows"></i>{$item.position}</td>
                                    <td>{$item.title|escape:'htmlall':'UTF-8'}</td>

                                    <td>
                                        <div class="btn-group-action">
                                            <div class="btn-group pull-left">
                                                <a class="btn btn-default"
                                                   href="{$item.edit_href}"
                                                   title="{l s='Edit' mod='faqop'}">
                                                    <i class="icon-edit"></i> {l s='Edit' mod='faqop'}
                                                </a>
                                                <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                                    <i class="icon-caret-down"></i>&nbsp;
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a href="{$item.remove_href}">
                                                            <i class="fas fa-folder-minus"></i> {l s='Remove' mod='faqop'}
                                                        </a>
                                                    </li>
                                                </ul>

                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            {/foreach}
                            </tbody>
                        {else}
                            <tr>
                                <td colspan="11" class="list-empty">
                                    <div class="list-empty-msg">
                                        <i class="icon-warning-sign list-empty-icon"></i>
                                        {l s='No items' mod='faqop'}
                                    </div>
                                </td>
                            </tr>
                        {/if}
                    </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel-footer clearfix">
            <div class="bulk-wrapper">
                <button id="bulk-button-remove" class="btn btn-default bulk-button" disabled="disabled"
                        data-type="{$type}" data-list="{$id_list}">
                    <i class="fas fa-folder-minus usual-process"></i>
                    <i class="fas fa-spinner fa-spin spin-process spin-process-inline" style="display:none"></i>
                    {l s='Remove selected items?' mod='faqop'}
                </button>
            </div>
            <a class="btn btn-default pull-right"
               href="{$new_href|escape:'quotes':'UTF-8'}">
                <i class="process-icon-new"></i>
                {l s='Create new item' mod='faqop'}
            </a>
            <a class="btn btn-default pull-right"
               href="{$add_ready_href|escape:'quotes':'UTF-8'}">
                <i class="process-icon-anchor"></i>
                {l s='Add existing items' mod='faqop'}
            </a>
            <a class="btn btn-default pull-right hidden-sm-down"
               href="{$back}">
                <i class="process-icon-back"></i>
                {l s='Back' mod='faqop'}
            </a>
        </div>
    </div>
