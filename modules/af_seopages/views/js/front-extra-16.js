/**
*  @author    Amazzing
*  @copyright Amazzing
*  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)*
*/

afsp.defineElements = function() {
	afsp.orig.defineElements();
	afsp.$.header = $('#center_column').find('h1').first();
	if (page_name == 'category') {
		afsp.$.description = $('#center_column').find('.cat_desc').first();
	} else {
		afsp.$.description = $('#center_column').find('.description_box').first();
	}
}
/* since 0.2.3 */
