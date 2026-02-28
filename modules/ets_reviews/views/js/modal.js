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

+function ($) {
  'use strict';

  // MODAL CLASS DEFINITION
  // ======================

  var ETSModal = function (element, options) {
    this.options        = options
    this.$body          = $(document.body)
    this.$element       = $(element)
    this.$backdrop      =
        this.isShown        = null
    this.scrollbarWidth = 0

    if (this.options.remote) {
      this.$element
          .find('.ets-rv-modal-content')
          .load(this.options.remote, $.proxy(function () {
            this.$element.trigger('loaded.bs.ets-rv-modal')
          }, this))
    }
  }

  ETSModal.DEFAULTS = {
    backdrop: true,
    keyboard: true,
    show: true
  }

  ETSModal.prototype.toggle = function (_relatedTarget) {
    return this.isShown ? this.hide() : this.show(_relatedTarget)
  }

  ETSModal.prototype.show = function (_relatedTarget) {
    var that = this
    var e    = $.Event('show.bs.ets-rv-modal', { relatedTarget: _relatedTarget })

    this.$element.trigger(e)

    if (this.isShown || e.isDefaultPrevented()) return

    this.isShown = true

    this.checkScrollbar()
    this.$body.addClass('ets-rv-modal-open')

    this.setScrollbar()
    this.escape()

    this.$element.on('click.dismiss.bs.ets-rv-modal', '[data-dismiss="ets-rv-modal"]', $.proxy(this.hide, this))

    this.backdrop(function () {
      var transition = $.support.transition && that.$element.hasClass('fade')

      if (!that.$element.parent().length) {
        that.$element.appendTo(that.$body) // don't move modals dom position
      }

      that.$element
          .show()
          .scrollTop(0)

      if (transition) {
        that.$element[0].offsetWidth // force reflow
      }

      that.$element
          .addClass('in')
          .attr('aria-hidden', false)

      that.enforceFocus()

      var e = $.Event('shown.bs.ets-rv-modal', { relatedTarget: _relatedTarget })

      transition ?
          that.$element.find('.ets-rv-modal-dialog') // wait for modal to slide in
              .one($.support.transition.end, function () {
                that.$element.trigger('focus').trigger(e)
              })
              .emulateTransitionEnd(300) :
          that.$element.trigger('focus').trigger(e)
    })
  }

  ETSModal.prototype.hide = function (e) {
    if (e) e.preventDefault()

    e = $.Event('hide.bs.ets-rv-modal')

    this.$element.trigger(e)

    if (!this.isShown || e.isDefaultPrevented()) return

    this.isShown = false

    this.$body.removeClass('ets-rv-modal-open')

    this.resetScrollbar()
    this.escape()

    $(document).off('focusin.bs.ets-rv-modal')

    this.$element
        .removeClass('in')
        .attr('aria-hidden', true)
        .off('click.dismiss.bs.ets-rv-modal')

    $.support.transition && this.$element.hasClass('fade') ?
        this.$element
            .one($.support.transition.end, $.proxy(this.hideModal, this))
            .emulateTransitionEnd(300) :
        this.hideModal()
  }

  ETSModal.prototype.enforceFocus = function () {
    $(document)
        .off('focusin.bs.ets-rv-modal') // guard against infinite focus loop
        .on('focusin.bs.ets-rv-modal', $.proxy(function (e) {
          if (this.$element[0] !== e.target && !this.$element.has(e.target).length) {
            this.$element.trigger('focus')
          }
        }, this))
  }

  ETSModal.prototype.escape = function () {
    if (this.isShown && this.options.keyboard) {
      this.$element.on('keyup.dismiss.bs.ets-rv-modal', $.proxy(function (e) {
        e.which == 27 && this.hide()
      }, this))
    } else if (!this.isShown) {
      this.$element.off('keyup.dismiss.bs.ets-rv-modal')
    }
  }

  ETSModal.prototype.hideModal = function () {
    var that = this
    this.$element.hide()
    this.backdrop(function () {
      that.$element.trigger('hidden.bs.ets-rv-modal')
    })
  }

  ETSModal.prototype.removeBackdrop = function () {
    this.$backdrop && this.$backdrop.remove()
    this.$backdrop = null
  }

  ETSModal.prototype.backdrop = function (callback) {
    var that = this
    var animate = this.$element.hasClass('fade') ? 'fade' : ''

    if (this.isShown && this.options.backdrop) {
      var doAnimate = $.support.transition && animate

      this.$backdrop = $('<div class="ets-rv-modal-backdrop ' + animate + '" />')
          .appendTo(this.$body)

      this.$element.on('click.dismiss.bs.ets-rv-modal', $.proxy(function (e) {
        if (e.target !== e.currentTarget) return
        this.options.backdrop == 'static'
            ? this.$element[0].focus.call(this.$element[0])
            : this.hide.call(this)
      }, this))

      if (doAnimate) this.$backdrop[0].offsetWidth // force reflow

      this.$backdrop.addClass('in')

      if (!callback) return

      doAnimate ?
          this.$backdrop
              .one($.support.transition.end, callback)
              .emulateTransitionEnd(150) :
          callback()

    } else if (!this.isShown && this.$backdrop) {
      this.$backdrop.removeClass('in')

      var callbackRemove = function() {
        that.removeBackdrop()
        callback && callback()
      }
      $.support.transition && this.$element.hasClass('fade') ?
          this.$backdrop
              .one($.support.transition.end, callbackRemove)
              .emulateTransitionEnd(150) :
          callbackRemove()

    } else if (callback) {
      callback()
    }
  }

  ETSModal.prototype.checkScrollbar = function () {
    if (document.body.clientWidth >= window.innerWidth) return
    this.scrollbarWidth = this.scrollbarWidth || this.measureScrollbar()
  }

  ETSModal.prototype.setScrollbar =  function () {
    var bodyPad = parseInt(this.$body.css('padding-right') || 0)
    if (this.scrollbarWidth) this.$body.css('padding-right', bodyPad + this.scrollbarWidth)
  }

  ETSModal.prototype.resetScrollbar = function () {
    this.$body.css('padding-right', '')
  }

  ETSModal.prototype.measureScrollbar = function () { // thx walsh
    var scrollDiv = document.createElement('div')
    scrollDiv.className = 'ets-rv-modal-scrollbar-measure'
    this.$body.append(scrollDiv)
    var scrollbarWidth = scrollDiv.offsetWidth - scrollDiv.clientWidth
    this.$body[0].removeChild(scrollDiv)
    return scrollbarWidth
  }


  // MODAL PLUGIN DEFINITION
  // =======================

  var old = $.fn.ETSModal

  $.fn.ETSModal = function (option, _relatedTarget) {
    return this.each(function () {
      var $this   = $(this)
      var data    = $this.data('bs.ets-rv-modal')
      var options = $.extend({}, ETSModal.DEFAULTS, $this.data(), typeof option == 'object' && option)

      if (!data) $this.data('bs.ets-rv-modal', (data = new ETSModal(this, options)))
      if (typeof option == 'string') data[option](_relatedTarget)
      else if (options.show) data.show(_relatedTarget)
    })
  }

  $.fn.ETSModal.Constructor = ETSModal


  // MODAL NO CONFLICT
  // =================

  $.fn.ETSModal.noConflict = function () {
    $.fn.ETSModal = old
    return this
  }


  // MODAL DATA-API
  // ==============

  $(document).on('click.bs.ets-rv-modal.data-api', '[data-toggle="ets-rv-modal"]', function (e) {
    var $this   = $(this)
    var href    = $this.attr('href')
    var $target = $($this.attr('data-target') || (href && href.replace(/.*(?=#[^\s]+$)/, ''))) //strip for ie7
    var option  = $target.data('bs.ets-rv-modal') ? 'toggle' : $.extend({ remote: !/#/.test(href) && href }, $target.data(), $this.data())

    if ($this.is('a')) e.preventDefault()

    $target
        .ETSModal(option, this)
        .one('hide', function () {
          $this.is(':visible') && $this.trigger('focus')
        })
  })

}(jQuery);
