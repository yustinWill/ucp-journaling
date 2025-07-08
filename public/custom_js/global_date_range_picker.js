'use strict';

// * Pickers with jQuery dependency (jquery)
$(function () {
    // Bootstrap Daterange Picker
    // --------------------------------------------------------------------
    var bsRangePickerBasic = $('#date-range-picker');

    // Basic
    if (bsRangePickerBasic.length) {
        bsRangePickerBasic.daterangepicker({
            opens: isRtl ? 'left' : 'right'
        });
    }

});
