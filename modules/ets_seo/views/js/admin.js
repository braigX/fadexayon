// noinspection EqualityComparisonWithCoercionJS
/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */

var etsSeoAdmin = {
  listControllersOverride: [
    'AdminCmsContent',
    'AdminMeta',
    'AdminCategories',
    'AdminManufacturers',
    'AdminSuppliers',
    'AdminProducts',
  ],
  getMetaCodeTemplate: function(is_title, is_snippet) {
    if (etsSeoAdmin.listControllersOverride.indexOf(etsSeoBo.currentController) < 0) {
      return '';
    }
    let html = '';
    if (typeof ETS_SEO_META_CODES !== 'undefined') {
      const meta_codes = is_title ? ETS_SEO_META_CODES.title : ETS_SEO_META_CODES.desc;
      html = '<div class="ets_seo_meta_code '+(is_snippet ? 'meta_code_snippet' : '')+'">';
      Object.keys(meta_codes).forEach(function(key) {
        html += '<button type="button" class="btn btn-default btn-add-met-code js-ets-seo-add-meta-code" data-code="' + meta_codes[key].code + '">';
        html += '<i class="fa fa-plus-circle"></i> ' + meta_codes[key].title;
        html += '</button>';
      });
      html += '</div>';
    }
    return html;
  },
  ajaxExportData: function(offset, type, count) {
    $.ajax({
      url: etsSeoBo.ajaxCtlUri,
      type: 'POST',
      data: {
        etsSeoExportData: 1,
        offset: offset,
        count: count,
        type: type,
      },
      dataType: 'json',
      beforeSend: function() {
        $('.js-ets-seo-export').addClass('active');
        $('.js-ets-seo-export').prop('disabled', true);
      },
      success: function(res) {
        if (res.success && res.continue_process) {
          etsSeoAdmin.ajaxExportData(res.offset, res.type, res.count);
          return false;
        }
        $('#configuration_form').append('<input type="hidden" name="etsSeoSubmitExport" value="1">');
        $('#configuration_form').submit();
      },
      complete: function() {
        $('.js-ets-seo-export').removeClass('active');
        $('.js-ets-seo-export').prop('disabled', false);
      },
      error: function(xhr) {
      },
    });
    return false;
  },
  changeDescRewriteRule: function($this) {
    if (ETS_SEO_LINK_REWRITE_RULES && ETS_SEO_DEFINED) {
      Object.keys(ETS_SEO_LINK_REWRITE_RULES).forEach(function(key) {
        const val = parseInt($this.val(), 10);
        var desc = val == 1 ? ETS_SEO_LINK_REWRITE_RULES[key]['desc_new_rule'] : ETS_SEO_LINK_REWRITE_RULES[key]['desc_rule'];
        const inputSelectors = [
          '#meta_settings_url_schema_form_' + key,
          '#meta_settings_form_url_schema_' + key,
          'input[name="PS_ROUTE_' + key + '"]'
        ];
        var $inputEl = null,
          $descEl = null;

        for (let i = 0; i < inputSelectors.length; i++) {
          $inputEl = $(inputSelectors[i]);
          if ($inputEl.length) {
            break;
          }
        }

        if (!$inputEl.length) {
          return;
        }

        $descEl = $inputEl.closest('.form-group').find('.form-text');
        if (!$descEl.leng) {
          $descEl = $inputEl.closest('.form-group').find('.help-block');
        }
        if (!$descEl.leng) {
          $descEl = $inputEl.parent().children('.form-text');
        }
        if (!$descEl.leng) {
          $descEl = $inputEl.siblings('.form-text');
        }

        if ($descEl && $descEl.length) {
          $descEl.html(desc);
        }
      });
    }
  },
  changeSiteOriginalOrPersonal: function(el) {
    if ($(el).val() == 'COMPANY') {
      $('#conf_id_ETS_SEO_SITE_PERSON_NAME').parent('.form-group').addClass('hide');
      $('#conf_id_ETS_SEO_SITE_PERSON_AVATAR').parent('.form-group').addClass('hide');

      $('#conf_id_ETS_SEO_SITE_ORIG_NAME').parent('.form-group').removeClass('hide');
      $('#conf_id_ETS_SEO_SITE_ORIG_LOGO').parent('.form-group').removeClass('hide');
    } else {
      $('#conf_id_ETS_SEO_SITE_PERSON_NAME').parent('.form-group').removeClass('hide');
      $('#conf_id_ETS_SEO_SITE_PERSON_AVATAR').parent('.form-group').removeClass('hide');

      $('#conf_id_ETS_SEO_SITE_ORIG_NAME').parent('.form-group').addClass('hide');
      $('#conf_id_ETS_SEO_SITE_ORIG_LOGO').parent('.form-group').addClass('hide');
    }
  },

  onChangeSitemapOptions: function() {
    let count = 0;
    const input = $('input[name="ETS_SEO_SITEMAP_OPTION[]"]');
    input.each(function() {
      if ($(this).is(':checked') && $(this).val() != 'all') {
        count++;
      }
    });
    if (count == (input.length - 1)) {
      $('input[name="ETS_SEO_SITEMAP_OPTION[]"][value="all"]').prop('checked', true);
    } else {
      $('input[name="ETS_SEO_SITEMAP_OPTION[]"][value="all"]').prop('checked', false);
    }
  },
  onChangeEnableRating: function() {
    const input = $('#ETS_SEO_RATING_ENABLED');
    if (input.val() == 2 || input.val() == 0) {
      $('.js-ets-seo-rating-field').closest('.form-group').addClass('hide');
    } else {
      $('.js-ets-seo-rating-field').closest('.form-group').removeClass('hide');
    }
  },
  onChangeRatingConfig: function() {
    const input = $('select[name=ets_seo_rating_enable]');
    if (input.val() == 2 || input.val() == 0) {
      $('.js-ets-seo-rating-config').addClass('hide');
    } else {
      $('.js-ets-seo-rating-config').removeClass('hide');
    }
  },
  showErrorRating: function(input, message) {
    if (input.next('.ets-rating-error').length) {
      input.next('.ets-rating-error').remove();
    }
    input.after('<p class="ets-rating-error">'+message+'</p>');
  },
  isFloat: function(str) {
    return !isNaN(str) && str.toString().indexOf('.') != -1;
  },
  isInt: function(str) {
    return !isNaN(str) && /^[0-9]+$/.test(str);
  },
  numberWithCommas: function(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
  },
  changeRatingStar: function() {
    const ratingEnable = $('select[name=ets_seo_rating_enable]').val();
    let avgRating = $('input[name=ets_seo_rating_average]').val();
    const ratingCount = $('input[name=ets_seo_rating_count]').val();
    let bgWidth = 0;
    if (ratingEnable == 1) {
      if ($('.snippet-preview--rating').hasClass('hide')) {
        $('.snippet-preview--rating').removeClass('hide');
      }
      if (!avgRating || avgRating == '0' || isNaN(avgRating) || parseFloat(avgRating) <= 0 || parseFloat(avgRating) > 5 || !ratingCount || ratingCount == '0' || isNaN(ratingCount) || !etsSeoAdmin.isInt(ratingCount) || parseInt(ratingCount) <= 0) {
        $('.snippet-preview--rating').addClass('hide');
        return;
      }
      if ($('.snippet-preview--rating .rating-info').hasClass('hide')) {
        $('.snippet-preview--rating .rating-info').removeClass('hide');
      }
      let textVote = ETS_SEO_MESSAGE.votes;
      if (etsSeoAdmin.isInt(ratingCount) && parseInt(ratingCount) < 2) {
        textVote = ETS_SEO_MESSAGE.vote;
      }
      $('.snippet-preview--rating .rating-info .text-vote').html(textVote);
      if (avgRating && !isNaN(avgRating)) {
        bgWidth = avgRating / 5 * 100;
        avgRating = parseFloat(avgRating).toFixed(1);
      }
      $('.snippet-preview--rating .rating-info .avg-rating').html(avgRating);
      $('.snippet-preview--rating .rating-info .rating-count').html(ratingCount ? etsSeoAdmin.numberWithCommas(ratingCount) : 0);

      $('.snippet-preview--rating .bg-star').css('width', bgWidth+'%');
    } else {
      if (!$('.snippet-preview--rating').hasClass('hide')) {
        $('.snippet-preview--rating').addClass('hide');
      }
      if (!$('.snippet-preview--rating .rating-info').hasClass('hide')) {
        $('.snippet-preview--rating .rating-info').addClass('hide');
      }
    }
  },
  validateFriendlyUrl: function(inputLinkRewrite) {
    const linkRewrites = [];
    Object.keys(ETS_SEO_LANGUAGES).forEach(function(key) {
      linkRewrites.push({id_lang: ETS_SEO_LANGUAGES[key], value: $(inputLinkRewrite+ETS_SEO_LANGUAGES[key]).val()});
    });
    $.ajax({
      url: etsSeoBo.ajaxCtlUri,
      type: 'POST',
      data: {validateLinkRewrite: 1,
        type: etsSeoBo.currentController,
        link_rewrites: linkRewrites,
        is_cms_category: ETS_SEO_IS_CMS_CATEGORY,
        id: ETS_SEO_DEFINED.id_current_page,
      },
      dataType: 'json',
      success: function(res) {
        if (!res.success) {
          $('#ajax_confirmation').next('.alert-danger').remove();
          $('#ajax_confirmation').after('<div class="alert alert-danger">'+res.error+'</div>');
          $(window).scrollTop(0);
        }
      },
    });
  },
  setTextCount: function(el, count) {
    el.find('.js-ets-seo-current-length').html(count);
  },
  changeTooltipContentProduct: function() {
    let content = $('#form_step5_meta_description').prev('label').attr('popover');
    if (content) {
      content = content.replace('160', '156');
      $('#form_step5_meta_description').prev('label').attr('popover', content);
      $('#form_step5_meta_description').prev('label').find('.help-box').attr('data-content', content);
    }
  },
  getTextCounter: function(text) {
    return '<small class="js-text-count form-text text-muted text-right"><em><span class="js-ets-seo-current-length">0</span> '+text+'</em></small>';
  },
  addTextCounter: function() {
    if (etsSeoBo.currentController == 'AdminProducts') {
      return;
    }
    $('input[id*="meta_title_"], input[id*="meta_page_title_"]').each(function() {
      const id = $(this).attr('id');
      if (id.indexOf('ets_seo_social') < 0) {
        const id_lang = etsSeo.getIdLang(id);
        if (!$(this).parent().find('.js-text-count').length && !$(this).parent().find('[id*="recommended_length_counter"]').length) {
          if ($(this).parent('.input-group').length) {
            $(this).parent('.input-group').after(etsSeoAdmin.getTextCounter(ETS_SEO_MESSAGE.meta_title_recommended));
            etsSeoAdmin.setTextCount($(this).parent('.input-group').next('.js-text-count'), $(this).val().length);
          } else {
            $(this).after(etsSeoAdmin.getTextCounter(ETS_SEO_MESSAGE.meta_title_recommended));
            etsSeoAdmin.setTextCount($(this).next('.js-text-count'), $(this).val().length);
          }
        }
      }
    });

    $('textarea[id*="meta_description_"], form input[id*="meta_description_"]').each(function() {
      const id = $(this).attr('id');
      if (id.indexOf('ets_seo_social') < 0) {
        const id_lang = etsSeo.getIdLang(id);
        if (!$(this).parent().find('.js-text-count').length && !$(this).parent().find('[id*="recommended_length_counter"]').length) {
          if (!$(this).parent().find('.js-text-count').length) {
            if ($(this).parent('.input-group').length) {
              $(this).parent('.input-group').after(etsSeoAdmin.getTextCounter(ETS_SEO_MESSAGE.meta_title_recommended));
              etsSeoAdmin.setTextCount($(this).parent('.input-group').next('.js-text-count'), $(this).val().length);
            } else {
              $(this).after(etsSeoAdmin.getTextCounter(ETS_SEO_MESSAGE.meta_desc_recommended));
              etsSeoAdmin.setTextCount($(this).next('.js-text-count'), $(this).val().length);
            }
          }
        }
      }
    });
  },
  initModalExplain: function() {
    const etsModalHtml = $('#boxModalEtsSeoTransExplain').html();
    $('body').append(etsModalHtml);
    $('#boxModalEtsSeoTransExplain').remove();

    $(document).on('click', '.js-ets-seo-show-explain-rule', function() {
      const rule = $(this).attr('data-rule') || '';
      const text = $(this).attr('data-text') || '';
      let content = $('#modalEtsSeoTransExplain .rule-msg.'+rule).html();
      content = content.replace(/\[page_title\]/gi, $('#modalEtsSeoTransExplain .rule-msg.'+rule).parent().attr('data-page-title'));
      $('#modalEtsSeoTransExplain .rule-msg.'+rule).html(content);
      $('#modalEtsSeoTransExplain .rule-msg:not(.hide)').addClass('hide');
      $('#modalEtsSeoTransExplain .rule-msg.'+rule).removeClass('hide');
      $('#modalEtsSeoTransExplain .ets-modal-title').html(text);
      $('#modalEtsSeoTransExplain:not(.ets-seo-d-flex)').addClass('show');
      return false;
    });
    $(document).on('click', '#modalEtsSeoTransExplain .ets-modal-close', function() {
      $('#modalEtsSeoTransExplain').removeClass('show');
    });
    $(document).keyup( function(e) {
      if (e.keyCode == 27) {
        $('#modalEtsSeoTransExplain').removeClass('show');
      }
    });

    window.onclick = function(event) {
      const etsModal = document.getElementById('modalEtsSeoTransExplain');
      if (event.target == etsModal) {
        $('#modalEtsSeoTransExplain').removeClass('show');
      }
    };
  },
  getLangIsoCodeById: function(id_lang) {
    for (const key in ETS_SEO_LANGUAGES) {
      if (ETS_SEO_LANGUAGES.hasOwnProperty(key) && (ETS_SEO_LANGUAGES[key] == id_lang)) {
        return key;
      }
    }
    return '';
  },
  ajaxXhrAnalysis: null,
  ajaxAnalysisPage: function(pages) {
    if (!pages || !pages.length) {
      return false;
    }
    this.ajaxXhrAnalysis = $.ajax({
      url: etsSeoBo.dashboardUri,
      type: 'POST',
      dataType: 'json',
      data: {
        etsSeoAnalysisPages: 1,
        dataPages: pages,
      },
      beforeSend: function() {

      },
      success: function(res) {
        if (res.success) {
          if (res.stop) {
            showSuccessMessage(res.message);
            $('#etsSeoModalManualAnalysis').modal('hide');
            window.location.reload();
            return false;
          }
          const scores = {};
          const dataPage = res.data.data;
          for (let i = 0; i < dataPage.length; i++) {
            scores[dataPage[i].id+'_'+dataPage[i].id_lang] = etsSeoAdmin.analysisPageManually(dataPage[i], res.data.page_type);
          }

          window.setTimeout(() => {
            etsSeoAdmin.ajaxPutDataAnalysis({page_type: res.data.page_type, score: scores, pages: pages, stop: res.data.stop}, dataPage.length);
          }, 200);
        } else {
          showErrorMessage(res.message);
          $('#etsSeoModalManualAnalysis').modal('hide');
        }
      },
      complete: function() {

      },
    });
  },
  ajaxPutDataAnalysis: function(scoreData, nbPageUpdated) {
    this.ajaxXhrAnalysis = $.ajax({
      url: etsSeoBo.dashboardUri,
      type: 'POST',
      dataType: 'json',
      data: {
        etsSeoSaveDataAnalysis: 1,
        scoreData: scoreData,
      },
      success: function(res) {
        if (res.success) {
          let nbUpdated = $('.js-ets-seo-div-analysis-data .nb_page_updated').attr('data-page');
          let totalPage = 0;
          $('#etsSeoModalManualAnalysis input[name="ets_seo_page[]"]:checked').each(function() {
            totalPage += parseInt($(this).attr('data-total-page'));
          });
          nbUpdated = parseInt(nbUpdated)+nbPageUpdated;
          const totalLeft = parseInt(totalPage) - nbUpdated;
          $('.js-ets-seo-div-analysis-data .nb_page_updated').html(nbUpdated);
          $('.js-ets-seo-div-analysis-data .nb_page_updated').attr('data-page', nbUpdated);
          $('.js-ets-seo-div-analysis-data .nb_page_left').attr('data-page', totalLeft);
          $('.js-ets-seo-div-analysis-data .nb_page_left').html(totalLeft);
          if (!res.stop && res.pages.length) {
            window.setTimeout(()=>{
              etsSeoAdmin.ajaxAnalysisPage(res.pages);
            }, 200);
          } else {
            showSuccessMessage(res.message);
            $('#etsSeoModalManualAnalysis').modal('hide');
            window.location.reload();
          }
        } else {
          showErrorMessage(res.message);
        }
      },
    });
  },
  analysisPageManually: function(dataPage, pageType) {
    this.resetAnalysisScore();
    const idLang = dataPage.id_lang;
    let content = (dataPage.description_short ? dataPage.description_short : '')+(dataPage.description ? dataPage.description : '');
    let text = content.replace(/<\/?[a-z][^>]*?>/gi, '\n');
    let metaTitle = dataPage.meta_title || dataPage.name;
    let metaDesc = dataPage.meta_description || dataPage.description_short || dataPage.description || '';
    const linkRewrite = dataPage.link_rewrite || '';
    let params = {
      name: dataPage.name,
      price: '10',
      category: 'category',
      description: dataPage.description_short,
      description2: dataPage.description,
      discount_price: '10',
      brand: 'brand',
    }
    content = etsSeo.getSeoMetaData(content, false, params);
    metaTitle = etsSeo.getSeoMetaData(metaTitle, false, params);
    metaDesc = etsSeo.getSeoMetaData(metaDesc, false, params);
    text = etsSeo.getSeoMetaData(text, false, params);
    // Basic rules (always analyze)
    etsSeo.rules.pageTitleLength(idLang, dataPage.name);
    etsSeo.rules.internalLink(idLang, content);
    etsSeo.rules.outboundLink(idLang, content);
    etsSeo.rules.singleH1(idLang, content, pageType);
    etsSeo.rules.seoTitleWidth(idLang, etsSeo.getSeoMetaData(metaTitle, true, params));
    etsSeo.rules.textLength(idLang, text, '', pageType);
    etsSeo.rules.metaDescLength(idLang, metaDesc);

    // Readability rules (always analyze)
    etsSeo.readability.consecutiveSentences(idLang, content);
    etsSeo.readability.fleschReadingEase(idLang, content);
    etsSeo.readability.notEnoughContent(idLang, content);
    etsSeo.readability.paragraphLength(idLang, content);
    etsSeo.readability.passive_voice(idLang, content);
    etsSeo.readability.sentenceLength(idLang, content);
    etsSeo.readability.subheadingDistribution(idLang, content);
    etsSeo.readability.transitionWords(idLang, content);

    // Keyphrase rules - only analyze if key_phrase exists
    const key_phrase = dataPage.key_phrase || '';
    etsSeo.rules.imageAltAttribute(idLang,key_phrase, content);
    etsSeo.rules.keyPhraseLength(idLang, key_phrase);
    etsSeo.rules.keyPhraseInTitle(idLang, key_phrase, metaTitle);
    etsSeo.rules.keyphraseInMetaDesc(idLang, key_phrase, metaDesc);
    etsSeo.rules.keyphraseInIntroduction(idLang, key_phrase, dataPage.description_short || '');
    etsSeo.rules.keyphraseDensity(idLang, key_phrase, text);
    etsSeo.rules.keyphraseInSubheading(idLang, key_phrase, content);
    etsSeo.rules.keyphraseInSlug(idLang, key_phrase, linkRewrite);

    // Get page title for keyphrase_in_page_title
    const prefix = etsSeo.prefixInput();
    let pageTitle = dataPage.name;
    etsSeo.rules.keyPhraseInPageTitle(idLang, key_phrase,etsSeo.getSeoMetaData(pageTitle, true, params) );

    // Minor keyphrase rules - only analyze if minor_key_phrase exists
    const minor_key_phrase = dataPage.minor_key_phrase || '';
    let minorKeyphraseArray = [];
    try {
      // Try parsing as JSON first
      const parsed = JSON.parse(minor_key_phrase);
      if (Array.isArray(parsed)) {
        minorKeyphraseArray = parsed.map(function(item) {
          return typeof item === 'object' && item.value ? item.value : item;
        });
      } else {
        minorKeyphraseArray = [parsed];
      }
    } catch (e) {
      // If not JSON, treat as comma-separated string
      minorKeyphraseArray = minor_key_phrase.split(',').map(function(item) {
        return item.trim();
      }).filter(function(item) {
        return item.length > 0;
      });
    }

    etsSeo.rules.minorKeyphraseLength(idLang, minorKeyphraseArray);
    etsSeo.rules.minorKeyphraseInContent(idLang, minorKeyphraseArray, text);
    etsSeo.rules.minorKeyphraseInMetaTitle(idLang, minorKeyphraseArray, metaTitle);
    etsSeo.rules.minorKeyphraseInMetaDesc(idLang, minorKeyphraseArray, metaDesc);
    etsSeo.rules.minorKeyphraseInPageTitle(idLang, minorKeyphraseArray, metaTitle);
    
    // Calculate total scores
    const score = {
      'readability': 0,
      'seo': 0,
    };
    
    // Build score_detail with individual rule scores
    const score_detail = {
      'seo': {},
      'readability': {}
    };
    
    // Calculate SEO score - exclude -999 (N/A) scores
    Object.keys(etsSeo.seo_score).forEach(function(key) {
      const ruleScore = etsSeo.seo_score[key][idLang] || 0;
      score_detail.seo[key] = ruleScore;
      // Skip N/A scores (-999) - they should not be counted in total
      if (parseInt(ruleScore) !== -999) {
        score.seo += parseInt(ruleScore);
      }
    });
    
    // Calculate Readability score - exclude -999 (N/A) scores
    Object.keys(etsSeo.readability_score).forEach(function(key) {
      const ruleScore = etsSeo.readability_score[key][idLang] || 0;
      score_detail.readability[key] = ruleScore;
      // Skip N/A scores (-999) - they should not be counted in total
      if (parseInt(ruleScore) !== -999) {
        score.readability += parseInt(ruleScore);
      }
    });
    
    // Build content_analysis
    const content_analysis = etsSeo.content_analysis[idLang] || {};

    return {
      id_lang: idLang,
      id: dataPage.id,
      score: score,
      score_detail: score_detail,
      content_analysis: content_analysis,
    };
  },
  resetAnalysisScore: function() {
    Object.keys(etsSeo.seo_score).forEach(function(key) {
      Object.keys(ETS_SEO_LANGUAGES).forEach(function(isoCode) {
        etsSeo.seo_score[key][ETS_SEO_LANGUAGES[isoCode]] = 0;
      });
    });
    Object.keys(etsSeo.readability_score).forEach(function(key) {
      Object.keys(ETS_SEO_LANGUAGES).forEach(function(isoCode) {
        etsSeo.readability_score[key][ETS_SEO_LANGUAGES[isoCode]] = 0;
      });
    });
  },
};

(function($) {
  if (etsSeoBo && !etsSeoBo.isEnable) {
    return;
  }
  $(document).on('change', 'input[name^="ets_seo_social_input_img"]', function() {
    const formData = new FormData();
    const id_lang = $(this).attr('data-idlang');

    formData.append('image', $(this)[0].files[0]);
    formData.append('etsSeoUploadSocialImage', 1);
    formData.append('id', ETS_SEO_DEFINED.id_current_page);
    formData.append('page_type', etsSeoBo.currentController);
    formData.append('id_lang', id_lang);
    formData.append('old_image', $(this).next('input[name="ets_seo_social_img[' + id_lang + ']"]').val());

    const $this = $(this);
    $.ajax({
      url: etsSeoBo.ajaxCtlUri,
      type: 'POST',
      data: formData,
      dataType: 'json',
      contentType: false,
      processData: false,
      success: function(res) {
        if (res.success) {
          $('.ets-seo-social-tab .img-preview-'+id_lang).removeClass('hide');
          $('.ets-seo-social-tab .img-preview-'+id_lang+' img').attr('src', res.image);
          $('input[name="ets_seo_social_img[' + id_lang + ']"]').val(res.image);
        } else {
          if (res.message) {
            showErrorMessage(res.message);
          }
        }
      },
    });
  });

  $(document).on('click', '.ets-seo-social-tab .remove-img', function() {
    if (!confirm(ets_seo_confirm_delete_image)) {
      return;
    }
    if ($(this).hasClass('active')) {
      return;
    }
    const img_path = $(this).next('img').attr('src');
    $(this).addClass('active');
    const $this = $(this);
    const id_lang = $(this).attr('data-idlang');
    $.ajax({
      url: etsSeoBo.ajaxCtlUri,
      type: 'POST',
      data: {
        etsSeoDeleteSocialImg: 1,
        img_path: img_path,
        id: ETS_SEO_DEFINED.id_current_page,
        controller_type: etsSeoBo.currentController,
        is_cms_category: ETS_SEO_IS_CMS_CATEGORY,
      },
      dataType: 'json',
      success: function(res) {
        if (res.success) {
          $this.parent('.img-preview').addClass('hide');
          $this.next('img').attr('src', '');
          $('input[name="ets_seo_social_input_img_'+id_lang+'"]').val('');
        }
      },
      complete: function() {
        $this.removeClass('active');
      },
    });
  });
  $(document).on('click', '.ets-seo-img-logo .remove-logo', function() {
    if (!confirm(ets_seo_confirm_delete_image)) {
      return;
    }

    if ($(this).hasClass('active')) {
      return;
    }
    const img_path = $(this).next('img').attr('src');
    const config_name = $(this).attr('data-name');
    const id = $(this).attr('data-name');
    if ($(this).attr('data-preview')) {
      $(this).parent('.ets-seo-img-logo').html('');
      $('#'+id+'-name').val('');
      return;
    }
    $(this).addClass('active');
    const $this = $(this);
    $.ajax({
      url: etsSeoBo.ajaxCtlUri,
      type: 'POST',
      data: {
        etsSeoDeleteLogoImg: 1,
        img_path: img_path,
        config_name: config_name,
      },
      dataType: 'json',
      success: function(res) {
        if (res.success) {
          $this.parent('.ets-seo-img-logo').html('');
          $('#'+id+'-name').val('');
        } else {
          alert(res.message);
        }
      },
      complete: function() {
        $this.removeClass('active');
      },
    });
  });

  $(document).on('change', '.js-ets-seo-checkall', function() {
    const inputName = $(this).attr('name');
    if ($(this).is(':checked')) {
      $('input[name="'+inputName+'"]').prop('checked', true);
    } else {
      $('input[name="'+inputName+'"]').prop('checked', false);
    }
  });

  $(document).on('change', 'input[name="ETS_SEO_SITEMAP_OPTION[]"]', function() {
    etsSeoAdmin.onChangeSitemapOptions();
  });

  $(document).on('click', '.js-ets-seo-tab-customize', function() {
    const itemActive = $(this).attr('data-tab');
    $('.js-ets-seo-customize-item').removeClass('active');
    $('.'+itemActive).addClass('active');
  });

  if (etsSeoBo.currentController == 'AdminProducts') {
    $(document).on('click', '#form-nav a[href="#step1"]', function(e) {
      $('#form_content .summary-description-container ul.nav-tabs .nav-link').each(function() {
        $(this).removeClass('active');
      });
      $('#form_content .summary-description-container .tab-content .tab-pane.panel-default').each(function() {
        $(this).removeClass('active');
      });
      e.preventDefault();
    });
  }
  $(document).on('change', 'input[name=ETS_SEO_ENABLE_REMOVE_ID_IN_URL]', function() {
    etsSeoAdmin.changeDescRewriteRule($(this));
    if (typeof ETS_SEO_LINK_REWRITE_RULES !== 'undefined' && etsSeoBo.currentController == 'AdminMeta') {
      let inputRuleDefine = {};
      if (typeof etsSeoBo.is178 != 'undefined' && etsSeoBo.is178) {
        inputRuleDefine = {
          product: $('#meta_settings_url_schema_form_product_rule'),
          category: $('#meta_settings_url_schema_form_category_rule'),
          layered: $('#meta_settings_form_url_schema_layered_rule'),
          supplier: $('#meta_settings_url_schema_form_supplier_rule'),
          manufacturer: $('#meta_settings_url_schema_form_manufacturer_rule'),
          cms: $('#meta_settings_url_schema_form_cms_rule'),
          cms_category: $('#meta_settings_url_schema_form_cms_category_rule'),
          module: $('#meta_settings_url_schema_form_module'),
        };
      } else if (ETS_SEO_DEFINED.is175) {
        inputRuleDefine = {
          product: $('#meta_settings_form_url_schema_product_rule'),
          category: $('#meta_settings_form_url_schema_category_rule'),
          layered: $('#meta_settings_form_url_schema_layered_rule'),
          supplier: $('#meta_settings_form_url_schema_supplier_rule'),
          manufacturer: $('#meta_settings_form_url_schema_manufacturer_rule'),
          cms: $('#meta_settings_form_url_schema_cms_rule'),
          cms_category: $('#meta_settings_form_url_schema_cms_category_rule'),
          module: $('#meta_settings_form_url_schema_module'),
        };
      } else {
        inputRuleDefine = {
          product: $('input[name=PS_ROUTE_product_rule]'),
          category: $('input[name=PS_ROUTE_category_rule]'),
          layered: $('input[name=PS_ROUTE_layered_rule]'),
          supplier: $('input[name=PS_ROUTE_supplier_rule]'),
          manufacturer: $('input[name=PS_ROUTE_manufacturer_rule]'),
          cms: $('input[name=PS_ROUTE_cms_rule]'),
          cms_category: $('input[name=PS_ROUTE_cms_category_rule]'),
          module: $('input[name=PS_ROUTE_module]'),
        };
      }
      if ($(this).val() == 1) {
        Object.keys(inputRuleDefine).forEach(function(key) {
          const keyRule = key == 'module' ? key : key + '_rule';

          inputRuleDefine[key].val(ETS_SEO_LINK_REWRITE_RULES[keyRule].new_rule);
        });
      } else {
        Object.keys(inputRuleDefine).forEach(function(key) {
          const keyRule = key == 'module' ? key : key + '_rule';
          inputRuleDefine[key].val(ETS_SEO_LINK_REWRITE_RULES[keyRule].rule);
          inputRuleDefine[key].val(ETS_SEO_LINK_REWRITE_RULES[keyRule].rule);
        });
      }
    }
  });

  $(document).ready(function() {
    if (etsSeoBo.currentController === 'AdminProducts') {
      if (ETS_SEO_DEFINED.id_current_page) {
        $('label.px-0.form-control-label').removeAttr('popover');
      } else {
        const bulkSelectAll = $('input[id="bulk_action_select_all"]');
        (bulkSelectAll.length > 1) && bulkSelectAll.first().parents('.row').first().detach();
      }
    }
    if (etsSeoBo.currentController === 'AdminEtsSeoNotFoundUrl') {
      $.ajax({
        url: etsSeoBo.notFoundCtlUrl,
        type: 'POST',
        dataType: 'json',
        data: {
          getSettingBtn: 1,
        },
        success: (res) => {
          if (res && res.ok) {
            $('.panel-heading').append(res.html);
          }
        },
      });
      $(document).on('change', 'input[name="ETS_SEO_ENABLE_RECORD_404_REQUESTS"]', (e) => {
        $.ajax({
          url: etsSeoBo.notFoundCtlUrl,
          type: 'POST',
          dataType: 'json',
          data: {
            setSwitchValue: 1,
            value: $(e.target).val(),
          },
          success: () => {
            showSuccessMessage(etsSeoBo.transMsg.saveSuccessful);
          },
        });
      });
    }
    const getAnalysisMissingPageBtn = (type, text) => {
      type = type || etsSeoBo.analysisPageType;
      text = text || etsSeoBo.transMsg.analyzeMissingPage;
      return `<button type="button" class="btn btn-default js-ets-seo-get-modal-analysis" data-type="${type}">${text}</button>`;
    };
    if (etsSeoBo.hasOwnProperty('analysisPageType') && etsSeoBo.hasOwnProperty('dashboardUri')) {
      $.ajax({
        url: etsSeoBo.dashboardUri,
        type: 'POST',
        dataType: 'json',
        data: {
          getNoAnalysisPage: 1,
          pageType: etsSeoBo.analysisPageType,
        },
        success: (res) => {
          if (res.ok) {
            const titleRow = $('.title-row .toolbar-icons .wrapper');
            if (etsSeoBo.analysisPageType === 'cms') {
              if (res.data.cms.noanalysis > 0) {
                titleRow.prepend(getAnalysisMissingPageBtn('cms'));
              }
              if (res.data.cms_category.noanalysis > 0) {
                titleRow.prepend(getAnalysisMissingPageBtn('cms_category', res.data.categoryBtnText));
              }
            } else {
              if (res.data && res.data.noanalysis > 0) {
                titleRow.prepend(getAnalysisMissingPageBtn());
              }
            }
          }
        },
      });
    }
    $(document).on('click', '.js-ets-seo-get-modal-analysis', function(e) {
      e.preventDefault();
      if ($('#etsSeoModalManualAnalysis').length) {
        $('#etsSeoModalManualAnalysis').modal('hide');
        $('#etsSeoModalManualAnalysis').remove();
      }
      const $this = $(this);
      const contentDiv = $('#content.bootstrap');
      $.ajax({
        url: etsSeoBo.dashboardUri,
        type: 'GET',
        dataType: 'json',
        data: {
          etsSeoGetAnalysisModal: 1,
        },
        beforeSend: function() {
          $this.addClass('loading');
          $this.prop('disabled', true);
        },
        success: function(res) {
          if (res.success) {
            contentDiv.length ? contentDiv.append(res.modal_html) : $('body').append(res.modal_html);
            const modalDiv = $('#etsSeoModalManualAnalysis');
            const type = $this.data('type') || etsSeoBo.analysisPageType;
            modalDiv.modal({backdrop: 'static', keyboard: false});
            if (etsSeoBo.hasOwnProperty('analysisPageType')) {
              modalDiv.find(`input[name="ets_seo_page[]"][value="${type}"]`).prop('checked', true);
              $('#etsSeoModalManualAnalysis .js-ets-seo-analysis-manually').trigger('click');
            }
            modalDiv.modal('show');
          }
        },
        complete: function() {
          $this.removeClass('loading');
          $this.prop('disabled', false);
        },
      });
      return false;
    });

    $(document).on('click', '#etsSeoModalManualAnalysis input[name=ets_seo_page_all]', function() {
      if ($(this).is(':checked')) {
        $('#etsSeoModalManualAnalysis input[name="ets_seo_page[]"]').prop('checked', true);
      } else {
        $('#etsSeoModalManualAnalysis input[name="ets_seo_page[]"]').prop('checked', false);
      }
    });

    $(document).on('change', '#etsSeoModalManualAnalysis input[name="ets_seo_page[]"]', function() {
      if ($(this).is(':checked')) {
        if ($('#etsSeoModalManualAnalysis input[name="ets_seo_page[]"]').length === $('#etsSeoModalManualAnalysis input[name="ets_seo_page[]"]:checked').length) {
          $('#etsSeoModalManualAnalysis input[name=ets_seo_page_all]').prop('checked', true);
        } else {
          $('#etsSeoModalManualAnalysis input[name=ets_seo_page_all]').prop('checked', false);
        }
      } else {
        $('#etsSeoModalManualAnalysis input[name=ets_seo_page_all]').prop('checked', false);
      }
    });

    $(document).on('click', '#etsSeoModalManualAnalysis .js-ets-seo-analysis-manually', function(e) {
      e.preventDefault();
      const formData = $('#etsSeoModalManualAnalysis form').serializeArray();
      if (!formData.length) {
        alert(etsSeoBo.transMsg.analyzePageRequire);
        return;
      }
      const dataPages = [];
      $.each(formData, function(i, el) {
        if (el.name == 'ets_seo_page[]') {
          dataPages.push(el.value);
        }
      });
      etsSeoAdmin.ajaxAnalysisPage(dataPages);
      $('#etsSeoModalManualAnalysis .box-select-page').addClass('hide');
      $('#etsSeoModalManualAnalysis .box-analysis').removeClass('hide');
      let totalPage = 0;
      $('#etsSeoModalManualAnalysis input[name="ets_seo_page[]"]:checked').each(function() {
        totalPage += parseInt($(this).attr('data-total-page'));
      });
      $('#etsSeoModalManualAnalysis .js-ets-seo-div-analysis-data .nb_page_left').attr('data-page', totalPage);
      $('#etsSeoModalManualAnalysis .js-ets-seo-div-analysis-data .nb_page_left').html(totalPage);
      return false;
    });

    $(document).on('click', '#etsSeoModalManualAnalysis .js-ets-seo-cancel-analysis', function(e) {
      e.preventDefault();
      if (etsSeoAdmin.ajaxXhrAnalysis && etsSeoAdmin.ajaxXhrAnalysis.readyState != 4) {
        etsSeoAdmin.ajaxXhrAnalysis.abort();
      }
      if (etsSeoBo.hasOwnProperty('analysisPageType')) {
        window.location.reload();
      }
      return false;
    });
    $(document).on('click', 'input[name="ETS_SEO_CHAT_GPT_ENABLE"]', function(e) {
      if ($('#ETS_SEO_CHAT_GPT_ENABLE_on:checked').length > 0) {
        $('#conf_id_ETS_SEO_CHAT_GPT_API_TOKEN > label').addClass('required');
      }
      if ($('#ETS_SEO_CHAT_GPT_ENABLE_off:checked').length > 0) {
        $('#conf_id_ETS_SEO_CHAT_GPT_API_TOKEN > label').removeClass('required');
      }
    });
    if ( $('input[name="ETS_SEO_CHAT_GPT_ENABLE"]').length > 0 && $('#ETS_SEO_CHAT_GPT_ENABLE_on:checked').length > 0 ) {
      $('#conf_id_ETS_SEO_CHAT_GPT_API_TOKEN > label').addClass('required');
    }


    etsSeoAdmin.onChangeSitemapOptions();
    setTimeout(function() {
      $('.bootstrap>.alert.alert-success').hide();
      $('#ajax_confirmation').next('.alert.alert-success').hide();
    }, 5000);
    if (etsSeoBo.currentController === 'AdminEtsSeoChatGpt') {
      const _showErrorMessage = (msg) => {
        $.growl({title: '', style: 'error', duration: 30000, message: msg});
      };
      const tplTable = $('#tableGptTemplate');
      if (tplTable.length) {
        $('#configuration_fieldset_templates .panel-heading:first').append(tplTable.data('count')).append(tplTable.find('#panelActions').html());
      }
      const showContentHelpBlock = (e) => {
        e && e.target && console.info(e.target);
        let select = $('select[name="display_page"]');
        if (e && e.target) {
          select = $(e.target);
        } else {
          select = select.first();
        }
        let helpContent = etsSeoBo.transMsg.gptTplContentHelp[select.val()];
        const re = new RegExp(/{(\w+)}/g);
        const matched = helpContent.match(re);
        if (matched) {
          matched.forEach((v) => {
            helpContent = helpContent.replace(v, `<a class="click-to-copy-btn" data-code="${v}" title="Click to copy" href="javascript:void(0)">${v}</a>`);
          });
        }
        $('p[id^="contentHelpBlock_"]').html(helpContent);
      };
      const _resetFieldValues = (frm) => {
        frm.find('.gpt-reset-when-complete').each((i, el) => {
          el = $(el);
          el.val(el.data('defaultValue') ? el.data('defaultValue') : '');
        });
      };
      showContentHelpBlock();
      $(document).on('change', '.ets_seo_popup select[name="display_page"]', showContentHelpBlock);
      $(document).on('click', '.click-to-copy-btn', (e) => {
        const clickedElem = $(e.target);
        etsHelper.copyTextToClipboard(
            clickedElem.data('code'),
            () => showSuccessMessage('Copied'),
            () => showErrorMessage(etsSeoBo.transMsg.anErrorOccur),
        );
      });
      $(document).on('click', '#configuration_fieldset_templates .btn-new-item', (e) => {
        $('.ets_seo_popup').removeClass('show');
        const addDiv = $('#gptTemplateAddDiv');
        addDiv.find('select[name="display_page"]').trigger('change');
        addDiv.find('.ets_seo_popup').addClass('show');
        e.preventDefault();
      });
      let isGptTemplateAjaxRunning = false;
      $(document).on('click', '.ets_seo_popup button[name="saveTemplateGPT"]', (e) => {
        e.preventDefault();
        if (isGptTemplateAjaxRunning) {
          return;
        }
        const clickedElem = $(e.target);
        const frm = clickedElem.parents('form#gptTemplateAddFrm');
        const idTpl = frm.find('input[name="id_ets_seo_gpt_template"]').val();
        const listTpl = $('#listGptTemplate');
        const totalTpl = $('#gptTemplateCount');
        const isEdit = Boolean(idTpl > 0);
        $.ajax({
          url: etsSeoBo.chatGptAdminUrl,
          type: 'POST',
          dataType: 'json',
          data: frm.serialize(),
          beforeSend: () => {
            isGptTemplateAjaxRunning = true;
          },
          success: (res) => {
            isGptTemplateAjaxRunning = false;
            if (res.ok) {
              showSuccessMessage(res.message);
              totalTpl.html(res.totalRecords);
              if (!isEdit) {
                _resetFieldValues(frm);
                listTpl.append(res.html);
              } else {
                const html = $($.parseHTML(res.html));
                $(`#gptTplId${idTpl}`).html(html.html());
              }
              $('.ets_seo_popup').removeClass('show');
            } else {
              _showErrorMessage(res.message);
              if (res.errors && res.errors.length > 1) {
                res.errors.forEach((v, i) => i > 0 && _showErrorMessage(v));
              }
            }
          },
          error: () => {
            _showErrorMessage(etsSeoBo.transMsg.anErrorOccur);
            isGptTemplateAjaxRunning = false;
          },
        });
      });
      $(document).on('click', '#listGptTemplate .btn-delete-item', function(e) {
        e.preventDefault();
        if (isGptTemplateAjaxRunning) {
          return;
        }
        const clickedElem = $(this);
        if (!confirm(clickedElem.data('confirm'))) {
          return;
        }
        const totalTpl = $('#gptTemplateCount');
        const idTpl = clickedElem.data('id');
        $.ajax({
          url: etsSeoBo.chatGptAdminUrl,
          type: 'POST',
          dataType: 'json',
          data: {
            deleteGptTemplate: 1,
            id: idTpl,
          },
          beforeSend: () => {
            isGptTemplateAjaxRunning = true;
          },
          success: (res) => {
            isGptTemplateAjaxRunning = false;
            if (res.ok) {
              showSuccessMessage(res.message);
              totalTpl.html(res.totalRecords);
              $(`#gptTplId${idTpl}`).detach();
            } else {
              _showErrorMessage(res.message);
              if (res.errors && res.errors.length > 1) {
                res.errors.forEach((v, i) => i > 0 && _showErrorMessage(v));
              }
            }
          },
          error: () => {
            _showErrorMessage(etsSeoBo.transMsg.anErrorOccur);
            isGptTemplateAjaxRunning = false;
          },
        });
      });
      $(document).on('click', '#listGptTemplate .btn-edit-item', function(e) {
        e.preventDefault();
        if (isGptTemplateAjaxRunning) {
          return;
        }
        const editDiv = $('#gptTemplateEditDiv');
        const clickedElem = $(this);
        $.ajax({
          url: etsSeoBo.chatGptAdminUrl,
          type: 'POST',
          dataType: 'json',
          data: {
            getEditFrm: 1,
            id: clickedElem.data('id'),
          },
          beforeSend: () => {
            isGptTemplateAjaxRunning = true;
          },
          success: (res) => {
            isGptTemplateAjaxRunning = false;
            if (res.ok) {
              editDiv.html(res.html);
              hideOtherLanguage(etsSeoBo.currentActiveLangId);
              editDiv.find('select[name="display_page"]').trigger('change');
              editDiv.find('.ets_seo_popup').addClass('show');
            } else {
              _showErrorMessage(res.message);
              if (res.errors && res.errors.length > 1) {
                res.errors.forEach((v, i) => i > 0 && _showErrorMessage(v));
              }
            }
          },
          error: () => {
            _showErrorMessage(etsSeoBo.transMsg.anErrorOccur);
            isGptTemplateAjaxRunning = false;
          },
        });
      });
      $(document).on('click', '.ets_seo_popup .close_popup, .ets_seo_popup .cancel_popup', (e) => {
        e.preventDefault();
        const clickedElem = $(e.target);
        const popupDiv = clickedElem.parents('.ets_seo_popup').first();
        popupDiv.removeClass('show');
        _resetFieldValues(popupDiv.find('form#gptTemplateAddFrm').first());
      });
    }
    if (etsSeoBo.currentController === 'AdminCmsContent') {
      $('label[for="cms_page_seo_preview"]').parent('.form-group').hide();
      if (etsSeoBo.isEditingCms) {
        let hasWrap = false;
        $('#cms_page_meta_title, #cms_page_meta_description, #cms_page_meta_keyword, #cms_page_friendly_url').each((i, el) => {
          el = $(el);
          let parent = el.parents('.form-group.row').first();
          if (!hasWrap) {
            parent.wrap('<div class="ets-seo-meta-tab card ets_seotop1_step_seo"><div class="meta-content card-block"></div></div>');
            parent = $('.ets-seo-meta-tab');
          }
          const prependSelector = hasWrap ? '#category-seo-setting .seo-setting-tab .ets-seo-meta-tab .meta-content' : '#category-seo-setting .seo-setting-tab .ets_seotop1_step_seo:first';
          hasWrap ? parent.appendTo(prependSelector) : parent.prependTo(prependSelector);
          hasWrap = true;
        });
        $('.ets-seo-meta-tab').prepend(`<div class="card-header"><h2 class="ets-seo-heading-analysis card-title">${etsSeoBo.transMsg.searchEngineOptimize}</h2><p class="subtitle">${etsSeoBo.transMsg.searchEngineOptimizeHelp}</p></div>`);
        window.setTimeout(() => $('#serp').parents('.form-group.row').first().hide(), 250);
      }
    }
    if (etsSeoBo.currentController === 'AdminMeta') {
      const updateRmIdAttrInput = function() {
        const checkedRemoveAttr = $('input[name="ETS_SEO_ENABLE_REMOVE_ATTR_ALIAS"]:checked');
        const divRmIdAttrInputs = $('#removeIdAttrAliasInputs');
        const rmAttrIdInputs = $('input[name="ETS_SEO_ENABLE_REMOVE_ATTR_ALIAS"]');
        if (checkedRemoveAttr.val() === '1') {
          divRmIdAttrInputs.hide();
          rmAttrIdInputs.prop('disable', true);
        } else {
          divRmIdAttrInputs.show();
          rmAttrIdInputs.prop('disable', false);
        }
      };
      updateRmIdAttrInput();
      $(document).on('change', 'input[name="ETS_SEO_ENABLE_REMOVE_ATTR_ALIAS"]', updateRmIdAttrInput);
    }
    const activeTabWithError = function() {
      const tabPanel = $('[role="tabpanel"]');
      const invalidInput = $('.invalid-feedback-container');
      if (!tabPanel.length || !invalidInput.length) {
        return;
      }
      tabPanel.removeClass('active').removeClass('show');
      const parentTab = invalidInput.first().parents('[role="tabpanel"]');
      $(`#${parentTab.attr('aria-labelledby')}`).trigger('click');
      parentTab.addClass('show').addClass('active');
      $([document.documentElement, document.body]).animate({
        scrollTop: invalidInput.first().offset().top - 150,
      }, 500);
    };
    activeTabWithError();
  });

  $(window).on('load', function() {
    if ($('.js-current-length').length) {
      $('.js-current-length').each(function() {
        if ($(this).next('span').html() == '70') {
          $(this).next('span').html('60');
        }
        if ($(this).next('span').html() == '160') {
          $(this).next('span').html('156');
        }
      });
    }
    $('.maxLength .currentLength').each(function() {
      if ($(this).next('span').html() == '70') {
        $(this).next('span').html('60');
      }
      if ($(this).next('span').html() == '160') {
        $(this).next('span').html('156');
      }
    });
    if ($('#form-ets_seo_redirect .panel-heading .badge').html() == '0') {
      $('#form-ets_seo_redirect .panel-heading .badge').hide();
    }

    etsSeoAdmin.changeTooltipContentProduct();
    $('input[id*="meta_keyword_"], input[id*="meta_keywords_"]').each(function() {
      $(this).attr('placeholder', ETS_SEO_MESSAGE.add_keyword);
      const idInput = $(this).attr('id');
      $('#'+idInput+'-tokenfield').attr('placeholder', ETS_SEO_MESSAGE.add_keyword);
      if ($(this).next('.tagify-container').length) {
        $(this).next('.tagify-container').find('input').attr('placeholder', ETS_SEO_MESSAGE.add_keyword);
      }
    });

    setTimeout(function() {
      etsSeoAdmin.changeDescRewriteRule($('input[name=ETS_SEO_ENABLE_REMOVE_ID_IN_URL]:checked'));

      if (jQuery().select2) {
        $('.js-ets-seo-select2').select2();
      }
      $('input[id*="meta_title_"], input[id*="meta_page_title_"], textarea[id*="meta_description_"], form input[id*="meta_description_"], form#meta_form input[id*="title_"], form#meta_form input[id*="description_"], input[id*="ets_seo_meta_title_"], textarea[id*="ets_seo_meta_description_"], input[name^="ets_seo_social_title"], textarea[name^="ets_seo_social_desc"]').each(function(_i, el) {
        const isSnippet = $(this).attr('id').indexOf('ets_seo_') < 0 ? false : true;
        const meta_codes = $(this).attr('id').indexOf('title') < 0 ? etsSeoAdmin.getMetaCodeTemplate(false, isSnippet) : etsSeoAdmin.getMetaCodeTemplate(true, isSnippet);
        if ($(this).closest('.input-group').length) {
          $(this).closest('.input-group').next('.ets_seo_meta_code').remove();
          $(this).closest('.input-group').after(meta_codes);
        } else {
          $(this).next('.ets_seo_meta_code').remove();
          $(this).after(meta_codes);
        }
      });
      $(document).trigger('metaShortCodesPlaced');
      $('input[id^=ets_seo_meta_title], textarea[id^=ets_seo_meta_description]').each(function() {
        const count = $(this).val().length;
        etsSeoAdmin.setTextCount($(this).next('.js-text-count'), count);
      });
      etsSeoAdmin.addTextCounter();
    }, 500);
  });

  $(document).on('change', '#ETS_SEO_SITE_OF_PERSON_OR_COMP', function() {
    etsSeoAdmin.changeSiteOriginalOrPersonal(this);
  });

  $(document).on('change', '#ETS_SEO_RATING_ENABLED', function() {
    etsSeoAdmin.onChangeEnableRating();
  });
  $(document).on('change', 'select[name=ets_seo_rating_enable]', function() {
    etsSeoAdmin.onChangeRatingConfig();
    etsSeoAdmin.changeRatingStar();
  });

  $(document).on('change', 'input[name=ets_seo_rating_average]', function() {
    etsSeoAdmin.changeRatingStar();
  });
  $(document).on('change', 'input[name=ets_seo_rating_count]', function() {
    etsSeoAdmin.changeRatingStar();
  });

  $(document).on('keyup', 'input[name^=ets_seo_meta_title], [id*="meta_title_"]', function() {
    const $this = $(this);
    const count = $this.val().length;
    if ($this.next('.js-text-count').length) {
      etsSeoAdmin.setTextCount($(this).next('.js-text-count'), count);
    }
  });
  $(document).on('keyup', 'textarea[name^=ets_seo_meta_desc], input[id*="meta_description_"]', function() {
    const $this = $(this);
    const count = $this.val().length;
    if ($this.next('.js-text-count').length) {
      etsSeoAdmin.setTextCount($(this).next('.js-text-count'), count);
    }
  });

  $(document).on('change', '#ETS_SEO_SITE_PERSON_AVATAR, #ETS_SEO_SITE_ORIG_LOGO, #ETS_SEO_FACEBOOK_DEFULT_IMG_URL', function() {
    const fileExtension = ['jpeg', 'jpg', 'png', 'gif'];
    if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) != -1) {
      const id = $(this).attr('id');
      const reader = new FileReader();
      reader.onload = function(e) {
        const imgOnErrorDiv = $('.ets-seo-logo-on-error');
        if (imgOnErrorDiv.length) {
          imgOnErrorDiv.hasClass('alert-danger') && imgOnErrorDiv.removeClass('alert-danger').addClass('alert-info');
          imgOnErrorDiv.html(`${etsSeoBo.transMsg.saveToFinishUpload}`);
        }
        const imgHtml = '<span class="remove-logo" data-preview="1" data-name="'+id+'" title="Delete"><i class="fa fa-close"></i></span>' + '<img src="'+e.target.result+'">';
        $('#conf_id_'+id+' .ets-seo-img-logo').html(imgHtml);
      };

      // read the image file as a data URL.
      reader.readAsDataURL(this.files[0]);
    } else {
      alert(text_image_not_valid);
    }
  });

  if (etsSeoBo.currentController == 'AdminProducts') {
    $(document).on('click', 'form input[type=submit]', function() {
    });
  }

  $(document).ready(function() {
    if($('#product_catalog_category_tree_filter').length)
    {
      $.ajax({
        url: '',
        data: 'updateNewFeatureFlag&ajax=1',
        type: 'post',
        dataType: 'json',
        success: function(json){

        },
        error: function(xhr, status, error)
        {

        }
      });

    }
    $('table .date_range input[type="text"]').attr('autocomplete', 'off');
    if (!$('.ets_seo_extra_tabs').next('#fieldset_0').children('.panel-heading').length) {
      $('.ets_seo_extra_tabs').next('#fieldset_0').prepend('<div class="panel-heading">&nbsp;</div>');
    }
    $('.ets-seo-file-input-bo').each(function() {
      const value = $(this).attr('data-value');
      if (value) {
        $(this).find('input[type="text"]').val(value);
      }
    });
    etsSeoAdmin.initModalExplain();
    $('[data-toggle="tooltip"]').tooltip();
    $('[data-toggle="popover"]').popover();
    etsSeoAdmin.changeSiteOriginalOrPersonal('#ETS_SEO_SITE_OF_PERSON_OR_COMP');
    etsSeoAdmin.onChangeEnableRating();
    etsSeoAdmin.onChangeRatingConfig();

    if (etsSeoBo.currentController == 'AdminProducts' || etsSeoBo.currentController == 'AdminMeta' || etsSeoBo.currentController == 'AdminCmsContent') {
      $(document).on('click', 'form.product-page input[type=submit], button[type=submit], form[name=meta] button:last-child', function() {
        $error = false;
        $('.ets-rating-error').remove();
        if ($('select[name=ets_seo_rating_enable]').length) {
          if ($('select[name=ets_seo_rating_enable]').val() == 1) {
            const ratingAvg = $('input[name=ets_seo_rating_average]');
            const ratingCount = $('input[name=ets_seo_rating_count]');
            const bestRating = $('input[name=ets_seo_rating_best]');
            const worstRating = $('input[name=ets_seo_rating_worst]');
            if (!ratingAvg.val()) {
              etsSeoAdmin.showErrorRating(ratingAvg, ETS_SEO_MESSAGE['rating_avg_required']);
              $error = true;
            } else if (isNaN(ratingAvg.val()) || parseFloat(ratingAvg.val()) <= 0 || parseFloat(ratingAvg.val()) > 5) {
              etsSeoAdmin.showErrorRating(ratingAvg, ETS_SEO_MESSAGE['rating_avg_decimal']);
              $error = true;
            } else {
              if (bestRating.val() != '') {
                if (!etsSeoAdmin.isInt(bestRating.val()) || parseInt(bestRating.val()) < parseFloat(ratingAvg.val()) || parseInt(bestRating.val()) > 5) {
                  etsSeoAdmin.showErrorRating(bestRating, ETS_SEO_MESSAGE['best_rating_int']);
                  $error = true;
                }
              }
              if (worstRating.val() != '') {
                if (!etsSeoAdmin.isInt(worstRating.val()) || parseInt(worstRating.val()) > parseFloat(worstRating.val()) || parseInt(worstRating.val()) <= 0) {
                  etsSeoAdmin.showErrorRating(worstRating, ETS_SEO_MESSAGE['worst_rating_int']);
                  $error = true;
                }
              }
            }

            if (!ratingCount.val()) {
              etsSeoAdmin.showErrorRating(ratingCount, ETS_SEO_MESSAGE['rating_count_required']);
              $error = true;
            } else if (!etsSeoAdmin.isInt(ratingCount.val()) || parseInt(ratingCount.val()) <= 0) {
              etsSeoAdmin.showErrorRating(ratingCount, ETS_SEO_MESSAGE['rating_count_invalid']);
              $error = true;
            }
          }
        }
        return !$error;
      });
    }
  });
})(jQuery);
function etsSeoLogoError(img) {
  const divAlert = $(`<div class="alert alert-danger ets-seo-logo-on-error">${etsSeoBo.transMsg.imageDoesNotExistOnServer}</div>`);
  $(img).parent().before(divAlert);
  const name = $(img).attr('data-name');
  $(`#${name}-name`).val('');
}
$(document).ready(function() {
  var space_header_top = $('.header-toolbar').outerHeight();
  if ( $('#header_infos').length > 0 ){
    var space_nav_top =$('#header_infos').outerHeight() + space_header_top;
  }else{
    var space_nav_top = space_header_top;
  }
  $('.ets_seo_menu').css('top',space_nav_top);
  setMore_menu();
  $(window).load(function() {
    setMore_menu();
  });
  $(window).resize(function() {
    setMore_menu();
    $(".ets_seo_menu li.hide_more").removeClass('show_hover');
  });
  $('.ets_seo_menu li.more_tab').on('click', function (e) {
    $(".ets_seo_menu li.hide_more").toggleClass('show_hover');
  });
  $(document).mouseup(function (e) {
    var confirm_popup = $('.ets_seo_menu li.hide_more');
    if (!confirm_popup.is(e.target) && confirm_popup.has(e.target).length === 0) {
      $(".ets_seo_menu li.hide_more").removeClass('show_hover');
    }
  });

});
function setMore_menu() {
  var menu_width_box = $('.ets_seo_menu').width();
  var itemwidthlist = 0;
  $(".ets_seo_menu .nav.navbar-nav > li:not(.hide-on-md):not(.more_tab)").each(function () {
      var itemwidth = $(this).width();
      itemwidthlist = itemwidthlist + itemwidth;
      if (itemwidthlist > menu_width_box - 70 && itemwidthlist > 500) {
        $(this).addClass('hide_more');
      } else {
        $(this).removeClass('hide_more');
      }
  });
}
