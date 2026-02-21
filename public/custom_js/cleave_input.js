'use strict';

function initCleaveCurrency(context) {
    $(context).find('.currency-input').each(function () {
        if (!this.cleave) {
            this.cleave = new Cleave(this, {
                numeral: true,
                numeralThousandsGroupStyle: 'thousand',
                prefix: 'Rp ',
                numeralDecimalMark: ',',
                delimiter: '.',
                noImmediatePrefix: false,
                rawValueTrimPrefix: true

            });
        }
    });

    $(context).find('.numeric-input').each(function () {
        if (!this.cleave) {
            const $input = $(this);
            this.cleave = new Cleave(this, {
                numeral: true,
                numeralThousandsGroupStyle: 'thousand',
                numeralDecimalMark: ',',
                delimiter: '.',
                numeralDecimalScale: 4, // <-- allow up to 4 decimals
                noImmediatePrefix: false,
                rawValueTrimPrefix: true,
                onValueChanged: function(e) {
                    // Store raw value for later use
                    $input.data('raw-value', e.target.rawValue);
                }
            });

            // On blur, format to strip trailing zeros for conversion-factor inputs
            $input.on('blur', function() {
                if ($(this).hasClass('conversion-factor')) {
                    const rawValue = $(this).data('raw-value') || this.cleave.getRawValue();
                    if (rawValue) {
                        $(this).val(toIndonesianNumberFormat(rawValue));
                    }
                }
            });
        }
    });
}

function formatCurrency(amount) {
    if (!amount || isNaN(amount)) return 'Rp 0';
    return 'Rp ' + new Intl.NumberFormat('id-ID', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 4
    }).format(amount)
}

function parseCurrency(str) {
    if (!str) return 0;
    // Remove all except digits, dot, and comma
    str = str.replace(/[^0-9.,]/g, '');
    // Remove thousand separator (.)
    str = str.replace(/\./g, '');
    // Replace decimal separator (,) with dot
    str = str.replace(',', '.');
    return parseFloat(str) || 0;
}

// function convertToIndonesianFormat(str) {
//     if (!str) return '';
//     // Remove all non-digit, non-dot, non-comma
//     str = str.replace(/[^0-9.,]/g, '');
//     // US format: 100,000.00 → Indonesian: 100.000,00
//     // Remove commas (thousand separator), replace dot (decimal) with comma
//     str = str.replace(/,/g, '.').replace(/\.(\d{2})$/, ',$1');
//     return str;
// }

function convertToIndonesianFormat(str) {
    if (!str) return '';
    // Remove all non-digit, non-dot, non-comma
    str = str.replace(/[^0-9.,]/g, '');
    // Remove commas (thousand separator)
    str = str.replace(/,/g, '.');
    // Replace last dot (decimal separator) with comma, allowing up to 4 decimals
    str = str.replace(/\.([0-9]{1,4})$/, ',$1');
    return str;
    //convert ke format display
}

function toIndonesianNumberFormat(value) {
    if (value == null || value === '') return '';
    // Remove all non-digit, non-dot, non-comma
    value = value.toString().replace(/[^0-9.,]/g, '');

    // Normalize to dot as decimal separator
    if (value.indexOf(',') > -1 && value.indexOf('.') > -1) {
        // Indonesian format: 1.234,5678
        value = value.replace(/\./g, ''); // remove thousand separator
        value = value.replace(',', '.');  // convert decimal to dot
    } else {
        // US format: 1,234.5678 or plain: 1234.5678
        value = value.replace(/,/g, ''); // remove thousand separator
    }

    let num = parseFloat(value);
    if (isNaN(num)) return '';

    // Smart formatting - only show decimals if necessary
    let numStr;
    if (num % 1 === 0) {
        // Whole number - no decimals
        numStr = Math.floor(num).toString();
    } else {
        // Has decimals - limit to max 4 decimals and strip trailing zeros
        numStr = num.toFixed(4).replace(/\.?0+$/, '');
    }

    // Split integer and decimals
    let parts = numStr.split('.');
    // Add thousand separator to integer part
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    // Join with comma as decimal separator
    return parts[1] ? `${parts[0]},${parts[1]}` : parts[0];
}   // "1.234.567,8912"

window.initCleaveCurrency = initCleaveCurrency;
window.formatCurrency = formatCurrency;
window.parseCurrency = parseCurrency;
window.convertToIndonesianFormat = convertToIndonesianFormat;
window.toIndonesianNumberFormat = toIndonesianNumberFormat;