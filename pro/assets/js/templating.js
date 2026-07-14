window.addEventListener('DOMContentLoaded', function () {
    var inputs = jQuery('input[type=color]');
    inputs.each(function (index) {
        jQuery(inputs[index]).ColorPicker({
            onChange: function (hsb, hex, rgb) {
                jQuery(inputs[index]).val('#' + hex);
            },
        });
    });
});
