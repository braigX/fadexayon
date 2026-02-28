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

jQuery.fn.etsRating = function (generalOptions) {
    const $ratings = $(this);

    $ratings.each(function initRating() {
        const $ratingComponent = $(this);
        var options = generalOptions ? generalOptions : {};
        if (typeof options.grade === "undefined" && $ratingComponent.data('grade')) {
            options.grade = $ratingComponent.data('grade');
        }
        if (!options.min && $ratingComponent.data('min')) {
            options.min = $ratingComponent.data('min');
        }
        if (!options.max && $ratingComponent.data('max')) {
            options.max = $ratingComponent.data('max');
        }
        if (!options.input && $ratingComponent.data('input')) {
            options.input = $ratingComponent.data('input');
        }
        var componentOptions = jQuery.extend({
            grade: null,
            input: null,
            min: 0,
            max: 5,
            starWidth: 20
        }, options);

        const minValue = Math.min(componentOptions.min, componentOptions.max);
        const maxValue = Math.max(componentOptions.min, componentOptions.max);
        const ratingValue = Math.min(Math.max(minValue, componentOptions.grade), maxValue);

        $ratingComponent.html('');
        $ratingComponent.append('<div class="ets-rv-star-content ets-rv-star-empty clearfix"></div>');
        $ratingComponent.append('<div class="ets-rv-star-content ets-rv-star-full clearfix"></div>');

        const emptyStars = $('.ets-rv-star-empty', this);
        const fullStars = $('.ets-rv-star-full', this);
        const emptyStar = $('<div class="star color1"><svg class="star_empty" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1201 1004l306-297-422-62-189-382-189 382-422 62 306 297-73 421 378-199 377 199zm527-357q0 22-26 48l-363 354 86 500q1 7 1 20 0 50-41 50-19 0-40-12l-449-236-449 236q-22 12-40 12-21 0-31.5-14.5t-10.5-35.5q0-6 2-20l86-500-364-354q-25-27-25-48 0-37 56-46l502-73 225-455q19-41 49-41t49 41l225 455 502 73q56 9 56 46z"/></svg><svg class="star_full" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1728 647q0 22-26 48l-363 354 86 500q1 7 1 20 0 21-10.5 35.5t-30.5 14.5q-19 0-40-12l-449-236-449 236q-22 12-40 12-21 0-31.5-14.5t-10.5-35.5q0-6 2-20l86-500-364-354q-25-27-25-48 0-37 56-46l502-73 225-455q19-41 49-41t49 41l225 455 502 73q56 9 56 46z"/></svg></div>');
        const fullStar = $('<div class="ets-rv-star-on"><svg class="star_empty" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1201 1004l306-297-422-62-189-382-189 382-422 62 306 297-73 421 378-199 377 199zm527-357q0 22-26 48l-363 354 86 500q1 7 1 20 0 50-41 50-19 0-40-12l-449-236-449 236q-22 12-40 12-21 0-31.5-14.5t-10.5-35.5q0-6 2-20l86-500-364-354q-25-27-25-48 0-37 56-46l502-73 225-455q19-41 49-41t49 41l225 455 502 73q56 9 56 46z"/></svg><svg class="star_full" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1728 647q0 22-26 48l-363 354 86 500q1 7 1 20 0 21-10.5 35.5t-30.5 14.5q-19 0-40-12l-449-236-449 236q-22 12-40 12-21 0-31.5-14.5t-10.5-35.5q0-6 2-20l86-500-364-354q-25-27-25-48 0-37 56-46l502-73 225-455q19-41 49-41t49 41l225 455 502 73q56 9 56 46z"/></svg></div>');

        var ratingInput;
        if (componentOptions.input) {
            ratingInput = $('<input type="number" name="' + componentOptions.input + '" id="' + componentOptions.input + '" />');
            ratingInput.val(ratingValue);
            ratingInput.css('display', 'none');
            ratingInput.change(displayInteractiveGrade);
            $ratingComponent.append(ratingInput);
            initInteractiveGrade();
        } else {
            displayGrade(ratingValue);
        }

        function initInteractiveGrade() {
            emptyStars.html('');
            fullStars.html('');
            var newStar;
            for (var i = minValue; i < maxValue; ++i) {
                newStar = emptyStar.clone();
                newStar.data('grade', i + 1);
                newStar.hover(function overStar() {
                    var overIndex = $('.star', fullStars).index($(this));
                    $('.star', fullStars).each(function overStars() {
                        $(this).removeClass('ets-rv-star-on');
                        var starIndex = $('.star', fullStars).index($(this));
                        if (starIndex <= overIndex) {
                            $(this).addClass('star-hover');
                        } else {
                            $(this).removeClass('star-hover');
                        }
                    });
                    ratingInput.val(overIndex+1);
                });
                newStar.click(function selectGrade() {
                    var selectedGrade = $(this).data('grade');
                    ratingInput.val(selectedGrade);
                });
                fullStars.append(newStar);
            }

            fullStars.hover(function () {
            }, displayInteractiveGrade);
            displayInteractiveGrade();
        }

        function displayInteractiveGrade() {
            $('.star', fullStars).each(function displayStar() {
                var starValue = $(this).data('grade');
                $(this).removeClass('star-hover');
                if (starValue <= ratingInput.val()) {
                    $(this).addClass('ets-rv-star-on');
                } else {
                    $(this).removeClass('ets-rv-star-on');
                }
            });
        }

        function displayGrade(grade) {
            emptyStars.html('');
            fullStars.html('');
            var newStar;
            for (var i = minValue; i <= maxValue; ++i) {
                if (i <= Math.floor(grade)) {
                    newStar = emptyStar.clone();
                    newStar.css('visibility', 'hidden');
                    emptyStars.append(newStar);
                    fullStars.append(fullStar.clone());
                } else if (i > Math.ceil(grade)) {
                    newStar = emptyStar.clone();
                    emptyStars.append(newStar.clone());
                } else {
                    //This the partial star composed of
                    // - one invisible partial empty star
                    // - one visible partial empty star (remaining part)
                    // - one visible partial full star
                    var fullWidth = (grade - i + 1) * componentOptions.starWidth;
                    var emptyWidth = componentOptions.starWidth - fullWidth;
                    newStar = emptyStar.clone();
                    newStar.css('visibility', 'hidden');
                    newStar.css('width', fullWidth);
                    emptyStars.append(newStar);

                    newStar = emptyStar.clone();
                    newStar.css('width', emptyWidth);
                    newStar.css('background-position', '0px -' + fullWidth + 'px');
                    newStar.css('background-position', '-' + fullWidth + 'px 0px');
                    newStar.css('marginLeft', 0);
                    emptyStars.append(newStar);

                    fullStar.css('width', fullWidth);
                    fullStars.append(fullStar.clone());
                }
            }
        }
    });
}
