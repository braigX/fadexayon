var start_report = false;
function refreshtab(prefix, div) {
    $.ajax({
        url: ec_seo_ajax,
        data: {
            majsel: 4,
            prefix: prefix,
            tok: EC_TOKEN_SEO,
            ajax : 1
        },
        dataType: 'html',
        success: function (data) {
            $('.'+div).html(data);
        }
    });
}
function refreshtabReport(prefix, div) {
    $.ajax({
        url: ec_seo_ajax,
        data: {
            majsel: 9,
            prefix: prefix,
            tok: EC_TOKEN_SEO,
            ajax : 1
        },
        dataType: 'json',
        success: function (data) {
            $('.'+div).html(data.tab);
            if (data.end && start_report == true) {
                refreshReport();
                start_report = false;
            }
        }
    });
}

$(document).ready(function () {
    
    var metaProductspanelInterval = 0;
    $(document).on('mouseenter', '.refreshMetaProductsPanel', function () {
        if (metaProductspanelInterval === 0) {
            metaProductspanelInterval = setInterval(function(){refreshtab('TASK_EC_SEO_META_PRODUCTS_', 'tabrefreshmetaProductspanel')}, 500);
        }
    });

    $(document).on('mouseleave', '.refreshMetaProductsPanel', function () {
        if (metaProductspanelInterval !== 0) {
            clearInterval(metaProductspanelInterval);
            metaProductspanelInterval = 0;
        } 
        clearInterval(metaProductspanelInterval);
    });

    var metaCategoriespanelInterval = 0;
    $(document).on('mouseenter', '.refreshMetaCategoriesPanel', function () {
        if (metaCategoriespanelInterval === 0) {
            metaCategoriespanelInterval = setInterval(function(){refreshtab('TASK_EC_SEO_META_CATEGORIES_', 'tabrefreshmetaCategoriespanel')}, 500);
        }
    });

    $(document).on('mouseleave', '.refreshMetaCategoriesPanel', function () {
        if (metaCategoriespanelInterval !== 0) {
            clearInterval(metaCategoriespanelInterval);
            metaCategoriespanelInterval = 0;
        } 
        clearInterval(metaCategoriespanelInterval);
    });

    var metaCMSpanelInterval = 0;
    $(document).on('mouseenter', '.refreshMetaCMSPanel', function () {
        if (metaCMSpanelInterval === 0) {
            metaCMSpanelInterval = setInterval(function(){refreshtab('TASK_EC_SEO_META_CMS_', 'tabrefreshmetaCMSpanel')}, 500);
        }
    });

    $(document).on('mouseleave', '.refreshMetaCMSPanel', function () {
        if (metaCMSpanelInterval !== 0) {
            clearInterval(metaCMSpanelInterval);
            metaCMSpanelInterval = 0;
        } 
        clearInterval(metaCMSpanelInterval);
    });

    var metaManufacturerspanelInterval = 0;
    $(document).on('mouseenter', '.refreshMetaManufacturersPanel', function () {
        if (metaManufacturerspanelInterval === 0) {
            metaManufacturerspanelInterval = setInterval(function(){refreshtab('TASK_EC_SEO_META_MANUFACTURERS_', 'tabrefreshmetaManufacturerspanel')}, 500);
        }
    });

    $(document).on('mouseleave', '.refreshMetaManufacturersPanel', function () {
        if (metaManufacturerspanelInterval !== 0) {
            clearInterval(metaManufacturerspanelInterval);
            metaManufacturerspanelInterval = 0;
        } 
        clearInterval(metaManufacturerspanelInterval);
    });

    var metaSupplierspanelInterval = 0;
    $(document).on('mouseenter', '.refreshMetaSuppliersPanel', function () {
        if (metaSupplierspanelInterval === 0) {
            metaSupplierspanelInterval = setInterval(function(){refreshtab('TASK_EC_SEO_META_SUPPLIERS_', 'tabrefreshmetaSupplierspanel')}, 500);
        }
    });

    $(document).on('mouseleave', '.refreshMetaSuppliersPanel', function () {
        if (metaSupplierspanelInterval !== 0) {
            clearInterval(metaSupplierspanelInterval);
            metaSupplierspanelInterval = 0;
        } 
        clearInterval(metaSupplierspanelInterval);
    });

    var backuppanelInterval = 0;
    $(document).on('mouseenter', '.refreshBackUpPanel', function () {
        if (backuppanelInterval === 0) {
            backuppanelInterval = setInterval(function(){refreshtab('TASK_EC_SEO_BACKUP_', 'tabrefreshbackuppanel')}, 500);
        }
    });

    $(document).on('mouseleave', '.refreshBackUpPanel', function () {
        if (backuppanelInterval !== 0) {
            clearInterval(backuppanelInterval);
            backuppanelInterval = 0;
        } 
        clearInterval(backuppanelInterval);
    });

    var imProductspanelInterval = 0;
    $(document).on('mouseenter', '.refreshImProductsPanel', function () {
        if (imProductspanelInterval === 0) {
            imProductspanelInterval = setInterval(function(){refreshtab('TASK_EC_SEO_IM_PRODUCTS_', 'tabrefreshImProductspanel')}, 500);
        }
    });

    $(document).on('mouseleave', '.refreshImProductsPanel', function () {
        if (imProductspanelInterval !== 0) {
            clearInterval(imProductspanelInterval);
            imProductspanelInterval = 0;
        } 
        clearInterval(imProductspanelInterval);
    });

    var imCategoriespanelInterval = 0;
    $(document).on('mouseenter', '.refreshImCategoriesPanel', function () {
        if (imCategoriespanelInterval === 0) {
            imCategoriespanelInterval = setInterval(function(){refreshtab('TASK_EC_SEO_IM_CATEGORIES_', 'tabrefreshImCategoriespanel')}, 500);
        }
    });

    $(document).on('mouseleave', '.refreshImCategoriesPanel', function () {
        if (imCategoriespanelInterval !== 0) {
            clearInterval(imCategoriespanelInterval);
            imCategoriespanelInterval = 0;
        } 
        clearInterval(imCategoriespanelInterval);
    });

    var imCMSpanelInterval = 0;
    $(document).on('mouseenter', '.refreshImCMSPanel', function () {
        if (imCMSpanelInterval === 0) {
            imCMSpanelInterval = setInterval(function(){refreshtab('TASK_EC_SEO_IM_CMS_', 'tabrefreshImCMSpanel')}, 500);
        }
    });

    $(document).on('mouseleave', '.refreshImCMSPanel', function () {
        if (imCMSpanelInterval !== 0) {
            clearInterval(imCMSpanelInterval);
            imCMSpanelInterval = 0;
        } 
        clearInterval(imCMSpanelInterval);
    });

    var imSupplierspanelInterval = 0;
    $(document).on('mouseenter', '.refreshImSuppliersPanel', function () {
        if (imSupplierspanelInterval === 0) {
            imSupplierspanelInterval = setInterval(function(){refreshtab('TASK_EC_SEO_IM_SUPPLIERS_', 'tabrefreshImSupplierspanel')}, 500);
        }
    });

    $(document).on('mouseleave', '.refreshImSuppliersPanel', function () {
        if (imSupplierspanelInterval !== 0) {
            clearInterval(imSupplierspanelInterval);
            imSupplierspanelInterval = 0;
        } 
        clearInterval(imSupplierspanelInterval);
    });

    var imManufacturerspanelInterval = 0;
    $(document).on('mouseenter', '.refreshImManufacturersPanel', function () {
        if (imManufacturerspanelInterval === 0) {
            imManufacturerspanelInterval = setInterval(function(){refreshtab('TASK_EC_SEO_IM_MANUFACTURERS_', 'tabrefreshImManufacturerspanel')}, 500);
        }
    });

    $(document).on('mouseleave', '.refreshImManufacturersPanel', function () {
        if (imManufacturerspanelInterval !== 0) {
            clearInterval(imManufacturerspanelInterval);
            imManufacturerspanelInterval = 0;
        } 
        clearInterval(imManufacturerspanelInterval);
    });

    var imageAltpanelInterval = 0;
    $(document).on('mouseenter', '.refreshImageAltPanel', function () {
        if (imageAltpanelInterval === 0) {
            imageAltpanelInterval = setInterval(function(){refreshtab('TASK_EC_SEO_IMAGE_ALT_', 'tabrefreshImageAltpanel')}, 500);
        }
    });

    $(document).on('mouseleave', '.refreshImageAltPanel', function () {
        if (imageAltpanelInterval !== 0) {
            clearInterval(imageAltpanelInterval);
            imageAltpanelInterval = 0;
        } 
        clearInterval(imageAltpanelInterval);
    });

    var reportpanelInterval = 0;
    $(document).on('mouseenter', '.refreshReportPanel', function () {
        if (reportpanelInterval === 0) {
            reportpanelInterval = setInterval(function(){refreshtabReport('TASK_GEN_EXCEL_', 'tabrefreshReportpanel')}, 500);
        }
    });

    $(document).on('mouseleave', '.refreshReportPanel', function () {
        if (reportpanelInterval !== 0) {
            clearInterval(reportpanelInterval);
            reportpanelInterval = 0;
        } 
        clearInterval(reportpanelInterval);
    });

    $('body').on('click','.taskReport button', function() {
        start_report = true;
    });
});