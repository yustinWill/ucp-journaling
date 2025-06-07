<script>
    'use strict';

    $(document).ready(function() {
        $('.select2').select2({
            placeholder: 'Pilih Data',
            allowClear: true
        });
    });

    function delay(callback, ms) {
        var timer = 0;
        return function() {
            var context = this,
                args = arguments;
            clearTimeout(timer);
            timer = setTimeout(function() {
                callback.apply(context, args);
            }, ms || 0);
        };
    }
</script>
