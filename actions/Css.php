<?php

namespace Modules\UITwix\Actions;

use CController;
use CControllerResponseData;

class Css extends CController {

    protected function init() {
        $this->disableCsrfValidation();
    }

    public function checkInput() {
        return true;
    }

    public function checkPermissions() {
        return true;
    }

    public function doAction() {
        parse_str(parse_url($_SERVER['HTTP_REFERER']??'', PHP_URL_QUERY), $args);
        $css_action = $args['action']??'';

        $this->setResponse((new CControllerResponseData([
            'css' => 'body { background-color: red !important }'
        ])));
    }
}
