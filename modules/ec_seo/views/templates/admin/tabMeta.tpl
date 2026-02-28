 {if !$refresh}<div id="table_meta_{$type|escape:'htmlall':'UTF-8'}" class="panel table_meta_info" data-type="{$type|escape:'htmlall':'UTF-8'}">{/if}
    <div class="panel-heading">
        {l s='Last task modification' mod='ec_seo'}
        <span class="badge">{$countInfo|escape:'htmlall':'UTF-8'}</span>
        <span class="panel-heading-action">
            <a class="list-toolbar-btn" href="#" onclick="Ec_search_gcplus('{$type|escape:'htmlall':'UTF-8'}', 'meta')">
                <span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="Rafraîchir la liste" data-html="true" data-placement="top">
                    <i class="process-icon-refresh"></i>
                </span>
            </a>
		</span>
    </div>
    <table class="table"  width="100%">
            <thead>
                <tr>
                    <th>ID </th>
                    <th>{l s='Language' mod='ec_seo'}</th>
                    <th>{l s='Name' mod='ec_seo'}</th>
                    <th>{l s='Meta title' mod='ec_seo'}</th>
                    <th>{l s='Meta description' mod='ec_seo'}</th>
                </tr>
            </thead>
            <tbody>
    <tr id="search_meta">
        <td><input value="{if isset($searchs['id'])}{$searchs['id']|escape:'htmlall':'UTF-8'}{/if}" class="conf" name="id" type="text" style="width:50px"/></td>
        <td><input value="{if isset($searchs['name'])}{$searchs['name']|escape:'htmlall':'UTF-8'}{/if}" class="conf" name="name" type="text" style="width:100px"/></td>
        <td></td>
        <td><input value="{if isset($searchs['meta_title'])}{$searchs['meta_title']|escape:'htmlall':'UTF-8'}{/if}" class="conf" name="meta_title" type="text" style="width:100px"/></td>
        <td><input value="{if isset($searchs['meta_description'])}{$searchs['meta_description']|escape:'htmlall':'UTF-8'}{/if}" class="conf" name="meta_description" type="text" style="width:100px"/></td>
    </tr>
    {foreach from=$lines item=line}
        <tr>
            <td>{$line.id|escape:'htmlall':'UTF-8'}</td>
            <td>{$line.lang|escape:'htmlall':'UTF-8'}</td>
            <td><a href="{$line.link|escape:'htmlall':'UTF-8'}" target="_blank">{$line.name|escape:'htmlall':'UTF-8'}</a></td>
            <td>{$line.meta_title|escape:'htmlall':'UTF-8'}</td>
            <td>{$line.meta_description|escape:'htmlall':'UTF-8'}</td>
        </tr>
    {/foreach}
    </tbody>
    </table>

    <div class="bottom-meta">
        <div class="pagination">
            {l s='Display' mod='ec_seo'}
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                {$pagination|escape:'htmlall':'UTF-8'}
                <i class="icon-caret-down"></i>
            </button>
            <ul class="dropdown-menu" data-type="{$type|escape:'htmlall':'UTF-8'}">
                <li>
                    <a href="#" class="pagination-items-page" data-items="2" data-list-id="product">2</a>
                </li>
                <li>
                    <a href="#" class="pagination-items-page" data-items="5" data-list-id="product">5</a>
                </li>
                <li>
                    <a href="#" class="pagination-items-page" data-items="10" data-list-id="product">10</a>
                </li>
                            <li>
                    <a href="#" class="pagination-items-page" data-items="20" data-list-id="product">20</a>
                </li>
                            <li>
                    <a href="javascript:void(0);" class="pagination-items-page" data-items="50" data-list-id="product">50</a>
                </li>
                            <li>
                    <a href="javascript:void(0);" class="pagination-items-page" data-items="100" data-list-id="product">100</a>
                </li>
                            <li>
                    <a href="javascript:void(0);" class="pagination-items-page" data-items="300" data-list-id="product">300</a>
                </li>
                            <li>
                    <a href="javascript:void(0);" class="pagination-items-page" data-items="1000" data-list-id="product">1000</a>
                </li>
                        </ul>
            / {$countInfo|escape:'htmlall':'UTF-8'} résultat(s)
            <input id="product-pagination-items-page" name="product_pagination" value="{$pagination|escape:'htmlall':'UTF-8'}" type="hidden">
        </div>
        <script type="text/javascript">
            $('.pagination-items-page').on('click',function(e){
                e.preventDefault();
                $('#'+$(this).data("list-id")+'-pagination-items-page').val($(this).data("items")).closest("form").submit();
            });
        </script>
        <ul class="pagination pull-right">
        {*
            <li class="disabled">
                <a href="javascript:void(0);" class="pagination-link" data-page="1" data-list-id="product">
                    <i class="icon-double-angle-left"></i>
                </a>
            </li>
    *}
            <li {if $pageActif == 1}class="disabled"{/if}>
                <a href="javascript:void(0);" class="pagination-link" onclick="Ec_rep_pagination(1, '{$type|escape:'htmlall':'UTF-8'}', 'meta')" data-list-id="product">
                    <i class="icon-double-angle-left"></i>
                </a>
            </li>
            <li {if $pageActif-1 == 0}class="disabled"{/if}>
                <a href="#" class="pagination-link" data-page="0" data-list-id="product" {if $pageActif-1 != 0}onclick="Ec_rep_pagination({$pageActif-1|escape:'htmlall':'UTF-8'}, '{$type|escape:'htmlall':'UTF-8'}', 'meta')"{/if}>
                    <i class="icon-angle-left"></i>
                </a>
            </li>
        

                {$cpt=0}
                {if $pageActif > 3}
                    <li class="disabled">
                            <a href="" class="pagination-link" data-page="..." data-list-id="product">...</a>
                    </li>
                {/if}
                {for $page=1 to $nbpage}
                    {if ($page < $pageActif && ($page+2) >= $pageActif) || ($page > $pageActif && ($page-2) <= $pageActif) || $page == $pageActif}
                    <li {if $pageActif == $page}class="active"{/if}>
                        <a href="" class="pagination-link"  data-list-id="product" onclick="Ec_rep_pagination({$page|escape:'htmlall':'UTF-8'}, '{$type|escape:'htmlall':'UTF-8'}', 'meta')">{$page|escape:'htmlall':'UTF-8'}</a>
                    </li>
                    {else if $cpt == 3}
                        <li class="disabled">
                            <a href="" class="pagination-link" data-page="..." data-list-id="product">...</a>
                        </li>
                    {/if}
                    {if $pageActif < $page}
                    {$cpt = $cpt+1}
                    {/if}
                {/for}
            <li {if $pageActif+1 > $nbpage}class="disabled"{/if}>
                <a href="javascript:void(0);" class="pagination-link" data-page="{$pageActif+1|escape:'htmlall':'UTF-8'}" {if $pageActif+1 <= $nbpage}onclick="Ec_rep_pagination({$pageActif+1|escape:'htmlall':'UTF-8'}, '{$type|escape:'htmlall':'UTF-8'}','meta')"{/if} data-list-id="product">
                    <i class="icon-angle-right"></i>
                </a>
            </li>
            <li {if $pageActif == $nbpage || $countInfo == 0}class="disabled"{/if}>
                <a href="javascript:void(0);" class="pagination-link" onclick="Ec_rep_pagination({$nbpage|escape:'htmlall':'UTF-8'}, '{$type|escape:'htmlall':'UTF-8'}', 'meta')" data-list-id="product">
                    <i class="icon-double-angle-right"></i>
                </a>
            </li>
            {*
            <li>
                <a href="javascript:void(0);" class="pagination-link" data-page="12" data-list-id="product">
                    <i class="icon-double-angle-right"></i>
                </a>
            </li>
            *}
        </ul>
        <script type="text/javascript">
            $('.pagination-link').on('click',function(e){
                e.preventDefault();

                if (!$(this).parent().hasClass('disabled'))
                    $('#submitFilter'+$(this).data("list-id")).val($(this).data("page")).closest("form").submit();
            });
        
        </script>
</div>
{if !$refresh}
    </div>
{/if}