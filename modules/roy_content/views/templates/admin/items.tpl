<script>
 	theme_url='{$htmlitems.theme_url}'
</script>
<ul class="nav nav-tabs">
	{foreach from=$htmlitems.lang.all item=lang}
		<li id="lang-{$lang.id_lang|escape:'htmlall':'UTF-8'}" class="lang-flag{if $lang.id_lang == $htmlitems.lang.default.id_lang} active{/if}">
			<a href="#items-{$lang.id_lang|escape:'htmlall':'UTF-8'}" data-toggle="tab">{$lang.name|escape:'htmlall':'UTF-8'}</a>
		</li>
	{/foreach}
</ul>
<div class="lang-items tab-content rb-admin">
{foreach name=langs from=$htmlitems.items key=lang item=langItems}
	<div id="items-{$lang|escape:'htmlall':'UTF-8'}" class="lang-content tab-pane {if $lang == $htmlitems.lang.default.id_lang}active{/if}" >
	{foreach name=hooks from=$langItems key=hook item=hookItems}
		<h4 class="hook-title">{l s='Hook' d='Modules.RoyBanners.Shop'} "{$hook|escape:'htmlall':'UTF-8'}"</h4>
		{if $hookItems}
			<ul id="items" class="list-unstyled">
				{foreach name=items from=$hookItems item=hItem}
					<li id="item-{$hItem.id_item|escape:'htmlall':'UTF-8'}" class="item well">
						<form method="post" action="{$htmlitems.postAction|escape:'htmlall':'UTF-8'}" enctype="multipart/form-data" class="item-form defaultForm  form-horizontal">
							<div class="btn-group pull-right">
								<button class="btn btn-default button-edit">
									<span class="button-edit-edit"><i class="icon-edit"></i> {l s='Edit' d='Modules.RoyBanners.Shop'}</span>
									<span class="button-edit-close hide"><i class="icon-remove"></i> {l s='Close' d='Modules.RoyBanners.Shop'}</span>
								</button>
								<button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
									<i class="icon-caret-down"></i>
								</button>
								<ul class="dropdown-menu">
									<li>
										<a href="{$htmlitems.postAction|escape:'htmlall':'UTF-8'}&removeItem&item_id={$hItem.id_item|escape:'htmlall':'UTF-8'}" name="removeItem" class="link-item-delete">
											<i class="icon-trash"></i> {l s='Delete item' d='Modules.RoyBanners.Shop'}
										</a>
									</li>
								</ul>
							</div>
							<span class="item-order">{if $hItem.item_order le 9}0{/if}{$hItem.item_order|escape:'htmlall':'UTF-8'}</span>
							<h5 class="item-title">{$hItem.title|escape:'htmlall':'UTF-8'}</h5>
							<br>
							{if $hItem.image}
								<img src="{$module_dir}img/{$hItem.image}" rel="#comments_{$hItem.id_item}" class="preview img-thumbnail" />
							{/if}
							<div class="item-container clearfix">
								<input type="hidden" name="id_lang" value="{$lang|escape:'htmlall':'UTF-8'}" />
								<input type="hidden" name="item_id" value="{$hItem.id_item|escape:'htmlall':'UTF-8'}" />
								<input type="hidden" name="item_order" value="{$hItem.item_order|escape:'htmlall':'UTF-8'}" />
								<div class="item-field form-group">
									<div class="col-lg-9 col-lg-offset-3">
										<div class="checkbox">
											<label class="control-label">
												{l s='Enable' d='Modules.RoyBanners.Shop'}
												<input type="checkbox" name="item_active" value="1"{if $hItem.active == 1} checked="checked"{/if} />
											</label>
										</div>
									</div>
								</div>

								<div class="item_cols item-field form-group">
									<label class="control-label col-lg-3">{l s='Item width' d='Modules.RoyBanners.Shop'}</label>
									<div class="col-lg-7">
										<select name="item_cols" default="4" class="fixed-width-lg">
											<option value="1"{if $hItem.cols == '1'} selected="selected"{/if}>1/12</option>
											<option value="2"{if $hItem.cols == '2'} selected="selected"{/if}>2/12</option>
											<option value="3"{if $hItem.cols == '3'} selected="selected"{/if}>3/12</option>
                      <option value="4"{if $hItem.cols == '4'} selected="selected"{/if}>4/12</option>
                      <option value="5"{if $hItem.cols == '5'} selected="selected"{/if}>5/12</option>
                      <option value="6"{if $hItem.cols == '6'} selected="selected"{/if}>6/12</option>
                      <option value="7"{if $hItem.cols == '7'} selected="selected"{/if}>7/12</option>
                      <option value="8"{if $hItem.cols == '8'} selected="selected"{/if}>8/12</option>
                      <option value="9"{if $hItem.cols == '9'} selected="selected"{/if}>9/12</option>
                      <option value="10"{if $hItem.cols == '10'} selected="selected"{/if}>10/12</option>
                      <option value="11"{if $hItem.cols == '11'} selected="selected"{/if}>11/12</option>
                      <option value="12"{if $hItem.cols == '12'} selected="selected"{/if}>12/12</option>
										</select>
									</div>
								</div>

								<div class="title_use item-field form-group">
									<div class="col-lg-9 col-lg-offset-3">
										<div class="checkbox">
											<label class="control-label">
												{l s='Display title over banner without hover' d='Modules.RoyBanners.Shop'}
												<input type="checkbox" name="item_title_use" value="1"{if $hItem.title_use == 1} checked="checked"{/if} />
											</label>
										</div>
									</div>
								</div>
								<div class="target item-field form-group">
									<div class="col-lg-9 col-lg-offset-3">
										<div class="checkbox">
											<label class="control-label">
												{l s='Enable hover effect for this banner' d='Modules.RoyBanners.Shop'}
												<input type="checkbox" name="item_hover" value="1"{if $hItem.hover == 1} checked="checked"{/if} />
											</label>
										</div>
									</div>
								</div>
                <div class="bg_color item-field form-group">
                  <label class="control-label col-lg-3">{l s='Hover background color' d='Modules.RoyBanners.Shop'}</label>
                  <div class="col-lg-7 color-inside">
                    <input type="color" name="item_bg_color" class="colorpicker" data-hex="true" value="{$hItem.bg_color|escape:'htmlall':'UTF-8'}" />
                  </div>
                </div>
                <div class="title_color item-field form-group">
                  <label class="control-label col-lg-3">{l s='Hover title color' d='Modules.RoyBanners.Shop'}</label>
                  <div class="col-lg-7 color-inside">
                    <input type="color" name="item_title_color" class="colorpicker" data-hex="true" value="{$hItem.title_color|escape:'htmlall':'UTF-8'}" />
                  </div>
                </div>
								<input type="hidden" name="item_hover_type" value="1" />
								<div class="item_fit item-field form-group">
									<div class="col-lg-9 col-lg-offset-3">
										<div class="checkbox">
											<label class="control-label">
												{l s='Fit banner height to text size on tablets and mobiles' d='Modules.RoyBanners.Shop'}
												<input type="checkbox" name="item_fit" value="1"{if $hItem.fit == 1} checked="checked"{/if} />
											</label>
										</div>
									</div>
								</div>
								<div class="item_hide item-field form-group">
									<div class="col-lg-9 col-lg-offset-3">
										<div class="checkbox">
											<label class="control-label">
												{l s='Hide banner on mobiles' d='Modules.RoyBanners.Shop'}
												<input type="checkbox" name="item_hide" value="1"{if $hItem.hide == 1} checked="checked"{/if} />
											</label>
										</div>
									</div>
								</div>
								<div class="item_hides item-field form-group">
									<div class="col-lg-9 col-lg-offset-3">
										<div class="checkbox">
											<label class="control-label">
												{l s='Hide banner on desktops' d='Modules.RoyBanners.Shop'}
												<input type="checkbox" name="item_hides" value="1"{if $hItem.hides == 1} checked="checked"{/if} />
											</label>
										</div>
									</div>
								</div>
								<div class="title item-field form-group">
									<label class="control-label col-lg-3">{l s='Title / Image title' d='Modules.RoyBanners.Shop'}</label>
									<div class="col-lg-7">
										<input type="text" name="item_title" value="{$hItem.title|escape:'htmlall':'UTF-8'}" />
									</div>
								</div>
								<div class="hook item-field form-group">
									<label class="control-label col-lg-3">{l s='Hook to which the image should be attached' d='Modules.RoyBanners.Shop'}</label>
									<div class="col-lg-7">
										<select name="item_hook" default="top" class="fixed-width-lg">
        							        <option value="beforeheader"{if $hItem.hook == 'beforeheader'} selected="selected"{/if}>before header</option>
											<option value="headercontent"{if $hItem.hook == 'headercontent'} selected="selected"{/if}>header content</option>
											<option value="top"{if $hItem.hook == 'top'} selected="selected"{/if}>top</option>
											<option value="left"{if $hItem.hook == 'left'} selected="selected"{/if}>left</option>
											<option value="right"{if $hItem.hook == 'right'} selected="selected"{/if}>right</option>
                                            <option value="home"{if $hItem.hook == 'home'} selected="selected"{/if}>home</option>
                                            <option value="footerbefore"{if $hItem.hook == 'footerbefore'} selected="selected"{/if}>before footer</option>
                                            <option value="footer"{if $hItem.hook == 'footer'} selected="selected"{/if}>footer</option>
                                            <option value="sidemobilemenu"{if $hItem.hook == 'sidemobilemenu'} selected="selected"{/if}>side mobile menu</option>
                                            <option value="productbeforebuy"{if $hItem.hook == 'productbeforebuy'} selected="selected"{/if}>product before buy</option>
										</select>
									</div>
								</div>
								<div class="image item-field form-group">
									<label class="control-label col-lg-3">{l s='Load your image' d='Modules.RoyBanners.Shop'}</label>
									<div class="col-lg-7">
										<input type="file" name="item_img" />
									</div>
								</div>
								<div class="image_w item-field form-group">
									<label class="control-label col-lg-3">{l s='Image width' d='Modules.RoyBanners.Shop'}</label>
									<div class="col-lg-7">
										<div class="input-group fixed-width-lg">
											<input name="item_img_w" type="text" maxlength="4" size="4" value="{$hItem.image_w|escape:'htmlall':'UTF-8'}"/>
											<span class="input-group-addon">{l s='pixels'}</span>
										</div>
									</div>
								</div>
								<div class="image_h item-field form-group">
									<label class="control-label col-lg-3">{l s='Image height' d='Modules.RoyBanners.Shop'}</label>
									<div class="col-lg-7">
										<div class="input-group fixed-width-lg">
											<input name="item_img_h" type="text" maxlength="4" size="4" value="{$hItem.image_h|escape:'htmlall':'UTF-8'}"/>
											<span class="input-group-addon">{l s='pixels'}</span>
										</div>
									</div>
								</div>
								<div class="url item-field form-group">
									<label class="control-label col-lg-3">{l s='Target link' d='Modules.RoyBanners.Shop'}</label>
									<div class="col-lg-7">
										<input type="text" name="item_url" value="{$hItem.url|escape:'htmlall':'UTF-8'}" />
									</div>
								</div>
								<div class="target item-field form-group">
									<div class="col-lg-9 col-lg-offset-3">
										<div class="checkbox">
											<label class="control-label">
												{l s='Open link in a new tab/page' d='Modules.RoyBanners.Shop'}
												<input type="checkbox" name="item_target" value="1"{if $hItem.target == 1} checked="checked"{/if} />
											</label>
										</div>
									</div>
								</div>
								<div class="video item-field form-group">
									<div class="col-lg-9 col-lg-offset-3">
										<div class="checkbox">
											<label class="control-label">
												{l s='Use as Video banner' d='Modules.RoyBanners.Shop'}
												<input type="checkbox" name="item_video" value="1"{if $hItem.video == 1} checked="checked"{/if} />
											</label>
										</div>
									</div>
								</div>
								<div class="html item-field form-group">
									<label class="control-label col-lg-3">{l s='HTML / Banner description' d='Modules.RoyBanners.Shop'}</label>
									<div class="col-lg-7">
										<textarea class="rte autoload_rte_custom" name="item_html" cols="65" rows="12">{$hItem.html|escape:'htmlall':'UTF-8'}</textarea>
									</div>
								</div>
								<div class="form-group">
									<div class="col-lg-7 col-lg-offset-3">
										<button type="button" class="btn btn-default button-item-edit-cancel" >
											<i class="icon-remove"></i> {l s='Cancel' d='Modules.RoyBanners.Shop'}
										</button>
										<input type="hidden" name="updateItem" value="" />
										<button type="submit" value="updateItem" class="btn btn-success button-save pull-right" onClick="this.form.submit();">
											<i class="icon-save"></i> {l s='Save' d='Modules.RoyBanners.Shop'}
										</button>
									</div>
								</div>
							</div>
						</form>
					</li>
				{/foreach}
			</ul>
		{else}
			<div class="item">
				<span class="text-muted">{l s='No items available' d='Modules.RoyBanners.Shop'}</span>
			</div>
		{/if}
	{/foreach}
	</div>
{/foreach}
</div>
