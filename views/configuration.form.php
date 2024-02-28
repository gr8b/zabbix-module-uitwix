<?php

/**
 * @param $this CView
 */

echo new CTemplateTag('uitwix-tmpl', [
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
]);
