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

var etsSeo = {
  debugPreview: true,
  productShortDescIdPrefixes: [
    'form_step1_description_short_',
    'product_description_description_short_',
  ],
  activeControllers: [
    'AdminCmsContent',
    'AdminMeta',
    'AdminCategories',
    'AdminManufacturers',
    'AdminSuppliers',
    'AdminProducts'],
  showMessageAnalysis: false,
  timeoutKeyup: 1000,
  timeoutTyping: null,
  timeoutTypingFocusKey: null,
  timeoutTypingMetaTitle: null,
  timeoutTypingMetaDesc: null,
  timeoutTypingFriendlyUrl: null,
  langSwitchTimer: null,
  analysisEnsuredLangs: [],
  analysisEnsureAttempts: {},
  initTabSeoRetryCount: 0,
  readabilityEnsureAttempts: {},
  // keyPhraseGlobal: {}, // DELME used anymore?
  minorKeyPhraseSucess: [],
  seo_score: {
    outbound_link: {},
    internal_link: {},
    text_length: {},
    keyphrase_length: {},
    keyphrase_in_subheading: {},
    keyphrase_in_title: {},
    keyphrase_in_page_title: {},
    keyphrase_in_intro: {},
    keyphrase_density: {},
    image_alt_attribute: {},
  seo_title_width: {},
    meta_description_length: {},
    keyphrase_in_meta_desc: {},
    keyphrase_in_slug: {},
    minor_keyphrase_in_content: {},
    minor_keyphrase_in_title: {},
    minor_keyphrase_in_desc: {},
    minor_keyphrase_in_page_title: {},
    minor_keyphrase_acceptance: {},
    single_h1: {},
    keyphrase_density_individual: {},
    minor_keyphrase_in_content_individual: {},
    minor_keyphrase_length: {},
  },
  readability_score: {
    not_enough_content: {},
    sentence_length: {},
    flesch_reading_ease: {},
    paragraph_length: {},
    consecutive_sentences: {},
    subheading_distribution: {},
    transition_words: {},
    passive_voice: {},
  },
  readability_problem: {
    not_enough_content: {},
    sentence_length: {},
    flesch_reading_ease: {},
    paragraph_length: {},
    consecutive_sentences: {},
    subheading_distribution: {},
    transition_words: {},
    passive_voice: {},
  },
  content_analysis: {},
  /**
   * @param {number} idLang
   * @param controller
   * @return {string}
   */
  getTextContentFromInputs: (idLang, controller) => {
    idLang = idLang || etsSeoBo.currentActiveLangId;
    controller = controller || etsSeoBo.currentController;
    const {content, short_desc} = etsSeo.prefixInput(controller);
    if (['AdminProducts','AdminCategories', 'AdminManufacturers'].indexOf(controller) !== -1) {
      let _content ='';
      if($(short_desc + idLang).length >0)
        _content += $(short_desc + idLang).val();
      if($(content + idLang).length>0)
        _content += $(content + idLang).val();
      return _content;
    }
    if (controller === 'AdminMeta') {
      return '';
    }
    return $(content + idLang).val();
  },
  getLinkRewriteFromInput: (idLang, controller) => {
    idLang = idLang || etsSeoBo.currentActiveLangId;
    controller = controller || etsSeoBo.currentController;
    const {link_rewrite: selector} = etsSeo.prefixInput(controller);
    return selector ? $(selector + idLang).val() : '';
  },
  getKeyPhraseFromInput: (idLang) => {
    idLang = idLang || etsSeoBo.currentActiveLangId;
    return $(`.ets_seotop1_step_seo .input-key-phrase-il-${idLang}`).val();
  },
  setKeyPhraseValueToInput: (value, idLang) => {
    idLang = idLang || etsSeoBo.currentActiveLangId;
    $(`.ets_seotop1_step_seo .input-key-phrase-il-${idLang}`).val(value);
  },
  initSeoScore: function(id) {
    const langId = id || etsSeoBo.currentActiveLangId;
    Object.keys(etsSeo.seo_score).forEach(function(k) {
      if (!etsSeo.seo_score[k].hasOwnProperty(langId)) {
        etsSeo.seo_score[k][langId] = 0;
      }
    });
    Object.keys(etsSeo.readability_score).forEach(function(k) {
      if (!etsSeo.readability_score[k].hasOwnProperty(langId)) {
        etsSeo.readability_score[k][langId] = 0;
      }
    });
  },
  parseProblemFromData: function() {
    Object.entries(ETS_SEO_SCORE_DATA).forEach(([k, item]) => {
      const contentAnalysis = item.content_analysis;
      if (contentAnalysis) {
        Object.keys(contentAnalysis).forEach((key) => {
          if (key.indexOf('_problem') !== -1) {
            const keyName = key.replace('_problem', '');
            this.readability_problem[keyName][item.id_lang] = contentAnalysis[key];
          }
        });
      }
    });
  },
  setSeoScore: function(rule, id_lang, score) {
    if (!Object.prototype.hasOwnProperty.call(etsSeo.seo_score, rule) || typeof etsSeo.seo_score[rule] !== 'object') {
      etsSeo.seo_score[rule] = {};
    }
    etsSeo.seo_score[rule][id_lang] = score;
    etsSeo.setScoreToFormData();
  },
  setReadabilityScore: function(rule, id_lang, score) {
    if (!Object.prototype.hasOwnProperty.call(etsSeo.readability_score, rule) || typeof etsSeo.readability_score[rule] !== 'object') {
      etsSeo.readability_score[rule] = {};
    }
    etsSeo.readability_score[rule][id_lang] = score;
    etsSeo.setScoreToFormData();
  },
  setReadabilityProblem: function(rule, id_lang, obj) {
    if (!Object.prototype.hasOwnProperty.call(etsSeo.readability_problem, rule) || typeof etsSeo.readability_problem[rule] !== 'object') {
      etsSeo.readability_problem[rule] = {};
    }
    etsSeo.readability_problem[rule][id_lang] = obj;
    if (typeof etsSeo.content_analysis[id_lang] === typeof undefined) {
      etsSeo.content_analysis[id_lang] = {};
    }
    etsSeo.content_analysis[id_lang][`${rule}_problem`] = obj;
    etsSeo.setScoreToFormData();
  },
  setScoreToFormData: function() {
    if (typeof ETS_SEO_LANGUAGES !== typeof undefined) {
      Object.keys(ETS_SEO_LANGUAGES).forEach((iso) => {
        const langId = ETS_SEO_LANGUAGES[iso];
        
        // Check if this language has existing data from database BEFORE initializing anything
        if (typeof ETS_SEO_SCORE_DATA !== 'object' || ETS_SEO_SCORE_DATA === null) {
          ETS_SEO_SCORE_DATA = {};
        }
        
        // Check if we have existing score data for this language (from database)
        const existingScoreData = ETS_SEO_SCORE_DATA[langId];
        const hasExistingData = existingScoreData && existingScoreData.score_analysis && 
                                (Object.keys(existingScoreData.score_analysis.seo_score || {}).length > 0 ||
                                 Object.keys(existingScoreData.score_analysis.readability_score || {}).length > 0);
        
        // Check if this language has any non-zero scores in current session
        let hasAnalyzedData = false;
        Object.keys(etsSeo.seo_score).forEach((rule) => {
          if (etsSeo.seo_score[rule] && etsSeo.seo_score[rule][langId] !== 0 && etsSeo.seo_score[rule][langId] !== undefined) {
            hasAnalyzedData = true;
          }
        });
        Object.keys(etsSeo.readability_score).forEach((rule) => {
          if (etsSeo.readability_score[rule] && etsSeo.readability_score[rule][langId] !== 0 && etsSeo.readability_score[rule][langId] !== undefined) {
            hasAnalyzedData = true;
          }
        });
        
        // Only initialize and update if:
        // 1. This language has existing data from database, OR
        // 2. This language has been analyzed in current session, OR
        // 3. This is the current active language
        const shouldProcess = hasExistingData || hasAnalyzedData || langId === etsSeoBo.currentActiveLangId;
        
        if (!shouldProcess) {
          // Skip this language - don't initialize scores to 0
          return;
        }
        
        // Initialize content_analysis if needed
        if (typeof etsSeo.content_analysis[langId] === typeof undefined) {
          etsSeo.content_analysis[langId] = {};
        }
        
        // Initialize score structures ONLY for languages that should be processed
        Object.keys(etsSeo.seo_score).forEach((rule) => {
          if (typeof etsSeo.seo_score[rule][langId] === typeof undefined) {
            etsSeo.seo_score[rule][langId] = 0;
          }
        });
        Object.keys(etsSeo.readability_score).forEach((rule) => {
          if (typeof etsSeo.readability_score[rule][langId] === typeof undefined) {
            etsSeo.readability_score[rule][langId] = 0;
          }
        });
        
        if (!ETS_SEO_SCORE_DATA[langId]) {
          ETS_SEO_SCORE_DATA[langId] = {
            score_analysis: {seo_score: {}, readability_score: {}},
            content_analysis: {},
          };
        }
        if (!ETS_SEO_SCORE_DATA[langId].score_analysis) {
          ETS_SEO_SCORE_DATA[langId].score_analysis = {seo_score: {}, readability_score: {}};
        }
        
        // Update score data
        ETS_SEO_SCORE_DATA[langId].score_analysis.seo_score = {};
        Object.keys(etsSeo.seo_score).forEach((rule) => {
          ETS_SEO_SCORE_DATA[langId].score_analysis.seo_score[rule] = etsSeo.seo_score[rule][langId];
        });
        ETS_SEO_SCORE_DATA[langId].score_analysis.readability_score = {};
        Object.keys(etsSeo.readability_score).forEach((rule) => {
          ETS_SEO_SCORE_DATA[langId].score_analysis.readability_score[rule] = etsSeo.readability_score[rule][langId];
        });
        ETS_SEO_SCORE_DATA[langId].content_analysis = etsSeo.content_analysis[langId];
      });
    }
    $('#ets_seo_score_data').val(JSON.stringify({
      seo_score: etsSeo.seo_score,
      readability_score: etsSeo.readability_score,
    }));

    const encodedContentAnalysis = etsSeo.encodeBase64InObject(etsSeo.content_analysis);
    $('#ets_seo_content_analysis').val(JSON.stringify(encodedContentAnalysis));
    
    etsSeo.setPreviewAnalysis();
  },
  replaceNbsps(str) {
    const re = new RegExp(String.fromCharCode(160), 'g');
    return str.replace(re, ' ');
  },
  prefixInput: function(controller) {
    controller = controller || etsSeoBo.currentController;
    const prefix = {
      meta_title: '',
      meta_desc: '',
      link_rewrite: '',
      content: '',
      title: '',
      short_desc: '',
      price: '',
      category: '',
      brand: '',
      discount_price: '',
    };
    if (controller === 'AdminProducts') {
      if ($('.product-page-v2').length) {
        prefix.meta_title = '#product_seo_meta_title_';
        prefix.title = '#product_header_name_';
        prefix.meta_desc = '#product_seo_meta_description_';
        prefix.link_rewrite = '#product_seo_link_rewrite_';
        prefix.content = '#product_description_description_';
        prefix.short_desc = '#product_description_description_short_';
        prefix.price = '#product_pricing_retail_price_price_tax_included';
        prefix.brand = '#product_description_manufacturer';
        prefix.discount_price = '#specific-prices-list-table';
        prefix.category = '#product_description_categories_default_category_id';
      } else {
        prefix.meta_title = '#form_step5_meta_title_';
        prefix.title = '#form_step1_name_';
        prefix.meta_desc = '#form_step5_meta_description_';
        prefix.link_rewrite = '#form_step5_link_rewrite_';
        prefix.content = '#form_step1_description_';
        prefix.short_desc = '#form_step1_description_short_';
        prefix.price = '#form_step2_price_ttc';
        prefix.brand = '#form_step1_id_manufacturer';
        prefix.discount_price = '#js-specific-price-list';
        prefix.category = 'input[name="ignore"][class="default-category"]';
      }

      return prefix;
    }
    if (controller === 'AdminCmsContent') {
      if (ETS_SEO_IS_CMS_CATEGORY) {
        prefix.meta_title = ETS_SEO_DEFINED.is176 ? '#cms_page_category_meta_title_' : '#meta_title_';
        prefix.meta_desc = ETS_SEO_DEFINED.is176 ? '#cms_page_category_meta_description_' : '#meta_description_';
        prefix.link_rewrite = ETS_SEO_DEFINED.is176 ? '#cms_page_category_friendly_url_' : '#link_rewrite_';
        prefix.content = ETS_SEO_DEFINED.is176 ? '#cms_page_category_description_' : '#description_';
        prefix.short_desc = ETS_SEO_DEFINED.is176 ? '#cms_page_category_description_' : '#description_';
        prefix.title = ETS_SEO_DEFINED.is176 ? '#cms_page_category_name_' : '#name_';
      } else {
        prefix.meta_title = ETS_SEO_DEFINED.is176 ? '#cms_page_meta_title_' : '#head_seo_title_';
        prefix.meta_desc = ETS_SEO_DEFINED.is176 ? '#cms_page_meta_description_' : '#meta_description_';
        prefix.link_rewrite = ETS_SEO_DEFINED.is176 ? '#cms_page_friendly_url_' : '#link_rewrite_';
        prefix.content = ETS_SEO_DEFINED.is176 ? '#cms_page_content_' : '#content_';
        prefix.title = ETS_SEO_DEFINED.is176 ? '#cms_page_title_' : '#name_';
        prefix.category = ETS_SEO_DEFINED.is176 ? 'input[name="cms_page[page_category_id]"]' : 'select[name="id_cms_category"]';
      }

      return prefix;
    }
    if (controller === 'AdminMeta') {
      prefix.meta_title = ETS_SEO_DEFINED.is176 ? '#meta_page_title_' : '#title_';
      prefix.title = ETS_SEO_DEFINED.is176 ? '#meta_page_title_' : '#title_';
      prefix.meta_desc = ETS_SEO_DEFINED.is176 ? '#meta_meta_description_' : '#description_';
      prefix.short_desc = ETS_SEO_DEFINED.is176 ? '' : '';
      prefix.link_rewrite = ETS_SEO_DEFINED.is176 ? '#meta_url_rewrite_' : '#url_rewrite_';
      prefix.content = ETS_SEO_DEFINED.is176 ? '' : '';

      return prefix;
    }
    if (controller === 'AdminCategories') {
      if ($('form[name=root_category]').length) {
        prefix.title = ETS_SEO_DEFINED.is176 ? '#root_category_name_' : '#name_';
        prefix.meta_title = ETS_SEO_DEFINED.is176 ? '#root_category_meta_title_' : '#meta_title_';
        prefix.meta_desc = ETS_SEO_DEFINED.is176 ? '#root_category_meta_description_' : '#meta_description_';
        prefix.link_rewrite = ETS_SEO_DEFINED.is176 ? '#root_category_link_rewrite_' : '#link_rewrite_';
        prefix.content = ETS_SEO_DEFINED.is176 ? '#root_category_description_' : '#description_';
        prefix.short_desc = ETS_SEO_DEFINED.is176 ? '#root_category_description_' : '#description_';
      } else {
        prefix.title = ETS_SEO_DEFINED.is176 ? '#category_name_' : '#name_';
        prefix.meta_title = ETS_SEO_DEFINED.is176 ? '#category_meta_title_' : '#meta_title_';
        prefix.meta_desc = ETS_SEO_DEFINED.is176 ? '#category_meta_description_' : '#meta_description_';
        prefix.link_rewrite = ETS_SEO_DEFINED.is176 ? '#category_link_rewrite_' : '#link_rewrite_';
        prefix.content = ETS_SEO_DEFINED.is176 ? '#category_content_' : '#content_';
        prefix.short_desc = ETS_SEO_DEFINED.is176 ? '#category_description_' : '#description_';
      }

      return prefix;
    }
    if (controller === 'AdminManufacturers') {
      prefix.title = ETS_SEO_DEFINED.is176 ? '#manufacturer_name_' : '#name_';
      prefix.meta_title = ETS_SEO_DEFINED.is176 ? '#manufacturer_meta_title_' : '#meta_title_';
      prefix.meta_desc = ETS_SEO_DEFINED.is176 ? '#manufacturer_meta_description_' : '#meta_description_';
      prefix.link_rewrite = ETS_SEO_DEFINED.is176 ? '#manufacturer_link_rewrite_' : '#link_rewrite_';
      prefix.content = ETS_SEO_DEFINED.is176 ? '#manufacturer_description_' : '#description_';
      prefix.short_desc = ETS_SEO_DEFINED.is176 ? '#manufacturer_short_description_' : '#short_description_';

      return prefix;
    }
    if (controller === 'AdminSuppliers') {
      prefix.title = ETS_SEO_DEFINED.isSf ? '#supplier_name_' : '#name_';
      prefix.meta_title = ETS_SEO_DEFINED.isSf ? '#supplier_meta_title_' : '#meta_title_';
      prefix.meta_desc = ETS_SEO_DEFINED.isSf ? '#supplier_meta_description_' : '#meta_description_';
      prefix.link_rewrite = ETS_SEO_DEFINED.isSf ? '#link_rewrite_' : '#link_rewrite_';
      prefix.content = ETS_SEO_DEFINED.isSf ? '#supplier_description_' : '#description_';
      prefix.short_desc = ETS_SEO_DEFINED.isSf ? '#supplier_description_' : '#description_';

      return prefix;
    }

    return prefix;
  },
  getMinorKeyphrase: function(id_lang) {
    let minor_key_phrase = $('.ets_seotop1_step_seo input.input-minor-keyphrase-il-' + id_lang).val();
    if (!minor_key_phrase) {
      return [];
    }
    try {
      minor_key_phrase = JSON.parse(minor_key_phrase);
      if (minor_key_phrase.length) {
        const minor = [];
        $.each(minor_key_phrase, function(i, el) {
          minor.push(el.value);
        });
        return minor;
      }
    } catch (e) {
      return [];
    }
    return [];
  },
  analysisContent: function(id_lang, content) {
    content = content || '';
    const text = content.replace(/<\/?[a-z][^>]*?>/gi, '\n');
    const key_phrase = $('.ets_seotop1_step_seo .input-key-phrase-il-' + id_lang).val();
    etsSeo.rules.outboundLink(id_lang, content);
    etsSeo.rules.internalLink(id_lang, content);
    etsSeo.rules.textLength(id_lang, text);
    etsSeo.rules.singleH1(id_lang, content);
    etsSeo.analysisKeypharse(id_lang, key_phrase, content);

    // Readability
    etsSeo.readability.notEnoughContent(id_lang, text);
    etsSeo.readability.sentenceLength(id_lang, content);
    etsSeo.readability.fleschReadingEase(id_lang, text);
    etsSeo.readability.paragraphLength(id_lang, content);
    etsSeo.readability.consecutiveSentences(id_lang, text);
    etsSeo.readability.subheadingDistribution(id_lang, content);
    etsSeo.readability.transitionWords(id_lang, text);
    etsSeo.readability.passive_voice(id_lang, text);
    etsSeo.analysisMinorKeyphrase(id_lang);
    const prefix = etsSeo.prefixInput();
    if (prefix.meta_desc && $(prefix.meta_desc + id_lang).length) {
      etsSeo.rules.metaDescLength(id_lang, $(prefix.meta_desc + id_lang).val());
    }
    etsSeo.changePreview(id_lang);
  },

  analysisKeypharse: function(id_lang, key_phrase, content) {
    if (typeof key_phrase === 'undefined') {
      key_phrase = '';
    }
    etsSeo.rules.keyPhraseLength(id_lang, key_phrase);
    const meta_title = etsSeo.getMetaTitle(id_lang, true);
    const meta_desc = etsSeo.getMetaDesc(id_lang, true);
    etsSeo.rules.keyPhraseInTitle(id_lang, key_phrase, meta_title);
    etsSeo.rules.keyphraseInMetaDesc(id_lang, key_phrase, meta_desc);
    etsSeo.rules.seoTitleWidth(id_lang, meta_title);
    const text_intro = etsSeo.getFirstParagraph(content);
    etsSeo.rules.keyphraseInIntroduction(id_lang, key_phrase, text_intro);
    const text = content ? content.replace(/<\/?[a-z][^>]*?>/gi, '\n') : '';
    etsSeo.rules.keyphraseDensity(id_lang, key_phrase, text);
    etsSeo.rules.keyphraseInSubheading(id_lang, key_phrase, content);
    etsSeo.rules.imageAltAttribute(id_lang, key_phrase, content);
  },

  // Get links in text content
  getLinks: function(str) {
    const tmp = document.createElement('div');
    tmp.innerHTML = str;
    const contentHTML = tmp.getElementsByTagName('a');
    const links = [];
    $.each(contentHTML, function() {
      if ($(this).attr('href')) {
        links.push($(this).attr('href'));
      }
    });

    return links;
  },
  getLinksNoFollowed: function(str) {
    const tmp = document.createElement('div');
    tmp.innerHTML = str;
    const contentHTML = tmp.getElementsByTagName('a');
    const links = [];
    $.each(contentHTML, function() {
      if ($(this).attr('href') && $(this).attr('rel') == 'nofollow') {
        links.push($(this).attr('href'));
      }
    });

    return links;
  },
  // Analysis rules
  rules: {
    outboundLink: function(id_lang, content) {
      content = content || '';
      if (!content) {
        etsSeo.removeAnalysisMessage('#analysis-result--list-' + id_lang,
            'outbound_link');
        if (etsSeoBo.currentController == 'AdminMeta') {
          etsSeo.setSeoScore('outbound_link', id_lang, 9);
        } else {
          etsSeo.setSeoScore('outbound_link', id_lang, 0);
        }
        return;
      }
      const links = etsSeo.getLinks(content);
      const noFollowedLinks = etsSeo.getLinksNoFollowed(content);
      const comp = new RegExp(location.host);
      const listLinks = [];
      const listNoFollowedLinks = [];
      $.each(links, function(i, link) {
        if (!comp.test(link)) { // Is outbound link
          listLinks.push(link);
        }
      });
      $.each(noFollowedLinks, function(i, link) {
        if (!comp.test(link)) { // Is outbound link
          listNoFollowedLinks.push(link);
        }
      });

      if (!listLinks.length && !listNoFollowedLinks.length) {
        etsSeo.getAnalysisMessage(
            '#analysis-result--list-' + id_lang,
            'outbound_link',
            ETS_SEO_DEFINED.seo_analysis_rules.outbound_link.error,
            'error',
        );
        etsSeo.setSeoScore('outbound_link', id_lang, 3);
      } else if (!listLinks.length && listNoFollowedLinks.length) {
        etsSeo.getAnalysisMessage(
            '#analysis-result--list-' + id_lang,
            'outbound_link',
            ETS_SEO_DEFINED.seo_analysis_rules.outbound_link.all_nofollowed,
            'warning',
        );
        etsSeo.setSeoScore('outbound_link', id_lang, 7);
      } else if (listLinks.length && listNoFollowedLinks.length) {
        etsSeo.getAnalysisMessage(
            '#analysis-result--list-' + id_lang,
            'outbound_link',
            ETS_SEO_DEFINED.seo_analysis_rules.outbound_link.both,
            'success',
        );
        etsSeo.setSeoScore('outbound_link', id_lang, 8);
      } else {
        etsSeo.getAnalysisMessage(
            '#analysis-result--list-' + id_lang,
            'outbound_link',
            ETS_SEO_DEFINED.seo_analysis_rules.outbound_link.success,
            'success',
        );
        etsSeo.setSeoScore('outbound_link', id_lang, 9);
      }
    },
    internalLink: function(id_lang, content) {
      content = content || '';
      if (!content) {
        if (etsSeoBo.currentController == 'AdminMeta') {
          etsSeo.setSeoScore('internal_link', id_lang, 9);
        } else {
          etsSeo.setSeoScore('internal_link', id_lang, 0);
        }
        etsSeo.removeAnalysisMessage('#analysis-result--list-' + id_lang,
            'internal_link');
        return;
      }
      const links = etsSeo.getLinks(content);
      const noFollowedLinks = etsSeo.getLinksNoFollowed(content);
      let comp;
      const listLinks = [];
      const listNoFollowedLinks = [];
      $.each(links, function(i, link) {
        if (etsSeoBo.shopUrls && etsSeoBo.shopUrls.length) {
          for (let j = 0; j < etsSeoBo.shopUrls.length; j++) {
            comp = new RegExp(etsSeoBo.shopUrls[j]);
            if (comp.test(link)) { // Is internal link
              listLinks.push(link);
              return;
            }
          }
        } else {
          comp = new RegExp(location.host);
          if (comp.test(link)) { // Is internal link
            listLinks.push(link);
          }
        }
      });
      $.each(noFollowedLinks, function(i, link) {
        if (etsSeoBo.shopUrls && etsSeoBo.shopUrls.length) {
          for (let j = 0; j < etsSeoBo.shopUrls.length; j++) {
            comp = new RegExp(etsSeoBo.shopUrls[j]);
            if (comp.test(link)) { // Is internal link
              listNoFollowedLinks.push(link);
              return;
            }
          }
        } else {
          comp = new RegExp(location.host);
          if (comp.test(link)) { // Is internal link
            listNoFollowedLinks.push(link);
          }
        }
      });
      if (!listLinks.length && !listNoFollowedLinks.length) {
        etsSeo.getAnalysisMessage(
            '#analysis-result--list-' + id_lang,
            'internal_link',
            ETS_SEO_DEFINED.seo_analysis_rules.internal_link.error,
            'error',
        );

        etsSeo.setSeoScore('internal_link', id_lang, 3);
      } else if (!listLinks.length && listNoFollowedLinks.length) {
        etsSeo.getAnalysisMessage(
            '#analysis-result--list-' + id_lang,
            'internal_link',
            ETS_SEO_DEFINED.seo_analysis_rules.internal_link.all_nofollowed,
            'warning',
        );

        etsSeo.setSeoScore('internal_link', id_lang, 7);
      } else if (listLinks.length && listNoFollowedLinks.length) {
        etsSeo.getAnalysisMessage(
            '#analysis-result--list-' + id_lang,
            'internal_link',
            ETS_SEO_DEFINED.seo_analysis_rules.internal_link.both,
            'warning',
        );

        etsSeo.setSeoScore('internal_link', id_lang, 8);
      } else {
        etsSeo.getAnalysisMessage(
            '#analysis-result--list-' + id_lang,
            'internal_link',
            ETS_SEO_DEFINED.seo_analysis_rules.internal_link.success,
            'success',
        );
        etsSeo.setSeoScore('internal_link', id_lang, 9);
      }
    },
    singleH1: function(id_lang, content, pageName) {
      content = content || '';
      pageName = pageName || '';
      if (!content) {
        etsSeo.removeAnalysisMessage('#analysis-result--list-' + id_lang,
            'single_h1');
        if (etsSeoBo.currentController == 'AdminMeta' || pageName == 'meta') {
          etsSeo.setSeoScore('single_h1', id_lang, 9);
        } else {
          etsSeo.setSeoScore('single_h1', id_lang, -999);
        }
        return;
      }

      const tmp = document.createElement('div');
      tmp.innerHTML = content;
      const h1 = tmp.getElementsByTagName('h1');
      if (h1.length) {
        etsSeo.getAnalysisMessage(
            '#analysis-result--list-' + id_lang,
            'single_h1',
            ETS_SEO_DEFINED.seo_analysis_rules.single_h1.error,
            'error',
        );
        etsSeo.setSeoScore('single_h1', id_lang, 1);
      } else {
        etsSeo.setSeoScore('single_h1', id_lang, 9);
        etsSeo.removeAnalysisMessage('#analysis-result--list-' + id_lang,
            'single_h1');
      }
    },

    // Text length
    textLength: function(id_lang, content, page_type, pageName) {
      content = content || '';
      pageName = pageName || '';
      if (etsSeoBo.currentController == 'AdminMeta' || pageName == 'meta') {
        etsSeo.setSeoScore('text_length', id_lang, 9);
        etsSeo.removeAnalysisMessage('#analysis-result--list-' + id_lang,
            'text_length');
        return;
      }
      // Taxonomy page >250 words
      // Regular post or page > 300 words, category: 100 words
      // Cornerstone content page > 900 words
      // Value: taxonomy, regular , cornerstone

      let min_length = 300;
      if (etsSeoBo.currentController == 'AdminCategories' || (etsSeoBo.currentController == 'AdminCmsContent' && ETS_SEO_IS_CMS_CATEGORY)) {
        page_type = 'category';
      }
      switch (page_type) {
        case 'taxonomy':
          min_length = 250;
          break;
        case 'regular':
          min_length = 300;
          break;
        case 'cornerstone':
          min_length = 900;
          break;
        case 'category':
          min_length = 100;
          break;
        default:
          min_length = 300;
      }
      const text_length = content.length ? content.trim().split(/\s+/).length : 0;

      if (text_length >= min_length) {
        if (ETS_SEO_DEFINED.seo_analysis_rules.text_length.success.short_code['[text_length]']) {
          ETS_SEO_DEFINED.seo_analysis_rules.text_length.success.short_code['[text_length]'].number = text_length;
        }
        etsSeo.getAnalysisMessage(
            '#analysis-result--list-' + id_lang,
            'text_length',
            ETS_SEO_DEFINED.seo_analysis_rules.text_length.success,
            'success',
        );
        etsSeo.setSeoScore('text_length', id_lang, 9);
      } else {
        if (ETS_SEO_DEFINED.seo_analysis_rules.text_length.error.short_code['[text_length]']) {
          ETS_SEO_DEFINED.seo_analysis_rules.text_length.error.short_code['[text_length]'].number = text_length;
        }
        if (ETS_SEO_DEFINED.seo_analysis_rules.text_length.error.short_code['[min_length]']) {
          ETS_SEO_DEFINED.seo_analysis_rules.text_length.error.short_code['[min_length]'].number = min_length;
        }

        etsSeo.getAnalysisMessage(
            '#analysis-result--list-' + id_lang,
            'text_length',
            ETS_SEO_DEFINED.seo_analysis_rules.text_length.error,
            'error',
        );
        if (page_type == 'category') {
          if (text_length < 30) {
            etsSeo.setSeoScore('text_length', id_lang, -10);
          } else if (text_length >= 30 && text_length <= 50) {
            etsSeo.setSeoScore('text_length', id_lang, 3);
          } else if (text_length > 50 && text_length < 100) {
            etsSeo.setSeoScore('text_length', id_lang, 6);
          }
        } else {
          if (text_length <= 99) {
            etsSeo.setSeoScore('text_length', id_lang, -20);
          } else if (text_length >= 100 && text_length <= 199) {
            etsSeo.setSeoScore('text_length', id_lang, -10);
          } else if (text_length >= 200 && text_length <= 249) {
            etsSeo.setSeoScore('text_length', id_lang, 3);
          } else if (text_length >= 250 && text_length <= 299) {
            etsSeo.setSeoScore('text_length', id_lang, 6);
          }
        }
      }
    },
    keyPhraseLength: function(id_lang, str) {
      str = str || '';
      if (!str) {
        etsSeo.getAnalysisMessage(
            '#analysis-result--list-' + id_lang,
            'keyphrase_length',
            ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_length.error,
            'error',
        );

        etsSeo.setSeoScore('keyphrase_length', id_lang, 0);
      } else {
        const words_length = str.trim().split(/\s+/g).length;
        etsSeo.getAnalysisMessage(
            '#analysis-result--list-' + id_lang,
            'keyphrase_length',
            ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_length.success,
            'success',
        );
        if (words_length < 5) {
          etsSeo.setSeoScore('keyphrase_length', id_lang, 9);
        } else if (words_length >= 5 && words_length <= 8) {
          if (ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_length.too_long.short_code['[count_length]']) {
            ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_length.too_long.short_code['[count_length]'].number = words_length;
          }
          etsSeo.setSeoScore('keyphrase_length', id_lang, 6);
          etsSeo.getAnalysisMessage(
              '#analysis-result--list-' + id_lang,
              'keyphrase_length',
              ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_length.too_long,
              'warning',
          );
        } else {
          if (ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_length.too_long.short_code['[count_length]']) {
            ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_length.too_long.short_code['[count_length]'].number = words_length;
          }
          etsSeo.getAnalysisMessage(
              '#analysis-result--list-' + id_lang,
              'keyphrase_length',
              ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_length.too_long,
              'warning',
          );
          etsSeo.setSeoScore('keyphrase_length', id_lang, 3);
        }
      }
    },
    keyPhraseInTitle: function(id_lang, key_phrase, title) {
      key_phrase = key_phrase || '';
      title = title || '';
      const prefix = etsSeo.prefixInput();
      const pageTitle = prefix.title ? (etsSeoBo.currentController != 'AdminManufacturers' && etsSeoBo.currentController != 'AdminSuppliers' ? $(prefix.title + id_lang).val() : $(prefix.title.slice(0, -1)).val()) : '';

      etsSeo.rules.keyPhraseInPageTitle(id_lang, key_phrase, pageTitle);
      if ((!title || ETS_SEO_FORCE_USE_META_TEMPLATE) && key_phrase) {
        title = etsSeo.getMetaTitle(id_lang);
      }
      if (!key_phrase || !title) {
        etsSeo.setSeoScore('keyphrase_in_title', id_lang, 0);
        etsSeo.removeAnalysisMessage('#analysis-result--list-' + id_lang,
            'keyphrase_in_title');
        return;
      }
      title = etsSeo.renderMetaData(title, id_lang, true);
      const myPattern = new RegExp('(?:^|\\s)' + etsSeo.escapeRegExp(key_phrase.trim()) + '(?:$|\\s|[^\\w])', 'gi');
      const matchResult = title.trim().match(myPattern);
      if (matchResult !== null) {
        const pattern2 = new RegExp('(?:^|\\s)' + etsSeo.escapeRegExp(key_phrase.trim()) + '(?:$|\\s|[^\\w])', 'i');
        const firstResult = title.match(pattern2);
        if (firstResult !== null && firstResult.index == 0) {
          etsSeo.getAnalysisMessage(
              '#analysis-result--list-' + id_lang,
              'keyphrase_in_title',
              ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_in_title.success,
              'success',
          );

          etsSeo.setSeoScore('keyphrase_in_title', id_lang, 9);
        } else {
          etsSeo.getAnalysisMessage(
              '#analysis-result--list-' + id_lang,
              'keyphrase_in_title',
              ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_in_title.warning,
              'warning',
          );
          etsSeo.setSeoScore('keyphrase_in_title', id_lang, 6);
        }
      } else {
        if (ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_in_title.error.short_code['[keyphrase]']) {
          ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_in_title.error.short_code['[keyphrase]'].string = key_phrase;
        }
        etsSeo.getAnalysisMessage(
            '#analysis-result--list-' + id_lang,
            'keyphrase_in_title',
            ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_in_title.error,
            'error',
        );
        etsSeo.setSeoScore('keyphrase_in_title', id_lang, 2);
      }
    },
    keyPhraseInPageTitle: function(id_lang, key_phrase, title) {
      key_phrase = key_phrase || '';
      title = title || '';
      if (!key_phrase || !title) {
        etsSeo.setSeoScore('keyphrase_in_page_title', id_lang, 0);
        etsSeo.removeAnalysisMessage('#analysis-result--list-' + id_lang,
            'keyphrase_in_page_title');
        return;
      }
      title = etsSeo.renderMetaData(title, id_lang, true);
      const myPattern = new RegExp('(?:^|\\s)' + etsSeo.escapeRegExp(key_phrase.trim()) + '(?:$|\\s|[^\\w])', 'gi');
      const matchResult = title.trim().match(myPattern);

      if (matchResult !== null) {
        const pattern2 = new RegExp('(?:^|\\s)' + etsSeo.escapeRegExp(key_phrase.trim()) + '(?:$|\\s|[^\\w])', 'i');
        const firstResult = title.match(pattern2);

        if (firstResult !== null && firstResult.index == 0) {
          etsSeo.getAnalysisMessage(
              '#analysis-result--list-' + id_lang,
              'keyphrase_in_page_title',
              ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_in_page_title.success,
              'success',
          );

          etsSeo.setSeoScore('keyphrase_in_page_title', id_lang, 9);
        } else {
          etsSeo.getAnalysisMessage(
              '#analysis-result--list-' + id_lang,
              'keyphrase_in_page_title',
              ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_in_page_title.warning,
              'warning',
          );
          etsSeo.setSeoScore('keyphrase_in_page_title', id_lang, 6);
        }
      } else {
        if (ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_in_page_title.error.short_code['[keyphrase]']) {
          ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_in_page_title.error.short_code['[keyphrase]'].string = key_phrase;
        }
        etsSeo.getAnalysisMessage(
            '#analysis-result--list-' + id_lang,
            'keyphrase_in_page_title',
            ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_in_page_title.error,
            'error',
        );
        etsSeo.setSeoScore('keyphrase_in_page_title', id_lang, 2);
      }
    },
    pageTitleLength: function(id_lang, title) {
      title = title || '';
      if (!title || !title.trim().length) {
        etsSeo.getAnalysisMessage(
            '#analysis-result--list-' + id_lang,
            'page_title_length',
            ETS_SEO_DEFINED.seo_analysis_rules.page_title_length.empty,
            'error',
        );
        return;
      }
      if (title.trim().length > 65) {
        etsSeo.getAnalysisMessage(
            '#analysis-result--list-' + id_lang,
            'page_title_length',
            ETS_SEO_DEFINED.seo_analysis_rules.page_title_length.too_long,
            'error',
        );
      } else {
        etsSeo.getAnalysisMessage(
            '#analysis-result--list-' + id_lang,
            'page_title_length',
            ETS_SEO_DEFINED.seo_analysis_rules.page_title_length.success,
            'success',
        );
      }
    },

    keyphraseInIntroduction: function(id_lang, key_phrase, intro) {
      intro = etsSeo.replaceNbsps(intro);
      key_phrase = key_phrase || '';
      intro = intro || '';

      if (!intro) {
        if (etsSeoBo.currentController == 'AdminMeta') {
          etsSeo.setSeoScore('keyphrase_in_intro', id_lang, 9);
        } else {
          etsSeo.setSeoScore('keyphrase_in_intro', id_lang, 0);
        }
        etsSeo.removeAnalysisMessage('#analysis-result--list-' + id_lang,
            'keyphrase_in_intro');
        return;
      }
      if (!key_phrase) {
        etsSeo.removeAnalysisMessage('#analysis-result--list-' + id_lang,
            'keyphrase_in_intro');
        return;
      }
      if (intro && key_phrase) {
        const myPattern = new RegExp('(?:^|\\s)' + etsSeo.escapeRegExp(key_phrase.trim()) + '(?:$|\\s|[^\\w])', 'gi');
        const matchResult = intro.trim().match(myPattern);

        if (matchResult !== null) {
          etsSeo.getAnalysisMessage(
              '#analysis-result--list-' + id_lang,
              'keyphrase_in_intro',
              ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_in_intro.success,
              'success',
          );
          etsSeo.setSeoScore('keyphrase_in_intro', id_lang, 9);
        } else {
          etsSeo.getAnalysisMessage(
              '#analysis-result--list-' + id_lang,
              'keyphrase_in_intro',
              ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_in_intro.error,
              'error',
          );

          etsSeo.setSeoScore('keyphrase_in_intro', id_lang, 3);
        }
      }
    },
    keyphraseInSubheading: function(id_lang, key_phrase, content) {
      key_phrase = key_phrase || '';
      content = content || '';
      if (!content || !key_phrase) {
        if (!content && etsSeoBo.currentController == 'AdminMeta') {
          etsSeo.setSeoScore('keyphrase_in_subheading', id_lang, 9);
        } else {
          etsSeo.setSeoScore('keyphrase_in_subheading', id_lang, 0);
        }
        etsSeo.removeAnalysisMessage('#analysis-result--list-' + id_lang,
            'keyphrase_in_subheading');
        return;
      }

      const tmp = document.createElement('div');
      tmp.innerHTML = content;
      const keyphraseArray = key_phrase.toLowerCase().split(/\s+/);
      let totalSubheading = 0;
      let totalSubheadingreflectKeyphrase = 0;
      for (let i = 2; i <= 3; i++) {
        const headings = tmp.getElementsByTagName('h' + i);
        totalSubheading += headings.length;
        if (headings && headings.length) {
          for (let k = 0; k < headings.length; k++) {
            const headingArrayContent = headings[k].innerText.toLowerCase().split(/\s+/);
            if (keyphraseArray.length > 1) {
              const tmpContain = [];
              for (let t = 0; t < headingArrayContent.length; t++) {
                if (keyphraseArray.indexOf(headingArrayContent[t]) !== -1 && tmpContain.indexOf(headingArrayContent[t]) === -1) {
                  tmpContain.push(headingArrayContent[t]);
                  if (tmpContain.length >= 2) {
                    break;
                  }
                }
              }
              if (tmpContain.length > 1) {
                totalSubheadingreflectKeyphrase++;
              }
            } else {
              if (headingArrayContent.indexOf(keyphraseArray[0]) !== -1) {
                totalSubheadingreflectKeyphrase++;
              }
            }
          }
        }
      }

      if (totalSubheading) {
        if (totalSubheading > 1) {
          const ratio = totalSubheadingreflectKeyphrase / totalSubheading * 100;
          if (ratio < 30) {
            etsSeo.getAnalysisMessage(
                '#analysis-result--list-' + id_lang,
                'keyphrase_in_subheading',
                ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_in_subheading.too_little,
                'error',
            );
            etsSeo.setSeoScore('keyphrase_in_subheading', id_lang, 3);
          } else if (ratio > 75) {
            etsSeo.getAnalysisMessage(
                '#analysis-result--list-' + id_lang,
                'keyphrase_in_subheading',
                ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_in_subheading.too_much,
                'error',
            );
            etsSeo.setSeoScore('keyphrase_in_subheading', id_lang, 3);
          } else {
            if (ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_in_subheading.good.short_code['[count]']) {
              ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_in_subheading.good.short_code['[count]'].number = totalSubheadingreflectKeyphrase;
            }
            etsSeo.getAnalysisMessage(
                '#analysis-result--list-' + id_lang,
                'keyphrase_in_subheading',
                ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_in_subheading.good,
                'success',
            );
            etsSeo.setSeoScore('keyphrase_in_subheading', id_lang, 9);
          }
        } else {
          if (!totalSubheadingreflectKeyphrase) {
            etsSeo.getAnalysisMessage(
                '#analysis-result--list-' + id_lang,
                'keyphrase_in_subheading',
                ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_in_subheading.too_little,
                'error',
            );
            etsSeo.setSeoScore('keyphrase_in_subheading', id_lang, 3);
          } else {
            if (ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_in_subheading.good.short_code['[count]']) {
              ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_in_subheading.good.short_code['[count]'].number = totalSubheadingreflectKeyphrase;
            }
            etsSeo.getAnalysisMessage(
                '#analysis-result--list-' + id_lang,
                'keyphrase_in_subheading',
                ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_in_subheading.good,
                'success',
            );
            etsSeo.setSeoScore('keyphrase_in_subheading', id_lang, 9);
          }
        }
      } else {
        etsSeo.setSeoScore('keyphrase_in_intro', id_lang, 9);
        etsSeo.removeAnalysisMessage('#analysis-result--list-' + id_lang,
            'keyphrase_in_subheading');
      }
    },
    keyphraseDensity: function(id_lang, key_phrase, content) {
      key_phrase = key_phrase || '';
      content = content || '';
      content = etsSeo.replaceNbsps(content);
      if (!content) {
        if (etsSeoBo.currentController == 'AdminMeta') {
          etsSeo.setSeoScore('keyphrase_density', id_lang, 9);
        } else {
          etsSeo.setSeoScore('keyphrase_density', id_lang, 0);
        }
        etsSeo.removeAnalysisMessage('#analysis-result--list-' + id_lang,
            'keyphrase_density');
        return;
      }
      if (!key_phrase) {
        etsSeo.setSeoScore('keyphrase_density', id_lang, 0);
        etsSeo.setSeoScore('keyphrase_density_individual', id_lang, 0);
        etsSeo.removeAnalysisMessage('#analysis-result--list-' + id_lang,
            'keyphrase_density');
        etsSeo.removeAnalysisMessage('#analysis-result--list-' + id_lang,
            'keyphrase_density_individual');
        return;
      }
      // The focus keyphrase should be found minimum of 2 times

      if (key_phrase && content) {
        const textArray = content.trim().split(/\s+/);
        let counter = 0;
        const good = false;
        const myPattern = new RegExp('(?:^|\\s)' + etsSeo.escapeRegExp(key_phrase.trim()) + '(?:$|\\s|[^\\w])', 'gi');
        const keyphrase_density = content.match(myPattern);
        const wordsLength = content.split(/ /g).length;

        let recommend_keyphrase_length = Math.ceil(wordsLength * 0.03);
        if (recommend_keyphrase_length < 3) {
          recommend_keyphrase_length = 3;
        }
        let recommend_keyphrase_length_min = Math.ceil(wordsLength * 0.003);
        if (recommend_keyphrase_length_min < 3) {
          recommend_keyphrase_length_min = 3;
        }

        if (keyphrase_density != null) {
          counter = keyphrase_density.length;
          if (counter >= 3) {
            const ratio = counter / wordsLength * 100;
            if (ratio > 0.3 && ratio <= 3) {
              if (ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_density.success.short_code['[count_word]']) {
                ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_density.success.short_code['[count_word]'].number = counter;
              }

              etsSeo.getAnalysisMessage(
                  '#analysis-result--list-' + id_lang,
                  'keyphrase_density',
                  ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_density.success,
                  'success',
              );

              etsSeo.setSeoScore('keyphrase_density', id_lang, 9);
            } else if (ratio > 3 && ratio <= 4 && counter > recommend_keyphrase_length) {
              if (ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_density.more_than.short_code['[count_word]']) {
                ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_density.more_than.short_code['[count_word]'].number = counter;
              }
              if (ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_density.more_than.short_code['[recommended_keyphrase_length]']) {
                ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_density.more_than.short_code['[recommended_keyphrase_length]'].number = recommend_keyphrase_length;
              }
              etsSeo.getAnalysisMessage(
                  '#analysis-result--list-' + id_lang,
                  'keyphrase_density',
                  ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_density.more_than,
                  'error',
              );

              etsSeo.setSeoScore('keyphrase_density', id_lang, -50);
            } else if (ratio > 0 && ratio <= 0.3 && counter < recommend_keyphrase_length_min) {
              if (ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_density.error.short_code['[count_word]']) {
                ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_density.error.short_code['[count_word]'].number = counter;
              }
              if (ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_density.error.short_code['[recommended_keyphrase_length]']) {
                ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_density.error.short_code['[recommended_keyphrase_length]'].number = recommend_keyphrase_length_min;
              }
              etsSeo.getAnalysisMessage(
                  '#analysis-result--list-' + id_lang,
                  'keyphrase_density',
                  ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_density.error,
                  'error',
              );

              etsSeo.setSeoScore('keyphrase_density', id_lang, 4);
            } else if (counter > recommend_keyphrase_length && ratio > 4) {
              if (ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_density.more_than.short_code['[count_word]']) {
                ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_density.more_than.short_code['[count_word]'].number = counter;
              }
              if (ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_density.more_than.short_code['[recommended_keyphrase_length]']) {
                ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_density.more_than.short_code['[recommended_keyphrase_length]'].number = recommend_keyphrase_length;
              }
              etsSeo.getAnalysisMessage(
                  '#analysis-result--list-' + id_lang,
                  'keyphrase_density',
                  ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_density.more_than,
                  'error',
              );

              etsSeo.setSeoScore('keyphrase_density', id_lang, 4);
            } else {
              if (ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_density.success.short_code['[count_word]']) {
                ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_density.success.short_code['[count_word]'].number = counter;
              }
              etsSeo.getAnalysisMessage(
                  '#analysis-result--list-' + id_lang,
                  'keyphrase_density',
                  ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_density.success,
                  'success',
              );
              etsSeo.setSeoScore('keyphrase_density', id_lang, 9);
            }
          } else {
            if (ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_density.error.short_code['[count_word]']) {
              ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_density.error.short_code['[count_word]'].number = counter;
            }
            if (ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_density.error.short_code['[recommended_keyphrase_length]']) {
              ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_density.error.short_code['[recommended_keyphrase_length]'].number = recommend_keyphrase_length_min;
            }
            etsSeo.getAnalysisMessage(
                '#analysis-result--list-' + id_lang,
                'keyphrase_density',
                ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_density.error,
                'error',
            );

            etsSeo.setSeoScore('keyphrase_density', id_lang, 4);
          }
        } else {
          if (ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_density.error.short_code['[count_word]']) {
            ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_density.error.short_code['[count_word]'].number = counter;
          }
          if (ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_density.error.short_code['[recommended_keyphrase_length]']) {
            ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_density.error.short_code['[recommended_keyphrase_length]'].number = recommend_keyphrase_length_min;
          }
          etsSeo.getAnalysisMessage(
              '#analysis-result--list-' + id_lang,
              'keyphrase_density',
              ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_density.error,
              'error',
          );

          etsSeo.setSeoScore('keyphrase_density', id_lang, 4);
        }
      }
      etsSeo.rules.keyphraseIndividual(id_lang, key_phrase, content);
    },
    keyphraseIndividual: function(id_lang, key_phrase, content) {
      key_phrase = key_phrase || '';
      content = content || '';
      if (!content || !key_phrase) {
        etsSeo.removeAnalysisMessage('#analysis-result--list-' + id_lang,
            'keyphrase_density_individual');
        if (!content && etsSeoBo.currentController == 'AdminMeta') {
          etsSeo.setSeoScore('single_h1', id_lang, 9);
        } else {
          etsSeo.setSeoScore('keyphrase_density_individual', id_lang, 0);
        }
        return false;
      }

      if (key_phrase.trim().indexOf(' ') == -1) {
        etsSeo.removeAnalysisMessage('#analysis-result--list-' + id_lang,
            'keyphrase_density_individual');
        etsSeo.setSeoScore('keyphrase_density_individual', id_lang, 9);
        return;
      }
      const subContent = content.replace(new RegExp('(?:^|\\s)' + etsSeo.escapeRegExp(key_phrase.trim()) + '(?:$|\\s|[^\\d\\W])', 'ig'), '');
      const keyphraseIndividuals = key_phrase.trim().split(/\s+/);
      const wordsLength = subContent.split(/ /g).length;
      let recommendedLength = Math.ceil(wordsLength * 0.003);
      if (recommendedLength < 1) {
        recommendedLength = 1;
      }
      const errorItems = [];

      for (let i = 0; i < keyphraseIndividuals.length; i++) {
        const desityAppearLength = (subContent.match(new RegExp(etsSeo.escapeRegExp(keyphraseIndividuals[i]), 'gi')) || []).length;
        if (desityAppearLength < recommendedLength) {
          errorItems.push({key: keyphraseIndividuals[i], count: desityAppearLength});
          break;
        }
      }

      if (errorItems.length) {
        if (ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_density_individual.error.short_code['[keyphrase_individual]']) {
          ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_density_individual.error.short_code['[keyphrase_individual]'].string = errorItems[0].key;
        }
        if (ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_density_individual.error.short_code['[recommended_keyphrase_length]']) {
          ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_density_individual.error.short_code['[recommended_keyphrase_length]'].number = recommendedLength;
        }
        etsSeo.getAnalysisMessage(
            '#analysis-result--list-' + id_lang,
            'keyphrase_density_individual',
            ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_density_individual.error,
            'error',
        );
        etsSeo.setSeoScore('keyphrase_density_individual', id_lang, 3);
      } else {
        etsSeo.getAnalysisMessage(
            '#analysis-result--list-' + id_lang,
            'keyphrase_density_individual',
            ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_density_individual.success,
            'success',
        );
        etsSeo.setSeoScore('keyphrase_density_individual', id_lang, 9);
      }
    },
    imageAltAttribute: function(id_lang, key_phrase, content) {
      content = content || '';
      if (!content) {
        if (etsSeoBo.currentController === 'AdminMeta') {
          etsSeo.setSeoScore('image_alt_attribute', id_lang, 9);
        } else {
          etsSeo.setSeoScore('image_alt_attribute', id_lang, 0);
        }
        etsSeo.removeAnalysisMessage('#analysis-result--list-' + id_lang,
            'image_alt_attribute');
        return;
      }
      let productImageContent = '';
      if (typeof ETS_SEO_PRODUCT_IMAGE !== 'undefined' && ETS_SEO_PRODUCT_IMAGE && ETS_SEO_PRODUCT_IMAGE.hasOwnProperty(id_lang)) {
        const images = ETS_SEO_PRODUCT_IMAGE[id_lang];
        if (images.length) {
          $.each(images, function(i, el) {
            if (el.legend !== undefined) {
              productImageContent += '<img alt="' + el.legend + '" >';
            }
          });
        }
      }

      const tmp = document.createElement('div');
      tmp.innerHTML = content + productImageContent;

      const imgTags = tmp.getElementsByTagName('img');

      if (imgTags.length) {
        let has_alt = 0;
        let alt_has_keyphrase = 0;
        const myPattern = new RegExp('(?:^|\\s)' + etsSeo.escapeRegExp(key_phrase.trim()) + '(?:$|\\s|[^\\d\\W])', 'i');

        for (let i = 0; i < imgTags.length; i++) {
          if ($(imgTags[i]).attr('alt')) {
            has_alt++;
            if (key_phrase) {
              if ($(imgTags[i]).attr('alt').match(myPattern) !== null) {
                alt_has_keyphrase++;
              }
            }
          }
        }
        if (!has_alt) {
          etsSeo.getAnalysisMessage(
              '#analysis-result--list-' + id_lang,
              'image_alt_attribute',
              ETS_SEO_DEFINED.seo_analysis_rules.image_alt_attribute.no_alt,
              'warning',
          );
          etsSeo.setSeoScore('image_alt_attribute', id_lang, 9);
        } else if (has_alt && !alt_has_keyphrase && key_phrase) {
          etsSeo.getAnalysisMessage(
              '#analysis-result--list-' + id_lang,
              'image_alt_attribute',
              ETS_SEO_DEFINED.seo_analysis_rules.image_alt_attribute.alt_no_keyphrase,
              'warning',
          );

          etsSeo.setSeoScore('image_alt_attribute', id_lang, 6);
        } else {
          etsSeo.getAnalysisMessage(
              '#analysis-result--list-' + id_lang,
              'image_alt_attribute',
              ETS_SEO_DEFINED.seo_analysis_rules.image_alt_attribute.success,
              'success',
          );
          const ratio = alt_has_keyphrase > 0 ? alt_has_keyphrase / has_alt : has_alt / imgTags.length;
          if (has_alt >= 5 && ratio < 0.3) {
            etsSeo.setSeoScore('image_alt_attribute', id_lang, 6);
          } else if (has_alt >= 5 && ratio > 0.7) {
            etsSeo.setSeoScore('image_alt_attribute', id_lang, 6);
          } else if (has_alt < 5 && ratio >= 0.3 && ratio <= 0.75) {
            etsSeo.setSeoScore('image_alt_attribute', id_lang, 9);
          } else if (has_alt < 5 && alt_has_keyphrase) {
            etsSeo.setSeoScore('image_alt_attribute', id_lang, 9);
          }
        }
      } else {
        etsSeo.getAnalysisMessage(
            '#analysis-result--list-' + id_lang,
            'image_alt_attribute',
            ETS_SEO_DEFINED.seo_analysis_rules.image_alt_attribute.error,
            'error',
        );

        etsSeo.setSeoScore('image_alt_attribute', id_lang, 9);
      }
    },

    seoTitleWidth: function(id_lang, seo_title) {
      seo_title = seo_title || '';
      const prefix = etsSeo.prefixInput();
      const pageTitle = prefix.title ? (etsSeoBo.currentController != 'AdminManufacturers' && etsSeoBo.currentController != 'AdminSuppliers' ? $(prefix.title + id_lang).val() : $(prefix.title.slice(0, -1)).val()) : '';
      if (!seo_title || ETS_SEO_FORCE_USE_META_TEMPLATE) {
        seo_title = etsSeo.getMetaTitle(id_lang);
      }

      if (!seo_title) {
        etsSeo.setSeoScore('seo_title_width', id_lang, 1);
        etsSeo.getAnalysisMessage(
            '#analysis-result--list-' + id_lang,
            'seo_title_width',
            ETS_SEO_DEFINED.seo_analysis_rules.seo_title_width.error,
            'error',
        );
        return;
      }
      seo_title = etsSeo.renderMetaData(seo_title, id_lang, true);
      // The text width should from 400px to 600px
      if (seo_title) {
        seo_title = seo_title.replace(/\r\n/gi, '\n').replace(/\n/gi, '').replace(/\s+/gi, ' ').trim();
        const text_width = seo_title.length;

        if (text_width >= 30 && text_width <= 60) {
          etsSeo.getAnalysisMessage(
              '#analysis-result--list-' + id_lang,
              'seo_title_width',
              ETS_SEO_DEFINED.seo_analysis_rules.seo_title_width.success,
              'success',
          );
          etsSeo.setSeoScore('seo_title_width', id_lang, 9);
        } else if (text_width < 30) {
          etsSeo.getAnalysisMessage(
              '#analysis-result--list-' + id_lang,
              'seo_title_width',
              ETS_SEO_DEFINED.seo_analysis_rules.seo_title_width.success,
              'success',
          );
          etsSeo.setSeoScore('seo_title_width', id_lang, 9);
        } else if (text_width > 60) {
          etsSeo.getAnalysisMessage(
              '#analysis-result--list-' + id_lang,
              'seo_title_width',
              ETS_SEO_DEFINED.seo_analysis_rules.seo_title_width.too_long,
              'error',
          );
          etsSeo.setSeoScore('seo_title_width', id_lang, 3);
        }
      } else {
        etsSeo.getAnalysisMessage(
            '#analysis-result--list-' + id_lang,
            'seo_title_width',
            ETS_SEO_DEFINED.seo_analysis_rules.seo_title_width.error,
            'error',
        );
        etsSeo.setSeoScore('seo_title_width', id_lang, 1);
      }
    },

    metaDescLength: function(id_lang, desc) {
      desc = desc || '';
      // The description length should between 120 and 156 characters
      if (!desc || ETS_SEO_FORCE_USE_META_TEMPLATE) {
        desc = etsSeo.getMetaDesc(id_lang);
      }

      if (!desc || !desc.length) {
        etsSeo.getAnalysisMessage(
            '#analysis-result--list-' + id_lang,
            'meta_description_length',
            ETS_SEO_DEFINED.seo_analysis_rules.meta_description_length.error,
            'error',
        );
        etsSeo.setSeoScore('meta_description_length', id_lang, 1);
        return;
      }
      desc = etsSeo.renderMetaData(desc, id_lang, false);
      const desc_length = desc.replace(/<\/?[a-z][^>]*?>/gi, ' ').replace(/\r\n/gi, '\n').replace(/\n/gi, '').replace(/\s+/gi, ' ').trim().length;
      if (desc_length < 120) {
        etsSeo.getAnalysisMessage(
            '#analysis-result--list-' + id_lang,
            'meta_description_length',
            ETS_SEO_DEFINED.seo_analysis_rules.meta_description_length.warning,
            'warning',
        );
        etsSeo.setSeoScore('meta_description_length', id_lang, 3);
      } else if (desc_length >= 120 && desc_length <= 156) {
        etsSeo.getAnalysisMessage(
            '#analysis-result--list-' + id_lang,
            'meta_description_length',
            ETS_SEO_DEFINED.seo_analysis_rules.meta_description_length.success,
            'success',
        );
        etsSeo.setSeoScore('meta_description_length', id_lang, 9);
      } else {
        etsSeo.getAnalysisMessage(
            '#analysis-result--list-' + id_lang,
            'meta_description_length',
            ETS_SEO_DEFINED.seo_analysis_rules.meta_description_length.over_limited,
            'warning',
        );
        etsSeo.setSeoScore('meta_description_length', id_lang, 6);
      }
    },

    keyphraseInMetaDesc: function(id_lang, key_phrase, desc) {
      key_phrase = key_phrase || '';
      desc = desc || '';
      if (key_phrase && (!desc || ETS_SEO_FORCE_USE_META_TEMPLATE)) {
        desc = etsSeo.getMetaDesc(id_lang);
      }

      if (!key_phrase || !desc) {
        etsSeo.setSeoScore('keyphrase_in_meta_desc', id_lang, 0);
        etsSeo.removeAnalysisMessage('#analysis-result--list-' + id_lang,
            'keyphrase_in_meta_desc');
        return;
      }
      desc = etsSeo.renderMetaData(desc, id_lang, false);
      let myPattern = new RegExp('(?:^|\\s)' + etsSeo.escapeRegExp(key_phrase.trim()) + '(?:$|\\s|[^\\d\\W])', 'gi');
      if (desc.trim().length == key_phrase.trim().length) {
        myPattern = new RegExp(etsSeo.escapeRegExp(key_phrase.trim()), 'gi');
      }
      const descMatch = desc.match(myPattern);
      if (descMatch !== null && descMatch.length <= 2) {
        etsSeo.getAnalysisMessage(
            '#analysis-result--list-' + id_lang,
            'keyphrase_in_meta_desc',
            ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_in_meta_desc.success,
            'success',
        );

        etsSeo.setSeoScore('keyphrase_in_meta_desc', id_lang, 9);
      } else if (descMatch !== null && descMatch.length > 2) {
        if (ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_in_meta_desc.more_than.short_code['[number]']) {
          ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_in_meta_desc.more_than.short_code['[number]'].number = descMatch.length;
        }
        etsSeo.getAnalysisMessage(
            '#analysis-result--list-' + id_lang,
            'keyphrase_in_meta_desc',
            ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_in_meta_desc.more_than,
            'error',
        );
        etsSeo.setSeoScore('keyphrase_in_meta_desc', id_lang, 3);
      } else {
        etsSeo.getAnalysisMessage(
            '#analysis-result--list-' + id_lang,
            'keyphrase_in_meta_desc',
            ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_in_meta_desc.error,
            'error',
        );

        etsSeo.setSeoScore('keyphrase_in_meta_desc', id_lang, 3);
      }
    },

    keyphraseInSlug: function(id_lang, key_phrase, slug) {
      key_phrase = key_phrase || '';
      slug = slug || '';
      if (!key_phrase || !slug) {
        etsSeo.setSeoScore('keyphrase_in_slug', id_lang, 0);
        etsSeo.removeAnalysisMessage('#analysis-result--list-' + id_lang,
            'keyphrase_in_slug');
        return;
      }

      let slugArray = slug.toLowerCase().match(/[0-9a-z'\-]+/gi);
      const kpArray = key_phrase.toLowerCase().match(/[0-9a-z'\-]+/gi);
      slugArray = slugArray.join('-').split('-'); // new code
      const matched = $.grep(slugArray, function(element) {
        return $.inArray(element, kpArray) !== -1;
      });

      if (matched.length == kpArray.length) {
        etsSeo.getAnalysisMessage(
            '#analysis-result--list-' + id_lang,
            'keyphrase_in_slug',
            ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_in_slug.success,
            'success',
        );
        etsSeo.setSeoScore('keyphrase_in_slug', id_lang, 9);
      } else if (matched.length) {
        etsSeo.getAnalysisMessage(
            '#analysis-result--list-' + id_lang,
            'keyphrase_in_slug',
            ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_in_slug.good,
            'success',
        );
        etsSeo.setSeoScore('keyphrase_in_slug', id_lang, 9);
      } else {
        etsSeo.getAnalysisMessage(
            '#analysis-result--list-' + id_lang,
            'keyphrase_in_slug',
            ETS_SEO_DEFINED.seo_analysis_rules.keyphrase_in_slug.warning,
            'warning',
        );
        etsSeo.setSeoScore('keyphrase_in_slug', id_lang, 6);
      }
    },
    minorKeyphraseLength: function(id_lang, minor_keyphrase) {
      minor_keyphrase = minor_keyphrase || '';
      if (!minor_keyphrase || !minor_keyphrase.length) {
        etsSeo.setSeoScore('minor_keyphrase_length', id_lang, 3);
        return;
      }
      const minor_errors = [];
      $.each(minor_keyphrase, function(i, item) {
        if (item.trim().split(/ /g).length > 4) {
          minor_errors.push(item);
        }
      });
      if (minor_errors.length) {
        if (ETS_SEO_DEFINED.seo_analysis_rules.minor_keyphrase_length.too_long.short_code['[count_length]']) {
          ETS_SEO_DEFINED.seo_analysis_rules.minor_keyphrase_length.too_long.short_code['[count_length]'].number = minor_errors[0].trim().split(/ /g).length;
        }
        if (ETS_SEO_DEFINED.seo_analysis_rules.minor_keyphrase_length.too_long.short_code['[minor_keyphrase]']) {
          ETS_SEO_DEFINED.seo_analysis_rules.minor_keyphrase_length.too_long.short_code['[minor_keyphrase]'].string = minor_errors[0];
        }
        etsSeo.getAnalysisMessage(
            '#analysis-result--list-' + id_lang,
            'minor_keyphrase_length',
            ETS_SEO_DEFINED.seo_analysis_rules.minor_keyphrase_length.too_long,
            'warning',
        );
        etsSeo.setSeoScore('minor_keyphrase_length', id_lang, 3);
      } else {
        etsSeo.getAnalysisMessage(
            '#analysis-result--list-' + id_lang,
            'minor_keyphrase_length',
            ETS_SEO_DEFINED.seo_analysis_rules.minor_keyphrase_length.success,
            'success',
        );
        etsSeo.setSeoScore('minor_keyphrase_length', id_lang, 9);
      }
    },
    minorKeyphraseInContent: function(id_lang, minor_keyphrase, content) {
      minor_keyphrase = minor_keyphrase || [];
      content = content || '';
      content = etsSeo.replaceNbsps(content);
      // minor_keyphrase mus be an array
      if (!minor_keyphrase || !content || !minor_keyphrase.length || etsSeoBo.currentController == 'AdminMeta') {
        etsSeo.setSeoScore('minor_keyphrase_in_content', id_lang, 9);
        if (!content && etsSeoBo.currentController == 'AdminMeta') {
          etsSeo.setSeoScore('minor_keyphrase_in_content_individual', id_lang, 9);
        } else {
          etsSeo.setSeoScore('minor_keyphrase_in_content_individual', id_lang, 0);
        }
        etsSeo.removeAnalysisMessage('#analysis-result--list-' + id_lang,
            'minor_keyphrase_in_content');
        etsSeo.removeAnalysisMessage('#analysis-result--list-' + id_lang,
            'minor_keyphrase_in_content_individual');
        return;
      }
      const minor_success = [];
      const minor_errors = [];
      const minor_over_limited = [];
      const minor_less_than = [];
      const wordsLength = content.split(/ /g).length;
      let minorKeyLengthRecommended = Math.ceil(wordsLength * 0.03);
      if (minorKeyLengthRecommended < 3) {
        minorKeyLengthRecommended = 3;
      }
      let minorKeyLengthRecommendedMin = Math.ceil(wordsLength * 0.003);
      if (minorKeyLengthRecommendedMin < 3) {
        minorKeyLengthRecommendedMin = 3;
      }

      $.each(minor_keyphrase, function(i, item) {
        const myPattern = new RegExp('(?:^|\\s)' + etsSeo.escapeRegExp(item) + '(?:$|\\s|[^\\d\\W])', 'gi');
        const resultMatched = content.match(myPattern);
        if (resultMatched != null && resultMatched.length) {
          const counter = resultMatched.length;
          if (counter >= 3) {
            const ratio = counter / wordsLength * 100;
            if (ratio > 0.3 && ratio <= 3) {
              minor_success.push(item);
            } else if (ratio > 3 && ratio <= 4 && counter > minorKeyLengthRecommended) {
              minor_over_limited.push({key: item, count: resultMatched.length});
            } else if (ratio > 0 && ratio <= 0.3 && counter < minorKeyLengthRecommendedMin) {
              minor_less_than.push({key: item, count: resultMatched.length});
            } else if (counter > minorKeyLengthRecommended && ratio > 4) {
              minor_over_limited.push({key: item, count: resultMatched.length});
            } else {
              minor_success.push(item);
            }
          } else {
            minor_less_than.push({key: item, count: resultMatched.length});
          }
        } else {
          minor_errors.push(item);
        }
      });

      if (minor_errors.length || minor_over_limited.length || minor_less_than.length) {
        if (minor_success.length) {
          etsSeo.setSeoScore('minor_keyphrase_in_content', id_lang, 5);
        } else {
          etsSeo.setSeoScore('minor_keyphrase_in_content', id_lang, 3);
        }
        if (minor_errors.length) {
          if (ETS_SEO_DEFINED.seo_analysis_rules.minor_keyphrase_in_content.error.short_code['[minor_keyphrase]']) {
            ETS_SEO_DEFINED.seo_analysis_rules.minor_keyphrase_in_content.error.short_code['[minor_keyphrase]'].string = minor_errors.join(', ');
          }
          etsSeo.getAnalysisMessage(
              '#analysis-result--list-' + id_lang,
              'minor_keyphrase_in_content',
              ETS_SEO_DEFINED.seo_analysis_rules.minor_keyphrase_in_content.error,
              'error',
          );
        } else if (minor_less_than.length) {
          if (ETS_SEO_DEFINED.seo_analysis_rules.minor_keyphrase_in_content.less_than.short_code['[minor_keyphrase]']) {
            ETS_SEO_DEFINED.seo_analysis_rules.minor_keyphrase_in_content.less_than.short_code['[minor_keyphrase]'].string = minor_less_than[0].key;
          }
          if (ETS_SEO_DEFINED.seo_analysis_rules.minor_keyphrase_in_content.less_than.short_code['[count_word]']) {
            ETS_SEO_DEFINED.seo_analysis_rules.minor_keyphrase_in_content.less_than.short_code['[count_word]'].number = minor_less_than[0].count;
          }
          if (ETS_SEO_DEFINED.seo_analysis_rules.minor_keyphrase_in_content.less_than.short_code['[recommended_minor_keyphrase_length]']) {
            ETS_SEO_DEFINED.seo_analysis_rules.minor_keyphrase_in_content.less_than.short_code['[recommended_minor_keyphrase_length]'].number = minorKeyLengthRecommendedMin;
          }
          etsSeo.getAnalysisMessage(
              '#analysis-result--list-' + id_lang,
              'minor_keyphrase_in_content',
              ETS_SEO_DEFINED.seo_analysis_rules.minor_keyphrase_in_content.less_than,
              'error',
          );
        } else {
          if (ETS_SEO_DEFINED.seo_analysis_rules.minor_keyphrase_in_content.over_limited.short_code['[minor_keyphrase]']) {
            ETS_SEO_DEFINED.seo_analysis_rules.minor_keyphrase_in_content.over_limited.short_code['[minor_keyphrase]'].string = minor_over_limited[0].key;
          }
          if (ETS_SEO_DEFINED.seo_analysis_rules.minor_keyphrase_in_content.over_limited.short_code['[count_word]']) {
            ETS_SEO_DEFINED.seo_analysis_rules.minor_keyphrase_in_content.over_limited.short_code['[count_word]'].number = minor_over_limited[0].count;
          }
          if (ETS_SEO_DEFINED.seo_analysis_rules.minor_keyphrase_in_content.over_limited.short_code['[recommended_minor_keyphrase_length]']) {
            ETS_SEO_DEFINED.seo_analysis_rules.minor_keyphrase_in_content.over_limited.short_code['[recommended_minor_keyphrase_length]'].number = minorKeyLengthRecommended;
          }
          etsSeo.getAnalysisMessage(
              '#analysis-result--list-' + id_lang,
              'minor_keyphrase_in_content',
              ETS_SEO_DEFINED.seo_analysis_rules.minor_keyphrase_in_content.over_limited,
              'error',
          );
        }
      } else {
        etsSeo.getAnalysisMessage(
            '#analysis-result--list-' + id_lang,
            'minor_keyphrase_in_content',
            ETS_SEO_DEFINED.seo_analysis_rules.minor_keyphrase_in_content.success,
            'success',
        );
        etsSeo.setSeoScore('minor_keyphrase_in_content', id_lang, 9);
      }
      etsSeo.rules.minorKeyphraseIndividual(id_lang, minor_keyphrase, content);
    },
    minorKeyphraseIndividual: function(id_lang, minor_keyphrase, content) {
      minor_keyphrase = minor_keyphrase || [];
      content = content || '';
      if (!minor_keyphrase || !content || !minor_keyphrase.length) {
        etsSeo.removeAnalysisMessage('#analysis-result--list-' + id_lang,
            'minor_keyphrase_in_content_individual');
        etsSeo.setSeoScore('minor_keyphrase_in_content_individual', id_lang, 9);
        return false;
      }
      const errorItems = [];
      const individualValid = [];
      for (let k = 0; k < minor_keyphrase.length; k++) {
        const minorItem = minor_keyphrase[k];
        if (minorItem.trim().indexOf(' ') !== -1) {
          individualValid.push(minorItem);
          const subContent = content.replace(new RegExp('(?:^|\\s)' + etsSeo.escapeRegExp(minorItem.trim()) + '(?:$|\\s|[^\\d\\W])', 'ig'), '');
          const keyphraseIndividuals = minorItem.trim().split(/\s+/);
          const wordsLength = subContent.split(/ /g).length;
          var recommendedLength = Math.ceil(wordsLength * 0.003);
          if (recommendedLength < 1) {
            recommendedLength = 1;
          }
          for (let i = 0; i < keyphraseIndividuals.length; i++) {
            const desityAppearLength = (subContent.match(new RegExp(etsSeo.escapeRegExp(keyphraseIndividuals[i]), 'g')) || []).length;
            if (desityAppearLength < recommendedLength) {
              errorItems.push({key: keyphraseIndividuals[i], count: desityAppearLength});
              break;
            }
          }
        }
        if (errorItems.length) {
          break;
        }
      }
      if (!individualValid.length) {
        etsSeo.removeAnalysisMessage('#analysis-result--list-' + id_lang,
            'minor_keyphrase_in_content_individual');
        etsSeo.setSeoScore('minor_keyphrase_in_content_individual', id_lang, 9);
        return false;
      }

      if (errorItems.length) {
        if (ETS_SEO_DEFINED.seo_analysis_rules.minor_keyphrase_in_content_individual.error.short_code['[keyphrase_individual]']) {
          ETS_SEO_DEFINED.seo_analysis_rules.minor_keyphrase_in_content_individual.error.short_code['[keyphrase_individual]'].string = errorItems[0].key;
        }
        if (ETS_SEO_DEFINED.seo_analysis_rules.minor_keyphrase_in_content_individual.error.short_code['[recommended_keyphrase_length]']) {
          ETS_SEO_DEFINED.seo_analysis_rules.minor_keyphrase_in_content_individual.error.short_code['[recommended_keyphrase_length]'].number = recommendedLength;
        }
        etsSeo.getAnalysisMessage(
            '#analysis-result--list-' + id_lang,
            'minor_keyphrase_in_content_individual',
            ETS_SEO_DEFINED.seo_analysis_rules.minor_keyphrase_in_content_individual.error,
            'error',
        );
        etsSeo.setSeoScore('minor_keyphrase_in_content_individual', id_lang, 3);
      } else {
        etsSeo.getAnalysisMessage(
            '#analysis-result--list-' + id_lang,
            'minor_keyphrase_in_content_individual',
            ETS_SEO_DEFINED.seo_analysis_rules.minor_keyphrase_in_content_individual.success,
            'success',
        );
        etsSeo.setSeoScore('minor_keyphrase_in_content_individual', id_lang, 9);
      }
    },
    minorKeyphraseInMetaTitle: function(id_lang, minor_keyphrase, title) {
      minor_keyphrase = minor_keyphrase || [];
      title = title || '';
      if (!title || ETS_SEO_FORCE_USE_META_TEMPLATE) {
        title = etsSeo.getMetaTitle(id_lang);
      }

      if (!minor_keyphrase || !title || !minor_keyphrase.length) {
        if (minor_keyphrase && !title) {
          const prefix = etsSeo.prefixInput();
          const pageTitle = prefix.title ? (etsSeoBo.currentController != 'AdminManufacturers' && etsSeoBo.currentController != 'AdminSuppliers' ? $(prefix.title + id_lang).val() : $(prefix.title.slice(0, -1)).val()) : '';
          etsSeo.rules.minorKeyphraseInPageTitle(id_lang, minor_keyphrase, pageTitle);
        }
        etsSeo.setSeoScore('minor_keyphrase_in_title', id_lang, 9);
        etsSeo.removeAnalysisMessage('#analysis-result--list-' + id_lang,
            'minor_keyphrase_in_title');
        return;
      }
      const minor_success = [];
      let minor_errors = [];
      const over_limited = [];
      title = etsSeo.renderMetaData(title, id_lang, true);
      $.each(minor_keyphrase, function(i, item) {
        let myPattern = new RegExp('(?:^|\\s)' + etsSeo.escapeRegExp(item) + '(?:$|\\s|[^\\d\\W])', 'gi');
        if (title.trim().length == item.trim().length) {
          myPattern = new RegExp(etsSeo.escapeRegExp(item.trim()), 'gi');
        }
        const resultMatched = title.match(myPattern);
        if (resultMatched != null && resultMatched.length) {
          if (resultMatched.length > 2 && !over_limited.length) {
            over_limited.push({key: item, count: resultMatched.length});
          }
          minor_success.push(item);
        } else {
          minor_errors.push(item);
        }
      });

      etsSeo.removeAnalysisMessage('#analysis-result--list-' + id_lang,
          'minor_keyphrase_acceptance');
      etsSeo.removeAnalysisMessage('#analysis-result--list-' + id_lang,
          'minor_keyphrase_in_title');
      etsSeo.removeAnalysisMessage('#analysis-result--list-' + id_lang,
          'minor_keyphrase_in_page_title');

      if (minor_errors.length || over_limited.length) {
        if (minor_success.length) {
          etsSeo.setSeoScore('minor_keyphrase_in_title', id_lang, 5);
        } else {
          etsSeo.setSeoScore('minor_keyphrase_in_title', id_lang, 3);
        }
        if (minor_errors.length) {
          const listMinorSucess = etsSeo.getMinorKeyphraseInMetaTileDesc(id_lang);
          minor_errors = etsSeo.differenceOf2Arrays(minor_errors, listMinorSucess);
          if (minor_errors.length) {
            if (ETS_SEO_DEFINED.seo_analysis_rules.minor_keyphrase_in_title.error.short_code['[minor_keyphrase]']) {
              ETS_SEO_DEFINED.seo_analysis_rules.minor_keyphrase_in_title.error.short_code['[minor_keyphrase]'].string = minor_errors.join(', ');
            }
            etsSeo.getAnalysisMessage(
                '#analysis-result--list-' + id_lang,
                'minor_keyphrase_in_title',
                ETS_SEO_DEFINED.seo_analysis_rules.minor_keyphrase_in_title.error,
                'error',
            );
          } else if (minor_keyphrase.length == listMinorSucess.length) {
            etsSeo.getAnalysisMessage(
                '#analysis-result--list-' + id_lang,
                'minor_keyphrase_acceptance',
                ETS_SEO_DEFINED.seo_analysis_rules.minor_keyphrase_acceptance.success,
                'success',
            );
          }
        } else {
          if (ETS_SEO_DEFINED.seo_analysis_rules.minor_keyphrase_in_title.over_limited.short_code['[minor_keyphrase]']) {
            ETS_SEO_DEFINED.seo_analysis_rules.minor_keyphrase_in_title.over_limited.short_code['[minor_keyphrase]'].string = over_limited[0].key;
          }
          if (ETS_SEO_DEFINED.seo_analysis_rules.minor_keyphrase_in_title.over_limited.short_code['[count_word]']) {
            ETS_SEO_DEFINED.seo_analysis_rules.minor_keyphrase_in_title.over_limited.short_code['[count_word]'].string = over_limited[0].count;
          }
          etsSeo.getAnalysisMessage(
              '#analysis-result--list-' + id_lang,
              'minor_keyphrase_in_title',
              ETS_SEO_DEFINED.seo_analysis_rules.minor_keyphrase_in_title.over_limited,
              'error',
          );
        }
      } else {
        etsSeo.removeAnalysisMessage('#analysis-result--list-' + id_lang,
            'minor_keyphrase_in_title');
        etsSeo.removeAnalysisMessage('#analysis-result--list-' + id_lang,
            'minor_keyphrase_in_page_title');
        etsSeo.removeAnalysisMessage('#analysis-result--list-' + id_lang,
            'minor_keyphrase_in_desc');
        etsSeo.getAnalysisMessage(
            '#analysis-result--list-' + id_lang,
            'minor_keyphrase_acceptance',
            ETS_SEO_DEFINED.seo_analysis_rules.minor_keyphrase_acceptance.success,
            'success',
        );
        etsSeo.setSeoScore('minor_keyphrase_in_title', id_lang, 9);
      }
      etsSeo.setSeoScore('minor_keyphrase_acceptance', id_lang, 9);
      etsSeo.setSeoScore('minor_keyphrase_in_page_title', id_lang, 9);
    },
    minorKeyphraseInPageTitle: function(id_lang, minor_keyphrase, title) {
      minor_keyphrase = minor_keyphrase || [];
      title = title || '';
      if (!minor_keyphrase || !title || !minor_keyphrase.length) {
        etsSeo.setSeoScore('minor_keyphrase_in_page_title', id_lang, 9);
        etsSeo.removeAnalysisMessage('#analysis-result--list-' + id_lang,
            'minor_keyphrase_in_page_title');
        return;
      }
      const minor_success = [];
      let minor_errors = [];
      const over_limited = [];
      title = etsSeo.renderMetaData(title, id_lang, true);
      $.each(minor_keyphrase, function(i, item) {
        let myPattern = new RegExp('(?:^|\\s)' + etsSeo.escapeRegExp(item) + '(?:$|\\s|[^\\d\\W])', 'gi');
        if (title.trim().length == item.trim().length) {
          myPattern = new RegExp(etsSeo.escapeRegExp(item.trim()), 'gi');
        }
        const resultMatched = title.match(myPattern);
        if (resultMatched != null && resultMatched.length) {
          if (resultMatched.length > 2 && !over_limited.length) {
            over_limited.push({key: item, count: resultMatched.length});
          }
          minor_success.push(item);
        } else {
          minor_errors.push(item);
        }
      });

      etsSeo.removeAnalysisMessage('#analysis-result--list-' + id_lang,
          'minor_keyphrase_acceptance');
      etsSeo.removeAnalysisMessage('#analysis-result--list-' + id_lang,
          'minor_keyphrase_in_page_title');

      if (minor_errors.length || over_limited.length) {
        if (minor_success.length) {
          etsSeo.setSeoScore('minor_keyphrase_in_page_title', id_lang, 5);
        } else {
          etsSeo.setSeoScore('minor_keyphrase_in_page_title', id_lang, 3);
        }
        if (minor_errors.length) {
          const listMinorSucess = etsSeo.getMinorKeyphraseInMetaTileDesc(id_lang);
          minor_errors = etsSeo.differenceOf2Arrays(minor_errors, listMinorSucess);

          if (minor_errors.length) {
            if (ETS_SEO_DEFINED.seo_analysis_rules.minor_keyphrase_in_page_title.error.short_code['[minor_keyphrase]']) {
              ETS_SEO_DEFINED.seo_analysis_rules.minor_keyphrase_in_page_title.error.short_code['[minor_keyphrase]'].string = minor_errors.join(', ');
            }
            etsSeo.getAnalysisMessage(
                '#analysis-result--list-' + id_lang,
                'minor_keyphrase_in_page_title',
                ETS_SEO_DEFINED.seo_analysis_rules.minor_keyphrase_in_page_title.error,
                'error',
            );
          } else if (minor_keyphrase.length == listMinorSucess.length) {
            etsSeo.getAnalysisMessage(
                '#analysis-result--list-' + id_lang,
                'minor_keyphrase_acceptance',
                ETS_SEO_DEFINED.seo_analysis_rules.minor_keyphrase_acceptance.success,
                'success',
            );
          }
        } else {
          if (ETS_SEO_DEFINED.seo_analysis_rules.minor_keyphrase_in_page_title.over_limited.short_code['[minor_keyphrase]']) {
            ETS_SEO_DEFINED.seo_analysis_rules.minor_keyphrase_in_page_title.over_limited.short_code['[minor_keyphrase]'].string = over_limited[0].key;
          }
          if (ETS_SEO_DEFINED.seo_analysis_rules.minor_keyphrase_in_page_title.over_limited.short_code['[count_word]']) {
            ETS_SEO_DEFINED.seo_analysis_rules.minor_keyphrase_in_page_title.over_limited.short_code['[count_word]'].string = over_limited[0].count;
          }
          etsSeo.getAnalysisMessage(
              '#analysis-result--list-' + id_lang,
              'minor_keyphrase_in_page_title',
              ETS_SEO_DEFINED.seo_analysis_rules.minor_keyphrase_in_page_title.over_limited,
              'error',
          );
        }
      } else {
        etsSeo.removeAnalysisMessage('#analysis-result--list-' + id_lang,
            'minor_keyphrase_in_title');
        etsSeo.removeAnalysisMessage('#analysis-result--list-' + id_lang,
            'minor_keyphrase_in_page_title');
        etsSeo.removeAnalysisMessage('#analysis-result--list-' + id_lang,
            'minor_keyphrase_in_desc');
        etsSeo.getAnalysisMessage(
            '#analysis-result--list-' + id_lang,
            'minor_keyphrase_acceptance',
            ETS_SEO_DEFINED.seo_analysis_rules.minor_keyphrase_acceptance.success,
            'success',
        );
        etsSeo.setSeoScore('minor_keyphrase_in_page_title', id_lang, 9);
      }
      etsSeo.setSeoScore('minor_keyphrase_acceptance', id_lang, 9);
    },
    minorKeyphraseInMetaDesc: function(id_lang, minor_keyphrase, desc) {
      minor_keyphrase = minor_keyphrase || [];
      desc = desc || '';
      if (!desc || ETS_SEO_FORCE_USE_META_TEMPLATE) {
        desc = etsSeo.getMetaDesc(id_lang);
      }

      if (!minor_keyphrase || !desc || !minor_keyphrase.length) {
        etsSeo.setSeoScore('minor_keyphrase_in_desc', id_lang, 9);
        etsSeo.removeAnalysisMessage('#analysis-result--list-' + id_lang,
            'minor_keyphrase_in_desc');
        return;
      }
      const minor_success = [];
      const minor_errors = [];
      const over_limited = [];
      desc = etsSeo.renderMetaData(desc, id_lang, false);
      $.each(minor_keyphrase, function(i, item) {
        let myPattern = new RegExp('(?:^|\\s)' + etsSeo.escapeRegExp(item) + '(?:$|\\s|[^\\d\\W])', 'gi');
        if (desc.trim().length == item.trim().length) {
          myPattern = new RegExp(etsSeo.escapeRegExp(item.trim()), 'gi');
        }
        const resultMatched = desc.match(myPattern);
        if (resultMatched != null && resultMatched.length) {
          if (resultMatched.length > 2 && !over_limited.length) {
            over_limited.push({key: item, count: resultMatched.length});
          }
          minor_success.push(item);
        } else {
          minor_errors.push(item);
        }
      });

      if (minor_errors.length || over_limited.length) {
        if (minor_success.length) {
          etsSeo.setSeoScore('minor_keyphrase_in_desc', id_lang, 5);
        } else {
          etsSeo.setSeoScore('minor_keyphrase_in_desc', id_lang, 3);
        }
        if (minor_errors.length) {
          if (ETS_SEO_DEFINED.seo_analysis_rules.minor_keyphrase_in_desc.error.short_code['[minor_keyphrase]']) {
            ETS_SEO_DEFINED.seo_analysis_rules.minor_keyphrase_in_desc.error.short_code['[minor_keyphrase]'].string = minor_errors.join(', ');
          }
          etsSeo.getAnalysisMessage(
              '#analysis-result--list-' + id_lang,
              'minor_keyphrase_in_desc',
              ETS_SEO_DEFINED.seo_analysis_rules.minor_keyphrase_in_desc.error,
              'error',
          );
        } else {
          if (ETS_SEO_DEFINED.seo_analysis_rules.minor_keyphrase_in_desc.over_limited.short_code['[minor_keyphrase]']) {
            ETS_SEO_DEFINED.seo_analysis_rules.minor_keyphrase_in_desc.over_limited.short_code['[minor_keyphrase]'].string = over_limited[0].key;
          }
          if (ETS_SEO_DEFINED.seo_analysis_rules.minor_keyphrase_in_desc.over_limited.short_code['[count_word]']) {
            ETS_SEO_DEFINED.seo_analysis_rules.minor_keyphrase_in_desc.over_limited.short_code['[count_word]'].number = over_limited[0].count;
          }

          etsSeo.getAnalysisMessage(
              '#analysis-result--list-' + id_lang,
              'minor_keyphrase_in_desc',
              ETS_SEO_DEFINED.seo_analysis_rules.minor_keyphrase_in_desc.over_limited,
              'error',
          );
        }
      } else {
        etsSeo.getAnalysisMessage(
            '#analysis-result--list-' + id_lang,
            'minor_keyphrase_in_desc',
            ETS_SEO_DEFINED.seo_analysis_rules.minor_keyphrase_in_desc.success,
            'success',
        );
        etsSeo.setSeoScore('minor_keyphrase_in_desc', id_lang, 9);
      }
    },
  },

  // Readability rules
  readability: {
    notEnoughContent: function(id_lang, content) {
      // Minimum 50 characters
      content = content || '';
      if (!content || content.replace(/\s/g, '').length < 50) {
        etsSeo.getAnalysisMessage(
            '#analysis-result--list-readablity-' + id_lang,
            'not_enough_content',
            ETS_SEO_DEFINED.readability_rules.not_enough_content.error,
            'error',
        );
        etsSeo.setReadabilityScore('not_enough_content', id_lang, 0);
      } else {
        etsSeo.removeAnalysisMessage(
            '#analysis-result--list-readablity-' + id_lang,
            'not_enough_content',
        );
        etsSeo.setReadabilityScore('not_enough_content', id_lang, 9);
      }
    },

    sentenceLength: function(id_lang, content) {
      content = content || '';
      if (!content) {
        etsSeo.setReadabilityScore('sentence_length', id_lang, 0);
        etsSeo.removeAnalysisMessage('#analysis-result--list-readablity-' + id_lang,
            'sentence_length');
      }
      // Maximum 25% sentence more than 20 words
      content = content.replace(/<br\s*\/?>/g, '\n').stripHtmlTags('mark');
      const sentences = content.split(/[.!?]/g);
      const total_sentence = sentences.length;
      let total_sentence_max_20_words = 0;
      for (let i = 0; i < sentences.length; i++) {
        const curSentence = sentences[i].replace(/<\/?[a-z][^>]*?>/gi, '')
            .split(/\s+/)
            .filter((v) => v.trim() != '');
        if (curSentence.length <= 20) {
          total_sentence_max_20_words++;
        }
      }
      const percent_good = total_sentence_max_20_words / total_sentence * 100;
      const percent_bad = 100 - percent_good;
      const sentenceRule = ETS_SEO_DEFINED && ETS_SEO_DEFINED.readability_rules ? ETS_SEO_DEFINED.readability_rules.sentence_length : null;
      if (percent_bad <= 25) {
        if (sentenceRule && sentenceRule.error && sentenceRule.error.short_code && sentenceRule.error.short_code['[link_js]']) {
          sentenceRule.error.short_code['[link_js]'].show = false;
        }
        etsSeo.getAnalysisMessage(
            '#analysis-result--list-readablity-' + id_lang,
            'sentence_length',
            ETS_SEO_DEFINED.readability_rules.sentence_length.success,
            'success',
        );
        etsSeo.setReadabilityScore('sentence_length', id_lang, 9);
      } else if (percent_bad > 25 && percent_bad <= 30) {
        if (sentenceRule && sentenceRule.error && sentenceRule.error.short_code) {
          if (sentenceRule.error.short_code['[number]']) {
            sentenceRule.error.short_code['[number]'].number = Math.round(100 - percent_good);
          }
          if (sentenceRule.error.short_code['[link_js]']) {
            sentenceRule.error.short_code['[link_js]'].link = `etsSeoAdmin.highlightBadSentences(${id_lang})`;
            sentenceRule.error.short_code['[link_js]'].show = false;
          }
        }
        etsSeo.getAnalysisMessage(
            '#analysis-result--list-readablity-' + id_lang,
            'sentence_length',
            ETS_SEO_DEFINED.readability_rules.sentence_length.error,
            'warning',
        );

        etsSeo.setReadabilityScore('sentence_length', id_lang, 6);
      } else {
        if (sentenceRule && sentenceRule.error && sentenceRule.error.short_code) {
          if (sentenceRule.error.short_code['[number]']) {
            sentenceRule.error.short_code['[number]'].number = Math.round(100 - percent_good);
          }
          if (sentenceRule.error.short_code['[link_js]']) {
            sentenceRule.error.short_code['[link_js]'].link = `etsSeoAdmin.highlightBadSentences(${id_lang})`;
            sentenceRule.error.short_code['[link_js]'].show = false;
          }
        }
        etsSeo.getAnalysisMessage(
            '#analysis-result--list-readablity-' + id_lang,
            'sentence_length',
            ETS_SEO_DEFINED.readability_rules.sentence_length.error,
            'error',
        );
        etsSeo.setReadabilityScore('sentence_length', id_lang, 3);
      }
    },

    fleschReadingEase: function(id_lang, content) {
      content = content || '';
      if (!content) {
        etsSeo.setReadabilityScore('flesch_reading_ease', id_lang, 0);
        etsSeo.removeAnalysisMessage('#analysis-result--list-readablity-' + id_lang,
            'flesch_reading_ease');
        return;
      }
      let iso_code = null;
      Object.keys(ETS_SEO_LANGUAGES).forEach(function(key) {
        if (ETS_SEO_LANGUAGES[key] == id_lang) {
          iso_code = key;
        }
      });
      if (iso_code !== 'en') {
        etsSeo.getAnalysisMessage(
            '#analysis-result--list-readablity-' + id_lang,
            'flesch_reading_ease',
            ETS_SEO_DEFINED.readability_rules.flesch_reading_ease.success,
            'success',
        );
        etsSeo.setReadabilityScore('flesch_reading_ease', id_lang, 9);
        return;
      }
      const score = Math.round(etsSeo.fleschReadingEase.score(content).score);
      if (score < 30) {
        if (ETS_SEO_DEFINED.readability_rules.flesch_reading_ease.error.short_code['[score]']) {
          ETS_SEO_DEFINED.readability_rules.flesch_reading_ease.error.short_code['[score]'].number = score;
        }
        etsSeo.getAnalysisMessage(
            '#analysis-result--list-readablity-' + id_lang,
            'flesch_reading_ease',
            ETS_SEO_DEFINED.readability_rules.flesch_reading_ease.error,
            'error',
        );
        etsSeo.setReadabilityScore('flesch_reading_ease', id_lang, 3);
      } else if (score >= 50 && score <= 60) {
        if (ETS_SEO_DEFINED.readability_rules.flesch_reading_ease.warning.short_code['[score]']) {
          ETS_SEO_DEFINED.readability_rules.flesch_reading_ease.warning.short_code['[score]'].number = score;
        }
        etsSeo.getAnalysisMessage(
            '#analysis-result--list-readablity-' + id_lang,
            'flesch_reading_ease',
            ETS_SEO_DEFINED.readability_rules.flesch_reading_ease.warning,
            'warning',
        );
        etsSeo.setReadabilityScore('flesch_reading_ease', id_lang, 6);
      } else {
        if (ETS_SEO_DEFINED.readability_rules.flesch_reading_ease.success.short_code['[score]']) {
          ETS_SEO_DEFINED.readability_rules.flesch_reading_ease.success.short_code['[score]'].number = score;
        }
        etsSeo.getAnalysisMessage(
            '#analysis-result--list-readablity-' + id_lang,
            'flesch_reading_ease',
            ETS_SEO_DEFINED.readability_rules.flesch_reading_ease.success,
            'success',
        );
        etsSeo.setReadabilityScore('flesch_reading_ease', id_lang, 9);
      }
    },

    paragraphLength: function(id_lang, content) {
      content = content || '';
      if (!content) {
        etsSeo.setReadabilityScore('paragraph_length', id_lang, 0);
        etsSeo.removeAnalysisMessage('#analysis-result--list-readablity-' + id_lang,
            'paragraph_length');
        return;
      }
      // Each paragraph should not over 150 words
      const tmp = document.createElement('div');
      tmp.innerHTML = content;
      const contentHTML = tmp.getElementsByTagName('p');
      let paragraph_over_150_words = 0;
      let paragraph_over_200_words = 0;
      for (let i = 0; i < contentHTML.length; i++) {
        const text_content_length = contentHTML[i].textContent.split(/\s+/).length;
        if (text_content_length > 150) {
          paragraph_over_150_words++;
          if (text_content_length > 200) {
            paragraph_over_200_words++;
          }
        }
      }
      if (paragraph_over_200_words) {
        if (ETS_SEO_DEFINED.readability_rules.paragraph_length.error.short_code['[number]']) {
          ETS_SEO_DEFINED.readability_rules.paragraph_length.error.short_code['[number]'].number = paragraph_over_150_words;
        }

        etsSeo.getAnalysisMessage(
            '#analysis-result--list-readablity-' + id_lang,
            'paragraph_length',
            ETS_SEO_DEFINED.readability_rules.paragraph_length.error,
            'error',
        );

        etsSeo.setReadabilityScore('paragraph_length', id_lang, 3);
      } else if (paragraph_over_150_words) {
        if (ETS_SEO_DEFINED.readability_rules.paragraph_length.warning.short_code['[number]']) {
          ETS_SEO_DEFINED.readability_rules.paragraph_length.warning.short_code['[number]'].number = paragraph_over_150_words;
        }

        etsSeo.getAnalysisMessage(
            '#analysis-result--list-readablity-' + id_lang,
            'paragraph_length',
            ETS_SEO_DEFINED.readability_rules.paragraph_length.warning,
            'warning',
        );
        etsSeo.setReadabilityScore('paragraph_length', id_lang, 6);
      } else {
        etsSeo.getAnalysisMessage(
            '#analysis-result--list-readablity-' + id_lang,
            'paragraph_length',
            ETS_SEO_DEFINED.readability_rules.paragraph_length.success,
            'success',
        );
        etsSeo.setReadabilityScore('paragraph_length', id_lang, 9);
      }
    },

    consecutiveSentences: function(id_lang, content) {
      content = content || '';
      if (!content) {
        etsSeo.setReadabilityScore('consecutive_sentences', id_lang, 0);
        etsSeo.removeAnalysisMessage('#analysis-result--list-readablity-' + id_lang,
            'consecutive_sentences');
        return;
      }

      // If The text contains >=3 consecutive sentences starting with the same word, it not good
      const sentences = content.split(/[.!?][ |\n]/g);
      const first_word_of_sentence = [];
      let firstWordOfSentence = '';
      let countWord = 0;
      const consecutive = [];

      for (let i = 0; i < sentences.length; i++) {
        if (sentences[i]) {
          let first_word = sentences[i].replace(/\r|\n/, '').trim().split(/\s+/)[0];
          const parttent = /[a-zA-Z0-9]/;
          if (!parttent.test(first_word)) {
            continue;
          }
          first_word = first_word.toLowerCase();
          if (firstWordOfSentence && firstWordOfSentence == first_word) {
            countWord++;
          } else {
            firstWordOfSentence = first_word;
            countWord = 1;
          }

          if (countWord >= 3 && consecutive.indexOf(first_word) == -1) {
            consecutive.push(first_word);
          }
        }
      }

      if (consecutive.length) {
        if (ETS_SEO_DEFINED.readability_rules.consecutive_sentences.error.short_code['[number]']) {
          ETS_SEO_DEFINED.readability_rules.consecutive_sentences.error.short_code['[number]'].number = consecutive.length;
        }
        etsSeo.getAnalysisMessage(
            '#analysis-result--list-readablity-' + id_lang,
            'consecutive_sentences',
            ETS_SEO_DEFINED.readability_rules.consecutive_sentences.error,
            'error',
        );

        etsSeo.setReadabilityScore('consecutive_sentences', id_lang, 3);
      } else {
        etsSeo.getAnalysisMessage(
            '#analysis-result--list-readablity-' + id_lang,
            'consecutive_sentences',
            ETS_SEO_DEFINED.readability_rules.consecutive_sentences.success,
            'success',
        );
        etsSeo.setReadabilityScore('consecutive_sentences', id_lang, 9);
      }
    },

    subheadingDistribution: function(id_lang, content) {
      content = content || '';
      if (!content) {
        etsSeo.setReadabilityScore('subheading_distribution', id_lang, 0);
        etsSeo.removeAnalysisMessage('#analysis-result--list-readablity-' + id_lang,
            'subheading_distribution');
        return;
      }
      const tmp = document.createElement('div');
      tmp.innerHTML = content;

      let has_heading = false;
      for (let i = 1; i <= 6; i++) {
        const heading = tmp.getElementsByTagName('h' + i);
        if (heading.length) {
          has_heading = true;
          break;
        }
      }

      if (has_heading) {
        etsSeo.getAnalysisMessage(
            '#analysis-result--list-readablity-' + id_lang,
            'subheading_distribution',
            ETS_SEO_DEFINED.readability_rules.subheading_distribution.good,
            'success',
        );
        etsSeo.setReadabilityScore('subheading_distribution', id_lang, 9);
      } else {
        etsSeo.getAnalysisMessage(
            '#analysis-result--list-readablity-' + id_lang,
            'subheading_distribution',
            ETS_SEO_DEFINED.readability_rules.subheading_distribution.success,
            'success',
        );

        etsSeo.setReadabilityScore('subheading_distribution', id_lang, 9);
      }
    },

    transitionWords: function(id_lang, content) {
      content = content || '';
      if (!content) {
        etsSeo.setReadabilityScore('transition_words', id_lang, 0);
        etsSeo.removeAnalysisMessage('#analysis-result--list-readablity-' + id_lang,
            'transition_words');
        return;
      }
      let iso_code = null;
      Object.keys(ETS_SEO_LANGUAGES).forEach(function(key) {
        if (ETS_SEO_LANGUAGES[key] == id_lang) {
          iso_code = key;
        }
      });
      if (iso_code !== 'en') {
        etsSeo.getAnalysisMessage(
            '#analysis-result--list-readablity-' + id_lang,
            'transition_words',
            ETS_SEO_DEFINED.readability_rules.transition_words.success,
            'success',
        );
        etsSeo.setReadabilityScore('transition_words', id_lang, 9);
        return;
      }

      const has_transition_word = [];
      const sentences = content.split(/[.!?][ |\n]/g);

      Object.keys(ETS_SEO_DEFINED.transition_words[iso_code]).forEach(function(key) {
        const words = ETS_SEO_DEFINED.transition_words[iso_code][key].split(',');
        for (let t = 0; t < words.length; t++) {
          const myPattern = new RegExp('(?:^|\\s)' + words[t].trim() + '(?:$|\\s|[^\\d\\W])', 'gi');
          for (let k = 0; k < sentences.length; k++) {
            if (sentences[k].toLowerCase().match(myPattern) !== null) {
              if (has_transition_word.indexOf(k) == -1) {
                has_transition_word.push(k);
              }
            }
          }
        }
      });

      const percent_match = has_transition_word.length / sentences.length * 100;

      if (percent_match >= 30) {
        etsSeo.getAnalysisMessage(
            '#analysis-result--list-readablity-' + id_lang,
            'transition_words',
            ETS_SEO_DEFINED.readability_rules.transition_words.success,
            'success',
        );

        etsSeo.setReadabilityScore('transition_words', id_lang, 9);
      } else if (percent_match >= 20 && percent_match <= 30) {
        if (ETS_SEO_DEFINED.readability_rules.transition_words.little.short_code['[count]']) {
          ETS_SEO_DEFINED.readability_rules.transition_words.little.short_code['[count]'].number = has_transition_word.length;
        }
        etsSeo.getAnalysisMessage(
            '#analysis-result--list-readablity-' + id_lang,
            'transition_words',
            ETS_SEO_DEFINED.readability_rules.transition_words.little,
            'warning',
        );
        etsSeo.setReadabilityScore('transition_words', id_lang, 6);
      } else if (percent_match > 0) {
        if (ETS_SEO_DEFINED.readability_rules.transition_words.too_little.short_code['[count]']) {
          ETS_SEO_DEFINED.readability_rules.transition_words.too_little.short_code['[count]'].number = has_transition_word.length;
        }
        etsSeo.getAnalysisMessage(
            '#analysis-result--list-readablity-' + id_lang,
            'transition_words',
            ETS_SEO_DEFINED.readability_rules.transition_words.too_little,
            'error',
        );
        etsSeo.setReadabilityScore('transition_words', id_lang, 3);
      } else {
        etsSeo.getAnalysisMessage(
            '#analysis-result--list-readablity-' + id_lang,
            'transition_words',
            ETS_SEO_DEFINED.readability_rules.transition_words.error,
            'error',
        );
        etsSeo.setReadabilityScore('transition_words', id_lang, 0);
      }
    },

    passive_voice: function(id_lang, content) {
      content = content || '';
      if (!content) {
        etsSeo.setReadabilityScore('passive_voice', id_lang, 0);
        etsSeo.removeAnalysisMessage('#analysis-result--list-readablity-' + id_lang,
            'passive_voice');
        return;
      }
      let iso_code = null;
      Object.keys(ETS_SEO_LANGUAGES).forEach(function(key) {
        if (ETS_SEO_LANGUAGES[key] == id_lang) {
          iso_code = key;
        }
      });
      if (iso_code !== 'en') {
        return;
      }
      const sentences = content.split(/[.!?][ |\n]/g).length;
      let passive_voice = 0;
      for (let k = 0; k < sentences.length; k++) {
        const matched = content.match(/\b((be(en)?)|(w(as|ere))|(is)|(a(er|m)))(.+(en|ed))([\s]|\.)/g);
        if (matched !== null) {
          passive_voice++;
        }
      }

      const percent_match = passive_voice / sentences.length * 100;
      if (percent_match > 15) {
        if (ETS_SEO_DEFINED.readability_rules.passive_voice.error.short_code['[number]']) {
          ETS_SEO_DEFINED.readability_rules.passive_voice.error.short_code['[number]'].number = Math.round(percent_match);
        }
        etsSeo.getAnalysisMessage(
            '#analysis-result--list-readablity-' + id_lang,
            'passive_voice',
            ETS_SEO_DEFINED.readability_rules.passive_voice.error,
            'success',
        );
        etsSeo.setReadabilityScore('passive_voice', id_lang, 3);
      } else if (percent_match <= 15 && percent_match >= 10) {
        if (ETS_SEO_DEFINED.readability_rules.passive_voice.error.short_code['[number]']) {
          ETS_SEO_DEFINED.readability_rules.passive_voice.error.short_code['[number]'].number = Math.round(percent_match);
        }
        etsSeo.getAnalysisMessage(
            '#analysis-result--list-readablity-' + id_lang,
            'passive_voice',
            ETS_SEO_DEFINED.readability_rules.passive_voice.error,
            'warning',
        );
        etsSeo.setReadabilityScore('passive_voice', id_lang, 6);
      } else {
        etsSeo.getAnalysisMessage(
            '#analysis-result--list-readablity-' + id_lang,
            'passive_voice',
            ETS_SEO_DEFINED.readability_rules.passive_voice.success,
            'success',
        );
        etsSeo.setReadabilityScore('passive_voice', id_lang, 9);
      }
    },

  },

  fleschReadingEase: {
    score: function(text) {
      return {
        score: 206.835 - (1.015 * etsSeo.fleschReadingEase.avgWordsSentance(text)) - (84.6 * etsSeo.fleschReadingEase.avgSyllablesWord(text)),
        gradingLevel: etsSeo.fleschReadingEase.gradingLevel(text),
      };
    },

    gradingLevel: function(text) {
      return ((.39 * etsSeo.fleschReadingEase.avgWordsSentance(text)) + (11.8 * etsSeo.fleschReadingEase.avgSyllablesWord(text)) - 15.59);
    },

    avgWordsSentance: function(text) {
      const sentences = text.split(/[.!?][ |\n]/g).length;
      const words = text.split(/ /g).length;
      return words / sentences;
    },

    avgSyllablesWord: function(text) {
      const words = text.split(/ /g);
      let syllables = 0;
      for (let i = 0; i < words.length; i++) {
        syllables = syllables + etsSeo.fleschReadingEase.countSyllables(words[i]);
      }

      return syllables / words.length;
    },

    countSyllables: function(word) {
      word = word.toLowerCase();
      if (word.length <= 3) {
        return 1;
      }

      word = word.replace(/(?:[^laeiouy]es|ed|[^laeiouy]e)$/, '');
      word = word.replace(/^y/, '');
      word = word.replace(/-/g, '');

      if (word.match(/[aeiouy]{1,2}/g)) {
        return word.match(/[aeiouy]{1,2}/g).length;
      }

      return 1;
    },
  },

  getFirstParagraph: function(text) {
    const tmp = document.createElement('div');
    tmp.innerHTML = text;
    const contentHTML = tmp.getElementsByTagName('p');
    if (contentHTML.length) {
      return contentHTML[0].textContent;
    }
    return '';
  },

  // Get result analysis message
  getAnalysisMessage: function(prefix_el_to_append, rule, message, type) {
    if (etsSeoBo.currentController == 'AdminCmsContent' || etsSeoBo.currentController == 'AdminMeta') {
      prefix_el_to_append = prefix_el_to_append.replace('seo-step-', '');
    }
    let text = message.text;
    if (message.short_code) {
      let textTitle = '';
      Object.keys(message.short_code).forEach(function(key) {
        if (message.short_code[key].type === 'link') {
          if (key === '[link_support]') {
            textTitle = message.short_code[key].text;
          }
          if (key === '[link_doc]') {
            text = text.replace(new RegExp(etsSeo.escapeRegExp(key), 'gi'), '<span class="analysis-text-action">' + message.short_code[key].text + '</span>');
          } else if (key === '[link_js]') {
            if (message.short_code[key].show) {
              text = text.replace(new RegExp(etsSeo.escapeRegExp(key), 'gi'), `<a href="javascript:void(0)" onclick="${message.short_code[key].link};event.preventDefault();" title="${message.short_code[key].text}"><i class="fa fa-eye"></i></a>`);
            } else {
              text = text.replace(new RegExp(etsSeo.escapeRegExp(key), 'gi'), '');
            }
          } else {
            const dataRule = rule;

            text = text.replace(new RegExp(etsSeo.escapeRegExp(key), 'gi'), '<a href="#" class="js-ets-seo-show-explain-rule" data-rule="' + dataRule + '" data-text="' + textTitle + '"><span class="ets-seo-link-explain-rule">' + message.short_code[key].text + '</span></a>');
          }
        } else if (message.short_code[key].type === 'number') {
          text = text.replace(new RegExp(etsSeo.escapeRegExp(key), 'gi'), '<span class="number">' + message.short_code[key].number + '</span>');
        } else if (message.short_code[key].type === 'string') {
          text = text.replace(new RegExp(etsSeo.escapeRegExp(key), 'gi'), '<span class="string">' + message.short_code[key].string + '</span>');
        }
      });
    }

    etsSeo.hideAnalysisMessage(prefix_el_to_append + '-error', rule);
    etsSeo.hideAnalysisMessage(prefix_el_to_append + '-warning', rule);
    etsSeo.hideAnalysisMessage(prefix_el_to_append + '-success', rule);
    if (type) {
      const listSelector = prefix_el_to_append + '-' + type;
      const $list = $(listSelector);
      const hasList = $list.length > 0;

      if (hasList) {
        const $existing = $list.find('li.' + rule);
        if ($existing.length) {
          $existing.html(text);
        } else {
          $list.append('<li class="' + rule + '">' + text + '</li>');
        }

        const $container = $list.parent();
        if ($container.hasClass('hide')) {
          $container.removeClass('hide');
        }
        if ($container.is(':hidden')) {
          $container.css('display', '');
        }

        if ($list.is(':hidden')) {
          $list.css('display', '');
        }

        const $multilangWrapper = $container.closest('.multilang-field');
        if ($multilangWrapper.length && $multilangWrapper.hasClass('lang-' + etsSeoBo.currentActiveLangId)) {
          if ($multilangWrapper.hasClass('hide')) {
            $multilangWrapper.removeClass('hide');
          }
          if ($multilangWrapper.is(':hidden')) {
            $multilangWrapper.css('display', '');
          }
        }
      }

      const langMatches = prefix_el_to_append.match(/\d+$/) || [];
      if (langMatches.length && typeof langMatches[0] !== 'undefined') {
        const id_lang = String(langMatches[0]);
        if (typeof etsSeo.content_analysis[id_lang] === 'undefined') {
          etsSeo.content_analysis[id_lang] = {};
        }
        etsSeo.content_analysis[id_lang][rule] = {el: prefix_el_to_append, type: type, text: text};
      }
    }
  },

  removeAnalysisMessage: function(prefix_el_to_append, rule) {
    etsSeo.hideAnalysisMessage(prefix_el_to_append + '-error', rule);
    etsSeo.hideAnalysisMessage(prefix_el_to_append + '-warning', rule);
    etsSeo.hideAnalysisMessage(prefix_el_to_append + '-success', rule);
    const langMatches = prefix_el_to_append.match(/\d+$/);
    if (langMatches.length && typeof langMatches[0] !== 'undefined') {
      const id_lang = langMatches[0];
      if (typeof etsSeo.content_analysis[id_lang] !== 'undefined' && typeof etsSeo.content_analysis[id_lang][rule] !== 'undefined') {
        etsSeo.content_analysis[id_lang][rule] = null;
      }
    }
  },

  hideAnalysisMessage: function(list, rule) {
    const $list = $(list);
    $list.find('li.' + rule).remove();
    if (!$list.find('li').length) {
      const $container = $list.parent();
      $container.addClass('hide').hide();
      $list.hide();
    }
  },

  getTextWidth: function(text) {
    const canvas = document.createElement('canvas');
    const context = canvas.getContext('2d');
    context.font = '20px arial';
    const metrics = context.measureText(text);
    return metrics.width;
  },

  ensureAnalysisRendered: function(id_lang) {
    const currentLangId = Number(etsSeoBo.currentActiveLangId || 0);
    const langId = Number(id_lang || currentLangId || 0);
    if (!langId) {
      if (currentLangId) {
        window.setTimeout(() => etsSeo.ensureAnalysisRendered(currentLangId), 120);
      }
      return;
    }

    if (langId !== currentLangId) {
      const langKey = String(langId);
      if (this.analysisEnsuredLangs.indexOf(langKey) === -1) {
        this.analysisEnsuredLangs.push(langKey);
      }
      return;
    }

    const langKey = String(langId);
    if (this.analysisEnsuredLangs.indexOf(langKey) !== -1) {
      return;
    }

    const $containers = $(`#box-seo-analysis .lang-${langId}, .box-seo-readability .lang-${langId}`);
    if (!$containers.length) {
      const attemptKey = String(langId);
      const attempts = this.analysisEnsureAttempts[attemptKey] || 0;
      const retryDelay = Math.min(600, 120 + (attempts * 120));
      this.analysisEnsureAttempts[attemptKey] = attempts + 1;
      window.setTimeout(() => etsSeo.ensureAnalysisRendered(langId), retryDelay);
      return;
    }

    const hasItems = $containers.find('.analysis-result--list li').length > 0;
    if (hasItems) {
      this.analysisEnsuredLangs.push(langKey);
      delete this.analysisEnsureAttempts[langKey];
      return;
    }

    const attempts = this.analysisEnsureAttempts[langKey] || 0;
    if (attempts >= 5) {
      this.analysisEnsuredLangs.push(langKey);
      delete this.analysisEnsureAttempts[langKey];
      return;
    }

    this.analysisEnsureAttempts[langKey] = attempts + 1;
    const attemptCount = this.analysisEnsureAttempts[langKey];
    const content = this.getTextContentFromInputs(langId, etsSeoBo.currentController) || '';
    this.analysisContent(langId, content);
    const nextDelay = Math.min(600, 150 + (attemptCount * 120));
    window.setTimeout(() => etsSeo.ensureAnalysisRendered(langId), nextDelay);
  },

  initTabSeo: function(id_lang) {
    const resolvedId = id_lang || etsSeoBo.currentActiveLangId;
    const langId = Number(resolvedId || 0);
    if (!langId && this.initTabSeoRetryCount < 5) {
      this.initTabSeoRetryCount += 1;
      window.setTimeout(() => etsSeo.initTabSeo(resolvedId), 150);
      return;
    } else if (!langId) {
      return;
    }
    this.initTabSeoRetryCount = 0;
    const langKey = String(langId);
    const scoreData = ETS_SEO_SCORE_DATA && (ETS_SEO_SCORE_DATA[langKey] || ETS_SEO_SCORE_DATA[langId]) ? (ETS_SEO_SCORE_DATA[langKey] || ETS_SEO_SCORE_DATA[langId]) : null;
    const runAnalysis = Boolean(!scoreData || !scoreData.score_analysis || !scoreData.content_analysis);
    // etsSeo.initSeoScore(id_lang);
    // etsSeo.detectMissingAnalyzeByLang();
    
    // IMPORTANT: Don't re-run analysis if we already have score data - use saved scores instead
    if (ETS_SEO_ENABLE_AUTO_ANALYSIS && runAnalysis) {
      const prefix = etsSeo.prefixInput();
      let content = '';
      let desc = ''; let shortDesc = '';
      if (prefix.short_desc && $(prefix.short_desc + langId).length) {
        shortDesc = $(prefix.short_desc + langId).val();
        content += shortDesc;
      }
      if (prefix.content && $(prefix.content + langId).length) {
        desc = $(prefix.content + langId).val();
        content += desc;
      }
      etsSeo.analysisContent(langId, content);
      etsSeo.showSuccessMessageAnalysis();
    } else {
      if (ETS_SEO_SCORE_DATA && scoreData) {
        //
        let scoreAnalysis = {};
        if (typeof scoreData !== 'undefined' && scoreData.score_analysis) {
          if (typeof scoreData.score_analysis === 'string') {
            scoreAnalysis = JSON.parse(scoreData.score_analysis);
          } else {
            scoreAnalysis = scoreData.score_analysis;
          }
        }
        const seoScore = scoreAnalysis.seo_score || {};
        const readabilityScore = scoreAnalysis.readability_score || {};
        Object.keys(etsSeo.seo_score).forEach(function(key) {
          etsSeo.seo_score[key][langId] = seoScore[key] || 0;
        });
        Object.keys(etsSeo.readability_score).forEach(function(key) {
          etsSeo.readability_score[key][langId] = readabilityScore[key] || 0;
        });
        if (typeof scoreData !== 'undefined' && scoreData.content_analysis) {
          let contentAnalysis = scoreData.content_analysis;
          if (typeof scoreData.content_analysis == 'string') {
            contentAnalysis = JSON.parse(scoreData.content_analysis);
          }
          if (contentAnalysis) {
            const input = $('#ets_seo_content_analysis');
            if (input.length) {
              const currentData = input.val() ? JSON.parse(input.val()) : {};
              currentData[langKey] = contentAnalysis;
              input.val(JSON.stringify(currentData));
            }
            const ssTypes = {
              success: false,
              warning: false,
              error: false,
            };
            const raTypes = {
              success: false,
              warning: false,
              error: false,
            };
            Object.keys(contentAnalysis).forEach(function(key) {
              if (contentAnalysis[key]) {
                const parentEl = $(contentAnalysis[key].el + '-' + contentAnalysis[key].type);
                if (!parentEl.length) {
                  return;
                }
                const isScore = parentEl.parents('#box-seo-analysis').length > 0;
                if (isScore) {
                  ssTypes[contentAnalysis[key].type] = true;
                } else {
                  raTypes[contentAnalysis[key].type] = true;
                }
                if(parentEl.find('.'+key).length==0)
                {
                    parentEl.append('<li class="' + key + '">' + contentAnalysis[key].text + '</li>');
                }
                else
                {
                    parentEl.find('.'+key).html(contentAnalysis[key].text);
                }
                if (key === 'sentence_length') {
                  parentEl.find('a:not([href="#"])').detach();
                }
              }
            });
            [ssTypes, raTypes].forEach((v, i) => {
              for (const [key, val] of Object.entries(v)) {
                const $target = $(`${i === 0 ? '#box-seo-analysis' : '.box-seo-readability'} .lang-${langId} .analysis-result--${key}`);
                if (!val) {
                  $target.hide().addClass('hide');
                } else {
                  $target.removeClass('hide');
                  if ($target.is(':hidden')) {
                    $target.css('display', '');
                  }
                }
              }
            });
            const hasReadabilityData = Object.values(raTypes).some(Boolean);
            if (hasReadabilityData) {
              this.readabilityEnsureAttempts[String(langId)] = 0;
            } else if (langId === Number(etsSeoBo.currentActiveLangId)) {
              const attemptKey = String(langId);
              const attempts = this.readabilityEnsureAttempts[attemptKey] || 0;
              if (attempts < 3) {
                this.readabilityEnsureAttempts[attemptKey] = attempts + 1;
                const currentContent = this.getTextContentFromInputs(langId, etsSeoBo.currentController) || '';
                this.analysisContent(langId, currentContent);
                this.analysisEnsuredLangs = this.analysisEnsuredLangs.filter((v) => v !== String(langId));
                window.setTimeout(() => etsSeo.ensureAnalysisRendered(langId), 160);
              }
            }
          }
          this.detectMissingAnalyzeByLang();
        }
      }
    }
    this.ensureAnalysisRendered(langId);
    if (langId === Number(etsSeoBo.currentActiveLangId) && (!scoreData || !scoreData.content_analysis)) {
      const fallbackContent = this.getTextContentFromInputs(langId, etsSeoBo.currentController) || '';
      this.analysisContent(langId, fallbackContent);
    }
  },

  changePreview: function(id_lang) {
    const prefix = etsSeo.prefixInput();
    let meta_title = $(prefix.meta_title + id_lang).val();

    if (!meta_title || ETS_SEO_FORCE_USE_META_TEMPLATE) {
      meta_title = etsSeo.getMetaTitle(id_lang);
    }
    if (!meta_title) {
      if ($(prefix.title + id_lang).length) {
        meta_title = $(prefix.title + id_lang).val();
      } else if ($(prefix.title.replace(/^_+|_+$/, '')).length) {
        meta_title = $(prefix.title.replace(/^_+|_+$/, '')).val();
      } else if ($('#form_step1_name_' + id_lang).length) {
        meta_title = $('#form_step1_name_' + id_lang).val();
      }
      if (etsSeoBo.currentController == 'AdminSuppliers' || etsSeoBo.currentController == 'AdminManufacturers') {
        if ($('#manufacturer_name').length) {
          meta_title = $('#manufacturer_name').val();
        } else {
          meta_title = $('#name').val();
        }
      }
    }
    let meta_desc = $(prefix.meta_desc + id_lang).val();

    if (!meta_desc || ETS_SEO_FORCE_USE_META_TEMPLATE) {
      meta_desc = etsSeo.getMetaDesc(id_lang);
    }
    if (!meta_desc) {
      if ($(prefix.short_desc + id_lang).length) {
        meta_desc = $(prefix.short_desc + id_lang).val();
      } else if ($('#form_step1_description_short_' + id_lang).length) {
        meta_desc = $('#form_step1_description_short_' + id_lang).val();
      }
      if (!meta_desc && etsSeoBo.currentController == 'AdminManufacturers') {
        meta_desc = $(prefix.content + id_lang).val();
      }
    }
    const link_rewrite = $(prefix.link_rewrite + id_lang).val() || '';
    const key_phrase = $('.input-key-phrase-il-' + id_lang).first().val() || '';

    let page_title = '';
    if (prefix.title && prefix.title != prefix.meta_title) {
      if ($(prefix.title + id_lang).length) {
        page_title = $(prefix.title + id_lang).val();
      } else if ($(prefix.title.replace(/^_+|_+$/, '')).length) {
        page_title = $(prefix.title.replace(/^_+|_+$/, '')).val();
      }

      if (!page_title) {
        page_title = '';
      }
    }
    if (etsSeoBo.currentController == 'AdminMeta') {
      page_title = $(prefix.meta_title + id_lang).val();
    }
    let price = '';
    let description = '';
    let description2 = '';
    let category = '';
    let brand = '';
    let discount_price = '';
    let priceNb = 0;
    if (prefix.price) {
      price = $(prefix.price).val() ? parseFloat($(prefix.price).val()) : 0;
      if ($('#combinations').length) {
        $('.attribute-default').each(function() {
          if ($(this).is(':checked')) {
            const id_combination = $(this).attr('data-id');
            const impact_price = $('#attribute_' + id_combination + ' input.attribute_priceTE').val();
            price = impact_price ? price + parseFloat(impact_price) : price;
          }
        });
      }
      priceNb = price;
      formatCurrencyCldr(price, function(v) {
        price = v;
      });
    }

    if (prefix.short_desc) {
      description = $(prefix.short_desc + id_lang).val();
    }
    if (prefix.content) {
      description2 = $(prefix.content + id_lang).val();
    }
    if (prefix.category) {
      if ($(prefix.category + ':checked').length) {
        var listCategories = [];
        $(prefix.category + ':checked').each(function() {
          listCategories.push($(this).parent('label').text());
        });
        category = listCategories.toString();
      } else if ($(prefix.category + ' option:selected').length) {
        var listCategories = [];
        $(prefix.category + ' option:selected').each(function() {
          listCategories.push($(this).text());
        });
        category = listCategories.toString();
      }
      if (ETS_SEO_CURRENT_CATEGORY_NAME && typeof ETS_SEO_CURRENT_CATEGORY_NAME[id_lang] !== 'undefined' && ETS_SEO_CURRENT_CATEGORY_NAME[id_lang]) {
        category = ETS_SEO_CURRENT_CATEGORY_NAME[id_lang];
      }
    }
    if (prefix.brand) {
      brand = $(prefix.brand).find('option:selected').text();
    }
    if (prefix.discount_price) {
      const discountText = $(prefix.discount_price).find('tbody tr:first-child > td:nth-child(9)').text();
      if (discountText.indexOf(currency.sign) !== -1) {
        const matchAmount = discountText.match(/[0-9\.]+/);
        if (matchAmount && priceNb) {
          var afterDiscount = parseFloat(priceNb+'') - parseFloat(matchAmount[0]);

          formatCurrencyCldr(afterDiscount, function(v) {
            discount_price = v;
          });
        }
      } else {
        const matchPercent = discountText.match(/[0-9\.]+/);
        if (matchPercent && priceNb) {
          var afterDiscount = parseFloat(priceNb+'') - (parseFloat(priceNb+'') * parseFloat(matchPercent[0])/100);

          formatCurrencyCldr(afterDiscount, function(v) {
            discount_price = v;
          });
        }
      }
    }
    if (!discount_price) {
      discount_price = price;
    }

    if (meta_title) {
      meta_title = etsSeo.getSeoMetaData(meta_title, true, {name: page_title, price: price, category: category, brand: brand, discount_price: discount_price});
    }
    if (meta_desc) {
      meta_desc = etsSeo.getSeoMetaData(meta_desc, false, {
        name: page_title,
        price: price,
        description: description,
        description2: description2,
        category: category,
        brand: brand,
        discount_price: discount_price,
      });
    }
    let textTitle = meta_title ? meta_title.replace(/<\/?[a-z][^>]*?>/gi, ' ') : '';
    textTitle = textTitle.replace(/\r\n/gi, '\n');
    textTitle = textTitle.replace(/\n/gi, ' ');
    textTitle = textTitle.replace(/\s\s+/gi, ' ').trim();
    if (textTitle && textTitle.length > 60) {
      meta_title = textTitle.substring(0, 60) + '...';
    }

    let textDesc = meta_desc ? meta_desc.replace(/<\/?[a-z][^>]*?>/gi, ' ') : '';
    textDesc = textDesc.replace(/\r\n/gi, '\n');
    textDesc = textDesc.replace(/\n/gi, ' ');
    textDesc = textDesc.replace(/\s\s+/gi, ' ').trim();
    if (textDesc.length > 160) {
      meta_desc = textDesc.substring(0, 160) + '...';
    }

    if (meta_desc) {
      meta_desc = meta_desc.replace(/<\/?[a-z][^>]*?>/gi, ' ').replace(new RegExp(etsSeo.escapeRegExp(key_phrase.toLowerCase()), 'gi'), '<strong>$&</strong>');
    }

    $('#ets-seo-snippet-preview-' + id_lang + ' .snippet-preview--title>.text').html(meta_title);
    if (typeof PS_ALLOW_ACCENTED_CHARS_URL === 'undefined') {
      PS_ALLOW_ACCENTED_CHARS_URL = false;
    }
    if (link_rewrite) {
      $('#ets-seo-snippet-preview-' + id_lang + ' .snippet-preview--baseurl>.text>.slug').html(str2url(link_rewrite, 'UTF-8', 0));
    }

    $('#ets-seo-snippet-preview-' + id_lang + ' .snippet-preview--desc>.text').html(meta_desc);
  },

  getSeoMetaData: function(str, is_title, param) {
    let meta_default = {
        "%shop-name%":{
          "code": "%shop-name%",
          "value": "La boutique des Animaux"
        },
        "%separator%":{
          "code": "%separator%",
          "value": "|"
        },
        "%product-name%":{
          "code": "%product-name%",
          "type": "title",
          "value":""
        },
        "%price%":{
          "code": "%price%",
          "type": "price",
          "value": "0.0"
        },
        "%discount-price%": {
          "code": "%discount-price%",
          "type": "discount_price",
          "value": ""
        },
        "%brand%": {
          "title": "Marque",
          "code": "%brand%",
          "type": "brand",
          "value": ""
        },
        "%category%": {
          "code": "%category%",
          "type": "category",
          "value": ""
        },
        "%ean13%": {
          "title": "EAN-13",
          "code": "%ean13%",
          "type": "ean13",
          "value": ""
        },
        "%summary%": {
          "title": "Résumé",
          "code": "%summary%",
          "type": "desc",
          "value": ""
        },
        "%description%": {
          "title": "Description",
          "code": "%description%",
          "type": "long_desc",
          "value": ""
        }
      }
    if (typeof ETS_SEO_META_CODES !== 'undefined') {
      const meta_codes = is_title ? (ETS_SEO_META_CODES.title.length ? ETS_SEO_META_CODES.title : meta_default)  : (ETS_SEO_META_CODES.desc.length ? ETS_SEO_META_CODES.desc : meta_default);

      Object.keys(meta_codes).forEach(function(key) {
        let value = meta_codes[key].value || '';
        if (typeof meta_codes[key].type !== 'undefined' && meta_codes[key].type == 'title' && typeof param.name !== 'undefined') {
          value = param.name;
        } else if (typeof meta_codes[key].type !== 'undefined' && meta_codes[key].type == 'price' && typeof param.price !== 'undefined') {
          value = param.price;
        } else if (typeof meta_codes[key].type !== 'undefined' && meta_codes[key].type == 'category' && typeof param.category !== 'undefined') {
          value = param.category;
        } else if (typeof meta_codes[key].type !== 'undefined' && meta_codes[key].type == 'desc' && typeof param.description !== 'undefined') {
          value = param.description;
        } else if (typeof meta_codes[key].type !== 'undefined' && meta_codes[key].type == 'desc2' && typeof param.description2 !== 'undefined') {
          value = param.description2;
        } else if (typeof meta_codes[key].type !== 'undefined' && meta_codes[key].type == 'brand' && typeof param.brand !== 'undefined') {
          value = param.brand;
        } else if (typeof meta_codes[key].type !== 'undefined' && meta_codes[key].type == 'discount_price' && typeof param.discount_price !== 'undefined') {
          value = param.discount_price || '';
        }

        if (value) {
          value = value.toString();

          value = value.replace(/<\/?[a-z][^>]*?>/gi, '');

          value = value.replace(/\r\n/gi, '\n').trim('\n').trim();
          str = str.replace(new RegExp(key, 'gi'), value);
        } else {
          str = str.replace(new RegExp(key, 'gi'), '');
        }
      });
    }
    return str;
  },

  getIdLang: function(id_input) {
    const arrayIdInput = id_input.split('_');
    return arrayIdInput[arrayIdInput.length - 1];
  },
  missingAnalyzeByLang: [],
  detectMissingAnalyzeByLang: function() {
    this.missingAnalyzeByLang = [];
    const input = $('#ets_seo_content_analysis');
    let contentAnalysis = {};
    if (input.length && input.val()) {
      contentAnalysis = JSON.parse(input.val());
    }
    etsSeoBo.languages.forEach((value) => {
      if (!contentAnalysis.hasOwnProperty(value.id_lang)) {
        this.missingAnalyzeByLang.push(value.id_lang);
      }
    });
    if (ETS_SEO_SCORE_DATA) {
      Object.keys(ETS_SEO_SCORE_DATA).forEach((value) => this.missingAnalyzeByLang.etsRemove(String(value)));
    }
  },
  timerSaveScore: null,
  isSavingProduct: false, // Flag to prevent auto-save during manual save
  initSaveScore: function() {
    this.timerSaveScore && window.clearTimeout(this.timerSaveScore);
    this.timerSaveScore = window.setTimeout(() => {
      if (!ETS_SEO_DEFINED.id_current_page) {
        return;
      }
      
      // Don't auto-save if user is manually saving the product
      if (etsSeo.isSavingProduct) {
        return;
      }
      
      const scores = JSON.parse($('#ets_seo_score_data').val());
      /**/
      let totalScore = 0;
      Object.keys(scores.seo_score).forEach(function(key) {
        totalScore += scores.seo_score[key][etsSeoBo.currentActiveLangId];
      });
      let contentAnalysis = $('#ets_seo_content_analysis').val();
      if (contentAnalysis) {
        contentAnalysis = JSON.parse(contentAnalysis);
      }
      const data = {
        etsSeoSaveScore: 1,
        id: ETS_SEO_DEFINED.id_current_page,
        page_type: etsSeoBo.currentController,
        readability_score: scores.readability_score,
        seo_score: scores.seo_score,
        content_analysis: contentAnalysis,
        is_cms_category: ETS_SEO_IS_CMS_CATEGORY,
      };
      $.ajax({
        url: etsSeoBo.ajaxCtlUri,
        type: 'POST',
        dataType: 'json',
        data: data,
        success: function(res) {
        },
      });
    }, 500);
  },
  renderMetaData: function(text, id_lang, is_title) {
    const prefix = etsSeo.prefixInput();
    let page_title = '';
    if (prefix.title && prefix.title != prefix.meta_title) {
      if ($(prefix.title + id_lang).length) {
        page_title = $(prefix.title + id_lang).val();
      } else if ($(prefix.title.replace(/^_+|_+$/, '')).length) {
        page_title = $(prefix.title.replace(/^_+|_+$/, '')).val();
      }

      if (!page_title) {
        page_title = '';
      }
    }
    if (etsSeoBo.currentController == 'AdminMeta') {
      page_title = $(prefix.meta_title + id_lang).val();
    }
    let price = '';
    let description = '';
    let description2 = '';
    let category = '';
    let discount_price = '';
    let priceNb = 0;
    let brand = '';
    if (prefix.price) {
      price = $(prefix.price).val() ? parseFloat($(prefix.price).val()) : 0;
      if ($('#combinations').length) {
        $('.attribute-default').each(function() {
          if ($(this).is(':checked')) {
            const id_combination = $(this).attr('data-id');
            const impact_price = $('#attribute_' + id_combination + ' input.attribute_priceTE').val();
            price = impact_price ? price + parseFloat(impact_price) : price;
          }
        });
      }
      priceNb = price;
      formatCurrencyCldr(price, function(v) {
        price = v;
      });
    }
    if (prefix.short_desc) {
      description = $(prefix.short_desc + id_lang).val();
    }
    if (prefix.content) {
      description2 = $(prefix.content + id_lang).val();
    }
    if (prefix.category) {
      if ($(prefix.category + ':checked').length) {
        const listCategories = [];
        $(prefix.category + ':checked').each(function() {
          listCategories.push($(this).parent('label').text());
        });
        category = listCategories.toString();
      }
      if (ETS_SEO_CURRENT_CATEGORY_NAME && typeof ETS_SEO_CURRENT_CATEGORY_NAME[id_lang] !== 'undefined' && ETS_SEO_CURRENT_CATEGORY_NAME[id_lang]) {
        category = ETS_SEO_CURRENT_CATEGORY_NAME[id_lang];
      }
    }
    if (prefix.discount_price) {
      const discountText = $(prefix.discount_price).find('tbody tr:first-child>td:nth-child(8)').text();

      if (discountText.indexOf(currency.sign) !== -1) {
        const matchAmount = discountText.match(/[0-9\.]+/);

        if (matchAmount && priceNb) {
          var afterDiscount = parseFloat(priceNb+'') - parseFloat(matchAmount[0]);

          formatCurrencyCldr(afterDiscount, function(v) {
            discount_price = v;
          });
        }
      } else {
        const matchPercent = discountText.match(/[0-9\.]+/);
        if (matchPercent && priceNb) {
          var afterDiscount = parseFloat(priceNb+'') - (parseFloat(priceNb+'') * parseFloat(matchPercent[0])/100);

          formatCurrencyCldr(afterDiscount, function(v) {
            discount_price = v;
          });
        }
      }
    }
    if (prefix.brand) {
      brand = $(prefix.brand).find('option:selected').text();
    }
    if (!discount_price) {
      discount_price = price;
    }

    if (text) {
      text = etsSeo.getSeoMetaData(text, is_title, {
        name: page_title,
        price: price,
        category: category,
        description: description,
        description2: description2,
        discount_price: discount_price,
        brand: brand,
      });
    }
    return text;
  },
  differenceOf2Arrays: function(array1, array2) {
    return array1.filter(function(obj) {
      return array2.indexOf(obj) == -1;
    });
  },
  getMinorKeyphraseInMetaTileDesc: function(id_lang) {
    const minorKeyphrase = etsSeo.getMinorKeyphrase(id_lang);
    const listMinorSuccess = [];
    if (minorKeyphrase.length > 0) {
      const prefix = etsSeo.prefixInput();
      const title = prefix.title ? (etsSeoBo.currentController != 'AdminManufacturers' && etsSeoBo.currentController != 'AdminSuppliers' ? $(prefix.title + id_lang).val() : $(prefix.title.slice(0, -1)).val()) : '';
      const meta_title = prefix.meta_title && $(prefix.meta_title + id_lang).length ? $(prefix.meta_title + id_lang).val() : '';

      $.each(minorKeyphrase, function(i, item) {
        const myPattern = new RegExp('(?:^|\\s)' + etsSeo.escapeRegExp(item) + '(?:$|\\s|[^\\d\\W])', 'gi');
        const matchedTitle = title.match(myPattern);
        const matchedMetaTitle = meta_title.match(myPattern);

        if ((matchedTitle !== null && matchedTitle.length) || (matchedMetaTitle !== null && matchedMetaTitle.length)) {
          listMinorSuccess.push(item);
        }
      });
    }
    return listMinorSuccess;
  },
  insertAtCaret: function(element, data) {
    const areaId = element.attr('id');
    const text = data;
    const txtarea = element[0];
    const scrollPos = txtarea.scrollTop;
    let strPos = 0;
    const br = ((txtarea.selectionStart || txtarea.selectionStart == '0') ?
            'ff' : (document.selection ? 'ie' : false));
    if (br == 'ie') {
      txtarea.focus();
      var range = document.selection.createRange();
      range.moveStart('character', -txtarea.value.length);
      strPos = range.text.length;
    } else if (br == 'ff') strPos = txtarea.selectionStart;

    const front = (txtarea.value).substring(0, strPos);
    const back = (txtarea.value).substring(strPos, txtarea.value.length);
    txtarea.value = front + text + back;
    strPos = strPos + text.length;
    if (br == 'ie') {
      txtarea.focus();
      var range = document.selection.createRange();
      range.moveStart('character', -txtarea.value.length);
      range.moveStart('character', strPos);
      range.moveEnd('character', 0);
      range.select();
    } else if (br == 'ff') {
      txtarea.selectionStart = strPos;
      txtarea.selectionEnd = strPos;
      txtarea.focus();
    }
    txtarea.scrollTop = scrollPos;
  },

  updateSnippet: function(inSnippet) {
    if (!ETS_SEO_LANGUAGES) {
      return;
    }
    Object.keys(ETS_SEO_LANGUAGES).forEach(function(key) {
      const id_lang = ETS_SEO_LANGUAGES[key];
      const input = etsSeo.prefixInput();
      if (inSnippet) {
        $(input.meta_title + id_lang).val($('#ets_seo_meta_title_' + id_lang).val());
        $(input.meta_desc + id_lang).val($('#ets_seo_meta_description_' + id_lang).val());
        $(input.link_rewrite + id_lang).val($('#ets_seo_link_rewrite_' + id_lang).val());
      } else {
        if ($(input.meta_title + id_lang).length) {
          $('input[id*="ets_seo_meta_title_' + id_lang + '"]').val($(input.meta_title + id_lang).val());
          if ($('input[id*="ets_seo_meta_title_' + id_lang + '"]').next('.js-text-count').length) {
            $('input[id*="ets_seo_meta_title_' + id_lang + '"]').next('.js-text-count').find('.js-ets-seo-current-length').html($(input.meta_title + id_lang).val().length);
          }
        }
        if ($(input.meta_desc + id_lang).length) {
          $('#ets_seo_meta_description_' + id_lang).val($(input.meta_desc + id_lang).val());
          if ($('textarea[id*="ets_seo_meta_description_' + id_lang + '"]').next('.js-text-count').length) {
            $('textarea[id*="ets_seo_meta_description_' + id_lang + '"]').next('.js-text-count').find('.js-ets-seo-current-length').html($(input.meta_desc + id_lang).val().length);
          }
        }
        if ($(input.link_rewrite + id_lang).length) {
          $('#ets_seo_link_rewrite_' + id_lang).val($(input.link_rewrite + id_lang).val());
        }
      }
      etsSeo.changePreview(id_lang);
    });
  },
  analysisMinorKeyphrase: function(idLang) {
    const prefix = etsSeo.prefixInput();
    if ((prefix.content && prefix.short_desc) || prefix.meta_title || prefix.meta_desc) {
      if (typeof idLang !== 'undefined' && idLang) {
        etsSeo.analysisMinorKeyphraseItem(idLang, prefix);
      } else {
        $('input.js-ets-seo-tagify').each(function() {
          const id_lang = $(this).attr('data-idlang');
          etsSeo.analysisMinorKeyphraseItem(id_lang, prefix);
        });
      }
    }
  },
  analysisMinorKeyphraseItem: function(id_lang, prefix) {
    const minor_keyphrase = etsSeo.getMinorKeyphrase(id_lang);
    let content = '';
    let title = '';
    let desc = '';
    if (prefix.short_desc) {
      content += $(prefix.short_desc + id_lang).length ? $(prefix.short_desc + id_lang).val() : '';
    }
    if (prefix.content) {
      content += $(prefix.content + id_lang).length ? $(prefix.content + id_lang).val() : '';
    }
    if (prefix.meta_title) {
      title = $(prefix.meta_title + id_lang).length ? $(prefix.meta_title + id_lang).val() : '';
    }
    if (prefix.meta_desc) {
      desc = $(prefix.meta_desc + id_lang).length ? $(prefix.meta_desc + id_lang).val() : '';
    }
    const text = content.replace(/<\/?[a-z][^>]*?>/gi, '\n');
    etsSeo.rules.minorKeyphraseLength(id_lang, minor_keyphrase);
    etsSeo.rules.minorKeyphraseInContent(id_lang, minor_keyphrase, text);
    etsSeo.rules.minorKeyphraseInMetaTitle(id_lang, minor_keyphrase, title);
    etsSeo.rules.minorKeyphraseInMetaDesc(id_lang, minor_keyphrase, desc);
  },
  setOpacityPreviewAnalysis: function(type, id_lang, level) {
    for (let i = 1; i <= 5; i++) {
      if (i <= level) {
        $(`.js-ets-seo-preview-analysis .${type} .js-ets-seo-processing-lang-${id_lang} .processing .level-${i}`).css('opacity', '1');
      } else if (i == level + 1) {
        $(`.js-ets-seo-preview-analysis .${type} .js-ets-seo-processing-lang-${id_lang} .processing .level-${i}`).css('opacity', '0.6');
      } else {
        $(`.js-ets-seo-preview-analysis .${type} .js-ets-seo-processing-lang-${id_lang} .processing .level-${i}`).css('opacity', '0.3');
      }
    }
  },

  setDataPreviewAnalysis: function(type, id_lang, seo_score, isIndex, keyPhrase, minorKeyphrase, actualRulesCount) {
    // Set color
    let setup = false;
    let textStatus = '';
    let classStatus = '';
    let overallScore = 0;
    if (type == 'seo-processing') {
      if (!isIndex) {
        setup = true;
        classStatus = 'grey-noindex';
        textStatus = ETS_SEO_MESSAGE.no_index;
      } else if (keyPhrase != '0' && !keyPhrase && !minorKeyphrase.length) {
        setup = true;
        classStatus = 'grey-nokeyphrase';
        textStatus = ETS_SEO_MESSAGE.no_focus_keyphrase;
      }
      // Use constant from PHP to match calculation in database (Catalog Products)
      let totalSeoRules = 0;
      if (typeof actualRulesCount !== 'undefined' && actualRulesCount) {
        totalSeoRules = actualRulesCount;
      } else {
        totalSeoRules = (ETS_SEO_DEFINED && ETS_SEO_DEFINED.TOTAL_SEO_RULE_SCORE) ? ETS_SEO_DEFINED.TOTAL_SEO_RULE_SCORE : Object.keys(etsSeo.seo_score).length;
      }
      overallScore = Math.round(seo_score / (totalSeoRules * 9) * 10);
    } else {
      // Use constant from PHP to match calculation in database (Catalog Products)
      let totalReadabilityRules = 0;
      if (typeof actualRulesCount !== 'undefined' && actualRulesCount) {
        totalReadabilityRules = actualRulesCount;
      } else {
        totalReadabilityRules = (ETS_SEO_DEFINED && ETS_SEO_DEFINED.TOTAL_READABILITY_RULE_SCORE) ? ETS_SEO_DEFINED.TOTAL_READABILITY_RULE_SCORE : Object.keys(etsSeo.readability_score).length;
      }
      overallScore = Math.round(seo_score / (totalReadabilityRules * 9) * 10);
    }


    if (setup) {
      // Do nothing
    } else if (overallScore <= 4) {
      classStatus = 'red';
      textStatus = ETS_SEO_MESSAGE.not_good;
    } else if (overallScore > 4 && overallScore <= 7) {
      classStatus = 'orange';
      textStatus = ETS_SEO_MESSAGE.acceptance;
    } else {
      classStatus = 'green';
      textStatus = ETS_SEO_MESSAGE.excellent;
    }
    const divPreviewScore = $(`.js-ets-seo-preview-analysis .${type} .js-ets-seo-processing-lang-${id_lang} .processing`);
    const divPreviewText = $(`.js-ets-seo-preview-analysis .${type} .js-ets-seo-processing-lang-${id_lang} .sub-title`);
    if (divPreviewScore.length) {
      divPreviewScore.attr('data-score', seo_score);
    }
    if (divPreviewScore.hasClass('excuting')) {
      return;
    }
    if (classStatus) {
      if (type == 'readability-processing' && etsSeoBo.currentController == 'AdminMeta') {
        divPreviewScore.addClass('grey-darken');
      } else {
        divPreviewScore.addClass(classStatus);
      }
    } else {
      divPreviewScore.addClass('grey-darken');
    }

    if (textStatus) {
      if (type == 'readability-processing' && etsSeoBo.currentController == 'AdminMeta') {
        divPreviewText.html(ETS_SEO_MESSAGE.not_analysis);
      } else {
        divPreviewText.html(textStatus);
      }
    } else {
      if (type == 'readability-processing' && etsSeoBo.currentController == 'AdminMeta') {
        divPreviewText.html(ETS_SEO_MESSAGE.not_analysis);
      }
    }

    // Set opacity
    if (overallScore < 2) {
      etsSeo.setOpacityPreviewAnalysis(type, id_lang, 1);
    } else if (overallScore >= 2 && overallScore < 4) {
      etsSeo.setOpacityPreviewAnalysis(type, id_lang, 1);
    } else if (overallScore >= 4 && overallScore < 6) {
      etsSeo.setOpacityPreviewAnalysis(type, id_lang, 2);
    } else if (overallScore >= 6 && overallScore < 8) {
      etsSeo.setOpacityPreviewAnalysis(type, id_lang, 3);
    } else if (overallScore >= 8 && overallScore < 10) {
      etsSeo.setOpacityPreviewAnalysis(type, id_lang, 4);
    } else {
      etsSeo.setOpacityPreviewAnalysis(type, id_lang, 5);
    }
  },
  idPreviewTimer: null,
  setPreviewAnalysis: function() {
    this.idPreviewTimer && window.clearTimeout(this.idPreviewTimer);
    this.idPreviewTimer = window.setTimeout(() => {
      const scoreInput = $('#ets_seo_score_data');
      const seo_analysis = scoreInput.val() ? JSON.parse(scoreInput.val()) : null;
      if (!seo_analysis) {
        return;
      }
      const processDiv = $('.js-ets-seo-preview-analysis .processing');
      processDiv.removeClass('red');
      processDiv.removeClass('orange');
      processDiv.removeClass('green');
      processDiv.removeClass('grey');
      processDiv.removeClass('violet');
      processDiv.removeClass('yellow');
      processDiv.removeClass('grey-nokeyphrase');
      processDiv.removeClass('grey-noindex');
      processDiv.removeClass('grey-darken');
      
      Object.keys(ETS_SEO_LANGUAGES).forEach(function(isocode) {
        const curIdLang = ETS_SEO_LANGUAGES[isocode];
        
        // FIRST: Check if this language has been analyzed and stored in database
        const storedAnalysis = ETS_SEO_SCORE_DATA && (ETS_SEO_SCORE_DATA[curIdLang] || ETS_SEO_SCORE_DATA[String(curIdLang)]) ? 
                              (ETS_SEO_SCORE_DATA[curIdLang] || ETS_SEO_SCORE_DATA[String(curIdLang)]) : null;
        
        // Check if we have valid stored analysis data from database
        let hasStoredAnalysis = false;
        let storedSeoScore = 0;
        let storedReadabilityScore = 0;
        
        if (storedAnalysis && storedAnalysis.score_analysis) {
          let scoreAnalysis = storedAnalysis.score_analysis;
          if (typeof scoreAnalysis === 'string') {
            try {
              scoreAnalysis = JSON.parse(scoreAnalysis);
            } catch (e) {
              scoreAnalysis = {};
            }
          }
          
          // Check if we have actual score data (not just empty objects)
          const seoScoreData = scoreAnalysis.seo_score || {};
          const readabilityScoreData = scoreAnalysis.readability_score || {};
          
          // Count non-zero scores to determine if language was actually analyzed
          let nonZeroSeoScores = 0;
          let nonZeroReadabilityScores = 0;
          
          Object.keys(seoScoreData).forEach(function(key) {
            const value = parseInt(seoScoreData[key], 10);
            // Skip N/A scores (-999) - they should not be counted in total (same as PHP logic)
            // But count score = 0 as valid (rule was evaluated, just got 0 points)
            if (!isNaN(value) && value !== -999) {
              storedSeoScore += value;
              if (value !== 0) {
                nonZeroSeoScores++;
              }
            }
          });
          
          Object.keys(readabilityScoreData).forEach(function(key) {
            const value = parseInt(readabilityScoreData[key], 10);
            // Skip N/A scores (-999) - they should not be counted in total (same as PHP logic)
            // But count score = 0 as valid (rule was evaluated, just got 0 points)
            if (!isNaN(value) && value !== -999) {
              storedReadabilityScore += value;
              if (value !== 0) {
                nonZeroReadabilityScores++;
              }
            }
          });
          
          // Language is considered analyzed if we have score data from database
          hasStoredAnalysis = (nonZeroSeoScores > 0 || nonZeroReadabilityScores > 0) || 
                             (Object.keys(seoScoreData).length > 0 || Object.keys(readabilityScoreData).length > 0);
        }
        
        // SECOND: Check if language has been analyzed in current session (from etsSeo.seo_score)
        let currentSeoScore = 0;
        let currentReadabilityScore = 0;
        
        Object.keys(etsSeo.seo_score).forEach(function(k) {
          if (etsSeo.seo_score[k] && etsSeo.seo_score[k].hasOwnProperty(curIdLang)) {
            const value = parseInt(etsSeo.seo_score[k][curIdLang], 10);
            // Skip N/A scores (-999) - they should not be counted in total (same as PHP logic)
            // But count score = 0 as valid (rule was evaluated, just got 0 points)
            if (!isNaN(value) && value !== -999) {
              currentSeoScore += value;
            }
          }
        });
        
        Object.keys(etsSeo.readability_score).forEach(function(k) {
          if (etsSeo.readability_score[k] && etsSeo.readability_score[k].hasOwnProperty(curIdLang)) {
            const value = parseInt(etsSeo.readability_score[k][curIdLang], 10);
            // Skip N/A scores (-999) - they should not be counted in total (same as PHP logic)
            // But count score = 0 as valid (rule was evaluated, just got 0 points)
            if (!isNaN(value) && value !== -999) {
              currentReadabilityScore += value;
            }
          }
        });
        
        // Use stored data if available, otherwise use current analysis
        let seo_score = hasStoredAnalysis ? storedSeoScore : currentSeoScore;
        let readability_score = hasStoredAnalysis ? storedReadabilityScore : currentReadabilityScore;
        
        // Count actual applied rules (excluding -999 N/A scores) for accurate calculation
        let actualSeoRulesCount = 0;
        let actualReadabilityRulesCount = 0;
        
        if (hasStoredAnalysis && storedAnalysis && storedAnalysis.score_analysis) {
          let scoreAnalysis = storedAnalysis.score_analysis;
          if (typeof scoreAnalysis === 'string') {
            try {
              scoreAnalysis = JSON.parse(scoreAnalysis);
            } catch (e) {
              scoreAnalysis = {};
            }
          }
          const seoScoreData = scoreAnalysis.seo_score || {};
          const readabilityScoreData = scoreAnalysis.readability_score || {};
          
          Object.keys(seoScoreData).forEach(function(key) {
            const value = parseInt(seoScoreData[key], 10);
            if (!isNaN(value) && value !== -999) {
              actualSeoRulesCount++;
            }
          });
          
          Object.keys(readabilityScoreData).forEach(function(key) {
            const value = parseInt(readabilityScoreData[key], 10);
            if (!isNaN(value) && value !== -999) {
              actualReadabilityRulesCount++;
            }
          });
        } else {
          // Count from current analysis
          Object.keys(etsSeo.seo_score).forEach(function(k) {
            if (etsSeo.seo_score[k] && etsSeo.seo_score[k].hasOwnProperty(curIdLang)) {
              const value = parseInt(etsSeo.seo_score[k][curIdLang], 10);
              if (!isNaN(value) && value !== -999) {
                actualSeoRulesCount++;
              }
            }
          });
          
          Object.keys(etsSeo.readability_score).forEach(function(k) {
            if (etsSeo.readability_score[k] && etsSeo.readability_score[k].hasOwnProperty(curIdLang)) {
              const value = parseInt(etsSeo.readability_score[k][curIdLang], 10);
              if (!isNaN(value) && value !== -999) {
                actualReadabilityRulesCount++;
              }
            }
          });
        }
        
        // Use actual rules count if available, otherwise fallback to total rules
        if (actualSeoRulesCount === 0) {
          actualSeoRulesCount = (ETS_SEO_DEFINED && ETS_SEO_DEFINED.TOTAL_SEO_RULE_SCORE) ? ETS_SEO_DEFINED.TOTAL_SEO_RULE_SCORE : Object.keys(etsSeo.seo_score).length;
        }
        if (actualReadabilityRulesCount === 0) {
          actualReadabilityRulesCount = (ETS_SEO_DEFINED && ETS_SEO_DEFINED.TOTAL_READABILITY_RULE_SCORE) ? ETS_SEO_DEFINED.TOTAL_READABILITY_RULE_SCORE : Object.keys(etsSeo.readability_score).length;
        }
        
        const indexInput = $(`#ets_seo_allow_search_engine_show_post-${curIdLang}`);
        const keyPharseInput = $(`input[name="ets_seo_key_phrase[${curIdLang}]"]`);
        let isIndex = null;
        if (indexInput.length) {
          const indexData = indexInput.val();
          if (indexData === '1' || indexData === '0') {
            isIndex = parseInt(indexData, 10);
          } else if (indexData === '2') {
            const configValue = indexInput.attr('data-value');
            if (configValue === '0' || configValue === '1') {
              isIndex = parseInt(configValue, 10);
            }
          }
        }
        if (isIndex === null) {
          if (storedAnalysis && typeof storedAnalysis.allow_search !== typeof undefined) {
            const allowSearchStored = parseInt(storedAnalysis.allow_search, 10);
            if (!isNaN(allowSearchStored)) {
              if (allowSearchStored === 0 || allowSearchStored === 1) {
                isIndex = allowSearchStored;
              } else if (allowSearchStored === 2) {
                const configValue = indexInput.attr ? indexInput.attr('data-value') : null;
                if (configValue === '0' || configValue === '1') {
                  isIndex = parseInt(configValue, 10);
                }
              }
            }
          }
        }
        if (isIndex === null || isNaN(isIndex)) {
          isIndex = 1;
        }
        let keyPhrase = '';
        if (keyPharseInput.length) {
          keyPhrase = keyPharseInput.val();
        }
        const minorKeyPhrase = etsSeo.getMinorKeyphrase(curIdLang);
        // Pass actualRulesCount to use dynamic calculation (excluding N/A rules)
        // This aligns with PHP backend logic for accurate score calculation
        etsSeo.setDataPreviewAnalysis('seo-processing', curIdLang, seo_score, isIndex, keyPhrase, minorKeyPhrase, actualSeoRulesCount);
        etsSeo.setDataPreviewAnalysis('readability-processing', curIdLang, readability_score, isIndex, keyPhrase, minorKeyPhrase, actualReadabilityRulesCount);
      });
      const input = $('#ets_seo_content_analysis');
      let saveScore = false;
      if (input.length && input.val()) {
        try {
          const ct = JSON.parse(input.val());
          Object.keys(ct).forEach((value) => {
            if (etsSeo.missingAnalyzeByLang.indexOf(value) !== -1) {
              saveScore = true;
              return;
            }
          });
        } catch (e) {
          prestashop.debug && console.error('Error occur when detecting new analyze content', e);
        }
      }
      if (saveScore) {
        var intervalInit = setInterval(function() {
          if ($('#ets_seo_score_data').val()) {
            clearInterval(intervalInit);
            etsSeo.initSaveScore();
          }
        }, 200);
      }
    }, 100);
  },

  showSuccessMessageAnalysis: function(saveScore) {
    saveScore = (typeof saveScore !== typeof undefined) ? saveScore : true;
    if (etsSeo.showMessageAnalysis) {
      return;
    }
    let show = 0;
    $('textarea[id*="meta_description_"], form input[id*="meta_description_"]').each(function() {
      if ($(this).length) {
        show = 1;
      }
    });
    if (!show) {
      return;
    }
    if (saveScore) {
      var intervalInit = setInterval(function() {
        if ($('#ets_seo_score_data').val()) {
          clearInterval(intervalInit);
          etsSeo.initSaveScore();
        }
      }, 200);
    }
    etsSeo.showMessageAnalysis = true;
    $('.js-ets-seo-preview-analysis .processing').removeClass('excuting');
    if (ETS_SEO_DEFINED.id_current_page) {
      if (!$('#ets_seo_score_data').val()) {
        var intvSeoScore = setInterval(function() {
          if ($('#ets_seo_score_data').val()) {
            etsSeo.setPreviewAnalysis();
            clearInterval(intvSeoScore);
          }
        }, 200);
      } else {
        etsSeo.setPreviewAnalysis();
      }
    } else {
      etsSeo.setPreviewAnalysis();
    }
  },
  changePlaceholderMeta: function() {
    if (etsSeoBo.currentController == 'AdminMeta') {
      return;
    }

    $('input[id*="meta_title_"], input[id*="meta_page_title_"]').each(function() {
      const id = $(this).attr('id');
      if (id.indexOf('ets_seo_social') < 0) {
        const id_lang = etsSeo.getIdLang(id);
        if (ETS_SEO_DEFINED.meta_template_configured[id_lang] && ETS_SEO_DEFINED.meta_template_configured[id_lang].title) {
          $(this).attr('placeholder', ETS_SEO_MESSAGE.placeholder_meta);
        } else {
          $(this).attr('placeholder', ETS_SEO_DEFINED.placeholder_meta.title);
        }
      }
    });
    $('textarea[id*="meta_description_"], form input[id*="meta_description_"]').each(function() {
      const id = $(this).attr('id');
      if (id.indexOf('ets_seo_social') < 0) {
        const id_lang = etsSeo.getIdLang(id);
        if (ETS_SEO_DEFINED.meta_template_configured[id_lang] && ETS_SEO_DEFINED.meta_template_configured[id_lang].desc) {
          $(this).attr('placeholder', ETS_SEO_MESSAGE.placeholder_meta);
        } else {
          $(this).attr('placeholder', ETS_SEO_DEFINED.placeholder_meta.desc);
        }
      }
    });
  },

  getMetaTitle: function(id_lang, get_origin) {
    const prefix = etsSeo.prefixInput();
    let title = '';
    if (prefix.meta_title && $(prefix.meta_title + id_lang).length && get_origin) {
      title = $(prefix.meta_title + id_lang).val();
    }
    if (!title || ETS_SEO_FORCE_USE_META_TEMPLATE) {
      if ($('#ets_seo_meta_template_title_' + id_lang).length && $('#ets_seo_meta_template_title_' + id_lang).val()) {
        title = $('#ets_seo_meta_template_title_' + id_lang).val();
      }
    }
    if (!title) {
      title = prefix.meta_title ? (etsSeoBo.currentController != 'AdminManufacturers' && etsSeoBo.currentController != 'AdminSuppliers' ? $(prefix.meta_title + id_lang).val() : $(prefix.meta_title.slice(0, -1)).val()) : '';
    }
    if (!title) {
      title = prefix.title ? (etsSeoBo.currentController != 'AdminManufacturers' && etsSeoBo.currentController != 'AdminSuppliers' ? $(prefix.title + id_lang).val() : $(prefix.title.slice(0, -1)).val()) : '';
    }
    if (title) {
      title = etsSeo.renderMetaData(title, id_lang, true);
    }

    return title;
  },
  getMetaDesc: function(id_lang, get_origin) {
    const prefix = etsSeo.prefixInput();
    let desc = '';
    if (prefix.meta_desc && $(prefix.meta_desc + id_lang).length && get_origin) {
      desc = $(prefix.meta_desc + id_lang).val().replace(/<\/?[a-z][^>]*?>/gi, '\n');
    }
    if (!desc && prefix.meta_desc && $(prefix.meta_desc + id_lang).length) {
      desc = $(prefix.meta_desc + id_lang).val().replace(/<\/?[a-z][^>]*?>/gi, '\n');
    }

    if (!desc || ETS_SEO_FORCE_USE_META_TEMPLATE ) {
      if ($('#ets_seo_meta_template_desc_' + id_lang).length && $('#ets_seo_meta_template_desc_' + id_lang).val()) {
        desc = $('#ets_seo_meta_template_desc_' + id_lang).val().replace(/<\/?[a-z][^>]*?>/gi, '\n');
      }
    }
    if (!desc) {
      if (prefix.short_desc && $(prefix.short_desc + id_lang).length) {
        desc = $(prefix.short_desc + id_lang).val().replace(/<\/?[a-z][^>]*?>/gi, '\n');
      }
    }
    if (!desc && (etsSeoBo.currentController == 'AdminManufacturers' || etsSeoBo.currentController == 'AdminProducts')) {
      desc = $(prefix.content + id_lang).val();
      if (desc) {
        desc = desc.replace(/<\/?[a-z][^>]*?>/gi, ' ').replace(/\r\n/gi, '\n').replace(/\n/gi, '').replace(/\s+/gi, ' ').trim().substr(0, 120);
      }
    }
    if (desc) {
      desc = etsSeo.renderMetaData(desc, id_lang, false);
    }

    return desc;
  },
  escapeRegExp: function(text) {
    return text.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, '\\$&').toLowerCase();
  },
  encodeBase64IfHtml: function(str) {
    if (!str || typeof str !== 'string') {
      return str;
    }

    if (typeof ETS_SEO_ENABLE_CONTENT_ANALYSIS_BASE64 !== 'undefined' && !ETS_SEO_ENABLE_CONTENT_ANALYSIS_BASE64) {
      return str;
    }
    
    const hasHtml = /<[^>]+>/.test(str) || /(class|id|data-|href|src|on\w+)\s*=/i.test(str);
    
    if (hasHtml) {
      try {
        const encoded = btoa(encodeURIComponent(str).replace(/%([0-9A-F]{2})/g, function(_, hex) {
          return String.fromCharCode(parseInt(hex, 16));
        }));
        return '__BASE64__' + encoded;
      } catch (e) {
        return str;
      }
    }
    
    return str;
  },
  encodeBase64InObject: function(obj) {
    if (typeof obj === 'string') {
      return this.encodeBase64IfHtml(obj);
    }
    if (Array.isArray(obj)) {
      return obj.map(item => this.encodeBase64InObject(item));
    }
    if (obj && typeof obj === 'object') {
      const encoded = {};
      for (const key in obj) {
        if (obj.hasOwnProperty(key)) {
          encoded[key] = this.encodeBase64InObject(obj[key]);
        }
      }
      return encoded;
    }
    return obj;
  },
  disableMetaInput: function() {
    if (etsSeoBo.currentController != 'AdminMeta' && ETS_SEO_FORCE_USE_META_TEMPLATE) {
      const prefix = etsSeo.prefixInput();
      Object.keys(ETS_SEO_LANGUAGES).forEach(function(key) {
        const id_lang =ETS_SEO_LANGUAGES[key];
        if ($('#ets_seo_meta_template_title_' + id_lang).length && $('#ets_seo_meta_template_title_' + id_lang).val() && prefix.meta_title && $(prefix.meta_title+id_lang).length) {
          var fakeInputMetaTitle = '<input type="text" readonly="readonly" class="'+$(prefix.meta_title+id_lang).attr('class')+' ets_seo_tmp_input ets_seo_meta_title_tmp_'+id_lang+'" value="'+ $('#ets_seo_meta_template_title_' + id_lang).val()+'" />';
          $(prefix.meta_title+id_lang).hide();
          $(prefix.meta_title+id_lang).before(fakeInputMetaTitle);
          if (etsSeoBo.currentActiveLangId == id_lang) {
            $(prefix.meta_title+id_lang).closest('.form-group').addClass('disable_codeseo');
          }
          const alertTitle = '<div class="alert alert-warning">'+ETS_SEO_MESSAGE.warning_title_use_meta_template+'</div>';
          if ($(prefix.meta_title+id_lang).parent('.translation-field').length) {
            $(prefix.meta_title+id_lang).parent('.translation-field').append(alertTitle);
          } else if ($(prefix.meta_title+id_lang).parents('.js-locale-input').length) {
            $(prefix.meta_title+id_lang).parents('.js-locale-input').parents('.locale-input-group').after(alertTitle);
          } else if ($(prefix.meta_title+id_lang).parents('.col-lg-9').length) {
            $(prefix.meta_title+id_lang).parents('.col-lg-9').append(alertTitle);
          }

          $('#ets_seo_meta_title_'+ id_lang).hide();
          $('#ets_seo_meta_title_'+ id_lang).before(fakeInputMetaTitle);
          $('#ets_seo_meta_title_'+ id_lang).closest('.form-group').addClass('disable_codeseo');
        }
        if ($('#ets_seo_meta_template_desc_' + id_lang).length && $('#ets_seo_meta_template_desc_' + id_lang).val() && prefix.meta_desc && $(prefix.meta_desc+id_lang).length) {
          $(prefix.meta_desc+id_lang).hide();
          const fakeInputMetaDesc = '<textarea readonly="readonly" class="'+$(prefix.meta_desc+id_lang).attr('class')+' ets_seo_tmp_input ets_seo_meta_desc_tmp_'+id_lang+'">'+$('#ets_seo_meta_template_desc_' + id_lang).val()+'</textarea>';
          $(prefix.meta_desc+id_lang).before(fakeInputMetaDesc);
          if (etsSeoBo.currentActiveLangId == id_lang) {
            $(prefix.meta_desc+id_lang).closest('.form-group').addClass('disable_codeseo');
          }
          const alertDesc = '<div class="alert alert-warning">'+ETS_SEO_MESSAGE.warning_desc_use_meta_template+'</div>';
          if ($(prefix.meta_desc+id_lang).parent('.translation-field').length) {
            $(prefix.meta_desc+id_lang).parent('.translation-field').append(alertDesc);
          } else if ($(prefix.meta_desc+id_lang).parents('.js-locale-input').length) {
            $(prefix.meta_desc+id_lang).parents('.js-locale-input').parents('.locale-input-group').after(alertDesc);
          } else if ($(prefix.meta_desc+id_lang).parents('.col-lg-9').length) {
            $(prefix.meta_desc+id_lang).parents('.col-lg-9').append(alertDesc);
          }
          $('#ets_seo_meta_description_'+ id_lang).hide();
          $('#ets_seo_meta_description_'+ id_lang).before(fakeInputMetaTitle);
          $('#ets_seo_meta_description_'+ id_lang).closest('.form-group').addClass('disable_codeseo');
        }
      });
    }
  },
  disableMetaInputs: function(idProduct) {
    if (etsSeoBo.currentController != 'AdminMeta' && ETS_SEO_FORCE_USE_META_TEMPLATE) {
      const prefix_meta_title = '#meta_title_'+idProduct+'_';
      const prefix_meta_description = '#meta_description_'+idProduct+'_';
      Object.keys(ETS_SEO_LANGUAGES).forEach(function(key) {
        const id_lang =ETS_SEO_LANGUAGES[key];
        if ($('#ets_pmn_seo_metatitle_' + id_lang).length && $('#ets_pmn_seo_metatitle_' + id_lang).val() && prefix_meta_title && $(prefix_meta_title+id_lang).length) {
          const fakeInputMetaTitle = '<input type="text" readonly="readonly" class="'+$(prefix_meta_title+id_lang).attr('class')+' ets_seo_tmp_input ets_seo_meta_title_tmp_'+id_lang+'" value="'+ $('#ets_pmn_seo_metatitle_' + id_lang).val()+'" />';
          $(prefix_meta_title+id_lang).hide();
          $(prefix_meta_title+id_lang).before(fakeInputMetaTitle);
          if (etsSeoBo.currentActiveLangId == id_lang) {
            $(prefix_meta_title+id_lang).closest('.form-group').addClass('disable_codeseo');
          }
          const alertTitle = '<div class="alert alert-warning">'+ETS_SEO_MESSAGE.warning_title_use_meta_template+'</div>';
          if ($(prefix_meta_title+id_lang).parent('.translation-field').length) {
            $(prefix_meta_title+id_lang).parent('.translation-field').append(alertTitle);
          } else if ($(prefix_meta_title+id_lang).parent('.js-locale-input').length) {
            $(prefix_meta_title+id_lang).parent('.js-locale-input').parents('.locale-input-group').after(alertTitle);
          } else if ($(prefix_meta_title+id_lang).parent('.col-lg-11').length) {
            $(prefix_meta_title+id_lang).parent('.col-lg-11').append(alertTitle);
          }
        }
        if ($('#ets_pmn_seo_metadescription_' + id_lang).length && $('#ets_pmn_seo_metadescription_' + id_lang).val() && prefix_meta_description && $(prefix_meta_description+id_lang).length) {
          $(prefix_meta_description+id_lang).hide();
          const fakeInputMetaDesc = '<textarea readonly="readonly" class="'+$(prefix_meta_description+id_lang).attr('class')+' ets_seo_tmp_input ets_seo_meta_desc_tmp_'+id_lang+'">'+$('#ets_pmn_seo_metadescription_' + id_lang).val()+'</textarea>';
          $(prefix_meta_description+id_lang).before(fakeInputMetaDesc);
          if (etsSeoBo.currentActiveLangId == id_lang) {
            $(prefix_meta_description+id_lang).closest('.form-group').addClass('disable_codeseo');
          }
          const alertDesc = '<div class="alert alert-warning">'+ETS_SEO_MESSAGE.warning_desc_use_meta_template+'</div>';
          if ($(prefix_meta_description+id_lang).parent('.translation-field').length) {
            $(prefix_meta_description+id_lang).parent('.translation-field').append(alertDesc);
          } else if ($(prefix_meta_description+id_lang).parent('.js-locale-input').length) {
            $(prefix_meta_description+id_lang).parent('.js-locale-input').parents('.locale-input-group').after(alertDesc);
          } else if ($(prefix_meta_description+id_lang).parent('.col-lg-11').length) {
            $(prefix_meta_description+id_lang).parent('.col-lg-11').append(alertDesc);
          }
        }
      });
    }
  },
  disableMetaInputs: function(idProduct) {
    if (etsSeoBo.currentController != 'AdminMeta' && ETS_SEO_FORCE_USE_META_TEMPLATE) {
      const prefix_meta_title = '#meta_title_'+idProduct+'_';
      const prefix_meta_description = '#meta_description_'+idProduct+'_';
      Object.keys(ETS_SEO_LANGUAGES).forEach(function(key) {
        const id_lang =ETS_SEO_LANGUAGES[key];
        if ($('#ets_pmn_seo_metatitle_' + id_lang).length && $('#ets_pmn_seo_metatitle_' + id_lang).val() && prefix_meta_title && $(prefix_meta_title+id_lang).length) {
          const fakeInputMetaTitle = '<input type="text" readonly="readonly" class="'+$(prefix_meta_title+id_lang).attr('class')+' ets_seo_tmp_input ets_seo_meta_title_tmp_'+id_lang+'" value="'+ $('#ets_pmn_seo_metatitle_' + id_lang).val()+'" />';
          $(prefix_meta_title+id_lang).hide();
          $(prefix_meta_title+id_lang).before(fakeInputMetaTitle);
          if (etsSeoBo.currentActiveLangId == id_lang) {
            $(prefix_meta_title+id_lang).closest('.form-group').addClass('disable_codeseo');
          }
          const alertTitle = '<div class="alert alert-warning">'+ETS_SEO_MESSAGE.warning_title_use_meta_template+'</div>';
          if ($(prefix_meta_title+id_lang).parent('.translation-field').length) {
            $(prefix_meta_title+id_lang).parent('.translation-field').append(alertTitle);
          } else if ($(prefix_meta_title+id_lang).parent('.js-locale-input').length) {
            $(prefix_meta_title+id_lang).parent('.js-locale-input').parents('.locale-input-group').after(alertTitle);
          } else if ($(prefix_meta_title+id_lang).parent('.col-lg-11').length) {
            $(prefix_meta_title+id_lang).parent('.col-lg-11').append(alertTitle);
          }
        }
        if ($('#ets_pmn_seo_metadescription_' + id_lang).length && $('#ets_pmn_seo_metadescription_' + id_lang).val() && prefix_meta_description && $(prefix_meta_description+id_lang).length) {
          $(prefix_meta_description+id_lang).hide();
          const fakeInputMetaDesc = '<textarea readonly="readonly" class="'+$(prefix_meta_description+id_lang).attr('class')+' ets_seo_tmp_input ets_seo_meta_desc_tmp_'+id_lang+'">'+$('#ets_pmn_seo_metadescription_' + id_lang).val()+'</textarea>';
          $(prefix_meta_description+id_lang).before(fakeInputMetaDesc);
          if (etsSeoBo.currentActiveLangId == id_lang) {
            $(prefix_meta_description+id_lang).closest('.form-group').addClass('disable_codeseo');
          }
          const alertDesc = '<div class="alert alert-warning">'+ETS_SEO_MESSAGE.warning_desc_use_meta_template+'</div>';
          if ($(prefix_meta_description+id_lang).parent('.translation-field').length) {
            $(prefix_meta_description+id_lang).parent('.translation-field').append(alertDesc);
          } else if ($(prefix_meta_description+id_lang).parent('.js-locale-input').length) {
            $(prefix_meta_description+id_lang).parent('.js-locale-input').parents('.locale-input-group').after(alertDesc);
          } else if ($(prefix_meta_description+id_lang).parent('.col-lg-11').length) {
            $(prefix_meta_description+id_lang).parent('.col-lg-11').append(alertDesc);
          }
        }
      });
    }
  },
  checkInputMetaTemplate: function(id_lang) {
    const prefix = etsSeo.prefixInput();

    if ($(prefix.meta_title+id_lang).length) {
      if ($(prefix.meta_title+id_lang).parent().find('.ets_seo_tmp_input').length) {
        $(prefix.meta_title+id_lang).closest('.form-group').addClass('disable_codeseo');
      } else {
        $(prefix.meta_title+id_lang).closest('.form-group').removeClass('disable_codeseo');
      }
    }
    if ($(prefix.meta_desc+id_lang).length) {
      if ($(prefix.meta_desc+id_lang).parent().find('.ets_seo_tmp_input').length) {
        $(prefix.meta_desc+id_lang).closest('.form-group').addClass('disable_codeseo');
      } else {
        $(prefix.meta_desc+id_lang).closest('.form-group').removeClass('disable_codeseo');
      }
    }
    if ($('#ets_seo_meta_title_'+id_lang).length) {
      if ($('#ets_seo_meta_title_'+id_lang).parent().find('.ets_seo_tmp_input').length) {
        $('#ets_seo_meta_title_'+id_lang).closest('.form-group').addClass('disable_codeseo');
      } else {
        $('#ets_seo_meta_title_'+id_lang).closest('.form-group').removeClass('disable_codeseo');
      }
    }
    if ($('#ets_seo_meta_description_'+id_lang).length) {
      if ($('#ets_seo_meta_description_'+id_lang).parent().find('.ets_seo_tmp_input').length) {
        $('#ets_seo_meta_description_'+id_lang).closest('.form-group').addClass('disable_codeseo');
      } else {
        $('#ets_seo_meta_description_'+id_lang).closest('.form-group').removeClass('disable_codeseo');
      }
    }
  },
  getCategoryName: function(id_category) {
    $.ajax({
      url: etsSeoBo.ajaxCtlUri,
      type: 'GET',
      data: {
        id_category: id_category,
        etsSeoGetCategoryName: 1,
      },
      dataType: 'json',
      success: function(res) {
        if (res.success) {
          ETS_SEO_CURRENT_CATEGORY_NAME = res.name;
        }
      },
    });
  },

};

(function($) {
  if (etsSeoBo && !etsSeoBo.isEnable) {
    return;
  }
  let etsSeoCHeckMCE = 0;
  let etsSeoMCELoaded = 0;
  setTimeout(function() {
    etsSeo.disableMetaInput();
  }, 300);
  setTimeout(function() {
    $('#form_switch_language').trigger('change');
    const id_lang = etsSeoBo.currentActiveLangId;
    const text_content = etsSeo.getTextContentFromInputs(id_lang);
    etsSeo.analysisContent(
        id_lang,
        text_content,
    );
  }, 1500);
  var checkInitTinyMce = setInterval(function() {
    if (typeof tinyMCE !== 'undefined' && tinyMCE.activeEditor && ETS_SEO_ENABLE_AUTO_ANALYSIS) {
      etsSeoMCELoaded++;
      $('textarea.autoload_rte, textarea.supertinymcepro').each(function() {
        const mceId = $(this).attr('id');
        if (!mceId) {
          return;
        }
        if (mceId && typeof tinyMCE.EditorManager.get(mceId) !== 'undefined') {
          tinyMCE.EditorManager.get(mceId).on('keyup, change', function(e) {
            tinyMCE.triggerSave();
            etsSeo.timeoutTyping && clearTimeout(etsSeo.timeoutTyping);
            etsSeo.timeoutTyping = setTimeout(function() {
              const id_lang = etsSeo.getIdLang(mceId);
              const text_content = etsSeo.getTextContentFromInputs(id_lang);
              etsSeo.analysisContent(
                  id_lang,
                  text_content,
              );
            }, etsSeo.timeoutKeyup);
          });
        }
      });

      // Onload
      clearInterval(checkInitTinyMce);

      if (etsSeoMCELoaded == 1) {
        setTimeout(function() {
          etsSeo.showSuccessMessageAnalysis();
        }, 2000);
      }
    } else {
      etsSeoCHeckMCE++;
      if (etsSeoCHeckMCE >= 150) {
        clearInterval(checkInitTinyMce);
      }
    }
  }, 200);

  if (((etsSeoBo.currentController == 'AdminCmsContent' && ETS_SEO_IS_CMS_CATEGORY) || etsSeoBo.currentController == 'AdminMeta') && ETS_SEO_ENABLE_AUTO_ANALYSIS) {
    $(document).on('keyup', 'input[id^="name_"], input[id^="cms_page_category_name_"]', function() {
      clearTimeout(etsSeo.timeoutTyping);
      const $this = $(this);
      etsSeo.timeoutTyping = setTimeout(function() {
        const id_lang = etsSeo.getIdLang($this.attr('id'));
        etsSeo.changePreview(id_lang);
      }, etsSeo.timeoutKeyup);
    });
  }

  if (ETS_SEO_ENABLE_AUTO_ANALYSIS) {
    $(document).on('keyup', 'textarea[id^="description_"], textarea[id^="cms_page_category_description_"]', function() {
      clearTimeout(etsSeo.timeoutTyping);
      const $this = $(this);
      etsSeo.timeoutTyping = setTimeout(function() {
        const text_content = $this.val();
        const id_lang = etsSeo.getIdLang($this.attr('id'));
        etsSeo.analysisContent(
            id_lang,
            text_content,
        );
        etsSeo.changePreview(id_lang);
      }, etsSeo.timeoutKeyup);
    });
  }

  $(document).on('click', '.ets_seotop1_step_seo .js-btn-preview-mode', function() {
    const mode = $(this).attr('data-mode');
    if (mode == 'mobile') {
      $('.ets_seotop1_step_seo .snippet-preview--desktop:not(.hide)').addClass('hide');
      $('.ets_seotop1_step_seo .snippet-preview--mobile').removeClass('hide');

      $('.ets_seotop1_step_seo .js-btn-preview-mode[data-mode="mobile"]:not(.active)').addClass('active');
      $('.ets_seotop1_step_seo .js-btn-preview-mode[data-mode="desktop"]').removeClass('active');
    } else {
      $('.ets_seotop1_step_seo .snippet-preview--mobile:not(.hide)').addClass('hide');
      $('.ets_seotop1_step_seo .snippet-preview--desktop').removeClass('hide');

      $('.ets_seotop1_step_seo .js-btn-preview-mode[data-mode="desktop"]:not(.active)').addClass('active');
      $('.ets_seotop1_step_seo .js-btn-preview-mode[data-mode="mobile"]').removeClass('active');
    }
  });

  /* Product page ========*/
  $(document).on('change', '#form_switch_language', function(e) {
    const lang = $(this).val();
    etsSeoBo.languages.forEach((obj) => {
      if (obj.iso_code === lang) {
        etsSeoBo.currentActiveLangId = obj.id_lang;
      }
    });
    $(document).trigger('langSelectChanged', {
      langId: etsSeoBo.currentActiveLangId,
      elem: e.target,
    });
    $('.ets_seotop1_step_seo .multilang-field.lang-' + lang).removeClass('hide');
    $('.ets_seotop1_step_seo .multilang-field:not(.lang-' + lang + ')').addClass('hide');
    $('.js-locale-btn').html(lang);
    etsSeo.checkInputMetaTemplate(ETS_SEO_LANGUAGES[lang]);
    etsSeo.initTabSeo(etsSeoBo.currentActiveLangId);
    if (ETS_SEO_ENABLE_AUTO_ANALYSIS) {
      const text_content = etsSeo.getTextContentFromInputs(etsSeoBo.currentActiveLangId);
      const key_phrase = $('.input-key-phrase-il-' + etsSeoBo.currentActiveLangId).val();
      const link_rewrite = etsSeo.getLinkRewriteFromInput(etsSeoBo.currentActiveLangId);
      etsSeo.analysisContent(etsSeoBo.currentActiveLangId, text_content);
      etsSeo.analysisKeypharse(etsSeoBo.currentActiveLangId, key_phrase, text_content);
      etsSeo.rules.keyphraseInSlug(etsSeoBo.currentActiveLangId, key_phrase, link_rewrite);
      etsSeo.changePreview(etsSeoBo.currentActiveLangId);
    }
  });
  // Key phrase change
  if (ETS_SEO_ENABLE_AUTO_ANALYSIS) {
    $(document).on('keyup', '.ets_seotop1_step_seo .input-key-phrase', function() {
      etsSeo.timeoutTyping && clearTimeout(etsSeo.timeoutTyping);
      const $this = $(this);
      etsSeo.timeoutTyping = setTimeout(function() {
        const id_lang = $this.data('idlang');
        const key_phrase = $this.val();
        const text_content = etsSeo.getTextContentFromInputs(id_lang);
        const link_rewrite = etsSeo.getLinkRewriteFromInput(id_lang);
        etsSeo.setKeyPhraseValueToInput(key_phrase, id_lang);
        etsSeo.analysisKeypharse(id_lang, key_phrase, text_content);
        etsSeo.rules.keyphraseInSlug(id_lang, key_phrase, link_rewrite);
        etsSeo.changePreview(id_lang);
      }, etsSeo.timeoutKeyup);
    });
  }

  $(document).on('focusout', '.ets_seotop1_step_seo .input-key-phrase', function() {
    const id_lang = $(this).attr('data-idlang');
    const minors = etsSeo.getMinorKeyphrase(id_lang);
    if (minors && minors.indexOf($(this).val().trim()) !== -1) {
      $(this).val('');
      showErrorMessage(ETS_SEO_MESSAGE.focus_keyphrase_same_minor_keyphrase);
    }
  });
  if (ETS_SEO_ENABLE_AUTO_ANALYSIS) {
    $(document).on('keyup', '.ets_seotop1_step_seo .input-key-phrase', function(e) {
      if (e.which == 13) {
        clearTimeout(etsSeo.timeoutTypingFocusKey);
        const $this = $(this);
        etsSeo.timeoutTypingFocusKey = setTimeout(function() {
          const id_lang = $this.attr('data-idlang');
          const minors = etsSeo.getMinorKeyphrase(id_lang);
          if (minors && minors.indexOf($this.val().trim()) !== -1) {
            $this.val('');
            showErrorMessage(ETS_SEO_MESSAGE.focus_keyphrase_same_minor_keyphrase);
          }
        }, etsSeo.timeoutKeyup);
      }
    });
    $(document).on('keyup', 'input[id^="form_step1_name"], input[id^="product_header_name"]', function() {
      clearTimeout(etsSeo.timeoutTyping);
      const $this = $(this);
      etsSeo.timeoutTyping = setTimeout(function() {
        const arrayIdInput = $this.attr('id').split('_');
        const id_lang = arrayIdInput[arrayIdInput.length - 1];
        const key_phrase = $('.input-key-phrase-il-' + id_lang).first().val();
        const text_content = $('#form_step1_description_short_' + id_lang).val() + $('#form_step1_description_' + id_lang).val();
        etsSeo.analysisKeypharse(id_lang, key_phrase, text_content);
        etsSeo.changePreview(id_lang);
      }, etsSeo.timeoutKeyup);
    });

    $(document).on('keyup', 'input[id^="category_name_"]', function() {
      clearTimeout(etsSeo.timeoutTyping);
      const $this = $(this);
      etsSeo.timeoutTyping = setTimeout(function() {
        const arrayIdInput = $this.attr('id').split('_');
        const id_lang = arrayIdInput[arrayIdInput.length - 1];
        const key_phrase = $('.input-key-phrase-il-' + id_lang).first().val();
        const text_content = $('#category_description_' + id_lang).val();

        etsSeo.analysisKeypharse(id_lang, key_phrase, text_content);
        etsSeo.changePreview(id_lang);
      }, etsSeo.timeoutKeyup);
    });

    // Seo tab
    $(document).on('keyup', 'input[name^="' + etsSeo.prefixInput().title + '"]', function() {
      clearTimeout(etsSeo.timeoutTyping);
      const $this = $(this);
      etsSeo.timeoutTyping = setTimeout(function() {
        const id_lang = etsSeo.getIdLang($this.attr('id'));
        if (id_lang) {
          const prefix = etsSeo.prefixInput();
          if (prefix.meta_title && $(prefix.meta_title + id_lang).length) {
            etsSeo.rules.seoTitleWidth(id_lang, $(prefix.meta_title + id_lang).val());
          }
          etsSeo.changePreview(id_lang);
        }
      }, etsSeo.timeoutKeyup);
    });
  }

  if (etsSeo.prefixInput().title && ETS_SEO_ENABLE_AUTO_ANALYSIS) {
    $(document).on('keyup', 'input[id^=' + etsSeo.prefixInput().title.replace('#', '') + ']', function() {
      clearTimeout(etsSeo.timeoutTyping);
      const $this = $(this);
      etsSeo.timeoutTyping = setTimeout(function() {
        const arrayIdInput = $this.attr('id').split('_');
        const id_lang = arrayIdInput[arrayIdInput.length - 1];
        const key_phrase = $('.input-key-phrase-il-' + id_lang).first().val();
        const prefix = etsSeo.prefixInput();
        let text_content = '';
        if (prefix.short_desc && prefix.short_desc != prefix.content && $(prefix.short_desc + id_lang).length) {
          text_content += $(prefix.short_desc + id_lang).val();
        }

        if (prefix.content && prefix.short_desc != prefix.content && $(prefix.content + id_lang).length) {
          text_content += $(prefix.content + id_lang).val();
        }
        etsSeo.analysisKeypharse(id_lang, key_phrase, text_content);
        etsSeo.changePreview(id_lang);
      }, etsSeo.timeoutKeyup);
    });
  }

  $(document).on('change', '[id^=ets_seo_allow_search_engine_show_post]', function() {
    etsSeo.setPreviewAnalysis();
  });
  if (etsSeoBo.currentController == 'AdminManufacturers') {
    $(document).on('keyup', 'input[id*=manufacturer_name]', function() {
      clearTimeout(etsSeo.timeoutTyping);
      etsSeo.timeoutTyping = setTimeout(function() {
        Object.keys(ETS_SEO_LANGUAGES).forEach(function(key) {
          const id_lang = ETS_SEO_LANGUAGES[key];
          const key_phrase = $('.input-key-phrase-il-' + id_lang).first().val();
          const prefix = etsSeo.prefixInput();
          let text_content = '';
          if (prefix.short_desc && prefix.short_desc != prefix.content && $(prefix.short_desc + id_lang).length) {
            text_content += $(prefix.short_desc + id_lang).val();
          }

          if (prefix.content && prefix.short_desc != prefix.content && $(prefix.content + id_lang).length) {
            text_content += $(prefix.content + id_lang).val();
          }

          etsSeo.analysisKeypharse(id_lang, key_phrase, text_content);
          etsSeo.changePreview(id_lang);
        });
      }, etsSeo.timeoutKeyup);
    });
  }

  if ((etsSeoBo.currentController == 'AdminManufacturers' || etsSeoBo.currentController == 'AdminSuppliers') && ETS_SEO_ENABLE_AUTO_ANALYSIS) {
    $(document).on('keyup', 'input[name=name]', function() {
      clearTimeout(etsSeo.timeoutTyping);
      etsSeo.timeoutTyping = setTimeout(function() {
        Object.keys(ETS_SEO_LANGUAGES).forEach(function(key) {
          const id_lang = ETS_SEO_LANGUAGES[key];
          const key_phrase = $('.input-key-phrase-il-' + id_lang).first().val();
          const prefix = etsSeo.prefixInput();
          let text_content = '';
          if (prefix.short_desc && prefix.short_desc != prefix.content && $(prefix.short_desc + id_lang).length) {
            text_content += $(prefix.short_desc + id_lang).val();
          }

          if (prefix.content && prefix.short_desc != prefix.content && $(prefix.content + id_lang).length) {
            text_content += $(prefix.content + id_lang).val();
          }
          etsSeo.analysisKeypharse(id_lang, key_phrase, text_content);
          etsSeo.changePreview(id_lang);
        });
      }, etsSeo.timeoutKeyup);
    });
  }

  if (ETS_SEO_ENABLE_AUTO_ANALYSIS) {
    // Meta title change
    $.each(['input[id*="meta_title_"]',
      'input[id*="meta_page_title_"]',
      'input[id*="head_seo_title_"]',
    ], function(_i, el) {
      $(document).on('keyup change', el, function() {
        clearTimeout(etsSeo.timeoutTypingMetaTitle);
        const $this = $(this);

        etsSeo.timeoutTypingMetaTitle = setTimeout(function() {
          const meta_title = $this.val();
          const arrayIdInput = $this.attr('id').split('_');
          const id_lang = arrayIdInput[arrayIdInput.length - 1];
          $('#ets_seo_meta_title_' + id_lang).val(meta_title);

          etsSeo.rules.seoTitleWidth(id_lang, meta_title);
          const key_phrase = $('.input-key-phrase-il-' + id_lang).first().val();
          etsSeo.rules.keyPhraseInTitle(id_lang, key_phrase, meta_title);
          const minor_key_phrase = etsSeo.getMinorKeyphrase(id_lang);
          etsSeo.rules.minorKeyphraseInMetaTitle(id_lang, minor_key_phrase, meta_title);
          etsSeo.changePreview(id_lang);
        }, etsSeo.timeoutKeyup);
      });
    });
    // Meta description change
    $.each(['textarea[id*="meta_description_"]',
      'form input[id*="meta_description_"]',
    ], function(i, el) {
      $(document).on('keyup change', el, function() {
        clearTimeout(etsSeo.timeoutTypingMetaDesc);
        const $this = $(this);
        etsSeo.timeoutTypingMetaDesc = setTimeout(function() {
          const meta_desc = $this.val();
          const arrayIdInput = $this.attr('id').split('_');
          const id_lang = arrayIdInput[arrayIdInput.length - 1];
          const key_phrase = $('.input-key-phrase-il-' + id_lang).first().val();
          $('#ets_seo_meta_description_' + id_lang).val(meta_desc);
          etsSeo.rules.metaDescLength(id_lang, meta_desc);
          etsSeo.rules.keyphraseInMetaDesc(id_lang, key_phrase, meta_desc);
          const minor_key_phrase = etsSeo.getMinorKeyphrase(id_lang);
          etsSeo.rules.minorKeyphraseInMetaDesc(id_lang, minor_key_phrase, meta_desc);
          etsSeo.changePreview(id_lang);
        }, etsSeo.timeoutKeyup);
      });
    });


    // Friendly URL change
    $.each(['input[id*="link_rewrite_"]',
      'input[id*="friendly_url_"]',
      'input[id*="url_rewrite_"]',
    ], function(_i, el) {
      $(document).on('keyup', el, function() {
        clearTimeout(etsSeo.timeoutTypingFriendlyUrl);
        const $this = $(this);
        etsSeo.timeoutTypingFriendlyUrl = setTimeout(function() {
          const link_rewrite = $this.val();
          const arrayIdInput = $this.attr('id').split('_');
          const id_lang = arrayIdInput[arrayIdInput.length - 1];
          const key_phrase = $('.input-key-phrase-il-' + id_lang).first().val();

          etsSeo.rules.keyphraseInSlug(id_lang, key_phrase, link_rewrite);
          etsSeo.changePreview(id_lang);
          $('#ets_seo_link_rewrite_' + id_lang).val(link_rewrite);
        }, etsSeo.timeoutKeyup);
      });
    });
  }
  // On add short code
  $(document).on('click', '.js-ets-seo-add-meta-code', function() {
    const meta_code = $(this).attr('data-code');
    const textValue = '';
    if (meta_code) {
      const meta_box = $(this).parent('.ets_seo_meta_code');
      if (!meta_box.prev('.input-group').length && (meta_box.parent().find('input').length || meta_box.parent().find('textarea').length)) {
        if (!meta_box.parent().find('input, textarea').parent().find('.ets_seo_tmp_input').length) {
          etsSeo.insertAtCaret(meta_box.parent().find('input, textarea'), meta_code);

          if (etsSeoBo.currentController !== 'AdminEtsSeoSearchAppearanceContentType') {
            meta_box.parent().find('input, textarea').change();
            etsSeo.changePreview(etsSeo.getIdLang(meta_box.parent().find('input, textarea').attr('id')));
          }
        }
      } else if (meta_box.prev('.input-group').length) {
        if (meta_box.prev('.input-group').find('input, textarea').length > 1) {
          const meta_input = meta_box.prev('.input-group').find('input[id$="_' + etsSeoBo.currentActiveLangId + '"], textarea[id$="_' + etsSeoBo.currentActiveLangId + '"]');
          if (!meta_input.parent().find('.ets_seo_tmp_input').length) {
            etsSeo.insertAtCaret(meta_input, meta_code);
            if (etsSeoBo.currentController !== 'AdminEtsSeoSearchAppearanceContentType') {
              meta_input.change();
              etsSeo.changePreview(etsSeo.getIdLang(meta_input.attr('id')));
            }
          }
        } else {
          const idInput = meta_box.prev('.input-group').find('input,textarea');
          if (!idInput.parent().find('.ets_seo_tmp_input').length) {
            etsSeo.insertAtCaret(idInput, meta_code);
            if (etsSeoBo.currentController !== 'AdminEtsSeoSearchAppearanceContentType') {
              idInput.change();
              etsSeo.changePreview(etsSeo.getIdLang(meta_box.prev('.input-group').find('input,textarea').attr('id')));
            }
          }
        }
      }
    }
    let inSnippet = false;
    if ($(this).parent('.ets_seo_meta_code').hasClass('meta_code_snippet')) {
      inSnippet = true;
    }
    etsSeo.updateSnippet(inSnippet);
    return false;
  });


  // Change language
  $(document).on('click', '.translatable-field ul.dropdown-menu>li>a', function() {
    if (etsSeo.activeControllers.indexOf(etsSeoBo.currentController) !== -1 && $('.ets_seotop1_step_seo').length) {
      const id_lang = $(this).attr('href').replace(/javascript:hideOtherLanguage\(|\);/g, '');

      $('.ets_seotop1_step_seo .multilang-field.lang-' + id_lang).removeClass('hide');
      $('.ets_seotop1_step_seo .multilang-field:not(.lang-' + id_lang + ')').addClass('hide');
      etsSeoBo.currentActiveLangId = id_lang;
      etsSeo.checkInputMetaTemplate(id_lang);
    }
  });

  $(document).on('click', '.js-ets-seo-btn-group-lang a', function() {
    const id_lang = $(this).attr('href').replace(/javascript:hideOtherLanguage\(|\);/g, '');

    $('.ets_seotop1_step_seo .multilang-field.lang-' + id_lang).removeClass('hide');
    $('.ets_seotop1_step_seo .multilang-field:not(.lang-' + id_lang + ')').addClass('hide');
    etsSeoBo.currentActiveLangId = id_lang;
    etsSeo.checkInputMetaTemplate(id_lang);
  });
  let idTimer;
  // Presta >= 176 change language
  $(document).on('click', '.locale-input-group .js-locale-item', function(e) {
    const id_lang = ETS_SEO_LANGUAGES[$(this).attr('data-locale')];
    etsSeoBo.currentActiveLangId = Number(id_lang);
    $(document).trigger('langSelectChanged', {
      langId: id_lang,
      elem: e.target,
    });
    idTimer && window.clearTimeout(idTimer);
    idTimer = window.setTimeout(() => etsSeo.initTabSeo(etsSeoBo.currentActiveLangId), 100);
    if ($('#form_switch_language').length) {
      const locale = $(this).attr('data-locale');
      $('#form_switch_language option[value="' + locale + '"]').prop('selected', true);
      $('#form_switch_language').change();
      $('.js-locale-btn').html(locale);
      etsSeoBo.currentActiveLangId = id_lang;
      etsSeo.checkInputMetaTemplate(id_lang);
      return;
    }
    if (etsSeo.activeControllers.indexOf(etsSeoBo.currentController) !== -1) {
      $('.ets_seotop1_step_seo .multilang-field.lang-' + id_lang).removeClass('hide');
      $('.ets_seotop1_step_seo .multilang-field:not(.lang-' + id_lang + ')').addClass('hide');

      etsSeoBo.currentActiveLangId = id_lang;
      etsSeo.checkInputMetaTemplate(id_lang);
    }
  });
  $(document).on('click', '.translationsLocales .nav-link', function(e) {
    const id_lang = ETS_SEO_LANGUAGES[$(this).attr('data-locale')];
    etsSeoBo.currentActiveLangId = Number(id_lang);
    $(document).trigger('langSelectChanged', {
      langId: id_lang,
      elem: e.target,
    });
    idTimer && window.clearTimeout(idTimer);
    idTimer = window.setTimeout(() => etsSeo.initTabSeo(etsSeoBo.currentActiveLangId), 100);
    if ($('#form_switch_language').length) {
      const locale = $(this).attr('data-locale');
      $('#form_switch_language option[value="' + locale + '"]').prop('selected', true);
      $('#form_switch_language').change();
      $('.js-locale-btn').html(locale);
      etsSeoBo.currentActiveLangId = id_lang;
      etsSeo.checkInputMetaTemplate(id_lang);
      return;
    }
    if (etsSeo.activeControllers.indexOf(etsSeoBo.currentController) !== -1) {
      $('.ets_seotop1_step_seo .multilang-field.lang-' + id_lang).removeClass('hide');
      $('.ets_seotop1_step_seo .multilang-field:not(.lang-' + id_lang + ')').addClass('hide');

      etsSeoBo.currentActiveLangId = id_lang;
      etsSeo.checkInputMetaTemplate(id_lang);
    }
  });
  $(document).on('langSelectChanged', function(_event, data) {
    const langId = data && data.langId ? Number(data.langId) : Number(etsSeoBo.currentActiveLangId);
    if (!langId) {
      return;
    }
    etsSeoBo.currentActiveLangId = langId;
    etsSeo.langSwitchTimer && window.clearTimeout(etsSeo.langSwitchTimer);
    etsSeo.langSwitchTimer = window.setTimeout(function() {
      const prefix = etsSeo.prefixInput();
      let content = '';
      if (prefix.short_desc && $(prefix.short_desc + langId).length) {
        content += $(prefix.short_desc + langId).val();
      }
      if (prefix.content && $(prefix.content + langId).length) {
        content += $(prefix.content + langId).val();
      }
      if (ETS_SEO_ENABLE_AUTO_ANALYSIS) {
        etsSeo.analysisContent(langId, content);
      }
      etsSeo.initTabSeo(langId);
    }, 200);
  });

  $(document).on('change', '.ets_seo_advanced_select2', function() {
    const data = $(this).val();

    $(this).parent().find('.ets-seo-select2-value').val(data.toString());
  });

  /* End product page */

  $(document).on('click', '.js-ets-seo-tab-customize', function() {
    Object.keys(ETS_SEO_LANGUAGES).forEach(function(key) {
      const id_lang = ETS_SEO_LANGUAGES[key];
      etsSeo.changePreview(id_lang);
    });
  });

  $(document).on('click', '.js-ets-seo-btn-toggle-snippet-meta', function() {
    if ($('.js-ets-seo-box-snippet-meta').hasClass('hide')) {
      $('.js-ets-seo-box-snippet-meta').removeClass('hide');
    } else {
      $('.js-ets-seo-box-snippet-meta').addClass('hide');
    }
    etsSeo.updateSnippet(false);
    return false;
  });

  $(document).on('keyup', 'input[id^="ets_seo_meta_title_"]', function() {
    clearTimeout(etsSeo.timeoutTyping);
    const $this = $(this);
    etsSeo.timeoutTyping = setTimeout(function() {
      const meta_title = $this.val();
      const id_lang = etsSeo.getIdLang($this.attr('id'));
      $(`input[id*="meta_title_${id_lang}"], input[id*="meta_page_title_${id_lang}"], input[id*="head_seo_title_${id_lang}"], input[id^="title_${id_lang}"]`).val(meta_title);
      etsSeo.changePreview(id_lang);
    }, etsSeo.timeoutKeyup);
  });

  $(document).on('keyup', 'input[id^="ets_seo_link_rewrite_"]', function() {
    clearTimeout(etsSeo.timeoutTyping);
    const $this = $(this);
    etsSeo.timeoutTyping = setTimeout(function() {
      const link_rewtire = $this.val();
      const id_lang = etsSeo.getIdLang($this.attr('id'));
      $('input[id*="link_rewrite_' + id_lang + '"], input[id*="friendly_url_' + id_lang + '"], input[id*="url_rewrite_' + id_lang + '"]').val(link_rewtire);
      etsSeo.changePreview(id_lang);
    }, etsSeo.timeoutKeyup);
  });

  $(document).on('keyup', 'textarea[id^="ets_seo_meta_description_"]', function() {
    clearTimeout(etsSeo.timeoutTyping);
    const $this = $(this);
    etsSeo.timeoutTyping = setTimeout(function() {
      const meta_desc = $this.val();
      const id_lang = etsSeo.getIdLang($this.attr('id'));
      $('textarea[id*="meta_description_' + id_lang + '"], form input[id*="meta_description_' + id_lang + '"]').val(meta_desc);
      etsSeo.changePreview(id_lang);
    }, etsSeo.timeoutKeyup);
  });

  $(document).on('click', '.js-ets-seo-show-seo-analysis-tab', function() {
    $('a[href="#step_ets_seo_analysis"]').tab('show');
    $('a[href="#category-seo-analysis"]').tab('show');
    $('.js-ets-seo-customize-item').removeClass('active');
    $('.js-ets-seo-tab-analysis').addClass('active');
    $('a[href="#ets_seo_analysis_tabs"]').tab('show');
    $('a[href="#product_seo_analysis-tab"]').tab('show');

    $('#step_ets_seo_analysis .js-ets-seo-show-seo-analysis-tab').hide();
    $('#category-seo-analysis .js-ets-seo-show-seo-analysis-tab').hide();
    $('.ets-seo-right-column .js-ets-seo-show-seo-analysis-tab').hide();

    return false;
  });

  $(document).on('click', '.ets_seo_categories a.js-ets-seo-tab-customize', function() {
    const tabActive = $(this).attr('href');
    if (tabActive == '#step_ets_seo_analysis' || tabActive == '#category-seo-analysis' || tabActive == '#ets_seo_analysis_tabs') {
      $('#step_ets_seo_analysis .js-ets-seo-show-seo-analysis-tab').hide();
      $('#category-seo-analysis .js-ets-seo-show-seo-analysis-tab').hide();
      $('.ets-seo-right-column .js-ets-seo-show-seo-analysis-tab').hide();
    } else {
      $('.ets-seo-right-column .js-ets-seo-show-seo-analysis-tab').show();
    }
    $(this).closest('.tab-content').find('.tab-pane:not(.translation-field)').removeClass('show');
    $(this).closest('.tab-content').find('.tab-pane:not(.translation-field)').removeClass('active');
    $(tabActive).addClass('show active');
  });

  // Click product tab
  $(document).on('click', 'a.nav-link', function() {
    const tabActive = $(this).attr('href');
    if (tabActive == '#step_ets_seo_analysis') {
      $('#step_ets_seo_analysis .js-ets-seo-show-seo-analysis-tab').hide();
    }
  });
  if (etsSeoBo.currentController == 'AdminProducts') {
    $(document).on('change', etsSeo.prefixInput().category, function() {
      let idCategory = $(this).val();
      if (!idCategory) {
        idCategory = $(this).parent().find('input[type="checkbox"]').val();
      }
      etsSeo.getCategoryName(idCategory);
    });
    $(document).bind('ajaxSuccess', (e, xhr, settings) => {
      if (settings.url.indexOf('sell/catalog/products/image/form') !== -1) {
        $('textarea[id*="form_image_legend_"]').each(function() {
          const elem = $(this);
          const id_lang = etsSeo.getIdLang(elem.attr('id'));
          if (!elem.data('isWarningSet') && ETS_SEO_DEFINED.meta_template_configured[id_lang] && ETS_SEO_DEFINED.meta_template_configured[id_lang].imgAlt) {
            if (ETS_SEO_DEFINED.meta_template_configured[id_lang].isForce) {
              const alertDiv = `<div class="alert alert-warning">${ETS_SEO_MESSAGE.warning_img_alt_use_meta_template}</div>`;
              elem.prop('disabled', true).val(ETS_SEO_DEFINED.meta_template_configured[id_lang].imgAlt);
              elem.parent().append(alertDiv);
            } else {
              elem.attr('placeholder', ETS_SEO_MESSAGE.placeholder_meta);
            }
            elem.data('isWarningSet', true);
          }
          if (!elem.data('isBtnAdded')) {
            elem.data('isBtnAdded', true);
            if (ETS_SEO_DEFINED.meta_template_configured[id_lang] && !ETS_SEO_DEFINED.meta_template_configured[id_lang].isForce) {
              if (ETS_SEO_DEFINED.meta_short_code_btn) {
                elem.parent().append(ETS_SEO_DEFINED.meta_short_code_btn);
              }
            }
          }
        });
      }
    });
  }


  if (etsSeoBo.currentController == 'AdminProducts' && ETS_SEO_ENABLE_AUTO_ANALYSIS) {
    // From 1.7.x PS using XMLHTTP for doing ajax in backend (almost action)
    const orgSend = XMLHttpRequest.prototype.send;
    XMLHttpRequest.prototype.send = function() {
      this.addEventListener('readystatechange', function(e) {
        if (this.readyState === XMLHttpRequest.DONE) {
          const settings = {url: this.responseURL};
          try {
            this.responseJSON = JSON.parse(this.response);
            _onXhrSuccess(e, this, settings);
          } catch (ex) {

          }
        }
      });
      return orgSend.apply(this, arguments);
    };
    // Action for upload, delete or update alt for product image
    function _onXhrSuccess(evt, xhr, settings) {
      if (xhr.readyState == 4 && xhr.status == 200 && xhr.responseJSON) {
        if (!ETS_SEO_PRODUCT_IMAGE) {
          ETS_SEO_PRODUCT_IMAGE = {};
        }
        let update = false;
        const res = xhr.responseJSON;
        if (res.legend && res.id) {
          if (!res.url_delete) { // Update data image
            Object.keys(res.legend).forEach(function(key) {
              if (ETS_SEO_PRODUCT_IMAGE[key]) {
                for (let i = 0; i < ETS_SEO_PRODUCT_IMAGE[key].length; i++) {
                  if (ETS_SEO_PRODUCT_IMAGE[key][i].id_image == res.id) {
                    ETS_SEO_PRODUCT_IMAGE[key][i].legend = res.legend[key];
                  }
                }
              }
            });
            update = true;
          }
        } else {
          if (settings.url && (settings.url.indexOf('/catalog/products/image/upload/') !== -1)) { // Upload image
            var idImage = $('.dz-preview').last().attr('data-id');
            const altImage = $('.dz-preview').last().find('img').attr('alt');
            let exists = false;
            Object.keys(ETS_SEO_PRODUCT_IMAGE).forEach(function(key) {
              if (typeof ETS_SEO_PRODUCT_IMAGE[key] !== 'undefined') {
                for (let i = 0; i < ETS_SEO_PRODUCT_IMAGE[key].length; i++) {
                  if (ETS_SEO_PRODUCT_IMAGE[key][i].id_image == idImage) {
                    exists = true;
                    break;
                  }
                }
                if (!exists) {
                  ETS_SEO_PRODUCT_IMAGE[key].push({
                    cover: null,
                    id_image: idImage,
                    legend: altImage,
                    position: 1,
                  });
                }
              }
            });
            if (!exists) {
              update = true;
            }
          } else if (settings.url && (settings.url.indexOf('/products/image/delete/') !== -1)) // Delete image
          {
            const lastOfUrl = settings.url.split('/').pop();
            var idImage = lastOfUrl.split('?')[0];
            Object.keys(ETS_SEO_PRODUCT_IMAGE).forEach(function(key) {
              if (typeof ETS_SEO_PRODUCT_IMAGE[key] !== 'undefined') {
                for (let i = 0; i < ETS_SEO_PRODUCT_IMAGE[key].length; i++) {
                  if (ETS_SEO_PRODUCT_IMAGE[key][i].id_image == idImage) {
                    ETS_SEO_PRODUCT_IMAGE[key].splice(i, 1);
                  }
                }
              }
            });
            update = true;
          }
        }
        if (update) {
          Object.keys(ETS_SEO_LANGUAGES).forEach(function(key) {
            const curIdLang = ETS_SEO_LANGUAGES[key];
            etsSeo.productShortDescIdPrefixes.forEach((selector) => {
              const elem = $(`#${selector}${curIdLang}`);
              if (elem.length && etsSeo.getTextContentFromInputs(curIdLang).length) {
                etsSeo.analysisContent(curIdLang, etsSeo.getTextContentFromInputs(curIdLang));
              }
            });
          });
        }
      }
    }
  }

  // Analysis after init

  $(window).on('load', function() {
    etsSeo.parseProblemFromData();
    etsSeo.changePlaceholderMeta();
    setTimeout(function() {
      etsSeo.updateSnippet();
    }, 500);
    if (etsSeo.activeControllers.indexOf(etsSeoBo.currentController) !== -1) {
      etsSeo.initTabSeo(etsSeoBo.currentActiveLangId);
      setTimeout(function() {
        if (!ETS_SEO_ENABLE_AUTO_ANALYSIS || ETS_SEO_SCORE_DATA) {
          if (ETS_SEO_SCORE_DATA) {
            Object.keys(ETS_SEO_SCORE_DATA).forEach((v) => {
              // noinspection EqualityComparisonWithCoercionJS
              if (v != etsSeoBo.currentActiveLangId) {
                etsSeo.initTabSeo(v);
              }
              etsSeo.missingAnalyzeByLang.etsRemove(v);
            });
          }
          $('#ets_seo_score_data').val(JSON.stringify({seo_score: etsSeo.seo_score, readability_score: etsSeo.readability_score}));
          etsSeo.showSuccessMessageAnalysis(false);
        }
      }, 300);

      if (etsSeoBo.currentController == 'AdminProducts') {
        $('#step_ets_seo_analysis .js-ets-seo-show-seo-analysis-tab').hide();
      }
    }

    if ($('.js-ets-seo-tagify').length) {
      $('.js-ets-seo-tagify').each(function() {
        const id = $(this).attr('id');
        const id_lang = $(this).attr('data-idlang');
        const etsTagify = new Tagify(document.querySelector('#' + id), {
          templates: {
            tag: function(v, tagData) {
              return '<tag contenteditable="false" spellcheck="false" class="tagify__tag ' + (tagData.class ? tagData.class : '') + '" ' + this.getAttributes(tagData) + '><x title="" class="tagify__tag__removeBtn"></x><div><span class="tagify__tag-text">' + v + '</span></div></tag>';
            },
          },
        });
        etsTagify.on('add', function(e) {
          const key_phrase = $('input[name="ets_seo_key_phrase[' + etsSeoBo.currentActiveLangId + ']"]').val();
          if (e.detail.data.value == key_phrase) {
            etsTagify.removeTag(e.detail.data.value);
            showErrorMessage(ETS_SEO_MESSAGE.minor_keyphrase_same_focus_keyphrase);
          }
          if (ETS_SEO_ENABLE_AUTO_ANALYSIS) {
            etsSeo.analysisMinorKeyphrase();
          }
        });
        etsTagify.on('remove', function(e) {
          if (ETS_SEO_ENABLE_AUTO_ANALYSIS) {
            etsSeo.analysisMinorKeyphrase();
          }
        });
        etsTagify.on('edit', function(e) {
          const key_phrase = $('input[name="ets_seo_key_phrase[' + etsSeoBo.currentActiveLangId + ']"]').val();
          if (e.detail.data.value == key_phrase) {
            etsTagify.removeTag(e.detail.data.value);
            showErrorMessage(ETS_SEO_MESSAGE.minor_keyphrase_same_focus_keyphrase);
          }
          if (ETS_SEO_ENABLE_AUTO_ANALYSIS) {
            etsSeo.analysisMinorKeyphrase();
          }
        });
      });
    }
  });

  $(document).on('click', '.js-ets-seo-btn-control-analysis:not(.loading)', function(event) {
    const $this = $(this);
    $this.addClass('loading');
    etsSeo.showMessageAnalysis = false;
    const startTime = new Date().getTime()/1000;
    const prefix = etsSeo.prefixInput();
    $('#ets_seo_score_data').val('');
    $('.js-ets-seo-preview-analysis .processing').addClass('excuting');
    $('.js-ets-seo-preview-analysis .sub-title').html(ETS_SEO_MESSAGE.analyzing);
    setTimeout(function() {
      Object.keys(ETS_SEO_LANGUAGES).forEach(function(key) {
        const id_lang = ETS_SEO_LANGUAGES[key];
        if ($(prefix.meta_title + id_lang).length) {
          let content = '';
          let desc = ''; let shortDesc = '';
          if (prefix.short_desc && $(prefix.short_desc + id_lang).length) {
            shortDesc = $(prefix.short_desc + id_lang).val();
            content += shortDesc;
          }
          if (prefix.content && $(prefix.content + id_lang).length) {
            desc = $(prefix.content + id_lang).val();
            content += desc;
          }
          etsSeo.analysisContent(id_lang, content);
        }
      });
      const endTime = new Date().getTime()/1000;
      let timeout = 3-( endTime-startTime);
      if (timeout < 0) {
        timeout = 0;
      }
      setTimeout(function() {
        etsSeo.showSuccessMessageAnalysis(false);
        showSuccessMessage(ETS_SEO_MESSAGE['analysis_success']);
        $this.removeClass('loading');
      }, timeout*1000);
    }, 500);
    return false;
  });

  $(document).on('change', '#form_step1_id_manufacturer', function() {
    Object.keys(ETS_SEO_LANGUAGES).forEach(function(key) {
      etsSeo.changePreview(ETS_SEO_LANGUAGES[key]);
    });
  });

  // Detect when user clicks Save button to prevent auto-save conflict
  $(document).on('click', 'button[name="submitAddproduct"], button[name="submitAddproductAndStay"], form[id^="product-form"] button[type="submit"]', function() {
    etsSeo.isSavingProduct = true;
    
    // Re-enable auto-save after 3 seconds (enough time for the save to complete)
    setTimeout(function() {
      etsSeo.isSavingProduct = false;
    }, 3000);
  });

  $(document).on('submit', 'form', function() {
    if ($(this).find('#ets_seo_score_data').length) {
      etsSeo.setScoreToFormData();
    }
  });
})(jQuery);
