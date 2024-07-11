<?php

namespace Modules\UITwix\Actions;

use CController;
use CControllerResponseData;
use Modules\UITwix\Services\Preferences;

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
        $referer_action = $args['action']??'';
        $preferences = (new Preferences)->get();

        $this->setResponse((new CControllerResponseData([
            'css' => $this->getCssForAction($referer_action, $preferences['css'])
        ])));
    }

    /**
     * Get custom styles matched passed$action.
     *
     * @param string $action
     * @param array  $css_mappings
     */
    protected function getCssForAction(string $action, array $css_mappings): string {
        $css = [];

        foreach ($css_mappings as $css_mapping) {
            if ($css_mapping['action'] === '' || $css_mapping['action'] === $action) {
                $css[] = "/* {$css_mapping['action']} */";
                $css[] = $css_mapping['css'];
            }
        }

        return implode("\r\n", $css);
    }
}
