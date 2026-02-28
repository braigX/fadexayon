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
(function($) {
  if (!etsSeoBo) {
    return;
  }
  if (!etsSeoBo.hasOwnProperty('isChatGptAvailable')) {
    return;
  }
  if (!etsSeoBo.isChatGptAvailable) {
    return;
  }
  if (etsSeoBo && !etsSeoBo.isEnable) {
    return;
  }
  const getSvgIcon = (w, h) => {
    w = w || 20;
    if (!h && w) {
      h = w;
    }
    h = h || 20;
    return `<svg width="${w}" height="${h}" viewBox="0 0 41 41" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M37.5324 16.8707C37.9808 15.5241 38.1363 14.0974 37.9886 12.6859C37.8409 11.2744 37.3934 9.91076 36.676 8.68622C35.6126 6.83404 33.9882 5.3676 32.0373 4.4985C30.0864 3.62941 27.9098 3.40259 25.8215 3.85078C24.8796 2.7893 23.7219 1.94125 22.4257 1.36341C21.1295 0.785575 19.7249 0.491269 18.3058 0.500197C16.1708 0.495044 14.0893 1.16803 12.3614 2.42214C10.6335 3.67624 9.34853 5.44666 8.6917 7.47815C7.30085 7.76286 5.98686 8.3414 4.8377 9.17505C3.68854 10.0087 2.73073 11.0782 2.02839 12.312C0.956464 14.1591 0.498905 16.2988 0.721698 18.4228C0.944492 20.5467 1.83612 22.5449 3.268 24.1293C2.81966 25.4759 2.66413 26.9026 2.81182 28.3141C2.95951 29.7256 3.40701 31.0892 4.12437 32.3138C5.18791 34.1659 6.8123 35.6322 8.76321 36.5013C10.7141 37.3704 12.8907 37.5973 14.9789 37.1492C15.9208 38.2107 17.0786 39.0587 18.3747 39.6366C19.6709 40.2144 21.0755 40.5087 22.4946 40.4998C24.6307 40.5054 26.7133 39.8321 28.4418 38.5772C30.1704 37.3223 31.4556 35.5506 32.1119 33.5179C33.5027 33.2332 34.8167 32.6547 35.9659 31.821C37.115 30.9874 38.0728 29.9178 38.7752 28.684C39.8458 26.8371 40.3023 24.6979 40.0789 22.5748C39.8556 20.4517 38.9639 18.4544 37.5324 16.8707ZM22.4978 37.8849C20.7443 37.8874 19.0459 37.2733 17.6994 36.1501C17.7601 36.117 17.8666 36.0586 17.936 36.0161L25.9004 31.4156C26.1003 31.3019 26.2663 31.137 26.3813 30.9378C26.4964 30.7386 26.5563 30.5124 26.5549 30.2825V19.0542L29.9213 20.998C29.9389 21.0068 29.9541 21.0198 29.9656 21.0359C29.977 21.052 29.9842 21.0707 29.9867 21.0902V30.3889C29.9842 32.375 29.1946 34.2791 27.7909 35.6841C26.3872 37.0892 24.4838 37.8806 22.4978 37.8849ZM6.39227 31.0064C5.51397 29.4888 5.19742 27.7107 5.49804 25.9832C5.55718 26.0187 5.66048 26.0818 5.73461 26.1244L13.699 30.7248C13.8975 30.8408 14.1233 30.902 14.3532 30.902C14.583 30.902 14.8088 30.8408 15.0073 30.7248L24.731 25.1103V28.9979C24.7321 29.0177 24.7283 29.0376 24.7199 29.0556C24.7115 29.0736 24.6988 29.0893 24.6829 29.1012L16.6317 33.7497C14.9096 34.7416 12.8643 35.0097 10.9447 34.4954C9.02506 33.9811 7.38785 32.7263 6.39227 31.0064ZM4.29707 13.6194C5.17156 12.0998 6.55279 10.9364 8.19885 10.3327C8.19885 10.4013 8.19491 10.5228 8.19491 10.6071V19.808C8.19351 20.0378 8.25334 20.2638 8.36823 20.4629C8.48312 20.6619 8.64893 20.8267 8.84863 20.9404L18.5723 26.5542L15.206 28.4979C15.1894 28.5089 15.1703 28.5155 15.1505 28.5173C15.1307 28.5191 15.1107 28.516 15.0924 28.5082L7.04046 23.8557C5.32135 22.8601 4.06716 21.2235 3.55289 19.3046C3.03862 17.3858 3.30624 15.3413 4.29707 13.6194ZM31.955 20.0556L22.2312 14.4411L25.5976 12.4981C25.6142 12.4872 25.6333 12.4805 25.6531 12.4787C25.6729 12.4769 25.6928 12.4801 25.7111 12.4879L33.7631 17.1364C34.9967 17.849 36.0017 18.8982 36.6606 20.1613C37.3194 21.4244 37.6047 22.849 37.4832 24.2684C37.3617 25.6878 36.8382 27.0432 35.9743 28.1759C35.1103 29.3086 33.9415 30.1717 32.6047 30.6641C32.6047 30.5947 32.6047 30.4733 32.6047 30.3889V21.188C32.6066 20.9586 32.5474 20.7328 32.4332 20.5338C32.319 20.3348 32.154 20.1698 31.955 20.0556ZM35.3055 15.0128C35.2464 14.9765 35.1431 14.9142 35.069 14.8717L27.1045 10.2712C26.906 10.1554 26.6803 10.0943 26.4504 10.0943C26.2206 10.0943 25.9948 10.1554 25.7963 10.2712L16.0726 15.8858V11.9982C16.0715 11.9783 16.0753 11.9585 16.0837 11.9405C16.0921 11.9225 16.1048 11.9068 16.1207 11.8949L24.1719 7.25025C25.4053 6.53903 26.8158 6.19376 28.2383 6.25482C29.6608 6.31589 31.0364 6.78077 32.2044 7.59508C33.3723 8.40939 34.2842 9.53945 34.8334 10.8531C35.3826 12.1667 35.5464 13.6095 35.3055 15.0128ZM14.2424 21.9419L10.8752 19.9981C10.8576 19.9893 10.8423 19.9763 10.8309 19.9602C10.8195 19.9441 10.8122 19.9254 10.8098 19.9058V10.6071C10.8107 9.18295 11.2173 7.78848 11.9819 6.58696C12.7466 5.38544 13.8377 4.42659 15.1275 3.82264C16.4173 3.21869 17.8524 2.99464 19.2649 3.1767C20.6775 3.35876 22.0089 3.93941 23.1034 4.85067C23.0427 4.88379 22.937 4.94215 22.8668 4.98473L14.9024 9.58517C14.7025 9.69878 14.5366 9.86356 14.4215 10.0626C14.3065 10.2616 14.2466 10.4877 14.2479 10.7175L14.2424 21.9419ZM16.071 17.9991L20.4018 15.4978L24.7325 17.9975V22.9985L20.4018 25.4983L16.071 22.9985V17.9991Z" fill="currentColor"></path></svg>`;
  };
  const getUserMsgHtml = (msg) => {
    return `<li id="chatgpt-message-tmp" class="chatgpt-message is_customer" data-id=""><div class="chatgpt-content"><i class="svg_icon" title="You"><svg width="20" height="20" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M896 0q182 0 348 71t286 191 191 286 71 348q0 181-70.5 347t-190.5 286-286 191.5-349 71.5-349-71-285.5-191.5-190.5-286-71-347.5 71-348 191-286 286-191 348-71zm619 1351q149-205 149-455 0-156-61-298t-164-245-245-164-298-61-298 61-245 164-164 245-61 298q0 250 149 455 66-327 306-327 131 128 313 128t313-128q240 0 306 327zm-235-647q0-159-112.5-271.5t-271.5-112.5-271.5 112.5-112.5 271.5 112.5 271.5 271.5 112.5 271.5-112.5 112.5-271.5z"></path></svg></i><p class="chatgpt-content">${msg}</p></div></li>`;
  };
  const getGptMsgHtml = (msg, id)=> {
    return `<li id="chatgpt-message-${id}" class="chatgpt-message is_chatgpt" data-id="${id}">
  <div class="chatgpt-content">
   <i class="svg_icon">${getSvgIcon(20, 20)}</i>
   <p class="chatgpt-content" style="margin-bottom: 0;">${msg}</p>
  </div>
</li>`;
  };
  const getApplyHtml = () => {
    const fieldOptions = () => {
      const html = [];
      etsSeoBo.gptAppendFields.forEach((obj) => {
        html.push(`<option value="${obj.field}">${obj.title}</option>`);
      });
      return html.join('');
    };
    const langOptions = () => {
      const html = [`<option value="__all" data-iso-code="__all">${etsSeoBo.transMsg.allLangLabel}</option>`];
      etsSeoBo.languages.forEach((obj) => {
        html.push(`<option value="${obj.id_lang}" data-iso-code="${obj.iso_code}">${obj.iso_code}</option>`);
      });
      return html.join('');
    };
    return `<div class="chatgpt-button-append row">
  <div class="chatgpt-button-append-content">
    <div class="form-control-label chatgpt-button-append-content-cell">${etsSeoBo.transMsg.applyLabel}</div>
    <div class="chatgpt-button-append-content-cell">
      <div class="chatgpt-button-select-lang">
        <select name="content-apply-chatgpt" class="form-control">
          ${fieldOptions()}
        </select>
        <div class="translatable-field-list">
          <select name="langIdToApply" class="form-control">
            ${langOptions()}
          </select>
        </div>
      </div>
    </div>
    <div class="chatgpt-button-append-content-cell chatgpt-button-apply">
      <button class="btn btn-default btn-apply-chatgpt">
        <i class="icon_svg">
          <svg width="12" height="12" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg">
            <path
                d="M1675 971q0 51-37 90l-75 75q-38 38-91 38-54 0-90-38l-294-293v704q0 52-37.5 84.5t-90.5 32.5h-128q-53 0-90.5-32.5t-37.5-84.5v-704l-294 293q-36 38-90 38t-90-38l-75-75q-38-38-38-90 0-53 38-91l651-651q35-37 90-37 54 0 91 37l651 651q37 39 37 91z"></path>
          </svg>
        </i> ${etsSeoBo.transMsg.applyBtn}
      </button>
    </div>
  </div>
</div>`;
  };
  const correctApplyFieldSelect = () => {
    const selectedVal = $('#etsSeoChatGptBox input[name="input_content_name"]').val();
    $('select[name="content-apply-chatgpt"]').val(selectedVal);
    $('select[name="langIdToApply"]').val(etsSeoBo.currentActiveLangId);
  };
  const addApplyBtn = () => {
    $('.chatgpt-message.is_chatgpt').each((i, el) => {
      el = $(el);
      if (el.find('.chatgpt-button-append').length) {
        return;
      }
      el.append(getApplyHtml());
    });
    correctApplyFieldSelect();
  };
  const replaceContentShortCode = (content, idLang) => {
    const re = new RegExp(/{(\w+)}/g);
    const matched = content.match(re);
    if (matched) {
      matched.forEach((v) => {
        const code = v.replace(re, '$1');
        if (etsSeoBo.gptContentShortCodeSelectorPrefix.hasOwnProperty(code)) {
          let selector = etsSeoBo.gptContentShortCodeSelectorPrefix[code];
          selector = selector.replace('${idLang}', idLang);
          const input = $(selector);
          if (code === 'language') {
            let isReplaced = false;
            etsSeoBo.languages.forEach((obj) => {
              if (Number(etsSeoBo.currentActiveLangId) === Number(obj.id_lang)) {
                content = content.replace(v, obj.name);
                isReplaced = true;
              }
            });
            if (!isReplaced) {
              content = content.replace(v, '');
            }
          } else if (code === 'default_category') {
            let val;
            if (input.is('#product_description_categories_default_category_id')) {
              val = input.length ? input.find('option:selected').text().trim() : '';
            } else {
              val = input.length ? input.parent().text().trim() : '';
            }
            content = content.replace(v, val);
          } else if (code === 'brand') {
            const val = input.length ? input.children('option:selected').text().trim() : '';
            content = content.replace(v, val);
          } else {
            const val = input.length ? input.val() : '';
            content = content.replace(v, val);
          }
        }
      });
    }

    return content;
  };
  const registerTemplateEvents = () => {
    $(document).on('gptLangSelectChanged', (e, params) => {
      hideOtherLanguage(params.langId);
    });
    $(document).on('langSelectChanged', (e, params) => {
      hideOtherLanguage(params.langId);
    });
    hideOtherLanguage(etsSeoBo.currentActiveLangId);
    $(document).on('click', '#etsSeoChatGptBox .gpt-item-template', (e) => {
      const clickedElem = $(e.target);
      const textBox = $('#etsSeoChatGptBox [name="message-chatppt"]');
      textBox.val(clickedElem.data('content').trim()).focus();
    });
  };
  const _addOpenBtnMetaFields = () => {
    const id = etsSeoBo.currentActiveLangId;
    $(`input[id$="meta_title_${id}"][name$="[meta_title][${id}]"], [id$="meta_description_${id}"][name$="[meta_description][${id}]`).each((i, el) => {
      const input = $(el);
      const isTitle = input.is(`input[id$="meta_title_${id}"]`);
      if ($('.ets-trans-field-boundary').length > 0) {
        input.parents('.form-group').first().find('label').append(`<button class="ets-seo-open-chatgpt-box btn-open-chatgpt" title="ChatGPT" data-field="${isTitle ? 'meta_title' : 'meta_description'}">${getSvgIcon(41, 41)}</button>`);
      } else {
        const selector = etsSeoBo.currentController === 'AdminProducts' ? 'label' : '.dropdown button.dropdown-toggle';
        input.parents('.form-group').first().find(selector).after(`<button class="ets-seo-open-chatgpt-box btn-open-chatgpt" title="ChatGPT" data-field="${isTitle ? 'meta_title' : 'meta_description'}">${getSvgIcon(41, 41)}</button>`);
      }
    });
  };
  const _addOpenGptCommonPage = (idSelectors) => {
    $(idSelectors).each((i, el) => {
      el = $(el);
      el.parents('.form-group.row').first()
          .addClass(`gpt-field-${el.attr('id')}`).find('label')
          .append(`<button class="ets-seo-open-chatgpt-box btn-open-chatgpt" title="ChatGPT" data-field="${el.attr('id')}">${getSvgIcon(41, 41)}</button>`);
    });
  };
  const addOpenGptBtnProduct = () => {
    let container = $('.summary-description-container');
    let list = container.find('div[id^="description"]');
    if (!container.length) {
      container = $('#product_description');
      list = container.find('div[id^="product_description"]');
    }
    list.each((i, el) => {
      el = $(el);
      if (!el.is('div[id$="description_short"]') && !el.is('div[id$="description"]')) {
        return;
      }
      const field = el.attr('id').replace('product_description_', '');
      el.append(`<button class="ets-seo-open-chatgpt-box btn-open-chatgpt" title="ChatGPT" data-field="${field}">${getSvgIcon(41, 41)}</button>`);
    });
    _addOpenBtnMetaFields();
  };
  const addOpenGptBtnCategory = () => {
    _addOpenGptCommonPage('#category_description, #category_additional_description');
    _addOpenBtnMetaFields();
  };
  const addOpenGptCms = () => {
    _addOpenGptCommonPage('#cms_page_content');
    _addOpenBtnMetaFields();
  };
  const resetChatBoxPosition = () => {
    const box = $('#etsSeoChatGptBox .ets-seo-chatgpt-box');
    if (box.outerHeight() + 100 > $(window).height()) {
      box.css('top', '30px');
    }
    if (box.find('.form-wrapper .chatgpt-message').length) {
      const frmWrapper = box.find('.form-wrapper');
      const lastChild = box.find('.chatgpt-message:last-child');
      frmWrapper.animate({
        scrollTop: frmWrapper.scrollTop() + lastChild.position().top + 30,
      });
    }
  };
  const registerDrag = () => {
    const click = {
      x: 0,
      y: 0,
    };
    const getLeftTopValues = (event, ui) => {
      const box = $('.ets-seo-chatgpt-box');
      const original = ui.originalPosition;
      let left = event.clientX - click.x + original.left;
      let top = event.clientY - click.y + original.top;
      const max_left = $(window).width() - box.outerWidth();
      const max_top = $(window).height() - box.outerHeight();
      if (left > max_left) {
        left = max_left;
      }
      if (top > max_top) {
        top = max_top;
      }
      return {
        left: left > 0 ? left : 0,
        top: top > 0 ? top : 0,
      };
    };
    $('.ets-seo-chatgpt-box').draggable({
      cursor: 'grabbing',
      connectToSortable: 'body',
      containment: 'body',
      handle: '.panel-heading',
      scroll: false,
      start: function(event) {
        click.x = event.clientX;
        click.y = event.clientY;
      },
      drag: function(event, ui) {
        ui.position = getLeftTopValues(event, ui);
      },
      stop: function(event, ui) {
        const box = $('.ets-seo-chatgpt-box');
        const {left, top} = getLeftTopValues(event, ui);
        box.attr('data-left', left > 0 ? left : 0);
        box.attr('data-top', top > 0 ? top : 0);
        box.css('left', (left > 0 ? left : 0) + 'px');
        box.css('top', (top > 0 ? top : 0) + 'px');
      },
    });
    $('.ets-seo-chatgpt-box.resize').resizable();
  };
  const _showErrorMessage = (msg) => {
    $.growl({title: '', style: 'error', duration: 30000, message: msg});
  };
  const _handleErrorMessages = (res) => {
    if (typeof res === typeof {}) {
      if (res.hasOwnProperty('ok') && res.ok === true) {
        return;
      }
      if (res.hasOwnProperty('hasErrors') && res.hasErrors === false) {
        return;
      }
      if (res.hasOwnProperty('message')) {
        _showErrorMessage(res.message);
      }
      if (res.hasOwnProperty('errors') && res.errors && res.errors.length > 1) {
        res.errors.forEach((v, i) => i > 0 && _showErrorMessage(v));
      }
    } else {
      _showErrorMessage(etsSeoBo.transMsg.anErrorOccur);
    }
  };
  const _refreshHeightBoxChatGPT = () => {
    let textBoxHeight;
    const actionDiv = $('.box-actions');
    const chatBoxHeading = $('.ets-seo-chatgpt-box .panel-heading');
    const sendMsgDiv = $('.chatgpt-box-send');
    const box = $('.ets-seo-chatgpt-box');
    if (actionDiv.length > 0) {
      textBoxHeight = sendMsgDiv.outerHeight() + chatBoxHeading.outerHeight() + 40 + actionDiv.outerHeight();
    } else {
      textBoxHeight = sendMsgDiv.outerHeight() + chatBoxHeading.outerHeight() + 40;
    }
    $('.ets-seo-chatgpt-box > .form-wrapper').css('max-height', 'calc(100% - '+ textBoxHeight +'px)');
    if ($('#chatgpt-history-list li').length > 0) {
      box.css('min-height', 'calc('+ textBoxHeight +'px + 140px)');
    } else {
      box.css('min-height', 'calc('+ textBoxHeight +'px + 40px)');
    }
  };

  $(document).ready(() => {
    /* Product */
    if (etsSeoBo.currentController === 'AdminProducts' && etsSeoBo.isEditingProduct) {
      addOpenGptBtnProduct();
      $(document).on('gptLangSelectChanged', (e, params) => {
        $('#form_switch_language').val(params.isoCode).trigger('change');
        if ($('.product-header-v2').length) {
          $(`.product-header-v2 .js-locale-item[data-locale="${params.isoCode}"]`).first().trigger('click');
        }
      });
    }
    /* Category */
    if (etsSeoBo.currentController === 'AdminCategories' && etsSeoBo.isEditingCategory) {
      addOpenGptBtnCategory();
      $(document).on('gptLangSelectChanged', (e, params) => {
        $(`.nav-link[data-locale="${params.isoCode}"]`).trigger('click');
        $(`.locale-input-group .js-locale-item[data-locale="${params.isoCode}"]`).first().trigger('click');
      });
    }
    /* CMS */
    if (etsSeoBo.currentController === 'AdminCmsContent' && etsSeoBo.isEditingCms) {
      addOpenGptCms();
      $(document).on('gptLangSelectChanged', (e, params) => {
        $(`.nav-link[data-locale="${params.isoCode}"]`).trigger('click');
        $(`.locale-input-group .js-locale-item[data-locale="${params.isoCode}"]`).first().trigger('click');
      });
    }
    /* End specific page */
  });
  $(document).on('change', '#etsSeoChatGptBox select[name="langIdToApply"]', (e) => {
    const changedElem = $(e.target);
    e.stopPropagation();
    if (changedElem.val() === '__all') {
      return;
    }
    etsSeoBo.currentActiveLangId = Number(changedElem.val());
    $(document).trigger('gptLangSelectChanged', {
      langId: etsSeoBo.currentActiveLangId,
      isoCode: changedElem.find('option:selected').data('isoCode'),
    });
  });
  $(document).on('langSelectChanged', (e, params) => {
    $('#etsSeoChatGptBox select[name="langIdToApply"]').val(params.langId);
  });
  let isChatBoxInit = false;
  $(document).on('click', '.ets-seo-open-chatgpt-box', function(e) {
    e.preventDefault();
    const clickedElem = $(this);
    if (!isChatBoxInit) {
      $.ajax({
        url: etsSeoBo.chatGptAdminUrl,
        data: {
          getChatBox: 1,
          currentPage: etsSeoBo.currentController,
        },
        type: 'POST',
        dataType: 'json',
        beforeSend: () => {
          // Add loading here, then hide loading on success & error
        },
        success: (res) => {
          if (res.ok && res.html) {
            $('body').append(`<div id="etsSeoChatGptBox" class="bootstrap">${res.html}</div>`);
            $('#etsSeoChatGptBox [name="message-chatppt"]').focus();
            $('#etsSeoChatGptBox input[name="input_content_name"]').val(clickedElem.data('field'));
            addApplyBtn();
            registerTemplateEvents();
            isChatBoxInit = true;
            registerDrag();
            resetChatBoxPosition();
          } else {
            _handleErrorMessages(res);
          }
          _refreshHeightBoxChatGPT();
        },
        error: () => {
          _handleErrorMessages();
        },
      });
    } else {
      $('#etsSeoChatGptBox').show();
      _refreshHeightBoxChatGPT();
      $('#etsSeoChatGptBox [name="message-chatppt"]').focus();
      $('#etsSeoChatGptBox input[name="input_content_name"]').val(clickedElem.data('field'));
      addApplyBtn();
      resetChatBoxPosition();
    }
  });
  const putMessageContentToInput = (prefix, field, idLang, content) => {
    const input = $(`${prefix} [id$="${field}_${idLang}"]`);
    if (!input.length) {
      return;
    }
    if (input.hasClass('autoload_rte')) {
      tinymce.get(input.attr('id')).setContent(content);
      tinymce.get(input.attr('id')).setDirty(true);
      tinymce.get(input.attr('id')).focus(false);
      tinymce.triggerSave();
      tinymce.execCommand('mceFocus', false, input.attr('id'));
    } else {
      input.val(content);
    }
  };
  $(document).on('click', '#etsSeoChatGptBox .btn-apply-chatgpt', function(e) {
    e.preventDefault();
    const selectorPrefix = etsSeoBo.hasOwnProperty('gptFieldSelectorPrefix') ? etsSeoBo.gptFieldSelectorPrefix : {};
    const clickedElem = $(this);
    const msgLine = clickedElem.parents('.chatgpt-message.is_chatgpt');
    const field = msgLine.find('select[name="content-apply-chatgpt"]').val();
    const idLang = msgLine.find('select[name="langIdToApply"]').val();
    const prefix = selectorPrefix.hasOwnProperty(field) ? selectorPrefix[field] : '';
    const content = msgLine.find('p.chatgpt-content').html().trim();
    if (idLang === '__all') {
      etsSeoBo.languages.forEach((obj) => putMessageContentToInput(prefix, field, obj.id_lang, content));
    } else {
      putMessageContentToInput(prefix, field, idLang, content);
    }
    showSuccessMessage(etsSeoBo.transMsg.applyConfirm);
  });
  $(document).on('click', '#etsSeoChatGptBox .close-chatgpt-box', (e) => {
    e.preventDefault();
    $('#etsSeoChatGptBox').hide();
  });
  $(document).on('click', '#etsSeoChatGptBox .maximize-chat-gpt', function(e) {
    const box = $('.ets-seo-chatgpt-box');
    box.addClass('maximize').removeClass('minimize');
    $('body').addClass('no_scroll');
  });
  $(document).on('click', '#etsSeoChatGptBox .minimize-chat-gpt', function(e) {
    const box = $('.ets-seo-chatgpt-box');
    box.addClass('minimize').removeClass('maximize');
    $('body').removeClass('no_scroll');
  });
  $(document).on('click', '#etsSeoChatGptBox .btn-send-gpt', (e) => {
    e.preventDefault();
    const msgContent = $('#etsSeoChatGptBox textarea[name="message-chatppt"]');
    if (!msgContent.val().trim().length) {
      alert(etsSeoBo.transMsg.messageRequire);
      return;
    }
    const replacedShortcodeContent = replaceContentShortCode(msgContent.val().trim(), etsSeoBo.currentActiveLangId);
    const messageList = $('#etsSeoChatGptBox #chatgpt-history-list');
    const actionDiv = $('#etsSeoChatGptBox .box-actions');
    $.ajax({
      url: etsSeoBo.chatGptAdminUrl,
      data: {
        sendMessage: 1,
        message: replacedShortcodeContent,
      },
      type: 'POST',
      dataType: 'json',
      beforeSend: () => {
        messageList.append(getUserMsgHtml(replacedShortcodeContent));
        msgContent.val('');
        messageList.append('<li id="chatgpt-message-loading" class="chatgpt-message"><p class="chatgpt-content chatgpt-loading"></p></li>');
        resetChatBoxPosition();
      },
      success: (res) => {
        messageList.find('#chatgpt-message-loading').detach();
        if (res.ok) {
          messageList.append(getGptMsgHtml(res.result, res.id));
          messageList.find('#chatgpt-message-tmp').attr('id', `chatgpt-message-${res.parentId}`).attr('data-id', res.parentId).data('id', res.parentId);
          actionDiv.show();
        } else {
          messageList.find('.chatgpt-message.is_customer#chatgpt-message-tmp').last().detach();
          _handleErrorMessages(res);
        }
        addApplyBtn();
        resetChatBoxPosition();
      },
      error: () => {
        messageList.find('#chatgpt-message-loading').detach();
        messageList.find('.chatgpt-message.is_customer#chatgpt-message-tmp').last().detach();
        _handleErrorMessages();
      },
    });
  });
  $(document).on('keydown', '#etsSeoChatGptBox [name="message-chatppt"]', (e) => {
    if (e.originalEvent.keyCode === 13) {
      e.preventDefault();
      $('#etsSeoChatGptBox .btn-send-gpt').trigger('click');
    }
  });
  $(document).on('click', '#etsSeoChatGptBox .btn-clear-all-message', (e) => {
    e.preventDefault();
    if (!confirm(etsSeoBo.transMsg.clearAllConfirm)) {
      return;
    }
    const messageList = $('#etsSeoChatGptBox #chatgpt-history-list');
    const actionDiv = $('#etsSeoChatGptBox .box-actions');
    $.ajax({
      url: etsSeoBo.chatGptAdminUrl,
      data: {
        clearAllMessages: 1,
      },
      type: 'POST',
      dataType: 'json',
      beforeSend: () => {
        messageList.append('<li id="chatgpt-message-loading" class="chatgpt-message"><p class="chatgpt-content chatgpt-loading"></p></li>');
      },
      success: (res) => {
        messageList.find('#chatgpt-message-loading').detach();
        if (res.ok) {
          messageList.html('');
          actionDiv.hide();
        } else {
          _handleErrorMessages(res);
        }
      },
      error: () => {
        messageList.find('#chatgpt-message-loading').detach();
        _handleErrorMessages();
      },
    });
  });
})(jQuery);
