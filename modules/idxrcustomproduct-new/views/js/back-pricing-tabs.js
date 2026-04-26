(function ($) {
    'use strict';

    function findRowByName(fieldName) {
        var $input = $('[name="' + fieldName + '"]');
        if (!$input.length) {
            return $();
        }
        return $input.first().closest('.form-group');
    }

    function addPricingTabs() {
        var groups = {
            fixed: ['idxr_prix_de_decoupe_cube', 'prixdecollage', 'prix_fixe', 'prix_fixe_vitrine', 'holes_fixed_price'],
            cutting: ['cut_price_4mm', 'cut_price_5mm', 'cut_price_6mm', 'cut_price_8mm', 'cut_price_10mm'],
            gluing: ['glue_price_4mm', 'glue_price_5mm', 'glue_price_6mm', 'glue_price_8mm', 'glue_price_10mm'],
            polishing: ['polish_price_4mm', 'polish_price_5mm', 'polish_price_6mm', 'polish_price_8mm', 'polish_price_10mm']
        };

        var labels = window.idxr_pricing_tabs_labels || {
            title: 'Pricing groups',
            fixed: 'Fixed prices',
            cutting: 'Cutting pricing',
            gluing: 'Gluing pricing',
            polishing: 'Polishing pricing'
        };

        var firstRow = $();
        $.each(groups, function (groupKey, fields) {
            $.each(fields, function (_, fieldName) {
                var $row = findRowByName(fieldName);
                if (!$row.length) {
                    return;
                }
                $row.addClass('idxr-pricing-row idxr-pricing-row-' + groupKey);
                if (!firstRow.length) {
                    firstRow = $row;
                }
            });
        });

        if (!firstRow.length) {
            return;
        }

        if ($('.idxr-pricing-inline-tabs').length) {
            return;
        }

        var tabsHtml = '' +
            '<div class="row idxr-pricing-inline-row">' +
            '  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">' +
            '    <div class="idxr-pricing-inline-tabs">' +
            '      <div class="idxr-pricing-inline-title">' + labels.title + '</div>' +
            '      <ul class="nav nav-tabs nav-justified" role="tablist">' +
            '        <li class="active"><a href="#" data-group="fixed">' + labels.fixed + '</a></li>' +
            '        <li><a href="#" data-group="cutting">' + labels.cutting + '</a></li>' +
            '        <li><a href="#" data-group="gluing">' + labels.gluing + '</a></li>' +
            '        <li><a href="#" data-group="polishing">' + labels.polishing + '</a></li>' +
            '      </ul>' +
            '    </div>' +
            '  </div>' +
            '</div>';

        firstRow.before(tabsHtml);

        function setActiveGroup(groupName) {
            $('.idxr-pricing-inline-tabs .nav-tabs li').removeClass('active');
            $('.idxr-pricing-inline-tabs .nav-tabs a[data-group="' + groupName + '"]').parent().addClass('active');
            $('.idxr-pricing-row').hide();
            $('.idxr-pricing-row-' + groupName).show();
        }

        $('.idxr-pricing-inline-tabs .nav-tabs a').on('click', function (e) {
            e.preventDefault();
            var groupName = $(this).data('group');
            setActiveGroup(groupName);
        });

        setActiveGroup('fixed');
    }

    $(document).ready(function () {
        addPricingTabs();
    });
})(jQuery);
