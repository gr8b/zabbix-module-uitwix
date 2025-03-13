($ => {
    document.addEventListener('DOMContentLoaded', e => {
        (new MutationObserver(mutations => {
            for (const mutation of [...mutations]) {
                for (const elm of [...mutation.addedNodes]) {
                    if (elm.matches && elm.matches('.multilineinput-modal')) {
                        const container = elm.querySelector('.multilineinput-container');

                        initCodeHighlight(container);
                    }
                }
            }
        })).observe(document.body, {
            childList: true,
            subtree: true
        });
    });

    function initCodeHighlight(container) {
        const textarea = container.querySelector('textarea');

        [...container.querySelectorAll('.multilineinput-line-numbers,textarea')].map(el => (el.style.display = 'none'));
        container.style = '';

        const ace_div = document.createElement('div');
        ace_div.style = 'width: 100%; height: 100%';
        ace_div.setAttribute('id', 'uitwix-ace-editor');
        container.append(ace_div);

        const editor = ace.edit('uitwix-ace-editor', {
            mode: 'ace/mode/javascript',
            theme: 'ace/theme/twilight',
            value: textarea.value,
            maxLines: Infinity,
            minLines: 7,
            enableBasicAutocompletion: true,
            enableLiveAutocompletion: true,
            showGutter: true,
            tooltipFollowsMouse: true
        });
        editor.session.on('change', () => {
            textarea.value = editor.getValue();
            $(textarea).trigger('change');// Update characters remaining counter.
            container.style = '';// Remove height set by 'change' handlers.
        });
    }

})(jQuery)