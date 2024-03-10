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
    })

    $nav.closest('form').on('submit', e => {
        let checkboxes = [];
        let colors = [];

        for (const checkbox of [...document.querySelectorAll('[name^="uitwix["]:checked')]) {
            const name = checkbox.getAttribute('name').match(/.+\[(.+)\]/)[1];

            checkboxes.push(name);
        }

        for (const color of [...document.querySelectorAll('input[name^="uitwix[color]"]')]) {
            const name = color.getAttribute('name').match(/.+\[(.+)\]\[(.+)\]/)[2];

            colors.push(`${name}:${color.value}`);
        }

        document.cookie = `uitwix=${encodeURIComponent(checkboxes.join('-'))}`;
        document.cookie = `uitwix-coloring=${encodeURIComponent(colors.join('-'))}`;
    })
});
