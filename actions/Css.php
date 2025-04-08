<?php

namespace Modules\UITwix\Actions;

use CController;
use CControllerResponseData;
use CWebUser;
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
        $preferences = (new Preferences)->get();
        $uri = $_SERVER['HTTP_REFERER'] ?? '';

        $this->setResponse((new CControllerResponseData([
            'css' => $this->getCssForAction($uri, $preferences)
        ])));
    }

    /**
     * Get custom styles matched passed$action.
     *
     * @param string $uri
     * @param array  $preferences
     */
    protected function getCssForAction(string $uri, array $preferences): string {
        $css = [];

        $debug = CWebUser::getDebugMode();
        parse_str(parse_url($uri, PHP_URL_QUERY), $query_args);
        $action = $query_args['action']??'';
        $css_mappings = $preferences['state']['css'] ? $preferences['css'] : [];

        if ($action === '') {
            $action = basename(parse_url($uri, PHP_URL_PATH));
            $query_args['action'] = $action;
        }

        if ($debug) {
            $css[] = "/* uri: {$uri} */";
            $css[] = "/* action: {$action} */";
        }

        foreach ($css_mappings as $css_mapping) {
            if ($css_mapping['action'] === '' || $css_mapping['action'] === $action) {
                $css[] = $debug ? "/* apply: {$css_mapping['action']} */" : '';
                $css[] = $css_mapping['css'];

                continue;
            }
            else if (strtolower(substr($css_mapping['action'], 0, 6)) === 'regex:') {
                if (preg_match(substr($css_mapping['action'], 6), $uri) === 1) {
                    $css[] = $debug ? "/* apply: {$css_mapping['action']} */" : '';
                    $css[] = $css_mapping['css'];
                }
                else if ($debug) {
                    $css[] = "/* skip: {$css_mapping['action']} */";
                }

                continue;
            }

            parse_str($css_mapping['action'], $action_args);

            /* @ is used to hide warnings thrown when string is compared against array */
            if ($action_args && !@array_diff_assoc($action_args, $query_args)) {
                $css[] = $debug ? "/* apply: {$css_mapping['action']} */" : '';
                $css[] = $css_mapping['css'];
            }
            else if ($debug) {
                $css[] = "/* skip: {$css_mapping['action']} */";
            }
        }

        $tags = $preferences['state']['colortags'] ? $preferences['colortags'] : [];
        if ($debug) {
            $css[] = '/* tags css */';
        }

        foreach ($tags as $tag) {
            $rule = '';

            switch ($tag['match']) {
                case Preferences::MATCH_BEGIN:
                    $rule = '.tag[data-hintbox-contents^="%1$s"] { background-color: %2$s }';
                    break;

                case Preferences::MATCH_CONTAIN:
                    $rule = '.tag[data-hintbox-contents*="%1$s"] { background-color: %2$s }';
                    break;

                case Preferences::MATCH_END:
                    $rule = '.tag[data-hintbox-contents$="%1$s"] { background-color: %2$s }';
                    break;
            }

            if ($rule !== '') {
                $css[] = sprintf($rule, $tag['value'], $tag['color']);
            }
        }

        $css[] = implode("\r\n", [
        ':root {',
            '--uitwix-body-bgcolor: '.$preferences['color']['bodybg'].';',
            '--uitwix-sidebar-bgcolor: '.$preferences['color']['asidebg'].';',
        '}']);

        return implode("\r\n", $css);
    }
}
