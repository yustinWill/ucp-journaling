'use strict';

$(function () {
    var bsRangePickerBasic = $('#date-range-picker');

    // Basic - default to last 30 days
    if (bsRangePickerBasic.length) {
        bsRangePickerBasic.daterangepicker({
            opens: isRtl ? 'left' : 'right',
            startDate: moment().subtract(30, 'days'),
            endDate: moment(),
            locale: {
                format: 'YYYY-MM-DD'
            }
        });

        bsRangePickerBasic.on('apply.daterangepicker', function () {
            $(this).trigger('change');
        });

        bsRangePickerBasic.on('cancel.daterangepicker', function () {
            $(this).val('').trigger('change');
        });
    }

    $('#base-date-picker').daterangepicker({
        singleDatePicker: true,
        opens: isRtl ? 'left' : 'right',
        locale: {
            format: 'YYYY-MM-DD'
        }
    });
});
