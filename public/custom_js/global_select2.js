/**
 * Selects & Tags
 */

'use strict';

$(function () {
    const selectPicker = $('.selectpicker'),
        select2 = $('.global-select2'),
        select2Icons = $('.select2-icons');

    // Bootstrap Select
    // --------------------------------------------------------------------
    if (selectPicker.length) {
        selectPicker.selectpicker();
    }

    // Select2
    // --------------------------------------------------------------------

    // Default
    if (select2.length) {
        select2.each(function () {
            var $this = $(this);
            var originalValue = $this.val(); // Preserve original selected value
            $this.wrap('<div class="position-relative"></div>').select2({
                placeholder: 'Pilih Data',
                allowClear: true,
                dropdownParent: $this.parent()
            });
            // Only clear if no original value was selected
            if (!originalValue) {
                $this.val(null).trigger('change');
            }
        });
    }

    // Select2 Icons
    if (select2Icons.length) {
        // custom template to render icons
        function renderIcons(option) {
            if (!option.id) {
                return option.text;
            }
            var $icon = "<i class='" + $(option.element).data('icon') + " me-2'></i>" + option.text;

            return $icon;
        }
        select2Icons.wrap('<div class="position-relative"></div>').select2({
            dropdownParent: select2Icons.parent(),
            templateResult: renderIcons,
            templateSelection: renderIcons,
            escapeMarkup: function (es) {
                return es;
            }
        });
    }
});