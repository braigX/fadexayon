<div class="new-item rb-admin">
	<div class="form-group">
		<button type="button" class="btn btn-default btn-lg button-new-item"><i class="icon-plus-sign"></i> {l s='Add item' d='Modules.RoyBanners.Shop'}</button>
	</div>
	<div class="item-container">
		<form method="post" action="{$htmlitems.postAction|escape:'htmlall':'UTF-8'}" enctype="multipart/form-data" class="item-form defaultForm  form-horizontal">
			<div class="well">
				<div class="language item-field form-group">
					<label class="control-label col-lg-3">{l s='Language' d='Modules.RoyBanners.Shop'}</label>
					<div class="col-lg-7">
						<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" >
							<span id="selected-language">
							{foreach from=$htmlitems.lang.all item=lang}
								{if $lang.id_lang == $htmlitems.lang.default.id_lang} {$lang.iso_code|escape:'htmlall':'UTF-8'}{/if}
							{/foreach}
							</span>
							<span class="caret">&nbsp;</span>
						</button>
						<ul class="languages dropdown-menu">
							{foreach from=$htmlitems.lang.all item=lang}
								<li id="lang-{$lang.id_lang|escape:'htmlall':'UTF-8'}" class="new-lang-flag"><a href="javascript:setLanguage({$lang.id_lang|escape:'htmlall':'UTF-8'}, '{$lang.iso_code|escape:'htmlall':'UTF-8'}');">{$lang.name|escape:'htmlall':'UTF-8'}</a></li>
							{/foreach}
						</ul>
						<input type="hidden" id="lang-id" name="id_lang" value="{$htmlitems.lang.default.id_lang|escape:'htmlall':'UTF-8'}" />
					</div>
				</div>
				<div class="title item-field form-group">
					<label class="control-label col-lg-3 ">{l s='Title' d='Modules.RoyBanners.Shop'}</label>
					<div class="col-lg-7">
						<input class="form-control" type="text" name="item_title"/>
					</div>
				</div>

				<div class="cols item-field form-group">
					<label class="control-label col-lg-3">{l s='Item width' d='Modules.RoyBanners.Shop'}</label>
					<div class="col-lg-7">
						<select name="item_cols" default="4" class="fixed-width-lg">
							<option value="1">1/12</option>
							<option value="2">2/12</option>
							<option value="3">3/12</option>
							<option value="4">4/12</option>
							<option value="5">5/12</option>
							<option value="6">6/12</option>
							<option value="7">7/12</option>
							<option value="8">8/12</option>
							<option value="9">9/12</option>
							<option value="10">10/12</option>
							<option value="11">11/12</option>
							<option value="12">12/12</option>
						</select>
					</div>
				</div>

				<div class="title_use item-field form-group">
					<div class="col-lg-9 col-lg-offset-3">
						<div class="checkbox">
							<label class="control-label">
								{l s='Display title over banner without hover' d='Modules.RoyBanners.Shop'}
								<input type="checkbox" name="item_title_use" value="1" />
							</label>
						</div>
					</div>
				</div>
				<div class="hover item-field form-group">
					<div class="col-lg-9 col-lg-offset-3">
						<div class="checkbox">
							<label class="control-label">
								{l s='Enable hover effect for this banner' d='Modules.RoyBanners.Shop'}
								<input type="checkbox" name="item_hover" value="1" />
							</label>
						</div>
					</div>
				</div>
				<div class="bg_color item-field form-group">
					<label class="control-label col-lg-3">{l s='Hover background color' d='Modules.RoyBanners.Shop'}</label>
					<div class="col-lg-7 color-inside">
						<input type="color" name="item_bg_color" class="colorpicker" data-hex="true" value="#ffffff" />
					</div>
				</div>
				<div class="title_color item-field form-group">
					<label class="control-label col-lg-3">{l s='Hover title color' d='Modules.RoyBanners.Shop'}</label>
					<div class="col-lg-7 color-inside">
						<input type="color" name="item_title_color" class="colorpicker" data-hex="true" value="#777777" />
					</div>
				</div>
				<input type="hidden" name="item_hover_type" value="1" />
				<div class="fit item-field form-group">
					<div class="col-lg-9 col-lg-offset-3">
						<div class="checkbox">
							<label class="control-label">
								{l s='Fit banner height to text size on tablets and mobiles' d='Modules.RoyBanners.Shop'}
								<input type="checkbox" name="item_fit" value="1" />
							</label>
						</div>
					</div>
				</div>
				<div class="hide_mobile item-field form-group">
					<div class="col-lg-9 col-lg-offset-3">
						<div class="checkbox">
							<label class="control-label">
								{l s='Hide banner on mobiles' d='Modules.RoyBanners.Shop'}
								<input type="checkbox" name="item_hide" value="1" />
							</label>
						</div>
					</div>
				</div>
				<div class="hide_desktop item-field form-group">
					<div class="col-lg-9 col-lg-offset-3">
						<div class="checkbox">
							<label class="control-label">
								{l s='Hide banner on desktops' d='Modules.RoyBanners.Shop'}
								<input type="checkbox" name="item_hides" value="1" />
							</label>
						</div>
					</div>
				</div>
				<div class="hook item-field form-group">
					<label class="control-label col-lg-3">{l s='Hook' d='Modules.RoyBanners.Shop'}</label>
					<div class="col-lg-7">
						<select class="form-control fixed-width-lg" name="item_hook" default="top">
							<option value="beforeheader">before header</option>
							<option value="headercontent">header content</option>
							<option value="top">top</option>
							<option value="left">left</option>
							<option value="right">right</option>
                            <option value="home">home</option>
                            <option value="footerbefore">before footer</option>
                            <option value="footer">footer</option>
                            <option value="sidemobilemenu">side mobile menu</option>
                            <option value="productbeforebuy">product before buy</option>
						</select>
					</div>
				</div>
				<div class="image item-field form-group">
					<label class="control-label col-lg-3">{l s='Image' d='Modules.RoyBanners.Shop'}</label>
					<div class="col-lg-7">
						<input type="file" name="item_img" />
					</div>
				</div>
				<div class="image_w item-field form-group">

					<label class="control-label col-lg-3">{l s='Image width' d='Modules.RoyBanners.Shop'}</label>
					<div class="col-lg-7">
						<div class="input-group fixed-width-lg">
							<span class="input-group-addon">{l s='px'}</span>
							<input name="item_img_w" type="text" maxlength="4"/>
						</div>
					</div>
				</div>
				<div class="image_h item-field form-group">
					<label class="control-label col-lg-3">{l s='Image height' d='Modules.RoyBanners.Shop'}</label>
					<div class="col-lg-7">
						<div class="input-group fixed-width-lg">
							<span class="input-group-addon">{l s='px'}</span>
							<input name="item_img_h" type="text" maxlength="4"/>
						</div>
					</div>
				</div>
				<div class="url item-field form-group">
					<label class="control-label col-lg-3">{l s='URL' d='Modules.RoyBanners.Shop'}</label>
					<div class="col-lg-7">
						<input type="text" name="item_url" placeholder="http://" />
					</div>
				</div>
				<div class="target item-field form-group">
					<div class="col-lg-9 col-lg-offset-3">
						<div class="checkbox">
							<label class="control-label">
								{l s='Open in new tab' d='Modules.RoyBanners.Shop'}
								<input type="checkbox" name="item_target" value="1" />
							</label>
						</div>
					</div>
				</div>
				<div class="video item-field form-group">
					<div class="col-lg-9 col-lg-offset-3">
						<div class="checkbox">
							<label class="control-label">
								{l s='Use as Video banner' d='Modules.RoyBanners.Shop'}
								<input type="checkbox" name="item_video" value="1" />
							</label>
						</div>
					</div>
				</div>
				<div class="html item-field form-group">
					<label class="control-label col-lg-3">{l s='HTML' d='Modules.RoyBanners.Shop'}</label>
					<div class="col-lg-7">
						<textarea class="rte autoload_rte_custom" name="item_html" cols="65" rows="12"></textarea>
					</div>
				</div>
				<div class="form-group">
					<div class="col-lg-7 col-lg-offset-3">
						<input type="hidden" name="updateItem" value="" />
						<button type="button" class="btn btn-default button-new-item-cancel"><i class="icon-remove"></i> {l s='Cancel' d='Modules.RoyBanners.Shop'}</button>
						<button type="submit" name="newItem" class="button-new-item-save btn btn-default pull-right" onClick="this.form.submit();"><i class="icon-save"></i> {l s='Save' d='Modules.RoyBanners.Shop'}</button>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
<script type="text/javascript">
	function setLanguage(language_id, language_code) {
		$('#lang-id').val(language_id);
		$('#selected-language').html(language_code);
	}
</script>
