/**
 *  @module     Advanced search (AmbJoliSearch)
 *  @file       ambjolisearch.php
 *  @subject    script principal pour gestion du module (install/config/hook)
 *  @copyright  Copyright (c) 2013-2023 Ambris Informatique SARL
 *  @license    Licensed under the EUPL-1.2-or-later
 *  Support by mail: support@ambris.com
 **/

$(document).ready(function() {
	if (typeof(jolisearch) !== 'undefined' && typeof(jolisearch.rules_to_remove) !== 'undefined') {
		for(var j=1; j < document.styleSheets.length; j++) {
			try {
				var sheet = document.styleSheets[j];
				var rules = new Array();
				if (document.styleSheets[j].cssRules) {
					rules = document.styleSheets[j].cssRules;
				} else {
					rules = document.styleSheets[j].rules;
				}
				for(var i = 0; i < rules.length;i++) {
					for(var k = 0; k < jolisearch.rules_to_remove.length; k++) {
						if (Array.isArray(jolisearch.rules_to_remove[k])) {
							if (rules[i].selectorText == jolisearch.rules_to_remove[k][0]) {
								rules[i].style.removeProperty(jolisearch.rules_to_remove[k][1]);
							}
						} else {
							if (rules[i].selectorText == jolisearch.rules_to_remove[k]) {
								sheet.removeRule(i);
							}
						}
					}
				}
			} catch(e) {
			}
		}
	}
});