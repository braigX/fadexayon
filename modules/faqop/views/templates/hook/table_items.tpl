{*
* Google-Friendly FAQ Pages and Lists With Schema Markup module
*
*    @author    Opossum Dev
*    @copyright Opossum Dev
*    @license   You are just allowed to modify this copy for your own use. You are not allowed
* to redistribute it. License is permitted for one Prestashop instance only, except for test
* instances.
*}

<div class="panel op-faq-panel"><h3><i class="icon-list-ul"></i> {$table_title}
        <span class="panel-heading-action">
		<a class="list-toolbar-btn"
           href="{$new_item_link}">
			<span title="" data-toggle="tooltip" class="label-tooltip"
                  data-original-title="{l s='Add new' mod='faqop'}" data-html="true">
				<i class="process-icon-new "></i>
			</span>
		</a>
	</span>
    </h3>
    <div id="opBlocksContent">
        <div id="block-for-error-grid">
            <div class="op-error-wrap" style="display:none"></div>
        </div>

        <div id="opBlocks">
            <div class="panel">
                <div class="table-responsive-row clearfix">
                    <table class="table tableDnD op-grid-table">
                        <thead>
                        <tr class="nodrag nodrop op-table-filter-row">
                            <th class="op-filter-cell" colspan="2">
                                <div class="op-filter-search-wrap op-hide-on-sm">
                                    <input id="op-filter-search-input" class="op-filter-search-input" type="text"
                                           placeholder="{l s='Search by title' mod='faqop'}"
                                           value="{if isset($searchtext)}{$searchtext}{/if}">
                                    <button id="op-filter-search-button" class="btn btn-primary op-filter-search-button"
                                            type="button">
                                        <i class="fas fa-search"></i>
                                        {l s='Go' mod='faqop'}
                                    </button>
                                </div>
                            </th>
                            <th class="op-filter-cell" colspan="2" style="text-align:right">
                                <a class="op-clear-filters"
                                   href="{$clean_url}"><i
                                            class="fas fa-eraser"></i> <span
                                            class="op-hide-on-390">{l s='Clear search' mod='faqop'}</span></a>
                            </th>
                        </tr>
                        <tr class="nodrag nodrop">
                            <th style="width: 60px" class="op-hide-on-mobile-view">

                            </th>

                            <th class="op-sortable-column {if isset($classnames)}{if isset($classnames.title_th_class_name)}{$classnames.title_th_class_name}{/if}{/if}"
                                data-sort-op-name="orderTitle">{l s='Title' mod='faqop'}
                                <span role="button" class="fas fa-chevron-down"
                                      aria-label="Sort by"></span>
                            </th>
                            <th style="width: 70px"
                                class="op-sortable-column {if isset($classnames)}{if isset($classnames.id_th_class_name)}{$classnames.id_th_class_name}{/if}{/if}"
                                data-sort-op-name="orderId">{l s='Id' mod='faqop'}
                                <span role="button" class="fas fa-chevron-down"
                                      aria-label="Sort by"></span>
                            </th>
                            <th style="width: 130px">{l s='Action' mod='faqop'}</th>

                        </tr>
                        </thead>
                        {if $items}
                            <tbody>
                            {foreach $items as $key => $item}
                                <tr class="row_hover">
                                    <td>
                                        <i class="grid-checkbox-items grid-checkbox-css far fa-square" data-row-bulk-id="{$item.id_item}"></i>
                                    </td>

                                    <td>{$item.title|escape:'htmlall':'UTF-8'}</td>
                                    <td>{$item.id_item}</td>
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
                                                        <a href="{$item.clone_href}">
                                                            <i class="far fa-clone"></i> {l s='Clone' mod='faqop'}
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="{$item.delete_href}">
                                                            <i class="icon-trash"></i> {l s='Delete' mod='faqop'}
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
                                        {l s='No items found' mod='faqop'}
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
        <button id="bulk-button-delete" class="op-small-btn bulk-button" disabled>
            <i class="far fa-trash-alt usual-process"></i>
            <i class="fas fa-spinner fa-spin spin-process spin-process-inline" style="display:none"></i>
            &nbsp;
            {l s='Delete selected items' mod='faqop'}
        </button>
        <a class="btn btn-default pull-right black-pink-default"
           href="{$new_item_link}">
            <i class="process-icon-new"></i>
            {l s='New item' mod='faqop'}
        </a>
    </div>
</div>

