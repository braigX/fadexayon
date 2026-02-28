$(document).ready(function () {

    // INSIDE TABS navigation
    $('.nav_inside > li > a').click(function (event) {
        event.preventDefault();//stop browser to take action for clicked anchor

        //get displaying tab content jQuery selector
        var active_tab_selector = $('.nav_inside > li.active > a').attr('href');

        //find actived navigation and remove 'active' css
        var actived_nav = $('.nav_inside > li.active');
        actived_nav.removeClass('active');

        //add 'active' css into clicked navigation
        $(this).parents('li').addClass('active');
        localStorage.setItem('lastTabInside', $(this).attr('href')); // Inside local set here

        //hide displaying tab content
        $('.tab-content.active').removeClass('active').addClass('hide');

        //show target tab content
        var target_tab_selector = $(this).attr('href');
        $(target_tab_selector).removeClass('hide');
        $(target_tab_selector).addClass('active');
    });

    // for bootstrap 3 use 'shown.bs.tab', for bootstrap 2 use 'shown' in the next line
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        // save the latest tab; use cookies if you like 'em better:
        localStorage.setItem('lastTab', $(this).attr('href'));


        // show first inside navigation on click on main
        var first_li_link = $(this).attr('href');
        var first_li_div = $(first_li_link).find('.nav_inside > li:first-child');
        var first_li_target_content = $(first_li_link).find('.nav_inside > li:first-child > a').attr('href');

        $('.tab-content.active').removeClass('active').addClass('hide');
        $('.nav_inside > li.active').removeClass('active');

        $(first_li_div).addClass('active');
        $(first_li_target_content).removeClass('hide').addClass('active');

    });

    // go to the latest tab, if it exists:
    var lastTab = localStorage.getItem('lastTab');
    if (lastTab) {
        $('[href="' + lastTab + '"]').tab('show');
    }
    var lastTabInside = localStorage.getItem('lastTabInside');
    if (lastTabInside) {
        $('.tab-content').removeClass('active').addClass('hide');
        $('[href="' + lastTabInside + '"]').tab('show');
        $(lastTabInside).removeClass('hide');
    }

    // Demo select
    $('.demo_apply').click(function () {

        if ($('#select_demo1').is(':checked')) var demo_number = '1';
        if ($('#select_demo2').is(':checked')) var demo_number = '2';
        if ($('#select_demo3').is(':checked')) var demo_number = '3';
        if ($('#select_demo4').is(':checked')) var demo_number = '4';
        if ($('#select_demo5').is(':checked')) var demo_number = '5';
        if ($('#select_demo6').is(':checked')) var demo_number = '6';

        // Base colors massive
        var demo_settings = {
            'g_lay': ['1', '1', '2', '4', '1'],
            'g_tp': ['150', '150', '150', '80', '150'],
            'g_bp': ['150', '150', '150', '80', '150'],
            'body_box_sw': ['1', '1', '1', '2', '1'],
            'nc_body_gg': ['15', '15', '15', '125', '15'],
            'nc_body_im_bg_ext': ['', '', '', '', ''],
            'nc_body_im_bg_repeat': ['0', '0', '0', '0', '0'],
            'nc_body_im_bg_position': ['0', '0', '0', '0', '0'],
            'nc_body_im_bg_fixed': ['0', '0', '0', '0', '0'],
            'gradient_scheme': ['1', '1', '0', '0', '0'],
            'display_gradient': ['1', '1', '0', '0', '0'],
            'body_bg_pattern': ['0', '0', '0', '0', '0'],
            'nc_main_bg': ['1', '1', '1', '1', '1'],
            'nc_main_gg': ['15', '15', '15', '15', '15'],
            'nc_main_im_bg_ext': ['', '', '', '', ''],
            'nc_main_im_bg_repeat': ['0', '0', '0', '0', '0'],
            'nc_main_im_bg_position': ['0', '0', '0', '0', '0'],
            'nc_main_im_bg_fixed': ['0', '0', '0', '0', '0'],
            'header_lay': ['1', '1', '2', '2', '1'],
            'nc_logo_normal': ['png', 'png', 'png', 'png', 'png'],
            'nc_header_shadow': ['1', '1', '1', '1', '1'],
            'nc_header_bg': ['4', '2', '4', '4', '2'],
            'nc_header_gg': ['15', '110', '15', '15', '180'],
            'nc_header_im_bg_ext': ['', '', '', '', ''],
            'nc_header_im_bg_repeat': ['0', '0', '0', '0', '0'],
            'nc_header_im_bg_position': ['0', '0', '0', '0', '0'],
            'nc_header_im_bg_fixed': ['0', '0', '0', '0', '0'],
            'nc_m_align': ['1', '1', '2', '2', '1'],
            'nc_m_layout': ['1', '2', '2', '2', '1'],
            'nc_m_under': ['1', '0', '0', '0', '1'],
            'nc_m_override': ['2', '2', '2', '2', '2'],
            'nc_m_br': ['5px', '5px', '5px', '5px', '3px'],
            'search_lay': ['1', '1', '4', '4', '1'],
            'nc_i_search': ['search2', 'search2', 'search3', 'search2', 'search2'],
            'cart_lay': ['1', '2', '4', '4', '4'],
            'cart_icon': ['cart2', 'cart8', 'cart10', 'cart5', 'cart9'],
            'nc_b_radius': ['4', '4', '4', '4', '4'],
            'nc_b_sh': ['1', '1', '1', '0', '1'],
            'i_b_radius': ['4', '4', '4', '4', '4'],
            'nc_loader': ['1', '1', '1', '1', '1'],
            'nc_loader_lay': ['1', '1', '1', '1', '1'],
            'nc_loader_logo': ['2', '4', '3', '2', '2'],
            'nc_logo_loader': ['png', 'png', 'png', 'png', 'png'],
            'ban_spa_behead': ['1', '1', '1', '1', '1'],
            'ban_ts_behead': ['0', '0', '0', '0', '0'],
            'ban_bs_behead': ['0', '0', '0', '0', '0'],
            'ban_spa_top': ['1', '1', '1', '1', '1'],
            'ban_ts_top': ['0', '0', '0', '0', '0'],
            'ban_bs_top': ['0', '0', '0', '0', '0'],
            'ban_ts_left': ['0', '0', '0', '0', '0'],
            'ban_bs_left': ['0', '0', '0', '0', '0'],
            'ban_ts_right': ['0', '0', '0', '0', '0'],
            'ban_bs_right': ['0', '0', '0', '0', '0'],
            'ban_spa_pro': ['1', '1', '1', '0', '0'],
            'ban_ts_pro': ['30', '30', '30', '1', '30'],
            'ban_bs_pro': ['0', '0', '0', '0', '0'],
            'ban_spa_befoot': ['1', '1', '1', '1', '1'],
            'ban_ts_befoot': ['30', '30', '30', '30', '30'],
            'ban_bs_befoot': ['0', '0', '0', '0', '0'],
            'ban_spa_foot': ['1', '1', '1', '1', '1'],
            'ban_ts_foot': ['30', '30', '30', '30', '30'],
            'ban_bs_foot': ['0', '0', '0', '0', '0'],
            'ban_spa_sidecart': ['1', '1', '1', '1', '1'],
            'ban_ts_sidecart': ['0', '0', '0', '0', '0'],
            'ban_bs_sidecart': ['0', '0', '0', '0', '0'],
            'ban_spa_sidesearch': ['1', '1', '1', '1', '1'],
            'ban_ts_sidesearch': ['0', '0', '0', '0', '0'],
            'ban_bs_sidesearch': ['0', '0', '0', '0', '0'],
            'ban_spa_sidemail': ['1', '1', '1', '1', '1'],
            'ban_ts_sidemail': ['0', '0', '0', '0', '0'],
            'ban_bs_sidemail': ['0', '0', '0', '0', '0'],
            'ban_spa_sidemobilemenu': ['1', '1', '1', '1', '1'],
            'ban_ts_sidemobilemenu': ['0', '0', '0', '0', '0'],
            'ban_bs_sidemobilemenu': ['0', '0', '0', '0', '0'],
            'ban_spa_product': ['1', '1', '1', '1', '1'],
            'ban_ts_product': ['10', '10', '10', '10', '10'],
            'ban_bs_product': ['0', '0', '0', '0', '0'],
            'nc_carousel_featured': ['1', '1', '1', '1', '1'],
            'nc_auto_featured': ['true', 'true', 'false', 'false', 'true'],
            'nc_items_featured': ['3', '3', '4', '4', '5'],
            'nc_carousel_best': ['1', '1', '1', '2', '2'],
            'nc_auto_best': ['true', 'false', 'false', 'false', 'false'],
            'nc_items_best': ['3', '4', '5', '4', '5'],
            'nc_carousel_new': ['1', '1', '1', '1', '1'],
            'nc_auto_new': ['true', 'true', 'true', 'true', 'true'],
            'nc_items_new': ['3', '3', '3', '4', '5'],
            'nc_carousel_sale': ['1', '1', '1', '1', '1'],
            'nc_auto_sale': ['true', 'true', 'true', 'true', 'true'],
            'nc_items_sale': ['3', '3', '3', '4', '5'],
            'nc_carousel_custom1': ['1', '1', '1', '1', '1'],
            'nc_auto_custom1': ['true', 'true', 'true', 'true', 'true'],
            'nc_items_custom1': ['3', '3', '3', '4', '5'],
            'nc_carousel_custom2': ['1', '1', '1', '1', '1'],
            'nc_auto_custom2': ['true', 'true', 'true', 'true', 'true'],
            'nc_items_custom2': ['3', '3', '3', '4', '5'],
            'nc_carousel_custom3': ['1', '1', '1', '1', '1'],
            'nc_auto_custom3': ['true', 'true', 'true', 'true', 'true'],
            'nc_items_custom3': ['3', '3', '3', '4', '5'],
            'nc_carousel_custom4': ['1', '1', '1', '1', '1'],
            'nc_auto_custom4': ['true', 'true', 'true', 'true', 'true'],
            'nc_items_custom4': ['3', '3', '3', '4', '5'],
            'nc_carousel_custom5': ['1', '1', '1', '1', '1'],
            'nc_auto_custom5': ['true', 'true', 'true', 'true', 'true'],
            'nc_items_custom5': ['3', '3', '3', '3', '5'],
            'brand_per_row': ['6', '6', '6', '6', '6'],
            'b_layout': ['1', '3', '2', '2', '1'],
            'sidebar_title': ['1', '0', '0', '0', '0'],
            'sidebar_title_b': ['0', '0', '0', '0', '0'],
            'sidebar_title_br': ['4', '4', '4', '4', '4'],
            'sidebar_title_b1': ['0', '0', '0', '0', '0'],
            'sidebar_title_b2': ['0', '0', '0', '0', '0'],
            'sidebar_title_b3': ['2', '2', '2', '2', '2'],
            'sidebar_title_b4': ['0', '0', '0', '0', '0'],
            'sidebar_content_b': ['0', '1', '1', '1', '1'],
            'sidebar_content_b1': ['1', '2', '2', '2', '2'],
            'sidebar_content_b2': ['1', '2', '2', '2', '2'],
            'sidebar_content_b3': ['1', '2', '2', '2', '2'],
            'sidebar_content_b4': ['1', '2', '2', '2', '2'],
            'sidebar_content_br': ['4', '0', '5', '2', '2'],
            'nc_product_switch': ['3', '3', '3', '3', '4'],
            'nc_subcat': ['0', '0', '0', '0', '0'],
            'nc_cat': ['0', '0', '0', '0', '0'],
            'nc_pc_layout': ['1', '2', '1', '1', '2'],
            'nc_pl_shadow': ['1', '1', '1', '0', '1'],
            'nc_show_q': ['1', '1', '1', '0', '1'],
            'nc_show_s': ['1', '1', '1', '0', '1'],
            'nc_second_img': ['1', '1', '1', '0', '1'],
            'nc_colors': ['0', '0', '0', '0', '0'],
            'nc_count_days': ['0', '1', '0', '1', '1'],
            'nc_i_qv': ['search1', 'search2', 'search3', 'search2', 'search2'],
            'nc_i_discover': ['discover1', 'discover1', 'discover2', 'discover2', 'discover1'],
            'nc_ai': ['1', '1', '1', '1', '1'],
            'pp_imgb': ['0', '1', '0', '0', '1'],
            'nc_pp_qq3': ['3', '4', '3', '3', '4'],
            'pp_z': ['search1', 'round_plus', 'qv2', 'qv2', 'search1'],
            'nc_mobadots': ['1', '1', '1', '1', '1'],
            'nc_sticky_add': ['0', '0', '0', '0', '0'],
            'nc_att_radio': ['1', '1', '1', '1', '1'],
            'pp_display_q': ['1', '1', '1', '1', '1'],
            'pp_display_refer': ['1', '1', '1', '1', '1'],
            'pp_display_cond': ['0', '0', '0', '0', '0'],
            'pp_display_brand': ['1', '1', '1', '1', '1'],
            'o_add': ['1', '1', '1', '1', '1'],
            'bl_lay': ['1', '1', '2', '2', '1'],
            'bl_cont': ['2', '2', '3', '3', '2'],
            'bl_row': ['3', '3', '3', '2', '3'],
            'bl_c_row': ['2', '3', '2', '2', '2'],
            'footer_lay': ['1', '1', '3', '3', '1'],
            'nc_logo_footer': ['png', 'png', 'png', 'png', 'png'],
            'levi_position': ['right', 'right', 'right', 'right', 'right'],
            'nc_logo_mobile': ['png', 'png', 'png', 'png', 'png'],
            'nc_mob_hp': ['1', '1', '1', '2', '2'],
            'nc_mob_cat': ['1', '1', '1', '2', '2'],
            'nc_hemo': ['1', '1', '3', '3', '3'],
            'f_headings': ['Cuprum', 'Cuprum', 'Cuprum', 'Cuprum', 'Inter'],
            'f_buttons': ['Cuprum', 'Cuprum', 'Cuprum', 'Cuprum', 'Inter'],
            'f_text': ['Poppins', 'Poppins', 'Poppins', 'Poppins', 'Inter'],
            'f_price': ['Cuprum', 'Cuprum', 'Cuprum', 'Cuprum', 'Inter'],
            'f_pn': ['Poppins', 'Poppins', 'Poppins', 'Poppins', 'Inter'],
            'latin_ext': ['0', '0', '0', '0', '0'],
            'cyrillic': ['0', '0', '0', '0', '0'],
            'font_size_pp': ['36', '36', '36', '36', '24'],
            'font_size_body': ['16', '16', '16', '16', '16'],
            'font_size_head': ['24', '24', '28', '24', '20'],
            'font_size_buttons': ['20', '20', '20', '20', '14'],
            'font_size_price': ['24', '24', '24', '24', '16'],
            'font_size_prod': ['24', '24', '24', '24', '22'],
            'font_size_pn': ['16', '16', '16', '16', '15'],
            'nc_up_hp': ['2', '2', '2', '1', '1'],
            'nc_up_nc': ['1', '1', '1', '1', '1'],
            'nc_up_np': ['1', '1', '1', '1', '1'],
            'nc_up_f': ['2', '2', '2', '1', '1'],
            'nc_up_bp': ['1', '1', '1', '1', '1'],
            'nc_up_mi': ['2', '2', '2', '1', '1'],
            'nc_up_menu': ['2', '2', '2', '1', '1'],
            'nc_up_head': ['2', '2', '2', '1', '1'],
            'nc_up_but': ['2', '2', '2', '1', '1'],
            'nc_fw_menu': ['600', '600', '600', '400', '500'],
            'nc_fw_heading': ['600', '600', '600', '500', '500'],
            'nc_fw_but': ['600', '600', '600', '400', '600'],
            'nc_fw_pn': ['500', '500', '500', '400', '500'],
            'nc_fw_ct': ['500', '500', '500', '400', '500'],
            'nc_fw_price': ['600', '600', '600', '600', '600'],
            'nc_ital_pn': ['1', '1', '1', '1', '1'],
            'nc_italic_pp': ['1', '1', '1', '1', '1'],
            'nc_ls': ['0', '0', '0', '0', '0'],
            'nc_ls_h': ['0', '0', '0', '0', '0'],
            'nc_ls_m': ['0', '0', '0', '0', '0'],
            'nc_ls_p': ['0', '0', '0', '0', '0'],
            'nc_ls_t': ['0', '0', '0', '0', '0'],
            'nc_ls_b': ['0', '0', '0', '0', '0']
        }

        var keys = Object.keys(demo_settings);
        for (var i = 0; i < keys.length; i++) {
            var key = keys[i];
            var value = demo_settings[key][demo_number - 1];
            var name = $("[name='" + key + "']");

            $(name).each(function () {
                $(this).val(value)
            });
        }

        $('.demo_apply').html('<i></i>Demo settings changed, click save changes to save it');

    });


    // Color schemes
    $('.colors_apply').click(function () {

        if ($('#select_scheme1').is(':checked')) var scheme_number = '1';
        if ($('#select_scheme2').is(':checked')) var scheme_number = '2';
        if ($('#select_scheme3').is(':checked')) var scheme_number = '3';
        if ($('#select_scheme4').is(':checked')) var scheme_number = '4';
        if ($('#select_scheme5').is(':checked')) var scheme_number = '5';
        if ($('#select_scheme6').is(':checked')) var scheme_number = '6';

        // Base colors massive
        var scheme_colors = {
            'main_background_color': ['#e5e5e5', '#e5e5e5', '#e5e5e5', '#e5e5e5', '#ffffff'],
            'nc_body_gs': ['#389290', '#389290', '#389290', '#f2d3da', '#f2d3da'],
            'nc_body_ge': ['#8480df', '#8480df', '#8480df', '#cccde9', '#cccde9'],
            'nc_main_bc': ['#f2f2f2', '#ffffff', '#ffffff', '#ffffff', '#ffffff'],
            'nc_main_gs': ['#f8f8f8', '#f8f8f8', '#f8f8f8', '#f8f8f8', '#f8f8f8'],
            'nc_main_ge': ['#d6d6d6', '#d6d6d6', '#d6d6d6', '#d6d6d6', '#d6d6d6'],
            'nc_header_bc': ['#f2f2f2', '#f2f2f2', '#f2f2f2', '#f2f2f2', '#f2f2f2'],
            'nc_header_gs': ['#f8f8f8', '#20bee5', '#f8f8f8', '#f8f8f8', '#fcfcfc'],
            'nc_header_ge': ['#d6d6d6', '#3c98f0', '#d6d6d6', '#d6d6d6', '#f2f2f2'],
            'nc_header_st_bg': ['#ffffff', '#ffffff', '#24343d', '#ffffff', '#ffffff'],
            'nc_header_st_bgh': ['#fafafa', '#ffffff', '#24343d', '#ffffff', '#ffffff'],
            'nc_header_st_link': ['#1c1c1c', '#1c1c1c', '#ffffff', '#000000', '#000000'],
            'nc_header_st_linkh': ['#00c293', '#3c98f0', '#c69cff', '#468676', '#000000'],
            'header_nbg': ['#ffffff', '#ffffff', '#edf0f4', '#ffffff', '#ffffff'],
            'header_nb': ['#f2f2f2', '#f2f2f2', '#edf0f4', '#fafafa', '#f2f2f2'],
            'header_nt': ['#bebebe', '#bebebe', '#b5b9be', '#989898', '#989898'],
            'header_nl': ['#424242', '#424242', '#383a41', '#989898', '#989898'],
            'header_nlh': ['#00c293', '#3c98f0', '#ad70ff', '#468676', '#ffd221'],
            'header_ns': ['#ffffff', '#ffffff', '#ffffff', '#ffffff', '#ffffff'],
            'nc_m_under_color': ['#00c293', '#3c98f0', '#f2f2f2', '#f2f2f2', '#ffd221'],
            'm_bg': ['#ffffff', '#ffffff', '#ffffff', '#ffffff', '#ffffff'],
            'm_link_bg_hover': ['#fafafa', '#fafafa', '#ffffff', '#000000', '#ffffff'],
            'm_link': ['#1c1c1c', '#ffffff', '#24343d', '#000000', '#000000'],
            'm_link_hover': ['#00c293', '#ffd325', '#7a40c9', '#468676', '#000000'],
            'm_popup_llink': ['#1c1c1c', '#1c1c1c', '#24343d', '#000000', '#000000'],
            'm_popup_llink_hover': ['#00bda0', '#3c98f0', '#7a40c9', '#468676', '#ffd221'],
            'm_popup_lbg': ['#ffffff', '#ffffff', '#ffffff', '#ffffff', '#ffffff'],
            'm_popup_lchevron': ['#cccccc', '#aaaaaa', '#cccccc', '#cccccc', '#cccccc'],
            'm_popup_lborder': ['#f2f2f2', '#ffffff', '#ffffff', '#ffffff', '#f2f2f2'],
            'search_bg': ['#ffffff', '#ffffff', '#ffffff', '#ffffff', '#ffffff'],
            'search_line': ['#ffffff', '#ffffff', '#ffffff', '#aaaaaa', '#ffd221'],
            'search_input': ['#aaaaaa', '#aaaaaa', '#aaaaaa', '#aaaaaa', '#aaaaaa'],
            'search_t': ['#1c1c1c', '#1c1c1c', '#24343d', '#000000', '#000000'],
            'search_icon': ['#1c1c1c', '#1c1c1c', '#24343d', '#000000', '#000000'],
            'search_bg_hover': ['#ffffff', '#ffffff', '#ffffff', '#ffffff', '#ffffff'],
            'search_lineh': ['#ffffff', '#ffffff', '#ffffff', '#000000', '#ffd221'],
            'search_inputh': ['#aaaaaa', '#aaaaaa', '#aaaaaa', '#aaaaaa', '#aaaaaa'],
            'search_t_hover': ['#1c1c1c', '#1c1c1c', '#24343d', '#000000', '#000000'],
            'search_iconh': ['#1c1c1c', '#1c1c1c', '#24343d', '#468676', '#000000'],
            'cart_bg': ['#ffffff', '#ffffff', '#7a40c9', '#f2d3da', '#ffd221'],
            'cart_b': ['#ffffff', '#ffffff', '#ffffff', '#ffffff', '#ffd221'],
            'cart_i': ['#1c1c1c', '#1c1c1c', '#24343d', '#000000', '#000000'],
            'cart_t': ['#1c1c1c', '#1c1c1c', '#24343d', '#000000', '#000000'],
            'cart_q': ['#1c1c1c', '#1c1c1c', '#ffffff', '#000000', '#000000'],
            'cart_bg_hover': ['#ffffff', '#ffffff', '#7a40c9', '#f2d3da', '#000000'],
            'cart_b_hover': ['#ffffff', '#ffffff', '#ffffff', '#ffffff', '#ffd221'],
            'cart_i_hover': ['#1c1c1c', '#1c1c1c', '#24343d', '#468676', '#000000'],
            'cart_t_hover': ['#1c1c1c', '#1c1c1c', '#24343d', '#468676', '#000000'],
            'cart_q_hover': ['#1c1c1c', '#1c1c1c', '#ffffff', '#000000', '#ffffff'],
            'g_bg_content': ['#ffffff', '#ffffff', '#ffffff', '#ffffff', '#ffffff'],
            'g_border': ['#f2f2f2', '#f2f2f2', '#f2f2f2', '#f2f2f2', '#f2f2f2'],
            'g_body_text': ['#777777', '#777777', '#777777', '#777777', '#777777'],
            'g_body_comment': ['#bbbbbb', '#bbbbbb', '#bbbbbb', '#bbbbbb', '#bbbbbb'],
            'g_body_link': ['#000000', '#000000', '#000000', '#468676', '#000000'],
            'g_body_link_hover': ['#00c293', '#20bee5', '#7a40c9', '#000000', '#555555'],
            'g_label': ['#1c1c1c', '#1c1c1c', '#1c1c1c', '#1c1c1c', '#1c1c1c'],
            'g_header': ['#1c1c1c', '#1c1c1c', '#1c1c1c', '#000000', '#000000'],
            'g_header_under': ['#f2f2f2', '#f2f2f2', '#f2f2f2', '#f0f0f0', '#f0f0f0'],
            'g_header_decor': ['#5fceb3', '#20bee5', '#7a40c9', '#f0f0f0', '#ffd221'],
            'g_cc': ['#f2f2f2', '#f2f2f2",', '#f2f2f2', '#f2f2f2', '#f2f2f2'],
            'g_ch': ['#00c293', '#20bee5', '#7a40c9', '#000000', '#000000'],
            'g_hb': ['#ffffff', '#ffffff', '#ffffff', '#ffffff', '#ffffff'],
            'g_hc': ['#1c1c1c', '#1c1c1c', '#1c1c1c', '#1c1c1c', '#1c1c1c'],
            'g_bg_even': ['#f2f2f2', '#f2f2f2', '#f2f2f2', '#f2f2f2', '#f2f2f2'],
            'g_color_even': ['#000000', '#000000', '#000000', '#000000', '#000000'],
            'g_acc_icon': ['#1c1c1c', '#1c1c1c', '#1c1c1c', '#bbbbbb', '#bbbbbb'],
            'g_acc_title': ['#1c1c1c', '#1c1c1c', '#1c1c1c', '#1c1c1c', '#1c1c1c'],
            'g_fancy_nbg': ['#ffffff', '#ffffff', '#ffffff', '#ffffff', '#ffffff'],
            'g_fancy_nc': ['#1c1c1c', '#1c1c1c', '#1c1c1c', '#1c1c1c', '#1c1c1c'],
            'b_normal_bg': ['#5fceb3', '#20bee5', '#ffffff', '#ffffff', '#ffd221'],
            'b_normal_border': ['#5fceb3', '#20bee5', '#f2f2f2', '#eeeeee', '#ffd221'],
            'b_normal_color': ['#ffffff', '#ffffff', '#1c1c1c', '#468676', '#000000'],
            'b_normal_bg_hover': ['#1c1c1c', '#ffc21a', '#7a40c9', '#ffffff', '#313131'],
            'b_normal_border_hover': ['#1c1c1c', '#ffc21a', '#7a40c9', '#575757', '#313131'],
            'b_normal_color_hover': ['#ffffff', '#ffffff', '#ffffff', '#468676', '#ffffff'],
            'b_ex_bg': ['#f05377', '#ffc21a', '#7a40c9', '#468676', '#ffd221'],
            'b_ex_border': ['#f05377', '#ffc21a', '#7a40c9', '#468676', '#ffd221'],
            'b_ex_color': ['#ffffff', '#ffffff', '#ffffff', '#ffffff', '#000000'],
            'i_bg': ['#ffffff', '#ffffff', '#ffffff', '#ffffff', '#ffffff'],
            'i_color': ['#323232', '#323232', '#323232', '#323232', '#323232'],
            'i_b_color': ['#f2f2f2', '#f2f2f2', '#f2f2f2', '#f2f2f2', '#f2f2f2'],
            'i_bg_focus': ['#ffffff', '#ffffff', '#ffffff', '#ffffff', '#ffffff'],
            'i_color_focus': ['#1c1c1c', '#1c1c1c', '#1c1c1c', '#1c1c1c', '#1c1c1c'],
            'i_b_focus': ['#5c5c5c', '#5c5c5c', '#7a40c9', '#468676', '#ffd221'],
            'i_ph': ['#aaaaaa', '#aaaaaa', '#aaaaaa', '#aaaaaa', '#aaaaaa'],
            'rc_bg_active': ['#00c293', '#20bee5', '#7a40c9', '#468676', '#ffd221'],
            'nc_loader_bg': ['#ffffff', '#ffffff', '#24343d', '#dfbed0', '#ffffff'],
            'nc_loader_color': ['#5fceb3', '#ffd234', '#7a40c9', '#ffffff', '#ffd221'],
            'nc_loader_color2': ['#5fceb3', '#5fceb3', '#5fceb3', '#5fceb3', '#ffd221'],
            'brand_name': ['#000000', '#000000', '#000000', '#000000', '#000000'],
            'brand_name_hover': ['#00c293', '#20bee5', '#7a40c9', '#468676', '#ffd221'],
            'b_link': ['#888888', '#888888', '#888888', '#888888', '#888888'],
            'b_link_hover': ['#323232', '#323232', '#7a40c9', '#468676', '#ffd221'],
            'b_separator': ['#dddddd', '#dddddd', '#dddddd', '#dddddd', '#dddddd'],
            'page_bq_q': ['#777777', '#777777', '#7a40c9', '#f2d3da', '#777777'],
            'contact_icon': ['#1c1c1c', '#1c1c1c', '#7a40c9', '#eeeeee', '#000000'],
            'warning_message_color': ['#e7b918', '#e7b918', '#e7b918', '#e7b918', '#ffd221'],
            'success_message_color': ['#00c293', '#00c293', '#00c293', '#00c293', '#00c293'],
            'danger_message_color': ['#f05377', '#f05377', '#f05377', '#f05377', '#ffd221'],
            'sidebar_title_bg': ['#5fceb3', '#5fceb3', '#7a40c9', '#7a40c9', '#ffd221'],
            'sidebar_title_border': ['#2fa085', '#2fa085', '#2fa085', '#2fa085', '#2fa085'],
            'sidebar_title_link': ['#ffffff', '#1c1c1c', '#24343d', '#24343d', '#24343d'],
            'sidebar_title_link_hover': ['#ffffff', '#20bee5', '#7a40c9', '#468676', '#ffd221'],
            'sidebar_block_content_bg': ['#ffffff', '#ffffff', '#ffffff', '#ffffff', '#ffffff'],
            'sidebar_block_content_border': ['#ffffff', '#f2f2f2', '#edf0f4', '#edf0f4', '#f2f2f2'],
            'sidebar_block_text_color': ['#424242', '#424242', '#424242', '#424242', '#424242'],
            'sidebar_block_link': ['#1c1c1c', '#1c1c1c', '#24343d', '#000000', '#000000'],
            'sidebar_block_link_hover': ['#00c293', '#20bee5', '#7a40c9', '#468676', '#ffd221'],
            'sidebar_item_separator': ['#f2f2f2', '#f2f2f2', '#f2f2f2', '#f2f2f2', '#fafafa'],
            'pl_filter_t': ['#1c1c1c', '#1c1c1c', '#24343d', '#000000', '#000000'],
            'sidebar_c': ['#d6d6d6', '#d6d6d6', '#d6d6d6', '#d6d6d6', '#d6d6d6'],
            'sidebar_hc': ['#323232', '#323232', '#323232', '#000000', '#000000'],
            'sidebar_button_bg': ['#ffffff', '#ffffff', '#ffffff', '#ffffff', '#ffd221'],
            'sidebar_button_border': ['#ededed', '#ededed', '#ededed', '#ededed', '#ffd221'],
            'sidebar_button_color': ['#323232', '#323232', '#24343d', '#000000', '#000000'],
            'sidebar_button_hbg': ['#323232', '#323232', '#7a40c9', '#468676', '#000000'],
            'sidebar_button_hborder': ['#323232', '#323232', '#7a40c9', '#468676', '#000000'],
            'sidebar_button_hcolor': ['#ffffff', '#ffffff', '#ffffff', '#ffffff', '#ffffff'],
            'sidebar_product_price': ['#444444', '#444444', '#444444', '#444444', '#444444'],
            'sidebar_product_oprice': ['#bbbbbb', '#bbbbbb', '#bbbbbb', '#bbbbbb', '#bbbbbb'],
            'pl_nav_grid': ['#1c1c1c', '#20bee5', '#1c1c1c', '#888888', '#000000'],
            'pl_number_color': ['#1c1c1c', '#1c1c1c', '#1c1c1c', '#1c1c1c', '#1c1c1c'],
            'pl_number_color_hover': ['#00c293', '#ffd325', '#1c1c1c', '#1c1c1c', '#1c1c1c'],
            'pl_item_bg': ['#ffffff', '#ffffff', '#ffffff', '#ffffff', '#ffffff'],
            'pl_item_border': ['#f2f2f2', '#f2f2f2', '#f2f2f2', '#ffffff', '#f2f2f2'],
            'nc_pl_item_borderh': ['#f2f2f2', '#f2f2f2', '#f2f2f2', '#ffffff', '#eeeeee'],
            'pl_product_name': ['#1c1c1c', '#1c1c1c', '#1c1c1c', '#000000', '#000000'],
            'pl_product_price': ['#1c1c1c', '#1c1c1c', '#1c1c1c', '#000000', '#000000'],
            'pl_product_oldprice': ['#bbbbbb', '#bbbbbb', '#bbbbbb', '#468676', '#bbbbbb'],
            'pl_list_description': ['#777777', '#777777', '#777777', '#777777', '#777777'],
            'pl_hover_but': ['#1c1c1c', '#1c1c1c', '#1c1c1c', '#000000', '#000000'],
            'pl_hover_but_bg': ['#ffffff', '#ffffff', '#ffffff', '#ffffff', '#ffffff'],
            'pl_product_new_bg': ['#ffffff', '#ffffff', '#24343d', '#f2d3da', '#ffffff'],
            'pl_product_new_border': ['#ffffff', '#ffffff', '#24343d', '#f2d3da', '#f2f2f2'],
            'pl_product_new_color': ['#5fceb3', '#20bee5', '#ffffff', '#000000', '#cccccc'],
            'pl_product_sale_bg': ['#1c1c1c', '#ffffff', '#7a40c9",', '#468676', '#ffffff'],
            'pl_product_sale_border': ['#1c1c1c', '#ffffff', '#7a40c9",', '#468676', '#ffd221'],
            'pl_product_sale_color': ['#ffffff', '#ffc21a', '#ffffff', '#ffffff', '#000000'],
            'pp_reviews_staron': ['#1c1c1c', '#ffd325', '#24343d', '#555555', '#ffd221'],
            'pp_reviews_staroff': ['#1c1c1c', '#e4e4e4', '#24343d', '#555555', '#eeeeee'],
            'nc_count_bg': ['#ffffff', '#ffffff', '#ffffff', '#ffffff', '#ffffff'],
            'nc_count_color': ['#888888', '#888888', '#888888', '#888888', '#cccccc'],
            'nc_count_time': ['#1c1c1c', '#1c1c1c', '#1c1c1c', '#1c1c1c', '#1c1c1c'],
            'nc_count_watch': ['#000000', '#000000', '#ffffff', '#f2d3da', '#ffffff'],
            'nc_count_watch_bg': ['#fbd4d6', '#ffd325', '#7a40c9', '#468676', '#ffd221'],
            'pp_img_border': ['#f2f2f2', '#f2f2f2', '#f2f2f2', '#f2f2f2', '#f2f2f2'],
            'pp_icon_border': ['#f2f2f2', '#f2f2f2', '#f2f2f2', '#f2f2f2', '#f2f2f2'],
            'pp_icon_border_hover': ['#323232', '#323232', '#323232', '#323232', '#323232'],
            'pp_zi': ['#bbbbbb', '#bbbbbb', '#bbbbbb', '#bbbbbb', '#bbbbbb'],
            'pp_zihbg': ['#ffffff', '#ffffff', '#ffffff', '#ffffff', '#ffffff'],
            'nc_mobadotsc': ['#525252', '#ffd325', '#525252', '#525252', '#525252'],
            'pp_att_label': ['#1c1c1c', '#1c1c1c', '#1c1c1c', '#1c1c1c', '#1c1c1c'],
            'pp_att_color_active': ['#1c1c1c', '#1c1c1c', '#1c1c1c', '#1c1c1c', '#1c1c1c'],
            'pp_price_color': ['#1c1c1c', '#1c1c1c', '#1c1c1c', '#1c1c1c', '#1c1c1c'],
            'pp_price_coloro': ['#bbbbbb', '#bbbbbb', '#bbbbbb', '#468676', '#cccccc'],
            'nc_pp_add_bg': ['#5fceb3', '#ffc21a', '#7a40c9', '#ffffff', '#ffd221'],
            'nc_pp_add_border': ['#5fceb3', '#ffc21a', '#7a40c9', '#468676', '#ffd221'],
            'nc_pp_add_color': ['#ffffff', '#ffffff', '#ffffff', '#000000', '#000000'],
            'nc_count_pr_title': ['#1c1c1c', '#1c1c1c', '#1c1c1c', '#1c1c1c', '#1c1c1c'],
            'nc_count_pr_bg': ['#ffffff', '#ffffff', '#ffffff', '#ffffff', '#ffffff'],
            'nc_count_pr_sep': ['#f2f2f2', '#f2f2f2', '#f2f2f2', '#f2f2f2', '#f2f2f2'],
            'nc_count_pr_numbers': ['#1c1c1c', '#1c1c1c', '#1c1c1c', '#1c1c1c', '#1c1c1c'],
            'nc_count_pr_color': ['#888888', '#888888', '#888888', '#888888', '#aaaaaa'],
            'pp_info_label': ['#c1c1c1', '#c1c1c1', '#c1c1c1', '#c1c1c1', '#c1c1c1'],
            'pp_info_value': ['#1c1c1c', '#1c1c1c', '#1c1c1c', '#1c1c1c', '#1c1c1c'],
            'o_option': ['#f2f2f2', '#f2f2f2', '#f2f2f2', '#f2f2f2', '#f2f2f2'],
            'o_option_active': ['#00bda0', '#ffd325', '#7a40c9', '#468676', '#ffd221'],
            'o_info_text': ['#777777', '#777777', '#777777', '#777777', '#777777'],
            'lc_bg': ['#00bda0', '#20bee5', '#7a40c9', '#468676', '#ffd221'],
            'lc_c': ['#ffffff', '#ffffff', '#ffffff', '#ffffff', '#ffffff'],
            'bl_head': ['#000000', '#000000', '#000000', '#000000', '#000000'],
            'bl_head_hover': ['#00c293', '#20bee5', '#7a40c9', '#468676', '#ffd221'],
            'bl_h_title': ['#000000', '#000000', '#000000', '#000000', '#000000'],
            'bl_h_title_h': ['#00c293', '#20bee5', '#7a40c9', '#468676', '#ffd221'],
            'bl_h_meta': ['#aaaaaa', '#aaaaaa', '#aaaaaa', '#aaaaaa', '#aaaaaa'],
            'bl_h_bg': ['#ffffff', '#ffffff', '#ffffff', '#ffffff', '#ffffff'],
            'bl_h_border': ['#ffffff', '#f2f2f2', '#f2f2f2', '#f2f2f2', '#f2f2f2'],
            'bl_desc': ['#777777', '#777777', '#777777', '#777777', '#777777'],
            'bl_rm_color': ['#000000', '#000000', '#000000', '#000000', '#000000'],
            'bl_rm_hover': ['#00c293', '#20bee5', '#7a40c9', '#468676', '#ffd221'],
            'footer_bg': ['#fafafa', '#2a2a2a', '#1f2022', '#fafafa', '#fafafa'],
            'footer_titles': ['#cccccc', '#6f6f6f', '#989ea2', '#000000', '#000000'],
            'footer_text': ['#9d9d9d', '#979797', '#989ea2', '#989898', '#aaaaaa'],
            'footer_link': ['#555555', '#eaeaea', '#ad70ff', '#000000', '#000000'],
            'footer_link_h': ['#000000', '#ffffff', '#ad70ff', '#000000', '#ffd221'],
            'footer_news_bg': ['#ffffff', '#383838', '#2e3133', '#ffffff', '#ffffff'],
            'footer_news_border': ['#ffffff', '#383838', '#2e3133', '#000000', '#f2f2f2'],
            'footer_news_placeh': ['#a0a0a0', '#a0a0a0', '#55575b', '#aaaaaa', '#bbbbbb'],
            'footer_news_color': ['#525252', '#ffffff', '#ffffff', '#000000', '#000000'],
            'footer_news_button': ['#ff4653', '#ffd325', '#7a40c9', '#468676', '#ffd221'],
            'nc_levi_bg': ['#ffffff', '#ffffff', '#ffffff', '#ffffff', '#ffffff'],
            'nc_levi_border': ['#ffffff', '#ffffff', '#ffffff', '#ffffff', '#ffffff'],
            'nc_levi_i': ['#1c1c1c', '#1c1c1c', '#1c1c1c', '#1c1c1c', '#1c1c1c'],
            'nc_levi_i_hover': ['#1c1c1c', '#1c1c1c', '#1c1c1c', '#1c1c1c', '#1c1c1c'],
            'nc_levi_cart': ['#1c1c1c', '#1c1c1c', '#1c1c1c', '#1c1c1c', '#1c1c1c'],
            'nc_levi_cart_a': ['#00c293', '#ffd325', '#7a40c9', '#7a40c9', '#ffd221'],
            'nc_levi_close': ['#f2f2f2', '#f2f2f2', '#f2f2f2', '#f2f2f2', '#f2f2f2'],
            'nc_levi_close_i': ['#1c1c1c', '#1c1c1c', '#1c1c1c', '#1c1c1c', '#1c1c1c'],
            'nc_side_bg': ['#ffffff', '#ffffff', '#ffffff', '#ffffff', '#ffffff'],
            'nc_side_title': ['#1c1c1c', '#1c1c1c', '#1c1c1c', '#1c1c1c', '#1c1c1c'],
            'nc_side_text': ['#aaaaaa', '#aaaaaa', '#aaaaaa', '#aaaaaa', '#aaaaaa'],
            'nc_side_light': ['#bbbbbb', '#bbbbbb', '#bbbbbb', '#bbbbbb', '#bbbbbb'],
            'nc_side_sep': ['#f2f2f2', '#f2f2f2', '#f2f2f2', '#f2f2f2', '#f2f2f2'],
            'nc_mob_header': ['#ffffff', '#ffffff', '#ffffff', '#ffffff', '#ffffff'],
            'nc_mob_menu': ['#1c1c1c', '#1c1c1c', '#1c1c1c', '#000000', '#000000'],
        }

        var keys = Object.keys(scheme_colors);
        for (var i = 0; i < keys.length; i++) {
            var key = keys[i];
            var value = scheme_colors[key][scheme_number - 1];
            var name = $("[name='" + key + "']");

            $(name).each(function () {
                $(this).val(value)
            });
        }

        $('.colors_apply').html('<i></i>Color scheme changed, click save changes to save it');

    });


    // reset popup prevent
    $(".reset_container").click(function () {
        $(".reset_popup").css('top', '0');
    });

    $(".no-button").click(function () {
        $(".reset_popup").css('top', '100px');
    });

    //slider
    var nc_b_speed = $("#nc_b_speed").val()
    $("#slider_nc_b_speed").slider({
        range: "min",
        min: 50,
        max: 1000,
        step: 5,
        value: nc_b_speed,
        slide: function (event, ui) {
            $("#nc_b_speed").val(ui.value);
        }
    });
    $("#nc_b_speed").val($("#slider_nc_b_speed").slider("value"));


    //slider
    var nc_fw_menu = $("#nc_fw_menu").val()
    $("#slider_nc_fw_menu").slider({
        range: "min", min: 300, max: 700, step: 100, value: nc_fw_menu, slide: function (event, ui) {
            $("#nc_fw_menu").val(ui.value);
        }
    });
    var nc_fw_heading = $("#nc_fw_heading").val()
    $("#slider_nc_fw_heading").slider({
        range: "min", min: 300, max: 700, step: 100, value: nc_fw_heading, slide: function (event, ui) {
            $("#nc_fw_heading").val(ui.value);
        }
    });
    var nc_fw_but = $("#nc_fw_but").val()
    $("#slider_nc_fw_but").slider({
        range: "min", min: 300, max: 700, step: 100, value: nc_fw_but, slide: function (event, ui) {
            $("#nc_fw_but").val(ui.value);
        }
    });
    var nc_fw_hp = $("#nc_fw_hp").val()
    $("#slider_nc_fw_hp").slider({
        range: "min", min: 300, max: 700, step: 100, value: nc_fw_hp, slide: function (event, ui) {
            $("#nc_fw_hp").val(ui.value);
        }
    });
    var nc_fw_pn = $("#nc_fw_pn").val()
    $("#slider_nc_fw_pn").slider({
        range: "min", min: 300, max: 700, step: 100, value: nc_fw_pn, slide: function (event, ui) {
            $("#nc_fw_pn").val(ui.value);
        }
    });
    var nc_fw_bb = $("#nc_fw_bb").val()
    $("#slider_nc_fw_bb").slider({
        range: "min", min: 300, max: 700, step: 100, value: nc_fw_bb, slide: function (event, ui) {
            $("#nc_fw_bb").val(ui.value);
        }
    });
    var nc_fw_add = $("#nc_fw_add").val()
    $("#slider_nc_fw_add").slider({
        range: "min", min: 300, max: 700, step: 100, value: nc_fw_add, slide: function (event, ui) {
            $("#nc_fw_add").val(ui.value);
        }
    });
    var nc_fw_f = $("#nc_fw_f").val()
    $("#slider_nc_fw_f").slider({
        range: "min", min: 300, max: 700, step: 100, value: nc_fw_f, slide: function (event, ui) {
            $("#nc_fw_f").val(ui.value);
        }
    });
    var nc_fw_ct = $("#nc_fw_ct").val()
    $("#slider_nc_fw_ct").slider({
        range: "min", min: 300, max: 700, step: 100, value: nc_fw_ct, slide: function (event, ui) {
            $("#nc_fw_ct").val(ui.value);
        }
    });
    var nc_fw_price = $("#nc_fw_price").val()
    $("#slider_nc_fw_price").slider({
        range: "min", min: 300, max: 700, step: 100, value: nc_fw_price, slide: function (event, ui) {
            $("#nc_fw_price").val(ui.value);
        }
    });


    $("#nc_b_speed").val($("#slider_nc_b_speed").slider("value"));

    if ($('#body_box_sw1').is(':checked')) { $('.if_body_box_bg').slideDown(500); } else { $('.if_body_box_bg').slideUp(500); }
    if ($('#body_box_sw2').is(':checked')) { $('.if_body_box_gr').slideDown(500); } else { $('.if_body_box_gr').slideUp(500); }
    if ($('#body_box_sw3').is(':checked')) { $('.if_body_box_im').slideDown(500); } else { $('.if_body_box_im').slideUp(500); }
    if ($('#body_box_sw4').is(':checked')) { $('.if_body_box_te').slideDown(500); } else { $('.if_body_box_te').slideUp(500); }

    if ($('#g_hp1_sw1').is(':checked')) { $('.if_g_hp1_bg').slideDown(500); } else { $('.if_g_hp1_bg').slideUp(500); }
    if ($('#g_hp1_sw2').is(':checked')) { $('.if_g_hp1_gr').slideDown(500); } else { $('.if_g_hp1_gr').slideUp(500); }
    if ($('#g_hp1_sw3').is(':checked')) { $('.if_g_hp1_im').slideDown(500); } else { $('.if_g_hp1_im').slideUp(500); }
    if ($('#g_hp1_sw4').is(':checked')) { $('.if_g_hp1_te').slideDown(500); } else { $('.if_g_hp1_te').slideUp(500); }

    if ($('#g_hp2_sw1').is(':checked')) { $('.if_g_hp2_bg').slideDown(500); } else { $('.if_g_hp2_bg').slideUp(500); }
    if ($('#g_hp2_sw2').is(':checked')) { $('.if_g_hp2_gr').slideDown(500); } else { $('.if_g_hp2_gr').slideUp(500); }
    if ($('#g_hp2_sw3').is(':checked')) { $('.if_g_hp2_im').slideDown(500); } else { $('.if_g_hp2_im').slideUp(500); }
    if ($('#g_hp2_sw4').is(':checked')) { $('.if_g_hp2_te').slideDown(500); } else { $('.if_g_hp2_te').slideUp(500); }

    if ($('#g_hp3_sw1').is(':checked')) { $('.if_g_hp3_bg').slideDown(500); } else { $('.if_g_hp3_bg').slideUp(500); }
    if ($('#g_hp3_sw2').is(':checked')) { $('.if_g_hp3_gr').slideDown(500); } else { $('.if_g_hp3_gr').slideUp(500); }
    if ($('#g_hp3_sw3').is(':checked')) { $('.if_g_hp3_im').slideDown(500); } else { $('.if_g_hp3_im').slideUp(500); }
    if ($('#g_hp3_sw4').is(':checked')) { $('.if_g_hp3_te').slideDown(500); } else { $('.if_g_hp3_te').slideUp(500); }

    if ($('#g_hp4_sw1').is(':checked')) { $('.if_g_hp4_bg').slideDown(500); } else { $('.if_g_hp4_bg').slideUp(500); }
    if ($('#g_hp4_sw2').is(':checked')) { $('.if_g_hp4_gr').slideDown(500); } else { $('.if_g_hp4_gr').slideUp(500); }
    if ($('#g_hp4_sw3').is(':checked')) { $('.if_g_hp4_im').slideDown(500); } else { $('.if_g_hp4_im').slideUp(500); }
    if ($('#g_hp4_sw4').is(':checked')) { $('.if_g_hp4_te').slideDown(500); } else { $('.if_g_hp4_te').slideUp(500); }

    if ($('#g_hp5_sw1').is(':checked')) { $('.if_g_hp5_bg').slideDown(500); } else { $('.if_g_hp5_bg').slideUp(500); }
    if ($('#g_hp5_sw2').is(':checked')) { $('.if_g_hp5_gr').slideDown(500); } else { $('.if_g_hp5_gr').slideUp(500); }
    if ($('#g_hp5_sw3').is(':checked')) { $('.if_g_hp5_im').slideDown(500); } else { $('.if_g_hp5_im').slideUp(500); }
    if ($('#g_hp5_sw4').is(':checked')) { $('.if_g_hp5_te').slideDown(500); } else { $('.if_g_hp5_te').slideUp(500); }

    if ($('#g_hp6_sw1').is(':checked')) { $('.if_g_hp6_bg').slideDown(500); } else { $('.if_g_hp6_bg').slideUp(500); }
    if ($('#g_hp6_sw2').is(':checked')) { $('.if_g_hp6_gr').slideDown(500); } else { $('.if_g_hp6_gr').slideUp(500); }
    if ($('#g_hp6_sw3').is(':checked')) { $('.if_g_hp6_im').slideDown(500); } else { $('.if_g_hp6_im').slideUp(500); }
    if ($('#g_hp6_sw4').is(':checked')) { $('.if_g_hp6_te').slideDown(500); } else { $('.if_g_hp6_te').slideUp(500); }

    if ($('#g_fs1_sw1').is(':checked')) { $('.if_g_fs1_bg').slideDown(500); } else { $('.if_g_fs1_bg').slideUp(500); }
    if ($('#g_fs1_sw2').is(':checked')) { $('.if_g_fs1_gr').slideDown(500); } else { $('.if_g_fs1_gr').slideUp(500); }
    if ($('#g_fs1_sw3').is(':checked')) { $('.if_g_fs1_im').slideDown(500); } else { $('.if_g_fs1_im').slideUp(500); }
    if ($('#g_fs1_sw4').is(':checked')) { $('.if_g_fs1_te').slideDown(500); } else { $('.if_g_fs1_te').slideUp(500); }

    if ($('#g_fs2_sw1').is(':checked')) { $('.if_g_fs2_bg').slideDown(500); } else { $('.if_g_fs2_bg').slideUp(500); }
    if ($('#g_fs2_sw2').is(':checked')) { $('.if_g_fs2_gr').slideDown(500); } else { $('.if_g_fs2_gr').slideUp(500); }
    if ($('#g_fs2_sw3').is(':checked')) { $('.if_g_fs2_im').slideDown(500); } else { $('.if_g_fs2_im').slideUp(500); }
    if ($('#g_fs2_sw4').is(':checked')) { $('.if_g_fs2_te').slideDown(500); } else { $('.if_g_fs2_te').slideUp(500); }

    if ($('#g_fs3_sw1').is(':checked')) { $('.if_g_fs3_bg').slideDown(500); } else { $('.if_g_fs3_bg').slideUp(500); }
    if ($('#g_fs3_sw2').is(':checked')) { $('.if_g_fs3_gr').slideDown(500); } else { $('.if_g_fs3_gr').slideUp(500); }
    if ($('#g_fs3_sw3').is(':checked')) { $('.if_g_fs3_im').slideDown(500); } else { $('.if_g_fs3_im').slideUp(500); }
    if ($('#g_fs3_sw4').is(':checked')) { $('.if_g_fs3_te').slideDown(500); } else { $('.if_g_fs3_te').slideUp(500); }

    if ($('#nc_header_bg1').is(':checked')) { $('.if_nc_header_bc').slideDown(500); } else { $('.if_nc_header_bc').slideUp(500); }
    if ($('#nc_header_bg2').is(':checked')) { $('.if_nc_header_gr').slideDown(500); } else { $('.if_nc_header_gr').slideUp(500); }
    if ($('#nc_header_bg3').is(':checked')) { $('.if_nc_header_im').slideDown(500); } else { $('.if_nc_header_im').slideUp(500); }
    if ($('#nc_header_bg4').is(':checked')) { $('.if_nc_header_te').slideDown(500); } else { $('.if_nc_header_te').slideUp(500); }

    if ($('#nc_main_bg1').is(':checked')) { $('.if_nc_main_bc').slideDown(500); } else { $('.if_nc_main_bc').slideUp(500); }
    if ($('#nc_main_bg2').is(':checked')) { $('.if_nc_main_gr').slideDown(500); } else { $('.if_nc_main_gr').slideUp(500); }
    if ($('#nc_main_bg3').is(':checked')) { $('.if_nc_main_im').slideDown(500); } else { $('.if_nc_main_im').slideUp(500); }
    if ($('#nc_main_bg4').is(':checked')) { $('.if_nc_main_te').slideDown(500); } else { $('.if_nc_main_te').slideUp(500); }

    if ($('#g_op2_sw1').is(':checked')) { $('.if_g_op2_bg').slideDown(500); } else { $('.if_g_op2_bg').slideUp(500); }
    if ($('#g_op2_sw2').is(':checked')) { $('.if_g_op2_gr').slideDown(500); } else { $('.if_g_op2_gr').slideUp(500); }
    if ($('#g_op2_sw3').is(':checked')) { $('.if_g_op2_im').slideDown(500); } else { $('.if_g_op2_im').slideUp(500); }
    if ($('#g_op2_sw4').is(':checked')) { $('.if_g_op2_te').slideDown(500); } else { $('.if_g_op2_te').slideUp(500); }

    if ($('#g_mc_sw1').is(':checked')) { $('.if_g_mc_bg').slideDown(500); } else { $('.if_g_mc_bg').slideUp(500); }
    if ($('#g_mc_sw2').is(':checked')) { $('.if_g_mc_gr').slideDown(500); } else { $('.if_g_mc_gr').slideUp(500); }
    if ($('#g_mc_sw3').is(':checked')) { $('.if_g_mc_im').slideDown(500); } else { $('.if_g_mc_im').slideUp(500); }
    if ($('#g_mc_sw4').is(':checked')) { $('.if_g_mc_te').slideDown(500); } else { $('.if_g_mc_te').slideUp(500); }

    if ($('#g_bc_sw1').is(':checked')) { $('.if_g_bc_bg').slideDown(500); } else { $('.if_g_bc_bg').slideUp(500); }
    if ($('#g_bc_sw2').is(':checked')) { $('.if_g_bc_gr').slideDown(500); } else { $('.if_g_bc_gr').slideUp(500); }
    if ($('#g_bc_sw3').is(':checked')) { $('.if_g_bc_im').slideDown(500); } else { $('.if_g_bc_im').slideUp(500); }
    if ($('#g_bc_sw4').is(':checked')) { $('.if_g_bc_te').slideDown(500); } else { $('.if_g_bc_te').slideUp(500); }


    if ($('#g_pro_w2').is(':checked')) { $('.if_pa_pro').slideDown(500); } else { $('.if_pa_pro').slideUp(500); }
    if ($('#g_mini_w2').is(':checked')) { $('.if_pa_mini').slideDown(500); } else { $('.if_pa_mini').slideUp(500); }
    if ($('#g_info_w2').is(':checked')) { $('.if_pa_info').slideDown(500); } else { $('.if_pa_info').slideUp(500); }
    if ($('#g_bra_w2').is(':checked')) { $('.if_pa_bra').slideDown(500); } else { $('.if_pa_bra').slideUp(500); }
    if ($('#g_blog_w2').is(':checked')) { $('.if_pa_blog').slideDown(500); } else { $('.if_pa_blog').slideUp(500); }
    if ($('#ban_wid_top2').is(':checked')) { $('.if_pa_ban_top').slideDown(500); } else { $('.if_pa_ban_top').slideUp(500); }
    if ($('#ban_wid_pro2').is(':checked')) { $('.if_pa_ban_pro').slideDown(500); } else { $('.if_pa_ban_pro').slideUp(500); }
    if ($('#ban_wid_mini2').is(':checked')) { $('.if_pa_ban_mini').slideDown(500); } else { $('.if_pa_ban_mini').slideUp(500); }
    if ($('#ban_wid_info2').is(':checked')) { $('.if_pa_ban_info').slideDown(500); } else { $('.if_pa_ban_info').slideUp(500); }
    if ($('#ban_wid_bra2').is(':checked')) { $('.if_pa_ban_bra').slideDown(500); } else { $('.if_pa_ban_bra').slideUp(500); }
    if ($('#ban_wid_home2').is(':checked')) { $('.if_pa_ban_home').slideDown(500); } else { $('.if_pa_ban_home').slideUp(500); }
    if ($('#ban_wid_foot2').is(':checked')) { $('.if_pa_ban_foot').slideDown(500); } else { $('.if_pa_ban_foot').slideUp(500); }
    if ($('#ban_wid_foott2').is(':checked')) { $('.if_pa_ban_foott').slideDown(500); } else { $('.if_pa_ban_foott').slideUp(500); }
    if ($('#ban_wid_footb2').is(':checked')) { $('.if_pa_ban_footb').slideDown(500); } else { $('.if_pa_ban_footb').slideUp(500); }
    if ($('#ban_wid_s12').is(':checked')) { $('.if_pa_ban_s1').slideDown(500); } else { $('.if_pa_ban_s1').slideUp(500); }
    if ($('#ban_wid_s22').is(':checked')) { $('.if_pa_ban_s2').slideDown(500); } else { $('.if_pa_ban_s2').slideUp(500); }
    if ($('#ban_wid_s32').is(':checked')) { $('.if_pa_ban_s3').slideDown(500); } else { $('.if_pa_ban_s3').slideUp(500); }


    if ($('#nc_pp_image3').is(':checked')) {
        $('.if_pp_image3').slideDown(500);
    } else { $('.if_pp_image3').slideUp(500); }

    if ($('#nc_pc_layout2').is(':checked') || $('#nc_pc_layout3').is(':checked')) {
        $('.if_pc_layout23').slideDown(500);
    } else { $('.if_pc_layout23').slideUp(500); }

    if ($('#header_lay5').is(':checked') || $('#header_lay6').is(':checked')) {
        $('.if_nav').slideDown(500);
    } else { $('.if_nav').slideUp(500); }

    if ($('#pl_filter_b1').is(':checked')) {
        $('.if_pl_filter_b').slideDown(500);
    } else { $('.if_pl_filter_b').slideUp(500); }

    if ($('#bl_dd1').is(':checked')) {
        $('.if_bl_dd').slideDown(500);
    } else { $('.if_bl_dd').slideUp(500); }

    if ($('#header_trah1').is(':checked')) {
        $('.if_trah').slideDown(500);
    } else { $('.if_trah').slideUp(500); }

    if ($('#header_trao1').is(':checked')) {
        $('.if_trao').slideDown(500);
    } else { $('.if_trao').slideUp(500); }

    if ($('#cl_popup_b1').is(':checked')) {
        $('.if_cl_popup_b').slideDown(500);
    } else { $('.if_cl_popup_b').slideUp(500); }

    if ($('#c_popup_b1').is(':checked')) {
        $('.if_c_popup_b').slideDown(500);
    } else { $('.if_c_popup_b').slideUp(500); }

    if ($('#m_popup_b1').is(':checked')) {
        $('.if_m_popup_b').slideDown(500);
    } else { $('.if_m_popup_b').slideUp(500); }

    if ($('#m_link_bgs1').is(':checked')) {
        $('.if_m_bg').slideDown(500);
    } else { $('.if_m_bg').slideUp(500); }

    if ($('#nc_loader_logo_1').is(':checked')) {
        $('.if_loader_logo').slideDown(500);
    } else { $('.if_loader_logo').slideUp(500); }

    if ($('#pl_nav_bot_b0').is(':checked')) {
        $('.if_pl_nav_bot_b').slideUp(500);
    }
    if ($('#pl_nav_bot_b1').is(':checked')) {
        $('.if_pl_nav_bot_b').slideDown(500);
    }
    if ($('#pl_nav_top_b0').is(':checked')) {
        $('.if_pl_nav_top_b').slideUp(500);
    }
    if ($('#pl_nav_top_b1').is(':checked')) {
        $('.if_pl_nav_top_b').slideDown(500);
    }
    if ($('#pp_li0').is(':checked')) {
        $('.if_pp_li').slideUp(500);
    }
    if ($('#pp_li1').is(':checked')) {
        $('.if_pp_li').slideDown(500);
    }
    if ($('#pp_imgb0').is(':checked')) {
        $('.if_pp_imgb').slideUp(500);
    }
    if ($('#pp_imgb1').is(':checked')) {
        $('.if_pp_imgb').slideDown(500);
    }
    if ($('#footer_map_enbb0').is(':checked')) {
        $('.if_footer_map_enbb').slideUp(500);
    }
    if ($('#footer_map_enbb1').is(':checked')) {
        $('.if_footer_map_enbb').slideDown(500);
    }
    if ($('#footer_map_en0').is(':checked')) {
        $('.if_footer_map_en').slideUp(500);
    }
    if ($('#footer_map_en1').is(':checked')) {
        $('.if_footer_map_en').slideDown(500);
    }
    if ($('#sidebar_categories_b0').is(':checked')) {
        $('.if_sidebar_categories_b').slideUp(500);
    }
    if ($('#sidebar_categories_b1').is(':checked')) {
        $('.if_sidebar_categories_b').slideDown(500);
    }
    if ($('#sidebar_content_b0').is(':checked')) {
        $('.if_sidebar_content_b').slideUp(500);
    }
    if ($('#sidebar_content_b1').is(':checked')) {
        $('.if_sidebar_content_b').slideDown(500);
    }
    if ($('#sidebar_block_content_qbg0').is(':checked')) {
        $('.if_sidebar_qbg').slideUp(500);
    }
    if ($('#sidebar_block_content_qbg1').is(':checked')) {
        $('.if_sidebar_qbg').slideDown(500);
    }
    if ($('#sidebar_title_b0').is(':checked')) {
        $('.if_sidebar_title_b').slideUp(500);
    }
    if ($('#sidebar_title_b1').is(':checked')) {
        $('.if_sidebar_title_b').slideDown(500);
    }
    if ($('#sidebar_title0').is(':checked')) {
        $('.if_sidebar_title').slideUp(500);
    }
    if ($('#sidebar_title1').is(':checked')) {
        $('.if_sidebar_title').slideDown(500);
    }
    if ($('#sidebar_bg0').is(':checked')) {
        $('.if_sidebar_bg').slideUp(500);
    }
    if ($('#sidebar_bg1').is(':checked')) {
        $('.if_sidebar_bg').slideDown(500);
    }
    if ($('#bl_rm_bg0').is(':checked')) {
        $('.if_rmbg').slideUp(500);
    }
    if ($('#bl_rm_bg1').is(':checked')) {
        $('.if_rmbg').slideDown(500);
    }
    if ($('#bl_rm_border0').is(':checked')) {
        $('.if_rmborder').slideUp(500);
    }
    if ($('#bl_rm_border1').is(':checked')) {
        $('.if_rmborder').slideDown(500);
    }
    if ($('#g_lay1').is(':checked') || $('#g_lay2').is(':checked')) {
        $('.if_boxed').slideUp(500);
    }
    if ($('#g_lay3').is(':checked') || $('#g_lay4').is(':checked')) {
        $('.if_boxed').slideDown(500);
    }
    if ($('#display_gradient_1').is(':checked')) {
        $('.if_image').slideDown(500);
    }
    if ($('#display_gradient_0').is(':checked')) {
        $('.if_image').slideUp(500);
    }

    //if boxed
    $('.nc_m_under').click(function () {
        if ($('#nc_m_under0').is(':checked')) {
            $('.if_under').slideUp(500);
        }
        if ($('#nc_m_under1').is(':checked')) {
            $('.if_under').slideDown(500);
        }
    });
    //if boxed
    $('.glay').click(function () {
        if ($('#g_lay1').is(':checked') || $('#g_lay2').is(':checked')) {
            $('.if_boxed').slideUp(500);
        }
        if ($('#g_lay3').is(':checked') || $('#g_lay4').is(':checked')) {
            $('.if_boxed').slideDown(500);
        }
    });
    //if image
    $('.if_img').click(function () {
        if ($('#display_gradient_1').is(':checked')) {
            $('.if_image').slideDown(500);
        }
        if ($('#display_gradient_0').is(':checked')) {
            $('.if_image').slideUp(500);
        }
    });
    //if blog readmore bg
    $('.bl_rm_bg').click(function () {
        if ($('#bl_rm_bg1').is(':checked')) {
            $('.if_rmbg').slideDown(500);
        }
        if ($('#bl_rm_bg0').is(':checked')) {
            $('.if_rmbg').slideUp(500);
        }
    });
    //if blog readmore border
    $('.bl_rm_border').click(function () {
        if ($('#bl_rm_border1').is(':checked')) {
            $('.if_rmborder').slideDown(500);
        }
        if ($('#bl_rm_border0').is(':checked')) {
            $('.if_rmborder').slideUp(500);
        }
    });
    //if sidebar bg
    $('.sidebar_bg').click(function () {
        if ($('#sidebar_bg1').is(':checked')) {
            $('.if_sidebar_bg').slideDown(500);
        }
        if ($('#sidebar_bg0').is(':checked')) {
            $('.if_sidebar_bg').slideUp(500);
        }
    });
    //if sidebar title bg
    $('.sidebar_title').click(function () {
        if ($('#sidebar_title1').is(':checked')) {
            $('.if_sidebar_title').slideDown(500);
        }
        if ($('#sidebar_title0').is(':checked')) {
            $('.if_sidebar_title').slideUp(500);
        }
    });
    //if sidebar title border
    $('.sidebar_title_b').click(function () {
        if ($('#sidebar_title_b1').is(':checked')) {
            $('.if_sidebar_title_b').slideDown(500);
        }
        if ($('#sidebar_title_b0').is(':checked')) {
            $('.if_sidebar_title_b').slideUp(500);
        }
    });
    //if sidebar content bg
    $('.sidebar_block_content_qbg').click(function () {
        if ($('#sidebar_block_content_qbg1').is(':checked')) {
            $('.if_sidebar_qbg').slideDown(500);
        }
        if ($('#sidebar_block_content_qbg0').is(':checked')) {
            $('.if_sidebar_qbg').slideUp(500);
        }
    });
    //if sidebar content border
    $('.sidebar_content_b').click(function () {
        if ($('#sidebar_content_b1').is(':checked')) {
            $('.if_sidebar_content_b').slideDown(500);
        }
        if ($('#sidebar_content_b0').is(':checked')) {
            $('.if_sidebar_content_b').slideUp(500);
        }
    });
    //if sidebar categories border
    $('.sidebar_categories_b').click(function () {
        if ($('#sidebar_categories_b1').is(':checked')) {
            $('.if_sidebar_categories_b').slideDown(500);
        }
        if ($('#sidebar_categories_b0').is(':checked')) {
            $('.if_sidebar_categories_b').slideUp(500);
        }
    });
    //if map icon
    $('.footer_map_en').click(function () {
        if ($('#footer_map_en1').is(':checked')) {
            $('.if_footer_map_en').slideDown(500);
        }
        if ($('#footer_map_en0').is(':checked')) {
            $('.if_footer_map_en').slideUp(500);
        }
    });
    //if map icon bb
    $('.footer_map_enbb').click(function () {
        if ($('#footer_map_enbb1').is(':checked')) {
            $('.if_footer_map_enbb').slideDown(500);
        }
        if ($('#footer_map_enbb0').is(':checked')) {
            $('.if_footer_map_enbb').slideUp(500);
        }
    });
    //if product image border
    $('.pp_imgb').click(function () {
        if ($('#pp_imgb1').is(':checked')) {
            $('.if_pp_imgb').slideDown(500);
        }
        if ($('#pp_imgb0').is(':checked')) {
            $('.if_pp_imgb').slideUp(500);
        }
    });
    //if product label icon
    $('.pp_li').click(function () {
        if ($('#pp_li1').is(':checked')) {
            $('.if_pp_li').slideDown(500);
        }
        if ($('#pp_li0').is(':checked')) {
            $('.if_pp_li').slideUp(500);
        }
    });
    //if top nav b
    $('.pl_nav_top_b').click(function () {
        if ($('#pl_nav_top_b1').is(':checked')) {
            $('.if_pl_nav_top_b').slideDown(500);
        }
        if ($('#pl_nav_top_b0').is(':checked')) {
            $('.if_pl_nav_top_b').slideUp(500);
        }
    });
    //if bot nav b
    $('.pl_nav_bot_b').click(function () {
        if ($('#pl_nav_bot_b1').is(':checked')) {
            $('.if_pl_nav_bot_b').slideDown(500);
        } else { $('.if_pl_nav_bot_b').slideUp(500); }
    });
    //if loader logo
    $('.nc_loader_logo').click(function () {
        if ($('#nc_loader_logo_1').is(':checked')) {
            $('.if_loader_logo').slideDown(500);
        } else { $('.if_loader_logo').slideUp(500); }
    });
    //if cl popup border
    $('.cl_popup_b').click(function () {
        if ($('#cl_popup_b1').is(':checked')) {
            $('.if_cl_popup_b').slideDown(500);
        } else { $('.if_cl_popup_b').slideUp(500); }
    });
    //if c popup border
    $('.c_popup_b').click(function () {
        if ($('#c_popup_b1').is(':checked')) {
            $('.if_c_popup_b').slideDown(500);
        } else { $('.if_c_popup_b').slideUp(500); }
    });
    //if m popup border
    $('.m_popup_b').click(function () {
        if ($('#m_popup_b1').is(':checked')) {
            $('.if_m_popup_b').slideDown(500);
        } else { $('.if_m_popup_b').slideUp(500); }
    });
    //if m bg
    $('.m_link_bgs').click(function () {
        if ($('#m_link_bgs1').is(':checked')) {
            $('.if_m_bg').slideDown(500);
        } else { $('.if_m_bg').slideUp(500); }
    });
    //if pl_filter_b
    $('.pl_filter_b').click(function () {
        if ($('#pl_filter_b1').is(':checked')) {
            $('.if_pl_filter_b').slideDown(500);
        } else { $('.if_pl_filter_b').slideUp(500); }
    });
    //if bl date
    $('.bl_dd').click(function () {
        if ($('#bl_dd1').is(':checked')) {
            $('.if_bl_dd').slideDown(500);
        } else { $('.if_bl_dd').slideUp(500); }
    });
    //if trah
    $('.header_trah').click(function () {
        if ($('#header_trah1').is(':checked')) {
            $('.if_trah').slideDown(500);
        } else { $('.if_trah').slideUp(500); }
    });
    //if trao
    $('.header_trao').click(function () {
        if ($('#header_trao1').is(':checked')) {
            $('.if_trao').slideDown(500);
        } else { $('.if_trao').slideUp(500); }
    });
    //if nav
    $('.header_lay').click(function () {
        if ($('#header_lay5').is(':checked') || $('#header_lay6').is(':checked')) {
            $('.if_nav').slideDown(500);
        } else { $('.if_nav').slideUp(500); }
    });
    //if nav
    $('.nc_pc_layout').click(function () {
        if ($('#nc_pc_layout2').is(':checked') || $('#nc_pc_layout3').is(':checked')) {
            $('.if_pc_layout23').slideDown(500);
        } else { $('.if_pc_layout23').slideUp(500); }
    });
    //if hover 4
    if ($('#nc_p_hover4').is(':checked')) {
        $('.if_hover4').slideDown(500);
    } else {
        $('.if_hover4').slideUp(500);
    }
    $('.nc_p_hover').click(function () {
        setTimeout(function () {
            if ($('#nc_p_hover4').is(':checked')) {
                $('.if_hover4').slideDown(500);
            } else {
                $('.if_hover4').slideUp(500);
            }
        }, 200);
    });
    //if container 4 then hover 4
    if ($('#nc_pc_layout4').is(':checked')) {
        $('#nc_p_hover4').prop("checked", true);
    }
    $('.nc_pc_layout').click(function () {
        setTimeout(function () {
            if ($('#nc_pc_layout4').is(':checked')) {
                $('#nc_p_hover4').prop("checked", true);
                $('.if_hover4').slideDown(500);
            }
        }, 200);
    });
    // sections start
    $('.body_box_sw').click(function () { if ($('#body_box_sw1').is(':checked')) { $('.if_body_box_bg').slideDown(500); } else { $('.if_body_box_bg').slideUp(500); } });
    $('.body_box_sw').click(function () { if ($('#body_box_sw2').is(':checked')) { $('.if_body_box_gr').slideDown(500); } else { $('.if_body_box_gr').slideUp(500); } });
    $('.body_box_sw').click(function () { if ($('#body_box_sw3').is(':checked')) { $('.if_body_box_im').slideDown(500); } else { $('.if_body_box_im').slideUp(500); } });
    $('.body_box_sw').click(function () { if ($('#body_box_sw4').is(':checked')) { $('.if_body_box_te').slideDown(500); } else { $('.if_body_box_te').slideUp(500); } });

    $('.g_hp1_sw').click(function () { if ($('#g_hp1_sw1').is(':checked')) { $('.if_g_hp1_bg').slideDown(500); } else { $('.if_g_hp1_bg').slideUp(500); } });
    $('.g_hp1_sw').click(function () { if ($('#g_hp1_sw2').is(':checked')) { $('.if_g_hp1_gr').slideDown(500); } else { $('.if_g_hp1_gr').slideUp(500); } });
    $('.g_hp1_sw').click(function () { if ($('#g_hp1_sw3').is(':checked')) { $('.if_g_hp1_im').slideDown(500); } else { $('.if_g_hp1_im').slideUp(500); } });
    $('.g_hp1_sw').click(function () { if ($('#g_hp1_sw4').is(':checked')) { $('.if_g_hp1_te').slideDown(500); } else { $('.if_g_hp1_te').slideUp(500); } });

    $('.g_hp2_sw').click(function () { if ($('#g_hp2_sw1').is(':checked')) { $('.if_g_hp2_bg').slideDown(500); } else { $('.if_g_hp2_bg').slideUp(500); } });
    $('.g_hp2_sw').click(function () { if ($('#g_hp2_sw2').is(':checked')) { $('.if_g_hp2_gr').slideDown(500); } else { $('.if_g_hp2_gr').slideUp(500); } });
    $('.g_hp2_sw').click(function () { if ($('#g_hp2_sw3').is(':checked')) { $('.if_g_hp2_im').slideDown(500); } else { $('.if_g_hp2_im').slideUp(500); } });
    $('.g_hp2_sw').click(function () { if ($('#g_hp2_sw4').is(':checked')) { $('.if_g_hp2_te').slideDown(500); } else { $('.if_g_hp2_te').slideUp(500); } });

    $('.g_hp3_sw').click(function () { if ($('#g_hp3_sw1').is(':checked')) { $('.if_g_hp3_bg').slideDown(500); } else { $('.if_g_hp3_bg').slideUp(500); } });
    $('.g_hp3_sw').click(function () { if ($('#g_hp3_sw2').is(':checked')) { $('.if_g_hp3_gr').slideDown(500); } else { $('.if_g_hp3_gr').slideUp(500); } });
    $('.g_hp3_sw').click(function () { if ($('#g_hp3_sw3').is(':checked')) { $('.if_g_hp3_im').slideDown(500); } else { $('.if_g_hp3_im').slideUp(500); } });
    $('.g_hp3_sw').click(function () { if ($('#g_hp3_sw4').is(':checked')) { $('.if_g_hp3_te').slideDown(500); } else { $('.if_g_hp3_te').slideUp(500); } });

    $('.g_hp4_sw').click(function () { if ($('#g_hp4_sw1').is(':checked')) { $('.if_g_hp4_bg').slideDown(500); } else { $('.if_g_hp4_bg').slideUp(500); } });
    $('.g_hp4_sw').click(function () { if ($('#g_hp4_sw2').is(':checked')) { $('.if_g_hp4_gr').slideDown(500); } else { $('.if_g_hp4_gr').slideUp(500); } });
    $('.g_hp4_sw').click(function () { if ($('#g_hp4_sw3').is(':checked')) { $('.if_g_hp4_im').slideDown(500); } else { $('.if_g_hp4_im').slideUp(500); } });
    $('.g_hp4_sw').click(function () { if ($('#g_hp4_sw4').is(':checked')) { $('.if_g_hp4_te').slideDown(500); } else { $('.if_g_hp4_te').slideUp(500); } });

    $('.g_hp5_sw').click(function () { if ($('#g_hp5_sw1').is(':checked')) { $('.if_g_hp5_bg').slideDown(500); } else { $('.if_g_hp5_bg').slideUp(500); } });
    $('.g_hp5_sw').click(function () { if ($('#g_hp5_sw2').is(':checked')) { $('.if_g_hp5_gr').slideDown(500); } else { $('.if_g_hp5_gr').slideUp(500); } });
    $('.g_hp5_sw').click(function () { if ($('#g_hp5_sw3').is(':checked')) { $('.if_g_hp5_im').slideDown(500); } else { $('.if_g_hp5_im').slideUp(500); } });
    $('.g_hp5_sw').click(function () { if ($('#g_hp5_sw4').is(':checked')) { $('.if_g_hp5_te').slideDown(500); } else { $('.if_g_hp5_te').slideUp(500); } });

    $('.g_hp6_sw').click(function () { if ($('#g_hp6_sw1').is(':checked')) { $('.if_g_hp6_bg').slideDown(500); } else { $('.if_g_hp6_bg').slideUp(500); } });
    $('.g_hp6_sw').click(function () { if ($('#g_hp6_sw2').is(':checked')) { $('.if_g_hp6_gr').slideDown(500); } else { $('.if_g_hp6_gr').slideUp(500); } });
    $('.g_hp6_sw').click(function () { if ($('#g_hp6_sw3').is(':checked')) { $('.if_g_hp6_im').slideDown(500); } else { $('.if_g_hp6_im').slideUp(500); } });
    $('.g_hp6_sw').click(function () { if ($('#g_hp6_sw4').is(':checked')) { $('.if_g_hp6_te').slideDown(500); } else { $('.if_g_hp6_te').slideUp(500); } });

    $('.g_fs1_sw').click(function () { if ($('#g_fs1_sw1').is(':checked')) { $('.if_g_fs1_bg').slideDown(500); } else { $('.if_g_fs1_bg').slideUp(500); } });
    $('.g_fs1_sw').click(function () { if ($('#g_fs1_sw2').is(':checked')) { $('.if_g_fs1_gr').slideDown(500); } else { $('.if_g_fs1_gr').slideUp(500); } });
    $('.g_fs1_sw').click(function () { if ($('#g_fs1_sw3').is(':checked')) { $('.if_g_fs1_im').slideDown(500); } else { $('.if_g_fs1_im').slideUp(500); } });
    $('.g_fs1_sw').click(function () { if ($('#g_fs1_sw4').is(':checked')) { $('.if_g_fs1_te').slideDown(500); } else { $('.if_g_fs1_te').slideUp(500); } });

    $('.g_fs2_sw').click(function () { if ($('#g_fs2_sw1').is(':checked')) { $('.if_g_fs2_bg').slideDown(500); } else { $('.if_g_fs2_bg').slideUp(500); } });
    $('.g_fs2_sw').click(function () { if ($('#g_fs2_sw2').is(':checked')) { $('.if_g_fs2_gr').slideDown(500); } else { $('.if_g_fs2_gr').slideUp(500); } });
    $('.g_fs2_sw').click(function () { if ($('#g_fs2_sw3').is(':checked')) { $('.if_g_fs2_im').slideDown(500); } else { $('.if_g_fs2_im').slideUp(500); } });
    $('.g_fs2_sw').click(function () { if ($('#g_fs2_sw4').is(':checked')) { $('.if_g_fs2_te').slideDown(500); } else { $('.if_g_fs2_te').slideUp(500); } });

    $('.g_fs3_sw').click(function () { if ($('#g_fs3_sw1').is(':checked')) { $('.if_g_fs3_bg').slideDown(500); } else { $('.if_g_fs3_bg').slideUp(500); } });
    $('.g_fs3_sw').click(function () { if ($('#g_fs3_sw2').is(':checked')) { $('.if_g_fs3_gr').slideDown(500); } else { $('.if_g_fs3_gr').slideUp(500); } });
    $('.g_fs3_sw').click(function () { if ($('#g_fs3_sw3').is(':checked')) { $('.if_g_fs3_im').slideDown(500); } else { $('.if_g_fs3_im').slideUp(500); } });
    $('.g_fs3_sw').click(function () { if ($('#g_fs3_sw4').is(':checked')) { $('.if_g_fs3_te').slideDown(500); } else { $('.if_g_fs3_te').slideUp(500); } });

    $('.nc_header_bg').click(function () { if ($('#nc_header_bg1').is(':checked')) { $('.if_nc_header_bc').slideDown(500); } else { $('.if_nc_header_bc').slideUp(500); } });
    $('.nc_header_bg').click(function () { if ($('#nc_header_bg2').is(':checked')) { $('.if_nc_header_gr').slideDown(500); } else { $('.if_nc_header_gr').slideUp(500); } });
    $('.nc_header_bg').click(function () { if ($('#nc_header_bg3').is(':checked')) { $('.if_nc_header_im').slideDown(500); } else { $('.if_nc_header_im').slideUp(500); } });
    $('.nc_header_bg').click(function () { if ($('#nc_header_bg4').is(':checked')) { $('.if_nc_header_te').slideDown(500); } else { $('.if_nc_header_te').slideUp(500); } });

    $('.nc_main_bg').click(function () { if ($('#nc_main_bg1').is(':checked')) { $('.if_nc_main_bc').slideDown(500); } else { $('.if_nc_main_bc').slideUp(500); } });
    $('.nc_main_bg').click(function () { if ($('#nc_main_bg2').is(':checked')) { $('.if_nc_main_gr').slideDown(500); } else { $('.if_nc_main_gr').slideUp(500); } });
    $('.nc_main_bg').click(function () { if ($('#nc_main_bg3').is(':checked')) { $('.if_nc_main_im').slideDown(500); } else { $('.if_nc_main_im').slideUp(500); } });
    $('.nc_main_bg').click(function () { if ($('#nc_main_bg4').is(':checked')) { $('.if_nc_main_te').slideDown(500); } else { $('.if_nc_main_te').slideUp(500); } });

    $('.g_op2_sw').click(function () { if ($('#g_op2_sw1').is(':checked')) { $('.if_g_op2_bg').slideDown(500); } else { $('.if_g_op2_bg').slideUp(500); } });
    $('.g_op2_sw').click(function () { if ($('#g_op2_sw2').is(':checked')) { $('.if_g_op2_gr').slideDown(500); } else { $('.if_g_op2_gr').slideUp(500); } });
    $('.g_op2_sw').click(function () { if ($('#g_op2_sw3').is(':checked')) { $('.if_g_op2_im').slideDown(500); } else { $('.if_g_op2_im').slideUp(500); } });
    $('.g_op2_sw').click(function () { if ($('#g_op2_sw4').is(':checked')) { $('.if_g_op2_te').slideDown(500); } else { $('.if_g_op2_te').slideUp(500); } });

    $('.g_mc_sw').click(function () { if ($('#g_mc_sw1').is(':checked')) { $('.if_g_mc_bg').slideDown(500); } else { $('.if_g_mc_bg').slideUp(500); } });
    $('.g_mc_sw').click(function () { if ($('#g_mc_sw2').is(':checked')) { $('.if_g_mc_gr').slideDown(500); } else { $('.if_g_mc_gr').slideUp(500); } });
    $('.g_mc_sw').click(function () { if ($('#g_mc_sw3').is(':checked')) { $('.if_g_mc_im').slideDown(500); } else { $('.if_g_mc_im').slideUp(500); } });
    $('.g_mc_sw').click(function () { if ($('#g_mc_sw4').is(':checked')) { $('.if_g_mc_te').slideDown(500); } else { $('.if_g_mc_te').slideUp(500); } });

    $('.g_bc_sw').click(function () { if ($('#g_bc_sw1').is(':checked')) { $('.if_g_bc_bg').slideDown(500); } else { $('.if_g_bc_bg').slideUp(500); } });
    $('.g_bc_sw').click(function () { if ($('#g_bc_sw2').is(':checked')) { $('.if_g_bc_gr').slideDown(500); } else { $('.if_g_bc_gr').slideUp(500); } });
    $('.g_bc_sw').click(function () { if ($('#g_bc_sw3').is(':checked')) { $('.if_g_bc_im').slideDown(500); } else { $('.if_g_bc_im').slideUp(500); } });
    $('.g_bc_sw').click(function () { if ($('#g_bc_sw4').is(':checked')) { $('.if_g_bc_te').slideDown(500); } else { $('.if_g_bc_te').slideUp(500); } });

    $('.g_pro_w').click(function () { if ($('#g_pro_w2').is(':checked')) { $('.if_pa_pro').slideDown(500); } else { $('.if_pa_pro').slideUp(500); } });
    $('.g_mini_w').click(function () { if ($('#g_mini_w2').is(':checked')) { $('.if_pa_mini').slideDown(500); } else { $('.if_pa_mini').slideUp(500); } });
    $('.g_info_w').click(function () { if ($('#g_info_w2').is(':checked')) { $('.if_pa_info').slideDown(500); } else { $('.if_pa_info').slideUp(500); } });
    $('.g_bra_w').click(function () { if ($('#g_bra_w2').is(':checked')) { $('.if_pa_bra').slideDown(500); } else { $('.if_pa_bra').slideUp(500); } });
    $('.g_blog_w').click(function () { if ($('#g_blog_w2').is(':checked')) { $('.if_pa_blog').slideDown(500); } else { $('.if_pa_blog').slideUp(500); } });
    $('.ban_wid_top').click(function () { if ($('#ban_wid_top2').is(':checked')) { $('.if_pa_ban_top').slideDown(500); } else { $('.if_pa_ban_top').slideUp(500); } });
    $('.ban_wid_pro').click(function () { if ($('#ban_wid_pro2').is(':checked')) { $('.if_pa_ban_pro').slideDown(500); } else { $('.if_pa_ban_pro').slideUp(500); } });
    $('.ban_wid_mini').click(function () { if ($('#ban_wid_mini2').is(':checked')) { $('.if_pa_ban_mini').slideDown(500); } else { $('.if_pa_ban_mini').slideUp(500); } });
    $('.ban_wid_info').click(function () { if ($('#ban_wid_info2').is(':checked')) { $('.if_pa_ban_info').slideDown(500); } else { $('.if_pa_ban_info').slideUp(500); } });
    $('.ban_wid_bra').click(function () { if ($('#ban_wid_bra2').is(':checked')) { $('.if_pa_ban_bra').slideDown(500); } else { $('.if_pa_ban_bra').slideUp(500); } });
    $('.ban_wid_home').click(function () { if ($('#ban_wid_home2').is(':checked')) { $('.if_pa_ban_home').slideDown(500); } else { $('.if_pa_ban_home').slideUp(500); } });
    $('.ban_wid_foot').click(function () { if ($('#ban_wid_foot2').is(':checked')) { $('.if_pa_ban_foot').slideDown(500); } else { $('.if_pa_ban_foot').slideUp(500); } });
    $('.ban_wid_foott').click(function () { if ($('#ban_wid_foott2').is(':checked')) { $('.if_pa_ban_foott').slideDown(500); } else { $('.if_pa_ban_foott').slideUp(500); } });
    $('.ban_wid_footb').click(function () { if ($('#ban_wid_footb2').is(':checked')) { $('.if_pa_ban_footb').slideDown(500); } else { $('.if_pa_ban_footb').slideUp(500); } });
    $('.ban_wid_s1').click(function () { if ($('#ban_wid_s12').is(':checked')) { $('.if_pa_ban_s1').slideDown(500); } else { $('.if_pa_ban_s1').slideUp(500); } });
    $('.ban_wid_s2').click(function () { if ($('#ban_wid_s22').is(':checked')) { $('.if_pa_ban_s2').slideDown(500); } else { $('.if_pa_ban_s2').slideUp(500); } });
    $('.ban_wid_s3').click(function () { if ($('#ban_wid_s32').is(':checked')) { $('.if_pa_ban_s3').slideDown(500); } else { $('.if_pa_ban_s3').slideUp(500); } });

    $('.nc_m_under').click(function () { if ($('#nc_m_under1').is(':checked')) { $('.if_under').slideDown(500); } else { $('.if_under').slideUp(500); } });
    $('.nc_pp_image').click(function () { if ($('#nc_pp_image3').is(':checked')) { $('.if_pp_image3').slideDown(500); } else { $('.if_pp_image3').slideUp(500); } });

});
