/**
*  @author    Amazzing
*  @copyright Amazzing
*  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)*
*/

$.extend(afsp, {
	orig: {
		defineElements: afsp.defineElements,
		updateElements: afsp.updateElements
	},
	defineElements: function() {
		afsp.orig.defineElements();
		afsp.$.header = $('#main').find('h1').first();
		afsp.$.description = $('#'+prestashop.page.page_name+'-description');
	},
	updateElements: function(data) {
		if (!afsp.originalContent) {
			afsp.originalContent = {
				header: afsp.$.header.html(),
				description: afsp.$.description.html(),
				meta_title: document.title,
			};
			af.$filterBlock.on('updateProductList', function(e, r) {
				if (!r.seopage && afsp.native_elements_updated) {
					afsp.updateElements(afsp.originalContent);
				}
			});
		}
		afsp.orig.updateElements(data);
		afsp.native_elements_updated = ('id_seopage' in data);
	},
	controllerActions: function() {},
});
/* since 0.2.4 */
