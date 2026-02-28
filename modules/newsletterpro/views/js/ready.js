/**
* Since 2013 Ovidiu Cimpean
*
* Ovidiu Cimpean - Newsletter Pro © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at addons4prestashop@gmail.com.
*
* @author    Ovidiu Cimpean <addons4prestashop@gmail.com>
* @copyright Since 2013 Ovidiu Cimpean
* @license   Do not edit, modify or copy this file
* @version   Release: 4
*/

var NewsletterPro_Ready = NewsletterPro_Ready || ({
	init: function() {
		this.callbacks = [];
		return this;
	},

	load: function(callback) {
		this.callbacks.push(callback);
	},

	dispatch: function(box) {
		for (var i = 0; i < this.callbacks.length; i++) {
			this.callbacks[i](box);
		}
	}
}.init());
