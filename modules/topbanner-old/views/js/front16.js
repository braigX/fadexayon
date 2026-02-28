/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */
if (typeof window.topBanner !== 'undefined' && typeof window.topBanner.front_controller !== 'undefined') {
	interceptFunction(ajaxCart, 'updateCartEverywhere', {
		after: function () {
			$.ajax({
				type: 'POST',
				url: window.topBanner.front_controller,
				data: {
					action: 'UpdateBanner',
					ajax: true,
				},
				success: function (data) {
					$('#ps_banner_ajax').html('<div id="ps_banner_ajax">' + data + '</div>');
				}
			});
		}
	});
}

function interceptFunction (object, fnName, options) {
    var noop = function () {};
    var fnToWrap = object[fnName];
    var before = options.before || noop;
    var after = options.after || noop;

    object[fnName] = function () {
        before.apply(this, arguments);
        var result = fnToWrap.apply(this, arguments);
        after.apply(this, arguments);
        return result
    }
}
