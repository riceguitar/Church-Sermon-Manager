// Modified from: https://wordpress.stackexchange.com/a/251191/111583
jQuery(function ($) {
    $(document).on('click', '#check_license_button', function () {
        if (typeof ajaxurl === 'undefined') {
            return;
        }
        let status = $('#license_status_response');
        let form = $('.forminp-licensing');

        status.text('Checking...');

        form.removeClass();
        form.addClass('forminp forminp-licensing status-checking');
        $.ajax(ajaxurl, {
            type: 'POST',
            data: {
                action: 'smp_check_license',
                license_key: $('#license_key').val(),
            },
            success: function (data) {
                data = isNaN(data) ? data : parseInt(data);
                if (data === 0) {
                    status.text('Invalid.'); // @todo - Localize. And rest of them too.
                    form.removeClass('status-checking');
                    form.addClass('status-invalid');
                } else if (data === 1) {
                    status.text('Valid.');
                    form.removeClass('status-checking');
                    form.addClass('status-valid');
                }else {
                    status.html('There was an error validating your license. Please try <a href="https://wpforchurch.com/my/" target="_blank">reissuing</a> the license or contact support. Error message: ' + data);
                    form.removeClass('status-checking');
                    form.addClass('status-error');
                } 
            },
            error: function () {
                status.text('There was an error while submitting a request. Please contact support.');
                form.removeClass('status-checking');
                form.addClass('status-error');
            }
        });
    });

});