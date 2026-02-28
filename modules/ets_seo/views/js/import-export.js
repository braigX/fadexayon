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
(function(jQuery) {
  /**
   * serializeObject extension for jQ
   *
   * @return {{}}
   */
  jQuery.fn.serializeObject = function() {
    const obj = {};

    jQuery.each( this.serializeArray(), function(i, o) {
      const n = o.name;
      const v = o.value;

      obj[n] = obj[n] === undefined ? v :
        jQuery.isArray( obj[n] ) ? obj[n].concat( v ) :
          [obj[n], v];
    });

    return obj;
  };

  function preventCloseTab(bool) {
    if (bool) {
      window.onbeforeunload = function() {
        return 'Some task is in progress. Are you sure, you want to close?';
      };
    } else {
      window.onbeforeunload = null;
    }
  }

  let hasTaskRunning = false;
  function _handleErrorResponse(progressDiv, response) {
    if (response.hasOwnProperty('errors') && response.errors.length) {
      let html = `<strong>Error(s) occur</strong><ul>`;
      jQuery.each(response.errors, (i, e) => {
        html += `<li>${e}</li>`;
      });
      html += `</ul>`;
      progressDiv.data('oldMsg', progressDiv.text()).removeClass('alert-info').addClass('alert-danger').html(html);
    } else {
      progressDiv.removeClass('alert-danger').addClass('alert-info').html(progressDiv.data('oldMsg'));
    }
  }
  function downloadURI(uri, name) {
    const link = document.createElement('a');
    link.download = name;
    link.href = uri;
    link.click();
  }
  function _doExportAjax(e) {
    e && e.preventDefault();
    const exHelpDiv = jQuery('.js-export-help');
    const exProgressDiv = jQuery('.js-export-progress');
    const btn = jQuery('.js-ets-seo-export');
    const isExporting = btn.data('isExporting');
    let data = {};
    if (isExporting) {
      data.continue = 1;
    } else {
      data = jQuery('#configuration_form').serializeObject();
    }
    data.exportData = 1;
    jQuery.ajax({
      url: '',
      method: 'POST',
      dataType: 'json',
      data: data,
      beforeSend: () => {
        preventCloseTab(true);
        btn.prop('disabled', true);
        if (!hasTaskRunning) {
          exHelpDiv.hide();
          exProgressDiv.show().html('Processing. Sending request...');
        }
      },
      success: (res) => {
        console.info(res);
        if (res.ok === true) {
          const data = res.data;
          if (data.result) {
            hasTaskRunning = !data.isCompleted;
          }
          if (res.message) {
            exProgressDiv.removeClass('alert-danger').addClass('alert-info');
            exProgressDiv.html(res.message);
          }
          if (hasTaskRunning) {
            btn.data('isExporting', 1);
            _doExportAjax();
          } else {
            btn.data('isExporting', 0);
          }
          if (data.isCompleted && data.fileName && data.downloadUrl) {
            downloadURI(`${data.downloadUrl}`, data.fileName);
          }
        } else {
          _handleErrorResponse(exProgressDiv, res);
          btn.prop('disabled', false);
        }
      },
      error: function() {
        console.info('error', arguments);
      },
      complete: () => {
        preventCloseTab(false);
        btn.prop('disabled', hasTaskRunning);
      },
    });
  }
  jQuery(document).on('click', '.js-ets-seo-export', _doExportAjax);
})(jQuery);
