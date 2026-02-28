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
/**
 * Class EtsHelpers
 *
 * @since 2.5.4
 * @constructor
 */
function EtsHelpers() {
  /**
   * Return TRUE if given value is function
   * @param {any} val
   *
   * @return {boolean}
   */
  this.isFn = function(val) {
    return typeof val === typeof Function;
  };
  /**
   * @param {any} val
   *
   * @return {boolean}
   */
  this.isset = function(val) {
    return typeof val !== typeof undefined;
  };
  /**
   * Strip html tags from string
   *
   * @param {String} str
   * @param {String|[String]|undefined} tags
   *
   * @return {String}
   */
  this.stripHtmlTags = function(str, tags) {
    let expr = '';
    if (this.isset(tags)) {
      if (Array.isArray(tags)) {
        expr = `(${tags.join('|')})`;
      } else {
        expr = tags;
      }
    } else {
      // will remove all tags
      expr = '[a-z0-9]';
    }
    const re = new RegExp(`<\\/?${expr}[^>]*?>`, 'gi');
    return str.replace(re, '');
  };
  /**
   * Detect string has given html tag
   *
   * @param {String} str
   * @param {String|[String]} tag
   *
   * @return {Boolean}
   */
  this.hasHtmlTag = function(str, tag) {
    let expr;
    if (Array.isArray(tag)) {
      expr = `(${tag.join('|')})`;
    } else {
      expr = tag;
    }
    const re = new RegExp(`<\\/?${expr}[^>]*?>`, 'gi');
    return re.test(str);
  };
  /**
   * Return location.hash with out "#"
   *
   * @return {string}
   */
  this.getLocationHash = function() {
    return location.hash.substr(1);
  };
  /**
   * Fallback function for old browsers
   * @param {String} text
   * @param {Function=} onSuccess
   * @param {Function=} onError
   */
  const fallbackCopyTextToClipboard = (text, onSuccess, onError) => {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.style.top = '0';
    textArea.style.left = '0';
    textArea.style.position = 'fixed';
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    try {
      const successful = document.execCommand('copy');
      const msg = successful ? 'successful' : 'unsuccessful';
      if (successful && typeof onSuccess === typeof Function) {
        onSuccess.apply(null, [text]);
      } else if (typeof onError === typeof Function) {
        onError.apply(null, [text, 'Fallback: Copying text command was unsuccessful']);
      }
    } catch (err) {
      onError.apply(null, [text, 'Fallback: Oops, unable to copy', err]);
      console.error('Fallback: Oops, unable to copy', err);
    }
    document.body.removeChild(textArea);
  };
  /**
   * Copy text to clipboard
   * @param {String} text
   * @param {Function=} onSuccess
   * @param {Function=} onError
   */
  this.copyTextToClipboard = (text, onSuccess, onError) => {
    if (!navigator.clipboard) {
      fallbackCopyTextToClipboard(text, onSuccess, onError);
      return;
    }
    navigator.clipboard.writeText(text).then(function() {
      if (typeof onSuccess === typeof Function) {
        onSuccess.apply(null, [text]);
      }
    }, function(err) {
      if (typeof onError === typeof Function) {
        onError.apply(null, [text, 'Async: Could not copy text', err]);
      }
    });
  };
  /**
   *
   * @param {any} value
   * @param {Array} array
   * @return {Array}
   */
  this.arrayRemove = (value, array) => {
    const index = array.indexOf(value);
    if (index !== -1) {
      array.splice(index, 1);
    }
    return array;
  };
}

window.etsHelper = new EtsHelpers();

/* Built-in prototypes */
if (!etsHelper.isFn(String.prototype.stripHtmlTags)) {
  String.prototype.stripHtmlTags = function(tags) {
    return etsHelper.stripHtmlTags(this, tags);
  };
}
if (!etsHelper.isFn(String.prototype.hasHtmlTag)) {
  String.prototype.hasHtmlTag = function(tag) {
    return etsHelper.hasHtmlTag(this, tag);
  };
}
if (!etsHelper.isFn(Array.prototype.etsRemove)) {
  Array.prototype.etsRemove = function(value) {
    return etsHelper.arrayRemove(value, this);
  };
}
