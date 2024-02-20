<?php

/**
 * @param $this CView
 */

echo
new CTemplateTag('uitwix-tmpl', [
    (new CListItem((new CLink(_('UI Twix'), '#uitwix'))))
        ->setId('tab_uitwix')
        ->setAttribute('role', 'tab'),
    (new CDiv((new CFormGrid([
        new CLabel(_('Enable sticky filters'), 'uitwix_sticky'),
        new CFormField((new CCheckBox('uitwix[sticky]', 1))->setChecked((int) $data['state']['sticky'])),

        new CLabel(_('Enable dragging of modal windows'), 'uitwix_windrag'),
        new CFormField((new CCheckBox('uitwix[windrag]', 1))->setChecked((int) $data['state']['windrag'])),

        new CLabel(_('Custom color theme')),
        new CFormField([
            new CDiv([
                (new CCheckBox('uitwix[bodybg]', 1))->setChecked((int) $data['state']['bodybg']),
                (new CLabel([
                    (new CInput('color', 'uitwix[color][bodybg]', $data['color']['bodybg']))
                        ->setEnabled(!!$data['state']['bodybg']),
                    _('Body background color')
                ]))->addClass(!!$data['state']['bodybg'] ? null : ZBX_STYLE_DISABLED)
            ]),
            new CDiv([
                (new CCheckBox('uitwix[asidebg]', 1))->setChecked((int) $data['state']['asidebg']),
                (new CLabel([
                    (new CInput('color', 'uitwix[color][asidebg]', $data['color']['asidebg']))
                        ->setEnabled(!!$data['state']['asidebg']),
                    _('Navigation background color')
                ]))->addClass(!!$data['state']['asidebg'] ? null : ZBX_STYLE_DISABLED)
            ])
        ])
    ]))))
        ->setId('uitwix')
        ->setAttribute('role', 'tabpanel')
        ->addStyle('display: none')
]),
new CTag('style', true, <<<'CSS'
input[type="color"] {
    padding: 0;
    border: 0;
    background-color: transparent;
    vertical-align: middle;
    margin-right: 5px;
}
CSS),
new CScriptTag(<<<'JAVASCRIPT'
($ => {
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
        let preferences = [];

        for (const checkbox of [...document.querySelectorAll('[name^="uitwix["]:checked')]) {
            const name = checkbox.getAttribute('name').match(/.+\[(.+)\]/)[1];

            preferences.push(name);
        }

        document.cookie = `uitwix=${preferences.join('-')}`;
    })
})(jQuery)
JAVASCRIPT);