'use strict';

$(function () {
    var bsRangePickerBasic = $('#date-range-picker');

    // Basic
    if (bsRangePickerBasic.length) {
        bsRangePickerBasic.daterangepicker({
            opens: isRtl ? 'left' : 'right',
            locale: {
                format: 'YYYY-MM-DD'
            }
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
