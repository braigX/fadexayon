$(document).ready(function () {
    //if ($('.admincategories').length) {

        mycolor = 'red';
        if (parseInt(ec_seo_total_score) >= 75) {
            mycolor = 'green';
        } else if (parseInt(ec_seo_total_score) >= 50) {
            mycolor = 'yellow';
        } else if (parseInt(ec_seo_total_score) >= 25) {
            mycolor = 'orange';
        }

        html = "<a href='"+ec_seo_href+"' class='seo-grp "+mycolor+"'><div class='seo-inner'>SEO<span class='score-btn'>"+ec_seo_total_score+"<span class='score-btn-percent'>%</span></div></span><div class='pulsation'></div></a>";
        //if ((window.location.href).indexOf("updatecategory") > -1) {
            $(html).appendTo($('#main'));
        //} else if ((window.location.href).indexOf("edit") > -1) {
            $(html).appendTo($('#main-div'));
       // } 
   // }
});