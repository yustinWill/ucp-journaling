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
            this.cleave = new Cleave(this, {
                numeral: true,
                numeralThousandsGroupStyle: 'thousand',
                numeralDecimalMark: ',',
                delimiter: '.',
                numeralDecimalScale: 4, // <-- allow up to 4 decimals
                noImmediatePrefix: false,
                rawValueTrimPrefix: true
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
}

window.initCleaveCurrency = initCleaveCurrency;
window.formatCurrency = formatCurrency;
window.parseCurrency = parseCurrency;
window.convertToIndonesianFormat = convertToIndonesianFormat;