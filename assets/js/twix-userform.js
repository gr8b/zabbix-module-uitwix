$(() => {
    const $nav = $('#uitwix');

    $nav.on('click', '[name="state[bodybg]"],[name="state[asidebg]"]', e => {
        const input = e.target.parentNode.querySelector('input[type="color"]');
        const input_bodyattr = {
            'state[bodybg]': 'uitwix-coloring-body',
            'state[asidebg]': 'uitwix-coloring-sidebar'
        }

        input.toggleAttribute('disabled', !e.target.checked);
        input.closest('label').classList.toggle('disabled', !e.target.checked);
        document.documentElement.toggleAttribute(input_bodyattr[e.target.getAttribute('name')], e.target.checked);
    });
    $nav.on('input', '[name="color[bodybg]"],[name="color[asidebg]"]', e => {
        const input_cssvar = {
            'color[bodybg]': '--uitwix-body-bgcolor',
            'color[asidebg]': '--uitwix-sidebar-bgcolor'
        }

        document.body.style.setProperty(input_cssvar[e.target.getAttribute('name')], e.target.value);
    });

    $nav.find('#uitwix-css-table table').dynamicRows({
        template: '#uitwix-css-table [data-template]',
        dataCallback: (row) => ({action: '', css: '', ...row}),
        sortable: true,
        sortable_options: {
            target: 'tbody',
            selector_handle: `div.${ZBX_STYLE_DRAG_ICON}`,
            freeze_end: 1,
            enable_sorting: true
        }
    }).on('tableupdate.dynamicRows', e => {
        const $cssinput = $(e.target).find('.form_row:last .multilineinput-control');

        ($cssinput.children().length == 0) && $cssinput.multilineInput($cssinput.data('options'));
    }).data('dynamicRows').addRows(
        JSON.parse($nav.find('#uitwix-css-table [data-rows]').html())
            .map(rule => {
                // Additional stringify required for escaping multiline values.
                let encoded = JSON.stringify(rule.css);

                return {...rule, css: encoded.substring(1, encoded.length - 1).replace(/"/g, '&quot;')}
            })
    );

    $nav.find('#uitwix-colortag-table table').dynamicRows({
        template: '#colortag-row-tmpl',
        rows: JSON.parse($nav.find('#colortag-data').html()),
        dataCallback: (row) => ({color: '#000000', ...row})
    });

    initCodeHighlight('uitwix-ace-playground');


    function initCodeHighlight(containerid) {
        const theme = document.documentElement.getAttribute('color-scheme') === 'dark' ? 'ace/theme/twilight' : '';
        const editor = ace.edit(containerid, {
            mode: 'ace/mode/javascript',
            theme,
            enableBasicAutocompletion: true,
            enableLiveAutocompletion: true,
            showGutter: true,
            readOnly: document.querySelector('[name="state[syntax]"]:checked') === null,
            tooltipFollowsMouse: true
        });

        document.querySelector('[name="state[syntax]"]').addEventListener('change', e => {
            editor.setOption('readOnly', !e.target.checked);
            editor.renderer.$cursorLayer.element.style.display = editor.getReadOnly() ? 'none' : '';
        });
        // editor.session.setMode('ace/mode/javascript');

        editor.session.setUseWorker(true);
        editor.renderer.$cursorLayer.element.style.display = editor.getReadOnly() ? 'none' : '';
    }
});
