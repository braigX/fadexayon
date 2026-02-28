{if !$ajax}<div id="fieldset" class="panel {$obj|escape:'htmlall':'UTF-8'}">{/if}
        <div class="panel-heading">
                <i class="icon-table"></i> {l s='Desactive or deleted url list' mod='ec_seo'}
                <span class="panel-heading-action">
                    <a id="desc-category-new" class="addForm list-toolbar-btn" style="cursor: pointer;" rel="{$obj|escape:'htmlall':'UTF-8'}">
                        <span class="label-tooltip" data-placement="left" data-html="true" 
                        data-original-title="{l s='Add' mod='ec_seo'}" data-toggle="tooltip" title="">
                            <i class="process-icon-new"></i>
                        </span>
                    </a>
                    <a id="desc-product-import" class="addForm list-toolbar-btn" style="cursor: pointer;" rel="import">
                        <span class="label-tooltip" data-placement="left" data-html="true" 
                        data-original-title="{l s='Import' mod='ec_seo'}" data-toggle="tooltip" title="">
                            <i class="process-icon-import"></i>
                        </span>
                    </a>
                  {*   <a id="desc-product-filter" class="addForm list-toolbar-btn" style="cursor: pointer;text-align: center;" rel="filter">
                        <span class="label-tooltip" style="display: inline-block;" data-placement="left" data-html="true" 
                        data-original-title="{l s='Filter' mod='ec_seo'}" data-toggle="tooltip" title="">
                            <i class="icon-filter"></i>
                        </span>
                    </a> *}
                    <a id="desc-product-default" class="addForm list-toolbar-btn" style="cursor: pointer;" rel="default">
                        <span class="label-tooltip" data-placement="left" data-html="true" 
                        data-original-title="{l s='Default' mod='ec_seo'}" data-toggle="tooltip" title="">
                            <i class="process-icon-cogs"></i>
                        </span>
                    </a>
                </span>
        </div>
        <div class="table-responsive clearfix">
        <table class="table" border="0" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <th style="width:20px;"></th>
                    <th style="width:100px;">{l s='ID' mod='ec_seo'}</th>
                    <th style="width:100px;">{l s='Name' mod='ec_seo'}</th>
                    <th style="width:100px;">{l s='Satus' mod='ec_seo'}</th>
                    <th style="width:50px;">{l s='Language' mod='ec_seo'}</th>
                    <th style="width:300px;">{l s='Link' mod='ec_seo'}</th>
                    <th style="width:100px;">{l s='Redirection type' mod='ec_seo'}</th>
                    <th style="width:100px;">{l s='Redirection active' mod='ec_seo'}</th>
                    <th style="width:100px;"></th>
                </tr>
            </thead>
            <tbody>
            <tr id="search{$obj|escape:'htmlall':'UTF-8'}" data-type="{$obj|escape:'htmlall':'UTF-8'}">
                <td></td>
                <td><input value="{if isset($searchs['idP'])}{$searchs['idP']|escape:'htmlall':'UTF-8'}{/if}" class="conf" name="idP" type="text"/></td>
                <td><input value="{if isset($searchs['nameS'])}{$searchs['nameS']|escape:'htmlall':'UTF-8'}{/if}" class="conf" name="nameS" type="text"/></td>
                <td></td>
                <td>
                    <select name="idL" class="conf">
                        <option value="">{l s='All' mod='ec_seo'}</option>
                        {foreach from=$languages item=lang}
                            <option {if isset($searchs['idL'])}{if $searchs['idL'] == $lang['id_lang']}selected{/if}{/if} value="{$lang['id_lang']|escape:'htmlall':'UTF-8'}">{$lang['name']|escape:'htmlall':'UTF-8'}</option>
                        {/foreach}
                    </select>
                </td>
                <td><input value="{if isset($searchs['lienS'])}{$searchs['lienS']|escape:'htmlall':'UTF-8'}{/if}" name="lienS" type="text" class="conf"/></td>
                <td>
                    <select name="typeRed" class="conf">
                        <option value="">{l s='All' mod='ec_seo'}</option>
                        <option {if isset($searchs['typeRed'])}{if $searchs['typeRed'] == "404"}selected{/if}{/if} value="404">404 Not Found</option>
                        <option {if isset($searchs['typeRed'])}{if $searchs['typeRed'] == "301"}selected{/if}{/if} value="301">301 Moved Permanently</option>
                        <option {if isset($searchs['typeRed'])}{if $searchs['typeRed'] == "302"}selected{/if}{/if} value="302">302 Moved Temporarily</option>
                        {if $obj == 'Product'} 
                            <option {if isset($searchs['typeRed'])}{if $searchs['typeRed'] == "categorydefault"}selected{/if}{/if} value="categorydefault">{l s='Default category' mod='ec_seo'}</option>
                        {/if}
                        <option {if isset($searchs['typeRed'])}{if $searchs['typeRed'] == "homepage"}selected{/if}{/if} value="homepage">{l s='Home page' mod='ec_seo'}</option>
                    </select>
                </td>
                <td></td>
                <td>
                    {if $rsearch}
                        <button name="submitResetproduct" class="btn btn-warning pull-right" onclick="Ec_resetsearch('{$obj|escape:'htmlall':'UTF-8'}')">
    						<i class="icon-eraser"></i> {l s='Reset' mod='ec_seo'}
    					</button>
                    {/if}
                    <button id="submitFilterButtonproduct" name="submitFilter" onclick="Ec_search('{$obj|escape:'htmlall':'UTF-8'}')" class="btn btn-default pull-right" data-list-id="product">
									<i class="icon-search"></i> {l s='Search' mod='ec_seo'}
                    </button>
                    
                </td>
            </tr>
{if $countads > 0}
            
            {foreach from=$infosmarty item=ad}
            <tr id="ic_{$ad.0.id|escape:'htmlall':'UTF-8'}" >
                            <td id="{$obj|escape:'htmlall':'UTF-8'}" 
                            {if $ad.0.activRed == 0}style="background-color:grey"
                            {elseif ($ad.0.typeRed == 'homepage' || $ad.0.typeRed == 'categorydefault') && $ad.0.onlineS == 1}style="background-color:white"
                            {elseif ($ad.0.lienS == '' && ($ad.0.typeRed == '404' || $ad.0.typeRed == '301' || $ad.0.typeRed == '302') && $ad.0.onlineS == 1 )}style="background-color:#FF4E40"
                            {elseif $ad.0.lienS == '' && $ad.0.onlineS == 0}style="background-color:#FFDC40"
                            {elseif $ad.0.lienS != '' && $ad.0.onlineS == 0}style="background-color:#38E05D"
                            {else}style="background-color:white{/if}>
                            <input type="checkbox" class="checkOn_{$obj|escape:'htmlall':'UTF-8'}" rel="{$ad.0.id|escape:'htmlall':'UTF-8'}"/></td>
                            <td>{$ad.0.idP|escape:'htmlall':'UTF-8'}</td>
                            <td>{$ad.0.nameS|escape:'htmlall':'UTF-8'}</td>
                            <td>{if $ad.1 == ''}<a class="list-action-enable action-disabled" title="Supprimé"><i class="icon-trash"></i>
                            {elseif $ad.1 == 0}<a class="list-action-enable action-disabled" title="Désactivé"><i class="icon-remove"></i>
                            {elseif $ad.1 == 1}<a class="list-action-enable action-enabled" title="Activé"><i class="icon-check"></i>
                            {/if}
                            </td>
                            <td><img class="imgm lan img-thumbnail" src="{$__PS_BASE_URI__|escape:'htmlall':'UTF-8'}img/l/{$ad.0.idL|escape:'htmlall':'UTF-8'}.jpg" rel="{$ad.0.idL|escape:'htmlall':'UTF-8'}"  class="lan" /></td> 
                            <td class="lienred"><div id="displayLien_{$ad.0.id|escape:'htmlall':'UTF-8'}" style="display:true">{$ad.0.lienS|escape:'htmlall':'UTF-8'}</div>
                                                <div id="editionLien_{$ad.0.id|escape:'htmlall':'UTF-8'}" style="display:none">
                                                <input type="text" style="width:300px;" name="lienRedirect" id="lienRedirect" value="{$ad.0.lienS|escape:'htmlall':'UTF-8'}" />
                                                <input type="hidden" name="idredi" id="idredi" value="" />
                                                    <input type="submit" class="btn btn-default majLien" 
                                                    name="majLien" value="{l s='Update link' mod='ec_seo'}" rel="{$ad.0.id|escape:'htmlall':'UTF-8'}" /> </div> </td>
                            <td class="type">
                            <select class="typeRedirect" name="{$ad.0.id|escape:'htmlall':'UTF-8'}">
                              <option {if $ad.0.typeRed == '404'} selected="selected"{/if} value="404">404 Not Found</option>
                              <option {if $ad.0.typeRed == '301'} selected="selected"{/if} value="301">301 Moved Permanently</option>
                              <option {if $ad.0.typeRed == '302'} selected="selected"{/if} value="302">302 Moved Temporarily</option>
                            {if $obj == 'Product' && isset($ad.1) && $ad.1 != ''} 
                            <option {if $ad.0.typeRed == 'categorydefault'} selected="selected"{/if}
                            value="categorydefault">{l s='Default category' mod='ec_seo'}</option>
                            {/if}
                            {*<option {if $ad.0.typeRed == 'manufacturer'} selected="selected"{/if} value="manufacturer">{l s='Manufacturer' mod='ec_seo'}</option>*}
                            <option {if $ad.0.typeRed == 'homepage'} selected="selected"{/if} value="homepage">{l s='Home page' mod='ec_seo'}</option>
                            </select>
                            </td>
                            <td><a rel="{$ad.0.onlineS|escape:'htmlall':'UTF-8'}" class="list-action-enable act action-{if $ad.0.onlineS == 0}disabled" title="Désactivé">
                            <i class="icon-remove"></i>{else}enabled" title="Activé"><i class="icon-check"></i>{/if}</td>
                            <td>
                                <div class="btn-group-action">
                                    <div class="btn-group pull-right">
                                        <a class="edit btn btn-default editRedirect" rel="{$ad.0.id|escape:'htmlall':'UTF-8'}" title="Modifier">
                                            <i class="icon-pencil"></i>
                                            {l s='Modify' mod='ec_seo'}
                                        </a>
                                        <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                            <i class="icon-caret-down"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="activeRedirect" rel="{$ad.0.id|escape:'htmlall':'UTF-8'}" 
                                                title="{if $ad.0.onlineS == 0}
                                                Active"><i class="icon-check"></i>{l s='Active the redirection' mod='ec_seo'}{else}Desactive">
                                                    <i class="icon-remove"></i>
                                                    {l s='Desactive the selection' mod='ec_seo'}{/if}
                                                </a>
                                            </li>
                                            <li class="divider"> </li>
                                            <li>
                                                <a class="suppRedirect" rel="{$ad.0.id|escape:'htmlall':'UTF-8'}" title={if $ad.0.activRed != 0}
                                                "Supprimer"><i class="icon-trash"></i>{l s='Delete' mod='ec_seo'}{else}"Restaurer"><i class="icon-trash"></i>{l s='Restore' mod='ec_seo'}{/if}
                                                </a>
                                            </li>
                                            <li class="divider"> </li>
                                            <li>
                                                <a class="search" rel="{$ad.0.idL|escape:'htmlall':'UTF-8'}" title="Afficher" href="{$ad.2|escape:'htmlall':'UTF-8'}" target="blank">
                                                    <i class="icon-search-plus"></i>
                                                    {l s='Display' mod='ec_seo'}
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </td>
                            </td>
                    {/foreach}
                    {/if}
                            </tbody>
                    </table></div>
                    {if isset($ad.0.id)}
                        </br><div class="btn-group-action col-lg-6">
                                    <div class="btn-group pull-left">
                                        <button class="btn btn-default dropdown-toggle" data-toggle="dropdown" type="button">
                                            {l s='Bulk actions' mod='ec_seo'}
                                            <i class="icon-caret-down"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="allChecked" rel="{$obj|escape:'htmlall':'UTF-8'}" name="allChecked">
                                                    <i class="icon-check-sign"></i>
                                                     {l s='Select all' mod='ec_seo'}
                                                </a>
                                            </li>
                                            <li>
                                                <a class="allUnchecked" rel="{$obj|escape:'htmlall':'UTF-8'}" name="allUnchecked">
                                                    <i class="icon-check-empty"></i>
                                                    {l s='Unselect all' mod='ec_seo'}
                                                </a>
                                            </li>
                                            <li class="divider"> </li>
                                            <li>
                                                <a class="activeAllRedirect" rel="{$obj|escape:'htmlall':'UTF-8'}" title="Active">
                                                    <i class="icon-check"></i>
                                                    {l s='Active the selection' mod='ec_seo'}
                                                </a>
                                            </li>
                                            <li>
                                                <a class="desactiveAllRedirect" rel="{$obj|escape:'htmlall':'UTF-8'}" title="Desactive">
                                                    <i class="icon-remove"></i>
                                                    {l s='Desactive the selection' mod='ec_seo'}
                                                </a>
                                            </li>
                                            <li class="divider"> </li>
                                            <li>
                                                <a class="editAllRedirect" rel="{$ad.0.id|escape:'htmlall':'UTF-8'}" title="ModifierLien">
                                                <i class="icon-pencil"></i>
                                                    {l s='Modify links' mod='ec_seo'}
                                                </a>
                                            </li>
                                            <li>
                                                <a class="editAllType" rel="{$ad.0.id|escape:'htmlall':'UTF-8'}" title="ModifierType">
                                                <i class="icon-pencil"></i>
                                                    {l s='Modify types' mod='ec_seo'}
                                                </a>
                                            </li>
                                            <li class="divider"> </li>
                                            <li>
                                                <a class="suppAllRedirect" rel="{$obj|escape:'htmlall':'UTF-8'}" title="Supprimer">
                                                    <i class="icon-trash"></i>
                                                    {l s='Delete selection' mod='ec_seo'}
                                                </a>
                                            </li>
                                            <li>
                                                <a class="restAllRedirect" rel="{$obj|escape:'htmlall':'UTF-8'}" title="Restaurer">
                                                    <i class="icon-trash"></i>
                                                    {l s='Restore selection' mod='ec_seo'}
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-lg-6">
		
		<div class="pagination">
			Affichage
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
   
            <li {if $pageActif == 1}class="disabled"{/if}>
                <a href="javascript:void(0);" class="pagination-link" onclick="Ec_seo_pagination(1,'{$obj|escape:'htmlall':'UTF-8'}')" data-list-id="product">
                    <i class="icon-double-angle-left"></i>
                </a>
            </li>
			<li {if $pageActif-1 == 0}class="disabled"{/if}>
				<a href="#" class="pagination-link" data-page="0" data-list-id="product" {if $pageActif-1 != 0}onclick="Ec_seo_pagination({$pageActif-1|escape:'htmlall':'UTF-8'},'{$obj|escape:'htmlall':'UTF-8'}')"{/if}>
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
                        <a href="" class="pagination-link"  data-list-id="product" onclick="Ec_seo_pagination({$page|escape:'htmlall':'UTF-8'},'{$obj|escape:'htmlall':'UTF-8'}')">{$page|escape:'htmlall':'UTF-8'}</a>
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
				<a href="javascript:void(0);" class="pagination-link" data-page="{$pageActif+1|escape:'htmlall':'UTF-8'}" {if $pageActif+1 <= $nbpage}onclick="Ec_seo_pagination({$pageActif+1|escape:'htmlall':'UTF-8'},'{$obj|escape:'htmlall':'UTF-8'}')"{/if} data-list-id="product">
					<i class="icon-angle-right"></i>
				</a>
			</li>
            <li {if $pageActif == $nbpage || $countInfo == 0}class="disabled"{/if}>
                <a href="javascript:void(0);" class="pagination-link" onclick="Ec_seo_pagination({$nbpage|escape:'htmlall':'UTF-8'}, '{$obj|escape:'htmlall':'UTF-8'}')" data-list-id="product">
                    <i class="icon-double-angle-right"></i>
                </a>
            </li>
		</ul>
		<script type="text/javascript">
			$('.pagination-link').on('click',function(e){
				e.preventDefault();

				if (!$(this).parent().hasClass('disabled'))
					$('#submitFilter'+$(this).data("list-id")).val($(this).data("page")).closest("form").submit();
			});
		</script>
	</div>
                                </br></br>
                                <div class="displaymajAll_{$obj|escape:'htmlall':'UTF-8'}" style="display:none">
                                    <div class="form-wrapper">
                                        <div class="form-group">
                                            <label class="control-label col-lg-3">{l s='Redirect link' mod='ec_seo'}</label>
                                                <div class="col-lg-9 ">
                                                    <input type="text"  style="width:300px;" name="lienAllRedirect" id="lienAllRedirect_{$obj|escape:'htmlall':'UTF-8'}" value="" /></br>
                                                    <input type="submit" class="btn btn-default majAllLien" rel = "{$obj|escape:'htmlall':'UTF-8'}" name="majAllLien" value="{l s='Update links' mod='ec_seo'}"/>
                                                </div>
                                        </div>
                                    </div></br></br></br></br>
                                </div>
                                <div class="displaymajType_{$obj|escape:'htmlall':'UTF-8'}" style="display:none">
                                    <div class="form-wrapper">
                                        <div class="form-group">
                                            <label class="control-label col-lg-3">{l s='Type of redirection' mod='ec_seo'}</label>
                                                <div class="col-lg-9 ">
                                                    <select class="typeRedirectAll" style="width:300px;" name="{$ad.0.id|escape:'htmlall':'UTF-8'}">
                                                        <option selected="selected" value="404">404 Not Found</option>
                                                        <option value="301">301 Moved Permanently</option>
                                                        <option value="302">302 Moved Temporarily</option>
                                                    {if $obj == 'Product'}
                                                        <option value="categorydefault">{l s='Default category' mod='ec_seo'}</option>
                                                    {/if}
                                                        <option value="homepage">{l s='Home page' mod='ec_seo'}</option>
                                                    </select>
                                                    
                                                    </br>
                                                    <input type="submit" class="btn btn-default majAllType" rel = "{$obj|escape:'htmlall':'UTF-8'}" name="majAllType" value="{l s='Update types' mod='ec_seo'}"/>
                                                </div>
                                        </div>
                                    </div></br></br></br></br>
                                </div>
            {/if}
            {if !$ajax}</div>{/if}         
            
            