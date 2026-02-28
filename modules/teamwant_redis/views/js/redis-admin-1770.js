/*
 * Redis Cache
 * Version: 2.1.1
 * Copyright (c) 2020-2022. Mateusz Szymański Teamwant
 * https://teamwant.pl
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @author    Teamwant <kontakt@teamwant.pl>
 * @copyright Copyright 2020-2023 © Teamwant Mateusz Szymański All right reserved
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * @category  Teamwant
 * @package   Teamwant
 */

window.onload = function () {
    if ($('[name="form[caching][caching_system]"]').length) {
        if ($('[name="form[caching][caching_system]"]:checked').val() == 'Redis') {
            getConfigurationTable($('[name="form[caching][caching_system]"]:checked'))
        }

        $('[name="form[caching][caching_system]"]').change(function () {
            removeContentInPerformanceTab('[name="form[caching][caching_system]"][value="Redis"]')

            if ($(this).val() == 'Redis') {
                getConfigurationTable($(this))
            }
        })
    }

    $('body').on('click', '#addNextAdminRedisConfigurationRow', function (e) {
        e.preventDefault();
        addNextAdminRedisConfigurationRow($(this));
    })

    $('body').on('click', '#saveAdminRedisConfigurationRow', function (e) {
        e.preventDefault();
        saveAdminRedisConfigurationRow($(this));
    })

    $('body').on('click', '[data-action="testAdminRedisConfigurationHost"]', function (e) {
        e.preventDefault();
        testAdminRedisConfigurationHost($(this));
    })

    $('body').on('click', '[data-action="removeAdminRedisConfigurationRow"]', function (e) {
        e.preventDefault();
        let tr =  $(this).closest('tr');
        tr.prev().prev().remove();
        tr.prev().remove();
        tr.next().remove();
        tr.remove();
    })

    $('body').on('click', '[data-action="adminRedisShowMore"]', function (e) {
        e.preventDefault();
        let element = $(this).closest('tr').next();
        if (element.hasClass('showmorerow') == true){
            element.removeClass('showmorerow');
            $(this).text($(this).data('textstart'));
        }else {
            element.addClass('showmorerow');
            $(this).text($(this).data('textstop'));
        }
    })
};

function removeContentInPerformanceTab(input) {
    $(input).parent().parent().find('.html').remove();
}

function getConfigurationTable(input) {
    $(".card-footer .btn-primary").off('click');

    let postdata = {
        ajax: 1,
        controller: 'AdminRedisConfiguration',
        action: 'getConfigurationTable',
        token: token_AdminRedisConfiguration,
    };

    $.ajax({
        type: 'GET',
        url: 'index.php',
        dataType: "json",
        data: postdata,
        success: function (r) {
            if (r.success == 0 && r.type == 'alert') {
                alert(r.data)
                removeContentInPerformanceTab(input);
                return;
            }

            if (r.success == 0 && r.type == 'html') {
                $(input).closest('.form-group').append(
                    $('<div class="html teamwant_redis_table_container"></div>').append(
                        $(r.data)
                    )
                );
            }

            if (r.success == 1) {
                $(input).closest('.form-group').append(
                    $('<div class="html teamwant_redis_table_container"></div>').append(
                        $(r.data)
                    )
                );
            }

            preventUnsaveRedisConfiguration();
        }
    });
}

function addNextAdminRedisConfigurationRow(input) {
    let postdata = {
        ajax: 1,
        controller: 'AdminRedisConfiguration',
        action: 'getConfigurationTableRow',
        token: token_AdminRedisConfiguration,
    };

    $.ajax({
        type: 'GET',
        url: 'index.php',
        dataType: "json",
        data: postdata,
        success: function (r) {
            if (r.success == 0 && r.type == 'alert') {
                alert(r.data)
                return;
            }

            if (r.success == 1) {
                $(input).closest('tr').before(
                    $(r.data)
                );
            }
        }
    });
}

function saveAdminRedisConfigurationRow(input) {
    input.find('.loader').show();

    let postdata = {
        ajax: 1,
        controller: 'AdminRedisConfiguration',
        action: 'saveConfigurationTable',
        token: token_AdminRedisConfiguration,
        data: input.closest('form').serialize()
    };

    //uzupelniamy brakujace pola na requestach
    let usedFields = {}
    input.closest('form').serializeArray().forEach(function(a, b) {
        usedFields[a.name] = 1;
    })

    if (!usedFields['form[twredis][use_cache_admin]']) {
        postdata.data += '&' + 'form[twredis][use_cache_admin]' + '=' + 0;
    }

    if (!usedFields['form[twredis][use_prefix]']) {
        postdata.data += '&' + 'form[twredis][use_prefix]' + '=' + 0;
    }

    if (!usedFields['form[twredis][use_multistore]']) {
        postdata.data += '&' + 'form[twredis][use_multistore]' + '=' + 0;
    }

    $.ajax({
        type: 'POST',
        url: 'index.php',
        dataType: "json",
        data: postdata,
        complete: function () {
            input.find('.loader').hide();
        },
        success: function (r) {
            if (r.success == 0 && r.type == 'alert') {
                let stopProcess = 0;
                alert(r.data)

                if (r.stopProcess) {
                    stopProcess = 1;
                    input.find('.loader').hide();
                }

                if (!stopProcess) {
                    $(input).closest('.form-group').find('.html.teamwant_redis_table_container').remove();
                }
                return;
            }

            if (r.success == 0 && r.type == 'html') {
                $(input).closest('.form-group').find('.html.teamwant_redis_table_container').html(
                    $(r.data)
                );
            }

            if (r.success == 1) {
                $(input).closest('.form-group').find('.html.teamwant_redis_table_container').html(
                    $(r.data)
                );
            }
        }
    });
}

function testAdminRedisConfigurationHost(input) {
    let table_tr = input.closest('tr');
    let table_inputs = table_tr.find('input');
    let table_inputs2 = table_tr.next().next().find('input');

    input.find('.loader').show();

    let postdata = {
        ajax: 1,
        controller: 'AdminRedisConfiguration',
        action: 'testAdminRedisConfigurationHost',
        token: token_AdminRedisConfiguration,
        data: {
            'scheme': table_inputs[0].value,
            'host': table_inputs[1].value,
            'port': table_inputs[2].value,
            'alias': table_inputs2[0].value,
            'username': table_inputs2[1].value,
            'password': table_inputs2[2].value,
            'database': table_inputs2[3].value
        }
    };

    $.ajax({
        type: 'POST',
        url: 'index.php',
        dataType: "json",
        data: postdata,
        success: function (r) {
            alert(r.data)
            input.find('.loader').hide();
        }
    });
}

function preventUnsaveRedisConfiguration() {
    if (
        $(".teamwant-redis-table input").length
        && $(".card-footer .btn-primary").length
        && $("#saveAdminRedisConfigurationRow").length
    ) {
        let anyChangeInRedis = 0;

        $(".teamwant-redis-table input").on("change", function () {
            anyChangeInRedis = 1;
        });

        $("#twredis_prefix").on("change", function () {
            anyChangeInRedis = 1;
        });

        $(
            "#form_twredis_use_cache_admin_0, #form_twredis_use_cache_admin_1" +
            ", #form_twredis_use_prefix_0, #form_twredis_use_prefix_1" +
            ", #form_twredis_use_multistore_0, #form_twredis_use_multistore_1"
        ).on("click", function () {
            anyChangeInRedis = 1;
        });

        $(".card-footer .btn-primary").click(function (e) {
            if (anyChangeInRedis) {
                var c = confirm(
                    tw_redis_lang_save_change_on_performance
                );
                if (!c) {
                    e.preventDefault();
                    return;
                }
            }
        });

        $("#saveAdminRedisConfigurationRow").click(function (e) {
            anyChangeInRedis = 0;
        });
    }
}