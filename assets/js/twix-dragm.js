(`; ${document.cookie}`).split(`; uitwix=`).pop().split(';')[0].split('-').indexOf('windrag') !== -1
&& ($ => {
    document.addEventListener('DOMContentLoaded', e => {
        (new MutationObserver(mutations => {
            for (const mutation of [...mutations]) {
                for (const elm of [...mutation.addedNodes]) {
                    if (elm.getAttribute && elm.getAttribute('role') === 'dialog') {
                        $('.overlay-dialogue.modal.modal-popup').draggable({
                            containment: 'window',
                            handle: '>.dashboard-widget-head',
                            start: (e, ui) => ui.helper.addClass('uitwix-moved')
                        });
                    }
                }
            }
        })).observe(document.body, {
            childList: true,
            subtree: true
        });
    });

    const centerDialog = eval(`f=${Overlay.prototype.centerDialog}`);

    Overlay.prototype.centerDialog = function () {
        if (!this.$dialogue.is('.uitwix-moved')) {
            centerDialog.call(this);
        }
    }
})(jQuery)
