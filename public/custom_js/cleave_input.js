'use strict';

function initCleaveCurrency(context) {
    $(context).find('.currency-input').each(function () {
        if (!this.cleave) {
            this.cleave = new Cleave(this, {
                numeral: true,
                numeralThousandsGroupStyle: 'thousand',
                prefix: 'Rp ',
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
                noImmediatePrefix: false,
                rawValueTrimPrefix: true

            });
        }
    });
}

window.initCleaveCurrency = initCleaveCurrency;