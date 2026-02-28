{*
* Since 2013 Ovidiu Cimpean
*
* Ovidiu Cimpean - Newsletter Pro © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at addons4prestashop@gmail.com.
*
* @author Ovidiu Cimpean <addons4prestashop@gmail.com>
* @copyright Since 2013 Ovidiu Cimpean
* @license   Do not edit, modify or copy this file
* @version   Release: 4
*}

<div id="filter-by-purchase-box" class="filter-by-purchase-box">
	<h4 class="title">{l s='Search products' mod='newsletterpro'}:</h4>
	<div class="clear" style="height: 0;">&nbsp;</div>
	<div class="poduct-search-container">
		<span class="product-search-span ajax-loader" style="display: none;">&nbsp;</span>
		<input id="filter-poduct-search" class="search-bar empty" tyle="text" value="{l s='search products by:' mod='newsletterpro'} {l s='name, reference, category or type:' mod='newsletterpro'} {l s='new products' mod='newsletterpro'} {l s='or' mod='newsletterpro'} {l s='price drop' mod='newsletterpro'}">
	</div>
	<div id="filter-product-list-box" class="userlist filter-product-list-box products-list">
		<table id="filter-product-list" class="filter-product-list"></table>
	</div>
	<div class="clear" style="clear: both;"></div>

	<br>
	<h4 class="title">{l s='Without purchase' mod='newsletterpro'}:</h4>
	<div class="form-group row">
		<div class="col-sm-12">
			<select id="pqnp-filer-without-purchase" class="form-control fixed-width-xxl">
				<option value="0">{l s='Disabled' mod='newsletterpro'}</option>
				<option value="1">{l s='Enabled' mod='newsletterpro'}</option>
			</select>
		</div>
	</div>

	<br>
	<h4 class="title">{l s='Order Date' mod='newsletterpro'}:</h4>
	<div class="form-group row">
		<div class="col-sm-5">
			<div class="input-group">
				<span class="input-group-addon">{l s='Start' mod='newsletterpro'}</span>
				<input id="pqnp-fbp-order-start-date" type="text" class="form-control" style="z-index: 99999;">
			</div>
		</div>
		<div class="col-sm-5">
			<div class="input-group">
				<span class="input-group-addon">{l s='End' mod='newsletterpro'}</span>
				<input id="pqnp-fbp-order-end-date" type="text" class="form-control" style="z-index: 99999;">
			</div>
		</div>
		<div class="col-sm-2">
			<a id="pqnp-fbp-clear-date" href="javascript:{}" class="btn btn-default"><i class="icon icon-eraser fa fa-eraser"></i>&nbsp;{l s='Clear' mod='newsletterpro'}</a>
		</div>
	</div>

	<h4 class="title">{l s='Products in filter' mod='newsletterpro'}:</h4>
	<div class="fbp-grid-box">
		<table id="fbp-grid" class="table table-bordered fbp-grid">
			<thead>
				<tr>
					<th class="image" data-template="image">{l s='Image' mod='newsletterpro'}</th>
					<th class="name" data-field="name">{l s='Name' mod='newsletterpro'}</th>
					<th class="reference" data-field="reference">{l s='Reference' mod='newsletterpro'}</th>
					<th class="price_display" data-field="price_display">{l s='Price' mod='newsletterpro'}</th>
					<th class="actions" data-template="actions">{l s='Actions' mod='newsletterpro'}</th>
				</tr>
			</thead>
		</table>
	</div>

</div>
