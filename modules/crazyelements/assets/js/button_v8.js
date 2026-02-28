
document.addEventListener("DOMContentLoaded", function (event) {
  //do work

  if (IS_CUSTOM == 'false') {

    $('textarea[name^=template_content]').each(function (i, tag) {
      if (DONT_EDIT == 'true') {
        var id = tag.id.split('_').pop();

        var content_html = "<h2 style='margin:0px;'>" + DONT_EDIT_MESSAGE + "</h2>";

        var btnObj = $(content_html).insertBefore(tag);

        if (ALLOW_PRESTA_EDITOR == 'no') {
          $(tag).hide();
        }
      } else {
        var id = tag.id.split('_').pop();


        var button_html = $('#edit_with_button').html();

        var btnObj = $(button_html).insertBefore(tag);

        var href = btnObj.attr('href');

        var modifiedHref = href.replace('&id_lang=', '');
        btnObj.attr('href', modifiedHref);

        if (ALLOW_PRESTA_EDITOR == 'no') {
          $(tag).hide();
        }
      }

    });

    $('textarea[name^=resource_]').each(function (i, tag) {
      if (DONT_EDIT == 'true') {
        var id = tag.id.split('_').pop();
        var content_html = "<h2 style='margin:0px;'>" + DONT_EDIT_MESSAGE + "</h2>";
        var btnObj = $(content_html).insertBefore(tag);
        $(tag).hide();
      } else {
        var id = tag.id.split('_').pop();
        var button_html = $('#edit_with_button').html();
        var btnObj = $(button_html).insertBefore(tag);
        var href = btnObj.attr('href');
        var modifiedHref = href.replace('&id_lang=', '&id_lang=' + id);
        btnObj.attr('href', modifiedHref);
        $(tag).hide();
      }
    });
    //------------------------------------------------==============================------------------------------------------//



    // if(_PS_VERSION_ >= '1.7.6.0'){

    $('textarea[id^=cms_page_content_]').each(function (i, tag) {


      if (DONT_EDIT == 'true') {
        var id = tag.id.split('_').pop();

        var content_html = "<h2 style='margin:0px;'>" + DONT_EDIT_MESSAGE + "</h2>";

        var btnObj = $(content_html).insertBefore(tag);


        if (ALLOW_PRESTA_EDITOR == 'no') {
          $(tag).hide();
        }
      } else {

        var id = tag.id.split('_').pop();


        var button_html = $('#edit_with_button').html();

        var btnObj = $(button_html).insertBefore(tag);

        var href = btnObj.attr('href');

        var modifiedHref = href.replace('&id_lang=', '&id_lang=' + id);
        btnObj.attr('href', modifiedHref);

        if (ALLOW_PRESTA_EDITOR == 'no') {
          $(tag).hide();
        }

      }

    });


    // category

    $('textarea[id^=category_description_]').each(function (i, tag) {

      if (DONT_EDIT == 'true') {
        var id = tag.id.split('_').pop();

        var content_html = "<h2 style='margin:0px;'>" + DONT_EDIT_MESSAGE + "</h2>";

        var btnObj = $(content_html).insertBefore(tag);

        if (ALLOW_PRESTA_EDITOR == 'no') {
          $(tag).hide();
        }
      } else {
        var id = tag.id.split('_').pop();


        var button_html = $('#edit_with_button').html();

        var btnObj = $(button_html).insertBefore(tag);

        var href = btnObj.attr('href');

        var modifiedHref = href.replace('&id_lang=', '&id_lang=' + id);
        btnObj.attr('href', modifiedHref);

        if (ALLOW_PRESTA_EDITOR == 'no') {
          $(tag).hide();
        }
      }


    });


    //product
    $('#product_description_description textarea[id^=product_description_description_]').each(function (i, tag) {

      if (DONT_EDIT == 'true') {
        var id = tag.id.split('_').pop();

        var content_html = "<h2 style='margin:0px;'>" + DONT_EDIT_MESSAGE + "</h2>";

        var btnObj = $(content_html).insertBefore(tag);

        if (ALLOW_PRESTA_EDITOR == 'no') {
          $(tag).hide();
        }
      } else {
        var id = tag.id.split('_').pop();


        var button_html = $('#edit_with_button').html();

        var btnObj = $(button_html).insertBefore(tag);

        var href = btnObj.attr('href');

        var modifiedHref = href.replace('&id_lang=', '&id_lang=' + id);
        btnObj.attr('href', modifiedHref);

        if (ALLOW_PRESTA_EDITOR == 'no') {
          $(tag).hide();
        }
      }
    });

    //supplier
    $('textarea[id^=description_]').each(function (i, tag) {

      if (DONT_EDIT == 'true') {
        var id = tag.id.split('_').pop();

        var content_html = "<h2 style='margin:0px;'>" + DONT_EDIT_MESSAGE + "</h2>";

        var btnObj = $(content_html).insertBefore(tag);

        if (ALLOW_PRESTA_EDITOR == 'no') {
          $(tag).hide();
        }
      } else {
        var id = tag.id.split('_').pop();


        var button_html = $('#edit_with_button').html();

        var btnObj = $(button_html).insertBefore(tag);

        var href = btnObj.attr('href');

        var modifiedHref = href.replace('&id_lang=', '&id_lang=' + id);
        btnObj.attr('href', modifiedHref);

        if (ALLOW_PRESTA_EDITOR == 'no') {
          $(tag).hide();
        }
      }


    });

    $('textarea[id^=supplier_description_]').each(function (i, tag) {

      if (DONT_EDIT == 'true') {
        var id = tag.id.split('_').pop();

        var content_html = "<h2 style='margin:0px;'>" + DONT_EDIT_MESSAGE + "</h2>";

        var btnObj = $(content_html).insertBefore(tag);

        if (ALLOW_PRESTA_EDITOR == 'no') {
          $(tag).hide();
        }
      } else {
        var id = tag.id.split('_').pop();


        var button_html = $('#edit_with_button').html();

        var btnObj = $(button_html).insertBefore(tag);

        var href = btnObj.attr('href');

        var modifiedHref = href.replace('&id_lang=', '&id_lang=' + id);
        btnObj.attr('href', modifiedHref);

        if (ALLOW_PRESTA_EDITOR == 'no') {
          $(tag).hide();
        }
      }


    });

    // manu
    $('textarea[id^=manufacturer_description_1]').each(function (i, tag) {

      if (DONT_EDIT == 'true') {
        var id = tag.id.split('_').pop();

        var content_html = "<h2 style='margin:0px;'>" + DONT_EDIT_MESSAGE + "</h2>";

        var btnObj = $(content_html).insertBefore(tag);

        if (ALLOW_PRESTA_EDITOR == 'no') {
          $(tag).hide();
        }
      } else {
        var id = tag.id.split('_').pop();


        var button_html = $('#edit_with_button').html();

        var btnObj = $(button_html).insertBefore(tag);

        var href = btnObj.attr('href');

        var modifiedHref = href.replace('&id_lang=', '&id_lang=' + id);
        btnObj.attr('href', modifiedHref);

        if (ALLOW_PRESTA_EDITOR == 'no') {
          $(tag).hide();
        }

      }

    });




    // }else{

    // usually cms
    $('textarea[name^=content_]').each(function (i, tag) {
      if (DONT_EDIT == 'true') {
        var id = tag.id.split('_').pop();

        var content_html = "<h2 style='margin:0px;'>" + DONT_EDIT_MESSAGE + "</h2>";

        var btnObj = $(content_html).insertBefore(tag);

        if (ALLOW_PRESTA_EDITOR == 'no') {
          $(tag).hide();
        }
      } else {
        var id = tag.id.split('_').pop();


        var button_html = $('#edit_with_button').html();

        var btnObj = $(button_html).insertBefore(tag);

        var href = btnObj.attr('href');

        var modifiedHref = href.replace('&id_lang=', '&id_lang=' + id);
        btnObj.attr('href', modifiedHref);

        if (ALLOW_PRESTA_EDITOR == 'no') {
          $(tag).hide();
        }
      }

    });
    // }
  } else {

    $('textarea[name^=' + FIELD_NAME + '_]').each(function (i, tag) {

      if (DONT_EDIT == 'true') {
        var id = tag.id.split('_').pop();

        var content_html = "<h2 style='margin:0px;'>" + DONT_EDIT_MESSAGE + "</h2>";

        var btnObj = $(content_html).insertBefore(tag);

      } else {
        var id = tag.name.split('_').pop();

        var button_html = $('#edit_with_button').html();

        var btnObj = $(button_html).insertBefore(tag);

        var href = btnObj.attr('href');

        var modifiedHref = href.replace('&id_lang=', '&id_lang=' + id);
        btnObj.attr('href', modifiedHref);
      }

    });

  }


});