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
        ]),

        new CLabel(_('Custom styles')),
        new CFormField([
            new CLabel([
                (new CCheckBox('uitwix[state][csseditor]', 1))->setChecked((int) $data['state']['csseditor']),
                new CSpan(_('Enable editor for all pages'))
            ]),
            new CDiv((new CButtonLink(_('Open editor')))->setId('uitwix-csseditor'))
        ]),

        new CLabel(_('Color tags')),
        new CFormField(
            (new CDiv([
                (new CTable())
                    ->setHeader([
                        (new CCol(_('String')))->setWidth(ZBX_TEXTAREA_SMALL_WIDTH),
                        new CCol(_('Match')),
                        '',
                        ''
                    ])
                    ->setFooter(
                        (new CCol(
                            (new CButtonLink(_('Add')))->addClass('element-table-add')
                        ))->setColSpan(4)
                    ),
                new CTemplateTag('colortag-row-tmpl', (new CRow([
                        (new CTextBox('uitwix-colortag[#{rowNum}][string]', '#{string}'))
                            ->removeId()
                            ->setAttribute('placeholder', _('value'))
                            ->setWidth(ZBX_TEXTAREA_SMALL_WIDTH),
                        (new CSelect('uitwix-colortag[#{rowNum}][match]'))->addOptions(CSelect::createOptionsFromArray([
                            1 => _('Starts with'),
                            2 => _('Contains'),
                            3 => _('Ends with')
                        ]))->removeId(),
                        (new CLabel([
                            (new CInput('color', 'uitwix-colortag[#{rowNum}][color]', '#{color}'))->removeId()
                        ])),
                        (new CButtonLink(_('Remove')))->addClass('element-table-remove')
                    ]))->addClass('form_row')
                ),
                new CTemplateTag('colortag-data', json_encode($data['colortags']))
            ]))
                ->setId('uitwix-colortag-table')
                ->addClass(ZBX_STYLE_TABLE_FORMS_SEPARATOR)
        )
    ]))))
        ->setId('uitwix')
        ->setAttribute('role', 'tabpanel')
        ->addStyle('display: none')
]);
