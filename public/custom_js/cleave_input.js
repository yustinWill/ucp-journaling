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
                noImmediatePrefix: false,
                rawValueTrimPrefix: true

            });
        }
    });
}

function formatCurrency(amount) {
    if (!amount || isNaN(amount)) return 'Rp 0';
    return 'Rp ' + new Intl.NumberFormat('id-ID', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
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

window.initCleaveCurrency = initCleaveCurrency;
window.formatCurrency = formatCurrency;
window.parseCurrency = parseCurrency;