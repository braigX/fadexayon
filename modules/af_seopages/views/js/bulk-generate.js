/**
*  @author    Amazzing
*  @copyright Amazzing
*  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)*
*/
var spg = {
	init: function() {
		spg.identifier = Math.floor(Math.random() * spg.timeNow());
		spg.$tab = $('#sp-generate');
		spg.$dynamicGroup = spg.$tab.find('.cr-lvl-3');
		spg.$tab.on('click', '.toggleParamsForm', function() {
			spg.$tab.toggleClass('show-params-form');
		}).on('change', '.crLvl', function() {
			let $parent = $(this).closest('.available-params').removeClass('ready'),
				val = $(this).val(),
				isSublevel = $(this).data('lvl') == 2;
			$(this).toggleClass('has-selection', val != '').
			closest('.cr-lvl').nextAll('.cr-lvl').addClass('hidden').
			filter('.cr-'+val).removeClass('hidden').find('select').val('').change();
			if (val && (isSublevel || !$parent.find('.cr-lvl-2').not('.hidden').length)) {
				let params = {
						action: 'bulkGenerateAction',
						bulk_action_name: 'callCriteriaOptions',
						type: val
					},
					response = function(r) {
						spg.$dynamicGroup.removeClass('loading').html(r.html).
						find('.opt-action[data-bulk-action="check"]').click(); // check all
						$parent.addClass('ready');
					};
				spg.$dynamicGroup.html('').removeClass('hidden col-lg-5 col-lg-8').
				addClass((isSublevel ? 'col-lg-5' : 'col-lg-8')+' loading');
				af.seoPages.ajaxRequest(params, response);
			}
		}).on('click', '.addParams', function() {
			let $selectedOption = $('.crLvl:visible').last().find('option:selected'),
				var_name = spg.getVarName($selectedOption.val()),
				name = $selectedOption.val();
				$inputs = spg.$dynamicGroup.find('.opt-checkbox:checked'),
				info = spg.$dynamicGroup.find('.selected-items').html();
			if (!$inputs.length) {
				alert('Please select at least one value');
				return;
			}
			$('.bulk-selected-groups').append(spg.renderGroup(var_name, name, $inputs, info));
			spg.updateGroupSummary();
			spg.$tab.removeClass('show-params-form').find('.cr-lvl-1').find('select').val('').change();
			$selectedOption.prop('disabled', true);
		}).on('click', '.removeGroup', function() {
			spg.removeGroup($(this).closest('.params-group'));
		}).on('click', '.toggleOtherFields', function() {
			$(this).siblings('.other-fields').toggleClass('hidden');
			$(this).find('i').toggleClass('icon-angle-down icon-angle-up');
			if (!spg.$tab.data('mceready')) {
				af.mce.activateVisible(spg.$tab.data('mceready', 1));
			}
		}).on('click', '.available-var', function() {
			spg.copyToClipBoard($.trim($(this).text()));
		}).on('change', '.bulk-action-name', function() {
			let isUpdateAction = $(this).val() == 'update';
			$('.bulk-update-options').toggleClass('hidden', !isUpdateAction);
			spg.$tab.find('.skip-info').html('').addClass('hidden');
			spg.markBlockedFields();
			if (isUpdateAction && !$(this).data('updateoptionsready')) {
				$(this).data('updateoptionsready', 1).siblings('.bulk-update-options')
				.find('.field-names').find('.opt-checkbox').on('change', function() {
					spg.markBlockedFields();
				});
			}
		}).on('click', '.runAction', function() {
			if (spg.$tab.hasClass('processing')) {
				spg.$tab.removeClass('processing');
			} else {
				af.mce.updateTextareaValues($('.bulk-generate-params.sp-form'));
				let action = $(this).closest('.bulk-generate-actions').find('.bulk-action-name').val(),
					data = $('.bulk-generate-params').serialize();
				spg.process(action, data, spg.simpleHash(data)+'-'+spg.identifier+'-'+action);
			}
		});
	},
	markBlockedFields: function() {
		let $checkboxes = $('.bulk-update-options').find('.field-names:visible').find('.opt-checkbox:checked'),
			$fields = $('.sp-form').find('.form-group').toggleClass('dont-process', $checkboxes.length > 0);
		$checkboxes.each(function() {
			$fields.filter('.'+$(this).val().replace('[]', '')).removeClass('dont-process');
		});
	},
	process: function(action, data, identifier) {
		let params = {
				action: 'bulkGenerateAction',
				bulk_action_name: action,
				data: data,
				identifier: identifier,
			},
			response = function(r) {
				spg.$tab.find('.process-info').html(r.info)
					.closest('.bulk-generate-actions').find('.thrown-errors').remove();
				if (action == 'generate') {
					let skipHTML = r.skip ? Object.values(r.skip).join('<br>') : '';
					spg.$tab.find('.skip-info').html(skipHTML).toggleClass('hidden', !skipHTML);
				}
				if (r.errors) {
					spg.$tab.removeClass('processing').find('.bulk-generate-actions').prepend(r.errors);
				} else if (r.complete) {
					spg.$tab.removeClass('processing').addClass('complete');
					af.seoPages.loadItems(1);
				} else {
					spg.process(action, '', identifier);
				}
			};
		if (data) { // first run
			spg.$tab.removeClass('complete').addClass('processing').find('.process-info').html('');
		} else if (!spg.$tab.hasClass('processing')) {
			return;
		}
		af.seoPages.ajaxRequest(params, response);
	},
	renderGroup: function(var_name, name, $inputs, info) {
		let html = '<div class="params-group" data-name="'+name+'">';
		html += '<span class="var-name">'+var_name+'</span>';
		html += '<input type="hidden" name="ui_vars['+var_name+']" value="{'+name+'}">';
		$inputs.each(function() {
			html += '<input type="hidden" name="criteria['+name+'][]" value="'+$(this).val()+'">';
		});
		html += '<a href="#" class="icon-trash removeGroup"></a>'+info;
		html += '</div>';
		return html;
	},
	removeGroup: function($group) {
		spg.$tab.find('option[value="'+$group.data('name')+'"]:disabled').prop('disabled', false);
		$group.remove();
		spg.updateGroupSummary();
	},
	updateGroupSummary: function() {
		let total = 0,
			varsHTML = '',
			maxW = 0;
		$('.params-group').each(function() {
			let num = parseInt($(this).find('.total-num').text()),
				$varContainer = $(this).find('.var-name').css({width: ''}),
				w = Math.ceil($varContainer.outerWidth());
			total = total ? total * num : num;
			varsHTML += '<span class="available-var b">'+$varContainer.html()+'</span> ';
			maxW = w > maxW ? w : maxW;
		});
		$('.params-group').find('.var-name').css({width: maxW+'px'});
		spg.$tab.toggleClass('show-step-2', !!total).find('.params-summary').find('.dynamic').html(total);
		$('.available-vars').find('.dynamic').html(varsHTML);
		if (!total) {
			$('.bulk-generate-params.sp-form').find('input[type="text"], textarea').val('').
			filter('textarea.mce-activated').each(function() {
				tinyMCE.get($(this).attr('id')).setContent('');
			});
			$('.bulk-action-name').val('generate').change();
			$('.process-info').html('');
		}
	},
	getVarName: function(val) {
		if (val == 'special') {
			val = 'special_filter';
		} else {
			switch (val[0]) {
				case 'c': val = 'category'; break;
				case 'a': val = 'attribute_'+val.slice(1); break;
				case 'f': val = 'feature_'+val.slice(1); break;
				case 'm': val = 'manufacturer'; break;
				case 's': val = 'supplier'; break;
				case 't': val = 'tag'; break;
				case 'q': val = 'condition'; break;
			}
		}
		return '{'+val+'}';
	},
	timeNow: function() {
		return new Date().getTime() / 1000;
	},
	simpleHash: function(string) {
		return string.split('').map(char => char.charCodeAt(0) * 5).reduce((acc, curr) => acc + curr);
	},
	copyToClipBoard: function(content) {
		if (navigator.clipboard && window.isSecureContext) { // navigator.clipboard requires https
			navigator.clipboard.writeText(content).then(function () {
				$.growl.notice({title:'', message: 'Copied'});
			});
		} else { // retro
			let $tmpInput = $('<input>').appendTo('body').val(content).select();
			document.execCommand('copy');
			$tmpInput.remove();
			$.growl.notice({title:'', message: 'Copied'});
		}
	},
};
$(document).ready(function() {
	spg.init();
});
/* since 1.0.1 */
