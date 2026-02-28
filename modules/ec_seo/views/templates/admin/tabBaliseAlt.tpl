 {if !$refresh}<div id="table_balisealt" class="panel" >{/if}
    <div class="panel-heading">
        {l s='Last task modification' mod='ec_seo'}
        <span class="badge">{$countInfo|escape:'htmlall':'UTF-8'}</span>
        <span class="panel-heading-action">
            <a id="refreshTabBaliseAlt" class="list-toolbar-btn" href="#">
                <span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="Rafraîchir la liste" data-html="true" data-placement="top">
                    <i class="process-icon-refresh"></i>
                </span>
            </a>
		</span>
    </div>
    <table class="table"  width="100%">
            <thead>
                <tr>
                    <th>{l s='ID Product' mod='ec_seo'} </th>
                    <th>ID Image</th>
                    <th>{l s='Language' mod='ec_seo'}</th>
                    <th>{l s='Name' mod='ec_seo'}</th>
                    <th>{l s='Image Alt Tag (image legend)' mod='ec_seo'}</th>
                    
                    {* <th>{l s='Replacement number' mod='ec_seo'}</th> *}
                </tr>
            </thead>
            <tbody>
    <tr id="search_mi">
        <td><input value="{if isset($searchs['id_product'])}{$searchs['id_product']|escape:'htmlall':'UTF-8'}{/if}" class="conf" name="id_product" type="text" style="width:50px"/></td>
        <td><input value="{if isset($searchs['id_image'])}{$searchs['id_image']|escape:'htmlall':'UTF-8'}{/if}" class="conf" name="id_image" type="text" style="width:50px"/></td>
        <td></td>
        <td><input value="{if isset($searchs['name'])}{$searchs['name']|escape:'htmlall':'UTF-8'}{/if}" class="conf" name="name" type="text" style="width:100px"/></td>
        <td><input value="{if isset($searchs['legend'])}{$searchs['legend']|escape:'htmlall':'UTF-8'}{/if}" class="conf" name="legend" type="text" style="width:100px"/></td>
    </tr>
    {foreach from=$lines item=line}
        <tr>
            <td>{$line.id_product|escape:'htmlall':'UTF-8'}</td>
            <td>{$line.id_image|escape:'htmlall':'UTF-8'}</td>
            <td>{$line.lang|escape:'htmlall':'UTF-8'}</td>
            <td><a href="{$line.link|escape:'htmlall':'UTF-8'}" target="_blank">{$line.name|escape:'htmlall':'UTF-8'}</a></td>
            <td>{$line.legend|escape:'htmlall':'UTF-8'}</td>
            {* <td>{$line.nb_replace}</td> *}
        </tr>
    {/foreach}
    </tbody>
    </table>

    <div class="bottom-mi">
        <div class="pagination">
            {l s='Display' mod='ec_seo'}
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                {$pagination|escape:'htmlall':'UTF-8'}
                <i class="icon-caret-down"></i>
            </button>
            <ul class="dropdown-menu">
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
                <a href="javascript:void(0);" class="pagination-link" onclick="Ec_rep_pagination_ba(1)" data-list-id="product">
                    <i class="icon-double-angle-left"></i>
                </a>
            </li>
            <li {if $pageActif-1 == 0}class="disabled"{/if}>
                <a href="#" class="pagination-link" data-page="0" data-list-id="product" {if $pageActif-1 != 0}onclick="Ec_rep_pagination_ba({$pageActif-1|escape:'htmlall':'UTF-8'})"{/if}>
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
                        <a href="" class="pagination-link"  data-list-id="product" onclick="Ec_rep_pagination_ba({$page|escape:'htmlall':'UTF-8'})">{$page|escape:'htmlall':'UTF-8'}</a>
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
                <a href="javascript:void(0);" class="pagination-link" data-page="{$pageActif+1|escape:'htmlall':'UTF-8'}" {if $pageActif+1 <= $nbpage}onclick="Ec_rep_pagination_ba({$pageActif+1|escape:'htmlall':'UTF-8'})"{/if} data-list-id="product">
                    <i class="icon-angle-right"></i>
                </a>
            </li>
            <li {if $pageActif == $nbpage || $countInfo == 0}class="disabled"{/if}>
                <a href="javascript:void(0);" class="pagination-link" onclick="Ec_rep_pagination_ba({$nbpage|escape:'htmlall':'UTF-8'})" data-list-id="product">
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