/**
 * Copyright 2022 LÍNEA GRÁFICA E.C.E S.L.
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
$(document).ready( function() {
    if ($('#lgcanonicalurls_force_http_https_off').prop('checked') == true) {
        $('#lgcanonicalurls_force_http_https_value_http').prop('disabled', true);
        $('#lgcanonicalurls_force_http_https_value_https').prop('disabled', true);
        $('input[name^="lgcanonicalurls_force_http_https_value"]:eq(0)').closest('div.margin-form').slideUp();
    }

    if ($('#lgcanonicalurls_force_http_https_on').prop('checked') == true) {
        $('#lgcanonicalurls_force_http_https_value_http').prop('disabled', false);
        $('#lgcanonicalurls_force_http_https_value_https').prop('disabled', false);
        $('input[name^="lgcanonicalurls_force_http_https_value"]:eq(0)').closest('div.margin-form').slideDown();
    }

    if ($('#lgcanonicalurls_ignoreparams_off').prop('checked') == true) {
        $('#lgcanonicalurls_params').prop('disabled', true);
        $('#lgcanonicalurls_params').closest('div.margin-form').slideUp();
    }

    if ($('#lgcanonicalurls_ignoreparams_on').prop('checked') == true) {
        $('#lgcanonicalurls_params').prop('disabled', false);
        $('#lgcanonicalurls_params').closest('div.margin-form').slideDown();
    }

    if ($('#lgcanonicalurls_canonicalhome_off').prop('checked') == true) {
        $('#lgcanonicalurls_canonicalhome_type_default').prop('disabled', true);
        $('#lgcanonicalurls_canonicalhome_type_custom').prop('disabled', true);
        $('#lgcanonicalurls_canonicalhome_type_default').closest('div.margin-form').slideUp();
        $('input[name^="LGCANONICALURLS_CANHOME_TEXT"]:nth-child(1)')
            .closest('div.margin-form')
            .parent()
            .closest('div.margin-form').slideUp();
    }

    if ($('#lgcanonicalurls_canonicalhome_on').prop('checked') == true) {
        $('#lgcanonicalurls_canonicalhome_type_default').prop('disabled', false);
        $('#lgcanonicalurls_canonicalhome_type_custom').prop('disabled', false);
        $('#lgcanonicalurls_canonicalhome_type_default').closest('div.margin-form').slideDown();
        $('input[name^="LGCANONICALURLS_CANHOME_TEXT"]:nth-child(1)')
            .closest('div.margin-form')
            .parent()
            .closest('div.margin-form').slideDown();
    }

    if ($('#lgcanonicalurls_hreflang_off').prop('checked') == true) {
        $('input[name^="lgcanonicalurls_region_code"]:eq(0)').closest('div.margin-form').slideUp();
        $('#lgcanonicalurls_lang_default').prop('disabled', true);
        $('select[name^="lgcanonicalurls_lang_default"]:first').closest('div.margin-form').slideUp();
    }

    if ($('#lgcanonicalurls_hreflang_on').prop('checked') == true) {
        $('input[name="lgcanonicalurls_region_code"]:first').closest('div.margin-form').slideDown();
        $('#lgcanonicalurls_lang_default').prop('disabled', false);
        $('select[name^="lgcanonicalurls_lang_default"]:first').closest('div.margin-form').slideDown();
    }

    $(document).on('change', '#lgcanonicalurls_force_http_https_off', function() {
        $('#lgcanonicalurls_force_http_https_value_http').prop('disabled', true);
        $('#lgcanonicalurls_force_http_https_value_https').prop('disabled', true);
        $('input[name^="lgcanonicalurls_force_http_https_value"]:eq(0)').closest('div.margin-form').slideUp();
    });

    $(document).on('change', '#lgcanonicalurls_force_http_https_on', function() {
        $('#lgcanonicalurls_force_http_https_value_http').prop('disabled', false);
        $('#lgcanonicalurls_force_http_https_value_https').prop('disabled', false);
        $('input[name^="lgcanonicalurls_force_http_https_value"]:eq(0)').closest('div.margin-form').slideDown();
    });

    $(document).on('change', '#lgcanonicalurls_ignoreparams_off', function() {
        $('#lgcanonicalurls_params').prop('disabled', true);
        $('#lgcanonicalurls_params').closest('div.margin-form').slideUp();
    });

    $(document).on('change', '#lgcanonicalurls_ignoreparams_on', function() {
        $('#lgcanonicalurls_params').prop('disabled', false);
        $('#lgcanonicalurls_params').closest('div.margin-form').slideDown();
    });

    $(document).on('change', '#lgcanonicalurls_hreflang_on', function() {
        $('input[name^="lgcanonicalurls_region_code"]:first').closest('div.margin-form').slideUp();
        $('#lgcanonicalurls_lang_default').prop('disabled', true);
        $('select[name^="lgcanonicalurls_lang_default"]:first').closest('div.margin-form').slideUp();
    });

    $(document).on('change', '#lgcanonicalurls_hreflang_off', function() {
        $('input[name^="lgcanonicalurls_region_code"]:first').closest('div.margin-form').slideDown();
        $('#lgcanonicalurls_lang_default').prop('disabled', false);
        $('select[name^="lgcanonicalurls_lang_default"]:first').closest('div.margin-form').slideDown();
    });

    $(document).on('click','#lgcanonicalurls_canonicalhome_off', function(){
        $('#lgcanonicalurls_canonicalhome_type_default').prop('disabled', true);
        $('#lgcanonicalurls_canonicalhome_type_custom').prop('disabled', true);
        $('#lgcanonicalurls_canonicalhome_type_default').closest('div.margin-form').slideUp();
        $('input[name^="LGCANONICALURLS_CANHOME_TEXT"]').prop('disabled', true);
        $('input[name^="LGCANONICALURLS_CANHOME_TEXT"]').closest('div.margin-form').slideUp();
    });

    $(document).on('click','#lgcanonicalurls_canonicalhome_on', function(){
        $('#lgcanonicalurls_canonicalhome_type_default').prop('disabled', false);
        $('#lgcanonicalurls_canonicalhome_type_custom').prop('disabled', false);
        $('#lgcanonicalurls_canonicalhome_type_default').closest('div.margin-form').slideDown();
        $('input[name^="LGCANONICALURLS_CANHOME_TEXT"]').prop('disabled', false);
        $('input[name^="LGCANONICALURLS_CANHOME_TEXT"]').closest('div.margin-form').slideDown();
    });

    $(document).on('change', 'input[name="lgcanonicalurls_canonicalhome_type"]', function() {
        if ($(this).val() == 'custom') {
            $('input[name^="LGCANONICALURLS_CANHOME_TEXT"]').each(function() {
                $(this).prop('disabled', false);
            });
        } else {
            $('input[name^="LGCANONICALURLS_CANHOME_TEXT"]').each(function() {
                $(this).prop('disabled', true);
            });
        }
    });

    $(document).on('change', 'input[name="lgcanonicalurls_type"]', function() {
        if ($(this).val() == 2) {
            $('input[name^="lgcanonicalurls_canonical_url"]').each(function() {
                $(this).prop('disabled', false);
            });
        } else {
            $('input[name^="lgcanonicalurls_canonical_url"]').each(function() {
                $(this).prop('disabled', true);
            });
        }
    });
});
