<?php

namespace Modules\UITwix\Actions;

use CController, CControllerResponseRedirect, CUrl;
use CMessageHelper, CCsrfTokenHelper;
use Modules\UITwix\Services\Preferences;

class ConfigurationFormUpdate extends CController {

    protected function checkInput() {
        $fields = [
            'state' =>      'array',
            'color' =>      'array',
            'colortags' =>  'array',
            'css' =>        'array',
            'syntax' =>     'array'
        ];

        $ret = $this->validateInput($fields);

        if (!$ret) {
            $response = new CControllerResponseRedirect(
                (new CUrl('zabbix.php'))->setArgument('action', 'mod.uitwix.form')
            );
            $response->setFormData($this->getInputAll());
            CMessageHelper::setErrorTitle(_('Cannot update configuration'));
            $this->setResponse($response);
        }

        return $ret;
    }

    protected function checkPermissions() {
        return true;
    }

    protected function doAction() {
        $preferences = new Preferences();
        $preferences->set($this->getInputAll());
        $curl = (new CUrl('zabbix.php'))->setArgument('action', 'mod.uitwix.form');

        $this->setResponse((new CControllerResponseRedirect($curl)));
    }
}
