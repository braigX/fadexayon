/**
* 2007-2025 PrestaShop
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2025 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

var is_enter = 0;

function set_all_images_on_page_hover()
{

	var products = [];
	jQuery( ".ajax_block_product" ).each(function() {

		var el = jQuery(this);
		if(el.find('.ajax_add_to_cart_button').length > 0)
		{

			var prd_id = el.find('.ajax_add_to_cart_button').attr('data-id-product');
			products.push(prd_id);	
		}	

	});	



	var response_z = $.ajax({ 
				  			type: "POST",   
				  			method: 'POST',
							url:  '../index.php?fc=module&module=ultimateimagetool&controller=ajaxswap',
							dataType : 'html',
							cache: false,
							data: {  action: 'get_all_swap_images', products: JSON.stringify(products) }, 
							async: true,
							success: function(msg) {

								is_enter = 0; 

								obj = JSON.parse(msg);
								if(obj.error == 0)
								{
									var images = obj.images;

									 for(var key in images) 
									 { 
									 	jQuery( ".ajax_block_product" ).each(function() 
									 	{
									 		var el = jQuery(this);

											if(el.find('.ajax_add_to_cart_button').length > 0)
											{

												prd_id = el.find('.ajax_add_to_cart_button').attr('data-id-product');
												if (prd_id == key)
												{
													if( typeof(el.find('img').attr('data-original')) !== 'undefined')
													{
														var original_src = el.find('img').attr('data-original');
													}
													else
													{
														var original_src = el.find('img').attr('src');
													}

													if(images[key]['all'].length > 0)
													{
														var all_images = '';
														all_images = '<div style="position:absolute; display:none; left:5px; top:5px; z-index:4;" class="uit_wrapper">';
														for(var key_all in images[key]['all']) 
														{ 
															all_images += '<img class="uit_img_thumb" style="max-width:60px; display:block; border:1px solid #eee; margin-bottom:2px; " src="'+images[key]['all'][key_all]['small']+'" big-img="'+images[key]['all'][key_all]['big']+'" />';
														}
														all_images += '</div>';
														el.find('img').parent().parent().prepend(all_images);												
													}

													
													el.find('img').attr('second-image-uit', images[key]['second']);
													el.find('img').attr('original-image-uit', original_src);
												}
											}
									 	});	

									 }
								}
								return;
							}
						}).responseText;		
}



jQuery(document).ready(function(){

		setTimeout(function(){
			set_all_images_on_page_hover();
		}, 100);


	jQuery('body').on('mouseenter','.ajax_block_product', function(){

		jQuery(this).find('.uit_wrapper').fadeIn();
		return false;


	});	

	jQuery('body').on('mouseleave','.ajax_block_product', function(){

		jQuery(this).find('.uit_wrapper').fadeOut();
		return false;

	});


	jQuery('body').on('mouseenter','.ajax_block_product .uit_img_thumb', function(){
		var current_el = jQuery(this);
		var parent_el = current_el.closest('.ajax_block_product');
		var big_img = current_el.attr('big-img');
		parent_el.find( 'img:not(.uit_img_thumb)' ).attr('src', big_img);


		return false;

	});



	jQuery('body').on('mouseleave','.ajax_block_product img', function(){

		var current_el = jQuery(this);
		var current_src = current_el.attr('src');

		if(current_el.hasClass('uit_img_thumb'))
			return false;

		if(current_el.attr('original-image-uit'))
		{
			if(current_src != current_el.attr('original-image-uit'))
			{
				current_el.attr('src', current_el.attr('original-image-uit'));
			}
			
		}
		return false;


	});	

	jQuery('body').on('mouseenter','.ajax_block_product img', function(){

	

		var el = jQuery(this).closest('.ajax_block_product');
		var current_el = jQuery(this);

		if(current_el.hasClass('uit_img_thumb'))
			return false;

		if( typeof(current_el.attr('original-image-uit')) !== 'undefined')
		{

			if( typeof(current_el.attr('second-image-uit')) !== 'undefined')
			{
				current_el.attr('src', current_el.attr('second-image-uit'));
			}

			return false;
		}


		if(el.find('.ajax_add_to_cart_button').length > 0)
		{

		if(is_enter == 1)
				return false;

		is_enter = 1;


		var prd_id = el.find('.ajax_add_to_cart_button').attr('data-id-product');

			if(prd_id.length> 0)
			{
			


				var response_z = $.ajax({ 
				  			type: "POST",   
				  			method: 'POST',
							url:  '../index.php?fc=module&module=ultimateimagetool&controller=ajaxswap',
							dataType : 'html',
							cache: false,
							data: {  action: 'get_swap_image', id_product: prd_id }, 
							async: true,
							success: function(msg) {

								is_enter = 0; 

								obj = JSON.parse(msg);
								if(obj.error == 0)
								{
									current_el.attr('second-image-uit', obj.img_src);
									current_el.attr('original-image-uit', current_el.attr('src'));
									current_el.attr('src', obj.img_src);


								}
								return;
							}
						}).responseText;			
			}

		}

	});

});