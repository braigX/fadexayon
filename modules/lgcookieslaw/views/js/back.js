/**
 * Copyright 2024 LÍNEA GRÁFICA E.C.E S.L.
 *
 * @author    Línea Gráfica E.C.E. S.L.
 * @copyright Lineagrafica.es - Línea Gráfica E.C.E. S.L. all rights reserved.
 * @license   https://www.apache.org/licenses/LICENSE-2.0
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

window.addEventListener('load', function() {
    var object_lgcookieslaw_back = new LGCookiesLawBack();

    object_lgcookieslaw_back.init();
});

function LGCookiesLawBack() {
    var self = this;

    var elements_selectors;
    var back_urls;
    var help_tabs;
    var current_url;

    this.init = function() {
        self.initBackUrls();
        self.initHelpTabs();
        self.initElements();
        self.initEvents();
    };

    this.initBackUrls = function() {
        self.back_urls = {
            admin_modules: currentIndex,
        };
    };

    this.initHelpTabs = function() {
        self.help_tabs = [
            'installation',
            'configuration',
            'general_settings',
            'banner_settings',
            'button_settings',
            'purposes',
            'cookies',
            'troubleshooting',
        ];
    };

    this.initElements = function() {
        self.initElementsSelectors();

        self.closeModuleAlerts();

        self.initLGSwapContainers();

        self.toggleFields('technical', 'switch');
        self.toggleFields('install_script', 'switch');
        self.toggleFields('consent_mode', 'switch');
        self.toggleFields('PS_LGCOOKIES_TESTMODE', 'switch');
        self.toggleFields('PS_LGCOOKIES_SHOW_CLOSE_BTN', 'switch');
        self.toggleFields('PS_LGCOOKIES_SHOW_FIXED_BTN', 'switch');
        self.toggleFields('PS_LGCOOKIES_SHOW_RJCT_BTN', 'switch');
        self.toggleFields('PS_LGCOOKIES_SAVE_USER_CONSENT', 'switch');
        self.toggleFields('PS_LGCOOKIES_CONSENT_MODE', 'switch');

        if ($(self.elements_selectors.external_module_notice).length) {
            if ($(self.elements_selectors.page_title).length) {
                $(self.elements_selectors.page_title).closest('.bootstrap').after($(self.elements_selectors.external_module_notice).detach());
            }

            $(self.elements_selectors.external_module_notice).removeClass('hide');
        }

        self.checkUrl();
    };

    this.initElementsSelectors = function() {
        self.elements_selectors = {
            tabs_container: '.lgmodule-container-help .tabs-container',
            help_tab: '.lgmodule-container-help .tabs-container label',
            module_alert: '.lgmodule-alert',
            lgswap_container: '.lgswap-container',
            lgswap_add: '.addLGSwap',
            lgswap_remove: '.removeLGSwap',
            lgswap_selected: '.selectedLGSwap',
            lgswap_selected_option: '.selectedLGSwap option',
            lgswap_available: '.availableLGSwap',
            lgswap_available_option: '.availableLGSwap option',
            lgcopy_input: '.lgcopy-input',
            lgip_button: '.lgip-button',
            lgbanner: '#lgbanner',
            lgbanner_image: '.lgbanner-image',
            lgbanner_image_selected: 'lgbanner-image-selected',
            button_submit: 'button:submit',
            technical: 'input:radio[name=technical]',
            install_script: 'input:radio[name=install_script]',
            consent_mode: 'input:radio[name=consent_mode]',
            ps_lgcookieslaw_testmode: 'input:radio[name=PS_LGCOOKIES_TESTMODE]',
            ps_lgcookieslaw_show_close_button: 'input:radio[name=PS_LGCOOKIES_SHOW_CLOSE_BTN]',
            ps_lgcookieslaw_show_fixed_button: 'input:radio[name=PS_LGCOOKIES_SHOW_FIXED_BTN]',
            ps_lgcookieslaw_show_reject_button: 'input:radio[name=PS_LGCOOKIES_SHOW_RJCT_BTN]',
            ps_lgcookies_save_user_consent: 'input:radio[name=PS_LGCOOKIES_SAVE_USER_CONSENT]',
            ps_lgcookies_consent_mode: 'input:radio[name=PS_LGCOOKIES_CONSENT_MODE]',
            external_module_notice: '#lgcookieslaw_external_module_notice',
            external_module_notice_hide_button: '#lgcookieslaw_external_module_notice_hide_button',
            external_module_notice_close_button: '#lgcookieslaw_external_module_notice_close_button',
            page_title: '#content .bootstrap > .page-head h2.page-title',
        };
    };

    this.initEvents = function() {
        $(self.elements_selectors.technical).change(function() {
            self.toggleFields('technical', 'switch');
        });

        $(self.elements_selectors.install_script).change(function() {
            self.toggleFields('install_script', 'switch');
        });

        $(self.elements_selectors.consent_mode).change(function() {
            self.toggleFields('consent_mode', 'switch');
        });

        $(self.elements_selectors.ps_lgcookieslaw_testmode).change(function() {
            self.toggleFields('PS_LGCOOKIES_TESTMODE', 'switch');
        });

        $(self.elements_selectors.ps_lgcookieslaw_show_close_button).change(function() {
            self.toggleFields('PS_LGCOOKIES_SHOW_CLOSE_BTN', 'switch');
        });

        $(self.elements_selectors.ps_lgcookieslaw_show_fixed_button).change(function() {
            self.toggleFields('PS_LGCOOKIES_SHOW_FIXED_BTN', 'switch');
        });

        $(self.elements_selectors.ps_lgcookieslaw_show_reject_button).change(function() {
            self.toggleFields('PS_LGCOOKIES_SHOW_RJCT_BTN', 'switch');
        });

        $(self.elements_selectors.ps_lgcookies_save_user_consent).change(function() {
            self.toggleFields('PS_LGCOOKIES_SAVE_USER_CONSENT', 'switch');
        });

        $(self.elements_selectors.ps_lgcookies_consent_mode).change(function() {
            self.toggleFields('PS_LGCOOKIES_CONSENT_MODE', 'switch');
        });

        $(self.elements_selectors.external_module_notice_hide_button).click(function(e) {
            e.preventDefault();

            var external_module_name = $(this).data('external-module-name');

            self.hideExternalModuleNotice(external_module_name);
        });

        $(self.elements_selectors.help_tab).click(function() {
            $(self.elements_selectors.help_tab).removeClass();

            $(this).addClass('active-tab');
        });

        self.changeImageLGBanner('PS_LGCOOKIES_BANNER_POSITION');

        self.copyLGClipboard();
        self.copyLGIP();
    };

    this.hideExternalModuleNotice = function(external_module_name) {
        var success = external_module_name;

        if (success) {
            $.ajax({
                type: 'POST',
                headers: {
                    'cache-control': 'no-cache',
                },
                url: self.back_urls.admin_modules + '?rand=' + new Date().getTime(),
                async: false,
                cache: false,
                dataType: 'json',
                data: {
                    controller: 'AdminModules',
                    module_name: 'lgcookieslaw',
                    configure: 'lgcookieslaw',
                    action: 'hideExternalModuleNotice',
                    ajax: true,
                    token: token,
                    external_module_name: external_module_name,
                },
                success: function(data) {
                    success = data.status;

                    if (data.status) {
                        showSuccessMessage(data.message);

                        $(self.elements_selectors.external_module_notice_close_button).click();
                    } else {
                        showErrorMessage(data.errors);
                    }
                },
            });
        }

        return success;
    };

    this.toggleFields = function(field_name, field_type) {
        if (field_name) {
            if (field_type == 'switch') {
                var field_name_is_enabled = $('#' + field_name + '_on').is(':checked');

                $('.form-group').each(function() {
                    if ($(this).find('.toggle_' + field_name + '_on').length > 0) {
                        if (!$(this).hasClass('translatable-field')) {
                            if (field_name_is_enabled) {
                                $(this).removeClass('lg-tab-inactive-field').addClass('lg-tab-active-field').slideDown();
                            } else {
                                $(this).removeClass('lg-tab-active-field').addClass('lg-tab-inactive-field').slideUp();
                            }
                        } else {
                            id_language = typeof id_language == 'undefined' ? 1 : id_language;

                            if ($(this).hasClass('lang-' + id_language)) {
                                if (field_name_is_enabled) {
                                    $(this).removeClass('lg-tab-inactive-field').addClass('lg-tab-active-field').slideDown();
                                } else {
                                    $(this).removeClass('lg-tab-active-field').addClass('lg-tab-inactive-field').slideUp();
                                }
                            }
                        }
                    }

                    if ($(this).find('.toggle_' + field_name + '_off').length > 0) {
                        if (!$(this).hasClass('translatable-field')) {
                            if (field_name_is_enabled) {
                                $(this).removeClass('lg-tab-active-field').addClass('lg-tab-inactive-field').slideUp();
                            } else {
                                $(this).removeClass('lg-tab-inactive-field').addClass('lg-tab-active-field').slideDown();
                            }
                        } else {
                            id_language = typeof id_language == 'undefined' ? 1 : id_language;

                            if ($(this).hasClass('lang-' + id_language)) {
                                if (field_name_is_enabled) {
                                    $(this).removeClass('lg-tab-active-field').addClass('lg-tab-inactive-field').slideUp();
                                } else {
                                    $(this).removeClass('lg-tab-inactive-field').addClass('lg-tab-active-field').slideDown();
                                }
                            }
                        }
                    }
                });
            } else if (field_type == 'select') {
                var field_name_option_select = $('#' + field_name).val();

                $('.form-group').each(function() {
                    if ($(this).find('.toggle_' + field_name).length > 0) {
                        if (!$(this).hasClass('translatable-field')) {
                            if ($(this).find('.toggle_selected_value_' + field_name_option_select).length > 0) {
                                $(this).removeClass('lg-tab-inactive-field').addClass('lg-tab-active-field').slideDown();
                            } else {
                                $(this).removeClass('lg-tab-active-field').addClass('lg-tab-inactive-field').slideUp();
                            }
                        } else {
                            id_language = typeof id_language == 'undefined' ? 1 : id_language;

                            if ($(this).hasClass('lang-' + id_language)) {
                                if ($(this).find('.toggle_selected_value_' + field_name_option_select).length > 0) {
                                    $(this).removeClass('lg-tab-inactive-field').addClass('lg-tab-active-field').slideDown();
                                } else {
                                    $(this).removeClass('lg-tab-active-field').addClass('lg-tab-inactive-field').slideUp();
                                }
                            }
                        }
                    }
                });
            }
        }
    };

    this.checkUrl = function() {
        self.current_url = window.location + '';

        if (self.current_url.indexOf('tab_lg=help') != -1 && self.current_url.indexOf('help_tab=') != -1) {
            var help_tab = self.current_url.substring(self.current_url.indexOf('help_tab=') + 9, self.current_url.length);

            if ($.inArray(help_tab, self.help_tabs)) {
                $('#' + help_tab).prop('checked', true);

                $(self.elements_selectors.help_tab).removeClass();

                $(self.elements_selectors.help_tab + '[for=' + help_tab + ']').addClass('active-tab');
            }
        }
    };

    this.initLGSwapContainers = function() {};

    this.closeModuleAlerts = function() {
        if ($(self.elements_selectors.module_alert).length) {
            $(self.elements_selectors.module_alert).fadeOut(6000);
        }
    };

    this.bindLGSwapSave = function(context) {
        if ($(self.elements_selectors.lgswap_selected_option, context).length !== 0) {
            $(self.elements_selectors.lgswap_selected_option, context).attr('selected', 'selected');
        } else {
            $(self.elements_selectors.lgswap_available_option, context).attr('selected', 'selected');
        }
    };

    this.bindLGSwapButton = function(prefix_button, prefix_select_remove, prefix_select_add, context) {
        $('.' + prefix_button + 'LGSwap', context).on('click', function(e) {
            e.preventDefault();

            $('.' + prefix_select_remove + 'LGSwap option:selected', context).each(function() {
                $('.' + prefix_select_add + 'LGSwap', context).append('<option value="' + $(this).val() + '">' + $(this).text() + '</option>');

                $(this).remove();
            });

            $(self.elements_selectors.lgswap_selected_option, context).prop('selected', true);
        });
    };

    this.copyLGClipboard = function() {
        $(self.elements_selectors.lgcopy_input).on('click', function(e) {
            e.preventDefault();

            var id_input = $(this).closest('.input-group').find('input[type=text]').attr('id');
            var input = document.getElementById(id_input);

            input.select();
            input.setSelectionRange(0, 99999);

            if (document.execCommand('copy')) {
                showSuccessMessage(lgcookieslaw_translates_copy_success_message);
            } else {
                showErrorMessage(lgcookieslaw_translates_copy_error_message);
            }
        });
    };

    this.copyLGIP = function() {
        $(self.elements_selectors.lgip_button).on('click', function(e) {
            e.preventDefault();

            var current_ip = $(this).data('ip');
            var id_destination_input = $(this).data('destination-input');

            if ($('#' + id_destination_input).val(current_ip)) {
                showSuccessMessage(lgcookieslaw_translates_copy_success_message);
            } else {
                showErrorMessage(lgcookieslaw_translates_copy_error_message);
            }
        });
    };

    this.changeImageLGBanner = function(change_element) {
        $('#' + change_element).on('change', function() {
            if ($(self.elements_selectors.lgbanner + '_' + change_element + ' ' + self.elements_selectors.lgbanner_image).length) {
                $(self.elements_selectors.lgbanner + '_' + change_element + ' ' + self.elements_selectors.lgbanner_image).removeClass(self.elements_selectors.lgbanner_image_selected);

                $(self.elements_selectors.lgbanner + '_' + change_element + ' ' + self.elements_selectors.lgbanner_image + '-' + $(this).val()).addClass(self.elements_selectors.lgbanner_image_selected);
            }
        });
    };
}
