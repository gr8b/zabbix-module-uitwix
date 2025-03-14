($ => {
    const ace_settings = {
        theme: 'ace/theme/twilight',
        maxLines: Infinity,
        minLines: 7,
        autoScrollEditorIntoView: true,
        enableBasicAutocompletion: true,
        enableLiveAutocompletion: true,
        showGutter: true,
        tooltipFollowsMouse: true
    }
    const fields_mode = [
        {
            mode: 'ace/mode/javascript',
            matches: /browser_script|script/
        },
        {
            mode: 'ace/mode/javascript',
            matches: /preprocessing\[\d+\]\[params\]\[\d+\]/
        },
        {
            mode: 'ace/mode/css',
            matches: /uitwix-css\[\d+\]\[css\]/
        }
    ]

    document.addEventListener('DOMContentLoaded', () => {
        (new MutationObserver(mutations => {
            const overlay = overlays_stack.end();

            // Validate overlay opener element is set and is jQuery object.
            if (overlay && overlay.element && typeof overlay.element.get === 'function') {
                const name = overlay.element.get(0).closest('.multilineinput-control').dataset.name;

                for (const field of fields_mode) {
                    if (!field.matches.test(name)) {
                        continue;
                    }

                    for (const mutation of [...mutations]) {
                        for (const elm of [...mutation.addedNodes]) {
                            if (elm.matches && elm.matches('.multilineinput-modal')) {
                                initCodeHighlight(elm.querySelector('.multilineinput-container'), {mode: field.mode});
                            }
                        }
                    }

                    break;
                }
            }
        })).observe(document.body, {
            childList: true,
            subtree: true
        });
    });

    function initCodeHighlight(container, options) {
        const textarea = container.querySelector('textarea');

        [...container.querySelectorAll('.multilineinput-line-numbers,textarea')].map(el => (el.style.display = 'none'));
        container.style = '';

        const ace_div = document.createElement('div');
        ace_div.style = 'width: 100%; height: 100%';
        ace_div.setAttribute('id', 'uitwix-ace-editor');
        container.append(ace_div);

        const editor = ace.edit('uitwix-ace-editor', {
            ...options,
            ...ace_settings,
            value: textarea.value,
            readOnly: textarea.getAttribute('readonly')
        });
        editor.session.on('change', () => {
            textarea.value = editor.getValue();
            $(textarea).trigger('change');// Update characters remaining counter.
            container.style = '';// Remove height set by 'change' handlers.
        });

        if (editor.getReadOnly()) {
            editor.renderer.$cursorLayer.element.style.display = 'none';
        }
        else {
            editor.focus();
        }
    }

})(jQuery)