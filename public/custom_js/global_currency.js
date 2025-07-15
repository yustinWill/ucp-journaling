/**
 * Global Currency Functions
 * Provides standardized currency formatting and parsing functions for Indonesian Rupiah
 */

'use strict';

// Global currency formatting function
window.formatCurrency = function(amount) {
    return 'Rp ' + amount.toLocaleString('id-ID');
};

// Global currency parsing function (removes formatting and returns number)
window.parseCurrency = function(value) {
    return parseInt(value.replace(/[^\d]/g, '') || '0');
};

// Global currency input formatting function (for direct input field formatting)
window.formatCurrencyInput = function(input) {
    let value = input.value.replace(/[^\d]/g, '');
    if (value) {
        value = parseInt(value).toLocaleString('id-ID');
        input.value = 'Rp ' + value;
    } else {
        input.value = '';
    }
};

// Global display currency formatting function (for readonly/display fields)
window.formatDisplayCurrency = function(amount) {
    return 'Rp ' + amount.toLocaleString('id-ID');
};