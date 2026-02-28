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
  if (typeof etsSeoFo === typeof undefined) {
    return;
  }

  function correctProductAttribute() {
    const hash = etsHelper.getLocationHash();
    if (hash.length && hash.split('/').length && etsSeoFo.productGroups) {
      let classSelector = '.product-variants';
      if (jQuery('.js-product-variants').length) {
        classSelector = '.js-product-variants';
      }
      if (jQuery('.ce-product-variants').length) {
        classSelector = '.ce-product-variants';
      }
      const parts = hash.split('/').map((v) => decodeURIComponent(v));
      let firstElem;
      parts.forEach((item) => {
        if (!item.trim().length) {
          return;
        }
        const re = new RegExp(/\d-\w+/);
        let name; let value; let id;
        if (re.test(item)) {
          [id, name, value] = item.split('-');
        } else {
          [name, value] = item.split('-');
        }
        name = name.toLowerCase();
        if (etsSeoFo.productGroups[name]) {
          const group = etsSeoFo.productGroups[name];
          const elem = jQuery(`${classSelector} [name="group[${group.idGroup}]"]`);
          if (!firstElem) {
            firstElem = elem;
          }
          if (elem.is('select')) {
            elem.find('option').prop('selected', false);
            elem.val('');
          } else {
            elem.prop('checked', false);
          }
          group.attributes.forEach((attr) => {
            let condTest;
            if (id) {
              condTest = Boolean(attr.id === Number(id));
            } else {
              condTest = Boolean(attr.url.toLowerCase() === value.toLowerCase());
            }
            if (condTest) {
              if (elem.is('select')) {
                elem.find(`option[value="${attr.id}"]`).prop('selected', true);
                elem.val(attr.id);
              } else {
                elem.filter(`[value="${attr.id}"]`).prop('checked', true);
              }
            }
          });
        }
      });
      if (window.hasOwnProperty('ceFrontend') && window.ceFrontend.hasOwnProperty('refreshProduct') && firstElem) {
        if (firstElem.is('select')) {
          firstElem.trigger('change.ce');
        } else {
          firstElem.first(':checked').trigger('change.ce');
        }
      } else {
        window.setTimeout(() => {
          console.log('correctProductAttribute timeout run');
          const e = jQuery.Event('change');
          e.target = firstElem;
          e.handleObj = e.handleObj ? e.handleObj : {};
          e.handleObj.selector = '.product-variants, .js-product-variants *[name]';
          e.originalEvent = e;
          prestashop.emit('updateProduct', {
            eventType: 'updatedProductCombination',
            event: e,
          });
        }, 200);
      }
    }
  }
  // Register events
  if (etsSeoFo.productHasGroups && etsSeoFo.conf.removeId) {
    jQuery(document).ready(correctProductAttribute);
  }
}(jQuery));
