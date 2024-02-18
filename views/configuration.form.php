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
        new CFormField((new CCheckBox('uitwix[sticky]', 1))->setChecked((int) $data['sticky'])),
        new CLabel(_('Enable modal windows drag'), 'uitwix_windrag'),
        new CFormField((new CCheckBox('uitwix[windrag]', 1))->setChecked((int) $data['windrag'])),
    ]))))
        ->setId('uitwix')
        ->setAttribute('role', 'tabpanel')
        ->addStyle('display: none')
]), new CScriptTag(<<<'JAVASCRIPT'
($ => {
    const tmpl = document.querySelector('#uitwix-tmpl');
    const $nav = $('#tabs');

    $nav.find('.ui-tabs-nav').append(tmpl.content.querySelector('#tab_uitwix'));
    $nav.find('[role="tabpanel"]:last').after(tmpl.content.querySelector('#uitwix'));
    $nav.tabs('refresh');

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