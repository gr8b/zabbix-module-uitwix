<?php

use Modules\UITwix\Services\Preferences;

/**
 * @var Cview $this
 * @var array $data
 */

$this->addJsFile('multilineinput.js');

$grid = (new CFormGrid([
    new CLabel(_('Enable sticky filters'), 'state_sticky'),
    new CFormField((new CCheckBox('state[sticky]', 1))->setChecked((int) $data['state']['sticky'])),

    new CLabel(_('Enable sticky header for widgets'), 'state_widget_header'),
    new CFormField((new CCheckBox('state[widget_header]', 1))->setChecked((int) $data['state']['widget_header'])),

    new CLabel(_('Enable dragging of modal windows'), 'state_windrag'),
    new CFormField((new CCheckBox('state[windrag]', 1))->setChecked((int) $data['state']['windrag'])),

    new CLabel(_('Custom color theme')),
    new CFormField([
        new CDiv([
            (new CCheckBox('state[bodybg]', 1))->setChecked((int) $data['state']['bodybg']),
            (new CLabel([
                (new CInput('color', 'color[bodybg]', $data['color']['bodybg']))
                    ->setEnabled(!!$data['state']['bodybg']),
                _('Body background color')
            ]))->addClass(!!$data['state']['bodybg'] ? null : ZBX_STYLE_DISABLED)
        ]),
        new CDiv([
            (new CCheckBox('state[asidebg]', 1))->setChecked((int) $data['state']['asidebg']),
            (new CLabel([
                (new CInput('color', 'color[asidebg]', $data['color']['asidebg']))
                    ->setEnabled(!!$data['state']['asidebg']),
                _('Navigation background color')
            ]))->addClass(!!$data['state']['asidebg'] ? null : ZBX_STYLE_DISABLED)
        ])
    ]),

    new CLabel(_('Color tags'), 'state_colortags'),
    new CFormField([
        (new CCheckBox('state[colortags]', 1))->setChecked((int) $data['state']['colortags']),
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
                    (new CSelect('colortag[#{rowNum}][match]'))
                        ->removeId()
                        ->addOptions(CSelect::createOptionsFromArray([
                            Preferences::MATCH_BEGIN => _('Starts with'),
                            Preferences::MATCH_CONTAIN => _('Contains'),
                            Preferences::MATCH_END => _('Ends with')
                        ]))
                        ->setWidth(ZBX_TEXTAREA_SMALL_WIDTH),
                    (new CTextBox('colortag[#{rowNum}][value]', '#{value}'))
                        ->removeId()
                        ->setAttribute('placeholder', _('value'))
                        ->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH),
                    (new CLabel([
                        (new CInput('color', 'colortag[#{rowNum}][color]', '#{color}'))->removeId()
                    ])),
                    (new CButtonLink(_('Remove')))->addClass('element-table-remove')
                ]))->addClass('form_row')
            ),
            new CTemplateTag('colortag-data', json_encode($data['colortags']))
        ]))
            ->setId('uitwix-colortag-table')
            ->addClass(ZBX_STYLE_TABLE_FORMS_SEPARATOR)
    ]),

    new CLabel(_('Custom styles'), 'state_css'),
    new CFormField([
        (new CCheckBox('state[css]', 1))->setChecked((int) $data['state']['css']),
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
                    (new CTextBox('css[#{rowNum}][action]', '#{*action}'))
                        ->removeId()
                        ->setAttribute('placeholder', _('action'))
                        ->setWidth(ZBX_TEXTAREA_MEDIUM_WIDTH),
                    (new CMultilineInput('css[#{rowNum}][css]', '', ['add_post_js' => false]))
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

    new CLabel(_('Code highlight'), 'state_syntax'),
    new CFormField([
        (new CCheckBox('state[syntax]', 1))->setChecked((int) $data['state']['syntax']),
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
        (new CForm('post', (new CUrl('zabbix.php'))->getUrl()))
            ->addVar(CSRF_TOKEN_NAME, CCsrfTokenHelper::get('mod.uitwix.form.update'))
            ->addVar('action', 'mod.uitwix.form.update')
            ->addItem(getMessages())
            ->addItem(
                (new CTabView())
                    ->addTab('uitwix', _('General'), $grid)
                    ->setFooter(makeFormFooter(new CSubmit('update', _('Update'))))
    ))
    ->show();
