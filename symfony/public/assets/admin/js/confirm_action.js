(function ($) {
    "use strict";
    $(document).ready(function () {
        $('.confirmAction').on('click', e => {
            e.preventDefault();
            const button = $(e.target);
            const title = button.data('title') || button.parent('a').first().data('title') || 'Are you sure?';
            const icon = button.data('icon') || button.parent('a').first().data('icon') || 'warning';
            const url = button.data('action') || button.parent('a').first().data('action');
            const hint = button.data('hint') || button.parent('a').first().data('hint') || "Confirm the action performed.";
            const cancelClassName = button.data('cancelClass') || button.parent('a').first().data('cancelClass') || '';
            const confirmClassName = button.data('confirmClass') || button.parent('a').first().data('confirmClass') || '';
            const cancelText = button.data('cancelText') || button.parent('a').first().data('cancelText') || 'Cancel';
            const confirmText = button.data('confirmText') || button.parent('a').first().data('confirmText') || 'Confirm';
            swal({
                title: title,
                text: hint,
                icon: icon,
                buttons: {
                    cancel: {
                        text: cancelText,
                        value: false,
                        visible: true,
                        className: cancelClassName,
                        closeModal: true,
                    },
                    confirm: {
                        text: confirmText,
                        value: true,
                        visible: true,
                        className: confirmClassName,
                        closeModal: true
                    }
                },
                dangerMode: true,
            })
                .then((confirm) => {
                    if (confirm) {
                        window.location.href = url;
                    }
                });
        });
    });
})(jQuery);
