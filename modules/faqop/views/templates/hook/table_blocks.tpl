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
        $(function () {
            var opCells = $("#opCells");
            opCells.sortable({
                opacity: 0.6,
                cursor: "move",
                update: function () {
                    var order = JSON.stringify($(this).sortable("toArray"));
                    updateBlocksPosition(order);
                }
            });
            opCells.hover(function () {
                    $(this).css("cursor", "move");
                },
                function () {
                    $(this).css("cursor", "auto");
                });
        })
    </script>
{/literal}

<div class="panel op-faq-panel"><h3><i class="icon-list-ul"></i> {$table_title}
        <span class="panel-heading-action">
		<a class="list-toolbar-btn"
           href="{$new_block_link}">
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
                            <th class="op-filter-cell">
                                <div class="op-active-chooser-wrapper">
                                    <div class="op-active-chooser op-filter-chooser-js"
                                         rel="op-active-chooser-dropdown">
                                        <span>{$activeList[$selectedActive]}</span><span role="button"
                                                                                         class="fas fa-chevron-down"
                                                                                         aria-label="Filter active"></span>
                                    </div>
                                    <div id="op-active-chooser-dropdown" class="op-active-chooser-dropdown">
                                        {foreach from=$activeList item=item key=key}
                                            <div class="op-active-chooser-dropdown-item op-active-chooser-dropdown-item-js"
                                                 data-chooser="{$key}">{$item}</div>
                                        {/foreach}
                                    </div>
                                </div>
                            </th>
                            <th class="op-filter-cell">
                                <div class="op-active-chooser-wrapper">
                                    <div class="op-active-chooser op-filter-chooser-js" rel="op-hook-chooser-dropdown">
                                        <span>{$hookList[$selectedHook]}</span><span role="button"
                                                                                     class="fas fa-chevron-down"
                                                                                     aria-label="Filter hook"></span>
                                    </div>
                                    <div id="op-hook-chooser-dropdown"
                                         class="op-active-chooser-dropdown op-hook-chooser-dropdown">
                                        <div class="op-filter-hook-search-wrapper">
                                            <input id="op-start-typing" type="text"
                                                   placeholder="{l s='Begin typing hook name' mod='faqop'}">
                                        </div>
                                        {foreach from=$hookList item=item key=key}
                                            <div class="op-active-chooser-dropdown-item op-hook-chooser-dropdown-item-js"
                                                 data-chooser="{$key}">{$item}</div>
                                        {/foreach}
                                    </div>
                                </div>
                            </th>
                            <th class="op-filter-cell op-hide-on-mobile-view"></th>

                            <th class="op-filter-cell op-hide-on-smaller"></th>
                            <th class="op-filter-cell op-hide-on-smaller"></th>
                            <th class="op-filter-cell op-hide-on-smaller"></th>
                            <th class="op-filter-cell op-hide-on-smaller"></th>

                            <th class="op-filter-cell" colspan="2" style="text-align:right">
                                <a class="op-clear-filters"
                                   href="{$clean_url}"><i
                                            class="fas fa-eraser"></i> <span
                                            class="op-hide-on-390">{l s='Clear filters' mod='faqop'}</span></a>
                            </th>
                        </tr>
                        <tr class="nodrag nodrop">
                            <th style="width: 30px" class="op-hide-on-mobile-view">

                            </th>
                            <th class="op-sortable-column {if isset($classnames)}{if isset($classnames.title_th_class_name)}{$classnames.title_th_class_name}{/if}{/if}"
                                data-sort-op-name="orderTitle">{l s='Title' mod='faqop'}
                                <span role="button" class="fas fa-chevron-down"
                                      aria-label="Sort by"></span>
                            </th>

                            <th style="width: 80px">{l s='Status' mod='faqop'}</th>
                            <th class="op-sortable-column {if isset($classnames)}{if isset($classnames.hook_th_class_name)}{$classnames.hook_th_class_name}{/if}{/if}"
                                data-sort-op-name="orderHook">{l s='Hook' mod='faqop'}
                                <span role="button" class="fas fa-chevron-down"
                                      aria-label="Sort by"></span>
                            </th>
                            <th class="op-sortable-column op-hide-on-mobile-view {if isset($classnames)}{if isset($classnames.pos_th_class_name)}{$classnames.pos_th_class_name}{/if}{/if}"
                                data-sort-op-name="orderPos">{l s='Position' mod='faqop'}<span role="button"
                                                                                                  class="fas fa-chevron-down"
                                                                                                  aria-label="Sort by"></span>
                            </th>

                            <th class="op-hide-on-smaller">{l s='Markup' mod='faqop'}</th>
                            <th class="op-hide-on-smaller">{l s='Pages' mod='faqop'}</th>
                            <th class="op-hide-on-smaller">{l s='Languages' mod='faqop'}</th>
                            <th class="op-hide-on-smaller">{l s='Currencies' mod='faqop'}</th>

                            <th style="width: 180px"
                                class="op-hide-on-smaller">{l s='Shortcode' mod='faqop'}</th>
                            <th style="width: 121px">{l s='Action' mod='faqop'}</th>

                        </tr>
                        </thead>
                        {if $blocks}
                            <tbody {if $change_positions}id="opCells" class="ui-sortable"{/if}>
                            {foreach $blocks as $key => $block}
                                <tr class="{if $key%2}alt_row{else}not_alt_row{/if} row_hover"
                                    id="op_row-{$block.block_type}-{$block.id_item}">
                                    <td>
                                        <i class="grid-checkbox grid-checkbox-css far fa-square" data-row-bulk-id="{$block.id_item}"
                                           data-row-bulk-type="{$block.block_type}"></i>
                                    </td>
                                    <td>{$block.title|escape:'htmlall':'UTF-8'}</td>

                                    <td><a class="btn {$block.status_class}" href="{$block.status_href}"
                                           title="{$block.status_title}"><i class="{$block.status_icon}"></i>
                                        </a></td>
                                    <td>{$block.hook_name|escape:'htmlall':'UTF-8'}</td>
                                    <td class="op-td-position op-hide-on-mobile-view">{if $change_positions}
                                            <i class="icon-arrows"></i>
                                        {/if}{$block.position}</td>
                                    <td class="op-hide-on-smaller">
                                        {if $block.show_markup}
                                            Yes
                                        {else}
                                            No
                                        {/if}
                                    </td>
                                    <td class="op-hide-on-smaller">
                                        {if !$block.not_all_pages}
                                            All
                                        {else}
                                            Selected
                                        {/if}
                                    </td>
                                    <td class="op-hide-on-smaller">
                                        {if !$block.not_all_languages}
                                            All
                                        {elseif $block.iso_array}
                                            {foreach $block.iso_array as $iso}
                                                {$iso}
                                            {/foreach}
                                        {else}
                                            -

                                        {/if}
                                    </td>
                                    <td class="op-hide-on-smaller">
                                        {if !$block.not_all_currencies}
                                            All
                                        {elseif $block.iso_cur_array}
                                            {foreach $block.iso_cur_array as $iso}
                                                {$iso}
                                            {/foreach}
                                        {else}
                                            -

                                        {/if}
                                    </td>

                                    <td class="op-hide-on-smaller">
                                        <input type="text" readonly="readonly"
                                               class="shortcode-readonly"
                                               value="{literal}{{/literal}{$block.shortcode}{literal}}{/literal}"></td>

                                    <td>
                                        <div class="btn-group-action">
                                            <div class="btn-group pull-left">
                                                <a class="btn btn-default"
                                                   href="{$block.edit_href}"
                                                   title="{l s='Edit' mod='faqop'}">
                                                    <i class="icon-edit"></i> {l s='Edit' mod='faqop'}
                                                </a>
                                                <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                                    <i class="icon-caret-down"></i>&nbsp;
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a href="{$block.clone_href}">
                                                            <i class="far fa-clone"></i> {l s='Clone' mod='faqop'}
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="{$block.delete_href}">
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
                                        {l s='No blocks found' mod='faqop'}
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
            <div class="op-active-chooser-wrapper">
                <div id="bulk-chooser" class="op-active-chooser bulk-chooser">
                    <span>{l s='Bulk actions' mod='faqop'}</span><span role="button"
                                                                       class="fas fa-chevron-down"
                                                                       aria-label="Bulk actions"></span>
                </div>
                <div id="bulk-chooser-dropdown" class="op-active-chooser-dropdown {if $multistore_active}op-active-chooser-dropdown-wider{/if}">
                    <div id="bulk-button-block-publish" class="op-active-chooser-dropdown-item">
                        <i class="fas fa-check usual-process"></i>
                        <i class="fas fa-spinner fa-spin spin-process spin-process-inline" style="display:none"></i>
                        {l s='Publish' mod='faqop'}
                    </div>
                    <div id="bulk-button-block-unpublish" class="op-active-chooser-dropdown-item">
                        <i class="far fa-minus-square usual-process"></i>
                        <i class="fas fa-spinner fa-spin spin-process spin-process-inline" style="display:none"></i>
                        {l s='Unpublish' mod='faqop'}
                    </div>
                    <div id="bulk-button-block-delete" class="op-active-chooser-dropdown-item">
                        <i class="far fa-trash-alt usual-process"></i>
                        <i class="fas fa-spinner fa-spin spin-process spin-process-inline" style="display:none"></i>
                        {l s='Delete' mod='faqop'}
                    </div>
                    {if $multistore_active}
                        <div id="bulk-button-block-copy-shop" class="op-active-chooser-dropdown-item bulk-button-block-copy-shop">
                            <i class="fas fa-store"></i>
                            {l s='Copy to another shop' mod='faqop'}
                        </div>
                    {/if}
                </div>
            </div>
        </div>
        <a class="btn btn-default pull-right black-pink-default"
           href="{$new_block_link}">
            <i class="process-icon-new"></i>
            {l s='New list' mod='faqop'}
        </a>
    </div>
</div>

{if $multistore_active}
    {include file=$includeTpl shops_list=$shops_list old_shop=$old_shop page_id=null}
{/if}

