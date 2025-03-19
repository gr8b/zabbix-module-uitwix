<?php

use Modules\UITwix\Services\Preferences;

/**
 * @var Cview $this
 * @var array $data
 */

$grid = (new CFormGrid([
    // (new CVar('uitwix-csrf', $data['uitwix-csrf'])),
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

    new CLabel(_('Color tags')),
    new CFormField([
        (new CCheckBox('uitwix[state][colortags]', 1))->setChecked((int) $data['state']['colortags']),
        (new CDiv([
            (new CTable())
                ->setHeader([
                    new CColHeader(_('Match')),
                    new CColHeader(_('String')),
                    '',
                    ''
                ])
                ->setFooter(
                    (new CCol(
                        (new CButtonLink(_('Add')))->addClass('element-table-add')
                    ))->setColSpan(4)
                ),
            new CTemplateTag('colortag-row-tmpl', (new CRow([
                    (new CSelect('uitwix-colortag[#{rowNum}][match]'))
                        ->removeId()
                        ->addOptions(CSelect::createOptionsFromArray([
                            Preferences::MATCH_BEGIN => _('Starts with'),
                            Preferences::MATCH_CONTAIN => _('Contains'),
                            Preferences::MATCH_END => _('Ends with')
                        ]))
                        ->setWidth(ZBX_TEXTAREA_SMALL_WIDTH),
                    (new CTextBox('uitwix-colortag[#{rowNum}][value]', '#{value}'))
                        ->removeId()
                        ->setAttribute('placeholder', _('value'))
                        ->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH),
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
    ]),

    new CLabel(_('Custom styles'), 'uitwix[state][css]'),
    new CFormField([
        (new CCheckBox('uitwix[state][css]', 1))->setChecked((int) $data['state']['css']),
        (new CDiv([
            (new CTable())
                ->setHeader([
                    '',
                    (new CColHeader(_('Action')))->setWidth(ZBX_TEXTAREA_SMALL_WIDTH),
                    new CColHeader(_('CSS')),
                    ''
                ])
                ->setFooter(
                    (new CCol(
                        (new CButtonLink(_('Add')))->addClass('element-table-add')
                    ))->setColSpan(3)
                ),
            (new CTemplateTag(null, (new CRow([
                    (new CCol((new CDiv())->addClass(ZBX_STYLE_DRAG_ICON)))->addClass(ZBX_STYLE_TD_DRAG_ICON),
                    (new CTextBox('uitwix-css[#{rowNum}][action]', '#{*action}'))
                        ->removeId()
                        ->setAttribute('placeholder', _('action'))
                        ->setWidth(ZBX_TEXTAREA_MEDIUM_WIDTH),
                    (new CMultilineInput('uitwix-css[#{rowNum}][css]', '', ['add_post_js' => false]))
                        ->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH)
                        ->setAttribute('data-options', json_encode([
                            'title' => _('CSS'),
                            'grow' => 'auto',
                            'rows' => 0,
                            'value' => '#{*css}',
                            'maxlength' => DB::getFieldLength('profiles', 'value_str')
                        ]))
                        ->removeId(),
                    (new CButtonLink(_('Remove')))->addClass('element-table-remove')
                ]))->addClass('form_row')
            ))->setAttribute('data-template', ''),
            (new CTemplateTag(null, json_encode($data['css'])))->setAttribute('data-rows', '')
        ]))
        ->setId('uitwix-css-table')
        ->addClass(ZBX_STYLE_TABLE_FORMS_SEPARATOR)
        ->addStyle('vertical-align: top')
    ]),

    new CLabel(_('Code highlight')),
    new CFormField([
        (new CCheckBox('uitwix[syntax][enabled]', 1))->setChecked((int) $data['syntax']['enabled']),
        (new CDiv(
            (new CDiv(implode("\n", [
                '// Playground',
                'function foo(items) {',
                '    let x = "Syntax highlight test";',
                '',
                '    return x;',
                '}'
            ])))
                ->setId('uitwix-ace-playground')
                ->addStyle('width: 50%; height: 150px')
        ))
    ])
]));


(new CHtmlPage())
    ->setTitle(_('UI Twix'))
    ->addItem(
        (new CForm('post', 'zabbix.php?action=mod.uitwix.form.update'))
            ->addVar(CSRF_TOKEN_NAME, CCsrfTokenHelper::get('mod.uitwix.form.update'))
            ->addItem(getMessages())
            ->addItem(
                (new CTabView())
                    ->addTab('uitwix', _('General'), $grid)
                    ->setFooter(makeFormFooter(new CSubmit('update', _('Update'))))
    ))
    ->show();
