// Modified from: https://wordpress.stackexchange.com/a/251191/111583
jQuery(function ($) {
    $(document).on('click', '.smp-notice.is-dismissible', function () {
        if (typeof ajaxurl === 'undefined') {
            return;
        }

        $.ajax(ajaxurl, {
            type: 'POST',
            data: {
                action: 'smp_notice_handler',
                id: $(this).closest('.smp-notice').attr('id'),
            },
        });
    });
});
