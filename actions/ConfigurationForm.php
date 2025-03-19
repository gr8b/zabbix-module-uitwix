<?php

namespace Modules\UITwix\Actions;

use CController, CControllerResponseData;
use Modules\UITwix\Services\Preferences;

class ConfigurationForm extends CController {

    public function init() {
        $this->disableCsrfValidation();
    }

    protected function checkInput() {
        return true;
    }

    protected function checkPermissions() {
        return true;
    }

    protected function doAction() {
        $preferences = new Preferences();
        $data = $preferences->get();
        $this->getInputs($data, array_keys($data));

        $this->setResponse((new CControllerResponseData($data)));
    }
}
