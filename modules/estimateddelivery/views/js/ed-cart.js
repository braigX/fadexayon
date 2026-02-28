/**
 /** * Estimated Delivery - Front Office Feature
 *
 * NOTICE OF LICENSE
 *
 * @author    Pol Rué
 * @copyright Smart Modules 2015
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * @category Transport & Logistics
 * Registered Trademark & Property of smart-modules.com
 *
 * ***************************************************
 * *               Estimated Delivery                *
 * *          http://www.smart-modules.com           *
 * *                                                  *
 * ***************************************************
 */

var edCalendarDates = [];
$(document).ready(function(e) {
    $(document).on('click', '#ed_choose_date', function(e) {
        $('.p_ed_delivery_date').datepicker('show');
        e.preventDefault();
    });
    if (typeof calendar_dates !== 'undefined') {
        /*Display add picker on page load*/
        addEdDatePicker();

        jQuery(".delivery-options input[type=radio]").on('click', function () {
            $('.p_ed_delivery_date').datepicker("destroy");
            addEdDatePicker();
        });

        prestashop.on('updatedCart', function () {
            addEdDatePicker();
        });
    }
});

function addEdDatePicker()
{
    // Prepare the calendar
    edCalendarDates = calendar_dates[getCarrier()];
    defaultDate = getSelectedDate(edCalendarDates);
    minDate = createDateFromFormat(edCalendarDates[0], ed_datepicker_format);
    maxDate = createDateFromFormat(edCalendarDates[(edCalendarDates.length)-1], ed_datepicker_format);

    try {
        // console.log([minDate, maxDate]);
        $('.p_ed_delivery_date').datepicker({
            dateFormat: ed_datepicker_format, // WAS "dd-mm-yy"
            defaultDate: defaultDate,
            minDate: minDate,
            maxDate: maxDate,
            beforeShowDay: generateAvailableEdDates,
            onClose: function (selectedDate) {
                updateEdCalendar(selectedDate)
            },
        });
        setTimeout(function() {
            $('.p_ed_delivery_date').datepicker('setDate', defaultDate);
            $('.p_ed_delivery_date').datepicker('refresh');
            updateEdCalendar(defaultDate);
            //console.log('Date set on Datepicker: ' + e);
            if ($('#delivery').length == 1) {
                $('#ed_calendar_display').insertBefore('#delivery');
            }
        }, 0);
    } catch (error) {
        console.error(error);
    }

    function createDateFromFormat(dateString, format) {
        // Detect the delimiter used in the date string
        const delimiterMatch = dateString.match(/[-\/\\|]/);
        if (!delimiterMatch) {
            console.error('Unsupported date format');
            return null;
        }

        const delimiter = delimiterMatch[0];
        const formatMap = format.split(delimiter);
        const dateParts = dateString.split(delimiter).map(Number);
        let day, month, year;

        // Create a mapping of format parts to date values
        const dateMap = {};
        formatMap.forEach((part, index) => {
            dateMap[part] = dateParts[index];
        });

        // Assign the values based on the format parts
        day = dateMap['dd'];
        month = dateMap['mm'];
        year = dateMap['yy'] || dateMap['yyyy'];

        // Adjust year for 'yy' format if necessary
        // if (format.includes('yy') && !format.includes('yyyy') && year < 2000) {
        //     year += 2000;
        // }

        // Months in JavaScript Date objects are zero-indexed (0 = January, 11 = December)
        return new Date(year, month - 1, day);
    }
    function getSelectedDate(dates) {
        let sdate = $('#calendar_current_date').text();
        if (sdate) {
            for (let date of dates) {
                if (date === sdate) {
                    return date;
                }
            }
        }
        return dates[0];
    }
    function updateEdCalendar(selectedDate) {
        $.ajax({
            type: 'POST',
            url: front_ajax_cart_url,
            async: true,
            cache: false,
            data: {
                customDate : true,
                selectedDate: selectedDate
            },
            dataType: "json",
            success: function (jsonData) {
                // console.log(jsonData);
                $('#calendar_current_date').text(jsonData.formatted_date);
            }
        });
    }
}
function generateAvailableEdDates(date) {
    let locale;
    const options = {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
    };

    if (typeof prestashop !== 'undefined') {
        locale = findValueByKey(prestashop, 'locale');
    }
    if (typeof ed_locale !== 'undefined' && !locale) {
        locale = ed_locale;
    }

    // Format the date consistently using '-' as the delimiter
    let formattedDate = date.toLocaleDateString(locale, options).replace(/[/\\|]/g, '-');
    if (locale === 'en-US') {
        // Special case for en-US locale to match the format correctly
        formattedDate = date.toISOString().split('T')[0];
    }

    // Logging for debugging
    console.log("Formatted Date:", formattedDate);
    console.log("edCalendarDates:", edCalendarDates);

    // Compare the formatted date with the edCalendarDates array
    if ($.inArray(formattedDate, edCalendarDates) !== -1) {
        return [true, "", "Available"];
    } else {
        return [false, "", "Unavailable"];
    }
}

function findValueByKey(obj, keyToFind) {
    for (const key in obj) {
        if (obj.hasOwnProperty(key) && typeof obj[key] === 'object') {
            const result = findValueByKey(obj[key], keyToFind);
            if (result !== undefined) {
                return result;
            }
        } else if (key === keyToFind) {
            return obj[key];
        }
    }
    return undefined; // Key not found
}

function getCarrier() {
    let id_carrier;
    const selectedCarrier = jQuery(".delivery-options input[type=radio]");
    if (selectedCarrier.length > 0 && selectedCarrier.length !== undefined) {
        for(i = 0; i < selectedCarrier.length; i++) {
            if(selectedCarrier[i].checked){
                //var id = selectedCarrier.val();
                var id = selectedCarrier[i].defaultValue;
                const myArray = id.split(",");
                id_carrier = myArray[0];
            }
        }
    } else {
        id_carrier = $('.current_carrier_id').val();
    }
    return id_carrier;
}