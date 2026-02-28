$(document).ready(function () {
    if ($('#category_grid').length > 0)  {
        $( "#category_grid table tbody tr td" ).each(function() {
            html = $(this).html();
             if(html.indexOf('ec_divscoreprod') != -1){
                html =  html.replace(/&lt;/g, '<');
                html =  html.replace(/&gt;/g, '>');
                html =  html.replace(/&amp;/g, '&');
                $(this).html(html);
            } 
        });
    }
    if ($('#product_grid').length > 0)  {
        $( "#product_grid table tbody tr td" ).each(function() {
            html = $(this).html();
             if(html.indexOf('ec_divscoreprod') != -1){
                html =  html.replace(/&lt;/g, '<');
                html =  html.replace(/&gt;/g, '>');
                html =  html.replace(/&amp;/g, '&');
                $(this).html(html);
            } 
        });
    }
});