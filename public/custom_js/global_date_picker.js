'use strict';

$(function () {
    $('.global-single-date-picker').flatpickr({
        monthSelectorType: 'static',
        static: true,
        onChange: function(selectedDates, _dateStr, instance) {
            if (selectedDates.length > 0) {
                var d = selectedDates[0];
                instance.element.value =
                    d.getFullYear() + '-' +
                    String(d.getMonth() + 1).padStart(2, '0') + '-' +
                    String(d.getDate()).padStart(2, '0');
                instance.element.dispatchEvent(new Event('change', { bubbles: true }));
            }
        }
    });
});
