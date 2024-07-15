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
        $preferences = (new Preferences)->get();

        $this->setResponse((new CControllerResponseData([
            'css' => $this->getCssForAction($args, $preferences['css'])
        ])));
    }

    /**
     * Get custom styles matched passed$action.
     *
     * @param string $query_args
     * @param array  $css_mappings
     */
    protected function getCssForAction(array $query_args, array $css_mappings): string {
        $css = [];
        $action = $query_args['action']??'';

        foreach ($css_mappings as $css_mapping) {
            if ($css_mapping['action'] === '' || $css_mapping['action'] === $action) {
                $css[] = "/* {$css_mapping['action']} */";
                $css[] = $css_mapping['css'];

                continue;
            }

            parse_str($css_mapping['action'], $action_args);

            if ($action_args && !array_diff_assoc($action_args, $query_args)) {
                $css[] = "/* {$css_mapping['action']} */";
                $css[] = $css_mapping['css'];
            }
        }

        return implode("\r\n", $css);
    }
}
