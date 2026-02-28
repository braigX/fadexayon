/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Presta.Site
 * @copyright 2019 Presta.Site
 * @license   LICENSE.txt
 */
$(window).on('load', function(){
    pswp_initMasonry();

    $(window).resize(function(){
        $('.pswp-carousel').each(function() {
            try {
                $(this)[0].slick.refresh();
            } catch(e) {
                console.log(e);
            }
        });
    });

    pswp_initCarousel();
});

$(function() {
    // Load posts by ajax
    $('.pswp-ajax-load').each(function() {
        const type = $(this).data('type');
        let id_product = pswp_getIdProduct();
        let id_block = $(this).data('id-block');
        const $this = $(this);
        $this.addClass('pswp-loading');

        if (type) {
            $.ajax({
                url: pswp_ajax_url,
                data: {ajax: true, action: 'getPosts', type: type, id_product: id_product, id_block: id_block},
                method: 'get',
                success: function (result) {
                    $this.html(result);
                    if ($this.hasClass('pswp_masonry')) {
                        pswp_initMasonry($this);
                    }
                    if ($this.hasClass('pswp-carousel')) {
                        pswp_initCarousel($this);
                    }
                },
                complete: function () {
                    $this.removeClass('pswp-loading');
                }
            });
        }
    });

    // Comments:
    let $comments_wrp = $('#pswp-comments');
    // load comment form
    $comments_wrp.on('click', '.pswp-write-comment-btn', function (e) {
        e.preventDefault();

        let id_parent = $(this).data('comment-id');
        let reply_to = $(this).data('reply-to');
        let $form_wrp = $(this).closest('.comment-reply').find('.pswp-comment-form-wrp');
        let url = $(this).attr('href');

        $(this).hide();
        $form_wrp.addClass('pswp-loading');

        $.ajax({
            url: url,
            data: {ajax: true, action: 'getCommentForm', id_parent: id_parent, reply_to: reply_to, token: pswp_token},
            method: 'post',
            success: function(result) {
                $form_wrp.html(result);
                $form_wrp.find('#pswp-comment').focus();
                // psgdpr module compatibility:
                window.document.dispatchEvent(new Event("DOMContentLoaded", {
                    bubbles: true,
                    cancelable: true
                }));
            },
            complete: function() {
                $form_wrp.removeClass('pswp-loading');
            }
        });
    });

    // submit comment form
    $comments_wrp.on('submit', '.pswp-comment-form', function (e) {
        e.preventDefault();

        var url = $(this).attr('href');
        var $form = $(this);
        var $footer = $form.find('.form-footer');
        $footer.addClass('pswp-btn-loading');
        $form.find('.pswp-comment-alert').html('').hide();
        $form.find('.pswp-btn-submit').prop('disabled', true);

        $.ajax({
            url: url,
            data: $form.serialize(),
            method: 'post',
            dataType: 'json',
            success: function(result) {
                if (result.html) {
                    $form.closest('.pswp-comment-form-wrp').html(result.html);
                    // psgdpr module compatibility:
                    window.document.dispatchEvent(new Event("DOMContentLoaded", {
                        bubbles: true,
                        cancelable: true
                    }));
                }
                if (result.reload) {
                    setTimeout(function () {
                        pswp_reloadCommentList();
                    }, 1500);
                }
            },
            error: function(xhr) {
                $form.siblings('.alert').remove();
                $form.before('<div class="alert alert-danger">Unknown error</div>');
            },
            complete: function() {
                $footer.removeClass('pswp-btn-loading');
            }
        });
    });

    // cancel and close comment form
    $comments_wrp.on('click', '.pswp-btn-cancel-comment', function (e) {
        e.preventDefault();

        var $wrp = $(this).closest('.comment-reply');
        $wrp.find('.comment-reply-link').show();
        $wrp.find('.pswp-comment-form-wrp').html('');
    });
});

function pswp_reloadCommentList() {
    $.ajax({
        url: window.location.href,
        data: {ajax: 1, action: 'getCommentList', token: pswp_token},
        method: 'post',
        success: function(result) {
            $('#pswp-comment-list').html(result);
        }
    });
}

function pswp_slickAdaptiveHeight($carousel, slick) {
    if (pswp_theme) {
        return false;
    }

    if ($carousel.hasClass('posts_container-fi')) {
        slick.$slider.find('.pswp-post-image-link').css('height', '');

        var min_height = 10000;
        slick.$slides.each(function () {
            var $img = $(this).find('.pswp-post-image:first');
            if ($img.length) {
                var img_height = $img.height();
                if (img_height < min_height && img_height > 0) {
                    min_height = img_height;
                }
            } else {
                var img_height = $(this).find('.pswp-post-wrp-wrp').height();
                if (img_height < min_height && img_height > 0) {
                    min_height = img_height;
                }
            }
        });
        // if any image found
        if (min_height !== 10000) {
            slick.$slideTrack.css('height', min_height + 'px');
            slick.$slider.find('.pswp-post-image-link').css('height', min_height + 'px');
        }
    } else {
        var max_height = 0;

        $carousel.find('.slick-active').each(function () {
            var height = $(this).find('.pswp-post-wrp-wrp').height();
            if (height > max_height) {
                max_height = height;
            }
        });

        if (max_height) {
            $carousel.find('.slick-slide').animate({'max-height': max_height}, 200);
        }
    }
}

function pswp_getIdProduct() {
    const $body = $('body#product');
    if ($body.length) {
        // check if id_product is already set
        if (typeof window.id_product !== 'undefined' && window.id_product) {
            return window.id_product;
        }

        // otherwise get it from the body class
        const classes = $body.attr('class');
        const regex = /product-id-(\d+)/;
        let match = classes.match(regex);
        let id_product = match[1];
        if (id_product && !isNaN(id_product)) {
            return id_product;
        }

        // otherwise get it from the form
        const $input = $('#product_page_product_id');
        if ($input.length) {
            id_product = $input.val();
            if (id_product && !isNaN(id_product)) {
                return id_product;
            }
        }
    }

    return 0;
}

function pswp_initCarousel($carousels) {
    if (typeof $carousels === 'undefined') {
        $carousels = $('.pswp-carousel').not('.pswp-ajax-load');
    }

    // for compatibility with lazyload:
    if ($carousels.length) {
        var img = document.querySelector(".pswp-post-image"),
            observer = new MutationObserver((changes) => {
                // try to detect src change and resize the slider
                changes.forEach(change => {
                    if(change.attributeName.includes('src')){
                        let $carousel = $(img).closest('.pswp-carousel');
                        if ($carousel.length && $carousel[0].slick) {
                            pswp_slickAdaptiveHeight($carousel, $carousel[0].slick);
                        }
                    }
                });
            });
        observer.observe(img, {attributes : true});
    }

    $carousels.each(function() {
        var $carousel = $(this);
        $(this).on('init afterChange', function (event, slick) {
            pswp_slickAdaptiveHeight($carousel, slick);
        });

        var cols = $(this).data('cols');
        var autoplay = !!parseInt($(this).data('autoplay'));
        var dots = !!parseInt($(this).data('dots'));
        var arrows = !!parseInt($(this).data('arrows'));
        $(this).pswp_slick({
            cssEase: 'cubic-bezier(0.420, 0.000, 0.580, 1.000)',
            slidesToShow: cols,
            slidesToScroll: cols,
            infinite: false,
            dots: dots,
            arrows: arrows,
            autoplay: autoplay,
            autoplaySpeed: 4000,
            responsive: [
                {
                    breakpoint: 991,
                    settings: {
                        slidesToShow: Math.max(1, cols - 1),
                        slidesToScroll: Math.max(1, cols - 1)
                    }
                },
                {
                    breakpoint: 767,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1
                    }
                }
            ]
        });
    });
}

function pswp_initMasonry($elements) {
    // Run masonry after images are loaded
    if ($.masonry || $.fn.masonry) {
        if (typeof $elements === 'undefined') {
            $elements = $('.pswp_grid.pswp_masonry').not('.pswp-ajax-load');
        }
        $elements.masonry({
            // options
            itemSelector: '.pswp-post'
        });
    }
}

function pswp_toggleSearch(element) {
    let $wrp = $(element).closest('.pswp-search-wrp');
    let $search_input = $wrp.find('.pswp-search-input');

    if ($search_input.is(':visible')) {
        $wrp.removeClass('pswp-search-visible');
        // $search_input.slideUp(100);
    } else {
        $wrp.addClass('pswp-search-visible');
        $search_input.focus();
    }
}