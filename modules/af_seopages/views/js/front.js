/**
*  @author    Amazzing
*  @copyright Amazzing
*  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)*
*/

var afsp = {
	bcSelector: '.breadcrumb',
	defineElements: function() {
		afsp.$ = {
			header: $('.page-header'),
			description: $('.page-description'),
			description_lower: $('.page-description-lower'),
		};
	},
	init: function() {
		afsp.defineElements();
		afsp.controllerActions();
		af.$filterBlock.on('updateProductList', function(e, r) {
			if (r.seopage) {
				afsp.updateElements(r.seopage);
			}
		});
	},
	controllerActions: function() {
		af.staticURL = af_sp_base_url;
		$('#af_form').append('<input type="hidden" name="sp_base_id" value="'+af_sp_base_id+'">');
		if (!af_sp_custom_base) {
			$('.sp-hidden-filter').addClass('sp-dynamic-params');
		}
	},
	updateElements: function(data) {
		document.title = data.meta_title;
		if (data.upd_url && af.dynamicParams.f) {
			data.criteria.split('-').map(c => {
				c = c.split(':');
				let id = afsp.isSpecialFilter(c[0]) ? c[0] : c[0][0]+'-'+c[1];
				af.$selectedFilters.find('.cf[data-id="'+id+'"]').data('url', ''); // exclude from dynamicParams
			});
			af.newURL = af.prepareUrlAndVerifyParams(data.upd_url);
		}
		if (data.breadcrumbs) {
			let bcHTML = $('<div>'+data.breadcrumbs+'</div>').find(afsp.bcSelector).first().html();
			$(afsp.bcSelector).html(bcHTML);
		}
		$.each(afsp.$, function(key, $el) {
			if ($el.length && key in data) {
				$el.html(data[key]);
			}
		});
	},
	isSpecialFilter: function(key) {
		return key.length > 1 && !/\d/.test(key);
	},
};
$(window).on('load', function() {
	setTimeout(function() {
		afsp.init();
	}, 0);
});
/* since 1.0.1 */
