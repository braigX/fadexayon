{$showShopContextWarning|sil_nofilter}

{sil_startForm id="formAddGroup" obj=$obj}

	{if $updateForm}
		{module->_displayTitle text="{l s='Update Group' mod='pm_seointernallinking'}"}
	{else}
		{module->_displayTitle text="{l s='Add a new Group' mod='pm_seointernallinking'}"}
	{/if}

	{if $updateForm}
		<input type="hidden" name="id_group" id="id_group" value="{$obj->id_group|intval}" />
	{/if}

	{sil_inputTextLang obj=$obj key='name' label={l s='Group name' mod='pm_seointernallinking'} required=true size='250px'}

	{* Type de groupe *}
	{sil_select obj=$obj key='group_type' options=$groupsType label={l s='Type of Group' mod='pm_seointernallinking'} defaultvalue=false size='250px'}

	<p id="groupInformations"></p>

	{* Begin - #groupProductZone *}
	<div id="groupProductZone">
		<div id="wrapProductsConfigTab">
        	<ul style="height: 30px;" id="configTab">
                <li><a href="#config-products-1">{l s='Categories' mod='pm_seointernallinking'}</a></li>
                <li><a href="#config-products-2">{l s='Products' mod='pm_seointernallinking'}</a></li>
                <li><a href="#config-products-3">{l s='Manufacturers' mod='pm_seointernallinking'}</a></li>
                <li><a href="#config-products-4">{l s='Suppliers' mod='pm_seointernallinking'}</a></li>
            </ul>

			<div id="config-products-1">
				<div id="categoriesWarning" style="display:none">
					{if $pm_htmloncategories_warning}
						{module->_showWarning text="{l s='This feature require the HTML on product & cms categories and manufacturers module.' mod='pm_seointernallinking'}<br />{l s='You can buy it here :' mod='pm_seointernallinking'}<br /><a href='http://www.presta-module.com/product.php?id_product=81' target='_blank'>{l s='HTML on product & cms categories and manufacturers' mod='pm_seointernallinking'}</a>"}
					{/if}
				</div>

				{* Choix des catégories *}
				{sil_categoryTree label={l s='Category' mod='pm_seointernallinking'} input_name='categories' selected_cat=$groupCategories category_root_id=$root_category_id}

				{* Options pour le groupe (inclusion/exclusion) *}
				{sil_inputActive obj=$obj key_active='category_type' key_db='category_type' label={l s='Categories above have to be excluded' mod='pm_seointernallinking'}}
			</div>

			<div id="config-products-2">
				{* Choix des produits *}
				<div class="product_picker">
					{sil_ajaxSelectMultiple selectedoptions=$selectedoptions.products key='products' label={l s='Products' mod='pm_seointernallinking'} remoteurl="{$base_config_url|sil_nofilter}&getItem=1&itemType=product" idcolumn='id_product' namecolumn='name'}
				</div>
				{* Options pour le groupe (inclusion/exclusion) *}
				{sil_inputActive obj=$obj key_active='product_type' key_db='product_type' label={l s='Products above have to be excluded' mod='pm_seointernallinking'}}
			</div>

			<div id="config-products-3">
				{* Choix des fabricants *}
				<div id="manufacturersWarning" style="display:none">
					{if $pm_htmloncategories_warning}
						{module->_showWarning text="{l s='This feature require the HTML on product & cms categories and manufacturers module.' mod='pm_seointernallinking'}<br />{l s='You can buy it here :' mod='pm_seointernallinking'}<br /><a href='http://www.presta-module.com/product.php?id_product=81' target='_blank'>{l s='HTML on product & cms categories and manufacturers' mod='pm_seointernallinking'}</a>"}
					{/if}
				</div>

				<div class="manufacturer_picker">
					{sil_ajaxSelectMultiple selectedoptions=$selectedoptions.manufacturers key='manufacturers' label={l s='Manufacturers' mod='pm_seointernallinking'} remoteurl="{$base_config_url|sil_nofilter}&getItem=1&itemType=manufacturer" idcolumn='id_manufacturer' namecolumn='name'}
				</div>

				{* Options pour le groupe (inclusion/exclusion) *}
				{sil_inputActive obj=$obj key_active='manufacturer_type' key_db='manufacturer_type' label={l s='Manufacturers above have to be excluded' mod='pm_seointernallinking'}}
			</div>

			<div id="config-products-4">
				{* Choix des fournisseurs *}
				<div class="supplier_picker">
					{sil_ajaxSelectMultiple selectedoptions=$selectedoptions.suppliers key='suppliers' label={l s='Suppliers' mod='pm_seointernallinking'} remoteurl="{$base_config_url|sil_nofilter}&getItem=1&itemType=supplier" idcolumn='id_supplier' namecolumn='name'}
				</div>

				{* Options pour le groupe (inclusion/exclusion) *}
				{sil_inputActive obj=$obj key_active='supplier_type' key_db='supplier_type' label={l s='Suppliers above have to be excluded' mod='pm_seointernallinking'}}
			</div>

			{* End Wrap Zone *}
		</div>

		<script type="text/javascript">
		{literal}
			$(document).ready(function() {
				$("#wrapProductsConfigTab").tabs({active: {/literal}{if isset($obj->group_type) && $obj->group_type == 5}2{else}0{/if}{literal}});
			});
		{/literal}
		</script>
	</div>
	{* End - #groupProductZone *}
	{* Begin - #groupCMSZone *}
	<div id="groupCMSZone">
		{* Choix des pages CMS *}
		<div class="cms_picker">
			{sil_ajaxSelectMultiple selectedoptions=$selectedoptions.cms_pages key='cms_pages' label={l s='CMS Pages' mod='pm_seointernallinking'} remoteurl="{$base_config_url|sil_nofilter}&getItem=1&itemType=cms" idcolumn='id_cms' namecolumn='meta_title'}
		</div>

		{* Options pour le groupe (inclusion/exclusion) *}
		{sil_inputActive obj=$obj key_active='cms_type' key_db='cms_type' label={l s='CMS Pages above have to be excluded' mod='pm_seointernallinking'}}
	</div>
	{* End - #groupCMSZone *}

	<br />

	<script type="text/javascript">
	{literal}
		$("form#formAddGroup").submit(function() {
			var nbPagesCMS = $("div.cms_picker div.ui-multiselect span.count").html().match(/[\d\.]+/g).join("");
			var nbCategories = $("input[name=\'categories[]\']:checked, input[name=\'categories[]\'][type=hidden]").size();
			var nbManufacturers = $("div.manufacturer_picker div.ui-multiselect span.count").html().match(/[\d\.]+/g).join("");
			if ($("select#group_type").val() == 1 && $("#groupInformations").html() == "") {
				alert("{/literal}{l s='You must select an item among the different tabs' js=1 mod='pm_seointernallinking'}{literal}");
				return false;
			} else if ($("select#group_type").val() == 2 && nbPagesCMS == 0) {
				alert("{/literal}{l s='You have to choose at least one CMS page' js=1 mod='pm_seointernallinking'}{literal}");
				return false;
			} else if ($("select#group_type").val() == 4 && nbCategories == 0) {
				alert("{/literal}{l s='You have to choose at least one category' js=1 mod='pm_seointernallinking'}{literal}");
				return false;
			} else if ($("select#group_type").val() == 5 && nbManufacturers == 0) {
				alert("{/literal}{l s='You have to choose at least one manufacturer' js=1 mod='pm_seointernallinking'}{literal}");
				return false;
			}
			return true;
		});
		
		function getGroupCombinaisonInformations() {
			var groupExplain = "";
			if ($("select#group_type").val() == 1) {
				
				var nbCategories = $("input[name=\'categories[]\']:checked, input[name=\'categories[]\'][type=hidden]").size();
				var exclusionCategories = $("input#category_type_on:checked").size();
				
				var nbProduits = $("div.product_picker div.ui-multiselect span.count").html().match(/[\d\.]+/g).join("");
				var exclusionProduits = $("input#product_type_on:checked").size();
				
				var nbFabricants = $("div.manufacturer_picker div.ui-multiselect span.count").html().match(/[\d\.]+/g).join("");
				var exclusionFabricants = $("input#manufacturer_type_on:checked").size();
				
				var nbFournisseurs = $("div.supplier_picker div.ui-multiselect span.count").html().match(/[\d\.]+/g).join("");
				var exclusionFournisseurs = $("input#supplier_type_on:checked").size();
				
				if (nbCategories == 0 && nbProduits == 0 && nbFabricants == 0 && nbFournisseurs == 0) {
					$("#groupInformations").html("");
					return;
				}
				if (nbProduits == 0) {
					groupExplain += "{/literal}{l s='Your products selection will be part' js=1 mod='pm_seointernallinking'}{literal}";
				}
				if (nbProduits > 0 && exclusionProduits == 1) {
					groupExplain += "{/literal}{l s='Your selection will contain all products excepted' js=1 mod='pm_seointernallinking'}{literal}&nbsp;"+ nbProduits +"&nbsp;{/literal}{l s='of them' js=1 mod='pm_seointernallinking'}{literal}";
				}
				if (nbProduits > 0 && exclusionProduits == 0) {
					groupExplain += "{/literal}{l s='Your selection will contain' js=1 mod='pm_seointernallinking'}{literal}&nbsp;"+ nbProduits +"&nbsp;{/literal}{l s='product(s)' js=1 mod='pm_seointernallinking'}{literal}";
				}

				if (nbProduits > 0) groupExplain += "{/literal}&nbsp;{l s='in' js=1 mod='pm_seointernallinking'}{literal}";
				if (nbProduits == 0) groupExplain += "{/literal}&nbsp;{l s='of' js=1 mod='pm_seointernallinking'}{literal}";
				
				if (nbCategories == 0) groupExplain += "{/literal}&nbsp;{l s='all the categories of your website' js=1 mod='pm_seointernallinking'}{literal}";
				if (nbCategories > 0 && exclusionCategories == 1) groupExplain += "{/literal}&nbsp;{l s='all the categories of your website excepted' js=1 mod='pm_seointernallinking'}{literal}&nbsp;"+ nbCategories +"&nbsp;{/literal}{l s='of them' js=1 mod='pm_seointernallinking'}{literal}";
				if (nbCategories > 0 && exclusionCategories == 0) groupExplain += "&nbsp;" + nbCategories +"&nbsp;{/literal}{l s='categorie(s)' js=1 mod='pm_seointernallinking'}{literal}";

				if (nbFabricants == 0) groupExplain += ", {/literal}&nbsp;{l s='of every manufacturer' js=1 mod='pm_seointernallinking'}{literal}";
				if (nbFabricants > 0 && exclusionFabricants == 1) groupExplain += ", {/literal}&nbsp;{l s='of every manufacturer excepted' js=1 mod='pm_seointernallinking'}{literal}&nbsp;"+ nbFabricants +"&nbsp;{/literal}{l s='of them' js=1 mod='pm_seointernallinking'}{literal}";
				if (nbFabricants > 0 && exclusionFabricants == 0) groupExplain += ", {/literal}&nbsp;{l s='of' js=1 mod='pm_seointernallinking'}{literal}&nbsp;"+ nbFabricants +"&nbsp;{/literal}{l s='manufacturer(s)' js=1 mod='pm_seointernallinking'}{literal}";

				if (nbFournisseurs == 0) groupExplain += ", {/literal}&nbsp;{l s='of every supplier' js=1 mod='pm_seointernallinking'}{literal}";
				if (nbFournisseurs > 0 && exclusionFournisseurs == 1) groupExplain += ", {/literal}&nbsp;{l s='of every supplier excepted' js=1 mod='pm_seointernallinking'}{literal}&nbsp;"+ nbFournisseurs +"&nbsp;{/literal}{l s='of them' js=1 mod='pm_seointernallinking'}{literal}";
				if (nbFournisseurs > 0 && exclusionFournisseurs == 0) groupExplain += ", {/literal}&nbsp;{l s='of' js=1 mod='pm_seointernallinking'}{literal}&nbsp;"+ nbFournisseurs +"&nbsp;{/literal}{l s='supplier(s)' js=1 mod='pm_seointernallinking'}{literal}";
			} else if ($("select#group_type").val() == 2) {
				$("select#multiselectcms_pages").multiSelectUpdateCount();
				var nbPagesCMS = $("div.cms_picker div.ui-multiselect span.count").html().match(/[\d\.]+/g).join("");
				var exclusionPagesCMS = $("input#cms_type_on:checked").size();
				
				if (nbPagesCMS == 0) groupExplain += "{/literal}{l s='You have to choose at least one CMS page' js=1 mod='pm_seointernallinking'}{literal}";
				if (nbPagesCMS > 0 && exclusionPagesCMS == 1) groupExplain += "{/literal}{l s='Your selection will contain all the CMS pages excepted' js=1 mod='pm_seointernallinking'}{literal}&nbsp;"+ nbPagesCMS +"&nbsp;{/literal}{l s='of them' js=1 mod='pm_seointernallinking'}{literal}";
				if (nbPagesCMS > 0 && exclusionPagesCMS == 0) groupExplain += "{/literal}{l s='Your selection will contain' js=1 mod='pm_seointernallinking'}{literal}&nbsp;"+ nbPagesCMS +"&nbsp;{/literal}{l s='CMS page(s)' js=1 mod='pm_seointernallinking'}{literal}";
			} else if ($("select#group_type").val() == 4) {
				
				var nbCategories = $("input[name=\'categories[]\']:checked, input[name=\'categories[]\'][type=hidden]").size();
				var exclusionCategories = $("input#category_type_on:checked").size();
				if (nbCategories == 0) {
					$("#groupInformations").html("");
					return;
				}
						
				groupExplain = "{/literal}{l s='Your selection will contain' js=1 mod='pm_seointernallinking'}{literal}";
				if (nbCategories > 0 && exclusionCategories == 1) groupExplain += "{/literal}&nbsp;{l s='all the categories of your website excepted' js=1 mod='pm_seointernallinking'}{literal}&nbsp;"+ nbCategories +"&nbsp;{/literal}{l s='of them' js=1 mod='pm_seointernallinking'}{literal}";
				if (nbCategories > 0 && exclusionCategories == 0) groupExplain += "&nbsp;" + nbCategories +"&nbsp;{/literal}{l s='categorie(s)' js=1 mod='pm_seointernallinking'}{literal}";
			} else if ($("select#group_type").val() == 5) {
				
				var nbFabricants = $("div.manufacturer_picker div.ui-multiselect span.count").html().match(/[\d\.]+/g).join("");
				var exclusionFabricants = $("input#manufacturer_type_on:checked").size();
				if (nbFabricants == 0) {
					$("#groupInformations").html("");
					return;
				}
				
				groupExplain = "{/literal}{l s='Your selection will contain' js=1 mod='pm_seointernallinking'}{literal}";
				if (nbFabricants > 0 && exclusionFabricants == 1) groupExplain += "{/literal}&nbsp;{l s='all the manufacturers of your website excepted' js=1 mod='pm_seointernallinking'}{literal}&nbsp;"+ nbFabricants +" {/literal}&nbsp;{l s='of them' js=1 mod='pm_seointernallinking'}{literal}";
				if (nbFabricants > 0 && exclusionFabricants == 0) groupExplain += "&nbsp;" + nbFabricants +"{/literal}&nbsp;{l s='manufacturer(s)' js=1 mod='pm_seointernallinking'}{literal}";
			}
			$("#groupInformations").html(groupExplain);
		}

		function checkForChanges() {
			getGroupCombinaisonInformations();
			setTimeout(checkForChanges, 1000);
		}
		
		function countAllMultiSelect() {
			$("a[href=\'#config-products-2\']").trigger("click");
			$("select#multiselectproducts").multiSelectUpdateCount();
			$("a[href=\'#config-products-3\']").trigger("click");
			$("select#multiselectmanufacturers").multiSelectUpdateCount();
			$("a[href=\'#config-products-4\']").trigger("click");
			$("select#multiselectsuppliers").multiSelectUpdateCount();
			$("a[href=\'#config-products-1\']").trigger("click");
			if ($("div#groupCMSZone:visible").size()) {
				$("select#multiselectcms_pages").multiSelectUpdateCount();
			} else {
				$("div#groupCMSZone").show(0);
				$("select#multiselectcms_pages").multiSelectUpdateCount();
				$("div#groupCMSZone").hide(0);
			}
			checkForChanges();
			$("select#group_type").trigger("change");
		}
		
		$(document).ready(function() { setTimeout(countAllMultiSelect, 500); });
		
		$("select[name=\'group_type\'], input[name=\'categories[]\'], input.check_all_children, input#category_type_on, input#category_type_off, input#supplier_type_on, input#supplier_type_off, input#manufacturer_type_on, input#manufacturer_type_off, input#product_type_on, input#product_type_off, input#cms_type_on, input#cms_type_off").bind("change", function() {
			getGroupCombinaisonInformations();
		});

		$("select[name=\'group_type\']").bind("change", function() {
			if ($("select#group_type").val() == 1) {
				$("a[href=\'#config-products-2\']").trigger("click");
				$("select#multiselectproducts").multiSelectUpdateCount();
				$("a[href=\'#config-products-3\']").trigger("click");
				$("select#multiselectmanufacturers").multiSelectUpdateCount();
				$("a[href=\'#config-products-4\']").trigger("click");
				$("select#multiselectsuppliers").multiSelectUpdateCount();
				$("a[href=\'#config-products-1\']").trigger("click");
			} else if ($("select#group_type").val() == 2) {
				$("select#multiselectcms_pages").multiSelectUpdateCount();
			}
		});
	{/literal}
	</script>

	{module->_displaySubmit text="{l s='Save' mod='pm_seointernallinking'}" name='submit_group'}

{sil_endForm id="formAddGroup"}