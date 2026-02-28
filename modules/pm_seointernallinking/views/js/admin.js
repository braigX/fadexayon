jQuery.fn.extend({
	multiSelectUpdateCount: function() {
		if (typeof(this.data("uiMultiselect")) != "undefined")
			this.data("uiMultiselect")._updateCount();
		else
			this.data("multiselect")._updateCount();
	}
});

jQuery(document).ready(function() {
	jQuery("form#formAddExpression").submit(function(e) {
		jQuery.getJSON(_base_config_url + '&expressionFormValidation=1&' + jQuery(this).serialize(), function(json) {
			if (json === true)  {
				return true;
			} else {
				jQuery("form#formAddExpression").data("validator").invalidate(json);
				e.preventDefault();
				return false;
			}
		});
	});

	jQuery("a.synchroniseeverything").click(function(e) {
		if (jQuery(this).hasClass('ui-state-disabled')) return false;
		
		if (confirm(jQuery(this).attr('title'))) {
			jQuery("div.taskDone").removeClass("taskDone");
			jQuery("a.synchroniseeverything, a.synchroniseeditorial, a.synchroniseallcmspages, a.synchroniseallcategories, a.synchroniseallmanufacturers, a.synchroniseallproducts, a.removealllinks, input[name=submit_global_options]").attr("disabled", true).addClass("ui-state-disabled");
			jQuery(this).pm_ajaxScriptLoad(e);
		}
		return false;
	});
	jQuery("a.synchroniseallproducts").on('click', function(e) {
		if (jQuery(this).hasClass('ui-state-disabled')) return false;
		
		if (confirm(jQuery(this).attr('title'))) {
			jQuery("#syncProductContainer").removeClass('taskDone');
			jQuery("a.synchroniseeverything, a.synchroniseeditorial, a.synchroniseallcmspages, a.synchroniseallcategories, a.synchroniseallmanufacturers, a.synchroniseallproducts, a.removealllinks, input[name=submit_global_options]").attr("disabled", true).addClass("ui-state-disabled");
			jQuery(this).hide();
			jQuery("#progressSyncProductInformation").show();
			jQuery("#progressSyncProductRemainingTime").show();
			jQuery("#progressSyncProduct").show();
			jQuery(this).pm_ajaxScriptLoad(e);
		}
		return false;
	});
	jQuery("a.synchroniseallcmspages").click(function(e) {
		if (jQuery(this).hasClass('ui-state-disabled')) return false;
		
		if (confirm(jQuery(this).attr('title'))) {
			jQuery("#syncCMSPagesContainer").removeClass('taskDone');
			jQuery("a.synchroniseeverything, a.synchroniseeditorial, a.synchroniseallcmspages, a.synchroniseallcategories, a.synchroniseallmanufacturers, a.synchroniseallproducts, a.removealllinks, input[name=submit_global_options]").attr("disabled", true).addClass("ui-state-disabled");
			jQuery(this).hide();
			jQuery("#progressSyncCMSPagesInformation").show();
			jQuery("#progressSyncCMSPagesRemainingTime").show();
			jQuery("#progressSyncCMSPages").show();
			jQuery(this).pm_ajaxScriptLoad(e);
		}
		return false;
	});
	jQuery("a.synchroniseallcategories").click(function(e) {
		if (jQuery(this).hasClass('ui-state-disabled')) return false;
		
		if (confirm(jQuery(this).attr('title'))) {
			jQuery("#syncCategoriesContainer").removeClass('taskDone');
			jQuery("a.synchroniseeverything, a.synchroniseeditorial, a.synchroniseallcmspages, a.synchroniseallcategories, a.synchroniseallmanufacturers, a.synchroniseallproducts, a.removealllinks, input[name=submit_global_options]").attr("disabled", true).addClass("ui-state-disabled");
			jQuery(this).hide();
			jQuery("#progressSyncCategoriesInformation").show();
			jQuery("#progressSyncCategoriesRemainingTime").show();
			jQuery("#progressSyncCategories").show();
			jQuery(this).pm_ajaxScriptLoad(e);
		}
		return false;
	});
	jQuery("a.synchroniseallmanufacturers").click(function(e) {
		if (jQuery(this).hasClass('ui-state-disabled')) return false;
		
		if (confirm(jQuery(this).attr('title'))) {
			jQuery("#syncManufacturersContainer").removeClass('taskDone');
			jQuery("a.synchroniseeverything, a.synchroniseeditorial, a.synchroniseallcmspages, a.synchroniseallcategories, a.synchroniseallmanufacturers, a.synchroniseallproducts, a.removealllinks, input[name=submit_global_options]").attr("disabled", true).addClass("ui-state-disabled");
			jQuery(this).hide();
			jQuery("#progressSyncManufacturersInformation").show();
			jQuery("#progressSyncManufacturersRemainingTime").show();
			jQuery("#progressSyncManufacturers").show();
			jQuery(this).pm_ajaxScriptLoad(e);
		}
		return false;
	});
	jQuery("a.synchroniseeditorial").click(function(e) {
		if (jQuery(this).hasClass('ui-state-disabled')) return false;
		
		if (confirm(jQuery(this).attr('title'))) {
			jQuery("#syncEditorialContainer").removeClass('taskDone');
			jQuery("a.synchroniseeverything, a.synchroniseeditorial, a.synchroniseallcmspages, a.synchroniseallcategories, a.synchroniseallmanufacturers, a.synchroniseallproducts, a.removealllinks, input[name=submit_global_options]").attr("disabled", true).addClass("ui-state-disabled");
			jQuery(this).hide();
			jQuery("#progressSyncEditorialInformation").show();
			jQuery("#progressSyncEditorial").show();
			jQuery(this).pm_ajaxScriptLoad(e);
		}
		return false;
	});
	jQuery("a.removealllinks").click(function(e) {
		if (jQuery(this).hasClass('ui-state-disabled')) return false;
		
		if (confirm(jQuery(this).attr('title'))) {
			jQuery("#deleteAllContainer").removeClass('taskDone');
			jQuery("a.synchroniseeverything, a.synchroniseeditorial, a.synchroniseallcmspages, a.synchroniseallcategories, a.synchroniseallmanufacturers, a.synchroniseallproducts, a.removealllinks, input[name=submit_global_options]").attr("disabled", true).addClass("ui-state-disabled");
			jQuery("#progressDeleteAllInformation").show();
			jQuery(this).hide();
			jQuery(this).pm_ajaxScriptLoad(e);
		}
		return false;
	});
	jQuery('select#group_type').bind('change', 'keyup', function() {
		switch(jQuery(this).val()) {
			case '1':
				jQuery('div#categoriesWarning').hide();
				jQuery('div#manufacturersWarning').hide();
				jQuery('div#groupProductZone').show();
				jQuery('div#groupCMSZone').hide();
				jQuery('div#groupEditorialZone').hide();
				
				jQuery("ul#configTab li").show();
				jQuery("ul#configTab li:eq(0) a").trigger('click');
				break;
			case '2':
				jQuery('div#categoriesWarning').hide();
				jQuery('div#manufacturersWarning').hide();
				jQuery('div#groupCMSZone').show();
				jQuery('div#groupProductZone').hide();
				jQuery('div#groupEditorialZone').hide();
				break;
			case '3':
				jQuery('div#categoriesWarning').hide();
				jQuery('div#manufacturersWarning').hide();
				jQuery('div#groupEditorialZone').show();
				jQuery('div#groupProductZone').hide();
				jQuery('div#groupCMSZone').hide();
				break;
			case '4':
				jQuery('div#categoriesWarning').show();
				jQuery('div#manufacturersWarning').hide();
				jQuery('div#groupProductZone').show();
				jQuery('div#groupCMSZone').hide();
				jQuery('div#groupEditorialZone').hide();
				
				jQuery("ul#configTab li").hide();
				jQuery("ul#configTab li:eq(0)").show();
				jQuery("ul#configTab li:eq(0) a").trigger('click');
				break;
			case '5':
				jQuery('div#manufacturersWarning').show();
				jQuery('div#categoriesWarning').hide();
				jQuery('div#groupProductZone').show();
				jQuery('div#groupCMSZone').hide();
				jQuery('div#groupEditorialZone').hide();
				
				jQuery("ul#configTab li").hide();
				jQuery("ul#configTab li:eq(2)").show();
				jQuery("ul#configTab li:eq(2) a").trigger('click');
				break;
		}
	});
	if (jQuery('input#id_group') && jQuery('input[name=submit_group]')) {
		jQuery('select#group_type').trigger('change');
		jQuery('input.search').css('width', '210px');
	}
});