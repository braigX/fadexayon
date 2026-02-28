/* Roy LeviBox */
$(function () {
    // Appearance
    $(function (f) {
        var element = f('.box-arrow');
        f(window).scroll(function () {
            var sctop = f(this).scrollTop() > 220;
            if (sctop) {
                $('.roy_levibox').addClass('arrow-show');
            } else {
                $('.roy_levibox').removeClass('arrow-show');
            }
        });
    });

    setTimeout(function () { $(window).scroll(); }, 1000);

    // Up Arrow
    $(".box-arrow").on('click', function () {
        $("html, body").animate({
            scrollTop: 0
        }, {
            duration: 500
        });
        return false;
    });
    $base_dir = 2;

});
/* /Roy LeviBox */
