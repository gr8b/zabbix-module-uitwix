$(() => {
    const tmpl = document.querySelector('#uitwix-tmpl');
    const $nav = $('#tabs');

    $nav.find('.ui-tabs-nav').append(tmpl.content.querySelector('#tab_uitwix'));
    $nav.find('[role="tabpanel"]:last').after(tmpl.content.querySelector('#uitwix'));
    $nav.tabs('refresh');

    $nav.on('click', '[name="uitwix[bodybg]"],[name="uitwix[asidebg]"]', e => {
        const input = e.target.parentNode.querySelector('input[type="color"]');
        const input_bodyattr = {
            'uitwix[bodybg]': 'uitwix-coloring-body',
            'uitwix[asidebg]': 'uitwix-coloring-sidebar'
        }

        input.toggleAttribute('disabled', !e.target.checked);
        input.closest('label').classList.toggle('disabled', !e.target.checked);
        document.documentElement.toggleAttribute(input_bodyattr[e.target.getAttribute('name')], e.target.checked);
    });
    $nav.on('input', '[name="uitwix[color][bodybg]"],[name="uitwix[color][asidebg]"]', e => {
        const input_cssvar = {
            'uitwix[color][bodybg]': '--uitwix-body-bgcolor',
            'uitwix[color][asidebg]': '--uitwix-sidebar-bgcolor'
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

    $nav.closest('form').on('submit', e => {
        let checkboxes = [];
        let colors = [];
        let colortags = [];

        for (const checkbox of [...document.querySelectorAll('[name^="uitwix["]:checked')]) {
            const name = checkbox.getAttribute('name').match(/.+\[(.+)\]/)[1];

            checkboxes.push(name);
        }

        for (const color of [...document.querySelectorAll('input[name^="uitwix[color]"]')]) {
            const name = color.getAttribute('name').match(/.+\[(.+)\]\[(.+)\]/)[2];

            colors.push(`${name}:${color.value}`);
        }

        for (const row of [...document.querySelectorAll('#uitwix-colortag-table table tr.form_row')]) {
            const value = row.querySelector('input[name$="[string]"]').value;

            if ($.trim(value) !== '') {
                colortags.push(`${value}\n${
                    row.querySelector('input[name$="[match]"]').value
                }\n${
                    row.querySelector('input[name$="[color]"]').value
                }`);
            }
        }

        document.cookie = `uitwix=${encodeURIComponent(checkboxes.join('-'))}`;
        document.cookie = `uitwix-coloring=${encodeURIComponent(colors.join('-'))}`;
        document.cookie = `uitwix-colortags=${encodeURIComponent(colortags.join("\n"))}`;
    })
});
