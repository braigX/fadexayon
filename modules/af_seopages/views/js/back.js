/**
*  @author    Amazzing
*  @copyright Amazzing
*  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)*
*/

$.extend(af, {seoPages: {
	init: function() {
		af.seoPages.$parent = $('#sp');
		af.seoPages.$parent.on('click', '.spEdit, .spAdd, .spDuplicate', function() {
			af.popup.open();
			var params = {
					action: 'renderSeoPageForm',
					id_seopage: $(this).closest('.sp-item').data('id'),
					is_duplicate: $(this).hasClass('spDuplicate') ? 1 : 0,
				},
				response = function(r) {
					af.popup.fill(r.title, r.content);
					af.seoPages.prepareForm(af.popup.$container.find('.sp-form'));
				};
			af.seoPages.ajaxRequest(params, response);
		}).on('click', '.spToggleStatus', function() {
			let $item = $(this).closest('.sp-item'),
				params = {
					action: 'setStatus',
					id_seopage: $item.data('id'),
					active: $item.hasClass('inactive') ? 1 : 0,
				},
				response = function(r) {
					if (r.saved) {
						$item.toggleClass('inactive');
						$.growl.notice({ title: '', message: af_txt.saved});
					}
				};
			af.seoPages.ajaxRequest(params, response);
		}).on('click', '.toggleExtraActions', function() {
			let $parent = $(this).closest('.sp-item-actions');
			if (!$parent.hasClass('show-extra')) {
				$parent.addClass('show-extra');
				setTimeout(function() {
					$(document).off('click.anywheresp').on('click.anywheresp', function() {
						$parent.removeClass('show-extra');
						$(document).off('click.anywheresp');
					});
				}, 100);
			}
		}).on('click', '.spDelete', function() {
			if ($(this).closest('.sp-item').hasClass('sp-default') || !confirm(af_txt.areYouSure)) {
				return;
			}
			var $item = $(this).closest('.sp-item'),
				params = {action: 'deleteSeoPage', id_seopage: $item.data('id')},
				response = function(r) {
					if (r.deleted) {
						$item.addClass('deleted');
						setTimeout(function() {
							af.seoPages.loadItems();
						}, 500);
					}
				};
			af.seoPages.ajaxRequest(params, response);
		}).on('click', '.go-to-page', function() {
			af.seoPages.loadItems($(this).data('page'));
		}).on('change', '.update-list', function() {
			var page = $(this).hasClass('reset-page') ? 1 : null;
			af.seoPages.loadItems(page);
		}).on('click', '.sp-sorting', function() {
			var $i = $(this).find('i');
			if ($(this).hasClass('current')) {
				$i.toggleClass('hidden');
			} else {
				$('.sp-sorting.secondary').removeClass('secondary');
				$('.sp-sorting.current').addClass('secondary').removeClass('current');
				$(this).addClass('current');
			}
			$('.sorting-form').find('[name="order[by]"]').val($(this).data('by')).
			siblings('[name="order[way]"]').val($i.not('.hidden').data('way')).
			siblings('[name="order_2[by]"]').val($('.sp-sorting.secondary').data('by') || '').
			siblings('[name="order_2[way]"]').val($('.sp-sorting.secondary').find('i').not('.hidden').data('way') || '');
			af.seoPages.loadItems();
		}).on('keyup', 'input.list-filter', function() {
			let $input = $(this);
			clearTimeout(af.seoPages.timer);
			af.seoPages.timer = setTimeout(function() {
				af.seoPages.applyFilter($input);
			}, 300);
		}).on('change', 'select.list-filter', function() {
			af.seoPages.applyFilter($(this));
		}).on('click', '.clearFilter', function() {
			af.seoPages.applyFilter($(this).parent().find('.list-param').val(''));
		});
		af.seoPages.tagifyCriteriaEvents($('.criteria-filter'), true);
		af.seoPages.activateSitemapActions();
		af.seoPages.activateAjaxModals();
	},
	applyFilter($input) {
		let isActive = $input.val() !== '';
		$input.toggleClass('list-param', isActive).closest('th').toggleClass('has-active-filter', isActive);
		af.seoPages.loadItems(1);
	},
	loadItems: function(page, highlightID) {
		if (page) {
			af.seoPages.$parent.find('.page-input').val(page);
		}
		var params = {
				action: 'loadSeoPages',
				list_params: af.seoPages.$parent.find('.list-params, .list-param').serialize(),
			},
			response = function(r) {
				if (r.html) {
					af.seoPages.$parent.find('.dynamic-rows').removeClass('loading').html(r.html.items);
					af.seoPages.$parent.find('.pagination-form').replaceWith(r.html.pagination);
					if (highlightID) {
						af.seoPages.highligtSavedItem(highlightID);
					}
				}
			};
		af.seoPages.$parent.find('.dynamic-rows').addClass('loading');
		af.seoPages.ajaxRequest(params, response);
	},
	highligtSavedItem: function(id) {
		var $item = af.seoPages.$parent.find('.sp-item[data-id="'+id+'"]');
		$item.addClass('flashing');
		setTimeout(function(){
			$item.removeClass('flashing');
		}, 500);
	},
	prepareForm: function($form) {
		af.seoPages.displayCriteria($form.find('.t-value'));
		$form.on('click', '.spSave', function() {
			af.mce.updateTextareaValues($form);
			var params = {action: 'saveSeoPage', seopage_data: $form.serialize()},
				response = function(r) {
					if ('errors' in r) {
						af.displayError(r.errors, 'prepend', $form);
					} else if (r.saved) {
						var $item = af.seoPages.$parent.find('.sp-item[data-id="'+r.saved+'"]');
						if ($item.length) {
							if ('item_html' in r) {
								$item.replaceWith(r.item_html);
							}
							af.seoPages.highligtSavedItem(r.saved);
						} else {
							af.seoPages.loadItems(1, r.saved);
						}
						$.growl.notice({ title: '', message: af_txt.saved});
						af.popup.close();
					}
				};
			$form.find('.thrown-errors').remove();
			af.seoPages.ajaxRequest(params, response);
		});
		af.seoPages.tagifyCriteriaEvents($form);
	},
	tagifyCriteriaEvents: function($container, isFilter) {
		$container.on('focusin focusout', '.quickSearch', function(e) {
			let $parent = $(this).parent();
			$parent.toggleClass('qs-focus', e.type == 'focusin' || $parent.is(':hover'));
		}).on('click', '.showMoreQs', function(e) {
			$(this).closest('.qs-group').find('.cut').removeClass('cut');
			$(this).remove();
		}).on('keyup', '.quickSearch', function() {
			var $input = $(this),
				$parent = $input.parent();
			clearTimeout(af.seoPages.timer);
			af.seoPages.timer = setTimeout(function() {
				var params = {action: 'quickSearch', q: $input.val()},
					response = function(r) {
						let $resultsContainer = $('.qs-results').html(r.html);
						$parent.addClass('has-results');
						af.seoPages.getTagifyContainer($input).find('.t-item').each(function() {
							let selector = '.qs-value[data-identifier="'+$(this).data('identifier')+'"]';
							$resultsContainer.find(selector).addClass('blocked');
						});
					};
				if (params.q.length > 1) {
					af.seoPages.ajaxRequest(params, response);
				} else {
					$parent.removeClass('has-results').find('.qs-results').html('');
				}
			}, 300);
		}).on('click', '.qs-value', function() {
			if (!$(this).hasClass('blocked')) {
				let $tContainer = af.seoPages.getTagifyContainer($(this)),
					criterionHTML = af.seoPages.renderCriterion($(this).data('identifier'), {
						id: $(this).find('.qs-id').text(),
						name: $(this).find('.qs-name').text(),
						info: $(this).closest('.qs-group').find('.info').text(),
					});
				$tContainer.find('.quick-add').before(criterionHTML);
				af.seoPages.updateCriteriaValue($tContainer, isFilter);
				$(this).closest('.has-results').removeClass('has-results qs-focus')
				.find('.quickSearch').val('');
			}
		}).on('click', '.t-remove', function(e) {
			e.preventDefault();
			var $parent = $(this).parent(),
				$tContainer = af.seoPages.getTagifyContainer($parent),
				identifier = $parent.data('identifier');
			$parent.remove();
			$tContainer.parent().find('.qs-value[data-identifier="'+identifier+'"]').removeClass('blocked');
			af.seoPages.updateCriteriaValue($tContainer, isFilter);
		});
		if (isFilter) {
			$container.closest('table').on('click', '.sp-criterion-preview', function() {
				let identifier = $(this).data('identifier'),
					$tContainer = $container.find('.t-items');
				if (!$tContainer.find('[data-identifier="'+identifier+'"]').length) {
					$tContainer.find('.quick-add').before(af.seoPages.renderCriterion(identifier, {name: $(this).text()}));
					af.seoPages.updateCriteriaValue($tContainer, true);
				}
			});
		}
	},
	displayCriteria: function($input) {
		var params = {
				action: 'getCriteriaDataForDisplay',
				identifiers: $input.val(),
			},
			response = function(r) {
				if ('data' in r) {
					$quickAdd = af.seoPages.getTagifyContainer($input).find('.quick-add');
					$.each(r.data, function(identifier, cr) {
						$quickAdd.before(af.seoPages.renderCriterion(identifier, cr));
					});
					af.seoPages.displayPossibleDuplicates($input);
				}
			};
		af.seoPages.ajaxRequest(params, response);
	},
	displayPossibleDuplicates: function($input) {
		var $form = $input.closest('form'),
			params = {
				action: 'renderPossibleDuplicates',
				criteria: $input.val(),
				id_seopage: $form.find('input[name="id_seopage"]').val(),
			},
			response = function(r) {
				$form.find('.sp-duplicates').html(r.html);
			};
		af.seoPages.ajaxRequest(params, response);
	},
	renderCriterion: function(identifier, cr) {
		let html = '<span class="t-item type-'+identifier[0]+'" data-identifier="'+identifier+'">';
		if (cr.info) {
			html += '<span class="t-id"><span class="t-info">'+cr.info+'</span>'+cr.id+'</span>';
		}
		html += cr.name+' <a href="#" class="t-remove">&times;</a></span>';
		return html;
	},
	getTagifyContainer: function($el) {
		return $el.closest('.af-tagify').find('.t-items');
	},
	updateCriteriaValue: function($tContainer, isFilter) {
		let $input = $tContainer.parent().find('.t-value'),
			value = [];
		$tContainer.find('.t-item').each(function() {
			value.push($(this).data('identifier'));
		});
		$input.val(value.join('-'));
		if (isFilter) {
			af.seoPages.applyFilter($input);
		} else {
			af.seoPages.displayPossibleDuplicates($input);
		}
	},
	activateSitemapActions: function() {
		af.seoPages.bulkClickSitemaps = [];
		$('.updAllSitemaps').on('click', function() {
			var $i = $(this).find('i');
			if ($i.hasClass('icon-spin')) {
				$i.removeClass('icon-spin');
				af.seoPages.bulkClickSitemaps = [];
			} else {
				$i.addClass('icon-spin');
				$(this).closest('table').find('.updSitemap').each(function() {
					af.seoPages.bulkClickSitemaps.push($(this));
				});
				if (af.seoPages.bulkClickSitemaps.length) {
					af.seoPages.bulkClickSitemaps[0].click();
				}
			}
		});
		$('.updSitemap').on('click', function() {
			var $i = $(this).find('i'), $tr = $(this).closest('tr');
			if (!$i.hasClass('icon-spin')) {
				$i.addClass('icon-spin');
				var params = {action: 'updSitemap', identifier: $tr.data('id')},
					response = function(r) {
						$i.removeClass('icon-spin');
						$tr.find('.links-num').html(r.links_num).siblings('.date-mod').html(r.date_mod).addClass('flashing');
						setTimeout(function(){
							$tr.find('.date-mod').removeClass('flashing');
						}, 500);
						af.seoPages.bulkClickSitemaps.shift();
						if (af.seoPages.bulkClickSitemaps.length) {
							af.seoPages.bulkClickSitemaps[0].click();
						} else {
							$('.updAllSitemaps').find('i').removeClass('icon-spin');
						}
					},
					errorResponse = function() {
						$.growl.error({title: '', message: 'Error. Check console log'});
						$i.removeClass('icon-spin');
					};
				af.seoPages.ajaxRequest(params, response, errorResponse);
			}
		});
	},
	activateAjaxModals: function() {
		var html = '<a href="#" class="btn btn-default ajaxModal" data-action="viewLog"><i class="icon-list"></i></a>';
		$('.log-settings').find('.settings-input').prepend(html);
		$(document).on('click', '.ajaxModal', function() {
			af.popup.open();
			var params = {action: $(this).data('action')},
				response = function(r) {
					if ('title' in r && 'content' in r) {
						af.popup.fill(r.title, r.content);
					}
				};
			af.seoPages.ajaxRequest(params, response);
		});
	},
	ajaxRequest: function(params, response, errorResponse) {
		params.sp = 1;
		ajaxRequest(params, response, errorResponse);
	},
	timer: false,
}});

$(document).ready(function() {
	af.seoPages.init();
});
/* since 1.0.1 */
