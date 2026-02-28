/*
*
*  @author AN Eshop Group
*  @copyright  AN Eshop Group
*  @version  Release: $Revision$
*  @license    Private
*/

window.addEventListener("DOMContentLoaded", (event) => {
    $('.Rating__Item__Slider').slick({
        arrows:false,
        dots:true,
        adaptiveHeight: true,
        slidesToShow:1,
        slidesToScroll:1
    });
});
