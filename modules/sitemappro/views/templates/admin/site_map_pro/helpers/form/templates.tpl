{*
* 2007-2018 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
* @author    SeoSA    <885588@bk.ru>
* @copyright 2012-2022 SeoSA
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
* International Registered Trademark & Property of PrestaShop SA
*}

{literal}
<script type="text/html" id="tpl_object_item">
	<tr <% if(type_object == 'product') { %>data-id-product="<%= id_object %>" data-categories="<%= categories%>" <%}%> class="object_item">
	<td class="object_item_name">
		<input type="hidden" name="sitemap[<%= type_object + id_object %>][type_object]" value="<%= type_object%>"/>
		<input type="hidden" name="sitemap[<%= type_object + id_object %>][id_object]" value="<%= id_object %>"/>
		<%= name %>
	</td>
	<td class="object_item_priority">
		<select class="fixed-width-sm" name="sitemap[<%= type_object + id_object %>][priority]">
			<% _.each(priorities, function(priority) { %>
			<option <% if (typeof priority_object != 'undefined' && priority_object == priority.id) { %>selected<% } %> value="<%= priority.id %>"><%= priority.id %></option>
			<% }); %>
		</select>
	</td>
	<td class="object_item_changefreq">
		<select class="fixed-width-md" name="sitemap[<%= type_object + id_object %>][changefreq]">
			<% _.each(changefreqs, function(changefreq) { %>
			<option <% if (typeof changefreq_object != 'undefined' && changefreq_object == changefreq.id) { %>selected<% } %> value="<%= changefreq.id %>"><%= changefreq.name %></option>
			<% }); %>
		</select>
	</td>

	<td class="object_item_action">
		<% if(type_object == 'category' || type_object == 'cms' || type_object == 'meta' || type_object == 'manufacturer' || type_object == 'supplier') { %>
		<div class="wrapp_checkbox">
			<input type="checkbox" value="1" <% if (is_export) { %>checked<% } %> name="sitemap[<%= type_object + id_object %>][is_export]"/>
			<span class="icon_checkbox"></span>
		</div>
		<%}%>
	</td>

	<td class="object_item_action">
		<% if(type_object == 'category' || type_object == 'product' || type_object == 'manufacturer' || type_object == 'supplier') { %>
		<a class="btn btn-danger" onclick="$(this).closest('tr').remove(); needSave(); hideTable(); return false;" href="#">
			<i class="icon-remove"></i>
            {/literal}<span class="hidden-xs-down">{l s='Delete' mod='sitemappro'}</span>{literal}
		</a>
		<%}%>
	</td>

	</tr>
</script>

<script type="text/html" id="tpl_object_item_user_link">
	<tr class="object_item">
		<td class="object_item_link">
			<input type="hidden" name="user_links[<%= index %>][id_user_link]" value="<%= user_link.id %>"/>
			<div class="clearfix">
				<% _.each(languages, function(lang) { %>
				<div class="translatable-field lang-<%=lang.id_lang%>">
					<div class="row">

						<div class="shop_domain col-md-12 col-lg-6">
							<div class="shop_domain_wrap">
								<label class="control-label">
                                    {/literal}{$shop_domain|escape:'quotes':'UTF-8'}{literal}
								</label>
							</div>
						</div>

						<div class="col-md-9 col-lg-4">
              <input type="text" name="user_links[<%= index %>][link][<%=lang.id_lang%>]"
                     value="<% if (user_link.link) { %><%=user_link.link[lang.id_lang]%><% } %>">
						</div>

						<% if (languages.length > 1) { %>
						<div class="translatable-field col-md-3 col-lg-2 lang-<%=lang.id_lang%>">
							<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" tabindex="-1">
								<%=lang.iso_code%>
								<span class="caret"></span>
							</button>
							<ul class="dropdown-menu">
								<% _.each(languages, function(l) { %>
								<li>
									<a onclick="$.changeLanguage(<%=l.id_lang %>);"><%=l.name%></a>
								</li>
								<% }); %>
							</ul>
						</div>
						<% } %>

					</div>
				</div>
				<% }); %>
			</div>
		</td>
		<td class="object_item_priority">
			<select class="fixed-width-sm" name="user_links[<%= index %>][priority]">
				<% _.each(priorities, function(priority) { %>
				<option <% if (user_link.priority == priority.id) { %>selected<% } %> value="<%= priority.id %>"><%= priority.id %></option>
				<% }); %>
			</select>
		</td>
		<td class="object_item_changefreq">
			<select class="fixed-width-md" name="user_links[<%= index %>][changefreq]">
				<% _.each(changefreqs, function(changefreq) { %>
				<option <% if (user_link.changefreq == changefreq.id) { %>selected<% } %> value="<%= changefreq.id %>"><%= changefreq.name %></option>
				<% }); %>
			</select>
		</td>
		<td class="object_item_action">
			<a class="btn btn-danger fixed-width-xs deleteUserLink" href="#">
				<i class="icon-remove"></i>
			</a>
			<input type="hidden" name="user_links[<%= index %>][deleted]" value="0">
		</td>
	</tr>
</script>
{/literal}